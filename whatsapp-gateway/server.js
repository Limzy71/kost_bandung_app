require('dotenv').config();
const express = require('express');
const cors = require('cors');
const qrcodeTerminal = require('qrcode-terminal');
const QRCode = require('qrcode');
const pino = require('pino');
const {
    default: makeWASocket,
    useMultiFileAuthState,
    DisconnectReason,
    fetchLatestBaileysVersion,
} = require('@whiskeysockets/baileys');

const PORT = process.env.PORT || 3001;
const GATEWAY_SECRET = process.env.GATEWAY_SECRET || '';
const AUTH_DIR = process.env.AUTH_DIR || './auth_info_baileys';

const app = express();
app.use(cors());
app.use(express.json());

let sock = null;
let state = {
    connected: false,
    status: 'disconnected',
    qr: null,
    qrBase64: null,
    lastSeen: new Date().toISOString(),
};

// Middleware: Bearer Token Auth
function authenticate(req, res, next) {
    if (!GATEWAY_SECRET) {
        return next();
    }

    const authHeader = req.headers.authorization;
    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({ success: false, message: 'Unauthorized: Bearer token required.' });
    }

    const token = authHeader.split(' ')[1];
    if (token !== GATEWAY_SECRET) {
        return res.status(403).json({ success: false, message: 'Forbidden: Invalid gateway secret.' });
    }

    next();
}

async function connectToWhatsApp() {
    try {
        const { state: authState, saveCreds } = await useMultiFileAuthState(AUTH_DIR);
        const { version } = await fetchLatestBaileysVersion();

        sock = makeWASocket({
            version,
            logger: pino({ level: 'silent' }),
            printQRInTerminal: false,
            auth: authState,
            browser: ['KostBandung Gateway', 'Chrome', '1.0.0'],
            syncFullHistory: false,
        });

        sock.ev.on('creds.update', saveCreds);

        sock.ev.on('connection.update', async (update) => {
            const { connection, lastDisconnect, qr } = update;

            if (qr) {
                state.status = 'qr_ready';
                state.connected = false;
                state.qr = qr;
                state.lastSeen = new Date().toISOString();

                try {
                    state.qrBase64 = await QRCode.toDataURL(qr);
                } catch (e) {
                    console.error('Failed to generate QR data URL:', e);
                }

                console.log('\n[KostBandung WA Gateway] QR Code generated. Scan with WhatsApp:');
                qrcodeTerminal.generate(qr, { small: true });
            }

            if (connection === 'open') {
                state.connected = true;
                state.status = 'connected';
                state.qr = null;
                state.qrBase64 = null;
                state.lastSeen = new Date().toISOString();
                console.log('\n[KostBandung WA Gateway] ✅ WhatsApp Connected successfully!');
            }

            if (connection === 'close') {
                state.connected = false;
                state.status = 'disconnected';
                state.lastSeen = new Date().toISOString();

                const shouldReconnect = (lastDisconnect?.error)?.output?.statusCode !== DisconnectReason.loggedOut;
                console.log(`[KostBandung WA Gateway] Connection closed due to: ${lastDisconnect?.error?.message}. Reconnect: ${shouldReconnect}`);

                if (shouldReconnect) {
                    setTimeout(connectToWhatsApp, 5000);
                } else {
                    console.log('[KostBandung WA Gateway] Logged out. Session deleted. Re-scan required.');
                }
            }
        });
    } catch (err) {
        console.error('[KostBandung WA Gateway] Init error:', err);
        setTimeout(connectToWhatsApp, 10000);
    }
}

// 1. Health & Connection Status
app.get('/status', (req, res) => {
    res.json({
        success: true,
        connected: state.connected,
        status: state.status,
        lastSeen: state.lastSeen,
    });
});

// 2. View QR Code (JSON or HTML)
app.get('/qr', (req, res) => {
    if (state.connected) {
        return res.send(`
            <html>
                <body style="font-family:sans-serif; text-align:center; padding:40px; background:#f4f4f5;">
                    <h2 style="color:#16a34a;">✅ WhatsApp Gateway Terhubung!</h2>
                    <p>Nomor WhatsApp aktif dan siap mengirim OTP KostBandung.</p>
                </body>
            </html>
        `);
    }

    if (!state.qrBase64) {
        return res.send(`
            <html>
                <body style="font-family:sans-serif; text-align:center; padding:40px; background:#f4f4f5;">
                    <h2>⏳ Menyiapkan QR Code...</h2>
                    <p>Silakan refresh halaman ini dalam beberapa detik.</p>
                </body>
            </html>
        `);
    }

    if (req.headers.accept && req.headers.accept.includes('application/json')) {
        return res.json({
            success: true,
            status: state.status,
            qrBase64: state.qrBase64,
        });
    }

    res.send(`
        <html>
            <head>
                <meta http-equiv="refresh" content="20">
                <title>Scan WhatsApp KostBandung</title>
            </head>
            <body style="font-family:sans-serif; text-align:center; padding:40px; background:#f4f4f5;">
                <div style="max-width:400px; margin:0 auto; background:white; padding:30px; border-radius:16px; border:3px solid black; box-shadow:6px 6px 0px 0px rgba(0,0,0,1);">
                    <h2 style="margin-top:0; text-transform:uppercase;">Scan WhatsApp Pairing</h2>
                    <p style="font-size:14px; color:#555;">Buka WhatsApp di HP > Perangkat Tertaut > Tautkan Perangkat</p>
                    <img src="${state.qrBase64}" alt="QR Code" style="width:250px; height:250px; margin:15px 0; border:2px solid #ddd; border-radius:8px;" />
                    <p style="font-size:12px; color:#888;">Halaman akan refresh otomatis setiap 20 detik.</p>
                </div>
            </body>
        </html>
    `);
});

// 3. Send WhatsApp Message
app.post('/send-message', authenticate, async (req, res) => {
    try {
        const { phone, message } = req.body;

        if (!phone || !message) {
            return res.status(420).json({ success: false, message: 'Phone and message fields are required.' });
        }

        if (!state.connected || !sock) {
            return res.status(503).json({ success: false, message: 'WhatsApp Gateway is not connected.' });
        }

        // Format clean E.164 phone
        let cleanPhone = String(phone).replace(/\D/g, '');
        if (cleanPhone.startsWith('0')) {
            cleanPhone = '62' + cleanPhone.slice(1);
        }

        const jid = `${cleanPhone}@s.whatsapp.net`;

        const sent = await sock.sendMessage(jid, { text: message });

        return res.json({
            success: true,
            messageId: sent.key.id,
            to: cleanPhone,
        });
    } catch (err) {
        console.error('[KostBandung WA Gateway] Send error:', err);
        return res.status(500).json({
            success: false,
            message: 'Failed to send message: ' + (err.message || 'Unknown error'),
        });
    }
});

app.listen(PORT, '127.0.0.1', () => {
    console.log(`[KostBandung WA Gateway] Listening on http://127.0.0.1:${PORT}`);
    connectToWhatsApp();
});

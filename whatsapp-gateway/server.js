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
        <!DOCTYPE html>
        <html lang="id">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Scan WhatsApp Pairing — KostBandung</title>
                <style>
                    body {
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                        background: #f4f4f5;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        min-height: 100vh;
                        margin: 0;
                        padding: 20px;
                        box-sizing: border-box;
                    }
                    .card {
                        max-width: 440px;
                        width: 100%;
                        background: white;
                        padding: 36px 28px;
                        border-radius: 20px;
                        border: 4px solid black;
                        box-shadow: 8px 8px 0px 0px rgba(0,0,0,1);
                        text-align: center;
                    }
                    h2 {
                        margin-top: 0;
                        font-size: 22px;
                        font-weight: 900;
                        text-transform: uppercase;
                        letter-spacing: -0.5px;
                    }
                    p.sub {
                        font-size: 14px;
                        color: #4b5563;
                        font-weight: 600;
                        margin-bottom: 20px;
                        line-height: 1.4;
                    }
                    .qr-container {
                        background: #fafafa;
                        border: 2px dashed #000;
                        border-radius: 14px;
                        padding: 16px;
                        display: inline-block;
                        margin-bottom: 16px;
                    }
                    img#qr-img {
                        width: 240px;
                        height: 240px;
                        display: block;
                    }
                    .timer-badge {
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        background: #fef08a;
                        border: 2px solid black;
                        border-radius: 8px;
                        padding: 6px 14px;
                        font-size: 13px;
                        font-weight: 800;
                    }
                    .connected-box {
                        display: none;
                        background: #dcfce7;
                        border: 3px solid #16a34a;
                        padding: 24px;
                        border-radius: 16px;
                        color: #15803d;
                    }
                </style>
            </head>
            <body>
                <div class="card" id="pairing-card">
                    <div id="unconnected-view">
                        <h2>Scan WhatsApp Pairing</h2>
                        <p class="sub">Buka WhatsApp di HP &rarr; <b>Perangkat Tertaut</b> &rarr; <b>Tautkan Perangkat</b></p>
                        
                        <div class="qr-container">
                            <img id="qr-img" src="${state.qrBase64}" alt="QR Code" />
                        </div>
                        
                        <div>
                            <div class="timer-badge">
                                ⏳ Refresh QR dalam: <span id="countdown" style="font-family: monospace; font-size: 15px;">20</span> detik
                            </div>
                        </div>
                    </div>

                    <div id="connected-view" class="connected-box">
                        <h2 style="color: #16a34a; margin-bottom: 8px;">✅ WhatsApp Terhubung!</h2>
                        <p style="font-weight: 700; color: #166534; font-size: 14px; margin: 0;">Nomor WhatsApp resmi bot KostBandung siap mengirimkan OTP.</p>
                    </div>
                </div>

                <script>
                    let timeLeft = 20;
                    const countdownEl = document.getElementById('countdown');
                    const qrImg = document.getElementById('qr-img');
                    const unconnView = document.getElementById('unconnected-view');
                    const connView = document.getElementById('connected-view');

                    const timer = setInterval(() => {
                        timeLeft--;
                        if (countdownEl) countdownEl.innerText = timeLeft;

                        if (timeLeft <= 0) {
                            fetchStatus();
                            timeLeft = 20;
                        }
                    }, 1000);

                    async function fetchStatus() {
                        try {
                            const res = await fetch('/qr', { headers: { 'Accept': 'application/json' } });
                            const data = await res.json();
                            if (data.connected || data.status === 'connected') {
                                clearInterval(timer);
                                unconnView.style.display = 'none';
                                connView.style.display = 'block';
                            } else if (data.qrBase64) {
                                qrImg.src = data.qrBase64;
                            }
                        } catch (e) {
                            console.error('Status poll error:', e);
                        }
                    }

                    // Poll every 3 seconds for instant connection detection
                    setInterval(async () => {
                        try {
                            const res = await fetch('/status');
                            const data = await res.json();
                            if (data.connected) {
                                clearInterval(timer);
                                unconnView.style.display = 'none';
                                connView.style.display = 'block';
                            }
                        } catch (e) {}
                    }, 3000);
                </script>
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
            message: 'Gagal mengirim pesan WhatsApp melalui gateway.',
        });
    }
});

app.listen(PORT, '127.0.0.1', () => {
    console.log(`[KostBandung WA Gateway] Listening on http://127.0.0.1:${PORT}`);
    connectToWhatsApp();
});

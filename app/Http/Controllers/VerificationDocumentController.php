<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationDocumentController extends Controller
{
    /**
     * @var array<int, string>
     */
    private const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    /**
     * Stream a private verification document to an authenticated admin only.
     */
    public function __invoke(string $kind, int $id): StreamedResponse
    {
        $path = match ($kind) {
            'identity' => User::findOrFail($id)->identity_doc_path,
            'ownership' => Kost::findOrFail($id)->ownership_doc_path,
            default => null,
        };

        abort_unless(is_string($path), 404);

        if (! in_array(Str::lower((string) pathinfo($path, PATHINFO_EXTENSION)), self::ALLOWED_EXTENSIONS, true)) {
            abort(404);
        }

        $disk = Storage::disk('verification_docs');

        abort_unless($disk->exists($path), 404);

        return $disk->response($path, headers: [
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}

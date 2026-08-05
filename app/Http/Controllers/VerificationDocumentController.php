<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VerificationDocumentController extends Controller
{
    /**
     * Stream a private verification document to an authenticated admin only.
     */
    public function __invoke(string $kind, int $id): StreamedResponse
    {
        $path = match ($kind) {
            'identity' => User::findOrFail($id)->identity_doc_path,
            'ownership' => Kost::with('user')->findOrFail($id)->ownership_doc_path,
            default => null,
        };

        abort_unless($path, 404);

        $disk = Storage::disk(config('filesystems.default'));

        abort_unless($disk->exists($path), 404);

        return $disk->response($path);
    }
}

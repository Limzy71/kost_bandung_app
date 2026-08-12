<?php

namespace App\Livewire\Admin;

use App\Mail\ChangeRequest\ReviewedMail;
use App\Models\Facility;
use App\Models\Kost;
use App\Models\KostChangeRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class ModerationDashboard extends Component
{
    use WithPagination;

    public string $search = '';

    public string $activeTab = 'pending'; // 'pending', 'published', 'rejected', 'all', 'facilities', 'verification', 'changes'

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function approve(int $kostId): void
    {
        $kost = Kost::with('user')->find($kostId);

        if (! $kost) {
            return;
        }

        $kost->status = 'published';
        $kost->save();

        $this->dispatch('show-toast', message: 'Properti "'.$kost->name.'" telah DISETUJUI & TAYANG PUBLIK!');
    }

    public function reject(int $kostId): void
    {
        $kost = Kost::find($kostId);

        if ($kost) {
            $kost->status = 'rejected';
            $kost->save();

            $this->dispatch('show-toast', message: 'Properti "'.$kost->name.'" telah DITOLAK.');
        }
    }

    public function approveIdentity(int $userId): void
    {
        $user = User::find($userId);

        if ($user) {
            $user->identity_verification_status = 'verified';
            $user->identity_verified_at = now();
            $user->identity_rejection_note = null;
            $user->save();

            $this->dispatch('show-toast', message: 'Identitas "'.$user->name.'" telah DISETUJUI & TERVERIFIKASI.');
        }
    }

    public function approveOwnership(int $kostId): void
    {
        $kost = Kost::find($kostId);

        if ($kost) {
            $kost->ownership_verification_status = 'verified';
            $kost->ownership_verified_at = now();
            $kost->ownership_rejection_note = null;
            $kost->save();

            $this->dispatch('show-toast', message: 'Kepemilikan "'.$kost->name.'" telah DISETUJUI & TERVERIFIKASI.');
        }
    }

    public function submitReject(string $type, int $id, ?string $reason = null): void
    {
        $note = trim((string) $reason);
        $note = $note !== '' ? $note : 'Dokumen tidak terbaca atau tidak sesuai. Silakan unggah ulang dokumen yang jelas.';

        if ($type === 'identity') {
            $user = User::find($id);
            if (! $user) {
                return;
            }
            $user->identity_verification_status = 'rejected';
            $user->identity_verified_at = null;
            $user->identity_rejection_note = $note;
            $user->save();

            $this->dispatch('show-toast', message: 'Identitas "'.$user->name.'" telah DITOLAK.');

            return;
        }

        if ($type === 'ownership') {
            $kost = Kost::find($id);
            if (! $kost) {
                return;
            }
            $kost->ownership_verification_status = 'rejected';
            $kost->ownership_verified_at = null;
            $kost->ownership_rejection_note = $note;
            $kost->save();

            $this->dispatch('show-toast', message: 'Kepemilikan "'.$kost->name.'" telah DITOLAK.');
        }
    }

    public function approveChange(int $requestId): void
    {
        $request = KostChangeRequest::with(['kost', 'user'])->find($requestId);

        if (! $request || $request->status !== KostChangeRequest::STATUS_PENDING) {
            return;
        }

        $kost = $request->kost;

        if ($request->name !== $kost->name) {
            $slug = Str::slug($request->name);
            $originalSlug = $slug;
            $count = 1;
            while (Kost::where('slug', $slug)->where('id', '!=', $kost->id)->exists()) {
                $slug = "{$originalSlug}-{$count}";
                $count++;
            }
            $kost->slug = $slug;
        }

        $kost->fill([
            'name' => $request->name,
            'gender_type' => $request->gender_type,
            'district' => $request->district,
            'address' => $request->address,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ])->save();

        $request->update([
            'status' => KostChangeRequest::STATUS_APPROVED,
            'reviewed_at' => now(),
        ]);

        if ($request->user->email) {
            Mail::to($request->user->email)->send(new ReviewedMail($kost, KostChangeRequest::STATUS_APPROVED));
        }

        $this->dispatch('show-toast', message: 'Perubahan data utama "'.$kost->name.'" telah DISETUJUI & DITERAPKAN!');
    }

    public function rejectChange(int $requestId, ?string $reason = null): void
    {
        $request = KostChangeRequest::with(['kost', 'user'])->find($requestId);

        if (! $request || $request->status !== KostChangeRequest::STATUS_PENDING) {
            return;
        }

        $note = trim((string) $reason);
        $note = $note !== '' ? $note : 'Pengajuan perubahan data utama tidak disetujui. Silakan periksa kembali data yang diajukan.';

        $request->update([
            'status' => KostChangeRequest::STATUS_REJECTED,
            'review_note' => $note,
            'reviewed_at' => now(),
        ]);

        if ($request->user->email) {
            Mail::to($request->user->email)->send(new ReviewedMail($request->kost, KostChangeRequest::STATUS_REJECTED, $note));
        }

        $this->dispatch('show-toast', message: 'Perubahan data utama "'.$request->kost->name.'" telah DITOLAK.');
    }

    public function approveFacility(int $facilityId): void
    {
        $facility = Facility::find($facilityId);

        if ($facility) {
            $facility->status = 'approved';
            $facility->save();

            $this->dispatch('show-toast', message: 'Fasilitas "'.$facility->name.'" telah DISETUJUI & Tersedia untuk Semua Pemilik!');
        }
    }

    public function rejectFacility(int $facilityId): void
    {
        $facility = Facility::find($facilityId);

        if ($facility) {
            $facility->kosts()->detach();
            $facility->status = 'rejected';
            $facility->save();

            $this->dispatch('show-toast', message: 'Fasilitas "'.$facility->name.'" telah DITOLAK dan DILEPAS dari seluruh kost.');
        }
    }

    public function render(): View
    {
        $pendingCount = Kost::where('status', 'pending')->count();
        $publishedCount = Kost::where('status', 'published')->count();
        $rejectedCount = Kost::where('status', 'rejected')->count();
        $totalCount = Kost::count();
        $pendingFacilityCount = Facility::where('status', 'pending')->count();
        $pendingChangeCount = KostChangeRequest::where('status', KostChangeRequest::STATUS_PENDING)->count();
        $verificationCount = User::where('identity_verification_status', 'pending')->count()
            + Kost::where('ownership_verification_status', 'pending')->count();

        $base = [
            'pendingCount' => $pendingCount,
            'publishedCount' => $publishedCount,
            'rejectedCount' => $rejectedCount,
            'totalCount' => $totalCount,
            'pendingFacilityCount' => $pendingFacilityCount,
            'pendingChangeCount' => $pendingChangeCount,
            'verificationCount' => $verificationCount,
        ];

        if ($this->activeTab === 'changes') {
            $changeRequests = KostChangeRequest::with(['kost', 'user'])
                ->orderByRaw("CASE WHEN status = '".KostChangeRequest::STATUS_PENDING."' THEN 1 ELSE 2 END")
                ->latest()
                ->paginate(9);

            return view('livewire.admin.moderation-dashboard', $base + [
                'changeRequests' => $changeRequests,
            ])->layout('layouts.app', [
                'title' => 'Moderation Dashboard — Admin KostBandung.web.id',
            ]);
        }

        if ($this->activeTab === 'facilities') {
            $facilities = Facility::where('status', 'pending')
                ->with(['kosts.user'])
                ->orderBy('name')
                ->paginate(9);

            return view('livewire.admin.moderation-dashboard', $base + [
                'facilities' => $facilities,
            ])->layout('layouts.app', [
                'title' => 'Moderation Dashboard — Admin KostBandung.web.id',
            ]);
        }

        if ($this->activeTab === 'verification') {
            $pendingIdentities = User::where('identity_verification_status', 'pending')
                ->orderBy('updated_at')
                ->get();

            $pendingOwnerships = Kost::with(['user', 'primaryImage'])
                ->where('ownership_verification_status', 'pending')
                ->orderBy('updated_at')
                ->get();

            return view('livewire.admin.moderation-dashboard', $base + [
                'pendingIdentities' => $pendingIdentities,
                'pendingOwnerships' => $pendingOwnerships,
            ])->layout('layouts.app', [
                'title' => 'Moderation Dashboard — Admin KostBandung.web.id',
            ]);
        }

        $query = Kost::query()
            ->with(['user', 'primaryImage', 'facilities', 'rules'])
            ->when($this->activeTab !== 'all', function ($q) {
                $q->where('status', $this->activeTab);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('district', 'like', '%'.$this->search.'%')
                        ->orWhere('address', 'like', '%'.$this->search.'%')
                        ->orWhereHas('user', function ($u) {
                            $u->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'published' THEN 2 ELSE 3 END")
            ->latest();

        return view('livewire.admin.moderation-dashboard', $base + [
            'kosts' => $query->paginate(9),
        ])->layout('layouts.app', [
            'title' => 'Moderation Dashboard — Admin KostBandung.web.id',
        ]);
    }
}

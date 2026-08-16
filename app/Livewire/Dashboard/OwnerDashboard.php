<?php

namespace App\Livewire\Dashboard;

use App\Models\BoostTrial;
use App\Models\Kost;
use App\Models\KostChangeRequest;
use App\Models\KostMessage;
use App\Models\User;
use App\Notifications\KostChangeReviewed;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\Features\SupportRedirects\Redirector;
use Livewire\WithPagination;

/**
 * @property-read int $totalProperti
 * @property-read int $totalKamarTersedia
 * @property-read int $pesanMasuk
 */
class OwnerDashboard extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    public ?int $deleteTargetId = null;

    public ?string $deleteTargetName = null;

    public string $deleteConfirmText = '';

    /**
     * @return RedirectResponse|Redirector|null
     */
    public function mount()
    {
        // If user does a hard refresh with '?page=' in the URL, redirect to clean URL to force reset.
        if (request()->has('page')) {
            return redirect()->to(request()->url());
        }

        return null;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function resetSearch(): void
    {
        $this->search = '';
        $this->resetPage();
    }

    public function updatedPage(): void
    {
        $this->dispatch('scroll-to-list');
    }

    public function markChangeNotificationsRead(): void
    {
        /** @var User $user */
        $user = Auth::user();

        $user->unreadNotifications()
            ->where('type', KostChangeReviewed::class)
            ->get()
            ->each->markAsRead();

        $this->dispatch('change-notifications-cleared');
    }

    public function handleChangeReviewed(array $payload): void
    {
        $payload = (array) $payload;

        $this->dispatch('show-toast', message: $payload['message'] ?? 'Pengajuan perubahan telah diulas.', type: ($payload['status'] ?? '') === KostChangeRequest::STATUS_APPROVED ? 'success' : 'error');
    }

    protected function getListeners(): array
    {
        return [
            'echo-private:App.Models.User.'.auth()->id().',.kost.message.sent' => '$refresh',
            'echo-private:App.Models.User.'.auth()->id().',change.request.reviewed' => 'handleChangeReviewed',
        ];
    }

    public function toggleAvailability(int $kostId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($kostId);

        if (! $kost) {
            return;
        }

        $kost->is_available = ! $kost->is_available;
        $kost->save();

        $statusText = $kost->is_available ? 'TERSEDIA' : 'PENUH';
        $this->dispatch('show-toast', message: 'Status ketersediaan "'.$kost->name.'" diubah ke '.$statusText);
    }

    public function openDeleteModal(int $kostId): void
    {
        /** @var User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($kostId);

        if (! $kost) {
            return;
        }

        $this->deleteTargetId = $kostId;
        $this->deleteTargetName = $kost->name;
        $this->deleteConfirmText = '';
        $this->resetErrorBag('deleteConfirmText');

        $this->dispatch('delete-modal-opened');
    }

    public function closeDeleteModal(): void
    {
        $this->reset(['deleteTargetId', 'deleteConfirmText']);
    }

    public function deleteKost(): void
    {
        /** @var User $user */
        $user = Auth::user();
        $kost = $user->kosts()->find($this->deleteTargetId);

        if (! $kost) {
            $this->dispatch('delete-modal-closed');
            $this->closeDeleteModal();

            return;
        }

        if (mb_strtoupper($this->deleteConfirmText) !== 'HAPUS') {
            $this->addError('deleteConfirmText', 'Ketik "HAPUS" untuk mengonfirmasi penghapusan permanen.');

            return;
        }

        $name = $kost->name;

        $kost->forceDelete();

        $this->dispatch('delete-modal-closed');
        $this->closeDeleteModal();

        $this->dispatch('show-toast', message: 'Properti "'.$name.'" telah DIHAPUS PERMANEN.');
    }

    #[Computed]
    public function totalProperti(): int
    {
        return $this->ownerKostsQuery()->count();
    }

    #[Computed]
    public function totalKamarTersedia(): int
    {
        return $this->ownerKostsQuery()->where('is_available', true)->count();
    }

    #[Computed]
    public function pesanMasuk(): int
    {
        return KostMessage::whereNull('read_at')
            ->where(function ($q) {
                $q->whereNull('sender_id')->orWhere('sender_id', '!=', Auth::id());
            })
            ->whereHas('conversation', function ($q) {
                $q->whereHas('kost', fn ($k) => $k->where('user_id', Auth::id()));
            })
            ->count();
    }

    public function render(): View
    {
        /** @var User $user */
        $user = Auth::user();

        if (session()->has('status')) {
            $this->dispatch('show-toast', message: session('status'));
        }

        $kosts = $this->searchQuery()->paginate(9);

        $changeNotifications = $user->unreadNotifications()
            ->where('type', KostChangeReviewed::class)
            ->latest()
            ->limit(5)
            ->get();

        return view('livewire.dashboard.owner-dashboard', [
            'owner' => $user,
            'totalProperti' => $this->totalProperti,
            'totalKamarTersedia' => $this->totalKamarTersedia,
            'pesanMasuk' => $this->pesanMasuk,
            'kosts' => $kosts,
            'hasTrial' => BoostTrial::where('user_id', $user->id)->exists(),
            'changeNotifications' => $changeNotifications,
        ])->layout('layouts.app', [
            'title' => 'Dashboard Pemilik Kost — KostBandung',
        ]);
    }

    /**
     * @return HasMany<Kost, User>
     */
    private function ownerKostsQuery(): HasMany
    {
        /** @var User $user */
        $user = Auth::user();

        return $user->kosts();
    }

    /**
     * @return Builder<Kost>
     */
    private function searchQuery(): Builder
    {
        return $this->ownerKostsQuery()->getQuery()
            ->with(['primaryImage', 'facilities'])
            ->withCount('conversations')
            ->withCount(['changeRequests as pendingChangeCount' => function ($query) {
                $query->where('status', KostChangeRequest::STATUS_PENDING);
            }])
            ->when($this->search, function ($query) {
                $term = '%'.addcslashes($this->search, '%_').'%';

                $query->where(function ($q) use ($term) {
                    $q->where('name', 'like', $term)
                        ->orWhere('district', 'like', $term)
                        ->orWhere('address', 'like', $term);
                });
            })
            ->latest();
    }
}

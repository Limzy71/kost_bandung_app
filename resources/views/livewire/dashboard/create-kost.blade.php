<div
    class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        @include('livewire.dashboard.partials.kost-form-header', [
            'bgClass' => 'bg-yellow-300',
            'badgeClass' => 'bg-black text-yellow-300',
            'badge' => 'Form Pendaftaran',
            'title' => 'Tambah Properti Kost Baru',
            'subtitle' => 'Isi detail properti kost Anda dengan lengkap untuk menarik minat pencari kost di Kota Bandung.',
        ])

        <!-- Form Start -->
        <form wire:submit.prevent="save" x-data="{ formIsOutOfBounds: false }" @bounds-update.window="formIsOutOfBounds = $event.detail" class="space-y-8">

            @include('livewire.dashboard.partials.kost-form', ['isEdit' => false])

            @include('livewire.dashboard.partials.kost-form-actions', ['isEdit' => false])

        </form>

    </div>
</div>

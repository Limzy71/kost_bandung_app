<div
    class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        @include('livewire.dashboard.partials.kost-form-header', [
            'bgClass' => 'bg-cyan-300',
            'badgeClass' => 'bg-black text-cyan-300',
            'badge' => 'Edit Properti',
            'title' => 'Edit Properti Kost',
            'subtitle' => 'Perbarui detail properti kost Anda. Perubahan langsung tercatat pada daftar properti Anda.',
        ])

        <!-- Form Start -->
        <form wire:submit.prevent="save" x-data="{ formIsOutOfBounds: false }" @bounds-update.window="formIsOutOfBounds = $event.detail" class="space-y-8">

            @include('livewire.dashboard.partials.kost-form', ['isEdit' => true, 'coreFieldsLocked' => $coreFieldsLocked])

            @include('livewire.dashboard.partials.kost-form-actions', ['isEdit' => true])

        </form>

    </div>
</div>

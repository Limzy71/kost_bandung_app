<div
    class="min-h-screen bg-[#f8f9fa] dark:bg-zinc-950 bg-[linear-gradient(to_right,#e5e7eb_1px,transparent_1px),linear-gradient(to_bottom,#e5e7eb_1px,transparent_1px)] dark:bg-[linear-gradient(to_right,#27272a_1px,transparent_1px),linear-gradient(to_bottom,#27272a_1px,transparent_1px)] bg-[size:24px_24px]">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12 space-y-8">

        @include('livewire.dashboard.partials.kost-form-header', [
            'bgClass' => 'bg-cyan-300',
            'badgeClass' => 'bg-black text-cyan-300',
            'badge' => 'Edit Properti',
            'title' => 'Edit Properti Kost',
            'subtitle' => $hasPendingChangeRequest
                ? 'Perubahan data utama masih menunggu persetujuan admin. Anda tetap dapat memperbarui detail properti lainnya.'
                : 'Perbarui detail properti kost Anda. Perubahan data utama kost yang sudah tayang akan dikirim ke admin untuk disetujui.',
        ])

        @if($hasPendingChangeRequest)
            <div class="border-4 border-blue-500 bg-blue-50 dark:bg-blue-950/30 rounded-2xl p-5 shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] dark:shadow-[6px_6px_0px_0px_rgba(255,255,255,0.25)] flex items-start gap-3">
                <x-icon name="lucide-hourglass" class="w-6 h-6 text-blue-700 dark:text-blue-300 stroke-[2.5] shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-black text-blue-900 dark:text-blue-200 uppercase">Pengajuan Perubahan Sedang Diproses</p>
                    <p class="text-xs font-bold text-blue-800 dark:text-blue-300 mt-1 leading-relaxed">
                        Perubahan data utama (nama, tipe penghuni, kecamatan, alamat, dan titik lokasi) telah dikirim ke admin dan menunggu persetujuan. Anda tidak dapat mengajukan perubahan data utama lagi sampai admin meninjau pengajuan Anda.
                    </p>
                </div>
            </div>
        @endif

        <!-- Form Start -->
        <form wire:submit.prevent="save" x-data="{ formIsOutOfBounds: false }" @bounds-update.window="formIsOutOfBounds = $event.detail" class="space-y-8">

            @include('livewire.dashboard.partials.kost-form', [
                'isEdit' => true,
                'coreFieldsLocked' => $coreFieldsLocked,
                'isPublished' => $isPublished,
                'hasPendingChangeRequest' => $hasPendingChangeRequest,
            ])

            @include('livewire.dashboard.partials.kost-form-actions', ['isEdit' => true])

        </form>

    </div>
</div>

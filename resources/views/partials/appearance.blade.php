<style>
    :root.dark {
        color-scheme: dark;
    }
</style>
<script>
    window.Flux = window.Flux || {};

    window.Flux.applyAppearance = function (appearance) {
        if (appearance === 'dark') {
            window.localStorage.setItem('flux.appearance', 'dark');
            document.documentElement.classList.add('dark');
        } else {
            window.localStorage.setItem('flux.appearance', 'light');
            document.documentElement.classList.remove('dark');
        }
    };

    // Default tema terang. Gelap hanya aktif jika pengguna memilihnya manual
    // melalui toggle (disimpan di localStorage).
    if (window.localStorage.getItem('flux.appearance') === null) {
        window.localStorage.setItem('flux.appearance', 'light');
    }
    
    // Apply on initial load
    window.Flux.applyAppearance(window.localStorage.getItem('flux.appearance') || 'light');
    
    // Apply when navigating via Livewire wire:navigate
    document.addEventListener('livewire:navigated', () => {
        window.Flux.applyAppearance(window.localStorage.getItem('flux.appearance') || 'light');
    });
</script>

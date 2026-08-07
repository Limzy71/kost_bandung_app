<style>
    :root.dark {
        color-scheme: dark;
    }
</style>
<script>
    window.Flux = window.Flux || {};
    window.Flux.applyAppearance = function (appearance) {
        var applyDark = function () { document.documentElement.classList.add('dark'); };
        var applyLight = function () { document.documentElement.classList.remove('dark'); };

        if (appearance === 'dark') {
            window.localStorage.setItem('flux.appearance', 'dark');
            applyDark();
        } else {
            window.localStorage.setItem('flux.appearance', 'light');
            applyLight();
        }
    };

    window.Flux.applyAppearance(window.localStorage.getItem('flux.appearance'));
</script>

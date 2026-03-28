<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? \App\Models\SiteSetting::getValue('APP_NAME') }}</title>

<link rel="icon" href="/assets/logo/main.png" sizes="any">
<link rel="icon" href="/assets/logo/main.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<style>
    @font-face {
        font-family: "Peyda";
        /*a name to be used later*/
        src: url("/assets/fonts/woff/PeydaWebFaNum-Regular.woff");
        /*URL to font*/
    }

    @font-face {
        font-family: "PeydaBold";
        /*a name to be used later*/
        src: url("/assets/fonts/woff/PeydaWebFaNum-Bold.woff");
        /*URL to font*/
    }

    @font-face {
        font-family: "PelakFa";
        /*a name to be used later*/
        src: url("/assets/fonts/woff/PelakFA-Bold.woff");
        /*URL to font*/
    }
</style>


<link rel="stylesheet" href="/assets/css/jalalidatepicker.min.css">
<link rel="stylesheet" href="/build/assets/app-BVnbn3S0.css">

@vite(['resources/css/app.css', 'resources/js/app.js'])

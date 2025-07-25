<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $title ?? \App\Models\SiteSetting::getValue('APP_NAME') }}</title>

<link rel="icon" href="/assets/logo/main.png" sizes="any">
<link rel="icon" href="/assets/logo/main.png" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />


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


<link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css">

@vite(['resources/css/app.css', 'resources/js/app.js'])

<!doctype html>
<html lang="{{ app()->currentLocale() }}">

<head>
    <meta charset="utf-8">
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ mix('/css/hotspot.css') }}">
</head>

<body>

<!-- two other colors

<body class="lite">
<body class="dark">

-->

<div class="ie-fixMinHeight">
    <div class="main">
        <div class="wrap animated fadeIn">
            <img class="logo" alt="logo" src="{{ asset('/img/hs-logo.svg') }}">

            <p class="info">{!! __('You need to be logged-in to access this network.') !!}</p>

            @if ($errors->any())
                <p class="info alert">
                    {{ $errors->first() }}
                </p>
            @endif

            <label>
                <p class="info">{!! __('By using this service, you acknowledge having read, understood and accepted the <a href="#" data-modal="termsModal">general terms of use</a>.') !!}</p>
            </label>

            <a href="{{ @route('hotspot.staff.redirect') }}" class="login-with google" >{{ __('Continue with Google') }}</a>

            <p class="info bt">{{ __('If you need help, contact the IT!') }}</p>

            @unless (app()->environment('production'))
            <pre>HS: {{ $hs }}<br>Captive: {{ $captive }}<br>Dst: {{ $dst ?? '' }}<br>Mac: {{ $mac }}</pre>
            @endunless

        </div>
    </div>
</div>

<div id="termsModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        @include('hotspot.terms')
    </div>
</div>

<script src="{{ mix('/js/hotspot.js') }}"></script>
</body>

</html>

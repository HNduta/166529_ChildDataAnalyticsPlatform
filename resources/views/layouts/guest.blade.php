<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AutiSync Analytics') }}</title>


    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    {{-- Laravel Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    {{-- ===================== AUTISYNC AUTH STYLES ===================== --}}
    <style>

        :root {
            --ink: #12735A;
            --ink-soft: #6f6a62;
            --line: #e6dfd4;
            --surface: #ffffff;
            --canvas: #faf6f0;

            /* Coral accent — secondary actions, paired with the emerald primary */
            --accent: #e8845c;
            --accent-soft: #f4b79c;
            --accent-tint: #fdf1ea;
        }


        html,
        body {
            font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
            background: var(--canvas);
            color: var(--ink);
        }


        /*
        |--------------------------------------------------------------------------
        | Small Supporting Text
        |--------------------------------------------------------------------------
        */

        .eyebrow {
            font-size: 11px;
            letter-spacing: .06em;
            color: var(--ink-soft);
            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | Decorative Skeleton / Illustration Placeholder
        |--------------------------------------------------------------------------
        */

        .skel {
            position: relative;
            overflow: hidden;
            background: #efe7dc;
            border-radius: 16px;
        }


        /* Coral variant of the illustration placeholder */
        .skel-accent {
            background: var(--accent);
        }


        .skel::after {
            content: "";
            position: absolute;
            inset: 0;

            transform: translateX(-100%);

            background:
                linear-gradient(
                    90deg,
                    rgba(255,255,255,0) 0%,
                    rgba(255,255,255,.75) 50%,
                    rgba(255,255,255,0) 100%
                );

            animation: shimmer 1.8s infinite;
        }


        /*
        |--------------------------------------------------------------------------
        | Reduced Motion
        |--------------------------------------------------------------------------
        */

        @media (prefers-reduced-motion: reduce) {

            .skel::after {
                animation: none;
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Skeleton Animation
        |--------------------------------------------------------------------------
        */

        @keyframes shimmer {

            100% {
                transform: translateX(100%);
            }

        }


        /*
        |--------------------------------------------------------------------------
        | Authentication Form Fields
        |--------------------------------------------------------------------------
        */

        .field-input {

            width: 100%;

            border: 1px solid var(--line);

            border-radius: 10px;

            padding: 10px 12px 10px 38px;

            font-size: 14px;

            background: #ffffff;

            color: var(--ink);

            outline: none;

            transition:
                border-color 0.2s ease,
                box-shadow 0.2s ease;

        }


        /*
        |--------------------------------------------------------------------------
        | Input Focus
        |--------------------------------------------------------------------------
        */

        .field-input:focus {

            border-color: var(--ink);

            box-shadow: 0 0 0 2px rgba(18, 115, 90, 0.12);

        }


        /*
        |--------------------------------------------------------------------------
        | Input Placeholder
        |--------------------------------------------------------------------------
        */

        .field-input::placeholder {
            color: #a89f93;
        }


        /*
        |--------------------------------------------------------------------------
        | Input Icons
        |--------------------------------------------------------------------------
        */

        .field-icon {

            position: absolute;

            left: 12px;

            top: 50%;

            transform: translateY(-50%);

            color: var(--ink-soft);

            pointer-events: none;

        }


        /*
        |--------------------------------------------------------------------------
        | Checkbox Styling
        |--------------------------------------------------------------------------
        */

        input[type="checkbox"] {

            width: 15px;

            height: 15px;

            accent-color: var(--ink);

        }


        /*
        |--------------------------------------------------------------------------
        | Accessibility
        |--------------------------------------------------------------------------
        */

        button:focus-visible,
        a:focus-visible,
        input:focus-visible {

            outline: 2px solid var(--ink);

            outline-offset: 2px;

        }


        /*
        |--------------------------------------------------------------------------
        | Responsive Adjustments
        |--------------------------------------------------------------------------
        */

        @media (max-width: 1023px) {

            body {
                background: var(--canvas);
            }

        }

    </style>

</head>


<body class="font-sans antialiased">

    {{-- 
    |--------------------------------------------------------------------------
    | Authentication Page Content
    |--------------------------------------------------------------------------
    |
    | The login.blade.php and register.blade.php files (and any other view
    | that opens with <x-guest-layout>) are injected here via the slot.
    | Do not add page-specific markup in this file — it is shared across
    | every guest/auth screen (login, register, forgot-password,
    | reset-password, verify-email, confirm-password).
    |
    --}}

    {{ $slot }}


</body>

</html>
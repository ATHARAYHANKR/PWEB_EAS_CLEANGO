<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'CleanGo Laundry')</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Layout guest page: tampilan login/register dengan background full-screen */
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-family: sans-serif;
            background-image: url('/images/bg-login.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            z-index: 0;
        }
        body > * {
            position: relative;
            z-index: 1;
        }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>
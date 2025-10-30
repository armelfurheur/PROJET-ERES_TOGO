
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Responsable HSE | ERES-TOGO</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/toastr@2.1.4/build/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/anomalie.js') }}"></script>
    <script src="{{ asset('js/proposition.js') }}"></script>
    <script src="{{ asset('js/rapport.js') }}"></script>
    <script src="{{ asset('js/archives.js') }}"></script>

</head>

<body>
    <div class="dashboard-container">
        @include('partials.sidebar')
        <div class="main-content">
            @include('partials.header')

            <main class="content-area">
                <div id="view-dashboard" class="hse-view">
                    @yield('content')
                </div>
            </main>
        </div>
</body>

</html>
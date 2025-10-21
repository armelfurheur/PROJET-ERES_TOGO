@extends('layouts.app')

@section('content')
<div class="flex flex-col items-center justify-center min-h-screen bg-gray-50 p-4">
    <h1 class="text-2xl font-bold mb-6">QR Code pour connexion</h1>

    <div id="qrcode" class="p-4 bg-white rounded-xl shadow-lg"></div>

    <p class="mt-4 text-gray-600 text-sm text-center max-w-sm">
        Scannez ce QR code avec votre smartphone pour accéder directement à la page de connexion.
    </p>

    <a href="{{ route('login') }}" class="mt-6 text-green-700 font-semibold hover:underline">
        Aller à la page de connexion
    </a>
</div>

<!-- Inclure la librairie QRCode.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: "{{ route('login') }}",
            width: 200,
            height: 200,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });
    });
</script>
@endsection

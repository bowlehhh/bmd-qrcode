@props([
    'size' => 'h-12 w-12',
    'alt' => 'Logo Kominfo',
    'class' => '',
])

<img src="{{ asset('branding/logo-kominfo-kubar.jpeg') }}" alt="{{ $alt }}" {{ $attributes->class(trim($size.' object-contain '.$class)) }}>

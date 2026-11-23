
@extends('layouts.app', ['titulo' => $titulo])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/oficios.css') }}">
@endpush

@section('contenido')

<section class="catalogo-oficios">

    {{-- Encabezado --}}
    <div class="catalogo-encabezado">
        <a href="{{ route('inicio') }}"
           class="catalogo-volver"
           aria-label="Volver al inicio">
            <i data-lucide="arrow-left"></i>
        </a>

        <h1>Seleccionar oficio</h1>
    </div>

    {{-- Catálogo --}}
    <div class="catalogo-grid">

        @php
            $presentacion = [
                'Albañilería' => ['Albañil', 'box'],
                'Carpintería' => ['Carpintero', 'hammer'],
                'Cerrajería' => ['Cerrajero', 'key-round'],
                'Electricidad' => ['Electricista', 'zap'],
                'Electridad' => ['Electricista', 'zap'],
                'Fletes' => ['Fletero', 'truck'],
                'Gas' => ['Gasista', 'flame'],
                'Pintura' => ['Pintor', 'paint-roller'],
                'Plomería' => ['Plomero', 'droplet'],
            ];
        @endphp

        @forelse ($oficios as $oficio)

            @php
                [$nombre, $icono] = $presentacion[$oficio->tipo]
                    ?? [$oficio->tipo, 'wrench'];
            @endphp

            <a
                href="{{ route('profesional.index', [
                    'oficios' => [$oficio->id]
                ]) }}"
                class="tarjeta-oficio"
            >
                <i data-lucide="{{ $icono }}"
                   aria-hidden="true"></i>

                <span>{{ $nombre }}</span>
            </a>

        @empty

            <p>No hay oficios disponibles.</p>

        @endforelse

    </div>

</section>

@endsection

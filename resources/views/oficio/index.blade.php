
@extends('layouts.app', ['titulo' => $titulo])

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/oficios.css') }}">
@endpush

@section('contenido')

<section class="catalogo-oficios">

    {{-- Encabezado --}}
    <div class="catalogo-encabezado">
        <a href="{{ url()->previous() }}"
        class="btn btn-accent btn-barra fw-semibold">
            <i data-lucide="arrow-left"></i> Volver
        </a>

        <h1>Seleccionar oficio</h1>
    </div>


    {{-- Catálogo --}}
    <div class="catalogo-grid">

        @php
            $iconos = [
                'Albañil' => 'box',
                'Carpintero' => 'hammer',
                'Cerrajero' => 'key-round',
                'Electricista' => 'zap',
                'Fletero' => 'truck',
                'Gasista' => 'flame',
                'Pintor' => 'paint-roller',
                'Plomero' => 'droplet',
            ];
        @endphp

        @forelse ($oficios as $oficio)

            <a
                href="{{ route('profesional.index', [
                    'oficios' => [$oficio->id]
                ]) }}"
                class="tarjeta-oficio"
            >
                <i data-lucide="{{ $iconos[$oficio->tipo] ?? 'wrench' }}"
                   aria-hidden="true"></i>

                <span>{{ $oficio->tipo }}</span>
            </a>

        @empty

            <p>No hay oficios disponibles.</p>

        @endforelse

    </div>

</section>

@endsection

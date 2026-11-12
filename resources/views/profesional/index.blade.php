@extends('layouts.app', ['title' => $titulo])

@section('content')
{{-- 
    $profesional->user->atributo (devuelve la tupla del atributo que se encuentra en user)
    $oficio->atributo (devuelve la tupla del atributo que se encuentra en oficios)
--}}
@forelse ($profesionales as $profesional)
        <article class="profesional">
            @if ($url = $profesional->user->urlImagen($profesional->user->imagen))
                <img
                    class="poster"
                    src="{{ $url }}"
                    alt="Poster de {{ $profesional->user->usuario }}"
                >
            @else
                <div class="sin-imagen">Sin imagen</div>
            @endif

            <div>
                <h2>{{ $profesional->user->usuario }}</h2>
                @foreach ($profesional->oficios as $oficio)
                    <p>{{ $oficio->tipo }}</p>
                @endforeach
                <p>{{ $profesional->provincia }} - {{ $profesional->ciudad }}</p>
                <a href="#">Ver detalle</a>
            </div>
        </article>
    @empty
        <p>No hay profesionals para mostrar.</p>
    @endforelse
@endsection
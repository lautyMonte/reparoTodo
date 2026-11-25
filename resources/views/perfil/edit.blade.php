@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/edit.css') }}">
@endpush

@section('contenido')

<div class="container mt-4" id="contenedor-perfil">
    <h2 class="titulo">Editar perfil</h2>

    {{--Cambiarlo por partials errores-validacion.blade--}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    
    <form action="{{ $profesional ? route('profesional.update'): route('usuario.update') }}" method="POST" enctype="multipart/form-data" class="form-perfil">

        @csrf
        @method('PUT')

        {{-- Portada + avatar (clickeables, sin íconos de lápiz) --}}
        <div class="perfil-header">
            <div for="portada" class="portada">
                <img src="{{ asset('img/portada/portada-default.jpg') }}" alt="Portada">
            </div>

            <label for="avatar" class="avatar" title="Cambiar foto">
                 <img src="{{$usuario->avatar()}}"
                            alt="Foto de {{ $usuario->usuario }}" id="preview-avatar">
                        <i data-lucide="pencil"></i>
            </label>
            <input type="file" name="imagen" id="avatar" accept="image/jpeg,image/png,image/jpg" hidden>
        </div>

        {{-- Datos de users --}}

        <div class="campo">
            <label for="usuario" class="form-label"><i data-lucide="user-round"></i>Usuario</label>
            <input type="text" name="usuario" id="usuario" class="form-control" value="{{ old('usuario', $usuario->usuario) }}" required>
        </div>

        <div class="campo">
            <label for="nombre" class="form-label">Nombre</label>
            <input type="text" name="nombre" id="nombre" class="form-control" value="{{ old('nombre', $usuario->nombre) }}" required>
        </div>

        <div class="campo">
            <label for="apellido" class="form-label">Apellido</label>
            <input type="text" name="apellido" id="apellido" class="form-control" value="{{ old('apellido', $usuario->apellido) }}" required>
        </div>

        <div class="campo">
            <label for="email" class="form-label"><i data-lucide="mail"></i>Correo electrónico</label>
            <input type="email" name="email" id="email" class="form-control"
                   value="{{ old('email', $usuario->email) }}" required>
        </div>

        <div class="campo">
            <label for="telefono" class="form-label"><i data-lucide="phone"></i>Teléfono</label>
            <input type="tel" name="telefono" id="telefono" class="form-control"
                   value="{{ old('telefono', $usuario->telefono) }}">
        </div>


        {{-- Datos exclusivos del profesional --}}
        @if ($profesional)
            <div class="campo">
                <label for="ciudad" class="form-label"><i data-lucide="map-pin"></i>Ubicación</label>
                <div class="fila">
                    <select id="provincia" name="provincia[]" class="tom-select" multiple required>
                        @foreach ($provincias as $provincia)
                            <option value="{{ $provincia->id }}" @selected(in_array( $provincia->id, old('provincia', $provinciasSeleccionadas) ))>
                                {{ $provincia->nombre }}
                            </option>
                        @endforeach
                    </select>
                    <select id="ciudad" name="ciudad[]" class="tom-select" multiple required>
                    </select>
                    
                </div>
            </div>

            @if ($oficios)
                @php
                    $seleccionados = old('oficios', $profesional->oficios->pluck('id')->all());
                @endphp

                <div class="campo">
                    <span class="form-label"><i data-lucide="wrench"></i>Oficios</span>
                    <div class="oficios">
                        @foreach ($oficios as $oficio)
                            <input type="checkbox" name="oficios[]" id="oficio-{{ $oficio->id }}"
                                   value="{{ $oficio->id }}" @checked(in_array($oficio->id, $seleccionados))>
                            <label for="oficio-{{ $oficio->id }}" class="chip">{{ $oficio->tipo }}</label>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif

        <div class="acciones">
            <button type="submit" class="btn-guardar">Guardar</button>
            <a href="{{ route('inicio') }}" class="btn-cancelar">Cancelar</a>
        </div>
    </form>
</div>
<script>
    const inputAvatar = document.getElementById('avatar');
    const previewAvatar = document.getElementById('preview-avatar');

    inputAvatar.addEventListener('change', function () {

        const archivo = this.files[0];

        if (archivo) {
            previewAvatar.src = URL.createObjectURL(archivo);
        }

    });
</script>
@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>

    <script>
        const provinciasDB = @js($provincias);
        const ciudadesAnteriores = @js(old('ciudad', $ciudadesAnteriores));
    </script>

    <script src="{{ asset('js/registro.js') }}"></script>
    <script src="{{ asset('js/ciudades.js') }}" defer></script>

@endpush
@endsection
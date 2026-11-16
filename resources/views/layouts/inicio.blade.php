@extends('layouts.app')

@section('content')

    @if (auth()->user()->tipo === 'comun')

        @include('profesional.listado')

    @elseif (auth()->user()->tipo === 'profesional')

        @include('profesional.perfil')

        @include('chat.bandeja')

    @endif

@endsection
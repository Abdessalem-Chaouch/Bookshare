@extends('baseF')

@section('content')
    @include('FrontOffice.Livres.LivreContenu', ['livres' => $livres])
@endsection

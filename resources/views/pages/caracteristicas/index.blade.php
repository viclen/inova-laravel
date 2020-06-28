@extends('layouts.app')

@section('content')
<editor-caracteristicas :dados="{{ $caracteristicas }}" :regras="{{ $regras }}" />
@endsection

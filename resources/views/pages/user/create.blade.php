@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-1">
        <div class="col-12">
            <a href="{{ route('users') }}" class="btn btn-light btn-sm">
                <span>
                    <fa-icon icon="arrow-left" />
                </span>
                Voltar
            </a>
        </div>
    </div>
    <form method="POST" action="{{ route('users') }}">
        @csrf

        <div class="form-group row">
            <label for="name" class="col-md-4 col-form-label text-md-right">{{ __('Nome') }}</label>

            <div class="col-md-6">
                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                    value="{{ old('name') }}" required autocomplete="name" autofocus>

                @error('name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="username" class="col-md-4 col-form-label text-md-right">{{ __('Login') }}</label>

            <div class="col-md-6">
                <input id="username" type="text" class="form-control @error('username') is-invalid @enderror"
                    name="username" value="{{ old('username') }}" required autocomplete="username" autofocus>

                @error('username')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Email') }}</label>

            <div class="col-md-6">
                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email">

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Senha') }}</label>

            <div class="col-md-6">
                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror"
                    name="password" required autocomplete="new-password">

                @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>

        <div class="form-group row">
            <label for="password-confirm"
                class="col-md-4 col-form-label text-md-right">{{ __('Confirmar Senha') }}</label>

            <div class="col-md-6">
                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required
                    autocomplete="new-password">
            </div>
        </div>

        <div class="form-group row mb-0">
            <div class="col-md-6 offset-md-4">
                <button type="submit" class="btn btn-success">
                    <span>
                        <fa-icon icon="save" />
                    </span>
                    &nbsp;Salvar
                </button>
                <a href="{{ route('users') }}" class="btn btn-secondary">
                    <span>
                        <fa-icon icon="times" />
                    </span>
                    &nbsp;Cancelar
                </a>
            </div>
        </div>
    </form>
</div>
@endsection

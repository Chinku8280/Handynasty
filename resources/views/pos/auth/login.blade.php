@extends('layouts.auth')

@section('content')

    <span class="logo-box">
        <img src="{{ $frontThemeSettings->logo_url }}" alt="logo" style="height:100px; width:100px;">
        {{-- <img src="{{  $settings->logo_url }}" alt="logo" style="height:100px; width:100px;"> --}}
    </span>

    <h4>{{$outlet->outlet_name ?? ''}}</h4>

    <h4 class="mb-30">@lang('app.signInToAccount')</h4>

    {{-- Display Error Messages --}}
    @if ($errors->has('errors'))
        <div class="alert alert-danger">
            {{ $errors->first('errors') }}
        </div>
    @endif

    <form action="{{ route('pos.login-submit', $outlet->outlet_slug) }}" method="post">
        @csrf
        <input type="hidden" name="outlet_id" value="{{$outlet->id}}">

        <div class="input-group">
            <i class="fa fa-envelope"></i>
            <input type="email" name="email" id="email" class="form-control {{ $errors->has('email') ? ' is-invalid' : '' }}" value="{{ old('email') }}" required autofocus>
            <label for="email">@lang('app.email')</label>
            @if ($errors->has('email'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
        <div class="input-group">
            <i class="fa fa-lock"></i>
            <input type="password" id="password" class="form-control {{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" required>
            <label for="password">@lang('app.password')</label>
            @if ($errors->has('password'))
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
        <div class="centering v-center">
                <span class="mb-4">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember">@lang('app.rememberMe')</label>
                </span>
                <span class="mb-4">
                    <a href="{{ route('password.request') }}" class="c-theme"> @lang('app.forgotPassword')</a>
                </span>
        </div>
        <div class="d-flex justify-content-between flex-wrap">
            {{-- <a href="{{ route('front.index') }}" class="btn btn-custom">@lang('front.navigation.backToHome')</a> --}}
            <button type="submit" class="btn btn-custom btn-blue">@lang('app.signIn')</button>
        </div>
    </form>
@endsection

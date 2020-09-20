@extends('layouts.app')
@section('content')
<div style="background-color: #002A3A; width: 100%;" class="m-0 p-0">

        <!-- Outer Row -->
        <div class="container-fluid row justify-content-center">

          <div class="col-xl-10 col-lg-12 col-md-9">

            <div class="card o-hidden border-0 shadow-lg my-5">
              <div class="card-body p-0">
                <!-- Nested Row within Card Body -->
                <div class="row">
                  <div class="col-lg-6 d-none d-lg-block"><img src="https://source.unsplash.com/500x450/?japan,nature,water"></div>
                  <!-- <div class="col-lg-6 d-none d-lg-block bg-login-image"></div> -->
                  <div class="col-lg-6">
                    <div class="p-5">
                      <div class="text-center">
                        <h1 class="h4 text-gray-900 mb-4">{{ __('Iniciar Sesión') }}</h1>
                      </div>
                      <form class="user" method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                          <input id="email" type="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Ingrese su correo electrónico..." autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror  
                        </div>
                        <div class="form-group">
                          <input id="password" type="password" class="form-control form-control-user @error('password') is-invalid @enderror" name="password" placeholder="Contraseña" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror  
                        </div>
                        <div class="form-group">
                          <div class="custom-control custom-checkbox small">

                                    <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="custom-control-label" for="remember">
                                        {{ __('Recuerdame') }}
                                    </label>
                          </div>
                        </div>

                        <button type="submit" class="btn btn-dark btn-user btn-block">
                                    {{ __('Login') }}
                                </button>

                                
                        <hr>
                        <!-- <a href="index.html" class="btn btn-google btn-user btn-block">
                          <i class="fab fa-google fa-fw"></i> Login with Google
                        </a>
                        <a href="index.html" class="btn btn-facebook btn-user btn-block">
                          <i class="fab fa-facebook-f fa-fw"></i> Login with Facebook
                        </a> -->
                      </form>
                      <div class="text-center">
                        @if (Route::has('password.request'))
                                    <a class="small btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Olvidaste tu contraseña?') }}
                                    </a>
                                @endif
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

        </div>
@endsection

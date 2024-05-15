@extends('clients.layouts.app')

@section('main')
    <section class="section-5">
        <div class="container my-5">
            <div class="py-lg-2">&nbsp;</div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-5">
                    @include('clients.messages')
                    <div class="card shadow border-0 p-5">
                        <h1 class="h3">Forgot password</h1>
                        <form action="{{ route('account.processForgotPassword') }}" method="post" name="forgotForm" id="forgotForm">
                            @csrf
                            <div class="mb-3">
                                <label for="" class="mb-2">Email*</label>
                                <input type="text" name="email" id="email" value="{{ @old('email')}}" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="Enter Email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary mt-2">Send</button>
                        </form>
                    </div>
                    <div class="mt-4 text-center">
                        <p>Have an account? <a href="{{ route('account.login') }}">Login</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

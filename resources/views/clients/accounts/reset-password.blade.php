@extends('clients.layouts.app')

@section('main')
    <section class="section-5">
        <div class="container my-5">
            <div class="py-lg-2">&nbsp;</div>
            <div class="row d-flex justify-content-center">
                <div class="col-md-5">
                    @include('clients.messages')
                    <div class="card shadow border-0 p-5">
                        <h1 class="h3">Reset password</h1>
                        <form action="{{ route('account.processResetPass') }}" method="post">
                            @csrf
                            <input type="hidden" value="{{$token}}" name="token">
                            <div class="mb-3">
                                <label for="" class="mb-2">Password *</label>
                                <input type="password" name="password" id="password" value="{{@old('password')}}"
                                       class="form-control @error('password') is-invalid @enderror"
                                       placeholder="Enter Password">
                                @error('password')
                                <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="" class="mb-2">Password Confirm *</label>
                                <input type="password" name="password_confirm" id="password_confirm"
                                       class="form-control @error('password_confirm') is-invalid @enderror"
                                       placeholder="Enter Password confirm">
                                @error('password_confirm')
                                <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="justify-content-between d-flex">
                                <button type="submit" class="btn btn-primary mt-2">Reset</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="py-lg-5">&nbsp;</div>
        </div>
    </section>
@endsection

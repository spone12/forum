@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('auth.email_comfirm') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ __('auth.email_confirm_link_send') }}
                        </div>
                    @endif

                    {{ __('auth.email_check') }}
                    {{ __('auth.email_not_get') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ __('auth.email_send_repeat') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ trans('auth.email.email_comfirm') }}</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            {{ trans('auth.email.email_confirm_link_send') }}
                        </div>
                    @endif

                    {{ trans('auth.email.email_check') }}
                    {{ trans('auth.email.email_not_get') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 m-0 align-baseline">{{ trans('auth.email.email_send_repeat') }}</button>.
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

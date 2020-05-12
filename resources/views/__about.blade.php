@extends('layouts.app')
@section('title-block')о@endsection
@section('content')

<div>test</div>
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                          {{ session('status') }}
                        </div>
                    @endif

                   
@endsection

@extends('base')

@section('content')
    <div class="column is-6-tablet is-6-desktop is-6-widescreen has-text-centered">
        <div class="is-size-4">Whoops, something went wrong...</div>
        <div class="">@if (isset($exception))
                {{ $exception->getMessage() }}
            @elseif($error)
                {{ $error }}
            @endif</div>
        <br>
        <a href="/" class="button">Try again</a>
    </div>
    </div>
@endsection

@section('script')
@endsection
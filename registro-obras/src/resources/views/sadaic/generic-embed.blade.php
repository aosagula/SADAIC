@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center" id="embed-header">
        <div class="col col-12">
            <h2>{{ $title }}</h2>
        </div>
    </div>
</div>
<div id="embed-content" class="container">
    <div class="row">
        <div class="col-12">
            {!! $content !!}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/sadaic.js') }}" defer></script>
@endpush

@push('styles')
<style>
#embed-header h2 {
    border-bottom: solid 1px rgb(208, 215, 219);
    padding-bottom: 8px;
}
</style>
@endpush

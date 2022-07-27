@extends('layouts.base')

@section('css')
<link rel='stylesheet' href='{{ env('APP_PROTOCOL') . env('APP_ADDRESS') }}/css/shorten_result.css' />
@endsection

@section('content')
<h3>Shortened URL</h3>
<div class="input-group">
    <input type='text' class='result-box form-control' value='{{$short_url}}' id='short_url' />
    <div class='input-group-addon' id='clipboard-copy' data-clipboard-target='#short_url' data-toggle='tooltip' data-placement='bottom' data-title='Copied!'>
        <i class='fa fa-clipboard' aria-hidden='true' title='Copy to clipboard'></i>
    </div>
</div>
<a id="generate-qr-code" class='btn btn-primary'>Generate QR Code</a>
<a href='{{route('admin')}}' class='btn btn-info'>Shorten another</a>

<div class="qr-code-container"></div>

@endsection


@section('js')
<script src='{{ env('APP_PROTOCOL') . env('APP_ADDRESS') }}/js/qrcode.min.js'></script>
<script src='{{ env('APP_PROTOCOL') . env('APP_ADDRESS') }}/js/clipboard.min.js'></script>
<script src='{{ env('APP_PROTOCOL') . env('APP_ADDRESS') }}/js/shorten_result.js'></script>
@endsection

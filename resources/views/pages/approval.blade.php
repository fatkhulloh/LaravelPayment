@extends('layouts.master')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="container mt-5">
    <h1>Hasil Pembayaran PayPal</h1>

            @if(isset($result->status) && $result->status === 'COMPLETED')
                <div class="alert alert-success">
                    <h3>✅ Pembayaran Berhasil!</h3>
                    <p>Order ID: {{ $result->id }}</p>
                    <p>Status: {{ $result->status }}</p>
                </div>
            @else
                <div class="alert alert-danger">
                    <h3>❌ Pembayaran Gagal!</h3>
                    <p>{{ $error ?? 'Terjadi kesalahan pada transaksi.' }}</p>
                </div>
            @endif

            <pre>{{ json_encode($result, JSON_PRETTY_PRINT) }}</pre>
        </div>
        </div>
    </div>
</div>
@endsection

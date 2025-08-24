@extends('layout.app')

@push('head')
<style>
  :root{ --gap-main: 25px; }
  .page{ margin-left: calc(var(--sb-w) + var(--gap-main)); padding: 12px; }
  .box{
    background:#fff; border:5px solid #e5e7eb; border-radius:20px;
    padding:16px 18px; color:#111827; max-width:900px;
  }
  html.dark .box{ background:#1f2937; border-color:#0062ff49; color:#e5e7eb; }
  h2{ margin:0 0 8px; }
</style>
@endpush

@section('title','Dashboard')

@section('content')
  <div class="page">
    <div class="box">
      <h2>Selamat Datang di Dashboard GetStockly 🎉</h2>
      <p>Silakan pilih menu di sidebar untuk mulai mengelola data.</p>
    </div>
  </div>
@endsection

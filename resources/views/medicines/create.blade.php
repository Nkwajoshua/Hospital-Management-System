@extends('layouts.app')
@section('title','Add Medicine | HMS')
@section('content')<div class="mb-4"><h1 class="h3">Add Medicine</h1></div><div class="card border-0 shadow-sm"><div class="card-body p-4"><form action="{{ route('medicines.store') }}" method="POST">@csrf @include('medicines._form',['submitLabel'=>'Add Medicine'])</form></div></div>@endsection

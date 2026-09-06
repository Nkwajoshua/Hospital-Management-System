@extends('layouts.app')
@section('title','Edit Medicine | HMS')
@section('content')<div class="mb-4"><h1 class="h3">Edit Medicine</h1></div><div class="card border-0 shadow-sm"><div class="card-body p-4"><form action="{{ route('medicines.update',$medicine) }}" method="POST">@csrf @method('PUT') @include('medicines._form',['submitLabel'=>'Save Changes'])</form></div></div>@endsection

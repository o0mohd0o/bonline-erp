@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h4 class="mb-0">Edit Service Template</h4>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('service-templates.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <form action="{{ route('service-templates.update', $serviceTemplate) }}" method="POST">
        @csrf
        @method('PUT')
        @include('service-templates.form')

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Update Template
            </button>
        </div>
    </form>
</div>
@endsection

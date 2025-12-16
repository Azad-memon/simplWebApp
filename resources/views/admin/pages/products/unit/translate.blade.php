@extends('admin.layouts.master') {{-- Use your actual Cuba layout here --}}

@section('title', 'Translations')
<style>
    .card-header {
    background-color: #f6f6f6;
    border-bottom: 1px solid #eee;
}

.card h5 {
    font-size: 1.2rem;
    font-weight: 600;
}

.form-control {
    border-radius: 6px;
}

.btn-primary {
    background-color: #3c8dbc;
    border-color: #367fa9;
}

</style>
@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="page-title-box">
        <h4 class="page-title">Translate: {{ $translations->name }}</h4>
    </div>

    <!-- Translations Table -->
   <div class="card">
                <div class="card-header">
            <a href="{{ route('admin.unit.index') }}" class="btn btn-dark mt-3">← Back to Units</a>
                </div>
                <div class="card-body">
                    @if (session()->has('green'))
                        <div class="alert alert-success">{{ session('green') }}</div>
                    @elseif(session()->has('red'))
                        <div class="alert alert-danger">{{ session('red') }}</div>
                    @endif
         @if($languages->isEmpty())
        <div class="alert alert-danger text-center">
            ⚠️ No languages found. Please add a language first to enable translations.
            <br>
            <a href="{{ route('admin.languages.index') }}" class="btn btn-sm btn-primary mt-2">
                <i class="fa fa-plus"></i> Add Language
            </a>
        </div>
        @else
   <div class="row">
    @foreach ($languages as $language)
        @php
            $title = $translations->translations
                        ->where('language_id', $language->id)
                        ->where('meta_key', 'title')
                        ->first();


        @endphp

        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 text-center"> {{ ucfirst($language->name) }} </h5>
                </div>

                 <form class="translation-form" data-lang="{{ $language->code }}" method="POST"
                        action="{{ route('admin.language-translation.updateAll') }}">
                        @csrf
                    <input type="hidden" name="language_id" value="{{ $language->id }}">
                    <input type="hidden" name="translatable_id" value="{{ $translations->id }}">
                    <input type="hidden" name="translatable_type" value="{{ get_class($translations) }}">

                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title_{{ $language->id }}" class="form-label fw-bold">Title</label>
                            <input type="text"
                                   class="form-control @error('title') is-invalid @enderror"
                                   name="title"
                                   id="title_{{ $language->id }}"
                                   value="{{ $title?->meta_value }}">
                        </div>

                    </div>

                    <div class="card-footer bg-light text-end">
                        <button type="submit" class="btn btn-dark">
                            <i class="fa fa-save me-1"></i> Update {{ ucfirst($language->name) }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
    @endif
</div>


                </div>
            </div>
</div>
@endsection

@extends('admin.layouts.master')
@section('title', 'Language Translations')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header">
                    <h5 style="display: inline">Language Translations</h5>
                    <a href="#" class="btn btn-primary"  id="add-translate" style="float: right" data-type="constraint">Add</a>
                </div>
                <div class="card-body">
                    @if (session()->has('green'))
                        <div class="alert alert-success">{{ session('green') }}</div>
                    @elseif(session()->has('red'))
                        <div class="alert alert-danger">{{ session('red') }}</div>
                    @endif
                     <table class="hover dataTable" id="example-style-4" role="grid"
                                    aria-describedby="example-style-4_info">
                        <thead>
                            <tr>
                                <th>Language</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Key</th>
                                <th>value</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($translations as $translation)
                                <tr>
                                    <td>{{ isset($translation->language->name) ? $translation->language->name :"" }}</td>
                                    <td>{{ $translation->translatable_type }}</td>
                                    <td>{{ isset($translation->translatable->title) ? $translation->translatable->title :"" }}</td>
                                    <td>{{ $translation->meta_key }}</td>
                                    <td>{{ $translation->meta_value }}</td>
                                    <td>
                                        <a href="#" class="btn btn-success"  id="edit-translate"
                                            data-id="{{ $translation->id }}" data-type="constraint">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                         <a class="btn btn-danger theme delete-btn"
                                                        href="javascript:void(0);"
                                                        data-id=""
                                                        data-action="{{ route('admin.language-translations.delete', $translation->id) }}"
                                                        type="button" data-original-title="btn btn-danger " title=""
                                                        data-bs-original-title=""><i class="fa fa-trash"></i>
                                         </a>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

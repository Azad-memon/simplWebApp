@extends('admin.layouts.master')
@section('title', 'Branch Settings')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6"> <!-- Smaller width for clean UI -->
            <div class="card shadow-sm border-0">

                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Branch Settings</h4>
                </div>

                <div class="card-body">

                    <form action="{{ route('badmin.settings.update') }}" method="POST">
                        @csrf

                        {{-- Printer IP --}}
                        <div class="mb-3">
                            <label for="printer_ip" class="form-label fw-bold">Printer IP</label>
                            <input type="text"
                                   class="form-control"
                                   id="printer_ip"
                                   name="printer_ip"
                                   value="{{ $settings->printer_ip ?? '' }}"
                                   placeholder="e.g. 192.168.1.50">
                        </div>

                        {{-- Printer Port --}}
                        <div class="mb-3">
                            <label for="printer_port" class="form-label fw-bold">Printer Port</label>
                            <input type="text"
                                   class="form-control"
                                   id="printer_port"
                                   name="printer_port"
                                   value="{{ $settings->printer_port ?? '9100' }}">
                        </div>

                        {{-- Extra Settings --}}
                        {{-- <div class="mb-3">
                            <label for="settings" class="form-label fw-bold">Extra Settings (JSON)</label>
                            <textarea class="form-control"
                                      id="settings"
                                      name="settings"
                                      rows="4"
                                      placeholder='{"key": "value"}'>{{ $settings->settings ?? '{}' }}</textarea>
                        </div> --}}

                        <button type="submit" class="btn btn-primary w-100">
                            Update Settings
                        </button>

                    </form>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection

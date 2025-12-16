@extends('admin.layouts.master')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-dark text-white text-center">
                    <h4 class="mb-0">✨ Popup Settings</h4>
                </div>
                <div class="card-body p-4">

                    @if(!$popup)
                        {{-- Create Form --}}
                        <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Image Upload --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Popup Image <span class="text-danger">*</span></label>

                                <div class="custom-file">
                                    <input type="file" name="image" id="imageInput" class="form-control d-none" accept="image/*" required>
                                    <label for="imageInput" class="btn btn-outline-dark w-100">
                                        <i class="bi bi-upload"></i> Choose Image
                                    </label>
                                </div>

                                {{-- Preview Box --}}
                                <div class="mt-3 text-center">
                                    <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow d-none" style="max-height: 250px;">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-dark w-100 mt-3">
                                <i class="bi bi-plus-circle"></i> Create Popup
                            </button>
                        </form>
                    @else
                        {{-- Update Form --}}
                        <form action="{{ route('admin.popup.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            {{-- Current Image --}}
                            <div class="mb-3 text-center">
                                <label class="form-label fw-bold">Current Image</label><br>
                                <img src="{{ $popup->image }}" alt="Popup Image" class="img-fluid rounded shadow" style="max-height: 250px;">
                            </div>

                            {{-- Change Image --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Change Image</label>
                                <div class="custom-file">
                                    <input type="file" name="image" id="imageInput" class="form-control d-none" accept="image/*">
                                    <label for="imageInput" class="btn btn-outline-success w-100">
                                        <i class="bi bi-upload"></i> Choose New Image
                                    </label>
                                </div>

                                {{-- Preview Box --}}
                                <div class="mt-3 text-center">
                                    <img id="previewImage" src="" alt="Preview" class="img-fluid rounded shadow d-none" style="max-height: 250px;">
                                </div>
                            </div>

                            {{-- Status --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold">Status</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ $popup->is_active == 1 ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ $popup->is_active == 0 ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100 mt-3">
                                <i class="bi bi-arrow-repeat"></i> Update Popup
                            </button>
                        </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>

{{-- JS for Image Preview --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const imageInput = document.getElementById("imageInput");
        const previewImage = document.getElementById("previewImage");

        if(imageInput){
            imageInput.addEventListener("change", function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        previewImage.src = e.target.result;
                        previewImage.classList.remove("d-none");
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endsection

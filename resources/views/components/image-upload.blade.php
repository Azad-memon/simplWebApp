<div class="upload-wrapper text-center position-relative">
    {{-- Preview Image --}}
     <img
        id="preview-image"
        src="{{ $value ? $value : '#' }}"
        alt="Preview"
        style="{{ $value ? '' : 'display:none;' }}
               {{ isset($is_banner) && $is_banner ? 'max-height: 400px; max-width:1200px;' : 'max-height: 150px;' }}"
        class="img-fluid mb-2"
    >

    {{-- Upload Circle Label (initially visible only if no image) --}}
    @if(!$value)
        <label for="{{ $id }}" class="upload-circle" id="upload-trigger">
            Click to Upload<br>Image
        </label>
    @endif

    {{-- File Input --}}
    <input
        type="file"
        name="{{ $name }}"
        id="{{ $id }}"
        accept="image/*"
        style="display:none"
    >


  {{-- Edit & Remove Icons --}}
    <span class="edit-icon upload-icon {{   $value ? $value : 'hide' }}" title="Edit"  data-target="{{ $id }}"  ><i class="fas fa-edit"></i></span>
     @if ($value)
     <input type="hidden" name="hidden_{{ $name }}" value="{{ $value }}">
     @endif

</div>

<script>
$(document).on('click', '.edit-icon', function () {
    const inputId = $(this).data('target');
    $("#" + inputId).trigger('click');
});

// File change → preview update
$(document).on('change', '.upload-wrapper input[type="file"]', function (e) {
    const file = e.target.files[0];
    const wrapper = $(this).closest('.upload-wrapper');
    const preview = wrapper.find('img[id^="preview-image"]');
    const trigger = wrapper.find('label[id^="upload-trigger"]');
    const editIcon = wrapper.find('.edit-icon');

    if (file) {
        const reader = new FileReader();
        reader.onload = function (evt) {
            preview.attr('src', evt.target.result).show();
            trigger.hide();
            editIcon.removeClass('hide');
        };
        reader.readAsDataURL(file);
    }
});
</script>


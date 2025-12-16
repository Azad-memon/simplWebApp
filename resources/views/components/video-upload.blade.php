<style>
.video-upload-circle {
    width: 150px;
    height: 150px;
    border: 2px dashed #ccc;

    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    cursor: pointer;
    margin: 10px auto;
    background-color: #f9f9f9;
    position: relative;
}

.video-upload-circle:hover {
    background-color: #f0f0f0;
}

.video-upload-circle input[type="file"] {
    display: none;
}

.video-preview {
    position: relative;
    display: inline-block;
}

.video-preview video {
    width: 150px;
    height: 150px;

    object-fit: cover;
}


.video-edit-icon {
    position: absolute;
    top: 5px;
    right: 5px;
    /* background-color: rgba(0, 0, 0, 0.6); */
    color: white;
    padding: 6px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: none;
    z-index: 10;
}

.video-preview:hover .video-edit-icon {
    display: block;
}
.hide {
     display: none;
}
.video-remove-icon {
    position: absolute;
    top: 5px;
    left: 5px;
    /* background-color: rgba(255, 0, 0, 0.7); */
    color: white;
    padding: 6px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 14px;
    display: none;
    z-index: 10;
}

.video-preview:hover .video-edit-icon,
.video-preview:hover .video-remove-icon {
    display: block;
}

</style>

<div class="video-upload text-center">
    @if ($value)
        <input type="hidden" name="hidden_{{ $name }}" value="{{ $value }}" class="hidden-video-path">
        <div class="video-preview">
            <video controls>
                <source src="{{ asset($value) }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>

            {{-- Edit Icon --}}
            <div class="video-edit-icon" title="Edit Video">
                <i class="fas fa-edit"></i>
            </div>

            {{-- Remove Icon --}}
            <div class="video-remove-icon" title="Remove Video">
                <i class="fas fa-trash-alt"></i>
            </div>
        </div>
    @endif

    {{-- Upload Circle --}}
    <label class="video-upload-circle @if ($value) hide @endif">
        <span style="font-size: 13px; color: #999;">Click to Upload Video</span>
        <input type="file" id="{{ $id }}" name="{{ $name }}" accept="video/*" style="display: none" />
    </label>
</div>



<script>
$(document).ready(function () {
$('.video-upload').each(function () {
    const $uploadWrapper = $(this);
    const $input = $uploadWrapper.find('input[type="file"]');
    const $label = $uploadWrapper.find('.video-upload-circle');

    // On file select (new video)
    $input.off('change').on('change', function () {
        const file = this.files[0];
        if (file && file.type.startsWith('video/')) {
            $label.addClass('hide');
            $uploadWrapper.find('.video-preview').remove();

            const videoURL = URL.createObjectURL(file);
            const $video = $('<video>', {
                controls: true,
                src: videoURL,
                css: {
                    width: '150px',
                    height: '150px',
                    objectFit: 'cover'
                }
            });


            const $editIcon = $('<div>', {
                class: 'video-edit-icon',
                html: '<i class="fas fa-edit"></i>'
            });


            const $removeIcon = $('<div>', {
                class: 'video-remove-icon',
                html: '<i class="fas fa-trash-alt"></i>'
            });


            const $preview = $('<div>', { class: 'video-preview' })
                .append($video)
                .append($editIcon)
                .append($removeIcon);

            $label.before($preview);
        }
    });


    $uploadWrapper.on('click', '.video-remove-icon', function () {
        $uploadWrapper.find('.video-preview').remove();
        $label.removeClass('hide');
        $input.val(null);
        $uploadWrapper.find('.hidden-video-path').val('');
    });

    $uploadWrapper.on('click', '.video-edit-icon', function (e) {
        e.stopPropagation();
        e.preventDefault();
        $input.click();
    });
});
});





</script>


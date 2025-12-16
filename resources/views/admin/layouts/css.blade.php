

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/font-awesome.css')}}">
<!-- ico-font-->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/icofont.css')}}">
<!-- Themify icon-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/themify.css')}}">
<!-- Flag icon-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/flag-icon.css')}}">
<!-- Feather icon-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/feather-icon.css')}}">
<!-- Plugins css start-->
@yield('css')
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/scrollbar.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/jsgrid.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/select2.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/date-picker.css')}}">
<!-- Bootstrap css-->

<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/bootstrap.css')}}">
<!-- App css-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/style.css')}}">

<link id="color" rel="stylesheet" href="{{asset('assets/css/color-1.css')}}" media="screen">
<!-- Responsive css-->
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/responsive.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('assets/css/vendors/datatables.css')}}">

<!-- include summernote css/js -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-bs5.min.css" />
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/vendors/photoswipe.css')}}">
<style>
    .btn-primary {
    background-color: #2c323f !important;
    border-color: #ffffff !important;
}
.page-wrapper.compact-wrapper .page-body-wrapper div.sidebar-wrapper .sidebar-main .sidebar-links .simplebar-wrapper .simplebar-mask .simplebar-content-wrapper .simplebar-content>li .sidebar-link.active {
    -webkit-transition: all 0.5s ease;
    transition: all 0.5s ease;
    position: relative;
    margin-bottom: 10px;
    background-color: #5a586c;
}
.active1{
    background-color: #7366ff;

}
    .ck-editor__editable {
    min-height: 200px;
}


  .upload-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 20px;
        flex-direction: column;
    }

    .upload-circle {
        width: 120px;
        height: 120px;
        border: 2px dashed #ccc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #aaa;
        font-size: 14px;
        cursor: pointer;
        text-align: center;
    }

    #preview-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }


.upload-wrapper {
    position: relative;
    width: 130px;
    height: 130px;
    margin: auto;
}

.upload-image {
    width: 130px;
    height: 130px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #ddd;
    display: block;
    position: relative;
    z-index: 1;
}

/* Shared icon styles */
.upload-icon {
    position: absolute;
    width: 30px;
    height: 30px;
    background: white;
    border-radius: 50%;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    z-index: 2;
    font-size: 14px;
    transition: all 0.2s;
    opacity: 0;
}

/* Position icons inside the image circle */
.upload-wrapper:hover .upload-icon {
    opacity: 1;
}

/* Edit icon (top-right) */
.edit-icon {
    top: 5px;
    right: 5px;
    color: #007bff;
}

/* Cross icon (top-left) */
.cross-icon {
    top: 5px;
    left: 5px;
    color: #dc3545;
}
.hide {
    display: none;
}


.custom-image-container-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    justify-content: center;
}

.custom-image-container {
    position: relative;
    border: 1px solid #ddd;
    border-radius: 6px;
    overflow: hidden;
    width: 50px;
    height: 50px;
}

.custom-img-responsive {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
    border-radius: 4px;
    transition: transform 0.3s ease;
}

.custom-img-responsive:hover {
    transform: scale(1.05);
}


</style>

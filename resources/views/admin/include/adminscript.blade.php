<script>
    function initializeMap(longitude, latitude, mapId) {
        //console.log("Initializing map with coordinates:", longitude, latitude);
        if (typeof google === 'undefined' || typeof google.maps === 'undefined') {
            console.error('Google Maps API has not loaded.');
            return;
        }

        const mapOptions = {
            center: {
                lat: latitude,
                lng: longitude
            },
            zoom: 15,
        };

        const mapElement = document.getElementById(mapId);
        if (!mapElement) {
            console.error(`Element with ID '${mapId}' not found.`);
            return;
        }

        const map = new google.maps.Map(mapElement, mapOptions);
        // console.log("Map initialized:", map);

        new google.maps.Marker({
            position: {
                lat: latitude,
                lng: longitude
            },
            map: map,
            title: "Location",
        });
    }

    let map, marker;

    function initMap(longitude = "", latitude = "") {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                let user_longitude = parseFloat(position.coords.longitude);
                let user_latitude = parseFloat(position.coords.latitude);

                if (longitude !== "") user_longitude = parseFloat(longitude);
                if (latitude !== "") user_latitude = parseFloat(latitude);
                const userLocation = {
                    lat: user_latitude,
                    lng: user_longitude,
                };
                map = new google.maps.Map(document.getElementById("map"), {
                    center: userLocation,
                    zoom: 13,
                    mapTypeId: "roadmap",
                    mapTypeControl: false,
                    fullscreenControl: false
                });

                marker = new google.maps.Marker({
                    map: map,
                    draggable: true,
                    position: userLocation,
                    visible: true,
                });

                updateLatLngFields(userLocation.lat, userLocation.lng);

                const input = document.getElementById("pac-input");
                const searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                map.addListener("bounds_changed", () => {
                    searchBox.setBounds(map.getBounds());
                });

                searchBox.addListener("places_changed", () => {
                    const places = searchBox.getPlaces();

                    if (places.length == 0) return;

                    const place = places[0];
                    if (!place.geometry || !place.geometry.location) return;

                    map.setCenter(place.geometry.location);
                    map.setZoom(15);
                    marker.setPosition(place.geometry.location);
                    marker.setVisible(true);

                    updateLatLngFields(place.geometry.location.lat(), place.geometry.location.lng());
                    document.getElementById("selected-address").innerText = place.formatted_address;
                    $('#location-input').val(place.formatted_address)
                });

                marker.addListener("dragend", (event) => {
                    updateLatLngFields(event.latLng.lat(), event.latLng.lng());
                });
            }, () => {
                handleLocationError(true, map.getCenter());
            });
        } else {
            handleLocationError(false, map.getCenter());
        }
    }

    function updateLatLngFields(lat, lng) {
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;
    }

    function handleLocationError(browserHasGeolocation, pos) {
        alert(browserHasGeolocation ?
            "Error: The Geolocation service failed." :
            "Error: Your browser doesn't support geolocation.");
    }

    async function submitFormAjax(formSelector, submitButtonSelector, options = {}) {
        const form = $(formSelector);
        const submitButton = $(submitButtonSelector);

        // store original button text if not stored already
        if (!submitButton.data('original-text')) {
            submitButton.data('original-text', submitButton.html());
        }
        const originalButtonText = submitButton.data('original-text');

        const actionUrl = form.attr('action');
        const method = form.attr('method') || 'POST';
        const formData = new FormData(form[0]);

        // determine modal either from options or nearest ancestor
        const modal = options.modalSelector ? $(options.modalSelector) : form.closest('.modal');
        const modalLoader = modal.length ? modal.find('.modal-loader') : $(); // jQuery empty set if no modal

        $.ajax({
            url: actionUrl,
            type: method,
            headers: options.token || {}, // pass csrf header here if needed
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                // show loader only for this modal
                if (modalLoader.length) {
                    modalLoader.removeClass('d-none');
                }

                // disable only inputs inside the form (keeps modal header close button active)
                form.find(':input').prop('disabled', true);

                // disable submit button and show inline spinner
                submitButton.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Please wait...'
                );
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                if (modal.length) modal.modal('hide');
                form[0].reset();


                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors || {};
                    // remove old errors
                    form.find('.text-danger').remove();
                    $.each(errors, function(key, value) {
                        var input = form.find('[name="' + key + '"]');
                        if (input.length) {
                            input.after('<span class="text-danger">' + value[0] + '</span>');
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong. Please try again.'
                    });
                }
            },
            complete: function() {
                // hide loader and re-enable form
                if (modalLoader.length) {
                    modalLoader.addClass('d-none');
                }
                form.find(':input').prop('disabled', false);

                // restore submit button
                submitButton.prop('disabled', false).html(originalButtonText);
            }
        });
    }


    // async function submitFormAjax(formSelector, submitButtonSelector, options = {}) {
    //     const form = $(formSelector);

    //     const submitButton = $(submitButtonSelector);
    //     const originalButtonText = submitButton.html();
    //     const actionUrl = form.attr('action');
    //     const method = form.attr('method') || 'POST';
    //     const formData = new FormData(form[0]);

    //     $.ajax({
    //         url: actionUrl,
    //         type: method,
    //         headers: options.token,
    //         data: formData,
    //         contentType: false, // ✅ important for file upload
    //         processData: false, // ✅ important for file upload
    //         success: function(response) {

    //             Swal.fire({
    //                 icon: 'success',
    //                 title: 'Success',
    //                 text: response.message,
    //                 showConfirmButton: true
    //             }).then((result) => {
    //                 if (result.isConfirmed) {
    //                     location.reload();
    //                 }
    //             });

    //             $(options.modalSelector).modal('hide');

    //             form[0].reset();

    //         },
    //         error: function(xhr) {
    //             if (xhr.status === 422) {

    //                 var errors = xhr.responseJSON.errors;

    //                 // First, remove all existing error messages
    //                 form.find('.text-danger').remove();
    //                 $.each(errors, function(key, value) {
    //                     var input = form.find('[name="' + key + '"]');
    //                     input.after('<span class="text-danger">' + value[0] + '</span>');
    //                 });
    //             } else {

    //                 Swal.fire({
    //                     icon: 'error',
    //                     title: 'Error',
    //                     text: 'Something went wrong. Please try again.'
    //                 });
    //             }
    //         }
    //     });
    // }
    //   //Languages
    //   document.addEventListener('DOMContentLoaded', function () {
    //     const languageModal = document.getElementById('languageModal');
    //     if (languageModal) {
    //   languageModal.addEventListener('show.bs.modal', function (event) {
    //       const form = languageModal.querySelector('#languageForm');
    //       const button = event.relatedTarget;
    //       const id = button.getAttribute('data-id');
    //       const name = button.getAttribute('data-name');
    //       const code = button.getAttribute('data-code');
    //       const storeUrl = form.getAttribute('data-store-url');
    //       const updateUrlTemplate = form.getAttribute('data-update-url');

    //       const modalTitle = languageModal.querySelector('.modal-title');
    //       const languageIdInput = languageModal.querySelector('#language-id');
    //       const languageNameInput = languageModal.querySelector('#name');
    //       const languageCodeInput = languageModal.querySelector('#code');
    //       const isDefaultCheckbox = languageModal.querySelector('#is_default');

    // console.log(isDefaultCheckbox);

    //     if (id) {
    //       modalTitle.textContent = 'Edit Language';
    //       languageIdInput.value = id;
    //       languageNameInput.value = name;
    //       languageCodeInput.value = code;
    //       const updateUrl = updateUrlTemplate.replace('null', id);
    //       isDefaultCheckbox.checked = isDefault === '1';
    //       form.action = updateUrl;

    //     } else {
    //       modalTitle.textContent = 'Add Language';
    //       languageIdInput.value = '';
    //       languageNameInput.value = '';
    //       languageCodeInput.value = '';
    //       form.action =storeUrl;
    //     }
    //   });
    // }//end languageModal
    // });
    $(document).on('change', '#is_default', function() {
        if ($(this).is(':checked')) {
            $(this).prop('checked', true);
            $("#is_default_name").val(1);
        } else {
            // Set value to 0, and make sure it still submits when unchecked (optional with hidden input)
            $(this).prop('checked', false);
            $("#is_default_name").val(0);
        }
    });
    $(document).on("click", "#add-language", async function(event) {
        var data_url = "<?= route('admin.language.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {

                $("#addtranslationModal").html(data);
                $("#languageModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-language", async function(event) {
        const id = $(this).attr("data-id");

        var data_url = "<?= route('admin.language.edit', ['id' => 'null']) ?>";
        const updateUrl = data_url.replace('null', id);

        $.ajax({
            url: updateUrl,
            success: function(data) {

                $("#addtranslationModal").html(data);
                $("#languageModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#languageForm', function(e) {
        e.preventDefault();
        submitFormAjax('#languageForm', '#custom-save-button', {
            modalSelector: '#languageModal',
            successMessage: 'Language saved successfully!',
            errorMessage: 'Failed to save language. Please check the form for errors.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-btn');

        deleteButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const encryptedId = this.getAttribute('data-id');
                let action = this.getAttribute('data-action') + encryptedId;
                if (encryptedId != "") {
                    if (this.hasAttribute('data-branchid')) {
                        const branchId = this.getAttribute('data-branchid');
                        action += '/' + branchId;
                    }
                } else {
                    action = this.getAttribute('data-action');

                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "This action cannot be undone.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Redirect to the delete route
                        window.location.href = action;
                    }
                });
            });
        });
    });
    //End Languages

    // Translate
    $(document).on("click", "#add-translate", async function(event) {

        const type = $(this).attr("data-type");
        const translatable_id = $(this).attr("data-translatable_id") ? $(this).attr(
            "data-translatable_id") : "";


        const data_url = "<?= route('admin.language-translation.add') ?>" +
            "?type=" + encodeURIComponent(type) +
            "&translatable_id=" + encodeURIComponent(translatable_id);

        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#translationModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-translate", async function(event) {
        const id = $(this).attr("data-id");
        const type = $(this).attr("data-type") ? $(this).attr("data-type") : "";
        const translatable_id = $(this).attr("data-translatable_id") ? $(this).attr(
            "data-translatable_id") : "";
        var data_url = "<?= route('admin.language-translation.edit', ['id' => 'null']) ?>" + "?type=" +
            encodeURIComponent(type) +
            "&translatable_id=" + encodeURIComponent(translatable_id);;
        const updateUrl = data_url.replace('null', id);
        $.ajax({
            url: updateUrl,
            success: function(data) {

                $("#addtranslationModal").html(data);
                $("#translationModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#translationForm', function(e) {
        e.preventDefault();
        submitFormAjax('#translationForm', '#custom-save-button', {
            modalSelector: '#translationModal',
            successMessage: 'Language saved successfully!',
            errorMessage: 'Failed to save language. Please check the form for errors.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    function updateTranslatableOptions(selectedType, selectedId = null) {
        const $idGroup = $('#translatable-id-group');
        const $idSelect = $('#translatable_id');

        $idSelect.html('<option value="">Loading...</option>');
        $idGroup.show();

        if (selectedType === 'App\\Models\\Constraint') {
            $.ajax({
                url: "{{ route('admin.constraints.dropdown') }}",
                method: 'GET',
                success: function(data) {
                    $idSelect.html('<option value="">-- Select Constraint --</option>');
                    $.each(data, function(index, item) {
                        $idSelect.append('<option value="' + item.id + '" ' + (item.id ==
                            selectedId ? 'selected' : '') + '>' + item.title + '</option>');
                    });
                },
                error: function() {
                    $idSelect.html('<option value="">Failed to load data</option>');
                }
            });
        } else if (selectedType === 'App\\Models\\Product') {
            $idSelect.html('<option value="">-- Select Product  --</option>');
        } else {
            $idGroup.hide();
        }
    }
    $(document).on('change', '#translatable_type', function() {
        const selectedType = $(this).val();
        updateTranslatableOptions(selectedType);
    });

    // On page load (for edit mode)
    const initialType = $('#translatable_type').val();
    const selectedId = "{{ $translation->translatable_id ?? '' }}";
    if (initialType) {
        updateTranslatableOptions(initialType, selectedId);
    }
    //End translate
    $(document).on("click", "#add-branch", async function(event) {
        var data_url = "<?= route('admin.branch.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {

                $("#addtranslationModal").html(data);
                $("#branchModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-branch", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.branch.edit', ['id' => 'null']) }}";
        const final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#branchesModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#saveuserform', function(e) {
        e.preventDefault();
        var form = $(this);
        submitFormAjax(form, '', {
            modalSelector: '#branchuserModal',
            successMessage: 'Staff added successfully',
            errorMessage: 'Failed to add staff',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    //constraints
    $(document).on("click", "#add-constraints", async function(event) {
        var data_url = "<?= route('admin.constraint.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#constraintModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-constraints", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.constraint.edit', ['constraint' => ':id']) }}";
        var final_url = data_url.replace(':id', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#constraintModal").modal("show");
            }
        })
    });

    $(document).on('submit', '#constraintForm', function(e) {
        e.preventDefault();

        submitFormAjax('#constraintForm', '#custom-save-button', {
            modalSelector: '#constraintModal',
            successMessage: 'Constraint saved successfully!',
            errorMessage: 'Failed to save constraint.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //End  constraints
    //branch users
    $(document).on("click", "#add-branch-user", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "<?= route('admin.branch.branchadmin.add', ['null']) ?>";
        const final_url = data_url.replace('null', id);

        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#branchuserModal").modal("show");
            }
        })
    });
    $(document).on('change', '.user-status-checkbox', function() {
        const checkbox = $(this);
        const span = checkbox.siblings('.switch-state');
        const anchor = checkbox.closest('.toggle-user-status');

        const isActive = checkbox.is(':checked');

        // Detect if values are 'active/inactive' or '1/0'
        const currentStatus = anchor.data('status');
        const statusType = typeof currentStatus === 'string' && (currentStatus === 'active' || currentStatus ===
                'inactive') ?
            'text' :
            'numeric';

        const newStatus = statusType === 'text' ?
            (isActive ? 'active' : 'inactive') :
            (isActive ? 1 : 0);

        const postUrl = anchor.data('url'); // dynamically passed URL
        anchor.attr('title', isActive ? 'Active' : 'Inactive');
        var type = "";
        type = anchor.data('type');
        if (type !== undefined && type !== null && type !== '') {
            type = type;
        }
        $.ajax({
            url: postUrl, // replace with your route
            type: 'POST',
            data: {
                id: anchor.data('id'),
                status: newStatus,
                type: type,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.message != "") {
                    // Toggle color class
                    span.removeClass('bg-success bg-danger')
                        .addClass(isActive ? 'bg-success' : 'bg-danger');

                    // Update data-status attribute
                    anchor.attr('data-status', newStatus);
                    // Optionally show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                        showConfirmButton: true
                    });
                }
            },
            error: function() {
                //   Swal.fire({
                //         icon: 'error',
                //         title: 'Error',
                //         text: 'Something went wrong. Please try again.'
                //         });
                checkbox.prop('checked', !isActive); // Revert without retriggering change
            }
        });
    });

    $(document).on("click", "#edit-user", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.branch.branchadmin.edituser', ['id' => 'null']) }}";
        const final_url = data_url.replace('null', id);
        // alert(final_url);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#branchuserModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#branchForm', function(e) {
        e.preventDefault();
        submitFormAjax('#branchForm', '#custom-save-button', {
            modalSelector: '#branchModal',
            successMessage: '',
            errorMessage: '',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });


    // end branch user
    //--------------------category script start  ------------//
    $(document).on("click", "#add-category", async function(event) {
        var data_url = "<?= route('admin.category.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#categoryModal").modal("show");
            }
        })
    });

    $(document).on("click", ".edit-category", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.category.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#categoryModal").modal("show");
            }
        })
    });

    $(document).on('click', '.toggle-icon', function() {
        const tr = $(this).closest('tr');
        const id = tr.data('id');

        // Toggle icon
        $(this).toggleClass('fa-plus-square fa-minus-square');

        // Toggle visibility of child rows
        $(`tr[data-parent="${id}"]`).toggle();
    });

    $('#example-style-4-cat').DataTable({
        order: [
            [0, 'asc']
        ], // Sort by hidden ID column
        columnDefs: [{
                targets: 0,
                visible: false
            } // Hide ID column
        ]
    });

    $(document).on('submit', '#categoryForm', function(e) {
        e.preventDefault();

        submitFormAjax('#categoryForm', '#custom-save-button', {
            modalSelector: '#categoryModal',
            successMessage: 'Category saved successfully!',
            errorMessage: 'Failed to save category.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //--------------------category script start  ------------//
    //--ingredients ------//
    $(document).on("click", "#add-ingredients", async function(event) {
        let categoryName = null;
        let categoryId = null;
        var data_url = "<?= route('admin.ingredient.add') ?>";
        categoryId = $(this).data("category-id");
        categoryName = $(this).data("category-name");

        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#IngredientsModal").modal("show");

                const $select = $("#IngredientsModal").find("#category_id");
                // If select2 is initialized, destroy it first (avoid dup)
                if ($.fn.select2 && $select.hasClass("select2-hidden-accessible")) {
                    try {
                        $select.select2("destroy");
                    } catch (e) {}
                }

                if (categoryId && categoryName) {
                    // Keep only the selected category and disable select (locked)
                    $select.empty()
                        .append($("<option>", {
                            value: categoryId,
                            text: categoryName,
                            selected: true
                        }));


                    // Init select2 with no search (one option) and dropdown inside modal
                    if ($.fn.select2) {
                        $select.select2({
                            dropdownParent: $("#IngredientsModal"),
                            width: "100%",
                            minimumResultsForSearch: Infinity
                        });
                    }
                } else {

                    if ($.fn.select2) {
                        $select.select2({
                            dropdownParent: $("#IngredientsModal"),
                            width: "100%"
                        });
                    }
                }

            }
        })
    });
    $(document).on("click", "#edit-ingredients", async function(event) {
        let categoryName = null;
        let categoryId = null;
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.ingredient.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        categoryId = $(this).data("category-id");
        categoryName = $(this).data("category-name");

        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#IngredientsModal").modal("show");

                const $select = $("#IngredientsModal").find("#category_id");
                // If select2 is initialized, destroy it first (avoid dup)
                if ($.fn.select2 && $select.hasClass("select2-hidden-accessible")) {
                    try {
                        $select.select2("destroy");
                    } catch (e) {}
                }

                if (categoryId && categoryName) {
                    // Keep only the selected category and disable select (locked)
                    $select.empty()
                        .append($("<option>", {
                            value: categoryId,
                            text: categoryName,
                            selected: true
                        }));


                    // Init select2 with no search (one option) and dropdown inside modal
                    if ($.fn.select2) {
                        $select.select2({
                            dropdownParent: $("#IngredientsModal"),
                            width: "100%",
                            minimumResultsForSearch: Infinity
                        });
                    }
                } else {

                    if ($.fn.select2) {
                        $select.select2({
                            dropdownParent: $("#IngredientsModal"),
                            width: "100%"
                        });
                    }
                }
            }
        })
    });

    $(document).on('submit', '#ingredientForm', function(e) {
        e.preventDefault();

        submitFormAjax('#ingredientForm', '#custom-save-button', {
            modalSelector: '#IngredientsModal',
            successMessage: 'ingredient saved successfully!',
            errorMessage: 'Failed to save Ingredients.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).ready(function() {
        $('#standard-table').DataTable();
        $('#custom-table').DataTable();
    });

    $('#standardIngredientsModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var customIngId = button.data('id'); // Extract info from data-* attributes
        var modal = $(this);
        modal.find('#saveSelectedIngredients').data('custom-ing-id', customIngId);
        var preSelectedIngredients = button.data('standard-ingredients') || [];
    });
    $(document).on('click', '[data-bs-target="#standardIngredientsModal"]', function() {
        // Reset all checkboxes first
        $('input[name="selected_ingredients[]"]').prop('checked', false);

        // Get selected IDs from data attribute
        let selected = $(this).data('standard-ingredients');

        if (selected && Array.isArray(selected)) {
            selected.forEach(function(id) {
                $('input[name="selected_ingredients[]"][value="' + id + '"]').prop('checked', true);
            });
        }
    });


    $('#saveSelectedIngredients').click(function() {
        var customIngId = $(this).data('custom-ing-id');
        var selectedIngredients = $('input[name="selected_ingredients[]"]:checked').map(function() {
            return $(this).val();
        }).get();
        // console.log('Custom Ingredient ID:', customIngId);
        // console.log('Selected Standard Ingredients:', selectedIngredients);

        $.ajax({
            url: '{{ route('admin.ingredient.attachStandard') }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                ingredients: selectedIngredients,
                custom_ing_id: customIngId
            },
            success: function(response) {
                if (response.message) {
                    $('#standardIngredientsModal').modal('hide');
                    location.reload();
                } else {
                    // alert('Failed to link standard ingredients.');
                }
            }
        });
    });

    //--product ------//
    $(document).on("click", "#add-product", async function(event) {
        var data_url = "<?= route('admin.product.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#productModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-product", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.product.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#productModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#productForm', function(e) {
        e.preventDefault();

        submitFormAjax('#productForm', '#custom-save-button', {
            modalSelector: '#productModal',
            successMessage: 'Product saved successfully!',
            errorMessage: 'Failed to save Product.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    let productName = '';

    $(document).on('input', '#product-name', function() {
        productName = $(this).val();

        // Convert to slug-friendly text
        let slug = productName
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '') // Remove special chars
            .replace(/\s+/g, '-') // Replace spaces with -
            .replace(/-+/g, '-'); // Remove multiple -

        $('#slug').val(slug);
    });

    $(document).on('change', '#cat_id', function() {
        let selectedOption = $(this).find('option:selected');
        let catText = selectedOption.text().trim();

        // Use the already typed product name (or current slug base)
        let slugBase = productName || $('#product-name').val();

        let slug = slugBase
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        // Combine with category text (also slugified)
        let catSlug = catText
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-');

        let finalSlug = `${catSlug}-${slug}`;
        $('#slug').val(finalSlug);
    });

    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });

    //-- End product ------//
    //--Product variants ---//
    $(document).on("click", "#add-variant", async function(event) {
        const productId = $(this).data("product-id");
        const productName = $(this).data("slug");

        var data_url = "{{ route('admin.product.variants.add') }}";

        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#variant-product-id").val(productId);


                $("#sku-varient").val(productName);


                $("#variantModal").modal("show");


                $(document).on("change", "#size", function() {
                    let selectedOption = $(this).find("option:selected");
                    let sizeText = selectedOption.data("sizename")
                        .trim(); // example: "Small (SM)"
                    //let sizeCode = "";


                    // let match = sizeText.match(/\(([^)]+)\)/);
                    // if (match && match[1]) {
                    //     sizeCode = match[1]; // SM
                    // } else {
                    //     sizeCode = selectedOption.text().trim(); // fallback if no code
                    // }


                    let finalSku = productName + "-" + sizeText;
                    $("#sku-varient").val(finalSku);
                });
            }
        });
    });

    $(document).on("click", "#edit-variant", async function(event) {
        const id = $(this).attr("data-id");
        const productId = $(this).data("product-id");
        const productName = $(this).data("slug");

        var data_url =
            "{{ route('admin.product.variants.edit', ['product' => 'null', 'variant' => 'null']) }}";
        const final_url = data_url.replace('null', productId).replace('null', id);

        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#variantModal").modal("show");


                let skuField = $("#sku-varient");
                if (skuField.val().trim() === "") {
                    skuField.val(productName);
                }


                $(document).on("change", "#size", function() {
                    let selectedOption = $(this).find("option:selected");
                    let sizeText = selectedOption.data("sizename")
                        .trim(); // example: "Small (SM)"
                    let sizeCode = "";


                    // let match = sizeText.match(/\(([^)]+)\)/);
                    // if (match && match[1]) {
                    //     sizeCode = match[1];
                    // } else {
                    //     sizeCode = selectedOption.text().trim(); // fallback
                    // }

                    // Final SKU: productname-sizecode
                    let finalSku = productName + "-" + sizeText;
                    $("#sku-varient").val(finalSku);
                });
            }
        });
    });

    $(document).on('submit', '#variantForm', function(e) {
        e.preventDefault();

        submitFormAjax('#variantForm', '#custom-save-button', {
            modalSelector: '#variantModal',
            successMessage: 'Product variant saved successfully!',
            errorMessage: 'Failed to save Product variant.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //deals
    $(document).on("click", "#add-deal", async function(event) {
        var data_url = "<?= route('admin.deals.create') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#dealModal").modal("show");
            }
        })
    });
    $(document).on("click", "#edit-deal", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.deals.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#dealModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#dealForm', function(e) {
        e.preventDefault();

        submitFormAjax('#dealForm', '#custom-save-button', {
            modalSelector: '#dealModal',
            successMessage: 'Deal saved successfully!',
            errorMessage: 'Failed to save Deal.',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    // varient ingredients
    $(document).on("click", "#add-variant-ingredient", async function(event) {
        var data_url = $(this).data("url");
        const variantid = $(this).data("variant-id");
        $.ajax({
            url: data_url,
            data: {
                variant_id: variantid
            },
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#ingredientModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-variant-ingredient", async function(event) {
        const variantId = $(this).data("variant-id");
        const ingredientId = $(this).data("id");
        var data_url =
            "{{ route('admin.product.variants.ingredients.edit', ['variant' => 'null', 'ingredient' => 'null']) }}";
        const final_url = data_url.replace('null', variantId).replace('null', ingredientId);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#ingredientModal").modal("show");
            }
        })
    });

    //languaage form

    $(document).on('submit', '.translation-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('.custom-save-button');

        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: 'Language saved successfully!',
            errorMessage: 'Failed to save language. Please check the form for errors.',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //Size
    $(document).on("click", "#add-size", async function(event) {
        var data_url = "<?= route('admin.size.create') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#sizeModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#size-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('.custom-save-button');

        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $(document).on("click", ".edit-size", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.size.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#sizeModal").modal("show");
            }
        })
    });
    //Unit
    $(document).on("click", "#add-unit", async function(event) {
        var data_url = "<?= route('admin.unit.create') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#unitModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#unit-form', function(e) {
        e.preventDefault();

        const $form = $(this);
        const $button = $form.find('.custom-save-button');

        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $(document).on("click", ".edit-unit", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.unit.edit', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#unitModal").modal("show");
            }
        })
    });

    //addons
    $(document).on("click", "#add-addon", async function(event) {
        var data_url = "<?= route('admin.addons.create') ?>";
        const productid = $(this).data("product-id");
        $.ajax({
            url: data_url,
            data: {
                productid: productid
            },
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addonModal").modal("show");
            }
        })
    });

    $(document).on('submit', '#addon-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).on("click", ".edit-addon", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.addons.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addonmodel").modal("show");
            }
        })
    });
    $(document).on('change', '#category_id_addon', function() {
        var categoryId = $(this).val();
        if (categoryId) {
            var url = '{{ route('admin.addons.getIngredientsByCategory', ':categoryId') }}';
            url = url.replace(':categoryId', categoryId); // Replace :categoryId with actual ID
            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {

                    $('#ingredient_ids').empty();


                    $.each(data, function(key, value) {
                        $('#ingredient_ids').append('<option value="' + value.ing_id +
                            '">' + value.ing_name + '</option>');
                    });
                }
            });
        } else {

            $('#ingredient_ids').empty();
        }
    });
    $(document).on('change', '#category_id_edit', function() {
        var categoryId = $(this).val();
        if (categoryId) {
            var url = '{{ route('admin.addons.getIngredientsByCategory', ':categoryId') }}';
            url = url.replace(':categoryId', categoryId);

            $.ajax({
                url: url,
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var $ingredientSelect = $('#ingredient_ids_edit');
                    $ingredientSelect.empty(); // Clear old options

                    $.each(data, function(key, value) {
                        $ingredientSelect.append(
                            '<option value="' + value.ing_id + '">' + value.ing_name +
                            '</option>'
                        );
                    });

                    // Refresh any select2 if used
                    if ($ingredientSelect.hasClass('select2-hidden-accessible')) {
                        $ingredientSelect.trigger('change');
                    }
                }
            });
        } else {
            $('#ingredient_ids_edit').empty();
        }
    });

    $(document).ready(function() {
        // Hide all dropdowns when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.dropdown').length) {
                $('.dropdown-menu').removeClass('show');
            }
        });

        // Toggle dropdown on button click
        $(document).on('click', '.dropdown-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevent bubbling up

            // Close other dropdowns
            $('.dropdown-menu').not($(this).next('.dropdown-menu')).removeClass('show');

            // Toggle this one
            $(this).next('.dropdown-menu').toggleClass('show');
        });
    });

    //add on product
    $(document).on("click", "#add-addon-product", async function(event) {
        var data_url = "<?= route('admin.addons.addProduct') ?>";
        const productid = $(this).data("product-id");
        $.ajax({
            url: data_url,
            data: {
                productid: productid
            },
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addonProductModal").modal("show");
            }
        })
    });

    $(document).on('submit', '#addon-product-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).on("click", ".edit-addon-product", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.addons.editProductAddon', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addonProductModal").modal("show");
            }
        })
    });

    // $(document).on('shown.bs.modal', '.modal', function () {
    //     // Delay Select2 initialization to ensure modal content is fully loaded
    //     setTimeout(function() {
    //      $(this).find('select').select2({
    //     placeholder: "-- Choose an Option --",
    //     allowClear: true,                      // Allow clear button
    //     minimumInputLength: 2,                 // Minimum characters to start search
    //     theme: "classic",                      // Classic theme (optional)
    //  //  width: '100%'                          // Ensure Select2 is responsive
    // });
    //     }.bind(this), 100);  // 100ms delay
    // });


    // $(document).on('hidden.bs.modal', '.modal', function () {
    //     console.log("Modal closed, destroying Select2");
    //     // Destroy Select2 when modal is closed
    //     $(this).find('select').select2('destroy');
    // });
    //App banner
    $(document).on("click", "#add-banner", async function(event) {
        var data_url = "<?= route('admin.banners.create') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#bannerModal").modal("show");
            }
        })
    });

    $(document).on('submit', '#bannerForm', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).on("click", ".edit-banner", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.banners.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#editBannerModal").modal("show");
            }
        })
    });

    //App Home page product
    $(document).on("click", "#add-app-product", async function(event) {
        var data_url = "<?= route('admin.home.product.create') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#bannerModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-app-product", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.home.product.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#bannerModal").modal("show");
            }
        })
    });
    // Coupon
    $(document).on("click", "#add-coupon", async function(event) {
        var data_url = "<?= route('admin.coupons.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#couponModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-coupon", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.coupons.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#couponModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#coupon-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //cms pages
    $(document).on("click", "#add-page", async function(event) {
        var data_url = "<?= route('admin.cms.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#cmsModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-page", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.cms.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#cmsModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#cms-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //ingredient categories

    $(document).on("click", "#add-ingredients-category", async function(event) {
        var data_url = "<?= route('admin.ingredient.category.add') ?>";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#ingcategoryModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-ingredients-category", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.ingredient.category.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#ingcategoryModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#ingcategory-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    //customers
    $(document).on("click", ".show-customer", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.customer.show', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#customerModal").modal("show");
            }
        })
    });
    $(document).on("click", ".show-loyalty", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.customer.show.showloyalty', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#customerModal").modal("show");
            }
        })
    });
    $(document).on("click", ".show-wallet", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.customer.show', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#customerModal").modal("show");
            }
        })
    });
    //payment methods
    $(document).on("click", ".add-payment", async function(event) {
        var data_url = "{{ route('admin.paymentmethod.create') }}";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#paymentMethodModal").modal("show");
            }
        })
    });
    $(document).on("click", ".edit-payment", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "{{ route('admin.paymentmethod.edit', ['id' => 'null ']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#paymentMethodModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#payment-form', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    $(document).on("click", "#ingredientFormqty", async function(event) {

        event.preventDefault();
        var branch_id = $(this).attr("data-branchId");
        var ing_id = $(this).attr("data-ingId");

        var data_url =
            "{{ route('admin.ingredient.updateQuantity', ['id' => '__id__', 'branchid' => '__branchid__']) }}";
        data_url = data_url.replace('__id__', ing_id).replace('__branchid__', branch_id);
        var quantity = $('#quantity-' + ing_id).val(); // Clear the input field before sending the request
        $.ajax({
            url: data_url,
            type: 'POST',
            data: {
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                $("#IngredientsModal").modal("hide");
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: data.success,
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });

            },
            error: function(xhr, status, error) {
                let errorMsg = 'An error occurred';
                if (xhr.responseJSON && xhr.responseJSON.errors && xhr.responseJSON.errors
                    .quantity) {
                    errorMsg = xhr.responseJSON.errors.quantity.join('<br>');
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    html: errorMsg
                });
                console.error("Error:", error);
            }

        });

    });

    $(document).on('submit', '.ingredient-form-update', function(e) {
        e.preventDefault();
        const $form = $(this);
        const $button = $form.find('.custom-save-button');
        var branch_id = $(this).attr("data-branchId");

        submitFormAjax($form, $button, {
            modalSelector: null, // No modal here
            successMessage: '',
            errorMessage: '',
            resetForm: false,
            reloadPage: false,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });
    $(document).on("click", "#update-branch-ingredient", async function(event) {
        const id = $(this).attr("data-id");
        const bid = $(this).attr("data-branchId");
        const quantity = $(this).attr("data-quantity");
        var data_url = "{{ route('admin.ing-inventory.view', ['id' => ':id', 'bid' => ':bid']) }}";
        var final_url = data_url.replace(':id', id).replace(':bid', bid);

        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#IngredientsModal").modal("show");
            }
        })
    });

    // Branchs
    $(document).on("click", "#addBranchShift", function(event) {
        event.preventDefault();
        const branchid = $(this).attr("data-branchid");
        var data_url = "{{ route('admin.shifts.create', ['branchid' => ':branchid']) }}";
        var final_url = data_url.replace(':branchid', branchid);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addShiftModal").modal("show");
            }
        });
    });
    $(document).on("click", "#editBranchShift", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "<?= route('admin.shifts.edit', ['id' => 'null']) ?>";
        const updateUrl = data_url.replace('null', id);
        $.ajax({
            url: updateUrl,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addShiftModal").modal("show");
            }
        })
    });
    //branch Staff
    $(document).on("click", "#addBranchStaff", function(event) {
        event.preventDefault();
        const branchid = $(this).attr("data-branchid");
        var data_url = "{{ route('admin.staff.create', ['branchid' => ':branchid']) }}";
        var final_url = data_url.replace(':branchid', branchid);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#branchuserModal").modal("show");
            }
        });
    });
    $(document).on("click", "#editdBranchStaff", async function(event) {
        const id = $(this).attr("data-id");
        const branchid = $(this).attr("data-branchid");
        var data_url = "{{ route('admin.staff.edit', ['id' => ':id', 'branchid' => ':branchid']) }}";
        var final_url = data_url.replace(':id', id).replace(':branchid', branchid);
        $.ajax({
            url: final_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#editBranchUserModal").modal("show");
            }
        })
    });

    //Dashboard Chart Data
    $(document).ready(function() {
        function animateNumber(selector, value) {
    $({ countNum: 0 }).animate({ countNum: value }, {
        duration: 1000,
        easing: 'swing',
        step: function() {
            $(selector).text(Math.floor(this.countNum).toLocaleString());
        },
        complete: function() {
            $(selector).text(this.countNum.toLocaleString());
        }
    });
}
        // Load Product Report
        function loadChart(dailyDate = null, monthDate = null, branchId = null) {
            // Show loader
            $("#chartLoader").show();

            $.ajax({
                url: "{{ route('admin.loadProductReport') }}",
                type: "GET",
                data: {
                    daily_date: dailyDate,
                    month_date: monthDate,
                    branch_id: branchId
                },
                success: function(res) {
                    let labels = res.labels;
                    let qty = labels.map(l => Number(res.qty[l] ?? 0));

                    // Clear old chart completely
                    $("#productReportChart").html("");

                    let options = {
                        chart: {
                            type: "bar",
                            height: 300
                        },
                        series: [{
                            name: "Quantity Sold",
                            data: qty
                        }],
                        xaxis: {
                            categories: labels
                        },
                        yaxis: [{
                            title: {
                                text: "Quantity"
                            }
                        }],
                        tooltip: {
                            shared: true,
                            intersect: false
                        }
                    };

                    let chart = new ApexCharts(
                        document.querySelector("#productReportChart"),
                        options
                    );

                    chart.render();

                    // Update stats
                    $("#total_products_sold").text(res.total_qty);
                    $("#total_revenue").text("Rs " + res.total_revenue);
                    $("#top_product").text(res.top_product !== 'N/A' ? res.top_product : 'N/A');
                },
                complete: function() {
                    $("#chartLoader").hide();
                }
            });
        }

        // Load Order Report
        function loadOrderReport(daily = null, month = null, branchId = null) {
            $("#order_loader").show();
            $.ajax({
                url: "{{ route('admin.loadSalesReport') }}",
                type: "GET",
                data: {
                    daily_date: daily,
                    month_date: month,
                    branch_id: branchId
                },
               success: function(res) {
                    animateNumber("#total_orders", res.total_orders);
                    animateNumber("#total_revenue_sales", res.total_revenue);
                    animateNumber("#gross_sale", res.gross_sale);
                    animateNumber("#total_tax", res.total_tax);
                    animateNumber("#cash_sale", res.cash_sale);
                    animateNumber("#card_sale", res.card_sale);
                    animateNumber("#online_sale", res.online_sale);
                    animateNumber("#credit_sale", res.credit_sale);
                    animateNumber("#total_discount", res.total_discount);
                    animateNumber("#total_refund", res.total_refund);
                },
                complete: function() {
                    $("#order_loader").hide();
                }
            });
        }

        // Product Report: Button click
        $("#loadProductReport").on("click", function() {
            let type = $("#report_type").val();
            let dailyDate = type === "daily" ? $("#daily_date").val() : null;
            let monthDate = type === "monthly" ? $("#month_date").val() : null;
            let branchId = $("#branch_id").val(); // Get selected branch

            loadChart(dailyDate, monthDate, branchId);
        });

        // Product Report: Branch change
        // $("#branch_id").on("change", function() {
        //     $("#loadProductReport").click();
        // });

        // Product Report: Show/hide inputs
        $('#report_type').on('change', function() {
            if ($(this).val() === 'daily') {
                $('#daily_date').show();
                $('#month_date').hide();
            } else {
                $('#daily_date').hide();
                $('#month_date').show();
            }
        });


        // Initial load
        $("#loadProductReport").click();


        // Order Report: Button click
        $("#loadOrderReport").on("click", function() {
            let type = $("#order_report_type").val();
            let daily = type === "daily" ? $("#order_daily_date").val() : null;
            let month = type === "monthly" ? $("#order_month_date").val() : null;
            let branchId = $("#branch_id").val(); // Same branch for order report

            loadOrderReport(daily, month, branchId);
        });
          $("#loadOrderReport").trigger("click");

        // Order Report: Branch change
        // $("#branch_id").on("change", function() {
        //     $("#loadOrderReport").click();
        // });

        // Order Report: Switch inputs
        $("#order_report_type").on("change", function() {
            if ($(this).val() === "daily") {
                $("#order_daily_date").show();
                $("#order_month_date").hide();
            } else {
                $("#order_daily_date").hide();
                $("#order_month_date").show();
            }
        });


//Load Dashboard Summary
        function loadDashboardSummary(branchId = '') {
    // Optional: Show a loader if you have one
    $.ajax({
        url: "{{ route('admin.loadDashboardSummary') }}", // Create this route in Laravel
        type: "GET",
        data: { branch_id: branchId },
        success: function(res) {
            animateNumber("#new_orders", res.new_orders);
            animateNumber("#new_customers", res.new_customers);
            animateNumber("#average_sale", res.average_sale);
            animateNumber("#gross_profit", res.gross_profit);
            animateNumber("#total_earnings", res.total_earnings);
        }
    });
}
        // Initial load
//  loadOrderReport();
loadDashboardSummary(); // Initial load
$("#branch_id").on("change", function() {
    let branchId = $(this).val();

    // Reload dashboard widgets
    loadDashboardSummary(branchId);

    // Reload Product Report chart
    let type = $("#report_type").val();
    let dailyDate = (type === "daily") ? $("#daily_date").val() : null;
    let monthDate = (type === "monthly") ? $("#month_date").val() : null;
    console.log(monthDate);
    loadChart(dailyDate, monthDate, branchId);

    // Reload Order Report
    let orderType = $("#order_report_type").val();
    let daily = (orderType === "daily") ? $("#order_daily_date").val() : null;
    let month = (orderType === "monthly") ? $("#order_month_date").val() : null;
    loadOrderReport(daily, month, branchId);
});

    });
</script>

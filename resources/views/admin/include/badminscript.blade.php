<script>
    async function submitFormAjax(formSelector, submitButtonSelector, options = {}) {
        const form = $(formSelector);
        const submitButton = $(submitButtonSelector);
        const originalButtonText = submitButton.html();
        const actionUrl = form.attr('action');
        const method = form.attr('method') || 'POST';
        const formData = new FormData(form[0]);

        $.ajax({
            url: actionUrl,
            type: method,
            headers: options.token,
            data: formData,
            contentType: false,
            processData: false,
             success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: response.message,
                    showConfirmButton: false,
                    timer: 1500,
                    timerProgressBar: true
                });

                // if (modal.length) modal.modal('hide');
                // form[0].reset();


                setTimeout(function() {
                    location.reload();
                }, 2000);
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors || {};
                    form.find('.text-danger').remove();

                    $.each(errors, function(key, value) {
                        var fieldName = key.includes('.') ? key.split('.')[0] + '[]' : key;

                        var input = form.find('[name="' + fieldName + '"]');
                        if (input.length) {
                            if (input.is('select')) {
                                input.closest('.mb-3').append('<span class="text-danger">' +
                                    value[0] + '</span>');
                            } else {
                                input.after('<span class="text-danger">' + value[0] +
                                    '</span>');
                            }
                        } else {
                            form.append('<span class="text-danger">' + value[0] + '</span>');
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

        });
    }
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

        $.ajax({
            url: postUrl, // replace with your route
            type: 'POST',
            data: {
                id: anchor.data('id'),
                status: newStatus,
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
    })

    $(document).on("click", "#update-branch-ingredient", async function(event) {
        let is_pos = null;
        const id = $(this).attr("data-id");
        is_pos = $(this).attr("data-is_pos");
        const quantity = $(this).attr("data-quantity");
        var data_url = "{{ route('badmin.ingredient.view', ['id' => 'null']) }}";
        var final_url = data_url.replace('null', id);
        $.ajax({
            url: final_url,
            type: "GET",
            data: {
                is_pos: is_pos,
                quantity: quantity
            },
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#IngredientsModal").modal("show");
            }
        })
    });

    $(document).on("click", "#ingredientFormqty", async function(event) {
        event.preventDefault();
        var branch_id = $(this).attr("data-branchId");
        var ing_id = $(this).attr("data-ingId");

        var data_url =
            "{{ route('badmin.ingredient.updateQuantity', ['id' => '__id__', 'branchid' => '__branchid__']) }}";
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
                    showConfirmButton: false,
                    timer: 1500
                });
                setTimeout(function() {
                    location.reload();
                }, 1600);

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
    //branch Staff
    $(document).on("click", "#addBranchStaff", function(event) {
        event.preventDefault();
        var data_url = "{{ route('badmin.staff.create') }}";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#branchuserModal").modal("show");
            }
        });
    });
    $(document).on("click", "#editdBranchStaff", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "<?= route('badmin.staff.edit', ['id' => 'null']) ?>";
        const updateUrl = data_url.replace('null', id);
        $.ajax({
            url: updateUrl,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#editBranchUserModal").modal("show");
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
    //branch Shift
    $(document).on("click", "#addBranchShift", function(event) {
        event.preventDefault();
        var data_url = "{{ route('badmin.shifts.create') }}";
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addShiftModal").modal("show");
            }
        });
    });
    $(document).on("click", "#editBranchShift", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = "<?= route('badmin.shifts.edit', ['id' => 'null']) ?>";
        const updateUrl = data_url.replace('null', id);
        $.ajax({
            url: updateUrl,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#addShiftModal").modal("show");
            }
        })
    });
    $(document).on('submit', '#addShiftForm', function(e) {
        e.preventDefault();
        var form = $(this);
        submitFormAjax(form, '', {
            modalSelector: '#branchuserModal',
            successMessage: 'Shift added successfully',
            errorMessage: 'Failed to add Shift',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });



    // delete Shift
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





    $(document).on("click", "#btn-create-station", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = `<?= route('badmin.station.create') ?>`;
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#StationModal").modal("show");
            }
        })
    });

    $(document).on('submit', '#create-station-form', function(e) {
        e.preventDefault();
        var form = $(this);
        submitFormAjax(form, '', {
            modalSelector: '#StationModal',
            successMessage: 'Station created successfully',
            errorMessage: 'Failed to created Station',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });



    $(document).on("click", "#edit-station", async function(event) {
        const id = $(this).attr("data-id");
        var data_url = `<?= route('badmin.station.edit', ['id' => '__ID__']) ?>`.replace('__ID__',
            id);
        $.ajax({
            url: data_url,
            success: function(data) {
                $("#addtranslationModal").html(data);
                $("#EditStationModal").modal("show");
            }
        })
    });


    $(document).on('submit', '#edit-station-form', function(e) {
        e.preventDefault();
        var form = $(this);
        submitFormAjax(form, '', {
            modalSelector: '#EditStationModal',
            successMessage: 'Station Updated successfully',
            errorMessage: 'Failed to created Station',
            resetForm: true,
            reloadPage: true,
            token: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    });

    // cashout /refund scripts



    // Toggle Sections
    $('#cashoutCheck').on('change', function() {
        $('#refundCheck').prop('checked', false);
        $('#refundSection').hide();
        $('#cashoutSection').toggle(this.checked);
    });

    $('#refundCheck').on('change', function() {
        $('#cashoutCheck').prop('checked', false);
        $('#cashoutSection').hide();
        $('#refundSection').toggle(this.checked);
    });

    // Category Change
    $('#cashoutCategory').change(function() {
        let val = $(this).val();
        $('#ingredientSection, #otherSection').hide();
        if (val === 'ingredient') $('#ingredientSection').show();
        if (val === 'other') $('#otherSection').show();
    });


    //  Submit Cashout
    $('#submitCashout').click(function() {
        let data = {
            _token: '{{ csrf_token() }}',
            type: 'cashout',
            category: $('#cashoutCategory').val(),
            ingredient_ids: $('#cashoutCategory').val() === 'ingredient' ?
                $('#ingredientList').val() :
                null,
            item_name: $('#cashoutCategory').val() === 'other' ?
                $('#otherItem').val() :
                null,
            amount: $('#cashoutCategory').val() === 'ingredient' ?
                $('#ing-price').val() :
                $('#otherPrice').val(),
        };

        $.post('{{ route('pos.cashout.store') }}', data, function(res) {
            if (res.success) {
                $('#cashoutCheck').prop('checked', false);
                $('#cashoutCategory').val('');
                $('#ingredientList').val('');
                $('#ing-price').val('');
                $('#otherItem').val('');
                $('#otherPrice').val('');
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: res.message || 'Something went wrong.'
                });
            }
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Unable to process your request. Please try again later.'
            });
        });
    });


    //  Search Refund Order
    $('#searchOrder').click(function() {
        let orderRef = $('#refundOrderId').val();

        if (!orderRef) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Input',
                text: 'Please enter an order reference number.'
            });
            return;
        }

        $.get('{{ route('pos.order.amount') }}', {
            order_ref: orderRef
        }, function(res) {

            if (res.status === 'found') {
                $('#refundAmount').val(res.amount);
                Swal.fire({
                    icon: 'success',
                    title: 'Order Found!',
                    text: `Refundable Amount: ${res.amount}`
                });
            } else if (res.status === 'refunded') {
                $('#refundAmount').val('');
                Swal.fire({
                    icon: 'error',
                    title: 'Already Refunded',
                    text: res.message
                });
            } else {
                $('#refundAmount').val('');
                Swal.fire({
                    icon: 'error',
                    title: 'Order Not Found',
                    text: res.message
                });
            }

        });
    });



    //  Submit Refund
    $('#submitRefund').click(function() {
        let data = {
            _token: '{{ csrf_token() }}',
            type: 'refund',
            category: 'refund',
            order_ref: $('#refundOrderId').val(),
            amount: $('#refundAmount').val(),
            remarks: $('#refundRemarks').val()
        };

        if (!data.order_ref || !data.amount) {
            Swal.fire({
                icon: 'warning',
                title: 'Missing Fields',
                text: 'Please fill in all required fields.'
            });
            return;
        }

        $.post('{{ route('pos.cashout.store') }}', data, function(res) {
            if (res.success) {
                $('#refundOrderId').val('');
                $('#refundAmount').val('')
                $('#refundRemarks').val('')
                $('#refundCheck').prop('checked', false);
                Swal.fire({
                    icon: 'success',
                    title: 'Refund Processed!',
                    text: res.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => location.reload());
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: res.message || 'Refund failed. Please try again.'
                });
            }
        }).fail(() => {
            Swal.fire({
                icon: 'error',
                title: 'Server Error',
                text: 'Unable to process refund at the moment.'
            });
        });
    });
</script>


function changeStatus(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to change this setting?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, change it!',
        customClass: {
            confirmButton: 'btn btn-danger me-3 waves-effect waves-light',
            cancelButton: 'btn btn-label-secondary waves-effect waves-light'
        },
        buttonsStyling: false
    }).then(function (result) {
        if (result.isConfirmed) {
            $.ajax({
                url: 'global-setting/update',
                method: 'POST',
                data: {
                    id: id,
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    console.log(response.icon);
                    console.log(response.text);
                    Swal.fire({
                        icon: 'success',
                        title: 'Updated!',
                        text: 'Changes has been updated successfully',
                        customClass: {
                            confirmButton: 'btn btn-success waves-effect waves-light'
                        }
                    });
                },
                error: function (error) {
                    console.log(error.responseJSON.message);
                    // handle the error case
                }
            });
        } else {
            $(".individual-section" + id).load(location.href + " .individual-section" + id);

        }
    });

}

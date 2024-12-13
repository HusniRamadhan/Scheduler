$(document).ready(function() {
    $('form').submit(function(e) {
        e.preventDefault();
        var form = $(this);

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                if (response.success) {
                    handleSuccessResponse(response);
                } else if (response.error) {
                    handleErrorResponse(response);
                }
            },
        });
    });

    function handleSuccessResponse(response) {
        Swal.fire({
            title: 'Success!',
            text: response.success,
            icon: 'success',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
        }).then((result) => {
            if (result.isConfirmed) {
                // Trigger a page refresh after the user clicks "OK"
                location.reload();
            }
        });
    }

    function handleErrorResponse(response) {
        Swal.fire({
            title: 'Error!',
            text: response.error,
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK',
        });
    }
});

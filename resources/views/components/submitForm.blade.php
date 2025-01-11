<script>
    $(document).on('submit', '.submitForm', function(e) {
        e.preventDefault();
        var form = $(this);
        let submit_btn = $(form).find(".submit_btn");
        var data = $(this).serialize();
        var url = $(this).attr('action');
        $.ajax({
            type: 'POST',
            url: url,
            data: data,
            success: function(response) {
                if (response.view) {
                    let target = response.target ?? '.appendBody';
                    $(target).html(response.view)
                }

                if (response.reload) {
                    location.reload();
                }
                try {
                    toastr.success('Saved');
                } catch (error) {
                    alert('Saved');
                }

                if (response.route) {
                    window.location.href = response.route;
                }
                $('#sourceModal').modal('hide');
                console.log('Data saved successfully:', response);
                $('.modal').modal('hide')
            },
            error: function(xhr, status, error) {
                $(submit_btn).prop("disabled", false);
                $(submit_btn).closest("div").find(".loader").addClass("d-none");
                if (xhr.status == 422) {
                    $(form).find("div.alert").remove();
                    var errorObj = xhr.responseJSON.errors;
                    $.map(errorObj, function(value, index) {
                        var appendIn = $(form)
                            .find('[name="' + index + '"]')
                            .closest("div");
                        if (!appendIn.length) {
                            toastr.error(value[0]);
                        } else {
                            $(appendIn).append(
                                '<div class="alert alert-danger" style="padding: 1px 5px;font-size: 12px"> ' +
                                value[0] +
                                "</div>"
                            );
                            $(".sipnner").addClass("d-none");
                        }
                    });
                } else {
                    toastr.error("Unknown Error");
                    $('.false_response_json').removeClass('hide');
                }
                $('#loader-overlay').addClass('hidden');
            },
        });
    });


    // function debounce(func, delay) {
    //     let timer;
    //     return function(...args) {
    //         const context = this;
    //         clearTimeout(timer);
    //         timer = setTimeout(() => func.apply(context, args), delay);
    //     };
    // }
</script>

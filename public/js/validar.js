$(document).ready(function () {
    $("#login").submit(validaUsuario);
});



function validaUsuario(e) {
    e.preventDefault();
    e.stopImmediatePropagation();
    $.ajax({
        type: "POST",
        url: 'index.php',
        data: $(this).serialize(),
        dataType: "json",
        success: function (response)
        {
            if (response.login) {
                e.target.submit();
            } else {
                $("#mensaje").removeClass("d-none");
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert(xhr.status);
            alert(thrownError);
        }
    });
}
;
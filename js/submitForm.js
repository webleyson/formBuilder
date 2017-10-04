$(document).ready(function() {
    $('button[type="submit"]').on('click', function(e) {
        var userId = $('#userId').val();
        alert(userId);
        e.preventDefault();
        var formData = $('form').serializeArray();
        var obj = {
            data: formData,
            do: "formAnswers",
            userId: userId,
        }

        $.ajax({
            url: 'ajax/dbfunctions.ajax.php',
            type: 'post',
            data: obj,
            success: function(response) {
                $('#confirm').html("<div class='alert alert-success'>" + response.message + "</div>");
            }
        })
    })
});
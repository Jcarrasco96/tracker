$(document).on("click", "#btn-add_website, #btn-update_website", function (event) {
    event.preventDefault();

    let title = $(this).attr('id') === 'btn-add_website' ? 'Add website' : 'Update website';
    let url = $(this).attr('id') === 'btn-add_website' ? $(this).data('url') : $(this).attr('href');

    $("#modal-app-title").html(title);

    $.ajax({
        type: 'get',
        url: url
    }).done(function (response) {
        $("#modal-app-container").html(response);
        $("#modal-app").modal('show');
    });

    return false;
});

$(document).on("click", "#btn-show_script", function (event) {
    event.preventDefault();

    $("#modal-app-title").html('Script for website');

    let html = '<textarea readonly aria-label="script" class="form-control" rows="2">';
    html += '<script defer src="api/script" data-website-id="' + $(this).data('id') + '"></script>';
    html += '</textarea>';

    $("#modal-app-container").html(html);
    $("#modal-app").modal('show');

    return false;
});

$(document).on("submit", "#form-add_website", async function (e) {
    e.preventDefault();

    let form = new FormData(this);

    sendAjaxAndValidate($(this).attr('action'), form, true);

    return false;
});

$(document).on("blur", "#form-add_website .form-control", function() {
    let formJq = $("#form-add_website");
    let form = new FormData(formJq[0]);

    form.append("_validate", "1");

    sendAjaxAndValidate(formJq.attr('action'), form);
});

function sendAjaxAndValidate(url, data, dismiss = false) {
    $.ajax({
        url,
        type: "POST",
        data,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function(result) {
            let formControlJq = $(".form-control");
            formControlJq.removeClass("is-invalid");
            formControlJq.addClass("is-valid");

            let formControlSelectJq = $(".form-select");
            formControlSelectJq.removeClass("is-invalid");
            formControlSelectJq.addClass("is-valid");

            $(".invalid-feedback").text("");

            if (result.success) {
                if (dismiss) {
                    // nsuccess(result.message);
                    // $("#modal-app").modal('hide');

                    window.location.reload();
                }
            } else {
                for (let field in result.errors) {
                    let input = $("#input" + field.charAt(0).toUpperCase() + field.slice(1));
                    let feedback = $("#invalid-" + field);

                    input.addClass("is-invalid");
                    feedback.text(result.errors[field]);
                }
            }
        },
        error: function(xhr, status, error){
            console.error("Error AJAX:", error);
        }
    });
}
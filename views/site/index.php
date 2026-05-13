<?php

/** @var Website[] $websites */

use app\core\App;
use app\core\helpers\Url;
use app\models\Website;

?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-2 d-print-none">
        <div>
            <h2>My sites</h2>
        </div>
        <div>
            <button class="btn btn-primary" id="btn-add_website" data-url="<?= Url::to('website/create') ?>"><i class="bi bi-plus-lg"></i> Add website</button>
        </div>
    </div>

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Id</th>
            <th>Domain</th>
            <th class="d-print-none">Action</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($websites as $site): ?>
            <tr id="td_<?= $site->id ?>">
                <td class="align-middle"><?= htmlspecialchars($site->id) ?></td>
                <td class="align-middle"><?= htmlspecialchars($site->domain) ?></td>
                <td class="align-middle d-print-none" style="width: 125px;">
                    <a href="<?= Url::to("website/$site->id") ?>" class="btn btn-primary btn-sm"><i class="bi bi-graph-up"></i></a>

                    <button class="btn btn-primary btn-sm" id="btn-show_script" data-id="<?= $site->id ?>"><i class="bi bi-filetype-js"></i></button>

                    <button data-url="<?= Url::to("website/$site->id") ?>" class="btn btn-danger btn-sm" id="btn-delete_website"><i class="bi bi-trash-fill"></i></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<script>
    let itemUrlDelete;
    let itemTdDelete;

    $('#modal-confirm').on('hidden.bs.modal', () => {
        itemUrlDelete = null;
        itemTdDelete = null;
    });

    $(document).on("click", "#btn-delete_website", function (event) {
        event.preventDefault();

        $("#modal-confirm-title").html('Delete website?');

        itemUrlDelete = $(this).data('url');
        itemTdDelete = $(this).closest('tr');

        $("#modal-confirm").modal('show');

        return false;
    });

    $(document).on("click", '#modal-confirm-button', function() {
        $.ajax({
            type: 'delete',
            url: itemUrlDelete,
            data: {
                is_ajax: true
            },
            success: function(result) {
                if (result.success) {
                    if (itemTdDelete) {
                        itemTdDelete.remove();
                    }
                    nsuccess(result.message);
                } else {
                    nerror("There was an error while trying to delete the item.");
                }

                $("#modal-confirm").modal('hide');
            },
            error: function(xhr, status, error){
                console.error("Error AJAX:", error);
            }
        });
    });

</script>

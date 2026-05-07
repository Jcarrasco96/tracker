<?php

use app\core\helpers\Url;

?>

<form id="form-add_website" class="g-3" action="<?= Url::to('website/create') ?>" method="post" novalidate>


    <div class="mb-2">
        <label for="inputDomain" class="form-label mb-1">Domain <span class="text-danger">*</span></label>
        <input type="text" class="form-control" id="inputDomain" name="domain" required>
        <div id="invalid-domain" class="invalid-feedback"></div>
    </div>

    <button type="submit" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Submit</button>

</form>

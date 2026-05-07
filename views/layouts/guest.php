<?php

/** @var string $content */
/** @var string $pageTitle */
/** @var array $scripts */

use app\core\helpers\Html;
use app\core\services\Renderer;
use app\core\widgets\Alert;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once '_head.php'; ?>
    <title><?= Html::encode($pageTitle) ?></title>
</head>
<body>

<main id="content" class="content d-none">
    <?= $content; ?>
</main>

<div class="preloader"></div>
<button type="button" class="btn btn-secondary btn-to-top"><i class="bi bi-caret-up-fill"></i></button>

<?= Renderer::renderScripts($scripts ?? []) ?>

<?= Html::js("bootstrap.bundle.min.js") ?>
<?= Html::js("index.js") ?>
<?= Html::js("growl-notification-bootstrap-alert/bootstrap-notify.min.js") ?>
<?= Html::js("notify.min.js") ?>

<?= Alert::run() ?>

</body>
</html>

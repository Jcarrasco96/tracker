<?php

/** @var array $styles */
/** @var array $headScripts */

use app\core\helpers\Html;
use app\core\helpers\Url;
use app\core\services\Renderer;

?>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="description" content="">
<meta name="keywords" content="">
<meta name="robots" content="noindex, nofollow">
<meta name="googlebot" content="noindex">

<link rel="apple-touch-icon" sizes="180x180" href="<?= Url::to('assets/img/apple-touch-icon.png') ?>">
<link rel="icon" type="image/png" sizes="32x32" href="<?= Url::to('assets/img/favicon-32x32.png') ?>">
<link rel="icon" type="image/png" sizes="16x16" href="<?= Url::to('assets/img/favicon-16x16.png') ?>">
<link rel="manifest" href="<?= Url::to('assets/img/site.webmanifest') ?>">

<!--<script defer src="http://127.0.0.1:3001/script.js" data-website-id="6b1d434d-1282-4f13-bdef-41c984266876"></script>-->
<script defer src="https://mytk.jcarrasco96.com/script.js" data-website-id="6b1d434d-1282-4f13-bdef-41c984266876"></script>

<?= Html::icon("img/favicon.png") ?>

<?= Renderer::renderStyles($styles ?? []) ?>

<?= Html::css("bootstrap.min.css") ?>
<?= Html::css("bootstrap-icons/bootstrap-icons.min.css") ?>
<?= Html::css("animate.min.css") ?>
<?= Html::css("preloader.css") ?>
<?= Html::css("style.css") ?>

<?= Renderer::renderScripts($headScripts ?? []) ?>

<?= Html::js("jquery-3.7.1.min.js") ?>

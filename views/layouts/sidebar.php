<?php

/** @var string $content */
/** @var string $pageTitle */
/** @var array $scripts */

use app\core\helpers\Html;
use app\core\helpers\Url;
use app\core\services\Renderer;
use app\core\widgets\Alert;

$path = $_SERVER['REQUEST_URI'] ?? '/';
$position = strpos($path, '?');

if ($position !== false) {
    $path = substr($path, 0, $position);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <?php include_once '_head.php'; ?>
    <title><?= Html::encode($pageTitle) ?></title>

    <style>
        #sidebar {
            width: 280px;
            background: #712cf9;
            color: white;
        }

        .scroll-section {
            height: 100vh;
            overflow-y: auto;
        }

        #sidebar .nav-link {
            color: rgba(255, 255, 255, 0.55);
        }
        #sidebar .nav-link.active {
            background-color: #6528e0;
            color: white;
        }
        #sidebar .nav-link:hover {
            background-color: #5a23c8;
            color: white;
        }
    </style>
</head>
<body>

<div class="d-flex">

    <div id="sidebar" class="scroll-section p-2 d-print-none">

        <a href="<?= Url::to('site/index') ?>" class="fs-4 text-white text-decoration-none mb-3 d-block px-3">Mi Sidebar</a>
        <hr>

        <div class="text-uppercase small px-3 mb-2">Client administration</div>
        <ul class="nav nav-pills flex-column mb-2 gap-1">
            <li class="nav-item"><a href="<?= Url::to('clients') ?>" class="nav-link <?= $path == '/clients' ? 'active' : '' ?>"><i class="bi bi-person-vcard"></i> Clients</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-person-lines-fill"></i> Clients Assignments</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-file-earmark-person"></i> Clients Documents</a></li>
        </ul>

        <div class="text-uppercase small px-3 mb-2">Progress Notes</div>
        <ul class="nav nav-pills flex-column mb-2 gap-1">
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-journals"></i> Progress Notes</a></li>
        </ul>

        <div class="text-uppercase small px-3 mb-2">Reports</div>
        <ul class="nav nav-pills flex-column mb-2 gap-1">
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-receipt"></i> Billing Timesheets</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-receipt-cutoff"></i> Custom Timesheets</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-calendar"></i> Calendar</a></li>
        </ul>

        <div class="text-uppercase small px-3 mb-2">System Administration</div>
        <ul class="nav nav-pills flex-column mb-2 gap-1">
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-clipboard-pulse"></i> Diagnostics</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-shop"></i> Domains</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-archive"></i> Files</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-compass"></i> Settings</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-gear"></i> General Configuration</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-person-lock"></i> Roles</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-person-gear"></i> Supervisor Assignments</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-person"></i> Users</a></li>
        </ul>

        <div class="text-uppercase small px-3 mb-2">Profile</div>
        <ul class="nav nav-pills flex-column mb-2 gap-1">
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-speedometer"></i> Dashboard</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-person-circle"></i> Account Settings</a></li>
            <li class="nav-item"><a href="<?= Url::to('site/about') ?>" class="nav-link"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        </ul>
    </div>

    <div id="content" class="content flex-grow-1 scroll-section p-4" style="display: none;">
        <?= $content; ?>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="modal-app" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="modal-app-title">Modal title</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-app-container"></div>
        </div>
    </div>
</div>

<div class="preloader"></div>
<button class="btn btn-secondary btn-to-top"><i class="bi bi-caret-up-fill"></i></button>

<?= Renderer::renderScripts($scripts ?? []) ?>

<?= Html::js("bootstrap.bundle.min.js") ?>
<?= Html::js("fix.container.min.js") ?>
<?= Html::js("index.min.js") ?>
<?= Html::js("growl-notification-bootstrap-alert/bootstrap-notify.min.js") ?>
<?= Html::js("notify.min.js") ?>

<?= Alert::run() ?>

</body>
</html>

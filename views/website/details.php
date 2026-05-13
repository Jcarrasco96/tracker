<?php

/** @var string $website */
/** @var array $summary */
/** @var array $pages */
/** @var array $referrers */
/** @var array $events */
/** @var array $timeSeries */
/** @var array $languages */
/** @var array $userAgents */
/** @var array $browsers */

use app\core\helpers\Url;

$script = '<script defer src="' . Url::to('api/script') . '" data-website-id="' . $website . '"></script>';

function iconOS($os): string
{
    $os = strtolower($os);

    if (str_contains($os, 'windows')) {
        return 'windows';
    } elseif (str_contains($os, 'mac')) {
        return 'command';
    } elseif (str_contains($os, 'android')) {
        return 'android2';
    } elseif (str_contains($os, 'ios')) {
        return 'apple';
    } elseif (str_contains($os, 'linux')) {
        return 'tux';
    } elseif (str_contains($os, 'chrome')) {
        return 'browser-chrome';
    } elseif (str_contains($os, 'freebsd')) {
        return 'tux';
    } else {
        return 'x-circle';
    }
}

function iconBrowser($browser): string
{
    $browser = strtolower($browser);

    if (str_contains($browser, 'edge')) {
        return 'browser-edge';
    } elseif (str_contains($browser, 'chrome')) {
        return 'browser-chrome';
    } elseif (str_contains($browser, 'safari')) {
        return 'browser-safari';
    } elseif (str_contains($browser, 'firefox')) {
        return 'browser-firefox';
    } elseif (str_contains($browser, 'ie')) {
        return 'windows';
    } else {
        return 'globe';
    }
}

function iconDevice($device): string
{
    $device = strtolower($device);

    if (str_contains($device, 'tablet')) {
        return 'tablet';
    } elseif (str_contains($device, 'mobile')) {
        return 'phone';
    } else {
        return 'window-desktop';
    }
}

?>

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-2 d-print-none">
        <div>
            <h2>Dashboard</h2>
        </div>
        <div>
            <button class="btn btn-primary" id="btn-add_website" data-url="<?= Url::to('website/create') ?>"><i class="bi bi-plus-lg"></i> Add website</button>

            <button class="btn btn-danger" id="btn-delete_website"><i class="bi bi-trash-fill"></i></button>
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-4">
            <div class="card p-3">
                <h6>Total visits</h6>
                <h3><?= intval($summary['total']) ?></h3>

                <div class="position-absolute top-50 end-0 translate-middle-y mx-3">
                    <i class="bi bi-eye" style="font-size: xxx-large;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h6>Unique users</h6>
                <h3><?= intval($summary['unique_visitors']) ?></h3>

                <div class="position-absolute top-50 end-0 translate-middle-y mx-3">
                    <i class="bi bi-person" style="font-size: xxx-large;"></i>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card p-3">
                <h6>Today</h6>
                <h3><?= intval($summary['today']) ?></h3>

                <div class="position-absolute top-50 end-0 translate-middle-y mx-3">
                    <i class="bi bi-calendar-day" style="font-size: xxx-large;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-6">
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top pages</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($pages)): ?>
                        <?php foreach ($pages as $page): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $page['url'] ?>
                                <span class="badge text-bg-primary rounded-pill"><?= $page['visits'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Referrers</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($referrers)): ?>
                        <?php foreach ($referrers as $referrer): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= empty($referrer['referrer']) ? 'Direct' : $referrer['referrer'] ?>
                                <span class="badge text-bg-primary rounded-pill"><?= $referrer['visits'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top events</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($events)): ?>
                        <?php foreach ($events as $event): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= empty($event['event_type']) ? 'Default' : $event['event_type'] ?>
                                <span class="badge text-bg-primary rounded-pill"><?= $event['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top User-Agent</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($userAgents)): ?>
                        <?php foreach ($userAgents as $agent): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <?= $agent['user_agent'] ?>
                                <span class="badge text-bg-primary rounded-pill"><?= $agent['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top languages</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($languages)): ?>
                        <?php foreach ($languages as $language): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p class="mb-0"><?= $language['language'] ?? 'Unknown' ?></p>
                                <span class="badge text-bg-primary rounded-pill"><?= $language['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top pages</h5>
                    <canvas id="chart" style="min-width: 100%; min-height: 230px;"></canvas>
                </div>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top browsers</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($browsers)): ?>
                        <?php foreach ($browsers as $browser): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p class="mb-0">
                                    <i class="bi bi-<?= iconBrowser($browser['browser'] ?? '') ?>"></i> <?= $browser['browser'] ?? 'Unknown' ?>
                                </p>
                                <span class="badge text-bg-primary rounded-pill"><?= $browser['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top Operative System</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($os)): ?>
                        <?php foreach ($os as $operativeSystem): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p class="mb-0">
                                    <i class="bi bi-<?= iconOS($operativeSystem['os'] ?? '') ?>"></i> <?= $operativeSystem['os'] ?? 'Unknown' ?>
                                </p>
                                <span class="badge text-bg-primary rounded-pill"><?= $operativeSystem['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top devices</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php if (!empty($devices)): ?>
                        <?php foreach ($devices as $device): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <p class="mb-0">
                                    <i class="bi bi-<?= iconDevice($device['device_type'] ?? '') ?>"></i> <?= ucfirst($device['device_type'] ?? 'Unknown') ?>
                                </p>
                                <span class="badge text-bg-primary rounded-pill"><?= $device['total'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">NO DATA</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>

    <div class="card mb-2">
        <div class="card-body">
            <h5 class="card-title mb-0">How install?</h5>

            <code><?= htmlentities($script) ?></code>
        </div>
    </div>

</div>

<script>
    $(document).on("click", "#btn-delete_website", function (event) {
        event.preventDefault();

        $("#modal-confirm-title").html('Delete website?');
        $("#modal-confirm").modal('show');

        return false;
    });

    $(document).on("click", '#modal-confirm-button', function() {
        $.ajax({
            type: 'delete',
            url: "<?= Url::to("website/$website") ?>",
            data: {
                is_ajax: true
            },
            success: function(result) {
                if (result.success) {
                    setTimeout(() => {
                        window.location = "<?= Url::to("site/index") ?>"
                    }, 500);
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

    const timeSeries = <?= json_encode($timeSeries) ?>;

    const ctx = document.getElementById('chart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: timeSeries.map(x => x.date),
            datasets: [{
                label: 'Visits',
                data: timeSeries.map(x => x.visits),
                fill: false,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        // options: {
        //     responsive: true,
        //     plugins: {
        //         legend: {position: 'top'},
        //         tooltip: {
        //             callbacks: {
        //                 label: function(context) {
        //                     const hour = context.label;
        //                     const val  = context.parsed.y;
        //                     return `${hour} – ${val} visit${val !== 1 ? 's' : ''}`;
        //                 }
        //             }
        //         }
        //     },
        //     scales: {
        //         x: {
        //             title: {display: true, text: 'Hour'},
        //             ticks: {
        //                 maxRotation: 0,
        //                 autoSkip: true,
        //                 maxTicksLimit: 12
        //             }
        //         },
        //         y: {
        //             title: {display: true, text: 'Visits'},
        //             beginAtZero: true,
        //             precision: 0
        //         }
        //     }
        // }
    });
</script>

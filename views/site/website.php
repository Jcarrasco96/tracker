<?php

/** @var string $website */
/** @var array $summary */
/** @var array $pages */
/** @var array $referrers */
/** @var array $events */
/** @var array $timeSeries */

use app\core\helpers\Url;

$script = '<script defer src="' . Url::to('script.js') . '" data-website-id="' . $website . '"></script>';

?>

<div class="container">

    <h3>Dashboard</h3>

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
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top pages</h5>
                    <canvas id="chart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top pages</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($pages as $page): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= $page['url'] ?>
                            <span class="badge text-bg-primary rounded-pill"><?= $page['visits'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Referrers</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($referrers as $referrer): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= empty($referrer['referrer']) ? 'Direct' : $referrer['referrer'] ?>
                            <span class="badge text-bg-primary rounded-pill"><?= $referrer['visits'] ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="card mb-2">
                <div class="card-body">
                    <h5 class="card-title mb-0">Top events</h5>
                </div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($events as $event): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= empty($event['event_type']) ? 'Default' : $event['event_type'] ?>
                            <span class="badge text-bg-primary rounded-pill"><?= $event['total'] ?></span>
                        </li>
                    <?php endforeach; ?>
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
    const timeSeries = <?= json_encode($timeSeries) ?>;

    const ctx = document.getElementById('chart');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: timeSeries.map(x => x.date),
            datasets: [{
                label: 'Visits',
                data: timeSeries.map(x => x.visits)
            }]
        }
    });
</script>

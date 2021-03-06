<?php

use app\models\Restaurant;
use yii\helpers\Html;
use yii\helpers\Url;

$this->title = 'NomNom';

$allRestaurants = Restaurant::getDetailedList();
$defaultRestaurant = Restaurant::findOne(Yii::$app->params['default_restaurant']);

if ($order->stage_end > time()) {
    $this->registerJs(<<<JS
function getTimeRemaining(endtime) {
    var t = Date.parse(endtime) - Date.parse(new Date());
    var seconds = t < 0 ? 0 : Math.floor((t / 1000) % 60);
    var minutes = t < 0 ? 0 : Math.floor((t / 1000 / 60) % 60);
    var hours = t < 0 ? 0 : Math.floor((t / (1000 * 60 * 60)) % 24);
    return {
        'total': t,
        'hours': hours,
        'minutes': minutes,
        'seconds': seconds
    };
}
function initializeClock(id, endtime) {
    var clock = document.getElementById(id);
    var hoursSpan = clock.querySelector('.hours');
    var minutesSpan = clock.querySelector('.minutes');
    var secondsSpan = clock.querySelector('.seconds');

    function updateClock() {
        var t = getTimeRemaining(endtime);

        hoursSpan.innerHTML = ('0' + t.hours).slice(-2);
        minutesSpan.innerHTML = ('0' + t.minutes).slice(-2);
        secondsSpan.innerHTML = ('0' + t.seconds).slice(-2);

        if (t.total <= 0) {
            clearInterval(timeinterval);
        }
    }

    updateClock();
    var timeinterval = setInterval(updateClock, 1000);
}

var deadline = new Date({$order->stage_end} * 1000);
initializeClock('clockdiv', deadline);
JS
);
}

?>
<?= $this->render('/menu/user', ['service' => 'nomnom']) ?>
<?= $this->render('/menu/admin', ['service' => 'nomnom']) ?>

<div class="row">
    <div class="col-lg-12">
        <h1>Zamówienie na dzień <?= date('Y/m/d') ?></h1>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <?php if ($order->stage_end > time()): ?>
        <div class="alert alert-warning">
            <strong>Zamknięcie głosowania za</strong><br>
            <div class="text-center">
                <div id="clockdiv">
                    <div>
                        <span class="hours"></span>
                        <div class="smalltext">Godziny</div>
                    </div>
                    <div>
                        <span class="minutes"></span>
                        <div class="smalltext">Minuty</div>
                    </div>
                    <div>
                        <span class="seconds"></span>
                        <div class="smalltext">Sekundy</div>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="alert alert-info">
            <strong>Głosowanie zamknięte.</strong>
        </div>
        <?php endif ?>
    </div>
</div>
<?php if ($defaultRestaurant): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <td><?= Html::encode($defaultRestaurant->name) ?></td>
                    <td><?= !empty($defaultRestaurant->url) ? Html::a($defaultRestaurant->url, $defaultRestaurant->url, ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($defaultRestaurant->screen) ? Html::a('Zobacz', '/uploads/menu/' . $defaultRestaurant->screen, ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>
<div class="row">
    <div class="col-lg-12">
        <h3>Druga restauracja</h3>
    </div>
</div>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <th>Nazwa restauracji</th>
                    <th>Link do menu</th>
                    <th>Zdjęcie menu</th>
                    <th></th>
                </tr>
                <?php foreach ($order->votesList as $id => $restaurant): ?>
                <tr>
                    <td><?= Html::encode($restaurant['name']) ?></td>
                    <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                    <td class="text-right">
                        <?php if (!$voted && $order->stage_end > time()): ?>
                        <a href="<?= Url::to(['site/vote', 'restaurant' => $id, 'order' => $order->id]) ?>" class="btn btn-success">Głosuję na to</a>
                        <?php endif ?>
                        <?php if ($voted && $voted['restaurant_id'] == $id): ?>
                        <i class="glyphicon glyphicon-hand-left"></i> <strong>Dzisiaj jem tylko stąd!</strong>
                        <?php endif ?>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>

<?php if (!$voted && $order->stage_end > time()): ?>
<div class="row" id="listaRestauracji">
    <div class="col-sm-12">
        <div class="form-group">
            <table class="table table-hover">
                <tr>
                    <th>Nazwa restauracji</th>
                    <th>Link do menu</th>
                    <th>Zdjęcie menu</th>
                    <th>Wszyscy mogą stąd zamawiać</th>
                    <th></th>
                </tr>
                <?php foreach ($allRestaurants as $id => $restaurant): ?>
                <tr class="<?= $restaurant['like'] == 0 ? 'warning' : '' ?>">
                    <td><?= Html::encode($restaurant['name']) ?></td>
                    <td><?= !empty($restaurant['url']) ? Html::a($restaurant['url'], $restaurant['url'], ['target' => 'restaurant']) : null ?></td>
                    <td><?= !empty($restaurant['screen']) ? Html::a('Zobacz', '/uploads/menu/' . $restaurant['screen'], ['target' => 'restaurant', 'class' => 'btn btn-info btn-xs']) : null ?></td>
                    <td><?= $restaurant['like'] ? 'Tak' : 'Nie' ?></td>
                    <td class="text-right">
                        <a href="<?= Url::to(['site/vote', 'restaurant' => $id, 'order' => $order->id]) ?>" class="btn btn-success">Głosuję na to</a>
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
<?php elseif ($order->stage_end > time()): ?>
<div class="row">
    <div class="col-sm-12">
        <div class="form-group text-right">
            <a href="<?= Url::to(['site/unvote', 'order' => $order->id]) ?>" class="btn btn-lg btn-danger" data-confirm="Czy na pewno chcesz usunąć swój głos?">Chcę usunąć mój głos</a>
        </div>
    </div>
</div>
<?php endif;

<?php

use kartik\datetime\DateTimePicker;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;

$this->title = 'BimBam';

?>
<?= $this->render('/menu/user') ?>
<?= $this->render('/menu/admin', ['service' => 'bimbam']) ?>

<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            <h3 class="pull-right"><div class="label label-primary">GitLab: <?= Html::encode(Yii::$app->user->identity->gitlab) ?></div></h3>
            <a href="<?= Url::to(['bimbam/add']) ?>" class="btn btn-default btn-lg"><span class="glyphicon glyphicon-plus"></span> Dodaj czas</a>
            <a href="https://docs.google.com/a/human-device.com/document/d/1vPt4CdMN3raZuhyDEJw0fRPNaGdE9ix9BjcOzR4XJP8/edit?usp=sharing" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-info-sign"></span> Instrukcje</a>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="form-group">
            Aby dodać czas w tickecie na GitLabie w jego treści w nowej linii wpisz <kbd>/spend CZAS</kbd>,
            gdzie CZAS podawany jest w formacie <em>?h ?m</em> (czyli np. <code>1h 10m</code> lub <code>25m</code>).<br>
            GitLab pozwala na podanie ujemnego czasu w celu skorygowania sumarycznej wartości.<br>
            Sprawdź, czy po prawej stronie widnieje Twój prawidłowy username z GitLaba, inaczej dodawany przez Ciebie w ticketach czas nie będzie rozpoznawany.<br>
            Aby dodać czas niezwiązany z konkretnym ticketem, kliknij w przycisk <span class="glyphicon glyphicon-plus"></span> Dodaj czas powyżej.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="form-group">
            Od: <?= DateTimePicker::widget([
                'name' => 'TimeSearch[from]',
                'value' => $searchModel->from,
                'options' => ['class' => 'date_range'],
                'pluginOptions' => ['autoclose' => true],
            ]) ?>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="form-group">
            Do: <?= DateTimePicker::widget([
                'name' => 'TimeSearch[to]',
                'value' => $searchModel->to,
                'options' => ['class' => 'date_range'],
                'pluginOptions' => ['autoclose' => true],
            ]) ?>
        </div>
    </div>
</div>

<?php Pjax::begin() ?>
<div class="row">
    <div class="col-lg-12 text-center">
        <div class="form-group">
            <h3><i class="glyphicon glyphicon-time"></i> Łączny czas dla wybranych dat: <strong><?= Yii::$app->formatter->asDuration($searchModel->summary) ?></strong></h3>
        </div>
    </div>
</div>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'filterSelector' => '.date_range',
    'columns'      => [
        [
            'attribute' => 'project_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'placeholder' => 'ID'],
            'value' => function ($model) {
                return Html::a(substr($model->project->url, 32), $model->project->url, ['class' => 'btn btn-xs btn-primary pull-right', 'target' => 'projekt'])
                    . Html::tag('span', $model->project_id, ['class' => 'badge'])
                    . ' "' . Html::encode($model->project->name) . '"';
            }
        ],
        [
            'attribute' => 'issue_id',
            'format' => 'raw',
            'filterInputOptions' => ['class' => 'form-control', 'id' => null, 'placeholder' => 'ID'],
            'value' => function ($model) {
                if (empty($model->issue_id)) {
                    return null;
                }
                return Html::a('link', $model->project->url . '/issues/' . $model->issue_id, ['class' => 'btn btn-xs btn-primary pull-right', 'target' => 'ticket'])
                    . Html::tag('span', $model->issue_id, ['class' => 'badge']);
            }
        ],
        [
            'attribute' => 'seconds',
            'format' => 'duration'
        ],
        [
            'attribute' => 'description',
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetime'
        ],
        [
            'class' => ActionColumn::class,
            'template' => '{update} {delete}',
            'visibleButtons' => [
                'update' => function ($model) {
                    return $model->issue_id === null;
                },
                'delete' => function ($model) {
                    return $model->issue_id === null;
                },
            ]
        ],
    ]
]); ?>
<?php Pjax::end();
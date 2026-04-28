<?php
namespace app\controllers;

use app\models\Vacancy;
use app\services\VacancyService;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\web\Response;

class VacancyController extends ActiveController
{
    public $modelClass = Vacancy::class;

    public function actions(): array
    {
        $actions = parent::actions();

        unset($actions['update'], $actions['delete'], $actions['options']);

        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    protected function verbs(): array
    {
        return [
            'index'  => ['GET', 'HEAD'],
            'view'   => ['GET', 'HEAD'],
            'create' => ['POST'],
        ];
    }

    public function prepareDataProvider(): ActiveDataProvider
    {
        return new ActiveDataProvider([
            'query' => Vacancy::find(),
            'sort' => [
                'defaultOrder' => ['created_at' => SORT_DESC],
                'attributes' => [
                    'title',
                    'salary',
                    'created_at',
                ],
            ],
            'pagination' => [
                'defaultPageSize' => \Yii::$app->params['defaultPageSize'],
                'pageSizeLimit' => [1, \Yii::$app->params['maxPageSize']],
            ],
        ]);
    }

    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator'] = [
            'class' => ContentNegotiator::class,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];

        unset($behaviors['authenticator'], $behaviors['rateLimiter']);
        return $behaviors;
    }
}

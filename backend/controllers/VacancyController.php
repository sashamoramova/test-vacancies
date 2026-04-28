<?php
namespace app\controllers;

use app\models\Vacancy;
use app\services\VacancyService;
use yii\rest\ActiveController;
use yii\data\ActiveDataProvider;
use yii\filters\ContentNegotiator;
use yii\web\Response;
use yii\filters\Cors;

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
                'defaultOrder' => ['created_at' => SORT_DESC, 'id' => SORT_DESC],
                'attributes' => [
                    'id',
                    'title' => [
                        'asc'  => ['title' => SORT_ASC,  'id' => SORT_ASC],
                        'desc' => ['title' => SORT_DESC, 'id' => SORT_DESC],
                    ],
                    'salary' => [
                        'asc'  => ['salary' => SORT_ASC,  'id' => SORT_ASC],
                        'desc' => ['salary' => SORT_DESC, 'id' => SORT_DESC],
                    ],
                    'created_at' => [
                        'asc'  => ['created_at' => SORT_ASC,  'id' => SORT_ASC],
                        'desc' => ['created_at' => SORT_DESC, 'id' => SORT_DESC],
                    ],
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

        $behaviors['corsFilter'] = [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['http://localhost:3000'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Expose-Headers' => [
                    'X-Pagination-Total-Count',
                    'X-Pagination-Page-Count',
                    'X-Pagination-Current-Page',
                    'X-Pagination-Per-Page',
                ],
                'Access-Control-Allow-Credentials' => false,
                'Access-Control-Max-Age' => 86400,
            ],
        ];

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

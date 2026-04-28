<?php
namespace app\controllers;

use yii\rest\Controller;
use yii\web\Response;

class SiteController extends Controller
{
    public function behaviors(): array
    {
        $b = parent::behaviors();
        unset($b['authenticator'], $b['rateLimiter']);
        return $b;
    }

    public function actionHealth(): array
    {
        return [
            'status' => 'ok',
            'service' => 'vacancies-api',
            'time' => date('c'),
            'php' => PHP_VERSION,
        ];
    }

    public function actionError(): array
    {
        $exception = \Yii::$app->errorHandler->exception;
        \Yii::$app->response->statusCode = $exception ? ($exception->statusCode ?? 500) : 500;

        return [
            'error' => true,
            'code'  => \Yii::$app->response->statusCode,
            'message' => $exception ? $exception->getMessage() : 'Unknown error',
        ];
    }
}

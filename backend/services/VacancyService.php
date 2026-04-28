<?php
namespace app\services;

use app\models\Vacancy;
use yii\base\BaseObject;

class VacancyService extends BaseObject
{
    /**
     * @param array $data Пришедшие поля из body
     * @return array{model: Vacancy|null, errors: array|null}
     */

    public function create(array $data): array
    {
        $model = new Vacancy();
        $model->setAttributes($data, /*safeOnly*/ true);

        if (!$model->save()) {
            return ['model' => null, 'errors' => $model->errors];
        }

        return ['model' => $model, 'errors' => null];
    }

    public function findOneOrFail(int $id): Vacancy
    {
        $model = Vacancy::findOne($id);
        if ($model === null) {
            throw new \yii\web\NotFoundHttpException("Вакансия #{$id} не найдена");
        }
        return $model;
    }
}

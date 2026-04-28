<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * @property int    $id
 * @property string $title
 * @property string $description
 * @property int    $salary
 * @property string $created_at
 * @property string $updated_at
 */

class Vacancy extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'vacancy';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public function rules(): array
    {
        return [
            [['title', 'description', 'salary'], 'required',
                'message' => 'Поле «{attribute}» обязательно'],
            ['title', 'string', 'max' => 255,
                'tooLong' => 'Название слишком длинное (максимум 255 символов)'],
            ['description', 'string', 'min' => 1, 'max' => 10000],
            ['salary', 'integer', 'min' => 0, 'max' => 10_000_000,
                'message' => 'Зарплата должна быть целым числом',
                'tooSmall' => 'Зарплата не может быть отрицательной'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id'          => 'ID',
            'title'       => 'Название',
            'description' => 'Описание',
            'salary'      => 'Зарплата',
            'created_at'  => 'Дата создания',
            'updated_at'  => 'Дата обновления',
        ];
    }

    public function fields(): array
    {
        return [
            'id',
            'title',
            'description',
            'salary',
            'created_at',
            'updated_at',
        ];
    }
}

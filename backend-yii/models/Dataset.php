<?php

namespace app\models;

use yii\db\ActiveRecord;

class Dataset extends ActiveRecord
{
    public static function tableName()
    {
        return 'datasets';
    }

    public function rules()
    {
        return [
            [['title', 'category_id', 'year'], 'required'],
            [['description'], 'string'],
            [['category_id', 'year', 'user_id'], 'integer'],
            [['title', 'creator', 'file_path', 'image', 'status'], 'string', 'max' => 255],
            [['approved_at', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

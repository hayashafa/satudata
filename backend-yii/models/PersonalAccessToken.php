<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class PersonalAccessToken extends ActiveRecord
{
    public static function tableName()
    {
        return 'personal_access_tokens';
    }

    public static function issueToken(int $userId, string $name = 'API Token'): string
    {
        $plain = Yii::$app->security->generateRandomString(40);
        $hash = hash('sha256', $plain);

        $now = date('Y-m-d H:i:s');

        $token = new self();
        $token->tokenable_type = 'App\\Models\\User';
        $token->tokenable_id = $userId;
        $token->name = $name;
        $token->token = $hash;
        $token->abilities = json_encode(['*']);
        $token->created_at = $now;
        $token->updated_at = $now;

        if (!$token->save(false)) {
            throw new \RuntimeException('Gagal membuat personal access token.');
        }

        return $plain;
    }

    public static function findByPlainToken(string $plain): ?self
    {
        $hash = hash('sha256', $plain);

        return self::find()->where(['token' => $hash])->one();
    }
}

<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'users';
    }


    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $pat = PersonalAccessToken::findByPlainToken($token);
        if (!$pat) {
            return null;
        }

        return static::findOne(['id' => $pat->tokenable_id]);
    }

    public static function findByUsername($username)
    {
        return static::findOne(['email' => $username]);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return (string) ($this->remember_token ?? '');
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() !== '' && $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        $hash = (string) ($this->password ?? '');
        if ($hash === '') {
            return false;
        }

        // Laravel uses bcrypt by default, compatible with password_verify.
        return password_verify($password, $hash);
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['password'], $fields['remember_token']);

        return $fields;
    }
}

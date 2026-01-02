<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use app\models\User;
use app\models\PersonalAccessToken;

class AuthController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    public function actionLogin()
    {
        $body = Yii::$app->request->getBodyParams();
        $email = $body['email'] ?? null;
        $password = $body['password'] ?? null;

        if (!$email || !$password) {
            throw new BadRequestHttpException('email dan password wajib diisi.');
        }

        $user = User::find()->where(['email' => $email])->one();
        if (!$user || !$user->validatePassword($password)) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Email atau password salah.'];
        }

        if ($user->is_frozen) {
            Yii::$app->response->statusCode = 403;
            return ['error' => 'Akun Anda dibekukan oleh administrator.'];
        }

        $plainToken = PersonalAccessToken::issueToken($user->id, 'Yii2 API Token');

        return [
            'token_type' => 'Bearer',
            'access_token' => $plainToken,
            'user' => [
                'id' => (int) $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'is_frozen' => (int) ($user->is_frozen ?? 0),
                'workplace' => $user->workplace ?? null,
                'gender' => $user->gender ?? null,
            ],
        ];
    }

    public function actionOptions()
    {
        Yii::$app->response->headers->set('Allow', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
        return [];
    }
}

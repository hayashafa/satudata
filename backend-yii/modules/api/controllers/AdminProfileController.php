<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\Response;

class AdminProfileController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
        ];

        return $behaviors;
    }

    public function actionUpdate()
    {
        $user = Yii::$app->user->identity;
        if (!$user) {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Unauthorized'];
        }

        $body = Yii::$app->request->getBodyParams();
        $name = trim((string) ($body['name'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));

        if ($name === '' || $email === '') {
            throw new BadRequestHttpException('name dan email wajib diisi.');
        }

        $exists = $user::find()
            ->where(['email' => $email])
            ->andWhere(['<>', 'id', $user->id])
            ->exists();

        if ($exists) {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Email sudah digunakan.'];
        }

        $user->name = $name;
        $user->email = $email;
        $user->workplace = $body['workplace'] ?? null;
        $user->gender = $body['gender'] ?? null;
        $user->save(false);

        return ['data' => $user->toArray()];
    }
}

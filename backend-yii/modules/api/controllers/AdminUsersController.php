<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\User;
use app\models\Dataset;

class AdminUsersController extends Controller
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

    public function actionIndex()
    {
        $search = Yii::$app->request->get('search');
        $sort = Yii::$app->request->get('sort', 'latest');

        $query = User::find()->where(['role' => ['admin', 'superadmin']]);

        if ($search) {
            $query->andWhere(['or',
                ['like', 'name', $search],
                ['like', 'email', $search],
            ]);
        }

        switch ($sort) {
            case 'name_az':
                $query->orderBy(['name' => SORT_ASC]);
                break;
            case 'name_za':
                $query->orderBy(['name' => SORT_DESC]);
                break;
            default:
                $query->orderBy(['created_at' => SORT_DESC]);
        }

        $users = $query->asArray()->all();

        return ['data' => $users];
    }

    public function actionView($id)
    {
        $user = User::findOne(['id' => $id]);
        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'User tidak ditemukan.'];
        }

        $datasets = Dataset::find()
            ->alias('d')
            ->where(['d.user_id' => $user->id])
            ->joinWith(['category c'])
            ->orderBy(['d.created_at' => SORT_DESC])
            ->asArray()
            ->all();

        return [
            'data' => [
                'user' => $user->toArray(),
                'datasets' => $datasets,
            ],
        ];
    }

    public function actionFreeze($id)
    {
        $me = Yii::$app->user->identity;
        if (!$me || ($me->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $user = User::findOne(['id' => $id]);
        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'User tidak ditemukan.'];
        }

        $user->is_frozen = 1;
        $user->save(false);

        return ['success' => true];
    }

    public function actionUnfreeze($id)
    {
        $me = Yii::$app->user->identity;
        if (!$me || ($me->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $user = User::findOne(['id' => $id]);
        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'User tidak ditemukan.'];
        }

        $user->is_frozen = 0;
        $user->save(false);

        return ['success' => true];
    }

    public function actionDelete($id)
    {
        $me = Yii::$app->user->identity;
        if (!$me || ($me->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $user = User::findOne(['id' => $id]);
        if (!$user) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'User tidak ditemukan.'];
        }

        if ((int) $me->id === (int) $user->id || ($user->role ?? null) === 'superadmin') {
            Yii::$app->response->statusCode = 403;
            return ['error' => 'Tidak dapat menghapus pengguna ini.'];
        }

        $user->delete();

        return ['success' => true];
    }
}

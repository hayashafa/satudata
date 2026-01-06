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

    public function actionCreate()
    {
        $me = Yii::$app->user->identity;
        if (!$me || ($me->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $body = Yii::$app->request->getBodyParams();

        $name = trim((string) ($body['name'] ?? ''));
        $email = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');
        $role = (string) ($body['role'] ?? 'admin');

        if ($name === '' || $email === '' || $password === '') {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Nama, email, dan password wajib diisi.'];
        }

        if (!in_array($role, ['admin', 'superadmin'], true)) {
            $role = 'admin';
        }

        $exists = User::find()->where(['email' => $email])->exists();
        if ($exists) {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Email sudah terdaftar.'];
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->role = $role;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->is_frozen = 0;
        $user->created_at = $user->created_at ?? date('Y-m-d H:i:s');
        $user->updated_at = $user->updated_at ?? date('Y-m-d H:i:s');

        if (!$user->save(false)) {
            Yii::$app->response->statusCode = 500;
            return ['error' => 'Gagal menyimpan user baru.'];
        }

        return ['data' => $user->toArray()];
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

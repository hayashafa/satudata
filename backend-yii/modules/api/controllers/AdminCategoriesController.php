<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use app\models\Category;

class AdminCategoriesController extends Controller
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
        $categories = Category::find()->orderBy(['name' => SORT_ASC])->asArray()->all();

        return ['data' => $categories];
    }

    public function actionCreate()
    {
        $user = Yii::$app->user->identity;
        if (!$user || ($user->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $body = Yii::$app->request->getBodyParams();
        $name = trim((string) ($body['name'] ?? ''));

        if ($name === '') {
            throw new BadRequestHttpException('name wajib diisi.');
        }

        $exists = Category::find()->where(['name' => $name])->exists();
        if ($exists) {
            Yii::$app->response->statusCode = 422;
            return ['error' => 'Kategori sudah ada.'];
        }

        $category = new Category();
        $category->name = $name;
        $category->save(false);

        return ['data' => $category->toArray()];
    }

    public function actionDelete($id)
    {
        $user = Yii::$app->user->identity;
        if (!$user || ($user->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $category = Category::findOne(['id' => $id]);
        if (!$category) {
            Yii::$app->response->statusCode = 404;
            return ['error' => 'Kategori tidak ditemukan.'];
        }

        if ($category->getDatasets()->exists()) {
            Yii::$app->response->statusCode = 409;
            return ['error' => 'Kategori tidak dapat dihapus karena sudah digunakan pada dataset.'];
        }

        $category->delete();

        return ['success' => true];
    }
}

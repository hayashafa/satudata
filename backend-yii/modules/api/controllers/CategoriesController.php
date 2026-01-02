<?php

namespace app\modules\api\controllers;

use yii\rest\Controller;
use yii\web\Response;
use app\models\Category;

class CategoriesController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/json'] = Response::FORMAT_JSON;

        return $behaviors;
    }

    public function actionIndex()
    {
        $categories = Category::find()->orderBy(['name' => SORT_ASC])->asArray()->all();

        return ['data' => $categories];
    }
}

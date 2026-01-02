<?php

namespace app\modules\api\controllers;

use Yii;
use yii\rest\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\models\Dataset;

class DatasetsController extends Controller
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
        $q = Yii::$app->request->get('q');
        $type = Yii::$app->request->get('type');
        $year = Yii::$app->request->get('year');
        $creator = Yii::$app->request->get('creator');
        $format = Yii::$app->request->get('format');
        $sort = Yii::$app->request->get('sort', 'latest');

        $query = Dataset::find()
            ->alias('d')
            ->where(['d.status' => 'approved'])
            ->joinWith(['category c']);

        if ($q) {
            $query->andWhere(['or',
                ['like', 'd.title', $q],
                ['like', 'd.description', $q],
            ]);
        }

        if ($type) {
            $query->andWhere(['d.category_id' => $type]);
        }

        if ($year) {
            $query->andWhere(['d.year' => $year]);
        }

        if ($creator) {
            $query->andWhere(['like', 'd.creator', $creator]);
        }

        if ($format) {
            $query->andWhere(['like', 'd.file_path', '.' . $format, false]);
        }

        switch ($sort) {
            case 'oldest':
                $query->orderBy(['d.created_at' => SORT_ASC]);
                break;
            case 'title_az':
                $query->orderBy(['d.title' => SORT_ASC]);
                break;
            case 'title_za':
                $query->orderBy(['d.title' => SORT_DESC]);
                break;
            default:
                $query->orderBy(['d.created_at' => SORT_DESC]);
        }

        $datasets = $query->asArray()->all();

        return [
            'data' => $datasets,
        ];
    }

    public function actionView($id)
    {
        $dataset = Dataset::find()
            ->alias('d')
            ->where(['d.id' => $id, 'd.status' => 'approved'])
            ->joinWith(['category c'])
            ->asArray()
            ->one();

        if (!$dataset) {
            throw new NotFoundHttpException('Dataset tidak ditemukan.');
        }

        return [
            'data' => $dataset,
        ];
    }
}

<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\Controller;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\db\Query;
use app\models\Dataset;
use app\models\User;

class AdminDashboardController extends Controller
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

    public function actionSummary()
    {
        $user = Yii::$app->user->identity;

        $totalDatasets = (int) Dataset::find()->count();
        $incomingDatasets = (int) Dataset::find()->where(['status' => 'pending'])->count();
        $approvedDatasets = (int) Dataset::find()->where(['status' => 'approved'])->count();
        $totalUsers = (int) User::find()->count();

        $latestDatasets = [];
        $topUploaders = [];

        if ($user && ($user->role ?? null) !== 'superadmin') {
            $totalDatasets = (int) Dataset::find()->where(['user_id' => $user->id])->count();
            $incomingDatasets = (int) Dataset::find()->where(['user_id' => $user->id, 'status' => 'pending'])->count();
        } else {
            $latestDatasets = Dataset::find()
                ->alias('d')
                ->joinWith(['category c', 'user u'])
                ->orderBy(['d.created_at' => SORT_DESC])
                ->limit(10)
                ->asArray()
                ->all();

            $topUploaders = (new Query())
                ->from(['u' => 'users'])
                ->where(['u.role' => ['admin', 'superadmin']])
                ->leftJoin(['d' => 'datasets'], 'd.user_id = u.id')
                ->groupBy(['u.id'])
                ->orderBy(['datasets_count' => SORT_DESC])
                ->limit(5)
                ->select([
                    'id' => 'u.id',
                    'name' => 'u.name',
                    'email' => 'u.email',
                    'role' => 'u.role',
                    'datasets_count' => 'COUNT(d.id)',
                ])
                ->all();
        }

        return [
            'totalDatasets' => $totalDatasets,
            'incomingDatasets' => $incomingDatasets,
            'approvedDatasets' => $approvedDatasets,
            'totalUsers' => $totalUsers,
            'latestDatasets' => $latestDatasets,
            'topUploaders' => $topUploaders,
        ];
    }

    public function actionRekapanUser()
    {
        $user = Yii::$app->user->identity;
        if (!$user || ($user->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $rows = (new Query())
            ->from(['u' => 'users'])
            ->where(['u.role' => ['admin', 'superadmin']])
            ->leftJoin(['d' => 'datasets'], 'd.user_id = u.id')
            ->groupBy(['u.id'])
            ->orderBy(['datasets_count' => SORT_DESC])
            ->select([
                'id' => 'u.id',
                'name' => 'u.name',
                'email' => 'u.email',
                'role' => 'u.role',
                'datasets_count' => 'COUNT(d.id)',
                'approved_datasets_count' => "SUM(CASE WHEN d.status = 'approved' THEN 1 ELSE 0 END)",
                'pending_datasets_count' => "SUM(CASE WHEN d.status = 'pending' THEN 1 ELSE 0 END)",
                'edited_datasets_count' => "SUM(CASE WHEN d.updated_at > d.created_at THEN 1 ELSE 0 END)",
            ])
            ->all();

        return [
            'data' => $rows,
        ];
    }
}

<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\helpers\FileHelper;
use yii\rest\Controller;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use app\models\Dataset;

class AdminDatasetsController extends Controller
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

    protected function laravelPublicStorageRoot(): string
    {
        $laravelRoot = dirname(Yii::getAlias('@app'));
        return $laravelRoot . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'public';
    }

    protected function storeUploadedFile(UploadedFile $file, string $relativeDir): string
    {
        $root = $this->laravelPublicStorageRoot();
        $targetDir = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativeDir);
        FileHelper::createDirectory($targetDir);

        $safeBase = preg_replace('/[^A-Za-z0-9_-]/', '_', pathinfo($file->name, PATHINFO_FILENAME));
        $ext = strtolower((string) $file->getExtension());
        $filename = $safeBase . '_' . time() . '_' . mt_rand(1000, 9999) . ($ext ? '.' . $ext : '');

        $full = $targetDir . DIRECTORY_SEPARATOR . $filename;
        $file->saveAs($full);

        return rtrim($relativeDir, '/') . '/' . $filename;
    }

    protected function deleteRelativeFile(?string $relativePath): void
    {
        if (!$relativePath) {
            return;
        }

        $root = $this->laravelPublicStorageRoot();
        $full = $root . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $relativePath);

        if (is_file($full)) {
            @unlink($full);
        }
    }

    public function actionIndex()
    {
        $status = Yii::$app->request->get('status');
        $userId = Yii::$app->request->get('user_id');
        $me = Yii::$app->user->identity;

        $query = Dataset::find()
            ->alias('d')
            ->joinWith(['category c', 'user u'])
            ->orderBy(['d.created_at' => SORT_DESC]);

        if ($status) {
            $query->andWhere(['d.status' => $status]);
        }

        if ($me && ($me->role ?? null) !== 'superadmin') {
            $query->andWhere(['d.user_id' => $me->id]);
        } elseif ($userId) {
            $query->andWhere(['d.user_id' => $userId]);
        }

        return ['data' => $query->asArray()->all()];
    }

    public function actionView($id)
    {
        $me = Yii::$app->user->identity;

        $dataset = Dataset::find()
            ->alias('d')
            ->joinWith(['category c', 'user u'])
            ->where(['d.id' => $id])
            ->asArray()
            ->one();

        if (!$dataset) {
            throw new NotFoundHttpException('Dataset tidak ditemukan.');
        }

        if ($me && ($me->role ?? null) !== 'superadmin' && (int) ($dataset['user_id'] ?? 0) !== (int) $me->id) {
            throw new ForbiddenHttpException();
        }

        return ['data' => $dataset];
    }

    public function actionCreate()
    {
        $me = Yii::$app->user->identity;

        $title = trim((string) Yii::$app->request->post('title', ''));
        $categoryId = Yii::$app->request->post('category_id');
        $year = Yii::$app->request->post('year');

        if ($title === '' || !$categoryId || !$year) {
            throw new BadRequestHttpException('title, category_id, year wajib diisi.');
        }

        $dataset = new Dataset();
        $dataset->title = $title;
        $dataset->description = Yii::$app->request->post('description');
        $dataset->category_id = (int) $categoryId;
        $dataset->year = (int) $year;
        $dataset->user_id = $me ? (int) $me->id : null;
        $dataset->creator = $me ? (string) $me->name : null;
        $dataset->status = 'pending';
        $dataset->save(false);

        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $dataset->file_path = $this->storeUploadedFile($file, 'datasets/files');
        }

        $image = UploadedFile::getInstanceByName('image');
        if ($image) {
            $dataset->image = $this->storeUploadedFile($image, 'datasets/images');
        }

        $dataset->save(false);

        return ['data' => $dataset->toArray()];
    }

    public function actionUpdate($id)
    {
        $me = Yii::$app->user->identity;

        $dataset = Dataset::findOne(['id' => $id]);
        if (!$dataset) {
            throw new NotFoundHttpException('Dataset tidak ditemukan.');
        }

        if ($me && ($me->role ?? null) !== 'superadmin') {
            if ((int) $dataset->user_id !== (int) $me->id || $dataset->status === 'approved') {
                throw new ForbiddenHttpException();
            }
        }

        $title = trim((string) Yii::$app->request->post('title', $dataset->title));
        $categoryId = Yii::$app->request->post('category_id', $dataset->category_id);
        $year = Yii::$app->request->post('year', $dataset->year);

        if ($title === '' || !$categoryId || !$year) {
            throw new BadRequestHttpException('title, category_id, year wajib diisi.');
        }

        $dataset->title = $title;
        $dataset->description = Yii::$app->request->post('description', $dataset->description);
        $dataset->category_id = (int) $categoryId;
        $dataset->year = (int) $year;

        $file = UploadedFile::getInstanceByName('file');
        if ($file) {
            $this->deleteRelativeFile($dataset->file_path);
            $dataset->file_path = $this->storeUploadedFile($file, 'datasets/files');
        }

        $image = UploadedFile::getInstanceByName('image');
        if ($image) {
            $this->deleteRelativeFile($dataset->image);
            $dataset->image = $this->storeUploadedFile($image, 'datasets/images');
        }

        $dataset->save(false);

        return ['data' => $dataset->toArray()];
    }

    public function actionApprove($id)
    {
        $me = Yii::$app->user->identity;
        if (!$me || ($me->role ?? null) !== 'superadmin') {
            throw new ForbiddenHttpException();
        }

        $dataset = Dataset::findOne(['id' => $id]);
        if (!$dataset) {
            throw new NotFoundHttpException('Dataset tidak ditemukan.');
        }

        $dataset->status = 'approved';
        $dataset->approved_at = date('Y-m-d H:i:s');
        $dataset->save(false);

        return ['success' => true];
    }

    public function actionDelete($id)
    {
        $me = Yii::$app->user->identity;

        $dataset = Dataset::findOne(['id' => $id]);
        if (!$dataset) {
            throw new NotFoundHttpException('Dataset tidak ditemukan.');
        }

        if ($me && ($me->role ?? null) !== 'superadmin') {
            if ((int) $dataset->user_id !== (int) $me->id || $dataset->status === 'approved') {
                throw new ForbiddenHttpException();
            }
        }

        $this->deleteRelativeFile($dataset->file_path);
        $this->deleteRelativeFile($dataset->image);

        $dataset->delete();

        return ['success' => true];
    }
}

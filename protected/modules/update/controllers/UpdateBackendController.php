<?php

namespace application\modules\update\controllers;

use Yii;
use yupe\components\controllers\BackController;
use yupe\widgets\YFlashMessages;

class UpdateBackendController extends BackController
{
    public function actionIndex()
    {
        if (Yii::app()->getRequest()->getIsPostRequest()) {

            $updates = Yii::app()->updateManager->getModulesUpdateList(
                Yii::app()->moduleManager->getModules()
            );

            Yii::app()->ajax->success(
                $this->renderPartial(
                    '_modules',
                    array(
                        'result' => $updates['result'],
                        'modules' => $updates['modules'],
                        'total' => $updates['total']
                    ),
                    true
                )
            );
        }

        $this->render('index');
    }

    public function actionUpdate()
    {
        if(!Yii::app()->getRequest()->getIsPostRequest()){
            throw new \CHttpException(404);
        }

        $module = Yii::app()->getRequest()->getPost('module');

        $version = Yii::app()->getRequest()->getPost('version');

        if(empty($module) || empty($version)) {
            throw new \CHttpException(404);
        }

        if(Yii::app()->updateManager->getModuleRemoteFile($module, $version)) {
            // установка новой версии модуля
            if(Yii::app()->updateManager->update($module, $version)) {
                Yii::app()->getUser()->setFlash(YFlashMessages::SUCCESS_MESSAGE, Yii::t('UpdateModule.update', 'Module updated!'));
                Yii::app()->ajax->success();
            }
        }

        Yii::app()->ajax->failure();
    }
} 
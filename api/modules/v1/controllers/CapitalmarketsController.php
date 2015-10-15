<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Capitalmarkets controller
 */
class CapitalmarketsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'login' => ['post'],
                    'content-services' => ['post'],
                ],
            ],
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['login', 'content-services'],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login', 'content-services'],
                        'roles' => ['?'],
                    ],
                ],
            ],
        ];
    }

    public function beforeAction($event)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Content-Type: application/json; charset=utf-8');

        return parent::beforeAction($event);
    }

    public function actionLogin()
    {
        $url = 'https://citimobilechallenge.anypresenceapp.com/capitalmarkets/v1/login?client_id=' . Yii::$app->params['clientId'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => Yii::$app->request->post('username'),
            'password' => Yii::$app->request->post('password')
        ]));

        $result = curl_exec($ch);

        if (curl_errno($ch) > 0) {
            $result = json_encode(['error_message' => curl_error($ch)]);
        }

        if ($result == null) {
            $result = json_encode(['error_message' => 'Give Bible money.', 'httpcode' => curl_getinfo($ch, CURLINFO_HTTP_CODE)]);
        }

        curl_close($ch);

        return $result;
    }

    public function actionContentServices()
    {
        $url = 'https://citimobilechallenge.anypresenceapp.com/capitalmarkets/v1/content_services?client_id=' . Yii::$app->params['clientId'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: ' . Yii::$app->params['prefixAuthorization'] . Yii::$app->request->post('token')
        ]);

        $result = curl_exec($ch);

        if (curl_errno($ch) > 0) {
            $result = json_encode(['error_message' => curl_error($ch)]);
        }

        if ($result == null) {
            $result = json_encode(['error_message' => 'Give Bible money.', 'httpcode' => curl_getinfo($ch, CURLINFO_HTTP_CODE)]);
        }

        curl_close($ch);

        return $result;
    }
}

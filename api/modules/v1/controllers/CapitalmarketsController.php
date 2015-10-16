<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Capitalmarkets controller
 */
class CapitalmarketsController extends BaseController
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
        return parent::beforeAction($event);
    }

    public function actionLogin()
    {
        $username = '';
        $password = '';
        $url = $this->getCitiServerHost('/capitalmarkets/v1/login');

        if (($rawBody = json_decode(Yii::$app->request->getRawBody(), true)) !== null) {
            $username = isset($rawBody['username']) ? $rawBody['username'] : $username;
            $password = isset($rawBody['password']) ? $rawBody['password'] : $password;
        } else {
            parse_str(file_get_contents('php://input'));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCitiServerHeader());

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'username' => $username,
            'password' => $password,
        ]));

        $result = $this->CurlExec($ch);

        curl_close($ch);

        return $result;
    }

    public function actionContentServices()
    {
        $token = '';
        $url = $this->getCitiServerHost('/capitalmarkets/v1/content_services');

        if (($rawBody = json_decode(Yii::$app->request->getRawBody(), true)) !== null) {
            $token = isset($rawBody['token']) ? $rawBody['token'] : $token;
        } else {
            parse_str(file_get_contents('php://input'));
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_PORT , 443);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getCitiServerHeader($this->getCitiServerAuthorization($token)));

        $result = $this->CurlExec($ch);

        curl_close($ch);

        return $result;
    }
}

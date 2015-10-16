<?php

namespace api\modules\v1\controllers;

use Yii;
use yii\web\Controller;

/**
 * Base controller
 */
class BaseController extends Controller
{
    protected function getCitiServerHost($action)
    {
        return Yii::$app->params['citiServerHost'] . $action . '?client_id=' . Yii::$app->params['clientId'];
    }

    protected function getCitiServerAuthorization($token)
    {
        return Yii::$app->params['prefixAuthorization'] . $token;
    }

    protected function getCitiServerHeader($headers = null)
    {
        $citiServerHeader = [Yii::$app->params['citiServerHeader']];

        foreach ((array) $headers as $header) {
            $citiServerHeader[] = $header;
        }

        return $citiServerHeader;
    }

    protected function CurlExec($ch)
    {
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if (curl_errno($ch) > 0) {
            $result = json_encode(['error_message' => curl_error($ch), 'httpcode' => $httpcode]);
        }

        if ($result == null) {
            $result = json_encode(['error_message' => 'CITI_RETURN_NULL', 'httpcode' => $httpcode]);
        }

        return $result;
    }
}

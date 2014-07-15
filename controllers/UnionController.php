<?php

namespace app\controllers;

use app\models\LoginForm;
use Yii;
use yii\web\Controller;
use app\models\Union;
use app\models\User;
use app\models\RegisterForm;


class UnionController extends Controller
{
    public function actionDban()
    {
        $app_key = '000c71729afb3d2e00255457d0f1b78a';
        $app_secret = '63179baaeebc86b7';
        $url_auth = 'https://www.douban.com/service/auth2/auth';
        $url_token = 'https://www.douban.com/service/auth2/token';
        parse_str(Yii::$app->getRequest()->queryString, $code);
        if (!isset($code['code'])) {
            $a_params = array(
                'client_id' => $app_key,
                'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/dban'),
                'response_type' => 'code',
                'state' => md5(uniqid(rand(), true))
            );
            return $this->redirect($url_auth .'?'. http_build_query($a_params));
        } else {
            $r_curl = curl_init($url_token);
            curl_setopt_array(
                $r_curl,
                array(
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => http_build_query(
                        array(
                            'grant_type' => 'authorization_code',
                            'client_id' => $app_key,
                            'client_secret' => $app_secret,
                            'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/dban'),
                            'code' => $code['code']
                        )
                    )
                )
            );
            $s_ret = curl_exec($r_curl);
            curl_close($r_curl);
            $result = json_decode($s_ret, true);
            $token = $result['access_token'];
            $openid = $result['douban_user_id'];
            return $this->getJudge($openid, "dban");
        }
    }

    public function actionQq()
    {
        $app_key = '100554472';
        $app_secret = '5dc5a04f0ce0fc834f8552571c3853e4';
        $url_auth = 'https://graph.qq.com/oauth2.0/authorize';
        $url_token = 'https://graph.qq.com/oauth2.0/token';
        $url_me = 'https://graph.qq.com/oauth2.0/me';
        parse_str(Yii::$app->getRequest()->queryString, $code);
        if (!isset($code['code'])) {
            $a_params = array(
                'client_id' => $app_key,
                'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/qq'),
                'response_type' => 'code',
                'state' => md5(uniqid(rand(), true))
            );
            return $this->redirect($url_auth .'?'. http_build_query($a_params));
        } else {
            $r_curl = curl_init($url_token);
            curl_setopt_array(
                $r_curl,
                array(
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => http_build_query(
                        array(
                            'grant_type' => 'authorization_code',
                            'client_id' => $app_key,
                            'client_secret' => $app_secret,
                            'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/qq'),
                            'code' => $code['code']
                        )
                    )
                )
            );
            $s_ret = curl_exec($r_curl);
            curl_close($r_curl);

            $arr = explode("&", $s_ret);
            $token = $arr['0'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_URL, $url_me .'?'. $token);
            $response =  curl_exec($ch);
            curl_close($ch);
            if (strpos($response, "callback") !== false) {
                $lpos = strpos($response, "(");
                $rpos = strrpos($response, ")");
                $response = substr($response, $lpos + 1, $rpos - $lpos -1);
            }
            $result = json_decode($response, true);
            $openid = $result['openid'];
            return $this->getJudge($openid, "qq");
        }
    }

    public function actionWeibo()
    {
        $app_key = '2461373634';
        $app_secret = 'f76620249870efbe929c4c2143a13571';
        $url_auth = 'https://api.weibo.com/oauth2/authorize';
        $url_token = 'https://api.weibo.com/oauth2/access_token';
        parse_str(Yii::$app->getRequest()->queryString, $code);
        if (!isset($code['code'])) {
            $a_params = array(
                'client_id' => $app_key,
                'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/weibo')
            );
            return $this->redirect($url_auth .'?'. http_build_query($a_params));
        } else {
            $r_curl = curl_init($url_token);
            curl_setopt_array(
                $r_curl,
                array(
                    CURLOPT_POST => true,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POSTFIELDS => http_build_query(
                        array(
                            'grant_type' => 'authorization_code',
                            'client_id' => $app_key,
                            'client_secret' => $app_secret,
                            'redirect_uri' => Yii::$app->request->hostInfo . Yii::$app->urlManager->createUrl('union/weibo'),
                            'code' => $code['code']
                        )
                    )
                )
            );
            $s_ret = curl_exec($r_curl);
            curl_close($r_curl);
            $result = json_decode($s_ret, true);
            $openid = $result['uid'];
            return $this->getJudge($openid, "weibo");
        }
    }

    private function getJudge($openid, $type)
    {
        $union = Union::findone(['passport' => $openid, 'source' => $type]);
        if ($union) {
            if($union->status == 'passed') {
                $user = User::findOne($union->customer);
                Yii::$app->user->login($user, 3600*24);
                return $this->actionInfo($user);
            } else {
                $union->status = 'passed';
                $union->save();
                $user = User::findOne($union->customer);
                Yii::$app->user->login($user, 3600*24);
                return $this->redirect(['union/info']);
            }
        } elseif (Yii::$app->user->id){
            $union = new Union();
            $union->customer = Yii::$app->user->id;
            $union->passport = $openid;
            $union->source = $type;
            $union->status = "passed";
            $union->save();
            return $this->redirect(['union/info']);
        } else {
            $rml = new RegisterForm();
            $lml = new LoginForm();
            return $this->render('register-login', [
                'rml' => $rml,
                'lml' => $lml,
                'openid' => $openid,
                'type' => $type,
            ]);
        }
    }

    public function actionRegister()
    {
        $model = new RegisterForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            $model->login();
            $union = new Union();
            $union->customer = $model->id;
            $union->passport = $_POST['openid'];
            $union->source = $_POST['type'];
            $union->status = "passed";
            $union->save();
            return $this->redirect(['union/info']);
        }
        $rml = new RegisterForm();
        $lml = new LoginForm();
        return $this->render('register-login', [
            'rml' => $rml,
            'lml' => $lml,
            'openid' => $_POST['openid'],
            'type' => $_POST['type'],
        ]);
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->login()) {
            $union = new Union();
            $union->customer = Yii::$app->user->id;
            $union->passport = $_POST['openid'];
            $union->source = $_POST['type'];
            $union->status = "passed";
            $union->save();
            return $this->redirect(['union/info']);
        }
        $rml = new RegisterForm();
        $lml = new LoginForm();
        return $this->render('register-login', [
            'rml' => $rml,
            'lml' => $lml,
            'openid' => $_POST['openid'],
            'type' => $_POST['type'],
        ]);
    }

    public function actionRemove($id)
    {
        switch($id) {
            case 'q': $type = "qq";break;
            case 'w': $type = "weibo";break;
            case 'd': $type = "dban";break;
        }
        $union = Union::findOne(['customer' => Yii::$app->user->id, 'source' => $type]);
        $union->status = 'expired';
        $union->save();
        return $this->actionInfo(Yii::$app->user);
    }

    public function actionInfo($model=null)
    {
        if($model == null) {
            $model = Yii::$app->user;
        }
        $binds = Union::findAll(['customer' => $model->id, 'status' => 'passed']);
        return $this->render('info', [
            'model' => $model,
            'binds' => $binds,
        ]);
    }

}



<?php

namespace app\modules\user\controllers;

use app\models\User;
use app\models\UserDetail;
use app\modules\core\helpers\EasyHelper;
use app\modules\core\helpers\UserHelper;
use app\modules\user\controllers\base\ModuleController;
use app\modules\user\models\LoginForm;
use app\modules\user\models\RegisterForm;
use app\modules\user\models\UserDetailForm;
use yii\filters\AccessControl;
use Yii;

class DefaultController extends ModuleController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => [
                    'login',
                    'logout',
                    'register',
                    'detail',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [
                            'login',
                            'register',
                        ],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => [
                            'logout',
                            'detail',
                        ],
                        'roles' => ['@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $this->goHome();
                },
            ],
        ];
    }

    public function actionLogin()
    {
        $model = new LoginForm();
        
        if ($model->load(Yii::$app->request->post())) {
            if($model->login()){
                EasyHelper::setSuccessMsg('登录成功');
                return $this->goHome();
            } else {
                EasyHelper::setErrorMsg('登录失败');
            }
        }

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        EasyHelper::setMessage('info', '已登出');
        return $this->goHome();
    }

    public function actionRegister()
    {
        $form = new RegisterForm();

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $user = new User();
                $user_detail = new UserDetail();

                $user->username = $form->username;
                $user->password = $form->password;

                $transaction = EasyHelper::beginTransaction();//开启事务
                $flow = $user->save(false);
                if ($flow) {
                    $user_detail->user_id = $user->user_id;
                    $user_detail->email = $form->email;
                }
                if ($flow && !$user_detail->save()) {
                    $flow = false;
                }
                if ($flow) {
                    $transaction->commit();//提交事务
                    EasyHelper::setSuccessMsg('注册成功');
                    return $this->redirect('login');
                } else {
                    $transaction->rollBack();//回滚事务
                    $form->addErrors($user->getErrors());
                    $form->addErrors($user_detail->getErrors());
                    EasyHelper::setErrorMsg('注册失败');
                }
            }
        }

        return $this->render('register', [
            'model' => $form
        ]);
    }

    public function actionDetail()
    {
        $model = UserDetail::findOne(['user_id' => UserHelper::getUserId()]);
        $form = new UserDetailForm();//在前端验证用的类，为了用验证码，另外就是日期的问题，数据库用的是时间戳，前端让用户选择日期

        if ($form->load(Yii::$app->request->post())) {
            if ($form->validate()) {
                $model->setAttributes($form->getAttributes());
                $model->birthday = $form->birthday ? strtotime($form->birthday) : 0;
                $model->updated_at = time();
                if ($model->save()) {
                    EasyHelper::setSuccessMsg('修改成功');
                    return $this->redirect(['detail', 'id' => $model->id]);
                } else {
                    EasyHelper::setErrorMsg('修改失败');
                    $form->addErrors($model->getErrors());
                }
            }
        } else {
            $form->setAttributes($model->getAttributes());
            $form->birthday = $model->birthday ? date('Y-m-d', $model->birthday) : '';
        }

        return $this->render('detail', [
            'model' => $form,
        ]);
    }
}

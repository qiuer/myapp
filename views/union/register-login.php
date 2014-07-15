<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Register-Login';
$this->params['breadcrumbs'][] = $this->title;
?>


    <input id="radio_r" type="radio" name="ch" checked="checked"> <label for="radio_r">未注册过洲际速递账户</label>&nbsp;&nbsp;&nbsp;&nbsp;
    <input id="radio_l" type="radio" name="ch" >  <label for="radio_l">已有洲际速递账户</label>

    <div class="site-register">
        <?php $form = ActiveForm::begin([
            'id' => 'register-form',
            'options' => ['class' => 'form-horizontal'],
            'action' => array('union/register'),
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>
        <?= $form->field($rml, 'username') ?>
        <?= $form->field($rml, 'password')->passwordInput() ?>
        <input type="hidden" name="openid" value="<?php echo $openid ?>">
        <input type="hidden" name="type" value="<?php echo $type ?>">
        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Register', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>

    <div class="site-login">
        <?php $form = ActiveForm::begin([
            'id' => 'login-form',
            'options' => ['class' => 'form-horizontal'],
            'action' => array('union/login'),
            'fieldConfig' => [
                'template' => "{label}\n<div class=\"col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                'labelOptions' => ['class' => 'col-lg-1 control-label'],
            ],
        ]); ?>
        <?= $form->field($lml, 'username') ?>
        <?= $form->field($lml, 'password')->passwordInput() ?>
        <?= $form->field($lml, 'rememberMe', [
            'template' => "<div class=\"col-lg-offset-1 col-lg-3\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
        ])->checkbox() ?>

        <input type="hidden" name="openid" value="<?php echo $openid ?>">
        <input type="hidden" name="type" value="<?php echo $type ?>">
        <div class="form-group">
            <div class="col-lg-offset-1 col-lg-11">
                <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
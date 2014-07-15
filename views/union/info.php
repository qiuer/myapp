<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Info';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>

    </p>

    <?php
    $arr = array();
    if(!empty($binds)) {
        foreach($binds as $bind ) {
            $arr[] = $bind->source;
        }
    echo in_array('qq', $arr) ? "<p><a href='index.php?r=union/remove&id=q'>解除QQ账号绑定</a></p>" : "<p><a href='index.php?r=union/qq'>绑定QQ账号</a></p>";
    echo in_array('weibo', $arr) ? "<p><a href='index.php?r=union/remove&id=w'>解除微博账号绑定</a></p>" : "<p><a href='index.php?r=union/weibo'>绑定微博账号</a></p>";
    echo in_array('dban', $arr) ? "<p><a href='index.php?r=union/remove&id=d'>解除豆瓣账号绑定</a></p>" : "<p><a href='index.php?r=union/dban'>绑定豆瓣账号</a></p>";

    } else {
        ?>
        <p><a href='index.php?r=union/qq'>绑定QQ账号</a></p>
        <p><a href='index.php?r=union/weibo'>绑定微博账号</a></p>
        <p><a href='index.php?r=union/dban'>绑定豆瓣账号</a></p>
    <?php } ?>
</div>

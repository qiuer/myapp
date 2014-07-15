<?php

use yii\db\Migration;

class m140715_071739_create_union_table extends Migration
{
    public function up()
    {
        $this->createTable('union' ,[
            'id' => 'pk',
            'customer' => 'int NOT NULL',
            'passport' => 'string(32) NOT NULL',
            'status' => "enum('passed','expired') NOT NULL",
            'source' => "enum('qq','weibo','dban') NOT NULL",
        ]);
    }

    public function down()
    {
        $this->dropTable('union');
    }
}

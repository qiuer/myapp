<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "union".
 *
 * @property integer $id
 * @property integer $customer
 * @property string $passport
 * @property string $status
 * @property string $source
 */
class Union extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'union';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customer', 'passport', 'status', 'source'], 'required'],
            [['customer'], 'integer'],
            [['status', 'source'], 'string'],
            [['passport'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customer' => 'Customer',
            'passport' => 'Passport',
            'status' => 'Status',
            'source' => 'Source',
        ];
    }
}

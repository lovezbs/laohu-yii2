<?php

namespace app\models\base;

use Yii;

/**
 * This is the model class for table "user_detail".
 *
 * @property string $id
 * @property string $user_id
 * @property string $avatar_file
 * @property string $gender
 * @property string $birthday
 * @property string $email
 * @property string $phone
 * @property string $resume
 * @property string $security_question
 * @property string $security_answer
 * @property string $updated_at
 */
class UserDetailBase extends \app\modules\core\extensions\HuActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_detail';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'birthday', 'updated_at'], 'integer'],
            [['gender'], 'string'],
            [['avatar_file', 'email', 'resume'], 'string', 'max' => 100],
            [['phone'], 'string', 'max' => 11],
            [['security_question'], 'string', 'max' => 30],
            [['security_answer'], 'string', 'max' => 64],
            [['user_id'], 'unique'],
            [['email'], 'unique'],
            [['phone'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户ID',
            'avatar_file' => '头像文件',
            'gender' => '性别',
            'birthday' => '生日',
            'email' => '邮箱',
            'phone' => '电话',
            'resume' => '简介',
            'security_question' => '密保问题',
            'security_answer' => '密保答案',
            'updated_at' => '修改时间',
        ];
    }
}

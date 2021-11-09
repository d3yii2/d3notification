<?php

namespace d3yii2\d3notification\models;

use \d3yii2\d3notification\models\base\D3nTypeUser as BaseD3nTypeUser;
use yii2d3\d3persons\models\User;

/**
 * This is the model class for table "d3n_type_user".
 */
class D3nTypeUser extends BaseD3nTypeUser
{
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

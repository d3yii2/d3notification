<?php

namespace d3yii2\d3notification\models;

use d3yii2\d3notification\dictionaries\D3nTypeDictionary;
use \d3yii2\d3notification\models\base\D3nType as BaseD3nType;

/**
 * This is the model class for table "d3n_type".
 */
class D3nType extends BaseD3nType
{
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        D3nTypeDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        D3nTypeDictionary::clearCache();
    }
}

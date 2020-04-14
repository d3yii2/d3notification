<?php

namespace d3yii2\d3notification\models;

use d3yii2\d3notification\dictionaries\D3nStatusDictionary;
use \d3yii2\d3notification\models\base\D3nStatus as BaseD3nStatus;

/**
 * This is the model class for table "d3n_status".
 */
class D3nStatus extends BaseD3nStatus
{
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);
        D3nStatusDictionary::clearCache();
    }

    public function afterDelete()
    {
        parent::afterDelete();
        D3nStatusDictionary::clearCache();
    }
}

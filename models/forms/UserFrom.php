<?php


namespace d3yii2\d3notification\models\forms;

use Yii;
use yii\base\Model;

class UserFrom extends Model
{
    public const OTHER = 'other';
    /**
     * @var string
     */
    public $notes;

    /** @var string[] */
    public $typeList;

    /** @var string */
    public $typeId;

    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'notes' => Yii::t('d3notification', 'Notes'),
            'typeId' => Yii::t('d3notification', 'Type')
        ]);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['typeId', 'notes'], 'required'],
            ['typeId', 'string'],
            ['notes', 'string', 'max' => 65535]
        ]);
    }

    public function typeListForDropdown(): array
    {
        $list = $this->typeList;
        $list[self::OTHER] = Yii::t('d3notification', 'Other');
        return $list;
    }

    public function load($data, $formName = null)
    {
        if (!parent::load($data, $formName)) {
            return false;
        }

        if ($this->typeId !== self::OTHER) {
            $this->notes = $this->typeList[$this->typeId];
        }

        return true;
    }
}

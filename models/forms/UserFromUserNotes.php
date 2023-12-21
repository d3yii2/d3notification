<?php


namespace d3yii2\d3notification\models\forms;

use Yii;
use yii\base\Model;

class UserFromUserNotes extends Model
{
    /**
     * @var string
     */
    public ?string $notes = null;

    /** @var string */
    public ?string $userNotes = null;

    public function attributeLabels(): array
    {
        return array_merge(parent::attributeLabels(), [
            'notes' => Yii::t('d3notification', 'Data'),
            'userNotes' => Yii::t('d3notification', 'Notes'),
        ]);
    }

    public function rules(): array
    {
        return array_merge(parent::rules(), [
            [['notes', 'userNotes'], 'string', 'max' => 65535]
        ]);
    }
}

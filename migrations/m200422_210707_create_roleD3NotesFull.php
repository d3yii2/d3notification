<?php



use yii\db\Migration;
use d3yii2\d3notification\accessRights\D3NotesFullUserRole;

class m200422_210707_create_roleD3NotesFull  extends Migration {

    public function up() {

        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3NotesFullUserRole::NAME);
        $auth->add($role);

    }

    public function down() {
        $auth = Yii::$app->authManager;
        $role = $auth->createRole(D3NotesFullUserRole::NAME);
        $auth->remove($role);
    }
}

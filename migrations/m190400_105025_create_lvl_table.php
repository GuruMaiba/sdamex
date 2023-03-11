<?php

use yii\db\Migration;
use app\models\Level;

/**
 * Handles the creation of table `lvl`.
 */
class m190400_105025_create_lvl_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('lvl', [
            'id' => $this->primaryKey(),
            'exp' => $this->integer()->defaultValue(0),
            'prize' => $this->string(),
            'isMax' => $this->boolean()->defaultValue(false),
        ]);

        $level = new Level();
            $level->isMax = true;
            $level->save();
    }

    public function safeDown()
    {
        $this->dropTable('lvl');
    }
}

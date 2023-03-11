<?php

use yii\db\Migration;

class m190530_154435_create_examaddition_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('examaddition', [
            'id' => $this->primaryKey(),
            'exercise_id' => $this->integer(),
            'task' => $this->text(),
            'text' => $this->text(),
            'decision' => $this->text(),
            'word_exp' => $this->integer()->defaultValue(1),
            'publish' => $this->boolean()->defaultValue(false),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('examaddition');
    }
}

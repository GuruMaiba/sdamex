<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examcorrelate}}`.
 */
class m190530_154237_create_examcorrelate_table extends Migration
{
    public function safeUp()
    {
        // TABLE examcorrelate (парное соотношение ответов)
        $this->createTable('examcorrelate', [
            'id' => $this->primaryKey(),
            'exercise_id' => $this->integer(),
            'task' => $this->text(),
            'text' => $this->text(),
            'decision' => $this->text(),
            'qst_name' => $this->string(),
            'ans_name' => $this->string(),
            'audio_name' => $this->string(),
            'qst_hidden' => $this->boolean()->defaultValue(false),
            'publish' => $this->boolean()->defaultValue(false),
            'pair_exp' => $this->integer()->defaultValue(1),
            'themes' => $this->string(),
        ]);

        // TABLE examcorrelate_pair (одна пара для соотношения)
        $this->createTable('examcorrelate_pair', [
            'id' => $this->primaryKey(),
            'examcorrelate_id' => $this->integer()->notNull(),
            'qst_text' => $this->text(),
            'ans_text' => $this->text(),
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('examcorrelate_pair');
        $this->dropTable('examcorrelate');
    }
}

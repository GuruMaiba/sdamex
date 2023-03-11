<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examsection}}`.
 */
class m190530_160422_create_theme_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('theme', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);

        // TABLE theme_webinar
        $this->createTable('theme_webinar', [
            'theme_id' => $this->integer()->notNull(),
            'webinar_id' => $this->integer()->notNull(),
            'PRIMARY KEY (theme_id,webinar_id)'
        ]);

        // TABLE theme_lesson
        $this->createTable('theme_lesson', [
            'theme_id' => $this->integer()->notNull(),
            'lesson_id' => $this->integer()->notNull(),
            'PRIMARY KEY (theme_id,lesson_id)'
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('theme_lesson');
        $this->dropTable('theme_webinar');
        $this->dropTable('theme');
    }
}

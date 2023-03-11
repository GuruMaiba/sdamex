<?php

use yii\db\Migration;

/**
 * Handles the creation of table `promoter`.
 */
class m150415_050000_create_promoter_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('promoter_code', [
            'code' => $this->string()->notNull(),
            'promoter_id' => $this->integer()->notNull(),
            'type' => $this->tinyInteger(2)->defaultValue(1)->notNull(),
            'reward' => $this->integer(6),
            'end_at' => $this->integer(),
            'PRIMARY KEY(code)',
        ]);
    }

    public function safeDown()
    {
        $this->dropTable('promoter_code');
    }
}

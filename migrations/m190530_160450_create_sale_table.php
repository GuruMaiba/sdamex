<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%examsection}}`.
 */
class m190530_160450_create_sale_table extends Migration
{
    public function safeUp()
    {
        $this->createTable('sale', [
            'id' => $this->primaryKey(),
            'child_id' => $this->integer(),
            'type' => $this->tinyInteger(2)->notNull(),
            'percent' => $this->tinyInteger(2)->notNull(),
            'start_at' => $this->integer(),
            'end_at' => $this->integer(),
        ]);

        // creates index for column `child_id`
        $this->createIndex(
            'idx-sale-child_id',
            'sale',
            'child_id'
        );
    }

    public function safeDown()
    {
        $this->dropIndex('idx-sale-child_id', 'sale');
        $this->dropTable('sale');
    }
}

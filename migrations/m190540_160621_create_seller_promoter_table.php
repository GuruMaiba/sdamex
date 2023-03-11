<?php

use yii\db\Migration;
use mdm\admin\components\Configs;

class m190540_160621_create_seller_promoter_table extends Migration
{
    public function safeUp()
    {
        // Люди пригласившие промотера
        $this->createTable('seller_promoter', [
            'promoter_id' => $this->integer()->notNull(),
            'seller_id' => $this->integer()->notNull(),
            'PRIMARY KEY(promoter_id, seller_id)',
        ]);

        // INDEX promoter_id
        $this->createIndex(
            'idx-seller_promoter-promoter_id',
            'seller_promoter',
            'promoter_id'
        );

        // FOREIGN KEY for promoter_id
        $this->addForeignKey(
            'fk-seller_promoter-promoter_id',
            'seller_promoter',
            'promoter_id',
            'user',
            'id',
            'CASCADE'
        );

        // INDEX seller_id
        $this->createIndex(
            'idx-seller_promoter-seller_id',
            'seller_promoter',
            'seller_id'
        );

        // FOREIGN KEY for seller_id
        $this->addForeignKey(
            'fk-seller_promoter-seller_id',
            'seller_promoter',
            'seller_id',
            'user',
            'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk-seller_promoter-seller_id', 'seller_promoter');                 // seller_id
        $this->dropIndex('idx-seller_promoter-seller_id', 'seller_promoter');
        $this->dropForeignKey('fk-seller_promoter-promoter_id', 'seller_promoter');                 // promoter_id
        $this->dropIndex('idx-seller_promoter-promoter_id', 'seller_promoter');
        $this->dropTable('seller_promoter');
    }
}

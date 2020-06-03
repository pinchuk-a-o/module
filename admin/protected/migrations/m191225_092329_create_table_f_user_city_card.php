<?php

class m191225_092329_create_table_f_user_city_card extends CDbMigration
{
    public function safeUp()
    {
        $this->createTable('{{user_city_card}}', [
            'id'          => 'pk',
            'user_id'     => 'INT UNSIGNED NOT NULL COMMENT "id пользователя у нас"',
            'customer_id' => 'INT UNSIGNED NOT NULL COMMENT "id пользователя у карты города"',
        ], 'ENGINE = innoDb  COMMENT = "связь с пользователями карт города"');

        $this->createIndex('user_id', '{{user_city_card}}', 'user_id');
        $this->createIndex('customer_id', '{{user_city_card}}', 'customer_id');
    }

    public function safeDown()
    {
        $this->dropIndex('customer_id', '{{user_city_card}}');
        $this->dropIndex('user_id', '{{user_city_card}}');

        $this->dropTable('{{user_city_card}}');
    }
}

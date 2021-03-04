<?php

use yii\db\Migration;

/**
 * Class m210304_131259_wiki
 */
class m210304_131259_wiki extends Migration
{

    public $tbOptions = '';

    // converte alias {{%tb_name}} en tb_name
    protected function getRawTableName($name)
    {
        return $this->db->schema->getRawTableName($name);
    }

    public function init()
    {
        parent::init();

        if($this->db->driverName === 'mysql' )
        {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $this->tbOptions  = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
    }

    public function safeUp()
    {
        $rawTbArticle  = $this->getRawTableName('{{%article}}');
        $rawTbAssets = $this->getRawTableName('{{%article_assets}}');

        $this->createTable($rawTbArticle, [
            'id'             => $this->primaryKey(),
            'title'          => $this->string(255)->notNull(),
            'slug'           => $this->string(20),
            'textcontent'    => $this->text(),
            'created_by'     => $this->integer(),
            'created_date'   => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
            'edited_by'      => $this->integer(),
            'edited_date'    => $this->timestamp()->defaultValue(null)->append('ON UPDATE CURRENT_TIMESTAMP'),
        ], $this->tbOptions);

        // not support sqlite..
        try{
        $this->addForeignKey($rawTbArticle . '_fkcreatedby', $rawTbArticle, 'created_by' , 'user', 'id', 'CASCADE', 'CASCADE');
        $this->addForeignKey($rawTbArticle . '_fkeditedby', $rawTbArticle, 'edited_by' , 'user', 'id', 'CASCADE', 'CASCADE');

        }
        catch(\Exception $e){
        }

        $this->createTable($rawTbAssets, [
            'id'             => $this->primaryKey(),
            'article_id'     => $this->integer()->notNull(),
            'path'           => $this->string(255),

        ], $this->tbOptions);
        
        // not support sqlite..
        try{
        $this->addForeignKey($rawTbAssets . '_fkarticle', $rawTbAsset, 'article_id' , $rawTbArticle, 'id', 'CASCADE', 'CASCADE');
        }
        catch(\Exception $e){
        }



    }

    public function safeDown()
    {
        $this->dropTable('{{%article_assets}}');
        $this->dropTable('{{%article}}');
    }
}

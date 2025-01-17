<?php

use think\migration\Migrator;

class Demo extends Migrator
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-abstractmigration-class
     *
     * The following commands can be used in this method and Phinx will
     * automatically reverse them when rolling back:
     *
     *    createTable
     *    renameTable
     *    addColumn
     *    renameColumn
     *    addIndex
     *    addForeignKey
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        // 创建测试表
        $this->table('demo', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '示例表'
        ])->addColumn('uid', 'integer', ['limit' => 10, 'null' => false, 'default' => 0, 'comment' => '用户ID'])
            ->addColumn('name', 'string', ['limit' => 30, 'null' => false, 'default' => '', 'comment' => '测试名称'])
            ->addColumn('create_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addColumn('delete_time', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '删除时间'])
            ->addIndex('uid')
            ->addIndex('create_time')
            ->addIndex('update_time')
            ->addIndex('delete_time')
            ->create();
    }
}

<?php

use think\migration\Migrator;

class Basic extends Migrator
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
        $this->createRuleTable();
        $this->createRoleTable();
        $this->createUserTable();
    }

    /**
     * 创建权限表
     *
     * @return void
     */
    private function createRuleTable(): void
    {
        $this->table('rule', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '权限表'
        ])->addColumn('pid', 'integer', ['signed' => false, 'limit' => 10, 'null' => false, 'default' => 0, 'comment' => '上级ID'])
            ->addColumn('type', 'tinyinteger', ['signed' => false, 'limit' => 3, 'null' => false, 'default' => 0, 'comment' => '类型：1菜单；2API'])
            ->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'default' => '', 'comment' => '权限名称'])
            ->addColumn('url', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '权限链接'])
            ->addColumn('sort', 'tinyinteger', ['signed' => false, 'limit' => 3, 'null' => false, 'default' => 100, 'comment' => '排序'])
            ->addColumn('create_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addColumn('delete_time', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '删除时间'])
            ->addIndex('pid')
            ->addIndex('type')
            ->addIndex('name')
            ->addIndex('url')
            ->addIndex('sort')
            ->addIndex('create_time')
            ->addIndex('update_time')
            ->addIndex('delete_time')
            ->create();
    }

    /**
     * 创建角色表
     *
     * @return void
     */
    private function createRoleTable(): void
    {
        $this->table('role', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '角色表'
        ])->addColumn('name', 'string', ['limit' => 50, 'null' => false, 'default' => '', 'comment' => '角色名称'])
            ->addColumn('rules', 'text', ['comment' => '权限集合'])
            ->addColumn('status', 'tinyinteger', ['signed' => false, 'limit' => 3, 'null' => false, 'default' => 1, 'comment' => '状态：1正常；2禁用'])
            ->addColumn('create_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addColumn('delete_time', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '删除时间'])
            ->addIndex('name')
            ->addIndex('status')
            ->addIndex('create_time')
            ->addIndex('update_time')
            ->addIndex('delete_time')
            ->create();
    }

    /**
     * 创建用户表
     *
     * @return void
     */
    private function createUserTable(): void
    {
        $this->table('user', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '用户表'
        ])->addColumn('account', 'string', ['limit' => 50, 'null' => false, 'default' => '', 'comment' => '用户名'])
            ->addColumn('password', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '密码'])
            ->addColumn('mobile', 'string', ['limit' => 20, 'null' => false, 'default' => '', 'comment' => '手机号'])
            ->addColumn('email', 'string', ['limit' => 50, 'null' => false, 'default' => '', 'comment' => '邮箱'])
            ->addColumn('roles', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '角色集合'])
            ->addColumn('status', 'tinyinteger', ['signed' => false, 'limit' => 3, 'null' => false, 'default' => 1, 'comment' => '状态：1正常；2禁用'])
            ->addColumn('create_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('update_time', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addColumn('delete_time', 'datetime', ['null' => false, 'default' => '0000-00-00 00:00:00', 'comment' => '删除时间'])
            ->addIndex(['account', 'password'])
            ->addIndex('mobile')
            ->addIndex('email')
            ->addIndex('roles')
            ->addIndex('status')
            ->addIndex('create_time')
            ->addIndex('update_time')
            ->addIndex('delete_time')
            ->create();
    }
}

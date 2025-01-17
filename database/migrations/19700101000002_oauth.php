<?php

use think\migration\Migrator;

class Oauth extends Migrator
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
        $this->createOauthAccessTokensTable();
        $this->createOauthRefreshTokensTable();
        $this->createOauthAuthorizationCodesTable();
        $this->createOauthClientsTable();
        $this->createOauthScopesTable();
        $this->createOauthClientScopesTable();
    }

    /**
     * 创建access_tokens表
     *
     * @return void
     */
    public function createOauthAccessTokensTable(): void
    {
        // 创建access_tokens表
        $this->table('oauth_access_tokens', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => 'access_tokens表'
        ])
            ->addColumn('access_token', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'comment' => 'access_token'])
            ->addColumn('user_id', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '用户标识'])
            ->addColumn('client_id', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '客户标识'])
            ->addColumn('scopes', 'string', ['limit' => 4000, 'null' => false, 'default' => '', 'comment' => '权限范围'])
            ->addColumn('revoked', 'tinyinteger', ['limit' => 3, 'null' => false, 'default' => 0, 'comment' => '是否已撤销：1是；0否'])
            ->addColumn('expires_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '过期时间'])
            ->addIndex('access_token')
            ->addIndex('user_id')
            ->addIndex('client_id')
            ->addIndex('revoked')
            ->addIndex('expires_at')
            ->create();
    }

    /**
     * 创建refresh_tokens表
     *
     * @return void
     */
    public function createOauthRefreshTokensTable(): void
    {
        // 创建refresh_tokens表
        $this->table('oauth_refresh_tokens', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => 'refresh_tokens表'
        ])
            ->addColumn('refresh_token', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'comment' => 'refresh_token'])
            ->addColumn('access_token', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'comment' => 'access_token'])
            ->addColumn('revoked', 'tinyinteger', ['limit' => 3, 'null' => false, 'default' => 0, 'comment' => '是否已撤销：1是；0否'])
            ->addColumn('expires_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '过期时间'])
            ->addIndex('refresh_token')
            ->addIndex('access_token')
            ->addIndex('revoked')
            ->addIndex('expires_at')
            ->create();
    }

    /**
     * 创建authorization_codes表
     *
     * @return void
     */
    public function createOauthAuthorizationCodesTable(): void
    {
        // 创建authorization_codes表
        $this->table('oauth_authorization_codes', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => 'authorization_codes表'
        ])
            ->addColumn('code', 'string', ['limit' => 100, 'null' => false, 'default' => '', 'comment' => 'code'])
            ->addColumn('user_id', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '用户标识'])
            ->addColumn('client_id', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '客户标识'])
            ->addColumn('scopes', 'string', ['limit' => 4000, 'null' => false, 'default' => '', 'comment' => '权限范围'])
            ->addColumn('revoked', 'tinyinteger', ['limit' => 3, 'null' => false, 'default' => 0, 'comment' => '是否已撤销：1是；0否'])
            ->addColumn('expires_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '过期时间'])
            ->addIndex('code')
            ->addIndex('user_id')
            ->addIndex('client_id')
            ->addIndex('revoked')
            ->addIndex('expires_at')
            ->create();
    }

    /**
     * 创建客户表
     *
     * @return void
     */
    public function createOauthClientsTable(): void
    {
        // 创建clients表
        $this->table('oauth_clients', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '客户表'
        ])
            ->addColumn('user_id', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '用户标识'])
            ->addColumn('name', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '客户名称'])
            ->addColumn('identifier', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '客户标识'])
            ->addColumn('secret', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '客户秘钥'])
            ->addColumn('redirect_uri', 'string', ['limit' => 2000, 'null' => false, 'default' => '', 'comment' => '重定向链接'])
            ->addColumn('grant_types', 'string', ['limit' => 255, 'null' => false, 'default' => '', 'comment' => '授权类型'])
            ->addColumn('revoked', 'tinyinteger', ['limit' => 3, 'null' => false, 'default' => 0, 'comment' => '是否已撤销：1是；0否'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addIndex('user_id')
            ->addIndex('name')
            ->addIndex('identifier')
            ->addIndex('secret')
            ->addIndex('grant_types')
            ->addIndex('revoked')
            ->addIndex('created_at')
            ->addIndex('updated_at')
            ->insert([
                'name'         => 'test',
                'identifier'   => 'shippingID',
                'secret'       => 'shippingSecret',
                'redirect_uri' => 'http://your-client.com/callback',
                'grant_types'  => 'authorization_code',
            ])
            ->create();
    }

    /**
     * 创建权限范围表
     *
     * @return void
     */
    public function createOauthScopesTable(): void
    {
        // 创建scopes表
        $this->table('oauth_scopes', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '权限范围表'
        ])
            ->addColumn('scope', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '权限范围名称'])
            ->addColumn('description', 'string', ['limit' => 4000, 'null' => false, 'default' => '', 'comment' => '权限范围介绍'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addIndex('scope')
            ->addIndex('created_at')
            ->addIndex('updated_at')
            ->insert([
                [
                    'scope'       => 'link_shop',
                    'description' => 'link_shop',
                ],
                [
                    'scope'       => 'unlink_shop',
                    'description' => 'unlink_shop',
                ]
            ])
            ->create();
    }

    /**
     * 创建客户权限范围表
     *
     * @return void
     */
    public function createOauthClientScopesTable(): void
    {
        // 创建scopes表
        $this->table('oauth_client_scopes', [
            'engine'    => 'Innodb',
            'collation' => 'utf8mb4_general_ci',
            'comment'   => '客户权限范围表'
        ])
            ->addColumn('client_id', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '客户标识'])
            ->addColumn('scope', 'string', ['limit' => 80, 'null' => false, 'default' => '', 'comment' => '权限范围名称'])
            ->addColumn('created_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP', 'comment' => '创建时间'])
            ->addColumn('updated_at', 'datetime', ['null' => false, 'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP', 'comment' => '更新时间'])
            ->addIndex('client_id')
            ->addIndex('scope')
            ->addIndex('created_at')
            ->addIndex('updated_at')
            ->insert([
                [
                    'client_id' => 'shippingID',
                    'scope'     => 'link_shop',
                ],
                [
                    'client_id' => 'shippingID',
                    'scope'     => 'unlink_shop',
                ]
            ])
            ->create();
    }
}

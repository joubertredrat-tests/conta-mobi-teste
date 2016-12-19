<?php
/**
 * Classe contendo todos os dados para montadem do schema
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\Lib;

use \Doctrine\DBAL\Schema\Schema;
use \Doctrine\DBAL\Platforms\MySqlPlatform;
use AcmeCorp\Api\Lib\Doctrine;

class InstallSchema
{
    /**
     * Construtor de classe
     *
     * @return void
     */
    private function __construct()
    {
        //Objeto vazio
    }

    /**
     * Executa a inclusao do schema
     *
     * @return void
     */
    public static function run()
    {
        $schema = new Schema();

        $products = $schema->createTable('products');
        $products->addColumn(
            'id',
            'integer',
            [
                'autoincrement' => true,
                'comment' => 'Identificador do produto'
            ]
        );
        $products->addColumn(
            'username',
            'string',
            [
                'length' => 200,
                'comment' => 'Nome do produto'
            ]
        );
        $products->addColumn(
            'price',
            'decimal',
            [
                'precision' => 10,
                'scale' => 2,
                'comment' => 'Preço do produto'
            ]
        );
        $products->addColumn('stock', 'integer', ['comment' => 'Estoque do produto']);
        $products->addColumn(
            'date_insert',
            'datetime',
            [
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Data de inclusão do registro'
            ]
        );
        $products->addColumn(
            'date_update',
            'datetime',
            [
                'notnull' => false,
                'default' => "NULL ON UPDATE CURRENT_TIMESTAMP",
                'comment' => 'Data de alteração do registro'
            ]
        );
        $products->setPrimaryKey(['id']);

        $users = $schema->createTable('users');
        $users->addColumn(
            'id',
            'integer',
            [
                'autoincrement' => true,
                'comment' => 'Identificador do usuário'
            ]
        );
        $users->addColumn(
            'name',
            'string',
            [
                'length' => 150,
                'comment' => 'Nome do usuário'
            ]
        );
        $users->addColumn(
            'email',
            'string',
            [
                'length' => 150,
                'comment' => 'E-mail do usuário'
            ]
        );
        $users->addColumn(
            'password',
            'string',
            [
                'length' => 200,
                'comment' => 'Senha do usuário'
            ]
        );
        $users->addColumn(
            'admin',
            'boolean',
            [
                'length' => 200,
                'comment' => 'Delimitador de usuário como admin'
            ]
        );
        $users->addColumn(
            'date_insert',
            'datetime',
            [
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Data de inclusão do registro'
            ]
        );
        $users->addColumn(
            'date_update',
            'datetime',
            [
                'notnull' => false,
                'default' => "NULL ON UPDATE CURRENT_TIMESTAMP",
                'comment' => 'Data de alteração do registro'
            ]
        );
        $users->setPrimaryKey(['id']);

        $tokens = $schema->createTable('tokens');
        $tokens->addColumn(
            'id',
            'integer',
            [
                'autoincrement' => true,
                'comment' => 'Identificador do token'
            ]
        );
        $tokens->addColumn(
            'token_key',
            'string',
            [
                'length' => 300,
                'comment' => 'Chave do token'
            ]
        );
        $tokens->addColumn(
            'expires',
            'datetime',
            [
                'comment' => 'Data de expiração do token'
            ]
        );
        $tokens->addColumn(
            'date_insert',
            'datetime',
            [
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Data de inclusão do registro'
            ]
        );
        $tokens->addColumn(
            'users_id',
            'integer',
            [
                'comment' => 'Identificador do usuário'
            ]
        );
        $tokens->setPrimaryKey(['id']);
        $tokens->addIndex(['users_id']);
        $tokens->addForeignKeyConstraint(
            $users,
            ['users_id'],
            ['id'],
            [
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE'
            ]
        );

        $logs = $schema->createTable('logs');
        $logs->addColumn(
            'id',
            'integer',
            [
                'autoincrement' => true,
                'comment' => 'Identificador do log'
            ]
        );
        $logs->addColumn(
            'operation',
            'text',
            [
                'comment' => 'Operação realizada'
            ]
        );
        $logs->addColumn(
            'type',
            'string',
            [
                'length' => 45,
                'comment' => 'Tipo de peração'
            ]
        );
        $logs->addColumn(
            'date',
            'datetime',
            [
                'default' => 'CURRENT_TIMESTAMP',
                'comment' => 'Data da operação'
            ]
        );
        $logs->addColumn(
            'users_id',
            'integer',
            [
                'comment' => 'Identificador do usuário'
            ]
        );
        $logs->setPrimaryKey(['id']);
        $logs->addIndex(['users_id']);
        $logs->addForeignKeyConstraint(
            $users,
            ['users_id'],
            ['id'],
            [
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE'
            ]
        );

        $queries = $schema->toSql(new MySqlPlatform());
        $connection = Doctrine::getInstance();

        foreach ($queries as $query) {
            $connection->query($query);
        }
    }

    /**
     * Inclusao de schema alternativo ao schema do doctrine,
     * possivelmente apresentando bugs.
     *
     * @return void
     */
    public static function runAlternative()
    {
        $connection = Doctrine::getInstance();

        $connection->query(
            "CREATE TABLE IF NOT EXISTS `products` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificador do produto',
              `name` VARCHAR(200) NOT NULL COMMENT 'Nome do produto',
              `price` DECIMAL(10,2) NOT NULL COMMENT 'Valor do produto',
              `stock` INT NOT NULL,
              `date_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de inclusão do registro',
              `date_update` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT 'Estoque do produto',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Lista de produtos';"
        );
        $connection->query(
            "CREATE TABLE IF NOT EXISTS `users` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificador do usuário',
              `name` VARCHAR(150) NOT NULL COMMENT 'Nome do usuário',
              `email` VARCHAR(150) NOT NULL COMMENT 'E-mail do usuário',
              `password` VARCHAR(200) NOT NULL COMMENT 'Senha do usuário',
              `admin` TINYINT(1) NOT NULL,
              `date_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de inclusão do registro',
              `date_update` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
                COMMENT 'Data de alteração do registro',
              PRIMARY KEY (`id`))
            ENGINE = InnoDB
            COMMENT = 'Listagem de usuários';"
        );
        $connection->query(
            "CREATE TABLE IF NOT EXISTS `logs` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificador do log',
              `operation` TEXT NOT NULL,
              `type` VARCHAR(45) NOT NULL,
              `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data da operação',
              `users_id` INT NOT NULL COMMENT 'Identificador do usuário',
              PRIMARY KEY (`id`),
              INDEX `fk_logs_users_idx` (`users_id` ASC),
              CONSTRAINT `fk_logs_users`
                FOREIGN KEY (`users_id`)
                REFERENCES `users` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;"
        );
        $connection->query(
            "CREATE TABLE IF NOT EXISTS `tokens` (
              `id` INT NOT NULL AUTO_INCREMENT COMMENT 'Identificador do token',
              `token_key` VARCHAR(300) NOT NULL COMMENT 'Chave do token',
              `expires` DATETIME NOT NULL COMMENT 'Data de expiração do token',
              `date_insert` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Data de inclusão do registro',
              `users_id` INT NOT NULL COMMENT 'Identificador do usuário',
              PRIMARY KEY (`id`),
              INDEX `fk_tokens_users1_idx` (`users_id` ASC),
              CONSTRAINT `fk_tokens_users1`
                FOREIGN KEY (`users_id`)
                REFERENCES `users` (`id`)
                ON DELETE NO ACTION
                ON UPDATE NO ACTION)
            ENGINE = InnoDB;"
        );
    }
}

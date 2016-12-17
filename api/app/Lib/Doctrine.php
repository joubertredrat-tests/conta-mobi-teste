<?php
/**
 * Classe no padrão singleton para criar a conexão com o
 * banco de dados utilizando a biblioteca DBAL do Doctrine.
 *
 * O singleton atualmente não é mais usado, porém,
 * para o prazo deste projeto, é a solução mais robusta.
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 * @see http://doctrine-dbal.readthedocs.org/en/latest/reference/query-builder.html
 * @see http://www.doctrine-project.org/api/dbal/2.5/index.html
 */

namespace AcmeCorp\Api\Lib;

use Symfony\Component\Yaml\Yaml;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\EchoSQLLogger;

class Doctrine
{
    /**
     * Carrega a instância do Connection. É utilizada para verificar
     * se a mesma encontra-se instanciada ou não.
     *
     * @var Doctrine\DBAL\Connection
     */
    private static $connection;

    /**
     * Assina o método construtor como privado, de forma a evitar que ele seja
     * instanciado com "new Doctrine()" ao invés de Doctrine::getInstance
     *
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Método estático para instanciar a classe aos moldes do
     * padrão singleton, garantindo que ela seja instanciada apenas uma vez.
     *
     * @return Doctrine\DBAL\Connection
     */
    public static function getInstance()
    {
        if (!self::isInstantiated()) {
            self::setInstance(
                self::buildInstace()
            );
        }
        return self::$connection;
    }

    /**
     * Define uma instância do Connection no singleton.
     *
     * @param Doctrine\DBAL\Connection $connection
     * @return void
     */
    private static function setInstance(Connection $connection)
    {
        self::$connection = $connection;
    }

    /**
     * Verifica se existe uma instância do Doctrine no singleton.
     *
     * @return bool
     */
    public static function isInstantiated()
    {
        return self::$connection instanceof Connection;
    }

    /**
     * Cria uma instância do DriverManager com os parametros definidos.
     *
     * @return \Doctrine\DBAL\Connection
     * @see Doctrine\DBAL\DriverManager.php
     * @see Doctrine\DBAL\Configuration.php
     * @see Doctrine\DBAL\Logging\EchoSQLLogger.php
     */
    private static function buildInstace()
    {
        $data = Yaml::parse(file_get_contents(CONFIG_PATH.'config.yml'));
        $config = new Configuration();
        if ($data['database']['debug'] == 'true') {
            $config->setSQLLogger(new EchoSQLLogger());
        }

        $params = [
            'host' => $data['database']['host'],
            'port' => $data['database']['port'],
            'user' => $data['database']['user'],
            'password' => $data['database']['password'],
            'dbname' => $data['database']['name'],
            'persistent' => $data['database']['persistent'] == 'true',
            'host' => $data['database']['host'],
            'charset' => 'utf8',
            'driver' => 'pdo_mysql',
        ];
        $connection = DriverManager::getConnection($params, $config);
        $connection->setFetchMode(\PDO::FETCH_OBJ);
        return $connection;
    }

    /**
     * Inicia uma transaction no objeto presente na classe
     *
     * @return void
     * @see Doctrine\DBAL\Connection.php
     */
    public static function beginTransaction()
    {
        if (!self::isInstantiated()) {
            self::setInstance(self::buildInstace());
        }
        self::$connection->beginTransaction();
    }

    /**
     * Faz o commit de uma transaction no objeto presente na classe
     *
     * @return void
     * @see Doctrine\DBAL\Connection.php
     */
    public static function commit()
    {
        if (!self::isInstantiated()) {
            throw new Exception(
                'Não é possível fazer commit sem uma instância ativa de conexão do Doctrine na classe'
            );
        }
        self::$connection->commit();
    }

    /**
     * Faz rollback de uma transaction em caso de erro
     *
     * @return void
     * @see Doctrine\DBAL\Connection.php
     */
    public static function rollBack()
    {
        if (!self::isInstantiated()) {
            throw new Exception(
                'Não é possível fazer commit sem uma instância ativa de conexão do Doctrine na classe'
            );
        }
        self::$connection->rollBack();
    }
}

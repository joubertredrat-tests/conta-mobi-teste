<?php
/**
 * Classe responsável pela manipulação
 * dos logs da API no banco de dados
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Model;

use AcmeCorp\Api\Lib\Doctrine;
use AcmeCorp\Api\V1\Model\User;

class Log
{
    /**
     * Constantes de tipo
     */
    const TYPE_INSERT = 'insert';
    const TYPE_SELECT = 'select';
    const TYPE_UPDATE = 'update';
    const TYPE_DELETE = 'delete';
    const TYPE_INTERACT = 'interact';

    /**
     * Registra uma ação no log
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $type
     * @param string $operation
     * @return int
     */
    private static function register(User $user, $type, $operation)
    {
        if (!in_array($type, self::getTypes())) {
            throw new \Exception('Unknown type: '.$type);
        }

        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->insert('logs')
            ->setValue('operation', ':operation')
            ->setValue('type', ':type')
            ->setValue('users_id', ':users_id')
            ->setParameter(':name', $operation, \PDO::PARAM_STR)
            ->setParameter(':email', $type, \PDO::PARAM_STR)
            ->setParameter(':password', $user->id, \PDO::PARAM_STR)
            ->execute()
        ;

        return $query_builder->getConnection()->lastInsertId();
    }

    /**
     * Registra uma ação de insert
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $operation
     * @return int
     */
    public static function registerInsert(User $user, $operation)
    {
        return self::register($user, self::TYPE_INSERT, $operation);
    }

    /**
     * Registra uma ação de select
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $operation
     * @return int
     */
    public static function registerSelect(User $user, $operation)
    {
        return self::register($user, self::TYPE_SELECT, $operation);
    }

    /**
     * Registra uma ação de update
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $operation
     * @return int
     */
    public static function registerUpdate(User $user, $operation)
    {
        return self::register($user, self::TYPE_UPDATE, $operation);
    }

    /**
     * Registra uma ação de delete
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $operation
     * @return int
     */
    public static function registerDelete(User $user, $operation)
    {
        return self::register($user, self::TYPE_DELETE, $operation);
    }

    /**
     * Registra uma ação de interact
     *
     * @param AcmeCorp\Api\V1\Model\User $user
     * @param string $operation
     * @return int
     */
    public static function registerInteract(User $user, $operation)
    {
        return self::register($user, self::TYPE_INTERACT, $operation);
    }

    /**
     * Requisita todos os logs
     *
     * @param string $type
     * @param User $user
     * @param array $order
     * @param array $limit
     * @return array
     */
    public static function rowsGet($type = null, User $user = null, $order = [], $limit = [])
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select(
                'l.id',
                'l.operation',
                'l.type',
                'l.date',
                'u.name'
            )
            ->from('logs', 'l')
            ->join('l', 'users', 'u', 'l.users_id = u.id')
        ;
        if ($type && in_array($type, self::getTypes())) {
            $query_builder
                ->andWhere('type = :type')
                ->setParameter(':type', $type, \PDO::PARAM_STR)
            ;
        }
        if ($user instanceof User) {
            $query_builder
                ->andWhere('users_id = :users_id')
                ->setParameter(':users_id', $user->id, \PDO::PARAM_INT)
            ;
        }
        if ($order) {
            $query_builder->orderBy($order[0], $order[1]);
        }
        if ($limit) {
            $query_builder
                ->setFirstResult($limit[0])
                ->setMaxResults($limit[1])
            ;
        }
        $data = $query_builder->execute()->fetchAll();

        $return = [];
        foreach ($data as $row) {
            $return[] = [
                'id' => (int) $row->id,
                'operation' => (string) $row->operation,
                'type' => (string) $row->type,
                'user_name' => (string) $row->name,
                'date' => (string) $row->date,
            ];
        }
        return $return;
    }

    /**
     * Requisita o total de logs
     *
     * @param string $type
     * @param User $user
     * @return int
     */
    public static function rowsCount($type = null, User $user = null)
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('COUNT(*) as total')
            ->from('logs')
        ;
        if ($type && in_array($type, self::getTypes())) {
            $query_builder
                ->andWhere('type = :type')
                ->setParameter(':type', $type, \PDO::PARAM_STR)
            ;
        }
        if ($user instanceof User) {
            $query_builder
                ->andWhere('users_id = :users_id')
                ->setParameter(':users_id', $user->id, \PDO::PARAM_INT)
            ;
        }

        $row = $query_builder->execute()->fetchAll();

        return (int) $row[0]->total;
    }

    /**
     * Requisita os tipos de operação
     *
     * @return array
     */
    public function getTypes()
    {
        return [
            self::TYPE_INSERT,
            self::TYPE_SELECT,
            self::TYPE_UPDATE,
            self::TYPE_DELETE,
            self::TYPE_INTERACT,
        ];
    }
}

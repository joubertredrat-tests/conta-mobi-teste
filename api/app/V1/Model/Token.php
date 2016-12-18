<?php
/**
 * Classe responsável pela manipulação
 * dos usuários no banco de dados
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Model;

use Ramsey\Uuid\Uuid;
use AcmeCorp\Api\Lib\Doctrine;

class Token
{
    /**
     * Identificador do token
     *
     * @var int
     */
    private $id;

    /**
     * Chave do token
     *
     * @var string
     */
    private $token_key;

    /**
     * Data de expiração do token
     *
     * @var string
     */
    private $expires;

    /**
     * Identificador do usuário
     *
     * @var int
     */
    private $user_id;

    /**
     * Data de inclusão do registro
     *
     * @var string
     */
    private $date_insert;

    /**
     * Construtor de classe
     *
     * @param int $id
     * @return void
     */
    public function __construct($id = null)
    {
        if (filter_var($id, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]])) {
            $query_builder = Doctrine::getInstance()->createQueryBuilder();
            $query_builder
                ->select('*')
                ->from('tokens')
                ->where('id = :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT)
            ;
            $row = $query_builder->execute()->fetch();
            if (!$row) {
                throw new Exception('Registry '.$id.' not found on database');
            }
            $this->id = (int) $id;
            $this->token_key = (string) $row->token_key;
            $this->expires = (string) $row->expires;
            $this->user_id = (int) $row->users_id;
            $this->date_insert = (string) $row->date_insert;
        } elseif (filter_var(
            $id,
            FILTER_VALIDATE_REGEXP,
            ['options' => ['regexp' => '/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/']]
        )) {
            $query_builder = Doctrine::getInstance()->createQueryBuilder();
            $query_builder
                ->select('*')
                ->from('tokens')
                ->where('token_key = :token_key')
                ->setParameter(':token_key', $id, \PDO::PARAM_STR)
            ;
            $row = $query_builder->execute()->fetch();
            if (!$row) {
                throw new Exception('Registry '.$id.' not found on database');
            }
            $this->id = (int) $row->id;
            $this->token_key = $id;
            $this->expires = (string) $row->expires;
            $this->user_id = (int) $row->users_id;
            $this->date_insert = (string) $row->date_insert;
        } elseif (is_null($id)) {
            //Objeto vazio
        } else {
            throw new Exception(
                'Try to injection on class '.__CLASS__.' construct, '.
                    'variable $id received value '.$id.' from type '.gettype($id)
            );
        }
    }

    /**
     * Verifica se o token está expirado
     *
     * @return boll
     */
    public function expired()
    {
        return (new \DateTime() > new \DateTime($this->expires));
    }

    /**
     * Requisita o usuário dono do token
     *
     * @return \AcmeCorp\Api\V1\Model\User
     */
    public function getUser()
    {
        return new User($this->user_id);
    }

    /**
     * Registra um novo token
     *
     * @param \AcmeCorp\Api\V1\Model\User $user
     * @return string
     */
    public static function registerNew(User $user)
    {
        $key = Uuid::uuid4();
        $expires = new \DateTime();
        $expires->modify('+3 hours');

        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->insert('tokens')
            ->setValue('token_key', ':token_key')
            ->setValue('expires', ':expires')
            ->setValue('users_id', ':users_id')
            ->setParameter(':token_key', $key, \PDO::PARAM_STR)
            ->setParameter(':expires', $expires->format('Y-m-d H:i:s'), \PDO::PARAM_STR)
            ->setParameter(':users_id', $user->id, \PDO::PARAM_INT)
            ->execute()
        ;

        return $key;
    }
}

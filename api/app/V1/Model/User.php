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

use AcmeCorp\Api\Lib\Doctrine;

class User
{
    /**
     * Identificador do usuário
     *
     * @var int
     */
    private $id;

    /**
     * Nome do usuário
     *
     * @var string
     */
    private $name;

    /**
     * E-mail do usuário
     *
     * @var string
     */
    private $email;

    /**
     * Senha do usuário
     *
     * @var string
     */
    private $password;

    /**
     * Delimitador de usuário como admin
     *
     * @var bool
     */
    private $admin;

    /**
     * Data de inclusão do registro
     *
     * @var string
     */
    private $date_insert;

    /**
     * Data de alteração do registro
     *
     * @var string
     */
    private $date_update;

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
                ->from('users')
                ->where('id = :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT)
            ;
            $row = $query_builder->execute()->fetch();
            if (!$row) {
                throw new Exception('Registry '.$id.' not found on database');
            }
            $this->id = (int) $id;
            $this->name = (string) $row->name;
            $this->email = (string) $row->email;
            $this->password = (string) $row->password;
            $this->admin = (bool) $row->admin;
            $this->date_insert = (string) $row->date_insert;
            $this->date_update = (string) $row->date_update;
        } elseif (is_null($id)) {
            $this->admin = false;
        } else {
            throw new Exception(
                'Try to injection on class '.__CLASS__.' construct, '.
                    'variable $id received value '.$id.' from type '.gettype($id)
            );
        }
    }

    /**
     * Atribui o dado ao objeto de acordo com o atributo informado
     * ou dispara exceção caso atributo não exista.
     *
     * @param string $attr
     * @param mixed $value
     * @return void
     */
    public function __set($attr, $value)
    {
        switch ($attr) {
            case 'name':
            case 'email':
                $this->$attr = $value;
                break;
            case 'password':
                $this->$attr = self::passwordHash($value);
                break;
            default:
                throw new Exception('Unknown attribute: '.$attr);
                break;
        }
    }

    /**
     * Informa o dado do atributo solicitado
     * ou dispara exceção caso atributo não exista.
     *
     * @param string $attr
     * @return mixed
     */
    public function __get($attr)
    {
        switch ($attr) {
            case 'id':
            case 'name':
            case 'email':
            case 'admin':
            case 'date_insert':
            case 'date_update':
                return $this->$attr;
                break;
            case 'password':
                return '*****';
                break;
            default:
                throw new Exception('Unknown attribute: '.$attr);
                break;
        }
    }

    /**
     * Adiciona privilégio de admin ao usuário
     *
     * @return void
     */
    public function grantAdmin()
    {
        $this->admin = true;
    }

    /**
     * Remove privilégio de admin do usuário
     *
     * @return void
     */
    public function revokeAdmin()
    {
        $this->admin = false;
    }

    /**
     * Adiciona um novo usuário
     *
     * @return int|bool
     */
    public function insert()
    {
        if (!$this->id) {
            $query_builder = Doctrine::getInstance()->createQueryBuilder();
            $query_builder
                ->insert('users')
                ->setValue('name', ':name')
                ->setValue('email', ':email')
                ->setValue('password', ':password')
                ->setValue('admin', ':admin')
                ->setParameter(':name', $this->name, \PDO::PARAM_STR)
                ->setParameter(':email', $this->email, \PDO::PARAM_STR)
                ->setParameter(':password', $this->password, \PDO::PARAM_STR)
                ->setParameter(':admin', $this->admin, \PDO::PARAM_BOOL)
                ->execute()
            ;
            $this->id = $query_builder->getConnection()->lastInsertId();
            return $this->id;
        }
        return false;
    }

    /**
     * Atualiza o usuário
     *
     * @return boolean
     */
    public function update()
    {
        if ($this->id) {
            $query_builder = Doctrine::getInstance()->createQueryBuilder();
            $query_builder
                ->update('users')
                ->set('name', ':name')
                ->set('email', ':email')
                ->set('password', ':password')
                ->set('admin', ':admin')
                ->where('id = :id')
                ->setParameter(':name', $this->name, \PDO::PARAM_STR)
                ->setParameter(':email', $this->email, \PDO::PARAM_STR)
                ->setParameter(':password', $this->password, \PDO::PARAM_STR)
                ->setParameter(':admin', $this->admin, \PDO::PARAM_BOOL)
                ->setParameter(':id', $this->id, \PDO::PARAM_INT)
                ->execute()
            ;
            return true;
        }
        return false;
    }

    /**
     * Remove o usuário
     *
     * @return boolean
     */
    public function delete()
    {
        if ($this->id) {
            $query_builder = Doctrine::getInstance()->createQueryBuilder();
            $query_builder
                ->delete('users')
                ->where('id = :id')
                ->setParameter(':id', $this->id, \PDO::PARAM_INT)
                ->execute()
            ;
            return true;
        }
        return false;
    }

    /**
     * Retorna os dados do objeto como array
     *
     * @return array
     */
    public function asArray()
    {
        return [
            'id' => (int) $this->id,
            'name' => (string) $this->name,
            'email' => (string) $this->email,
            'password' => (string) '*****',
            'admin' => (bool) $this->admin,
            'date_insert' => (string) $this->date_insert,
            'date_update' => (string) $this->date_update,
        ];
    }

    /**
     * Requisita todos os usuários
     *
     * @param array $order
     * @param array $limit
     * @return array
     */
    public static function rowsGet($order = [], $limit = [])
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('*')
            ->from('users')
        ;
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
                'name' => (string) $row->name,
                'email' => (string) $row->email,
                'password' => (string) '*****',
                'admin' => (bool) $row->admin,
                'date_insert' => (string) $row->date_insert,
                'date_update' => (string) $row->date_update,
            ];
        }
        return $return;
    }

    /**
     * Requisita o total de usuários
     *
     * @return int
     */
    public static function rowsCount()
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('COUNT(*) as total')
            ->from('users')
        ;
        $row = $query_builder->execute()->fetchAll();

        return (int) $row[0]->total;
    }

    /**
     * Verifica se um e-mail já existe
     *
     * @param string $email
     * @param int $id
     * @return bool
     */
    public function has($email, $id = null)
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('id')
            ->from('users')
            ->where('email = :email')
            ->setParameter(':email', $email, \PDO::PARAM_STR)
        ;
        if ($id) {
            $query_builder
                ->andWhere(
                    $query_builder->expr()->notIn('id', [$id])
                )
            ;
        }

        return $query_builder->execute()->rowCount() > 0;
    }

    /**
     * Cria um hash de uma senha plana
     *
     * @param string $password_plain
     * @return string
     */
    public static function passwordHash($password_plain)
    {
        return password_hash($password_plain, PASSWORD_DEFAULT);
    }

    /**
     * Verifica se a senha plana corresponde a senha com hash
     *
     * @param string $password_plan
     * @param string $password_hash
     * @return bool
     */
    public function passwordVerify($password_plain, $password_hash)
    {
        return password_verify($password_plain, $password_hash);
    }

    /**
     * Realiza a autenticação de um usuário
     *
     * @param string $email
     * @param string $password
     * @return self|bool
     */
    public static function auth($email, $password)
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('*')
            ->from('users')
            ->where('email = :email')
            ->setParameter(':email', $email, \PDO::PARAM_STR)
        ;
        $row = $query_builder->execute()->fetch();
        if (!$row) {
            return false;
        }

        if (self::passwordVerify($password, $row->password)) {
            return new self($row->id);
        } else {
            return false;
        }
    }
}

<?php
/**
 * Classe responsável pela manipulação
 * dos produtos no banco de dados
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Model;

use AcmeCorp\Api\Lib\Doctrine;

class Product
{
    /**
     * Identificador do produto
     *
     * @var int
     */
    private $id;

    /**
     * Nome do produto
     *
     * @var string
     */
    private $name;

    /**
     * Preço do produto
     *
     * @var float
     */
    private $price;

    /**
     * Estoque do produto
     *
     * @var int
     */
    private $stock;

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
                ->from('products')
                ->where('id = :id')
                ->setParameter(':id', $id, \PDO::PARAM_INT)
            ;
            $row = $query_builder->execute()->fetch();
            if (!$row) {
                throw new Exception('Registry '.$id.' not found on database');
            }
            $this->id = (int) $id;
            $this->name = (string) $row->name;
            $this->price = (float) $row->price;
            $this->stock = (int) $row->stock;
            $this->date_insert = (string) $row->date_insert;
            $this->date_update = (string) $row->date_update;
        } elseif (is_null($id)) {

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
            case 'price':
            case 'stock':
                $this->$attr = $value;
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
            case 'price':
            case 'stock':
            case 'date_insert':
            case 'date_update':
                return $this->$attr;
                break;
            default:
                throw new Exception('Unknown attribute: '.$attr);
                break;
        }
    }

    /**
     * Adiciona um novo produto
     *
     * @return int
     */
    public function insert()
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->insert('products')
            ->setValue('name', ':name')
            ->setValue('price', ':price')
            ->setValue('stock', ':stock')
            ->setParameter(':name', $this->name, \PDO::PARAM_STR)
            ->setParameter(':price', $this->price, \PDO::PARAM_STR)
            ->setParameter(':stock', $this->stock, \PDO::PARAM_INT)
            ->execute()
        ;
        $this->id = $query_builder->getConnection()->lastInsertId();
        return $this->id;
    }

    /**
     * Atualiza um produto
     *
     * @return boolean
     */
    public function update()
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->update('products')
            ->set('name', ':name')
            ->set('price', ':price')
            ->set('stock', ':stock')
            ->setParameter(':name', $this->name, \PDO::PARAM_STR)
            ->setParameter(':price', $this->price, \PDO::PARAM_STR)
            ->setParameter(':stock', $this->stock, \PDO::PARAM_INT)
            ->execute()
        ;
        return true;
    }

    /**
     * Remove o produto
     *
     * @return boolean
     */
    public function delete()
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->delete('products')
            ->where('id = :id')
            ->setParameter(':id', $this->id, \PDO::PARAM_INT)
            ->execute()
        ;
        return true;
    }

    /**
     * Requisita todos os produtos
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
            ->from('products')
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

        return $query_builder->execute()->fetchAll();
    }

    /**
     * Requisita o total de produtos
     *
     * @return array
     */
    public static function rowsCount()
    {
        $query_builder = Doctrine::getInstance()->createQueryBuilder();
        $query_builder
            ->select('COUNT(*) as total')
            ->from('products')
        ;
        $row = $query_builder->execute()->fetchAll();

        return (int) $row[0]->total;
    }
}

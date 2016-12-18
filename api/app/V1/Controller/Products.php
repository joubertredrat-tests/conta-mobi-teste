<?php
/**
 * Controller dos produtos
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use AcmeCorp\Api\V1\Model\Product;
use AcmeCorp\Api\V1\Model\Log;

class Products extends ApiController
{
    /**
     * Exibe todos os produtos
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName ProductsList
     * @apiGroup Products
     * @apiVersion 0.1.0
     * @api {get} /products/ Listagem de produtos
     * @apiDescription Listagem de todos os produtos cadastrados no banco de dados.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiSuccess {Object[]} products Lista de produtos.
     * @apiSuccess {Number} products.id Identificador do produto.
     * @apiSuccess {String} products.name Nome do produto.
     * @apiSuccess {String} products.price Preço do produto.
     * @apiSuccess {Number} products.stock Total de itens do produto no estoque.
     * @apiSuccess {String} products.date_insert Data de cadastro do produto.
     * @apiSuccess {String} products.date_update Data da última atualização do produto.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "id": 1,
     *             "name": "Caneta azul",
     *             "price": "2.26",
     *             "stock": 79,
     *             "date_insert": "2016-12-17 18:40:57",
     *             "date_update": "2016-12-18 17:27:39"
     *         },
     *         {
     *             "id": 2,
     *             "name": "Lapizeira",
     *             "price": "15.30",
     *             "stock": 6,
     *             "date_insert": "2016-12-17 18:41:00",
     *             "date_update": ""
     *         },
     *         {
     *             "id": 3,
     *             "name": "Lápis HB",
     *             "price": "3.21",
     *             "stock": 0,
     *             "date_insert": "2016-12-17 18:41:01",
     *             "date_update": "2016-12-18 17:28:19"
     *         },
     *         {
     *             "id": 4,
     *             "name": "Borracha",
     *             "price": "0.59",
     *             "stock": 122,
     *             "date_insert": "2016-12-17 18:48:24",
     *             "date_update": ""
     *         }
     *     ]
     */
    public function displayAll(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }

        $data = Product::rowsGet();
        Log::registerSelect($this->token->getUser(), 'Listagem de produtos');

        return $this->response($data);
    }

    /**
     * Exibe um produto em específico
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName ProductsGet
     * @apiGroup Products
     * @apiVersion 0.1.0
     * @api {get} /products/:product_id Exibição do produto
     * @apiDescription Exibição de um produto cadastrado no banco de dados.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} product_id Identificador do produto.
     * @apiSuccess {Number} products.id Identificador do produto.
     * @apiSuccess {String} products.name Nome do produto.
     * @apiSuccess {String} products.price Preço do produto.
     * @apiSuccess {Number} products.stock Total de itens do produto no estoque.
     * @apiSuccess {String} products.date_insert Data de cadastro do produto.
     * @apiSuccess {String} products.date_update Data da última atualização do produto.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "id": 1,
     *         "name": "Caneta azul",
     *         "price": "2.26",
     *         "stock": 79,
     *         "date_insert": "2016-12-17 18:40:57",
     *         "date_update": "2016-12-18 17:27:39"
     *     }
     */
    public function display(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }

        $product = new Product($id);
        $data = $product->asArray();
        Log::registerSelect($this->token->getUser(), 'Exibição do produto '.$product->name);

        return $this->response($data);
    }

    /**
     * Insere um produto no banco de dados
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName ProductsInsert
     * @apiGroup Products
     * @apiVersion 0.1.0
     * @api {post} /products/ Inclusão de produto
     * @apiDescription Adiciona um produto ao banco de dados de acordo com os dados informados.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {String} name Nome do produto.
     * @apiParam {String} price Preço do produto.
     * @apiParam {Number} stock Quantidade de itens do produto no estoque.
     * @apiSuccess {String} message Mensagem de sucesso.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 Created
     *     {
     *         "message": "Created, id 5"
     *     }
     */
    public function insert(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }

        $name = $this->request->get('name');
        $price = filter_var(
            $this->request->get('price'),
            FILTER_VALIDATE_REGEXP,
            ['options' => ['regexp' => '|^\d+\.\d{2}$|']]
        );
        $stock = filter_var(
            $this->request->get('stock'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );
        $err = [];
        if (is_bool($name) && !$name) {
            $err[] = 'name';
        }
        if (is_bool($price) && !$price) {
            $err[] = 'price';
        }
        if (is_bool($stock) && !$stock) {
            $err[] = 'stock';
        }
        if ($err) {
            $return['code'] = self::RESPONSE_NOT_ACEPTED;
            $return['message'] = 'Invalid fields: '.implode(', ', $err);
            return $this->response($return, $return['code']);
        }

        $product = new Product();
        $product->name = $name;
        $product->price = $price;
        $product->stock = $stock;
        $id = $product->insert();

        Log::registerInsert($this->token->getUser(), 'Cadastro do produto '.$product->name);

        $data['message'] = 'Created, id '.$id;
        return $this->response($data, self::RESPONSE_SUCCESS_INSERT);
    }

    /**
     * Atualiza um produto em específico
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName ProductsUpdate
     * @apiGroup Products
     * @apiVersion 0.1.0
     * @api {patch} /products/:product_id Alteração de produto
     * @apiDescription Altera um produto no banco de dados de acordo com os dados informados.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} product_id Identificador do produto.
     * @apiParam {String} [name] Nome do produto.
     * @apiParam {String} [price] Preço do produto.
     * @apiParam {Number} [stock] Quantidade de itens do produto no estoque.
     * @apiSuccess {String} message Mensagem de sucesso.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "message": "Updated"
     *     }
     */
    public function update(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }

        $name = $this->request->get('name');
        $price = filter_var(
            $this->request->get('price'),
            FILTER_VALIDATE_REGEXP,
            ['options' => ['regexp' => '|^\d+\.\d{2}$|']]
        );
        $stock = filter_var(
            $this->request->get('stock'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 0]]
        );

        $product = new Product($id);
        if ($name) {
            $product->name = $name;
        }
        if ($price) {
            $product->price = $price;
        }
        if ($stock) {
            $product->stock = $stock;
        }
        $product->update();

        Log::registerUpdate($this->token->getUser(), 'Alteração do produto '.$product->name);
        $data['message'] = 'Updated';
        return $this->response($data);
    }

    /**
     * Remove um produto
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName ProductsDelete
     * @apiGroup Products
     * @apiVersion 0.1.0
     * @api {delete} /products/:product_id Remoção de produto
     * @apiDescription Remove um produto do banco de dados.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} product_id Identificador do produto.
     * @apiSuccess {String} message Mensagem de sucesso.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "message": "Deleted"
     *     }
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }

        $product = new Product($id);
        $product->delete();

        Log::registerDelete($this->token->getUser(), 'Exclusão do produto '.$product->name);

        $data['message'] = 'Deleted';
        return $this->response($data);
    }
}

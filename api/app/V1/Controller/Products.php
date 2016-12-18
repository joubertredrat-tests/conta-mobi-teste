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

class Products extends ApiController
{
    /**
     * Exibe todos os produtos
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function displayAll(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $data = Product::rowsGet();

        return $this->response($data);
    }

    /**
     * Exibe um produto em específico
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function display(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $product = new Product($id);
        $data = $product->asArray();

        return $this->response($data);
    }

    /**
     * Insere um produto no banco de dados
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function insert(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

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

        $data['code'] = self::RESPONSE_SUCCESS_INSERT;
        $data['message'] = 'Created, id '.$id;
        return $this->response($data);
    }

    /**
     * Atualiza um produto em específico
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function update(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

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
     */
    public function delete(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $product = new Product($id);
        $product->delete();

        $data['message'] = 'Deleted';
        return $this->response($data);
    }
}

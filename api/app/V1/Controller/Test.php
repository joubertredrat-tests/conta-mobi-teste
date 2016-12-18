<?php
/**
 * Simples Controller de teste
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class Test extends ApiController
{
    /**
     * Responde o ping com um pong
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName Ping
     * @apiGroup Ping
     * @apiVersion 0.1.0
     * @api {get} /ping/ Teste de resposta da API
     * @apiDescription Método de requisição para teste de resposta da API.
     * @apiSuccess {String} message Resposta da API com o timestamp da resposta.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "message": "Pong in 1482088230"
     *     }
     */
    public function ping(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        return $this->response(['message' => 'Pong in '.time()]);
    }
}

<?php
/**
 * Estrutura base do controller da API na versão 0.1.x
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

abstract class ApiController implements BaseController
{
    /**
     * Silex Application
     *
     * @var Silex\Application
     */
    protected $app;

    /**
     * Symfony Request
     *
     * @var Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /*
     * Respostas suportadas pela API
     */
    const RESPONSE_SUCCESS = 200;
    const RESPONSE_SUCCESS_INSERT = 201;
    const RESPONSE_NOT_ACEPTED = 400;
    const RESPONSE_AUTH_ERROR = 401;
    const RESPONSE_AUTH_EXPIRED = 401;
    const RESPONSE_NOT_ALLOWED = 403;
    const RESPONSE_NOT_FOUND = 404;
    const RESPONSE_ERROR_GENERAL = 500;

    /**
     * (non-PHPdoc)
     *
     * @see AcmeCorp\Api\V1\Controller\BaseController::setApplication
     */
    public function setApplication(Application $app)
    {
        $this->app = $app;
    }

    /**
     * (non-PHPdoc)
     *
     * @see AcmeCorp\Api\V1\Controller\BaseController::setRequest
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Formata a resposta em formato json
     *
     * @param array $data
     * @param int $code
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    protected function response(array $data, $code = self::RESPONSE_SUCCESS)
    {
        return $this->app->json($data, $code);
    }
}

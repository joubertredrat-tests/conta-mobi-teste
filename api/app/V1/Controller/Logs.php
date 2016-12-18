<?php
/**
 * Controller dos logs
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use AcmeCorp\Api\V1\Model\Log;
use AcmeCorp\Api\V1\Model\User;

class Logs extends ApiController
{
    /**
     * Exibe todos os logs
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
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

        $type = $this->request->get('type');
        $user_id = filter_var(
            $this->request->get('user_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );
        if ($user_id) {
            $user = new User($user_id);
        } else {
            $user = null;
        }

        $data = Log::rowsGet($type, $user, ['id', 'DESC']);
        Log::registerSelect($this->token->getUser(), 'Listagem de logs');

        return $this->response($data);
    }

    /**
     * Exibe todos os tipos de logs
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function displayTypes(Application $app, Request $request)
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

        $data = Log::getTypes();
        Log::registerSelect($this->token->getUser(), 'Listagem de tipos de log');

        return $this->response($data);
    }
}

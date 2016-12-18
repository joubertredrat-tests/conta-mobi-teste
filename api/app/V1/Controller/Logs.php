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
     *
     * @apiName LogsList
     * @apiGroup Logs
     * @apiVersion 0.1.0
     * @api {get} /logs/?user_id=:user_id&type=:type Listagem de logs
     * @apiDescription Listagem de todos os logs dos eventos realizados nesta API.
     * Somente usuários com privilégio de admin ou o próprio usuário pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} [user_id] Identificador do usuário.
     * @apiParam {Number} [type] Tipo de operação. Para ver os tipos disponíveis,
     * consulte a próxima chamada.
     * @apiSuccess {Object[]} logs Lista de logs.
     * @apiSuccess {Number} logs.id Identificador do log.
     * @apiSuccess {String} logs.operation Texto descritivo da operação realizada.
     * @apiSuccess {String} logs.type Tipo de operação realizado.
     * @apiSuccess {String} logs.user_name Nome do usuário que realizou a ação.
     * @apiSuccess {String} users.date Data de registro do log.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "id": 10,
     *             "operation": "Exclusão do produto Estojo",
     *             "type": "delete",
     *             "user_name": "Admin",
     *             "date": "2016-12-18 16:15:21"
     *         },
     *         {
     *             "id": 9,
     *             "operation": "Autenticação na API",
     *             "type": "select",
     *             "user_name": "Admin",
     *             "date": "2016-12-18 16:15:21"
     *         },
     *         {
     *             "id": 8,
     *             "operation": "Listagem de produtos",
     *             "type": "select",
     *             "user_name": "Admin",
     *             "date": "2016-12-18 16:14:48"
     *         },
     *         {
     *             "id": 7,
     *             "operation": "Autenticação na API",
     *             "type": "select",
     *             "user_name": "Admin",
     *             "date": "2016-12-18 16:14:48"
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

        $type = $this->request->get('type');
        $user_id = filter_var(
            $this->request->get('user_id'),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1]]
        );

        if ($this->token->getUser()->admin) {
            if ($user_id) {
                $user = new User($user_id);
            } else {
                $user = null;
            }
        } else {
            $user = $this->token->getUser();
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
     *
     * @apiName LogsTypes
     * @apiGroup Logs
     * @apiVersion 0.1.0
     * @api {get} /logs/types/ Listagem de tipos de logs
     * @apiDescription Listagem dos tipos de logs que uma ação pode ter,
     * sendo este usado como filtro na chamada acima.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiSuccess {Object[]} logs Lista de tipos de logs.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         "insert",
     *         "select",
     *         "update",
     *         "delete",
     *         "interact"
     *     ]
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

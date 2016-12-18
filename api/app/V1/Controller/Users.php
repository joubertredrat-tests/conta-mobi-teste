<?php
/**
 * Controller dos usuários
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use AcmeCorp\Api\V1\Model\User;
use AcmeCorp\Api\V1\Model\Token;
use AcmeCorp\Api\V1\Model\Log;

class Users extends ApiController
{
    /**
     * Exibe todos os usuários
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName UsersList
     * @apiGroup Users
     * @apiVersion 0.1.0
     * @api {get} /users/ Listagem de usuários
     * @apiDescription Listagem de todos os usuários cadastrados no banco de dados.
     * Somente usuários com privilégio de admin pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiSuccess {Object[]} users Lista de usuários.
     * @apiSuccess {Number} users.id Identificador do usuário.
     * @apiSuccess {String} users.name Nome do usuário.
     * @apiSuccess {String} users.email E-mail do usuário.
     * @apiSuccess {String} users.password Texto representativo de uma senha.
     * @apiSuccess {Boolean} users.admin Delimitador do usuário como admin.
     * @apiSuccess {String} users.date_insert Data de cadastro do usuário.
     * @apiSuccess {String} users.date_update Data da última atualização do usuário.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     [
     *         {
     *             "id": 1,
     *             "name": "Admin",
     *             "email": "admin@dev.local",
     *             "password": "*****",
     *             "admin": true,
     *             "date_insert": "2016-12-18 11:50:47",
     *             "date_update": ""
     *         },
     *         {
     *             "id": 2,
     *             "name": "User 1",
     *             "email": "user-1@dev.local",
     *             "password": "*****",
     *             "admin": true,
     *             "date_insert": "2016-12-18 11:53:15",
     *             "date_update": "2016-12-18 12:28:38"
     *         },
     *         {
     *             "id": 3,
     *             "name": "User 2",
     *             "email": "other-2@dev.local",
     *             "password": "*****",
     *             "admin": true,
     *             "date_insert": "2016-12-18 12:17:05",
     *             "date_update": "2016-12-18 12:25:57"
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
        if (!$this->token->getUser()->admin) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can see this data';
            return $this->response($data, $data['code']);
        }

        $data = User::rowsGet();

        Log::registerSelect($this->token->getUser(), 'Listagem de usuários');

        return $this->response($data);
    }

    /**
     * Exibe um usuário em específico
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName UsersGet
     * @apiGroup Users
     * @apiVersion 0.1.0
     * @api {get} /users/:user_id Exibição do usuário
     * @apiDescription Exibição de um usuário cadastrado no banco de dados.
     * Somente usuários com privilégio de admin ou o próprio usuário pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} user_id Identificador do usuário.
     * @apiSuccess {Number} users.id Identificador do usuário.
     * @apiSuccess {String} users.name Nome do usuário.
     * @apiSuccess {String} users.email E-mail do usuário.
     * @apiSuccess {String} users.password Texto representativo de uma senha.
     * @apiSuccess {Boolean} users.admin Delimitador do usuário como admin.
     * @apiSuccess {String} users.date_insert Data de cadastro do usuário.
     * @apiSuccess {String} users.date_update Data da última atualização do usuário.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "id": 1,
     *         "name": "Admin",
     *         "email": "admin@dev.local",
     *         "password": "*****",
     *         "admin": true,
     *         "date_insert": "2016-12-18 11:50:47",
     *         "date_update": ""
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

        $user = new User($id);
        if (!$this->token->getUser()->admin &&
            $this->token->getUser()->id != $user->id
        ) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can view other users';
            return $this->response($data, $data['code']);
        }
        $data = $user->asArray();
        Log::registerSelect($this->token->getUser(), 'Exibição do usuário ');

        return $this->response($data);
    }

    /**
     * Insere um usuário no banco de dados
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName UsersInsert
     * @apiGroup Users
     * @apiVersion 0.1.0
     * @api {post} /products/ Inclusão de usuário
     * @apiDescription Adiciona um usuário ao banco de dados de acordo com os dados informados.
     * Somente usuários com privilégio de admin pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} id Identificador do usuário.
     * @apiParam {String} name Nome do usuário.
     * @apiParam {String} email E-mail do usuário.
     * @apiParam {String} password Senha do usuário.
     * @apiParam {String="true","true"} admin Delimitador do usuário como admin.
     * @apiSuccess {String} message Mensagem de sucesso.
     * @apiHeaderExample {json} Exemplo de autenticação:
     *     {
     *         "X-Auth-Token": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 201 Created
     *     {
     *         "message": "Created, id 4"
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
        if (!$this->token->getUser()->admin) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can insert new users';
            return $this->response($data, $data['code']);
        }

        $name = $this->request->get('name');
        $email = filter_var(
            $this->request->get('email'),
            FILTER_VALIDATE_EMAIL
        );
        $password = $this->request->get('password');
        $admin = $this->request->get('admin');
        $err = [];
        if (is_bool($name) && !$name) {
            $err[] = 'name';
        }
        if (is_bool($email) && !$email) {
            $err[] = 'email';
        }
        if (is_bool($password) && !$password) {
            $err[] = 'password';
        }
        if ($err) {
            $return['code'] = self::RESPONSE_NOT_ACEPTED;
            $return['message'] = 'Invalid fields: '.implode(', ', $err);
            return $this->response($return, $return['code']);
        }

        if (User::has($email)) {
            $return['code'] = self::RESPONSE_NOT_ACEPTED;
            $return['message'] = 'Email '.$email.' already exists, please select another one';
            return $this->response($return, $return['code']);
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        if (!is_bool($admin) and $admin == 'true') {
            $user->grantAdmin();
        }
        $id = $user->insert();

        Log::registerInsert($this->token->getUser(), 'Cadastro do usuário '.$user->name);

        $data['code'] = self::RESPONSE_SUCCESS_INSERT;
        $data['message'] = 'Created, id '.$id;
        return $this->response($data);
    }

    /**
     * Atualiza um usuário no banco de dados
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName UsersUpdate
     * @apiGroup Users
     * @apiVersion 0.1.0
     * @api {patch} /users/:user_id Alteração de usuário
     * @apiDescription Altera um usuário ao banco de dados de acordo com os dados informados.
     * Somente usuários com privilégio de admin ou o próprio usuário pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} user_id Identificador do usuário.
     * @apiParam {String} [name] Nome do usuário.
     * @apiParam {String} [email] E-mail do usuário.
     * @apiParam {String} [password] Senha do usuário.
     * @apiParam {String="true","true"} [admin] Delimitador do usuário como admin.
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
        $email = filter_var(
            $this->request->get('email'),
            FILTER_VALIDATE_EMAIL
        );
        $password = $this->request->get('password');
        $admin = $this->request->get('admin');


        if ($email && User::has($email, $id)) {
            $return['code'] = self::RESPONSE_NOT_ACEPTED;
            $return['message'] = 'Email '.$email.' already exists, please select another one';
            return $this->response($return, $return['code']);
        }

        $user = new User($id);
        if (!$this->token->getUser()->admin &&
            $this->token->getUser()->id != $user->id
        ) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can edit other users';
            return $this->response($data, $data['code']);
        }

        if ($name) {
            $user->name = $name;
        }
        if ($email) {
            $user->email = $email;
        }
        if ($passwrod) {
            $user->password = $password;
        }
        if (!is_bool($admin)) {
            switch ($admin) {
                case 'true':
                    $user->grantAdmin();
                    break;
                case 'false':
                    $user->revokeAdmin();
                    break;
            }
        }
        $user->update();

        Log::registerUpdate($this->token->getUser(), 'Alteração do usuário '.$user->name);
        $data['message'] = 'Updated';
        return $this->response($data);
    }

    /**
     * Remove um usuário
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $id
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName UsersDelete
     * @apiGroup Users
     * @apiVersion 0.1.0
     * @api {delete} /users/:user_id Remoção de usuário
     * @apiDescription Remove um usuário do banco de dados.
     * Somente usuários com privilégio de admin pode acessar este recurso.
     * @apiHeader {String} X-Auth-Token Token para autenticação.
     * @apiParam {Number} user_id Identificador do usuário.
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
        if (!$this->token->getUser()->admin) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can delete users';
            return $this->response($data, $data['code']);
        }

        $user = new User($id);
        $user->delete();

        Log::registerDelete($this->token->getUser(), 'Exclusão do usuário '.$user->name);
        $data['message'] = 'Deleted';
        return $this->response($data);
    }

    /**
     * Autentica um usuário
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
     *
     * @apiName Auth
     * @apiGroup Auth
     * @apiVersion 0.1.0
     * @api {post} /auth/ Autenticação de usuário
     * @apiDescription Interface para autenticação do usuário na API.
     * Este recurso é necessário para acessar as outras chamadas que solicitar autenticação.
     * Ao fazer a operação com sucesso, é retornado um token, sendo este necessário
     * para acessar os demais recursos da API que solicitar "X-Auth-Token" no header.
     * @apiParam {String} email E-mail do usuário.
     * @apiParam {String} password Senha do usuário.
     * @apiSuccess {String} message Token.
     * @apiSuccessExample {json} Success-Response:
     *     HTTP/1.1 200 OK
     *     {
     *         "message": "52f86f8c-ba2b-4089-bf14-a0a1e69581e2"
     *     }
     */
    public function login(Application $app, Request $request)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $email = filter_var(
            $this->request->get('email'),
            FILTER_VALIDATE_EMAIL
        );
        $password = $this->request->get('password');
        $err = [];
        if (is_bool($email) && !$email) {
            $err[] = 'email';
        }
        if (is_bool($password) && !$password) {
            $err[] = 'password';
        }
        if ($err) {
            $return['code'] = self::RESPONSE_NOT_ACEPTED;
            $return['message'] = 'Invalid fields: '.implode(', ', $err);
            return $this->response($return, $return['code']);
        }

        $user = User::auth($email, $password);
        if (!$user) {
            $return['code'] = self::RESPONSE_AUTH_ERROR;
            $return['message'] = 'Login fail';
            return $this->response($return, $return['code']);
        }

        $key = Token::registerNew($user);
        $data['message'] = $key;

        return $this->response($data);
    }
}

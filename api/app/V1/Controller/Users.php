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
     */
    public function display(Application $app, Request $request, $id)
    {
        $this->setApplication($app);
        $this->setRequest($request);

        $user = new User($id);
        $data = $user->asArray();

        return $this->response($data);
    }

    /**
     * Insere um usuário no banco de dados
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
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

        $data['code'] = self::RESPONSE_SUCCESS_INSERT;
        $data['message'] = 'Created, id '.$id;
        return $this->response($data);
    }

    /**
     * Insere um usuário no banco de dados
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

        $auth = $this->auth();
        switch ($auth['code']) {
            case self::RESPONSE_AUTH_ERROR:
            case self::RESPONSE_AUTH_EXPIRED:
                return $this->response($auth, $auth['code']);
                break;
        }
        if (!$this->token->getUser()->admin) {
            $data['code'] = self::RESPONSE_NOT_ALLOWED;
            $data['message'] = 'Only admin users can update users';
            return $this->response($data, $data['code']);
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

        $data['message'] = 'Deleted';
        return $this->response($data);
    }

    /**
     * Autentica um usuário
     *
     * @param Silex\Application $app
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return Symfony\Component\HttpFoundation\JsonResponse
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
        $data[] = $key;

        return $this->response($data);
    }
}

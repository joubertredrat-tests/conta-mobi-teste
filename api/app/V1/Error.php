<?php
/**
 * Classe responsável pela manipulação de erros
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\Yaml\Yaml;

class Error
{
    /**
     * Responde a requisição reportando o erro.
     *
     * @param Silex\Application $app
     * @param \Exception $e
     * @param Symfony\Component\HttpFoundation\Request $request
     * @param int $code
     * @return Symfony\Component\HttpFoundation\JsonResponse
     */
    public function response(Application $app, \Exception $e, Request $request, $code)
    {
        $data = Yaml::parse(file_get_contents(CONFIG_PATH.'config.yml'));

        switch ($code) {
            case 404:
                $message = 'Not found';
                break;
            case 405:
                $message = 'Method Not Allowed';
                break;
            default:
                $message = 'Ooops';
                break;
        }
        if ($data['api']['debug']) {
            $message .= ' - '.$e->getMessage();
        }

        return $app->json(['code' => $code, 'message' => $message], $code);
    }
}

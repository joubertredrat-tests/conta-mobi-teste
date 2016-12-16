<?php
/**
 * Classe responsável pela implementação de rotas da versão 0.1.x
 * no endpoint v1 da API
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1;

use Silex\Application;

class Router
{
    /*
     * Prefixo da url da versão 1
     */
    const URI_PREFIX = 'v1';

    /**
     * Injeta as rotas do V1 no app do Silex
     *
     * @param Silex\Application $app
     * @return void
     */
    public static function inject(Application &$app)
    {
        $app->get(
            '/'.self::URI_PREFIX.'/ping/',
            'AcmeCorp\Api\V1\Controller\Test::ping'
        );
    }
}

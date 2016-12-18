<?php
/**
 * Front-end do sistema de API, responsável por receber as rotas,
 * processar a requisição e responder com os dados correspondentes.
 * Para a operação da API em formato de url amigável, foi usado o microframework Silex.
 * Toda a estrutura desta API foi programada seguindo o padrão PSR-2
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 * @see http://silex.sensiolabs.org/
 * @see http://www.php-fig.org/psr/psr-2/
 */

$parts = [
    __DIR__,
    '..',
    'app',
    'bootstrap.php'
];
require_once(implode(DIRECTORY_SEPARATOR, $parts));

$app = new Silex\Application();
$app['debug'] = true;
AcmeCorp\Api\V1\Router::inject($app);

$app->error(function (\Exception $e, $request, $code) use ($app) {
    $Error = new AcmeCorp\Api\V1\Error();
    return $Error->response($app, $e, $request, $code);
});

$app->run();

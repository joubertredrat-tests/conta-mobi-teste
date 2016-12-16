<?php
/**
 * Interface base para manipulação dos elementos do framework
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Controller;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

interface BaseController
{
    /**
     * Define o app
     *
     * @param Silex\Application $app
     * @return void
     */
    public function setApplication(Application $app);

    /**
     * Define o request
     *
     * @param Symfony\Component\HttpFoundation\Request $request
     * @return void
     */
    public function setRequest(Request $request);
}

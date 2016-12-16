<?php
/**
 * Arquivo responsável pela chamada de todos os autoloaders do framework
 * e demais componentes usados na aplicaçao
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

$parts = [
    __DIR__,
    '..',
    'vendor',
    'autoload.php'
];
require_once(implode(DIRECTORY_SEPARATOR, $parts));

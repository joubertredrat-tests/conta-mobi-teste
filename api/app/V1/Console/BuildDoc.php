<?php
/**
 * Comando para geração de documentação de HTML da API
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BuildDoc extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:build-doc')
            ->setDescription('Build API documentation.')
            ->setHelp('Build new documentation is data was chnaged')
        ;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::execute
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Build API documentation',
            '',
            'Building new API documentation...',
        ]);

        $apidoc_cmd = '/usr/local/bin/apidoc';
        $command = shell_exec('which '.$apidoc_cmd);

        if (!$command) {
            $output->writeln([
                'Apidoc not found',
                '',
                "Apidoc isn't installed, to install, look here, http://apidocjs.com/",
            ]);
        } else {
            shell_exec('ln -s '.APP_PATH.'/V1/Controller '.APP_PATH.'/files/apidoc/.');
            shell_exec($apidoc_cmd.' -i '.APP_PATH.'/files/apidoc -o '.PUBLIC_PATH.'docs/0.1/');
            shell_exec('rm '.APP_PATH.'/files/apidoc/Controller');
            $output->writeln('Done, you can view documentation now in http://domain/v1/docs/latest');
        }
    }
}

<?php
/**
 * Comando para rodar a API usando o webserver do PHP
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 * @see http://www.php-fig.org/psr/psr-2/
 */

namespace AcmeCorp\Api\V1\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Run extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:run')
            ->setDescription('Listen API')
            ->setHelp('Start listen API')
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
            'Listen API',
            '',
            'Listening on http://0.0.0.0:8000 ...'
        ]);

        shell_exec('php -S 0.0.0.0:8000 -t '.PUBLIC_PATH.' '.PUBLIC_PATH.'index.php');
    }
}

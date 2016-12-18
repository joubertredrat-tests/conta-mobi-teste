<?php
/**
 * Comando para checar se o código fonte está no padrão PSR-2
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

class CheckPsr2 extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:check-psr2')
            ->setDescription('PSR-2 check code')
            ->setHelp('Check if API source code is PSR-2 like')
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
            'PSR-2 check code',
            '',
            'Checking code...',
        ]);

        $output->writeln(shell_exec('php '.BIN_PATH.'phpcs.phar '.APP_PATH));
    }
}

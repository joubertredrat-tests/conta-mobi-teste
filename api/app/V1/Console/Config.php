<?php
/**
 * Comando para configuração do app da API
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 */

namespace AcmeCorp\Api\V1\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Yaml\Yaml;
use AcmeCorp\Api\Lib\Doctrine;

class Config extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:config')
            ->setDescription('Config application.')
            ->setHelp('Create new application config')
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
            'Config application',
            ''
        ]);

        $helper = $this->getHelper('question');

        $question = new Question('Database host (default localhost): ', 'localhost');
        do {
            $database_host = $helper->ask($input, $output, $question);
        } while (is_null($database_host));
        $question = new Question('Database port (default 3306): ', '3306');
        do {
            $database_port = $helper->ask($input, $output, $question);
        } while (is_null($database_port));
        $question = new Question('Database user: ');
        do {
            $database_user = $helper->ask($input, $output, $question);
        } while (is_null($database_user));
        $question = new Question('Database password: ');
        do {
            $database_password = $helper->ask($input, $output, $question);
        } while (is_null($database_password));
        $question = new Question('Database name: ');
        do {
            $database_name = $helper->ask($input, $output, $question);
        } while (is_null($database_name));

        $database_confirm = [
            'Host: '.$database_host,
            'Port: '.$database_port,
            'User: '.$database_user,
            'Password: '.$database_password,
            'Name: '.$database_name,
        ];

        $question = new ChoiceQuestion(
            "Are you sure that this information is correct?\n\n".implode("\n", $database_confirm),
            ['no', 'yes'],
            1
        );
        $question->setErrorMessage('This option %s is invalid.');

        $confirm = $helper->ask($input, $output, $question);
        if ($confirm == 'no') {
            $output->writeln('Try run command again');
        } else {
            if (!Doctrine::testConnection(
                $database_host,
                $database_port,
                $database_user,
                $database_password,
                $database_name
            )) {
                $output->writeln('Database information is incorrect, try again');
                return false;
            }
        }

        $data['database'] = [
            'host' => $database_host,
            'port' => $database_port,
            'user' => $database_user,
            'password' => $database_password,
            'name' => $database_name
        ];
        $data['api'] = [
            'debug' => true
        ];
        $yaml = Yaml::dump($data);
        file_put_contents(CONFIG_PATH.'config.yml', $yaml);

        $output->writeln('Done');
    }
}

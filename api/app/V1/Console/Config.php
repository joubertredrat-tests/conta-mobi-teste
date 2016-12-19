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

        if (file_exists(CONFIG_PATH.'config.yml')) {
            $output->writeln('Application already configured');
            return false;
        }

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
            return false;
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


        $question = new Question('SMTP host: ');
        do {
            $smtp_host = $helper->ask($input, $output, $question);
        } while (is_null($smtp_host));
        $question = new Question('SMTP port: ');
        do {
            $smtp_port = $helper->ask($input, $output, $question);
        } while (is_null($smtp_port));
        $question = new ChoiceQuestion(
            'SMTP secure mode',
            ['TLS', 'SSL', 'STARTTLS'],
            1
        );
        $question->setErrorMessage('This option %s is invalid.');
        $smtp_secure = $helper->ask($input, $output, $question);
        $question = new Question('SMTP username e-mail: ');
        do {
            $smtp_username = $helper->ask($input, $output, $question);
        } while (is_null($smtp_username) ||
            !filter_var(
                $smtp_username,
                FILTER_VALIDATE_EMAIL
            )
        );
        $question = new Question('SMTP password: ');
        do {
            $smtp_password = $helper->ask($input, $output, $question);
        } while (is_null($smtp_password));
        $question = new Question('From e-mail (default '.$smtp_username.'): ', $smtp_username);
        do {
            $smtp_from = $helper->ask($input, $output, $question);
        } while (is_null($smtp_from));


        $smtp_confirm = [
            'Host: '.$smtp_host,
            'Port: '.$smtp_port,
            'Secure: '.$smtp_secure,
            'Username: '.$smtp_username,
            'Password: '.$smtp_password,
            'From e-mail: '.$smtp_from,
        ];

        $question = new ChoiceQuestion(
            "Are you sure that this information is correct?\n\n".implode("\n", $smtp_confirm),
            ['no', 'yes'],
            1
        );
        $question->setErrorMessage('This option %s is invalid.');

        $confirm = $helper->ask($input, $output, $question);
        if ($confirm == 'no') {
            return false;
        }

        $data['smtp'] = [
            'host' => $smtp_host,
            'port' => $smtp_port,
            'secure' => $smtp_secure,
            'username' => $smtp_username,
            'password' => $smtp_password,
            'from' => $smtp_from
        ];

        $yaml = Yaml::dump($data);
        file_put_contents(CONFIG_PATH.'config.yml', $yaml);

        $output->writeln('Done');
    }
}

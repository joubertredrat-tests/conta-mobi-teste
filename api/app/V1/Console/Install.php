<?php


namespace AcmeCorp\Api\V1\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use AcmeCorp\Api\Lib\InstallSchema;
use AcmeCorp\Api\V1\Model\User;

class Install extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:install')
            ->setDescription('Install API application.')
            ->setHelp("This command install schema and application")
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
            'Install',
            ''
        ]);

        if (file_exists(CONFIG_PATH.'app.lock')) {
            $output->writeln('Application already installed');
            return false;
        }

        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            "Are you sure with install aplication? ",
            ['no', 'yes'],
            1
        );

        $question->setErrorMessage('This option %s is invalid.');

        $confirm = $helper->ask($input, $output, $question);
        if ($confirm == 'yes') {
            $output->writeln([
                'Installing database...',
                ''
            ]);

            InstallSchema::runAlternative();

            $output->writeln([
                'Database installed',
                '',
                'Now you will create first admin user',
                ''
            ]);

            $question = new Question('User name (default Admin): ', 'Admin');
            do {
                $user_name = $helper->ask($input, $output, $question);
            } while (is_null($user_name));
            $question = new Question('Email (default admin@dev.local): ', 'admin@dev.local');
            do {
                $user_email = $helper->ask($input, $output, $question);
            } while (is_null($user_email) ||
                !filter_var(
                    $user_email,
                    FILTER_VALIDATE_EMAIL
                )
            );
            $question = new Question('Password (default admin): ', 'admin');
            do {
                $user_password = $helper->ask($input, $output, $question);
            } while (is_null($user_password));

            $user = new User();
            $user->name = $user_name;
            $user->email = $user_email;
            $user->password = $user_password;
            $user->grantAdmin();
            $id = $user->insert();

            if ($id) {
                touch(CONFIG_PATH.'app.lock');
                $output->writeln([
                    '',
                    'User created, now you can use API'
                ]);
            } else {
                $output->writeln([
                    '',
                    'Something is wrong, try again'
                ]);
            }
        }
    }
}

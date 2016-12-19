<?php
/**
 * Comando para geração de relatório dos produtos a ser enviados por e-mail
 *
 * @author Joubert <eu@redrat.com.br>
 * @copyright Copyright (c) 2016, Acme Corporation
 * @copyright Copyright (c) 2016, Conta Mobi
 * @see http://www.php-fig.org/psr/psr-2/
 */

namespace AcmeCorp\Api\V1\Console;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use AcmeCorp\Api\V1\Model\Product;
use AcmeCorp\Api\V1\Model\User;

class Reports extends Command
{
    /**
     * (non-PHPdoc)
     *
     * @see Symfony\Component\Console\Command\Command::configure
     */
    protected function configure()
    {
        $this
            ->setName('app:products-report')
            ->setDescription('Generate products report')
            ->setHelp('Generate products report and send to all users on API by e-mail')
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
            'Generate products report',
            ''
        ]);

        $data = Product::rowsGet(['stock', 'ASC']);

        $products_outstock = [];
        $products_stock = [];
        foreach ($data as $row) {
            if ($row['stock'] > 0) {
                $products_stock[] = [
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'stock' => $row['stock'],
                ];
            } else {
                $products_outstock[] = [
                    'name' => $row['name'],
                    'price' => $row['price'],
                    'stock' => $row['stock'],
                ];
            }
        }

        $html = [];
        $html[] = '<h4>Relatório de produtos</h4>';
        if ($products_outstock) {
            $html[] = '<strong>Abaixo lista de produtos sem estoque</strong>';
            $html[] = '<ul>';
            foreach ($products_outstock as $product) {
                $html[] = '<li>Nome: '.$product['name'].' - Preço: '.$product['price'].'</li>';
            }
            $html[] = '</ul>';
        }
        if ($products_stock) {
            $html[] = '<strong>Abaixo lista dos demais produtos em ordem de estoque</strong>';
            $html[] = '<ul>';
            foreach ($products_stock as $product) {
                $html[] = '<li>Nome: '.$product['name'].
                    ' - Preço: '.$product['price'].
                        ' - Estoque: '.$product['stock'].'</li>';
            }
            $html[] = '</ul>';
        }

        $config = Yaml::parse(file_get_contents(CONFIG_PATH.'config.yml'));

        $data = User::rowsGet();
        $mail = new \PHPMailer();

        $mail->isSMTP();
        $mail->Host = $config['smtp']['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $config['smtp']['username'];
        $mail->Password = $config['smtp']['password'];
        $mail->SMTPSecure = $config['smtp']['secure'];
        $mail->Port = $config['smtp']['port'];

        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Relatório de produtos - '. date('d/m/Y H:i:s');
        $mail->Body = implode('', $html);
        $mail->AltBody = 'E-mail disponível somente no formato HTML';
        $mail->setFrom($config['smtp']['from'], 'Report');

        foreach ($data as $row) {
            $mail->addBCC($row['email']);
        }

        $output->writeln('Done');
    }
}

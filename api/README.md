## Acme Corp API

Bem vindos a API da Acme Corp. Esta API tem o objetivo de facilitar a manutenção do estoque por meio de API REST.

#### Instalação

Para instalação desta API, é necessário os seguintes pré-requisitos.
* PHP >= 7.0
* Mysql >= 5.6 ou MariaDB >= 10.0
* Apache, Nginx, Lighttpd ou qualquer outro webserver com suporte a PHP.

Para fazer a instalação, é necessário seguir os passos abaixo.

* Faça o clone deste repositório
* Crie um banco de dados e defina um usuário com permissão de leitura e escrita neste banco
* Acesse a pasta `api` e execute o comando `php bin/console app:config`
* Siga as instruções e informe os dados do banco de dados criados anteriormente
* Ao final da execução do comando com sucesso, execute o comando `php bin/console app:install`
* Siga as instruções e informe os dados para criação do primeiro usuário admin

Feito estes passos com sucesso, sua aplicação está instalada com sucesso.

#### Execução

Atualmente existe duas formas de execução da aplicação.

A primeira consiste em usar o server PHP embutido, porém, este é somente para fins de teste, não sendo recomendado para ambientes de produção.

Para rodar em ambiente teste, basta executar `php bin/console app:run`, uma vez feito isto, a API estárá disponível no endereço http://0.0.0.0:8000

A segunda forma consiste em apontar o document root do webserver para a pasta `public`, sendo este configurável e personalizável com o domínio desejado ou até mesmo implementações de segurança, como SSL.

#### Utilização

Como a aplicação é REST, não existe formulários, nem views, somente o endpoint e as chamadas que podem ser realizadas usando um cliente HTTP. Porém, para utilização correta das chamadas disponíveis, basta consultar a documentação da API em http://0.0.0.0:8000/v1/docs/latest/ ou em http://dominio-escolhido/v1/docs/latest/

#### Bugs e informações

Para reportar bugs ou solicitar mais informações, basta abrir uma issue neste repositório


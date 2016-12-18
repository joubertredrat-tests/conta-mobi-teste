Bem vindos a API da Acme Corp.
Esta API tem o objetivo de facilitar a manutenção do estoque por meio de API REST.

### Informações ###

#### Requerimentos ####

Para utilização dos recursos desta API é necessário que o profissional que irá fazer a integração entre sistemas
tenha conhecimento em programação de sistemas ou similares, bem como a linguagem ou interface para a integração
tenha suporte a chamadas [HTTP](https://www.w3.org/Protocols/) e processamento de texto em formato [JSON](http://www.json.org).

#### Requisição ####

As requisições aos métodos descritas nesta API obedecem ao padrão determinado pelo [W3C](https://www.w3.org) para requisições [HTTP](https://www.w3.org/Protocols/),
sendo os métodos de requisição suportados para uso nesta API os listados abaixo.

| Método | Uso |
|:------:|------|
| [GET](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.3) | Listagem e exibição de dados |
| [POST](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.5) | Inclusão de novos registros |
| [PATCH](https://tools.ietf.org/html/rfc5789#section-2) | Alteração de registros existentes |
| [DELETE](https://www.w3.org/Protocols/rfc2616/rfc2616-sec9.html#sec9.7) | Remoção ou desativação de registros |

Todas as chamadas disponíveis nesta API estão descritas ao decorrer desta documentação, porém, existe dois casos em que
pode ocorrer erros nas chamadas.

* Chamada inexistente: Caso seja realizada uma chamada em qualquer endereço diferente dos descritos nesta documentação,
será retornado uma resposta [HTTP](https://www.w3.org/Protocols/) de código [404](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.5)
e uma mensagem informada que a chamada não existe, como o exemplo abaixo.
```
{
    "code": 404,
    "message": "Not found"
}
```

* Chamada com método incorreto: A chamada está correta, porém, o método usado está incorreto, neste caso,
será retornado uma resposta [HTTP](https://www.w3.org/Protocols/) de código [405](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.6)
e uma mensagem informada que o método não foi aceito, como o exemplo abaixo.
```
{
    "code": 405,
    "message": "Method Not Allowed"
}
```

#### Resposta ####

A API da base utiliza os códigos de resposta [HTTP](https://www.w3.org/Protocols/) como padrão de resposta para o cliente, conforme pode ser conferido abaixo.

| Código | Resposta | Causas |
|:------:|:------:|------|
| [200](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.1) | OK | A requisição ocorreu com sucesso, ou seja, sem nenhum erro |
| [201](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.2.2) | Created | O registro foi criado com sucesso |
| [400](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.1) | Bad Request | O método foi requisitado incorretamente por falta de parâmetros obrigatórios |
| [401](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.2) | Unauthorized | Ocorreu uma falha no login pelo token não ter sido informado ou estar expirado |
| [403](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.4) | Forbidden | A API não permitiu alguma ação realizada na API, como alteração de dados por exemplo |
| [404](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.4.5) | Not Found | O método informado não existe ou nenhum dado foi retornado |
| [500](https://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html#sec10.5.1) | Internal Server Error | Ocorreu algum erro durante o processamento da requisiçao |


Além códigos de resposta [HTTP](https://www.w3.org/Protocols/), a API retorna o status da chamada com texto em formato [JSON](http://www.json.org),
formato padrão usado para API REST. Vale lembrar que esta API **NÃO suporta retorno de chamadas em formato [XML](https://www.w3.org/XML/).**

Atenção na definição do endereço de URL para as chamadas, pois algumas chamadas termina com barra e outras não.

Mais informações sobre o código de resposta [HTTP](https://www.w3.org/Protocols/) pode ser conferidas em
[https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html](https://www.w3.org/Protocols/rfc2616/rfc2616-sec6.html) e
[https://en.wikipedia.org/wiki/List_of_HTTP_status_codes](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes).

Todas as datas requeridas nas consultas ou retornadas como dados deverão e estarão em formato ODBC canonical.

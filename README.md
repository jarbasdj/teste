# Instruções para rodar a aplicação

**Pacotes de terceiros utilizados**

Foram usados os seguintes pacotes de terceiros:

[https://packagist.org/packages/coffeecode/router](https://packagist.org/packages/coffeecode/router)
Para a utilização de rotas dentro da aplicação.

[https://symfony.com/doc/current/components/var_dumper.html](https://symfony.com/doc/current/components/var_dumper.html)
Para exibição de mensagens de erro interno.

**Docker**

`docker-compose up -d`

Configurações de porta apache e mysql estão em `./docker/.env`

**Servidor Local**

Edite o arquivo `./app/Models/DB.php` para editar o acesso ao banco de dados.
Edite o arquivo `./app/config/routes.php` e edite a linha `$router =  new  Router("http://localhost");` com a URL onde o projeto vai rodar.
O Apache precisa estar com o `mod_rewrite` habilitado para as rotas funcionarem.

**Banco de dados**
Na raíz do projeto tem um arquivo chamado `Database.sql` com a estrutura do banco e alguns dados de teste.
Na raiz também tem o arquivo `Database.mwb` (Mysql Workbench) com o diagrama do banco.

**Testando a API**

Para testar as requisições, basta importar no Postman o arquivo `Teste.postman_collection.json` que está na raíz do projeto. Nele contém todos os endpoints com dados e parâmetros necessários.
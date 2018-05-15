# Integração Fast2Pay x Bematech - Misterchef
> Este script foi desenvolvido em uma parceria da Fast2Pay com a Bematech - Misterchef, para realizar uma integração entre os sistemas.

Projeto desenvolvido para realizar uma integração entre o App da Fast2Pay e o sistema Misterchef, da Bematech. O script foi desenvolvido com o apoio da Olitécnica, de Caxias do Sul.
A integração tem o seguinte funcionamento:
    Inicialmente é realizada a consulta das mesas abertas no sistema da Bematech, após é realizado a consulta de mesas vinculadas no app da Fast2Pay.
    Todas as mesas abertas e vinculadas são inseridas no banco de dados de integração e capturados através de API para não serem mais exibidos na consulta da Fast2Pay.
    A partir desse ponto, todas as ações são feitas com o conteúdo cadastrado no banco de dados, que tenha status diferente de 9 (Itens com status 9 já tiveram seu processo finalizado).

## Requisitos
* [WampServer](http://www.wampserver.com/en/#download-wrapper)
* PHP 7.0 (ou superior)
* MySQL 5.7 (ou superior)

* Extensões do PHP
    * php_mysqli

* Módulos do Apache
    * rewrite_module

## Instalação

Windows:
* Instalar o WAMPServer
    * Edite o arquivo httpd.conf (disponível em wamp64\bin\apache\apache2.4.23\conf\original\httpd.conf), mudando a porta padrão para 8080

* Acesse o banco de dados através do arquivo adminer.php disponível na raiz do projeto (http://localhost:8080/adminer.php) ou através do software que preferir e crie a base de dados de integração.

```
CREATE DATABASE `fast2pay` COLLATE 'utf8_general_ci';
```

Importe o arquivo .sql disponível na pasta temp/. Após execute o SQL abaixo, para garantirmos que a tabela estará limpa e pronta para começarmos.

```
TRUNCATE TABLE `f2p_linked_tables`;
```

Para rodar a aplicação, acesse http://localhost:8080/fast2pay

## Configuração para Desenvolvimento e Implementação

A configuração da aplicação está disponível nos arquivos database.php, fast2pay.php e constants.php, que estão no diretório application/config/...

No arquivo database.php é possível configurar a conexão para o banco, se este for diferente de:

```
    Usuário: root
    Senha:
```

No arquivo fast2pay.php estão todas as principais configurações da aplicação, são elas:

```
    url_bematech - É a URL de conexão a API da Bematech (deve iniciar com http://  e terminar com /). Default: http://127.0.0.1/
    url_bematech_base - É a sequência da URL de conexão a API da Bematech (deve terminar com /). Default: IntegracaoPedidosOnlineIntranet/;
    CodigoEstabelecimento - É o código de estabelecimento fornecido pela Bematech - Default: vazio (se o script rodar em conexão local não é necessário);
    CodigoIntegracao - É o código de integração fornecido pela Bematech - Default: vazio (se o script rodar em conexão local não é necessário);

    url_fast2pay - É a URL de conexão a API da Fast2Pay (deve iniciar com http://  e terminar com /). Default: 'https://wst.fast2pay.com.br/v2.0/_integra/' (wst é servidor de homologação);
    file_extension - É extensão do arquivo de chamada. Criada para o caso de a extensão ser removida no futuro. Default: '.php';
    user_fast2pay - É o usuário criado pela Fast2Pay, exclusivamente para o cliente.
    pass_fast2pay - É a senha criada pela Fast2Pay, exclusivamente para o cliente.;
    cnpj_fast2pay - É o CNPJ do cliente.
```

No arquivo constants.php definimos algumas constantes importantes para o funcionamento da aplicação:

```
    PRODUTO_FAST2PAY - É o código do produto criado para a Fast2Pay dentro do Misterchef. No zero54 foi utilizado: 998.
    PASTA_IMPRESSAO  - É a pasta de impressão utilizada pelo Misterchef. Defautl: C:/Ms/Spool/
    PORTA_IMPRESSAO  - É o nome do arquivo de impressão, disponível no diretório comprovantes/, sem o .txt. Default: COM
```


## Verificações

Caso algo de errado na aplicação, é recomendado testar as APIS através do PostMan.
Os métodos PHP da classe API possuem um parâmetro, especificado em phpdocs, para debug, onde o padrão é false. Este valor não deve ser alterado em ambiente de produção.

## Configuração do CRON

Execute o comando abaixo no CMD (prompt de comando) para configurar o CRON no Windows.
```
    schtasks /create /sc minute /mo 1 /tn fast2pay /tr "C:\wamp64\www\fast2pay\cronjob\fast2pay.vbs /mo 1 /ru ""
```

##Liberando acesso através da rede

Para obter acesso do servidor através de outros computadores da rede, devemos configurar o WAMP. Este passo a passo foi retirado do blog do [Tiago Matos](http://www.tiagomatos.com/blog/servidor-wamp-acessivel-via-rede).

Abra o arquivo httpd.conf localizado na sua pasta de instalação wamp/bin/apache/apacheX.X.X/conf/httpd.conf. Ou se preferir, clique no ícone do wamp localizado na Área de Notificação, escolha Apache > httpd.conf. Localize o trecho abaixo:

```
#
# Controls who can get stuff from this server.
#

# onlineoffline tag - don't remove
Order Deny,Allow
#Deny from all
Allow from 127.0.0.1
```

Estas linhas devem ser substítuidas por:

```
#
# Controls who can get stuff from this server.
#

# onlineoffline tag - don't remove
Require all granted
```

Reinicie todos os serviços do servidor WAMP.

## Desenvolvimento

Gabriel Stringari de Miranda – [@stringariSM](https://github.com/stringariSM) – gabriel@gabrielstringari.com


## Atualizações

- Atualizaçāo automática funcionando.
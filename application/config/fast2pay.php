<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
/--------------------------------------------------------------------------
/ Configuração da Integração - Bematech
/--------------------------------------------------------------------------
/ url_bematech = Deve conter a url padrão para a chamada da API. Informar o valor com http:// até o último /.
/               Se o script rodar local, usar "http://127.0.0.1/".
/
/ url_bematech_base = Deve conter a base da chamada da API para a Bematech.
/                     No caso de ser local IntegracaoPedidosOnlineIntranet, se for online IntegracaoPedidosOnline
/
*/

$config['url_bematech'] = 'http://127.0.0.1/';
$config['url_bematech_base'] = 'IntegracaoPedidosOnlineIntranet/';
$config['CodigoEstabelecimento'] = '';
$config['CodigoIntegracao'] = '44';

/*
/--------------------------------------------------------------------------
/ Configuração da Integração - Fast2Pay
/--------------------------------------------------------------------------
/ url_fast2pay = Deve conter a url padrão para a chamada da API. Informar o valor com http:// até o último /.
/               Conferir manual de integração do fast2pay. Ex (url de testes): "https://wst.fast2pay.com.br/v2.0/_integra/"
/ file_extension = A chamada da API é feita para um arquivo .php, portanto deve ser colocada a extensão. Caso seja alterado para omitir a extensão, deve-se remover esta configuração.
/ user_fast2pay = Usuário específico do cliente. Necessário para a chamada da API do fast2pay
/ pass_fast2pay = Senha específica do cliente. Necessário para a chamada da API do fast2pay
/ cnpj_fast2pay = CNPJ do cliente. Necessário para a chamada da API do fast2pay
*/

$config['url_fast2pay'] = 'https://wst.fast2pay.com.br/v2.0/_integra/';
$config['file_extension'] = '.php';
$config['user_fast2pay'] = 'gemoojen@gmail.com';
$config['pass_fast2pay'] = 'zero54';
$config['cnpj_fast2pay'] = '23305714000103';

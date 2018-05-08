<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Integracao extends CI_Controller {

    private $configuration = array();
    public function __construct()
    {
        date_default_timezone_set("America/Sao_Paulo");
        setlocale(LC_ALL, 'pt_BR');

        //Teste
        parent::__construct();
        $this->config->load('fast2pay');
    }

    /**
    * @author Gabriel Stringari de Miranda [gabriel@gabrielstringari.com]
    * @version 2018-05-04
    */
	public function index()
    {
        $lMesasBematech = array();
        $lMesasFast2Pay = array();

		log_message('debug', 'Script Iniciado');

        //Trazemos todas as mesas abertas no Bematech
        $lMesasAbertasBematech = $this->api->getBematechApi('ConsultarMesasAbertas');
        if(isset($lMesasAbertasBematech)
            && isset($lMesasAbertasBematech->ConsultarMesasAbertasResult)
                && $lMesasAbertasBematech->ConsultarMesasAbertasResult->Erros == null
                    && isset($lMesasAbertasBematech->ConsultarMesasAbertasResult->Mesas)
                        && count($lMesasAbertasBematech->ConsultarMesasAbertasResult->Mesas) > 0)
        {
            $lMesasAbertasBematech = $lMesasAbertasBematech->ConsultarMesasAbertasResult->Mesas;
            foreach ($lMesasAbertasBematech as $key => $val) {
                //No status 3 a mesa esta fechada, portando não pode ser vinculada novamente
                if((int)$val->StatusMesa !== 3){
                    $lMesasBematech[(int)$val->NumeroMesa] = (int)$val->NumeroMesa;
                }
            }
        }

        //E todas as vinculadas (não capturadas) do Fast2Pay
        $lMesasVinculadasFast = $this->api->getFast2PayAPi('usuario_vincula_consulta');

		//Verificação de Erros
		if(isset($lMesasVinculadasFast)
			&& isset($lMesasVinculadasFast->Solicitacao)
				&& ($lMesasVinculadasFast->Solicitacao->Resultado == 'False'))
		{
			echo ('Ocorreu um erro na consulta.<br/>Erro: '.$lMesasVinculadasFast->Solicitacao->Codigo.' - '.$lMesasVinculadasFast->Solicitacao->Mensagem.'<hr/>');
			log_message('debug', 'Ocorreu um erro na consulta.<br/>Erro: '.$lMesasVinculadasFast->Solicitacao->Codigo.' - '.$lMesasVinculadasFast->Solicitacao->Mensagem);
			if($lMesasVinculadasFast->Solicitacao->Codigo == 'E001')
				die();
		}

		if(!isset($lMesasAbertasBematech)
			|| (isset($lMesasAbertasBematech) && empty($lMesasAbertasBematech)))
		{
			echo ('Ocorreu um erro na consulta de Mesas da Bematech. Possível problema de conexão com o IIS.<hr/>');
			log_message('debug', 'Ocorreu um erro na consulta de Mesas da Bematech. Possível problema de conexão com o IIS.');
		}

        if(isset($lMesasVinculadasFast)
            && isset($lMesasVinculadasFast->Solicitacao)
                    && !(isset($lMesasVinculadasFast->Solicitacao->Resultado) && $lMesasVinculadasFast->Solicitacao->Resultado == 'False')
        ){
            foreach ($lMesasVinculadasFast as $keyFast => $valFast) {
                $idMesa = (int)$valFast->Id;
                if(in_array($idMesa, $lMesasBematech)){
                    //Geramos o número do documento com o Ano-Mês-Dia-Hora-Minuto-Segundo-Numero da Mesa
                    $nrDocumento = date('Y').date('m').date('d').date('H').date('i').date('s').(string)$idMesa;
                    $lInsertMesa = array(
                        'id_table' =>  $idMesa,
                        'cpf'     =>  (string)$valFast->CPF,
                        'rg'       => (string)$valFast->RG,
                        'date'     =>  date('Y-m-d'),
                        'status'   =>  1,
                        'cobranca' => 0,
                        'nrdocumento' => $nrDocumento,
                    );

					$lVinculadoAnterior = $this->integracao_m->get(array(
                        'id_table' => (int)$idMesa,
                        'status'   => 1,
                    ));

                    if (isset($lVinculadoAnterior) && is_array($lVinculadoAnterior) && count($lVinculadoAnterior))
                    {
                        //Verifica se possui uma cobrança da mesma mesa vinculada anteriormente. Se possuir, exclui a vinculada e cria a nova
                        $mesa_vinculada = end($lVinculadoAnterior);
                        $this->integracao_m->update($mesa_vinculada->nrdocumento, array(
                            'status' => 9,
                            'json_pagamento' => 'Excluído após vinculação em outro aparelho.'
                        ));

                        $this->api->getFast2PayAPi('cobranca_exclui', array(
                            'cdchave' => 1,
                            'vlchave' => (string)$mesa_vinculada->rg,
                            'nrdocumento' => $mesa_vinculada->nrdocumento
                        ));
                    }

                    //Vamos salvar os dados no banco de integração, para sabermos que foi capturado
                    $this->integracao_m->insert($lInsertMesa);

                    //Depois de salvar, chamamos a API de captura, para informar ao fast2pay que não precisa mais retornar esse cara
                    $this->api->getFast2PayAPi('usuario_vincula_captura', array(
                        'id' => $idMesa
                    ));
                }
            }
        }

        //Agora vamos verificar se possuímos alguma mesa vinculada, já capturada e realizar as operações necessárias
        $lMesasCapturadas = $this->integracao_m->get(array('status' => 1));
		//var_dump($lMesasCapturadas);
        if($lMesasCapturadas && is_array($lMesasCapturadas) && count($lMesasCapturadas) > 0){
            foreach ($lMesasCapturadas as $keyCap => $valCap) {
                $lConsultaMovimentacao = $this->api->getBematechApiJson('ConsultarMovimentacaoMesa', array('parametros' => array('NumeroMesa' => (int)$valCap->id_table)));
                $lConsultaMovimentacao = isset($lConsultaMovimentacao->ConsultarMovimentacaoMesaResult) ? $lConsultaMovimentacao->ConsultarMovimentacaoMesaResult : false;

				if($lConsultaMovimentacao && ((int)$lConsultaMovimentacao->StatusMesa !== 1 && (int)$lConsultaMovimentacao->StatusMesa !== 2))
				{
					//Setamos o status como 2, isso quer dizer que foi pago, mas ainda não saiu
                    $this->integracao_m->update($valCap->nrdocumento, array(
						'status'         => 2,
                        'json_pagamento' => 'Pago no Caixa',
                        'caixa'          => 1
					));

					$this->api->getFast2PayAPi('cobranca_exclui', array(
						'cdchave'     => 1,
						'vlchave'     => (string)$valCap->rg,
						'nrdocumento' => $valCap->nrdocumento
					));
				}

                $lDescricaoConsumo = 'Comanda/Mesa: '.(string)$valCap->id_table;
                $lValorTotal = 0.0;
                if($lConsultaMovimentacao && isset($lConsultaMovimentacao->Itens)){
                    //Formato o valor sempre em 2 casas depois da vírgula
                    $lValorTotal = number_format($lConsultaMovimentacao->Totais->TotalConta,2);

                    foreach ($lConsultaMovimentacao->Itens as $keyProduto => $valProduto) {
                        if($lDescricaoConsumo !== '') $lDescricaoConsumo .= '\n';
                        $lDescricaoConsumo .= $valProduto->Quantidade.' - '.$valProduto->Produto->Descricao.' - R$ '.$valProduto->Produto->PrecoVenda;
                    }
                }

                if((int)$valCap->cobranca == 0){
                    //Se ainda não foi feito o registro da cobrança, insere-se uma nova no fast2pay
                    $this->api->getFast2PayAPi('cobranca_nova', array(
                        'cdchave' => 1,
                        'vlchave' => (string)$valCap->rg,
                        'dscobranca' => $lDescricaoConsumo,
                        'vlvalor' => $lValorTotal,
                        'nrdocumento' => $valCap->nrdocumento
                    ));

                    //Atualiza-se o registro no banco para informar que a cobrança foi feita
                    $this->integracao_m->update($valCap->nrdocumento, array(
                        'cobranca' => 1,
                        'valor' => (float)$lValorTotal
                    ));
                }else{
                    if($valCap->valor != $lValorTotal)
                    {
                        //Caso já tenha sido feito o registro da cobrança, altera a existente
                        $this->api->getFast2PayAPi('cobranca_altera', array(
                            'cdchave' => 1,
                            'vlchave' => (string)$valCap->rg,
                            'dscobranca' => $lDescricaoConsumo,
                            'vlvalor' => $lValorTotal,
                            'nrdocumento' => $valCap->nrdocumento
                        ));
                        //Atualiza-se o registro no banco para informar que a cobrança foi feita
                        $this->integracao_m->update($valCap->nrdocumento, array(
                            'cobranca' => 1,
                            'valor' => (float)$lValorTotal
                        ));
					}
				}

                //Agora vamos verificar o pagamento
                $cobrancas = $this->api->getFast2PayAPi('pagamento_consulta', array(
                    'cdchave' => 1,
                    'vlchave' => (string)$valCap->rg,
                    'dscobranca' => $lDescricaoConsumo,
                    'vlvalor' => $lValorTotal,
                    'nrdocumento' => $valCap->nrdocumento
                ));

                //Para pegar é necessário não estar no status de Digitação ou de Fechado
                if(isset($cobrancas->Pagamento) && ((int)$lConsultaMovimentacao->StatusMesa !== 3 && (int)$lConsultaMovimentacao->StatusMesa !== 2))
				{
					foreach($cobrancas as $keyCobranca => $valCobranca)
					{
						$resultCobranca = $valCobranca;
						//Se o status for 1 = Pago, 2 = Em Aberto, 3 = Cancelado - Consultar maual fast2pay para verificar retorno
						if($resultCobranca->Resultado == 'True' && (int)$resultCobranca->Status == 1)
						{
							//Gero um pedido com o produto definido para a fast2pay
							$this->api->getBematechApiJson('EnviarPedido', array(
								'parametros' => array(
									'CodigoIntegracao' => (int)$this->config->item('CodigoIntegracao'),
									'Pedido' => array(
										'NumeroMesa' => (int)$valCap->id_table,
										'CodigoGarcom' => 6969,
										'Itens' => array(
											array(
											   'CodigoExterno' => (string)$valCap->nrdocumento,
											   'TipoItem' => '0',
											   'Produto' => array(
												  'Codigo' => PRODUTO_FAST2PAY,
												  'Descricao' => 'COMANDA MESA FAST2PAY',
												  'PrecoVenda' => '0.01'
											   ),
											   'Quantidade' => '1',
											   'ValorTotal' => '0.01'
											)
										)
									)
								)
							), false);

							//Chamo a API da Bematech para fechar a conta do cliente
							$this->api->getBematechApiJson('FecharConta', array(
								'parametros' => array(
									'Conta' => array(
										'NumeroMesa' => (int)$valCap->id_table,
										'QuantidadePessoas' => '1',
										'TirarServico' => 'false',
										'Desconto' => '0.01'
									)
								)
							), false);


                            //Verifica a flag de impressão
                            if(isset($valCap->printed) && (int)$valCap->printed != 1)
                            {
                                //Buscamos o arquivo de comprovante
                                $arquivoComprovante = file_get_contents(FCPATH.'comprovantes/'.PORTA_IMPRESSAO . '.txt',"r");

                                //Vamos alterar as informações de {NUMERO_MESA}, {DATA_HORA} e {NOME_CLIENTE}
                                $arquivoComprovante = str_replace('{NUMERO_MESA}', (string)$valCap->id_table, $arquivoComprovante);
                                $arquivoComprovante = str_replace('{DATA_HORA}', (string)date('d/m/Y H:i:s'), $arquivoComprovante);
                                $arquivoComprovante = str_replace('{NOME_CLIENTE}', (string)$resultCobranca->Nome, $arquivoComprovante);

                                $fpComprovante = fopen(PASTA_IMPRESSAO . (string)$valCap->nrdocumento . '.txt', "a");

                                if(fwrite($fpComprovante, $arquivoComprovante)){
                                    $this->integracao_m->update($valCap->nrdocumento, array(
                                        'printed' => 1
                                    ));
                                }

                                fclose($fpComprovante);

                                //Setamos o status como 2, isso quer dizer que foi pago, mas ainda não saiu
                                $this->integracao_m->update($valCap->nrdocumento, array(
                                    'status' => 2,
                                    'json_pagamento' => json_encode($resultCobranca),
                                ));
                            }
						}
					}
                }
            }
        }

		log_message('debug', 'Script Finalizado');
        echo 'Integração executada!';
    }
}
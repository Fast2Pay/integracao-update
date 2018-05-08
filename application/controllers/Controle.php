<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Controle extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->config->load('fast2pay');
        $this->load->model('integracao_m');
    }

    /**
     * @author Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
     * @version 2017-12-22
     */
    public function index()
    {
        $lMesasPagas = $this->integracao_m->get(array('status' => 2, 'exit' => 0));
        $this->load->view('controle', array('mesas' => $lMesasPagas));
    }

    public function search()
    {
        if (!$this->input->is_ajax_request()) {
           die('No direct script access allowed');
        }

        $id_table = (int)$this->input->post('id_table');
        $lmesa = $this->integracao_m->get(array('id_table' => $id_table,'status' => 2, 'exit' => 0));

        $retorno = array('status'=> false);
        if(isset($lmesa) && is_array($lmesa) && count($lmesa) > 0){
            $lmesa = end($lmesa);

            if(!empty($lmesa) && ($lmesa->status == 2) && ($lmesa->cobranca == 1) && ($lmesa->exit !== 0) && (!empty($lmesa->json_pagamento))){
                $retorno['status'] = true;
                $retorno['mesa'] = $lmesa;

                $this->integracao_m->update($lmesa->nrdocumento, array('status' => 9, 'exit' => 1));
            }else{
                $retorno['status'] = false;
            }
        }else{
            $lConsultaMovimentacao = $this->api->getBematechApiJson('ConsultarMovimentacaoMesa', array('parametros' => array('NumeroMesa' => (int)$id_table)));
            $lConsultaMovimentacao = isset($lConsultaMovimentacao->ConsultarMovimentacaoMesaResult)
                                        ? $lConsultaMovimentacao->ConsultarMovimentacaoMesaResult
                                        : false;

            if($lConsultaMovimentacao && (int)$lConsultaMovimentacao->StatusMesa == 0)
			{
                $retorno['status'] = true;
                $retorno['mesa'] = array(
                    'caixa' => 1,
					'bematech' => 1
                );
            }elseif($lConsultaMovimentacao && (int)$lConsultaMovimentacao->StatusMesa == 3)
			{
				if(isset($lConsultaMovimentacao->Itens)){
					foreach ($lConsultaMovimentacao->Itens as $keyProduto => $valProduto)
					{
						if(isset($valProduto->Produto) && $valProduto->Produto->Codigo == PRODUTO_FAST2PAY){
							$retorno['status'] = true;
							$retorno['mesa'] = array(
								'caixa' => 1,
								'bematech' => 1
							);
						}
					}
				}
			}


        }

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($retorno));
    }
}

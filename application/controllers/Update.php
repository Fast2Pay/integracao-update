<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Update extends CI_Controller {

    public function __construct()
    {
        date_default_timezone_set("America/Sao_Paulo");
        setlocale(LC_ALL, 'pt_BR');

        parent::__construct();

        $this->load->library('github_updater');
    }

    public function index()
    {
        try{
            log_message('debug', 'Atualização iniciada.');
            if($this->github_updater->has_update()){
                if($this->github_updater->update()){
                    log_message('info', 'Aplicação de integração atualizada com sucesso.');
                    echo( 'Aplicação de integração atualizada com sucesso.' );
                }else{
                    log_message('error', 'Ocorreu um erro ao atualizar a aplicação.');
                    echo( 'Ocorreu um erro ao atualizar a aplicação.' );
                }
            }else{
                log_message('info', 'Você já possui a última versão da aplicação de integração.');
                echo( 'Você já possui a última versão da aplicação de integração.' );
            }
            log_message('debug', 'Atualização iniciada.');
        }catch(Exception $e){
            log_message('error', 'Ocorreu um erro ao atualizar a aplicação. '.$e);
        }
    }
}
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
        if($this->github_updater->has_update()){
            if($this->github_updater->update()){
                die( 'Aplicação de integração atualizada com sucesso.' );
            }else{
                die( 'Ocorreu um erro ao atualizar a aplicação.' );
            }
        }else{
            die( 'Você já possui a última versão da aplicação de integração.' );
        }
    }
}
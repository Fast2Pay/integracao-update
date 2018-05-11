<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Api{

    var $ci;
    var $key;
    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->config->load('fast2pay');
        $this->ci->load->library('curl');
    }

    /**
    * Converte o XML em string para um objeto
    * @author Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
    */
    private function _xmlToObject($pStringXML)
    {
        return simplexml_load_string($pStringXML);
    }

    /**
    * Converte o JSON em string para um objeto
    * @author Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
    */
    private function _jsonToObject($pStringJson)
    {
        return (Object) json_decode($pStringJson);
    }

     /**
    * Realiza a chamada da API do Bematech, baseado nas configurações
    * @author Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
    * @version 2017-11-21
    *
    * @param string $pMethod String com o método a ser acessado.
    * @param array[] $pParams Array com os parâmetros que devem ser passados na chamada.
    *
    * @return ArrayObject - retorno da chamada
    */
    public function getBematechApi($pMethod, $pParams = array(), $pDebug = false)
    {
        try{
            $this->ci->curl->create($this->ci->config->item('url_bematech').$this->ci->config->item('url_bematech_base').'MesaService.svc/'.$pMethod);

            $this->ci->curl->post();
            $this->ci->curl->ssl(false);

            $response = $this->ci->curl->execute();
            // Errors
            if($pDebug == true){
                // echo '<pre>';die(var_dump($response));
                echo '<hr> Error code: ';var_dump($this->ci->curl->error_code);
                echo '<hr> Error String: ';var_dump($this->ci->curl->error_string);
                echo '<hr> Information: ';var_dump($this->ci->curl->info); // array
            }
        } catch (Exception $e){

        }

        return $this->_jsonToObject($response);
    }


    public function getBematechApiJson($pMethod, $pParams = array(), $pDebug = false)
    {
        try{
            $data_string = json_encode($pParams);



            //VERIFICAR FUNCIONAMENTO
            $ch = curl_init($this->ci->config->item('url_bematech').$this->ci->config->item('url_bematech_base').'MesaService.svc/'.$pMethod);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );

            $response = curl_exec($ch);
        } catch (Exception $e){

        }

        return $this->_jsonToObject($response);
    }

     /**
    * Realiza a chamada da API do Fast2Pay, baseado nas configurações
    * @author Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
    * @version 2017-11-21
    *
    * @param string $pMethod String com o método a ser acessado.
    * @param array[] $pParams Array com os parâmetros que devem ser passados na chamada.
    *
    * @return ArrayObject - retorno da chamada
    */
    public function getFast2PayAPi($pMethod, $pParams = array(), $pDebug = false)
    {
        $this->ci->curl->create($this->ci->config->item('url_fast2pay') . $pMethod . $this->ci->config->item('file_extension'));
        $post = array(
            'usuario' => $this->ci->config->item('user_fast2pay'),
            'senha' => $this->ci->config->item('pass_fast2pay'),
            'cnpj' => $this->ci->config->item('cnpj_fast2pay'),
        );

        $post = array_merge($post, $pParams);

        $this->ci->curl->post($post);
        $this->ci->curl->ssl(false);

        $response = $this->ci->curl->execute();

        // Errors
        if($pDebug == true){
            // echo '<pre>';die(var_dump($response));
            echo '<hr> Error code: ';var_dump($this->ci->curl->error_code);
            echo '<hr> Error String: ';var_dump($this->ci->curl->error_string);
            echo '<hr> Information: ';var_dump($this->ci->curl->info); // array
        }

        return $this->_xmlToObject($response);
    }
}
/* End of file Curl.php */
/* Location: ./application/libraries/Curl.php */
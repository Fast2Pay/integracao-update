<?php
/**
* Model que faz o jogo com a tabela de integração
* @author Gabriel Stringari <gabriel@gabrielstringari.com>
* @version 2017-11-17
*/
class Integracao_m extends F2P_Model {

        private $table = 'f2p_linked_tables';

        public function get(array $where = array())
        {
            $this->db->select('*')
                     ->from($this->table);

            if(isset($where) && $where && is_array($where) && (count($where) > 0)){
                $this->db->where($where);
            }

            $result = $this->db->get()->result();
            return $result;
        }

        public function insert(array $insert)
        {
            $this->db->trans_start();
            $this->db->insert($this->table, $insert);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                log_message();
            }
        }

        public function update(string $nrdocumento, array $update)
        {
            if($update && is_array($update) && count($update) > 0){
                foreach ($update as $key => $value) {
                    $this->db->set($key, $value);
                }
                $this->db->where('nrdocumento', $nrdocumento);
                $this->db->update($this->table);
            }
        }

        public function delete()
        {
            $this->db->update($this->table, $this, array('id' => $_POST['id']));
        }

}
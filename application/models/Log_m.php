<?php
/**
* Model que faz o jogo com a tabela de integração
* @author Gabriel Stringari <gabriel@gabrielstringari.com>
* @version 2017-11-17
*/
class Log_m extends F2P_Model {

        private $table = 'f2p_logs';

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

        public function getToday(array $where = array())
        {
            $this->db->select('*')
                     ->from($this->table)
                     ->where('date', date('Y-m-d'));

            $result = $this->db->get()->row();
            return $result;
        }

        public function insert(array $insert)
        {
            $default = array(
                'date' => date('Y-m-d'),
                'time' => date('H:m:i'),
            );

            $insert = array_merge($default, $insert);

            $this->db->trans_start();
            $this->db->insert($this->table, $insert);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
            {
                log_message();
            }
        }
}
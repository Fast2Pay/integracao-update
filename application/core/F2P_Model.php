<?php (defined('BASEPATH')) or exit('No direct script access allowed');

/**
 * CodeIgniter MX_Model
 *
 * Metodos utilizado em todos os models no projeto atual.
 *
 * @package     CodeIgniter
 * @author      Gabriel Stringari de Miranda <gabriel@gabrielstringari.com>
 * @subpackage  Model
 * @category    Model
 * @link        http://gabrielstringari.com
 * @version 1.0.0
 *
 */
class F2P_Model extends CI_Model
{

    public function __construct()
    {
        //Carrega o banco de dados
        $this->load->database();

        //Seta timezone do brasil no MYSQL
        $this->db->simple_query("SET time_zone= '+3:00'");
        $this->db->simple_query("SET lc_time_names= 'pt_BR'");

        $this->db->query("SET SESSION group_concat_max_len = 1000000");

        parent::__construct();

    }
}

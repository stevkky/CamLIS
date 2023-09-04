<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//require_once (APPPATH.'libraries/Aauth.php');
class Caauth extends Aauth
{
	public function __construct()
	{
		parent::__construct();
	}

    /**
     * Create user group
     * @param string $group_name
     * @param string $definition
     * @param string $default_page
     * @return bool
     */
	public function create_group($group_name, $definition = '', $default_page = '')
    {
        $query = $this->aauth_db->get_where($this->config_vars['groups'], array('name' => $group_name));

        if ($query->num_rows() < 1) {

            $data = array(
                'name' => $group_name,
                'definition'=> $definition,
                'default_page' => $default_page
            );
            $this->aauth_db->insert($this->config_vars['groups'], $data);
            return $this->aauth_db->insert_id();
        }

        $this->info($this->CI->lang->line('aauth_info_group_exists'));
        return FALSE;
    }

    /**
     * Update user group
     * @param $group_par
     * @param bool $group_name
     * @param bool $definition
     * @param bool $default_page
     * @return mixed
     */
    public function update_group($group_par, $group_name=FALSE, $definition=FALSE, $default_page=FALSE) {

        $group_id = $this->get_group_id($group_par);

        if ($group_name != FALSE) {
            $data['name'] = $group_name;
        }

        if ($definition != FALSE) {
            $data['definition'] = $definition;
        }

        if ($default_page != FALSE) {
            $data['default_page'] = $default_page;
        }

        $this->aauth_db->where('id', $group_id);
        return $this->aauth_db->update($this->config_vars['groups'], $data);
    }
}
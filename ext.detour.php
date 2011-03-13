<?php 

class Detour_ext {

	var $settings        = array();
	var $name            = 'Detour';
	var $version         = '0.5';
	var $description     = 'Reroute urls to another URL.';
	var $settings_exist  = 'y';
	var $docs_url        = 'http://www.cityzen.com/addons/detour';
	var $urlName		 = 'detour';
	
	function Detour_ext($settings = FALSE)
	{
	
		$this->__construct($settings);
		
	}
	
	
	function __construct($settings = FALSE){

		$this->settings = $settings;
		$this->EE =& get_instance();
	
	}
	
	
	function sessions_start(){
	
		$url = $this->EE->uri->uri_string;

		$sql = "SELECT new_url, detour_method
		FROM exp_detours 
		WHERE original_url = '" . $this->EE->db->escape_str($url) . "' LIMIT 1";

		$query = $this->EE->db->query($sql);

		if($query->num_rows() > 0)
		{
			$row = $query->row();

			header('Location: ' . $row->new_url, TRUE, $row->detour_method);		
			$this->extensions->end_script;
			exit;
		}
	
	}


	function settings_form($current)
	{
		
		$vars = array();
		
		$vars['current'] = $current;
		
		$vars['file'] = $this->urlName;
		
		$vars['detour_options'] = array(
			'detour' => $this->EE->lang->line('option_detour'),
			'ignore' => $this->EE->lang->line('option_ignore'),
		);
		
		// Get current Detours
		
		$vars['currentDetours'] = array();
		
		$currentDetoursSQL = $this->EE->db->query("SELECT detour_id, original_url, new_url, detour_method
			FROM exp_detours 
			ORDER BY detour_id");
		
		foreach($currentDetoursSQL->result_array() as $value)
		{
			extract($value);
			$vars['currentDetours'][] = array($original_url, $new_url, $detour_id, $detour_method);
		}
		
		return $this->EE->load->view('settings', $vars, TRUE);
				
	}


	function save_settings()
	{
		
		
		$this->EE->load->helper('string');
		
		unset(
			$_POST['file'], 
			$_POST['submit']
		);
		
		if( ($_POST['old_url']) && ($_POST['new_url']) ){
			
			$original_url = trim_slashes(trim($_POST['old_url']));
		
			$data = array(
				'original_url' => xss_clean($original_url),
				'new_url' => $_POST['new_url'], 
				'detour_method' => $_POST['new_detour_method']
			);
	
			$this->EE->db->insert('exp_detours', $data);
			
		}
		
		if(!empty($_POST['detour_delete'])){
			
			$delete_sql = "DELETE 
			FROM exp_detours 
			WHERE detour_id IN (" . implode(',', $_POST['detour_delete']) . ")";

			$this->EE->db->query($delete_sql);
			
		}
		
		$this->EE->functions->redirect(
			BASE.AMP.'C=addons_extensions'.AMP.'M=extension_settings'.AMP.'file=detour'
		);

	}

	
	function activate_extension()
	{
	
		$this->EE->load->dbforge();
	
		$data = array(
		  'class'       => __CLASS__,
		  'hook'        => 'sessions_start',
		  'method'      => 'sessions_start',
		  'settings'    => serialize($this->settings),
		  'priority'    => 1,
		  'version'     => $this->version,
		  'enabled'     => 'y'
		);
		
		// insert in database
		$this->EE->functions->clear_caching('db');
		$this->EE->db->insert('exp_extensions', $data);
		
		$fields = array(
			'detour_id'	=> array('type' => 'int', 'constraint' => '10', 'unsigned' => TRUE, 'auto_increment' => TRUE),
			'original_url'	=> array('type' => 'varchar', 'constraint' => '250'),
			'new_url'	=> array('type' => 'varchar', 'constraint' => '250', 'null' => TRUE, 'default' => NULL), 
			'detour_method' => array('type' => 'int', 'constraint' => '3', 'unsigned' => TRUE, 'default' => '301')
		);

		$this->EE->dbforge->add_field($fields);
		$this->EE->dbforge->add_key('detour_id', TRUE);
	
		$this->EE->dbforge->create_table('detours');
		
		unset($fields);
	      
	}

	
	function disable_extension()
	{
		$this->EE->load->dbforge();
		
		$this->EE->functions->clear_caching('db');
		$this->EE->db->where('class', __CLASS__);
		$this->EE->db->delete('exp_extensions');
		
		$this->EE->dbforge->drop_table('detours');
		
	}	
	
}
//END CLASS




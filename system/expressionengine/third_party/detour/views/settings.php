<?php   if ( ! defined('BASEPATH')) exit('No direct script access allowed');
    $this->EE =& get_instance();
?>

<?php foreach ($cp_messages as $cp_message_type => $cp_message) : ?>
	<p class="notice <?=$cp_message_type?>"><?=$cp_message?></p>
<?php endforeach; ?>

<?php	
	echo form_open('C=addons_extensions'.AMP.'M=save_extension_settings'.AMP.'file='.$file);

	$this->table->set_template($cp_pad_table_template);
	$this->table->set_heading(
		array(
			'data' => $this->EE->lang->line('title_url'),
			'style' => 'width:45%;'
		),
		array(
			'data' => $this->EE->lang->line('title_redirect'),
			'style' => 'width:45%;'
		),
		array(
			'data' => $this->EE->lang->line('title_method')
		),	
		array(
			'data' => 'Delete'
		)
	);

	foreach($currentDetours as $detour)
	{
	
		/*
		if($detour[3] == '301'){
			$detour_method = '<select name="detour_method[' . $detour[0] . '][]"><option value="301" selected>301</option><option value="302">302</option></select>';
		}elseif($detour[3] == '302'){
			$detour_method = '<select name="detour_method[' . $detour[0] . '][]"><option value="301">301</option><option value="302" selected>302</option></select>';
		}
		*/	
	
		$this->table->add_row(
			$detour[0],
			$detour[1],
			'<strong>' . $detour[3] . '</strong>',
			'<input type="checkbox" name="detour_delete[]" value="' . $detour[2] . '" />'
		);
	
	}
	
	$this->table->add_row(
		form_input('old_url', ''),
		form_input('new_url', ''),
		'<select name="new_detour_method"><option value="301">301</option><option value="302">302</option></select>',
		'&nbsp;' // Just a space placeholder
	);

	echo $this->table->generate();							
	
	echo form_submit(array('name' => 'submit', 'value' => $this->EE->lang->line('save_settings'), 'class' => 'submit'));
	echo form_close();

	
?>
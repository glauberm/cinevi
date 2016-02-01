<?php
// ensure this file is being included by a parent file
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/*
*
* Main template for JComments. Don't change it without serious reasons ;)
* Then creating new template you can copy this file to new template's dir without changes
*
* Style for JComments component created by GavickPro
*
*/
class jtt_tpl_index extends JoomlaTuneTemplate
{
	function render() 
	{
		$object_id = $this->getVar('comment-object_id');
		$object_group = $this->getVar('comment-object_group');

		// comments data is prepared in tpl_list and tpl_comments templates 
		$comments = $this->getVar('comments-list', '');

		// form data is prepared in tpl_form template.
		$form = $this->getVar('comments-form');

		if ($comments != '' || $form != '') 
		{
			// include comments css (only if we are in administor's panel)
			if ($this->getVar('comments-css', 0) == 1) 
			{
				include_once (JCOMMENTS_HELPERS . DS . 'system.php');
				echo '<link href="'.JCommentsSystemPluginHelper::getCSS().'" rel="stylesheet" type="text/css" />';
			}

			// include JComments JavaScript initialization

			echo '<script type="text/javascript">';
			echo 'var jcomments=new JComments('.$object_id.', \''.$object_group.'\',\''.$this->getVar('ajaxurl').'\');';
			echo 'jcomments.setList(\'comments-list\');';
			echo '</script>';
			// IMPORTANT: Do not rename this div's id! Some JavaScript functions references to it!
			echo '<div id="jc">';
			echo '<div id="comments">'.$comments.'</div>';
			// Display comments form (or link to show form)
			if (isset($form)) 
			{ 
				echo $form; 
			}

			echo '<div id="comments-footer" align="center">'.$this->getVar('support').'</div>';
			
			// Some magic like dynamic comments list loader (anticache) and auto go to anchor script
			$aca = (int) ($this->getVar('comments-gotocomment') == 1);
			$acp = (int) ($this->getVar('comments-anticache') == 1);
			$acf = (int) (($this->getVar('comments-form-link') == 1) && ($this->getVar('comments-form-locked', 0) == 0));

			if ($aca || $acp || $acf) 
			{
				echo '<script type="text/javascript">';
				echo 'jcomments.setAntiCache('.$aca.','.$acp.','.$acf.');';
				echo '</script>'; 
			}
			
			echo '</div>';
		}
	}
}
?>
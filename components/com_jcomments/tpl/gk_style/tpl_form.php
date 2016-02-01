<?php
// ensure this file is being included by a parent file
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/*
 * Comments form template
 * Style for JComments component created by GavickPro
 */
class jtt_tpl_form extends JoomlaTuneTemplate
{
	function render() 
	{
		if ($this->getVar('comments-form-message', 0) == 1) 
		{
			$this->getMessage( $this->getVar('comments-form-message-text') );
			return;
		}
		
		if ($this->getVar('comments-form-link', 0) == 1) 
		{
			$this->getCommentsFormLink();
			return;
		}

		$this->getCommentsFormFull();
	}

	/*
	 * Displays full comments form (with smiles, bbcodes and other stuff) 
	 */
	function getCommentsFormFull()
	{
		$object_id = $this->getVar('comment-object_id');
		$object_group = $this->getVar('comment-object_group');
	
		echo '<h4><span>'.JText::_('FORM_HEADER').'</span></h4>';
		echo '<a id="addcomments" href="#addcomments"></a>';
		echo '<form id="comments-form" name="comments-form" action="javascript:void(null);">';
	
		if ($this->getVar( 'comments-form-policy', 0) == 1) 
		{
			echo '<div class="comments-policy">'.$this->getVar( 'comments-policy' ).'</div>';
		}
		
	 	if ($this->getVar( 'comments-form-user-name', 1) == 1)
	 	{
			echo '<p class="jc_field">';
			echo '<label for="comments-form-name">'.JText::_('FORM_NAME').'</label>';
			echo '<input id="comments-form-name" type="text" name="name" value="" maxlength="'.$this->getVar('comment-name-maxlength').'" size="22" tabindex="1" />';
			echo '</p>';
		}
	
		if ($this->getVar( 'comments-form-user-email', 1) == 1) 
		{
			$text = ($this->getVar('comments-form-email-required', 1) == 0) ? JText::_('FORM_EMAIL') : JText::_('FORM_EMAIL_REQUIRED');

			echo '<p class="jc_field">';
			echo '<label for="comments-form-email">'.$text.'</label>';
			echo '<input id="comments-form-email" type="text" name="email" value="" size="22" tabindex="2" />';
			echo '</p>';
		}
		
		if ($this->getVar('comments-form-user-homepage', 0) == 1) 
		{
			$text = ($this->getVar('comments-form-homepage-required', 1) == 0) ? JText::_('FORM_HOMEPAGE') : JText::_('FORM_HOMEPAGE_REQUIRED');
			echo '<p class="jc_field">';
			echo '<label for="comments-form-homepage">'.$text.'</label>';
			echo '<input id="comments-form-homepage" type="text" name="homepage" value="" size="22" tabindex="3" />';
			echo '</p>';
		}
		
		if ($this->getVar('comments-form-title', 0) == 1) 
		{
			$text = ($this->getVar('comments-form-title-required', 1) == 0) ? JText::_('FORM_TITLE') : JText::_('FORM_TITLE_REQUIRED');
			echo '<p class="jc_field">';
			echo '<label for="comments-form-title">'.$text.'</label>';
			echo '<input id="comments-form-title" type="text" name="title" value="" size="22" tabindex="4" />';
			echo '</p>';
		}

		echo '<p class="clearbox">';
		echo '<textarea id="comments-form-comment" name="comment" cols="65" rows="8" tabindex="5"></textarea>';
		echo '</p>';

		if ($this->getVar('comments-form-subscribe', 0) == 1) 
		{
			echo '<p>';
			echo '<input class="checkbox" id="comments-form-subscribe" type="checkbox" name="subscribe" value="1" tabindex="5" />';
			echo '<label for="comments-form-subscribe">'.JText::_('FORM_SUBSCRIBE').'</label><br />';
			echo '</p>';
		}

		if ($this->getVar('comments-form-captcha', 0) == 1) 
		{
			$link = JCommentsFactory::getLink('captcha');

			echo '<p class="jc_field">';
			echo '<img class="captcha" onclick="jcomments.clear(\'captcha\');" id="comments-form-captcha-image" name="captcha-image" src="'.$link.'" width="120" height="60" alt="'.JText::_('FORM_CAPTCHA').'" />';
			echo '</p>';
			
			echo '<p class="jc_field">';
			echo '<input class="captcha" id="comments-form-captcha" type="text" name="captcha-refid" value="" size="5" tabindex="6" /><br />';
			echo '</p>';
			echo '<p class="jc_field">';
			echo '<span class="captcha" onclick="jcomments.clear(\'captcha\');">'.JText::_('FORM_CAPTCHA_REFRESH').'</span>';
			echo '</p>';
		}

		echo '<div id="comments-form-buttons">';
		echo '<div class="btn" id="comments-form-send"><div><a href="#" tabindex="7" onclick="jcomments.saveComment();return false;" title="'.JText::_('FORM_SEND_HINT').'">'.JText::_('FORM_SEND').'</a></div></div>';
		echo '<div class="btn" id="comments-form-cancel" style="display:none;"><div><a href="#" tabindex="8" onclick="return false;" title="'.JText::_('FORM_CANCEL').'">'.JText::_('FORM_CANCEL').'</a></div></div>';
		echo '<div style="clear:both;"></div>';
		echo '</div>';
		echo '<input type="hidden" name="object_id" value="'.$object_id.'" />';
		echo '<input type="hidden" name="object_group" value="'.$object_group.'" />';
		echo '</form>';

		echo '<script type="text/javascript">';
		echo 'function JCommentsInitializeForm(){';
		echo 'var jcEditor = new JCommentsEditor(\'comments-form-comment\', true);';
		
		if ($this->getVar('comments-form-bbcode', 0) == 1) 
		{
			$bbcodes = array( 'b'=> array(0 => JText::_('FORM_BBCODE_B'), 1 => JText::_('Enter text'))
					, 'i'=> array(0 => JText::_('FORM_BBCODE_I'), 1 => JText::_('Enter text'))
					, 'u'=> array(0 => JText::_('FORM_BBCODE_U'), 1 => JText::_('Enter text'))
					, 's'=> array(0 => JText::_('FORM_BBCODE_S'), 1 => JText::_('Enter text'))
					, 'img'=> array(0 => JText::_('FORM_BBCODE_IMG'), 1 => JText::_('Enter full URL to the image'))
					, 'url'=> array(0 => JText::_('FORM_BBCODE_URL'), 1 => JText::_('Enter full URL'))
					, 'hide'=> array(0 => JText::_('FORM_BBCODE_HIDE'), 1 => JText::_('Enter text to hide it from unregistered'))
					, 'quote'=> array(0 => JText::_('FORM_BBCODE_QUOTE'), 1 => JText::_('Enter text to quote'))
					, 'list'=> array(0 => JText::_('FORM_BBCODE_LIST'), 1 => JText::_('Enter list item text'))
					);

			foreach($bbcodes as $k=>$v) 
			{
				if ($this->getVar('comments-form-bbcode-' . $k , 0) == 1) 
				{
					$title = trim(addslashes($v[0]));
					$text = trim(addslashes($v[1]));
					echo 'jcEditor.addButton(\''.$k.'\',\''.$title.'\',\''.$text.'\');';
				}
			}
		}

		$customBBCodes = $this->getVar('comments-form-custombbcodes');
	
		if (count($customBBCodes)) 
		{
			foreach($customBBCodes as $code) 
			{
				if ($code->button_enabled) 
				{
    				$k = 'custombbcode' . $code->id;
					$title = trim(addslashes($code->button_title));
					$text = empty($code->button_prompt) ? JText::_('Enter text') : JText::_($code->button_prompt);
					$open_tag = $code->button_open_tag;
					$close_tag = $code->button_close_tag;
					$icon = $code->button_image;
					$css = $code->button_css;
					echo 'jcEditor.addButton(\''.$k.'\',\''.$title.'\',\''.$text.'\',\''.$open_tag.'\',\''.$close_tag.'\',\''.$css.'\',\''.$icon.'\');';
    			}
			}
		}

		$smiles = $this->getVar( 'comment-form-smiles' );

		if (isset($smiles)) 
		{
			if (is_array($smiles)&&count($smiles) > 0) 
			{
				echo 'jcEditor.initSmiles(\''.$this->getVar( "smilesurl" ).'\');';
				
				foreach ($smiles as $code => $icon) 
				{
					$code = trim(addslashes($code));
					$icon = trim(addslashes($icon));

					echo 'jcEditor.addSmile(\''.$code.'\',\''.$icon.'\');';
				}
			}
		}

		if ($this->getVar( 'comments-form-showlength-counter', 0) == 1) 
		{
			echo 'jcEditor.addCounter('.$this->getVar('comment-maxlength').', \''.JText::_('FORM_CHARSLEFT_PREFIX').'\', \''.JText::_('FORM_CHARSLEFT_SUFFIX').'\', \'counter\');';
		}
		
		echo 'jcomments.setForm(new JCommentsForm(\'comments-form\', jcEditor));';
		echo '}';
		echo 'setTimeout(JCommentsInitializeForm, 100);';
		echo '</script>';
	}

	/*
	*
	* Displays link to show comments form
	* 
	*/
	function getCommentsFormLink()
	{
		$object_id = $this->getVar('comment-object_id');
		$object_group = $this->getVar('comment-object_group');

		echo '<div id="comments-form-link">';
		echo '<a id="addcomments" class="showform" href="#addcomments" onclick="jcomments.showForm('.$object_id.',\''.$object_group.'\', \'comments-form-link\'); return false;">'.JText::_('FORM_HEADER').'</a>';
		echo '</div>';

	}

	/*
	*
	* Displays service message
	* 
	*/
	function getMessage( $text )
	{
		if ($text != '') 
		{
			echo '<a id="addcomments" href="#addcomments"></a>';
			echo '<p class="message">'.$text.'</p>';
		}
	}
}

?>
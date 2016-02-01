<?php
// ensure this file is being included by a parent file
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/*
*
* Template for links (Readmore and Add comment) attached to content items 
* on fronpage, blog-section or blog-category. Used from content mambot called jcomments.content plugin
*
* Style for JComments component created by GavickPro
*
*/
class jtt_tpl_links extends JoomlaTuneTemplate
{

	/*
	*
	* Display Readmore link
	*
	*/
	function getReadmoreLink() 
	{
		if ($this->getVar('readmore_link_hidden', 0) == 1) 
		{
			return '';
		}

		$link  = $this->getVar('link-readmore');
		$text  = $this->getVar('link-readmore-text');
		$title = $this->getVar('link-readmore-title');

		return '<a class="readmore-link btn btn-primary btn-sm" href="'. $link .'" title="' . $title . '">' . $text . '</a>';
	}

	/*
	*
	* Display Comments or Add comments link
	*
	*/
	function getCommentsLink()
	{
		if ($this->getVar('comments_link_hidden') == 1) 
		{
			return '';
		}

		$style = $this->getVar('comments_link_style');
		$count = $this->getVar('comments-count');
		$link  = $this->getVar('link-comment');

		if ($count == 0) 
		{
			return '<a href="' . $link . '#addcomments" class="comment-link btn btn-link btn-sm">' . JText::_('Novo Comentário') . '</a>';
		} 
		else 
		{
			$text = JText::sprintf('Read comments', $count);

			if ($this->getVar('use-plural-forms', 0)) 
			{
				$comments_pf = JText::_('comments_pf');

				if ($comments_pf != '') 
				{
					global $mainframe;
					
					$pf = JoomlaTuneLanguageTools::getPlural($mainframe->getCfg('lang'), $count, $comments_pf);
					
					if ($pf != '') 
					{
						$text = JText::sprintf('COMMENTS2', $count, $pf);
					}
				}
			}

			switch($style) 
			{
				case -1:
					return '<span class="comment-link">' . $text . '</span>';
					break;
				default:
					return '<a href="' . $link . '#comments" class="comment-link btn btn-link">' . $text . '</a>';
					break;
			}
		}
	}
	
	/*
	*
	* Main template function
	* 
	*/
	function render() 
	{
		if ($this->getReadmoreLink() != '' || $this->getCommentsLink() != '')
		{
			echo '<div class="jcomments-links">'.$this->getReadmoreLink().' '.$this->getCommentsLink().'</div><hr>';
		}
	}
	
}
?>
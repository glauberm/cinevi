<?php
// ensure this file is being included by a parent file
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/*
*
* Comments list template. Displays comments list with header and navigation (pagination) 
*
* Style for JComments component created by GavickPro
*
*/
class jtt_tpl_tree extends JoomlaTuneTemplate
{
	function render() 
	{
		$comments = $this->getVar('comments-items');

		if (isset($comments)) 
		{
			$this->getHeader();

			echo '<div class="comments-list" id="comments-list-0">';
			$i = 0;
			
			$count = count($comments);
			$currentLevel = 0;
		
			foreach($comments as $id => $comment) 
			{
				if ($currentLevel < $comment->level) 
				{
        			echo '</div>';
					echo '<div class="comments-list" id="comments-list-'.$comment->parent.'">';				
				} 
				else 
				{
					$j = 0;
	
					if ($currentLevel >= $comment->level) 
					{
						$j = $currentLevel - $comment->level;
					} 
					else if ($comment->level > 0 && $i == $count - 1) 
					{
						$j = $comment->level;
					}

					while($j > 0) 
					{
						echo '</div>';
						$j--;
					}
				}
				
				echo '<div class="'.($i%2 ? 'odd' : 'even').'" id="comment-item-'.$id.'">';
				echo $comment->html;

				if ($comment->children == 0) 
				{
					echo '</div>';
				}
				
				if ($comment->level > 0 && $i == $count - 1)
			 	{
					$j = $comment->level;
				}

				while($j > 0) 
				{
					echo '</div>';
     				$j--;
				}

				$i++;
				$currentLevel = $comment->level;
			}

			echo '</div>';
			echo '<div id="comments-list-footer">'.$this->getFooter().'</div>';

		} 
		else 
		{
			// display single comment item (works when new comment is added)
			$comment = $this->getVar('comment-item');

			if (isset($comment)) 
			{
				$i = $this->getVar('comment-modulo');
				$id = $this->getVar('comment-id');
				echo '<div class="'.($i%2 ? 'odd' : 'even').'" id="comment-item-'.$id.'">'.$comment.'</div>';
			} 
			else 
			{
				echo '<div class="comments-list" id="comments-list-0"></div>';
			}
		}

	}

	/*
	*
	* Display comments header and small buttons: rss and refresh
	*
	*/
	function getHeader()
	{
		$object_id = $this->getVar('comment-object_id');
		$object_group = $this->getVar('comment-object_group');

		$btnRSS = '';
		$btnRefresh = '';
		
		if ($this->getVar('comments-refresh', 1) == 1) 
		{
			$btnRefresh = '<a class="refresh" href="#" title="'.JText::_('REFRESH').'" onclick="jcomments.showPage('.$object_id.',\''. $object_group . '\',0);return false;">&nbsp;</a>';
		}

		if ($this->getVar('comments-rss') == 1) 
		{
			$link = $this->getVar('rssurl');
			$btnRSS = '<a class="rss" href="'.$link.'" title="'.JText::_('RSS').'" target="_blank">&nbsp;</a>';
		}

		echo '<h4>'.JText::_('HEADER').$btnRSS.$btnRefresh.'</h4>';
	}

	/*
	*
	* Display RSS feed and/or Refresh buttons after comments list
	*
	*/
	function getFooter()
	{
		$footer = '';

		$object_id = $this->getVar('comment-object_id');
		$object_group = $this->getVar('comment-object_group');

		$lines = array();

		if ($this->getVar('comments-refresh', 1) == 1) 
		{
			$lines[] = '<a class="refresh" href="#" title="'.JText::_('REFRESH').'" onclick="jcomments.showPage('.$object_id.',\''. $object_group . '\',0);return false;">'.JText::_('REFRESH').'</a>';
		}

		if ($this->getVar('comments-rss', 1) == 1) 
		{
			$link = $this->getVar('rssurl');
			$lines[] = '<a class="rss" href="'.$link.'" target="_blank">'.JText::_('RSS').'</a>';
		}

		if ($this->getVar('comments-can-subscribe', 0) == 1) 
		{
			$isSubscribed = $this->getVar('comments-user-subscribed', 0);

			$text = $isSubscribed ? JText::_('Unsubscribe') : JText::_('Subscribe');
			$func = $isSubscribed ? 'unsubscribe' : 'subscribe';

			$lines[] = '<a id="comments-subscription" class="subscribe" href="#" title="' . $text . '" onclick="jcomments.' . $func . '('.$object_id.',\''. $object_group . '\');return false;">'. $text .'</a>';
		}

		if (count($lines)) 
		{
			$footer = implode('<br />', $lines);			
		}

		return $footer;
	}
}

?>
<?php
// ensure this file is being included by a parent file
(defined('_VALID_MOS') OR defined('_JEXEC')) or die('Direct Access to this location is not allowed.');
/*
*
* Comment item template. Repesents one comment item. Results of rendering used in tpl_list.php
* 
* Style for JComments component created by GavickPro
*
*/
class jtt_tpl_comment extends JoomlaTuneTemplate
{
	function render()
	{
		$comment = $this->getVar('comment');

		if (isset($comment)) 
		{
			if ($this->getVar('get_comment_vote', 0) == 1) 
			{
				// return comment vote
			 	$this->getCommentVoteValue( $comment );
			} 
			else if ($this->getVar('get_comment_body', 0) == 1) 
			{
				// return only comment body (for example after quick edit)
				echo $comment->comment;
			} 
			else 
			{
				// return all comment item
				$comment_number = $this->getVar('comment-number', 1);
				//$thisurl = str_replace( 'amp;', '', $this->getVar( 'thisurl', '' ));
				$thisurl = $this->getVar('thisurl', '');

				$commentBoxIndentStyle = ($this->getVar('avatar') == 1) ? ' avatar-indent' : '';

				if ($this->getVar('avatar') == 1) 
				{
					echo '<div class="comment-avatar">'.$comment->avatar.'</div>';
				}

				echo '<div class="comment-box'.$commentBoxIndentStyle.'">';

				if ($this->getVar('comment-show-vote', 0) == 1) 
				{
					$this->getCommentVote( $comment );
				}

				if (($this->getVar('comment-show-title') > 0) && ($comment->title != '')) 
				{
					echo '<span class="comment-title">'.$comment->title.'</span> &mdash; ';
     			}
     			
				if ($this->getVar('comment-show-homepage') == 1) 
				{
					echo '<a class="author-homepage" href="'.$comment->homepage.'" rel="nofollow" title="'.$comment->author.'">'.$comment->author.'</a>';
				}
			 	else 
			 	{
					echo '<span class="comment-author">'.$comment->author.'</span>';
				}

				echo '<span class="comment-date">'.JCommentsText::formatDate($comment->datetime, JText::_('DATETIME_FORMAT')).'</span>';
				
				echo '<a class="comment-anchor" href="'.$thisurl.'#comment-'.$comment->id.'" id="comment-'.$comment->id.'">#'.$comment_number.'</a>';
				
				echo '<div class="comment-body" id="comment-body-'.$comment->id.'">'.$comment->comment.'</div>';
				
				if (($this->getVar('button-reply') == 1)
					|| ($this->getVar('button-quote') == 1)) 
				{
					echo '<span class="comments-buttons">';

					if ($this->getVar('button-reply') == 1) 
					{
						echo '<a href="#" onclick="jcomments.showReply('.$comment->id.'); return false;">'.JText::_('Reply').'</a>';
					
						if ($this->getVar('button-quote') == 1) 
						{					
 							echo ' | <a href="#" onclick="jcomments.showReply('.$comment->id.',1); return false;">'.JText::_('Reply with quote').'</a> | ';
						}
					}
					
					if ($this->getVar('button-quote') == 1) 
					{
						echo '<a href="#" onclick="jcomments.quoteComment('.$comment->id.'); return false;">'.JText::_('Quote').'</a>';
					}

					echo '</span>';
     			}

				echo '</div><div class="clear"></div>';
				// show frontend moderation panel
				$this->getCommentAdministratorPanel( $comment );
			}
		}
	}

	/*
	*
	* Displays comment's administration panel
	*
	*/
	function getCommentAdministratorPanel( &$comment )
	{
		if ($this->getVar('comments-panel-visible', 0) == 1) 
		{
			$imagesPath = $this->getVar('siteurl') . '/components/com_jcomments/tpl/'.$this->getVar('template').'/images';
			
			echo '<p class="toolbar" id="comment-toolbar-'.$comment->id.'">';
			
			if ($this->getVar('button-edit') == 1) 
			{
				$text = JText::_('EDIT');
				echo '<a class="edit" href="#" onclick="jcomments.editComment('.$comment->id.');return false;">'.$text.'</a> ';
			}

			if ($this->getVar('button-delete') == 1) 
			{
				$text = JText::_('DELETE');
				echo '<a class="delete" href="#" onclick="if (confirm(\''.JText::_('CONFIRM_DELETE').'\')){jcomments.deleteComment('.$comment->id.');}return false;">'.$text.'</a> ';
			}

			if ($this->getVar('button-publish') == 1) 
			{
				$text = $comment->published ? JText::_('UNPUBLISH') : JText::_('PUBLISH');
				$el_class = $comment->published ? 'unpublish' : 'publish';
				echo '<a class="'.$el_class.'" href="#" onclick="jcomments.publishComment('.$comment->id.');return false;">'.$text.'</a> ';
			}

			if ($this->getVar('button-ip') == 1)
		 	{
				$text = JText::_('IP') . ' ( ' . $comment->ip . ' ) ';
				echo '<a class="ip" href="#" onclick="jcomments.go(\'http://www.ripe.net/perl/whois?searchtext='.$comment->ip.'\');return false;">'.$text.'</a> ';
			}
			
			echo '</p>';
			echo '<div class="clear"></div>';
        }
	}

	function getCommentVote( &$comment )
	{
		$value = intval($comment->isgood) - intval($comment->ispoor);

		if ($value == 0 && $this->getVar('button-vote', 0) == 0) 
		{
			return;
		}
		
		echo '<span class="comments-vote">';
		echo '<span id="comment-vote-holder-'.$comment->id.'">';

		if ($this->getVar('button-vote', 0) == 1) 
		{
			echo '<a href="#" class="vote-good" title="'.JText::_('VOTE_GOOD').'" onclick="jcomments.voteComment('.$comment->id.', 1);return false;"></a><a href="#" class="vote-poor" title="'.JText::_('VOTE_POOR').'" onclick="jcomments.voteComment('.$comment->id.', -1);return false;"></a>';
		}
		
		echo $this->getCommentVoteValue( $comment );
		echo '</span>';
		echo '</span>';
	}

	function getCommentVoteValue( &$comment )
	{
		$value = intval($comment->isgood - $comment->ispoor);

		if ($value == 0 && $this->getVar('button-vote', 0) == 0 && $this->getVar('get_comment_vote', 0) == 0) 
		{
			// if current value is 0 and user has no rights to vote - hide 0
			return;
		}

		if ($value < 0) 
		{
			$class = 'poor';
		} 
		else if ($value > 0) 
		{
			$class = 'good';
			$value = '+' . $value;
		} 
		else 
		{
			$class = 'none';
		}
		
		echo '<span class="vote-'.$class.'">'.$value.'</span>';
	}
}

?>
<?php

defined('_JEXEC') or die;

$lang = JFactory::getLanguage(); ?>
<nav>
	AAA
<ul class="pagination pager pagenav">
<?php if ($row->prev) :
	$direction = $lang->isRtl() ? 'right' : 'left'; ?>
	<li class="previous">
		<a href="<?php echo $row->prev; ?>" rel="prev">
			<?php echo '<span class="icon-chevron-' . $direction . '"></span> ' . $row->prev_label; ?>
		</a>
	</li>
<?php endif; ?>
<?php if ($row->next) :
	$direction = $lang->isRtl() ? 'left' : 'right'; ?>
	<li class="next">
		<a href="<?php echo $row->next; ?>" rel="next">
			<?php echo $row->next_label . ' <span class="icon-chevron-' . $direction . '"></span>'; ?>
		</a>
	</li>
<?php endif; ?>
</ul>
</nav>

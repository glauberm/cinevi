<?php

defined('_JEXEC') or die;

?>

<ul class="category-module<?php echo $moduleclass_sfx; ?> list-inline">
	<?php foreach ($list as $item) : ?>
		<li>
			<?php var_dump("<pre>", $item); ?>
	        <a href="<?php echo $item->alternative_readmore; ?>" target="_blank">
				<img class="media-object" src="<?php echo $item->images; ?>" alt="<?php echo $item->title; ?>" />
			</a>
		</li>
	<?php endforeach; ?>
</ul>

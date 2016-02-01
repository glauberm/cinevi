<?php

defined('_JEXEC') or die;

?>

<ul class="category-module<?php echo $moduleclass_sfx; ?> list-unstyled">
	<?php foreach ($list as $item) : ?>
		<li>
            <div class="media">
                <div class="media-left">
                    <a href="images/docs/monografia/MODELO_AUTORIZACAO_FILMAGEM_IACS.pdf" target="_blank"><img class="media-object" src="images/icones/pdf.png" alt="PDF" /></a>
                </div>
                <div class="media-body">
                    <p id="media-heading" class="media-heading"><a href="<?php echo $item->displayIntrotext; ?>" target="_blank"><?php echo $item->title; ?></a>
                    </p>
                </div>
            </div>
		</li>
	<?php endforeach; ?>
</ul>

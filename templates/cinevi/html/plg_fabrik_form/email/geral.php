<?php

defined('_JEXEC') or die('Restricted access');

?>
<table border="1" style="border-collapse: collapse; width: 100%; border: 1px solid #045771; font-size: 14px;">
<?php
foreach ($this->data as $key => $val)
{
?>
	<tr>
		<td style="padding: 4px; font-weight: bold;"><?php echo $key; ?></td>
    </tr>
    <tr>
		<td style="padding: 4px 4px 8px;">{cine7_projetos___id}</td>
		<?php
		if (is_array($val)) :
			foreach ($val as $v):
				if (is_array($v)) :
					echo implode(", ", $v);
				else:
					echo implode(", ", $val);
				endif;
			endforeach;
		else:
			echo $val;
		endif;
		?>
		</td>
	</tr>
<?php
}
?>
</table>

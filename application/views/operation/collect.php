<html>
	<h>TEST</h>
	<?php echo validation_errors(); ?>
	<?php echo form_open('operation/collect'); ?>
	<table>
		<tr>
			<td>自身位置</td>
			<td><input type="text" name="selflocation"></td>
		</tr>
		<tr>
			<td>目标ID</td>
			<td><input type="text" name="targetId"></td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="collect.."></td>
		</tr>
	</table>
</html>
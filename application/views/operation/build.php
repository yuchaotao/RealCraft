<html>
	<h>TEST</h>
	<?php echo validation_errors(); ?>
	<?php echo form_open('operation/build'); ?>
	<table>
		<tr>
			<td>自身位置</td>
			<td><input type="text" name="selflocation"></td>
		</tr>
		<tr>
			<td>目标位置</td>
			<td><input type="text" name="targetlocation"></td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="Build"></td>
		</tr>
	</table>
</html>
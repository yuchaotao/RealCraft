<html>
	<?php echo validation_errors(); ?>
	<?php echo form_open('account/register'); ?>
	<table>
		<tr>
			<td>用户名</td>
			<td><input type="text" name="username"></td>
		</tr>
		<tr>
			<td>密码</td>
			<td><input type="password" name="password"></td>
		</tr>
		<tr>
			<td>邮箱</td>
			<td><input type="email" name="email"></td>
		</tr>
		<tr>
			<td><input type="submit" name="submit" value="register"></td>
		</tr>
	</table>
</html>
		<table cellpadding="5" cellspacing="4" border="0" align="center" class="maintable">
			<tr>
				<td class="maintitle_apply_left">
					<b><img src="images/title.gif" alt="" />&nbsp;{$lng['admin']['loggersettings']}</b>
				</td>
				<td class="maintitle_apply_right" nowrap="nowrap"><a href="$filename?page=settings&amp;s=$s">{$lng['panel']['backtooverview']}</a>
				</td>
			</tr>
			<tr>
				<td class="main_field_name"><b>{$lng['serversettings']['logger']['enable']}:</b></td>
				<td class="main_field_display" nowrap="nowrap">$loggingenabled</td>
			</tr>
			<tr>
				<td class="main_field_name"><b>{$lng['serversettings']['logger']['severity']}:</b></td>
				<td class="main_field_display" nowrap="nowrap"><select class="dropdown_noborder" name="logger_severity">$loggingseverity</select></td>
			</tr>
			<tr>
				<td class="main_field_name"><b>{$lng['serversettings']['logger']['types']}:</b><br />{$lng['serversettings']['logger']['types_desc']}</td>
				<td class="main_field_display" nowrap="nowrap"><input type="text" class="text" name="logger_logtypes" value="{$settings['logger']['logtypes']}" /></td>
			</tr>
			<tr>
				<td class="main_field_name"><b>{$lng['serversettings']['logger']['logfile']}:</b></td>
				<td class="main_field_display" nowrap="nowrap"><input type="text" class="text" name="logger_logfile" value="{$settings['logger']['logfile']}" /></td>
			</tr>
			<tr>
				<td class="main_field_name"><b>{$lng['serversettings']['logger']['logcron']}:</b></td>
				<td class="main_field_display" nowrap="nowrap">$logginglogcron</td>
			</tr>
			<tr>
				<td class="maintitle_apply_right" nowrap="nowrap" colspan="2">
					<if $_part == 'logging'>
						<input type="hidden" name="part" value="logging" />
					</if>
					<input class="bottom" type="reset" value="{$lng['panel']['reset']}" /><input class="bottom" type="submit" value="{$lng['panel']['save']}" />
				</td>
			</tr>
		</table>
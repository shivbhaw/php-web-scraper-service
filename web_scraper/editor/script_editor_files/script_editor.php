		<center>
		<input type="text" value="" id="script_editor_script_id" hidden="true" />
		<form action="script_editor_deployer.php" id="php_script_form" method="post"><!-- onSubmit="return fixPOSTphpscript()">-->
			<table border="1" width="90%">
				<tr>
					<td class="script_info">
						<label>Name: </label>
					</td>
					<td>
						<input class="input_text" id="script_editor_name_textbox"  name="script_editor_name" type="text" disabled />
					</td>
				</tr>
				<tr>
					<td class="script_info">
						<label>Description: </label>
					</td>
					<td>
						<input class="input_text" id="script_editor_description_textbox" name="script_editor_description" type="text" disabled />
					</td>
				</tr>
				<tr>
					<td class="script_info">
						<label>Test URLs: </label>
					</td>
					<td>
						<!-- add lined class using jquery lined function -->
						<textarea id="script_editor_url_textarea" rows="3" style="width:100%;resize:none;" form="php_script_form" placeholder="Place Dataset Url Here, if multiple place each on a seperate line"></textarea>
						<input hidden="true" class="input_text" id="script_editor_url_textbox" name="script_editor_url" type="text" /><br>
					</td>
				</tr>
			</table>
			<input hidden="true" type="text" name="script_editor_script" id="script_editor_script_php">
			<input id="php_script_submit" type="submit" hidden="true">
		</form>
		</center>

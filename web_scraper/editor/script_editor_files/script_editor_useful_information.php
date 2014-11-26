<script>
function ShowHide()
{
	var hiddenDiv = document.getElementById("HiddenDiv");
	console.log(hiddenDiv.hidden);
	if(hiddenDiv.hidden === undefined || hiddenDiv.hidden === false)
	{
		hiddenDiv.hidden = true;
	}
	else
	{
		hiddenDiv.hidden = false;
	}
}

$.getJSON('editor/script_editor_files/variables.json', function(data){
	var row_count = 3;
	var html = "<tr>";
	for(var i = 0; i < data.length; i++ )
	{
		html += "<td>"+data[i]['name']+"</td><td>"+data[i]['type']+"</td>";
		if(i%row_count == row_count -1 && i != data.length-1 && i != 0)
		{
			html += "</tr><tr>"
		}
	}
	html += "</tr>"
	$('#app_variables_helper').html(html);
});

</script>
<style>
.floating_info {
margin-bottom:1em;
}
</style>
<div class="floating_info">
<a id="useful_information_script_editor_url" onclick="javascript:ShowHide()">Useful Information (click to Show/Hide)</a>
<div class="mid" id="HiddenDiv"  hidden="true"><!-- style="display: none;">-->
	<center>
	<div class="function_div">
		<br>
		<b>Useful Functions:</b>
		<table class="helper_tables" border="1">
			<tr>
				<td>
					<i>$url:</i>Url Name
				</td><td>
					<i>$url_value:</i>Website Data (string)
				</td></tr>
				<tr><td>
					<i>$url_header:</i>Website Header Data (array)
				</td><td>
					<i>$links:</i>Array of Url's (array)
				</td></tr>
				<tr><td>
					to not add sub_url to returned value, change $linkscount to -1
				</td><td>
					<i>$:</i>...
				</td>
			</tr>
		</table>
		<br>
		<br>
		<?= file_get_contents('editor/extra_editor_information.html') ?>
		<br>
		<br>
		<table class="helper_tables"border="1">
			<tr>
				<td>
					<p>
						&#149; Found below are the variables which are to be filled in by you, especially ones marked with **.<br>
						&#149; Above you will find many values and default values of said variables.<br>
						&#149; And at the very top you will find some functions that will be very helpful to you.<br>
						&#149; These were generated whilst the initial creation of the data scraper scripts and were proven to be very helpful then.
					</p>
				</td>
			</tr>
		</table>

		<br>
		<p><b>Variables to fill in [type](ones with ** are required)</b><br></p>
		<table id="app_variables_helper" class="helper_tables" border="1">

		</table>
	</div>
	</center>
</div>
</div>

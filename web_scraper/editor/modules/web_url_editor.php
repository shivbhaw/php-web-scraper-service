<div id="url-editor">
	<center>
	<div id="urleditor_list">
		<div>
		<button class="new_button" id="url_editor_new_button">New</button>
		</div>
		<table class="table_style" id="table_of_urls" border="1" width ="80%">
			<!-- New Table Urls will be added here-->
		</table>
		<br>
		<button class="action_buttons" id="url_editor_update_button">Update</button>
		<button class="action_buttons" id="url_editor_revert_button">Return all to Default</button>
	</div>

	<!-- Url Editor -->
	<div id="urleditor_editor" hidden>
		<br><br>
		<table width="80%" border="1">
			<tr>
				<td width="20%">
					Source Name
				</td>
				<td width="20%">
					URL
				</td>
				<td width="20%">
					Script Type
				</td>
				<td width="14%">
					Update When?
				</td>
				<td width="12%">
					Need MINK?
				</td>
				<td width="14%">
					Plan Removal?
				</td>
			</tr>
			<tr id="url_editor_edit_row">
				<td width="0%" hidden="true">
					<p id="urlinput_id"></p>
				</td>
				<td width="20%">
					<input style="width:100%" id="urlinput_name" type="text" value="" placeholder="Name of Source">
				</td>
				<td width="20%">
					<input style="width:100%" id="urlinput_url" type="text" value="" placeholder="URL of Source">
				</td>
				<td width="20%">
					<select id="urlinput_script_selector">
						<!-- Javascript will change this with selection of script names -->
					</select>
				</td>
				<td width="14%">
					<select id="urlinput_frequency">
						<option value="d">Daily</option>
						<option value="w">Weekly</option>
						<option value="m">Monthly</option>
						<option value="y">Yearly</option>
					</select>
				</td>
				<td width="14%">
					<center>
						<input id="urlinput_mink" type="checkbox" name="remove_traces" value="">
					</center>
				</td>
				<td width="12%">
					<center>
						<input id="urlinput_removal" type="checkbox" name="remove_traces" value="">
					</center>
				</td>
			</tr>
		</table>
		<br>
		<button class="action_buttons" id="urlinput_ok_button">Add</button>
		<button class="action_buttons" id="urlinput_cancel_button">Cancel</button>
	</div>
	</center>
</div>
<script>

	function updateURLEditor()
	{
		var request = $.ajax({
			url: "editor/php/database_accessor.php",
			type: "GET",
			dataType: "html",
			data: {"type": 'urls', 'values':localStorage.getItem('urls')},
			success: function(msg){
				//TODO SUCCESS FAILURE
			}
		});
	}

	function revertURLEditor(DontRebuildTable)
	{
		deactivatePage();
		$.getJSON( "app_config/urls.json", function( data ) {
			localStorage.setItem('urls',JSON.stringify(data));//JSON.stringify(fixDBArray(JSON.parse(msg))));
			if(!DontRebuildTable)
				fillURLTable();
				reactivatePage();
		});
	}

	function urlEditorOK()
	{
		var id = document.getElementById("urlinput_id").innerHTML;
		var name = document.getElementById("urlinput_name").value;
		var url = document.getElementById("urlinput_url").value;
		var schedule = document.getElementById("urlinput_frequency").value;
		var mink = document.getElementById("urlinput_mink").checked;
		var removal = document.getElementById("urlinput_removal").checked;
		var script_id = document.getElementById("urlinput_script_selector").value;
		var url_array = JSON.parse(localStorage.getItem('urls'));

		if(id == -1)
		{
			id = random_id();
			url_array.push({'datascraper_url_id':id, 'name':name, 'url':url,'scraper_script_id':script_id, 'schedule_time':schedule, 'remove_all': removal == true ? 1 : 0, 'mink_required': mink == true ? 1 : 0});
		}
		else
		{
			for(key in url_array)
			{
				if(url_array[key]['datascraper_url_id'] == id)
				{
					url_array[key]['name'] = name;
					url_array[key]['url'] = url;
					url_array[key]['scraper_script_id'] = script_id;
					url_array[key]['schedule_time'] = schedule;
					url_array[key]['remove_all'] = removal == true ? 1 : 0;
					url_array[key]['mink_required'] = mink == true ? 1 : 0;
					break;
				}
			}
		}
		localStorage.setItem('urls', JSON.stringify(url_array));
		fillURLTable();
		ShowHideDivs('urleditor_list','urleditor_editor');

	}

	function urlEditorCancel()
	{
		fillURLTable();
		ShowHideDivs('urleditor_list','urleditor_editor');
	}

	function editURLUrlEditor(id){
		var url_id = -1;
		var name = "";
		var url = "";
		var script_id = -1;
		var update_when = "d";
		var need_mink = 0;
		var plan_removal = 0;
		if(id != undefined)
		{
			var arr = JSON.parse(localStorage.getItem("urls"));
			for(key in arr)
			{
				if(arr[key]["datascraper_url_id"] === id.toString())
				{
					url_id = arr[key]["datascraper_url_id"];
					name = arr[key]["name"];
					url = arr[key]["url"];
					script_id = arr[key]["scraper_script_id"];
					update_when = arr[key]["schedule_time"];
					need_mink = parseInt(arr[key]['mink_required']);
					plan_removal = parseInt(arr[key]["remove_all"]);
					break;
				}
			}
			document.getElementById("urlinput_ok_button").innerHTML = "Update";
		}
		else
		{
			document.getElementById("urlinput_ok_button").innerHTML = "Add";
		}
		document.getElementById("urlinput_id").innerHTML = url_id;
		document.getElementById("urlinput_name").value = name;
		document.getElementById("urlinput_url").value = url;
		document.getElementById("urlinput_frequency").value = update_when;
		document.getElementById("urlinput_mink").checked = need_mink;
		document.getElementById("urlinput_removal").checked = plan_removal;
		selectScriptNames = document.getElementById("urlinput_script_selector");
		selectScriptNames.innerHTML = createScriptOptions();
		selectScriptNames.value = script_id;
		ShowHideDivs('urleditor_editor','urleditor_list');
	}

	function removeURLUrlEditor(id)
	{
		if(confirm('Are you sure?'))
		{
			var arr = JSON.parse(localStorage.getItem("urls"));
			arr = jQuery.grep(arr, function(value) {
				return value["datascraper_url_id"] != id.toString();
			});
			localStorage.setItem("urls", JSON.stringify(arr));
		}
		fillURLTable();
	}

	function getScriptName(id)
	{
		var script_array = JSON.parse(localStorage.getItem("scripts"));
		for(key in script_array)
		{
			if(script_array[key]['scraper_script_id'] == id)
			{
				return script_array[key]['name'];
			}
		}
	}

	function fillURLTable(){
		var url_array = JSON.parse(localStorage.getItem("urls"));
		var html = "";

		if(url_array.length != 0)//allowTable)
		{
			html+= "<tr>";
			html+= '<td width="12%">Source Name</td>';
			html+= '<td width="16%">URL</td>';
			html+= '<td width="16%">Script Type</td>';
			html+= '<td width="12%">Update When?</td>';
			html+= '<td width="12%">Need MINK?</td>';
			html+= '<td width="12%">Plan Removal?</td>';
			html+= '<td width="10%"></td><td width="10%"></td>';
			html+= "</tr>";

			for(url_array_key in url_array)
			{
				html+= "<tr>";
				html+= '<td width="12%">'+url_array[url_array_key]["name"]+'</td>';
				html+= '<td width="16%">'+url_array[url_array_key]["url"]+'</td>';
				html+= '<td width="16%">'+getScriptName(url_array[url_array_key]["scraper_script_id"])+'</td>';
				html+= '<td width="12%">'+(url_array[url_array_key]["schedule_time"] == 'd' ? 'Daily' : url_array[url_array_key]["schedule_time"] == 'w' ? 'Weekly' : url_array[url_array_key]["schedule_time"] == 'm' ? 'Monthly' : url_array[url_array_key]["schedule_time"] == 'y' ? 'Yearly' :  'Unknown')+'</td>';
				html+= '<td width="12%">'+(url_array[url_array_key]["mink_required"] == 1 ? 'true' : 'false')+'</td>';
				html+= '<td width="12%">'+(url_array[url_array_key]["remove_all"] == 1 ? 'true' : 'false')+'</td>';


				html+= '<td width="10%"><button class="full_box_button" onclick="javascript:editURLUrlEditor(\''+url_array[url_array_key]["datascraper_url_id"]+'\');">Edit</a></td>';
				html+= '<td width="10%"><button class="full_box_button" onclick="javascript:removeURLUrlEditor(\''+url_array[url_array_key]["datascraper_url_id"]+'\');">Remove</a></td>';
				html+= '</tr>';
			}
		}
		else
		{
			html+= "<tr>";
			html+= 'NO URLS FOUND';
			html+= '</tr>';
		}
		//$("#title h1")[0].innerHTML = "Open Data Scraper - URLS";
		document.getElementById("table_of_urls").innerHTML = html;
	}

	function createScriptOptions()
	{
		var scripts = JSON.parse(localStorage.getItem("scripts"));
		var html = "";
		for(s_key in scripts)
		{
			if(scripts[s_key]["scraper_script_id"] != -1 && scripts[s_key]["scraper_script_id"] != -2)
				html+= '<option value="'+scripts[s_key]["scraper_script_id"]+'">'+scripts[s_key]["name"]+'</option>';
		}
		return '<option value="not_real_script_value">Unknown</option>'+html;
	}

	add_onload(function(){
		var tabs = document.getElementById("table-of-contents");
		tabs.innerHTML +=  "<li><a href=\'#url-editor\'>URL Editor</a>&nbsp;&nbsp;</li>";

		document.getElementById("url_editor_new_button").onclick = function() {
			editURLUrlEditor();
		}

		document.getElementById("url_editor_update_button").onclick = function() {
			updateURLEditor();
		}

		document.getElementById("url_editor_revert_button").onclick= function() {
			revertURLEditor();
		}

		document.getElementById("urlinput_ok_button").onclick = function(){
			urlEditorOK();
		}

		document.getElementById("urlinput_cancel_button").onclick = function(){
			urlEditorCancel();
		}
		revertURLEditor();
	});
</script>

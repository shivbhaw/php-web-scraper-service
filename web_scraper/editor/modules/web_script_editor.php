<!-- Ace Code Editor Library -->
<!--
<script src="editor/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script src="editor/js/ace/theme-twilight.js" type="text/javascript" charset="utf-8"></script>
<script src="editor/js/ace/mode-php.js" type="text/javascript" charset="utf-8"></script>
<script src="editor/js/ace/worker-php.js" type="text/javascript" charset="utf-8"></script>
<script src="editor/js/ace/ext-language_tools.js"></script>
-->
<script src="editor/js/ace/ace.js" type="text/javascript" charset="utf-8"></script>
<script>
var script_deployer_url = "editor/script_editor_files/script_editor_deployer.php";
</script>

<!-- Script Editor Methods (custom) -->
<!--<script src="editor/web_script_functions.js"></script>-->
<div id="script-editor">
	<div id="script-editor-nonfullscreen">
		<center>
		<div id="scripteditor_list">
			<div>
			<button class="new_button" id="script_editor_new_button">New</button>
			</div>
			<table class="table_style" id="table_of_scripts" border="1" width ="80%">
				<!-- New Table of Scripts will be added here-->
			</table>
			<br>
			<button class="action_buttons" id="script_editor_update_button">Update</button>
			<button class="action_buttons" id="script_editor_revert_button">Return all to Default</button>
		</div>
		<!-- script Editor -->
		<div id="scripteditor_editor" hidden>
			<br>
			<table width="80%" border="1">
				<tr>
					<td width="20%">
						Source Name
					</td>
					<td width="40%">
						Description
					</td>
				</tr>
				<tr id="script_editor_edit_row">
					<td width="0%" hidden="true">
						<p id="scriptinput_id"></p>
					</td>
					<td width="20%">
						<input style="width:100%" id="scriptinput_name" type="text">
					</td>
					<td width="80%">
						<input style="width:100%" id="scriptinput_description" type="text">
					</td>
				</tr>
			</table>
			<table width="80%" border="1" style="margin-top:0.1em;">
			<tr>
				<td>
					Urls
				</td>
				<td>
					<textarea id="scripteditor_url_textarea" style="border: none; width: 100%; -webkit-box-sizing: border-box; -moz-box-sizing: border-box; box-sizing: border-box;"> </textarea><!--textarea style="width:100%; height:100%;" id="scripteditor_url_textarea"/ -->
				</td>
				</tr>
			</table>
			<br>
			<button class="action_buttons" id="scriptinput_ok_button">Add</button>
			<button class="action_buttons" id="scriptinput_cancel_button">Cancel</button>
		</div>
		</center>
		<div id="full_screen_script_editor" class="full_screen_script_editor" hidden>
			<?= file_get_contents('editor/script_editor_files/script_editor_useful_information.php') ?>
			<div id="scripteditor_hideshow_div" align="right" class="full_screen_script_editor_hide_button">
				<button id="scripteditor_hideshow">Hide Title</button>
			</div>
			<div class="ace_script_editor" id="php_script" name="php_script"></div><!--< %=dbRule.getValue("SourceCode")%>-->
			<center><button id="submitphpscriptbutton" style="width:100%; height:4em;" form="phpScriptForm">Submit ( if nothing, resubmit) </button></center>
			<iframe class="script_iframe" id="scriptDeploymentFrame"></iframe>
		</div>
	</div>
</div>
<script>

	function revertScriptEditor(DontRebuildTable)
	{
		deactivatePage();
		$.getJSON( "app_config/scripts.json", function( data ) {
			localStorage.setItem('scripts',JSON.stringify(data));
			if(!DontRebuildTable)
				fillScriptTable();
				reactivatePage();
			});

	}

	function updateScriptEditor()
	{
		deactivatePage();
		var request = $.ajax({
			url: "editor/database_accessor.php",
			type: "POST",
			proccessData: false, // this is true by default
			dataType: "html",//"application/json"
			data: {"type": 'scripts', 'values':localStorage.getItem('scripts')},
			success: function(msg){
				console.log(msg);
				reactivatePage();
			}
		});
	}

	function ScriptEditorOK()
	{
		var script_array = JSON.parse(localStorage.getItem('scripts'));
		var id = document.getElementById("scriptinput_id").innerHTML;
		var name = document.getElementById("scriptinput_name").value;
		var description = document.getElementById("scriptinput_description").value;
		var script = ace.edit("php_script").getValue();//.getSession().getValue();

		if(id == '')
		{
			id = random_id();
			script_array.push({'scraper_script_id':id, 'name':name, 'description':description,'script':script});
		}
		else
		{
			for(key in script_array)
			{
				if(script_array[key]['scraper_script_id'] == id)
				{
					script_array[key]['name'] = name;
					script_array[key]['description'] = description;
					script_array[key]['script'] = script;
					break;
				}
			}
		}
		localStorage.setItem('scripts', JSON.stringify(script_array));
		fillScriptTable();
		ShowHideDivs('scripteditor_list','scripteditor_editor');
		$('#full_screen_script_editor').hide();

	}

	function ScriptEditorCancel()
	{
		fillScriptTable();
		ShowHideDivs('scripteditor_list','scripteditor_editor');
		$('#full_screen_script_editor').hide();
	}

	function editScriptEditor(id){
		var script_id = null;
		var name = "";
		var description = "";
		var script = "<\?php\necho 'enter your script here';\n?>";

		if(id != undefined)
		{
			var arr = JSON.parse(localStorage.getItem("scripts"));
			for(key in arr)
			{
				if(arr[key]["scraper_script_id"] === id.toString())
				{
					var script_id = arr[key]["scraper_script_id"];
					var name = arr[key]["name"];;
					var description = arr[key]["description"];
					var script = arr[key]['script'];
					break;
				}
			}
			document.getElementById("scriptinput_ok_button").innerHTML = "Update";
		}
		else
		{
			document.getElementById("scriptinput_ok_button").innerHTML = "Add";
		}
		document.getElementById("scriptinput_id").innerHTML = script_id;
		document.getElementById("scriptinput_name").value = name;
		document.getElementById("scriptinput_description").value = description;
		ace.edit("php_script").setValue(script);

		if(id == '-1' || id == '-2')
		{
			document.getElementById("scriptinput_name").disabled = true;
			document.getElementById("scriptinput_description").disabled = true;
			document.getElementById("scripteditor_url_textarea").disabled = true;
			document.getElementById("submitphpscriptbutton").disabled = true;
		}
		else if(document.getElementById("scriptinput_name").disabled == true)
		{
			document.getElementById("scriptinput_name").disabled = false;
			document.getElementById("scriptinput_description").disabled = false;
			document.getElementById("scripteditor_url_textarea").disabled = false;
			document.getElementById("submitphpscriptbutton").disabled = false;
		}

		ShowHideDivs('scripteditor_editor','scripteditor_list');
		document.getElementById('scriptDeploymentFrame').src = script_deployer_url;
		$('#full_screen_script_editor').show();
	}

	function removeScriptEditor(id)
	{
		if(confirm('Are you sure?'))
		{
			var arr = JSON.parse(localStorage.getItem("scripts"));
			arr = jQuery.grep(arr, function(value) {
				return value["scraper_script_id"] != id.toString();
			});
			localStorage.setItem("scripts", JSON.stringify(arr));
		}
		fillScriptTable();
	}

	//ACE CODE EDITOR
		//START OF ACE EDITOR JAVASCRIPT REQUIRED METHODS
	ace.require("ace/ext/language_tools");
	var editor = ace.edit("php_script");
	editor.setTheme("ace/theme/textmate");
	editor.getSession().setMode("ace/mode/php");
	editor.setOptions({
	    enableBasicAutocompletion: true
	});
	editor.renderer.setShowPrintMargin(false);

	editor.commands.addCommand({
		name: 'saveFile',
		bindKey: {
		win: 'Ctrl-S',
		mac: 'Command-S',
		sender: 'editor|cli'
		},
		exec: function(env, args, request) {
			//$("#submitphpscriptbutton").click();
		}
	});

	//prevent backspace from taking you back a page
	$(document).unbind('keydown').bind('keydown', function (event) {
	    var doPrevent = false;
	    if (event.keyCode === 8) {
	        var d = event.srcElement || event.target;
	        if ((d.tagName.toUpperCase() === 'INPUT' && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'PASSWORD' || d.type.toUpperCase() === 'FILE' || d.type.toUpperCase() === 'EMAIL' ))
	             || d.tagName.toUpperCase() === 'TEXTAREA') {
	            doPrevent = d.readOnly || d.disabled;
	        }
	        else {
	            doPrevent = true;
	        }
	    }

	    if (doPrevent) {
	        event.preventDefault();
	    }
	});



	function fillScriptTable(){
		var script_array = JSON.parse(localStorage.getItem("scripts"));
		var html = "";

		if(script_array.length != 0)//allowTable)
		{
			html+= "<tr>";
			html+= '<td width="20%"><b>Source Name</b></td>';
			html+= '<td width="30%"><b>Description</b></td>';
			html+= '<td width="30%"><b>Script Excerpt</b></td>';
			html+= '<td width="10%"></td><td width="10%"></td>';
			html+= "</tr>";

			for(script_array_key in script_array)
			{
				html+= "<tr>";
				html+= '<td width="12%">'+script_array[script_array_key]["name"]+'</td>';
				html+= '<td width="16%">'+script_array[script_array_key]["description"].substr(0, 50)+'</td>';
				html+= '<td width="16%">'+script_array[script_array_key]["script"].substr(0, 50)+'</td>';

				html+= '<td width="10%"><button class="full_box_button" onclick="javascript:editScriptEditor(\''+script_array[script_array_key]["scraper_script_id"]+'\');">Edit</a></td>';
				if(script_array[script_array_key]["scraper_script_id"] != -1 && script_array[script_array_key]['scraper_script_id'] != -2)
					html+= '<td width="10%"><button class="full_box_button" onclick="javascript:removeScriptEditor(\''+script_array[script_array_key]["scraper_script_id"]+'\');">Remove</a></td>';
				else
					html+= '<td width="10%"></td>';
				html+= '</tr>';
			}
		}
		else
		{
			html+= "<tr>";
			html+= 'NO scriptS FOUND';
			html+= '</tr>';
		}
		//$("#title h1")[0].innerHTML = "Open Data Scraper - scriptS";
		document.getElementById("table_of_scripts").innerHTML = html;
	}

	add_onload(function(){
		var tabs = document.getElementById("table-of-contents");
		tabs.innerHTML +=  "<li><a href=\'#script-editor\'>Script Editor</a>&nbsp;&nbsp;</li>";

		document.getElementById("script_editor_new_button").onclick = function() {
			editScriptEditor();
		}

		document.getElementById("script_editor_update_button").onclick = function() {
			updateScriptEditor();
		}

		document.getElementById("script_editor_revert_button").onclick= function() {
			revertScriptEditor();
		}

		document.getElementById("scriptinput_ok_button").onclick = function(){
			ScriptEditorOK();
		}

		document.getElementById("scriptinput_cancel_button").onclick = function(){
			ScriptEditorCancel();
		}

		document.getElementById("scripteditor_hideshow").onclick = function(){
			if($("#scripteditor_hideshow").html() == "Hide Title") {
				javascript:ShowHideDivs('','title');
				javascript:ShowHideDivs('', 'script-editor-nonfullscreen');
				$("#scripteditor_hideshow").html("Show Title");
				document.getElementById('scripteditor_hideshow_div').style.top = "2";
				ShowHideDivs('', "table-of-contents");
			}
			else {
				javascript:ShowHideDivs('title','');
				javascript:ShowHideDivs('script-editor-nonfullscreen', '');
				$("#scripteditor_hideshow").html("Hide Title");
				document.getElementById('scripteditor_hideshow_div').style.top = "65";
				ShowHideDivs("table-of-contents", "");
			}
		};

		document.getElementById('submitphpscriptbutton').onclick = function(){
			document.getElementById('scriptDeploymentFrame').src = script_deployer_url;
			var iframe = $("#scriptDeploymentFrame").contents();
			iframe.find("#scriptoutput_url_textbox")[0].value = document.getElementById("scripteditor_url_textarea").value.replace("\n", "<br>");
			iframe.find("#scriptoutput_script_textbox")[0].value = ace.edit("php_script").getValue();
			iframe.find("#scriptoutput_script_submit").click();
		}

		revertScriptEditor();
	});
</script>

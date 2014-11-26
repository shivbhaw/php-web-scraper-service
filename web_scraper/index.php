	<!-- JS libraries -->
	<link rel="stylesheet" href="editor/css/style.css">

	<script src="editor/js/jquery/jquery-1.10.2.js"></script>
	<script src="editor/js/jquery/jquery-ui-1.10.4.custom.min.js"></script>
	<script src="editor/js/underscore-min.js"></script>

	<!-- event handler adder && library of needed functions -->
	<script src="editor/js/functions.js"></script>

	<script>
		localStorage.setItem("func_onload", "");
		localStorage.setItem("func_post_onload", "");

		function add_onload(post_onload, add_to_end, funcstring){
			if(funcstring == undefined)
			{
				if(add_to_end == undefined)
				{
					funcstring = post_onload;
				}
				else
				{
					funcstring = add_to_end;
				}
			}

			if(typeof funcstring != "string")
			{
				funcstring = funcstring.toString();
				funcstring = funcstring.slice(12, funcstring.length-1);
			}

			var var_name = "";
			if(post_onload == false)
			{
				var_name = "func_post_onload";
			}
			else
			{
				var_name = "func_onload";
			}
			old_func = localStorage.getItem(var_name);
			if(add_to_end == false)
			{
				localStorage.setItem(var_name, funcstring + old_func);
			}
			else
			{
				localStorage.setItem(var_name, old_func + funcstring);
			}
		}
		window.onload = function(){
			eval(localStorage.getItem('func_onload'));
			eval(localStorage.getItem('func_post_onload'));
		}
	</script>

	<!-- Header Title -->
	<center>
		<div id="title">
			<h1> Data Scraper Web Portal </h1>
		</div>
	</center>


	<div id="tableofcontents" style="width:100%">
		<center>
			<ul id="table-of-contents" class="menu-left">
			<!-- dynamically added tabs -->
			</ul>
		</center>
		<!-- import modules -->
		<?php

			$modules = scandir("editor/modules");
			foreach($modules as $module)
			{
				if($module != "." && $module != ".." && $module != '.DS_Store')
				{
					require_once("editor/modules/".$module);
				}
			}
		?>
	</div>
	<script>
		add_onload(false, function(){
			$( "#tableofcontents" ).tabs();
		});

	</script>

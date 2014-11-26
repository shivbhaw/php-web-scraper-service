
//CREATE RANDOM ARRAY OF CHARACTERS TO MAKE A TEMPORARY UNIQUE ID
function random_id() {
  return Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1)
  	+ Math.floor((1 + Math.random()) * 0x10000).toString(16).substring(1);
}

//HIDE OR SHOW HTML
function ShowHideDivs(divIdShow,divIdHide)
{
	if(divIdShow != '')
		document.getElementById(divIdShow).style.display = 'block';
	if(divIdHide != '')
		document.getElementById(divIdHide).style.display = 'none';
}

function deactivatePage()
{
	$('input[type="submit"], button').each(function(){
		if($(this)[0]["nodeName"] === "A")
		{
			$(this).click(function () {return false;});
			//$(this).bind('click', false);
		}
		else
		{
			$(this)[0].disabled = true;
		}
	});
}

function reactivatePage()
{
	$('input[type="submit"], button').each(function(){
		if($(this)[0]["nodeName"] === "A")
		{
			$(this).unbind('click');
		}
		else
		{
			$(this)[0].disabled = false;
		}
	});
}

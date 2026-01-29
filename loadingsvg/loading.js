


function openLoading()
{
	lnk = document.createElement('div');
	lnk.setAttribute('id', 'loadingBackground' );
	lnk.setAttribute('class', 'loadingBackground' );
	document.getElementsByTagName("body").item(0).appendChild(lnk);
	
	lnk = document.createElement('iframe');
	lnk.setAttribute('id', 'iframeLoading' );
	lnk.setAttribute('class', 'iframeLoading' );
	lnk.setAttribute('src',  '/rotinasweb/cadastroNovo/splash.gif' );
	lnk.setAttribute('marginheight', '0' );
	lnk.setAttribute('marginwidth', '0' );
	lnk.setAttribute('scrolling', '0' );
	lnk.setAttribute('frameborder', '0' );
	document.getElementById("loadingBackground").appendChild(lnk);
	
}

async function closeLoading(){
	var element =  document.getElementById('loadingBackground');
	if (typeof(element) != 'undefined' && element != null)
	{
		element.remove();
	}
}
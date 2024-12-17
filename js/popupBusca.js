$(document).ready(function(){  
	$("#resultdosNovos").scroll(function () { 
		$("#resultadoCabecalho").scrollLeft($("#resultdosNovos").scrollLeft());
	});
});

/*
$('#iframePopup').load(function() {
    openLoadingBusca();
});
*/

function closeLoadingBusca()
{
	document.getElementById("PopupBuscaLoading").remove();
	$("#iframePopup").css("display", "block");
}

function openLoadingBusca()
{
	lnk = document.createElement('iframe');
	lnk.setAttribute('id', 'PopupBuscaLoading' );
	lnk.setAttribute('class', 'PopupBuscaLoading' );
	lnk.setAttribute('src',  '/rotinasweb/cadastroNovo/loadingsvg/loading.html' );
	lnk.setAttribute('marginheight', '0' );
	lnk.setAttribute('marginwidth', '0' );
	lnk.setAttribute('scrolling', '0' );
	lnk.setAttribute('frameborder', '0' );
	lnk.setAttribute('style', 'display:block' );
	document.getElementById("popupBackground").appendChild(lnk);

	$("#iframePopup").css("display", "none");
}

function openPopupBusca(eNomTbl,eBncCod,eHtmCod,eBncDes,eHtmDes,eWheAdd)
{
	lnk = document.createElement('div');
	lnk.setAttribute('id', 'popupBackground' );
	lnk.setAttribute('class', 'popupBackground' );
	document.getElementsByTagName("body").item(0).appendChild(lnk);

	

	lnk = document.createElement('iframe');
	lnk.setAttribute('id', 'iframePopup' );
	lnk.setAttribute('name', 'iframePopup' );
	lnk.setAttribute('class', 'iframePopup' );
	lnk.setAttribute('style', 'display:none' );
	lnk.setAttribute('src', './popup_busca/popupBusca.php?eNomTbl='+eNomTbl +"&eBncCod=" + eBncCod + "&eHtmCod=" + eHtmCod +"&eBncDes=" + eBncDes + "&eHtmDes=" + eHtmDes+"&eWheAdd=" + eWheAdd );
	lnk.setAttribute('marginheight', '0' );
	lnk.setAttribute('marginwidth', '0' );
	lnk.setAttribute('scrolling', '0' );
	lnk.setAttribute('frameborder', '0' );
	document.getElementById("popupBackground").appendChild(lnk);

	openLoadingBusca();
}

function closePopupBusca(eCmpRtr){
	$("#"+eCmpRtr).focus();
	document.getElementById("popupBackground").remove();
}

function returnPopupBusca(eHtmCod,eValCod,eHtmDes,eValDes){
	$("#"+eHtmCod).focus();
	document.getElementById(eHtmDes).innerHTML = eValDes;
	document.getElementById(eHtmCod).value = eValCod;
	document.getElementById("popupBackground").remove();
}
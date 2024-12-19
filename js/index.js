var abas = ['resumo','cadastro','endereco','fisica','definicoes','cooperados','financeiro','fiscal','contatos','bens','dividas','pronaf','tecnica','animais'];

$(document).ready(function(){  
    $('.data').mask('00/00/0000');
    $('.hora').mask('00:00:00');
    $('.telefone_com_ddd').mask('(00) 00000-0000');
    $('.dinheiro').mask('#.##0,00', {reverse: true});
    $('.numero').mask('#.##0', {reverse: true});
    $('.cpf').mask('000.000.000-00', {reverse: true});
    $('.cnpj').mask('00.000.000/0000-00', {reverse: true});
});

function returnValues(aCodVal,aCgcCpf,aCodCli,aNomCli){
    $('#nCodVal').val(aCodVal).change();
    $('#nCgcCpf').val(aCgcCpf);
    $('#nCodCli').val(aCodCli);
    $('#aCodCli').val(aNomCli);
    caregarTodasTelas(aCodCli);
}

function mostraAba(aba){
    $("[id^=aba]").removeClass("abaSelecionada");
    $("[id^=tela]").removeClass("telaSelecionada");
    $("#aba"+aba).addClass("abaSelecionada");
    $("#tela"+aba).addClass("telaSelecionada");
}

function caregarTodasTelas(nCodCli){
    $( "#nCgcCpf" ).addClass( "disabled" );
    $( "#nCodCli" ).addClass( "disabled" );
    $( "#nCodVal" ).addClass( "disabled" );
    $( "#aCodCli" ).addClass( "disabled" );

    abas.forEach(function (item, indice, array) {
        caregarTelas(item,nCodCli);
    });
}

function limparTodasTelas(){
    abas.forEach(function (item, indice, array) {
        document.getElementById("iframe_"+item).innerHTML = "";
    });
    $( "#nCgcCpf" ).removeClass( "disabled" );
    $( "#nCodCli" ).removeClass( "disabled" );
    $( "#aCodCli" ).removeClass( "disabled" );
    $( "#BSalvar" ).addClass( "disabled" );
    $( "#BCancelar" ).addClass( "disabled" );
    $( "#BExcluir" ).addClass( "disabled" );
    $( "#nCodVal" ).removeClass( "disabled" );
    $( '#nCgcCpf' ).focus();
}

function caregarTelas(aNomTel,nCodCli){
    lnk = document.createElement('iframe');
	lnk.setAttribute('id', 'iframeLoading_'+aNomTel );
	lnk.setAttribute('class', 'iframeLoading' );
	lnk.setAttribute('src',  '/rotinasweb/cadastroNovo/loadingsvg/loading.html' );
	lnk.setAttribute('marginheight', '0' );
	lnk.setAttribute('marginwidth', '0' );
	lnk.setAttribute('scrolling', '0' );
	lnk.setAttribute('frameborder', '0' );
	document.getElementById("iframe_"+aNomTel).appendChild(lnk);

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const myArray = this.responseText;
      
            document.getElementById("iframe_"+aNomTel).innerHTML = myArray;
        }
    }
    xmlhttp.open("GET", "./load/"+aNomTel+".php?eCodCli="+nCodCli, true);
    xmlhttp.send();
}

function buscaCpf(nDocCli){
    
    var xmlhttp = new XMLHttpRequest();
    
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const myArray = this.responseText.split("||");
            $('#nCgcCpf').val(myArray[0]);
            $('#nCodCli').val(myArray[1]);
            $('#aNomCli').html(myArray[2]);
            $('#nCodCli').focus();
        }
    }
    
    xmlhttp.open("GET", "./consultas/buscaCpf.php?nDocCli="+nDocCli, true);
    xmlhttp.send();
    
}

function buscaCliente(nDocCli){
    if(nDocCli != "" && nDocCli != " " && nDocCli != undefined){
        if(document.getElementById("aNomCli").innerHTML != "CADASTRO NOVO"){
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    const myArray = this.responseText.split("||");
                    
                    if(myArray[0] == "0" ){
                        alert('Cliente não encontrado');
                    }
                    else { 
                        if(myArray[1] == undefined || myArray[1] == ""){
                            alert(myArray[0]);    
                        }   else {

                            $('#nCgcCpf').val(myArray[0]);
                            $('#nCodCli').val(myArray[1]);
                            $('#aNomCli').html(myArray[2]);

                            caregarTodasTelas(myArray[1]);
                        }
                    }
                }
            }
            xmlhttp.open("GET", "./consultas/buscaCliente.php?nCodCli="+nDocCli, true);
            xmlhttp.send();
        }
        
        $( "#BSalvar" ).removeClass( "disabled" );
        $( "#BCancelar" ).removeClass( "disabled" );
        $( "#BExcluir" ).removeClass( "disabled" );

        $( "#BSalvar" ).val( "Alterar" );
        if(document.getElementById("aNomCli").innerHTML == "CADASTRO NOVO"){
            $( "#nCgcCpf" ).addClass( "disabled" );
            $( "#nCodCli" ).addClass( "disabled" );
            $( "#nCodVal" ).addClass( "disabled" );
            $( "#aCodCli" ).addClass( "disabled" );
            $( "#BSalvar" ).val( "Inserir" );
        }

    } else {
        
    } 
}



function buscaDescricao(eNomTbl,eCmpFil,eValFil,eCmpRet,eWheAdd,eIdeRec){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            if(this.responseText == " "){
                alert("Registro "+eValFil+" naão foi localizado!");
                document.getElementById(eIdeRec).innerHTML = this.responseText;
            } else {
                document.getElementById(eIdeRec).innerHTML = this.responseText;
            }
        } 
    }
    xmlhttp.open("GET", "./consultas/buscaDescricao.php?eNomTbl="+eNomTbl+"&eCmpFil="+eCmpFil+"&eValFil="+eValFil+"&eCmpRet="+eCmpRet+"&eWheAdd="+eWheAdd, true);
    xmlhttp.send();
}

/*
function login(){
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            const myArray = document.getElementById("FormIndex").innerHTML + this.responseText;
            document.getElementById("FormIndex").innerHTML = myArray;
        }
    }
    xmlhttp.open("GET", "./login.php", true);
    xmlhttp.send();
}
*/

$(document).on('keypress', 'input,select', function (e) {
    if (e.which == 13) {
        e.preventDefault();
        /*
        var $next ="";
        nTabNex = this.tabIndex;
        do{
            nTabNex += 1;
            $next = $('[tabIndex=' + nTabNex + ']');
            console.log(nTabNex);
        } while (!$next.length && nTabNex <= 9999);

        if(!$next.length){
            $next = $('[tabIndex=1]');        
            $next.focus() .click();
        }
        */
    }
});





function scrollAnt($eIdeEle){
    var scroll = document.getElementById($eIdeEle);
    scroll.scrollLeft -= 20;
}

function scrollPrx($eIdeEle){
    var scroll = document.getElementById($eIdeEle);
    scroll.scrollLeft += 20;
}
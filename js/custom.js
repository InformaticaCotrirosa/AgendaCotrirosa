const cadform = document.getElementById("cad-usuario-form");
const msgAlertErroCad = document.getElementById("msgAlertErroCad");

cadform.addEventListener("submit",async (e) =>{
    e.preventDefault;
    const dadosForm = new FormData(cadform);
    const dados = await fetch("cadastrar.php",{
        method: "POST",
        body: dadosForm
    });

    const resposta = await dados.json();
    console.log(resposta);

    if(resposta['erro']){
        msgAlertErroCad.innerHTML =  resposta['msg']
    }
});
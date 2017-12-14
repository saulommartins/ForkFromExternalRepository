/**
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
/**
* Funções AJAX
* Data de Criação: 31/01/2006


* @author Analista: Fábio Bertoldi Rodrigues
* @author Desenvolvedor: Lucas Stephanou

$Revision: 28520 $
$Name$
$Author: leandro.zis $
$Date: 2008-03-12 13:36:17 -0300 (Qua, 12 Mar 2008) $

Casos de uso: uc-01.01.00
*/

//Fila de conexões - Variaveis globais
fila=[];
ifila=0;

//Mostra imagem de loading enquanto chama a pagina requisitada
function imagem(){
// Var objImg é uma flag que verifica a existência do objeto imagem do Ajax
// Caso essa variavel seja true ela passa a ser true
   var boImg   = new Boolean(false);
// Verifica a Existência do frame principal
    if(window.parent.frames["telaPrincipal"]){
// Verifica se a imagem existe no frame principal
      if(window.parent.frames["telaPrincipal"].document.getElementById("carregando")){
       var loading = window.parent.frames["telaPrincipal"].document.getElementById("carregando");
       boImg = true;
      }
// Verifica se a imagem existe no formulário ativo
    }else if(document.getElementById("carregando")){
       var loading = document.getElementById("carregando");
       boImg = true;
    }
// Verifica se a imagem já existe na tela
// Caso exista ela verifica seu display alterando para o seu aparecimento ou não.
    if(boImg==true){
      if(loading.style.display=="block"){
        loading.style.display="none";
      }else{
        loading.style.display="block";
      }
    }
}

function ajax(stPagina,stCtrl,stId){
    /* Cria novo objeto Http Request */
    xmlhttp = new XMLHttpRequest();

    //Mostra imagem de loading enquanto chama a pagina requisitada
    imagem();
   
    //Abre a url
    stCtrl = "&stCtrl="+stCtrl;
//    stParametros = "<?=$sessao->id?>"+stCtrl;
    stLink = stPagina+stCtrl;

    xmlhttp.open("GET", stLink ,true);
    xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; charset=utf-8");

    //Executada quando o navegador obtiver o código
    xmlhttp.onreadystatechange=function() {

        if (xmlhttp.readyState==4){

            //Lê o texto
            var texto=xmlhttp.responseText

            //Exibe o texto no div conteúdo
            var conteudo=window.parent.frames["telaPrincipal"].document.getElementById(stId);
            conteudo.innerHTML=texto;
            imagem();
        }
    }
    xmlhttp.send(null)
}

function executaJavaScript( stJs ) {
    imagem();
    var d =(window.parent.frames["telaPrincipal"])?window.parent.frames["telaPrincipal"].document:document;
    var f =(window.parent.frames["telaPrincipal"])?window.parent.frames["telaPrincipal"].document.frm:document.frm;
    try {
        eval( stJs );
    } catch( err ) {
      if(window.parent.frames["telaPrincipal"]){
        window.parent.frames["telaPrincipal"].document.getElementById("spnErro").innerHTML = "<p align='center'><br><big><b>Erro:</b><br> "+err.name+": "+err.message+"</big><pre><p align='left'><b>Erro ao executar o seguinte código:</b><br><br>"+stJs.htmlEntities()+"<br></pre><pre><p align='left'><b>Exindo página gerada pelo oculto:</b><br><br>"+stJs+"<br></pre><p align='center'><big><b>Fim do Erro</b><big>";
        window.parent.frames["telaPrincipal"].document.getElementById("spnErro").style.display = 'block';
	eval( stJs );
      }
    }finally{
       imagem();
    }
}

function ajaxJavaScript( stPagina, stCtrl, parametrosGET ) {
    stCtrl = "&stCtrl="+stCtrl;
    stLink = stPagina+stCtrl;
    if (typeof parametrosGET != "undefined") {
        stLink += parametrosGET;
    }


    //Adiciona à fila
    fila[fila.length]=stLink;

    //Se não há conexões pendentes, executa
    if( (ifila+1) == fila.length ) {
        ajaxJavaScriptRun();
    }
}

function ajaxJavaScriptPOST( stPagina, parametrosPOST, stCtrl ) {
    stCtrl = "&stCtrl="+stCtrl;
    stLink = stPagina+stCtrl;

    //Adiciona à fila
    fila[fila.length]=stLink;

    //Se não há conexões pendentes, executa
    if( (ifila+1) == fila.length ) {
        ajaxJavaScriptRunPOST(parametrosPOST);
    }
}

//Executa a próxima conexão da fila
function ajaxJavaScriptRun(){
    imagem();

    xmlhttp = new XMLHttpRequest();

    //Abre a conexão
    xmlhttp.open("GET",fila[ifila],true);

    //Função para tratamento do retorno
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4){
            //Lê o texto
            var texto=xmlhttp.responseText;

            //Executa o texto como um javaScript
            executaJavaScript( texto );

            //Roda o próximo
            ifila++;
            if( ifila < fila.length ) {
                ajaxJavaScriptRun();
            }
          imagem();
        }
    }
    //Executa
    xmlhttp.send(null);
}

//Executa a próxima conexão da fila
function ajaxJavaScriptRunPOST(stParametros){
    imagem();

    xmlhttp = new XMLHttpRequest();

    //Abre a conexão
    xmlhttp.open("POST",fila[ifila],true);

    //Função para tratamento do retorno
    xmlhttp.onreadystatechange=function() {
        if (xmlhttp.readyState==4){
            //Lê o texto
            var texto=xmlhttp.responseText;

            //Executa o texto como um javaScript
            executaJavaScript( texto );

            //Roda o próximo
            ifila++;
            if( ifila < fila.length ) {
                ajaxJavaScriptRun();
            }
          imagem();
        }
    }
    
    //Executa
    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xmlhttp.send(stParametros.substring(1));
}

//Verifica se existe algum processo em andamento, se não executa de forma sincrona.
function ajaxJavaScriptSincrono( stPagina, stCtrl, sessaoId ) {
    stCtrl = "&stCtrl="+stCtrl;
    stLink = stPagina+stCtrl;

    if( (ifila) == fila.length ) {
        xmlhttp = new XMLHttpRequest();
        imagem();

        //Abre a conexão
        xmlhttp.open("GET",stLink,false);

        fila[fila.length]=stLink; //meu

        //Executa
        xmlhttp.send(null);

        //Recebe
        var texto=xmlhttp.responseText;

        //Executa o texto como um javaScript

        ifila++; // meu
        executaJavaScript( texto );

        //Retira a imagem loading
        imagem();
    } else {
        alertaAviso('Aguarde todos os processos concluírem.','form','erro', sessaoId );
    }
}

//Verifica se existe algum processo em andamento, se não executa de forma sincrona.
function ajaxJavaScriptSincronoPOST( stPagina, stParametros, stCtrl, sessaoId ) {
    stCtrl = "&stCtrl="+stCtrl;
    stLink = stPagina+stCtrl;

    if( (ifila) == fila.length ) {
        xmlhttp = new XMLHttpRequest();
        imagem();

        //Abre a conexão
        xmlhttp.open("POST",stLink,false);

        fila[fila.length]=stLink; //meu

        //Executa
	    xmlhttp.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xmlhttp.send(stParametros.substring(1));

        //Recebe
        var texto=xmlhttp.responseText;

        //Executa o texto como um javaScript

        ifila++; // meu
        executaJavaScript( texto );

        //Retira a imagem loading
        imagem();
    } else {
        alertaAviso('Aguarde todos os processos concluírem.','form','erro', sessaoId );
    }
}

function ajaxJavaScriptSincronoRH( stPagina, stCtrl, inId, sessaoId ) {
    stCtrl = "&stCtrl="+stCtrl+"&inId="+inId;
    ajaxJavaScriptSincrono(stPagina,stCtrl,sessaoId);
}

//parametros padrão para funcionamento da função
//stPersistente=TPersistente&stIdCombo=stCodCombo&stCampoId=cod_registro&stCampoDesc=nom_registro
//$stPersistente  = $_GET['stPersistente'];->Nome da classe persistente, deve ter o prefixo com constante do caminho
//$stIdCombo      = $_GET['stIdCombo'];->Id do combo a ser preenchido
//$stCampoId      = $_GET['stCampoId'];->Campo do recordset que deve ser usado com valor do combo
//$stCampoDesc    = $_GET['stCampoDesc'];->Campo do recordset que deve ser usado na descrição do combo
//parametros de filtro
//&cod_registro=10&ano_exericicio=2006
//stCtrl= preencher/limpar
function preencheComboAjax(stPagina,stParametros,stCtrl ){
    stPagina += "&" + stParametros;
    ajaxJavaScript( stPagina, stCtrl )
}

<script type="text/javascript">
/*
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
</script>
<?
/**
    * Página de Formulario de Inclusao/Alteracao de Serviços

    * Data de Criação   : 13/04/2005


    * @author Fernando Zank Correa Evangelista

    * @ignore

	* $Id: JSManterNatureza.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    *Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.6  2007/02/27 14:18:39  rodrigo
Bug #8420#

Revision 1.4  2007/02/07 18:34:29  rodrigo
#8345#

Revision 1.3  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

//Definições das funções de formulário
function buscaValor(tipoBusca){
    var stTmpAction = document.frm.action;
    var stTmpTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
   // document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stTmpAction;
    document.frm.target = stTmpTarget;
}

//Função para Formatar campo(criar a máscara) ex FormataCampo(this,event,'###-#);
function FormataCampo(Campo,teclapres,mascara){
    //pegando o tamanho do texto da caixa de texto com delay de -1 no event
    //ou seja o caractere que foi digitado não será contado.
    strtext = Campo.value
    tamtext = strtext.length
    //pegando o tamanho da mascara
    tammask = mascara.length
    //criando um array para guardar cada caractere da máscara
    arrmask = new Array(tammask)
    //jogando os caracteres para o vetor
    for (var i = 0 ; i < tammask; i++){
        arrmask[i] = mascara.slice(i,i+1)
    }
    
    if (((((arrmask[tamtext] == "#") || (arrmask[tamtext] == "9"))) || (((arrmask[tamtext+1] != "#") || (arrmask[tamtext+1] != "9"))))){
        if ((teclapres.keyCode >= 37 && teclapres.keyCode <= 40)||(teclapres.keyCode >= 48 && teclapres.keyCode <= 57)||(teclapres.keyCode >= 96 && teclapres.keyCode <= 105)||(teclapres.keyCode == 8)||(teclapres.keyCode == 9) ||(teclapres.keyCode == 46) ||(teclapres.keyCode == 13)){
            Organiza_Casa(Campo,arrmask[tamtext],teclapres.keyCode,strtext)
        }
        else{
            Detona_Event(Campo,strtext)
        }
    }
    else{
        if ((arrmask[tamtext] == "A"))    {
            charupper = event.valueOf()
            //charupper = charupper.toUpperCase()
            Detona_Event(Campo,strtext)
            masktext = strtext + charupper
            Campo.value = masktext
        }
    }
}
function Organiza_Casa(Campo,arrpos,teclapres_key,strtext){
    if (((arrpos == "/") || (arrpos == ".") || (arrpos == ",") || (arrpos == ":") || (arrpos == " ") || (arrpos == "-")) && !(teclapres_key == 8)){
        separador = arrpos
        masktext = strtext + separador
        Campo.value = masktext
    }
}
function Detona_Event(Campo,strtext){
    event.returnValue = false
    if (strtext != "") {
        Campo.value = strtext
    }
}

function verificaValor(){
   var vlr = new String(verificaValor.arguments[0].value);
   var cmp = new String(verificaValor.arguments[0].name);
   var obj = eval("document.forms[0]."+cmp);
   if((vlr=="0") || (vlr=="-0") || (vlr=="00")||(vlr=="000")||(vlr=="000-")||(vlr=="000-0")||(vlr=="0000")){
       obj.value = null;
       alertaAviso("@Código da Natureza inválido("+vlr+").","form","erro","<?=Sessao::getId()?>");    
   }
}

</SCRIPT>

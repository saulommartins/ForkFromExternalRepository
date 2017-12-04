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
<?php
/**
* Arquivo de instância para manutenção de documentos dinâmicos
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.03.99
*/
?>

<script type="text/javascript">

function setControleTextArea(controle){
    document.frm.stControleTextArea.value = controle.name;
}

function insertAtCursor(myField, myValue) {
  if (myField.selectionStart || myField.selectionStart == 0) {
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos) +" "+ myValue +" "+ myField.value.substring(endPos, myField.value.length);
   } 
   else {
   myField.value += myValue;
 }
}


function insereTags(myField1, myValue) {
var myField = eval('document.frm.'+ myField1);

if ((myField.selectionStart || myField.selectionStart == 0) && myValue != 0){
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos) +"<"+ myValue +">"+ myField.value.substring(startPos,endPos) +"</"+ myValue +">"+ myField.value.substring(endPos, myField.value.length);
   } 
   else {
   myField.value += myValue;
 }
}

function insereTagsBanco(myField1, myValue) {
var myField = eval('document.frm.'+ myField1);

if ((myField.selectionStart || myField.selectionStart == 0) && myValue != 0){
    var startPos = myField.selectionStart;
    var endPos = myField.selectionEnd;
    myField.value = myField.value.substring(0, startPos) +"["+ myValue +"]"+ myField.value.substring(endPos, myField.value.length);
   } 
   else {
   myField.value += myValue;
 }
}



function modificaDado(controle,indice){
   if(controle == 'incluiBloco'){
      document.frm.inBloco.value = parseInt(document.frm.inBloco.value)+1;
      document.frm.stInclui.value = indice;
      controle = 'mostraBloco';
   }
    document.frm.stCtrl.value = controle;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';

    document.frm.stInclui.value = "";
    
}

function modificaDado2(controle,indice){
  
  var aux;
   if (controle == 'removeBloco'){
      if(document.frm.inBloco.value > 1) {
         aux = controle;
         document.frm.stRemove.value = indice;
         controle = 'mostraBloco';
    }
   }
    
    document.frm.stCtrl.value = controle;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';

    if(aux == 'removeBloco'){
       document.frm.stRemove.value ="" ;
       document.frm.inBloco.value = parseInt(document.frm.inBloco.value)-1;
     } 
}




</script>

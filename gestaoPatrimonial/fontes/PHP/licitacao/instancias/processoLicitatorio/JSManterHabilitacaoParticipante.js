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
    * Arquivo JavaScript
    * Data de CriaÃ§Ã£o   : 20/11/2006


    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 19378 $
    $Name$
    $Autor:$
    $Date: 2007-01-16 15:43:48 -0200 (Ter, 16 Jan 2007) $

    * Casos de uso: uc-03.05.19

*/

/*
$Log$
Revision 1.2  2007/01/16 17:42:58  hboaventura
Bug #8076#

Revision 1.1  2006/11/20 17:45:04  fernando
função limpar


*/
?>
<script>
function Limpar(){
     executaFuncaoAjax('limpar');
}

function LimparAlterarDocumento(){
	document.getElementById('spnAlterarDocumentoParticipante').innerHTML = '';
}

function formataDiasValidosDocumento()
{
   montaParametrosGET('sincronizaDiasValidos');
}

function formataDataValidaDocumento()
{
   montaParametrosGET('sincronizaDataValida');
}

function validaData(obj){
   //var dtAtual = new Number(<?=date('Ymd')?>);
   var dtEmiss = document.getElementById('dt_emissao');

   arEmiss  = dtEmiss.value.split("/");
   inEmiss  = parseInt(arEmiss[2]+arEmiss[1]+arEmiss[0]);
   
   dtVal    = obj.value.split("/");
   dtValida = parseInt(dtVal[2]+dtVal[1]+dtVal[0]);

     if(dtValida < inEmiss){
          obj.value = "";
          document.getElementById('inNumDiasValido').value = "";
          document.getElementById('dt_emissao').focus();
          alertaAviso("@A Data de Validade deve ser maior ou igual a Data de Emissão.",'form','erro','<?=Sessao::getId()?>');

          return false;
     }
     return true;
}

function bloqueiaDesbloqueiaCampos(obj)
{
     if(obj.value == '') {
          jq('#dt_validade').val('');
          jq('#inNumDiasValido').val('');
          jq('#dt_validade').attr('disabled','disabled');
          jq('#inNumDiasValido').attr('disabled','disabled');
     } else {
          jq('#dt_validade').removeAttr('disabled');
          jq('#inNumDiasValido').removeAttr('disabled');
          jq('#inNumDiasValido').focus();
     }
}

</script>

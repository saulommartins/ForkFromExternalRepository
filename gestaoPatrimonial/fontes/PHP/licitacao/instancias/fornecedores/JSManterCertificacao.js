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
    * Pagina de JavaScript para Incluir Cadastro/Certificação
    * Data de Criação   : 02/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    $Id: JSManterCertificacao.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
    $Revision: 19180 $
    $Name$
    $Autor: $
    $Date: 2007-01-09 10:50:28 -0200 (Ter, 09 Jan 2007) $

    * Casos de uso: uc-03.05.13
*/

/*

$Log:

*/
?>

<script type="text/javascript">

function validaData(obj){
   var dtAtual = new Number(<?=date('Ymd')?>);
   dtVal    = obj.value.split("/");
   dtValida = parseInt(dtVal[2]+dtVal[1]+dtVal[0]);
     //if(dtValida < dtAtual){
     //    alertaAviso("@A Data de Validade deve ser maior ou igual a data atual.",'form','erro','<?=Sessao::getId()?>');
     //    obj.value = "";
     //    obj.focus();
     //     document.getElementById('inNumDiasValido').value = "";
     //    return false;
     //}
     return true;
}

function limpaFormularioDocumentoExtra(){
	executaFuncaoAjax('limpaDocumento');
}

function Limpar() {
	executaFuncaoAjax('montaAtributos');
	executaFuncaoAjax('abilitaCampos' );
}

function limpaFormularioDocumentoExtra()
{
   jq('#stDataValidade').attr('disabled','disabled');
   jq('#inNumDiasValido').attr('disabled','disabled');
}

function formataDiasValidosDocumento()
{
   montaParametrosGET('sincronizaDiasValidos');
}

function formataDataValidaDocumento()
{
   montaParametrosGET('sincronizaDataValida');
}

function bloqueiaDesbloqueiaCampos(obj)
{
    if(obj.value =='') {          
        jq('#stDataValidade').val('');
        jq('#inNumDiasValido').val('');
        jq('#stDataValidade').attr('disabled',true);
        jq('#inNumDiasValido').attr('disabled',true);

    } else {
        jq('#stDataValidade').attr('disabled',false);
        jq('#inNumDiasValido').attr('disabled',false);
        jq('#inNumDiasValido').focus();
    }
}

</script>

<?php
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
?>
<script>
IncluirFiscal = function () {

   if (document.frm.inFiscal.value!='') {
     montaParametrosGET( 'IncluirFiscal', '', true);
   } else {
          alertaAviso('Campo Fiscal inválido!()','form','erro','<?=Sessao::getId()?>');
   }
}
IncluirCredito = function () {

   if (document.frm.inCodCredito.value!='') {
     montaParametrosGET( 'IncluirCredito', '', true);
   } else {
          alertaAviso('Campo Crédito inválido!()','form','erro','<?=Sessao::getId()?>');
   }
}

LimparCredito = function () {
    montaParametrosGET('LimparGrupoCredito');
}

LimparGrupo = function () {
    montaParametrosGET('LimparGrupo');
}

IncluirGrupoCredito = function () {

   if (document.frm.inCodGrupo.value!='') {
     montaParametrosGET( 'IncluirGrupoCredito', '', true);
   } else {
          alertaAviso('Campo Grupo de Crédito inválido!()','form','erro','<?=Sessao::getId()?>');
   }
}
limparFiscal = function () {
    document.frm.inFiscal.value  = "";
    document.getElementById('stFiscal').innerHTML = "&nbsp";
}

limparFormEncerrar = function () {
    document.getElementById('dtEncerramento').value     = '';
    document.getElementById('stObeservacao').value      = '';
    document.getElementById('stCodDocumentoTxt').value  = '';
    document.getElementById('stCodDocumento').selectedIndex = 0;
}

function limparFormAlterar()
{
    window.location.reload();
}

Cancelar = function () {
    <?$stLink = "&pg=".$sessao->link["pg"]."&pos=".$sessao->link["pos"];?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId();?>";
    document.frm.submit();
}
MudaVinculo = function (event) {
    montaParametrosGET( 'mudarVinculo', '', true);
}

desabilitaForm = function (stIdForm) {

    var form = window.document.getElementById(stIdForm);
    var elementos = form.elements;
    var totalElementos = elementos.length;
    for (var k = 0; k < totalElementos; k++) {
        var elementoAtual = elementos[k];
        switch (elementoAtual.type) {
            case "button": case "submit": case "reset":
            break;
            default:
                elementoAtual.disabled = true;
            break;
        }
    }
    habilitarCampoFiscal();

    return false;
}

habilitarCampoFiscal = function () {

    var campoInFiscal = window.document.frm.inFiscal;
    campoInFiscal.disabled = false;
}

</script>

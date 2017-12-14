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
<?php
/**
    * Arquivo de Java Script do Inciar Processo Fiscal
    * Data de Criação: 25/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
?>
<script type="text/javascript">

incluirDescontos = function () {
    if (document.frm.stPrazoAntecipacao.value == '') {
        alertaAviso('Digite um Prazo de Antecipação!()','form','erro','<?=Sessao::getId()?>');
        document.frm.stPrazoAntecipacao.focus();

        return false;
    }

    if (document.frm.stValorDesconto.value == '') {
        alertaAviso('Digite um Valor de Desconto!()','form','erro','<?=Sessao::getId()?>');
        document.frm.stValorDesconto.focus();

        return false;
    }

    montaParametrosGET( 'montaIncluirDescontos', '', true);
    limparDescontos();
}

limparDescontos = function () {
    document.frm.stPrazoAntecipacao.value  = "";
    document.frm.stValorDesconto.value  = "";
}

limparFormulario = function () {
    if (document.frm.inCodTipoPenalidade) {
        document.frm.inCodTipoPenalidade.value = '<?= $_REQUEST['inCodTipoPenalidade'] ?>';
    }

    if (document.frm.inCodSelecTipoPenalidade) {
        document.frm.inCodSelecTipoPenalidade.value = '<?= $_REQUEST['inCodTipoPenalidade'] ?>';
    }

    if (document.frm.stNomPenalidade) {
        document.frm.stNomPenalidade.value     = '<?= $_REQUEST['stNomPenalidade'] ?>';
    }

    if (document.getElementById('stNorma')) {
        document.getElementById('stNorma').innerHTML = '&nbsp;';
    }

    if (document.frm.inCodNorma) {
        document.frm.inCodNorma.value          = '<?= $_REQUEST['inCodNorma'] ?>';
    }

    if (document.frm.inCodIndicador) {
        document.frm.inCodIndicador.value      = '<?= $_REQUEST['inCodIndicador'] ?>';
    }

    if (document.frm.inCodFuncao) {
        document.frm.inCodFuncao.value         = '<?= $_REQUEST['inCodFuncao'] ?>';
    }

    if (document.frm.inCodUnidade) {
        document.frm.inCodUnidade.value        = '<?= $_REQUEST['inCodUnidade'] ?>';
    }

    if (document.frm.stCodDocumentoTxt) {
        document.frm.stCodDocumentoTxt.value   = '';
    }

    if (document.frm.stCodDocumento) {
        document.frm.stCodDocumento.value      = '';
    }

    document.frm.boConceder[0].checked     = '<?= $_REQUEST['boConceder'] == 'f' ? false : true ?>';
    document.frm.boConceder[1].checked     = '<?= $_REQUEST['boConceder'] == 'f' ? true : false ?>';
    montaParametrosGET('montaFormulario');
}

</script>

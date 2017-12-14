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
 * Javascript que valida inclusão e alteração da Infração
 * Data de Criação: 04/08/2007

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 $Id: JSManterInfracao.php 64421 2016-02-19 12:14:17Z fabio $

 * Casos de uso:
 */
?>
<script type="text/javascript">

function incluirPenalidade()
{
    if (document.frm.inCodPenalidade.value != '') {
        montaParametrosGET('incluirPenalidade');
    } else {
        alertaAviso('Campo Penalidade inválido!', 'form', 'erro', '<?= Sessao::getId() ?>');
    }
}

function limparPenalidade()
{
    document.frm.inCodPenalidade.value = '';
    document.getElementById('stPenalidade').innerHTML = '&nbsp;';
}

function limparCampos()
{
    document.frm.inTipoFiscalizacao.value = '<?= $_REQUEST['inTipoFiscalizacao'] ?>';
    document.frm.cmbTipoFiscalizacao.value = '<?= $_REQUEST['inTipoFiscalizacao'] ?>';
    document.frm.stNomInfracao.value = '<?= $_REQUEST['stNomInfracao'] ?>';
    document.getElementById('stNorma').innerHTML = '&nbsp;';
    document.frm.inCodNorma.value = document.frm.inHdnCodNorma.value;
    document.frm.stCodDocumentoTxt.value = '<?= $_REQUEST['stCodDocumento'] ?>';
    document.frm.stCodDocumento.value = '<?= $_REQUEST['stCodDocumento'] ?>';
    document.frm.boCominar[0].checked = '<?= $_REQUEST['boCominar'] == 'f' ? false : true ?>';
    document.frm.boCominar[1].checked = '<?= $_REQUEST['boCominar'] == 'f' ? true : false ?>';
}

function limparFormulario()
{
    limparCampos();
    limparPenalidade();
    montaParametrosGET('carregarPenalidades');
}

</script>

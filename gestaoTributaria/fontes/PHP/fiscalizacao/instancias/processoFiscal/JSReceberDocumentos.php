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
    * Data de Criação: 15/08/2008

    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Zainer Cruz dos Santos Silva

    * @package URBEM
    * @subpackage

    * @ignore

    * Casos de uso:

    $Id:$
*/
?>
<script type="text/javascript">
incluirDocumento = function () {
    if (document.frm.txtDocumentos.value!='') {
        montaParametrosGET( 'incluirDocumento', '', true);
    } else {
        alertaAviso('Documento inválido!()','form','erro','<?=Sessao::getId()?>');
    }
}

limparDocumento = function () {
        document.frm.txtDocumentos.value  = "";
    document.frm.cmbDocumentos.value  = "";
}

function verificaHiddenForm()
{
    for (i = 0; i < document.frm.elements.length; i++) {
        if (BuscaValorSubString(document.frm.elements[i].name, "documento",0,9)) {
            return true;
        }
    }
    alertaAviso('Necessário incluir pelo menos um Documento!()','form','erro','<?=Sessao::getId()?>');

    return false;
}

function SalvarInicioProcesso()
{
    if (Valida()) {
        if (verificaHiddenForm()) {
             document.frm.submit();
        } else {
            LiberaFrames(true,true);
        }
    }
}

function BuscaValorString(alvo,valor)
{
    var re = new RegExp(valor);

    if (alvo.match(re)) {
            return true;
    } else {
        return false;
    }
}

function BuscaValorSubString(alvo,busca,ini,fim)
{
    if (alvo.substr(ini,fim) == busca) {
            return true;
    } else {
        return false;
    }
}
</script>

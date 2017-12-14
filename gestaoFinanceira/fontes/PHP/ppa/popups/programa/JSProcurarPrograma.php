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

function inserePrograma(inCodPrograma, stNomPrograma, inCodProgramaBanco)
{
    var campoCod   = document.frm.campoNum;
    var campoNom   = document.frm.campoNom;
    var stBusca    = document.frm.tipoBusca;
    var pageUpdate;

    window.opener.parent.frames['telaPrincipal'].document.getElementById(campoCod.value).value = inCodPrograma;
    window.opener.parent.frames['telaPrincipal'].document.getElementById(campoNom.value).innerHTML = stNomPrograma;

    window.opener.parent.frames['telaPrincipal'].document.getElementById(campoCod.value).focus();

    if (window.opener.parent.frames['telaPrincipal'].document.getElementsByName('hdnInCodPrograma').length > 0) {
        window.opener.parent.frames['telaPrincipal'].document.getElementsByName('hdnInCodPrograma')[0].value = inCodProgramaBanco;
    }
    window.close();
}

function novoPrograma(inCodFunc, noTitulo, inCodModulo, noModulo, inCodAcao)
{
    window.opener.parent.frames['telaMenu'].location = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/index/menu.php?<?=Sessao::getId();?>&nivel=3&cod_func_pass='+inCodFunc+'&cod_gestao_pass=2&stTitulo='+noTitulo+'&stNomeGestao=Financeira&modulos='+noModulo;
    window.opener.parent.frames['telaPrincipal'].location = '../../../../../../gestaoFinanceira/fontes/PHP/ppa/instancias/programas/FMManterPrograma.php?<?=Sessao::getId();?>&acao='+inCodAcao+'&stAcao=incluir&modulo='+inCodModulo+'&funcionalidade='+inCodFunc+'&nivel=1&cod_gestao_pass=2&stNomeGestao=Financeira&modulos='+noModulo;
    window.close();
}

</script>

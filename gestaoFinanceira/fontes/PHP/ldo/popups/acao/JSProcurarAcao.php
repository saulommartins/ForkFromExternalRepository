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
/**
 * Javascript do 02.10.03 - Manter Ação
 * Data de Criação: 16/02/2009
 * Copyright CNM - Confederação Nacional de Municípios
 *
 * @author Jânio Eduardo Vasconcellos de Magalhães <janio.magalhaes>
 * @package GF
 * @subpackage LDO
 * @uc 02.10.03 - Manter Ação
 */

function inserirAcao(inNumAcao, stDescAcao)
{
    var inTarget = document.frm.campoNum;
    var stTarget = document.frm.campoNom;
    var stBusca  = document.frm.tipoBusca;
    var stForm   = document.frm.nomForm.value;

    window.opener.parent.frames['telaPrincipal'].document.getElementById(inTarget.value).value = inNumAcao.replace(/^\s+|\s+$/g,"");
    window.opener.parent.frames['telaPrincipal'].document.getElementById(stTarget.value).innerHTML = stDescAcao;
    window.opener.parent.frames['telaPrincipal'].document.getElementById(inTarget.value).focus();

    sPag = '<?php echo CAM_GF_LDO_POPUPS;?>acao/OCProcurarAcao.php?<?php echo Sessao::getId();?>&stNomCampoCod=' + inTarget.value + '&stIdCampoDesc=' + stTarget.value + '&stNomForm=' + stForm +' &inNumAcao=' + inNumAcao + '&stCtrl=listaAcao&boExibePrograma=1&stScript=true&stCtrl=listaAcao';

    window.opener.parent.frames["oculto"].location.replace(sPag);

    window.close();
}

function construirHidden(stNome, stValor, stForm)
{
    if (!window.opener.parent.frames['telaPrincipal'].document.getElementById(stNome)) {
        var input = document.createElement('input');
            input.type  = 'hidden';
            input.name  = stNome;
            input.value = stValor;

        eval("window.opener.parent.frames['telaPrincipal'].document." + stForm).appendChild(input);
    }
}
</script>

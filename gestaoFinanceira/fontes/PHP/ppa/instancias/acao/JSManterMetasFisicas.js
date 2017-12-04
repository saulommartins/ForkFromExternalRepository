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
 * Javascript de Lançar Metas Fisicas Realizadas.
 * Data de Criacao: 15/04/2016

 * @author Analista : Valtair Santos
 * @author Desenvolvedor : Michel Teixeira
 * @ignore

 $Id: JSManterMetasFisicas.js 64971 2016-04-15 18:54:14Z michel $

**/
?>

<script type="text/javascript">

function buscaValor(stTipoBusca, stAux, stLinha)
{
    var stTarget = document.frm.target;
    var stAction = document.frm.action;

    document.frm.stCtrl.value = stTipoBusca;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'+stAux;
    document.frm.submit();

    document.frm.action = stAction;
    document.frm.target = stTarget;

    if(stLinha)
        TableTreeLineControl( stLinha , 'none', '', 'none');
}

</script>

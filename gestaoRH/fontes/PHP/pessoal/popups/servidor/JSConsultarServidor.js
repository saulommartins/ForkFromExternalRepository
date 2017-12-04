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
    * Página de javascript
    * Data de Criação   : 07/07/2016

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Michel teixeira

    * @ignore

    $Id: JSConsultarServidor.js 66023 2016-07-08 15:01:19Z michel $

*/

?>

<script type="text/javascript">

function buscaValor(tipoBusca,Aba){
    document.frm.stCtrl.value = tipoBusca;
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    switch( Aba ){
        case 1:
            document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>';
            break
        case 2:
            document.frm.action = '<?=$pgOculDocumentacao;?>?<?=Sessao::getId();?>';
            break
        case 3:
            document.frm.action = '<?=$pgOculContrato;?>?<?=Sessao::getId();?>';
            break
        case 4:
            document.frm.action = '<?=$pgOculPrevidencia;?>?<?=Sessao::getId();?>';
            break
        case 5:
            document.frm.action = '<?=$pgOculDependentes;?>?<?=Sessao::getId();?>';
            break
        case 6:
            document.frm.action = '<?=$pgOculAtributos;?>?<?=Sessao::getId();?>';
            break
        default:
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
            break
    }
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget
}

function alterarDado( stAcao, Aba, inLinha ){
    var stCtrlValor = document.frm.stCtrl.value;
    document.frm.stCtrl.value = stAcao;
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    switch( Aba ){
        case 0:
            document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 1:
            document.frm.action = '<?=$pgOculIdentificacao;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 2:
            document.frm.action = '<?=$pgOculDocumentacao;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 3:
            document.frm.action = '<?=$pgOculContrato;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 4:
            document.frm.action = '<?=$pgOculPrevidencia;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 5:
            document.frm.action = '<?=$pgOculDependentes;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
            break
        case 6:
            document.frm.action = '<?=$pgOculAtributos;?>?<?=Sessao::getId();?>&inLinha='+inLinha;
    }
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
    document.frm.stCtrl.value = stCtrlValor;
}

</script>

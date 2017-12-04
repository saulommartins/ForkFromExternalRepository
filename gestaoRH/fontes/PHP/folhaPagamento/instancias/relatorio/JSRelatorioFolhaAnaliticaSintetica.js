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
    * JavaScript de Relatório da Folha Analítica/Sintética
    * Data de Criação: 21/03/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: alex $
    $Date: 2008-02-14 12:54:21 -0200 (Qui, 14 Fev 2008) $

    * Casos de uso: uc-04.05.50
*/

/*
$Log$
Revision 1.2  2006/08/08 17:43:36  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
    stAction = document.frm.action; 
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOculF;?>?<?=Sessao::getId();?>'
    document.frm.submit();
    document.frm.action = stAction;
}

function excluirContrato( inId ){
    stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirContrato';
    document.frm.action = '<?=$pgOculF;?>?<?=Sessao::getId();?>?&inId='+inId;
    document.frm.submit();
    document.frm.action = stAction;
}

///////////// ORDENACAO

function trocaOrdenacao(labelIdFrom, labelIdTo){
    var objSpanFrom = document.getElementById("span" + labelIdFrom);
    var objSpanTo   = document.getElementById("span" + labelIdTo);
        
    if(objSpanTo && objSpanFrom){
        
        //Get the current envinroment
        var objComboFromSelectedIndex = document.getElementById("stAlfNum"+labelIdFrom).selectedIndex;
        var objComboToSelectedIndex   = document.getElementById("stAlfNum"+labelIdTo).selectedIndex;
        
        //Do replace
        var fatherTo   = objSpanTo.parentNode;
        var fatherFrom = objSpanFrom.parentNode;
        
        var objSpanFromClone = objSpanFrom.cloneNode(true);
        var objSpanToClone   = objSpanTo.cloneNode(true);
        
        objSpanFromClone.id = "span"+labelIdTo;
        objSpanToClone.id   = "span"+labelIdFrom;
        
        fatherFrom.replaceChild(objSpanToClone, objSpanFrom);
        fatherTo.replaceChild(objSpanFromClone, objSpanTo);
        
        //Put combo states
        var objComboFrom = document.getElementById("stAlfNum"+labelIdFrom);
        var objComboTo   = document.getElementById("stAlfNum"+labelIdTo);
        
        objComboFrom.id = "stAlfNum"+labelIdTo;
        objComboTo.id   = "stAlfNum"+labelIdFrom;
        
        objComboFrom.selectedIndex = objComboFromSelectedIndex;
        objComboTo.selectedIndex   = objComboToSelectedIndex;
    }
}

</script>

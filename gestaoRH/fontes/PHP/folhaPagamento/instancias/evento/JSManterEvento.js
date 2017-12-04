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
* Página de JavaScript
* Data de Criação   : 26/08/2005


* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Eduardo Antunez

* @ignore 

$Revision: 30566 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.05.06
*/

/*
$Log$
Revision 1.5  2006/08/08 17:42:41  vandre
Adicionada tag log.

*/
?>
<script type="text/javascript">

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function selecionaSubDivisao(stAba,boSelected) {
    if(document.frm.hdnNatureza.value != 'Base'){
        i = 0;
        while (eval('document.frm.inCodSubDivisaoSelecionados'+stAba+'[i]')) {
            eval('document.frm.inCodSubDivisaoSelecionados'+stAba+'[i].selected = '+boSelected+';');
            i++;        
        }
    }
}

function selecionaCargo(stAba,boSelected) {
    if(document.frm.hdnNatureza.value != 'Base'){
        i = 0;
        while (eval('document.frm.inCodCargoSelecionados'+stAba+'[i]')) {
            eval('document.frm.inCodCargoSelecionados'+stAba+'[i].selected = '+boSelected+';');
            i++;        
        }
    }
}

function processaCaso( stAcao , inId ){
    document.frm.stCtrl.value = stAcao;
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId='+inId;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function replicaDescricao( stDesc ) {
    document.frm.stDescricaoSal.value = stDesc+" Salário";
    document.frm.stDescricaoFer.value = stDesc+" Férias"; 
    document.frm.stDescricao13o.value = stDesc+" 13o Salário";
    document.frm.stDescricaoRes.value = stDesc+" Rescisão";
}

</script>

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
    * JavaScript
    * Data de Criação: 04/11/2005


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-04-16 16:43:24 -0300 (Seg, 16 Abr 2007) $

    * Casos de uso: uc-04.05.07
*/

/*
$Log$
Revision 1.6  2007/04/16 19:42:55  souzadl
Bug #9122#

Revision 1.5  2006/08/08 17:43:13  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function buscaValor(tipoBusca){
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaValorFiltro(tipoBusca){
     target = document.frm.target ;
     action = document.frm.action ;
     document.frm.stCtrl.value = tipoBusca;
     document.frm.target = 'oculto';
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = action;
     document.frm.target = target;
}

function processaPopup(inContrato,inNumCGM,stServidor){
    stLink = "<?=Sessao::getId();?>"+"&inContrato="+inContrato+"&inNumCGM="+inNumCGM+"&stServidor="+stServidor+"&stAcao=alterar";
    abrePopUp('<?php echo CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FMManterRegistroEvento.php','frm','inNumCGM','inCampoInner','',''+stLink+'','800','550')
}

function alteraDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    var stTarget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function salvarOkFiltro(){
    document.frm.stOkRetorno.value = 'filtro';
    document.frm.submit();
}

function salvarOkLista(){
    document.frm.stOkRetorno.value = 'lista';
    document.frm.submit();
}

</script>

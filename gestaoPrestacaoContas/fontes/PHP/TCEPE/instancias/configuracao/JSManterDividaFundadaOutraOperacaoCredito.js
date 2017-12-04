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
* 
* Data de Criação: 17/03/2014

* @author Analista: 
* @author Desenvolvedor: Arthur Cruz

Casos de uso: uc-01.05.03
*/

?>
<script type="text/javascript">

function incluir(stExercicio,inMes,inCodEntidade){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgForm;?>?stAcao=configurar&<?=Sessao::getId();?>&stExercicio=' + stExercicio + "&inCodEntidade=" + inCodEntidade;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function buscaValor(tipoBusca){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function modificaDado(tipoBusca, inId){
    var stAction = document.frm.action;
    var stTarget = document.frm.target;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.target = 'oculto'
    document.frm.submit();
    document.frm.action = stAction; 
    document.frm.target = stTarget;
}

function limparDivida(){
    jQuery( '#inCGM' ).val('');
    jQuery( '#stNomCredor' ).html('&nbsp;');
    
    jQuery( '#inCodLeiAutorizacao' ).val('');
    jQuery( '#stNomeLeiAutorizacao' ).html('&nbsp;');
    jQuery( 'span#stDataNorma' ).html('');
    jQuery( '#inNumeroContrato' ).val('')
    jQuery( '#vlSaldoAnterior' ).val('')
    jQuery( '#vlInscricaoExercicio' ).val('')
    jQuery( '#vlBaixaExercicio' ).val('')
}

</script>
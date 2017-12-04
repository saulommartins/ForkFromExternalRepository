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

function incluir(inExercicio,inMes,inCodEntidade){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.action = '<?=$pgForm;?>?stAcao=configurar&<?=Sessao::getId();?>&inExercicio=' + inExercicio + '&inMes=' + inMes + "&inCodEntidade=" + inCodEntidade;
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
    document.getElementById( 'inCodLeiAutorizacao' ).value         = '';
    document.getElementById( 'stNomeLeiAutorizacao' ).innerHTML    = '&nbsp;';
    document.getElementById( 'inNumContratoDivida' ).value         = '';
    document.getElementById( 'dtAssinaturaDivida' ).value          = '';
    document.getElementById( 'stObjetoContrato' ).value            = '';
    document.getElementById( 'stDescDivida' ).value                = '';
    document.getElementById( 'inTipoLancamento' ).value            = '';
    document.getElementById( 'inCGMCredor' ).value                 = '';
    document.getElementById( 'stNomeCGMCredor' ).value             = '';
    document.getElementById( 'stNomeCGMCredor' ).innerHTML         = '&nbsp';
    document.getElementById( 'stJustificativaCancelamento' ).value = '';
    document.getElementById( 'flValorSaldoAnterior' ).value        = '';
    document.getElementById( 'flValorContratacaoMes' ).value       = '';
    document.getElementById( 'flValorAmortizacaoMes' ).value       = '';
    document.getElementById( 'flValorCancelamentoMes' ).value      = '';
    document.getElementById( 'flValorEncampacaoMes' ).value        = '';
    document.getElementById( 'flValorAtualizacaoMes' ).value       = '';
    document.getElementById( 'flValorSaldoAtual' ).value           = '';
}

</script>
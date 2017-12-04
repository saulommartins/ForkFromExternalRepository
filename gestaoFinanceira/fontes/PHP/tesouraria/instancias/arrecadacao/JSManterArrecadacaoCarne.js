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
    * Arquivo JavaScript
    * Data de Criação   : 21/10/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-07-31 11:01:08 -0300 (Ter, 31 Jul 2007) $
    
    * Casos de uso: uc-02.04.34

*/

/*
$Log$
Revision 1.4  2007/07/31 14:01:08  domluc
Ajuste do Caso de Uso

Revision 1.3  2007/07/25 16:14:18  domluc
Atualizado Arr por Carne

Revision 1.2  2006/07/05 20:38:50  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparItem() {
    parent.frames['telaPrincipal'].document.frm.inCodEntidade='';
    parent.frames['telaPrincipal'].document.frm.inCodPlano.value='';
    parent.frames['telaPrincipal'].document.frm.stCarne.value='';
    parent.frames['telaPrincipal'].document.frm.stObservacoes.value='';
    parent.frames['telaPrincipal'].document.getElementById('stNomConta').innerHTML='&nbsp;';
    parent.frames['telaPrincipal'].document.getElementById('spnDados').innerHTML='';
}

function incluirItem(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'incluirItem';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
    
}

function excluirItem( stAcao, stExercicio, stCarne ) {
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = stAcao;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stCarne='+stCarne+'&stExercicio='+stExercicio;
    document.frm.submit();
    document.frm.stCtrl.value = stCtrl;
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function somaValorGeral( nuVlSoma ) {
    var nuVlGeral = parent.frames['telaPrincipal'].document.frm.nuVlTotalLista.value;
    if( nuVlGeral == '' ) nuVlGeral = new String( '0' );

    nuVlSoma  = nuVlSoma.replace( new  RegExp("[.]","g") ,'');
    nuVlSoma  = nuVlSoma.replace( ",", "." );

    nuVlGeral = new String( parseFloat(nuVlGeral) + parseFloat( nuVlSoma ) );

    arVlTotal = nuVlGeral.split( "." );
    if( !arVlTotal[1] )
        arVlTotal[1] = '00';
    var inCount = 0;
    var inValor = "";
    for( var i = (arVlTotal[0].length-1); i >= 0; i-- ) {
        if( inCount == 3 ) {
           inValor = '.' + inValor;
           inCount = 0;
        }
        inValor = arVlTotal[0].charAt(i) + inValor;
        inCount++;
    }   
    while( arVlTotal[1].length < 2 ) {
        arVlTotal[1] = arVlTotal[1] + '0';
    }
    nuVlTotal = inValor + ',' + arVlTotal[1];
    parent.frames['telaPrincipal'].document.frm.nuVlTotalLista.value = nuVlGeral;
    calculaTroco();
    parent.frames['telaPrincipal'].document.getElementById('nuVlTotalLista').innerHTML = nuVlTotal;
}

function limpar() {
    limpaFormulario();
    document.frm.nuVlTotalLista.value='';
    document.getElementById('nuVlTotalLista').innerHTML='&nbsp;';
    document.getElementById('stVlTroco').innerHTML='&nbsp;';
    limparItem();
    buscaDado( 'limpaFormulario' );
}

</script>
                

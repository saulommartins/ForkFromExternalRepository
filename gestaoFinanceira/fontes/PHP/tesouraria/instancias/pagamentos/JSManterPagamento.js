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
    * Data de Criação   : 26/10/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-10-23 13:33:46 -0300 (Seg, 23 Out 2006) $
    
    * Casos de uso: uc-02.04.05

*/

/*
$Log$
Revision 1.4  2006/10/23 16:32:50  domluc
Add opção para multiplos boletins

Revision 1.3  2006/07/05 20:39:28  cleisson
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

<?php

// O código abaixo foi removido devido a alteração do projeto 
// Comentado para fácil re-implementação, se necessário
/*
function limparItem() {
    parent.frames['telaPrincipal'].document.frm.inCodPlano.value='';
    parent.frames['telaPrincipal'].document.frm.HdninCodPlano.value='';
    parent.frames['telaPrincipal'].document.frm.stNomConta.value='';
    parent.frames['telaPrincipal'].document.frm.nuValor.value='';
    parent.frames['telaPrincipal'].document.getElementById('stNomConta').innerHTML='&nbsp;';
}

function incluirItem(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    var stCtrl   = document.frm.stCtrl.value; 
    var erro     = '';

    if( !document.frm.inCodPlano.value )
        erro = erro + '@Campo Conta Pagadora inválido!()';
    if( !document.frm.nuValor.value )
        erro = erro + '@Campo Valor inválido!()';
        
    if( erro != '' ) {
        alertaAviso(erro,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.target = 'oculto';
        document.frm.stCtrl.value = 'incluirItem';
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
        document.frm.stCtrl.value = stCtrl;
        document.frm.action = stAction;
        document.frm.target = stTarget;
    }
}

function excluirItem(stControle, inCodPlano ){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodPlano=' + inCodPlano;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
*/
?>

</script>
                

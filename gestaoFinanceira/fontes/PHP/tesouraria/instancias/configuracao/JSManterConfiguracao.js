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
    * Data de Criação   : 01/09/2005


    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore
    
    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.04.01

*/

/*
$Log$
Revision 1.4  2006/07/05 20:39:21  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado, boReiniciaComprovacao ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&boReiniciaComprovacao='+boReiniciaComprovacao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function mudaStatus( inIdAssinatura, boNovoStatus ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'mudaStatus';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&boNovoStatus='+boNovoStatus+'&inIdAssinatura='+inIdAssinatura;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function montaListaAssinatura( inNumComprovacao, boReiniciaComprovacao ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'montaListaAssinatura';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumeracaoComprovacao='+inNumComprovacao+'&boReiniciaComprovacao='+boReiniciaComprovacao;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function incluirAssinatura() {
    var mensagem = "";
   
    if(!document.frm.inNumCgm.value)
        mensagem += '@Campo CGM inválido!()';
    if(!document.frm.stNomCgm.value && document.frm.inNumCgm.value )
        mensagem += '@Campo CGM inválido!()';
    if(!document.frm.inCodEntidade.value)
        mensagem += '@Campo Entidade inválido!()';
    
    if(!document.frm.stCargo.value)
        mensagem += '@Campo Cargo inválido!()';
    if(!document.frm.boSituacao.value)
        mensagem += '@Campo Situação inválido!()';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        if( document.frm.inIdAssinatura.value == '' )
            document.frm.stCtrl.value = 'incluirAssinatura';
        else 
            document.frm.stCtrl.value = 'alterarAssinatura';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparAssinatura();
    }
    
}

function excluirAssinatura( inNumCgm ){
    document.frm.stCtrl.value = 'excluirAssinatura';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inNumCgm=' + inNumCgm;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function limparAssinatura() {
    document.frm.inIdAssinatura.value = '';
    document.frm.inNumCgm.value = '';
    document.frm.stNomCgm.value = '';
    document.getElementById( 'stNomCgm' ).innerHTML = "&nbsp;"
    document.frm.stCargo.value = '';
    document.frm.boSituacao.value = '';
}

function alterarAssinatura( inIdAssinatura, inNumCgm, stNomCgm, stCargo, boSituacao, inCodEntidade ) {
    document.frm.inIdAssinatura.value = inIdAssinatura;
    document.frm.inNumCgm.value = inNumCgm;
    document.frm.stNomCgm.value = stNomCgm;
    document.getElementById( 'stNomCgm' ).innerHTML = stNomCgm;
    document.frm.stCargo.value = stCargo;
    document.frm.boSituacao.value = boSituacao;
    document.frm.inCodEntidade.value = inCodEntidade;
}

</script>
                

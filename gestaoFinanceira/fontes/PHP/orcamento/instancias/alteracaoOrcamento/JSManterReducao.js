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
    

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cleisson $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $
    
    * Casos de uso: uc-02.01.07
*/

/*
$Log$
Revision 1.3  2006/07/05 20:42:23  cleisson
Adicionada tag Log aos arquivos

*/
?>
<script type="text/javascript">

function buscaDado( BuscaDado ){
    document.frm.stCtrl.value = BuscaDado;
    stAction = document.frm.action;
    stTarget = document.frm.target;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.target = 'oculto';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirDotacaoRedutora() {
    var mensagem = "";
    var inCodDotacaoReducao  = document.frm.inCodDotacaoReducao.value;
    var nuVlDotacaoRedutora   = document.frm.nuVlDotacaoRedutora.value;
    
    if(!inCodDotacaoReducao)
        mensagem += '@Campo Dotação Orcamentária inválido!()';
    if(!nuVlDotacaoRedutora)
        mensagem += '@Campo Valor inválido!()';
    
    nuVlDotacaoRedutora = nuVlDotacaoRedutora.replace( new  RegExp("[.]","g") ,'');
    nuVlDotacaoRedutora = nuVlDotacaoRedutora.replace( "," ,'.');
    if( nuVlDotacaoRedutora == 0 )
        mensagem += "@Campo Valor inválido!( o valor deve ser maior que 0 (zero) )";

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        stAction = document.frm.action;
        stTarget = document.frm.target;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluirReducao';
        document.frm.target = 'oculto';
        document.frm.submit();
        document.frm.target = stTarget;
        document.frm.action = stAction;
        limparRedutora();
    }
    
}

function limparListas() {
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'limparListas';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparRedutora() {
    document.frm.inCodDotacaoReducao.value = "";
    document.frm.nuVlDotacaoRedutora.value  = "";
    document.getElementById("stNomDotacaoRedutora").innerHTML = "&nbsp;";
}
function limparSuplementada() {
    document.frm.inCodDotacaoSuplementada.value = "";
    document.frm.nuVlDotacaoSuplementada.value  = "";
    document.getElementById("stNomDotacaoSuplementada").innerHTML = "&nbsp;";
}
function Limpar() {
    var d = document;
    var f = d.frm;
    limparListas();
    limparRedutora();
    limparSuplementada();    
    d.getElementById( "stNomTipoNorma" ).innerHTML = "&nbsp;";
    d.getElementById( "spnSuplementada" ).innerHTML = "";
    d.getElementById( "spnReducoes" ).innerHTML = "";
    f.nuVlTotal.value = '';
    f.inCodNorma.value = '';
    f.stMotivo.value = '';
}
                                    
function excluirDotacaoRedutora(stControle, inCodDespesa ){
    document.frm.stCtrl.value = stControle;
    stAction = document.frm.action;
    stTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodDotacaoReducao=' + inCodDespesa;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirDotacaoSuplementada() {
    var mensagem = "";
    var inCodDotacaoSuplementada  = document.frm.inCodDotacaoSuplementada.value;
    var nuVlDotacaoSuplementada   = document.frm.nuVlDotacaoSuplementada.value;
    
    if(!inCodDotacaoSuplementada)
        mensagem += '@Campo Dotação Orcamentária inválido!()';
    if(!nuVlDotacaoSuplementada)
        mensagem += '@Campo Valor inválido!()';
    
    nuVlDotacaoSuplementada  = nuVlDotacaoSuplementada.replace( new  RegExp("[.]","g") ,'');
    nuVlDotacaoSuplementada  = nuVlDotacaoSuplementada.replace( "," ,'.');
    if( nuVlDotacaoSuplementada == 0 )
        mensagem += "@Campo Valor inválido!( o valor deve ser maior que 0 (zero) )";

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        stAction = document.frm.action;
        stTarget = document.frm.target;
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluirSuplementada';
        document.frm.target = 'oculto';
        document.frm.submit();
        document.frm.target = stTarget;
        document.frm.action = stAction;
        limparSuplementada();
    }
    
}
function excluirDotacaoSuplementada(stControle, inCodDespesa ){
    document.frm.stCtrl.value = stControle;
    stAction = document.frm.action;
    stTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodDotacaoSuplementada=' + inCodDespesa;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}
</script>
                

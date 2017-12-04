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
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = BuscaDado;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


function incluirDespesaSuplementar() {
    var mensagem = "";
   
    if(!document.frm.inCodDespesaSuplementar.value)
        mensagem += '@Campo Dotação Orçamentária inválido!()';
    if(!document.frm.nuVlSuplementar.value)
        mensagem += '@Campo Valor inválido!()';
    
    var nuVlDotacaoSuplementar = document.frm.nuVlSuplementar.value.replace( new  RegExp("[.]","g") ,'');
    nuVlDotacaoSuplementar = nuVlDotacaoSuplementar.replace( "," ,'.');
    if( nuVlDotacaoSuplementar == 0)
        mensagem += '@Campo Valor inválido!( o valor deve ser maior que 0 (zero) )';

    if( mensagem ) {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    } else {
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.stCtrl.value = 'incluirDespesaSuplementar';
        document.frm.submit();
        document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
        limparDespesaSuplementar();
    }
    
}

function excluirDespesa(stControle,  inCodDespesa ){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodDespesa=' + inCodDespesa;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}


function limparListaSuplementar() {
    var stTarget = document.frm.target;
    var stAction = document.frm.action; 
    limparDespesaSuplementar();
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'limparDespesaSuplementar';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function limparDespesaSuplementar() {
    document.frm.inCodDespesaSuplementar.value = '';
    document.getElementById("stNomDespesaSuplementar").innerHTML = "&nbsp;";
    document.frm.nuVlSuplementar.value = '';
}

function Limpar() {
    var d = document;
    var f = d.frm;
    limparListaSuplementar();
    d.getElementById( "stNomTipoNorma" ).innerHTML = "&nbsp;";
    d.getElementById( "stNomDespesaSuplementar" ).innerHTML = "&nbsp;";
    d.getElementById( "spnDespesaSuplementar" ).innerHTML = "";
    f.nuVlTotal.value = '';
    f.inCodNorma.value = '';
    f.stMotivo.value = '';
}

</script>
                

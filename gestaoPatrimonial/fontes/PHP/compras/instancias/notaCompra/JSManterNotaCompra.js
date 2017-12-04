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
    * Data de Criação   : 06/09/2006


    * @author Analista: Cleisson da Silva Barboza
    * @author Desenvolvedor: Thiago La Delfa Cabelleira

    * @ignore

    $Revision: 18659 $
    $Name$
    $Autor:$
    $Date: 2006-12-11 08:45:18 -0200 (Seg, 11 Dez 2006) $

    * Casos de uso: uc-03.04.29

*/

/*
$Log$
Revision 1.1  2006/12/11 10:45:18  thiago
arquivos para nota de compra

Revision 1.1  2006/12/11 10:21:08  thiago
arquivo javascript




*/
?>
<script type="text/javascript">

function consultarNotaCompra( num_nota, cod_ordem , cod_empenho ){
    
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.stCtrl.value = 'consultarNotaCompra';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&num_nota='+num_nota+'&cod_ordem='+cod_ordem+'&cod_empenho='+cod_empenho;
//    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function excluirFornecedor( inCodCatalogo,classificacao ){
    document.frm.stCtrl.value = 'excluirFornecedor';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodCatalogo='+inCodCatalogo+'&classificacao='+classificacao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function excluirAtividade( inCodAtividade ){
    document.frm.stCtrl.value = 'excluirAtividade';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodAtividade='+inCodAtividade;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

//function carregarAtividade( valorComposto , inCodigoAtividade , stNomeAtividade , inCodigoModalidade ){
function carregarAtividade( BuscaDado, inCodigoAtividade, inCodigoModalidade ){
    //document.frm.stValorComposto.value         = valorComposto;
    document.frm.inCodigoAtividade.value       = inCodigoAtividade;
    //document.frm.stNomeAtividade.value         = stNomeAtividade;
    //if ( inCodigoModalidade || "" ) {
    //    document.frm.inCodigoModalidade.value  = inCodigoModalidade;
        //document.frm.cmbCodigoModalidade.value = inCodigoModalidade;
    //}
    document.frm.stCtrl.value = BuscaDado;
    var stTarget = document.frm.target;
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.target = stTarget;
}

function mudaStatusPadrao( inIdUsuario, boNovoPadrao ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = 'oculto';
    document.frm.stCtrl.value = 'mudaStatusPadrao';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&boNovoPadrao='+boNovoPadrao+'&inIdUsuario='+inIdUsuario;
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTarget;
}


//function limpaFormularioContaBancaria(){
//    f = document.frm;
//    f.inCodBancoTxt.value = '';
//    f.stNumAgenciaTxt.value = '';
//    f.stNumConta.value = '';
//    limpaSelect(f.stNumAgencia,0); 
//    f.stNumAgencia.options[0] = new Option('Selecione','', 'selected');
//    f.inCodBanco.selectedIndex = 0 ;
//    f.inCodBancoTxt.focus(); 
//}

function limpaFormularioFornecedor(){
    f = document.frm;
    d = document;
    f.inCodCatalogoTxt.value = '';
    f.inCodCatalogo.selectedIndex = 0 ;
    f.inCodCatalogoTxt.focus(); 
    d.getElementById('spnClassificacao').innerHTML = ' ';    

}

function Limpar(){
    d = document;
    d.getElementById('spnClassificacao').innerHTML = ' ';    
    d.getElementById('spnListaContaBancaria').innerHTML = ' ';    
    d.getElementById('spnListaFornecedor').innerHTML = ' ';    
    buscaValor('limpaFormulario');
}
</script>

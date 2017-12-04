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
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @ignore

    $Revision: 23224 $
    $Name$
    $Autor:$
    $Date: 2007-06-13 12:57:04 -0300 (Qua, 13 Jun 2007) $

    * Casos de uso: uc-03.04.03

*/

/*
$Log$
Revision 1.4  2007/06/13 15:57:04  bruce
Bug #8707#

Revision 1.3  2006/10/21 13:21:32  bruce
retirado o controle para atividades

Revision 1.2  2006/10/02 14:48:32  fernando
função para limpar os spans e a sessao->transf3 das listas que formam o span.

Revision 1.1  2006/09/21 17:51:20  fernando
Formulário de inclusão de fornecedores


*/
?>
<script type="text/javascript">



function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.stCtrl.value = variavel;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}


function preencheCombosAtividade(){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'preencheCombosAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function preencheProxCombo( inPosicao  ){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}


/*
function preencheCombos(){
    document.frm.stCtrl.value = 'preencheCombos';
    var target = document.frm.target;
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProx;?>?<?=Sessao::getId();?>';
    document.frm.target = target;
}
*/
function excluirContaBancaria( inCodBanco, stNumAgencia , stNumConta ){
    
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    document.frm.stCtrl.value = 'excluirContaBancaria';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inCodBanco='+inCodBanco+'&stNumConta='+stNumConta+'&stNumAgencia='+stNumAgencia;
//    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
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
    buscaValor('limpaFormulario');
    d.getElementById('inCGM').focus();
}

function ValidaSocio() {
    if(document.getElementById('cgmSocio').value == '') {
        alertaAviso('Campo (CGM do Sócio) deve estar preenchido antes de adicionar a lista.', null, 'n_incluir', '<?=Sessao::getId();?>');
        return false;
    }
    
    if(document.getElementById('inCodTipo').value == '') {
        alertaAviso('Campo (Tipo) deve estar preenchido antes de adicionar a lista.', null, 'n_incluir', '<?=Sessao::getId();?>');
        return false;
    }
    
    return true;
}

</script>
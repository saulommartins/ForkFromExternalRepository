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
    * Página de funções javascript de Atividades
    * Data de Criação   : 25/11/2004


    * @author Tonismar Régis Bernardo

    * @ignore

	* $Id: JSProcurarAtividade.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso : uc-05.02.07, uc-03.04.03
*/

/*
$Log$
Revision 1.5  2007/02/14 17:38:03  tonismar
bug #8152

Revision 1.4  2006/09/15 13:45:18  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

?>

<script type="text/javascript">

function preencheProxCombo( inPosicao  ){
    document.frm.stCtrl.value = 'preencheProxCombo';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicao='+inPosicao;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosAtividade(){
    document.frm.stCtrl.value = 'preencheCombosAtividade';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheProxComboServico( inPosicaoServico  ){
    document.frm.stCtrl.value = 'preencheProxComboServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inPosicaoServico='+inPosicaoServico;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheCombosServico(){
    document.frm.stCtrl.value = 'preencheCombosServico';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheServicoVigencia(){
    document.frm.stCtrl.value = 'preencheServicoVigencia';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function preencheVigencia(){
    var d = document.frm;
    d.stCtrl.value = 'preencheVigencia';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = "";
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function buscaCodigoVigencia(){
    var d = document.frm;
    d.stCtrl.value = 'buscaCodigoVigencia';
    d.target = "oculto";
    d.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    d.submit();
    d.target = "";
    d.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirServico(){
    var mensagem = validarServico();

    if ( mensagem == '' ){
        buscarValor('MontaServico');
    } else {
        SistemaLegado::alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function buscarValor(tipoBusca){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function buscarCnae(){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'buscaCnae';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function validarServico(){
    var mensagem = '';
    var d = document.frm;

    if ( d.stChaveServico.value == 0) {
        mensagem += "@Campo Servico inválido!( )";
    }
    return mensagem;
}

function limparSelectMultiplo(stSelecionado, stDisponivel){
    passaItem(stSelecionado, stDisponivel, 'tudo' );
}

function Limpar(){
    limparSelectMultiplo('inCodElementosSelecionados', 'inCodElementosDisponiveis');
    limparSelectMultiplo('inCodResponsaveisSelecionados', 'inCodResponsaveisDisponiveis');
    limparServico();
}
    
function limparServico() {
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparServico';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.getElementById("spnServicoCadastrado").innerHTML = "&nbsp;";
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function excluiDado(stControle, inId){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}

function Cancelar(){
<?php
    $link = Sessao::read( "link" );
    $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "telaPrincipal";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

function filtrar(filtro){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;   
    document.frm.action = '<?=$pgFilt;?>?<?=Sessao::getId();?>'+filtro;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

</script>

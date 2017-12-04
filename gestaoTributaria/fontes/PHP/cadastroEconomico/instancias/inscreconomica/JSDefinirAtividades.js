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
    * Página de JavaScript para Definir Atividade
    * Data de Criação   : 25/11/2004


    * @author Tonismar Régis Bernardo

    * @ignore

	* $Id: JSDefinirAtividades.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:07  fabio
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

function buscaValor(tipoBusca){
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function incluirAtividade(){
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    document.frm.stCtrl.value = 'montaAtividade';
    document.frm.target = 'oculto';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function incluirHorario(){

    if ( mensagem == '' ){
        buscaValor('montaHorario');
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}


function incluirHorario() {
    var stTarget   = document.frm.target;
    var stAction   = document.frm.action;
    var erro       = false;
    var mensagem   = "";
    stCampoInicio  = document.frm.hrHorarioInicio;
    stCampoTermino = document.frm.hrHorarioTermino;

/*
    if ( stCampoInicio.value == "" ) {
        erro = true;
        mensagem += "@Campo Horário de Início inválido!";
        if ( stCampoTermino.value == "") {
            mensagem += "@Campo Horário de Término inválido!";
        }
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else if ( stCampoTermino.value == "") {
        erro = true;
        mensagem += "@Campo Horário de Término inválido!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else 
    if ( stCampoTermino.value < stCampoInicio.value ) {
*/
    if ( stCampoInicio.value && stCampoTermino.value && (stCampoTermino.value < stCampoInicio.value) ) {
        erro = true;
        mensagem += "@Horário de Término deve ser após o Horário de Início!";
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
        document.frm.stCtrl.value = 'incluirHorario';
        document.frm.target = "oculto";
        document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
        document.frm.submit();
    }
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function excluirHorario( inIndice ){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'excluirHorario';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function limparHorario(){
    document.frm.dia1.checked = false;
    document.frm.dia2.checked = false;
    document.frm.dia3.checked = false;
    document.frm.dia4.checked = false;
    document.frm.dia5.checked = false;
    document.frm.dia6.checked = false;
    document.frm.dia7.checked = false;
    document.frm.hrHorarioInicio.value = '';
    document.frm.hrHorarioTermino.value = '';
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'limparHorario';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inIndice='+inIndice;
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;
}

function limparAtividade(){

    document.frm.dtDataTermino.value = '';
    document.frm.stChaveAtividade.value = '';
    document.frm.inCodAtividade_1.value = '';
    document.frm.stPrincipal[1].checked = true;
    
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.target = "oculto";
    document.frm.stCtrl.value = 'limparAtividade';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.target = stTarget;
    document.frm.action = stAction;    

}

function limpar(){

    document.frm.stCtrl.value = 'limpar';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    //Cancelar();
}    

function excluirDado(stControle, inId){
    var stTarget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.action = stAction;
    document.frm.target = stTarget;
}
function Cancelar () {
<?php
    $link = Sessao::read( "link" );
     $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];
?>
    document.frm.target = "";
    document.frm.action = "<?=$pgList.'?'.Sessao::getId().$stLink;?>";
    document.frm.submit();
}

</script>

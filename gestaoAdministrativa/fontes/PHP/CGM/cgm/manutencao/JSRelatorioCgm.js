<?php
/*
* Arquivo de javascript para o relatório de CGM
* Data de Criação: 04/07/2013

* Copyright CNM - Confederação Nacional de Municípios

$Id: $

* @author Analista      : Eduardo Schitz
* @author Desenvolvedor : Franver Sarmento de Moraes

* @package URBEM
*/

?>

<script type="text/javascript">
function preencheUf( stLimpar ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheUf';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stLimpar=' + stLimpar;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function preencheMunicipio( stLimpar ){
    var stTraget = document.frm.target;
    var stAction = document.frm.action;
    document.frm.stCtrl.value = 'preencheMunicipio';
    document.frm.target = "oculto";
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stLimpar=' + stLimpar;
    document.frm.submit();
    document.frm.target = stTraget;
    document.frm.action = stAction;
}

function Valida(){
    var mensagem = "";
    var erro = false;
    var campo;
    if (erro) alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    return !(erro);
}
function Salvar(){
    if (Valida()){
        document.frm.submit();
    }
}

function Limpar(){
    document.frm.reset();
    preencheUf( 'limpar' );
    preencheMunicipio( 'limpar' );
}

function buscaValor(tipoBusca){
	alert(tipoBusca);
    document.frm.stCtrl.value = tipoBusca;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
</script>
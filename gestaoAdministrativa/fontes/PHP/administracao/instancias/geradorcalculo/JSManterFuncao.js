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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005


* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3075 $
$Name$
$Author: pablo $
$Date: 2005-11-29 14:45:45 -0200 (Ter, 29 Nov 2005) $

Casos de uso: uc-01.03.95
*/
?>

<script type="text/javascript">

function goOculto(stControle){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}
function Salvar_Fica(stControle){
    stCtrl = document.frm.stAcao.value;
    document.frm.stAcao.value = 'salva_fica';
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
    document.frm.stAcao.value = stCtrl;
}

function AdicionaParametro(stControle){ 
    var mensagem = '';
    var tipoParametro = document.frm.stTipoParametro.value;
    var nomeParametro = document.frm.stNomeParametro.value;
    
    if (tipoParametro == 0) {
        mensagem += "@Campo Tipo inválido!( )";
    }
    if (nomeParametro == 0) {
        mensagem += "@Campo Nome inválido!( )";
    }
    if (mensagem == ''){
        goOculto(stControle);
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function AdicionaVariavel(stControle){
    var mensagem = '';
    var tipoVariavel = document.frm.stTipoVariavel.value;
    var nomeVariavel = document.frm.stNomeVariavel.value;
    var valorVariavel = document.frm.stValorVariavel.value;
    
    if (tipoVariavel == 0) {
        mensagem += "@Campo Tipo inválido!( )";
    }
    if (nomeVariavel == 0) {
        mensagem += "@Campo Nome inválido!( )";
    }
    if (mensagem == ''){
        goOculto(stControle);
    } else {
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
        return false;
    }
}

function excluiDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function ordenaDado(stOrdem, inId, stNomeParametro){
    document.frm.stCtrl.value = 'ordenaDado';
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&stOrdem=' +stOrdem+ '&inId=' + inId + '&stNomeParametro=' + stNomeParametro;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function limpaParametros(){
    document.frm.stTipoParametro.value     = '';
    document.frm.stTipoParametroTxt.value  = '';
    document.frm.stNomeParametro.value     = '';
}

function AbrePopUp( stName ){
    var stName = stName + '.php?<?=Sessao::getId();?>';
    var stMensagem = '';
    
    stName = stName + '&stPosicao=';
    if( document.frm.rdbPosicao ){
        if( document.frm.rdbPosicao.value ){
            stName = stName + document.frm.rdbPosicao.value;
        }else{
            for(var inCount=0; inCount<document.frm.rdbPosicao.length; inCount++){
                if(document.frm.rdbPosicao[inCount].checked == true)
                    stName = stName + document.frm.rdbPosicao[inCount].value;
            }
        }
        janela = window.open(stName,'AbrePopUp','scrollbars=yes,menubar=no,location=no,status=no,left=200,top=70,width=700,innerwidth=700,height=500,innerheight=500');
        janela.focus();
    }else{
        stMensagem = 'Deve-se criar variáveis para efetuar esta operação.';
        alertaAviso(stMensagem,'form','erro','<?=Sessao::getId();?>');
    }
}
function AbrePopupAcao( stName, stAcao, stPosicao, stComplemento ){
    var stName = stName + '.php?<?=Sessao::getId();?>';
    var stMensagem = '';
    stName = stName + '&stAcao=' + stAcao;
    stName = stName + '&stPosicao=' + stPosicao;
    stName = stName + stComplemento;
    
    janela = window.open(stName,'AbrePopUp','scrollbars=yes,menubar=no,location=no,status=no,left=200,top=70,width=700,innerwidth=700,height=500,innerheight=500');
    janela.focus();
}

function validaNome( campo, evento ){
    var expRegular = new RegExp("[0-9a-zA-Z]","g");
    var retorno = true;
    var teclaPressionada;
    var caracter;
    var strTemp;
    if ( navigator.appName == "Netscape" ){
        teclaPressionada = evento.which;
    } else {
        teclaPressionada = evento.keyCode;
    }
    caracter = String.fromCharCode( teclaPressionada );
    if( !validaTecla( evento.keyCode ) ){
        if( caracter.search(expRegular) ){
            retorno = false;
        }else{
            if( campo.value == 0 && caracter.search( new RegExp('[a-zA-Z]','g') ) == -1 ){
                retorno = false;
            }else{
                retorno = true;
            }
        }
    }
    return retorno;
}

function limpaCampoBiblioteca(){
    document.frm.inCodBiblioteca.value = "";
    document.getElementById('stCodBiblioteca').innerHTML ="&nbsp;";  
}

function BuscaValores( stCombo ){
   var stTarget = document.frm.target;
   var stAction = document.frm.action;
   document.frm.stCtrl.value = stCombo;
   document.frm.target = 'oculto';
   document.frm.action = '<?=$pgOcul.'?'.Sessao::getId();?>';
   document.frm.submit();
   document.frm.target = stTarget;
   document.frm.action = stAction;
}
</script>

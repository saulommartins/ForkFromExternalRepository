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
  * Página de 
  * Data de criação : 09/06/2005


    * @author Programador: Vandré Miguel Ramos 




    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    Caso de uso: uc-04.04.08
**/

?>

<script type="text/javascript">

function bloqueiaAbas(){
    document.links['id_layer_2'].href = "javascript:buscaValor('exibeAviso');";
    document.links['id_layer_3'].href = "javascript:buscaValor('exibeAviso');";
    document.links['id_layer_4'].href = "javascript:buscaValor('exibeAviso');";       
}

function desabilitaNorma(){
    window.parent.frames["telaPrincipal"].document.frm.inCodClassificacaoTxt.disabled          = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodClassificacaoTxt.style.color       = '#333333';
    window.parent.frames["telaPrincipal"].document.frm.inCodClassificacao.disabled          = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodClassificacao.style.color       = '#333333';    
    window.parent.frames["telaPrincipal"].document.frm.inCodNormaTxt.disabled          = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodNormaTxt.style.color       = '#333333';
    window.parent.frames["telaPrincipal"].document.frm.inCodNorma.disabled             = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodNorma.style.color          = '#333333';
    window.parent.frames["telaPrincipal"].document.frm.inCodTipoNormaTxt.disabled      = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodTipoNormaTxt.style.color   = '#333333';
    window.parent.frames["telaPrincipal"].document.frm.inCodTipoNorma.disabled         = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodTipoNorma.style.color      = '#333333';
    window.parent.frames["telaPrincipal"].document.frm.inCodOperadorTxt.disabled       = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodOperadorTxt.style.color    = '#333333';    
    window.parent.frames["telaPrincipal"].document.frm.inCodOperador.disabled            = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodOperador.style.color         = '#333333';

}

function desabilitaAfastamento(){
    window.parent.frames["telaPrincipal"].document.frm.inCodSefipTxt.disabled     = true;
    window.parent.frames["telaPrincipal"].document.frm.inCodSefip.disabled        = true;
    window.parent.frames["telaPrincipal"].document.frm.inIdIntervalo.disabled     = true;
    window.parent.frames["telaPrincipal"].document.frm.inInicioIntervalo.disabled = true;
    window.parent.frames["telaPrincipal"].document.frm.inFimIntervalo.disabled    = true;
    window.parent.frames["telaPrincipal"].document.frm.flPercentualDesc.disabled  = true;
    window.parent.frames["telaPrincipal"].document.frm.btnIncluir.disabled        = true;
    window.parent.frames["telaPrincipal"].document.frm.btnAlterar.disabled        = true;
    window.parent.frames["telaPrincipal"].document.frm.btnLimpar.disabled         = true;
}

function desabilitaRescisao(){
  var inSize = window.parent.frames["telaPrincipal"].document.frm.elements.length;
  var stCausaRescisao = '';

  for ( var i = 0; i < inSize; i++ ){
     if(window.parent.frames["telaPrincipal"].document.frm.elements[i].name.substr(0,16) == 'inCodAbaRescisao'){
        stCausaRescisao = window.parent.frames["telaPrincipal"].document.frm.elements[i].name;
        window.parent.frames["telaPrincipal"].document.frm.elements[i].disabled  = true;
     }
  }
}



function buscaValor(tipoBusca){
     document.frm.stCtrl.value = tipoBusca;
     document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>'
     document.frm.submit();
     document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function marcaRescisao(codCausa){
var inSize = window.parent.frames["telaPrincipal"].document.frm.elements.length;
var stCausaRescisao = '';
var stCausa = '';

for ( var i = 0; i < inSize; i++ ){
   if(window.parent.frames["telaPrincipal"].document.frm.elements[i].name.substr(0,16) == 'inCodAbaRescisao'){
      stCausaRescisao = window.parent.frames["telaPrincipal"].document.frm.elements[i].name;
      stCausa = stCausaRescisao.split('_');
      if(stCausa[1] == codCausa){
         window.parent.frames["telaPrincipal"].document.frm.elements[i].checked = true;
      }
   }
}

}

function focusIncluir(){
    document.frm.inCodClassificacao.focus();
}

function validaDesconto( desconto, campo, descricao ){
    var mensagem = '';
    var d = document.frm;
    var valor  = campo.value;

    desconto = desconto.replace('.','');
    desconto = desconto.replace(',','.');

    if( desconto > 100){
        mensagem += "@Campo " + descricao + " inválido!( " + valor + " )";
        campo.value = "";
        campo.focus();
    }
    if(mensagem != ''){
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
    }
}

function excluiDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function alteraDado(stControle, inId, inInicioIntervalo, inFimIntervalo, flPercentualDesc){
    document.frm.inIdIntervalo.value        = inId;
    document.frm.inInicioIntervalo.value    = inInicioIntervalo;
    document.frm.inFimIntervalo.value       = inFimIntervalo;
    document.frm.flPercentualDesc.value     = flPercentualDesc;
}

function alterarDado(stControle, inId){
    document.frm.stCtrl.value = stControle;
    document.frm.action = '<?=$pgOcul;?>?<?=Sessao::getId();?>&inId=' + inId;
    document.frm.submit();
    document.frm.action = '<?=$pgProc;?>?<?=Sessao::getId();?>';
}

function validaAssentamento(){
    var mensagem = '';
    var d = document.frm;
    var valorInicial = '';
    var valorFinal = '';
    var PercentualDesconto = '';

    valorInicial = document.frm.elements['inInicioIntervalo'].value;
    valorInicial = valorInicial.replace(',','');
    valorInicial = valorInicial.replace('.','');
    valorInicial = parseFloat(valorInicial);

    valorFinal = document.frm.elements['inFimIntervalo'].value;
    valorFinal = valorFinal.replace(',','');
    valorFinal = valorFinal.replace('.','');
    valorFinal = parseFloat(valorFinal);

    PercentualDesconto = document.frm.elements['flPercentualDesc'].value;
    PercentualDesconto = PercentualDesconto.replace(',','');
    PercentualDesconto = PercentualDesconto.replace('.','');
    PercentualDesconto = parseFloat(PercentualDesconto);



   if ( valorInicial  == '0' || !valorInicial) {
        mensagem += "@Salário inicial inválido ( )";
    }

    if ( valorFinal  == '0' || !valorFinal ) {
        mensagem += "@Salário final inválido ( )";
    }
    if ( valorInicial >= valorFinal ) {
        mensagem += "@Valor inicial deve ser menor que final.";
    }

    if ( PercentualDesconto  == '0' || !PercentualDesconto ) {
        mensagem += "@Percentual de desconto inválido ( )";
    }
    return mensagem;
}

function IncluiFaixa(){
    var mensagem = "";
    if(document.frm.inIdIntervalo.value != ''){
        mensagem += "@Alteração em processo! Clique em alterar para concluir ou limpar para cancelar!"
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }else{
        mensagem += validaAssentamento();

        if ( mensagem == '' ){
            buscaValor('MontaFaixa');
        } else {
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return false;
        }
    }
}   

function AlteraFaixa(){
    var mensagem = "";
    if(document.frm.inIdIntervalo.value==''){
        mensagem += "@Inclusão em processo! Clique em incluir para concluir ou limpar para cancelar!"
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        return false;
    }else{
        mensagem += validaAssentamento();

        if ( mensagem == '' ){
            buscaValor('MontaFaixaAlteracao');
        } else {
            alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>');
            return false;
        }
    }
}   

function limpaAssentamento(){
    document.frm.dtDataInicio.value         = "";
    document.frm.dtDataEncerramento.value   = "";
    document.frm.inQuantidadeMeses.value    = "";
    document.frm.nuPercentualCorrecao.value = "";
}

function Limpar(){
    HabilitaLayer("layer_1");
    bloqueiaAbas();
    buscaValor("limpaSessao");
}

function limpaCampos(){
    document.frm.inCodClassificacaoTxt.value='';
    document.frm.inCodClassificacao.value='';
    document.frm.stDescricao.value='';
    document.frm.stSigla.value='';
//     document.frm.stInicioVigencia.value='';
//     document.frm.stFimVigencia.value='';
    document.frm.inCodTipoNormaTxt.value='';
    document.frm.inCodTipoNorma.value='';
    document.frm.inCodNormaTxt.value='';
    document.frm.inCodNorma.value='';
//     document.frm.inCodRegimeVinculadoTxt.value='';
//     document.frm.inCodRegimeVinculado.value='';
//     document.frm.inCodEventoTxt.value='';
//     document.frm.inCodEvento.value='';
}

</script>

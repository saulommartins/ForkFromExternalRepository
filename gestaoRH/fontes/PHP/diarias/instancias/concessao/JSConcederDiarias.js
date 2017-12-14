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
    * Página de Javascript
    * Data de Criação: 22/08/2008

    
    * @author Analista: Dagiane	Vieira	
    * @author Desenvolvedor: <Alex Cardoso>
    
    * @ignore
    
    $Id: JSConcederDiarias.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
    
    * Casos de uso: uc-04.09.02
*/
?>
<script type="text/javascript">

function ValidaConcessao(){
      var erro = false;
      var mensagem = "";
      stCampo = document.frm.stCodNorma;
     if( stCampo ) {
         if( stCampo.value.length < 1 ){
             erro = true;
             mensagem += "@Campo Lei/Decreto inválido!("+stCampo.value+")";
         }
     }
     stCampo = document.frm.stCodNorma;
     if( stCampo ) {
         if( !validaExpressaoInteira( stCampo , '[0-9/]' ) ){
             erro = true;
             mensagem += "@Campo Descrição da Norma apresenta caracteres inválidos! ( "+stCampo.value+" )";
         }
     }
     stCampo = document.frm.dtInicio;
     if( stCampo ) {
         if( stCampo.value.length > 0 ){
             if( !isData( stCampo.value ) ){
                 erro = true;
                 mensagem += "@Campo Período da Viagem inválido!("+stCampo.value+")";
             }
         }
     }
     stCampo = document.frm.dtInicio;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Período da Viagem inválido!()";
        }
    }
     stCampo = document.frm.dtTermino;
     if( stCampo ) {
         if( stCampo.value.length > 0 ){
             if( !isData( stCampo.value ) ){
                 erro = true;
                 mensagem += "@Campo Período da Viagem inválido!("+stCampo.value+")";
             }
         }
     }
     stCampo = document.frm.dtTermino;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Período da Viagem inválido!()";
        }
    }
    
    stCampo = document.frm.hrInicio;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Hora Saída inválido!()";
        }
    }    
    
    stCampo = document.frm.hrTermino;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Hora Chegada inválido!()";
        }
    }    
    
     stCampo = document.frm.inCodPais;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo País Destino inválido!()";
        }
    }
     stCampo = document.frm.inCodEstado;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Estado Destino inválido!()";
        }
    }
     stCampo = document.frm.inCodMunicipio;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Cidade Destino inválido!()";
        }
    }
    stCampo = document.frm.stMotivo;
    if( stCampo ) {
        var expReg = new RegExp("\n","ig");
        var stCampo = stCampo.value.replace(expReg, '');
        if( trim( stCampo ) == "" ){
            erro = true;
            mensagem += "@Campo Motivo da Viagem inválido!()";
        }
    }
     stCampo = document.frm.inCodTipo;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Tipo de Diária inválido!()";
        }
    }
     stCampo = document.frm.nuValorDiaria;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo  Valor da Diária inválido!()";
        }
    }
     stCampo = document.frm.nuQuantidade;
     if( stCampo ) {
         if( !isFloat( stCampo.value ) ){
             erro = true;
             mensagem += "@Campo Quantidade inválido!("+stCampo.value+")";
         }
     }
     stCampo = document.frm.nuQuantidade;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Quantidade inválido!()";
        }
    }
     stCampo = document.frm.nuValorTotal;
     if( stCampo ) {
         if( !isFloat( stCampo.value ) ){
             erro = true;
             mensagem += "@Campo Valor Total inválido!("+stCampo.value+")";
         }
     }
     stCampo = document.frm.nuValorTotal;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Valor Total inválido!()";
        }
    }
     /*
     stCampo = document.frm.dtPagamento;
     if( stCampo ) {
         if( stCampo.value.length > 0 ){
             if( !isData( stCampo.value ) ){
                 erro = true;
                 mensagem += "@Campo Data do Pagamento inválido!("+stCampo.value+")";
             }
         }
     }
     stCampo = document.frm.dtPagamento;
     if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Data do Pagamento inválido!()";
        }
     }
     */
     if( (ifila) < fila.length ) {
         erro = true;
         mensagem += 'Aguarde todos os processos concluírem.';
     }
     if( erro ){ 
          alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
     }
    return !erro;
}
  
//Funcao especifica de limpeza de formulário. limpa também as spans e os valores dos selects para o componente de MontaPaisEstadoMunicipio
function limpaFormularioConcessao(/* boLimparComboPaises = false */ ){

    if( document.frm.stAssinatura )
        document.frm.stAssinatura.value = ''; 
    if( document.getElementById('stNorma') )
        document.getElementById('stNorma').innerHTML = '&nbsp;'; 
    if( document.frm.stCodNorma )
        document.frm.stCodNorma.value = ''; 
    if( document.frm.dtInicio )
        document.frm.dtInicio.value='';
    if( document.frm.dtInicio )
        document.frm.dtInicio.value='';
    if( document.frm.hrInicio )
        document.frm.hrInicio.value='00:00';
    if( document.frm.hrTermino )
        document.frm.hrTermino.value='00:00';
    if( document.getElementById('') )
        document.getElementById('').innerHTML = '&nbsp;';
    if( document.frm.dtTermino )
        document.frm.dtTermino.value='';
    if( document.frm.stMotivo )
        document.frm.stMotivo.value='';
        
    document.frm.inCodTipo.selectedIndex = 0;
    
    if( document.getElementById('nuValorDiariaFormatado') )
        document.getElementById('nuValorDiariaFormatado').innerHTML = '&nbsp;';
    if( document.frm.nuValorDiaria )
        document.frm.nuValorDiaria.value='';        
    if( document.frm.nuQuantidade )
        document.frm.nuQuantidade.value='<? if(isset($obTxtQuantidade)){ echo $obTxtQuantidade->getValue(); }?>';
    if( document.frm.nuValorTotal )
        document.frm.nuValorTotal.value='';
    /*if( document.frm.dtPagamento )
        document.frm.dtPagamento.value='';*/
        
    if( document.frm.inCodDiariaChave )
        document.frm.inCodDiariaChave.value='';
    if( document.frm.inCodContratoChave )
        document.frm.inCodContratoChave.value='';
    if( document.frm.stTimestampChave )
        document.frm.stTimestampChave.value='';
    
    if( document.getElementById('nuValorDiariaFormatado') )
        document.getElementById('nuValorDiariaFormatado').innerHTML = "";
        
    if( document.getElementById('dtAto') )
        document.getElementById('dtAto').innerHTML = "";
        
    if( document.frm.btIncluirConcessao )
        document.frm.btIncluirConcessao.disabled = false;
        
    if( document.frm.btAlterarConcessao )
        document.frm.btAlterarConcessao.disabled = true;
        
    if( jQuery('#spnEmpenho') )
        jQuery('#spnEmpenho').html('');
        
    if(arguments[0] == null)
        ajaxJavaScriptSincrono('<?=$pgOcul?>?<?=Sessao::getId()?>', 'preencherPais', '<?=Sessao::getId()?>');
    else
        executaFuncaoAjax('preencherPais', '<?=Sessao::getId()?>');
}

function salvarFiltro(){
    document.frm.stRetorno.value = 'filtro';
    document.frm.submit();
}

function salvarLista(){
    document.frm.stRetorno.value = 'lista';
    document.frm.submit();
}

function salvarRecibo(){
    document.frm.stAcao.value = 'consultar';
    document.frm.stRetorno.value = 'recibo';
    document.frm.submit();
}



</script>
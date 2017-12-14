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
    * Data de Criação: 10/04/2007

    
    * @author Analista: Dagiane	Vieira	
    * @author Desenvolvedor: <Alex Cardoso>
    
    * @ignore
    
    $Id: JSExportacaoBancoBanPara.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
    
    * Casos de uso: uc-04.08.18
*/
?>
<script type="text/javascript">

 function ValidaOrgao(){
      var erro = false;
      var mensagem = "";
      stCampo = document.frm.inCodigoOrgao;
     if( stCampo ) {
         if( !isInt( stCampo.value ) ){
             erro = true;
             mensagem += "@Campo Código do Órgão inválido!("+stCampo.value+")";
         }
     }
     stCampo = document.frm.inCodigoOrgao;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Código do Órgão inválido!()";
        }
    }
     stCampo = document.frm.stDescricao;
    if( stCampo ) {
        if( trim( stCampo.value ) == "" ){
            erro = true;
            mensagem += "@Campo Descrição inválido!()";
        }
    }
     selecionaTodosSelect(document.frm.inCodLotacaoSelecionados);
    if(     document.frm.inCodLotacaoSelecionados ) {
    var inLength =     document.frm.inCodLotacaoSelecionados.options.length;
        if( ( inLength == 1 && trim(    document.frm.inCodLotacaoSelecionados.options[inLength - 1].value) == '') || inLength == 0 ){
            erro = true;
            mensagem += "@Campo Lotação inválido!()";
        }
    }
     selecionaTodosSelect(document.frm.inCodLocalSelecionados);
     if( (ifila) < fila.length ) {
         erro = true;
         mensagem += 'Aguarde todos os processos concluírem.';
     }
     if( erro ){ 
          alertaAviso(mensagem,'form','erro','PHPSESSID=cd74cc43184dea11aba1e7c9a00090b7&iURLRandomica=20080410140440.227', '../');
     }
     return !erro;
 }

 function limpaFormularioOrgao(){
    if( document.frm.inCodigoOrgao )
        document.frm.inCodigoOrgao.value='';
    if( document.frm.stDescricao )
        document.frm.stDescricao.value='';
    if( document.frm.inCodLotacaoSelecionados && document.frm.inCodLotacaoDisponiveis )
        passaItem(document.frm.inCodLotacaoSelecionados,document.frm.inCodLotacaoDisponiveis, 'tudo');
    if( document.frm.inCodLocalSelecionados && document.frm.inCodLocalDisponiveis )
        passaItem(document.frm.inCodLocalSelecionados,document.frm.inCodLocalDisponiveis, 'tudo');
    if( document.frm.btIncluirOrgao )
         document.frm.btIncluirOrgao.disabled = false;
    if( document.frm.btAlterarOrgao )
         document.frm.btAlterarOrgao.disabled = true;
 }
 
</script>
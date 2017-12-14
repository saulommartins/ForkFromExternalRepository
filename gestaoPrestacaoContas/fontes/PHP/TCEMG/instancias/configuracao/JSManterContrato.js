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
	* Arquivo JavaScript utilizado no Form de manutenção de contratos
	* Data de Criação   : 21/02/2014

	* @author Analista      Sergio Luiz dos Santos
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: JSManterContrato.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

?>
<script type="text/javascript">

function ValidaContrato()
{
    if( Valida() ){
        var erro = false;
        var mensagem = "";
        
        if (document.getElementById('inVeiculo')) {
           stCampo = document.getElementById('inVeiculo');
           if( trim( stCampo.value ) == "" ){
               erro = true;
               mensagem += "@Campo Veículo de Publicação não selecionado!().";
           }
        }
        
        if (document.getElementById('spnLista')) {
            stCampo = document.getElementById('spnLista');
            if( trim( stCampo.innerHTML ) == "" ){
                erro = true;
                mensagem += "@Campo Empenho não selecionado!().";
            }
        }
        
        if (document.getElementById('spnFornecedor')) {
            stCampo = document.getElementById('spnFornecedor');
            if( trim( stCampo.innerHTML ) == "" ){
                erro = true;
                mensagem += "@Campo Fornecedor não selecionado!().";
            }
        }
        
        if (document.getElementById('inCodEntidadeModalidadeTxt')) {
            stCampo = document.getElementById('inCodEntidadeModalidadeTxt');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Entidade Modalidade não selecionado!().";
            }
        }
        
        if (document.getElementById('inCodOrgaoModalidadeTxt')) {
            stCampo = document.getElementById('inCodOrgaoModalidadeTxt');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Orgão Modalidade não selecionado!().";
            }
        }
        
        if (document.getElementById('inCodUnidadeModalidadeTxt')) {
            stCampo = document.getElementById('inCodUnidadeModalidadeTxt');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Unidade Modalidade não selecionado!().";
            }
        }
        
        if (document.getElementById('stTipoProcesso')) {
            stCampo = document.getElementById('stTipoProcesso');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Tipo de Processo não selecionado!().";
            }
        }
        
        if (document.getElementById('inNumProcesso')) {
            stCampo = document.getElementById('inNumProcesso');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Número do Processo inválido!().";
            }
        }
        
        if (document.getElementById('stExercicioProcesso')) {
            stCampo = document.getElementById('stExercicioProcesso');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Exercício Processo inválido!().";
            }
        }
        
        if (document.getElementById('stFormaFornecimento')) {
            stCampo = document.getElementById('stFormaFornecimento');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Forma de Fornecimento ou Regime de Execução inválido!().";
            }
        }
        
        if (document.getElementById('stFormaPagamento')) {
            stCampo = document.getElementById('stFormaPagamento');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Forma de Pagamento inválido!().";
            }
        }
        
        if (document.getElementById('stFormaPrazo')) {
            stCampo = document.getElementById('stFormaPrazo');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Prazo de Execução inválido!().";
            }
        }
        
        if (document.getElementById('stFormaMulta')) {
            stCampo = document.getElementById('stFormaMulta');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Multa Rescisória inválido!().";
            }
        }
	
	if (document.getElementById('stMultaInadimplemento')) {
            stCampo = document.getElementById('stMultaInadimplemento');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Multa Inadimplemento inválido!().";
            }
        }
        
        if (document.getElementById('stGarantia')) {
            stCampo = document.getElementById('stGarantia');
            if( trim( stCampo.value ) == "" ){
                erro = true;
                mensagem += "@Tipo de Garantia Contratual não selecionado!().";
            }
        }
        
        if( erro ){ 
             alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
        }
        else{
           Salvar();
        }
    }
}
</script>

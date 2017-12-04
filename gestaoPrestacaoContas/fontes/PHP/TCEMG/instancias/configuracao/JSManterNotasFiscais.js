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
    * Página de JavaScript
    * Data de Criação   : 05/02/2014
    
    
    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira
    
    * @package URBEM
    * @subpackage 
    
    * @ignore 
    
    $Id: JSManterNotasFiscais.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

?>
<script type="text/javascript">


function ValidaNF(){
     var erro = false;
     var mensagem = "";
     
     if (document.getElementById('stTipoDocto')) {
	stCampo = document.getElementById('stTipoDocto');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Tipo Docto Fiscal não selecionado!().";
	}
     }
     
     if (document.getElementById('inCodEntidade')) {
	stCampo = document.getElementById('inCodEntidade');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Entidade não selecionado!().";
	}
     }
     
     if (document.getElementById('dtEmissao')) {
	stCampo = document.getElementById('dtEmissao');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Data de Emissão inválido!().";
	}
     }
     
     if (document.getElementById('inNumeroNF') && !document.getElementById('inChave')) {
	stCampo = document.getElementById('inNumeroNF');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Número do Docto Fiscal inválido!().";
	}
     }
          
     if (document.getElementById('inNumSerie') && !document.getElementById('inChave')) {
	stCampo = document.getElementById('inNumSerie');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Série do Docto Fiscal inválido!().";
	}
     }
     
     if (document.getElementById('inChave')) {
	stCampo = document.getElementById('inChave');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Chave de Acesso inválido!().";
	}
     }
     
     if (document.getElementById('inChaveMunicipal')) {
	stCampo = document.getElementById('inChaveMunicipal');
	if( trim( stCampo.value ) == "" ){
	    erro = true;
	    mensagem += "@Campo Chave de Acesso Municipal inválido!().";
	}
     }

    if (jQuery('#hdnVlAssociadoTotal').val() != '') {
        var nuTotalNf = jQuery('#nuTotalNf').val().replace(".", "").replace(",", ".");
        if (jQuery('#hdnVlAssociadoTotal').val() != nuTotalNf) {
            erro = true;
            mensagem += "@A soma dos valores associados deve ser igual ao valor líquido da nota fiscal!().";
        }
    }

    if (jQuery('#nuTotalNf').val() == '') {
        erro = true;
        mensagem += "@Campo Valor Total Docto Fiscal não informado!().";
    }

    if (jQuery('#nuVlDesconto').val() == '') {
        erro = true;
        mensagem += "@Campo Valor Desconto Docto Fiscal não informado!().";
    }

    if( erro ){ 
        alertaAviso(mensagem,'form','erro','<?=Sessao::getId();?>', '../');
    } else {
	   Salvar();
    }
}

</script>

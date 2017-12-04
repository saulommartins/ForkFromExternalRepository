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
    * Arquivo de Java Script do Inciar Processo Fiscal
    * Data de Criação: 29/07/2008

    
    * @author Analista: Heleno Menezes dos Santos
    * @author Desenvolvedor: Janilson Mendes P. da Silva
    
    * @package URBEM
    * @subpackage

    * @ignore
       
    * Casos de uso: 
    
    $Id:$
*/
?>
<script type="text/javascript">
    incluirDescontos = function(){

	if(document.frm.txtDocumentos.value != ''){
		montaParametrosGET( 'incluirDescontos', '', true); 
	} else{
		alertaAviso('Documento inválido!()','form','erro','<?=Sessao::getId()?>');
	}
    }

    limparDescontos = function(){
    	document.frm.txtDocumentos.value  = "";
	document.frm.cmbDocumentos.value  = "";
    }
</script>

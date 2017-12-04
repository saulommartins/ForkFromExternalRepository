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
	* Arquivo JavaScript utilizado no Form de manutenção de rescisão de contratos
	* Data de Criação   : 06/05/2014

	* @author Analista      Silvia Martins Silva
	* @author Desenvolvedor Michel Teixeira

	* @package URBEM
	* @subpackage

	* @ignore

	$Id: JSRescindirContrato.js 62838 2015-06-26 13:02:49Z diogo.zarpelon $
*/

?>
<script type="text/javascript">

function ValidaRescindir()
{
    if( Valida() ){
        var erro = false;
        var mensagem = "";
        
        if (document.getElementById('dtRescisao')) {	    
            var dataRescisao = (document.getElementById('dtRescisao').value).split("/");  
	    var dtRescisao = new Date(dataRescisao[2], dataRescisao[1]-1, dataRescisao[0]);
	    var dataInicial = (document.getElementById('dtInicioContrato').value).split("/");  
	    var dtInicial = new Date(dataInicial[2], dataInicial[1]-1, dataInicial[0]);
	    
	    if ( dtRescisao <= dtInicial ){
		    erro = true;
		    mensagem += "@Data da Rescisão Anterior ou Igual a Data Inicial do Contrato!().";    
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

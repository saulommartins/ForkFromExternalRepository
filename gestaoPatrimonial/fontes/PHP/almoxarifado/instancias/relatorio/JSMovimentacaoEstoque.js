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
    * Página de JavaScript de Movimentação de Estoque
    * Data de Criação   : 13/08/2007


    * @author Analista      : Gelson W. Gonçalves
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * Casos de uso: uc-03.03.24
*/

/*
$Log$
Revision 1.1  2007/08/13 21:58:58  hboaventura
uc_03-03-24



*/
?>

<script type="text/javascript">

function Salvar(){
	if( Valida() ){
		if( $('stTipoRelatorio').value == 'A' )
		{
        	document.frm.submit();
        }
        else{
        	if( $('stDataSaldo').value != '' ){
	        	document.frm.submit();        		
        	}
        	else{
	        	alertaAviso('Campo Situação até inválido!()','form','erro','<?php echo Sessao::getId() ?>', '../');
        	}
        }
    }
    
}


</script>


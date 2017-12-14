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
<?
/**
    * Data de Criação: 28/09/2007

    
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura 
    
    * @package URBEM
    * @subpackage 
    
    $Revision: 27451 $
    $Name$
    $Author: hboaventura $
    $Date: 2008-01-09 17:43:37 -0200 (Qua, 09 Jan 2008) $
    
    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/10/05 12:59:35  hboaventura
inclusão dos arquivos



*/
?>
<script>

	function Limpar()
	{		
		ajaxJavaScript( 'OCManterTransferirBem.php?<?php echo Sessao::getId(); ?>','limparBens' );	
	}

	function LimparCodigos()
	{
		//limpa os campos cod
		$('inCodBemInicio').value = '';
		$('inCodBemFim').value = '';
		
		//limpa os campos text
		$('stNomBemInicio').innerHTML = '&nbsp;';
		$('stNomBemFim').innerHTML = '&nbsp;';
	}
        
    function selecionarTodos(){
        var cont = 0;

        while(cont < document.frm.elements.length){
           var namee = document.frm.elements[cont].name;
           if( (document.frm.elements[cont].type == 'checkbox') && ( document.frm.elements[cont].name != 'boTodos') && ( namee.match('boTransferir')) ){
                if(document.frm.boTodos.checked == true){
                    document.frm.elements[cont].checked = true;
                }
                else{
                    document.frm.elements[cont].checked = false;
                }
           }
           cont++;
        }
    }

</script>          

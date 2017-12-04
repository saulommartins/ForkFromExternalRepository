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
    * Data de Criação: 27/02/2008

    
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira
    
    * @package URBEM
    * @subpackage 
        
    
    * Casos de uso: 
*/

/*
$Log$

*/
?>
<script type="text/javascript" >
	function Limpar()
	{
		var span = document.getElementById('spanPeriodicidade');
		span.innerHTML = "<input name=\"stPeriodoInicial\" tabindex=\"1\" onkeypress=\"JavaScript:return inteiro( event );\" onkeyup=\"JavaScript:mascaraData(this, event);\" onblur=\"JavaScript:if( !verificaData( this ) ){ this.value = '';}if  ( oldValue != this.value ) {ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/processamento/OCPeriodicidade.php?PHPSESSID=dbf03758d16fd083650e56fae2adebbb&iURLRandomica=20080228085718.606&inIdComponente=&stPeriodoInicial='+this.value+'&stTipo=inicial','preenchePeriodo'); }\" onfocus=\"JavaScript: oldValue = this.value;\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\"><span>  até   </span><input name=\"stPeriodoFinal\" tabindex=\"1\" onkeypress=\"JavaScript:return inteiro( event );\" onkeyup=\"JavaScript:mascaraData(this, event);\" onblur=\"JavaScript:if( !verificaData( this ) ){ this.value = '';}if  ( oldValue != this.value ) {ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/processamento/OCPeriodicidade.php?PHPSESSID=dbf03758d16fd083650e56fae2adebbb&iURLRandomica=20080228085718.606&inIdComponente=&stPeriodoFinal='+this.value+'&stTipo=final','preenchePeriodo'); }\" onfocus=\"JavaScript: oldValue = this.value;\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\"> ";			
		
		limpaFormulario();
	}
</script>

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
  * Página de 
  * Data de criação : 03/11/2005


    * @author Analista: 
    * @author Programador: Fernando Zank Correa Evangelista 




    $Revision: 28470 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-10 16:25:04 -0300 (Seg, 10 Mar 2008) $

    Caso de uso: uc-03.02.11
**/

/*
$Log$
Revision 1.5  2006/07/06 14:07:05  diego
Retirada tag de log com erro.

Revision 1.4  2006/07/06 12:11:27  diego


*/
?>
<script>

function buscaValor(variavel){
    var stTraget = document.frm.target;
    document.frm.target = "oculto";
    var stAction = document.frm.action;
    document.frm.stCtrl.value = variavel;
    document.frm.action = 'OCFichaPatrimonial.php?+<?=Sessao::getId();?>';
    document.frm.submit();
    document.frm.action = stAction;
    document.frm.target = stTraget;
}

function Limpar()
{
		var span = document.getElementById('spanPeriodicidade');
		span.innerHTML = "<input name=\"stPeriodoInicial\" tabindex=\"1\" onkeypress=\"JavaScript:return inteiro( event );\" onkeyup=\"JavaScript:mascaraData(this, event);\" onblur=\"JavaScript:if( !verificaData( this ) ){ this.value = '';}if  ( oldValue != this.value ) {ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/processamento/OCPeriodicidade.php?PHPSESSID=dbf03758d16fd083650e56fae2adebbb&iURLRandomica=20080228085718.606&inIdComponente=&stPeriodoInicial='+this.value+'&stTipo=inicial','preenchePeriodo'); }\" onfocus=\"JavaScript: oldValue = this.value;\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\"><span>  até   </span><input name=\"stPeriodoFinal\" tabindex=\"1\" onkeypress=\"JavaScript:return inteiro( event );\" onkeyup=\"JavaScript:mascaraData(this, event);\" onblur=\"JavaScript:if( !verificaData( this ) ){ this.value = '';}if  ( oldValue != this.value ) {ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/processamento/OCPeriodicidade.php?PHPSESSID=dbf03758d16fd083650e56fae2adebbb&iURLRandomica=20080228085718.606&inIdComponente=&stPeriodoFinal='+this.value+'&stTipo=final','preenchePeriodo'); }\" onfocus=\"JavaScript: oldValue = this.value;\" maxlength=\"10\" size=\"11\" align=\"left\" type=\"text\"> ";			
		
		var grupo = document.getElementById('inCodGrupo');
			
		var tam = grupo.options.length;
        while (tam >= 1) {
            grupo.options[tam] = null;
            tam = tam - 1 ;
        }	
			
		var especie = document.getElementById('inCodEspecie');
		
		var tam = especie.options.length;
        while (tam >= 1) {
            especie.options[tam] = null;
            tam = tam - 1 ;
        }	
		
		limpaFormulario();
}
</script>

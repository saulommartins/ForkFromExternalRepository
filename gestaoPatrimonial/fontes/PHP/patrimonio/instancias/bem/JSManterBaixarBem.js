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
    * Data de Criação: 18/09/2007

    
    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura 
    
    * @package URBEM
    * @subpackage 
    
    $Revision: 25675 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-27 09:57:24 -0300 (Qui, 27 Set 2007) $
    
    * Casos de uso: uc-03.01.06
*/

/*
$Log$
Revision 1.1  2007/09/27 12:57:24  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:04  hboaventura
Adicionando ao repositório


*/
?>
<script>
	
    function Limpar()
	{		
		ajaxJavaScript( 'OCManterBaixarBem.php?<?php echo Sessao::getId(); ?>','limparBens' );
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
    
    function consultarBensBaixados(inCodigo, stDataBaixa, stMotivo) {
    
        var link    = "<?= CAM_GP_PAT_INSTANCIAS.'bem/LSManterBaixarBemPopUp.php'; ?>";
        var sessao  = "<?= Sessao::getId();?>";
        var params  = "&inCodigo="+inCodigo+"&stDataBaixa="+stDataBaixa+"&stMotivo="+stMotivo;
        
        // Abre a listagem que exibe os bens baixados
        abrePopUp(link, '', '', '', sessao+params, '','');
        
    }
    
</script>          
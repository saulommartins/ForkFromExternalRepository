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
    * Página de Javascript do Férias
    * Data de Criação: 22/09/2006

    
    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Vandré Miguel Ramos

    * @ignore

    $Revision: 16858 $
    $Name$
    $Author: vandre $
    $Date: 2006-10-17 10:16:42 -0300 (Ter, 17 Out 2006) $

    * Casos de uso: uc-01.04.02
*/

/*
$Log$
Revision 1.1  2006/10/17 13:16:42  vandre
Criação do componente para buscar normas.


*/
?>
<script type="text/javascript">
function abrePopUpNorma(){
    var width  = 800;
    var height = 550;
    var sSessao      = "<?=Sessao::getId()?>";
    if(document.frm.inCodTipoNormaTxt.value!= ""){
    	var sFiltros     = "&inCodTipoNormaTxt="+document.frm.inCodTipoNormaTxt.value+"&nomForm=frm&campoNum=stCodNorma&campoNom=stNorma&boComponente=true&boRetornaNumExercicio=true";
    	var sUrlFrames   = "<?=CAM_GA_NORMAS_POPUPS;?>normas/FLNorma.php?"+sSessao+sFiltros;
    	window.open( sUrlFrames, "popUpNormas", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    } else{
    	alertaAviso("@Informe o Tipo da Norma",'form','erro',sSessao);
    	document.frm.inCodTipoNormaTxt.focus();	
    }
}

</script>


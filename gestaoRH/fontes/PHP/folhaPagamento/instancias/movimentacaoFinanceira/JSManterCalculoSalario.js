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
    * JavaScript
    * Data de Criação: 29/11/2005


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-08 09:46:10 -0300 (Seg, 08 Out 2007) $

    * Casos de uso: uc-04.05.09
*/

/*
$Log$
Revision 1.7  2007/10/08 12:39:47  souzadl
Bug#10318#

Revision 1.6  2006/09/28 16:48:13  vandre
Adicionada nova opção para consulta de ficha financeira.

Revision 1.5  2006/08/08 17:43:13  vandre
Adicionada tag log.

*/
?>

<script type="text/javascript">

function processarPopUp(inCodContrato,inRegistro,stNumCgm,stNomCgm){
    var width  = 800;
    var height = 550;
    var sFiltros     = "&inCodContrato="+inCodContrato+"&inRegistro="+inRegistro+"&inCodConfiguracao=1&nom_cgm="+stNomCgm+"&numcgm="+stNumCgm;
    var sUrlConsulta = "FMConsultarFichaFinanceira.php?";
    var sSessao      = "<?=Sessao::getId()?>";
    var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FRConsultarFichaFinanceira.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
    if( Valida() ){
        window.open( sUrlFrames, "popUpConsultaFichaFinanceira", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    }	
}



</script>

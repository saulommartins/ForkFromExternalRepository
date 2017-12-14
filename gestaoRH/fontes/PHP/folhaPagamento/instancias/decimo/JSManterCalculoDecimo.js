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
    * Página de JS de Calculo de Décimo
    * Data de Criação: 13/11/2006


    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2006-11-13 15:51:20 -0200 (Seg, 13 Nov 2006) $

    * Casos de uso: uc-04.05.11
*/


?>
<script type="text/javascript"> 

function processarPopUp(inCodContrato,inRegistro,stNumCgm,stNomCgm){
	var width  = 800;
    var height = 550;
    var sFiltros     = "&inCodContrato="+inCodContrato+"&inRegistro="+inRegistro+"&inCodConfiguracao=3&nom_cgm="+stNomCgm+"&numcgm="+stNumCgm;
    var sUrlConsulta = "FMConsultarFichaFinanceira.php?";
    var sSessao      = "<?=Sessao::getId()?>";
    var sUrlFrames   = "<?=CAM_GRH_FOL_POPUPS;?>movimentacaoFinanceira/FRConsultarFichaFinanceira.php?sUrlConsulta="+sUrlConsulta+sSessao+sFiltros;
    if( Valida() ){
        window.open( sUrlFrames, "popUpConsultaFichaFinanceira", "width="+width+",height="+height+",resizable=1,scrollbars=1,left=0,top=0" );
    }	
}

</script>

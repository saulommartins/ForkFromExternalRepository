<?php
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
?>
<?php
/**
    * Oculto de processamento do componente IPopUpCodigoEstagio
    * Data de Criação: 05/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2006-11-28 14:28:00 -0200 (Ter, 28 Nov 2006) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function validarCodigoEstagio()
{
    $stJs = "";

    include_once(CAM_GRH_EST_MAPEAMENTO."TEstagioEstagiarioEstagio.class.php");
    $obTEstagioEstagiarioEstagio = new TEstagioEstagiarioEstagio();
    $stFiltro = " WHERE numero_estagio = '".$_GET['inCodigoEstagio']."'";
    $obTEstagioEstagiarioEstagio->recuperaTodos($rsEstagiario,$stFiltro);
    if ( $rsEstagiario->getNumLinhas() < 0 ) {
        $stJs .= "f.inCodigoEstagio.value = '';\n";
        $stJs .= "alertaAviso('O código estágio ".$_GET['inCodigoEstagio']." é inválido.','form','erro','".Sessao::getId()."');\n";
        $stCGM = "&nbsp;";
    } else {
        include_once(CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php");
        $obTCGMCGM = new TCGMCGM();
        $obTCGMCGM->setDado("numcgm",$rsEstagiario->getCampo("cgm_estagiario"));
        $obTCGMCGM->recuperaPorChave($rsCGM);
        $stCGM = $rsCGM->getCampo("numcgm")."-".$rsCGM->getCampo("nom_cgm");
    }

    $stJs.= "d.getElementById('stCGM').innerHTML = '".$stCGM."';";

    return $stJs;
}

switch ( $request->get('stCtrl') ) {
    case "validarCodigoEstagio":
        $stJs = validarCodigoEstagio();
    break;
}

if ($stJs) {
    echo $stJs;
}
?>

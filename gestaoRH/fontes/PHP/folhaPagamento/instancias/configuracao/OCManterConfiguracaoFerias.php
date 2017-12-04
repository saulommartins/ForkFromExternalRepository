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
    * Oculto de Configuração Férias
    * Data de Criação: 09/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-05 17:06:51 -0300 (Ter, 05 Jun 2007) $

    * Casos de uso: uc-04.05.32
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoEvento.class.php'                               );
include_once ( CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoDecimoEvento.class.php'                         );

function preencherEvento()
{
    $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
    $inCodTipo = $_GET['inCodTipo'];
    $stNatureza= $_GET['stNatureza'];
    $obTFolhaPagamentoEvento->recuperaEventoCodigoNatureza( $rsEvento, $_GET['inCodigoEvento'] , $stNatureza, true );
    $stInner = "stInner_".$inCodTipo;
    if ( $rsEvento->getNumLinhas() > 0 ) {
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '".$rsEvento->getCampo('descricao')."';  \n";
        $stJs .= "f.HdnstInner_Cod_".$inCodTipo.".value = '".$rsEvento->getCampo('cod_evento')."';  \n";
    } else {
        $stJs .= "f.stInner_Cod_".$inCodTipo.".value = '';                  \n";
        $stJs .= "d.getElementById('".$stInner."').innerHTML = '&nbsp;';    \n";
    }

    return $stJs;
}

switch ($_GET["stCtrl"]) {
    case 'preencherEvento':
        $stJs .= preencherEvento();
    break;
}

if ($stJs != "") {
    echo $stJs;
}

?>

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
    * Oculto
    * Data de Criação: 08/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Camargo Finger

    * @ignore

    * Casos de uso: uc-04.05.61
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = 'ManterEventoDescontoExterno';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

/*
 * Preenche todos os eventos ao carregar o formulário
 */
function preencherInnerEventos()
{
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventosDescontoExterno.class.php"                  );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php"                                              );

    $obTFolhaPagamentoConfiguracaoEventosDescontoExterno = new TFolhaPagamentoConfiguracaoEventosDescontoExterno;
    $obTFolhaPagamentoConfiguracaoEventosDescontoExterno->recuperaTodos( $rsConfiguracaoEventosDescontoExterno );

    if ( $rsConfiguracaoEventosDescontoExterno->getNumLinhas() != -1 ) {

        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;
        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosDescontoExterno->getCampo("evento_base_previdencia");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoBasePrevidencia = $rsEvento->getCampo("descricao");
        $nuEventoBasePrevidencia = $rsEvento->getCampo("codigo");

        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosDescontoExterno->getCampo("evento_desconto_previdencia");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoDescontoPrevidencia = $rsEvento->getCampo("descricao");
        $nuEventoDescontoPrevidencia = $rsEvento->getCampo("codigo");

        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosDescontoExterno->getCampo("evento_base_irrf");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoBaseIRRF = $rsEvento->getCampo("descricao");
        $nuEventoBaseIRRF = $rsEvento->getCampo("codigo");

        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosDescontoExterno->getCampo("evento_desconto_irrf");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoDescontoIRRF = $rsEvento->getCampo("descricao");
        $nuEventoDescontoIRRF = $rsEvento->getCampo("codigo");

        $stJs .= "f.inCodigoEventoBasePrevidencia.value = '".trim($nuEventoBasePrevidencia)."';                                           \n";
        $stJs .= "d.getElementById('inCampoInnerEventoBasePrevidencia').innerHTML = '".trim($stDescricaoEventoBasePrevidencia)."';        \n";

        $stJs .= "f.inCodigoEventoDescontoPrevidencia.value = '".trim($nuEventoDescontoPrevidencia)."';                                   \n";
        $stJs .= "d.getElementById('inCampoInnerEventoDescontoPrevidencia').innerHTML = '".trim($stDescricaoEventoDescontoPrevidencia)."';\n";

        $stJs .= "f.inCodigoEventoBaseIRRF.value = '".trim($nuEventoBaseIRRF)."';                                                         \n";
        $stJs .= "d.getElementById('inCampoInnerEventoBaseIRRF').innerHTML    = '".trim($stDescricaoEventoBaseIRRF)."';                   \n";

        $stJs .= "f.inCodigoEventoDescontoIRRF.value = '".trim($nuEventoDescontoIRRF)."';                                                 \n";
        $stJs .= "d.getElementById('inCampoInnerEventoDescontoIRRF').innerHTML    = '".trim($stDescricaoEventoDescontoIRRF)."';           \n";
    } else {
        $stJs = '';
    }

    return $stJs;
}

/*
 * Preenche um evento quando solicitado
 */
function preencherInnerEvento()
{
    $nuCodigoEvento = trim($_GET['nuCodigoEvento']);
    $stNomeCampoEvento = trim($_GET['stNomeCampoEvento']);

    if ( !empty($nuCodigoEvento) && !empty($stNomeCampoEvento) ) {
        include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoEvento.class.php"                                            );

        $obRFolhaPagamentoEvento = new RFolhaPagamentoEvento;
        $obRFolhaPagamentoEvento->setCodigo         ( $nuCodigoEvento          );
        $obRFolhaPagamentoEvento->setNatureza       ( "I"                      );
        $obRFolhaPagamentoEvento->setEventoSistema  ( 'true'                   );
        $obRFolhaPagamentoEvento->listarEvento      ( $rsEvento                );

        if ( $rsEvento->getNumLinhas() != -1 ) {
            $stJs  = "f.inCodigoEvento".$stNomeCampoEvento.".value = '".trim($rsEvento->getCampo("codigo"))."';                                         \n";
            $stJs .= "d.getElementById('inCampoInnerEvento".$stNomeCampoEvento."').innerHTML = '".trim($rsEvento->getCampo("descricao"))."';            \n";
        } else {
            $stJs  = "f.inCodigoEvento".$stNomeCampoEvento.".value = '';                                                                                \n";
            $stJs .= "f.inCodigoEvento".$stNomeCampoEvento.".focus();                                                                                   \n";
            $stJs .= "d.getElementById('inCampoInnerEvento".$stNomeCampoEvento."').innerHTML = '&nbsp;';                                                \n";
            $stJs .= "alertaAviso('Informar neste campo apenas eventos informativos e automáticos de sistema.','form','erro','".Sessao::getId()."');        \n";
        }

    } else {
        $stJs  = "f.inCodigoEvento".$stNomeCampoEvento.".value = '';                                                    \n";
        $stJs .= "f.inCodigoEvento".$stNomeCampoEvento.".focus();                                                       \n";
        $stJs .= "d.getElementById('inCampoInnerEvento".$stNomeCampoEvento."').innerHTML = '&nbsp;';                    \n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "preencherInnerEventos":
        $stJs = preencherInnerEventos();
        break;
    case "preencherInnerEvento":
        $stJs = preencherInnerEvento();
        break;
}

if ($stJs != "") {
    echo $stJs;
}

?>

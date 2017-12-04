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
    * Titulo do arquivo (Ex.: "Formulario de configuração do IPERS")
    * Data de Criação   : 23/06/2008

    * @author Rafael Garbin

    * Casos de uso: uc-04.05.66

    $Id: OCConfiguracaoIpers.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

$stPrograma = "ConfiguracaoIpers";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

/*
* Preenche todos os eventos ao carregar o formulário
*/
function preencherInnerEventos()
{
    $stNomeCampoEvento = trim($_GET['stNomeCampoEvento']);

    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoEventosDescontoExterno.class.php" );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEvento.class.php" );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoIpe.class.php" );

    $obTFolhaPagamentoConfiguracaoIpe = new TFolhaPagamentoConfiguracaoIpe();
    $stOrdem = " configuracao_ipe.vigencia::varchar||configuracao_ipe.cod_configuracao::varchar DESC";
    $stOrdem .= " LIMIT 1";
    $obTFolhaPagamentoConfiguracaoIpe->recuperaRelacionamento( $rsConfiguracaoEventosIpe, $stFiltro="", $stOrdem );

    if ( $rsConfiguracaoEventosIpe->getNumLinhas() != -1 ) {

        $obTFolhaPagamentoEvento = new TFolhaPagamentoEvento;

        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosIpe->getCampo("cod_evento_base");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoBase = $rsEvento->getCampo("descricao");
        $inCodEventoBase       = $rsEvento->getCampo("codigo");

        $stFiltro = " WHERE cod_evento = ".$rsConfiguracaoEventosIpe->getCampo("cod_evento_automatico");
        $obTFolhaPagamentoEvento->recuperaTodos( $rsEvento, $stFiltro );
        $stDescricaoEventoDesconto = $rsEvento->getCampo("descricao");
        $inCodEventoDesconto       = $rsEvento->getCampo("codigo");

        $stJs = "f.inCodigoEventoBaseIPERS.value = '".$inCodEventoBase."' \n";
        $stJs .= "$('stEventoBaseIPERS').innerHTML = '".trim($stDescricaoEventoBase)."'; \n";

        $stJs .= "f.inCodigoEventoDescontoIPERS.value = '".$inCodEventoDesconto."' \n";
        $stJs .= "$('stEventoDescontoIPERS').innerHTML = '".trim($stDescricaoEventoDesconto)."'; \n";
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
            #$stJs  = "f.inCodigoEvento".$stNomeCampoEvento.".value = '".trim($rsEvento->getCampo("codigo"))."';                                         \n";
            #$stJs .= "d.getElementById('inCampoInnerEvento".$stNomeCampoEvento."').innerHTML = '".trim($rsEvento->getCampo("descricao"))."';            \n";
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

$stJs = "";
switch ($_REQUEST['stCtrl']) {
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

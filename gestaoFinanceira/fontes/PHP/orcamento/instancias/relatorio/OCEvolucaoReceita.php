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
    * Página de Oculto para Relatório da Evolução da Receita
    * Data de Criação  : 15/07/2008

    * @author Leopoldo Braga Barreiro

    * Casos de uso : uc-02.01.37

    * $Id: $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRelatorioBalanceteDespesa.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
include_once( CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php" );

$stCtrl = $_GET["stCtrl"] ? $_GET["stCtrl"] : $_POST["stCtrl"];

$stPrograma = "EvolucaoReceita";
$pgOcul = "OC" . $stPrograma . ".php";

$stJs = "";

switch ($_REQUEST['stCtrl']) {

    case "mascaraClassificacaoFiltroInicial":
        if ( trim( $_POST['stCodEstruturalInicial'] ) != "" ) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['stCodEstruturalInicial'] );
            $stJs = "f.stCodEstruturalInicial.value = '" . $arMascClassificacao[1] . "';\n";
        }
    break;

    case "mascaraClassificacaoFiltroFinal":
        if ( trim( $_POST['stCodEstruturalFinal'] ) != "" ) {
            $arMascClassificacao = Mascara::validaMascaraDinamica( $_POST['stMascClassificacao'] , $_POST['stCodEstruturalFinal'] );
            $stJs = "f.stCodEstruturalFinal.value = '" . $arMascClassificacao[1] . "';\n";
        }
    break;

}

if (strlen($stJs) > 0) {
    SistemaLegado::executaFrameOculto( $stJs );
}

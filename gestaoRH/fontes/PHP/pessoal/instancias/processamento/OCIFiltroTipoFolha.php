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
    * Oculto do componente IFiltroTipoFolha
    * Data de Criação: 24/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: tiago $
    $Date: 2007-09-26 18:57:40 -0300 (Qua, 26 Set 2007) $

    * Casos de uso: uc-04.00.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function gerarSpanTipoFolha()
{
    switch ($_GET["inCodConfiguracao"]) {
        case "0":
            $stHtml = montaSpanComplementar($stEval);
            break;
         case "1":
            if ($_GET["boMostraAcumularSalCompl"]) {
                $stHtml = montaSpanSalario($stEval);
            }
            break;
        case "2":
            if ($_GET["boDesdobramento"] == "true" and (strpos($_GET["stDesdobramentoFolhas"],"F")!== false or strpos($_GET["stDesdobramentoFolhas"],"T")!== false) ) {
                $stHtml = montaSpanFerias($stEval);
            }
            break;
        case "3":
            if ($_GET["boDesdobramento"] == "true" and (strpos($_GET["stDesdobramentoFolhas"],"D")!== false or strpos($_GET["stDesdobramentoFolhas"],"T")!== false) ) {
                $stHtml = montaSpanDecimo($stEval);
            }
            break;
        case "4":
            if ($_GET["boDesdobramento"] == "true" and (strpos($_GET["stDesdobramentoFolhas"],"R")!== false or strpos($_GET["stDesdobramentoFolhas"],"T")!== false) ) {
                $stHtml = montaSpanRescisao($stEval);
            }
            break;
        default:
            $stHtml = "";
            $stEval = "";
            break;
    }
    $stJs .= "d.getElementById('spnTipoFolha').innerHTML = '$stHtml';\n";
    $stJs .= "f.hdnTipoFolha.value = '$stEval';\n";

    return $stJs;
}
function montaSpanSalario($stEval)
{
    $obAcumularSalCompl = new CheckBox;
    $obAcumularSalCompl->setName        ( "boAcumularSalCompl"                              );
    $obAcumularSalCompl->setRotulo       ( "Acumular com as Complementares?"                          );
    $obAcumularSalCompl->setTitle          ( "Selecione a caso deseja acumular."      );
    $obAcumularSalCompl->setValue        ( true                                                      );

    $obFormulario = new Formulario;
    $obFormulario->addComponente        ( $obAcumularSalCompl                           );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function montaSpanComplementar($stEval)
{
    $rsPeriodoMovimentacao = new recordset;
    if ($_GET["inCodMes"] != "" and $_GET["inAno"] != "") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_GET["inCodMes"]);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_GET["inAno"]);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);
    }
    $rsFolhaComplementar = new recordset;
    if ($rsPeriodoMovimentacao->getNumLinhas() == 1) {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoComplementar.class.php");
        $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar;
        $stFiltro = " AND complementar.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
        $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsFolhaComplementar,$stFiltro);
    }

    $obCmbFolhaComplementar = new Select;
    $obCmbFolhaComplementar->setRotulo                    ( "Folha Complementar"                                  	);
    $obCmbFolhaComplementar->setTitle                     ( "Selecione a folha complementar."                     	);
    $obCmbFolhaComplementar->setName                      ( "inCodComplementar"                                   	);
    $obCmbFolhaComplementar->setId                        ( "inCodComplementar"                                   	);
    $obCmbFolhaComplementar->setValue                     ( $inCodComplementar                                   	);
    $obCmbFolhaComplementar->setStyle                     ( "width: 200px"                                        	);
    $obCmbFolhaComplementar->addOption                    ( "", "Selecione"                                       	);
    $obCmbFolhaComplementar->setCampoID                   ( "[cod_complementar]"                                  	);
    $obCmbFolhaComplementar->setCampoDesc                 ( "[cod_complementar]"                                  	);
    $obCmbFolhaComplementar->preencheCombo                ( $rsFolhaComplementar                                  	);
    $obCmbFolhaComplementar->setNull					  ( false 												  	);

    if ($_GET["boMostraAcumularSalCompl"]) {
        $obAcumularSalCompl = new CheckBox;
        $obAcumularSalCompl->setName        ( "boAcumularSalCompl"                              );
        $obAcumularSalCompl->setRotulo       ( "Acumular com a Salário?"                          );
        $obAcumularSalCompl->setTitle          ( "Selecione a caso deseja acumular."      );
        $obAcumularSalCompl->setValue        ( true                                                      );
    }
    $obFormulario = new Formulario;
    $obFormulario->addComponente             ( $obCmbFolhaComplementar                               	);
    if ($_GET["boMostraAcumularSalCompl"]) {
        $obFormulario->addComponente         ($obAcumularSalCompl           		);
    }
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function montaSpanFerias($stEval)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoDesdobramento.class.php");
    $obTFolhaPagamentoConfiguracaoDesdobramento = new TFolhaPagamentoConfiguracaoDesdobramento();
    $stFiltro = "   WHERE cod_configuracao = ".$_GET["inCodConfiguracao"];
    $obTFolhaPagamentoConfiguracaoDesdobramento->recuperaTodos($rsConfiguracaoDesdobramento,$stFiltro," ORDER BY descricao");

    $obCmbDesdobramentos = new Select;
    $obCmbDesdobramentos->setRotulo                  ( "Desdobramento"                              				);
    $obCmbDesdobramentos->setTitle                   ( "Selecione o desdobramento."                             	);
    $obCmbDesdobramentos->setName                    ( "stDesdobramento"                                   			);
    $obCmbDesdobramentos->addOption                  ( "Selecione", "Selecione"                                              );
    $obCmbDesdobramentos->setCampoID                 ( "[desdobramento]"                                  	        );
    $obCmbDesdobramentos->setCampoDesc               ( "[descricao]"                                  	            );
    $obCmbDesdobramentos->preencheCombo              ( $rsConfiguracaoDesdobramento                                 );
    $obCmbDesdobramentos->setStyle                   ( "width: 250px"                                               );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbDesdobramentos );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function montaSpanDecimo($stEval)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoDesdobramento.class.php");
    $obTFolhaPagamentoConfiguracaoDesdobramento = new TFolhaPagamentoConfiguracaoDesdobramento();
    $stFiltro = "   WHERE cod_configuracao = ".$_GET["inCodConfiguracao"];
    $obTFolhaPagamentoConfiguracaoDesdobramento->recuperaTodos($rsConfiguracaoDesdobramento,$stFiltro," ORDER BY descricao");

    $obCmbDesdobramentos = new Select;
    $obCmbDesdobramentos->setRotulo          ( "Desdobramento"               );
    $obCmbDesdobramentos->setTitle           ( "Selecione o desdobramento."  );
    $obCmbDesdobramentos->setName            ( "stDesdobramento"             );
    $obCmbDesdobramentos->addOption          ( "", "Selecione"               );
    $obCmbDesdobramentos->setCampoID         ( "[desdobramento]"             );
    $obCmbDesdobramentos->setCampoDesc       ( "[descricao]"                 );
    $obCmbDesdobramentos->preencheCombo      ( $rsConfiguracaoDesdobramento  );
    $obCmbDesdobramentos->setStyle           ( "width: 250px"                );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbDesdobramentos );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

function montaSpanRescisao($stEval)
{
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoDesdobramento.class.php");
    $obTFolhaPagamentoConfiguracaoDesdobramento = new TFolhaPagamentoConfiguracaoDesdobramento();
    $stFiltro = "   WHERE cod_configuracao = ".$_GET["inCodConfiguracao"];
    $obTFolhaPagamentoConfiguracaoDesdobramento->recuperaTodos($rsConfiguracaoDesdobramento,$stFiltro," ORDER BY descricao");

    $obCmbDesdobramentos = new Select;
    $obCmbDesdobramentos->setRotulo          ( "Desdobramento"              );
    $obCmbDesdobramentos->setTitle           ( "Selecione o desdobramento." );
    $obCmbDesdobramentos->setName            ( "stDesdobramento"            );
    $obCmbDesdobramentos->addOption          ( "", "Selecione"              );
    $obCmbDesdobramentos->setCampoID         ( "[desdobramento]"            );
    $obCmbDesdobramentos->setCampoDesc       ( "[descricao]"                );
    $obCmbDesdobramentos->preencheCombo      ( $rsConfiguracaoDesdobramento );
    $obCmbDesdobramentos->setStyle           ( "width: 250px"               );

    $obFormulario = new Formulario;
    $obFormulario->addComponente( $obCmbDesdobramentos );
    $obFormulario->montaInnerHtml();
    $obFormulario->obJavaScript->montaJavaScript();
    $stEval = $obFormulario->obJavaScript->getInnerJavaScript();
    $stEval = str_replace("\n","",$stEval);

    return $obFormulario->getHTML();
}

switch ($_GET["stCtrl"]) {
    case "gerarSpanTipoFolha":
        $stJs = gerarSpanTipoFolha();
        break;
}
if ($stJs) {
    echo $stJs;
}
?>

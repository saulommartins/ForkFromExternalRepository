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
    * Página de Formulário do Exportação Remessa Banrisul
    * Data de Criação: 10/06/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.08.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php" );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulRemuneracao.class.php" );
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAConsignacaoBanrisulLiquido.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "CreditoBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('gerarSpan','stSituacao');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
$obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

$obComboSituacao = new Select;
$obComboSituacao->setRotulo                         ( "Cadastro"                                            );
$obComboSituacao->setTitle                          ( "Selecione o cadastro para filtro."                   );
$obComboSituacao->setName                           ( "stSituacao"                                          );
$obComboSituacao->setValue                          ( "ativos"                                              );
$obComboSituacao->setStyle                          ( "width: 200px"                                        );
$obComboSituacao->addOption                         ( "", "Selecione"                                       );
$obComboSituacao->addOption                         ( "ativos", "Ativos"                                    );
$obComboSituacao->addOption                         ( "aposentados", "Aposentados"                          );
$obComboSituacao->addOption                         ( "pensionistas", "Pensionistas"                        );
$obComboSituacao->addOption                         ( "todos", "Todos"                                      );
$obComboSituacao->setNull                           ( false                                                 );
$obComboSituacao->obEvento->setOnChange("montaParametrosGET('gerarSpan','stSituacao');");

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obSpnAtivosAposentadosPensionistas = new Span();
$obSpnAtivosAposentadosPensionistas->setId("spnAtivosAposentadosPensionistas");

$obTIMAConsignacaoBanrisulRemuneracao = new TIMAConsignacaoBanrisulRemuneracao();
$obTIMAConsignacaoBanrisulRemuneracao->recuperaRelacionamento($rsConfiguracaoRemuneracao);
Sessao::write("rsConfiguracaoRemuneracao", $rsConfiguracaoRemuneracao);

$obTIMAConsignacaoBanrisulLiquido = new TIMAConsignacaoBanrisulLiquido();
$obTIMAConsignacaoBanrisulLiquido->recuperaRelacionamento($rsConfiguracaoLiquido);
Sessao::write("rsConfiguracaoLiquido", $rsConfiguracaoLiquido);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true											);
$obFormulario->addComponente($obComboSituacao);
$obFormulario->addSpan($obSpnCadastro);
$obFormulario->addSpan($obSpnAtivosAposentadosPensionistas);
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

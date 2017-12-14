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
    * Página de Filtro do Relatório de IRRF
    * Data de Criação : 07/08/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore

    $Revision: 30547 $
    $Name$
    $Autor: $
    $Date: 2008-01-07 12:05:54 -0200 (Seg, 07 Jan 2008) $

    * Casos de uso: uc-04.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                	);
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploAgencia.class.php"                              	);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioIRRF";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once($pgJS);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                                  		);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                               );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                          ( "stCaminho"                                           );
$obHdnCaminho->setValue                         ( CAM_GRH_FOL_INSTANCIAS."relatorio/".$pgProc );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                              );
$obBtnLimpar->setTipo                           ( "button"                                              );
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFormulario', '', true);"    );

$obBtnOk = new OK;

$obChkTodos = new Radio();
$obChkTodos->setName("stSituacao");
$obChkTodos->setRotulo("Cadastro");
$obChkTodos->setLabel("Todos");
$obChkTodos->setValue("todos");
$obChkTodos->setNull(false);
$obChkTodos->setChecked(true);
$obChkTodos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkAtivos = new Radio();
$obChkAtivos->setName("stSituacao");
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setValue("ativos");
$obChkAtivos->setTitle("Selecione o tipo de cadastro para emissão do comprovante de rendimentos");
$obChkAtivos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkAposentados = new Radio();
$obChkAposentados->setName("stSituacao");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setValue("aposentados");
$obChkAposentados->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setName("stSituacao");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setValue("pensionistas");
$obChkPensionistas->obEvento->setOnChange("montaParametrosGET('gerarSpanPensionistas','stSituacao');");

$obChkRescindidos = new Radio();
$obChkRescindidos->setName("stSituacao");
$obChkRescindidos->setLabel("Rescindidos");
$obChkRescindidos->setValue("rescindidos");
$obChkRescindidos->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$arChkCadastro = array($obChkTodos,$obChkAtivos,$obChkRescindidos,$obChkAposentados,$obChkPensionistas);

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

// filtro de competencia
$obIFiltroCompetencia = new IFiltroCompetencia;

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setMostraDesdobramento( true );
$obIFiltroTipoFolha->setValorPadrao("1");

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setName    ( "stOrdenacao"            );
$obCmbOrdenacao->setValue   ( 'A'                      );
$obCmbOrdenacao->setRotulo  ( "Ordenação"              );
$obCmbOrdenacao->setTitle   ( "Selecione a ordenação." );
$obCmbOrdenacao->addOption  ( "A","Alfabética"         );
$obCmbOrdenacao->addOption  ( "N","Numérica"           );
$obCmbOrdenacao->setStyle   ( "width: 250px"           );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                        ( $obHdnCaminho                                         );
$obFormulario->addHidden                        ( $obHdnCtrl                                            );
$obFormulario->addHidden                        ( $obHdnTipoFiltroExtra,true							);
$obFormulario->addTitulo                        ( "Seleção do Filtro"									);
$obFormulario->agrupaComponentes($arChkCadastro);
$obFormulario->addSpan($obSpnCadastro);
$obIFiltroCompetencia->geraFormulario           ( $obFormulario                                         );
$obIFiltroTipoFolha->geraFormulario				( $obFormulario											);
$obFormulario->addComponente                    ( $obCmbOrdenacao );
$obFormulario->defineBarra                      ( array($obBtnOk,$obBtnLimpar)                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

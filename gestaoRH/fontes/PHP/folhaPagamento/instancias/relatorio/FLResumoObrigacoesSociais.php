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
    * Página de Filtro do Recibo de Férias
    * Data de Criação: 02/10/2006

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: alex $
    $Date: 2007-12-17 10:07:04 -0200 (Seg, 17 Dez 2007) $

    * Casos de uso: uc-04.05.64
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);

//Define o nome dos arquivos PHP
$stPrograma = "ResumoObrigacoesSociais";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";
$jsOnload   = "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl");
$obHdnCtrl->setValue( $stCtrl );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
$obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

$obIFiltroTipoFolha = new IFiltroTipoFolha();

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaPrevidencia.class.php");
$obTFolhaPagamentoPrevidenciaPrevidencia = new TFolhaPagamentoPrevidenciaPrevidencia();
$stFiltro = " AND tipo_previdencia = 'o'";
$obTFolhaPagamentoPrevidenciaPrevidencia->recuperaRelacionamento($rsPrevidencia,$stFiltro," descricao");
$obCmbPrevidencia = new Select;
$obCmbPrevidencia->setRotulo                        ( "Previdência");
$obCmbPrevidencia->setTitle                         ( "Selecione a previdência para emissão do relatório.");
$obCmbPrevidencia->setName                          ( "inCodPrevidencia");
$obCmbPrevidencia->setStyle                         ( "width: 200px");
$obCmbPrevidencia->setNull(false);
$obCmbPrevidencia->addOption                        ( "","Selecione");
$obCmbPrevidencia->setCampoId("cod_previdencia");
$obCmbPrevidencia->setCampoDesc("descricao");
$obCmbPrevidencia->preencheCombo($rsPrevidencia);

$obRdoAtivo = new Radio;
$obRdoAtivo->setName                        ( "stSituacao"                                           );
$obRdoAtivo->setRotulo                      ( "Situação Servidor"                                    );
$obRdoAtivo->setLabel                       ( "Ativos"                                               );
$obRdoAtivo->setTitle                       ( "Selecione a situação do servidor para filtro."        );
$obRdoAtivo->setValue                       ( "A"                                                );
$obRdoAtivo->setChecked(true);
$obRdoAtivo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obRdoRescindido = new Radio;
$obRdoRescindido->setName                      ( "stSituacao"                                             );
$obRdoRescindido->setRotulo                    ( "Situação Servidor"                                      );
$obRdoRescindido->setLabel                     ( "Rescindidos"                                            );
$obRdoRescindido->setTitle                     ( "Selecione a situação do servidor para filtro."          );
$obRdoRescindido->setValue                     ( "R"                                            );
$obRdoRescindido->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obRdoInativo = new Radio;
$obRdoInativo->setName                      ( "stSituacao"                                               );
$obRdoInativo->setRotulo                    ( "Situação Servidor"                                        );
$obRdoInativo->setLabel                     ( "Aposentados"                                              );
$obRdoInativo->setTitle                     ( "Selecione a situação do servidor para filtro."            );
$obRdoInativo->setValue                     ( "P"                                                  );
$obRdoInativo->obEvento->setOnChange("montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');");

$obRdoPensionista = new Radio;
$obRdoPensionista->setName                  ( "stSituacao"                                       );
$obRdoPensionista->setRotulo                ( "Situação Servidor"                                );
$obRdoPensionista->setLabel                 ( "Pensionistas"                                     );
$obRdoPensionista->setTitle                 ( "Selecione a situação do servidor para filtro."    );
$obRdoPensionista->setValue                 ( "E"                                      );
$obRdoPensionista->obEvento->setOnChange("montaParametrosGET('gerarSpanPensionistas','stSituacao');");

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setName                         ( "stOrdenacao"                  );
$obCmbOrdenacao->setValue                        ( 'A'                            );
$obCmbOrdenacao->setRotulo                       ( "Ordenação"                    );
$obCmbOrdenacao->setTitle                        ( "Selecione a ordenação."       );
$obCmbOrdenacao->addOption                       ( "A","Alfabética"               );
$obCmbOrdenacao->addOption                       ( "N","Numérica"                 );
$obCmbOrdenacao->setStyle                        ( "width: 250px"                 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "telaPrincipal"                 );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','stSituacao,stTipoFiltro,inCodAtributo',true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnTipoFiltroExtra, true );
$obFormulario->addTitulo( "Seleção do Filtro" );
$obFormulario->agrupaComponentes( array($obRdoAtivo,$obRdoRescindido,$obRdoInativo,$obRdoPensionista) );
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obIFiltroTipoFolha->geraFormulario($obFormulario);
$obFormulario->addSpan($obSpnCadastro);
$obFormulario->addComponente($obCmbPrevidencia);
$obFormulario->addComponente( $obCmbOrdenacao );
$obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

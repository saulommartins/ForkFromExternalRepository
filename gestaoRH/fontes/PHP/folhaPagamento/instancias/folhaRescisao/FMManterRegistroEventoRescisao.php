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
    * Página de Formulário do Registro de Evento de Rescisão
    * Data de Criação: 17/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: melo $
    $Date: 2007-07-24 14:47:15 -0300 (Ter, 24 Jul 2007) $

    * Casos de uso: uc-04.05.54
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_COMPONENTES."IBscEvento.class.php"                                           );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterRegistroEventoRescisao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php?".Sessao::getId();
$pgJS       = "JS".$stPrograma.".js";
include_once($pgJS);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao      = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::write('arEventosExcluidos',array());

$stLocation  = $pgList.'?'.Sessao::getId()."&stAcao=".$stAcao."&stTipoFiltro=".$_REQUEST['stTipoFiltro'];
switch ($_REQUEST['stTipoFiltro']) {
    case "contrato":
    case "cgm_contrato":
        $stLocation .= "&inContrato=".$_REQUEST['inContrato'];
        break;
    case "cargo":
        $stLocation .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLocation .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLocation .= "&inCodCargo=".$_REQUEST['inCodCargo'];
        $stLocation .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        break;
    case "funcao":
        $stLocation .= "&inCodRegime=".$_REQUEST['inCodRegime'];
        $stLocation .= "&inCodSubDivisao=".$_REQUEST['inCodSubDivisao'];
        $stLocation .= "&inCodEspecialidade=".$_REQUEST['inCodEspecialidade'];
        $stLocation .= "&inCodFuncao=".$_REQUEST['inCodFuncao'];
        break;
    case "lotacao":
        $stLocation .= "&inCodLotacao=".$_REQUEST['inCodLotacao'];
        break;
    case "local":
        $stLocation .= "&inCodLocal=".$_REQUEST['inCodLocal'];
        break;
    case "padrao":
        $stLocation .= "&inCodPadrao=".$_REQUEST['inCodPadrao'];
        break;
}

$jsOnload   = "executaFuncaoAjax('processarForm','&inCodContrato=".$_REQUEST['inCodContrato']."');";

//include_once($pgJS);

#sessao->transf = "";
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obHdnContrato =  new Hidden;
$obHdnContrato->setName                         ( "inCodContrato"                                                       );
$obHdnContrato->setValue                        ( $_REQUEST['inCodContrato']                                            );

$obHdnRegistro =  new Hidden;
$obHdnRegistro->setName                         ( "inRegistro"                                                          );
$obHdnRegistro->setValue                        ( $_REQUEST['inRegistro']                                               );

$obHdnCargo =  new Hidden;
$obHdnCargo->setName                            ( "inCodCargo"                                                          );
$obHdnCargo->setValue                           ( $_REQUEST['inCodCargo']                                               );

$obHdnSubDivisao =  new Hidden;
$obHdnSubDivisao->setName                       ( "inCodSubDivisao"                                                     );
$obHdnSubDivisao->setValue                      ( $_REQUEST['inCodSubDivisao']                                          );

$obHdnEspecialidade =  new Hidden;
$obHdnEspecialidade->setName                    ( "inCodEspecialidade"                                                  );
$obHdnEspecialidade->setValue                   ( $_REQUEST['inCodEspecialidade']                                       );

$obHdnPeriodoMovimentacao =  new Hidden;
$obHdnPeriodoMovimentacao->setName              ( "inCodPeriodoMovimentacao"                                            );
$obHdnPeriodoMovimentacao->setValue             ( $rsUltimaMovimentacao->getCampo("cod_periodo_movimentacao")           );

$obLblContrato = new Label;
$obLblContrato->setName                         ( "stContrato"                                                          );
$obLblContrato->setRotulo                       ( "Matrícula"                                                           );
$obLblContrato->setValue                        ( $_REQUEST['inRegistro']                                               );

$obLblDataRescisao = new Label;
$obLblDataRescisao->setName                         ( "dtRescisao"                                                          );
$obLblDataRescisao->setRotulo                       ( "Data Rescisão"                                                           );
$obLblDataRescisao->setValue                        ( $_REQUEST['dtRescisao']                                               );

$obLblCausa = new Label;
$obLblCausa->setName                         ( "stCausa"                                                          );
$obLblCausa->setRotulo                       ( "Causa"                                                           );
$obLblCausa->setValue                        ( $_REQUEST['inNumCausa']."-".$_REQUEST['stDescricaoCausa']                                               );

$obLblCGM = new Label;
$obLblCGM->setName                              ( "stCGM"                                                               );
$obLblCGM->setRotulo                            ( "CGM"                                                                 );
$obLblCGM->setValue                             ( $_REQUEST['inNumCGM'] ."-".$_REQUEST['stNomCGM']                      );

$obIBscEvento = new IBscEvento;
$obIBscEvento->obBscInnerEvento->setNullBarra   ( false                                                                 );
//$obIBscEvento->obTxtValor->setNullBarra         ( false                                                                 );
$obIBscEvento->setInformarValorQuantidade       ( true                                                                  );
$obIBscEvento->setSugerirValorQuantidade(true);
Sessao::write('obIBscEvento',$obIBscEvento);

$obTxtDesdobramento = new TextBox;
$obTxtDesdobramento->setRotulo                  ( "Desdobramento"                                                       );
$obTxtDesdobramento->setName                    ( "stDesdobramento"                                                     );
$obTxtDesdobramento->setId                      ( "stDesdobramento"                                                     );
$obTxtDesdobramento->setValue                   ( $stDesdobramento                                                      );
$obTxtDesdobramento->setTitle                   ( "Selecione o desdobramento."                                          );
$obTxtDesdobramento->setSize                    ( 5                                                                     );
$obTxtDesdobramento->setMaxLength               ( 5                                                                     );
$obTxtDesdobramento->setNullBarra               ( false                                                                 );

$obCmbDesdobramento = new Select;
$obCmbDesdobramento->setName                    ( "stCmbDesdobramento"                                                  );
$obCmbDesdobramento->setId                      ( "stCmbDesdobramento"                                                  );
$obCmbDesdobramento->setValue                   ( $stDesdobramento                                                      );
$obCmbDesdobramento->setRotulo                  ( "Desdobramento"                                                       );
$obCmbDesdobramento->setTitle                   ( "Selecione o desdobramento."                                          );
$obCmbDesdobramento->setNullBarra               ( false                                                                 );
$obCmbDesdobramento->addOption                  ( "", "Selecione"                                                       );
$obCmbDesdobramento->addOption                  ( "S", "Saldo Salário"                                                       );
$obCmbDesdobramento->addOption                  ( "A", "Aviso Prévio Indenizado"                                                       );
$obCmbDesdobramento->addOption                  ( "V", "Férias Vencidas"                                                       );
$obCmbDesdobramento->addOption                  ( "P", "Férias Proporcionais"                                                       );
$obCmbDesdobramento->addOption                  ( "D", "13º Salário"                                                       );
$obCmbDesdobramento->setStyle                   ( "width: 250px"                                                        );

$arIncluirAlterar = array($obIBscEvento->obBscInnerEvento,
$obIBscEvento->obTxtValor,
$obIBscEvento->obTxtQuantidade,
$obIBscEvento->obTxtQuantidadeParcelas,
$obIBscEvento->obSpnDadosEvento,
$obIBscEvento->obLblTextoComplementar,
$obTxtDesdobramento,
$obCmbDesdobramento);

Sessao::write('arIncluirAlterar',$arIncluirAlterar);

$obSpnEventosCadastrados = new Span;
$obSpnEventosCadastrados->setId                 ( "spnEventosCadastrados"                                               );

$obSpnEventosBase = new Span;
$obSpnEventosBase->setId                        ( "spnEventosBase"                                                      );

$obSpnBotoes = new Span;
$obSpnBotoes->setId                             ( "spnBotoes"                                                           );

$obHdnEvalBotoes =  new HiddenEval;
$obHdnEvalBotoes->setName                       ( "hdnEvalBotoes"                                                       );
$obHdnEvalBotoes->setValue                      ( $hdnEvalBotoes                                                        );

$obHdnOkRetorno = new hidden();
$obHdnOkRetorno->setName("stOkRetorno");

$obBtnOkFiltro = new Ok;
$obBtnOkFiltro->setName("okFiltro");
$obBtnOkFiltro->setValue("OK/Filtro");
$obBtnOkFiltro->obEvento->setOnClick("salvarOkFiltro();");

$obBtnOkLista = new Ok;
$obBtnOkLista->setName("okLista");
$obBtnOkLista->setValue("OK/Lista");
$obBtnOkLista->obEvento->setOnClick("salvarOkLista();");

$obBtnCancelar = new Button;
$obBtnCancelar->setName                         ( 'cancelar'                                            );
$obBtnCancelar->setValue                        ( 'Cancelar'                                            );
$obBtnCancelar->obEvento->setOnClick            ( "Cancelar('".$stLocation."');"                        );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new FormularioAbas;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addHidden                        ( $obHdnContrato                                                        );
$obFormulario->addHidden                        ( $obHdnCargo                                                           );
$obFormulario->addHidden                        ( $obHdnSubDivisao                                                      );
$obFormulario->addHidden                        ( $obHdnEspecialidade                                                   );
$obFormulario->addHidden                        ( $obHdnRegistro                                                        );
$obFormulario->addHidden                        ( $obHdnPeriodoMovimentacao                                             );
$obFormulario->addTitulo                        ( "Dados da Matrícula"                                                  );
$obFormulario->addComponente                    ( $obLblContrato                                                        );
$obFormulario->addComponente                    ( $obLblCGM                                                             );
$obFormulario->addComponente                    ( $obLblDataRescisao );
$obFormulario->addComponente                    ( $obLblCausa );
$obFormulario->addAba                           ( "Eventos"                                                             );
$obFormulario->addTitulo("Dados do Evento");
$obIBscEvento->geraFormulario                   ( $obFormulario                                                         );
$obFormulario->addComponenteComposto            ( $obTxtDesdobramento,$obCmbDesdobramento                               );
$obFormulario->addSpan                          ( $obSpnBotoes                                                          );
$obFormulario->addHidden                        ( $obHdnEvalBotoes,true                                                 );
$obFormulario->addSpan                          ( $obSpnEventosCadastrados                                              );
$obFormulario->addAba                           ( "Base"                                                                );
$obFormulario->addSpan                          ( $obSpnEventosBase                                                     );
$obFormulario->addHidden($obHdnOkRetorno);
$obFormulario->defineBarra(array($obBtnOkFiltro,$obBtnOkLista,$obBtnCancelar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

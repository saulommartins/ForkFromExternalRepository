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
    * Filtro de Relatório de Férias vencidas
    * Data de Criação: 04/07/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @ignore
    * Casos de uso: uc-04.04.46
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "RelatorioFeriasVencidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma."Filtro.php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS   );

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                      ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
$obForm->setTarget                                      ( "oculto"                                  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                                     ( "stCtrl"                                                  );
$obHdnCtrl->setValue                                    ( $stCtrl                                                   );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                                  ( "stCaminho"                                                 );
$obHdnCaminho->setValue                                 ( CAM_GRH_PES_INSTANCIAS."relatorio/OCRelatorioFeriasVencidas.php" );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisao();

$dtDataVencimento = new Data;
$dtDataVencimento->setName                              ( 'dtDataVencimento'               );
$dtDataVencimento->setValue                             ( date('d/m/Y')                    );
$dtDataVencimento->setTitle                             ( 'Informe a data para o filtro. ' );
$dtDataVencimento->setNull                              ( false                            );
$dtDataVencimento->setRotulo                            ( 'Data do vencimento.'            );
$dtDataVencimento->setSize                              ( 10                               );

$obCmbOrdenacaoLotacao = new Select;
$obCmbOrdenacaoLotacao->setName                         ( "stOrdenacaoLotacao"                  );
$obCmbOrdenacaoLotacao->setValue                        ( 'A'                                   );
$obCmbOrdenacaoLotacao->setRotulo                       ( "Ordenação"                           );
$obCmbOrdenacaoLotacao->setTitle                        ( "Selecione a ordenação para lotação." );
$obCmbOrdenacaoLotacao->addOption                       ( "A","Alfabética"                      );
$obCmbOrdenacaoLotacao->addOption                       ( "N","Numérica"                        );
$obCmbOrdenacaoLotacao->setStyle                        ( "width: 250px"                        );

$obChkOrdenacaoLotacao = new CheckBox;
$obChkOrdenacaoLotacao->setName                         ( 'boOrdenacaoLotacao' );
$obChkOrdenacaoLotacao->setId                           ( 'boOrdenacaoLotacao' );
$obChkOrdenacaoLotacao->setValue                        ( 't'                  );
$obChkOrdenacaoLotacao->setChecked                      ( false                );
$obChkOrdenacaoLotacao->setLabel                        ( 'Lotação' );

$obCmbOrdenacaoRegime = new Select;
$obCmbOrdenacaoRegime->setName                          ( "stOrdenacaoRegime"                  );
$obCmbOrdenacaoRegime->setValue                         ( 'A'                                  );
$obCmbOrdenacaoRegime->setRotulo                        ( ""                                   );
$obCmbOrdenacaoRegime->setTitle                         ( "Selecione a ordenação para regime." );
$obCmbOrdenacaoRegime->addOption                        ( "A","Alfabética"                     );
$obCmbOrdenacaoRegime->addOption                        ( "N","Numérica"                       );
$obCmbOrdenacaoRegime->setStyle                         ( "width: 250px"                       );

$obChkOrdenacaoRegime = new CheckBox;
$obChkOrdenacaoRegime->setName                          ( 'boOrdenacaoRegime' );
$obChkOrdenacaoRegime->setId                            ( 'boOrdenacaoRegime' );
$obChkOrdenacaoRegime->setValue                         ( 't'                 );
$obChkOrdenacaoRegime->setChecked                       ( false               );
$obChkOrdenacaoRegime->setLabel                         ( 'Regime' );

$obCmbOrdenacaoCGM = new Select;
$obCmbOrdenacaoCGM->setName                             ( "stOrdenacaoCGM"             );
$obCmbOrdenacaoCGM->setValue                            ( 'A'                          );
$obCmbOrdenacaoCGM->setRotulo                           ( ""                           );
$obCmbOrdenacaoCGM->setTitle                            ( "Selecione a ordenação CGM." );
$obCmbOrdenacaoCGM->addOption                           ( "A","Alfabética"             );
$obCmbOrdenacaoCGM->addOption                           ( "N","Numérica"               );
$obCmbOrdenacaoCGM->setStyle                            ( "width: 250px"               );

$obChkOrdenacaoCGM = new CheckBox;
$obChkOrdenacaoCGM->setName                             ( 'boOrdenacaoCGM' );
$obChkOrdenacaoCGM->setId                               ( 'boOrdenacaoCGM' );
$obChkOrdenacaoCGM->setValue                            ( 't'              );
$obChkOrdenacaoCGM->setChecked                          ( false            );
$obChkOrdenacaoCGM->setLabel                            ( 'CGM' );

$obBtnLimparCampos = new Button;
$obBtnLimparCampos->setName                             ( "btnLimparCampos"             );
$obBtnLimparCampos->setValue                            ( "Limpar"                      );
$obBtnLimparCampos->setTipo                             ( "button"                      );
$obBtnLimparCampos->obEvento->setOnClick                ( "buscaValor('limparCampos');" );
$obBtnLimparCampos->setDisabled                         ( false                         );
$obBtnOK = new Ok;
$botoesForm  = array ( $obBtnOK , $obBtnLimparCampos );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                                  ( $obForm                                                           );
$obFormulario->addHidden                                ( $obHdnCtrl                                                        );
$obFormulario->addHidden                                ( $obHdnCaminho                                                     );
$obFormulario->addTitulo                                ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addTitulo                                ( 'Seleção do Filtro'                                               );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->addComponente                            ( $dtDataVencimento                                                 );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoLotacao, $obChkOrdenacaoLotacao )           );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoRegime , $obChkOrdenacaoRegime  )           );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoCGM    , $obChkOrdenacaoCGM     )           );
$obFormulario->defineBarra                              ($botoesForm);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

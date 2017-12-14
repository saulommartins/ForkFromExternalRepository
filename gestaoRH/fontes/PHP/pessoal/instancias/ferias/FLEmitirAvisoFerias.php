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
    * Filtro do Relatório de Aviso de Férias
    * Data de Criação: 25/05/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: André Machado

    * @ignore
    * Casos de uso: uc-04.04.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "EmitirAvisoFerias";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";

$arContratos = array();
Sessao::write('arContratos', $arContratos);
Sessao::write('valida_ativos_cgm', 'false');
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DO FORM
$obForm = new Form;
//$obForm->setAction                                      ( CAM_FW_POPUPS."relatorio/OCRelatorio.php" );
//$obForm->setTarget                                      ( "oculto"                                  );
$obForm->setAction                                        ( $pgProc                                   );
$obForm->setTarget                                        ( "telaPrincipal"                           );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                                     ( "stCtrl"                                                  );
$obHdnCtrl->setValue                                    ( $stCtrl                                                   );

$obHdnEval = new HiddenEval;
$obHdnEval->setName                                     ( "stEval" );
$obHdnEval->setValue                                    ( "" );

$obhdnFiltro = new Hidden;
$obhdnFiltro->setName                                   ( 'hdnFiltro' );
$obhdnFiltro->setValue                                  ( $hdnFiltro  );

$obHdnSpans =  new HiddenEval;
$obHdnSpans->setName                                    ( "hdnSpans"                                                );
$obHdnSpans->setValue                                   ( ""                                                        );

$obHdnFiltrar =  new HiddenEval;
$obHdnFiltrar->setName                                  ( "hdnTipoFiltro"                                           );
$obHdnFiltrar->setValue                                 ( ""                                                        );

$obHdnCaminho = new Hidden;
$obHdnCaminho->setName                                  ( "stCaminho"                                               );
//$obHdnCaminho->setValue                                 ( CAM_GRH_PES_INSTANCIAS."relatorio/OCRelatorioHistoricoFerias.php" );
$obHdnCaminho->setValue                                 ( CAM_GRH_PES_INSTANCIAS."ferias/".$pgProc                  );

/// filtro de competencia
$obIFiltroCompetencia = new IFiltroCompetencia;

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setTodos();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisao();
$obIFiltroComponentes->setGeral(false);

//$dtDataLimite = new Data;
//$dtDataLimite->setName                                  ( 'dtDataLimite'                            );
//$dtDataLimite->setValue                                 ( date('d/m/Y')                             );
//$dtDataLimite->setTitle                                 ( 'Informe a data para o filtro. '          );
//$dtDataLimite->setRotulo                                ( 'Data Limite'                             );
//$dtDataLimite->setSize                                  ( 10                                        );

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

$obCmbOrdenacaoContrato = new Select;
$obCmbOrdenacaoContrato->setName                        ( "stOrdenacaoContrato"             );
$obCmbOrdenacaoContrato->setValue                       ( 'A'                          );
$obCmbOrdenacaoContrato->setRotulo                      ( ""                           );
$obCmbOrdenacaoContrato->setTitle                       ( "Selecione a ordenação da Matrícula do Servidor." );
$obCmbOrdenacaoContrato->addOption                      ( "A","Alfabética"             );
$obCmbOrdenacaoContrato->addOption                      ( "N","Numérica"               );
$obCmbOrdenacaoContrato->setStyle                       ( "width: 250px"               );

$obChkOrdenacaoContrato = new CheckBox;
$obChkOrdenacaoContrato->setName                        ( 'boOrdenacaoContrato' );
$obChkOrdenacaoContrato->setId                          ( 'boOrdenacaoContrato' );
$obChkOrdenacaoContrato->setValue                       ( 't'              );
$obChkOrdenacaoContrato->setChecked                     ( false            );
$obChkOrdenacaoContrato->setLabel                       ( 'Matrícula Servidor' );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                                  ( $obForm                                                           );
$obFormulario->addHidden                                ( $obHdnCtrl                                                        );
$obFormulario->addHidden                                ( $obhdnFiltro                                                      );
$obFormulario->addHidden                                ( $obHdnEval   ,true                                                );
$obFormulario->addHidden                                ( $obHdnCaminho                                                     );
$obFormulario->addTitulo                                ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addTitulo                                ( 'Seleção do Filtro'                                               );
$obIFiltroCompetencia->geraFormulario                   ( $obFormulario                                                     );
$obIFiltroComponentes->geraFormulario                   ( $obFormulario                                                     );
//$obFormulario->addComponente                            ( $dtDataLimite                                                     );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoLotacao, $obChkOrdenacaoLotacao )           );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoRegime , $obChkOrdenacaoRegime  )           );
$obFormulario->agrupaComponentes                        ( array( $obCmbOrdenacaoContrato    , $obChkOrdenacaoContrato     )           );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

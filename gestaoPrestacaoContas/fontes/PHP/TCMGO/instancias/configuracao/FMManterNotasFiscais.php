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
/*
    * Formulário de Cadastro de Notas Fiscais
    * Data de Criação   : 17/09/2008

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( CAM_GPC_TGO_MAPEAMENTO."TTCMGOTipoNotaFiscal.class.php" );
$obTTCMGOTipoNotaFiscal = new TTCMGOTipoNotaFiscal;

$stOrder = " ORDER BY descricao ";
$obTTCMGOTipoNotaFiscal->recuperaTodos($rsTipoNota, "", $stOrder);

$stFiltroAux = Sessao::read('filtroAux');
$stLink = "&stExercicio=".$request->get('stExercicio').$stFiltroAux ;
//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnDataEmissao = new Hidden;
$obHdnDataEmissao->setName  ( "data_emissao" );
$obHdnDataEmissao->setValue ( $_REQUEST['data_emissao'] );

$obHdnCodNota = new Hidden;
$obHdnCodNota->setName ( "inCodNota" );
$obHdnCodNota->setValue ( $_REQUEST['inCodNota'] );

$obHdnInscEstadual = new Hidden;
$obHdnInscEstadual->setName ("insc_estadual");
$obHdnInscEstadual->setValue ( $_REQUEST['insc_estadual'] );

$obHdnVlTotal = new Hidden;
$obHdnVlTotal->setName ("nuVlTotal");
$obHdnVlTotal->setValue ( $_REQUEST['nuVlTotal'] );

$obTxtNota = new TextBox;
$obTxtNota->setName     ( "inNumNota"                                 );
$obTxtNota->setId       ( "inNumNota"                                 );
$obTxtNota->setValue    ( $_REQUEST['inNumNota']                      );
$obTxtNota->setRotulo   ( "Número do Docto Fiscal"                    );
$obTxtNota->setTitle    ( "Informe o número do Docto Fiscal     ."    );
$obTxtNota->setNull     ( true                        );
$obTxtNota->setInteiro  ( true                                        );
$obTxtNota->setSize     ( 10                                          );
$obTxtNota->setMaxLength( 10                                          );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setNull ( false );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade"            );
$obHdnCodEntidade->setValue ( $_REQUEST['cod_entidade'] );

$obCmbTipoNota = new Select;
$obCmbTipoNota->setName      ( "inCodTipoNota"             );
$obCmbTipoNota->setRotulo    ( "Tipo Docto Fiscal"         );
$obCmbTipoNota->setId        ( "stTipoDocto"               );
$obCmbTipoNota->setCampoId   ( "cod_tipo"                  );
$obCmbTipoNota->setCampoDesc ( "descricao"                 );
$obCmbTipoNota->addOption    ( '','Selecione'              );
$obCmbTipoNota->preencheCombo( $rsTipoNota                 );
$obCmbTipoNota->setNull      ( false                       );
if ($_REQUEST['stAcao'] == 'incluir') {
        $obCmbTipoNota->setValue     ( ''                          );
}

if (Sessao::getExercicio() > '2011') {
    $obCmbTipoNota->obEvento->setOnChange("montaParametrosGET('montaChave', 'stTipoDocto');");
    $obHdnCtrl->setValue ("montaChave");
}

$obTxtSerie = new TextBox;
$obTxtSerie->setName     ( "inNumSerie"                          );
$obTxtSerie->setId       ( "inNumSerie"                          );
$obTxtSerie->setValue    ( $_REQUEST['inNumSerie']               );
$obTxtSerie->setRotulo   ( "Série do Docto Fiscal"               );
$obTxtSerie->setTitle    ( "Informe a série do Docto Fiscal."    );
$obTxtSerie->setNull     ( true                        );
$obTxtSerie->setInteiro  ( false                                 );
$obTxtSerie->setSize     ( 8                                     );
$obTxtSerie->setMaxLength( 8                                     );

$obSpnChave = new Span();
$obSpnChave->setId( 'spnChave' );

$obTxtAIDF = new TextBox;
$obTxtAIDF->setName     ( "stAIFD"                    );
$obTxtAIDF->setId       ( "stAIDF"                    );
$obTxtAIDF->setValue    ( $_REQUEST['stAIDF']         );
$obTxtAIDF->setRotulo   ( "Número da AIDF"            );
$obTxtAIDF->setTitle    ( "Informe o número da AIDF." );
$obTxtAIDF->setNull     ( true                        );
$obTxtAIDF->setInteiro  ( false                       );
$obTxtAIDF->setSize     ( 18                          );
$obTxtAIDF->setMaxLength( 15                          );

$obDtEmissao = new Data;
$obDtEmissao->setName     ( "dtEmissao"                            );
$obDtEmissao->setId       ( "dtEmissao"                            );
$obDtEmissao->setRotulo   ( "Data de Emissão"                      );
$obDtEmissao->setValue    ( $_REQUEST['dtEmissao']                 );
$obDtEmissao->setTitle    ( 'Informe a data de emissão.'           );
$obDtEmissao->setNull     ( false                                  );
$obDtEmissao->setSize     ( 10                                     );
$obDtEmissao->setMaxLength( 10                                     );

$obTxtIncricaoMunicipal = new TextBox;
$obTxtIncricaoMunicipal->setName     ( "inNumInscricaoMunicipal"                   );
$obTxtIncricaoMunicipal->setId       ( "inNumInscricaoMunicipal"                   );
$obTxtIncricaoMunicipal->setValue    ( $_REQUEST['inNumInscricaoMunicipal']        );
$obTxtIncricaoMunicipal->setRotulo   ( "Inscrição Municipal"                       );
$obTxtIncricaoMunicipal->setTitle    ( "Informe o número da Inscrição Municipal."  );
$obTxtIncricaoMunicipal->setNull     ( true                                        );
$obTxtIncricaoMunicipal->setInteiro  ( true                                        );
$obTxtIncricaoMunicipal->setSize     ( 18                                          );
$obTxtIncricaoMunicipal->setMaxLength( 15                                          );

$obTxtIncricaoEstadual = new TextBox;
$obTxtIncricaoEstadual->setName     ( "inNumInscricaoEstadual"                   );
$obTxtIncricaoEstadual->setId       ( "inNumInscricaoEstadual"                   );
$obTxtIncricaoEstadual->setValue    ( $_REQUEST['inNumInscricaoEstadual']        );
$obTxtIncricaoEstadual->setRotulo   ( "Inscrição Estadual"                       );
$obTxtIncricaoEstadual->setTitle    ( "Informe o número da Inscrição Estadual."  );
$obTxtIncricaoEstadual->setNull     ( true                                       );
$obTxtIncricaoEstadual->setInteiro  ( true                                       );
$obTxtIncricaoEstadual->setSize     ( 18                                         );
$obTxtIncricaoEstadual->setMaxLength( 15                                         );

$obTxtVlNotaFiscal = new Numerico;
$obTxtVlNotaFiscal->setName     ( "nuVlNotaFiscal"            );
$obTxtVlNotaFiscal->setRotulo   ( "Valor do Docto Fiscal"     );
$obTxtVlNotaFiscal->setAlign    ( 'RIGHT'                     );
$obTxtVlNotaFiscal->setTitle    ( ""                          );
$obTxtVlNotaFiscal->setMaxLength( 19                          );
$obTxtVlNotaFiscal->setSize     ( 21                          );
$obTxtVlNotaFiscal->setValue    ( $_REQUEST['nuVlNotaFiscal'] );
$obTxtVlNotaFiscal->setNull     ( false                       );

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicioEmpenho"   );
$obTxtExercicio->setValue    ( Sessao::getExercicio() );
$obTxtExercicio->setRotulo   ( "Exercício"            );
$obTxtExercicio->setTitle    ( "Informe o exercício." );
$obTxtExercicio->setInteiro  ( false                  );
$obTxtExercicio->setNull     ( false                  );
$obTxtExercicio->setMaxLength( 4                      );
$obTxtExercicio->setSize     ( 5                      );

$obBscEmpenho = new BuscaInner;
$obBscEmpenho->setTitle            ( "Informe o número do empenho."  );
$obBscEmpenho->setRotulo           ( "**Número do Empenho"           );
$obBscEmpenho->setId               ( "stEmpenho"                     );
$obBscEmpenho->setValue            ( $_REQUEST['stEmpenho']          );
$obBscEmpenho->setMostrarDescricao ( true                            );
$obBscEmpenho->obCampoCod->setName ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setId   ( "numEmpenho"                    );
$obBscEmpenho->obCampoCod->setValue(  $numEmpenho                    );
$obBscEmpenho->obCampoCod->obEvento->setOnBlur( "montaParametrosGET('preencheInner','numEmpenho, inCodEntidade, stExercicioEmpenho, dtEmissao, inCodNota');" );
$obBscEmpenho->setFuncaoBusca("abrePopUp('".CAM_GF_EMP_POPUPS."empenho/FLEmpenho.php','frm','numEmpenho','stEmpenho','empenhoNotaFiscal&inCodEntidade='+document.frm.inCodEntidade.value + '&dtFinal='+document.frm.dtEmissao.value + '&dtEmissao='+document.frm.dtEmissao.value+'&stCampoExercicio=stExercicioEmpenho&stExercicioEmpenho='+document.frm.stExercicioEmpenho.value,'".Sessao::getId()."','800','550');");

if (Sessao::getExercicio() > 2010) {
    $obCmbLiquidacao = new Select;
    $obCmbLiquidacao->setName              ('cmbLiquidacao');
    $obCmbLiquidacao->setId                ('cmbLiquidacao');
    $obCmbLiquidacao->setRotulo            ('*Liquidação');
    $obCmbLiquidacao->setTitle             ('Selecione a liquidação.');
    $obCmbLiquidacao->addOption            ('', 'Selecione');
    $obCmbLiquidacao->setCampoId           ('mixCombo');
    $obCmbLiquidacao->setCampoDesc         ('mixCombo');
    $obCmbLiquidacao->setNull              (true);
    $obCmbLiquidacao->setStyle             ('width: 220px');
}

$obTxtVlAssociado = new Numerico;
$obTxtVlAssociado->setName     ( "nuVlAssociado"            );
$obTxtVlAssociado->setRotulo   ( "**Valor Associado"        );
$obTxtVlAssociado->setAlign    ( 'RIGHT'                    );
$obTxtVlAssociado->setTitle    ( $_REQUEST['nuVlAssociado'] );
$obTxtVlAssociado->setMaxLength( 19                         );
$obTxtVlAssociado->setSize     ( 21                         );
$obTxtVlAssociado->setValue    ( ''                         );
$obTxtVlAssociado->setNull     ( true                       );

$obTxtVlTotal = new Label;
$obTxtVlTotal->setName    ( "nuSoma"               );
$obTxtVlTotal->setId      ( "nuSoma"               );
$obTxtVlTotal->setRotulo  ( "Total Vinculado"      );
$obTxtVlTotal->setValue   ( $_REQUEST['nuSoma']    );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"     );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
if (Sessao::getExercicio() > 2010) {
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','dtEmissao, numEmpenho, stExercicioEmpenho, inCodEntidade, nuVlAssociado, nuVlTotal, cmbLiquidacao');" );
} else {
    $obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','dtEmissao, numEmpenho, stExercicioEmpenho, inCodEntidade, nuVlAssociado, nuVlTotal');" );
}

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btnLimpar");
$obBtnLimpar->setId                ( "limpar" );
$obBtnLimpar->setValue             ( "Limpar" );
$obBtnLimpar->obEvento->setOnClick ( "montaParametrosGET('limpar');" );

$spnLista = new Span;
$spnLista->setId  ( 'spnLista' );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                                       );
$obFormulario->addHidden        ( $obHdnAcao                                    );
$obFormulario->addHidden        ( $obHdnCtrl                                    );
$obFormulario->addHidden        ( $obHdnDataEmissao                             );
$obFormulario->addHidden        ( $obHdnCodEntidade                             );
$obFormulario->addComponente    ( $obCmbTipoNota                                );
$obFormulario->addHidden        ( $obHdnCodNota                                 );
$obFormulario->addHidden        ( $obHdnInscEstadual                            );
$obFormulario->addHidden        ( $obHdnVlTotal                                 );
$obFormulario->addComponente    ( $obTxtNota                                    );
$obFormulario->addComponente    ( $obEntidadeUsuario                            );
$obFormulario->addComponente    ( $obTxtSerie                                   );
$obFormulario->addComponente    ( $obTxtAIDF                                    );
$obFormulario->addComponente    ( $obDtEmissao                                  );
$obFormulario->addComponente    ( $obTxtIncricaoMunicipal                       );
$obFormulario->addComponente    ( $obTxtIncricaoEstadual                        );
$obFormulario->addComponente    ( $obTxtVlNotaFiscal                            );

if (Sessao::getExercicio() > '2011') {
    $obFormulario->addSpan      ( $obSpnChave );
}

$obFormulario->addTitulo        ( "Dados dos empenhos dos Documentos Fiscais"   );
$obFormulario->addComponente    ( $obTxtExercicio                               );
$obFormulario->addComponente    ( $obBscEmpenho                                 );
if (Sessao::getExercicio() > 2010) {
    $obFormulario->addComponente($obCmbLiquidacao);
}
$obFormulario->addComponente    ( $obTxtVlAssociado                             );
$obFormulario->addComponente    ( $obTxtVlTotal                                 );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"",""    );
$obFormulario->addSpan          ( $spnLista                                     );

if ($_REQUEST['stAcao'] == 'incluir') {
    $obFormulario->Cancelar($pgForm.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'] );
} elseif ($_REQUEST['stAcao'] == 'alterar') {
    $obFormulario->Cancelar($pgList.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'].$stLink );
}

$jsOnload = "montaParametrosGET('carregaDados','inCodNota, inNumNota, inNumInscricaoEstadual, stAcao');";

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

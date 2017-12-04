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
    * Formulário de Cadastro de Notas Fiscais TCEMG
    * Data de Criação   : 05/02/2014

    * @author Analista      Sergio Luiz dos Santos
    * @author Desenvolvedor Michel Teixeira

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FMManterNotasFiscais.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGTipoNotaFiscal.class.php";
include_once CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGNotaFiscal.class.php";

$stPrograma = "ManterNotasFiscais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);
$obTTCEMGTipoNotaFiscal = new TTCEMGTipoNotaFiscal;

$stOrder = " ORDER BY descricao ";
$obTTCEMGTipoNotaFiscal->recuperaTodos($rsTipoNota, "", $stOrder);

$stFiltroAux = Sessao::read('filtroAux');
$stLink = "&stExercicio=".$request->get('stExercicioNota').$stFiltroAux ;

$obTTCEMGNotaFiscal = new TTCEMGNotaFiscal;

$vlTotal        = '0,00';
$vlDesconto     = '0,00';
$vlTotalLiquido = '0,00';

if ($request->get('stAcao') != 'incluir') {
    $stFiltro  = " WHERE NF.cod_nota     = " . $request->get('inCodNota');
    $stFiltro .= "   AND NF.cod_entidade = " . $request->get('cod_entidade');
    $stFiltro .= "   AND NF.exercicio    = '" . $request->get('exercicio') . "'";

    $obTTCEMGNotaFiscal->recuperaNotasFiscais($rsRecordSet, $stFiltro);
    $rsRecordSet->addFormatacao('vl_total'        , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('vl_desconto'     , 'NUMERIC_BR');
    $rsRecordSet->addFormatacao('vl_total_liquido', 'NUMERIC_BR');

    if ($rsRecordSet->getNumLinhas() > 0) {
        $vlTotal        = $rsRecordSet->getCampo('vl_total');
        $vlDesconto     = $rsRecordSet->getCampo('vl_desconto');
        $vlTotalLiquido = $rsRecordSet->getCampo('vl_total_liquido');
    }    
}

Sessao::remove('arLiquidacoes');
//*****************************************************//
// Define COMPONENTES DO FORMULARIO
//*****************************************************//

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"              );
$obHdnAcao->setValue( $_REQUEST['stAcao']   );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnCodNota = new Hidden;
$obHdnCodNota->setName  ( "inCodNota"                   );
$obHdnCodNota->setValue ( $_REQUEST['inCodNota']        );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario;
$obEntidadeUsuario->setCodEntidade($_REQUEST['cod_entidade']);
$obEntidadeUsuario->setNull ( false );

$obHdnCodEntidade = new Hidden;
$obHdnCodEntidade->setName  ( "cod_entidade"            );
$obHdnCodEntidade->setValue ( $_REQUEST['cod_entidade'] );

$obHdnVlAssociadoTotal = new Hidden;
$obHdnVlAssociadoTotal->setName  ( "hdnVlAssociadoTotal" );
$obHdnVlAssociadoTotal->setId    ( "hdnVlAssociadoTotal" );

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
        $obCmbTipoNota->setValue     ( ''                  );
}

$obCmbTipoNota->obEvento->setOnChange("montaParametrosGET('montaSpan', 'stTipoDocto');");
$obHdnCtrl->setValue ("montaSpan");

$obSpnChave = new Span();
$obSpnChave->setId( 'spnChave' );

$obSpnSerie = new Span();
$obSpnSerie->setId( 'spnSerie' );

$obSpnNumero = new Span();
$obSpnNumero->setId( 'spnNumero' );

$obTxtAIDF = new TextBox;
$obTxtAIDF->setName     ( "stAIFD"                    );
$obTxtAIDF->setId       ( "stAIDF"                    );
$obTxtAIDF->setValue    ( $_REQUEST['stAIDF']         );
$obTxtAIDF->setRotulo   ( "Número da AIDF"            );
$obTxtAIDF->setTitle    ( "Informe o número da Autorização da Impressão do Documento Fiscal." );
$obTxtAIDF->setNull     ( true                        );
$obTxtAIDF->setInteiro  ( false                       );
$obTxtAIDF->setSize     ( 18                          );
$obTxtAIDF->setMaxLength( 15                          );

$obDtEmissao = new Data;
$obDtEmissao->setName     ( "dtEmissao"                         );
$obDtEmissao->setId       ( "dtEmissao"                         );
$obDtEmissao->setRotulo   ( "Data de Emissão"                   );
$obDtEmissao->setValue    ( $_REQUEST['dtEmissao']              );
$obDtEmissao->setTitle    ( 'Informe a data de emissão.'        );
$obDtEmissao->setNull     ( false                               );
$obDtEmissao->setSize     ( 10                                  );
$obDtEmissao->setMaxLength( 10                                  );

$Exercicio = ($request->get('stExercicioNota')!='') ? $request->get('stExercicioNota') :Sessao::getExercicio();

$obTxtExercicio = new TextBox;
$obTxtExercicio->setName     ( "stExercicio"          );
$obTxtExercicio->setId       ( "stExercicio"          );
$obTxtExercicio->setValue    ( $Exercicio             );
$obTxtExercicio->setRotulo   ( "Exercício"            );
$obTxtExercicio->setTitle    ( "Informe o exercício." );
$obTxtExercicio->setInteiro  ( false                  );
$obTxtExercicio->setNull     ( false                  );
$obTxtExercicio->setMaxLength( 4                      );
$obTxtExercicio->setSize     ( 5                      );

$obTxtIncricaoMunicipal = new TextBox;
$obTxtIncricaoMunicipal->setName     ( "inNumInscricaoMunicipal"                   );
$obTxtIncricaoMunicipal->setId       ( "inNumInscricaoMunicipal"                   );
$obTxtIncricaoMunicipal->setValue    ( $_REQUEST['inNumInscricaoMunicipal']        );
$obTxtIncricaoMunicipal->setRotulo   ( "Inscrição Municipal"                       );
$obTxtIncricaoMunicipal->setTitle    ( "Informe o número da Inscrição Municipal da Entidade.");
$obTxtIncricaoMunicipal->setNull     ( true                                        );
$obTxtIncricaoMunicipal->setInteiro  ( true                                        );
$obTxtIncricaoMunicipal->setSize     ( 25                                          );
$obTxtIncricaoMunicipal->setMaxLength( 30                                          );

$obTxtIncricaoEstadual = new TextBox;
$obTxtIncricaoEstadual->setName     ( "inNumInscricaoEstadual"                   );
$obTxtIncricaoEstadual->setId       ( "inNumInscricaoEstadual"                   );
$obTxtIncricaoEstadual->setValue    ( $_REQUEST['inNumInscricaoEstadual']        );
$obTxtIncricaoEstadual->setRotulo   ( "Inscrição Estadual"                       );
$obTxtIncricaoEstadual->setTitle    ( "Informe o número da Inscrição Estadual da Entidade." );
$obTxtIncricaoEstadual->setNull     ( true                                       );
$obTxtIncricaoEstadual->setInteiro  ( true                                       );
$obTxtIncricaoEstadual->setSize     ( 25                                         );
$obTxtIncricaoEstadual->setMaxLength( 30                                         );

$obTxtExercicioEmpenho = new TextBox;
$obTxtExercicioEmpenho->setName     ( "stExercicioEmpenho"              );
$obTxtExercicioEmpenho->setValue    ( Sessao::getExercicio()            );
$obTxtExercicioEmpenho->setRotulo   ( "Exercício do Empenho"            );
$obTxtExercicioEmpenho->setTitle    ( "Informe o exercício do empenho." );
$obTxtExercicioEmpenho->setInteiro  ( false                             );
$obTxtExercicioEmpenho->setNull     ( false                             );
$obTxtExercicioEmpenho->setMaxLength( 4                                 );
$obTxtExercicioEmpenho->setSize     ( 5                                 );

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

$obCmbLiquidacao = new Select;
$obCmbLiquidacao->setName              ('cmbLiquidacao'             );
$obCmbLiquidacao->setId                ('cmbLiquidacao'             );
$obCmbLiquidacao->setRotulo            ('*Liquidação'               );
$obCmbLiquidacao->setTitle             ('Selecione a liquidação.'   );
$obCmbLiquidacao->addOption            ('', 'Selecione'             );
$obCmbLiquidacao->setCampoId           ('mixCombo'                  );
$obCmbLiquidacao->setCampoDesc         ('mixCombo'                  );
$obCmbLiquidacao->setNull              (true                        );
$obCmbLiquidacao->setStyle             ('width: 220px'              );
$obCmbLiquidacao->obEvento->setOnChange("montaParametrosGET('montaLiquidacao', 'cmbLiquidacao');");

$obTxtVlAssociado = new Numerico;
$obTxtVlAssociado->setName     ( "nuVlAssociado"            );
$obTxtVlAssociado->setRotulo   ( "**Valor Associado"        );
$obTxtVlAssociado->setAlign    ( 'RIGHT'                    );
$obTxtVlAssociado->setTitle    ( $_REQUEST['nuVlAssociado'] );
$obTxtVlAssociado->setMaxLength( 19                         );
$obTxtVlAssociado->setSize     ( 21                         );
$obTxtVlAssociado->setValue    ( $_REQUEST['nuVlAssociado'] );
$obTxtVlAssociado->setNull     ( true                       );

$obTxtVlTotalLiquid = new Label;
$obTxtVlTotalLiquid->setName    ( "nuTotalLiquidacao"               );
$obTxtVlTotalLiquid->setId      ( "nuTotalLiquidacao"               );
$obTxtVlTotalLiquid->setRotulo  ( "Valor Total Liquidação"          );
$obTxtVlTotalLiquid->setValue   ( $_REQUEST['nuTotalLiquidacao']    );

$obTxtVlTotalDoctoFiscal = new Numerico;
$obTxtVlTotalDoctoFiscal->setName     ( "nuTotalNf"       );
$obTxtVlTotalDoctoFiscal->setId       ( "nuTotalNf"       );
$obTxtVlTotalDoctoFiscal->setRotulo   ( "Valor Total Docto Fiscal"   );
$obTxtVlTotalDoctoFiscal->setAlign    ( 'RIGHT'                      );
$obTxtVlTotalDoctoFiscal->setMaxLength( 19                           );
$obTxtVlTotalDoctoFiscal->setSize     ( 21                           );
$obTxtVlTotalDoctoFiscal->setNull     ( false                        );
$obTxtVlTotalDoctoFiscal->setValue    ( $vlTotal                     );
$obTxtVlTotalDoctoFiscal->obEvento->setOnChange("montaParametrosGET('atualizaValorLiquido','nuTotalNf, nuVlDesconto');" );

$obTxtVlDescontoDoctoFiscal = new Numerico;
$obTxtVlDescontoDoctoFiscal->setName     ( "nuVlDesconto"       );
$obTxtVlDescontoDoctoFiscal->setId       ( "nuVlDesconto"       );
$obTxtVlDescontoDoctoFiscal->setRotulo   ( "Valor Desconto Docto Fiscal"   );
$obTxtVlDescontoDoctoFiscal->setAlign    ( 'RIGHT'                         );
$obTxtVlDescontoDoctoFiscal->setMaxLength( 19                              );
$obTxtVlDescontoDoctoFiscal->setSize     ( 21                              );
$obTxtVlDescontoDoctoFiscal->setNull     ( false                           );
$obTxtVlDescontoDoctoFiscal->setValue    ( $vlDesconto                     );
$obTxtVlDescontoDoctoFiscal->obEvento->setOnChange("montaParametrosGET('atualizaValorLiquido','nuTotalNf, nuVlDesconto');" );

$obTxtVlTotalLiquidNF = new Label;
$obTxtVlTotalLiquidNF->setName    ( "nuTotalLiquidNf"               );
$obTxtVlTotalLiquidNF->setId      ( "nuTotalLiquidNf"               );
$obTxtVlTotalLiquidNF->setRotulo  ( "Valor Liquido Docto Fiscal"    );
$obTxtVlTotalLiquidNF->setValue   ( $vlTotalLiquido                 );
$obTxtVlTotalLiquidNF->setNull    ( false                           );

$obBtnIncluir = new Button;
$obBtnIncluir->setValue             ( "Incluir"     );
$obBtnIncluir->setName              ( "btnIncluir"  );
$obBtnIncluir->setId                ( "btnIncluir"  );
$obBtnIncluir->obEvento->setOnClick ( "montaParametrosGET('incluirEmpenhoLista','hdnVlAssociadoTotal, inCodNota, dtEmissao, numEmpenho, stExercicioEmpenho, inCodEntidade, nuVlAssociado, nuTotalNf, nuVlDesconto, nuTotalLiquidNf, cmbLiquidacao');" );

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
$obFormulario->addHidden        ( $obHdnCodEntidade                             );
$obFormulario->addHidden        ( $obHdnVlAssociadoTotal                        );
$obFormulario->addComponente    ( $obCmbTipoNota                                );
$obFormulario->addHidden        ( $obHdnCodNota                                 );
$obFormulario->addComponente    ( $obEntidadeUsuario                            );
$obFormulario->addComponente    ( $obTxtIncricaoMunicipal                       );
$obFormulario->addComponente    ( $obTxtIncricaoEstadual                        );
$obFormulario->addComponente    ( $obTxtAIDF                                    );
$obFormulario->addComponente    ( $obDtEmissao                                  );
$obFormulario->addComponente    ( $obTxtExercicio                               );
$obFormulario->addSpan          ( $obSpnNumero                                  );
$obFormulario->addSpan          ( $obSpnSerie                                   );
$obFormulario->addSpan          ( $obSpnChave                                   );

$obFormulario->addTitulo        ( "Dados Financeiros dos Documentos Fiscais"   );
$obFormulario->addComponente    ( $obTxtVlTotalDoctoFiscal                     );
$obFormulario->addComponente    ( $obTxtVlDescontoDoctoFiscal                  );
$obFormulario->addComponente    ( $obTxtVlTotalLiquidNF                        );
$obFormulario->addTitulo        ( "Dados dos empenhos dos Documentos Fiscais"   );
$obFormulario->addComponente    ( $obTxtExercicioEmpenho                        );
$obFormulario->addComponente    ( $obBscEmpenho                                 );
$obFormulario->addComponente    ( $obCmbLiquidacao                              );
$obFormulario->addComponente    ( $obTxtVlTotalLiquid                           );
$obFormulario->addComponente    ( $obTxtVlAssociado                             );
$obFormulario->agrupaComponentes( array( $obBtnIncluir, $obBtnLimpar ),"",""    );
$obFormulario->addSpan          ( $spnLista                                     );

$obOk  = new Ok();
$obOk->obEvento->setOnClick("ValidaNF();");

$obCancelar  = new Cancelar();
if ($_REQUEST['stAcao'] == 'incluir') {
    $obCancelar->obEvento->setOnClick("Cancelar('".$pgForm.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'] ."','telaPrincipal');");
} else {
    $obCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$_REQUEST['stAcao'].$stLink ."','telaPrincipal');");
}

$obFormulario->defineBarra( array( $obOk, $obCancelar ) );

$jsOnload = "montaParametrosGET('carregaDados','inCodNota, inNumNota, stExercicio, cod_entidade, stAcao');";

$obFormulario->show();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

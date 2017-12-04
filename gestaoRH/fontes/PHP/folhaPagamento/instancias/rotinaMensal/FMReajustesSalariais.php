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
    * Arquivo de Formulário
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-02 14:35:36 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-04.05.26
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "ReajustesSalariais";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

Sessao::remove("arRegistros");
Sessao::remove("arPadroes");
Sessao::clean();

$stAcao = $request->get('stAcao');

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $request->get('stAcao')                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgList  );
$obForm->setTarget      ( "telaPrincipal" );

$obChkAtivos = new Radio();
$obChkAtivos->setRotulo("Cadastro");
$obChkAtivos->setName("stCadastro");
$obChkAtivos->setTitle("Selecione o tipo de cadastro a reajustar.");
$obChkAtivos->setValue("a");
$obChkAtivos->setLabel("Ativos");
$obChkAtivos->setChecked(true);
$obChkAtivos->setNull(false);
$obChkAtivos->obEvento->setOnChange("montaParametrosGET('limparFiltro','stCadastro');");

$obChkAposentados = new Radio();
$obChkAposentados->setRotulo("Cadastros a Reajustar");
$obChkAposentados->setName("stCadastro");
$obChkAposentados->setTitle("Selecione o tipo de cadastro a reajustar.");
$obChkAposentados->setValue("o");
$obChkAposentados->setLabel("Aposentados");
$obChkAposentados->setNull(false);
$obChkAposentados->obEvento->setOnClick("montaParametrosGET('limparFiltro','stCadastro');");

$obChkPensionistas = new Radio();
$obChkPensionistas->setRotulo("Cadastros a Reajustar");
$obChkPensionistas->setName("stCadastro");
$obChkPensionistas->setTitle("Selecione o tipo de cadastro a reajustar.");
$obChkPensionistas->setValue("p");
$obChkPensionistas->setLabel("Pensionistas");
$obChkPensionistas->setNull(false);
$obChkPensionistas->obEvento->setOnClick("montaParametrosGET('limparFiltro','stCadastro');");

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setAtributoPensionista();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setRegSubFunEsp();

$obRdoPadroes = new Radio();
$obRdoPadroes->setRotulo("Valores à Reajustar");
$obRdoPadroes->setName("stReajuste");
$obRdoPadroes->setId("stReajustePadrao");
$obRdoPadroes->setTitle("Marque uma das opções para ajuste: valores de padrões ou valores de eventos.");
$obRdoPadroes->setLabel("Padrões");
$obRdoPadroes->setValue("p");
$obRdoPadroes->setChecked(true);
$obRdoPadroes->setNull(false);
$obRdoPadroes->obEvento->setOnClick("montaParametrosGET('gerarSpanValoresReajustes');");

$obRdoEventos = new Radio();
$obRdoEventos->setRotulo("Valores à Reajustar");
$obRdoEventos->setName("stReajuste");
$obRdoEventos->setId("stReajusteEvento");
$obRdoEventos->setTitle("Marque uma das opções para ajuste: valores de padrões ou valores de eventos.");
$obRdoEventos->setLabel("Eventos");
$obRdoEventos->setValue("e");
$obRdoEventos->setNull(false);
$obRdoEventos->obEvento->setOnClick("montaParametrosGET('gerarSpanValoresReajustes');");

$obSpnValoresReajustes = new Span();
$obSpnValoresReajustes->setId("spnValoresReajustes");

if (trim($stAcao) == "incluir") {

    $obRdoTipoReajustePercentual = new Radio();
    $obRdoTipoReajustePercentual->setRotulo("Reajuste em Percentual/Valor");
    $obRdoTipoReajustePercentual->setName("stTipoReajuste");
    $obRdoTipoReajustePercentual->setId("stTipoReajustePercentual");
    $obRdoTipoReajustePercentual->setTitle("Marque uma das opções para reajuste: reajuste por Percentual ou reajuste por Valor.");
    $obRdoTipoReajustePercentual->setLabel("Percentual");
    $obRdoTipoReajustePercentual->setValue("p");
    $obRdoTipoReajustePercentual->setChecked(true);
    $obRdoTipoReajustePercentual->setNull(false);
    $obRdoTipoReajustePercentual->obEvento->setOnClick("montaParametrosGET('gerarSpanTipoReajuste','stCtrl,stTipoReajuste,stTipoReajuste,nuPercentualReajuste,dtVigencia');");

    $obRdoTipoReajusteValor = new Radio();
    $obRdoTipoReajusteValor->setRotulo("Reajuste em Percentual/Valor");
    $obRdoTipoReajusteValor->setName("stTipoReajuste");
    $obRdoTipoReajusteValor->setId("stTipoReajusteValor");
    $obRdoTipoReajusteValor->setTitle("Marque uma das opções para ajuste: valores de padrões ou valores de eventos.");
    $obRdoTipoReajusteValor->setLabel("Valor");
    $obRdoTipoReajusteValor->setValue("v");
    $obRdoTipoReajusteValor->setNull(false);
    $obRdoTipoReajusteValor->obEvento->setOnClick("montaParametrosGET('gerarSpanTipoReajuste','stCtrl,stTipoReajuste,stTipoReajuste,nuPercentualReajuste,dtVigencia');");

    $obSpnTipoReajuste = new Span();
    $obSpnTipoReajuste->setId("spnTipoReajuste");

    $obHdnTipoReajuste = new hiddenEval();
    $obHdnTipoReajuste->setName("hdnTipoReajuste");
    $obHdnTipoReajuste->setId("hdnTipoReajuste");
    $obHdnTipoReajuste->setValue("");

    $obNumFaixaInicial = new Numerico();
    $obNumFaixaInicial->setRotulo("Faixa de Valores à Reajustar");
    $obNumFaixaInicial->setTitle("Informe a faixa de valores à reajuste.");
    $obNumFaixaInicial->setName("nuFaixaInicial");
    $obNumFaixaInicial->setValue("0,01");

    $obNumFaixaFinal = new Numerico();
    $obNumFaixaFinal->setRotulo("Faixa de Valores à Reajustar");
    $obNumFaixaFinal->setTitle("Informe a faixa de valores à reajuste.");
    $obNumFaixaFinal->setName("nuFaixaFinal");
    $obNumFaixaFinal->setValue("999.999,99");

    $obDtVigencia = new Data();
    $obDtVigencia->setRotulo("Vigência");
    $obDtVigencia->setTitle("Informe a data de vigência (início) do reajuste.");
    $obDtVigencia->setName("dtVigencia");
    $obDtVigencia->setNull(false);
    $obDtVigencia->setValue(date("d/m/Y"));

/************************************/
    include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php" );
    $obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
    $obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->listarTodos ( $rsTipoNorma );

    $obCmbTipoNorma = new Select;
    $obCmbTipoNorma->setRotulo              ( "Tipo de Norma"             );
    $obCmbTipoNorma->setName                ( "inCodTipoNorma"            );
    $obCmbTipoNorma->setCampoID             ( "cod_tipo_norma"            );
    $obCmbTipoNorma->setCampoDesc           ( "nom_tipo_norma"            );
    $obCmbTipoNorma->addOption              ( "", "Selecione"             );
    $obCmbTipoNorma->setNull                ( false                        );
    $obCmbTipoNorma->preencheCombo          ( $rsTipoNorma                );
    $obCmbTipoNorma->obEvento->setOnChange  ( "buscaValor('montaNorma');" );

    $obCmbNorma = new Select;
    $obCmbNorma->setRotulo        ( "Norma"         );
    $obCmbNorma->setName          ( "inCodNorma"    );
    $obCmbNorma->setCampoID       ( "cod_norma"     );
    $obCmbNorma->setCampoDesc     ( "nom_norma"     );
    $obCmbNorma->addOption        ( "", "Selecione" );
    $obCmbNorma->setNull          ( false            );
/************************************/

    $obTxtObservacao = new TextArea();
    $obTxtObservacao->setRotulo("Observações para Assentamento");
    $obTxtObservacao->setTitle("Informar o texto de observações para o assentamento.");
    $obTxtObservacao->setName("stObservacao");

    $obLblFaixa = new Label();
    $obLblFaixa->setValue(" até ");

    $obBtnOk = new Ok;
    $obBtnOk->setValue              ( "Simular"                                                );
    $obBtnOk->setName				( "btnOk" 												   );
    $obBtnOk->setTitle				( "Clique para simular os valores do reajuste de salário." );
    $obBtnOk->obEvento->setOnClick	( "montaParametrosGET('submeter', 'stReajuste,inCodConfiguracao,inCodigoEvento', true);"              );

    $obLblMensagem = new Label();
    $obLblMensagem->setRotulo("Mensagem");
    $obLblMensagem->setValue("Para realizar reajuste salarial deve ser incluído um assentamento de classificação do tipo assentamento e motivo igual a reajuste salarial.");
} elseif (trim($stAcao) == "excluir") {
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoReajuste.class.php");
    $stFiltro = " WHERE origem = 'P'";
    $obTFolhaPagamentoReajuste = new TFolhaPagamentoReajuste();
    $obTFolhaPagamentoReajuste->recuperaReajuste($rsReajuste, $stFiltro, " ORDER BY cod_reajuste");

    $obCmbReajuste = new Select;
    $obCmbReajuste->setName      ( "inCodReajuste"       );
    $obCmbReajuste->setRotulo    ( "Reajuste"            );
    $obCmbReajuste->setTitle     ( "Informe o reajuste para exclusão." );
    $obCmbReajuste->setNull      ( false                 );
    $obCmbReajuste->setCampoId   ( "[cod_reajuste]*_*[origem]" );
    $obCmbReajuste->setCampoDesc ( "[descricao]"         );
    $obCmbReajuste->addOption    ( "", "Selecione"       );
    $obCmbReajuste->preencheCombo( $rsReajuste           );
    $obCmbReajuste->setStyle     ( "width:auto"          );

    $obBtnOk = new Ok;
    $obBtnOk->setValue              ( "Listar"                                                     );
    $obBtnOk->setName		    ( "btnListar" 						   );
    $obBtnOk->setTitle		    ( "Clique para listar os contratos para exclusão de reajuste." );
    $obBtnOk->obEvento->setOnClick  ( "montaParametrosGET('submeter', '', true);"                  );
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAssentamento.class.php");
$obTPessoalAssentamentoAssentamento = new TPessoalAssentamentoAssentamento();
$stFiltro  = " AND classificacao_assentamento.cod_tipo = 1";
$stFiltro .= " AND assentamento_assentamento.cod_motivo = 8";
$obTPessoalAssentamentoAssentamento->recuperaAssentamento($rsAssentamentoAssentamento,$stFiltro);

$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 				( $stTitulo , "right" 	);
if (trim($stAcao) == "incluir") {
    if ($rsAssentamentoAssentamento->getNumLinhas() <= 0) {
        $obFormulario->addComponente($obLblMensagem);
    } else {
        $jsOnload = "montaParametrosGET('processarForm');";
        $obFormulario->addForm( $obForm );
        $obFormulario->addHidden($obHdnTipoReajuste, true);
        $obFormulario->agrupaComponentes(array($obChkAtivos,$obChkAposentados,$obChkPensionistas));
        $obIFiltroComponentes->geraFormulario($obFormulario);
        $obFormulario->addTitulo("Informações para Reajuste");
        $obFormulario->agrupaComponentes(array($obRdoPadroes,$obRdoEventos));
        $obFormulario->addSpan($obSpnValoresReajustes);
        $obFormulario->agrupaComponentes(array($obRdoTipoReajustePercentual,$obRdoTipoReajusteValor));
        $obFormulario->addSpan($obSpnTipoReajuste);
        $obFormulario->agrupaComponentes(array($obNumFaixaInicial,$obLblFaixa,$obNumFaixaFinal));
        $obFormulario->addComponente($obDtVigencia);
        $obFormulario->addComponente($obCmbTipoNorma);
        $obFormulario->addComponente($obCmbNorma    );
        $obFormulario->addComponente($obTxtObservacao);
        $obFormulario->defineBarra(array( $obBtnOk ));
    }
} elseif (trim($stAcao) == "excluir") {
    $jsOnload = "montaParametrosGET('processarForm');";
    $obFormulario->addForm( $obForm );
    $obFormulario->agrupaComponentes(array($obChkAtivos,$obChkAposentados,$obChkPensionistas));
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->addTitulo("Dados para Exclusão do Reajuste Salarial");
    $obFormulario->agrupaComponentes(array($obRdoPadroes,$obRdoEventos));
    $obFormulario->addSpan($obSpnValoresReajustes);
    $obFormulario->addComponente($obCmbReajuste);
    $obFormulario->defineBarra(array( $obBtnOk ));
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
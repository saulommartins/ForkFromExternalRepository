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
    * Arquivo filtro - configuração arquivo DDC TCE/MG
    * Data de Criação: 08/03/2014

    * @author Analista:      Sergio Luiz dos Santos
    * @author Desenvolvedor: Arthur Cruz

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoFinanceira/fontes/PHP/orcamento/classes/componentes/ITextBoxSelectEntidadeUsuario.class.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/normas/classes/componentes/IPopUpNorma.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDividaFundadaOperacaoCredito.class.php';
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundadaOperacaoCredito";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";


include_once($pgJs);

Sessao::write('link', '');
Sessao::write('arDividas', array());
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTTCEPEDividaFundadaOperacaoCredito = new TTCEPEDividaFundadaOperacaoCredito();
$obTTCEPEDividaFundadaOperacaoCredito->recuperaTodos($rsDividas, " WHERE exercicio = '".$_REQUEST['stExercicio']."' AND cod_entidade = '".$_REQUEST['inCodEntidade']."'");
 
$inCount = 0;
foreach($rsDividas->getElementos() as $registro) {
    $obNorma = new RNorma;
    $obNorma->setCodNorma($registro['cod_norma']);
    $obNorma->listarDecreto( $rsNorma );
    
    $arTmp[$inCount]['id']                           = date('Ymdhisu').mt_rand();;
    $arTmp[$inCount]['stExercicio']                  = $registro['exercicio'];
    $arTmp[$inCount]['inCodEntidade']                = $registro['cod_entidade'];
    $arTmp[$inCount]['inTipoOperacaoCredito']        = $registro['tipo_operacao_credito'];
    $arTmp[$inCount]['stTipoOperacaoCredito']        = $registro['tipo_operacao_credito'] == 1 ? 'Interna' : 'Externa';
    $arTmp[$inCount]['inCodLeiAutorizacao']          = $registro['cod_norma'];
    $arTmp[$inCount]['stLeiAutorizacao']             = $registro['cod_norma']." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
    $arTmp[$inCount]['stDataNorma']                  = $registro['dt_assinatura'];
    $arTmp[$inCount]['inNumeroContrato']             = $registro['num_contrato'];
    $arTmp[$inCount]['vlSaldoAnteriorTitulo']        = number_format($registro['vl_saldo_anterior_titulo'], 2, ',', '');
    $arTmp[$inCount]['vlInscricaoExercicioTitulo']   = number_format($registro['vl_inscricao_exercicio_titulo'], 2, ',', '');
    $arTmp[$inCount]['vlBaixaExercicioTitulo']       = number_format($registro['vl_baixa_exercicio_titulo'], 2, ',', '');
    $arTmp[$inCount]['vlSaldoAnteriorContrato']      = number_format($registro['vl_saldo_anterior_contrato'], 2, ',', '');
    $arTmp[$inCount]['vlInscricaoExercicioContrato'] = number_format($registro['vl_inscricao_exercicio_contrato'], 2, ',', '');
    $arTmp[$inCount]['vlBaixaExercicioContrato']     = number_format($registro['vl_baixa_exercicio_contrato'], 2, ',', '');
    
    $inCount++;
}
Sessao::write('arDividas', $arTmp);

$jsOnload.= "executaFuncaoAjax('montaListaDivida');";


//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName("stAcao");
$obHdnAcao->setId("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnId = new Hidden;
$obHdnId->setName("id");
$obHdnId->setId("id");
$obHdnId->setValue( '' );

$obHdnEntidade = new Hidden;
$obHdnEntidade->setName("inCodEntidade");
$obHdnEntidade->setId("inCodEntidade");
$obHdnEntidade->setValue($_REQUEST['inCodEntidade']);

$obHdnExercicio = new Hidden;
$obHdnExercicio->setName("stExercicio");
$obHdnExercicio->setId("stExercicio");
$obHdnExercicio->setValue($_REQUEST['stExercicio']);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget("oculto");

$obTipoOperacaoCredito = new Select();
$obTipoOperacaoCredito->setRotulo("Tipo de Operação de crédito");
$obTipoOperacaoCredito->setTitle("Tipo de Operação de crédito");
$obTipoOperacaoCredito->setId('inTipoOperacaoCredito');
$obTipoOperacaoCredito->setName("inTipoOperacaoCredito");
$obTipoOperacaoCredito->setNull(false);
$obTipoOperacaoCredito->setOptions(array('1' => 'Interna', '2' => 'Externa'));

$obIPopUpLeiAutorizacao = new IPopUpNorma();
$obIPopUpLeiAutorizacao->obInnerNorma->setId               ( "stNomeLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setId   ( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setName ( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->setRotulo           ( "Lei de Autorização"  );
$obIPopUpLeiAutorizacao->obInnerNorma->setTitle            ( "Informe o número de Lei de Autorização" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setValue( $inLeiAutorizacao );
$obIPopUpLeiAutorizacao->obInnerNorma->setValue            ( $stNomNorma );
$obIPopUpLeiAutorizacao->obLblDataNorma->setRotulo( "Data da lei autorizativa " );
$obIPopUpLeiAutorizacao->setExibeDataNorma(true);

$obInNumeroContrato = new Inteiro;
$obInNumeroContrato->setName     ( "inNumeroContrato"   );
$obInNumeroContrato->setId       ( "inNumeroContrato"   );
$obInNumeroContrato->setRotulo   ( "Número do Contrato" );
$obInNumeroContrato->setValue    ( $inNumeroContrato    );
$obInNumeroContrato->setTitle    ( "Número do Contrato" );
$obInNumeroContrato->setNull     ( false );
$obInNumeroContrato->setSize     ( 9 );
$obInNumeroContrato->setMaxLength( 9 );

$obVlSaldoAnteriorTitulo = new Numerico();
$obVlSaldoAnteriorTitulo->setId    ( "vlSaldoAnteriorTitulo" );
$obVlSaldoAnteriorTitulo->setName  ( "vlSaldoAnteriorTitulo" );
$obVlSaldoAnteriorTitulo->setRotulo( "Saldo anterior - Operação de crédito em títulos" );
$obVlSaldoAnteriorTitulo->setTitle ( "Saldo anterior - Operação de crédito em títulos" );
$obVlSaldoAnteriorTitulo->setNull  ( false );
$obVlSaldoAnteriorTitulo->setValue ( $vlSaldoAnteriorTitulo );
$obVlSaldoAnteriorTitulo->setDecimais(2);
$obVlSaldoAnteriorTitulo->setMaxLength(16);
$obVlSaldoAnteriorTitulo->setSize(17);

$obVlInscricaoExercicioTitulo = new Numerico();
$obVlInscricaoExercicioTitulo->setId    ( "vlInscricaoExercicioTitulo" );
$obVlInscricaoExercicioTitulo->setName  ( "vlInscricaoExercicioTitulo" );
$obVlInscricaoExercicioTitulo->setRotulo( "Inscrições no exercício - Operação de crédito em títulos"  );
$obVlInscricaoExercicioTitulo->setTitle ( "Inscrições no exercício - Operação de crédito em títulos"  );
$obVlInscricaoExercicioTitulo->setValue ( $vlInscricaoExercicioTitulo );
$obVlInscricaoExercicioTitulo->setNull  ( false );
$obVlInscricaoExercicioTitulo->setDecimais(2);
$obVlInscricaoExercicioTitulo->setMaxLength(16);
$obVlInscricaoExercicioTitulo->setSize(17);

$obVlBaixaExercicioTitulo = new Numerico();
$obVlBaixaExercicioTitulo->setId    ( "vlBaixaExercicioTitulo" );
$obVlBaixaExercicioTitulo->setName  ( "vlBaixaExercicioTitulo" );
$obVlBaixaExercicioTitulo->setRotulo( "Baixa no exercício - Operação de crédito em títulos"  );
$obVlBaixaExercicioTitulo->setTitle ( "Baixa no exercício - Operação de crédito em títulos"  );
$obVlBaixaExercicioTitulo->setValue ( $vlBaixaExercicioTitulo );
$obVlBaixaExercicioTitulo->setNull  ( false );
$obVlBaixaExercicioTitulo->setDecimais(2);
$obVlBaixaExercicioTitulo->setMaxLength(16);
$obVlBaixaExercicioTitulo->setSize(17);

$obVlSaldoAnteriorContrato = new Numerico();
$obVlSaldoAnteriorContrato->setId    ( "vlSaldoAnteriorContrato" );
$obVlSaldoAnteriorContrato->setName  ( "vlSaldoAnteriorContrato" );
$obVlSaldoAnteriorContrato->setRotulo( "Saldo anterior - Operação de crédito em contratos" );
$obVlSaldoAnteriorContrato->setTitle ( "Saldo anterior - Operação de crédito em contratos" );
$obVlSaldoAnteriorContrato->setValue ( $vlSaldoAnteriorContrato );
$obVlSaldoAnteriorContrato->setNull  ( false );
$obVlSaldoAnteriorContrato->setDecimais(2);
$obVlSaldoAnteriorContrato->setMaxLength(16);
$obVlSaldoAnteriorContrato->setSize(17);

$obVlInscricaoExercicioContrato = new Numerico();
$obVlInscricaoExercicioContrato->setId    ( "vlInscricaoExercicioContrato" );
$obVlInscricaoExercicioContrato->setName  ( "vlInscricaoExercicioContrato" );
$obVlInscricaoExercicioContrato->setRotulo( "Inscrições no exercício - Operação de crédito em contratos" );
$obVlInscricaoExercicioContrato->setTitle ( "Inscrições no exercício - Operação de crédito em contratos" );
$obVlInscricaoExercicioContrato->setValue ( $vlInscricaoExercicioContrato  );
$obVlInscricaoExercicioContrato->setNull  ( false );
$obVlInscricaoExercicioContrato->setDecimais(2);
$obVlInscricaoExercicioContrato->setMaxLength(16);
$obVlInscricaoExercicioContrato->setSize(17);

$obVlBaixaExercicioContrato = new Numerico();
$obVlBaixaExercicioContrato->setId    ( "vlBaixaExercicioContrato" );
$obVlBaixaExercicioContrato->setName  ( "vlBaixaExercicioContrato" );
$obVlBaixaExercicioContrato->setRotulo( "Baixa no exercício - Operação de crédito em contratos"  );
$obVlBaixaExercicioContrato->setTitle ( "Baixa no exercício - Operação de crédito em contratos"  );
$obVlBaixaExercicioContrato->setValue ( $vlBaixaExercicioContrato );
$obVlBaixaExercicioContrato->setNull  ( false );
$obVlBaixaExercicioContrato->setDecimais(2);
$obVlBaixaExercicioContrato->setMaxLength(16);
$obVlBaixaExercicioContrato->setSize(17);


$obSpnListaDividas = new Span();
$obSpnListaDividas->setId("spnListaDividas");

$obBtnIncluirDivida = new Button();
$obBtnIncluirDivida->setName             ( "btIncluirDivida" );
$obBtnIncluirDivida->setId               ( "btIncluirDivida" );
$obBtnIncluirDivida->setValue            ( "Incluir"         );
$obBtnIncluirDivida->obEvento->setOnClick( "montaParametrosGET('incluirDividaLista', 'id, stExercicio, inCodEntidade, inTipoOperacaoCredito, inCodLeiAutorizacao, stDataNorma, stLeiAutorizacao, inNumeroContrato, vlSaldoAnteriorTitulo, vlInscricaoExercicioTitulo, vlBaixaExercicioTitulo, vlSaldoAnteriorContrato, vlInscricaoExercicioContrato, vlBaixaExercicioContrato');"  );
$obBtnIncluirDivida->setTitle            ( "Clique para incluir a dívida a lista"  );

$obBtnAlterarDivida = new Button();
$obBtnAlterarDivida->setName             ( "btAlterarDivida" );
$obBtnAlterarDivida->setId               ( "btAlterarDivida" );
$obBtnAlterarDivida->setValue            ( "Alterar"         );
$obBtnAlterarDivida->obEvento->setOnClick( "montaParametrosGET('alterarListaDivida', 'id, stExercicio, inCodEntidade, inTipoOperacaoCredito, inCodLeiAutorizacao, stDataNorma, stLeiAutorizacao, inNumeroContrato, vlSaldoAnteriorTitulo, vlInscricaoExercicioTitulo, vlBaixaExercicioTitulo, vlSaldoAnteriorContrato, vlInscricaoExercicioContrato, vlBaixaExercicioContrato');"  );
$obBtnAlterarDivida->setTitle            ( "Clique para alterar a dívida a lista"  );
$obBtnAlterarDivida->setDisabled         ( true              );

$obBtLimpar = new Button();
$obBtLimpar->setValue('Limpar');
$obBtLimpar->setName ( "btnLimpar" );
$obBtLimpar->setId   ( "btnLimpar" );
$obBtLimpar->obEvento->setOnClick("limparDivida();");
$obBtLimpar->setTitle( "Clique para limpar a dívida a lista"  );

$obBtEnviar = new Button();
$obBtEnviar->setValue('Ok');
$obBtEnviar->setName ( "btnEnviar" );
$obBtEnviar->setId   ( "btnENviar" );
$obBtEnviar->obEvento->setOnClick("montaParametrosGET('enviarFormulario','inNumContratoDivida')");
$obBtEnviar->setTitle( "Clique para enviar"  );


//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
//$obFormulario->setLarguraRotulo( 30 );
//$obFormulario->setLarguraCampo ( 70 );
$obFormulario->addHidden( $obHdnEntidade );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addHidden( $obHdnExercicio );

$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obTipoOperacaoCredito );
$obIPopUpLeiAutorizacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obInNumeroContrato );

$obFormulario->addComponente( $obVlSaldoAnteriorTitulo );
$obFormulario->addComponente( $obVlInscricaoExercicioTitulo );
$obFormulario->addComponente( $obVlBaixaExercicioTitulo );
$obFormulario->addComponente( $obVlSaldoAnteriorContrato );
$obFormulario->addComponente( $obVlInscricaoExercicioContrato );
$obFormulario->addComponente( $obVlBaixaExercicioContrato );

$obFormulario->defineBarra(array($obBtnIncluirDivida, $obBtnAlterarDivida, $obBtLimpar));
$obFormulario->addSpan($obSpnListaDividas);
$obFormulario->defineBarra(array($obBtEnviar));

$obFormulario->show();

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');

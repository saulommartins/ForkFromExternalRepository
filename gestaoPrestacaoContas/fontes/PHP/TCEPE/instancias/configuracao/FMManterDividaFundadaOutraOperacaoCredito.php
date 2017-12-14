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
include_once CAM_GF_EMP_COMPONENTES.'IPopUpCredor.class.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEDividaFundadaOutraOperacaoCredito.class.php';
include_once CAM_GA_NORMAS_NEGOCIO."RNorma.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGM.class.php";


//Define o nome dos arquivos PHP
$stPrograma = "ManterDividaFundadaOutraOperacaoCredito";
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

$obTTCEPEDividaFundadaOutraOperacaoCredito = new TTCEPEDividaFundadaOutraOperacaoCredito();
$obTTCEPEDividaFundadaOutraOperacaoCredito->recuperaTodos($rsDividas, " WHERE exercicio = '".$_REQUEST['stExercicio']."' AND cod_entidade = '".$_REQUEST['inCodEntidade']."'");

$inCount = 0;
foreach($rsDividas->getElementos() as $registro) {
    $obNorma = new RNorma;
    $obNorma->setCodNorma($registro['cod_norma']);
    $obNorma->listarDecreto( $rsNorma );
    
    $obTCGM = new TCGM;
    $obTCGM->setDado('numcgm', $registro['cgm_credor']);
    $obTCGM->recuperaPorChave($rsCGM);
    
    $arTmp[$inCount]['id']                   = date('Ymdhisu').mt_rand();;
    $arTmp[$inCount]['stExercicio']          = $registro['exercicio'];
    $arTmp[$inCount]['inCodEntidade']        = $registro['cod_entidade'];
    $arTmp[$inCount]['inCodCredor']          = $registro['cgm_credor'];
    $arTmp[$inCount]['stNomCGM']             = $rsCGM->getCampo('nom_cgm');
    $arTmp[$inCount]['inCodLeiAutorizacao']  = $registro['cod_norma'];
    $arTmp[$inCount]['stLeiAutorizacao']     = $registro['cod_norma']." - ".$rsNorma->getCampo('nom_tipo_norma')." ".$rsNorma->getCampo('num_norma_exercicio')." - ".$rsNorma->getCampo('nom_norma');
    $arTmp[$inCount]['stDataNorma']          = $registro['dt_assinatura'];
    $arTmp[$inCount]['inNumeroContrato']     = $registro['num_contrato'];
    $arTmp[$inCount]['vlSaldoAnterior']      = number_format($registro['vl_saldo_anterior'], 2, ',', '');
    $arTmp[$inCount]['vlInscricaoExercicio'] = number_format($registro['vl_inscricao_exercicio'], 2, ',', '');
    $arTmp[$inCount]['vlBaixaExercicio']     = number_format($registro['vl_baixa_exercicio'], 2, ',', '');
    
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

$obIPopUpLeiAutorizacao = new IPopUpNorma();
$obIPopUpLeiAutorizacao->obInnerNorma->setId               ( "stNomeLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setId   ( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setName ( "inCodLeiAutorizacao" );
$obIPopUpLeiAutorizacao->obInnerNorma->setRotulo           ( "Lei de Autorização"  );
$obIPopUpLeiAutorizacao->obInnerNorma->setTitle            ( "Informe o número de Lei de Autorização" );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->setValue( $inLeiAutorizacao );
$obIPopUpLeiAutorizacao->obInnerNorma->setValue            ( $stNomNorma );
$obIPopUpLeiAutorizacao->obLblDataNorma->setRotulo( "Data da lei autorizativa " );
$obIPopUpLeiAutorizacao->obInnerNorma->obCampoCod->obEvento->setOnChange(" montaParametrosGET('buscaLeiAutorizacao', 'inCodLeiAutorizacao'); ");
$obIPopUpLeiAutorizacao->setExibeDataNorma(true);

// Busca de Credor
$obPopUpCredor = new IPopUpCredor( $obForm );
$obPopUpCredor->setNull ( true );

$obInNumeroContrato = new Inteiro;
$obInNumeroContrato->setName     ( "inNumeroContrato"   );
$obInNumeroContrato->setId       ( "inNumeroContrato"   );
$obInNumeroContrato->setRotulo   ( "Número do Contrato" );
$obInNumeroContrato->setValue    ( $inNumeroContrato    );
$obInNumeroContrato->setTitle    ( "Número do Contrato" );
$obInNumeroContrato->setNull     ( false );
$obInNumeroContrato->setSize     ( 9 );
$obInNumeroContrato->setMaxLength( 9 );

$obVlSaldoAnterior = new Numerico();
$obVlSaldoAnterior->setId    ( "vlSaldoAnterior" );
$obVlSaldoAnterior->setName  ( "vlSaldoAnterior" );
$obVlSaldoAnterior->setRotulo( "Saldo anterior" );
$obVlSaldoAnterior->setTitle ( "Saldo anterior" );
$obVlSaldoAnterior->setNull  ( false );
$obVlSaldoAnterior->setValue ( $vlSaldoAnterior );
$obVlSaldoAnterior->setDecimais(2);
$obVlSaldoAnterior->setMaxLength(16);
$obVlSaldoAnterior->setSize(17);

$obVlInscricaoExercicio = new Numerico();
$obVlInscricaoExercicio->setId    ( "vlInscricaoExercicio" );
$obVlInscricaoExercicio->setName  ( "vlInscricaoExercicio" );
$obVlInscricaoExercicio->setRotulo( "Inscrições no exercício"  );
$obVlInscricaoExercicio->setTitle ( "Inscrições no exercício"  );
$obVlInscricaoExercicio->setValue ( $vlInscricaoExercicio );
$obVlInscricaoExercicio->setNull  ( false );
$obVlInscricaoExercicio->setDecimais(2);
$obVlInscricaoExercicio->setMaxLength(16);
$obVlInscricaoExercicio->setSize(17);

$obVlBaixaExercicio = new Numerico();
$obVlBaixaExercicio->setId    ( "vlBaixaExercicio" );
$obVlBaixaExercicio->setName  ( "vlBaixaExercicio" );
$obVlBaixaExercicio->setRotulo( "Baixa no exercício" );
$obVlBaixaExercicio->setTitle ( "Baixa no exercício" );
$obVlBaixaExercicio->setValue ( $vlBaixaExercicio );
$obVlBaixaExercicio->setNull  ( false );
$obVlBaixaExercicio->setDecimais(2);
$obVlBaixaExercicio->setMaxLength(16);
$obVlBaixaExercicio->setSize(17);

$obSpnListaDividas = new Span();
$obSpnListaDividas->setId("spnListaDividas");

$obBtnIncluirDivida = new Button();
$obBtnIncluirDivida->setName             ( "btIncluirDivida" );
$obBtnIncluirDivida->setId               ( "btIncluirDivida" );
$obBtnIncluirDivida->setValue            ( "Incluir"         );
$obBtnIncluirDivida->obEvento->setOnClick( "montaParametrosGET('incluirDividaLista', 'id, stExercicio, inCodEntidade, inCodCredor, inCodLeiAutorizacao, stDataNorma, stLeiAutorizacao, inNumeroContrato, vlSaldoAnterior, vlInscricaoExercicio, vlBaixaExercicio');"  );
$obBtnIncluirDivida->setTitle            ( "Clique para incluir a dívida a lista"  );

$obBtnAlterarDivida = new Button();
$obBtnAlterarDivida->setName             ( "btAlterarDivida" );
$obBtnAlterarDivida->setId               ( "btAlterarDivida" );
$obBtnAlterarDivida->setValue            ( "Alterar"         );
$obBtnAlterarDivida->obEvento->setOnClick( "montaParametrosGET('alterarListaDivida', 'id, stExercicio, inCodEntidade, inCodCredor, inCodLeiAutorizacao, stDataNorma, stLeiAutorizacao, inNumeroContrato, vlSaldoAnterior, vlInscricaoExercicio, vlBaixaExercicio');"  );
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
$obFormulario->addHidden( $obHdnEntidade );
$obFormulario->addHidden( $obHdnId );
$obFormulario->addHidden( $obHdnExercicio );

$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->addComponente( $obPopUpCredor );
$obIPopUpLeiAutorizacao->geraFormulario( $obFormulario );
$obFormulario->addComponente( $obInNumeroContrato );
$obFormulario->addComponente( $obVlSaldoAnterior );
$obFormulario->addComponente( $obVlInscricaoExercicio );
$obFormulario->addComponente( $obVlBaixaExercicio );

$obFormulario->defineBarra(array($obBtnIncluirDivida, $obBtnAlterarDivida, $obBtLimpar));
$obFormulario->addSpan($obSpnListaDividas);
$obFormulario->defineBarra(array($obBtEnviar));

$obFormulario->show();

include_once('../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php');

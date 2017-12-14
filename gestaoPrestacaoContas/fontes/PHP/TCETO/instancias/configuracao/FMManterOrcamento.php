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
    * Pacote de configuração do TCETO - Formulário Configurar Orçamento
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FMManterOrcamento.php 60816 2014-11-17 18:10:03Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';
include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOAlteracaoLeiPPA.class.php";

$stPrograma = 'ManterOrcamento';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';
include_once ($pgOcul);
include_once ($pgJs);

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction($pgProc);
$obForm->setTarget('oculto');

$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue($stAcao);

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
//Consulta
$obTExportacaoConfiguracao = new TAdministracaoConfiguracao;
$obTExportacaoConfiguracao->setDado("cod_modulo", 64);
$obTExportacaoConfiguracao->setDado("exercicio",Sessao::getExercicio());

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_cod_norma");
$obTExportacaoConfiguracao->consultar();
$vlCodNorma = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_complementacao_loa");
$obTExportacaoConfiguracao->consultar();
$vlComplementacaoLoa = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_adicional");
$obTExportacaoConfiguracao->consultar();
$vlCreditoAdicional = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_antecipacao");
$obTExportacaoConfiguracao->consultar();
$vlCreditoAntecipacao = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_interno");
$obTExportacaoConfiguracao->consultar();
$vlCreditoInterno = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_externo");
$obTExportacaoConfiguracao->consultar();
$vlCreditoExterno = $obTExportacaoConfiguracao->getDado('valor');

// Verificar Lei/Norma/Decreto
$nomNorma = '&nbsp;';

if(is_numeric($vlCodNorma)){
    $obTNorma = new TNorma;
    $stFiltro = ' WHERE N.cod_norma='.$vlCodNorma.' ';
    $obTNorma->recuperaNormasDecreto($rsLei, $stFiltro);
    
    if($rsLei->getNumLinhas()>0)
        $nomNorma  = $rsLei->getCampo('nom_tipo_norma').' '.$rsLei->getCampo('num_norma_exercicio').' - '.$rsLei->getCampo('nom_norma');
    else{
        $jsOnload .= "alertaAviso('@Código da Lei/Decreto informado não existe. (".$vlCodNorma.")','form','erro','".Sessao::getId()."');\n";
        $jsOnload .= "jq('#inCodNorma').val('');\n";
    }
}

$obTTCETOAlteracaoLeiPPA = new TTCETOAlteracaoLeiPPA();
$obTTCETOAlteracaoLeiPPA->recuperaAlteracaoLeisPPA($rsLeiAlteracao, "", "", $boTransacao);

$inId = 0;
foreach ($rsLeiAlteracao->getElementos() as $dados) {
    $arAlteracaoLei[$inId]['inId']               = $inId;
    $arAlteracaoLei[$inId]['cod_norma']          = $dados['cod_norma'];
    $arAlteracaoLei[$inId]['data_alteracao_lei'] = SistemaLegado::dataToBr( $dados['data_alteracao'] );    
    $arAlteracaoLei[$inId]['descricao']          = $dados['nom_norma'];    
    $inId++;
}

Sessao::write('arAlteracaoLei', $arAlteracaoLei);

// Define Objeto BuscaInner para Norma
$obBscNorma = new BuscaInner;
$obBscNorma->setRotulo ( "Lei Orçamentária Anual"   );
$obBscNorma->setTitle  ( "Selecione uma Lei Orçamentária." );
$obBscNorma->setNulL   ( false                      );
$obBscNorma->setId     ( "stNomTipoNorma"           );
$obBscNorma->setValue  ( $nomNorma                  );
$obBscNorma->obCampoCod->setName     ( "inCodNorma" );
$obBscNorma->obCampoCod->setId       ( "inCodNorma" );
$obBscNorma->obCampoCod->setSize     ( 10           );
$obBscNorma->obCampoCod->setMaxLength( 7            );
$obBscNorma->obCampoCod->setValue    ( $vlCodNorma  );
$obBscNorma->obCampoCod->setAlign    ( "left"       );
$obBscNorma->obCampoCod->obEvento->setOnChange(" buscaValor('PreencheNorma'); ");
$obBscNorma->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','inCodNorma','stNomTipoNorma','','".Sessao::getId()."','800','550');");

$obTxtComplementacaoLoa = new TextArea;
$obTxtComplementacaoLoa->setName ('stComplementacaoLoa');
$obTxtComplementacaoLoa->setId   ('stComplementacaoLoa');
$obTxtComplementacaoLoa->setRotulo('Complementação LOA');
$obTxtComplementacaoLoa->setTitle('Informe a Complementação LOA');
$obTxtComplementacaoLoa->setMaxCaracteres(255);
$obTxtComplementacaoLoa->setNull (false);
$obTxtComplementacaoLoa->setValue($vlComplementacaoLoa);

// Define Objeto Numeric para CreditoAdicional
$obTxtCreditoAdicional = new Numerico;
$obTxtCreditoAdicional->setName     ( "vlCreditoAdicional" );
$obTxtCreditoAdicional->setId       ( "vlCreditoAdicional" );
$obTxtCreditoAdicional->setRotulo   ( "Percentual de Crédito Adicional"             );
$obTxtCreditoAdicional->setTitle    ( "Informe o percentual de crédito adicional."  );
$obTxtCreditoAdicional->setDecimais ( 2 );
$obTxtCreditoAdicional->setNegativo ( false );
$obTxtCreditoAdicional->setNull     ( false );
$obTxtCreditoAdicional->setSize     ( 10 );
$obTxtCreditoAdicional->setMaxLength( 5 );
$obTxtCreditoAdicional->setMinValue ( 0 );
$obTxtCreditoAdicional->setMaxValue ( 100 );
$obTxtCreditoAdicional->setValue    ( $vlCreditoAdicional  );

// Define Objeto Numeric para CreditoAntecipacao
$obTxtCreditoAntecipacao = new Numerico;
$obTxtCreditoAntecipacao->setName     ( "vlCreditoAntecipacao" );
$obTxtCreditoAntecipacao->setId       ( "vlCreditoAntecipacao" );
$obTxtCreditoAntecipacao->setRotulo   ( "Percentual de Operações de Crédito por Antecipação"            );
$obTxtCreditoAntecipacao->setTitle    ( "Informe o percentual de operações de crédito por antecipação." );
$obTxtCreditoAntecipacao->setDecimais ( 2 );
$obTxtCreditoAntecipacao->setNegativo ( false );
$obTxtCreditoAntecipacao->setNull     ( false );
$obTxtCreditoAntecipacao->setSize     ( 10 );
$obTxtCreditoAntecipacao->setMaxLength( 5 );
$obTxtCreditoAntecipacao->setMinValue ( 0 );
$obTxtCreditoAntecipacao->setMaxValue ( 100 );
$obTxtCreditoAntecipacao->setValue    ( $vlCreditoAntecipacao  );

// Define Objeto Numeric para CreditoInterno
$obTxtCreditoInterno = new Numerico;
$obTxtCreditoInterno->setName     ( "vlCreditoInterno" );
$obTxtCreditoInterno->setId       ( "vlCreditoInterno" );
$obTxtCreditoInterno->setRotulo   ( "Percentual de Operação de Crédito Interno"             );
$obTxtCreditoInterno->setTitle    ( "Informe o percentual de operação de crédito interno."  );
$obTxtCreditoInterno->setDecimais ( 2 );
$obTxtCreditoInterno->setNegativo ( false );
$obTxtCreditoInterno->setNull     ( false );
$obTxtCreditoInterno->setSize     ( 10 );
$obTxtCreditoInterno->setMaxLength( 5 );
$obTxtCreditoInterno->setMinValue ( 0 );
$obTxtCreditoInterno->setMaxValue ( 100 );
$obTxtCreditoInterno->setValue    ( $vlCreditoInterno  );

// Define Objeto Numeric para CreditoInterno
$obTxtCreditoExterno = new Numerico;
$obTxtCreditoExterno->setName     ( "vlCreditoExterno" );
$obTxtCreditoExterno->setId       ( "vlCreditoExterno" );
$obTxtCreditoExterno->setRotulo   ( "Percentual de Operação de Crédito Externo"             );
$obTxtCreditoExterno->setTitle    ( "Informe o percentual de operação de crédito externo."  );
$obTxtCreditoExterno->setDecimais ( 2 );
$obTxtCreditoExterno->setNegativo ( false );
$obTxtCreditoExterno->setNull     ( false );
$obTxtCreditoExterno->setSize     ( 10 );
$obTxtCreditoExterno->setMaxLength( 5 );
$obTxtCreditoExterno->setMinValue ( 0 );
$obTxtCreditoExterno->setMaxValue ( 100 );
$obTxtCreditoExterno->setValue    ( $vlCreditoExterno  );

//Lei Alteração PPA (campo para buscar norma)
$obBscNormaAlteracao = new BuscaInner;
$obBscNormaAlteracao->setRotulo ( "*Lei Alteração PPA"   );
$obBscNormaAlteracao->setTitle  ( "Selecione uma Lei Orçamentária." );
$obBscNormaAlteracao->setId     ( "stNorma"              );
$obBscNormaAlteracao->setName   ( "stNorma"              );
$obBscNormaAlteracao->obCampoCod->setName     ( "stCodNorma" );
$obBscNormaAlteracao->obCampoCod->setId       ( "stCodNorma" );
$obBscNormaAlteracao->obCampoCod->setSize     ( 10           );
$obBscNormaAlteracao->obCampoCod->setMaxLength( 7            );
$obBscNormaAlteracao->obCampoCod->setAlign    ( "left"       );
$obBscNormaAlteracao->obCampoCod->obEvento->setOnChange("buscaValor('PreencheAlteracaoNorma'); ");
$obBscNormaAlteracao->setFuncaoBusca("abrePopUp('".CAM_GA_NORMAS_POPUPS."normas/FLNorma.php','frm','stCodNorma','stNorma','','".Sessao::getId()."','800','550');");

//Data Publicação da Lei Alteração - campo data
$obDtAlteracaoPub = new Data();
$obDtAlteracaoPub->setRotulo ( "*Data Publicação da Lei Alteração");
$obDtAlteracaoPub->setName   ( "stDataAlteracaoPPA" );

$obHdnInId = new Hidden;
$obHdnInId->setName("hdnInId");
$obHdnInId->setId  ("hdnInId");

//Botão para Incluir 
$obBtnIncluir = new Button;
$obBtnIncluir->setValue('Incluir');
$obBtnIncluir->setId('btIncluirLei');
$obBtnIncluir->obEvento->setOnClick("buscaValor('incluirLista');");

$obSpnLista = new Span();
$obSpnLista->setId('spnListaNormaAlteracao');

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addHidden($obHdnInId);

$obFormulario->addTitulo('Lei Orçamentária Anual');
$obFormulario->addComponente($obTxtComplementacaoLoa);
$obFormulario->addComponente($obBscNorma);

$obFormulario->addTitulo('Percentuais da LOA');
$obFormulario->addComponente($obTxtCreditoAdicional);
$obFormulario->addComponente($obTxtCreditoAntecipacao);
$obFormulario->addComponente($obTxtCreditoInterno);
$obFormulario->addComponente($obTxtCreditoExterno);

$obFormulario->addTitulo('Alterações PPA');
$obFormulario->addComponente($obBscNormaAlteracao);
$obFormulario->addComponente($obDtAlteracaoPub);
$obFormulario->addComponente($obBtnIncluir);
$obFormulario->addSpan($obSpnLista);

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

$jsOnload = "buscaValor('montarLista');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>


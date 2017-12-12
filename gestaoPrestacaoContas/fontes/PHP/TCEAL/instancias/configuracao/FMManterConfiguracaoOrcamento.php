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

    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
    * $Id: FMManterConfiguracaoOrcamento.php 60639 2014-11-05 12:33:42Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_FW_COMPONENTES . '/Table/Table.class.php');
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
include_once(CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoOrcamento.class.php');
include_once(CAM_GA_NORMAS_MAPEAMENTO."TNorma.class.php");

$stPrograma = 'ManterConfiguracaoOrcamento';
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
$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_cod_norma");
$obTExportacaoConfiguracao->consultar();
$vlCodNorma = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_complementacao_loa");
$obTExportacaoConfiguracao->consultar();
$vlComplementacaoLoa = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_credito_adicional");
$obTExportacaoConfiguracao->consultar();
$vlCreditoAdicional = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_credito_antecipacao");
$obTExportacaoConfiguracao->consultar();
$vlCreditoAntecipacao = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao = clone $obTExportacaoConfiguracao;
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_credito_interno");
$obTExportacaoConfiguracao->consultar();
$vlCreditoInterno = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_credito_externo");
$obTExportacaoConfiguracao->consultar();
$vlCreditoExterno = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_metas_receitas_anuais");
$obTExportacaoConfiguracao->consultar();
$vlMetasReceitasAnuais = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_receitas_primarias");
$obTExportacaoConfiguracao->consultar();
$vlReceitasPrimarias = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_metas_despesas_anuais");
$obTExportacaoConfiguracao->consultar();
$vlMetasDespesasAnuais = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_despesas_primarias");
$obTExportacaoConfiguracao->consultar();
$vlDespesasPrimarias = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_resultado_primario");
$obTExportacaoConfiguracao->consultar();
$vlResultadoPrimario = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_resultado_nominal");
$obTExportacaoConfiguracao->consultar();
$vlResultadoNominal = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_divida_publica_consolidada");
$obTExportacaoConfiguracao->consultar();
$vlDividaPublicaConsolidada = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao = new TTCEALConfiguracaoOrcamento;
$obTExportacaoConfiguracao->setDado("cod_modulo", 62);
$obTExportacaoConfiguracao->setDado("parametro",  "tceal_config_divida_publica_liquida");
$obTExportacaoConfiguracao->consultar();
$vlDividaPublicaLiquida = $obTExportacaoConfiguracao->getDado('valor');

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

// Define Objeto BuscaInner para Norma
$obBscNorma = new BuscaInner;
$obBscNorma->setRotulo ( "Lei/Decreto"   );
$obBscNorma->setTitle  ( "Selecione uma lei ou decreto." );
$obBscNorma->setNulL   ( false                      );
$obBscNorma->setId     ( "stNomTipoNorma"           );
$obBscNorma->setValue  ( $nomNorma                );
$obBscNorma->obCampoCod->setName     ( "inCodNorma" );
$obBscNorma->obCampoCod->setId       ( "inCodNorma" );
$obBscNorma->obCampoCod->setSize     ( 10           );
$obBscNorma->obCampoCod->setMaxLength( 7            );
$obBscNorma->obCampoCod->setValue    ( $vlCodNorma );
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
$obTxtCreditoAdicional->setRotulo   ( "Percentual de Crédito Adicional"   );
$obTxtCreditoAdicional->setTitle    ( "Informe o percentual de crédito adicional." );
$obTxtCreditoAdicional->setDecimais ( 4 );
$obTxtCreditoAdicional->setNegativo ( false );
$obTxtCreditoAdicional->setNull     ( false );
$obTxtCreditoAdicional->setSize     ( 10 );
$obTxtCreditoAdicional->setMaxLength( 10 );
$obTxtCreditoAdicional->setMinValue ( 0 );
$obTxtCreditoAdicional->setMaxValue ( 100 );
$obTxtCreditoAdicional->setValue    ( $vlCreditoAdicional  );

// Define Objeto Numeric para CreditoAntecipacao
$obTxtCreditoAntecipacao = new Numerico;
$obTxtCreditoAntecipacao->setName     ( "vlCreditoAntecipacao" );
$obTxtCreditoAntecipacao->setId       ( "vlCreditoAntecipacao" );
$obTxtCreditoAntecipacao->setRotulo   ( "Percentual de Operações de Crédito por Antecipação"   );
$obTxtCreditoAntecipacao->setTitle    ( "Informe o percentual de operações de crédito por antecipação." );
$obTxtCreditoAntecipacao->setDecimais ( 4 );
$obTxtCreditoAntecipacao->setNegativo ( false );
$obTxtCreditoAntecipacao->setNull     ( false );
$obTxtCreditoAntecipacao->setSize     ( 10 );
$obTxtCreditoAntecipacao->setMaxLength( 10 );
$obTxtCreditoAntecipacao->setMinValue ( 0 );
$obTxtCreditoAntecipacao->setMaxValue ( 100 );
$obTxtCreditoAntecipacao->setValue    ( $vlCreditoAntecipacao  );

// Define Objeto Numeric para CreditoInterno
$obTxtCreditoInterno = new Numerico;
$obTxtCreditoInterno->setName     ( "vlCreditoInterno" );
$obTxtCreditoInterno->setId       ( "vlCreditoInterno" );
$obTxtCreditoInterno->setRotulo   ( "Percentual de Operações de Crédito Interno"   );
$obTxtCreditoInterno->setTitle    ( "Informe o percentual de operações de crédito interno." );
$obTxtCreditoInterno->setDecimais ( 4 );
$obTxtCreditoInterno->setNegativo ( false );
$obTxtCreditoInterno->setNull     ( false );
$obTxtCreditoInterno->setSize     ( 10 );
$obTxtCreditoInterno->setMaxLength( 10 );
$obTxtCreditoInterno->setMinValue ( 0 );
$obTxtCreditoInterno->setMaxValue ( 100 );
$obTxtCreditoInterno->setValue    ( $vlCreditoInterno  );

// Define Objeto Numeric para CreditoInterno
$obTxtCreditoExterno = new Numerico;
$obTxtCreditoExterno->setName     ( "vlCreditoExterno" );
$obTxtCreditoExterno->setId       ( "vlCreditoExterno" );
$obTxtCreditoExterno->setRotulo   ( "Percentual de Operações de Crédito Externo"   );
$obTxtCreditoExterno->setTitle    ( "Informe o percentual de operações de crédito externo." );
$obTxtCreditoExterno->setDecimais ( 4 );
$obTxtCreditoExterno->setNegativo ( false );
$obTxtCreditoExterno->setNull     ( false );
$obTxtCreditoExterno->setSize     ( 10 );
$obTxtCreditoExterno->setMaxLength( 10 );
$obTxtCreditoExterno->setMinValue ( 0 );
$obTxtCreditoExterno->setMaxValue ( 100 );
$obTxtCreditoExterno->setValue    ( $vlCreditoExterno  );

######### Configuração de Órgão Unidade

$obTxtOrgaoExecutivo = new TextBox;
$obTxtOrgaoExecutivo->setName        ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setId          ( "inCodOrgaoExecutivo" );
$obTxtOrgaoExecutivo->setRotulo      ( "Órgão Poder Executivo" );
$obTxtOrgaoExecutivo->setTitle       ( "Informe o código do orgão relativo ao poder executivo");
$obTxtOrgaoExecutivo->setInteiro     ( true );
$obTxtOrgaoExecutivo->setSize        ( 4 );
$obTxtOrgaoExecutivo->setMaxLength   ( "2" );
$obTxtOrgaoExecutivo->setNull        ( false );
$obTxtOrgaoExecutivo->setValue       ( $vlCodOrgaoExecutivo  );

######### Metas Fiscais Anexo 1

$obTxtMetasReceitasAnuais = new Numerico;
$obTxtMetasReceitasAnuais->setName     ( "nuMetasReceitasAnuais" );
$obTxtMetasReceitasAnuais->setId       ( "nuMetasReceitasAnuais" );
$obTxtMetasReceitasAnuais->setRotulo   ( "Metas de Receitas Anuais"   );
$obTxtMetasReceitasAnuais->setTitle    ( "Informe as metas de receitas anuais." );
$obTxtMetasReceitasAnuais->setDecimais ( 4 );
$obTxtMetasReceitasAnuais->setNegativo ( false );
$obTxtMetasReceitasAnuais->setNull     ( false );
$obTxtMetasReceitasAnuais->setSize     ( 10 );
$obTxtMetasReceitasAnuais->setMaxLength( 10 );
$obTxtMetasReceitasAnuais->setMinValue ( 0 );
$obTxtMetasReceitasAnuais->setMaxValue ( 100 );
$obTxtMetasReceitasAnuais->setValue    ( $vlMetasReceitasAnuais  );

$obTxtReceitasPrimarias = new Numerico;
$obTxtReceitasPrimarias->setName     ( "nuReceitasPrimarias" );
$obTxtReceitasPrimarias->setId       ( "nuReceitasPrimarias" );
$obTxtReceitasPrimarias->setRotulo   ( "Receitas Primárias"   );
$obTxtReceitasPrimarias->setTitle    ( "Informe as receitas primárias." );
$obTxtReceitasPrimarias->setDecimais ( 4 );
$obTxtReceitasPrimarias->setNegativo ( false );
$obTxtReceitasPrimarias->setNull     ( false );
$obTxtReceitasPrimarias->setSize     ( 10 );
$obTxtReceitasPrimarias->setMaxLength( 10 );
$obTxtReceitasPrimarias->setMinValue ( 0 );
$obTxtReceitasPrimarias->setMaxValue ( 100 );
$obTxtReceitasPrimarias->setValue    ( $vlReceitasPrimarias );

$obTxtMetasDespesasAnuais = new Numerico;
$obTxtMetasDespesasAnuais->setName     ( "nuMetasDespesasAnuais" );
$obTxtMetasDespesasAnuais->setId       ( "nuMetasDespesasAnuais" );
$obTxtMetasDespesasAnuais->setRotulo   ( "Metas de Despesas Anuais"   );
$obTxtMetasDespesasAnuais->setTitle    ( "Informe o percentual de operações de crédito externo." );
$obTxtMetasDespesasAnuais->setDecimais ( 4 );
$obTxtMetasDespesasAnuais->setNegativo ( false );
$obTxtMetasDespesasAnuais->setNull     ( false );
$obTxtMetasDespesasAnuais->setSize     ( 10 );
$obTxtMetasDespesasAnuais->setMaxLength( 10 );
$obTxtMetasDespesasAnuais->setMinValue ( 0 );
$obTxtMetasDespesasAnuais->setMaxValue ( 100 );
$obTxtMetasDespesasAnuais->setValue    ( $vlMetasDespesasAnuais  );

$obTxtDespesasPrimarias = new Numerico;
$obTxtDespesasPrimarias->setName     ( "nuDespesasPrimarias" );
$obTxtDespesasPrimarias->setId       ( "nuDespesasPrimarias" );
$obTxtDespesasPrimarias->setRotulo   ( "Despesas Primárias"   );
$obTxtDespesasPrimarias->setTitle    ( "Informe as despesas primárias." );
$obTxtDespesasPrimarias->setDecimais ( 4 );
$obTxtDespesasPrimarias->setNegativo ( false );
$obTxtDespesasPrimarias->setNull     ( false );
$obTxtDespesasPrimarias->setSize     ( 10 );
$obTxtDespesasPrimarias->setMaxLength( 10 );
$obTxtDespesasPrimarias->setMinValue ( 0 );
$obTxtDespesasPrimarias->setMaxValue ( 100 );
$obTxtDespesasPrimarias->setValue    ( $vlDespesasPrimarias  );

$obTxtResultadoPrimario = new Numerico;
$obTxtResultadoPrimario->setName     ( "nuResultadoPrimario" );
$obTxtResultadoPrimario->setId       ( "nuResultadoPrimario" );
$obTxtResultadoPrimario->setRotulo   ( "Resultado Primário"   );
$obTxtResultadoPrimario->setTitle    ( "Informe o resultado primário." );
$obTxtResultadoPrimario->setDecimais ( 4 );
$obTxtResultadoPrimario->setNegativo ( false );
$obTxtResultadoPrimario->setNull     ( false );
$obTxtResultadoPrimario->setSize     ( 10 );
$obTxtResultadoPrimario->setMaxLength( 10 );
$obTxtResultadoPrimario->setMinValue ( 0 );
$obTxtResultadoPrimario->setMaxValue ( 100 );
$obTxtResultadoPrimario->setValue    ( $vlResultadoPrimario  );

$obTxtResultadoNominal = new Numerico;
$obTxtResultadoNominal->setName     ( "nuResultadoNominal" );
$obTxtResultadoNominal->setId       ( "nuResultadoNominal" );
$obTxtResultadoNominal->setRotulo   ( "Resultado Nominal"   );
$obTxtResultadoNominal->setTitle    ( "Informe o resultado nominal." );
$obTxtResultadoNominal->setDecimais ( 4 );
$obTxtResultadoNominal->setNegativo ( false );
$obTxtResultadoNominal->setNull     ( false );
$obTxtResultadoNominal->setSize     ( 10 );
$obTxtResultadoNominal->setMaxLength( 10 );
$obTxtResultadoNominal->setMinValue ( 0 );
$obTxtResultadoNominal->setMaxValue ( 100 );
$obTxtResultadoNominal->setValue    ( $vlResultadoNominal  );

$obTxtDividaPublicaConsolidada = new Numerico;
$obTxtDividaPublicaConsolidada->setName     ( "nuDividaPublicaConsolidada" );
$obTxtDividaPublicaConsolidada->setId       ( "nuDividaPublicaConsolidada" );
$obTxtDividaPublicaConsolidada->setRotulo   ( "Dívida Pública Consolidada" );
$obTxtDividaPublicaConsolidada->setTitle    ( "Informe a dívida pública consolidada." );
$obTxtDividaPublicaConsolidada->setDecimais ( 4 );
$obTxtDividaPublicaConsolidada->setNegativo ( false );
$obTxtDividaPublicaConsolidada->setNull     ( false );
$obTxtDividaPublicaConsolidada->setSize     ( 10 );
$obTxtDividaPublicaConsolidada->setMaxLength( 10 );
$obTxtDividaPublicaConsolidada->setMinValue ( 0 );
$obTxtDividaPublicaConsolidada->setMaxValue ( 100 );
$obTxtDividaPublicaConsolidada->setValue    ( $vlDividaPublicaConsolidada  );

$obTxtDividaPublicaLiquida = new Numerico;
$obTxtDividaPublicaLiquida->setName     ( "nuDividaPublicaLiquida" );
$obTxtDividaPublicaLiquida->setId       ( "nuDividaPublicaLiquida" );
$obTxtDividaPublicaLiquida->setRotulo   ( "Dívida Pública Líquida" );
$obTxtDividaPublicaLiquida->setTitle    ( "Informe a dívida pública líquida." );
$obTxtDividaPublicaLiquida->setDecimais ( 4 );
$obTxtDividaPublicaLiquida->setNegativo ( false );
$obTxtDividaPublicaLiquida->setNull     ( false );
$obTxtDividaPublicaLiquida->setSize     ( 10 );
$obTxtDividaPublicaLiquida->setMaxLength( 10 );
$obTxtDividaPublicaLiquida->setMinValue ( 0 );
$obTxtDividaPublicaLiquida->setMaxValue ( 100 );
$obTxtDividaPublicaLiquida->setValue    ( $vlDividaPublicaLiquida  );

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

$obFormulario->addTitulo('Lei Orçamentária Anual');
$obFormulario->addComponente($obBscNorma);
$obFormulario->addComponente($obTxtComplementacaoLoa);
$obFormulario->addTitulo('Percentuais da LOA');
$obFormulario->addComponente($obTxtCreditoAdicional);
$obFormulario->addComponente($obTxtCreditoAntecipacao);
$obFormulario->addComponente($obTxtCreditoInterno);
$obFormulario->addComponente($obTxtCreditoExterno);
$obFormulario->addTitulo('Metas Fiscais Anexo 1');
$obFormulario->addComponente( $obTxtMetasReceitasAnuais );
$obFormulario->addComponente( $obTxtReceitasPrimarias );
$obFormulario->addComponente( $obTxtMetasDespesasAnuais );
$obFormulario->addComponente( $obTxtDespesasPrimarias );
$obFormulario->addComponente( $obTxtResultadoPrimario );
$obFormulario->addComponente( $obTxtResultadoNominal );
$obFormulario->addComponente( $obTxtDividaPublicaConsolidada );
$obFormulario->addComponente( $obTxtDividaPublicaLiquida );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

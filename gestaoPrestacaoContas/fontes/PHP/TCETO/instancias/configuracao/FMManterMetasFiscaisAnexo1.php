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

    * Pacote de configuração do TCETO - Formulário Configurar Metas Fiscais Anexo 1
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FMManterMetasFiscaisAnexo1.php 60958 2014-11-26 13:57:16Z jean $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_FW_COMPONENTES.'/Table/Table.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
include_once CAM_GA_NORMAS_MAPEAMENTO.'TNorma.class.php';

$stPrograma = 'ManterMetasFiscaisAnexo1';
$pgFilt = 'FL'.$stPrograma.'.php';
$pgList = 'LS'.$stPrograma.'.php';
$pgForm = 'FM'.$stPrograma.'.php';
$pgProc = 'PR'.$stPrograma.'.php';
$pgOcul = 'OC'.$stPrograma.'.php';
$pgJs   = 'JS'.$stPrograma.'.js';

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

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_metas_receitas_anuais");
$obTExportacaoConfiguracao->consultar();
$vlMetasReceitasAnuais = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_receitas_primarias");
$obTExportacaoConfiguracao->consultar();
$vlReceitasPrimarias = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_metas_despesas_anuais");
$obTExportacaoConfiguracao->consultar();
$vlMetasDespesasAnuais = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_despesas_primarias");
$obTExportacaoConfiguracao->consultar();
$vlDespesasPrimarias = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_resultado_primario");
$obTExportacaoConfiguracao->consultar();
$vlResultadoPrimario = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_resultado_nominal");
$obTExportacaoConfiguracao->consultar();
$vlResultadoNominal = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_divida_publica_consolidada");
$obTExportacaoConfiguracao->consultar();
$vlDividaPublicaConsolidada = $obTExportacaoConfiguracao->getDado('valor');

$obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_divida_consolidada_liquida");
$obTExportacaoConfiguracao->consultar();
$vlDividaConsolidadaLiquida = $obTExportacaoConfiguracao->getDado('valor');

######### Metas Fiscais Anexo 1
$obTxtMetasReceitasAnuais = new Numerico;
$obTxtMetasReceitasAnuais->setName     ( "nuMetasReceitasAnuais" );
$obTxtMetasReceitasAnuais->setId       ( "nuMetasReceitasAnuais" );
$obTxtMetasReceitasAnuais->setRotulo   ( "Metas de Receita Anual"   );
$obTxtMetasReceitasAnuais->setTitle    ( "Informe as metas de receita anual." );
$obTxtMetasReceitasAnuais->setDecimais ( 2 );
$obTxtMetasReceitasAnuais->setNegativo ( false );
$obTxtMetasReceitasAnuais->setNull     ( false );
$obTxtMetasReceitasAnuais->setSize     ( 15 );
$obTxtMetasReceitasAnuais->setMaxLength( 15 );
$obTxtMetasReceitasAnuais->setMinValue ( 0 );
$obTxtMetasReceitasAnuais->setValue    ( $vlMetasReceitasAnuais  );

$obTxtReceitasPrimarias = new Numerico;
$obTxtReceitasPrimarias->setName     ( "nuReceitasPrimarias" );
$obTxtReceitasPrimarias->setId       ( "nuReceitasPrimarias" );
$obTxtReceitasPrimarias->setRotulo   ( "Receita Primária"   );
$obTxtReceitasPrimarias->setTitle    ( "Informe a receita primária." );
$obTxtReceitasPrimarias->setDecimais ( 2 );
$obTxtReceitasPrimarias->setNegativo ( false );
$obTxtReceitasPrimarias->setNull     ( true  );
$obTxtReceitasPrimarias->setSize     ( 15 );
$obTxtReceitasPrimarias->setMaxLength( 15 );
$obTxtReceitasPrimarias->setMinValue ( 0 );
$obTxtReceitasPrimarias->setValue    ( $vlReceitasPrimarias );

$obTxtMetasDespesasAnuais = new Numerico;
$obTxtMetasDespesasAnuais->setName     ( "nuMetasDespesasAnuais" );
$obTxtMetasDespesasAnuais->setId       ( "nuMetasDespesasAnuais" );
$obTxtMetasDespesasAnuais->setRotulo   ( "Metas de Despesa Anual"   );
$obTxtMetasDespesasAnuais->setTitle    ( "Informe as metas de despesa anual." );
$obTxtMetasDespesasAnuais->setDecimais ( 2 );
$obTxtMetasDespesasAnuais->setNegativo ( false );
$obTxtMetasDespesasAnuais->setNull     ( true  );
$obTxtMetasDespesasAnuais->setSize     ( 15 );
$obTxtMetasDespesasAnuais->setMaxLength( 15 );
$obTxtMetasDespesasAnuais->setMinValue ( 0 );
$obTxtMetasDespesasAnuais->setValue    ( $vlMetasDespesasAnuais  );

$obTxtDespesasPrimarias = new Numerico;
$obTxtDespesasPrimarias->setName     ( "nuDespesasPrimarias" );
$obTxtDespesasPrimarias->setId       ( "nuDespesasPrimarias" );
$obTxtDespesasPrimarias->setRotulo   ( "Despesa Primária"   );
$obTxtDespesasPrimarias->setTitle    ( "Informe a despesa primária." );
$obTxtDespesasPrimarias->setDecimais ( 2 );
$obTxtDespesasPrimarias->setNegativo ( false );
$obTxtDespesasPrimarias->setNull     ( true  );
$obTxtDespesasPrimarias->setSize     ( 15 );
$obTxtDespesasPrimarias->setMaxLength( 15 );
$obTxtDespesasPrimarias->setMinValue ( 0 );
$obTxtDespesasPrimarias->setValue    ( $vlDespesasPrimarias  );

$obTxtResultadoPrimario = new Numerico;
$obTxtResultadoPrimario->setName     ( "nuResultadoPrimario" );
$obTxtResultadoPrimario->setId       ( "nuResultadoPrimario" );
$obTxtResultadoPrimario->setRotulo   ( "Resultado Primário"   );
$obTxtResultadoPrimario->setTitle    ( "Informe o resultado primário." );
$obTxtResultadoPrimario->setDecimais ( 2 );
$obTxtResultadoPrimario->setNegativo ( false );
$obTxtResultadoPrimario->setNull     ( true  );
$obTxtResultadoPrimario->setSize     ( 15 );
$obTxtResultadoPrimario->setMaxLength( 15 );
$obTxtResultadoPrimario->setMinValue ( 0 );
$obTxtResultadoPrimario->setValue    ( $vlResultadoPrimario  );

$obTxtResultadoNominal = new Numerico;
$obTxtResultadoNominal->setName     ( "nuResultadoNominal" );
$obTxtResultadoNominal->setId       ( "nuResultadoNominal" );
$obTxtResultadoNominal->setRotulo   ( "Resultado Nominal"   );
$obTxtResultadoNominal->setTitle    ( "Informe o resultado nominal." );
$obTxtResultadoNominal->setDecimais ( 2 );
$obTxtResultadoNominal->setNegativo ( false );
$obTxtResultadoNominal->setNull     ( true  );
$obTxtResultadoNominal->setSize     ( 15 );
$obTxtResultadoNominal->setMaxLength( 15 );
$obTxtResultadoNominal->setMinValue ( 0 );
$obTxtResultadoNominal->setValue    ( $vlResultadoNominal  );

$obTxtDividaPublicaConsolidada = new Numerico;
$obTxtDividaPublicaConsolidada->setName     ( "nuDividaPublicaConsolidada" );
$obTxtDividaPublicaConsolidada->setId       ( "nuDividaPublicaConsolidada" );
$obTxtDividaPublicaConsolidada->setRotulo   ( "Dívida Pública Consolidada" );
$obTxtDividaPublicaConsolidada->setTitle    ( "Informe a dívida pública consolidada." );
$obTxtDividaPublicaConsolidada->setDecimais ( 2 );
$obTxtDividaPublicaConsolidada->setNegativo ( false );
$obTxtDividaPublicaConsolidada->setNull     ( true  );
$obTxtDividaPublicaConsolidada->setSize     ( 15 );
$obTxtDividaPublicaConsolidada->setMaxLength( 15 );
$obTxtDividaPublicaConsolidada->setMinValue ( 0 );
$obTxtDividaPublicaConsolidada->setValue    ( $vlDividaPublicaConsolidada  );

$obTxtDividaPublicaLiquida = new Numerico;
$obTxtDividaPublicaLiquida->setName     ( "nuDividaConsolidadaLiquida" );
$obTxtDividaPublicaLiquida->setId       ( "nuDividaConsolidadaLiquida" );
$obTxtDividaPublicaLiquida->setRotulo   ( "Dívida Consolidada Líquida" );
$obTxtDividaPublicaLiquida->setTitle    ( "Informe a dívida consolidada líquida." );
$obTxtDividaPublicaLiquida->setDecimais ( 2 );
$obTxtDividaPublicaLiquida->setNegativo ( false );
$obTxtDividaPublicaLiquida->setNull     ( true  );
$obTxtDividaPublicaLiquida->setSize     ( 15 );
$obTxtDividaPublicaLiquida->setMaxLength( 15 );
$obTxtDividaPublicaLiquida->setMinValue ( 0 );
$obTxtDividaPublicaLiquida->setValue    ( $vlDividaConsolidadaLiquida  );

$obFormulario = new Formulario();
$obFormulario->addForm($obForm);
$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);

$obFormulario->addTitulo('Metas Fiscais Anexo 1');
$obFormulario->addComponente( $obTxtMetasReceitasAnuais         );
$obFormulario->addComponente( $obTxtReceitasPrimarias           );
$obFormulario->addComponente( $obTxtMetasDespesasAnuais         );
$obFormulario->addComponente( $obTxtDespesasPrimarias           );
$obFormulario->addComponente( $obTxtResultadoPrimario           );
$obFormulario->addComponente( $obTxtResultadoNominal            );
$obFormulario->addComponente( $obTxtDividaPublicaConsolidada    );
$obFormulario->addComponente( $obTxtDividaPublicaLiquida        );

$obOk  = new Ok;
$obLimpar = new Button;
$obLimpar->setValue( "Limpar" );
$obLimpar->obEvento->setOnClick( "limpaFormulario();" );

$obFormulario->defineBarra( array( $obOk, $obLimpar ) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

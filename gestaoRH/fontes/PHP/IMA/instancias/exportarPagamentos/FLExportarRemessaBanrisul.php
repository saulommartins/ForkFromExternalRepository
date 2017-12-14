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
    * Página de Formulário do Exportação Remessa Banrisul
    * Data de Criação: 26/02/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: FLExportarRemessaBanrisul.php 65860 2016-06-22 18:08:07Z michel $

    * Casos de uso: uc-04.08.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBanrisul.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBanrisul";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$jsOnload  = "montaParametrosGET('gerarSpan','stSituacao,inAno,inCodMes');";
$jsOnload .= "montaParametrosGET('atualizarGrupoConta','inAno,inCodMes');";

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnTipoFiltroExtra = new hiddenEval();
$obHdnTipoFiltroExtra->setName("hdnTipoFiltroExtra");
$obHdnTipoFiltroExtra->setValue("eval(document.frm.hdnTipoFiltro.value);");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','inCodConfiguracao,stSituacao,stAcao,inCodComplementar,stDesdobramento,stTipoFiltro,inCodAtributo,inCodMes,inAno,inNumeroSequencial',true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

$obComboSituacao = new Select;
$obComboSituacao->setRotulo ( "Cadastro"                                            );
$obComboSituacao->setTitle  ( "Selecione o cadastro para filtro."                   );
$obComboSituacao->setName   ( "stSituacao"                                          );
$obComboSituacao->setValue  ( "ativos"                                              );
$obComboSituacao->setStyle  ( "width: 200px"                                        );
$obComboSituacao->addOption ( "", "Selecione"                                       );
$obComboSituacao->addOption ( "ativos", "Ativos"                                    );
$obComboSituacao->addOption ( "aposentados", "Aposentados"                          );
$obComboSituacao->addOption ( "pensionistas", "Pensionistas"                        );
$obComboSituacao->addOption ( "estagiarios", "Estagiários"                          );
$obComboSituacao->addOption ( "rescindidos", "Rescindidos"                          );
$obComboSituacao->addOption ( "pensao_judicial", "Pensão Judicial"                  );
$obComboSituacao->addOption ( "todos", "Todos"                                      );
$obComboSituacao->setNull   ( false                                                 );
$obComboSituacao->obEvento->setOnChange("montaParametrosGET('gerarSpan','stSituacao,inAno,inCodMes');");

$obSpnCadastro = new Span();
$obSpnCadastro->setId("spnCadastro");

$obSpnAtivosAposentadosPensionistas = new Span();
$obSpnAtivosAposentadosPensionistas->setId("spnAtivosAposentadosPensionistas");

$obMonValorLiquidoInicial = new Moeda();
$obMonValorLiquidoInicial->setRotulo("Filtrar Valores Líquidos de:");
$obMonValorLiquidoInicial->setName("nuValorLiquidoInicial");
$obMonValorLiquidoInicial->setTitle("Informe a faixa de salários líquidos que deverão ser considerados no arquivo.");
$obMonValorLiquidoInicial->setValue("0,01");
$obMonValorLiquidoInicial->obEvento->setOnChange("montaParametrosGET('validarValores','nuValorLiquidoInicial,nuValorLiquidoFinal');");

$obLblAte = new Label();
$obLblAte->setValue("Até");

$obMonValorLiquidoFinal = new Moeda();
$obMonValorLiquidoFinal->setName("nuValorLiquidoFinal");
$obMonValorLiquidoFinal->setValue("99.999.999,99");
$obMonValorLiquidoFinal->obEvento->setOnChange("montaParametrosGET('validarValores','nuValorLiquidoInicial,nuValorLiquidoFinal');");

$arFiltrarValores = array($obMonValorLiquidoInicial,$obLblAte,$obMonValorLiquidoFinal);

$obPercentual = new Numerico();
$obPercentual->setRotulo("Percentual à Pagar do Líquido:");
$obPercentual->setName("nuPercentualPagar");
$obPercentual->setTitle("Informe o percentual à pagar do salário líquido de cada servidor.");
$obPercentual->setSize(10);
$obPercentual->setMaxLength(6);
$obPercentual->setValue("100,00");

$obLblPercentual = new Label();
$obLblPercentual->setValue("%");

$arPercentual = array($obPercentual,$obLblPercentual);

$obTIMAConfiguracaoConvenioBanrisul = new TIMAConfiguracaoConvenioBanrisul();
$obTIMAConfiguracaoConvenioBanrisul->recuperaTodos($rsConfiguracaoConvenio);
Sessao::write("rsConfiguracaoConvenio", $rsConfiguracaoConvenio);

$obLblCodigoConvenio = new Label();
$obLblCodigoConvenio->setRotulo("Código do Convênio no Banco");
$obLblCodigoConvenio->setNull(false);
$obLblCodigoConvenio->setValue($rsConfiguracaoConvenio->getCampo("cod_convenio_banco"));

$obSpnGrupoContas = new Span();
$obSpnGrupoContas->setId("spnGrupoContas");

$obLblAgenciaConvenio = new Label();
$obLblAgenciaConvenio->setRotulo("Agência do Convênio");
$obLblAgenciaConvenio->setNull(false);
$obLblAgenciaConvenio->setValue($rsConfiguracaoConvenio->getCampo("num_agencia")." - ".$rsConfiguracaoConvenio->getCampo("nom_agencia"));

$obLblContaConvenio = new Label();
$obLblContaConvenio->setRotulo("Conta do Convênio");
$obLblContaConvenio->setNull(false);
$obLblContaConvenio->setValue($rsConfiguracaoConvenio->getCampo("num_conta_corrente"));

$obDtGeracaoArquivo = new Data();
$obDtGeracaoArquivo->setRotulo("Data da Geração Arquivo");
$obDtGeracaoArquivo->setName("dtGeracaoArquivo");
$obDtGeracaoArquivo->setTitle("Informar a data de geração do arquivo.");
$obDtGeracaoArquivo->setNull(false);
$obDtGeracaoArquivo->setValue(date('d/m/Y'));

$obDtPagamento = new Data();
$obDtPagamento->setRotulo("Data do Pagamento");
$obDtPagamento->setName("dtPagamento");
$obDtPagamento->setTitle("Informar a data provável de pagamento.");
$obDtPagamento->setNull(false);
$obDtPagamento->setValue(date('d/m/Y'));
$obDtPagamento->obEvento->setOnChange("montaParametrosGET('validarDataPagamento','dtPagamento,dtGeracaoArquivo')");

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
$obTAdministracaoConfiguracao->pegaConfiguracao($stValor,"num_sequencial_arquivo_banrisul".Sessao::getEntidade());

$obIntNumeroSequencial = new Inteiro();
$obIntNumeroSequencial->setRotulo("Número Seqüencial Arquivo");
$obIntNumeroSequencial->setTitle("Informar o número da remessa. Deve ser seqüencial e maior que zero. Deve repetir-se somente quando o tipo de movimento for Alteração de Lançamento. Para Inclusões de Lançamentos Novos, o número deverá ser crescente (sequencial anterior + 1).");
$obIntNumeroSequencial->setName("inNumeroSequencial");
$obIntNumeroSequencial->setId("inNumeroSequencial");
$obIntNumeroSequencial->setValue($stValor);
$obIntNumeroSequencial->setNull(false);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario();
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addHidden     ( $obHdnCtrl );
$obFormulario->addHidden     ( $obHdnTipoFiltroExtra,true );
$obFormulario->addTitulo     ( "Seleção do Filtro" );
$obFormulario->addComponente ( $obComboSituacao );
$obFormulario->addSpan       ( $obSpnAtivosAposentadosPensionistas );
$obFormulario->addSpan       ( $obSpnCadastro );
$obFormulario->addTitulo        ( "Informações Gerais para emissão do arquivo" ,"left" );
$obFormulario->agrupaComponentes( $arFiltrarValores );
$obFormulario->agrupaComponentes( $arPercentual );
$obFormulario->addComponente    ( $obLblCodigoConvenio );
$obFormulario->addSpan          ( $obSpnGrupoContas );
$obFormulario->addComponente    ( $obDtGeracaoArquivo );
$obFormulario->addComponente    ( $obDtPagamento );
$obFormulario->addComponente    ( $obIntNumeroSequencial );
$obFormulario->defineBarra      ( array($obBtnOk,$obBtnLimpar) );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Página de Formulário do IMA Configuração - CaixaEconomicaFederal
    * Data de Criação: 31/03/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @ignore

    * Casos de uso: uc-04.08.18
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgencia.class.php"		                                     );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"		                         );
include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"		                             );

$stPrograma = "ExportacaoBancoBanPara";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$stAcao     = $request->get('stAcao');
$dtVigencia = date('d/m/Y');
if (isset($_REQUEST["dtVigencia"])) {
    $dtVigencia = $_REQUEST["dtVigencia"];
}
Sessao::write("dtVigencia",$dtVigencia);

include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanpara.class.php");
$obTIMAConfiguracaoBanpara = new TIMAConfiguracaoBanpara();

$jsOnload = "montaParametrosGET('atualizarLotacao','dtVigencia,stAcao');";
if (trim($stAcao) == "alterar") {
    $obTIMAConfiguracaoBanpara->setDado("vigencia", $dtVigencia);
    $obTIMAConfiguracaoBanpara->recuperaRelacionamento($rsConfiguracaoBanpara);

    $inCodigoEmpresa = $rsConfiguracaoBanpara->getCampo("codigo");
    Sessao::write("inCodEmpresa", $inCodigoEmpresa);
    $jsOnload .= "montaParametrosGET('preencherOrgaos', 'stAcao');";
} else {
    include_once( CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBanparaEmpresa.class.php");
    $stFiltro = " ORDER BY cod_empresa DESC limit 1";
    $obTIMAConfiguracaoBanparaEmpresa = new TIMAConfiguracaoBanparaEmpresa;
    $obTIMAConfiguracaoBanparaEmpresa->recuperaTodos($rsConfiguracaoBanparaEmpresa,$stFiltro);
    $inCodigoEmpresa = $rsConfiguracaoBanparaEmpresa->getCampo("codigo");
}

$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl" );
if (isset($stCtrl)) {
    $obHdnCtrl->setValue( $stCtrl );
}

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obHdnCodEmpresa = new hidden();
$obHdnCodEmpresa->setName("inCodEmpresa");
$obHdnCodEmpresa->setId("inCodEmpresa");
if (isset($inCodEmpresa)) {
    $obHdnCodEmpresa->setValue($inCodEmpresa);
}

$obHdnCodOrgao = new hidden();
$obHdnCodOrgao->setName("inCodOrgao");
$obHdnCodOrgao->setId("inCodOrgao");
if (isset($inCodOrgao)) {
    $obHdnCodOrgao->setValue($inCodOrgao);
}

$obHdnAssinaturaOrgao = new hidden();
$obHdnAssinaturaOrgao->setName("stAssinatura");
$obHdnAssinaturaOrgao->setId("stAssinatura");

if ($stAcao == 'alterar') {
    $obTxtCodigoEmpresa = new Label();
    $obTxtCodigoEmpresa->setRotulo          ( "Código da Empresa"	);
    $obTxtCodigoEmpresa->setValue           ( $inCodigoEmpresa      );

    $obHdnCodigoEmpresa = new hidden();
    $obHdnCodigoEmpresa->setName            ( "inCodigoEmpresa"		);
    $obHdnCodigoEmpresa->setId              ( "inCodigoEmpresa"		);
    $obHdnCodigoEmpresa->setValue           ( $inCodigoEmpresa      );
} else {
    $obTxtCodigoEmpresa = new TextBox;
    $obTxtCodigoEmpresa->setRotulo          ( "Código da Empresa"	);
    $obTxtCodigoEmpresa->setName            ( "inCodigoEmpresa"		);
    $obTxtCodigoEmpresa->setId              ( "inCodigoEmpresa"		);
    $obTxtCodigoEmpresa->setValue           ( $inCodigoEmpresa      );
    $obTxtCodigoEmpresa->setTitle           ( "Informe o código da entidade conforme numeração fornecida pelo Banco." );
    $obTxtCodigoEmpresa->setSize            ( 10                    );
    $obTxtCodigoEmpresa->setMaxLength       ( 8                     );
    $obTxtCodigoEmpresa->setInteiro         ( true                  );
    $obTxtCodigoEmpresa->setNull			( false					);
}

$obTxtCodigoOrgao = new TextBox;
$obTxtCodigoOrgao->setRotulo            ( "Código do Orgão"		);
$obTxtCodigoOrgao->setName              ( "inCodigoOrgao"       );
$obTxtCodigoOrgao->setId                ( "inCodigoOrgao"   	);
if (isset($inCodigoOrgao)) {
    $obTxtCodigoOrgao->setValue             ( $inCodigoOrgao        );
}
$obTxtCodigoOrgao->setTitle             ( "Informe o código da entidade conforme numeração fornecida pelo Banco." );
$obTxtCodigoOrgao->setSize              ( 10                    );
$obTxtCodigoOrgao->setMaxLength         ( 8                     );
$obTxtCodigoOrgao->setInteiro           ( true                  );
$obTxtCodigoOrgao->setNullBarra         ( false					);
$obTxtCodigoOrgao->setObrigatorioBarra(true);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo            ( "Descrição"		        );
$obTxtDescricao->setName              ( "stDescricao"			);
$obTxtDescricao->setId                ( "stDescricao"			);
if (isset($stDescricao)) {
    $obTxtDescricao->setValue             ( $stDescricao            );
}
$obTxtDescricao->setTitle             ( "Informe o código da entidade conforme numeração fornecida pelo Banco." );
$obTxtDescricao->setSize              ( 50                      );
$obTxtDescricao->setMaxLength         ( 40                      );
$obTxtDescricao->setNullBarra      	  ( false					);
$obTxtDescricao->setObrigatorioBarra(true);

$obISelectMultiploLotacao = new ISelectMultiploLotacao();
$obISelectMultiploLotacao->setNullBarra(false);
$obISelectMultiploLotacao->setTitle(utf8_decode("Selecione as lotações que correspondam ao código do órgão"));
$obISelectMultiploLotacao->setObrigatorioBarra(true);

$obISelectMultiploLocal = new ISelectMultiploLocal();
$obISelectMultiploLocal->setTitle(utf8_decode("Selecione os locais que correspondam ao código do órgão"));

$arComponentes = array($obTxtCodigoOrgao,
                                     $obTxtDescricao,
                                     $obISelectMultiploLotacao,
                                     $obISelectMultiploLocal
                                     );

$obSpanOrgaos = new Span;
$obSpanOrgaos->setId    ( "spnOrgaos" );

$obHdnOrgaos = new hiddenEval();
$obHdnOrgaos->setId("hdnOrgaos");

$obDtVigencia = new Data();
$obDtVigencia->setRotulo	( "Vigência"	);
$obDtVigencia->setName	    ( "dtVigencia"	);
$obDtVigencia->setId		( "dtVigencia"	);
$obDtVigencia->setValue	    ( $dtVigencia   );
$obDtVigencia->setNull      ( false			);
$obDtVigencia->obEvento->setOnChange("montaParametrosGET('atualizarLotacao','dtVigencia,stAcao');");
if (trim($stAcao)!="incluir") {
    $obDtVigencia->setReadOnly(true);
}

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter', 'inCodigoEmpresa,dtVigencia', true);");

$obBtnLimpar = new Limpar();
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparForm');");

$stName = "Orgao";

$obBtnIncluir = new Button;
$obBtnIncluir->setName              ( "btIncluir$stName"    );
$obBtnIncluir->setId                ( "btIncluir$stName"    );
$obBtnIncluir->setValue             ( "Incluir"             );
$obBtnIncluir->obEvento->setOnClick ( " if ( ValidaOrgao() ) { buscaValor('incluir$stName', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."'); }" );
$arBarra[] = $obBtnIncluir;

$obBtnAlterar = new Button;
$obBtnAlterar->setName              ( "btAlterar$stName"    );
$obBtnAlterar->setId                ( "btAlterar$stName"    );
$obBtnAlterar->setValue             ( "Alterar"             );
$obBtnAlterar->setDisabled          ( true                  );
$obBtnAlterar->obEvento->setOnClick ( " if ( ValidaOrgao() ) { buscaValor('alterar$stName', '".$pgOcul."', '".$pgProc."', '', '".Sessao::getId()."'); }" );
$arBarra[] = $obBtnAlterar;

$obBtnLimpar = new Button;
$obBtnLimpar->setName              ( "btLimpar$stName"          );
$obBtnLimpar->setValue             ( "Limpar"                   );
$obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
$arBarra[] = $obBtnLimpar;

//Teste de Periodo de Movimentação
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

$stFiltro = " WHERE dt_inicial <= to_date('".$dtVigencia."','dd/mm/yyyy')	\n";
$stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1                            \n";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

/**************************************************************************************************************************
* Define FORMULARIO
**************************************************************************************************************************/
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm                                                           );

if ( $rsPeriodoMovimentacao->getNumLinhas()>0 ) {
    $obFormulario->addHidden     ( $obHdnAcao                                                        );
    $obFormulario->addHidden     ( $obHdnCtrl                                                        );
    $obFormulario->addTitulo 	 ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" );
    $obFormulario->addTitulo     ( "Configuração da Exportação Bancária" 							 );
    $obFormulario->addTitulo     ( "Banco BanPará"                     		                         );
    $obFormulario->addComponente ( $obDtVigencia          									         );
    $obFormulario->addHidden     ( $obHdnCodEmpresa             								     );
    $obFormulario->addHidden     ( $obHdnCodOrgao             									     );
    $obFormulario->addHidden     ( $obHdnAssinaturaOrgao        								     );
    $obFormulario->addComponente ( $obTxtCodigoEmpresa             								     );
    
    if ($stAcao == 'alterar') {
        $obFormulario->addHidden ( $obHdnCodigoEmpresa             								     );
    }
    
    $obFormulario->addTitulo     ( "Configuração de Orgãos" 	                                     );
    $obFormulario->addComponente ( $obTxtCodigoOrgao                                                 );
    $obFormulario->addComponente ( $obTxtDescricao                                                   );
    $obFormulario->addComponente ( $obISelectMultiploLotacao                                         );
    $obFormulario->addComponente ( $obISelectMultiploLocal                                           );
    $obFormulario->defineBarra   ( array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar)                   );
    $obFormulario->addSpan       ( $obSpanOrgaos             									     );
    $obFormulario->defineBarra   ( array($obBtnOk,$obBtnLimpar)                                      );
}else{
    $stMensagem = "Não há período de movimentação aberto. Para efetuar a configuração da exportação bancária é necessário abri-lo.";
    $obLblMensagem = new Label;
    $obLblMensagem->setRotulo               ( "Situação"                            );
    $obLblMensagem->setValue                ( $stMensagem                           );
    $obFormulario->addTitulo                ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
    $obFormulario->addTitulo                ( "Configuração da Exportação Bancária" );
    $obFormulario->addTitulo                ( "Período de Movimentação"             );
    $obFormulario->addComponente            ( $obLblMensagem                        );
}

$obFormulario->Show();

include_once( $pgJS );

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

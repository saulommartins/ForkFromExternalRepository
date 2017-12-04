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
    * Página de Formulário do IMA Configuração
    * Data de Criação: 20/12/2006

    * @author Analista: Dagiane
    * @author Desenvolvedor: Alexandre Melo

    * @ignore

    * Casos de uso: uc-04.08.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoExportacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST['stAcao'];
Sessao::write("stNumBanco", "001");

$jsOnload = "montaParametrosGET('atualizarLotacao','dtVigencia,stAcao');";
if (trim($stAcao)!="incluir") {
    $jsOnload .= "executaFuncaoAjax('processarForm');";
}

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$dtVigencia = date('d/m/Y');
if (isset($_REQUEST["dtVigencia"])) {
    $dtVigencia = $_REQUEST["dtVigencia"];
}
Sessao::write("dtVigencia",$dtVigencia);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

//Definicao dos componentes
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setId    ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obVigencia = new Data();
$obVigencia->setRotulo	( "Vigência"	);
$obVigencia->setName	( "dtVigencia"	);
$obVigencia->setId		( "dtVigencia"	);
$obVigencia->setValue	( $dtVigencia   );
$obVigencia->setNull    ( false			);
$obVigencia->obEvento->setOnChange("montaParametrosGET('atualizarLotacao','dtVigencia,stAcao');");
if (trim($stAcao)!="incluir") {
    $obVigencia->setReadOnly(true);
}

//codigo de convenio com o banco
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBb.class.php");
$obTIMAConfiguracaoConvenioBb = new TIMAConfiguracaoConvenioBb;
$obTIMAConfiguracaoConvenioBb->recuperaTodos($rsConfiguracao);
$stCodConvenio = $rsConfiguracao->getCampo("cod_convenio_banco");

$obTxtCodigoConvenio = new TextBox;
$obTxtCodigoConvenio->setRotulo          ( "Código Convênio com Banco"		);
$obTxtCodigoConvenio->setName            ( "stCodConvenio"					);
$obTxtCodigoConvenio->setId              ( "stCodConvenio"                  );
$obTxtCodigoConvenio->setValue           ( $stCodConvenio                   );
$obTxtCodigoConvenio->setTitle           ( "Informe o Código do Convênio firmado entre a Prefeitura e o Banco." );
$obTxtCodigoConvenio->setSize            ( 12                                              					    );
$obTxtCodigoConvenio->setMaxLength       ( 10                                                                   );
$obTxtCodigoConvenio->setInteiro         ( true                                                                 );
$obTxtCodigoConvenio->setNull			 ( false											                    );
if (trim($stAcao)!="incluir") {
    $obTxtCodigoConvenio->setReadOnly(true);
}
$arComponentes = array();
$stIds = "";

//Agencia do convenio
include_once ( CAM_GT_MON_COMPONENTES."IMontaAgenciaConta.class.php");
$obTMONAgenia = new TMONAgencia;
$stFiltro = " WHERE ban.num_banco = '".Sessao::read("stNumBanco")."'";
$obTMONAgenia->recuperaRelacionamento($rsAgencia,$stFiltro);

$obIMontaAgenciaConta = new IMontaAgenciaConta();
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setNumBanco(Sessao::read("stNumBanco"));
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setDisabled(true);
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obSelect->setDisabled(true);
$obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->obSelect->preencheCombo($rsAgencia);
$obIMontaAgenciaConta->obBscConta->setNull(true);
$obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setNull(true);
$obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setNull(true);
$stIds .= $obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->obTextBox->getId().",";
$stIds .= $obIMontaAgenciaConta->obBscConta->obCampoCod->getId().",";

$obTxtDescricao = new TextBox();
$obTxtDescricao->setName("stDescricaoConvenio");
$obTxtDescricao->setId("stDescricaoConvenio");
$obTxtDescricao->setRotulo("Descrição Convênio");
$obTxtDescricao->setMaxLength(60);
$obTxtDescricao->setSize(65);
$obTxtDescricao->setTitle("Informe a descrição do convênio ou do grupo de conta para identificação.");
$obTxtDescricao->setNullBarra(false);
$arComponentes[] = $obTxtDescricao;
$stIds .= $obTxtDescricao->getId().",";

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php");
$obISelectMultiploLotacao = new ISelectMultiploLotacao();
$obISelectMultiploLotacao->setNullBarra(false);
$obISelectMultiploLotacao->setTitle(utf8_decode("Selecione as lotações para classificar por grupo de conta."));
$arComponentes[] = $obISelectMultiploLotacao;
$stIds .= $obISelectMultiploLotacao->getNomeLista2().",";

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php");
$obISelectMultiploLocal = new ISelectMultiploLocal();
$obISelectMultiploLocal->setTitle(utf8_decode("Selecione os locais para classificar por grupo de conta."));
$arComponentes[] = $obISelectMultiploLocal;
$stIds .= $obISelectMultiploLocal->getNomeLista2();

$obSpnContasConvenio = new Span();
$obSpnContasConvenio->setId("spnContasConvenio");

$stJs  = "var url = '".CAM_GRH_IMA_INSTANCIAS."configuracao/OCManterConfiguracaoExportacao.php?".Sessao::getId()."' \n";
$stJs .= "selecionaTodosSelect(inCodLotacaoSelecionados); \n";
$stJs .= "selecionaTodosSelect(inCodLocalSelecionados); \n";

$obBtnIncluir = new Button();
$obBtnIncluir->setValue("Incluir");
$obBtnIncluir->setName("btIncluirConfBB");
$obBtnIncluir->setId("btIncluirConfBB");
$obBtnIncluir->obEvento->setOnclick(" $stJs
                                      jQuery('#stCtrl').val('incluirConfBB');
                                      jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                    ");

$obBtnAlterar = new Button();
$obBtnAlterar->setValue("Alterar");
$obBtnAlterar->setName("btAlterarConfBB");
$obBtnAlterar->setId("btAlterarConfBB");
$obBtnAlterar->obEvento->setOnclick(" $stJs
                                      jQuery('#stCtrl').val('alterarConfBB');
                                      jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                    ");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setName("btLimparConfBB");
$obBtnLimpar->setId("btLimparConfBB");
$obBtnLimpar->obEvento->setOnClick("executaFuncaoAjax('limparConfBB');");

//Envia codigo do banco para processamento
Sessao::write('BANCO', $inCodBanco);

//Teste de Periodo de Movimentação
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                    );
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

$stFiltro = " WHERE dt_inicial <= to_date('".$dtVigencia."','dd/mm/yyyy')	\n";
$stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1                            \n";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

if ( $rsPeriodoMovimentacao->getNumLinhas()>0 ) {
    $obFormulario->addHidden             ( $obHdnAcao                            											);
    $obFormulario->addHidden             ( $obHdnCtrl                                                                       );
    $obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" 				);
    $obFormulario->addTitulo             ( "Configuração da Exportação Bancária" 											);
    $obFormulario->addTitulo             ( "Banco do Brasil"                     											);
    $obFormulario->addComponente         ( $obVigencia             											    		    );
    $obFormulario->addComponente         ( $obTxtCodigoConvenio             											    );
    $obFormulario->addTitulo("Contas do Convênio");
    $obIMontaAgenciaConta->geraFormulario( $obFormulario																	);
    $obFormulario->addComponente($obTxtDescricao);
    $obFormulario->addComponente($obISelectMultiploLotacao);
    $obFormulario->addComponente($obISelectMultiploLocal);
    $obFormulario->defineBarra(array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar));
    $obFormulario->addSpan($obSpnContasConvenio);
    
    $obBtnOk = new OK();
    $obBtnLimpar = new Limpar();
    
    $obBtnLimpar->obEvento->setOnclick('jQuery(\'#btIncluirConfBB\').attr(\'disabled\',\'disabled\');
                                        jQuery(\'#btAlterarConfBB\').attr(\'disabled\',\'disabled\');
                                        executaFuncaoAjax(\'limparForm\');');
    
    $obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
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

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

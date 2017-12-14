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
    * Página de Formulário do Configuração SEFIP
    * Data de Criação: 12/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    * Casos de uso: uc-04.08.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoSEFIP";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgDown     = "DW".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::write("arModalidades", array());
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$jsOnload = "executaFuncaoAjax('preencherForm');";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//$stAcao = $_REQUEST['stAcao'] ?  $_REQUEST['stAcao'] : Sessao::read('acao');

if (Sessao::read('NOVAacao') != "") {
    $stAcao = Sessao::read('NOVAacao');
} else {
    $stAcao = $request->get('stAcao');
}

$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado("cod_modulo",40);
$obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado("parametro","cnae_fiscal".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsCnaeFiscal);

$obTAdministracaoConfiguracao->setDado("parametro","codigo_outras_entidades_sefip".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsCodigoOutrasEntidades);

$obTAdministracaoConfiguracao->setDado("parametro","centralizacao".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsCentralizado);

$obTAdministracaoConfiguracao->setDado("parametro","fpas".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsFpas);

$obTAdministracaoConfiguracao->setDado("parametro","gps".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsGps);

$obTAdministracaoConfiguracao->setDado("parametro","nome_pessoa_contato_sefip".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsPessoaContato);

$obTAdministracaoConfiguracao->setDado("parametro","telefone_pessoa_contato_sefip".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsTelefoneContato);

$obTAdministracaoConfiguracao->setDado("parametro","DDD_pessoa_contato_sefip".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsDDDContato);

$obTAdministracaoConfiguracao->setDado("parametro","mail_pessoa_contato_sefip".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsEmailContato);

$obTAdministracaoConfiguracao->setDado("parametro","tipo_inscricao".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsTipoInscricao);
Sessao::write("inTipoInscricao", ($rsTipoInscricao->getCampo("valor") != "") ? $rsTipoInscricao->getCampo("valor") : 1);

$obTAdministracaoConfiguracao->setDado("parametro","inscricao_fornecedor".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsInscricaoFornecedor);
Sessao::write("inscricao_fornecedor", ($rsInscricaoFornecedor->getCampo("valor") != 0) ? $rsInscricaoFornecedor->getCampo("valor") : "");

if ( $rsCnaeFiscal->getCampo("valor") != "" ) {
    $obTCEMCnaeFiscal = new TCEMCnaeFiscal;
    $stFiltro = " WHERE cod_cnae = ".$rsCnaeFiscal->getCampo("valor");
    $obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsCnaeFiscal,$stFiltro );
}

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                                               );
$obForm->setTarget                              ( "oculto"                                                              );

Sessao::write("obForm", $obForm);

//Definicao dos componentes
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obTxtCodigoCentralizacao = new Inteiro();
$obTxtCodigoCentralizacao->setRotulo          ( "Código de Centralização"		);
$obTxtCodigoCentralizacao->setName            ( "inCodCentralizacao"					);
$obTxtCodigoCentralizacao->setValue           ( $rsCentralizado->getCampo("valor")                     );
$obTxtCodigoCentralizacao->setTitle           ( "Informe o código de centralização da prefeitura, ver tabela específica manual SEFIP." );
$obTxtCodigoCentralizacao->setSize            ( 5                                              					    );
$obTxtCodigoCentralizacao->setMaxLength       ( 1                                                                   );
$obTxtCodigoCentralizacao->setNull			 ( false											                    );

$obTxtCodigoOutrasEntidades = new Inteiro();
$obTxtCodigoOutrasEntidades->setRotulo          ( "Código de Outras Entidades"               );
$obTxtCodigoOutrasEntidades->setName            ( "inCodigoOutrasEntidades"                  );
$obTxtCodigoOutrasEntidades->setValue           ( $rsCodigoOutrasEntidades->getCampo("valor"));
$obTxtCodigoOutrasEntidades->setTitle           ( "Informe o código de outras entidades."    );
$obTxtCodigoOutrasEntidades->setSize            ( 5                                          );
$obTxtCodigoOutrasEntidades->setMaxLength       ( 4                                          );
$obTxtCodigoOutrasEntidades->setNull            ( false                                      );
$obTxtCodigoOutrasEntidades->setPreencheComZeros ( "E"     );
$obTxtCodigoOutrasEntidades->setMascara("9999");

$obTxtFPAS = new Inteiro();
$obTxtFPAS->setRotulo          ( "FPAS"		);
$obTxtFPAS->setName            ( "inCodFPAS"					);
$obTxtFPAS->setValue           ( $rsFpas->getCampo("valor")                     );
$obTxtFPAS->setTitle           ( "Informe o código de do FPAS da prefeitura, ver tabela específica manual SEFIP." );
$obTxtFPAS->setSize            ( 5                                              					    );
$obTxtFPAS->setMaxLength       ( 3                                                                   );
$obTxtFPAS->setNull			 ( false											                    );

$obTxtPagamentoGPS = new Inteiro();
$obTxtPagamentoGPS->setRotulo          ( "Código de Pagamento GPS"		);
$obTxtPagamentoGPS->setName            ( "inCodPagamentoGPS"					);
$obTxtPagamentoGPS->setValue           ( $rsGps->getCampo("valor")                     );
$obTxtPagamentoGPS->setTitle           ( "Informe o código de pagamento da GPS, ver tabela específica manual SEFIP." );
$obTxtPagamentoGPS->setSize            ( 5                                              					    );
$obTxtPagamentoGPS->setMaxLength       ( 4                                                                   );
$obTxtPagamentoGPS->setNull			 ( false											                    );

$obBscCodigoCnae = new BuscaInner;
$obBscCodigoCnae->setRotulo                         ( "Código CNAE Fiscal"                              );
$obBscCodigoCnae->setTitle                          ( "Informe o código do CNAE fiscal da prefeitura."                 );
$obBscCodigoCnae->setNull                           ( false                                           );
$obBscCodigoCnae->setId                             ( "stCnae"                                );
$obBscCodigoCnae->setValue                          ( $rsCnaeFiscal->getCampo("nom_atividade")                                 );
$obBscCodigoCnae->obCampoCod->setInteiro(false);
$obBscCodigoCnae->obCampoCodHidden->setValue      ( $rsCnaeFiscal->getCampo("cod_cnae") );
$obBscCodigoCnae->obCampoCod->setName               ( "inCodCnae"                             );
$obBscCodigoCnae->obCampoCod->setValue              ( $rsCnaeFiscal->getCampo("valor_composto")                              );
$obBscCodigoCnae->obCampoCod->setSize               ( 20                                              );
$obBscCodigoCnae->obCampoCod->setMaxLength          ( 160                                               );
$obBscCodigoCnae->obCampoCod->obEvento->setOnChange ( "montaParametrosGET('buscaCnae','inCodCnae');"            );
$obBscCodigoCnae->setFuncaoBusca                    ( "abrePopUp('".CAM_GRH_IMA_POPUPS."configuracao/FLProcurarCnae.php','frm','inCodCnae','stCnae','".$stAcao."','".Sessao::getId()."','800','550')" );

$obCmbTipoInscricao = new Select();
$obCmbTipoInscricao->setName("inTipoInscricao");
$obCmbTipoInscricao->setRotulo("Tipo de Inscrição Fornecedor Folha de Pagamento");
$obCmbTipoInscricao->setTitle("Informe o tipo de inscrição do fornecedor da folha de pagamento.");
$obCmbTipoInscricao->setNull(false);
$obCmbTipoInscricao->setStyle( "width: 200px" );
$obCmbTipoInscricao->setValue($rsTipoInscricao->getCampo("valor"));
$obCmbTipoInscricao->addOption("1","CNPJ");
$obCmbTipoInscricao->addOption("2","CEI");
$obCmbTipoInscricao->addOption("3","CPF");
$obCmbTipoInscricao->obEvento->setOnChange("montaParametrosGET('gerarSpanInscricaoFornecedor','inTipoInscricao');");

$obSpnTipoInscricao = new Span();
$obSpnTipoInscricao->setId("spnTipoInscricao");

$obTxtPessoaContato = new TextBox();
$obTxtPessoaContato->setRotulo("Nome da Pessoa de Contato");
$obTxtPessoaContato->setName("stPessoaContato");
$obTxtPessoaContato->setTitle("Informe o nome do contato.");
$obTxtPessoaContato->setSize(30);
$obTxtPessoaContato->setMaxLength(20);
$obTxtPessoaContato->setNull(false);
$obTxtPessoaContato->setValue($rsPessoaContato->getCampo("valor"));

$obTxtDDDContato = new TextBox();
$obTxtDDDContato->setRotulo("Telefone do Contato");
$obTxtDDDContato->setName("stDDDContato");
$obTxtDDDContato->setTitle("Informe o telefone do contato. Utilizar DDD + fone.");
$obTxtDDDContato->setSize(3);
$obTxtDDDContato->setMaxLength(2);
$obTxtDDDContato->setNull(false);
$obTxtDDDContato->setInteiro(true);
$obTxtDDDContato->setValue($rsDDDContato->getCampo("valor"));

$obTxtTelefoneContato = new TextBox();
$obTxtTelefoneContato->setRotulo("Telefone do Contato");
$obTxtTelefoneContato->setName("stTelefoneContato");
$obTxtTelefoneContato->setTitle("Informe o telefone do contato. Utilizar DDD + fone.");
$obTxtTelefoneContato->setSize(11);
$obTxtTelefoneContato->setMaxLength(10);
$obTxtTelefoneContato->setNull(false);
$obTxtTelefoneContato->setInteiro(true);
$obTxtTelefoneContato->setValue($rsTelefoneContato->getCampo("valor"));

$obTxtEmailContato = new TextBox();
$obTxtEmailContato->setRotulo("E-mail do Contato");
$obTxtEmailContato->setName("stEmailContato");
$obTxtEmailContato->setTitle("Informe o endereço de e-mail do contato.");
$obTxtEmailContato->setSize(70);
$obTxtEmailContato->setMaxLength(60);
$obTxtEmailContato->setValue($rsEmailContato->getCampo("valor"));

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAModalidadeRecolhimento.class.php");
$obTIMAModalidadeRecolhimento = new TIMAModalidadeRecolhimento();
$stFiltro = " WHERE cod_modalidade = 1 OR cod_modalidade = 2";
$obTIMAModalidadeRecolhimento->recuperaTodos($rsModalidadeRecolhimento,$stFiltro);

$obTxtCodModalidadeRecolhimento = new TextBox;
$obTxtCodModalidadeRecolhimento->setRotulo              ( "Tipo Modalidade"                      );
$obTxtCodModalidadeRecolhimento->setTitle               ( "Selecione a modalidade da Sefip, para classificar conforme a categoria do servidor" );
$obTxtCodModalidadeRecolhimento->setName                ( "inCodModalidadeRecolhimentoTxt"                  );
$obTxtCodModalidadeRecolhimento->setSize                ( 6                                                 );
$obTxtCodModalidadeRecolhimento->setMaxLength           ( 3                                                 );
$obTxtCodModalidadeRecolhimento->setInteiro             ( true                                              );
$obTxtCodModalidadeRecolhimento->setSize                ( 10                                                );
$obTxtCodModalidadeRecolhimento->setNullBarra          ( false                                             );

$obCmbCodModalidadeRecolhimento = new Select;
$obCmbCodModalidadeRecolhimento->setRotulo              ( "Tipo Modalidade"                      );
$obCmbCodModalidadeRecolhimento->setName                ( "inCodModalidadeRecolhimento"                     );
$obCmbCodModalidadeRecolhimento->setStyle               ( "width: 450px"                                    );
$obCmbCodModalidadeRecolhimento->addOption              ( "","Selecione"                                    );
$obCmbCodModalidadeRecolhimento->setCampoID             ( "sefip"                                           );
$obCmbCodModalidadeRecolhimento->setCampoDesc           ( "descricao"                                       );
$obCmbCodModalidadeRecolhimento->preencheCombo          ( $rsModalidadeRecolhimento                         );
$obCmbCodModalidadeRecolhimento->setNullBarra           ( false                                             );

include_once(CAM_GRH_PES_COMPONENTES."ISelectMultiploCategoria.class.php");
$obISelectMultiploCategoria = new ISelectMultiploCategoria();
$obISelectMultiploCategoria->setRotulo("Categoria da Sefip");
$obISelectMultiploCategoria->setTitle("Selecione a(s) categoria(s) da Sefip, relacionadas à modalidade indicada.");
$obISelectMultiploCategoria->setNullBarra(false);

$arCampos = array($obTxtCodModalidadeRecolhimento,$obCmbCodModalidadeRecolhimento,$obISelectMultiploCategoria);

$obSpnModalidadeRecolhimento = new Span();
$obSpnModalidadeRecolhimento->setId("spnModalidadeRecolhimento");

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("montaParametrosGET('submeter','',true);");

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimpar"                                                           );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limpar');"                                  );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden             ( $obHdnAcao                            											);
$obFormulario->addHidden             ( $obHdnCtrl                                                                       );
$obFormulario->addTitulo             ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" 				);
$obFormulario->addTitulo             ( "Configuração da SEFIP" 											);
$obFormulario->addComponente         ( $obBscCodigoCnae );
$obFormulario->addComponente         ( $obTxtCodigoCentralizacao             											    );
$obFormulario->addComponente         ( $obTxtCodigoOutrasEntidades );
$obFormulario->addComponente         ( $obTxtFPAS);
$obFormulario->addComponente         ( $obTxtPagamentoGPS);
$obFormulario->addComponente         ( $obCmbTipoInscricao );
$obFormulario->addSpan($obSpnTipoInscricao);
$obFormulario->addComponente($obTxtPessoaContato);
$obFormulario->agrupaComponentes(array($obTxtDDDContato,$obTxtTelefoneContato));
$obFormulario->addComponente($obTxtEmailContato);
$obFormulario->addTitulo("Modalidades de Recolhimento");
$obFormulario->addComponenteComposto($obTxtCodModalidadeRecolhimento,$obCmbCodModalidadeRecolhimento);
$obFormulario->addComponente($obISelectMultiploCategoria);
$obFormulario->incluirAlterar("Modalidade",$arCampos,true);
$obFormulario->addSpan($obSpnModalidadeRecolhimento);
$obFormulario->defineBarra( array($obBtnOk,$obBtnLimpar)                                          );

$stComplementoValida  = "stCampo = document.frm.".$obTxtEmailContato->getName().".value.length;\n";
$stComplementoValida .= "if (stCampo>0) {\n";
$stComplementoValida .= "   if (!(/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(document.frm.".$obTxtEmailContato->getName().".value))) {\n";
$stComplementoValida .= "       erro = true;\n";
$stComplementoValida .= "       mensagem += '@Campo E-mail do Contato inválido!()';\n";
$stComplementoValida .= "   }\n";
$stComplementoValida .= "}\n";
//$stComplementoValida .= "if (erro == false) {\n";
//$stComplementoValida .= "   BloqueiaFrames(true,false);\n";
//$stComplementoValida .= "   parent.frames[2].document.body.scrollTop=0; }\n";

$obFormulario->obJavaScript->setComplementoValida($stComplementoValida);

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
* Página de Formulário de Pessoal - Rescindir Contrato
* Data de Criação   : 18/10/2005

* @author Analista: Vandr? Miguel Ramos
* @author Desenvolvedor: Eduardo Antunez

* @ignore

$Id: FMRescindirContrato.php 65923 2016-06-30 13:18:20Z michel $

* Casos de uso: uc-04.04.44
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_PES_NEGOCIO."RPessoalRescisaoContrato.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPadrao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";

$stAcao = $request->get("stAcao");
$stServidor = $request->get('inRegistro')." - ".$request->get('stNomCGM');

$arLink = Sessao::read('link');
$stPrograma = "RescindirContrato";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php"."?".Sessao::getId()."&stAcao=".$stAcao."&pg=".$arLink["pg"]."&pos=".$arLink["pos"];
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$rsNorma = $rsPadraoNorma = new RecordSet;

$obRPessoalRescisaoContrato  = new RPessoalRescisaoContrato;
$obRPessoalRescisaoContrato->obRPessoalCausaRescisao->listarCausa($rsCausaRescisao);

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);

$obRFolhaPagamentoPadrao = new RFolhaPagamentoPadrao;
$obRFolhaPagamentoPadrao->obRNorma->obRTipoNorma->listarTodos ( $rsTipoNorma );

$obTUsuario = new TUsuario;
$stFiltro = " WHERE usuario.numcgm = ".$request->get('inNumCGM')." AND usuario.status = 'A' ";
$obTUsuario->recuperaRelacionamento($rsUsuarioSessao, $stFiltro);

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

$obHdnCgm = new Hidden;
$obHdnCgm->setName  ( "inNumCGM" );
$obHdnCgm->setValue ( $request->get('inNumCGM') );

$obHdnEval = new hiddenEval();
$obHdnEval->setName  ( "stEval" );
$obHdnEval->setValue ( ""       );

$obHdnEvalAviso = new hiddenEval();
$obHdnEvalAviso->setName  ( "stEvalAviso" );
$obHdnEvalAviso->setValue ( ""       );

$obLblServidor = new Label;
$obLblServidor->setRotulo( "Matrícula" );
$obLblServidor->setValue ( $stServidor );

$obTxtDtRescisao = new Data;
$obTxtDtRescisao->setName  ( "dtRescisao" );
if (sessao::read('incluirRescisaoContratoPensionista') != null) {
    $dtRescisao = $request->get('dtRescisao');
}
$obTxtDtRescisao->setValue ( $dtRescisao );
$obTxtDtRescisao->setRotulo( "Data da Rescisão" );
$obTxtDtRescisao->setNull  ( false );
$obTxtDtRescisao->setTitle ( 'Informe a data de rescisão' );
$obTxtDtRescisao->obEvento->setOnChange ( "montaParametrosGET('validarDataRescisao','dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');" );

$obTxtCausaRescisao = new TextBox;
$obTxtCausaRescisao->setName     ( "inCausaRescisao"   );
$obTxtCausaRescisao->setValue    ( $inCausaRescisao    );
$obTxtCausaRescisao->setRotulo   ( "Causa da Rescisão" );
$obTxtCausaRescisao->setSize     ( 5                   );
$obTxtCausaRescisao->setMaxLength( 3                   );
$obTxtCausaRescisao->setNull     ( false               );
$obTxtCausaRescisao->setInteiro  ( true                );
$obTxtCausaRescisao->setTitle    ( 'Informe a causa da rescisão' );
$obTxtCausaRescisao->obEvento->setOnChange("montaParametrosGET('gerarSpanObito', 'dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');");

$obCmbCausaRescisao = new Select;
$obCmbCausaRescisao->setName       ( "stCausaRescisao"       );
$obCmbCausaRescisao->setRotulo     ( "Causa da Rescisão"     );
$obCmbCausaRescisao->setStyle      ( "width: 600px"          );
$obCmbCausaRescisao->setCampoId    ( "num_causa"             );
$obCmbCausaRescisao->setCampoDesc  ( "descricao"             );
$obCmbCausaRescisao->addOption     ( " ","Selecione"         );
$obCmbCausaRescisao->preencheCombo ( $rsCausaRescisao        );
$obCmbCausaRescisao->setNull       ( false                   );
$obCmbCausaRescisao->setTitle      ( "Informe a causa da rescisão." );
$obCmbCausaRescisao->obEvento->setOnChange("montaParametrosGET('gerarSpanObito', 'dtRescisao,inCodContrato,inCausaRescisao,inCodSubDivisao,dtPosse,dtNomeacao,dtAdmissao');");

$obTxtTipoNorma = new TextBox;
$obTxtTipoNorma->setRotulo              ( "Tipo de Norma"                                 );
$obTxtTipoNorma->setTitle               ( "Informe o tipo de norma para seleção da norma" );
$obTxtTipoNorma->setName                ( "inCodTipoNormaTxt"                             );
$obTxtTipoNorma->setValue               ( $inCodTipoNormaTxt                              );
$obTxtTipoNorma->setSize                ( 5                                               );
$obTxtTipoNorma->setMaxLength           ( 5                                               );
$obTxtTipoNorma->setInteiro             ( true                                            );
$obTxtTipoNorma->setNull                ( true                                            );
$obTxtTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('MontaNorma','inCodTipoNorma');" );

$obCmbTipoNorma = new Select;
$obCmbTipoNorma->setRotulo              ( "Tipo de Norma"             );
$obCmbTipoNorma->setName                ( "inCodTipoNorma"            );
$obCmbTipoNorma->setValue               ( $inCodTipoNorma             );
$obCmbTipoNorma->setStyle               ( "width: 200px"              );
$obCmbTipoNorma->setCampoID             ( "cod_tipo_norma"            );
$obCmbTipoNorma->setCampoDesc           ( "nom_tipo_norma"            );
$obCmbTipoNorma->addOption              ( "", "Selecione"             );
$obCmbTipoNorma->setNull                ( true                        );
$obCmbTipoNorma->preencheCombo          ( $rsTipoNorma                );
$obCmbTipoNorma->obEvento->setOnChange  ( "montaParametrosGET('MontaNorma','inCodTipoNorma');" );

$obTxtNorma = new TextBox;
$obTxtNorma->setRotulo        ( "Norma"                               );
$obTxtNorma->setTitle         ( "Informe a norma vinculada ao padrão" );
$obTxtNorma->setName          ( "inCodNormaTxt"                       );
$obTxtNorma->setValue         ( $inCodNormaTxt                        );
$obTxtNorma->setSize          ( 5                                     );
$obTxtNorma->setMaxLength     ( 5                                     );
$obTxtNorma->setInteiro       ( true                                  );
$obTxtNorma->setNull          ( true                                  );

$obCmbNorma = new Select;
$obCmbNorma->setRotulo        ( "Norma"         );
$obCmbNorma->setName          ( "inCodNorma"    );
$obCmbNorma->setValue         ( $inCodNorma     );
$obCmbNorma->setStyle         ( "width: 200px"  );
$obCmbNorma->setCampoID       ( "cod_norma"     );
$obCmbNorma->setCampoDesc     ( "nom_norma"     );
$obCmbNorma->addOption        ( "", "Selecione" );
$obCmbNorma->setNull          ( true            );

//Armazena o CasoCausa retornado para a inclusão
$obHdnCasoCausa = new Hidden;
$obHdnCasoCausa->setName  ( "inCasoCausa" );
$obHdnCasoCausa->setValue ( $stCasoCausa );

$obLblCasoCausa = new Label();
$obLblCasoCausa->setId     ( "stCasoCausa"   );
$obLblCasoCausa->setValue    ( $stCasoCausa    );
$obLblCasoCausa->setRotulo   ( "*Caso da Causa" );

$obSpnObto = new Span();
$obSpnObto->setId("spnObto");

$dtPosse    = $request->get('dtPosse');
$dtNomeacao = $request->get('dtNomeacao');
$dtAdmissao = $request->get('dtAdmissao');

//Data de Posse
$obHdnDtPosse = new Hidden;
$obHdnDtPosse->setName  ( "dtPosse" );
$obHdnDtPosse->setValue ( substr($dtPosse,6,4)."/".substr($dtPosse,3,2)."/".substr($dtPosse,0,2) );

//Data de Nomeacao
$obHdnDtNomeacao = new Hidden;
$obHdnDtNomeacao->setName  ( "dtNomeacao" );
$obHdnDtNomeacao->setValue ( substr($dtNomeacao,6,4)."/".substr($dtNomeacao,3,2)."/".substr($dtNomeacao,0,2) );

//Data de Admissao
$obHdnDtAdmissao = new Hidden;
$obHdnDtAdmissao->setName  ( "dtAdmissao" );
$obHdnDtAdmissao->setValue ( substr($dtAdmissao,6,4)."/".substr($dtAdmissao,3,2)."/".substr($dtAdmissao,0,2) );

//SubDivisao
$obHdnCodSubDivisao = new Hidden;
$obHdnCodSubDivisao->setName  ( "inCodSubDivisao" );
$obHdnCodSubDivisao->setValue ( $request->get('inCodSubDivisao') );

//Contrato
$obHdnCodContrato = new Hidden;
$obHdnCodContrato->setName  ( "inCodContrato" );
$obHdnCodContrato->setValue ( $request->get('inCodContrato') );

//Registro
$obHdnRegistro = new Hidden;
$obHdnRegistro->setName  ( "inRegistro" );
$obHdnRegistro->setValue ( $request->get('inRegistro') );

//flag para gerar termo de recisão
$obHdnGeraTermoRecisao = new Hidden;
$obHdnGeraTermoRecisao->setName  ( "boGeraTermoRecisao" );
$obHdnGeraTermoRecisao->setValue ( 'false' );

$obSpnAviso = new Span();
$obSpnAviso->setId("spnAviso");

$obTFolhaPagamentoFolhaSituacao = new TFolhaPagamentoFolhaSituacao();
$obTFolhaPagamentoFolhaSituacao->recuperaUltimaFolhaSituacao($rsFolhaSituacao);

$obChkFolhaSalario =new CheckBox();
$obChkFolhaSalario->setRotulo("Incorporar Cálculos Rescisão");
$obChkFolhaSalario->setLabel("Folha Salário");
$obChkFolhaSalario->setValue(true);
$obChkFolhaSalario->setName("boFolhaSalario");
$obChkFolhaSalario->setTitle("Informe se os cálculos serão incorporados a rescisão.");
$obChkFolhaSalario->obEvento->setOnchange("montaParametrosGET('validarIncorporarFolhaSalario', 'boFolhaSalario,inCodContrato');");

if (sessao::read('incluirRescisaoContratoPensionista') != null) {
    $obChkFolhaSalario->setChecked(true);
}

if ( $rsFolhaSituacao->getCampo("situacao") == "f" ) {
    $obChkFolhaSalario->setDisabled(true);
}

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsCompetencia);
$obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
$stFiltro  = " AND cod_contrato = ".$request->get('inCodContrato');
$stFiltro .= " AND cod_periodo_movimentacao = ".$rsCompetencia->getCampo("cod_periodo_movimentacao");
$obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosCalculados,$stFiltro);
$obChkFolhaDecimo =new CheckBox();
$obChkFolhaDecimo->setRotulo("Incorporar Cálculos ? Rescisão");
$obChkFolhaDecimo->setLabel("Folha 13° Salário");
$obChkFolhaDecimo->setValue(true);
$obChkFolhaDecimo->setName("boFolhaDecimo");
$obChkFolhaDecimo->setTitle("Informe se os cálculos serão incorporados a rescisão.");
if ( $rsEventosCalculados->getNumLinhas() < 0 ) {
    $obChkFolhaDecimo->setDisabled(true);
}

$obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
$stFiltro  = " WHERE cod_periodo_movimentacao = ".$rsCompetencia->getCampo("cod_periodo_movimentacao");
$stFiltro .= "   AND cod_contrato = ".$request->get("inCodContrato");
$obTFolhaPagamentoEventoCalculado->recuperaContratosCalculados($rsContratosCalculados,$stFiltro);
$obLblObservacao = new Label();
$obLblObservacao->setId("lblObservacao");
$obLblObservacao->setRotulo("Observação");
$obLblObservacao->setValue("A folha salário encontra-se fechada e calculada para o contrato que está sendo rescindido.");

$stComplemento  = "stCampo = document.frm.inCasoCausa; \n";
$stComplemento .= "if (stCampo) {\n";
$stComplemento .= "    if ( trim( stCampo.value ) == \"\" ) {\n";
$stComplemento .= "        erro = true;\n";
$stComplemento .= "        mensagem += \"@Campo Caso da Causa inválido!()\";\n";
$stComplemento .= "    }\n";
$stComplemento .= "}\n";

if($rsUsuarioSessao->getNumLinhas()==1){
    $obLblUsuario = new Label;
    $obLblUsuario->setRotulo( "Usuário" );
    $obLblUsuario->setValue ( $rsUsuarioSessao->getCampo('username') );

    $obLblSetor = new Label;
    $obLblSetor->setRotulo( "Setor" );
    $obLblSetor->setValue ( $rsUsuarioSessao->getCampo('nom_setor') );

    //Radio para definicao de tipo Item
    $obRdDesativarS = new Radio;
    $obRdDesativarS->setRotulo     ( "*Desativar o usuário de acesso ao urbem" );
    $obRdDesativarS->setName       ( "stDesativarUsuario" );
    $obRdDesativarS->setId         ( "stDesativarUsuario1" );
    $obRdDesativarS->setValue      ( "sim" );
    $obRdDesativarS->setLabel      ( "Sim" );
    $obRdDesativarS->setChecked    ( true );

    $obRdDesativarN = new Radio;
    $obRdDesativarN->setRotulo   ( "*Desativar o usuário de acesso ao urbem" );
    $obRdDesativarN->setName     ( "stDesativarUsuario" );
    $obRdDesativarN->setId       ( "stDesativarUsuario2" );
    $obRdDesativarN->setValue    ( "nao" );
    $obRdDesativarN->setLabel    ( "Não" );
    $obRdDesativarN->setChecked  ( false );

    $arRadiosDesativar = array( $obRdDesativarS, $obRdDesativarN );
}

$obBtnOk = new Ok();
$obBtnOk->obEvento->setOnClick("confirmPopUp('Imprimir', 'Imprimir rescisão?', 'document.frm.boGeraTermoRecisao.value = \'true\'; montaParametrosGET(\'submeter\',\'\',true)', 'montaParametrosGET(\'submeter\',\'\',true)');");

$obBtnCancelar = new Button();
$obBtnCancelar->setValue("Cancelar");
$obBtnCancelar->obEvento->setOnClick("Cancelar('".$pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&stCodigo='.$request->get('stCodigo').'&stDescricao='.$request->get('stDescricao')."');");

$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden             ( $obHdnCtrl                );
$obFormulario->addHidden             ( $obHdnEval, true          );
$obFormulario->addHidden             ( $obHdnEvalAviso, true     );
$obFormulario->addHidden             ( $obHdnAcao                );
$obFormulario->addHidden             ( $obHdnDtPosse             );
$obFormulario->addHidden             ( $obHdnDtNomeacao          );
$obFormulario->addHidden             ( $obHdnDtAdmissao          );
$obFormulario->addHidden             ( $obHdnCodSubDivisao       );
$obFormulario->addHidden             ( $obHdnCasoCausa           );
$obFormulario->addHidden             ( $obHdnCodContrato         );
$obFormulario->addHidden             ( $obHdnRegistro            );
$obFormulario->addHidden             ( $obHdnGeraTermoRecisao    );
$obFormulario->addHidden             ( $obHdnCgm                 );
$obFormulario->addTitulo             ( "Dados de rescisão" );
$obFormulario->addComponente         ( $obLblServidor );
$obFormulario->addComponenteComposto ( $obTxtCausaRescisao, $obCmbCausaRescisao );
$obFormulario->addSpan               ( $obSpnAviso );
$obFormulario->addComponente         ( $obTxtDtRescisao );
$obFormulario->agrupaComponentes(array($obChkFolhaSalario,$obChkFolhaDecimo));
if ($rsContratosCalculados->getNumLinhas() > 0 and $rsFolhaSituacao->getCampo("situacao") == "f") {
    $obFormulario->addComponente     ( $obLblObservacao );
}
$obFormulario->addSpan               ( $obSpnObto );
$obFormulario->addComponente         ( $obLblCasoCausa );
$obFormulario->addComponenteComposto ( $obTxtTipoNorma, $obCmbTipoNorma       );
$obFormulario->addComponenteComposto ( $obTxtNorma, $obCmbNorma               );

if($rsUsuarioSessao->getNumLinhas()==1){
    $obFormulario->addTitulo         ( "Dados de Usuário" );
    $obFormulario->addComponente     ( $obLblUsuario );
    $obFormulario->addComponente     ( $obLblSetor );
    $obFormulario->agrupaComponentes ( $arRadiosDesativar );
}

$obFormulario->defineBarra(array($obBtnOk,$obBtnCancelar));
$obFormulario->obJavaScript->setComplementoValida($stComplemento);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

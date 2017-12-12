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
    * Formulario para Inclusão de Terminais - Tesouraria
    * Data de Criação   : 06/09/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Cleisson da Silva Barboza
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @ignore
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GF_TES_NEGOCIO."RTesourariaTerminal.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma      = "ManterTerminal";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

include_once( $pgJs );
$stCodVerificador    = $request->get('stCodVerificador');
$inNumTerminal      = $request->get('inNumTerminal');
$stSituacao         = $request->get('stSituacao');
$inCgm              = $request->get('inCgm');
$stTimestampTerminal= $request->get('stTimestampTerminal');

$stAcao = $request->get('stAcao');

$obRTesourariaTerminal = new RTesourariaTerminal();
if ($stAcao=='alterar') {
    $obRTesourariaTerminal->setCodTerminal      ( $inNumTerminal    );
    $obRTesourariaTerminal->setTimestampTerminal( $stTimestampTerminal);
    $obRTesourariaTerminal->setCodVerificador   ( $stCodVerificador);
    $obRTesourariaTerminal->consultar();
    $arUsuario = array();
    $inCount = 0;
    $arRTesourariaUsuarioTerminal = $obRTesourariaTerminal->getUsuarioTerminal();
    foreach ($arRTesourariaUsuarioTerminal as $obRTesourariaUsuarioTerminal) {
        $arUsuario[$inCount]['id_usuario' ] = $inCount;
        $arUsuario[$inCount]['numcgm'        ] = $obRTesourariaUsuarioTerminal->obRCGM->getNumCGM();
        $obRTesourariaUsuarioTerminal->obRCGM->consultar($rsCGM);
        $arUsuario[$inCount]['nom_cgm'       ] = $obRTesourariaUsuarioTerminal->obRCGM->getNomCGM();
        $arUsuario[$inCount]['responsavel'   ] = $obRTesourariaUsuarioTerminal->getResponsavel();
        $inCount++;
    }

    Sessao::write('arUsuario', $arUsuario);
    Sessao::write('situacao', $stSituacao);
    SistemaLegado::executaFramePrincipal( "montaListaUsuario();" );
} else {
    $obRTesourariaTerminal->buscaProximoCodigo();
    $inNumTerminal = $obRTesourariaTerminal->getCodTerminal();
    $boResponsavel = "f";
}

// OBJETOS HIDDEN

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl"            );
$obHdnCtrl->setValue ( $request->get("stCtrl") );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao"            );
$obHdnAcao->setValue ( $stAcao             );

// TIPO DE BUSCA UTILIZADO NA BUSCA DE CGM
$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( "usuario" );

$obHdnCgmUsuario = new Hidden;
$obHdnCgmUsuario->setName( "cgmUsuario" );
$obHdnCgmUsuario->setValue( Sessao::read('numCgm') );

// VALIDA SE O USUÁRIO LOGADO PODE INLUIR TERMINAIS
if ($stAcao=='incluir') {
    $stValidaCgm = "if (document.frm.cgmUsuario.value!='0') { erro = true; mensagem += '@Somente o Administrador do sistema pode incluir terminais!';} ";
}
// VALIDA SE O USUÁRIO LOGADO PODE ALTERAR TERMINAIS

if ($stAcao=='alterar') {
    $stValidaCgm = "if ((document.frm.cgmUsuario.value!='0') && (document.frm.cgmUsuario.value!=".$inCgm.")) { erro = true; mensagem += '@Somente o Administrador do sistema ou o Responsável pelo Terminal pode alterar terminais!';} ";
}

$obHdnValidaCGM = new HiddenEval;
$obHdnValidaCGM->setName("hdnValidaCgm");
$obHdnValidaCGM->setValue( $stValidaCgm );

$obHdnTimestampTerminal = new Hidden;
$obHdnTimestampTerminal->setName  ( "stTimestampTerminal"       );
$obHdnTimestampTerminal->setValue ( $stTimestampTerminal        );

// DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( "oculto" );

// DEFINE OBJETOS DO FORMULARIO - INCLUIR

$obHdnNroTerminal = new Hidden;
$obHdnNroTerminal->setName  ( "inNumTerminal"       );
$obHdnNroTerminal->setValue ( $inNumTerminal        );

//Define Objeto Label para Nr. do Terminal
$obLblNroTerminal = new Label;
$obLblNroTerminal->setName      ( "inNumTerminal"                                       );
$obLblNroTerminal->setValue     ( $inNumTerminal                                        );
$obLblNroTerminal->setRotulo    ( "Nr. Terminal"                                        );

//Define Objeto Text para Nr. do Terminal
$obTxtNroTerminal = new TextBox;
$obTxtNroTerminal->setName      ( "inNumTerminal"                                                       );
$obTxtNroTerminal->setValue     ( $inNumTerminal                                                        );
$obTxtNroTerminal->setRotulo    ( "Nr. Terminal"                                                        );
$obTxtNroTerminal->setTitle     ( "Informe o número que deve ser cadastrado para este Terminal de Caixa");
$obTxtNroTerminal->setNull      ( false                                                                 );
$obTxtNroTerminal->setMaxLength ( 3                                                                     );
$obTxtNroTerminal->setSize      ( 4                                                                     );

//Define Objeto Text para IP da Máquina
$obTxtCodVerificador = new TextBox;
$obTxtCodVerificador->setName        ( "stCodVerificador"                                    );
$obTxtCodVerificador->setValue       ( $stCodVerificador                                     );
$obTxtCodVerificador->setRotulo      ( "Código Verificador"                                  );
$obTxtCodVerificador->setTitle       ( "Informe o Código Verificador do Terminal"            );
$obTxtCodVerificador->setNull        ( false                                                 );
$obTxtCodVerificador->setSize        ( 45                                                    );
$obTxtCodVerificador->setMaxLength   ( 32                                                    );

//Define Objeto Text para Nr. do Terminal
$obBtnGerarCodigo = new Button;
$obBtnGerarCodigo->setName      ( "boGerarCodigo"                    );
$obBtnGerarCodigo->setValue     ( "Gerar Código"                     );
//$obBtnGerarCodigo->obEvento->setOnClick( "gerarCodigo(this.value);" );
$obBtnGerarCodigo->obEvento->setOnClick( "montaParametrosGET('gerarCodigo', '');" );

// Define objeto BuscaInner para cgm
$obBscCGM = new BuscaInner();
$obBscCGM->setRotulo                 ( "*Usuário de Terminal de Caixa"                                              );
$obBscCGM->setTitle                  ( "Informe o código cgm do Usuário de Terminal de Caixa que deseja pesquisar"  );
$obBscCGM->setId                     ( "stNomCgm"                                                                   );
$obBscCGM->setNull                   ( true                                                                         );
$obBscCGM->obCampoCod->setName       ( "inNumCgm"                                                                   );
$obBscCGM->obCampoCod->setSize       ( 10                                                                           );
$obBscCGM->obCampoCod->setMaxLength  ( 8                                                                            );
$obBscCGM->obCampoCod->setAlign      ( "left"                                                                       );
$obBscCGM->setFuncaoBusca            ("abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inNumCgm','stNomCgm','usuario','".Sessao::getId()."','800','550');");
$obBscCGM->setValoresBusca           ( CAM_GA_CGM_POPUPS.'cgm/OCProcurarCgm.php?'.Sessao::getId(), $obForm->getName() );

// Define Objeto Select para informar se o usuário é Responsável pelo terminal
$obCmbResponsavel = new Select();
$obCmbResponsavel->setRotulo ( "*Responsável pelo Terminal" );
//$obCmbResponsavel->setTitle  ( "Selecione se o cgm informado for de um usuário responsável pelo terminal de caixa"  );
$obCmbResponsavel->setName   ( "boResponsavel"              );
$obCmbResponsavel->addOption ( "t","Sim"                    );
$obCmbResponsavel->addOption ( "f","Não"                    );
$obCmbResponsavel->setValue  ( $boResponsavel               );
$obCmbResponsavel->setStyle  ( "width: 120px"               );
$obCmbResponsavel->setNull   ( true                         );

// Define Objeto Select para informar se o Terminal está Ativo ou Inativo
$obCmbSituacao = new Select();
$obCmbSituacao->setRotulo ( "*Situação"                  );
$obCmbSituacao->setTitle  ( "Informe a situação do Terminal"  );
$obCmbSituacao->setName   ( "stSituacao"                 );
$obCmbSituacao->addOption ( "Ativo","Ativo"              );
$obCmbSituacao->addOption ( "Inativo","Inativo"          );
$obCmbSituacao->setValue  ( $stSituacao                  );
$obCmbSituacao->setStyle  ( "width: 120px"               );
$obCmbSituacao->setNull   ( true                         );

// Define objeto span para lista de usuários
$obSpnLista = new Span();
$obSpnLista->setId( "spnLista" );

$obOk = new Ok;
// Define Objeto Button para Icluir Usuario
$obBtnIncluir = new Button;
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->obEvento->setOnClick( "incluirUsuario();" );

// Define Objeto Button para limpar
$obBtnLimpar = new Button;
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->obEvento->setOnClick( "limparUsuario();" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm                                   );
$obFormulario->addHidden    ( $obHdnCtrl                                );
$obFormulario->addHidden    ( $obHdnAcao                                );
$obFormulario->addHidden    ( $obHdnTipoBusca                           );
$obFormulario->addHidden    ( $obHdnCgmUsuario                          );
$obFormulario->addHidden    ( $obHdnValidaCGM, true                     );
$obFormulario->addTitulo    ( "Dados para Terminal e Usuários"          );

if ($stAcao=='incluir') {
    $obFormulario->addComponente( $obTxtNroTerminal                     );
    $obFormulario->agrupaComponentes ( array( $obTxtCodVerificador, $obBtnGerarCodigo ) );
} elseif ($stAcao=='alterar') {
    $obFormulario->addHidden    ( $obHdnNroTerminal                     );
    $obFormulario->addComponente( $obLblNroTerminal                     );
    $obFormulario->addComponente( $obTxtCodVerificador                  );
    $obFormulario->addComponente( $obCmbSituacao                        );
    $obFormulario->addComponente( $obHdnTimestampTerminal               );
}
$obFormulario->addTitulo    ( "Usuários de Terminal de Caixa"           );
$obFormulario->addComponente( $obBscCGM                                 );
$obFormulario->addComponente( $obCmbResponsavel                         );
$obFormulario->agrupaComponentes ( array( $obBtnIncluir, $obBtnLimpar ) );
$obFormulario->addSpan      ( $obSpnLista                               );
$obFormulario->defineBarra  ( array( $obOk )                            );

$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

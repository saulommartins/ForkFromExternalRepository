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
    * Página de Formulario Conceder Remissão

    * Data de Criação   : 20/08/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMConcederRemissao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.11

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_NORMAS_CLASSES.'componentes/IPopUpNorma.class.php';
include_once CAM_GA_ADM_COMPONENTES.'IPopUpFuncao.class.php';
include_once CAM_GT_ARR_COMPONENTES.'MontaGrupoCredito.class.php';
include_once CAM_GT_MON_COMPONENTES.'IPopUpCredito.class.php';
include_once CAM_GT_DAT_NEGOCIO.'RDATConfiguracao.class.php';
include_once CAM_GT_CEM_COMPONENTES.'IPopUpEmpresaIntervalo.class.php';
include_once CAM_GT_CIM_COMPONENTES.'IPopUpImovelIntervalo.class.php';

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = 'incluir';
}

//Define o nome dos arquivos PHP
$stPrograma    = "ConcederRemissao";
$pgForm        = "FM".$stPrograma.".php";
if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
    $pgProc        = "PR".$stPrograma.".php";
} else {
    $pgProc        = "PR".$stPrograma."Credito.php";
}
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".js";

include_once( $pgJs );

Sessao::remove('arListaGrupoCredito');
Sessao::remove('arListaCredito');
Sessao::write( "InscricoesRemir", -1 );

$obRDATConfiguracao = new RDATConfiguracao;
$obRDATConfiguracao->consultar();
$stValorLimite = $obRDATConfiguracao->getLimites();

//limite por credito
$obTxtLimiteCredito = new Moeda;
$obTxtLimiteCredito->setRotulo ( 'Limite por Crédito' );
$obTxtLimiteCredito->setTitle ( 'Valor limite para cada crédito.' );
$obTxtLimiteCredito->setName ( 'stLimiteCredito' );
$obTxtLimiteCredito->setNull ( false );

if ($_REQUEST['boTipoLancamentoManual'] != 'credito' || $stValorLimite == 5 || $stValorLimite == 2) {
    //limite por exercicio
    $obTxtLimiteExercicio = new Moeda;
    $obTxtLimiteExercicio->setRotulo ( 'Limite por Exercício' );
    $obTxtLimiteExercicio->setTitle ( 'Valor limite para cada exercício.' );
    $obTxtLimiteExercicio->setName ( 'stLimiteExercicio' );
    $obTxtLimiteExercicio->setNull ( false );
}

//limite total
$obTxtLimiteTotal = new Moeda;
$obTxtLimiteTotal->setRotulo ( 'Limite Total' );
$obTxtLimiteTotal->setTitle ( 'Valor limite total para os lançamentos.' );
$obTxtLimiteTotal->setName ( 'stLimiteTotal' );
$obTxtLimiteTotal->setNull ( false );

//fundamentacao legal
$obIPopUpNorma = new IPopUpNorma;
$obIPopUpNorma->obInnerNorma->setRotulo ( "Fundamentação Legal" );
$obIPopUpNorma->obInnerNorma->setTitle ( "Fundamentação legal que regulamenta a remisão." );

if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
    //regra de verificacao
    $obIPopUpFuncao = new IPopUpFuncao;
    $obIPopUpFuncao->obInnerFuncao->setRotulo ( "Regra de Verificação" );
    $obIPopUpFuncao->obInnerFuncao->setTitle ( "Regra para validar os lançamentos a serem remidos." );
    $obIPopUpFuncao->setCodModulo( 33 );
    $obIPopUpFuncao->setCodBiblioteca( 4 );
}

$obDtLimiteInscricaoDA = new Data;
$obDtLimiteInscricaoDA->setName ( "dtLimiteInscricaoDA" );
$obDtLimiteInscricaoDA->setRotulo ( "Data Limite de Inscrição em DA" );
$obDtLimiteInscricaoDA->setTitle ( "Data limite para as inscrições em dívida ativa." );
$obDtLimiteInscricaoDA->setNull ( false );

if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
    $obIPopUpGrupoCredito = new MontaGrupoCredito;
    $obIPopUpGrupoCredito->setRotulo ( "*Grupo de Crédito" );
    $obIPopUpGrupoCredito->setTitulo ( "Grupos de créditos alvo da remissão." );
} else {
    $obIPopUpCredito = new IPopUpCredito;
    $obIPopUpCredito->setRotulo('Crédito');
    $obIPopUpCredito->setTitle ('Informe o código de crédito.');
    $obIPopUpCredito->setNull  (true);

    $obExercicio = new Exercicio;
    $obExercicio->setRotulo('Exercicio');
}

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle  ( "Processo referente à inscrição em dívida." );
$obBscProcesso->setNull   ( true );
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca ( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php', 'frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obRDATConfiguracao->listarModelosDocumentoRemissao($rsModelosDocumento);

//Documento de remissão de dívida
$obCmbCertidaoRemissao = new Select;
$obCmbCertidaoRemissao->setName     ('inCodModeloDocumentoRemissao');
$obCmbCertidaoRemissao->setRotulo   ('Certidão de Remissão');
$obCmbCertidaoRemissao->setTitle    ('Selecione um modelo de documento para a remissão automática de dívida.');
$obCmbCertidaoRemissao->setNull     (false);
$obCmbCertidaoRemissao->setStyle    ('width: 220px');
$obCmbCertidaoRemissao->addOption('', 'Selecione');
$obCmbCertidaoRemissao->setCampoId('cod_documento');
$obCmbCertidaoRemissao->setCampoDesc('nome_documento');
$obCmbCertidaoRemissao->preencheCombo($rsModelosDocumento);

$obBtnIncluirGrupoCredito = new Button;
$obBtnIncluirGrupoCredito->setName              ( "btnIncluirGrupoCredito" );
$obBtnIncluirGrupoCredito->setValue             ( "Incluir" );
$obBtnIncluirGrupoCredito->setTipo              ( "button" );

if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
    $obBtnIncluirGrupoCredito->obEvento->setOnClick ( "montaParametrosGET('IncluirGrupoCredito', 'inCodGrupo', true);" );
} else {
    $obBtnIncluirGrupoCredito->obEvento->setOnClick ( "montaParametrosGET('IncluirCredito');" );
}
$obBtnIncluirGrupoCredito->setDisabled          ( false );

$obBtnLimparGrupoCredito = new Button;
$obBtnLimparGrupoCredito->setName               ( "btnLimparGrupoCredito" );
$obBtnLimparGrupoCredito->setValue              ( "Limpar" );
$obBtnLimparGrupoCredito->setTipo               ( "button" );
$obBtnLimparGrupoCredito->obEvento->setOnClick  ( "ajaxJavaScript('".$pgOcul."', 'limpaGrupoCredito');" );
$obBtnLimparGrupoCredito->setDisabled           ( false );

$botoesGrupoCredito = array ( $obBtnIncluirGrupoCredito, $obBtnLimparGrupoCredito );

$obSpnListaGrupos = new Span;
$obSpnListaGrupos->setID("spnListaGrupos");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl']  );

$obHdnTipoLancamento =  new Hidden;
$obHdnTipoLancamento->setName ('boTipoLancamentoManual');
$obHdnTipoLancamento->setValue($_REQUEST['boTipoLancamentoManual']);

$obBscContribuinte = new BuscaInnerIntervalo;
$obBscContribuinte->setRotulo           ( "Contribuinte"    );
$obBscContribuinte->obLabelIntervalo->setValue ( "até"          );
$obBscContribuinte->obCampoCod->setName     ("inCodContribuinteInicial"  );
$obBscContribuinte->obCampoCod->setValue        ( $inCodContribuinteInicio  );
$obBscContribuinte->obCampoCod->obEvento->setOnChange("buscaValor('buscaContribuinteInicio');");
$obBscContribuinte->setFuncaoBusca( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteInicial','stNome','','".Sessao::getId()."','800','450');" ));
$obBscContribuinte->obCampoCod2->setName        ("inCodContribuinteFinal"  );
$obBscContribuinte->obCampoCod2->setValue       ( $inCodContribuinteFinal  );
$obBscContribuinte->obCampoCod2->obEvento->setOnChange("buscaValor('buscaContribuinteFinal');");
$obBscContribuinte->setFuncaoBusca2( str_replace("'","&quot;","abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodContribuinteFinal','stNome','','".Sessao::getId()."','800','450');" ));

$obIPopUpEmpresa = new IPopUpEmpresaIntervalo;
$obIPopUpEmpresa->setVerificaInscricao(false);
$obIPopUpImovel  = new IPopUpImovelIntervalo;
$obIPopUpImovel->setVerificaInscricao(false);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.04.11" );
$obFormulario->addHidden ($obHdnAcao);
$obFormulario->addHidden ($obHdnCtrl);
$obFormulario->addHidden ($obHdnTipoLancamento);
$obFormulario->addTitulo ('Dados para Remissão');
$obFormulario->addComponente( $obBscContribuinte );
$obIPopUpImovel->geraFormulario ( $obFormulario );
$obIPopUpEmpresa->geraFormulario ( $obFormulario );
switch ($stValorLimite) {
    case 1: //limite por credito
        $obFormulario->addComponente ( $obTxtLimiteCredito );
        if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
            $obIPopUpFuncao->geraFormulario ($obFormulario);
        }
        break;

    case 2: //limite por exercicio
        $obFormulario->addComponente($obTxtLimiteExercicio);
        break;

    case 3: //limite total
        $obFormulario->addComponente($obTxtLimiteTotal);
        break;

    case 4: //limite por credito e limite total
        $obFormulario->addComponente($obTxtLimiteCredito);
        $obFormulario->addComponente($obTxtLimiteTotal);
        if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
            $obIPopUpFuncao->geraFormulario ( $obFormulario );
        }
        break;

    case 5: //limite por exercicio e limite total
        $obFormulario->addComponente($obTxtLimiteExercicio);
        $obFormulario->addComponente($obTxtLimiteTotal);
        break;

    case 6: //limite por credito, exercicio e total
        $obFormulario->addComponente($obTxtLimiteCredito);
        if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
            $obFormulario->addComponente($obTxtLimiteExercicio);
        }
        $obFormulario->addComponente($obTxtLimiteTotal);
        if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
            $obIPopUpFuncao->geraFormulario($obFormulario);
        }
        break;

    case 7: //limite por credito e limite por exercicio
        $obFormulario->addComponente($obTxtLimiteCredito);
        if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
            $obFormulario->addComponente($obTxtLimiteExercicio);
            $obIPopUpFuncao->geraFormulario($obFormulario);
        }
        break;
}

$obIPopUpNorma->geraFormulario($obFormulario);
$obFormulario->addComponente($obDtLimiteInscricaoDA);
$obFormulario->addComponente($obCmbCertidaoRemissao);
if ($_REQUEST['boTipoLancamentoManual'] != 'credito') {
    $obFormulario->addTitulo ('Grupo de Crédito');
    $obIPopUpGrupoCredito->geraFormulario($obFormulario, true, true);
} else {
    $obFormulario->addTitulo('Crédito');
    $obIPopUpCredito->geraFormulario($obFormulario, true, true);
    $obFormulario->addComponente($obExercicio);
}
$obFormulario->defineBarra($botoesGrupoCredito, 'left', '');
$obFormulario->addSpan($obSpnListaGrupos);
$obFormulario->ok();
$obFormulario->show();

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
    * Página de Formulario de Inclusao/Alteracao de Hierarquias
    * Data de Criação   : 19/11/2004

    * @author Tonismar Régis Bernardo
    * @ignore

    * $Id: FMManterAtividadeNivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.15  2007/05/17 21:12:28  cercato
Bug #9273#

Revision 1.14  2007/05/09 19:51:29  cercato
Bug #9231#

Revision 1.13  2007/04/26 14:54:18  cercato
Bug #9220#

Revision 1.12  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"      );
include_once ( CAM_GA_CSE_NEGOCIO."RProfissao.class.php"         );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMElemento.class.php"       );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMServico.class.php"        );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaServico.class.php"   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaCnae.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

include_once ($pgJS);

Sessao::write( "sessao_transf", array() );
Sessao::write( "Servicos", array() );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMAtividade    = new RCEMAtividade;
$obRProfissao       = new RProfissao;
$obRCEMElemento     = new RCEMElemento( $this );
$obRCEMServico      = new RCEMServico;
$obMontaAtividade   = new MontaAtividade;
$obMontaServico     = new MontaServico;
$rsUltimoNivel      = new RecordSet;
$rsVigencia         = new RecordSet;
$rsNiveisServico    = new RecordSet;

$obMontaCnae = new MontaCnae;
$obMontaCnae->setCadastroCnae( false );
$obMontaCnae->setPopUp( false );

$rsResponsaveisSelecionados = new RecordSet;
$rsResponsaveisDisponiveis  = new RecordSet;
$rsElementosSelecionados    = new RecordSet;
$rsElementosDisponiveis     = new RecordSet;

$obRProfissao->listarProfissao( $rsResponsaveisDisponiveis );
$obRCEMElemento->listarElemento( $rsElementosDisponiveis  );

$obRCEMAtividade->obRCEMConfiguracao->setAnoExercicio( Sessao::getExercicio() );
$obRCEMAtividade->obRCEMConfiguracao->consultarConfiguracao();
$boCnae = $obRCEMAtividade->obRCEMConfiguracao->getCNAE();

if ($stAcao == "incluir") {
    $arChaveNivel = explode( "-", $_REQUEST["stChaveNivel"] );
    $inCodigoVigencia = $arChaveNivel[0];
    $inCodigoNivel    = $arChaveNivel[1];
    $obRCEMAtividade->setCodigoVigencia ( $inCodigoVigencia );
    $obRCEMAtividade->setCodigoNivel    ( $inCodigoNivel    );

    $obMontaAtividade->setCadastroAtividade( true );
    $obMontaAtividade->setCodigoVigencia ( $inCodigoVigencia );
    $obMontaAtividade->setCodigoNivel    ( $inCodigoNivel    );
    $obRCEMAtividade->consultarNivel();

} else {
    $inCodigoVigencia    = $_REQUEST["inCodigoVigencia"];
    $inCodigoNivel       = $_REQUEST["inCodigoNivel"];
    $inCodigoAtividade   = $_REQUEST["inCodigoAtividade"];
    $stValorComposto     = $_REQUEST["stValorComposto"];
    $flAliquota          = $_REQUEST["flAliquota"];
    $obMontaCnae->setValorComposto( $_REQUEST['stValorCompostoCnae'] );

    $obRCEMAtividade->setCodigoVigencia    ( $inCodigoVigencia    );
    $obRCEMAtividade->setCodigoNivel       ( $inCodigoNivel       );
    $obRCEMAtividade->setCodigoAtividade   ( $inCodigoAtividade   );

    $obMontaAtividade->setCodigoVigencia    ( $inCodigoVigencia   );
    $obMontaAtividade->setCodigoNivel       ( $inCodigoNivel      );
    $obMontaAtividade->setCodigoAtividade   ( $inCodigoAtividade  );
    $obMontaAtividade->setValorComposto     ( $stValorComposto    );
    $obRCEMAtividade->consultarAtividade();

    $obRCEMAtividade->obRCEMServico->setAtivo(true);
    $obRCEMAtividade->listarAtividadeServico( $rsAtividadeServico );
    
    $inCount = 0;
    $arServicosSessao = array();
    while ( !$rsAtividadeServico->eof() ) {
        $arTmp['inId']            = ++$inCount;
        $arTmp['inCodigoServico'] = $rsAtividadeServico->getCampo( "cod_servico" );
        $arTmp['inCodigoEstrutural'] = $rsAtividadeServico->getCampo( "cod_estrutural" );
        $arTmp['stNomeServico']   = $rsAtividadeServico->getCampo( "nom_servico" );
        $arServicosSessao[] = $arTmp;
        $rsAtividadeServico->proximo();
    }
    
    Sessao::write( "Servicos", $arServicosSessao );
    $obRCEMAtividade->addAtividadeProfissao();
    $obRCEMAtividade->roUltimaProfissao->listarAtividadeProfissaoSelecionados( $rsResponsaveisSelecionados );
    $obRCEMAtividade->roUltimaProfissao->listarAtividadeProfissaoDisponiveis ( $rsResponsaveisDisponiveis  );

    $obRCEMAtividade->addAtividadeElemento();
    $obRCEMAtividade->roUltimoElemento->listarElementoAtividadeSelecionados  ( $rsElementosSelecionados );
    $obRCEMAtividade->roUltimoElemento->listarElementoAtividadeDisponiveis   ( $rsElementosDisponiveis  );
}

// VERIFICA SE E O ULTIMO NIVEL
$obErro = $obRCEMAtividade->listarNiveisPosteriores( $rsUltimoNivel );
if ( $rsUltimoNivel->getCampo(cod_nivel) == "" ) {
    $boUltimoNivel = true;
} else {
    $boUltimoNivel = false;
}

$obErro = $obRCEMAtividade->listarNiveisAnteriores( $rsNiveisAnteriores );

$stNomeNivel        = $obRCEMAtividade->getNomeNivel();
$stMascara          = $obRCEMAtividade->getMascara();
$inValorAtividade   = $obRCEMAtividade->getValor();
$stValorReduzido    = $_REQUEST["stValorReduzido"];
$stValorComposto    = substr( $stValorReduzido, 0, strlen( $stValorReduzido ) - ( strlen( $inValorAtividade ) + 1 ) );

if ( strlen($inValorAtividade) ) {
    $stValorComposto .= ".";
    for ($inX=0; $inX<strlen($inValorAtividade); $inX++) {
        $stValorComposto .= "0";
    }

    $obRCEMAtiv = new RCEMAtividade;
    $obRCEMAtiv->setValorComposto( $stValorComposto );
    $obRCEMAtiv->setCodigoVigencia( $inCodigoVigencia );
    $obRCEMAtiv->listarAtividade( $rsListaAtividadeAnterior );

    $stValorComposto .= " - ".$rsListaAtividadeAnterior->getCampo("nom_atividade");
}

if ($_REQUEST["stValorComposto"]) {
    $inValorAtividade = $_REQUEST["stValorComposto"]." - ".$_REQUEST["stNomeAtividade"];
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnCodigoNivel = new Hidden;
$obHdnCodigoNivel->setName  ( "inCodigoNivel" );
$obHdnCodigoNivel->setValue ( $inCodigoNivel  );

$obHdnCodigoVigencia = new Hidden;
$obHdnCodigoVigencia->setName  ( "inCodigoVigencia" );
$obHdnCodigoVigencia->setvalue ( $inCodigoVigencia );

$obHdnChaveServico = new Hidden;
$obHdnChaveServico->setName  ( "stChaveServico" );
$obHdnChaveServico->setValue ( $_REQUEST["stChaveServico"]  );

$obHdnUltimoNivel = new Hidden;
$obHdnUltimoNivel->setName  ( "boUltimoNivel" );
$obHdnUltimoNivel->setValue ( $boUltimoNivel  );

$obHdnCodCnae = new Hidden;
$obHdnCodCnae->setName  ( "inCodCnae" );
$obHdnCodCnae->setValue ( $_REQUEST["inCodCnae"]  );

$obLbNomeNivel = new Label;
$obLbNomeNivel->setRotulo( "Nível" );
$obLbNomeNivel->setValue( $stNomeNivel );

$obTxtCodigoAtividade = new TextBox;
$obTxtCodigoAtividade->setName      ( "inValorAtividade"    );
$obTxtCodigoAtividade->setRotulo    ( "Código"              );
$obTxtCodigoAtividade->setNull      ( false                 );
$obTxtCodigoAtividade->setMaxLength ( strlen( $stMascara)   );
$obTxtCodigoAtividade->setSize      ( strlen( $stMascara)   );
$obTxtCodigoAtividade->setValue     ( $inValorAtividade     );
$obTxtCodigoAtividade->setId        ( "codigoAtividade"     );
$obTxtCodigoAtividade->setMascara   ( $stMascara            );

$obTxtNomeAtividade = new TextBox;
$obTxtNomeAtividade->setName      ( "stNomeAtividade"   );
$obTxtNomeAtividade->setRotulo    ( "Nome"              );
$obTxtNomeAtividade->setMaxLength ( 240                 );
$obTxtNomeAtividade->setSize      ( 60                  );
$obTxtNomeAtividade->setNull      ( false               );
$obTxtNomeAtividade->setId        ( "nomeAtividade"     );
$obTxtNomeAtividade->setValue     ( $_REQUEST["stNomeAtividade"]    );

$flAliquota = str_replace( ".", ",", $flAliquota );

$obTxtAliquota = new Moeda;
$obTxtAliquota->setRotulo          ( "Alíquota" );
$obTxtAliquota->setName            ( "flAliquota" );
$obTxtAliquota->setValue           ( $flAliquota  );
$obTxtAliquota->setTitle           ( "Alíquota cobrada sobre o serviço" );
$obTxtAliquota->setNull            ( false );
$obTxtAliquota->setMaxLength       ( 10    );

$obBuscaCnae = new BuscaInner;
$obBuscaCnae->setId                             ( "inNumCnae"     );
$obBuscaCnae->setNull                           ( true              );
$obBuscaCnae->obCampoCod->setName               ( "inNumCnae"     );
$obBuscaCnae->obCampoCod->setValue              ( $_REQUEST["inNumCnae"]      );
$obBuscaCnae->obCampoCod->setInteiro            ( false             );
$obBuscaCnae->obCampoCod->obEvento->setOnChange ( "buscarCnae();" );
$obBuscaCnae->setFuncaoBusca                    ("abrePopUp('".CAM_GT_CEM_POPUPS."cnae/FLProcurarCnae.php','frm','inNumCnae','inNumCnae','','".Sessao::getId()."','800','550')");
$obBuscaCnae->setRotulo                         ( "CNAE"         );
$obBuscaCnae->setTitle                          ( "CNAE a qual a Atividade está vinculada" );

//definicao dos combos de responsaveis
$obCmbResponsaveis = new SelectMultiplo();
$obCmbResponsaveis->setName   ( "inCodResponsaveisSelecionados" );
$obCmbResponsaveis->setRotulo ( "Responsáveis" );
$obCmbResponsaveis->setNull   ( true );
$obCmbResponsaveis->setTitle  ( "Responsáveis que serão solicitados na inclusão da Atividade" );

// lista de responsaveis disponiveis
$obCmbResponsaveis->SetNomeLista1 ( "inCodResponsaveisDisponiveis" );
$obCmbResponsaveis->setCampoId1   ( "cod_profissao" );
$obCmbResponsaveis->setCampoDesc1 ( "nom_profissao" );
$obCmbResponsaveis->SetRecord1    ( $rsResponsaveisDisponiveis );

// lista de responsaveis selecionados
$obCmbResponsaveis->SetNomeLista2 ( "inCodResponsaveisSelecionados" );
$obCmbResponsaveis->setCampoId2   ( "cod_profissao" );
$obCmbResponsaveis->setCampoDesc2 ( "nom_profissao" );
$obCmbResponsaveis->SetRecord2    ( $rsResponsaveisSelecionados );

//definicao dos combos de elementos
$obCmbElementos = new SelectMultiplo();
$obCmbElementos->setName   ( "inCodElementosSelecionados" );
$obCmbElementos->setRotulo ( "Elementos" );
$obCmbElementos->setNull   ( true );
$obCmbElementos->setTitle  ( "Elementos que serão solicitados na inclusão da Atividade" );

// lista de elementos disponiveis
$obCmbElementos->SetNomeLista1 ( "inCodElementosDisponiveis" );
$obCmbElementos->setCampoId1   ( "cod_elemento" );
$obCmbElementos->setCampoDesc1 ( "nom_elemento" );
$obCmbElementos->SetRecord1    ( $rsElementosDisponiveis );

// lista de elementos selecionados
$obCmbElementos->SetNomeLista2 ( "inCodElementosSelecionados" );
$obCmbElementos->setCampoId2   ( "cod_elemento" );
$obCmbElementos->setCampoDesc2 ( "nom_elemento" );
$obCmbElementos->SetRecord2    ( $rsElementosSelecionados );

$obRCEMServico->recuperaVigenciaAtual( $rsVigenciaAtual );
$inCodigoVigenciaServico = $rsVigenciaAtual->getCampo( "cod_vigencia" );

$obFormulario   = new Formulario;
$obMontaServico->setCodigoVigenciaServico( $inCodigoVigenciaServico );
$obMontaServico->setCadastroAtividade( true );
$obMontaServico->geraFormulario( $obFormulario );
$obFormulario->montaInnerHTML();

$obTxtCodigoVigencia = new TextBox;
$obTxtCodigoVigencia->setName               ( "inCodigoVigenciaServico"  );
$obTxtCodigoVigencia->setRotulo             ( "Vigência"                 );
$obTxtCodigoVigencia->setMaxLength          ( 7                          );
$obTxtCodigoVigencia->setSize               ( 7                          );
$obTxtCodigoVigencia->setValue              ( $inCodigoVigenciaServico   );
$obTxtCodigoVigencia->obEvento->setOnChange ("preencheVigencia();"       );

$obRCEMServico->listarVigencia( $rsVigencia );

$obCmbVigencia = new Select;
$obCmbVigencia->setName                  ( "stDataVigencia"                 );
$obCmbVigencia->setValue                 ( $inCodigoVigenciaServico         );
$obCmbVigencia->setRotulo                ( "Vigência"                       );
$obCmbVigencia->setTitle                 ( "Vigência em que está o serviço" );
$obCmbVigencia->setCampoId               ( "[cod_vigencia]"                 );
$obCmbVigencia->setCampoDesc             ( "dt_inicio"                      );
$obCmbVigencia->addOption                ( "", "Selecione"                  );
$obCmbVigencia->preencheCombo            ( $rsVigencia                      );
$obCmbVigencia->setStyle                 ( "width: 200px"                   );
$obCmbVigencia->obEvento->setOnChange    ( "buscaCodigoVigencia();"         );

$obButtonIncluirServico = new Button;
$obButtonIncluirServico->setName             ( "btnIncluirServico" );
$obButtonIncluirServico->setValue            ( "Incluir" );
$obButtonIncluirServico->obEvento->setOnClick ( "incluirServico();" );
//$obButtonIncluirServico->obEvento->setOnClick ( "return incluirServico();" );

$obButtonLimparServico = new Button;
$obButtonLimparServico->setName              ( "btnLimparServico" );
$obButtonLimparServico->setValue             ( "Limpar" );
$obButtonLimparServico->obEvento->setOnClick  ( "limparServico('limparServico');" );

$obButtonOk = new Ok;
$obButtonOk->setName   ( "btnIncluir" );
$obButtonOk->setValue  ( "Ok"         );
$obButtonOk->obEvento->setOnClick( "Salvar();" );

$obButtonLimpar = new Limpar;
$obButtonLimpar->setName ( "btnLimpar" );
$obButtonLimpar->setValue( "Limpar"    );
$obButtonLimpar->obEvento->setOnClick( "Limpar();" );

$obSpnServicoCadastrado = new Span;
$obSpnServicoCadastrado->setId ( "spnServicoCadastrado" );

$obSpnNivelServico = new Span;
$obSpnNivelServico->setId    ( "spnServico" );
$obSpnNivelServico->setValue ( $obFormulario->getHTML() );

//DEFINICAO DOS COMPONENTES PARA ALTERAÇÃO
$obLblValorComposto = new Label;
$obLblValorComposto->setRotulo ( "Nível Superior" );
$obLblValorComposto->setValue  ( $stValorComposto );

$obLblCodigoAtividade = new Label;
$obLblCodigoAtividade->setRotulo ( "Código" );
$obLblCodigoAtividade->setValue  ( $inValorAtividade  );

$obHdnCodigoAtividade = new Hidden;
$obHdnCodigoAtividade->setName  ( "inCodigoAtividade" );
$obHdnCodigoAtividade->setValue ( $inCodigoAtividade  );

$obHdnValorReduzido = new Hidden;
$obHdnValorReduzido->setName  ( "stValorReduzido" );
$obHdnValorReduzido->setValue ( $stValorReduzido  );

$obHdnValorAtividade = new Hidden;
$obHdnValorAtividade->setName  ( "inValorAtividade" );
$obHdnValorAtividade->setValue ( $inValorAtividade  );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto"     );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm              );
$obFormulario->setAjuda      ( "UC-05.02.07");
$obFormulario->addTitulo     ( "Dados para Nível"   );
$obFormulario->addHidden     ( $obHdnAcao           );
$obFormulario->addHidden     ( $obHdnCtrl           );
$obFormulario->addHidden     ( $obHdnCodigoNivel    );
$obFormulario->addHidden     ( $obHdnCodigoVigencia );
$obFormulario->addHidden     ( $obHdnUltimoNivel    );
$obFormulario->addHidden     ( $obHdnValorReduzido  );
$obFormulario->addHidden     ( $obHdnCodCnae        );
$obFormulario->addComponente ( $obLbNomeNivel       );
if ($stAcao == "incluir") {
    if ($_GET["stValorComposto"]) {
        $obMontaAtividade->setValorComposto(  $_GET["stValorComposto"] );
        $obMontaAtividade->geraFormularioPreenchido( $obFormulario  );
    } else {
        $obMontaAtividade->geraFormulario( $obFormulario  );
    }

    $obFormulario->addComponente ( $obTxtCodigoAtividade   );
    $inNivel = explode("-", $_REQUEST["stChaveNivel"] );
    if ($inNivel[1] == 1) {
        $obFormulario->setFormFocus( $obTxtCodigoAtividade->getid() );
    } else {
        $obFormulario->setFormFocus( "stChaveAtividade");
    }
} else {
    $obFormulario->addHidden     ( $obHdnCodigoAtividade );
    $obFormulario->addHidden     ( $obHdnValorAtividade  );
    if ( $rsNiveisAnteriores->getNumLinhas() > 0 ) {
        $obFormulario->addComponente ( $obLblValorComposto   );
    }
    $obFormulario->addComponente ( $obLblCodigoAtividade );

    $obFormulario->setFormFocus( $obTxtNomeAtividade->getid() );
}
$obFormulario->addComponente ( $obTxtNomeAtividade     );
if ($boUltimoNivel) {
    $obFormulario->addComponente ( $obTxtAliquota          );
    if ($boCnae == 'Vincular') {
        if (( $stAcao == "incluir" ) || ( empty($_REQUEST['stValorCompostoCnae']) )) {
            $obMontaCnae->geraFormulario  ( $obFormulario         );
        } elseif ($stAcao == "alterar") {
            $obMontaCnae->geraFormularioPreenchido( $obFormulario );
        }
    }
    $obFormulario->addTitulo     ( "Responsáveis técnicos" );
    $obFormulario->addComponente ( $obCmbResponsaveis      );
    $obFormulario->addTitulo     ( "Elementos para base do cálculo" );
    $obFormulario->addComponente ( $obCmbElementos         );
    $obFormulario->addTitulo     ( "Serviços prestados no exercício da atividade" );
    $obFormulario->addComponenteComposto( $obTxtCodigoVigencia, $obCmbVigencia );
    $obFormulario->addSpan       ( $obSpnNivelServico      );
    $obFormulario->defineBarra   ( array($obButtonIncluirServico, $obButtonLimparServico), "left", "");
    $obFormulario->addSpan       ( $obSpnServicoCadastrado );
}

if ($stAcao == "incluir") {
    if ($inNivel[1] == 1) {
        $obFormulario->OK();
    } else {
        $obFormulario->defineBarra   ( array( $obButtonOk, $obButtonLimpar ), "left" , "" );
    }
} else {
    $obFormulario->Cancelar();
}

$obFormulario->show();

if ($stAcao == "alterar") {
    sistemaLegado::executaFrameOculto("buscarValor('ListaServico');");
}
?>

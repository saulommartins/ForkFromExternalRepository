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
    * Página de Formulario de Definiçao de Atividade para uma Inscrição Econômica
    * Data de Criação   : 30/12/2004

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMDefinirAtividades.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.18  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GA_CGM_NEGOCIO."RCGM.class.php" );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "DefinirAtividades";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

Sessao::write( 'Atividades', array() );

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

$obRCGM = new RCGM;
$obRCGM->setNumCGM($_REQUEST["inCGM"]);
$obRCGM->consultar($rsCGM);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"] );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"] );

$inCodAtividade = $_REQUEST["inCodigoAtividade"];

$obHdnCodigoAtividade = new Hidden;
$obHdnCodigoAtividade->setName  ( "inCodigoAtividade" );
$obHdnCodigoAtividade->setValue ( $inCodAtividade );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName  ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( $_REQUEST["inInscricaoEconomica"]  );

$obHdnDtAbertura = new Hidden;
$obHdnDtAbertura->setName ( "stDtAbertura" );
$obHdnDtAbertura->setValue( $_REQUEST["stDtAbertura"] );

$obHdnNumCGM = new Hidden;
$obHdnNumCGM->setName ( "inCGM" );
$obHdnNumCGM->setValue( $_REQUEST["inCGM"] );

$obHdnNomCGM = new Hidden;
$obHdnNomCGM->setName ( "stCGM" );
$obHdnNomCGM->setValue( $_REQUEST["stCGM"] );

$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );

//MONTAGEM DA LISTA DE HORÁRIOS JÁ CADASTRADOS
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->listarInscricaoHorarios( $rsHorarios );
$inCount=0;
$arHorariosSessao = array();
while ( !$rsHorarios->eof() ) {
    //REMOVE OS DOIS ÚLTIMOS ZEROS DOS SEGUNDOS QUE VEM POR DEFAULT NO BANCO
    $horaInicio = substr( $rsHorarios->getCampo('hr_inicio'), 0, -3);
    $horaFim    = substr( $rsHorarios->getCampo('hr_termino'), 0, -3);
    $arHorariosSessao[$inCount]['inId'                 ] = $inCount;
    $arHorariosSessao[$inCount]['inDia'                ] = $rsHorarios->getCampo('cod_dia');
    $arHorariosSessao[$inCount]['hrInicio'             ] = $horaInicio; //$rsHorarios->getCampo('hr_inicio');
    $arHorariosSessao[$inCount]['hrTermino'            ] = $horaFim; //$rsHorarios->getCampo('hr_termino');
    $arHorariosSessao[$inCount]['stDia'                ] = $rsHorarios->getCampo('nom_dia');
//    $sessao->transf4['horarios'][$inCount]['inInscricaoEconomica' ] = $rsHorarios->getCampo('inscricao_economica');
    $rsHorarios->proximo();
    $inCount++;
}

Sessao::write( 'horarios', $arHorariosSessao );
//MONTAGEM DA LISTA DE ATIVIDADE JÁ CADASTRADAS
$obRCEMInscricaoAtividade->addAtividade();
$rsAtividades = new RecordSet;
$obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );
$count = "";
$arAtividadesSessao = Sessao::read( "Atividades" );
while ( !$rsAtividades->eof() ) {
    $arTmp['inId']              = ++$count;
    $arTmp['inCodigoAtividade'] = $rsAtividades->getCampo( "cod_atividade"  );
    $arTmp['stNomeAtividade']   = $rsAtividades->getCampo( "nom_atividade"  );
    $arTmp['stChaveAtividade']  = $rsAtividades->getCampo( "cod_estrutural" );
    $arTmp['dtDataInicio']      = $rsAtividades->getCampo( "dt_inicio"      );
    $arTmp['dtDataTermino']     = $rsAtividades->getCampo( "dt_termino"     );
    if ( $rsAtividades->getCampo( "principal" ) == "t" ) {
        $arTmp['stPrincipal'] = "sim";
    } else {
        $arTmp['stPrincipal'] = "não";
    }

    $arAtividadesSessao[] = $arTmp;

    $rsAtividades->proximo();
}

Sessao::write( "Atividades", $arAtividadesSessao );
if ($_REQUEST["stAcao"] == 'def_ativ') {
    sistemaLegado::executaFrameOculto("buscaValor('recuperaAtividadeHorario')");
}

// labels do cgm e ie
$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo	( "Inscrição Econômica" );
$obLblInscricaoEconomica->setName	( "lblInscricaoEconomica" );
$obLblInscricaoEconomica->setValue	( $_REQUEST["inInscricaoEconomica"] );

$obLblNumCGM = new Label;
$obLblNumCGM->setRotulo	( "CGM" );
$obLblNumCGM->setName	( "lblNumCGM" );
$obLblNumCGM->setValue	( $obRCGM->getNumCGM()." - ".$obRCGM->getNomCGM() );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente ao cadastro de atividade para inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $_REQUEST["inNumProcesso"] );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obRdoPrincipalSim = new Radio;
$obRdoPrincipalSim->setName    ( "stPrincipal"   );
$obRdoPrincipalSim->setRotulo  ( "*Principal"    );
$obRdoPrincipalSim->setLabel   ( "Sim"           );
$obRdoPrincipalSim->setValue   ( "sim"           );

$obRdoPrincipalNao = new Radio;
$obRdoPrincipalNao->setName    ( "stPrincipal" );
$obRdoPrincipalNao->setRotulo  ( "*Principal"  );
$obRdoPrincipalNao->setLabel   ( "Não"         );
$obRdoPrincipalNao->setValue   ( "não"         );
$obRdoPrincipalNao->setChecked ( true          );

$obTxtDataInicio = new Data;
$obTxtDataInicio->setName    ( "dtDataInicio"   );
$obTxtDataInicio->setRotulo  ( "*Data de Início" );
//$obTxtDataInicio->setValue   ( $dtDataInicio   );
$obTxtDataInicio->setValue   ( date('d/m/Y')   );
$obTxtDataInicio->setNull    ( "false"          );

$obTxtDataTermino = new Data;
$obTxtDataTermino->setName    ( "dtDataTermino"  );
$obTxtDataTermino->setRotulo  ( "Data de Término");
$obTxtDataTermino->setValue   ( $_REQUEST["dtDataTermino"] );

$obBtnIncluirAtividade = new Button;
$obBtnIncluirAtividade->setName( "stIncluirAtividade" );
$obBtnIncluirAtividade->setValue( "Incluir" );
$obBtnIncluirAtividade->obEvento->setOnClick( "incluirAtividade();" );

$obBtnLimparAtividade = new Button;
$obBtnLimparAtividade->setName               ( "btnLimparAtividade" );
$obBtnLimparAtividade->setValue              ( "Limpar" );
$obBtnLimparAtividade->setTipo               ( "button" );
$obBtnLimparAtividade->obEvento->setOnClick  ( "limparAtividade();" );
$obBtnLimparAtividade->setDisabled           ( false );

$obSpnListaAtividade = new Span;
$obSpnListaAtividade->setId( "lsListaAtividade" );

$obCheckDomingo = new CheckBox;
$obCheckDomingo->setName               ( "dia1"                            );
$obCheckDomingo->setRotulo             ( "*Dia da Semana"                  );
$obCheckDomingo->setValue              ( "1"                               );
$obCheckDomingo->setLabel              ( "Domingo"                         );
$obCheckDomingo->setNull               ( true                              );

$obCheckSegunda = new CheckBox;
$obCheckSegunda->setName               ( "dia2"                            );
$obCheckSegunda->setRotulo             ( "*Dia da Semana"                   );
$obCheckSegunda->setValue              ( "2"                               );
$obCheckSegunda->setLabel              ( "Segunda-feira"                   );
$obCheckSegunda->setNull               ( true                              );

$obCheckTerca = new CheckBox;
$obCheckTerca->setName                 ( "dia3"                            );
$obCheckTerca->setRotulo               ( "*Dia da Semana"                  );
$obCheckTerca->setValue                ( "3"                               );
$obCheckTerca->setLabel                ( "Terça-feira"                     );
$obCheckTerca->setNull                 ( true                              );

$obCheckQuarta = new CheckBox;
$obCheckQuarta->setName                ( "dia4"                            );
$obCheckQuarta->setRotulo              ( "*Dia da Semana"                  );
$obCheckQuarta->setValue               ( "4"                               );
$obCheckQuarta->setLabel               ( "Quarta-feira"                    );
$obCheckQuarta->setNull                ( true                              );
$obCheckQuinta = new CheckBox;
$obCheckQuinta->setName                ( "dia5"                            );
$obCheckQuinta->setRotulo              ( "*Dia da Semana"                  );
$obCheckQuinta->setValue               ( "5"                               );
$obCheckQuinta->setLabel               ( "Quinta-feira"                    );
$obCheckQuinta->setNull                ( true                              );
$obCheckSexta = new CheckBox;
$obCheckSexta->setName                 ( "dia6"                            );
$obCheckSexta->setRotulo               ( "*Dia da Semana"                  );
$obCheckSexta->setValue                ( "6"                               );
$obCheckSexta->setLabel                ( "Sexta-feira"                     );
$obCheckSexta->setNull                 ( true                              );

$obCheckSabado = new CheckBox;
$obCheckSabado->setName                ( "dia7"                            );
$obCheckSabado->setRotulo              ( "*Dia da Semana"                  );
$obCheckSabado->setValue               ( "7"                               );
$obCheckSabado->setLabel               ( "Sábado"                          );
$obCheckSabado->setNull                ( true                              );

$obHrHorarioInicio = new Hora;
$obHrHorarioInicio->setName            ( "hrHorarioInicio"                 );
$obHrHorarioInicio->setValue           ( $_REQUEST["hrHorarioInicio"]      );
$obHrHorarioInicio->setRotulo          ( "Horário de Início"              );
$obHrHorarioInicio->setMaxLength       ( 20                                );
$obHrHorarioInicio->setSize            ( 10                                );
$obHrHorarioInicio->setNull            ( true                              );

$obHrHorarioTermino = new Hora;
$obHrHorarioTermino->setName           ( "hrHorarioTermino"                );
$obHrHorarioTermino->setValue          ( $_REQUEST["hrHorarioTermino"]     );
$obHrHorarioTermino->setRotulo         ( "Horário de Término"             );
$obHrHorarioTermino->setMaxLength      ( 20                                );
$obHrHorarioTermino->setSize           ( 10                                );
$obHrHorarioTermino->setNull           ( true                              );
// DEFINE BOTOES
$obBtnIncluirDias = new Button;
$obBtnIncluirDias->setName              ( "btnIncluirDias"      );
$obBtnIncluirDias->setValue             ( "Incluir"             );
$obBtnIncluirDias->setTipo              ( "button"              );
$obBtnIncluirDias->obEvento->setOnClick ( "incluirHorario();"   );
$obBtnIncluirDias->setDisabled          ( false                 );

$obBtnLimparDias = new Button;
$obBtnLimparDias->setName               ( "btnLimparDias"       );
$obBtnLimparDias->setValue              ( "Limpar"              );
$obBtnLimparDias->setTipo               ( "button"              );
$obBtnLimparDias->obEvento->setOnClick  ( "limparHorario();"    );
$obBtnLimparDias->setDisabled           ( false                 );

$obCheckSegueResponsaveis = new CheckBox;
$obCheckSegueResponsaveis->setName                ( "boSegueResponsaveis"                    );
$obCheckSegueResponsaveis->setValue               ( "1"                                   );
$obCheckSegueResponsaveis->setLabel               ( "Seguir para Definição de Responsáveis?" );
$obCheckSegueResponsaveis->setNull                ( true                                  );
$obCheckSegueResponsaveis->setChecked     ( true                                  );

$obSpnListaHorario = new Span;
$obSpnListaHorario->setId( "lsListaHorario" );

$obBtnOK = new OK;

$onBtnLimpar = new Button;
$onBtnLimpar->setValue( "Limpar" );
$onBtnLimpar->obEvento->setOnClick( "limpar();" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda            ( "UC-05.02.10");
$obFormulario->addTitulo           ( "Dados da Inscrição Econômica" );
$obFormulario->addHidden           ( $obHdnCodigoAtividade );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnInscricaoEconomica );
$obFormulario->addHidden           ( $obHdnNumCGM );
$obFormulario->addHidden           ( $obHdnNomCGM );
$obFormulario->addHidden           ( $obHdnDtAbertura );
$obFormulario->addComponente       ( $obLblNumCGM );
$obFormulario->addComponente       ( $obLblInscricaoEconomica );
$obFormulario->addComponente       ( $obBscProcesso );
$obFormulario->addTitulo           ( "Atividades Econômicas" );
//$obFormulario->addComponente       ( $obBscAtividade );
$obMontaAtividade->geraFormulario  ( $obFormulario );
$obFormulario->agrupaComponentes   ( array( $obRdoPrincipalSim, $obRdoPrincipalNao ) );
$obFormulario->addComponente       ( $obTxtDataInicio  );
$obFormulario->addComponente       ( $obTxtDataTermino );
$obFormulario->agrupaComponentes   ( array( $obBtnIncluirAtividade, $obBtnLimparAtividade ) );
//$obFormulario->addTitulo           ( "Lista de atividades econômicas" );
$obFormulario->addSpan             ( $obSpnListaAtividade );
$obFormulario->addTitulo           ( "Horário de Exercício das Atividades" );
$obFormulario->addComponente       ( $obCheckSegunda );
$obFormulario->addComponente       ( $obCheckTerca   );
$obFormulario->addComponente       ( $obCheckQuarta  );
$obFormulario->addComponente       ( $obCheckQuinta  );
$obFormulario->addComponente       ( $obCheckSexta   );
$obFormulario->addComponente       ( $obCheckSabado  );
$obFormulario->addComponente       ( $obCheckDomingo );
$obFormulario->addComponente       ( $obHrHorarioInicio  );
$obFormulario->addComponente       ( $obHrHorarioTermino );
$obFormulario->agrupaComponentes   ( array( $obBtnIncluirDias, $obBtnLimparDias ) );
//$obFormulario->addTitulo           ( "Lista de horários" );
$obFormulario->addSpan             ( $obSpnListaHorario  );
$obFormulario->addComponente       ( $obCheckSegueResponsaveis );
$obFormulario->defineBarra( array( $obBtnOK , $onBtnLimpar ) );
$obFormulario->show();

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
    * Página de Formulario de Alteração de Horário para uma Inscrição Econômica
    * Data de Criação   : 31/01/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricaoHorarioAlteracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.14  2007/03/08 13:53:00  cercato
Bug #8599#

Revision 1.13  2007/03/07 19:36:01  rodrigo
Bug #6483#

Revision 1.12  2007/02/12 11:07:08  rodrigo
#6483#

Revision 1.11  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"   );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PRDefinirAtividades.php";
$pgOcul = "OCDefinirAtividades.php";
$pgNatAlt = "FM".$stPrograma."NaturezaAlteracao.php";
$pgSocAlt = "FM".$stPrograma."SociedadeAlteracao.php";
$pgAtvAlt = "FM".$stPrograma."AtividadeAlteracao.php";
$pgDomAlt = "FM".$stPrograma."DomicilioAlteracao.php";
$pgEleAlt = "FM".$stPrograma."ElementosAlteracao.php";
$pgHorAlt = "FM".$stPrograma."HorarioAlteracao.php";
$pgJS   = "JSDefinirAtividades.js";

include_once( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obRCEMInscricaoEconomica = new RCEMEmpresaDeDireito;

if ($_REQUEST[ 'inInscricaoEconomica' ]) {
    $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );
}

$obRCEMInscricaoEconomica->listarInscricaoHorarios( $rsHorarios );
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
sistemaLegado::executaFrameOculto( "buscaValor('recuperarHorario');" );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( 'inInscricaoEconomica' );
$obHdnInscricaoEconomica->setValue( $_REQUEST[ 'inInscricaoEconomica' ] );

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

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de horário de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obSpnListaHorario = new Span;
$obSpnListaHorario->setId( "lsListaHorario" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda            ( "UC-05.02.10" );
$obFormulario->addComponente       ( $obBscProcesso );
$obFormulario->addTitulo           ( "Horário de Exercício das Atividades" );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnInscricaoEconomica );
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
//$obFormulario->addTitulo           ( "Lista de Horários" );
$obFormulario->addSpan             ( $obSpnListaHorario  );
$obFormulario->Cancelar();
$obFormulario->show();

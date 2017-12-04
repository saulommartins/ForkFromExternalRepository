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
    * Página de Formulario de Alteração de Atividade
    * Data de Criação   : 12/05/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMAlterarAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.12  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"          );
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma = "AlterarAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSManterInscricao.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

Sessao::write( 'Atividades', array() );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $stCtrl  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCodigoAtividade = new Hidden;
$obHdnCodigoAtividade->setName  ( "inCodigoAtividade" );
$obHdnCodigoAtividade->setValue ( $inCodigoAtividade );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName  ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( $inInscricaoEconomica  );

$obHdnDtAbertura = new Hidden;
$obHdnDtAbertura->setName ( "stDtAbertura" );
$obHdnDtAbertura->setValue( $stDtAbertura  );

$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $inInscricaoEconomica );
$obRCEMInscricaoAtividade->addAtividade();
$rsAtividades = new RecordSet;
$obRCEMInscricaoAtividade->consultarAtividadesInscricao( $rsAtividades );
$count = "";

$arAtividadesSessao = array();
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

Sessao::write( 'Atividades', $arAtividadesSessao );
sistemaLegado::executaFrameOculto('buscaValor("montaAtividadeAlterar")');

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de atividades de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $inNumProcesso );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obLblCGM = new Label;
$obLblCGM->setName  ( "inNumCGMInscricao" );
$obLblCGM->setValue ( $_REQUEST['inCGM']." - ".$_REQUEST['stCGM'] );
$obLblCGM->setRotulo( "CGM"               );

$obLblInscricao = new Label;
$obLblInscricao->setName ( "inInscricaoEconomica"  );
$obLblInscricao->setValue( $_REQUEST['inInscricaoEconomica']);
$obLblInscricao->setRotulo( "Inscrição Econômica"  );

$obBtnIncluirAtividade = new Button;
$obBtnIncluirAtividade->setName( "stIncluirAtividade" );
$obBtnIncluirAtividade->setValue( "Incluir" );
$obBtnIncluirAtividade->obEvento->setOnClick( "incluirAtividade();" );

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
//$obTxtDataInicio->setValue   ( $dtDataInicio    );
$obTxtDataInicio->setValue   ( date('d/m/Y')     );
$obTxtDataInicio->setNull    ( "false"          );

$obTxtDataTermino = new Data;
$obTxtDataTermino->setName    ( "dtDataTermino"  );
$obTxtDataTermino->setRotulo  ( "Data de Término");
$obTxtDataTermino->setValue   ( $dtDataTermino   );

$obBtnLimparAtividade = new Button;
$obBtnLimparAtividade->setName               ( "btnLimparAtividade"       );
$obBtnLimparAtividade->setValue              ( "Limpar"              );
$obBtnLimparAtividade->setTipo               ( "button"              );
$obBtnLimparAtividade->obEvento->setOnClick  ( "limparAtividade();"    );
$obBtnLimparAtividade->setDisabled           ( false                 );

$obSpnListaAtividade = new Span;
$obSpnListaAtividade->setId( "lsListaAtividade" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda            ( "UC-05.02.10");
$obFormulario->addTitulo           ( "Dados para Inscrição Econômica" );
$obFormulario->addComponente       ( $obLblCGM );
$obFormulario->addComponente       ( $obLblInscricao );
$obFormulario->addComponente       ( $obBscProcesso );
$obFormulario->addTitulo           ( "Atividades Econômicas" );
$obFormulario->addHidden           ( $obHdnCodigoAtividade );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnInscricaoEconomica );
$obFormulario->addHidden           ( $obHdnDtAbertura );
$obMontaAtividade->geraFormulario  ( $obFormulario );
$obFormulario->agrupaComponentes   ( array( $obRdoPrincipalSim, $obRdoPrincipalNao ) );
$obFormulario->addComponente       ( $obTxtDataInicio  );
$obFormulario->addComponente       ( $obTxtDataTermino );
$obFormulario->agrupaComponentes   ( array( $obBtnIncluirAtividade, $obBtnLimparAtividade ) );
$obFormulario->addSpan             ( $obSpnListaAtividade );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar();
}

$obFormulario->Show();

?>

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
    * Página de Formulario de Alteração de Sociedade
    * Data de Criação   : 09/05/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMAlterarSociedade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.9  2007/10/15 19:51:18  cercato
Ticket#10407#

Revision 1.8  2006/09/15 14:33:01  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "AlterarSociedade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LSManterInscricao.php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}
Sessao::write( 'socios', array() );
$arSociosSessao = array();
$obRCEMEmpresaDeDireito = new RCEMEmpresaDeDireito;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $_REQUEST["stCtrl"] );

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setTitle ( "Processo do protocolo referente à alteração de sociedade de inscrição econômica" );
$obBscProcesso->setNull ( true );
$obBscProcesso->obCampoCod->setName ("inNumProcesso");
$obBscProcesso->obCampoCod->setId   ("inNumProcesso");
$obBscProcesso->obCampoCod->setValue( $_REQUEST["inNumProcesso"] );
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso');" );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp( "mascaraDinamico('99999/9999', this, event);" );
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inNumProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName  ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( $_REQUEST["inInscricaoEconomica"]  );

$obLblCGM = new Label;
$obLblCGM->setName  ( "inNumCGMInscricao" );
$obLblCGM->setValue ( $_REQUEST['inCGM']." - ".$_REQUEST['stCGM'] );
$obLblCGM->setRotulo( "CGM"               );

$obLblInscricao = new Label;
$obLblInscricao->setName ( "inInscricaoEconomica"  );
$obLblInscricao->setValue( $_REQUEST['inInscricaoEconomica']);
$obLblInscricao->setRotulo( "Inscrição Econômica"  );

$obLblCapitalSocial = new Label;
$obLblCapitalSocial->setRotulo( "*Capital Social (R$)" );
$obLblCapitalSocial->setId    ( "flCapitalSocial" );

$obRCEMEmpresaDeDireito->setInscricaoEconomica( $_REQUEST["inInscricaoEconomica"] );
$rsSocios = new RecordSet;
$obRCEMEmpresaDeDireito->listarEmpresaDireitoSociedade( $rsSocios );

$count = "";
while ( !$rsSocios->eof() ) {
    $arTmp['inLinha']       = ++$count;
    $arTmp['inCodigoSocio'] = $rsSocios->getCampo( 'numcgm'      );
    $arTmp['stNomeSocio']   = $rsSocios->getCampo( 'nom_cgm'     );
    $arTmp['flQuota']       = str_replace('.',',',$rsSocios->getCampo( 'quota_socio' ) );

    $arSociosSessao[] = $arTmp;
    $rsSocios->proximo();
}

Sessao::write( 'socios', $arSociosSessao );

sistemaLegado::executaFrameOculto('buscaValor("ListaSocio")');

$obBscSocio = new BuscaInner;
$obBscSocio->setRotulo( "*Sócio" );
$obBscSocio->setId( "stNomeSocio" );
$obBscSocio->obCampoCod->setName("inCodigoSocio");
$obBscSocio->obCampoCod->setValue( $_REQUEST["inCodigoSocio"] );
$obBscSocio->obCampoCod->obEvento->setOnChange("busca('buscaSocio');");
$obBscSocio->setFuncaoBusca( "abrePopUp('".CAM_GA_CGM_POPUPS."cgm/FLProcurarCgm.php','frm','inCodigoSocio','stNomeSocio','todos','".Sessao::getId()."','800','550');" );

$obTxtQuota = new Moeda;
$obTxtQuota->setRotulo          ( "*Quota (R$)" );
$obTxtQuota->setName            ( "flQuota" );
$obTxtQuota->setValue           ( $_REQUEST["flQuota"]  );
$obTxtQuota->setTitle           ( "Valor da quota do sócio (em Reais)" );
$obTxtQuota->setMaxLength       ( 10    );

$obSpnListaSocio = new Span;
$obSpnListaSocio->setId( "lsListaSocio" );

$obButtonIncluirSocio = new Button;
$obButtonIncluirSocio->setName              ( "btnIncluirSocio" );
$obButtonIncluirSocio->setValue             ( "Incluir" );
$obButtonIncluirSocio->obEvento->setOnClick ( "return incluirSocio();" );

$obButtonLimparSocio = new Button;
$obButtonLimparSocio->setName              ( "btnLimparSocio" );
$obButtonLimparSocio->setValue             ( "Limpar" );
$obButtonLimparSocio->obEvento->setOnClick ( "limparSocio();" );

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm             ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");

$obFormulario->addTitulo           ( "Dados da Inscrição Econômica" );
$obFormulario->addHidden           ( $obHdnInscricaoEconomica );
$obFormulario->addHidden           ( $obHdnAcao );
$obFormulario->addHidden           ( $obHdnCtrl );
$obFormulario->addComponente       ( $obLblCGM );
$obFormulario->addComponente       ( $obLblInscricao );
$obFormulario->addComponente       ( $obBscProcesso );
$obFormulario->addTitulo           ( "Dados para Sociedade" );
$obFormulario->addComponente       ( $obLblCapitalSocial );
$obFormulario->addTitulo           ( "Sociedade" );
$obFormulario->addComponente       ( $obBscSocio );
$obFormulario->addComponente       ( $obTxtQuota );
$obFormulario->agrupaComponentes   ( array( $obButtonIncluirSocio, $obButtonLimparSocio ) );
$obFormulario->addSpan             ( $obSpnListaSocio );
$obFormulario->Cancelar();
$obFormulario->Show();
?>

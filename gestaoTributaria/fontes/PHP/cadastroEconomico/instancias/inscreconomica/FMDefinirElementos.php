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
    * Página de Formulario de Definiçao de Elementos para uma Inscrição Econômica
    * Data de Criação   : 25/04/2005

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMDefinirElementos.php 65763 2016-06-16 17:31:43Z evandro $

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
include_once ( CAM_GT_CEM_COMPONENTES."MontaAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "DefinirElementos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRCEMInscricaoAtividade = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );

$obMontaAtividade = new MontaAtividade;
$obMontaAtividade->setCadastroAtividade( false );

Sessao::remove( "elementos" );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( $_REQUEST["stCtrl"]  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST["stAcao"]  );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName  ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( $_REQUEST["inInscricaoEconomica"] );

$obLblCGM = new Label;
$obLblCGM->setName  ( "inNumCGMInscricao" );
$obLblCGM->setValue ( $_REQUEST['inCGM']." - ".$_REQUEST['stCGM'] );
$obLblCGM->setRotulo( "CGM"               );

$obLblInscricao = new Label;
$obLblInscricao->setName ( "inInscricaoEconomica"  );
$obLblInscricao->setValue( $_REQUEST['inInscricaoEconomica']);
$obLblInscricao->setRotulo( "Número de Inscrição"  );

$obHdnCodigoElemento = new Hidden;
$obHdnCodigoElemento->setName  ( "inCodigoElemento" );
$obHdnCodigoElemento->setValue ( $_REQUEST["inCodigoElemento"]  );

/*$rsElementos = new RecordSet;
$obRCEMInscricaoAtividade->addAtividade();
$obRCEMInscricaoAtividade->roUltimaAtividade->addAtividadeElemento();
$obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
$obRCEMInscricaoAtividade->listarElementoAtividade( $rsElementos );*/

$obCmbElemento = new Select;
$obCmbElemento->setName               ( "stElemento"    );
$obCmbElemento->setValue              ( $_REQUEST["stElemento"] );
$obCmbElemento->setRotulo             ( "Elemento"      );
$obCmbElemento->setCampoId            ( "cod_elemento"  );
$obCmbElemento->setCampoDesc          ( "nom_elemento"  );
$obCmbElemento->addOption             ( "", "Selecione" );
//$obCmbElemento->preencheCombo         ( $rsElementos    );
$obCmbElemento->obEvento->setOnChange ( "preencheCodigoElemento();" );

$obSpnAtributosElementos = new Span;
$obSpnAtributosElementos->setId( "spnElementos" );

$obBtnIncluirElemento = new Button;
$obBtnIncluirElemento->setName             ( "btnIncluirElemento" );
$obBtnIncluirElemento->setValue            ( "Incluir" );
$obBtnIncluirElemento->obEvento->setOnClick( "return incluirElementos();" );

$obBtnLimparElemento = new Button;
$obBtnLimparElemento->setName              ( "btnLimparElemento" );
$obBtnLimparElemento->setValue             ( "Limpar" );
$obBtnLimparElemento->obEvento->setOnClick ( "buscaValor('limparElementos');" );

$obSpnListaElementos = new Span;
$obSpnListaElementos->setId ( "lsElementos" );

$obCmbAtividade = new Select;
$obCmbAtividade->setRotulo    ( "Atividade"     );
$obCmbAtividade->setName      ( "cmbAtividade"  );
$obCmbAtividade->setId        ( "cmbAtividade"  );
$obCmbAtividade->addOption    ( "", "Selecione Atividade"   );
$obCmbAtividade->setCampoId   ( "cod_atividade" );
$obCmbAtividade->setCampoDesc ( "[cod_estrutural] - [nom_atividade]" );
$obCmbAtividade->setStyle     ( "width:250px"     );
$obCmbAtividade->obEvento->setOnChange("buscaValor('montaElementoAtividade');");

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addTitulo    ( "Dados da Inscrição Econômica" );
$obFormulario->addHidden    ( $obHdnCtrl );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnInscricaoEconomica );
$obFormulario->addHidden    ( $obHdnCodigoElemento     );
$obFormulario->addComponente( $obLblCGM );
$obFormulario->addComponente( $obLblInscricao );
$obFormulario->addTitulo    ( "Dados para Elementos para Base de Cálculo" );
//$obMontaAtividade->geraFormulario  ( $obFormulario );
$obFormulario->addComponente( $obCmbAtividade );
$obFormulario->addComponente( $obCmbElemento );
$obFormulario->addSpan      ( $obSpnAtributosElementos );
$obFormulario->agrupaComponentes( array( $obBtnIncluirElemento, $obBtnLimparElemento ) );
$obFormulario->addSpan      ( $obSpnListaElementos );
$obFormulario->Ok();
$obFormulario->show();
if ($_REQUEST['stAcao'] == "def_elem") {
    $obMontaAtividade = new MontaAtividade;
    $obMontaAtividade->setInscricaoEconomica ($_REQUEST["inInscricaoEconomica"]);
    $obMontaAtividade->geraFormularioRestrito($stJs,"cmbAtividade");
    $stJs .= "buscaValor('montaElementosAlteracao');";
    sistemaLegado::executaFrameOculto($stJs);
}

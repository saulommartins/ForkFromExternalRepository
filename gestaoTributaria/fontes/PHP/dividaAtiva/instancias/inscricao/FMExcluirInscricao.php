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
    * Página de Formulario de Filtro para exclusão de Dívida Ativa

    * Data de Criação   : 26/09/2006

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FMExcluirInscricao.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.04.03

*/

/*
$Log$
Revision 1.5  2007/07/23 19:21:56  cercato
Bug#9723#

Revision 1.4  2007/07/17 14:37:48  cercato
correcao para rotina de cancelamento de divida.

Revision 1.3  2007/04/16 13:13:20  cercato
Bug #9107#

Revision 1.2  2006/10/05 15:35:24  dibueno
Alterações no nome dos arquivos relacionados e destino da ação

Revision 1.1  2006/10/02 15:16:26  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_COMPONENTES."IPopUpDivida.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaCancelada.class.php" );
include_once(CAM_FRAMEWORK."/request/Request.class.php" );
if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "alterar";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ExcluirInscricao";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OCManterInscricao.php";
$pgJs          = "JSManterInscricao.js";

include_once( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao']  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $request->get('stCtrl')  );

$obTDATDividaCancelada = new TDATDividaCancelada;
$obTDATDividaCancelada->consultarMascaraProcesso( $stMascaraProcesso, Sessao::getExercicio() );

//DEFINICAO DO TEXT AREA
$obTxtMotivo = new TextArea;
$obTxtMotivo->setName ( "stMotivo" );
$obTxtMotivo->setNull ( false );
$obTxtMotivo->setTitle ( "Informe o motivo para a cancelamento da divida ativa." );
$obTxtMotivo->setRotulo ("Motivo");

$obBuscaDivida = new IPopUpDivida;

$obBscProcesso = new BuscaInner;
$obBscProcesso->setRotulo ( "Processo" );
$obBscProcesso->setNull ( true );
$obBscProcesso->setTitle ("Informe o processo referente ao cancelamento da inscrição em dívida.");
$obBscProcesso->obCampoCod->setName ("inProcesso");
$obBscProcesso->obCampoCod->setId   ("inProcesso");
if (isset($inProcesso)) {
    $obBscProcesso->obCampoCod->setValue( $inProcesso );
}
$obBscProcesso->obCampoCod->obEvento->setOnChange( "buscaValor('buscaProcesso','');" );
$obBscProcesso->obCampoCod->setSize ( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->setMaxLength( strlen($stMascaraProcesso) );
$obBscProcesso->obCampoCod->obEvento->setOnKeyUp ("mascaraDinamico('".$stMascaraProcesso."', this, event);");
$obBscProcesso->setFuncaoBusca( "abrePopUp('".CAM_GA_PROT_POPUPS."processo/FLBuscaProcessos.php','frm','inProcesso','campoInner2','','".Sessao::getId()."','800','550')" );

$obCBoxEmitir = new CheckBox;
$obCBoxEmitir->setName ( "boEmissaoDocumento" );
$obCBoxEmitir->setLabel ( "Emitir Comprovante de Cancelamento de inscrição em dívida" );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->setAjuda     ( "UC-05.04.03" );
$obFormulario->addHidden    ( $obHdnAcao );
$obFormulario->addHidden    ( $obHdnCtrl );

$obBuscaDivida->geraFormulario ( $obFormulario );
$obFormulario->addComponente ( $obTxtMotivo );
$obFormulario->addComponente ( $obBscProcesso );
$obFormulario->addComponente ( $obCBoxEmitir );

$obFormulario->Ok ();
$obFormulario->show();

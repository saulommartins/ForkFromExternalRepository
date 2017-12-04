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
    * Página de Formulario de Anulação de Nota Avulsa

    * Data de Criação   : 24/06/2008

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @ignore

    * $Id: FMEscriturarReceita.php 29710 2008-05-07 14:23:45Z cercato $

    *Casos de uso: uc-05.03.22

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "AnularNotaAvulsa";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php?stAcao=$stAcao";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $stCtrl  );

$obHdnInscricaoEconomica = new Hidden;
$obHdnInscricaoEconomica->setName ( "inInscricaoEconomica" );
$obHdnInscricaoEconomica->setValue ( $_REQUEST["inInscricaoEconomica"] );

$obHdnCGM = new Hidden;
$obHdnCGM->setName ( "inNumCGM" );
$obHdnCGM->setValue ( $_REQUEST["inNumCGM"] );

$obHdnAtividade = new Hidden;
$obHdnAtividade->setName ( "inCodAtividade" );
$obHdnAtividade->setValue ( $_REQUEST["inCodAtividade"] );

$obHdnNroSerie = new Hidden;
$obHdnNroSerie->setName ( "stNroSerie" );
$obHdnNroSerie->setValue ( $_REQUEST["stNroSerie"] );

$obHdnNroNota = new Hidden;
$obHdnNroNota->setName ( "inNroNota" );
$obHdnNroNota->setValue ( $_REQUEST["inNroNota"] );

$obHdnCodNota = new Hidden;
$obHdnCodNota->setName ( "inCodNota" );
$obHdnCodNota->setValue ( $_REQUEST["inCodNota"] );

$obHdnNumeracao = new Hidden;
$obHdnNumeracao->setName ( "inNumeracao" );
$obHdnNumeracao->setValue ( $_REQUEST["inNumeracao"] );

$obHdnCodConvenio = new Hidden;
$obHdnCodConvenio->setName ( "inCodConvenio" );
$obHdnCodConvenio->setValue ( $_REQUEST["inCodConvenio"] );

$obLblInscricaoEconomica = new Label;
$obLblInscricaoEconomica->setRotulo    ( "Inscrição Econômica" );
$obLblInscricaoEconomica->setName      ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setId        ( "stInscricaoEconomica" );
$obLblInscricaoEconomica->setValue     ( $_REQUEST["inInscricaoEconomica"] );

$obLblCGMdoPrestador = new Label;
$obLblCGMdoPrestador->setRotulo    ( "CGM do Prestador" );
$obLblCGMdoPrestador->setName      ( "stLblCGM" );
$obLblCGMdoPrestador->setId        ( "stLblCGM" );
$obLblCGMdoPrestador->setValue     ( $_REQUEST["inNumCGM"]." - ".$_REQUEST["stNomCGM"] );

$obLblAtividade = new Label;
$obLblAtividade->setRotulo    ( "Atividade" );
$obLblAtividade->setName      ( "stAtividade" );
$obLblAtividade->setId        ( "stAtividade" );
if ( $_REQUEST["inCodAtividade"] )
    $obLblAtividade->setValue     ( $_REQUEST["inCodAtividade"]." - ".$_REQUEST["stNomAtividade"] );

$obLblSerie = new Label;
$obLblSerie->setRotulo    ( "Série" );
$obLblSerie->setName      ( "stSerie" );
$obLblSerie->setId        ( "stSerie" );
$obLblSerie->setValue     ( $_REQUEST["stNroSerie"] );

$obLblNota = new Label;
$obLblNota->setRotulo    ( "Número da Nota" );
$obLblNota->setName      ( "stNota" );
$obLblNota->setId        ( "stNota" );
$obLblNota->setValue     ( $_REQUEST["inNroNota"] );

$obTxtObservacao = new TextArea;
$obTxtObservacao->setTitle ( "Observação." );
$obTxtObservacao->setName ( "stObservacao" );
$obTxtObservacao->setRotulo ( "Observação" );
$obTxtObservacao->setValue ( "" );
$obTxtObservacao->setNull ( false );
$obTxtObservacao->setCols ( 30 );
$obTxtObservacao->setRows ( 5 );
$obTxtObservacao->setMaxCaracteres( 300 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );
$obForm->settarget ( "oculto" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.03.22" );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnInscricaoEconomica );
$obFormulario->addHidden ( $obHdnCGM );
$obFormulario->addHidden ( $obHdnAtividade );
$obFormulario->addHidden ( $obHdnNroSerie );
$obFormulario->addHidden ( $obHdnNroNota );
$obFormulario->addHidden ( $obHdnCodNota );
$obFormulario->addHidden ( $obHdnNumeracao );
$obFormulario->addHidden ( $obHdnCodConvenio );

$obFormulario->addTitulo ( "Dados para Anulação de Nota Avulsa" );
$obFormulario->addComponente ( $obLblInscricaoEconomica );
$obFormulario->addComponente ( $obLblCGMdoPrestador );
$obFormulario->addComponente ( $obLblAtividade );
$obFormulario->addComponente ( $obLblSerie );
$obFormulario->addComponente ( $obLblNota );
$obFormulario->addComponente ( $obTxtObservacao );

$obFormulario->Cancelar( $pgList );
$obFormulario->show();

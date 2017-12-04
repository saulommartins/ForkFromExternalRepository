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
    * Página de Formulario de Inclusao/Alteracao de Fiscal

    * Data de Criação   : 23/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: FLManterFiscal.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php"                                            );

$stAcao       = $_REQUEST['stAcao'];

Sessao::write( 'link', "" );
Sessao::write( 'arValores', array() );

if ( empty( $stAcao ) ) { $stAcao = "incluir"; }

//Define o nome dos arquivos PHP
$stPrograma = "ManterFiscal";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl  );

$obRadioAtivoNao = new Radio;
$obRadioAtivoNao->setName   ( "boAtivo"                        );
$obRadioAtivoNao->setRotulo ( "Ativo"                          );
$obRadioAtivoNao->setTitle  ( "Informe a situação do Fiscal." );
$obRadioAtivoNao->setValue  ( "f"                          );
$obRadioAtivoNao->setLabel  ( "Não"                            );
$obRadioAtivoNao->setNull   ( true                             );
$obRadioAtivoNao->setChecked( true                             );

$obRadioAtivoSim = new Radio;
$obRadioAtivoSim->setName   ( "boAtivo" );
$obRadioAtivoSim->setValue  ( "t"    );
$obRadioAtivoSim->setLabel  ( "Sim"     );
$obRadioAtivoSim->setNull   ( true      );
$obRadioAtivoSim->setChecked( false     );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obPopUpCGM = new IPopUpCGM( $obForm );
$obPopUpCGM->setNull  ( true                        );
$obPopUpCGM->setRotulo( "CGM"                       );
$obPopUpCGM->setTitle ( "Código do CGM do Fiscal." );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm          ( $obForm                                  );
$obFormulario->addHidden        ( $obHdnAcao                               );
$obFormulario->addHidden        ( $obHdnCtrl                               );
$obFormulario->addTitulo        ( "Dados para Filtro"                      );
$obFormulario->addComponente    ( $obPopUpCGM                              );
$obFormulario->agrupaComponentes( array($obRadioAtivoNao,$obRadioAtivoSim) );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

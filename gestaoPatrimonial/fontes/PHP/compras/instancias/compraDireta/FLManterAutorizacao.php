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
 * Filtro para Compra Direta
 * Data de Criação   : 29/01/2007

 * @author Analista: Gelson
 * @author Desenvolvedor: Lucas Teixeira Stephanou

 * @ignore

 * Casos de uso : uc-03.04.33

 $Id: FLManterAutorizacao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

require_once CAM_GP_COM_COMPONENTES."ISelectModalidade.class.php";
require_once CAM_GP_COM_COMPONENTES."ISelectTipoObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpEditObjeto.class.php";
require_once CAM_GP_COM_COMPONENTES."IPopUpMapaCompras.class.php";
require_once CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php";

# Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$obForm = new Form;
$obForm->setAction( $pgList );

$obForm->setTarget( "telaPrincipal" );

//Define o Hidden de ação (padrão no framework)
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o Hidde de controle (padrão no framework)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
$obEntidadeUsuario->setNull( true );

$obISelectModalidade = new Select();
$obISelectModalidade->setRotulo  ("Modalidade"                      );
$obISelectModalidade->setTitle   ("Selecione a modalidade."         );
$obISelectModalidade->setName    ("inCodModalidade"                 );
$obISelectModalidade->setCampoID ("cod_modalidade"                  );
$obISelectModalidade->addOption  ("","Selecione"                    );
$obISelectModalidade->addOption  ("8","8 - Dispensa de Licitação"   );
$obISelectModalidade->addOption  ("9","9 - Inexibilidade"           );

$obTxtCompraDireta = new TextBox();
$obTxtCompraDireta->setName      ( "inCompraDireta"          );
$obTxtCompraDireta->setId        ( "inCompraDireta"          );
$obTxtCompraDireta->setRotulo    ( "Código Compra Direta"       );
$obTxtCompraDireta->setTitle     ( "Informe o código da Compra Direta.");
$obTxtCompraDireta->setInteiro   ( true                         );

$obPeriodicidade = new Periodicidade();
$obPeriodicidade->setExercicio      ( Sessao::getExercicio());
$obPeriodicidade->setValue          ( 4                 );
$obPeriodicidade->setValidaExercicio( true              );
$obPeriodicidade->obDataInicial->setName    ( "stDtInicial" );
$obPeriodicidade->obDataFinal->setName      ( "stDtFinal" );

$obMapa = new IPopUpMapaCompras( $obForm );
$obMapa->setRotulo ( 'Mapa'                      );
$obMapa->setTitle  ( 'Código do mapa de compras.' );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo ( "Selecionar Compra Direta"   );
$obFormulario->addComponente ( $obEntidadeUsuario       );
$obFormulario->addComponente ( $obISelectModalidade     );
$obFormulario->addComponente ( $obTxtCompraDireta       );
$obFormulario->addComponente ( $obPeriodicidade         );
$obFormulario->addComponente ( $obMapa                  );

$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

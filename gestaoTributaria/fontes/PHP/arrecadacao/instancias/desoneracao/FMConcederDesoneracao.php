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
  * Página de Formulário para Conceder Desoneração
  * Data de criação : 03/06/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @ignore

    * $Id: FMConcederDesoneracao.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.04
**/

/*
$Log$
Revision 1.6  2006/09/15 11:50:40  fabio
corrigidas tags de caso de uso

Revision 1.5  2006/09/15 11:04:52  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once (CAM_GT_ARR_NEGOCIO."RARRDesoneracao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDesoneracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FMConcederDesoneracao.php";
$pgFormVinculo = "FMConcederDesoneracaoVinculo.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

$obRARRDesoneracao = new RARRDesoneracao;
$stAcao = $_REQUEST[ "stAcao" ];
$stCtrl = $_REQUEST[ "stCtrl" ];
//DEFINIÇÃO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( 'stAcao' );
$obHdnAcao->setValue( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl  );

$obRdoConcessaoContribuinte = new Radio;
$obRdoConcessaoContribuinte->setTitle   ( "Forma para apurar a diferença de pagamento." );
$obRdoConcessaoContribuinte->setName    ( "boConcessao"   );
$obRdoConcessaoContribuinte->setRotulo  ( "*Concessão"       );
$obRdoConcessaoContribuinte->setLabel   ( "por Contribuinte" );
$obRdoConcessaoContribuinte->setValue   ( "contribuinte"              );
$obRdoConcessaoContribuinte->setChecked ( true               );

$obRdoConcessaoGrupo = new Radio;
$obRdoConcessaoGrupo->setName    ( "boConcessao"    );
$obRdoConcessaoGrupo->setRotulo  ( "*Concessão"     );
$obRdoConcessaoGrupo->setLabel   ( "por Grupo"      );
$obRdoConcessaoGrupo->setValue   ( "grupo"          );

$obBtnOk = new Ok;
$obBtnLimpar = new Limpar;

//DEFINICAO DO FORMULARIO
$obForm = new Form;
$obForm->setAction            ( $pgFormVinculo  );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm      );
$obFormulario->addHidden      ( $obHdnAcao   );
$obFormulario->addHidden      ( $obHdnCtrl   );
$obFormulario->addTitulo      ( "Dados para Desoneração" );
$obFormulario->agrupaComponentes( array( $obRdoConcessaoContribuinte, $obRdoConcessaoGrupo ) );
$obFormulario->Ok();
$obFormulario->Show();

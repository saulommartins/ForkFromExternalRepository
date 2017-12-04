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
/*
    * Filtro do Popup para Natureza JuridicaR
    * Data de Criação   : 20/04/2005
    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo
    *
    * @ignore

    * $Id: FLProcurarNaturezaJuridica.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08
*/

/*
$Log$
Revision 1.8  2006/09/15 13:50:45  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php" );

//Define valores para sessao
/*session_regenerate_id();
Sessao::getId() = "PHPSESSID=".session_id();
$sessao->geraURLRandomica();
Sessao::read('acao')   = "139";
Sessao::read('modulo') = "14";*/

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarNaturezaJuridica";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$obRCEMNaturezaJuridica = new RCEMNaturezaJuridica;

$stAcao = $request->get('stAcao');

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

// Definição dos objetos para o formuário
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

$obTxtCodigoNatureza = new TextBox;
$obTxtCodigoNatureza->setRotulo         ( "Código"           );
$obTxtCodigoNatureza->setName           ( "inCodigoNatureza" );
$obTxtCodigoNatureza->setValue          ( $inCodigoNatureza  );
$obTxtCodigoNatureza->setMascara        ( "999-9"            );
$obTxtCodigoNatureza->setSize           ( 10                 );
$obTxtCodigoNatureza->setMaxLength      ( 10                 );

$obTxtNomeNatureza = new TextBox;
$obTxtNomeNatureza->setRotulo         ( "Nome"             );
$obTxtNomeNatureza->setName           ( "stNomeNatureza"   );
$obTxtNomeNatureza->setValue          ( $stNomeNatureza    );
$obTxtNomeNatureza->setSize           ( 80                 );
$obTxtNomeNatureza->setMaxLength      ( 80                 );

$obForm = new Form;
$obForm->setAction                  ( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm );
$obFormulario->addHidden      ( $obHdnCtrl );
$obFormulario->addHidden      ( $obHdnAcao );
$obFormulario->addHidden      ( $obHdnForm );
$obFormulario->addHidden      ( $obHdnCampoNum );
$obFormulario->addHidden      ( $obHdnCampoNom );
$obFormulario->addTitulo      ( "Dados para filtro"  );
$obFormulario->addComponente  ( $obTxtCodigoNatureza );
$obFormulario->addComponente  ( $obTxtNomeNatureza   );
$obFormulario->Ok();
$obFormulario->show();

?>

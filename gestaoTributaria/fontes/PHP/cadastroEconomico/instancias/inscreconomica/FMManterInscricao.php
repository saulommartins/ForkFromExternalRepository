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
    * Página de Formulario de Inclusao/Alteracao de Inscrição Econômica
    * Data de Criação   : 22/12/2004

    * @author  Tonismar Régis Bernardo

    * @ignore

    * $Id: FMManterInscricao.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.7  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterInscricao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgFormNivel = "FM".$stPrograma."Nivel.php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( $stCtrl );

// CODIGO 1 -> EMPRESA DE FATO
// CODIGO 2 -> EMPRESA DE DIREITO
// CODIGO 3 -> AUTÔNOMO
/*
$obTxtCodigoEnquadramento = new TextBox;
$obTxtCodigoEnquadramento->setName               ( "inCodigoEnquadramento"   );
$obTxtCodigoEnquadramento->setRotulo             ( "Enquadramento"           );
$obTxtCodigoEnquadramento->setMaxLength          ( 7                         );
$obTxtCodigoEnquadramento->setSize               ( 7                         );
$obTxtCodigoEnquadramento->setValue              ( $inCodigoEnquadramento    );
$obTxtCodigoEnquadramento->obEvento->setOnChange ("preencheEnquadramento();" );
$obTxtCodigoEnquadramento->setInteiro            ( true                      );

$rsEnquadramento = new RecordSet;
$obRCEMInscricaoEconomica->listarCadastroAtributo( $rsEnquadramento );

$obCmbEnquadramento = new Select;
$obCmbEnquadramento->setName               ( "stEnquadramento"         );
$obCmbEnquadramento->setRotulo             ( "Enquadramento"           );
$obCmbEnquadramento->setNull               ( false                     );
$obCmbEnquadramento->setCampoId            ( "cod_cadastro"            );
$obCmbEnquadramento->setCampoDesc          ( "nom_cadastro"            );
$obCmbEnquadramento->addOption             ( "", "Selecione"           );
$obCmbEnquadramento->preencheCombo         ( $rsEnquadramento          );
$obCmbEnquadramento->obEvento->setOnChange ( "preencheCodigoEnquadramento();" );
*/

$obREmpresaDeFato = new Radio;
$obREmpresaDeFato->setName    ( "inCodigoEnquadramento"   );
$obREmpresaDeFato->setTitle   ( "Enquadramento" );
$obREmpresaDeFato->setRotulo  ( "Enquadramento"               );
$obREmpresaDeFato->setValue   ( 1             );
$obREmpresaDeFato->setLabel   ( "Empresa de Fato"      );
$obREmpresaDeFato->setNull    ( false                   );
$obREmpresaDeFato->setChecked ( true );

$obREmpresaDeDireito = new Radio;
$obREmpresaDeDireito->setName    ( "inCodigoEnquadramento"   );
$obREmpresaDeDireito->setValue   ( 2             );
$obREmpresaDeDireito->setLabel   ( "Empresa de Direito "   );
$obREmpresaDeDireito->setNull    ( false                   );

$obRAutonomo = new Radio;
$obRAutonomo->setName    ( "inCodigoEnquadramento"   );
$obRAutonomo->setValue   ( 3             );
$obRAutonomo->setLabel   ( "Autônomo"   );
$obRAutonomo->setNull    ( false                   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgFormNivel );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm               ( $obForm );
$obFormulario->setAjuda      ( "UC-05.02.10");
$obFormulario->addTitulo             ( "Dados para Inscrição Econômica" );
$obFormulario->addHidden             ( $obHdnAcao );
$obFormulario->addHidden             ( $obHdnCtrl );
//$obFormulario->addComponenteComposto ( $obTxtCodigoEnquadramento, $obCmbEnquadramento );
$obFormulario->agrupaComponentes  ( array ($obREmpresaDeFato, $obREmpresaDeDireito, $obRAutonomo) );
$obFormulario->ok();
$obFormulario->show();

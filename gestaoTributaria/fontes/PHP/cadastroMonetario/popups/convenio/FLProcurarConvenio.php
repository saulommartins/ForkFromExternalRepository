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
  * Página de Popup de Convênio
  * Data de criação : 08/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

    * $Id: FLProcurarConvenio.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.05.04
**/

/*
$Log$
Revision 1.8  2006/09/18 08:47:14  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once(CAM_GT_MON_NEGOCIO."RMONBanco.class.php");
include_once(CAM_GT_MON_NEGOCIO."RMONConvenio.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarConvenio";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once($pgJs);

$obRMONConvenio = new RMONConvenio;

$obIFrame = new IFrame;
$obIFrame->setName  ("oculto"   );
$obIFrame->setWidth ("100%"     );
$obIFrame->setHeight("0"      );

$obIFrame2 = new IFrame;
$obIFrame2->setName   ( "telaMensagem" );
$obIFrame2->setWidth  ( "100%"         );
$obIFrame2->setHeight ( "50"           );

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto HIDDEN para armazenar variavel de controle (stCtrl)
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

$obRMONBanco = new RMONBanco;
$obRMONBanco->listarBanco( $rsBanco );
$obRMONConvenio->listarTipoConvenio( $rsTipo );

$obTxtCodBanco = new TextBox;
$obTxtCodBanco->setRotulo    ( "Banco" );
$obTxtCodBanco->setName      ( "inNumBanco"   );
$obTxtCodBanco->setValue     ( $inNumBanco    );
$obTxtCodBanco->setSize      ( 8                  );
$obTxtCodBanco->setMaxLength ( 8                  );
$obTxtCodBanco->setNull      ( false              );
$obTxtCodBanco->setInteiro   ( true               );

$obCmbBanco = new Select;
$obCmbBanco->setName       ( "cmbBanco"      );
$obCmbBanco->addOption     ( "", "Selecione" );
$obCmbBanco->setCampoId    ( "num_banco"     );
$obCmbBanco->setCampoDesc  ( "nom_banco"     );
$obCmbBanco->setValue      ( $inNumBanco     );
$obCmbBanco->preencheCombo ( $rsBanco        );
$obCmbBanco->setNull       ( false           );
$obCmbBanco->setStyle      ( "width: 220px"  );

$obTxtCodTipo = new TextBox;
$obTxtCodTipo->setRotulo    ( "Tipo de Convênio" );
$obTxtCodTipo->setName      ( "inCodTipo"        );
$obTxtCodTipo->setValue     ( $inCodTipo         );
$obTxtCodTipo->setSize      ( 8                  );
$obTxtCodTipo->setMaxLength ( 8                  );
$obTxtCodTipo->setNull      ( false              );
$obTxtCodTipo->setInteiro   ( true               );

$obCmbTipo = new Select;
$obCmbTipo->setName       ( "cmbTipo"       );
$obCmbTipo->addOption     ( "", "Selecione" );
$obCmbTipo->setCampoId    ( "cod_tipo"      );
$obCmbTipo->setCampoDesc  ( "nom_tipo"      );
$obCmbTipo->setValue      ( $inCodTipo      );
$obCmbTipo->preencheCombo ( $rsTipo         );
$obCmbTipo->setNull       ( false           );
$obCmbTipo->setStyle      ( "width: 220px"  );

$obTxtConvenio = new TextBox;
$obTxtConvenio->setRotulo    ( "Convênio" );
$obTxtConvenio->setName      ( "inCodConvenio"    );
$obTxtConvenio->setValue     ( $inCodConvenio     );
$obTxtConvenio->setSize      ( 8                  );
$obTxtConvenio->setMaxLength ( 8                  );
$obTxtConvenio->setInteiro   ( true               );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden             ( $obHdnCtrl          );
$obFormulario->addHidden             ( $obHdnAcao          );
$obFormulario->addHidden             ( $obHdnCampoNom      );
$obFormulario->addHidden             ( $obHdnCampoNum      );
$obFormulario->addTitulo             ( "Dados para Filtro" );
$obFormulario->addComponenteComposto ( $obTxtCodBanco, $obCmbBanco );
$obFormulario->addComponenteComposto ( $obTxtCodTipo,  $obCmbTipo  );
$obFormulario->addComponente         ( $obTxtConvenio      );

$obFormulario->OK();
$obFormulario->show();

$obIFrame->show();
$obIFrame2->show();

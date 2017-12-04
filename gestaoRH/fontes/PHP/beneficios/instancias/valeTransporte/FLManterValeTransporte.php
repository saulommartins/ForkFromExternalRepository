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
* Página de filtro do vale transporte
* Data de Criação: 11/07/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30922 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.06.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_BEN_NEGOCIO."RBeneficioValeTransporte.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterValeTransporte";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( $pgJS );

Sessao::write('link', '');

$stAcao = $request->get('stAcao');
$inCodEmpresaVT = $_REQUEST['inCodEmpresaVT'] ? $_REQUEST['inCodEmpresaVT'] : 0;

$obRBeneficioValeTransporte  = new RBeneficioValeTransporte;

//Instancia o formulário
$obForm = new Form;
$obForm->setAction   ( $pgList );
//$obForm->setTarget  ( "telaPrincipal" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );
$obHdnCtrl->setValue ( ""       );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );

$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obTxtCodEmpresaVT = new TextBox;
$obTxtCodEmpresaVT->setRotulo      ( "Fornecedor"                           );
$obTxtCodEmpresaVT->setName        ( "inCodEmpresaVT"                       );
$obTxtCodEmpresaVT->setValue       ( $inCodEmpresaVT                        );
$obTxtCodEmpresaVT->setTitle       ( "Selecione a fornecedora para filtro"  );
$obTxtCodEmpresaVT->setSize        ( 7                                      );
$obTxtCodEmpresaVT->setMaxLength   ( 7                                      );
$obTxtCodEmpresaVT->setInteiro     ( true                                   );
$obTxtCodEmpresaVT->setNull        ( false                                  );

$obRBeneficioValeTransporte->obRBeneficioFornecedorValeTransporte->listarFornecedorValeTransporteRelatorio( $rsEmpresaVT );
$obCmbNomeEmpresaVT = new Select;
$obCmbNomeEmpresaVT->setName       ( "stNomeEmpresaVT"                      );
$obCmbNomeEmpresaVT->setValue      ( $inCodEmpresaVT                        );
$obCmbNomeEmpresaVT->setTitle      ( "Selecione a fornecedora para filtro"  );
$obCmbNomeEmpresaVT->setNull       ( false                                  );
$obCmbNomeEmpresaVT->setCampoId    ( "cgm_fornecedor"                       );
$obCmbNomeEmpresaVT->setCampoDesc  ( "nom_cgm"                              );
$obCmbNomeEmpresaVT->addOption     ( "", "Selecione" 		                );
$obCmbNomeEmpresaVT->addOption     ( "0", "Todas", "selected"               );
$obCmbNomeEmpresaVT->preencheCombo ( $rsEmpresaVT                           );
$obCmbNomeEmpresaVT->setStyle      ( "width: 200px"                         );

$obFormulario->addTitulo( "Filtro para seleção" );
$obFormulario->addComponenteComposto( $obTxtCodEmpresaVT, $obCmbNomeEmpresaVT);

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

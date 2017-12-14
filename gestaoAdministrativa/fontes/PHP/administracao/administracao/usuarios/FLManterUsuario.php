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
* Manutneção de usuários
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 27773 $
$Name$
$Author: luiz $
$Date: 2008-01-28 08:38:57 -0200 (Seg, 28 Jan 2008) $

Casos de uso: uc-01.03.93
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_FW_LEGADO."funcoesLegado.lib.php");
setAjuda( "UC-01.03.93" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterUsuario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_REQUEST['stAcao'] ? $_REQUEST['stAcao'] : 'incluir';

$stAcao = $stAcao == 'incluir' ? 'usuario' : $stAcao;

$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( 'stAcao'  );
$obHdnAcao->setValue ( $stAcao );

$obTxtCGM = new TextBox;
$obTxtCGM->setName     ( 'inNumCGM' );
$obTxtCGM->setRotulo   ( 'CGM' );
$obTxtCGM->setMaxLength( 10   );
$obTxtCGM->setSize     ( 10   );
$obTxtCGM->setInteiro  ( true );

$obTxtNome = new TextBox;
$obTxtNome->setName      ( 'stNomCGM' );
$obTxtNome->setRotulo    ( 'Nome'   );
$obTxtNome->setMaxLength ( 200 );
$obTxtNome->setSize      ( 50 );

$obTxtUserName =  new TextBox;
$obTxtUserName->setName      ( 'stUserName' );
$obTxtUserName->setRotulo    ( 'Username'   );
$obTxtUserName->setSize      ( 15           );
$obTxtUserName->setMaxLength ( 15           );

$obTxtCNPJ = new CNPJ;
$obTxtCNPJ->setRotulo( 'CNPJ' );

$obTxtCPF = new CPF;
$obTxtCPF->setRotulo( 'CPF' );

$obTxtRG =  new Inteiro;
$obTxtRG->setName      ( 'inRG' );
$obTxtRG->setRotulo    ( 'RG' );
$obTxtRG->setMaxLength ( 15 );
$obTxtRG->setSize      ( 15 );

$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm    );
$obFormulario->addTitulo     ( 'Dados para o Filtro' );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtCGM  );
$obFormulario->addComponente ( $obTxtNome );
if ($stAcao != 'incluir') {
 $obFormulario->addComponente ( $obTxtUserName );
}
$obFormulario->addComponente ( $obTxtCNPJ );
$obFormulario->addComponente ( $obTxtCPF  );
$obFormulario->addComponente ( $obTxtRG   );
$obFormulario->Ok();
$obFormulario->show();
?>

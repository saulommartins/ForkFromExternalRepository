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
    *
    * Página de Filtro de Gestao Fiscal Medidas
    * Data de Criação: 29/07/2013

    * @author Analista:
    * @author Desenvolvedor: Carolina Schwaab Marcal

    * @ignore

    * Casos de uso:

    $Id:

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEMG_MAPEAMENTO."TTCEMGMedidas.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoPoderPublico.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "GestaoFiscalMedidas";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( $stCtrl );

$obForm = new Form;
$obForm->setAction( $pgForm  );
$obForm->setTarget( "telaPrincipal" );

$obPoderPublico = new TAdministracaoPoderPublico();
$rsPoderPublico = new RecordSet();
$stFiltro= '';
$obPoderPublico->recuperaTodos($rsPoderPublico,$stFiltro);

$obTipoPoder = new Select();
$obTipoPoder->setRotulo         ( "Tipo Poder"                 );
$obTipoPoder->setName          ('inTipoPoder');
$obTipoPoder->addOption        ('','Selecione');
$obTipoPoder->setNull      (false      );
$obTipoPoder->setCampoId      ( 'cod_poder'      );
$obTipoPoder->setCampoDesc ( 'nome'      );
$obTipoPoder->setTitle   ('Selecione o tipo de poder');
$obTipoPoder->preencheCombo($rsPoderPublico);

$obMes = new Mes();
$obMes->setId                 ('inMes');
$obMes->setNull      (false      );

$obFormulario = new Formulario;
$obFormulario->addForm      ( $obForm );
$obFormulario->addTitulo    ( "Dados para filtro" );
$obFormulario->addComponente   ($obTipoPoder );
$obFormulario->addComponente   ($obMes           );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

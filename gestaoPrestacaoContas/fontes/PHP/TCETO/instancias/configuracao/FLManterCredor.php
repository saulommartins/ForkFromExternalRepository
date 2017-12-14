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
    * Pacote de configuração do TCETO - Filtro Configurar Credor
    * Data de Criação   : 06/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: FLManterCredor.php 60660 2014-11-06 16:28:53Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCETO_NEGOCIO.'RExportacaoTCETOArqCredor.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

$obRegra = new RExportacaoTCETOArqCredor();
$obRegra->obRExportacaoTCETOCredor->listarExercicios( $rsRecordSet ) ;

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];
if ( empty( $stAcao ) )
    $stAcao = "incluir";

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

$obCmbPeriodo = new Select;
$obCmbPeriodo->setRotulo            ( "*Exercício"  );
$obCmbPeriodo->setName              ( "stPeriodo"   );
$obCmbPeriodo->setValue             ( ""            );
$obCmbPeriodo->addOption            ( "t", " Todos ");
$obCmbPeriodo->addOption            ( "a", " Ano "  );
$obCmbPeriodo->obEvento->setOnChange( "addCmb();"   );

$obCmbAno = new Select;
$obCmbAno->setName      ( "stAno"           );
$obCmbAno->setID        ( "stAno"           );
$obCmbAno->setValue     ( ""                );
$obCmbAno->setCampoId   ( "exercicio"       );
$obCmbAno->addOption    ( "", "Selecione"   );
$obCmbAno->setCampoDesc ( "exercicio"       );
$obCmbAno->setStyle     ( "display: none"   );
$obCmbAno->preencheCombo( $rsRecordSet );

$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addTitulo( "Dados para filtro" );
$obFormulario->agrupaComponentes( array( $obCmbPeriodo,$obCmbAno ));
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

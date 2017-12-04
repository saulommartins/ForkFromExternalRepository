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
* Arquivo instância para popup de Fornecedor
* Data de Criação: 12/09/2006

* @author Analista: Diego Barbosa Victoria
* @author Desenvolvedor: Diego Barbosa Victoria

$Revision: 19121 $
$Name$
$Author: bruce $
$Date: 2007-01-05 09:21:55 -0200 (Sex, 05 Jan 2007) $

Casos de uso: uc-03.04.03

*/

/*
$Log$
Revision 1.4  2007/01/05 11:21:13  bruce
Bug #7898#
Bug #7806#

Revision 1.3  2006/11/09 19:13:32  gelson
Correção do caso de uso.

Revision 1.2  2006/09/14 09:11:41  cleisson
Criação do componente fornecedor

Revision 1.1  2006/09/14 09:05:11  cleisson
Criação do componente fornecedor
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarFornecedor";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include( $pgJS );
$stAcao = $request->get('stAcao');
$nomForm = $_REQUEST['nomForm'];
$campoNom = $_REQUEST['campoNom'];
$campoNum = $_REQUEST['campoNum'];

//destroi arrays de sessao que armazenam os dados do FILTRO
Sessao::remove('filtro');
Sessao::remove('link');

//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden;
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $nomForm );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoConsulta" );
$obHdnTipoBusca->setValue( $_GET['stTipoConsulta'] );

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

//Definição das Caixas de Texto
$obTxtNomeCgm = new TextBox;
$obTxtNomeCgm->setTitle( "Informe o nome desejado" );
$obTxtNomeCgm->setName( "stNomeCgm" );
$obTxtNomeCgm->setRotulo( "Nome" );
$obTxtNomeCgm->setSize( 60 );
$obTxtNomeCgm->setMaxLength( 60 );

//Componente que define o tipo de busca
$obTipoBuscaNomCgm = new TipoBusca( $obTxtNomeCgm );

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnTipoBusca );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addTitulo( "Dados do Fornecedor" );
$obFormulario->addComponente( $obTipoBuscaNomCgm );
$obFormulario->OK();
$obFormulario->show();

$obIFrame = new IFrame;
$obIFrame->setName("telaMensagem");
$obIFrame->setWidth("100%");
$obIFrame->setHeight("10%");
$obIFrame->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

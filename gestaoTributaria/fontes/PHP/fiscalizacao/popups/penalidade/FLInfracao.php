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
 * Arquivo instância para popup de Infracao
 * Data de Criação: 11/08/2008

 * @author Analista      : Heleno Menezes da Silva
 * @author Desenvolvedor : Fellipe Esteves dos Santos

 * @ignore

 * Casos de uso:
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GT_FIS_COMPONENTES . 'ITextBoxSelectTipoFiscalizacao.class.php' );

# Define o nome dos arquivos PHP
$stPrograma = "Infracao";

$pgList = "LS" . $stPrograma . ".php";

# Destroi arrays de sessao que armazenam os dados do FILTRO
unset( $sessao->filtro );
unset( $sessao->link );

# Instancia o formulário
$obForm = new Form();
$obForm->setAction( $pgList );

$obHdnCtrl = new Hidden();
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnForm = new Hidden();
$obHdnForm->setName( "nomForm" );
$obHdnForm->setValue( $_REQUEST[ 'nomForm' ] );

$obHdnAcao = new Hidden();
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $_GET['stAcao'] );

$obHdnCampoNum = new Hidden();
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $_REQUEST[ 'campoNum' ] );

$obHdnCampoNom = new Hidden();
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $_REQUEST[ 'campoNom' ] );

# Criação do formulário
$obFormulario = new Formulario();
$obFormulario->addForm( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addTitulo( "Dados do Filtro para Infração" );

# Define tipo de fiscalização
$obTipoFiscalizacao = new ITextBoxSelectTipoFiscalizacao();
$obTipoFiscalizacao->setTitle( "Tipo de fiscalização que poderá utilizar a infração." );
$obTipoFiscalizacao->setNull( false );
$obTipoFiscalizacao->geraFormulario( $obFormulario );

# Define descrição da infração
$obDescricao = new TextBox();
$obDescricao->setName( "stNomInfracao" );
$obDescricao->setRotulo( "Descrição" );
$obDescricao->setTitle( "Descrição da Infração." );
$obDescricao->setSize( 50 );
$obDescricao->setMaxLength( 80 );
$obFormulario->addComponente( $obDescricao );

$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

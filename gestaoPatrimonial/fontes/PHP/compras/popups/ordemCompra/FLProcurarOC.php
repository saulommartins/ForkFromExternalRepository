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
* Arquivo instância para popup de CGM
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Thiago La Delfa  Cabelleira

$Revision: 18799 $
$Name$
$Author: thiago $
$Date: 2006-12-15 12:57:52 -0200 (Sex, 15 Dez 2006) $

Casos de uso: uc-01.02.92
*/

session_regenerate_id();
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GP_COM_MAPEAMENTO."TComprasOrdemCompraNota.class.php");
require_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeUsuario.class.php");

Sessao::geraURLRandomica();

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarOC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";
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

$obHdnCampoNum = new Hidden;
$obHdnCampoNum->setName( "campoNum" );
$obHdnCampoNum->setValue( $campoNum );

//Define HIDDEN com o o nome do campo texto
$obHdnCampoNom = new Hidden;
$obHdnCampoNom->setName( "campoNom" );
$obHdnCampoNom->setValue( $campoNom );

//Define HIDDEN com o o nome do campo texto(empenho)
$obHdnCampoEmp = new Hidden;
$obHdnCampoEmp->setName( "campoEmp" );
$obHdnCampoEmp->setValue( $campoEmp );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

//Definição das Caixas de Texto
$obFiltroExercicio = new TextBox;
$obFiltroExercicio->setTitle( "Informe o exercício desejado." );
$obFiltroExercicio->setName( "filtroExercicio" );
$obFiltroExercicio->setRotulo( "Exercício" );
$obFiltroExercicio->setValue(Sessao::getExercicio());
$obFiltroExercicio->setSize( 5 );
$obFiltroExercicio->setMaxLength( 4 );

$obTxtEntidade = new ITextBoxSelectEntidadeUsuario;
$obTxtEntidade->setRotulo('Código da Entidade');
$obTxtEntidade->setTitle('Selecione a entidade.');
$obTxtEntidade->setNull(true);

$obTxOrdem = new TextBox();
$obTxOrdem->setName('inCodOrdem');
$obTxOrdem->setTitle('Informe o código da ordem.').
$obTxOrdem->setRotulo('Código da ordem');

//Criação do formulário
$obFormulario = new Formulario;
$obFormulario->addForm  ( $obForm );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnForm );
$obFormulario->addHidden( $obHdnCampoNum );
$obFormulario->addHidden( $obHdnCampoNom );
$obFormulario->addHidden( $obHdnCampoEmp );
$obFormulario->addHidden( $obHdnTipoBusca);
$obFormulario->addTitulo( "Ordens de Compra" );
$obFormulario->addComponente( $obTxtEntidade);
$obFormulario->addComponente( $obTxOrdem);
$obFormulario->addComponente( $obFiltroExercicio );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

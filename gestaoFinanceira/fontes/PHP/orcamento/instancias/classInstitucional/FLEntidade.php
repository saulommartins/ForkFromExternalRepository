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
    * Página de Formulario de Inclusao/Alteracao de Entidade
    * Data de Criação   : 15/07/2004

    * @author Desenvolvedor: Roberto Pawelski Rodrigues

    * @ignore

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2007-05-21 16:04:19 -0300 (Seg, 21 Mai 2007) $

    * Casos de uso: uc-02.01.02
*/

/*
$Log$
Revision 1.8  2007/05/21 19:04:19  melo
Bug #9229#

Revision 1.7  2007/05/21 18:54:31  luciano
#8856#

Revision 1.6  2006/07/14 17:18:42  leandro.zis
Bug #6179#

Revision 1.5  2006/07/05 20:42:39  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "Entidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ($pgJS);

$obREntidadeOrcamento  = new ROrcamentoEntidade;

Sessao::remove('filtro');
Sessao::remove('pg');
Sessao::remove('pos');
Sessao::remove('paginando');
//sessao->transf4 = array( 'filtro' => array(), 'pg' => '' , 'pos' => '', 'paginando' => false );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "alterar";
}

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgList );
$obForm->setTarget( "telaPrincipal" );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Define o objeto TEXT para armazenar o CÓDIGO DO CGM
$obTxtCodEntidade = new TextBox;
$obTxtCodEntidade->setName     ( "inCodigoEntidade" );
$obTxtCodEntidade->setValue    ( $inCodigoEntidade  );
$obTxtCodEntidade->setRotulo   ( "Código"   );
$obTxtCodEntidade->setTitle    ( "Informe o código."   );
$obTxtCodEntidade->setSize     ( 11 );
$obTxtCodEntidade->setMaxLength( 10 );
$obTxtCodEntidade->setNull     ( true );
$obTxtCodEntidade->setInteiro  ( true );

//Define o objeto TEXT para armazenar o CÓDIGO DO CGM
$obTxtNomEntidade = new TextBox;
$obTxtNomEntidade->setName     ( "stNomEntidade" );
$obTxtNomEntidade->setValue    ( $stNomEntidade  );
$obTxtNomEntidade->setRotulo   ( "Nome" );
$obTxtNomEntidade->setTitle    ( "Informe o nome." );
$obTxtNomEntidade->setSize     ( 80 );
$obTxtNomEntidade->setMaxLength( 80 );
$obTxtNomEntidade->setNull     ( true );

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->setAjuda ( "UC-02.01.02"           );
$obFormulario->addForm( $obForm );

$obFormulario->addHidden        ( $obHdnCtrl          );
$obFormulario->addHidden        ( $obHdnAcao          );
$obFormulario->addTitulo        ( "Dados para Filtro" );
$obFormulario->addComponente    ( $obTxtCodEntidade   );
$obFormulario->addComponente    ( $obTxtNomEntidade   );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Página de Filtro Histórico de Empenho
    * Data de Criação   : 01/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Gelson W. Gonçalves

    * @ignore

    $Revision: 30805 $
    $Name$
    $Author: leandro.zis $
    $Date: 2006-07-14 17:59:57 -0300 (Sex, 14 Jul 2006) $

    * Casos de uso: uc-02.03.01
*/

/*
$Log$
Revision 1.6  2006/07/14 20:59:57  leandro.zis
Bug #6181#

Revision 1.5  2006/07/05 20:47:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once( CAM_GF_INCLUDE."validaGF.inc.php");
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GF_EMP_NEGOCIO."REmpenhoHistorico.class.php");

$stPrograma = "ManterHistorico";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//***********************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/

Sessao::remove('filtro');
Sessao::remove('link');
Sessao::write('filtro', array());
Sessao::write('pg', '');
Sessao::write('pos', '');
Sessao::write('paginando', false);

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtCodHistorico = new TextBox;
$obTxtCodHistorico->setRotulo        ( "Código" );
$obTxtCodHistorico->setName          ( "inCodHistorico" );
$obTxtCodHistorico->setTitle         ( "Informe um código" );
$obTxtCodHistorico->setValue         ( $inCodHistorico );
$obTxtCodHistorico->setSize          ( 11 );
$obTxtCodHistorico->setMaxLength     ( 9  );
$obTxtCodHistorico->setInteiro       ( true  );

$obTxtNomHistorico = new TextBox;
$obTxtNomHistorico->setRotulo        ( "Descrição" );
$obTxtNomHistorico->setName          ( "stNomHistorico" );
$obTxtCodHistorico->setTitle         ( "Informe uma descrição" );
$obTxtNomHistorico->setValue         ( $stNomHistorico );
$obTxtNomHistorico->setSize          ( 80 );
$obTxtNomHistorico->setMaxLength     ( 80 );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addComponente        ( $obTxtCodHistorico  );
$obFormulario->addComponente        ( $obTxtNomHistorico  );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

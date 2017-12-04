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
* Arquivo de instância para manutenção de funções
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 28850 $
$Name$
$Author: rodrigosoares $
$Date: 2008-03-28 11:22:38 -0300 (Sex, 28 Mar 2008) $

Casos de uso: uc-01.03.95
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_NEGOCIO."RFuncao.class.php");
require_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoBiblioteca.class.php";

$stPrograma = "ManterFuncao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

include_once( $pgJs );

$stAcao = $request->get('stAcao');
$stNomeFuncao = $_REQUEST["stNomeFuncao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obRBiblioteca = new RBiblioteca( new RModulo );
$obRBiblioteca->roRModulo->listarModulosPorResponsavel( $rsModulo );
Sessao::write('obRBiblioteca', $obRBiblioteca);

$obForm = new Form;
$obForm->setAction( $pgProc  );
$obForm->setTarget( "oculto" );

$obTxtNomeFuncao = new TextBox;
$obTxtNomeFuncao->setRotulo        ( "Nome" );
$obTxtNomeFuncao->setName          ( "stNomeFuncao" );
$obTxtNomeFuncao->setValue         ( $stNomeFuncao );
$obTxtNomeFuncao->setSize          ( 60 );
$obTxtNomeFuncao->setMaxLength     ( 60 );
$obTxtNomeFuncao->setNull          ( true );

$obCmbModulo = new Select();
$obCmbModulo->setRotulo            ( "Módulo" );
$obCmbModulo->setName              ( "inCodModulo" );
$obCmbModulo->setId                ( "inCodModulo" );
$obCmbModulo->setTitle             ( "Informe o módulo." );
$obCmbModulo->setStyle             ( "width: 250px" );
$obCmbModulo->setNull              ( false );
$obCmbModulo->addOption            ( "","Selecione" );
$obCmbModulo->setCampoId           ( "cod_modulo" );
$obCmbModulo->setCampoDesc         ( "[nom_modulo]" );
$obCmbModulo->preencheCombo        ( $rsModulo );
$obCmbModulo->obEvento->setOnChange( "BuscaValores('buscaCadastro');limpaCampoBiblioteca();" );

$obCmbBiblioteca = new Select();
$obCmbBiblioteca->setRotulo   ( "Biblioteca" );
$obCmbBiblioteca->setId       ( "inCodBiblioteca" );
$obCmbBiblioteca->setName     ( "inCodBiblioteca" );
$obCmbBiblioteca->setTitle    ( "Informe a biblioteca." );
$obCmbBiblioteca->setStyle    ( "width: 250px" );
$obCmbBiblioteca->setNull     ( false );
$obCmbBiblioteca->addOption   ( "","Selecione" );
$obCmbBiblioteca->setCampoId  ( "cod_biblioteca" );
$obCmbBiblioteca->setCampoDesc( "[cod_biblioteca] - [nom_biblioteca]" );

$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addComponente        ( $obTxtNomeFuncao );
$obFormulario->addComponente        ( $obCmbModulo     );
$obFormulario->addComponente        ( $obCmbBiblioteca );

$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

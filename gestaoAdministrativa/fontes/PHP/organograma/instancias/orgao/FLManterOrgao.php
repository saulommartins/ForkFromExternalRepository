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
 * Arquivo de instância para manutenção de orgao
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 Casos de uso: uc-01.05.02

 $Id: FLManterOrgao.php 59612 2014-09-02 12:00:51Z gelson $

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ORGAN_NEGOCIO."ROrganogramaOrgao.class.php";

$stPrograma = "ManterOrgao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

# Remove a sessao Link
Sessao::remove('link');

$rsOrganograma = new RecordSet;
$obRegra       = new ROrganogramaOrgao;

$stOrdem = " ORDER BY TO_DATE(implantacao::text, 'yyyy-mm-dd') DESC ";

$obRegra->obROrganograma->listar ($rsOrganograma, $stFiltro, $stOrdem);

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbOrganograma = new Select;
$obCmbOrganograma->setRotulo        ( "Organograma" );
$obCmbOrganograma->setName          ( "inCodOrganograma" );
$obCmbOrganograma->setStyle         ( "width: 200px");
$obCmbOrganograma->setCampoID       ( "cod_organograma" );
$obCmbOrganograma->setCampoDesc     ( "implantacao" );
$obCmbOrganograma->addOption        ( "", "Selecione" );
$obCmbOrganograma->setValue         ( $inCodOrganograma );
$obCmbOrganograma->setNull          ( false );
$obCmbOrganograma->preencheCombo    ( $rsOrganograma );

$obTxtCodigo    = new Inteiro();
$obTxtCodigo->setName       ( 'inCodigo' );
$obTxtCodigo->setRotulo     ( 'Código' );
$obTxtCodigo->setTitle      ( 'Código do órgão.');
$obTxtCodigo->setSize       (5);
$obTxtCodigo->setMaxLength  (5);

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo        ( "Descrição" );
$obTxtDescricao->setTitle         ( "Descrição do órgão.");
$obTxtDescricao->setName          ( "stDescricao" );
$obTxtDescricao->setValue         ( $stDescricao  );
$obTxtDescricao->setSize          ( 40 );
$obTxtDescricao->setMaxLength     ( 100 );

$obForm = new Form;
$obForm->setAction                  ( $pgList );
$obForm->setTarget                  ( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm );
$obFormulario->addHidden            ( $obHdnAcao );
$obFormulario->addHidden            ( $obHdnCtrl );

$obFormulario->addTitulo            ( "Dados para Filtro" );
$obFormulario->addComponente        ( $obCmbOrganograma );
$obFormulario->addComponente        ( $obTxtCodigo );
$obFormulario->addComponente        ( $obTxtDescricao );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>

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
    * Pagina de Formulario de Inclusao/Alteracao de ESPECIES

    * Data de Criação   : 08/12/2005

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterEspecie.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.09

*/

/*
$Log$
Revision 1.9  2006/09/15 14:57:53  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONEspecieCredito.class.php" );

if ( empty( $_REQUEST['stAcao'] ) ) {
    $_REQUEST['stAcao'] = "incluir";
    }

//Define o nome dos arquivos PHP
$stPrograma    = "ManterEspecie";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";
//include_once( $pgJs );

$obRMONEspecieCredito = new RMONEspecieCredito;

Sessao::remove('stLink');
Sessao::remove('link');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName   ( "stCtrl" );
$obHdnCtrl->setValue  ( $_REQUEST['stCtrl'] );

$obTxtCodEspecie = new TextBox;
$obTxtCodEspecie->setRotulo  ( 'Código');
$obTxtCodEspecie->setTitle   ( 'Código da Espécie');
$obTxtCodEspecie->setName    ( 'inCodEspecie');
$obTxtCodEspecie->setValue   ( $inCodEspecie );
$obTxtCodEspecie->setInteiro ( true );
$obTxtCodEspecie->setSize    ( 10 );
$obTxtCodEspecie->setMaxLength ( 10 );
$obTxtCodEspecie->setNull    ( true );

$obTxtDescricaoEspecie = new TextBox ;
$obTxtDescricaoEspecie->setRotulo    ( "Descrição" );
$obTxtDescricaoEspecie->setTitle     ( "Descrição da Espécie do Crédito" );
$obTxtDescricaoEspecie->setName      ( "stDescricaoEspecie");
$obTxtDescricaoEspecie->setValue     ( $stNomBanco );
$obTxtDescricaoEspecie->setSize      ( 80 );
$obTxtDescricaoEspecie->setMaxLength ( 80 );

$obTxtCodNatureza = new TextBox;
$obTxtCodNatureza->setRotulo  ( 'Natureza');
$obTxtCodNatureza->setTitle   ( 'Natureza da Espécie');
$obTxtCodNatureza->setName    ( 'inCodNatureza');
$obTxtCodNatureza->setValue   ( $inCodNatureza );
$obTxtCodNatureza->setInteiro ( true );
$obTxtCodNatureza->setSize    ( 10 );
$obTxtCodNatureza->setMaxLength ( 10 );
$obTxtCodNatureza->setNull    ( true );

$obTxtCodGenero = new TextBox;
$obTxtCodGenero->setRotulo  ( 'Gênero');
$obTxtCodGenero->setTitle   ( 'Gênero da Espécie');
$obTxtCodGenero->setName    ( 'inCodGenero');
$obTxtCodGenero->setValue   ( $inCodGenero );
$obTxtCodGenero->setInteiro ( true );
$obTxtCodGenero->setSize    ( 10 );
$obTxtCodGenero->setMaxLength ( 10 );
$obTxtCodGenero->setNull    ( true );

$obRMONEspecieCredito->ListarNatureza ( $rsNatureza );

$obCmbNatureza = new Select;
$obCmbNatureza->setName          ( "cmbNatureza"              );
$obCmbNatureza->addOption        ( "", "Selecione"            );
$obCmbNatureza->setValue         ( $_REQUEST['inCodNatureza'] );
$obCmbNatureza->setCampoId       ( "cod_natureza"             );
$obCmbNatureza->setCampoDesc     ( "nom_natureza"             );
$obCmbNatureza->preencheCombo    ( $rsNatureza               );
$obCmbNatureza->setNull          ( true                    );
$obCmbNatureza->setStyle         ( "width: 220px"           );

$obRMONEspecieCredito->ListarGenero ( $rsGenero );

$obCmbGenero = new Select;
$obCmbGenero->setName          ( "cmbGenero"             );
$obCmbGenero->addOption        ( "", "Selecione"          );
$obCmbGenero->setValue         ( $_REQUEST['inCodGenero'] );
$obCmbGenero->setCampoId       ( "cod_genero"             );
$obCmbGenero->setCampoDesc     ( "nom_genero"             );
$obCmbGenero->preencheCombo    ( $rsGenero               );
$obCmbGenero->setNull          ( true                    );
$obCmbGenero->setStyle         ( "width: 220px"           );

$obForm = new Form;
$obForm->setAction ( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm ( $obForm );
$obFormulario->setAjuda ( "UC-05.05.09" );
$obFormulario->addTitulo ('Dados para Filtro');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addComponenteComposto ( $obTxtCodNatureza , $obCmbNatureza );
$obFormulario->addComponenteComposto ( $obTxtCodGenero , $obCmbGenero );

if ($_REQUEST['stAcao'] == "excluir") {
    $obFormulario->addComponente ( $obTxtCodEspecie );
}

$obFormulario->addComponente ( $obTxtDescricaoEspecie );

$obFormulario->ok();
$obFormulario->show ();

$stJs .= 'f.inCodNatureza.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

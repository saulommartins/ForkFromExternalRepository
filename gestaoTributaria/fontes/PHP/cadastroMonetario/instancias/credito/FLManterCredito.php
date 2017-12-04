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
    * Pagina de Formulario de Inclusao/Alteracao de CREDITO

    * Data de Criacao: 22/12/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @ignore

    * $Id: FLManterCredito.php 63839 2015-10-22 18:08:07Z franver $

    *Casos de uso: uc-05.05.10

*/

/*
$Log$
Revision 1.9  2006/09/15 14:57:49  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCredito.class.php" );

$obRMONCredito =  new RMONCredito;

//Define a funcao do arquivo, ex: incluir, excluir, alterar, consultar, etc

if ( empty( $_REQUEST['stAcao']) ) {
    $_REQUEST['stAcao'] = "incluir";
}
Sessao::remove('linkCredito');
Sessao::remove('stLink');

//Define o nome dos arquivos PHP
$stPrograma    = "ManterCredito";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

/***********************************************/

include_once ( $pgJs );

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue ( $_REQUEST['stCtrl'] );

$obHdnCodAcrescimo = new Hidden;
$obHdnCodAcrescimo->setName  ('inCodAcrescimo');
$obHdnCodAcrescimo->setValue ( $_REQUEST['inCodAcrescimo'] );

$obTxtCodCredito = new TextBox;
$obTxtCodCredito->setRotulo  ( 'Código');
$obTxtCodCredito->setTitle   ( 'Código do Crédito');
$obTxtCodCredito->setName    ( 'inCodCredito');
$obTxtCodCredito->setValue   ( $inCodCredito );
$obTxtCodCredito->setInteiro ( false );
$obTxtCodCredito->setSize    ( 10 );
$obTxtCodCredito->setMaxLength ( 10 );
$obTxtCodCredito->setNull    ( true );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo  ( 'Descrição');
$obTxtDescricao->setTitle   ( 'Descrição do crédito');
$obTxtDescricao->setName    ( 'stDescricao');
$obTxtDescricao->setValue   ( $stDescricao );
$obTxtDescricao->setInteiro ( false );
$obTxtDescricao->setSize    ( 80 );
$obTxtDescricao->setMaxLength ( 80 );
$obTxtDescricao->setNull    ( true );

$obTxtCodEspecie = new TextBox;
$obTxtCodEspecie->setRotulo  ( 'Espécie');
$obTxtCodEspecie->setTitle   ( 'Espécie do Crédito');
$obTxtCodEspecie->setName    ( 'inCodEspecie');
$obTxtCodEspecie->setValue   ( $inCodEspecie);
$obTxtCodEspecie->setInteiro ( true );
$obTxtCodEspecie->setSize    ( 10 );
$obTxtCodEspecie->setMaxLength ( 10 );
$obTxtCodEspecie->setNull    ( true );
//--------------------------------------------------------------//

//------------------------------------------------- COMBOS
$obRMONCredito->ListarEspecie ( $rsEspecie );

$obCmbEspecie = new Select;
$obCmbEspecie->setRotulo       ( "Espécie"    );
$obCmbEspecie->setTitle        ( "Espécie do crédito"    );
$obCmbEspecie->setName         ( "cmbEspecie"              );
$obCmbEspecie->addOption       ( "", "Selecione"        );
$obCmbEspecie->setValue        ( $_REQUEST['inCodEspecie'] );
$obCmbEspecie->setCampoId      ( "cod_especie"             );
$obCmbEspecie->setCampoDesc    ( "nom_especie"             );
$obCmbEspecie->preencheCombo   ( $rsEspecie                );
$obCmbEspecie->setNull         ( true                  );
$obCmbEspecie->setStyle        ( "width: 220px"         );
//--------------------------------------------------------------------//

//--------------------------------
// DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgList );
//------------------------------------------------------
//MONTA FORMULARIO
$obFormulario = new Formulario;
$obFormulario->setAjuda  ( "UC-05.05.10" );
$obFormulario->addForm   ( $obForm );
$obFormulario->addTitulo ('Dados para Filtro');

$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnCtrl );

$obFormulario->addComponente ( $obTxtCodCredito );
$obFormulario->addComponenteComposto  ( $obTxtCodEspecie, $obCmbEspecie );
$obFormulario->addComponente ( $obTxtDescricao );

$obFormulario->ok();

$obFormulario->show();

$stJs .= 'f.inCodCredito.focus();';
sistemaLegado::executaFrameOculto ( $stJs );

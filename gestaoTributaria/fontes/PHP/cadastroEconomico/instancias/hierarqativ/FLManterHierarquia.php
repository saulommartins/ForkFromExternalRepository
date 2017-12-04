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
    * Página de Formulario de filtro de Nível
    * Data de Criação   : 18/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: FLManterHierarquia.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.06

*/

/*
$Log$
Revision 1.9  2006/09/15 14:32:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNivelAtividade.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterHierarquia";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ( $pgJS );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obRCEMNivelAtividade = new RCEMNivelAtividade;
$rsDataVigencia       = new RecordSet;

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( 'stCtrl' );
$obHdnCtrl->setValue( '' );

$obCmbDataVigencia = new Select;
$obRCEMNivelAtividade->listarVigencia( $rsDataVigencia );
$obCmbDataVigencia->setRotulo    ( "Vigência"        );
$obCmbDataVigencia->addOption    ( "", "Selecione"   );
$obCmbDataVigencia->setCampoId   ( "cod_vigencia"    );
$obCmbDataVigencia->setCampoDesc ( "dt_inicio"       );
$obCmbDataVigencia->setStyle     ( "width:150px"     );
$obCmbDataVigencia->setNull      ( false             );
$obCmbDataVigencia->setName      ( "inCodigoVigencia");
$obCmbDataVigencia->setId        ( "VigenciaAtual" );
$obCmbDataVigencia->setValue     (  $inCodigoVigencia);
$obCmbDataVigencia->preencheCombo(  $rsDataVigencia  );

$obTxtNomeNivel = new TextBox;
$obTxtNomeNivel->setName         ( "stNomeNivel" );
$obTxtNomeNivel->setSize         ( 40 );
$obTxtNomeNivel->setMaxLength    ( 80 );
$obTxtNomeNivel->setNull         ( true );
$obTxtNomeNivel->setRotulo       ( "Nome" );
$obTxtNomeNivel->setValue        ( $stNomeNivel );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList  );
$obForm->setTarget( "telaPrincipal" );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                           );
$obFormulario->setAjuda             ( "UC-05.02.06");
$obFormulario->addHidden            ( $obHdnCtrl                        );
$obFormulario->addTitulo            ( "Dados para Filtro"               );
$obFormulario->addHidden            ( $obHdnAcao                        );
$obFormulario->addComponente        ( $obCmbDataVigencia                );
$obFormulario->addComponente        ( $obTxtNomeNivel                   );

$obFormulario->setFormFocus( $obCmbDataVigencia->getid() );

$obFormulario->OK();
$obFormulario->show();

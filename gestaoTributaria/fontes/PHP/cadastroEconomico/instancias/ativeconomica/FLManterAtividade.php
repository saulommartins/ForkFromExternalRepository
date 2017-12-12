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
    * Página de Formulario de filtro de atividade
    * Data de Criação   : 25/11/2004

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: FLManterAtividade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.07

*/

/*
$Log$
Revision 1.11  2007/04/26 14:54:18  cercato
Bug #9220#

Revision 1.10  2006/09/15 14:32:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAtividade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

$obRCEMAtividade = new RCEMAtividade;
$obRCEMAtividade->geraMascara( $stMascara );

$rsNivel = new RecordSet;
$obRCEMAtividade->listarNiveisVigencia( $rsNivel );
$obRCEMAtividade->recuperaVigenciaAtual( $rsVigencia );

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obHdnVigenciaAtual =  new Hidden;
$obHdnVigenciaAtual->setName   ( "inVigenciaAtual" );
$obHdnVigenciaAtual->setValue  ( $rsVigencia->getCampo("cod_vigencia") );

$obCmbNivel = new Select;
$obCmbNivel->setRotulo    ( "Nível"         );
$obCmbNivel->addOption    ( "", "Todos"     );
$obCmbNivel->setCampoId   ( "[cod_nivel]-[cod_vigencia]" );
$obCmbNivel->setCampoDesc ( "nom_nivel"     );
$obCmbNivel->setId        ( "cmbNivel"      );
$obCmbNivel->setStyle     ( "width:250px"   );
$obCmbNivel->setName      ( "inCodigoNivel" );
$obCmbNivel->preencheCombo( $rsNivel     );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "stValorComposto" );
$obTxtCodigo->setRotulo    ( "Código" );
$obTxtCodigo->setMaxLength ( strlen( $stMascara ) );
//$obTxtCodigo->setMinLength ( strlen( $stMascara ) );
$obTxtCodigo->setSize      ( strlen( $stMascara ) );
//$obTxtCodigo->obEvento->setOnKeyUp("mascaraDinamico('".$stMascara."', this, event);");

$obTxtNomeAtividade = new TextBox;
$obTxtNomeAtividade->setName      ( "stNomeAtividade" );
$obTxtNomeAtividade->setRotulo    ( "Nome" );
$obTxtNomeAtividade->setMaxLength ( 240 );
$obTxtNomeAtividade->setSize      ( 80 );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm               );
$obFormulario->setAjuda      ( "UC-05.02.07");
$obFormulario->addTitulo     ( "Dados para Filtro"   );
$obFormulario->addHidden     ( $obHdnAcao            );
$obFormulario->addHidden     ( $obHdnVigenciaAtual   );
$obFormulario->addComponente ( $obCmbNivel           );
$obFormulario->addComponente ( $obTxtCodigo          );
$obFormulario->addComponente ( $obTxtNomeAtividade   );

$obFormulario->setFormFocus( $obCmbNivel->getid() );

$obFormulario->OK();
$obFormulario->show();
?>

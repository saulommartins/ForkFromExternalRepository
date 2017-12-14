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
* Página de Formulario de filtro de Penalidade
* Data de Criação   : 17/10/2006

* @author Analista: Lucas Teixeira Stephanou
* @author Desenvolvedor: Lucas Teixeira Stephanou

* Casos de uso :uc-03.05.28
*/

/*
$Log$
Revision 1.1  2006/10/17 12:00:15  domluc
PopUp de Penalidade usada no Componente de Penalidade.

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ProcurarPenalidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "excluir";
}

Sessao::write('link', '');

//DEFINICAO DOS COMPONENTES
$obHdnAcao = new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

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

$obIntCodigo = new Inteiro;
$obIntCodigo->setName   ( 'inCodPenalidade' );
$obIntCodigo->setId     ( 'inCodPenalidade' );
$obIntCodigo->setRotulo ( 'Código' );
$obIntCodigo->setTitle  ( 'Informe o código' );
$obIntCodigo->setNull   ( true  );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao"              );
$obTxtDescricao->setRotulo    ( "Descrição"                     );
$obTxtDescricao->setMaxLength ( 80                              );
$obTxtDescricao->setSize      ( 50                              );

$obCmpTipoBusca = new TipoBusca( $obTxtDescricao );

$obHdnTipoBusca = new Hidden;
$obHdnTipoBusca->setName( "stTipoBusca" );
$obHdnTipoBusca->setValue( $_REQUEST['tipoBusca'] );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addHidden( $obHdnForm              );
$obFormulario->addHidden( $obHdnCampoNum          );
$obFormulario->addHidden( $obHdnCampoNom          );
$obFormulario->addHidden( $obHdnTipoBusca 		  );
$obFormulario->addTitulo     ( "Dados para filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obIntCodigo    );
$obFormulario->addComponente ( $obCmpTipoBusca );
$obFormulario->OK();
$obFormulario->show();

?>

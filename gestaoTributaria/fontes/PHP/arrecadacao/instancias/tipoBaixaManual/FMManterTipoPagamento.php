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
    * Página de Formulario para inclusao na tabela arrecadaçaõ tipo pagamento
    * Data de Criação   : 05/05/2005

    * @@author Analista      : Fabio Bertoldi Rodrigues
    * @@author Desenvolvedor : Lucas Teixeira Stephanou

    * @@ignore

    * $Id: FMManterTipoPagamento.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.3  2006/09/15 11:19:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoPagamento.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoPagamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";
include_once( $pgJs );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::write( "link", "" );

//DEFINICAO DOS COMPONENTES
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName  ( "stCtrl" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obLblCodigoTipo = new Label;
$obLblCodigoTipo->setName   ( "stCodigoTipo"    );
$obLblCodigoTipo->setId     ( "stCodigoTipo"    );
$obLblCodigoTipo->setValue  ( $_REQUEST["inCodigoTipo"]     );

$obTxtDescricao = new TextBox ;
$obTxtDescricao->setName        ( "stDescricao"     );
$obTxtDescricao->setMaxLength   ( 80                );
$obTxtDescricao->setRotulo      ( "Descrição"       );
$obTxtDescricao->setNull        ( false             );
$obTxtDescricao->setValue       ( $_REQUEST["stDescricao"]      );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addTitulo            ( "Dados para tipo de baixa"    );
if ($stAcao == "alterar")
    $obFormulario->addComponente    ( $obLblCodigoTipo              );
$obFormulario->addComponente        ( $obTxtDescricao      );
$obFormulario->Ok();
$obFormulario->setFormFocus( $obTxtDescricao->getId() );
$obFormulario->show();

?>

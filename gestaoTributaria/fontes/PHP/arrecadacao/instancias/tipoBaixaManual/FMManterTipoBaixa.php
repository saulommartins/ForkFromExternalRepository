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

    * @author Analista      : Fabio Bertoldi Rodrigues
    * @author Desenvolvedor : Lucas Teixeira Stephanou

    * @ignore

    * $Id: FMManterTipoBaixa.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.09
*/

/*
$Log$
Revision 1.7  2006/09/15 11:19:33  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRTipoPagamento.class.php"    );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoBaixa";
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

$obHdnCodigoTipo = new Hidden;
$obHdnCodigoTipo->setName( "inCodigoTipo" );
$obHdnCodigoTipo->setValue( $_REQUEST["inCodigoTipo"] );

$obLblCodigoTipo = new Label;
$obLblCodigoTipo->setRotulo ( "Código"                         );
$obLblCodigoTipo->setTitle  ( "Código do Tipo de Baixa Manual" );
$obLblCodigoTipo->setName   ( "stCodigoTipo"            );
$obLblCodigoTipo->setId     ( "stCodigoTipo"            );
$obLblCodigoTipo->setValue  ( $_REQUEST["inCodigoTipo"] );

$obTxtDescricao = new TextBox ;
$obTxtDescricao->setName        ( "stDescricao"                 );
$obTxtDescricao->setTitle       ( "Descrição do tipo de baixa." );
$obTxtDescricao->setId          ( "stDescricao"           );
$obTxtDescricao->setMaxLength   ( 80                      );
$obTxtDescricao->setStyle       ( "width:200px;"          );
$obTxtDescricao->setRotulo      ( "Descrição"             );
$obTxtDescricao->setNull        ( false                   );
$obTxtDescricao->setValue       ( trim($_REQUEST["stNomeTipo"]) );

$obTxtDescricaoResumida = new TextBox ;
$obTxtDescricaoResumida->setName        ( "stNomeResumido"                       );
$obTxtDescricaoResumida->setTitle       ( "Descrição resumida do tipo de baixa." );
$obTxtDescricaoResumida->setId          ( "stNomeResumido"            );
$obTxtDescricaoResumida->setMaxLength   ( 20                          );
$obTxtDescricaoResumida->setSize        ( 20                          );
$obTxtDescricaoResumida->setStyle       ( "width:200px;"              );
$obTxtDescricaoResumida->setRotulo      ( "Descrição Resumida"        );
$obTxtDescricaoResumida->setNull        ( false                       );
$obTxtDescricaoResumida->setValue       ( trim($_REQUEST["stNomeResumido"]) );

$obRdbBaixaPagamento = new Radio;
$obRdbBaixaPagamento->setRotulo   ( "Utilização"                           );
$obRdbBaixaPagamento->setName     ( "boPagamento"                          );
$obRdbBaixaPagamento->setLabel    ( "Pagamento"                            );
$obRdbBaixaPagamento->setValue    ( "t"                                    );
$obRdbBaixaPagamento->setChecked  ( ( $_REQUEST['boPagamento'] == "t" OR !$_REQUEST['boPagamento'] ) );
$obRdbBaixaPagamento->setNull     ( false                                  );

$obRdbBaixaCancelamento = new Radio;
$obRdbBaixaCancelamento->setRotulo   ( "Utilização"                            );
$obRdbBaixaCancelamento->setName     ( "boPagamento"                           );
$obRdbBaixaCancelamento->setLabel    ( "Cancelamento"                          );
$obRdbBaixaCancelamento->setValue    ( "f"                                     );
$obRdbBaixaCancelamento->setChecked  ( ( $_REQUEST['boPagamento'] == "f" ) );
$obRdbBaixaCancelamento->setNull     ( false                                   );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addHidden            ( $obHdnCodigoTipo              );
$obFormulario->addTitulo            ( "Dados para Tipo de Baixa"    );
if ($stAcao == "alterar") {
    $obFormulario->addComponente    ( $obLblCodigoTipo     );
}
$obFormulario->addComponente        ( $obTxtDescricao         );
$obFormulario->addComponente        ( $obTxtDescricaoResumida );
$obFormulario->agrupaComponentes    ( array( $obRdbBaixaPagamento, $obRdbBaixaCancelamento ) );
$obFormulario->Ok();
$obFormulario->setFormFocus( $obTxtDescricao->getId() );
$obFormulario->show();

?>

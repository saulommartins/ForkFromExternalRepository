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
    * Página de filtro para o cadastro de trecho
    * Data de Criação   : 02/09/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    * $Id: FLManterTrecho.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.01.06
*/

/*
$Log$
Revision 1.8  2006/10/26 16:02:36  dibueno
Alterações do nome do campo ID da busca

Revision 1.7  2006/09/18 10:31:51  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CIM_NEGOCIO."RCIMTrecho.class.php"     );

//Define o nome dos arquivos PHP
$stPrograma  = "ManterTrecho";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgFormBaixa = "FM".$stPrograma."Baix.php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJs        = "JS".$stPrograma.".js";

$pgProx = $pgList;

include( $pgJs );

Sessao::remove('link');
Sessao::remove('stLink');

if ( empty( $_REQUEST['stAcao'] ) ) {
    $stAcao = "incluir";
}

//DEFINICAO DOS COMPONENTES DO FORMULARIO
$obHdnAcao = new Hidden;
$obHdnAcao->setName  ( "stAcao" );
$obHdnAcao->setValue ( $_REQUEST['stAcao'] );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obHdnNomLogradouro = new Hidden;
$obHdnNomLogradouro->setName ( "stNomeLogradouro" );
$obHdnNomLogradouro->setValue( $_REQUEST ["stNomeLogradouro"] );

$obBscLogradouro = new BuscaInner;
$obBscLogradouro->setRotulo                       ( "Logradouro"                               );
$obBscLogradouro->setTitle                        ( "Logradouro onde o trecho está localizado" );
$obBscLogradouro->setId                           ( "campoInnerLogr"                           );
$obBscLogradouro->obCampoCod->setName             ( "inNumLogradouro"                          );
$obBscLogradouro->obCampoCod->setId               ( "inNumLogradouro"                          );
$obBscLogradouro->obCampoCod->setValue            ( $inNumLogradouro                           );
$obBscLogradouro->obCampoCod->obEvento->setOnChange( "buscaLogradouroFiltro();"                );
$stBusca  = "abrePopUp('../../../cadastroImobiliario/popups/logradouro/FLProcurarLogradouro.php','frm','inNumLogradouro',";
$stBusca .= "'campoInnerLogr','juridica','".Sessao::getId()."','800','550')";
$obBscLogradouro->setFuncaoBusca                  ( $stBusca );

$obTxtSequencia = new TextBox;
$obTxtSequencia->setRotulo    ( "Seqüência"           );
$obTxtSequencia->setTitle     ( "Seção do logradouro" );
$obTxtSequencia->setName      ( "inCodSequencia"      );
$obTxtSequencia->setValue     ( $inCodSequencia       );
$obTxtSequencia->setSize      ( 10                    );
$obTxtSequencia->setMaxLength ( 10                    );
$obTxtSequencia->setInteiro   ( true                  );

$obForm = new Form;
$obForm->setAction            ( $pgProx               );
$obForm->setTarget            ( "telaPrincipal"       );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm        ( $obForm                   );
$obFormulario->setAjuda ( "UC-05.01.06" );
$obFormulario->addHidden      ( $obHdnAcao                );
$obFormulario->addHidden      ( $obHdnCtrl                );
$obFormulario->addHidden      ( $obHdnNomLogradouro       );
$obFormulario->addTitulo      ( "Dados para Filtro"       );
$obFormulario->addComponente  ( $obBscLogradouro          );
$obFormulario->addComponente  ( $obTxtSequencia           );
$obFormulario->OK();
$obFormulario->setFormFocus   ( $obBscLogradouro->obCampoCod->getId() );
$obFormulario->show();
?>

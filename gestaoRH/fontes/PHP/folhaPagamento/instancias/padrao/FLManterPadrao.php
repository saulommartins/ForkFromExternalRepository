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
    * Página de Formulario de filtro de padrao
    * Data de Criação   : 03/12/2004

    * @author Analista: ???
    * @author Desenvolvedor: Gustavo Passos Tourinho

    * @ignore

    $Revision: 30840 $
    $Name$
    $Author: andre $
    $Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

    * Casos de uso :uc-04.05.05

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
//Define o nome dos arquivos PHP
$stPrograma = "ManterPadrao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

//include_once ($pgJS);
//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

Sessao::remove("link");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao  );

$obTxtCodigo = new TextBox;
$obTxtCodigo->setName      ( "stCodPadrao"                 );
$obTxtCodigo->setTitle     ( "Informe o código do padrão"  );
$obTxtCodigo->setRotulo    ( "Código"                      );
$obTxtCodigo->setMaxLength ( 6                             );
$obTxtCodigo->setSize      ( 6                             );
$obTxtCodigo->setInteiro   ( true                          );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setName      ( "stDescricao"                   );
$obTxtDescricao->setTitle     ( "Informe a descrição do padrão" );
$obTxtDescricao->setRotulo    ( "Descrição"                     );
$obTxtDescricao->setMaxLength ( 80                              );
$obTxtDescricao->setSize      ( 80                              );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction( $pgList );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm       ( $obForm );
$obFormulario->addTitulo     ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo     ( "Dados para filtro" );
$obFormulario->addHidden     ( $obHdnAcao );
$obFormulario->addComponente ( $obTxtCodigo );
$obFormulario->addComponente ( $obTxtDescricao );
$obFormulario->OK();
$obFormulario->show();
?>

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
* Filtro para Classificação de Assentamento
* Data de Criação   : 10/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Programador: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php"      );

$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//************************************************/
// Limpa a variavel de sessão para o filtro
//***********************************************/
Sessao::remove('filtroRelatorio');
Sessao::remove('link');

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRPessoalClassificacaoAssentamento    = new RPessoalClassificacaoAssentamento;
$rsTipo                                 = new Recordset;

$obRPessoalClassificacaoAssentamento->listarTipo( $rsTipo );
$arTipo = $rsTipo->getElementos();
$arTMP['cod_tipo']  = 'todos';
$arTMP['descricao'] = 'Todos';
array_unshift($arTipo,$arTMP);
$rsTipo                                 = new Recordset;
$rsTipo->preenche( $arTipo );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbTipo = new Select;
$obCmbTipo->setRotulo                   ( "Tipo"                                    );
$obCmbTipo->setName                     ( "inFiltroCodTipo"                         );
$obCmbTipo->setId                       ( "inFiltroCodTipo"                         );
$obCmbTipo->setValue                    ( $inCodTipo                                );
$obCmbTipo->setStyle                    ( "width: 200px"                            );
$obCmbTipo->setCampoID                  ( "cod_tipo"                                );
$obCmbTipo->setCampoDesc                ( "descricao"                               );
$obCmbTipo->addOption                   ( "", "Selecione"                           );
$obCmbTipo->setNull                     ( false                                     );
$obCmbTipo->preencheCombo               ( $rsTipo                                   );

//DEFINICAO DOS COMPONENTES
$obForm = new Form;
$obForm->setAction                      ( $pgList                                   );
$obForm->setTarget                      ( "telaPrincipal"                           );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                  ( $obForm                                   );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden                ( $obHdnAcao                                );
$obFormulario->addHidden                ( $obHdnCtrl                                );

$obFormulario->addTitulo                ( "Dados para o Filtro"                     );
$obFormulario->addComponente            ( $obCmbTipo                                );
$obFormulario->setFormFocus             ( $obCmbTipo->getId()                       );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

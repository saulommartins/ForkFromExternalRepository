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
* Filtro de condição do assentamento
* Data de Criação: 08/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @package URBEM
* @subpackage

$Revision: 30860 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.04.13
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamentoVinculado.class.php"      );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCondicaoAssentamento.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalAssentamento.class.php"               );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalVantagem.class.php"                   );

$stPrograma = "ManterCondicao";
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
$obRFolhaPagamentoFolhaSituacao  = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRPessoalVantagem              = new RPessoalVantagem;
$obRPessoalAssentamento1         = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalAssentamento2         = new RPessoalAssentamento($obRPessoalVantagem);
$obRPessoalCondicaoAssentamento  = new RPessoalCondicaoAssentamento();
$obRPessoalAssentamentoVinculado = new RPessoalAssentamentoVinculado( $obRPessoalAssentamento1,$obRPessoalAssentamento2,$obRPessoalCondicaoAssentamento );
$rsClassificacao                 = new Recordset;

$obRPessoalAssentamentoVinculado->obRPessoalAssentamento1->obRPessoalClassificacaoAssentamento->listarClassificacao( $rsClassificacao );

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obTxtClassificacao = new TextBox;
$obTxtClassificacao->setRotulo              ( "Classificação"                           );
$obTxtClassificacao->setTitle               ( "Informe a classificação do assentamento." );
$obTxtClassificacao->setName                ( "inCodClassificacaoTxt"                   );
$obTxtClassificacao->setId                  ( "inCodClassificacaoTxt"                   );
$obTxtClassificacao->setValue               ( $inCodClassificacaoTxt                    );
$obTxtClassificacao->setSize                ( 6                                         );
$obTxtClassificacao->setMaxLength           ( 6                                         );
$obTxtClassificacao->setInteiro             ( true                                      );
$obTxtClassificacao->setNull                ( false                                     );

$obCmbClassificacao = new Select;
$obCmbClassificacao->setRotulo              ( "Classificação"                           );
$obCmbClassificacao->setName                ( "inCodClassificacao"                      );
$obCmbClassificacao->setValue               ( $inCodClassificacao                       );
$obCmbClassificacao->setStyle               ( "width: 200px"                            );
$obCmbClassificacao->setCampoID             ( "cod_classificacao"                       );
$obCmbClassificacao->setCampoDesc           ( "descricao"                               );
$obCmbClassificacao->addOption              ( "", "Selecione"                           );
$obCmbClassificacao->setNull                ( false                                     );
$obCmbClassificacao->preencheCombo          ( $rsClassificacao                          );

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
$obFormulario->addComponenteComposto    ( $obTxtClassificacao, $obCmbClassificacao  );
$obFormulario->setFormFocus             ( $obTxtClassificacao->getId()              );
$obFormulario->OK                   ();
$obFormulario->show                 ();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

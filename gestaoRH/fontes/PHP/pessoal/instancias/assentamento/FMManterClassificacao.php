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
* Página de Formulario de Inclusao/Alteracao de Assentamento
* Data de Criação   : 28/01/2005

* @author Analista: ???
* @author Programador: Lucas Leusin Oaigen

* @ignore

$Revision: 30888 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalClassificacaoAssentamento.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterClassificacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obRPessoalClassificacaoAssentamento    = new RPessoalClassificacaoAssentamento;
$rsTipo                                 = new Recordset;

$obRPessoalClassificacaoAssentamento->listarTipo( $rsTipo );

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $_GET['stAcao'] ?  $_GET['stAcao'] : $_POST['stAcao'];

if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

$stFiltro = $_POST['inFiltroCodTipo'] ? $_POST['inFiltroCodTipo'] : $_GET['inFiltroCodTipo'];

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
//Instancia o formulário
$obForm = new Form;
$obForm->setAction( $pgProc     );
$obForm->setTarget( "oculto"    );

//Define o objeto da ação stAcao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ( "stAcao"  );
$obHdnAcao->setValue( $stAcao   );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName ( "stCtrl"  );
$obHdnCtrl->setValue( ""        );

$obHdnCodClassificacao = new Hidden;
$obHdnCodClassificacao->setName         ( "inCodClassificacao"                            );
$obHdnCodClassificacao->setValue        ( $_GET['inCodClassificacao']                              );

$obHdnFiltroCodTipo = new Hidden;
$obHdnFiltroCodTipo->setName            ( "inFiltroCodTipo"                            );
$obHdnFiltroCodTipo->setValue           ( $stFiltro                                       );

$obTxtDescricao = new TextBox;
$obTxtDescricao->setRotulo              ( "Classificação"                                 );
$obTxtDescricao->setTitle               ( "Informe a descrição da classificação."          );
$obTxtDescricao->setName                ( "stDescricao"                                   );
$obTxtDescricao->setId                  ( "stDescricao"                                   );
$obTxtDescricao->setValue               ( $_REQUEST["stDescricao"]                                    );
$obTxtDescricao->setSize                ( 40                                              );
$obTxtDescricao->setMaxLength           ( 80                                              );
$obTxtDescricao->setNull                ( false                                           );

$obTxtTipo = new TextBox;
$obTxtTipo->setRotulo                   ( "Tipo"                                    );
$obTxtTipo->setTitle                    ( "Informe o tipo da classificação."         );
$obTxtTipo->setName                     ( "inCodTipoTxt"                            );
$obTxtTipo->setValue                    ( $_REQUEST["inCodTipoTxt"]                             );
$obTxtTipo->setSize                     ( 6                                         );
$obTxtTipo->setMaxLength                ( 6                                         );
$obTxtTipo->setInteiro                  ( true                                      );
$obTxtTipo->setNull                     ( false                                     );
if ($_REQUEST['stAcao'] == 'alterar') {
    $obTxtTipo->setReadOnly             ( true                                      );
}

$obCmbTipo = new Select;
$obCmbTipo->setRotulo                   ( "Tipo"                                    );
$obCmbTipo->setName                     ( "inCodTipo"                               );
$obCmbTipo->setValue                    ( $_REQUEST["inCodTipo"]                                );
$obCmbTipo->setStyle                    ( "width: 200px"                            );
$obCmbTipo->setCampoID                  ( "cod_tipo"                                );
$obCmbTipo->setCampoDesc                ( "descricao"                               );
$obCmbTipo->addOption                   ( "", "Selecione"                           );
$obCmbTipo->setNull                     ( false                                     );
$obCmbTipo->preencheCombo               ( $rsTipo                                   );
if ($_REQUEST['stAcao'] == 'alterar') {
    $obCmbTipo->setDisabled             ( true                                      );
}

//****************************************//
//Monta FORMULARIO
//****************************************//
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );

$obFormulario->addHidden                ( $obHdnCtrl                                );
$obFormulario->addHidden                ( $obHdnAcao                                );
$obFormulario->addHidden                ( $obHdnCodClassificacao                    );
$obFormulario->addHidden                ( $obHdnFiltroCodTipo                       );
$obFormulario->addTitulo                ( "Descrição da Classificação"              );
$obFormulario->addComponente            ( $obTxtDescricao                           );
$obFormulario->addComponenteComposto    ( $obTxtTipo, $obCmbTipo                    );
$obFormulario->setFormFocus             ( $obTxtDescricao->getId()                  );

if ($stAcao == "incluir") {
    $obFormulario->OK();
} else {
    $obFormulario->Cancelar( $pgList.'?'.Sessao::getId().'&stAcao='.$stAcao.'&inFiltroCodTipo='.$stFiltro );
}
$obFormulario->show();

if ($stAcao == "incluir") {
    $js .= "focusIncluir();";
    sistemaLegado::executaFrameOculto($js);
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Página de Filtro de Conceder de 13º Salário
    * Data de Criação: 14/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30711 $
    $Name$
    $Author: souzadl $
    $Date: 2008-03-26 17:16:54 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-04.05.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );

//Define o nome dos arquivos PHP
$stPrograma = "ConcederDecimo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

Sessao::write("link","");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao($obRFolhaPagamentoPeriodoMovimentacao);
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();
$jsOnload   = "montaParametrosGET('processarFiltro','stAcao');";

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                                              );
$obHdnAcao->setValue                            ( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                                              );
$obHdnCtrl->setValue                            ( $stCtrl                                                               );

$obSpnPercentualAdiantamento = new Span;
$obSpnPercentualAdiantamento->setId                              ( "spnPercentualAdiantamento"                                                            );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegSubFunEsp();

$obRdoDesdSaldo = new Radio();
$obRdoDesdSaldo->setRotulo("Desdobramento");
$obRdoDesdSaldo->setName("stDesdobramento");
$obRdoDesdSaldo->setId("stDesdobramento");
$obRdoDesdSaldo->setLabel("Saldo de 13º Salário");
$obRdoDesdSaldo->setValue("D");
$obRdoDesdSaldo->setTitle("Selecione o desdobramento para concessão de décimo terceiro.");
$obRdoDesdSaldo->setDisabled(true);

$obRdoDesdComplementacao = new Radio();
$obRdoDesdComplementacao->setRotulo("Desdobramento");
$obRdoDesdComplementacao->setName("stDesdobramento");
$obRdoDesdComplementacao->setId("stDesdobramento");
$obRdoDesdComplementacao->setLabel("Complementação de 13º Salário");
$obRdoDesdComplementacao->setValue("C");
$obRdoDesdComplementacao->setTitle("Selecione o desdobramento para concessão de décimo terceiro.");
$obRdoDesdComplementacao->setDisabled(true);

$obRdoDesdAdiantamento = new Radio();
$obRdoDesdAdiantamento->setRotulo("Desdobramento");
$obRdoDesdAdiantamento->setName("stDesdobramento");
$obRdoDesdAdiantamento->setId("stDesdobramento");
$obRdoDesdAdiantamento->setLabel("Adiantamento de 13º Salário");
$obRdoDesdAdiantamento->setValue("A");
$obRdoDesdAdiantamento->setTitle("Selecione o desdobramento para concessão de décimo terceiro.");
$obRdoDesdAdiantamento->setChecked(true);
$obRdoDesdAdiantamento->setDisabled(true);

$arDesdobramentos = array($obRdoDesdComplementacao,$obRdoDesdSaldo,$obRdoDesdAdiantamento);

$obBntOk = new ok();
$obBntOk->obEvento->setOnClick( "montaParametrosGET('submeter','stAcao',true);" );

if ($stAcao !== "inserir") {
    $obBntOk->obEvento->setOnClick              ( "montaParametrosGET('excluir','',true);"                             );
}

$obBtnLimpar = new Button;
$obBtnLimpar->setName                           ( "btnLimparFiltro"                                                     );
$obBtnLimpar->setValue                          ( "Limpar"                                                              );
$obBtnLimpar->setTipo                           ( "button"                                                              );
$obBtnLimpar->obEvento->setOnClick              ( "executaFuncaoAjax('limparFiltro');"                                  );

//DEFINICAO DO FORM
$obForm = new Form;
if ($stAcao == "inserir") {
    $obForm->setAction                          ( $pgProc                                                               );
    $obForm->setTarget                          ( "oculto"                                                              );
} else {
    $obForm->setAction                          ( $pgList                                                               );
}

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                                               );
$obFormulario->addTitulo                        ( $stTitulo ,"right"      );
$obFormulario->addHidden                        ( $obHdnAcao                                                            );
$obFormulario->addHidden                        ( $obHdnCtrl                                                            );
$obFormulario->addTitulo                        ( "Seleção do Filtro"                                                   );
$obIFiltroComponentes->geraFormulario($obFormulario);
if ($stAcao == "inserir") {
    $obFormulario->agrupaComponentes($arDesdobramentos);
}
$obFormulario->addSpan                      ( $obSpnPercentualAdiantamento                                                           );
$obFormulario->defineBarra                      ( array($obBntOk,$obBtnLimpar)                                          );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

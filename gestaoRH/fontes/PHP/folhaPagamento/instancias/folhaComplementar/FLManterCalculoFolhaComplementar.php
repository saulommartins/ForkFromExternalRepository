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
    * Filtro Gerar Folha de Pagamento Complementar
    * Data de Criação: 24/01/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30766 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-18 09:34:28 -0200 (Qui, 18 Out 2007) $

    * Casos de uso: uc-04.05.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once (CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                              );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoFolhaComplementar";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

Sessao::remove("calculados");
Sessao::remove("link");
Sessao::write("boExcluirCalculados",true);

$obRFolhaPagamentoPeriodoMovimentacao =  new RFolhaPagamentoPeriodoMovimentacao ;
$obRFolhaPagamentoPeriodoMovimentacao->addRFolhaPagamentoFolhaComplementar();
$obRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsMovimentacao);
if ($rsMovimentacao->getCampo('cod_periodo_movimentacao') != "") {
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao($rsMovimentacao->getCampo('cod_periodo_movimentacao'));
} else {
    $obRFolhaPagamentoPeriodoMovimentacao->setCodPeriodoMovimentacao(0);
}
$obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->consultarFolhaComplementarAberta();
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();
$arMeses['01'] = "Janeiro";
$arMeses['02'] = "Fevereiro";
$arMeses['03'] = "Março";
$arMeses['04'] = "Abril";
$arMeses['05'] = "Maio";
$arMeses['06'] = "Junho";
$arMeses['07'] = "Julho";
$arMeses['08'] = "Agosto";
$arMeses['09'] = "Setembro";
$arMeses['10'] = "Outubro";
$arMeses['11'] = "Novembro";
$arMeses['12'] = "Dezembro";
$arData = explode("/",$rsMovimentacao->getCampo('dt_final'));
$inCodComplementar = $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getCodComplementar();
$stComplementar = $inCodComplementar." - ".$arMeses[$arData[1]];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             ( "stAcao"                                              );
$obHdnAcao->setValue                            ( $stAcao                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             ( "stCtrl"                                              );
$obHdnCtrl->setId                               ( "stCtrl"                                              );
$obHdnCtrl->setValue                            ( $stStrl                                               );

$obHdnErro =  new Hidden;
$obHdnErro->setName                             ( "stErro"                                              );
$obHdnErro->setValue                            ( "f"                                                   );

$obHdnCodPeriodoMovimentacao =  new Hidden;
$obHdnCodPeriodoMovimentacao->setName           ( "inCodPeriodoMovimentacao"                            );
$obHdnCodPeriodoMovimentacao->setValue          ( $rsMovimentacao->getCampo('cod_periodo_movimentacao') );

$obLblComplementar = new Label;
$obLblComplementar->setId                       ( "stComplementar"                                      );
$obLblComplementar->setValue                    ( $stComplementar                                       );
$obLblComplementar->setRotulo                   ( "Complementar"                                        );

$obHdnComplementar =  new Hidden;
$obHdnComplementar->setName                     ( "inCodComplementar"                                   );
$obHdnComplementar->setValue                    ( $inCodComplementar                                    );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setTodos();

$obBntOk = new ok();
$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."folhaComplementar/OCManterCalculoFolhaComplementar.php?".Sessao::getId()."' \n";
$obBntOk->obEvento->setOnClick( " $stJs
                                  jQuery('#stCtrl').val('submeter');
                                  jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                ");

$obBtnLimpar = new Limpar();

$stMensagem = "Nenhuma folha complementar está aberta. Para efetuar o calculo da folha complementar, é necessário abri-lá ou reabri-lá.";
$obLblMensagem = new Label;
$obLblMensagem->setRotulo               ( "Situação"                                                );
$obLblMensagem->setValue                ( $stMensagem                                               );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                               );
$obForm->setTarget                              ( "oculto"                                              );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm                          ( $obForm                                               );
$obFormulario->addTitulo                        ( $stTitulo ,"right" );
if ( $obRFolhaPagamentoPeriodoMovimentacao->roRFolhaPagamentoFolhaComplementar->getSituacao() == 'a' ) {
    $obFormulario->addHidden                    ( $obHdnAcao                                            );
    $obFormulario->addHidden                    ( $obHdnCtrl                                            );
    $obFormulario->addHidden                    ( $obHdnComplementar                                    );
    $obFormulario->addHidden                    ( $obHdnCodPeriodoMovimentacao                          );
    $obFormulario->addHidden                    ( $obHdnErro                                            );
    $obFormulario->addTitulo                    ( "Folha Complementar"                                  );
    $obFormulario->addComponente                ( $obLblComplementar                                    );
    $obFormulario->addTitulo                    ( "Seleção do Filtro"                                   );
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->defineBarra                  ( array($obBntOk,$obBtnLimpar)                          );
} else {
    $obFormulario->addComponente(                 $obLblMensagem                                           );
}
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Filtro
    * Data de Criação: 29/11/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: FLManterCalculoSalario.php 65863 2016-06-22 20:38:54Z michel $

    * Casos de uso: uc-04.05.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php";
include_once CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalculoSalario";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);
$obRFolhaPagamentoFolhaSituacao->consultarFolha();
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();

$stAcao = $request->get('stAcao');

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ( "stCtrl");
$obHdnCtrl->setId   ( "stCtrl");
$obHdnCtrl->setValue( $request->get('stStrl'));

$obHdnErro =  new Hidden;
$obHdnErro->setName ( "stErro" );
$obHdnErro->setValue( "f"      );

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setEvento();

$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."movimentacaoFinanceira/OCManterCalculoSalario.php?".Sessao::getId()."'; \n";
$stJs .= "jQuery('#stCtrl').val('submeter'); \n";
$stJs .= "jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html'); \n";

$obBntOk = new ok();
$obBntOk->obEvento->setOnClick( $stJs );

$obBtnLimpar = new Limpar();

$stMensagem = "A folha salário está fechada. Para efetuar o cálculo da folha é necessário reabri-lá.";
$obLblMensagem = new Label;
$obLblMensagem->setRotulo                       ( "Situação"                                            );
$obLblMensagem->setValue                        ( $stMensagem                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc                                               );
$obForm->setTarget                              ( "oculto"                                              );

$obSpnSpan1 = new Span;
$obSpnSpan1->setId                              ( "spnSpan1"                                            );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
if ( $obRFolhaPagamentoFolhaSituacao->getSituacao() == 'Aberto' ) {
    $obFormulario->addForm                      ( $obForm                                               );
    $obFormulario->addHidden                    ( $obHdnAcao                                            );
    $obFormulario->addHidden                    ( $obHdnCtrl                                            );
    $obFormulario->addHidden                    ( $obHdnErro                                            );
    $obFormulario->addTitulo                    ( $stTitulo ,"right"   );
    $obFormulario->addTitulo                    ( "Seleção do Filtro"                                   );
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->defineBarra                  ( array($obBntOk,$obBtnLimpar)                          );
} else {
    $obFormulario->addTitulo                    ( "Folha Salário"                                       );
    $obFormulario->addComponente                ( $obLblMensagem                                        );
}
$obFormulario->addSpan                          ( $obSpnSpan1                                           );
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Formulário
    * Data de Criação: 17/07/2007

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30849 $
    $Name$
    $Author: souzadl $
    $Date: 2007-10-02 18:27:04 -0300 (Ter, 02 Out 2007) $

    * Casos de uso: uc-04.05.62
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$stPrograma = 'EmitirAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$jsOnload = "executaFuncaoAjax('gerarSpanOrigemValores');";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obForm = new Form;
$obForm->setAction ( $pgList  );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $request->get('stAcao')  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );
$obCkbOrigem1 = new Radio();
$obCkbOrigem1->setRotulo("Origem dos Valores");
$obCkbOrigem1->setName("stOrigem");
$obCkbOrigem1->setTitle("Marque a origem dos dados que serão gerados no empenho.");
$obCkbOrigem1->setValue("f");
$obCkbOrigem1->setLabel("Folha Pagamento");
$obCkbOrigem1->setChecked(true);
$obCkbOrigem1->setNull(false);
$obCkbOrigem1->obEvento->setOnChange("montaParametrosGET('gerarSpanOrigemValores','stOrigem',true);");

$obCkbOrigem2 = new Radio();
$obCkbOrigem2->setRotulo("Origem dos Valores");
$obCkbOrigem2->setName("stOrigem");
$obCkbOrigem2->setTitle("Marque a origem dos dados que serão gerados no empenho.");
$obCkbOrigem2->setValue("p");
$obCkbOrigem2->setLabel("Previdência");
$obCkbOrigem2->setNull(false);
$obCkbOrigem2->obEvento->setOnChange("montaParametrosGET('gerarSpanOrigemValores','stOrigem,stOpcoes',true);");

$obCkbOrigem3 = new Radio();
$obCkbOrigem3->setRotulo("Origem dos Valores");
$obCkbOrigem3->setName("stOrigem");
$obCkbOrigem3->setTitle("Marque a origem dos dados que serão gerados no empenho.");
$obCkbOrigem3->setValue("g");
$obCkbOrigem3->setLabel("FGTS");
$obCkbOrigem3->setNull(false);
$obCkbOrigem3->obEvento->setOnChange("montaParametrosGET('gerarSpanOrigemValores','stOrigem',true);");

$obCkbOrigem4 = new Radio();
$obCkbOrigem4->setRotulo("Origem dos Valores");
$obCkbOrigem4->setName("stOrigem");
$obCkbOrigem4->setTitle("Marque a origem dos dados que serão gerados no empenho.");
$obCkbOrigem4->setValue("d");
$obCkbOrigem4->setLabel("Diárias");
$obCkbOrigem4->setNull(false);
$obCkbOrigem4->obEvento->setOnChange("montaParametrosGET('gerarSpanOrigemValores','stOrigem',true);");

$obSpnOrigem = new Span();
$obSpnOrigem->setId("spnOrigem");

$obHdnOrigem = new hiddenEval();
$obHdnOrigem->setId("hdnOrigem");
$obHdnOrigem->setName("hdnOrigem");

include_once(CAM_GA_ADM_COMPONENTES."IMontaAssinaturas.class.php");
$obIMontaAssinaturas = new IMontaAssinaturas();
$obIMontaAssinaturas->definePapeisDisponiveis("autorizacao_empenho");

$obHdnEntidade = new hidden();
$obHdnEntidade->setId("inCodEntidade");
$obHdnEntidade->setName("inCodEntidade");
$obHdnEntidade->setValue(Sessao::getCodEntidade($boTransacao));

$obBtnGerar = new Ok();
$obBtnGerar->setValue("Gerar Lista");
$obBtnGerar->setStyle( "width: 100px" );

$obFormulario = new Formulario();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );
$obFormulario->addHidden ( $obHdnEntidade );
$obFormulario->agrupaComponentes(array($obCkbOrigem1,$obCkbOrigem2,$obCkbOrigem3,$obCkbOrigem4));
$obFormulario->addSpan($obSpnOrigem);
$obFormulario->addHidden($obHdnOrigem,true);
$obIMontaAssinaturas->geraFormulario($obFormulario);
$obFormulario->defineBarra(array($obBtnGerar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

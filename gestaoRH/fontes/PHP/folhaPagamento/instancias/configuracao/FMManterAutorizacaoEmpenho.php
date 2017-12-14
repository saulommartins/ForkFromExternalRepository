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
    * Data de Criação: 10/07/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.29

    $Id: FMManterAutorizacaoEmpenho.php 66355 2016-08-17 13:30:17Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php"                   );

$stPrograma = 'ManterAutorizacaoEmpenho';
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$jsOnload = "montaParametrosGET('processarForm', 'dtVigencia,stAcao');
             montaParametrosGET('gerarSpansAbas');
             montaParametrosGET('carregaEvento');

             //Adiciona o bind para limpar o combo de historico dentro da span
             jQuery('#btLimparAutorizacao').bind('click', function () {
                jQuery('#inCodHistoricoPadrao').val('');
             }); ";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$dtVigencia = $request->get("dtVigencia");
if (trim($dtVigencia) == "") {
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    $dtVigencia = $rsPeriodoMovimentacao->getCampo('dt_inicial');
}

$arDtVigencia = explode("/", $dtVigencia);
Sessao::write("inExercicioVigencia", $arDtVigencia[2]);
Sessao::write("dtVigencia",$dtVigencia);

$obForm = new Form;
$obForm->setAction ( $pgProc  );
$obForm->setTarget ( 'oculto' );

$obHdnAcao = new Hidden;
$obHdnAcao->setName     ( "stAcao" );
$obHdnAcao->setValue    ( $stAcao  );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     ( "stCtrl" );
$obHdnCtrl->setId       ( "stCtrl" );
$obHdnCtrl->setValue    ( ""       );

$obVigencia = new Data();
$obVigencia->setRotulo	( "Vigência"	);
$obVigencia->setName	( "dtVigencia"	);
$obVigencia->setId		( "dtVigencia"	);
$obVigencia->setValue	( $dtVigencia   );
$obVigencia->setNull    ( false			);
$obVigencia->obEvento->setOnChange("montaParametrosGET('atualizarLotacao','dtVigencia,stAcao');");

$obLblVigencia = new Label();
$obLblVigencia->setRotulo	( "Vigência"  );
$obLblVigencia->setValue	( $dtVigencia );
$obLblVigencia->setNull     ( false		  );

$obHdnVigencia = new Hidden();
$obHdnVigencia->setName   	( "dtVigencia" );
$obHdnVigencia->setId   	( "dtVigencia" );
$obHdnVigencia->setValue	( $dtVigencia  );
$obHdnVigencia->setNull     ( false		   );

include_once 'FMManterAutorizacaoEmpenhoAbaAutorizacao.php';
include_once 'FMManterAutorizacaoEmpenhoAbaLLA.php';
include_once 'FMManterAutorizacaoEmpenhoAbaEventos.php';

$obBtnOk = new Ok;
$obBtnOk->obEvento->setOnClick("BloqueiaFrames(true,false); montaParametrosGET('submeter','stAcao','true');");

$obBtnLimpar = new Button;
$obBtnLimpar->setName             ( "btnLimpar"                          );
$obBtnLimpar->setValue            ( "Limpar Abas"                        );
$obBtnLimpar->setTipo             ( "button"                             );
$obBtnLimpar->obEvento->setOnClick( "executaFuncaoAjax('limparFiltro');" );

$obFormulario = new FormularioAbas();
$obFormulario->addForm   ( $obForm    );
$obFormulario->addTitulo ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden ( $obHdnCtrl );
$obFormulario->addHidden ( $obHdnAcao );

$obFormulario->addAba   ( "Dados Autorização"    );
$obFormulario->addTitulo("Vigência da Configuração");
if ($stAcao != 'incluir') {
    $obFormulario->addComponente($obLblVigencia);
    $obFormulario->addHidden($obHdnVigencia);
} else {
    $obFormulario->addComponente($obVigencia);
}
$obFormulario->addTitulo ( "Configuração da Autorização de Empenho" );
$obFormulario->addComponente($obBscFornecedor);
$obFormulario->addComponente($obTxtDescricaoAutorizacao);
$obFormulario->addSpan($obSpnCmbHistoricoPadrao);
$obFormulario->addTitulo("Item da Autorização");
$obFormulario->addComponente($obTxtDescricaoItemAutorizacao);
$obFormulario->addComponente($obTxtComplementoAutorizacao);               
$obFormulario->IncluirAlterar("Autorizacao" ,$arComponentesAutorizacao,true,true,'inNumCGM,campoInner,stDescricaoAutorizacao,inCodHistoricoPadrao,stDescricaoItemAutorizacao,stComplementoAutorizacao');
$obFormulario->addSpan($obSpnConfiguracoesEmpenhos);

$obFormulario->addAba ( "Lotação/Local/Atributo" );
$obFormulario->addSpan($obSpnComboOpcoes);
$obFormulario->addSpan($obSpnOpcoesConfiguracao);
$obFormulario->addHidden($obHdnOpcoesConfiguracao);
$obFormulario->defineBarraAba($arBotoesLLA);
$obFormulario->addSpan($obSpnConfiguracoesLLA);

$obFormulario->addAba ( "Eventos" );
$obFormulario->addSpan($obSpnComboOpcoesConfiguracaoEvento);
$obFormulario->addSpan($obSpnOpcoesConfiguracaoEventos);
$obFormulario->addHidden($obHdnOpcoesConfiguracaoEventos);
$obFormulario->addHidden($obHdnNumPAOEvento);
$obFormulario->addHidden($obHdnDotacaoEvento);
$obFormulario->addComponente($obCmbConfiguracao);
$obFormulario->addComponente($obISelectMultiploEvento);
$obISelectMultiploRegSubCarEsp->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes(array($obCkbSituacao1,$obCkbSituacao2,$obCkbSituacao3));
$obFormulario->addComponente($obIPopUpPAO);
$obFormulario->addComponente($obBscRubricaDespesaSal);
$obFormulario->addComponente($obCmbDotacao);
$obFormulario->defineBarraAba($arBotoesEventos);
$obFormulario->addSpan($obSpnConfiguracoesEventos);
$obFormulario->defineBarra(array($obBtnOk,$obBtnLimpar));
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

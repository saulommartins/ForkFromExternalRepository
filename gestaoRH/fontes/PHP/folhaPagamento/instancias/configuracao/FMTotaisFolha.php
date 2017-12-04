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
/*
* Formulário para configuração de totais da folha
* Data de Criação   : 04/03/2009

* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

* @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

$stPrograma = "TotaisFolha";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::remove("arConfiguracoes");
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTotaisFolhaEventos.class.php"                        );
include_once ( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoTotaisFolha.class.php"                        );
$obTFolhaPagamentoTotaisFolhaEventos = new TFolhaPagamentoTotaisFolhaEventos();
$obTFolhaPagamentoConfiguracaoTotaisFolha = new TFolhaPagamentoConfiguracaoTotaisFolha();
$obTFolhaPagamentoConfiguracaoTotaisFolha->recuperaTodos($rsConfiguracoesTotais);
$arConfiguracoes = $rsConfiguracoesTotais->getElementos();
foreach ($arConfiguracoes as $inIndex=>$arConfiguracao) {
    $stFiltro = " WHERE cod_configuracao = ".$arConfiguracao["cod_configuracao"];
    $obTFolhaPagamentoTotaisFolhaEventos->recuperaTodos($rsEventos,$stFiltro);
    $arCodEventos = array();
    while (!$rsEventos->eof()) {
        $arCodEventos[] = $rsEventos->getCampo("cod_evento");
        $rsEventos->proximo();
    }
    $arConfiguracoes[$inIndex]["inId"]    = $inIndex;
    $arConfiguracoes[$inIndex]["eventos"] = $arCodEventos;
}
Sessao::write("arConfiguracoes",$arConfiguracoes);
$jsOnload = "executaFuncaoAjax('montaListaConfiguracoes');";

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $_REQUEST["stAcao"]                                                   );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setId                                	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$arComponentes = array();

$obTxtDescConfiguracao = new TextBox;
$obTxtDescConfiguracao->setRotulo("Nome da Configuração");
$obTxtDescConfiguracao->setName("descricao");
$obTxtDescConfiguracao->setId("descricao");
$obTxtDescConfiguracao->setTitle("Informe a descrição da configuração para o relatório de totais.");
$obTxtDescConfiguracao->setSize(60);
$obTxtDescConfiguracao->setMaxLength(60);
$obTxtDescConfiguracao->setNullBarra(false);
$arComponentes[] = $obTxtDescConfiguracao;

include_once(CAM_GRH_FOL_COMPONENTES."ISelectMultiploEvento.class.php");
$obISelectMultiploEvento = new ISelectMultiploEvento;
$obISelectMultiploEvento->setNullBarra(false);
$obISelectMultiploEvento->setProventos();
$obISelectMultiploEvento->setDescontos();
$obISelectMultiploEvento->setOrdem("codigo");
$obISelectMultiploEvento->montarEventosDisponiveis();
$arComponentes[] = $obISelectMultiploEvento;

$obSpnConfiguracoes = new Span();
$obSpnConfiguracoes->setId("spnConfiguracoes");

$stJs  = "var url = '".CAM_GRH_FOL_INSTANCIAS."configuracao/OCTotaisFolha.php?".Sessao::getId()."' \n";
$stJs .= "selecionaTodosSelect(inCodEventoSelecionados); \n";
$stJs .= "selecionaTodosSelect(inCodEventoSelecionados); \n";

$obBtnIncluir = new Button();
$obBtnIncluir->setValue("Incluir");
$obBtnIncluir->setName("btIncluirConfiguracao");
$obBtnIncluir->setId("btIncluirConfiguracao");
$obBtnIncluir->obEvento->setOnclick(" $stJs
                                      jQuery('#stCtrl').val('incluirConfiguracao');
                                      jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                      limpaFormulario(); executaFuncaoAjax('montaListaConfiguracoes');
                                    ");

$obBtnAlterar = new Button();
$obBtnAlterar->setValue("Alterar");
$obBtnAlterar->setName("btAlterarConfiguracao");
$obBtnAlterar->setId("btAlterarConfiguracao");
$obBtnAlterar->obEvento->setOnclick(" $stJs
                                      jQuery('#stCtrl').val('alterarConfiguracao');
                                      jQuery.post(url, jQuery('#frm').serialize(),function (data) {eval(data);},'html');
                                      limpaFormulario(); executaFuncaoAjax('montaListaConfiguracoes');
                                    ");

$obBtnLimpar = new Button();
$obBtnLimpar->setValue("Limpar");
$obBtnLimpar->setName("btLimparConfiguracao");
$obBtnLimpar->setId("btLimparConfiguracao");
$obBtnLimpar->obEvento->setOnClick(" limpaFormulario(); executaFuncaoAjax('montaListaConfiguracoes');");

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addForm                              ( $obForm                                                               );
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addTitulo("Configuração Relatório Totais da Folha");
$obFormulario->addComponente($obTxtDescConfiguracao);
$obFormulario->addComponente($obISelectMultiploEvento);
$obFormulario->defineBarra(array($obBtnIncluir,$obBtnAlterar,$obBtnLimpar));
$obFormulario->addSpan($obSpnConfiguracoes);
$obFormulario->ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

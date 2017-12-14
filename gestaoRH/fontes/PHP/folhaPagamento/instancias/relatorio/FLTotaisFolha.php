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
* Formulário para totais da folha
* Data de Criação   : 05/03/2009

* @author Analista      Dagiane Vieira
* @author Desenvolvedor Diego Lemos de Souza

* @package URBEM
* @subpackage

* @ignore # só use se for paginas que o cliente visualiza, se for mapeamento ou classe de negocio não se usa
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php"                                   );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"									);
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php"										);

//Define o nome dos arquivos PHP
$stPrograma = "TotaisFolha";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName( "stAcao");
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName( "stCtrl");
$obHdnCtrl->setValue( $stCtrl );

$obIFiltroTipoFolha = new IFiltroTipoFolha();
$obIFiltroTipoFolha->setValorPadrao(1);

$obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);

$obIFiltroComponentes = new IFiltroComponentes;
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setRegSubFunEsp();
$obIFiltroComponentes->setGrupoRegSubFunEsp();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setGrupoAtributoServidor();
$obIFiltroComponentes->setAtributoPensionista();
$obIFiltroComponentes->setGrupoAtributoPensionista();
$obIFiltroComponentes->setFiltroPadrao("geral");
$jsOnload   = $obIFiltroComponentes->getOnLoad($jsOnload);

include_once ( CAM_GRH_PES_COMPONENTES."ISelectMultiploBanco.class.php"                                 );
$obISelectMultiploBanco  = new ISelectMultiploBanco(false);

$obChkAgrupar = new CheckBox();
$obChkAgrupar->setRotulo("Agrupamento");
$obChkAgrupar->setLabel("Agrupar");
$obChkAgrupar->setName("boAgruparBanco");
$obChkAgrupar->setValue("true");
$obChkAgrupar->setTitle("Marque para agrupar e quebrar página no relatório.");
$obChkAgrupar->obEvento->setOnChange("document.frm.boQuebrarBanco.disabled = !document.frm.boQuebrarBanco.disabled;");

$obChkQuebrarPagina = new CheckBox();
$obChkQuebrarPagina->setRotulo("Agrupamento");
$obChkQuebrarPagina->setLabel("Quebrar Página");
$obChkQuebrarPagina->setName("boQuebrarBanco");
$obChkQuebrarPagina->setValue("true");
$obChkQuebrarPagina->setTitle("Marque para agrupar e quebrar página no relatório.");
$obChkQuebrarPagina->setDisabled(true);

$obRdoAtivo = new CheckBox;
$obRdoAtivo->setName                        ( "stSituacaoAtivos"                                           );
$obRdoAtivo->setRotulo                      ( "Situação Servidor"                                    );
$obRdoAtivo->setLabel                       ( "Ativos"                                               );
$obRdoAtivo->setTitle                       ( "Selecione a situação do servidor para filtro."        );
$obRdoAtivo->setValue                       ( "A"                                                );

$obRdoRescindido = new CheckBox;
$obRdoRescindido->setName                      ( "stSituacaoRescindidos"                                             );
$obRdoRescindido->setRotulo                    ( "Situação Servidor"                                      );
$obRdoRescindido->setLabel                     ( "Rescindidos"                                            );
$obRdoRescindido->setTitle                     ( "Selecione a situação do servidor para filtro."          );
$obRdoRescindido->setValue                     ( "R"                                            );

$obRdoInativo = new CheckBox;
$obRdoInativo->setName                      ( "stSituacaoAposentados"                                               );
$obRdoInativo->setRotulo                    ( "Situação Servidor"                                        );
$obRdoInativo->setLabel                     ( "Aposentados"                                              );
$obRdoInativo->setTitle                     ( "Selecione a situação do servidor para filtro."            );
$obRdoInativo->setValue                     ( "P"                                                  );

$obRdoPensionista = new CheckBox;
$obRdoPensionista->setName                  ( "stSituacaoPensionistas"                                       );
$obRdoPensionista->setRotulo                ( "Situação Servidor"                                );
$obRdoPensionista->setLabel                 ( "Pensionistas"                                     );
$obRdoPensionista->setTitle                 ( "Selecione a situação do servidor para filtro."    );
$obRdoPensionista->setValue                 ( "E"                                      );

$obCmbOrdenacao = new Select;
$obCmbOrdenacao->setName                         ( "stOrdenacao"                  );
$obCmbOrdenacao->setValue                        ( 'A'                            );
$obCmbOrdenacao->setRotulo                       ( "Ordenação"                    );
$obCmbOrdenacao->setTitle                        ( "Selecione a ordenação."       );
$obCmbOrdenacao->addOption                       ( "A","Alfabética"               );
$obCmbOrdenacao->addOption                       ( "N","Numérica"                 );
$obCmbOrdenacao->setStyle                        ( "width: 250px"                 );

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoTotaisFolha.class.php");
$obTFolhaPagamentoConfiguracaoTotaisFolha = new TFolhaPagamentoConfiguracaoTotaisFolha();
$obTFolhaPagamentoConfiguracaoTotaisFolha->recuperaTodos($rsConfiguracoes);

$obCmbConfiguracao = new Select;
$obCmbConfiguracao->setName                         ( "inCodConfiguracaoTotais"                  );
$obCmbConfiguracao->setRotulo                       ( "Configuração"                    );
$obCmbConfiguracao->setTitle                        ( "Selecione a configuração para seleção dos eventos para filtro do relatório."       );
$obCmbConfiguracao->setStyle                        ( "width: 250px"                 );
$obCmbConfiguracao->addOption                       ( "","Selecione"               );
$obCmbConfiguracao->setCampoId("cod_configuracao");
$obCmbConfiguracao->setCampoDesc("descricao");
$obCmbConfiguracao->preencheCombo($rsConfiguracoes);

$obChkAgruparEventos = new CheckBox();
$obChkAgruparEventos->setRotulo("Agrupar Eventos por Elemento de Despesa");
$obChkAgruparEventos->setName("boAgruparEventos");
$obChkAgruparEventos->setValue("true");
$obChkAgruparEventos->setTitle("Marque para agrupar os valores dos eventos por elemento de despesa.");

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                              ( $pgProc );
$obForm->setTarget                              ( "oculto"                 );

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm( $obForm );
$obFormulario->addTitulo( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden( $obHdnAcao );
$obFormulario->addHidden( $obHdnCtrl );
$obFormulario->addTitulo( "Seleção do Filtro" );
$obIFiltroCompetencia->geraFormulario($obFormulario);
$obIFiltroTipoFolha->geraFormulario($obFormulario);
$obIFiltroComponentes->geraFormulario($obFormulario);
$obFormulario->agrupaComponentes( array($obRdoAtivo,$obRdoRescindido,$obRdoInativo,$obRdoPensionista) );
$obFormulario->addComponente( $obISelectMultiploBanco );
$obFormulario->addComponenteComposto($obChkAgrupar,$obChkQuebrarPagina);
$obFormulario->addComponente( $obCmbOrdenacao );
$obFormulario->addComponente($obCmbConfiguracao);
$obFormulario->addComponente($obChkAgruparEventos);
$obFormulario->ok(true);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

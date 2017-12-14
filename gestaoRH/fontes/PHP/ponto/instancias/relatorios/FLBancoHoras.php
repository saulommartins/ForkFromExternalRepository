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
    * Fornulário de Filtro para Relatório Banco de Horas
    * Data de Criação   : 10/12/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );
$stTitulo = $obRFolhaPagamentoFolhaSituacao->consultarCompetencia();
$dtCompetencia = $obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->getDtFinal();
$arCompetencia = explode("-",$dtCompetencia);
$dtInicial = "01/".$arCompetencia[1]."/".$arCompetencia[0];
$dtFinal   = $arCompetencia[2]."/".$arCompetencia[1]."/".$arCompetencia[0];
$dtSaldo   = date("d/m/Y",mktime (0, 0, 0, $arCompetencia[1]  , "01"-1, $arCompetencia[0]));

//Define o nome dos arquivos PHP
$stPrograma = "BancoHoras";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                                 ( "stAcao"                                          );
$obHdnAcao->setValue                                ( $stAcao                                           );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction                                  ( $pgProc                                           );

include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                  );
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setGeral(false);
$obIFiltroComponentes->setFiltroPadrao('contrato_todos');
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setDisabledQuebra();
$obIFiltroComponentes->setTodos();

$obPeriodo = new Periodo();
$obPeriodo->setRotulo("Período Leitura do Banco Dados");
$obPeriodo->setTitle("Informe o período para emissão do relatório.");
$obPeriodo->setNull(false);
$obPeriodo->obDataInicial->setValue($dtInicial);
$obPeriodo->obDataFinal->setValue($dtFinal);
$obPeriodo->obDataInicial->obEvento->setOnChange("montaParametrosGET('processarDataSaldo','stDataInicial')");

$obSaldo = new Data();
$obSaldo->setRotulo("Saldo do Banco de Horas Desde");
$obSaldo->setName("dtSaldoBanco");
$obSaldo->setId("dtSaldoBanco");
$obSaldo->setNull(false);
$obSaldo->setValue($dtSaldo);
$obSaldo->obEvento->setOnChange("montaParametrosGET('validarDataSaldo','stDataInicial,dtSaldoBanco')");

$obRdoAlfabetica = new Radio();
$obRdoAlfabetica->setRotulo("Ordenação dos Servidores");
$obRdoAlfabetica->setLabel("Alfabética");
$obRdoAlfabetica->setName('boOrdenacaoAlfabetica');
$obRdoAlfabetica->setValue(1);
$obRdoAlfabetica->setChecked(true);

$obRdoNumerica = new Radio();
$obRdoNumerica->setLabel("Numérica");
$obRdoNumerica->setName('boOrdenacaoAlfabetica');
$obRdoNumerica->setValue(0);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $stTitulo ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obFormulario->addTitulo            ( "Seleção do Filtro"           );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroComponentes->getOnload($jsOnload);
$obFormulario->addComponente($obPeriodo);
$obFormulario->addComponente($obSaldo);
$obFormulario->agrupaComponentes( array($obRdoAlfabetica,$obRdoNumerica) );
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Arquivo de Filtro de Relação de Diárias
    * Data de Criação: 07/08/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.09.03

    $Id: FLRelacaoDiarias.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoFolhaSituacao.class.php"                             );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                       );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao( new RFolhaPagamentoPeriodoMovimentacao );

//Define o nome dos arquivos PHP
$stPrograma = "RelacaoDiarias";
$pgFilt      = "FL".$stPrograma.".php";
$pgList      = "LS".$stPrograma.".php";
$pgForm      = "FM".$stPrograma.".php";
$pgProc      = "PR".$stPrograma.".php";
$pgOcul      = "OC".$stPrograma.".php";
$pgJS        = "JS".$stPrograma.".js";

Sessao::remove("link");
$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName ( "stAcao" );
$obHdnAcao->setValue( $stAcao );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ( $pgProc );

include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setCargo();
$obIFiltroComponentes->setFuncao();
$obIFiltroComponentes->setAtributoServidor();
$obIFiltroComponentes->setGrupoLotacao();
$obIFiltroComponentes->setGrupoLocal();
$obIFiltroComponentes->setGrupoRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setGrupoCargo();
$obIFiltroComponentes->setGrupoFuncao();
$obIFiltroComponentes->setGrupoAtributoServidor();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);

$obPeriodoPagamento = new Periodo();
$obPeriodoPagamento->setRotulo("Período de Pagamento");
$obPeriodoPagamento->setTitle("Informe o período de pagamento para filtro das diárias.");
$obPeriodoPagamento->obDataInicial->setValue($rsPeriodoMovimentacao->getCampo("dt_inicial"));
$obPeriodoPagamento->obDataFinal->setValue($rsPeriodoMovimentacao->getCampo("dt_final"));

$obPeriodoViagem = new Periodo();
$obPeriodoViagem->setRotulo("Período de Viagem");
$obPeriodoViagem->setTitle("Informe o período de viagem para filtro das diárias.");
$obPeriodoViagem->obDataInicial->setValue("");
$obPeriodoViagem->obDataInicial->setName('stDataInicialViagem');
$obPeriodoViagem->obDataFinal->setValue("");
$obPeriodoViagem->obDataFinal->setName('stDataFinalViagem');

include_once(CAM_GRH_DIA_COMPONENTES."ISelectTipoDiaria.class.php");
$obISelectTipoDiaria = new ISelectTipoDiaria();

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addTitulo                            ( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right"  );
$obFormulario->addForm                              ( $obForm                                           );
$obFormulario->addHidden                            ( $obHdnAcao                                        );
$obIFiltroComponentes->geraFormulario($obFormulario);
#$obFormulario->addComponente($obPeriodoPagamento);
$obFormulario->addComponente($obISelectTipoDiaria);
$obFormulario->addComponente($obPeriodoViagem);
$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

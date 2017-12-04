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
    * Página de Filtro para Relatorio Espelho Ponto
    * Data de Criação: 15/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma    = "RelatorioEspelhoPonto";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao   = $_REQUEST["stAcao"];

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');

$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

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

$obRFolhaPagamentoFolhaSituacao->roRFolhaPagamentoPeriodoMovimentacao->listarUltimaMovimentacao($rsUltimaMovimentacao);

$obDataInicioPeriodo = new Data();
$obDataInicioPeriodo->setRotulo("Período");
$obDataInicioPeriodo->setTitle("Informe o periodo para emissão do relatório");
$obDataInicioPeriodo->setName("dtInicioPeriodo");
$obDataInicioPeriodo->setId("dtInicioPeriodo");
$obDataInicioPeriodo->setNull(false);
if($rsUltimaMovimentacao->getNumLinhas() > 0)
    $obDataInicioPeriodo->setValue($rsUltimaMovimentacao->getCampo('dt_inicial'));

$obDataFimPeriodo = new Data();
$obDataFimPeriodo->setName("dtFimPeriodo");
$obDataFimPeriodo->setId("dtFimPeriodo");
$obDataFimPeriodo->setNull(false);
if($rsUltimaMovimentacao->getNumLinhas() > 0)
    $obDataFimPeriodo->setValue($rsUltimaMovimentacao->getCampo('dt_final'));

$obLabelDataPeriodo = new Label();
$obLabelDataPeriodo->setValue("&nbsp;&nbsp;à&nbsp;&nbsp;");

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

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( 'telaPrincipal' );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addTitulo            ( "Seleção do Filtro"           );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroComponentes->getOnload($jsOnload);
$obFormulario->agrupaComponentes( array($obDataInicioPeriodo,$obLabelDataPeriodo,$obDataFimPeriodo) );
$obFormulario->agrupaComponentes( array($obRdoAlfabetica,$obRdoNumerica) );
$obFormulario->Ok();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

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
    * Página de Filtro para Manter Vinculo de Escalas
    * Data de Criação: 09/10/2008

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: $

    * Casos de uso: uc-04.10.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_COMPONENTES."IBuscaInnerEscala.class.php"                                   );
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma    = "ManterVinculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

if (isset($_REQUEST['stAcao'])) {
    $stAcao = $request->get('stAcao');
} else {
    $link = Sessao::read('link');
    if ( is_array($link) ) {
        $stAcao = $link['stAcao'];
        Sessao::remove('link');
    }
}

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                         );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName('stCtrl');

$obHdnAcao = new Hidden;
$obHdnAcao->setName('stAcao');
$obHdnAcao->setValue($stAcao);

$obBuscaInnerEscala = new IBuscaInnerEscala();
if ($stAcao == 'incluir') {
    $obBuscaInnerEscala->obBscEscala->setNull(false);
}

$obIFiltroComponentes = new IFiltroComponentes();
$obIFiltroComponentes->setMatricula();
$obIFiltroComponentes->setCGMMatricula();
$obIFiltroComponentes->setLotacao();
$obIFiltroComponentes->setLocal();
$obIFiltroComponentes->setRegimeSubDivisaoFuncao();
$obIFiltroComponentes->setGeral(false);

if ($stAcao == "incluir") {
    $obIFiltroComponentes->setAtivos();
    $obIFiltroComponentes->setFiltroPadrao('contrato');
} else {
    $obIFiltroComponentes->setTodos();
    $obIFiltroComponentes->setFiltroPadrao('contrato_todos');
}

$obDataInicioPeriodo = new Data();
$obDataInicioPeriodo->setRotulo("Período");
$obDataInicioPeriodo->setTitle("Informe o periodo para filtro");
$obDataInicioPeriodo->setName("dtInicioPeriodo");
$obDataInicioPeriodo->setId("dtInicioPeriodo");
$obDataInicioPeriodo->setNull(false);

$obDataFimPeriodo = new Data();
$obDataFimPeriodo->setName("dtFimPeriodo");
$obDataFimPeriodo->setId("dtFimPeriodo");
$obDataFimPeriodo->setNull(false);

$obLabelDataPeriodo = new Label();
$obLabelDataPeriodo->setValue("&nbsp;&nbsp;à&nbsp;&nbsp;");

$obForm = new Form;
$obForm->setAction( ($stAcao == "incluir")?$pgProc:$pgList );
$obForm->setTarget( ($stAcao == "incluir")?'oculto':'telaPrincipal' );

$obFormulario = new Formulario;
$obFormulario->addForm              ( $obForm                       );
$obFormulario->addTitulo			( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addHidden            ( $obHdnCtrl                    );
$obFormulario->addHidden            ( $obHdnAcao                    );
$obFormulario->addTitulo            ( "Escala"                      );
$obBuscaInnerEscala->geraFormulario($obFormulario);
$obFormulario->addTitulo            ( "Seleção do Filtro"           );
$obIFiltroComponentes->geraFormulario($obFormulario);
$obIFiltroComponentes->getOnload($jsOnload);

$obBtnOk = new Ok();
if($stAcao == "incluir")
    $obBtnOk->obEvento->setOnClick("BloqueiaFrames(true,false); Salvar();");

$obBtnLimpar = new Limpar();

if ($stAcao !== "incluir") {
    $obFormulario->agrupaComponentes( array($obDataInicioPeriodo,$obLabelDataPeriodo,$obDataFimPeriodo) );
}

$obFormulario->defineBarra(array($obBtnOk, $obBtnLimpar));
$obFormulario->show();

include_once($pgJs);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

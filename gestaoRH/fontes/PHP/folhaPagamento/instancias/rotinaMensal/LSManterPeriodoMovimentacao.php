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
* Página de exclusão do Periodo de Movimentacao
* Data de Criação: 27/10/2005

* @author Analista: Vandre Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @ignore

$Revision: 30566 $
$Name$
$Author: andre $
$Date: 2007-06-04 10:30:34 -0300 (Seg, 04 Jun 2007) $

* Casos de uso: uc-04.05.40
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"   );

//Define o nome dos arquivos PHP
$stPrograma = "ManterPeriodoMovimentacao";
$pgList     = "LS".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

include_once($pgJS);
include_once($pgOcul);

$stCaminho = CAM_GRH_FOL_INSTANCIAS."rotinaMensal/";

$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

$rsLista = new RecordSet;
$obRPeriodoMovimentacao = new RFolhaPagamentoPeriodoMovimentacao;
$obRPeriodoMovimentacao->listarUltimaMovimentacao($rsLista);

//Se não tiver nenhum registro
if ($rsLista->getNumLinhas() > 0) {
    $arLista = array();
    $arLista[0]['cod_periodo_movimentacao'] = $rsLista->getCampo('cod_periodo_movimentacao');
    $arLista[0]['dt_inicial'] = $rsLista->getCampo('dt_inicial');
    $arLista[0]['dt_final']   = $rsLista->getCampo('dt_final');
    if ($rsLista->getCampo('situacao') == 'a' )
        $arLista[0]['situacao']   = 'Aberto';
    else
        $arLista[0]['situacao']   = 'Fechado';

    $rsLista = new RecordSet;
    $rsLista->preenche($arLista);
}

$obLista = new Lista;
$obRFolhaPagamentoFolhaSituacao->consultarCompetencia();
$obLista->setRecordSet          ( $rsLista );
$stTitulo = ' </div></td></tr><tr><td colspan="5" class="alt_dados">Período Aberto';
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia().$stTitulo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Inicial" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Data Final" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Situação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 3 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_inicial" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "dt_final" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$obLista->ultimaAcao->setAcao( "excluir" );
//$btnteste = $obLista->ultimaAcao->getBotao();
//$btnteste->obEvento->setOnClick                  ( "buscaValorFiltro('submeter');"                       );
//$btnteste = $obLista->ultimaAcao->setBotao($btnteste);
$obLista->ultimaAcao->addCampo( "&inCodPeriodoMovimentacao" , "cod_periodo_movimentacao" );
$obLista->ultimaAcao->addCampo( "stDescQuestao", "ATENÇÃO! Ao confirmar a exclusão do período, todos os dados relativos à geração da folha de pagamento do período que está aberto serão perdidas! Período [dt_inicial] - [dt_final]" );

$obLista->ultimaAcao->setLink( $stCaminho.$pgProc."?".Sessao::getId().$stLink."&stAcao=excluir" );
//$obLista->ultimaAcao->setLink( $stCaminho.$pgOcul."?".Sessao::getId().$stLink."&stCtrl=excluir& " );

$obLista->commitAcao();

$obLista->show();
?>

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
    * Lista de Alterar Assentamento Gerado
    * Data de Criação: 09/05/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Revision: 32866 $
    $Name$
    $Author: tiago $
    $Date: 2007-07-19 12:24:20 -0300 (Qui, 19 Jul 2007) $

    * Casos de uso: uc-04.04.14
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGeracaoAssentamento.class.php"                              );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalContrato.class.php"                                      );

//Define o nome dos arquivos PHP
$stPrograma = "ManterGeracaoAssentamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCaminho = CAM_GRH_PES_INSTANCIAS."assentamento/";

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$obRPessoalGeracaoAssentamento = new RPessoalGeracaoAssentamento;
$rsContrato = new recordset;
if ($request->get('inContrato') != "") {
    $obTPessoalContrato = new TPessoalContrato;
    $stFiltro = " WHERE registro = ".$request->get('inContrato');
    $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltro);
}

$stLink .= '&inCodLotacao='      .$request->get('inCodLotacao');
$stLink .= '&inCodAssentamento=' .$request->get('inCodAssentamento');
$stLink .= '&inContrato='        .$request->get('inContrato');
$stLink .= '&boCargoExercido='   .$request->get('boCargoExercido');
$stLink .= '&inCodCargo='        .$request->get('inCodCargo');
$stLink .= '&inCodEspecialidade='.$request->get('inCodEspecialidade');
$stLink .= '&boFuncaoExercida='  .$request->get('boFuncaoExercida');
$stLink .= '&stDataInicial='     .$request->get('stDataInicial');
$stLink .= '&stDataFinal='       .$request->get('stDataFinal');
$stLink .= '&stModoGeracao='     .$request->get('stModoGeracao');
$stLink .= '&HdninCodLotacao='   .$request->get('HdninCodLotacao');


$stFiltroPaginacao = '';
//MANTEM FILTRO E PAGINACAO
$arLink = Sessao::read('link');
if ($_GET["pg"] and  $_GET["pos"]) {
    $arLink["pg"]  = $_GET["pg"];
    $arLink["pos"] = $_GET["pos"];

    $stFiltroPaginacao = "&pg=".$request->get("pg")."&pos=".$request->get("pos");
}

$rsLista = new RecordSet;
$arFiltros['inCodAssentamento'] = $request->get('inCodAssentamento');
$arFiltros['inCodClassificacao'] = $request->get('inCodClassificacao');
$arFiltros['inCodContrato']     = $rsContrato->getCampo("cod_contrato");
if ($request->get('boCargoExercido')) {
    $arFiltros['inCodCargo']        = $request->get('inCodCargo');
    $arFiltros['inCodEspecialidade']= $request->get('inCodEspecialidade');
}
if ($request->get('boFuncaoExercida')) {
    $arFiltros['inCodFuncao']             = $request->get('inCodCargo');
    $arFiltros['inCodEspecialidadeFuncao']= $request->get('inCodEspecialidade');
}
$arFiltros['inCodLotacao']       = $request->get('inCodLotacao');
$arFiltros['dtPeriodoInicial2']  = $request->get('stDataInicial');
$arFiltros['dtPeriodoFinal2']    = $request->get('stDataFinal');

$obRPessoalGeracaoAssentamento->listarAssentamentoServidor( $rsLista,$arFiltros,$stOrdem );
$request->set('dtInicial', $rsLista->getCampo('dt_inicial') );
$request->set('dtFinal',$rsLista->getCampo('dt_final') );

$stLink .= "&dtInicial=".$rsLista->getCampo('dt_inicial');
$stLink .= "&dtFinal=".$rsLista->getCampo('dt_final');

$stOrdem = " nom_cgm,cod_contrato,assentamento_gerado.periodo_inicial";

$stLink .= "&stAcao=".$stAcao;
$obLista = new Lista;
$obLista->setTitulo             ('<div align="right">'.$obRFolhaPagamentoFolhaSituacao->consultarCompetencia());
$obLista->obPaginacao->setFiltro("&stLink=".$stLink );
$obLista->setRecordSet( $rsLista );
$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Matrícula");
$obLista->ultimoCabecalho->setWidth( 1 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Servidor");
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Assentamento");
$obLista->ultimoCabecalho->setWidth( 17 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Período");
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("Situação");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 2 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("DIREITA");
$obLista->ultimoDado->setCampo( "registro" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "[numcgm]-[nom_cgm]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "descricao_assentamento" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "[periodo_inicial] a [periodo_final]" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("CENTRO");
$obLista->ultimoDado->setCampo( "situacao" );
$obLista->commitDado();

$obLista->addAcao();
$stAcao = ( $stAcao == "excluir" ) ? "Excluir" : $stAcao;
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->addCampo("&inCodAssentamentoGerado","cod_assentamento_gerado");
$obLista->ultimaAcao->addCampo("&inRegistro","registro");
$obLista->ultimaAcao->setLinkId("botaoAcao");
$obLista->ultimaAcao->setLink( $pgForm."?".Sessao::getId().$stLink.$stFiltroPaginacao );
$obLista->commitAcao();
$obLista->show();

?>

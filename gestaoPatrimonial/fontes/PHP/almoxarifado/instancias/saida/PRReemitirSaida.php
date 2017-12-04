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
    * Página de processamento
    * Data de Criação: 27/03/2009

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Grasiele Torres

    * @package URBEM
    * @subpackage

    * @ignore

    * Caso de uso: uc-03.03.32

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_MAPEAMENTO.'TAlmoxarifadoNaturezaLancamento.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ReemitirSaida";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

Sessao::setTrataExcecao( true );

$stAcao = $request->get('stAcao');

$inNumLancamento    = $_REQUEST['inNumLancamento'];
$inCodNatureza      = $_REQUEST['inCodNatureza'];
$exercicioReemissao = $_REQUEST['stExercicioLancamento'];

$obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
$obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'S');
$obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza'  , $inCodNatureza);
$obTAlmoxarifadoNaturezaLancamento->setDado('num_lancamento', $inNumLancamento);
$obTAlmoxarifadoNaturezaLancamento->setDado('exercicio_lancamento'  , $exercicioReemissao);

switch ($inCodNatureza) {
    //Saída por Transferência
    case 2:
        $obTAlmoxarifadoNaturezaLancamento->recuperaTotalPaginaSaida($totalPagina);
        $total_pagina = $totalPagina->getCampo('total_pagina');

        $stCaminho = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraMovimentacaoTransferencia.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento."&inTotalPagina=".$total_pagina.'&exercicioReemissao='.$exercicioReemissao, "Saída por Transferência"." ","incluir","aviso", Sessao::getId(), "../");
        break;

    //Saída por Requisição
    case 7:
        $stGrupo = 	"GROUP BY natureza_lancamento.num_lancamento
                        , natureza_lancamento.TIMESTAMP
                        , natureza.descricao
                        , natureza_lancamento.exercicio_lancamento
                        , lancamento_requisicao.cod_almoxarifado
                        , lancamento_requisicao.cod_requisicao       \n";
        $obTAlmoxarifadoNaturezaLancamento->recuperaDadosReemissaoSaidaRequisicao($rsSaidaRequisicao, $stFiltro, $stGrupo, $stOrdem);
        $inCodAlmoxarifado = $rsSaidaRequisicao->getCampo('cod_almoxarifado');
        $inCodRequisicao   = $rsSaidaRequisicao->getCampo('cod_requisicao');

        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraRelatorioSaida.php?'.Sessao::getId().'&stAcao='.$stAcao;
        $stParametros = '&inCodAlmoxarifado='.$inCodAlmoxarifado.'&inNumLancamento='.$inNumLancamento.'&inCodRequisicao='.$inCodRequisicao.'&exercicioReemissao='.$_REQUEST['stExercicio'];
        SistemaLegado::alertaAviso($stCaminho.$stParametros, "Saída por Requisição"."","incluir","aviso", Sessao::getId(), "../");
        break;

    //Saída Diversa
    case 9:
        $stCaminho = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao, "Saída Diversa"." ","incluir","aviso", Sessao::getId(), "../");
        break;

    //Saída por Estorno de Entrada
    case 10:
        $obTAlmoxarifadoNaturezaLancamento->recuperaDadosReemissaoSaidaEstornoEntrada($rsSaidaEstornoEntrada, $stFiltro);
        $inCodLancamentoEntrada = $rsSaidaEstornoEntrada->getCampo('cod_lancamento');
        $inNumLancamentoEntrada = $rsSaidaEstornoEntrada->getCampo('num_lancamento');

        $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraEstornoEntrada.php?'.Sessao::getId().'&stAcao='.$stAcao;
        $stParametros = '&inNumLancamento='.$inNumLancamento.'&inCodLancamentoEntrada='.$inCodLancamentoEntrada.'&exercicioReemissao='.$exercicioReemissao;
        SistemaLegado::alertaAviso($stCaminho.$stParametros, "Saída por Estorno de Entrada"."","incluir","aviso", Sessao::getId(), "../");
        break;

    //Saída por Abastecimento
    case 12:
        $stCaminho = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraSaidaAutorizacaoAbastecimento.php?'.Sessao::getId().'&stAcao='.$stAcao;
        SistemaLegado::alertaAviso($stCaminho.'&inNumLancamento='.$inNumLancamento.'&exercicioReemissao='.$exercicioReemissao, "Saída por Abastecimento"." ","incluir","aviso", Sessao::getId(), "../");
        break;

}
Sessao::encerraExcecao();

?>

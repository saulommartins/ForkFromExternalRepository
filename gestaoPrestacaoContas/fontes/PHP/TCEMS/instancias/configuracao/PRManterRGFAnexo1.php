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
    * Página de Processamento da Configuração do Anexo 1
    * Data de Criação   : 25/07/2011

    * @author Desenvolvedor: Davi Ritter Aroldi
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEMS_MAPEAMENTO."TTCEMSDespesasNaoComputadas.class.php");
include_once(CAM_GPC_TCEMS_MAPEAMENTO."TTCEMSReceitaCorrenteLiquida.class.php");

$stPrograma = "ManterRGFAnexo1";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

//inclui as despesas não computadas no banco de dados
$arListaDespesa = Sessao::read('arListaDespesa');

$obTTCEMSDespesasNaoComputadas = new TTCEMSDespesasNaoComputadas();
$obTTCEMSDespesasNaoComputadas->setDado('exercicio', Sessao::read('exercicio'));
$obTTCEMSDespesasNaoComputadas->excluir();

if (count($arListaDespesa)) {
    foreach ($arListaDespesa as $despesa) {
        $obTTCEMSDespesasNaoComputadas = new TTCEMSDespesasNaoComputadas();
        $obTTCEMSDespesasNaoComputadas->setDado('exercicio', $despesa['stExercicio']);
        $obTTCEMSDespesasNaoComputadas->setDado('descricao', $despesa['stDescricao']);
        $obTTCEMSDespesasNaoComputadas->setDado('quadrimestre1', $despesa['nuQuadrimestreValor1'] ? $despesa['nuQuadrimestreValor1'] : 0.00);
        $obTTCEMSDespesasNaoComputadas->setDado('quadrimestre2', $despesa['nuQuadrimestreValor2'] ? $despesa['nuQuadrimestreValor2'] : 0.00);
        $obTTCEMSDespesasNaoComputadas->setDado('quadrimestre3', $despesa['nuQuadrimestreValor3'] ? $despesa['nuQuadrimestreValor3'] : 0.00);
        if ($despesa['inId']) {
            $obTTCEMSDespesasNaoComputadas->setDado('id', $despesa['inId']);
        } else {
            $obTTCEMSDespesasNaoComputadas->setDado('id', $obTTCEMSDespesasNaoComputadas->proximoId());
        }

        $obTTCEMSDespesasNaoComputadas->inclusao();
    }
}

//inclui a receita corrente líquida no banco de dados
for ($count = 1; $count <= 3; $count++) {
    if ($_REQUEST['nuQuadrimestre'.$count]) {
        $nuQuadrimestre = $_REQUEST['nuQuadrimestre'.$count];
        $inMesInicial = (($count - 1) * 4) + 1;
        $inMesFinal = $count * 4;

        $flQuadrimestre = str_replace('.', '', $nuQuadrimestre);
        $flQuadrimestre = str_replace(',', '.', $flQuadrimestre);
        $nuValorMes = number_format(($flQuadrimestre / 4), 2, '.', '');

        //calcula e trunca as as quatro casas decimas que possam existir na divisão
        $nuResto = 0.00;
        if ($flQuadrimestre != ($nuValorMes * 4)) {
            $nuResto = $flQuadrimestre - ($nuValorMes * 4);
            $nuResto = round($nuResto, 2);
        }

        $stFiltroMes = '';
        for ($i = $inMesInicial; $i <= $inMesFinal; $i++) {
            $stFiltroMes .= ','.$i;
        }
        $stFiltroMes = substr($stFiltroMes, 1);

        $stFiltro = " WHERE exercicio = '".Sessao::read('exercicio')."' AND mes in (".$stFiltroMes.") ";

        $obTTCEMSReceitaCorrenteLiquida = new TTCEMSReceitaCorrenteLiquida();
        $obTTCEMSReceitaCorrenteLiquida->recuperaTodos($rsQuadrimestre, $stFiltro);

        $obTTCEMSReceitaCorrenteLiquida->setDado('ano', $_REQUEST['stExercicio']);
        $obTTCEMSReceitaCorrenteLiquida->setDado('exercicio', Sessao::read('exercicio'));
        if ($rsQuadrimestre->getNumLinhas() > 0) {
            //alteração
            while (!$rsQuadrimestre->eof()) {
                $obTTCEMSReceitaCorrenteLiquida->setDado('mes', $rsQuadrimestre->getCampo('mes'));
                $obTTCEMSReceitaCorrenteLiquida->setDado('valor', $nuValorMes);
                if ($rsQuadrimestre->getCampo('mes') == $inMesFinal) {
                    $obTTCEMSReceitaCorrenteLiquida->setDado('valor', $nuValorMes+$nuResto);
                }
                $obTTCEMSReceitaCorrenteLiquida->alteracao();
                $rsQuadrimestre->proximo();
            }
        } else {
            //inclusão
            for ($i = $inMesInicial; $i <= $inMesFinal; $i++) {
                $obTTCEMSReceitaCorrenteLiquida->setDado('mes', $i);
                $obTTCEMSReceitaCorrenteLiquida->setDado('valor', $nuValorMes);
                if ($i == $inMesFinal) {
                    $obTTCEMSReceitaCorrenteLiquida->setDado('valor', $nuValorMes+$nuResto);
                }
                $obTTCEMSReceitaCorrenteLiquida->inclusao();
                $rsQuadrimestre->proximo();
            }
        }
    }
}

SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=configurar","RGF Anexo 1 configurado com sucesso!","manter","aviso", Sessao::getId(), "../");

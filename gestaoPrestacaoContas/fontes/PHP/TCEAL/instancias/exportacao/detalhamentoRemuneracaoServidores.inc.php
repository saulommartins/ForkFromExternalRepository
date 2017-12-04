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

    * Página de Include Oculta - Exportação Arquivos Relacionais - DetalhamentoRemuneracaoServidores.xml
    *
    * Data de Criação: 02/06/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: detalhamentoRemuneracaoServidores.inc.php 60769 2014-11-14 12:08:14Z jean $
    *
    * @ignore
    *
*/

include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALDetalhamentoRemuneracaoServidores.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TExportacaoRelacionais.class.php';


$obTTCEALDetalhamentoRemuneracaoServidores = new TTCEALDetalhamentoRemuneracaoServidores();
$obTExportacaoRelacionais = new TExportacaoRelacionais();

$undGestora = explode(',', $stEntidades);
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$arEsquemasEntidades   = array();
$stNomeArquivo         = "DetalhamentoRemuneracaoServidores";

foreach ($undGestora as $inCodEntidade) {
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);
    if ($rsEsquema->getNumLinhas() > 0 || $codEntidadePrefeitura ==$inCodEntidade ) {
        $arEsquemasEntidades[] = $inCodEntidade;
    } 
}

foreach ($arEsquemasEntidades as $inCodEntidade) {
    if ($codEntidadePrefeitura != $inCodEntidade) {
        $stEntidade = '_'.$inCodEntidade;
        $entidade = $inCodEntidade;        
    } else {
        $stEntidade = '';
    }
    
    if ($inCodEntidade ==  $codEntidadePrefeitura) {
        $inCodEntidade='';
        $entidade= $codEntidadePrefeitura;
    }
    
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stFiltroPeriodo = " AND dt_final BETWEEN TO_DATE('".$dtInicial."', 'dd/mm/yyyy') AND TO_DATE('".$dtFinal."', 'dd/mm/yyyy')";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);
    $arResult = array();
    $idCount = 0;
    foreach ($rsPeriodoMovimentacao->getElementos() as $arPeriodo) { 
        $arMes = explode('/',$arPeriodo ["dt_inicial"]);
        $mes = $arMes[1];

        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('stExercicio', Sessao::getExercicio());
        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('inCodEntidade', $entidade );
        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('inBimestre', $inBimestre );
        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('cod_periodo_movimentacao', $arPeriodo["cod_periodo_movimentacao"] );
        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('mes', $mes  );
        $obTTCEALDetalhamentoRemuneracaoServidores->setDado('stEntidade', $stEntidade );
        $obTTCEALDetalhamentoRemuneracaoServidores->recuperaListarServidores($rsServidores);
    
        foreach($rsServidores->getElementos() as $arServidores){
              
            $arResult[$idCount]['CodUndGestora']    = $arServidores['cod_und_gestora'];
            $arResult[$idCount]['CodigoUA']         = $arServidores['codigo_ua'];
            $arResult[$idCount]['Bimestre']         = $arServidores['bimestre'];
            $arResult[$idCount]['Exercicio']        = $arServidores['exercicio'];
            $arResult[$idCount]['Cpf']              = $arServidores['cpf'];
            $arResult[$idCount]['Matricula']        = $arServidores['registro'];
            $arResult[$idCount]['CargaHoraria']     = $arServidores['horas_semanais'];         
            $arResult[$idCount]['CodRubricaSal']    = $arServidores['codigo'];
            $arResult[$idCount]['Valor']            = $arServidores['valor'];
            $arResult[$idCount]['CodMesReferencia'] = $arServidores['mes'];
            $idCount++;     
        }
    }

    return $arResult;
}

?>
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

    * Página de Include Oculta - Exportação Arquivos Relacionais - FuncoesServidores.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: funcoesServidores.inc.php 64853 2016-04-07 18:27:30Z carlos.silva $
    *
    * @ignore
    *
*/
   
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALFuncoesServidores.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TExportacaoRelacionais.class.php';
        
$undGestora = explode(',', $stEntidades);
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());

$obTTCEALFuncoesServidores = new TTCEALFuncoesServidores();
$obTExportacaoRelacionais = new TExportacaoRelacionais();

$stNomeArquivo ="FuncoesServidores";
$arResult = array();
$idCount = 0;

foreach ($undGestora as $inCodEntidade) {
    $stEntidade = '';
    
    $obTEntidade = new TEntidade();
    $stFiltro = " WHERE nspname = 'pessoal_".$inCodEntidade."'";
    $obTEntidade->recuperaEsquemasCriados($rsEsquema,$stFiltro);

    if(($rsEsquema->getNumLinhas() > 0)&&($codEntidadePrefeitura !=$inCodEntidade)){
        $stEntidade = '_'.$inCodEntidade;    
    }
    
    $entidade = $inCodEntidade;

    if ($inCodEntidade == $codEntidadePrefeitura || $stEntidade=='') {
        $inCodEntidade='';
    }else{
        $inCodEntidade=$stEntidade;   
    }

    $stPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stPeriodoMovimentacao->setDado('dtInicial', $dtInicial);
    $stPeriodoMovimentacao->setDado('dtFinal', $dtFinal  );
    $stPeriodoMovimentacao->recuperaPeriodoMovimentacaoTCEAL($rsPeriodoMovimentacao);
    
    if($rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') != '') {
        $rsRecordSet = "rsFuncoesServidores";
        $rsRecordSet .= $stEntidade;
        $$rsRecordSet = new RecordSet();
        
        $obTTCEALFuncoesServidores->setDado('dtInicial', $dtInicial);
        $obTTCEALFuncoesServidores->setDado('dtFinal', $dtFinal);
        $obTTCEALFuncoesServidores->setDado('codPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
        $obTTCEALFuncoesServidores->setDado('stExercicio', Sessao::getExercicio());
        $obTTCEALFuncoesServidores->setDado('stEntidade', $stEntidade);
        $obTTCEALFuncoesServidores->setDado('inCodEntidade', $inCodEntidade);
        $obTTCEALFuncoesServidores->setDado('entidade', $entidade);
    
        $obTTCEALFuncoesServidores->listarExportacaoFuncoesServidores($rsRecordSet);
    
        while (!$rsRecordSet->eof()) {
            $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
            $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
            $arResult[$idCount]['Bimestre'] = $inBimestre;
            $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
            $arResult[$idCount]['CodFuncao'] = $rsRecordSet->getCampo('cod_funcao');
            $arResult[$idCount]['Descricao'] = $rsRecordSet->getCampo('descricao');
            
            $idCount++;
            $rsRecordSet->proximo();
        }
    }
}
?>
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

    * Página de Include Oculta - Exportação Arquivos Relacionais - cargosServidores.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Carolina Schwaab Marçal
    *
    * $Id: cargosServidores.inc.php 64853 2016-04-07 18:27:30Z carlos.silva $
    *
    * @ignore
    *
*/

include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALCargosServidores.class.php';
include_once CAM_GPC_TCEAL_NEGOCIO.'RExportacaoRelacionais.class.php';
        
$obTExportacaoRelacionais = new TExportacaoRelacionais();
$obTTCEALCargosServidores = new TTCEALCargosServidores();

$undGestora            = explode(',', $stEntidades);
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'CargosServidores';
$arEsquemasEntidades   = array();

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
        $inCodEntidade ='';
        $entidade = $codEntidadePrefeitura;
    }

    $stPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stPeriodoMovimentacao->setDado('dtInicial', $dtInicial);
    $stPeriodoMovimentacao->setDado('dtFinal', $dtFinal);
    $stPeriodoMovimentacao->recuperaPeriodoMovimentacaoTCEAL($rsPeriodoMovimentacao);

    if($rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') != '') {
        $rsRecordSet = "rsCargosServidores";
        $rsRecordSet .= $stEntidade;
        $$rsRecordSet = new RecordSet();
    
        $obTTCEALCargosServidores->setDado('dtInicial', $dtInicial);
        $obTTCEALCargosServidores->setDado('dtFinal', $dtFinal);
        $obTTCEALCargosServidores->setDado('codPeriodoMovimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
        $obTTCEALCargosServidores->setDado('stExercicio', Sessao::getExercicio());
        $obTTCEALCargosServidores->setDado('stEntidade', $stEntidade);
        $obTTCEALCargosServidores->setDado('inCodEntidade', $inCodEntidade);
        $obTTCEALCargosServidores->setDado('entidade', $entidade);
    
        $obTTCEALCargosServidores->listarExportacaoCargosServidores($rsRecordSet);
       
        $stNomeArquivo = 'CargosServidores';
        $arResult = array();
        $idCount = 0;
    
        while (!$rsRecordSet->eof()) {
            $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
            $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
            $arResult[$idCount]['Bimestre'] = $inBimestre;
            $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
            $arResult[$idCount]['CodCargo'] = $rsRecordSet->getCampo('cod_cargo');
            $arResult[$idCount]['Descricao'] = $rsRecordSet->getCampo('descricao');
    
            $idCount++;
    
            $rsRecordSet->proximo();
        }
        
        return $arResult;
    }
}
   
?>
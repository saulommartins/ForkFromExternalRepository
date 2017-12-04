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

    
    
    * Data de Criação: 30/05/2014
    *
    * @author: Evandro Melos
    *
    * $Id: contaDisponibilidade.inc.php 65758 2016-06-15 20:05:35Z lisiane $
    *
    * @ignore
    *
*/

include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TExportacaoRelacionais.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALContaDisponibilidade.class.php';

$obTTCEALContaDisponibilidade   = new TTCEALContaDisponibilidade();
$obTExportacaoRelacionais       = new TExportacaoRelacionais();

$obTTCEALContaDisponibilidade->setDado('exercicio', Sessao::getExercicio());
$obTTCEALContaDisponibilidade->setDado('cod_entidade', $stEntidades );
$obTTCEALContaDisponibilidade->setDado('bimestre', $inBimestre );
$obTTCEALContaDisponibilidade->setDado('dt_inicial', $dtInicial );
$obTTCEALContaDisponibilidade->setDado('dt_final', $dtFinal );
$obTTCEALContaDisponibilidade->listarExportacaoContaDisponibilidade($rsRecordSet,"","",$boTransacao);

$stNomeArquivo = "ContaDisponibilidade";
$stVersao = "2.1";
$arResult = array();
$idCount = 0;

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao'] = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria'] = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodContaBalancete'] = $rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['CodRecVinculado'] = $rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['Tipo'] = $rsRecordSet->getCampo('tipo_conta');
    $arResult[$idCount]['CodBanco'] = $rsRecordSet->getCampo('cod_banco');
    $arResult[$idCount]['CodAgenciaBanco'] = $rsRecordSet->getCampo('cod_agencia_banco');
    $arResult[$idCount]['NumContaCorrente'] = $rsRecordSet->getCampo('num_conta_corrente');
    $arResult[$idCount]['Classificacao'] = $rsRecordSet->getCampo('classificacao');
    $idCount++;

    $rsRecordSet->proximo();
}

return $arResult;

?>
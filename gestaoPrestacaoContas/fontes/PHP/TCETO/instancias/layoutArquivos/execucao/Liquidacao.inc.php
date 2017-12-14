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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Liquidacao.xml
    *
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    $Id: Liquidacao.inc.php 60726 2014-11-11 19:48:00Z evandro $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOLiquidacao.class.php';

$obTMapeamento = new TTCETOLiquidacao();

$obTMapeamento->setDado('exercicio'     , Sessao::getExercicio());
$obTMapeamento->setDado('cod_entidade'  , $inCodEntidade        );
$obTMapeamento->setDado('und_gestora'   , $inCodEntidade        );
$obTMapeamento->setDado('dtInicial'     , $stDataInicial        );
$obTMapeamento->setDado('dtFinal'       , $stDataFinal          );
$obTMapeamento->setDado('bimestre'      , $inBimestre           );
$obTMapeamento->recuperaLiquidacao($rsRecordSet);

$idCount=0;
$stNomeArquivo = 'Liquidacao';
$arResult = array();

$rsRecordSet->addFormatacao("historico","MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['bimestre']         = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['numeroEmpenho']    = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['numeroLiquidacao'] = $rsRecordSet->getCampo('num_liquidacao');
    $arResult[$idCount]['data']             = $rsRecordSet->getCampo('dt_liquidacao');
    $arResult[$idCount]['valor']            = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['sinal']            = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['historico']        = $rsRecordSet->getCampo('historico');
    $arResult[$idCount]['codigoOperacao']   = $rsRecordSet->getCampo('cod_operacao');
    $arResult[$idCount]['numeroProcesso']   = $rsRecordSet->getCampo('num_processo');
    $arResult[$idCount]['idCredor']         = $rsRecordSet->getCampo('cod_credor');
    $arResult[$idCount]['referenciaMes']    = $rsRecordSet->getCampo('referencia_mes');
    $arResult[$idCount]['referenciaAno']    = $rsRecordSet->getCampo('referencia_ano');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;

?>
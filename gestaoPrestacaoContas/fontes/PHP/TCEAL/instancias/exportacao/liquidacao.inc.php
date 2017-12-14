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
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: liquidacao.inc.php 65532 2016-05-31 17:45:01Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLiquidacao.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALLiquidacao = new TTCEALLiquidacao();

$UndGestora = explode(',', $stEntidades);
if(count($UndGestora)>1){
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    foreach ($UndGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado( 'cod_entidade', $cod_entidade );
        $stCondicao = " AND CGM.nom_cgm ILIKE 'prefeitura%' ";
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if($rsEntidade->inNumLinhas>0)
            $CodUndGestora = $cod_entidade;
    }
    if(!$CodUndGestora)
        $CodUndGestora = $UndGestora[0];
} else {
    $CodUndGestora = $stEntidades;
}

$obTTCEALLiquidacao->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALLiquidacao->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALLiquidacao->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALLiquidacao->setDado('dtInicial'     , $dtInicial            );
$obTTCEALLiquidacao->setDado('dtFinal'       , $dtFinal              );
$obTTCEALLiquidacao->setDado('bimestre'      , $inBimestre           );
$obTTCEALLiquidacao->recuperaLiquidacao($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Liquidacao';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']             = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['Exercicio']            = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumEmpenho']           = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['NumLiquidacao']        = $rsRecordSet->getCampo('num_liquidacao');
    $arResult[$idCount]['DataLiquidacao']       = $rsRecordSet->getCampo('dt_liquidacao');
    $arResult[$idCount]['Valor']                = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['Sinal']                = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['CodOperacao']          = $rsRecordSet->getCampo('codigo_operacao');
    $arResult[$idCount]['NumProcesso']          = $rsRecordSet->getCampo('num_processo');
    $arResult[$idCount]['CodCredor']            = $rsRecordSet->getCampo('cod_credor');
    $arResult[$idCount]['Referencia']           = $rsRecordSet->getCampo('referencia');
    $arResult[$idCount]['Historico']            = $rsRecordSet->getCampo('historico');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>
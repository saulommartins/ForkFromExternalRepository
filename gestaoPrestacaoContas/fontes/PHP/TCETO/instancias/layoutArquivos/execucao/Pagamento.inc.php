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

  * Layout exportação TCE-TO arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: Pagamento.inc.php 60894 2014-11-21 13:13:08Z arthur $
  * $Date: 2014-11-21 11:13:08 -0200 (Fri, 21 Nov 2014) $
  * $Author: arthur $
  * $Rev: 60894 $
  *
*/
?>

<?php
/**
    * Página de Include Oculta - Exportação Arquivos Relacionais - Pagamento.xml
    *
    * Data de Criação: 17/06/2014
    *
    * @author: Evandro Melos
    *
    $Id: Pagamento.inc.php 60894 2014-11-21 13:13:08Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOLiquidacao.class.php';

$obTTCETOLiquidacao = new TTCETOLiquidacao();

$UndGestora = explode(',', $inCodEntidade);
$boTransacao = new Transacao();

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
    $CodUndGestora = $inCodEntidade;
}

$obTTCETOLiquidacao->setDado('exercicio'    , Sessao::getExercicio());
$obTTCETOLiquidacao->setDado('cod_entidade' , $inCodEntidade );
$obTTCETOLiquidacao->setDado('und_gestora'  , $CodUndGestora );
$obTTCETOLiquidacao->setDado('dtInicial'    , $stDataInicial );
$obTTCETOLiquidacao->setDado('dtFinal'      , $stDataFinal );
$obTTCETOLiquidacao->setDado('bimestre'     , $inBimestre );
$obTTCETOLiquidacao->recuperaPagamento($rsRecordSet, "", "" , $boTransacao);

$idCount = 0;
$stNomeArquivo = 'Pagamento';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']         = $inBimestre;
    $arResult[$idCount]['exercicio']        = Sessao::getExercicio();
    $arResult[$idCount]['numeroEmpenho']    = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['numeroLiquidacao'] = $rsRecordSet->getCampo('num_liquidacao');
    $arResult[$idCount]['numeroPagamento']  = $rsRecordSet->getCampo('num_pagamento');
    $arResult[$idCount]['data']             = $rsRecordSet->getCampo('data_pagamento');
    $arResult[$idCount]['valor']            = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['sinal']            = $rsRecordSet->getCampo('sinal');
    $arResult[$idCount]['historico']        = $rsRecordSet->getCampo('historico');
    $arResult[$idCount]['codigoOperacao']   = $rsRecordSet->getCampo('codigo_operacao');
    $arResult[$idCount]['numeroProcesso']   = $rsRecordSet->getCampo('cod_estrutural');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>
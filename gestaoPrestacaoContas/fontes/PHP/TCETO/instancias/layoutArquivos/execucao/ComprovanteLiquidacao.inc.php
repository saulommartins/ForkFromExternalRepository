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
  * $Id: ComprovanteLiquidacao.inc.php 60731 2014-11-12 15:50:04Z franver $
  * $Date: 2014-11-12 13:50:04 -0200 (Wed, 12 Nov 2014) $
  * $Author: franver $
  * $Rev: 60731 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOComprovanteLiquidacao.class.php';

$rsRecordSet = new RecordSet();
$obTTCETOComprovanteLiquidacao = new TTCETOComprovanteLiquidacao();
$obTTCETOComprovanteLiquidacao->setDado('exercicio'   , Sessao::getExercicio() );
$obTTCETOComprovanteLiquidacao->setDado('cod_entidade', $inCodEntidade );
$obTTCETOComprovanteLiquidacao->setDado('dt_inicial'  , $stDataInicial );
$obTTCETOComprovanteLiquidacao->setDado('dt_final'    , $stDataFinal );
$obTTCETOComprovanteLiquidacao->setDado('bimestre'    , $inBimestre );
$obTTCETOComprovanteLiquidacao->recuperaComprovanteLiquidacao($rsRecordSet,"","",$boTransacao);

$idCount = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']      = $rsRecordSet->getCampo( 'id_unidade_gestora' );
    $arResult[$idCount]['bimestre']              = $rsRecordSet->getCampo( 'bimestre'        );
    $arResult[$idCount]['exercicio']             = $rsRecordSet->getCampo( 'exercicio'       );
    $arResult[$idCount]['numeroEmpenho']         = $rsRecordSet->getCampo( 'numero_empenho'     );
    $arResult[$idCount]['numeroLiquidacao']      = $rsRecordSet->getCampo( 'numero_liquidacao'  );
    $arResult[$idCount]['tipoDocumento']         = $rsRecordSet->getCampo( 'tipo_documento'  );
    $arResult[$idCount]['numeroDocumento']       = $rsRecordSet->getCampo( 'numero_documento'   );
    $arResult[$idCount]['sinal']                 = $rsRecordSet->getCampo( 'sinal' );
    $arResult[$idCount]['valor']                 = $rsRecordSet->getCampo( 'valor' );
    $arResult[$idCount]['dataDocumento']         = $rsRecordSet->getCampo( 'data_documento' );
    $arResult[$idCount]['descricao']             = $rsRecordSet->getCampo( 'descricao' );
    $arResult[$idCount]['autorizacaoNotaFiscal'] = $rsRecordSet->getCampo( 'autorizacao_nota_fiscal' );
    $arResult[$idCount]['modeloNotaFiscal']      = $rsRecordSet->getCampo( 'modelo_nota_fiscal' );

    $idCount++;
    
    $rsRecordSet->proximo();
}

$obTTCETOComprovanteLiquidacao = null;
$rsRecordSet = null;
?>
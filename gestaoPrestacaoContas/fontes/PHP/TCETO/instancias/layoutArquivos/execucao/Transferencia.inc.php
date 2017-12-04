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
  * $Id: Transferencia.inc.php 60890 2014-11-20 19:21:44Z carolina $
  * $Date: 2014-11-20 17:21:44 -0200 (Thu, 20 Nov 2014) $
  * $Author: carolina $
  * $Rev: 60890 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOTransferencias.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$inCodUniGestora = explode(',', $inCodEntidade);

if (count($inCodUniGestora) > 1){
    
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    
    foreach ($inCodUniGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado('exercicio'    , Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado('cod_entidade' , $cod_entidade );
        
        $stCondicao = " AND LOWER(CGM.nom_cgm) LIKE '%prefeitura%' ";
        
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if ($rsEntidade->inNumLinhas > 0) {
            $inCodUniGestora = $cod_entidade;
        }
    }

    if (!$inCodUniGestora) {
        $inCodUniGestora = $UndGestora[0];
    }
    
} else {
    $inCodUniGestora =$inCodEntidade;
}

$obTTCETOTransferencias = new TTCETOTransferencias();
$obTTCETOTransferencias->setDado('exercicio'    , Sessao::getExercicio());
$obTTCETOTransferencias->setDado('cod_entidade' , $inCodEntidade);
$obTTCETOTransferencias->setDado('und_gestora'  , $inCodUniGestora);
$obTTCETOTransferencias->setDado('dtInicial'    , $stDataInicial);
$obTTCETOTransferencias->setDado('dtFinal'      , $stDataFinal);
$obTTCETOTransferencias->setDado('bimestre'     , $inBimestre);
$obTTCETOTransferencias->recuperaTransferencias($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Transferencias';
$stVersao="2.1";
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']    = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']            = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['exercicio']           = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idRecursoVinculado']  = $rsRecordSet->getCampo('id_rec_vinculado');
    $arResult[$idCount]['contaContabil']       = $rsRecordSet->getCampo('conta_contabil');
    $arResult[$idCount]['debitoCredito']       = $rsRecordSet->getCampo('debito_credito');
    $arResult[$idCount]['banco']               = $rsRecordSet->getCampo('banco');
    $arResult[$idCount]['agenciaBanco']        = $rsRecordSet->getCampo('agencia_banco');
    $arResult[$idCount]['numeroContaCorrente'] = $rsRecordSet->getCampo('num_conta_corrente');
    $arResult[$idCount]['numeroRegistro']      = $rsRecordSet->getCampo('numero_registro');
    $arResult[$idCount]['tipoTransferencia']   = $rsRecordSet->getCampo('cod_tipo_transferencia');
    $arResult[$idCount]['data']                = $rsRecordSet->getCampo('dt_transferencia');
    $arResult[$idCount]['valor']               = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['descricao']           = $rsRecordSet->getCampo('descricao');

    $idCount++;
    
    $rsRecordSet->proximo();
}
unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade, $obTTCEALTransferencias);

?>
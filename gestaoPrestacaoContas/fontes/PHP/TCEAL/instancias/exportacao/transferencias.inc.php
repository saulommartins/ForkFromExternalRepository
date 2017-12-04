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
 * Página de Include Oculta - Exportação Arquivos Execucao - transferencias.xml
 *
 * Data de Criação: 27/05/2014
 *
  $Id: transferencias.inc.php 59612 2014-09-02 12:00:51Z gelson $
 *
 * @author: Diogo Zarpelon.
 *
 */

include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALTransferencias.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$inCodUniGestora = explode(',', $stEntidades);

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
    $inCodUniGestora = $stEntidades;
}

$obTTCEALTransferencias = new TTCEALTransferencias();
$obTTCEALTransferencias->setDado('exercicio'    , Sessao::getExercicio());
$obTTCEALTransferencias->setDado('cod_entidade' , $stEntidades);
$obTTCEALTransferencias->setDado('und_gestora'  , $inCodUniGestora);
$obTTCEALTransferencias->setDado('dtInicial'    , $dtInicial);
$obTTCEALTransferencias->setDado('dtFinal'      , $dtFinal);
$obTTCEALTransferencias->setDado('bimestre'     , $inBimestre);
$obTTCEALTransferencias->recuperaTransferencias($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Transferencias';
$stVersao="2.1";
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['CodUndGestora']                   = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                        = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']                        = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']                       = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['Data']                            = $rsRecordSet->getCampo('dt_transferencia');
    $arResult[$idCount]['CodContaBalanceteDebitada']       = $rsRecordSet->getCampo('cod_conta_balancete_debitada');
    $arResult[$idCount]['CodBancoContaDebitada']           = $rsRecordSet->getCampo('cod_banco_conta_debitada');
    $arResult[$idCount]['CodAgenciaBancoContaDebitada']    = $rsRecordSet->getCampo('cod_agencia_banco_conta_debitada');
    $arResult[$idCount]['NumContaBancariaDebitada']        = $rsRecordSet->getCampo('num_conta_bancaria_debitada');
    $arResult[$idCount]['CodContaBalanceteCreditada']      = $rsRecordSet->getCampo('cod_conta_balancete_credito');
    $arResult[$idCount]['TitularContaBancariaCreditada']   = $rsRecordSet->getCampo('titular_conta_bancaria_creditada');
    $arResult[$idCount]['CodBancoContaCreditada']          = $rsRecordSet->getCampo('cod_banco_conta_credito');
    $arResult[$idCount]['CodAgenciaBancoContaCreditada']   = $rsRecordSet->getCampo('cod_agencia_banco_conta_credito');
    $arResult[$idCount]['NumContaBancariaCreditada']       = $rsRecordSet->getCampo('num_conta_bancaria_credito');
    $arResult[$idCount]['Valor']                           = $rsRecordSet->getCampo('valor');
    $arResult[$idCount]['Descricao']                       = $rsRecordSet->getCampo('descricao');

    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade, $obTTCEALTransferencias);

?>
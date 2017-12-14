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
 * Página de Include Oculta - Exportação Arquivos Execução - LancamentosEmpenho.xml
 *
 * Data de Criação: 03/06/2014
 *
 * @author: Diogo Zarpelon
 *
 $Id: lancamentosEmpenho.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
 *
 * @ignore
 *
 */

include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLancamentosEmpenho.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTMapeamento = new TTCEALLancamentosEmpenho();

$inCodUniGestora = explode(',', $stEntidades);

if (count($inCodUniGestora) > 1) {
    
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

$obTMapeamento->setDado('exercicio'    , Sessao::getExercicio());
$obTMapeamento->setDado('cod_entidade' , $stEntidades );
$obTMapeamento->setDado('und_gestora'  , $inCodUniGestora );
$obTMapeamento->setDado('dtInicial'    , $dtInicial );
$obTMapeamento->setDado('dtFinal'      , $dtFinal );
$obTMapeamento->setDado('bimestre'     , $inBimestre );

$obTMapeamento->recuperaLancamentosEmpenho($rsRecordSet);

$idCount = 0;
$stVersao="2.1";
$stNomeArquivo = 'LancamentosEmpenho';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']   = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']        = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']        = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['Exercicio']       = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumEmpenho']      = $rsRecordSet->getCampo('num_empenho');
    $arResult[$idCount]['CodContaDespesa'] = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['Valor']           = $rsRecordSet->getCampo('vl_empenho');
    $arResult[$idCount]['Sinal']           = $rsRecordSet->getCampo('sinal');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>
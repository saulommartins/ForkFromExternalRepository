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
 * Página de Include Oculta - Exportação Arquivos Execucao - AlteracoesLeis.xml
 *
 * Data de Criação: 26/06/2014
 *
 * @author: Arthur Cruz.
 *
 */

include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALAtribContContabeis.class.php';


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

$obTTCEALAtribContContabeis = new TTCEALAtribContContabeis();
$obTTCEALAtribContContabeis->setDado('exercicio'    , Sessao::getExercicio() );
$obTTCEALAtribContContabeis->setDado('cod_entidade' , $stEntidades );
$obTTCEALAtribContContabeis->setDado('und_gestora'  , $inCodUniGestora );
$obTTCEALAtribContContabeis->setDado('bimestre'     , $request->get('bimestre') );
$obTTCEALAtribContContabeis->setDado('dtInicial'    , $dtInicial );
$obTTCEALAtribContContabeis->setDado('dtFinal'      , $dtFinal   );

$obTTCEALAtribContContabeis->recuperaAtribContabeis($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'AtribContContabeis';
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['CodUndGestora']      = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']           = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']           = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']          = $rsRecordSet->getCampo('exercicio');    
    $arResult[$idCount]['CodContaBalancete']  = $rsRecordSet->getCampo('cod_conta_balancete');
    $arResult[$idCount]['Descricao']          = $rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['TipoConta']          = $rsRecordSet->getCampo('tipo_conta');
    $arResult[$idCount]['NivelConta']         = $rsRecordSet->getCampo('nivel_conta');
    $arResult[$idCount]['Escrituracao']       = $rsRecordSet->getCampo('escrituracao');
    $arResult[$idCount]['NaturezaInformacao'] = $rsRecordSet->getCampo('natureza_informacao');
    $arResult[$idCount]['IndicadorSuperavit'] = $rsRecordSet->getCampo('indicador_superavit');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $inCodUniGestora, $obTOrcamentoEntidade, $obTTCEALAtribContContabeis);

?>
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
    * Página de Include Oculta - Exportação Arquivos Orçamento - LdoMetasFiscaisAnexoI.xml
    *
    * Data de Criação: 02/07/2014
    *
    * @author: Arthur Cruz
    *
    $Id: ldoMetasFiscaisAnexoI.inc.php 59612 2014-09-02 12:00:51Z gelson $
    *
*/
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLdoMetasFiscaisAnexoI.class.php'    );
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );

$obTTCEALLdoMetasFiscaisAnexoI = new TTCEALLdoMetasFiscaisAnexoI();

$UndGestora = explode(',', $stEntidades);

if(count($UndGestora) > 1){
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    
    foreach ($UndGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado( 'cod_entidade', $cod_entidade );
        $stCondicao = " AND CGM.nom_cgm = 'prefeitura' ";
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if($rsEntidade->inNumLinhas>0)
            $CodUndGestora = $cod_entidade;
    }
    if(!$CodUndGestora)
        $CodUndGestora = $UndGestora[0];
}else{
    $CodUndGestora = $stEntidades;    
}

$obTTCEALLdoMetasFiscaisAnexoI->setDado('exercicio'   , Sessao::getExercicio()   );
$obTTCEALLdoMetasFiscaisAnexoI->setDado('cod_entidade', $stEntidades             );
$obTTCEALLdoMetasFiscaisAnexoI->setDado('und_gestora' , $CodUndGestora           );
$obTTCEALLdoMetasFiscaisAnexoI->setDado('bimestre'    , $request->get('bimestre'));
$obTTCEALLdoMetasFiscaisAnexoI->setDado('dtInicial'   , $dtInicial               );
$obTTCEALLdoMetasFiscaisAnexoI->setDado('dtFinal'     , $dtFinal                 );

$obTTCEALLdoMetasFiscaisAnexoI->recuperaLdoMetasFiscaisAnexoI($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'LdoMetasFiscaisAnexoI';
$arResult = array();

while (!$rsRecordSet->eof()) {
       
    $arResult[$idCount]['CodUndGestora']            = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                 = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']                 = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']                = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['NumLDO']                   = $rsRecordSet->getCampo('num_ldo');
    $arResult[$idCount]['MetasReceitasAnuais']      = $rsRecordSet->getCampo('metas_receitas_anuais');
    $arResult[$idCount]['ReceitasPrimarias']        = $rsRecordSet->getCampo('receitas_primarias');
    $arResult[$idCount]['MetasDespesasAnuais']      = $rsRecordSet->getCampo('metas_despesas_anuais');
    $arResult[$idCount]['DespesasPrimarias']        = $rsRecordSet->getCampo('despesas_primarias');
    $arResult[$idCount]['ResultadoPrimario']        = $rsRecordSet->getCampo('resultado_primario');
    $arResult[$idCount]['DividaPublicaConsolidada'] = $rsRecordSet->getCampo('divida_publica_consolidada');
    $arResult[$idCount]['DividaConsolidadaLiquida'] = $rsRecordSet->getCampo('divida_consolidada_liquida');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALLdoMetasFiscaisAnexoI);
?>
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
    * Página de Include Oculta - Exportação Arquivos Relacionais - PPA.xml
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Arthur Cruz
    *
*/
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALPPA.class.php'    );
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );

$obTTCEALPPA = new TTCEALPPA();

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

$obTTCEALPPA->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALPPA->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALPPA->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALPPA->setDado('dtInicial'     , $dtInicial            );
$obTTCEALPPA->setDado('dtFinal'       , $dtFinal              );

$obTTCEALPPA->recuperaPPA($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Ppa';
$stVersao="2.0";
$arResult = array();

while (!$rsRecordSet->eof()) {
       
    $arResult[$idCount]['CodUndGestora']       = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']            = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Exercicio']           = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodOrgao']            = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']  = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodPrograma']         = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['CodProjAtividade']    = $rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['CodPrograma']         = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['MetaFisica1Ano']      = $rsRecordSet->getCampo('meta_fisica_1ano');
    $arResult[$idCount]['MetaFisica2Ano']      = $rsRecordSet->getCampo('meta_fisica_2ano');
    $arResult[$idCount]['MetaFisica3Ano']      = $rsRecordSet->getCampo('meta_fisica_3ano');
    $arResult[$idCount]['MetaFisica4Ano']      = $rsRecordSet->getCampo('meta_fisica_4ano'); 
    $arResult[$idCount]['MetaFisicaTotal']     = $rsRecordSet->getCampo('meta_fisica_total'); 
    $arResult[$idCount]['MetaFinanceira1Ano']  = $rsRecordSet->getCampo('meta_financeira_1ano');
    $arResult[$idCount]['MetaFinanceira2Ano']  = $rsRecordSet->getCampo('meta_financeira_2ano');
    $arResult[$idCount]['MetaFinanceira3Ano']  = $rsRecordSet->getCampo('meta_financeira_3ano');
    $arResult[$idCount]['MetaFinanceira4Ano']  = $rsRecordSet->getCampo('meta_financeira_4ano'); 
    $arResult[$idCount]['MetaFinanceiraTotal'] = $rsRecordSet->getCampo('meta_financeira_total'); 
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALPPA);
?>
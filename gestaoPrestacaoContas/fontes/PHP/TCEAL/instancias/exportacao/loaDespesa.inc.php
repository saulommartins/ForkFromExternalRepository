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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Credor.xml
    *
    * Data de Criação: 03/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: loaDespesa.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLoaDespesa.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$TTCEALLoaDespesa = new TTCEALLoaDespesa();

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

$TTCEALLoaDespesa->setDado('exercicio'     , Sessao::getExercicio());
$TTCEALLoaDespesa->setDado('cod_entidade'  , $stEntidades          );
$TTCEALLoaDespesa->setDado('und_gestora'   , $CodUndGestora        );
$TTCEALLoaDespesa->setDado('dtInicial'     , $dtAnoInicial         );
$TTCEALLoaDespesa->setDado('dtFinal'       , $dtAnoFinal           );
$TTCEALLoaDespesa->setDado('bimestre'      , $inBimestre           );

$TTCEALLoaDespesa->recuperaDespesa($rsRecordSet);

$idCount=0;
$stNomeArquivo = 'LoaDespesa';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']      = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']           = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Exercicio']          = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao']           = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria'] = $rsRecordSet->getCampo('cod_unid_orcamentaria');
    $arResult[$idCount]['CodFuncao']          = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['CodSubFuncao']       = $rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['CodPrograma']        = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['CodProjAtividade']   = $rsRecordSet->getCampo('cod_proj_atividade');
    $arResult[$idCount]['CodContaDespesa']    = $rsRecordSet->getCampo('cod_conta_despesa');
    $arResult[$idCount]['CodRecVinculado']    = $rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['DotacaoInicial']     = $rsRecordSet->getCampo('dotacao_inicial');
    $arResult[$idCount]['Janeiro']            = $rsRecordSet->getCampo('janeiro');
    $arResult[$idCount]['Fevereiro']          = $rsRecordSet->getCampo('fevereiro');
    $arResult[$idCount]['Marco']              = $rsRecordSet->getCampo('marco');
    $arResult[$idCount]['Abril']              = $rsRecordSet->getCampo('abril');
    $arResult[$idCount]['Maio']               = $rsRecordSet->getCampo('maio');
    $arResult[$idCount]['Junho']              = $rsRecordSet->getCampo('junho');
    $arResult[$idCount]['Julho']              = $rsRecordSet->getCampo('julho');
    $arResult[$idCount]['Agosto']             = $rsRecordSet->getCampo('agosto');
    $arResult[$idCount]['Setembro']           = $rsRecordSet->getCampo('setembro');
    $arResult[$idCount]['Outubro']            = $rsRecordSet->getCampo('outubro');
    $arResult[$idCount]['Novembro']           = $rsRecordSet->getCampo('novembro');
    $arResult[$idCount]['Dezembro']           = $rsRecordSet->getCampo('dezembro');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);
?>
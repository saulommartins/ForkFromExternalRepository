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
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: credor.inc.php 58365 2014-05-27 18:43:38Z michel $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALLoaReceita.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALLoaReceita = new TTCEALLoaReceita();

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

$obTTCEALLoaReceita->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALLoaReceita->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALLoaReceita->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALLoaReceita->setDado('dtInicial'     , $dtAnoInicial         );
$obTTCEALLoaReceita->setDado('dtFinal'       , $dtAnoFinal           );
$obTTCEALLoaReceita->setDado('dtBiInicial'   , $dtInicial            );
$obTTCEALLoaReceita->setDado('dtBiFinal'     , $dtFinal              );
$obTTCEALLoaReceita->setDado('bimestre'      , $inBimestre           );

$obTTCEALLoaReceita->recuperaReceita($rsRecordSet);

$idCount=0;
$stNomeArquivo = 'LoaReceita';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']            = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                 = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Exercicio']                = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao']                 = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']       = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodRecVinculado']          = $rsRecordSet->getCampo('cod_recurso');
    $arResult[$idCount]['CodContaReceita']          = $rsRecordSet->getCampo('cod_receita');
    $arResult[$idCount]['ValorReceitaOrcada']       = $rsRecordSet->getCampo('vl_receita');
    $arResult[$idCount]['Descricao']                = $rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['TipoNivelConta']           = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['NumNivelConta']            = $rsRecordSet->getCampo('nivel');
    $arResult[$idCount]['MetaArrecadacao1Bimestre'] = $rsRecordSet->getCampo('meta_1b');
    $arResult[$idCount]['MetaArrecadacao2Bimestre'] = $rsRecordSet->getCampo('meta_2b');
    $arResult[$idCount]['MetaArrecadacao3Bimestre'] = $rsRecordSet->getCampo('meta_3b');
    $arResult[$idCount]['MetaArrecadacao4Bimestre'] = $rsRecordSet->getCampo('meta_4b');
    $arResult[$idCount]['MetaArrecadacao5Bimestre'] = $rsRecordSet->getCampo('meta_5b');
    $arResult[$idCount]['MetaArrecadacao6Bimestre'] = $rsRecordSet->getCampo('meta_6b');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);
?>
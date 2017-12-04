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
    $Id: balanceteReceita.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBalanceteReceita.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTMapeamento = new TTCEALBalanceteReceita();

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

$stNomePoder = SistemaLegado::pegaDado(  "parametro"
                                       , "administracao.configuracao"
                                       , "WHERE cod_modulo = 8 
                                            AND parametro ilike 'cod_entidade%'
                                            AND exercicio = '".Sessao::getExercicio()."'
                                            AND valor = '".$CodUndGestora."';");

$stNomePoder = trim(substr($stNomePoder,13,15));

switch ($stNomePoder) {
    case 'prefeitura':
        $stCodOrgaoTCEAL    = 'tceal_config_cod_orgao_executivo';
        $stCodUnidadeTCEAL  = 'tceal_config_cod_unidade_executivo';
        break;
    case 'camara':
        $stCodOrgaoTCEAL    = 'tceal_config_cod_orgao_legislativo';
        $stCodUnidadeTCEAL  = 'tceal_config_cod_unidade_legislativo';        
        break;
    case 'rpps':
        $stCodOrgaoTCEAL    = 'tceal_config_cod_orgao_rpps';
        $stCodUnidadeTCEAL  = 'tceal_config_cod_unidade_rpps';
        break;
    default:
        $stCodOrgaoTCEAL    = 'tceal_config_cod_orgao_outros';
        $stCodUnidadeTCEAL  = 'tceal_config_cod_unidade_outros';
        break;
}

foreach($UndGestora as $stEntidade){
    $rsRecordSet  = "rsRecordSet";
    $rsRecordSet .= $stEntidade;        
    $$rsRecordSet = new RecordSet();

    $obTMapeamento->setDado('exercicio'         , Sessao::getExercicio());
    $obTMapeamento->setDado('cod_entidade'      , $stEntidade          );
    $obTMapeamento->setDado('und_gestora'       , $CodUndGestora        );
    $obTMapeamento->setDado('dt_inicial'        , $dtInicial            );
    $obTMapeamento->setDado('dt_final'          , $dtFinal              );
    $obTMapeamento->setDado('bimestre'          , $inBimestre           );
    $obTMapeamento->setDado('poder_cod_orgao'   , $stCodOrgaoTCEAL      );
    $obTMapeamento->setDado('poder_cod_unidade' , $stCodUnidadeTCEAL    );
    $obTMapeamento->recuperaBalanceteReceita($$rsRecordSet);
}
$idCount=0;
$stNomeArquivo = 'BalanceteReceita';
$arResult = array();

while (!$$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']            = $$rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']                 = $$rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']                 = $inBimestre;
    $arResult[$idCount]['Exercicio']                = $$rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodOrgao']                 = $$rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']       = $$rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodContaReceita']          = $$rsRecordSet->getCampo('cod_conta_receita');
    $arResult[$idCount]['CodContaContabil']         = $$rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['PrevInicialReceita']       = $$rsRecordSet->getCampo('prev_inicial_receita');
    $arResult[$idCount]['PrevAtualizadaReceita']    = $$rsRecordSet->getCampo('prev_atualizada_receita');
    $arResult[$idCount]['ReceitaRealizada']         = $$rsRecordSet->getCampo('receita_realizada');
    $arResult[$idCount]['MetaArrecadacaoBimestral'] = $$rsRecordSet->getCampo('meta_arrecadacao_bimestral');
    $arResult[$idCount]['TipoNivelConta']           = $$rsRecordSet->getCampo('tipo_nivel_conta');
    $arResult[$idCount]['NumNivelConta']            = $$rsRecordSet->getCampo('num_nivel_conta');
    $arResult[$idCount]['CodRecVinculado']          = $$rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['Descricao']                = $$rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['CaracPeculiar']            = $$rsRecordSet->getCampo('carac_peculiar');
    
    $idCount++;
    
    $$rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);
?>
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
    * Página de Include Oculta - Exportação Arquivos Execucao - ReceitaArrecadada.xml
    *
    * Data de Criação: 04/07/2014
    *
    * @author: Evandro Melos
    *
    $Id: receitaArrecadada.inc.php 65528 2016-05-31 13:42:36Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALReceitaArrecadada.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$TTCEALReceitaArrecadada = new TTCEALReceitaArrecadada();

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

$stNomePoder = SistemaLegado::pegaDado("parametro"
                                      ,"administracao.configuracao"
                                      ,"WHERE cod_modulo = 8 
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
foreach($UndGestora as $stEntidade) {
    $rsRecordSet  = "rsRecordSet";
    $rsRecordSet .= $stEntidade;        
    $$rsRecordSet = new RecordSet();
    $TTCEALReceitaArrecadada->setDado('exercicio'         , Sessao::getExercicio() );
    $TTCEALReceitaArrecadada->setDado('cod_entidade'      , $stEntidade            );
    $TTCEALReceitaArrecadada->setDado('und_gestora'       , $CodUndGestora         );
    $TTCEALReceitaArrecadada->setDado('dtInicial'         , $dtInicial             );
    $TTCEALReceitaArrecadada->setDado('dtFinal'           , $dtFinal               );
    $TTCEALReceitaArrecadada->setDado('bimestre'          , $inBimestre            );
    $TTCEALReceitaArrecadada->setDado('poder_cod_orgao'   , $stCodOrgaoTCEAL       );
    $TTCEALReceitaArrecadada->setDado('poder_cod_unidade' , $stCodUnidadeTCEAL     );

    $TTCEALReceitaArrecadada->recuperaReceitaArrecadada($$rsRecordSet);
}
$idCount = 0;
$stNomeArquivo = 'ReceitaArrecadada';
$arResult = array();

while (!$$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']      = $$rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']           = $$rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']           = $inBimestre;
    $arResult[$idCount]['Exercicio']          = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao']           = $$rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria'] = $$rsRecordSet->getCampo('cod_unid_orcamentaria');
    $arResult[$idCount]['CodContaReceita']    = $$rsRecordSet->getCampo('cod_conta_receita');
    $arResult[$idCount]['CodContaContabil']   = $$rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['CodBanco']           = $$rsRecordSet->getCampo('cod_banco');
    $arResult[$idCount]['CodAgencia']         = $$rsRecordSet->getCampo('cod_agencia');
    $arResult[$idCount]['NumConta']           = $$rsRecordSet->getCampo('num_conta');
    $arResult[$idCount]['CodContaAtivo']      = $$rsRecordSet->getCampo('cod_conta_ativo');
    $arResult[$idCount]['DataArrecadacao']    = $$rsRecordSet->getCampo('data_arrecadacao');
    $arResult[$idCount]['Valor']              = $$rsRecordSet->getCampo('valor');
    $arResult[$idCount]['CodRecVinculado']    = $$rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['FormaArrecadacao']   = $$rsRecordSet->getCampo('forma_arrecadacao');
    $arResult[$idCount]['DataRegistro']       = $$rsRecordSet->getCampo('data_registro');
    
    $idCount++;
    
    $$rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>
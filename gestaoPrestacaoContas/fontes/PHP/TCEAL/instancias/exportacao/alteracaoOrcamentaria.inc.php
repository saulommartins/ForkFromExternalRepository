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
    * Página de Include Oculta - Exportação Arquivos Relacionais - AlteracaoOrcamentaria.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: alteracaoOrcamentaria.inc.php 65450 2016-05-23 19:49:16Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALAlteracaoOrcamentaria.class.php';

$obTTCEALAlteracaoOrcamentaria = new TTCEALAlteracaoOrcamentaria();

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

$obTTCEALAlteracaoOrcamentaria->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALAlteracaoOrcamentaria->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALAlteracaoOrcamentaria->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALAlteracaoOrcamentaria->setDado('dtInicial'     , $dtInicial            );
$obTTCEALAlteracaoOrcamentaria->setDado('dtFinal'       , $dtFinal              );
$obTTCEALAlteracaoOrcamentaria->setDado('bimestre'      , $inBimestre           );

$obTTCEALAlteracaoOrcamentaria->recuperaAlteracaoOrcamentaria($rsRecordSet);

$idCount=0;
$stVersao="2.1";
$stNomeArquivo = 'AlteracaoOrcamentaria';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']         = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']              = $rsRecordSet->getCampo('codigo_ua_real');
    $arResult[$idCount]['Bimestre']              = $inBimestre;
    $arResult[$idCount]['Exercicio']             = Sessao::getExercicio();
    $arResult[$idCount]['CodOrgao']              = $rsRecordSet->getCampo('num_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']    = $rsRecordSet->getCampo('num_unidade');
    $arResult[$idCount]['CodFuncao']             = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['CodSubFuncao']          = $rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['CodPrograma']           = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['CodProjAtividade']      = $rsRecordSet->getCampo('num_pao');
    $arResult[$idCount]['CodContaDespesa']       = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['CodRecVinculado']       = $rsRecordSet->getCampo('cod_recurso');
    $arResult[$idCount]['DocumentoAlteracao']    = $rsRecordSet->getCampo('documento_alteracao');
    $arResult[$idCount]['NumDocAlteracao']       = $rsRecordSet->getCampo('num_doc_alteracao');
    $arResult[$idCount]['DataDocAlteracao']      = $rsRecordSet->getCampo('dt_doc_alteracao');
    $arResult[$idCount]['DataPubDocAlteracao']   = $rsRecordSet->getCampo('dt_pub_alteracao');
    $arResult[$idCount]['NumLeiAutorizacao']     = $rsRecordSet->getCampo('num_lei_autorizacao');
    $arResult[$idCount]['DataLeiAutorizacao']    = $rsRecordSet->getCampo('dt_lei_autorizacao');
    $arResult[$idCount]['DataPubLeiAutorizacao'] = $rsRecordSet->getCampo('dt_pub_autorizacao');
    $arResult[$idCount]['NumAlteracao']          = $rsRecordSet->getCampo('num_alteracao');
    $arResult[$idCount]['TipoAlteracao']         = $rsRecordSet->getCampo('tipo_alteracao');
    $arResult[$idCount]['ValorAlteracao']        = $rsRecordSet->getCampo('vl_alteracao');
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade);

?>
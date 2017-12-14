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
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALReceita.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALReceita = new TTCEALReceita();
$obTOrcamentoEntidade = new TOrcamentoEntidade;

$UndGestora = explode(',', $stEntidades);
    
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

$stNomePoder = $rsEntidade->getCampo('entidade');

if ( preg_match("/prefeitura/i", $stNomePoder) ) {
    $stCodOrgaoTCEAL    = 'tceal_orgao_prefeitura';
    $stCodUnidadeTCEAL  = 'tceal_unidade_prefeitura';
}elseif ( preg_match("/camara/i", $stNomePoder) ) {
    $stCodOrgaoTCEAL    = 'tceal_orgao_camara';
    $stCodUnidadeTCEAL  = 'tceal_unidade_camara';        
}elseif ( preg_match("/fundo/i", $stNomePoder) ) {
    $stCodOrgaoTCEAL    = 'tceal_orgao_rpps';
    $stCodUnidadeTCEAL  = 'tceal_unidade_rpps';
}else{
    $stCodOrgaoTCEAL    = 'tceal_orgao_outros';
    $stCodUnidadeTCEAL  = 'tceal_unidade_outros';
}

$obTTCEALReceita->setDado('exercicio'         , Sessao::getExercicio());
$obTTCEALReceita->setDado('cod_entidade'      , $stEntidades          );
$obTTCEALReceita->setDado('und_gestora'       , $CodUndGestora        );
$obTTCEALReceita->setDado('dtInicial'         , $dtInicial            );
$obTTCEALReceita->setDado('dtFinal'           , $dtFinal              );
$obTTCEALReceita->setDado('bimestre'          , $inBimestre           );
$obTTCEALReceita->setDado('poder_cod_orgao'   , $stCodOrgaoTCEAL      );
$obTTCEALReceita->setDado('poder_cod_unidade' , $stCodUnidadeTCEAL    );

$obTTCEALReceita->recuperaReceita($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Receita';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']        = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']             = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre']             = $rsRecordSet->getCampo('bimestre');;
    $arResult[$idCount]['Exercicio']            = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodOrgao']             = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['CodUndOrcamentaria']   = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['CodContaReceita']      = $rsRecordSet->getCampo('cod_receita');
    $arResult[$idCount]['CodContaContabil']     = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['RealizadaJaneiro']     = $rsRecordSet->getCampo('realizada_jan');
    $arResult[$idCount]['RealizadaFevereiro']   = $rsRecordSet->getCampo('realizada_fev');
    $arResult[$idCount]['RealizadaMarco']       = $rsRecordSet->getCampo('realizada_mar');
    $arResult[$idCount]['RealizadaAbril']       = $rsRecordSet->getCampo('realizada_abr');
    $arResult[$idCount]['RealizadaMaio']        = $rsRecordSet->getCampo('realizada_mai');
    $arResult[$idCount]['RealizadaJunho']       = $rsRecordSet->getCampo('realizada_jun');
    $arResult[$idCount]['RealizadaJulho']       = $rsRecordSet->getCampo('realizada_jul');
    $arResult[$idCount]['RealizadaAgosto']      = $rsRecordSet->getCampo('realizada_ago');
    $arResult[$idCount]['RealizadaSetembro']    = $rsRecordSet->getCampo('realizada_set');
    $arResult[$idCount]['RealizadaOutubro']     = $rsRecordSet->getCampo('realizada_out');
    $arResult[$idCount]['RealizadaNovembro']    = $rsRecordSet->getCampo('realizada_nov');
    $arResult[$idCount]['RealizadaDezembro']    = $rsRecordSet->getCampo('realizada_dez');
    $arResult[$idCount]['CaracPeculiar']        = $rsRecordSet->getCampo('carac_peculiar');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALReceita);

?>
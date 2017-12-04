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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Funcao.xml
    *
    * Data de Criação: 28/05/2014
    *
    * @author: Arthur Cruz
    *
*/
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALFuncao.class.php'    );
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );

$obTTCEALFuncao = new TTCEALFuncao();

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

$obTTCEALFuncao->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALFuncao->setDado('bimestre'      , $inBimestre           );
$obTTCEALFuncao->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALFuncao->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALFuncao->setDado('dtInicial'     , $dtInicial            );
$obTTCEALFuncao->setDado('dtFinal'       , $dtFinal              );

$obTTCEALFuncao->recuperaFuncao($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Funcao';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']      = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']      = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['Exercicio']     = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodFuncao']     = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['Nome']          = $rsRecordSet->getCampo('nome');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALFuncao);
?>
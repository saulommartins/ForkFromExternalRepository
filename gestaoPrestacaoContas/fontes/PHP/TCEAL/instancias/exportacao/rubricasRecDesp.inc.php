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
    * Página de Include Oculta - Exportação Arquivos Relacionais - RubricasRecDesp.xml
    *
    * Data de Criação: 29/05/2014
    *
    * @author: Evandro Melos
    *
*/
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALRubricasRecDesp.class.php'    );

$obTTCEALRubricasRecDesp = new TTCEALRubricasRecDesp();

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

$obTTCEALRubricasRecDesp->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALRubricasRecDesp->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALRubricasRecDesp->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALRubricasRecDesp->setDado('dtInicial'     , $dtInicial            );
$obTTCEALRubricasRecDesp->setDado('dtFinal'       , $dtFinal              );

$obTTCEALRubricasRecDesp->recuperaRubricasRecDesp($rsRecordSet,"","ORDER BY receita_despesa.cod_estrutural",$boTransacao);

$idCount = 0;
$stNomeArquivo = 'RubricasRecDesp';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['GrupoConta'] = $rsRecordSet->getCampo('grupo_conta');
    $arResult[$idCount]['CodRubrica'] = $rsRecordSet->getCampo('cod_rubrica');
    $arResult[$idCount]['Especificacao'] = $rsRecordSet->getCampo('especificacao');
    $arResult[$idCount]['TipoNivelConta'] = $rsRecordSet->getCampo('tipo_nivel_conta');
    $arResult[$idCount]['NumNivelConta'] = $rsRecordSet->getCampo('num_nivel_conta');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALRubricasRecDesp);
?>
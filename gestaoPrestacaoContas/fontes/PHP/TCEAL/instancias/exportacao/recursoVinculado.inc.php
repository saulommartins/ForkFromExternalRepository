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
    * Página de Include Oculta - Exportação Arquivos Relacionais - RecursoVinculado.xml
    *
    * Data de Criação: 29/05/2014
    *
    $Id: recursoVinculado.inc.php 64812 2016-04-05 16:31:27Z carlos.silva $
    *
    * @author: Arthur Cruz
    *
*/
include_once ( CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALRecursoVinculado.class.php'    );
include_once ( CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php' );

$obTTCEALRecursoVinculado = new TTCEALRecursoVinculado();

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

$obTTCEALRecursoVinculado->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALRecursoVinculado->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALRecursoVinculado->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALRecursoVinculado->setDado('dtInicial'     , $dtInicial            );
$obTTCEALRecursoVinculado->setDado('dtFinal'       , $dtFinal              );

$obTTCEALRecursoVinculado->recuperaRecursoVinculado($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'RecursoVinculado';
$arResult = array();
$stVersao="2.1";

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora']    = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA']         = ($rsRecordSet->getCampo('codigo_ua') == "" ? '0000' : $rsRecordSet->getCampo('codigo_ua'));
    $arResult[$idCount]['Bimestre']         = $inBimestre;
    $arResult[$idCount]['Exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodRecVinculado']  = $rsRecordSet->getCampo('codrecvinculado');
    $arResult[$idCount]['Nome']             = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['Finalidade']       = $rsRecordSet->getCampo('finalidade');
    $arResult[$idCount]['Tipo']             = $rsRecordSet->getCampo('cod_tipo_esfera');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALRecursoVinculado);
?>
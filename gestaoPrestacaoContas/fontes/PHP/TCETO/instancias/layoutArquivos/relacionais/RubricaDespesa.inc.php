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
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
*/

include_once ( CAM_GPC_TCETO_MAPEAMENTO.'TTCETORubricaDespesa.class.php'    );

$obTTCETORubricaDespesa = new TTCETORubricaDespesa();

$obTTCETORubricaDespesa->setDado('exercicio'     , Sessao::getExercicio());
$obTTCETORubricaDespesa->setDado('cod_entidade'  , $inCodEntidade        );
$obTTCETORubricaDespesa->setDado('und_gestora'   , $inCodEntidade        );
$obTTCETORubricaDespesa->setDado('dtInicial'     , $stDataInicial        );
$obTTCETORubricaDespesa->setDado('dtFinal'       , $stDataFinal          );

$obTTCETORubricaDespesa->recuperaRubricaDespesa($rsRecordSet,"","ORDER BY conta_despesa.cod_estrutural",$boTransacao);

$idCount = 0;
$stNomeArquivo = 'RubricaDespesa';
$arResult = array();

$rsRecordSet->addFormatacao("nome","MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']         = $inBimestre;
    $arResult[$idCount]['exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idRubricaDespesa'] = $rsRecordSet->getCampo('cod_rubrica');
    $arResult[$idCount]['nome']             = $rsRecordSet->getCampo('especificacao');
    $arResult[$idCount]['tipoNivel']        = $rsRecordSet->getCampo('tipo_nivel_conta');
    $arResult[$idCount]['numeroNivel']      = $rsRecordSet->getCampo('num_nivel_conta');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;
?>

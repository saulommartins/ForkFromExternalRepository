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
    * Página de Include Oculta - Exportação Arquivos Execução - DecretoAlteracaoOrcamentaria.xm
    *
    * Data de Criação: 12/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: DecretoAlteracaoOrcamentaria.inc.php 60913 2014-11-24 18:43:20Z evandro $
    *
    * @ignore
    *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETODecretoAlteracaoOrcamentaria.class.php';

$obTTCETODecretoAlteracaoOrcamentaria = new TTCETODecretoAlteracaoOrcamentaria();

$obTTCETODecretoAlteracaoOrcamentaria->setDado('exercicio'     , Sessao::getExercicio());
$obTTCETODecretoAlteracaoOrcamentaria->setDado('cod_entidade'  , $inCodEntidade        );
$obTTCETODecretoAlteracaoOrcamentaria->setDado('und_gestora'   , $inCodEntidade        );
$obTTCETODecretoAlteracaoOrcamentaria->setDado('dtInicial'     , $stDataInicial        );
$obTTCETODecretoAlteracaoOrcamentaria->setDado('dtFinal'       , $stDataFinal          );
$obTTCETODecretoAlteracaoOrcamentaria->setDado('bimestre'      , $inBimestre           );

$obTTCETODecretoAlteracaoOrcamentaria->recuperaAlteracaoOrcamentaria($rsRecordSet);

$idCount=0;
$stNomeArquivo = 'AlteracaoOrcamentaria';
$arResult = array();

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']         = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['bimestre']                 = $inBimestre;
    $arResult[$idCount]['exercicio']                = Sessao::getExercicio();
    $arResult[$idCount]['idOrgao']                  = $rsRecordSet->getCampo('num_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']    = $rsRecordSet->getCampo('num_unidade');
    $arResult[$idCount]['idFuncao']                 = $rsRecordSet->getCampo('cod_funcao');
    $arResult[$idCount]['idSubFuncao']              = $rsRecordSet->getCampo('cod_subfuncao');
    $arResult[$idCount]['idPrograma']               = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['idProjetoAtividade']       = $rsRecordSet->getCampo('num_pao');
    $arResult[$idCount]['idRubricaDespesa']         = $rsRecordSet->getCampo('cod_estrutural');
    $arResult[$idCount]['idRecursoVinculado']       = $rsRecordSet->getCampo('cod_recurso');    
    $arResult[$idCount]['numeroDocumento']          = $rsRecordSet->getCampo('num_doc_alteracao');
    $arResult[$idCount]['dataDocumento']            = $rsRecordSet->getCampo('dt_doc_alteracao');
    $arResult[$idCount]['dataAlteracao']            = $rsRecordSet->getCampo('dt_pub_alteracao');
    $arResult[$idCount]['numeroAlteracao']          = $rsRecordSet->getCampo('num_alteracao');
    $arResult[$idCount]['tipoAlteracao']            = $rsRecordSet->getCampo('tipo_alteracao');
    $arResult[$idCount]['valorAlteracao']           = $rsRecordSet->getCampo('vl_alteracao');
    $arResult[$idCount]['sinal']                    = $rsRecordSet->getCampo('sinal');
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;
?>
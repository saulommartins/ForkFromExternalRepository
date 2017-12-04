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

  * Layout exportação TCE-TO arquivo : 
  * Data de Criação

  * @author Analista:
  * @author Desenvolvedor: 
  *
  * @ignore
  * $Id: Empenho.inc.php 60880 2014-11-20 15:41:01Z evandro $
  * $Date: 2014-11-20 13:41:01 -0200 (Thu, 20 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60880 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOEmpenho.class.php';

$obTTCETOEmpenho = new TTCETOEmpenho();

$obTTCETOEmpenho->setDado('exercicio'     , Sessao::getExercicio());
$obTTCETOEmpenho->setDado('cod_entidade'  , $inCodEntidade        );
$obTTCETOEmpenho->setDado('dt_inicial'    , $stDataInicial        );
$obTTCETOEmpenho->setDado('dt_final'      , $stDataFinal          );
$obTTCETOEmpenho->setDado('bimestre'      , $inBimestre           );
$obTTCETOEmpenho->recuperaEmpenho($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Empenho';
$arResult = array();

while (!$rsRecordSet->eof()) {        
    $arResult[$idCount]['idUnidadeGestora']        = $rsRecordSet->getCampo('cod_und_gestora'   );
    $arResult[$idCount]['bimestre']                = $rsRecordSet->getCampo('bimestre'          );
    $arResult[$idCount]['exercicio']               = $rsRecordSet->getCampo('exercicio'         );
    $arResult[$idCount]['idOrgao']                 = $rsRecordSet->getCampo('num_orgao'         );
    $arResult[$idCount]['idUnidadeOrcamentaria']   = $rsRecordSet->getCampo('num_unidade'       );
    $arResult[$idCount]['idFuncao']                = $rsRecordSet->getCampo('cod_funcao'        );
    $arResult[$idCount]['idSubFuncao']             = $rsRecordSet->getCampo('cod_subfuncao'     );
    $arResult[$idCount]['idPrograma']              = $rsRecordSet->getCampo('cod_programa'      );
    $arResult[$idCount]['idProjetoAtividade']      = $rsRecordSet->getCampo('projeto_atividade' );
    $arResult[$idCount]['contaContabil']           = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['idRubricaDespesa']        = $rsRecordSet->getCampo('rubrica_despesa'   );
    $arResult[$idCount]['idRecursoVinculado']      = $rsRecordSet->getCampo('recurso_vinculado' );
    $arResult[$idCount]['idCredor']                = $rsRecordSet->getCampo('cod_credor'        );
    $arResult[$idCount]['numeroEmpenho']           = $rsRecordSet->getCampo('num_empenho'       );
    $arResult[$idCount]['data']                    = $rsRecordSet->getCampo('dt_empenho'        );
    $arResult[$idCount]['valor']                   = $rsRecordSet->getCampo('vl_empenho'        );
    $arResult[$idCount]['sinal']                   = $rsRecordSet->getCampo('sinal'             );
    $arResult[$idCount]['historico']               = $rsRecordSet->getCampo('historico'         );
    $arResult[$idCount]['contraPartida']           = $rsRecordSet->getCampo('contra_partida'    );
    $arResult[$idCount]['modalidadeLicitacao']     = $rsRecordSet->getCampo('modal_licita'      );
    $arResult[$idCount]['caracteristicaPeculiar']  = $rsRecordSet->getCampo('carac_peculiar'    );
    $arResult[$idCount]['numeroProcesso']          = $rsRecordSet->getCampo('num_processo'      );
    $arResult[$idCount]['numeroContrato']          = $rsRecordSet->getCampo('num_contrato'      );
    $arResult[$idCount]['dataContrato']            = $rsRecordSet->getCampo('dt_contrato'       );
    $arResult[$idCount]['numeroConvenio']          = $rsRecordSet->getCampo('num_convenio'      );
    $arResult[$idCount]['numeroObra']              = $rsRecordSet->getCampo('num_obra'          );
    $arResult[$idCount]['tipo']                    = $rsRecordSet->getCampo('tipo'              );
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

$rsRecordSet = null;

?>
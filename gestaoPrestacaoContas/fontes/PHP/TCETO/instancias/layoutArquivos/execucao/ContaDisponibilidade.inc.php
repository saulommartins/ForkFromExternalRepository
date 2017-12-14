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
  * $Id: ContaDisponibilidade.inc.php 60829 2014-11-18 12:35:01Z evandro $
  * $Date: 2014-11-18 10:35:01 -0200 (Tue, 18 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60829 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOContaDisponibilidade.class.php';

$obTTCETOContaDisponibilidade   = new TTCETOContaDisponibilidade();
       
$obTTCETOContaDisponibilidade->setDado('exercicio', Sessao::getExercicio());
$obTTCETOContaDisponibilidade->setDado('cod_entidade', $inCodEntidade );
$obTTCETOContaDisponibilidade->setDado('bimestre', $inBimestre );
$obTTCETOContaDisponibilidade->listarExportacaoContaDisponibilidade($rsRecordSet,"","",$boTransacao);

$stNomeArquivo = "ContaDisponibilidade";
$arResult = array();
$idCount = 0;

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadedGestora']      = $rsRecordSet->getCampo('cod_und_gestora'     );
    $arResult[$idCount]['bimestre']               = $inBimestre;
    $arResult[$idCount]['exercicio']              = Sessao::getExercicio();
    $arResult[$idCount]['idOrgao']                = $rsRecordSet->getCampo('cod_orgao'           );
    $arResult[$idCount]['idUnidadeOrcamentaria']  = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['contaContabil']          = $rsRecordSet->getCampo('cod_conta_contabil'  );
    $arResult[$idCount]['idRecursoVinculado']     = $rsRecordSet->getCampo('cod_rec_vinculado'   );
    $arResult[$idCount]['banco']                  = $rsRecordSet->getCampo('cod_banco'           );
    $arResult[$idCount]['agenciaBanco']           = $rsRecordSet->getCampo('cod_agencia_banco'   );
    $arResult[$idCount]['numeroContaCorrente']    = $rsRecordSet->getCampo('num_conta_corrente'  );
    $arResult[$idCount]['tipo']                   = $rsRecordSet->getCampo('tipo'                );
    $arResult[$idCount]['classificacao']          = $rsRecordSet->getCampo('classificacao'       );
    $idCount++;

    $rsRecordSet->proximo();
}

return $arResult;
?>
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
  * $Id: ContaReceitaOrcamentaria.inc.php 60828 2014-11-18 11:44:09Z evandro $
  * $Date: 2014-11-18 09:44:09 -0200 (Tue, 18 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60828 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOContaReceitaOrcamentaria.class.php";

$boTransacao = new Transacao();
$obTTCETOContaReceitaOrcamentaria = new TTCETOContaReceitaOrcamentaria();

$obTTCETOContaReceitaOrcamentaria->setDado('exercicio'  , $inExercicio  );
$obTTCETOContaReceitaOrcamentaria->setDado('entidade'   , $inCodEntidade);
$obTTCETOContaReceitaOrcamentaria->setDado('dt_inicial' , $stDataInicial);
$obTTCETOContaReceitaOrcamentaria->setDado('dt_final'   , $stDataFinal  );
$obTTCETOContaReceitaOrcamentaria->setDado('bimestre'   , $inBimestre   );

$obTTCETOContaReceitaOrcamentaria->recuperaTodos($rsRecordSet, "" ,"" , $boTransacao );

$rsRecordSet->addFormatacao('nome', "MB_SUBSTRING(0,255)");

$idCount = 0;
$arResult = array();

while (!$rsRecordSet->eof()) {

    $arResult[$idCount]['idUnidadeGestora']           = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']                   = $inBimestre;
    $arResult[$idCount]['exercicio']                  = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idContaReceitaOrcamentaria'] = $rsRecordSet->getCampo('conta_receita');//estrutural receita
    $arResult[$idCount]['nome']                       = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['tipoNivel']                  = $rsRecordSet->getCampo('tipo_nivel');
    $arResult[$idCount]['numeroNivel']                = $rsRecordSet->getCampo('nivel');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

?>
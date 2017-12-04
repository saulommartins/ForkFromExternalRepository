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
  * $Id: LoaReceita.inc.php 60694 2014-11-10 17:09:02Z evandro $
  * $Date: 2014-11-10 15:09:02 -0200 (Mon, 10 Nov 2014) $
  * $Author: evandro $
  * $Rev: 60694 $
  *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOLoaReceita.class.php';

$obTTCETOLoaReceita = new TTCETOLoaReceita();

$obTTCETOLoaReceita->setDado('exercicio'    , $inExercicio   );
$obTTCETOLoaReceita->setDado('cod_entidade' , $inCodEntidade );
$obTTCETOLoaReceita->setDado('und_gestora'  , $inCodEntidade );
$obTTCETOLoaReceita->setDado('dtInicial'    , $stDataInicial );
$obTTCETOLoaReceita->setDado('dtFinal'      , $stDataFinal   );
$obTTCETOLoaReceita->setDado('bimestre'     , $inBimestre    );

$obTTCETOLoaReceita->recuperaReceita($rsRecordSet);

$stNomeArquivo = 'LoaReceita';
$idCount=0;
$arResult = array();
//Formatando tamanhdo do campo descricao
$rsRecordSet->addFormatacao("descricao","MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['idUnidadeGestora']           = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['exercicio']                  = Sessao::getExercicio();
    $arResult[$idCount]['idOrgao']                    = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']      = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['idRecursoVinculado']         = $rsRecordSet->getCampo('cod_recurso');
    $arResult[$idCount]['contaContabil']              = $rsRecordSet->getCampo('conta_contabil');
    $arResult[$idCount]['idContaReceitaOrcamentaria'] = $rsRecordSet->getCampo('cod_receita');
    $arResult[$idCount]['valorReceitaOrcada']         = $rsRecordSet->getCampo('vl_receita');
    $arResult[$idCount]['descricao']                  = $rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['tipoNivelConta']             = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['numeroNivelConta']           = $rsRecordSet->getCampo('nivel');
    $arResult[$idCount]['metaArrecadacao1Bimestre']   = $rsRecordSet->getCampo('meta_1b');
    $arResult[$idCount]['metaArrecadacao2Bimestre']   = $rsRecordSet->getCampo('meta_2b');
    $arResult[$idCount]['metaArrecadacao3Bimestre']   = $rsRecordSet->getCampo('meta_3b');
    $arResult[$idCount]['metaArrecadacao4Bimestre']   = $rsRecordSet->getCampo('meta_4b');
    $arResult[$idCount]['metaArrecadacao5Bimestre']   = $rsRecordSet->getCampo('meta_5b');
    $arResult[$idCount]['metaArrecadacao6Bimestre']   = $rsRecordSet->getCampo('meta_6b');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

?>
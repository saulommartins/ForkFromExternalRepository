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
    * Data de Criação: 11/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: Credor.inc.php 60902 2014-11-21 17:56:16Z arthur $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOCredor.class.php';

$obTTCETOCredor = new TTCETOCredor();

$obTTCETOCredor->setDado('exercicio'     , Sessao::getExercicio());
$obTTCETOCredor->setDado('cod_entidade'  , $inCodEntidade        );
$obTTCETOCredor->setDado('und_gestora'   , $inCodEntidade        );
$obTTCETOCredor->setDado('dtInicial'     , $stDataInicial        );
$obTTCETOCredor->setDado('dtFinal'       , $stDataFinal          );

$obTTCETOCredor->recuperaCredor($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Credor';
$arResult = array();

$rsRecordSet->addFormatacao( "nome"    , "MB_SUBSTRING(0,100)" );
$rsRecordSet->addFormatacao( "endereco", "MB_SUBSTRING(0,100)" );
$rsRecordSet->addFormatacao( "cidade"  , "MB_SUBSTRING(0,100)" );

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora']     = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']             = $inBimestre;
    $arResult[$idCount]['exercicio']            = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idCredor']             = $rsRecordSet->getCampo('cod_credor');
    $arResult[$idCount]['data']                 = $rsRecordSet->getCampo('data');
    $arResult[$idCount]['nome']                 = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['inscricaoEstadual']    = $rsRecordSet->getCampo('insc_estadual');
    $arResult[$idCount]['inscricaoMunicipal']   = $rsRecordSet->getCampo('insc_municipal');
    $arResult[$idCount]['endereco']             = $rsRecordSet->getCampo('endereco');
    $arResult[$idCount]['cidade']               = $rsRecordSet->getCampo('cidade');
    $arResult[$idCount]['uf']                   = $rsRecordSet->getCampo('uf');
    $arResult[$idCount]['pais']                 = $rsRecordSet->getCampo('pais');
    $arResult[$idCount]['cep']                  = $rsRecordSet->getCampo('cep');
    $arResult[$idCount]['fone']                 = $rsRecordSet->getCampo('fone');
    $arResult[$idCount]['fax']                  = $rsRecordSet->getCampo('fax');
    $arResult[$idCount]['tipo']                 = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['numeroRegistro']       = $rsRecordSet->getCampo('numero_registro');
    
    $idCount++;
    $rsRecordSet->proximo();
}

return $arResult;

?>
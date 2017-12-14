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
 * Página de Include Oculta - Exportação Arquivos Relacionais - Orgao.xml
 *
 * Data de Criação: 10/11/2014
 *
 * @author: Evandro Melos
 *
 * $Id: Orgao.inc.php 60704 2014-11-11 11:28:55Z evandro $
 */

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOOrgao.class.php';

$obTTCETOOrgao = new TTCETOOrgao();

$obTTCETOOrgao->setDado('exercicio'    , Sessao::getExercicio() );
$obTTCETOOrgao->setDado('bimestre'     , $inBimestre            );
$obTTCETOOrgao->setDado('und_gestora'  , $inCodEntidade         );
$obTTCETOOrgao->setDado('dtInicial'    , $stDataInicial         );
$obTTCETOOrgao->setDado('dtFinal'      , $stDataFinal           );

$obTTCETOOrgao->recuperaOrgao($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'Orgao';
$arResult = array();

$rsRecordSet->addFormatacao("nome","MB_SUBSTRING(0,100)");

while (!$rsRecordSet->eof()) {
    
    $arResult[$idCount]['idUnidadeGestora'] = $rsRecordSet->getCampo('cod_und_gestora');    
    $arResult[$idCount]['bimestre']         = $rsRecordSet->getCampo('bimestre');
    $arResult[$idCount]['exercicio']        = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idOrgao']          = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['nome']             = $rsRecordSet->getCampo('nome');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;

?>
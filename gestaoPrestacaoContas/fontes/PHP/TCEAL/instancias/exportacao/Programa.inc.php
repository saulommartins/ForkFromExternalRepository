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
    * Página de Include Oculta - Exportação Arquivos Relacionais - Programa.xml
    *
    * Data de Criação: 27/05/2014
    *
    * @author: Franver Sarmento de Moraes
    *
    $Id: Programa.inc.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * @ignore
    *
*/


include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALPrograma.class.php';

$obTTCEALPrograma = new TTCEALPrograma();
$obTTCEALPrograma->setDado('exercicio', Sessao::getExercicio());
$obTTCEALPrograma->setDado('cod_entidade',$stEntidades);

$obTTCEALPrograma->recuperaPrograma($rsRecordSet, $stCondicao, $stOrdem, $boTransacao);

$idCount=0;
$stNomeArquivo = 'Programa';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodPrograma'] = $rsRecordSet->getCampo('cod_programa');
    $arResult[$idCount]['Nome'] = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['Objetivo'] = $rsRecordSet->getCampo('objetivo');
    $arResult[$idCount]['PublicoAlvo'] = $rsRecordSet->getCampo('publico_alvo');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($obTTCEALPrograma);
?>
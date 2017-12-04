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
    * Data de Criação: 27/05/2014
    *
    * @author: Michel Teixeira
    *
    $Id: credor.inc.php 59693 2014-09-05 12:39:50Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALCredor.class.php';
include_once CAM_GF_ORC_MAPEAMENTO.'TOrcamentoEntidade.class.php';

$obTTCEALCredor = new TTCEALCredor();

$UndGestora = explode(',', $stEntidades);
if(count($UndGestora)>1){
    $obTOrcamentoEntidade = new TOrcamentoEntidade;
    foreach ($UndGestora as $cod_entidade) {
        $obTOrcamentoEntidade->setDado( 'exercicio', Sessao::getExercicio() );
        $obTOrcamentoEntidade->setDado( 'cod_entidade', $cod_entidade );
        $stCondicao = " AND CGM.nom_cgm ILIKE 'prefeitura%' ";
        $obTOrcamentoEntidade->recuperaRelacionamentoNomes( $rsEntidade, $stCondicao );
        
        if($rsEntidade->inNumLinhas>0)
            $CodUndGestora = $cod_entidade;
    }
    if(!$CodUndGestora)
        $CodUndGestora = $UndGestora[0];
}
else
    $CodUndGestora = $stEntidades;

$obTTCEALCredor->setDado('exercicio'     , Sessao::getExercicio());
$obTTCEALCredor->setDado('cod_entidade'  , $stEntidades          );
$obTTCEALCredor->setDado('und_gestora'   , $CodUndGestora        );
$obTTCEALCredor->setDado('dtInicial'     , $dtInicial            );
$obTTCEALCredor->setDado('dtFinal'       , $dtFinal              );

$obTTCEALCredor->recuperaCredor($rsRecordSet);

$idCount=0;
$stNomeArquivo = 'Credor';
$arResult = array();

while (!$rsRecordSet->eof()) {
    $arResult[$idCount]['CodUndGestora'] = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['CodigoUA'] = $rsRecordSet->getCampo('codigo_ua');
    $arResult[$idCount]['Bimestre'] = $inBimestre;
    $arResult[$idCount]['Exercicio'] = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['CodCredor'] = $rsRecordSet->getCampo('cod_credor');
    $arResult[$idCount]['Nome'] = $rsRecordSet->getCampo('nome');
    $arResult[$idCount]['InscricaoEstadual'] = $rsRecordSet->getCampo('insc_estadual');
    $arResult[$idCount]['InscricaoMunicipal'] = $rsRecordSet->getCampo('insc_municipal');
    $arResult[$idCount]['Endereco'] = $rsRecordSet->getCampo('endereco');
    $arResult[$idCount]['Cidade'] = $rsRecordSet->getCampo('cidade');
    $arResult[$idCount]['UF'] = $rsRecordSet->getCampo('uf');
    $arResult[$idCount]['Cep'] = $rsRecordSet->getCampo('cep');
    $arResult[$idCount]['Fone'] = $rsRecordSet->getCampo('fone');
    $arResult[$idCount]['Fax'] = $rsRecordSet->getCampo('fax');
    $arResult[$idCount]['Tipo'] = $rsRecordSet->getCampo('tipo');
    $arResult[$idCount]['NumeroDoRegistro'] = '';
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

unset($UndGestora, $CodUndGestora, $obTOrcamentoEntidade, $obTTCEALCredor);
?>
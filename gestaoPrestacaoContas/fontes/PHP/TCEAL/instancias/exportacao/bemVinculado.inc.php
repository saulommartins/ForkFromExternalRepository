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
    * Página de Include Oculta - Exportação Arquivos Relacionais - BemVinculado.xml
    *
    * Data de Criação: 05/08/2014
    *
    * @author: Carlos Adriano
    *
    $Id: bemVinculado.inc.php 59756 2014-09-09 19:50:47Z carlos.silva $
    *
    * @ignore
    *
*/
include_once CAM_GRH_ENT_MAPEAMENTO.'TEntidade.class.php';
include_once CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALBemVinculado.class.php';

$obTTCEALBemVinculado  = new TTCEALBemVinculado();
$codEntidadePrefeitura = SistemaLegado::pegaConfiguracao("cod_entidade_prefeitura", 8, Sessao::getExercicio());
$stNomeArquivo         = 'BemVinculado';

foreach (explode(',',$stEntidades) as $inCodEntidade) {
    $arEsquemasEntidades[] = $inCodEntidade;
}
    
foreach ($arEsquemasEntidades as $inCodEntidade) {
    
    $rsRecordSet = "rsBemVinculado";
    $rsRecordSet .= $stEntidade;
    $$rsRecordSet = new RecordSet();
    
    $obTTCEALBemVinculado->setDado('stExercicio'  , Sessao::getExercicio());
    $obTTCEALBemVinculado->setDado('inCodEntidade', $inCodEntidade );
    $obTTCEALBemVinculado->setDado('bimestre'     , $inBimestre  );
    $obTTCEALBemVinculado->setDado('dt_incial'    , $dtInicial );
    $obTTCEALBemVinculado->setDado('dt_final'     , $dtFinal );
    $obTTCEALBemVinculado->recuperaBemVinculado ($rsRecordSet);

    $idCount=0;
    $arResult = array();
    
    while (!$rsRecordSet->eof()) {
        $arResult[$idCount]['CodUndGestora']      = $rsRecordSet->getCampo('cod_und_gestora');
        $arResult[$idCount]['CodigoUA']           = $rsRecordSet->getCampo('codigo_ua');
        $arResult[$idCount]['Bimestre']           = $rsRecordSet->getCampo('bimestre');
        $arResult[$idCount]['Exercicio']          = $rsRecordSet->getCampo('exercicio');
        $arResult[$idCount]['CodOrgao']           = $rsRecordSet->getCampo('cod_orgao');
        $arResult[$idCount]['CodUndOrcamentaria'] = $rsRecordSet->getCampo('cod_und_orcamentaria');
        $arResult[$idCount]['NumBem']             = $rsRecordSet->getCampo('num_bem');
        $arResult[$idCount]['NumTombamento']      = $rsRecordSet->getCampo('num_tombamento');
        $arResult[$idCount]['NumEmpenho']         = $rsRecordSet->getCampo('num_empenho');
        
        $idCount++;
        
        $rsRecordSet->proximo();
    }
}
    
unset($UndGestora, $CodUndGestora, $obTTCEALServidor, $stPeriodoMovimentacao, $obTEntidade);
?>
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
    * Página de Include Oculta - Exportação Arquivos Execução - BalanceteReceita.xml
    *
    * Data de Criação: 12/11/2014
    *
    * @author: Evandro Melos
    *
    * $Id: BalanceteReceita.inc.php 60943 2014-11-25 18:56:56Z evandro $
    *
    * @ignore
    *
*/
include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOBalanceteReceita.class.php';

$obTTCETOBalanceteReceita = new TTCETOBalanceteReceita();

$obTTCETOBalanceteReceita->setDado('exercicio'   , Sessao::getExercicio());
$obTTCETOBalanceteReceita->setDado('cod_entidade', $inCodEntidade        );
$obTTCETOBalanceteReceita->setDado('und_gestora' , $inCodEntidade        );
$obTTCETOBalanceteReceita->setDado('dt_inicial'  , $stDataInicial        );
$obTTCETOBalanceteReceita->setDado('dt_final'    , $stDataFinal          );
$obTTCETOBalanceteReceita->setDado('bimestre'    , $inBimestre           );

$obTTCETOBalanceteReceita->recuperaBalanceteReceita($rsRecordSet);

$idCount = 0;
$stNomeArquivo = 'BalanceteReceita';
$arResult = array();

$rsRecordSet->addFormatacao("descricao","MB_SUBSTRING(0,255)");

while (!$rsRecordSet->eof()) {
        
    $arResult[$idCount]['idUnidadeGestora']            	    = $rsRecordSet->getCampo('cod_und_gestora');
    $arResult[$idCount]['bimestre']                 	    = $inBimestre;
    $arResult[$idCount]['exercicio']                	    = $rsRecordSet->getCampo('exercicio');
    $arResult[$idCount]['idOrgao']                 	        = $rsRecordSet->getCampo('cod_orgao');
    $arResult[$idCount]['idUnidadeOrcamentaria']       	    = $rsRecordSet->getCampo('cod_und_orcamentaria');
    $arResult[$idCount]['contaContabil']         	        = $rsRecordSet->getCampo('cod_conta_contabil');
    $arResult[$idCount]['idContaReceitaOrcamentaria']       = $rsRecordSet->getCampo('cod_conta_receita');
    $arResult[$idCount]['idRecursoVinculado'] 		        = $rsRecordSet->getCampo('cod_rec_vinculado');
    $arResult[$idCount]['valorReceitaOrcadaInicial']        = $rsRecordSet->getCampo('valor_receita_orcada_inicial');
    $arResult[$idCount]['valorReceitaOrcadaAtualizada']     = $rsRecordSet->getCampo('valor_receita_orcada_atualizada');
    $arResult[$idCount]['metaArrecadacaoBimestral']         = $rsRecordSet->getCampo('meta_arrecadacao_bimestral');
    $arResult[$idCount]['valorReceitaRealizadaBimestral']   = $rsRecordSet->getCampo('valor_receita_realizada_bimestral');
    $arResult[$idCount]['valorReceitaRealizadaAcumulada']   = $rsRecordSet->getCampo('valor_receita_realizada_acumulada');
    $arResult[$idCount]['descricao']		                = $rsRecordSet->getCampo('descricao');
    $arResult[$idCount]['tipoNivelConta']   	            = $rsRecordSet->getCampo('tipo_nivel_conta');
    $arResult[$idCount]['numeroNivelConta']        	        = $rsRecordSet->getCampo('num_nivel_conta');
    $arResult[$idCount]['caracteristicaPeculiar']           = $rsRecordSet->getCampo('carac_peculiar');
    
    $idCount++;
    
    $rsRecordSet->proximo();
}

return $arResult;
?>
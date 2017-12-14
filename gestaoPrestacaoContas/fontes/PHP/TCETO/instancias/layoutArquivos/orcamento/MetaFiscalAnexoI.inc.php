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
  * $Id: MetaFiscalAnexoI.inc.php 60955 2014-11-26 13:42:24Z michel $
  * $Date: 2014-11-26 11:42:24 -0200 (Wed, 26 Nov 2014) $
  * $Author: michel $
  * $Rev: 60955 $
  *
*/

include_once CAM_GPC_TCETO_MAPEAMENTO.'TTCETOMetaFiscalAnexoI.class.php';

$obTTCETOMetaFiscalAnexoI = new TTCETOMetaFiscalAnexoI();

$obTTCETOMetaFiscalAnexoI->setDado('exercicio'    , $inExercicio   );
$obTTCETOMetaFiscalAnexoI->setDado('cod_entidade' , $inCodEntidade );
$obTTCETOMetaFiscalAnexoI->recuperaUndGestora($rsRecordSet);

$stNomeArquivo = 'MetaFiscalAnexoI';
$idCount=0;

$tceto_config_metas_receitas_anuais      = SistemaLegado::pegaConfiguracao('tceto_config_metas_receitas_anuais', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_receitas_primarias         = SistemaLegado::pegaConfiguracao('tceto_config_receitas_primarias', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_metas_despesas_anuais      = SistemaLegado::pegaConfiguracao('tceto_config_metas_despesas_anuais', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_despesas_primarias         = SistemaLegado::pegaConfiguracao('tceto_config_despesas_primarias', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_resultado_primario         = SistemaLegado::pegaConfiguracao('tceto_config_resultado_primario', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_resultado_nominal          = SistemaLegado::pegaConfiguracao('tceto_config_resultado_nominal', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_divida_publica_consolidada = SistemaLegado::pegaConfiguracao('tceto_config_divida_publica_consolidada', 64, Sessao::getExercicio(), $boTransacao);
$tceto_config_divida_consolidada_liquida = SistemaLegado::pegaConfiguracao('tceto_config_divida_consolidada_liquida', 64, Sessao::getExercicio(), $boTransacao);

$arResult[0]['idUnidadeGestora']         = $rsRecordSet->getCampo('cod_und_gestora');
$arResult[0]['bimestre']                 = $inBimestre;
$arResult[0]['exercicio']                = Sessao::getExercicio();

$arResult[0]['metaReceitaAnual']         = str_replace(',', '.', str_replace('.', '',$tceto_config_metas_receitas_anuais        ));
$arResult[0]['receitaPrimaria']          = str_replace(',', '.', str_replace('.', '',$tceto_config_receitas_primarias           ));
$arResult[0]['metaDespesaAnual']         = str_replace(',', '.', str_replace('.', '',$tceto_config_metas_despesas_anuais        ));
$arResult[0]['despesaPrimaria']          = str_replace(',', '.', str_replace('.', '',$tceto_config_despesas_primarias           ));
$arResult[0]['resultadoPrimario']        = str_replace(',', '.', str_replace('.', '',$tceto_config_resultado_primario           ));
$arResult[0]['resultadoNominal']         = str_replace(',', '.', str_replace('.', '',$tceto_config_resultado_nominal            ));
$arResult[0]['dividaPublicaConsolidada'] = str_replace(',', '.', str_replace('.', '',$tceto_config_divida_publica_consolidada   ));
$arResult[0]['dividaConsolidadaLiquida'] = str_replace(',', '.', str_replace('.', '',$tceto_config_divida_consolidada_liquida   ));
    
    

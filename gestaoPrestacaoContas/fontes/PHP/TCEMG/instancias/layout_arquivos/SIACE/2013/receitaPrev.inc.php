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
/*
    * Arquivo de geracao do arquivo receitaPrev TCM/MG
    * Data de Criação   : 22/01/2009
    
    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz
    
    * @package URBEM
    * @subpackage
    
    * @ignore
    
    $Id: receitaPrev.inc.php 62777 2015-06-17 13:47:51Z michel $
*/

include_once( CAM_GPC_TCEMG_MAPEAMENTO.Sessao::getExercicio().'/FTCEMGReceitaPrev.class.php');

$arFiltros = Sessao::read('filtroRelatorio');

$obFTCEMGReceitaPrev = new FTCEMGReceitaPrev();
foreach ($arDatasInicialFinal as $stDatas) {
    $obFTCEMGReceitaPrev->setDado('exercicio'   , Sessao::getExercicio());
    $obFTCEMGReceitaPrev->setDado('cod_entidade', implode(',',$arFiltros['inCodEntidadeSelecionado']));
    $obFTCEMGReceitaPrev->setDado('dt_inicial'  , $stDatas['stDtInicial'] );
    $obFTCEMGReceitaPrev->setDado('dt_final'    , $stDatas['stDtFinal'] );
    $obFTCEMGReceitaPrev->recuperaTodos($rsReceitaPrev, "", " ORDER BY cod_tipo ");
    
    $obExportador->roUltimoArquivo->addBloco($rsReceitaPrev);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('periodo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('cod_tipo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('NUMERICO_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contrib_pat');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contrib_serv_ativo');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contrib_serv_inat_pens');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('rec_patrimoniais');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('alienacao_bens');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outras_rec_cap');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('comp_prev');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('outras_rec');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('contrib_pat_anterior');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('repasses_prev');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('receitas_prev_intra');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('deducoes_receita');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_fin_recursos_cobertura');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_fin_repasse_pag_resp');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_fin_rec_formacao_reserva');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_fin_outros_aportes_rpps');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_prev_rec_cob_def_fin');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_prev_rec_cob_def_atuarial');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    $obExportador->roUltimoArquivo->roUltimoBloco->setDelimitador(';');
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna('plano_prev_outro_aportes_rpps');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado('VALOR_ZEROS_ESQ');
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
}
unset($arFiltros);
unset($obFTCEMGReceitaPrev);
unset($rsReceitaPrev);

?>
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
    * Página de Formulario de Manter Adjudicacao
    * Data de Criação: 23/10/2006

    * @author Analista: Anelise Schwengber
    * @author Desenvolvedor: Andre Almeida

    * @ignore

    $Id: $

    * Casos de uso: uc-03.05.20
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function geraArquivosExecOrcamentaria(&$obExportador , $stDataInicial, $stDataFinal)
{
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAnulado.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacao.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoItemAnulado.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPaga.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoContaPagadora.class.php"  );
    include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoNotaLiquidacaoPagaAnulada.class.php"  );

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');

    $obTEmpenhoEmpenho = new TEmpenhoEmpenho;
    $obTEmpenhoEmpenho->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoEmpenho->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoEmpenho->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoEmpenho->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoEmpenho->recuperaEmpenhoEsfinge( $rsEmpenho );

    $obExportador->addArquivo("empenho.txt");
    $obExportador->roUltimoArquivo->addBloco($rsEmpenho);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cat_economica");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_grupo_natureza");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_modalidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_elemento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_fonte");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_acao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_pao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_historico");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(640);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sem_num_licitacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sem_num_contrato");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sem_num_convenio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_pessoa");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_cic");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(100);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obTEmpenhoEmpenhoAnulado = new TEmpenhoEmpenhoAnulado;
    $obTEmpenhoEmpenhoAnulado->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoEmpenhoAnulado->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoEmpenhoAnulado->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoEmpenhoAnulado->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoEmpenhoAnulado->recuperaEstornoEmpenhoEsfinge( $rsEstornoEmpenho );

    $obExportador->addArquivo("estornoempenho.txt");
    $obExportador->roUltimoArquivo->addBloco($rsEstornoEmpenho);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("motivo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obTEmpenhoNotaLiquidacao = new TEmpenhoNotaLiquidacao;
    $obTEmpenhoNotaLiquidacao->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoNotaLiquidacao->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoNotaLiquidacao->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoNotaLiquidacao->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoNotaLiquidacao->recuperaLiquidacaoEsfinge( $rsLiquidacao );
    
    $obExportador->addArquivo("liquidacao.txt");
    $obExportador->roUltimoArquivo->addBloco($rsLiquidacao);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_total");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obTEmpenhoNotaLiquidacaoItemAnulado = new TEmpenhoNotaLiquidacaoItemAnulado;
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoNotaLiquidacaoItemAnulado->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoNotaLiquidacaoItemAnulado->recuperaEstornoLiquidacaoEsfinge( $rsEstornoLiquidacao );

    $obExportador->addArquivo("estornoliquidacao.txt");
    $obExportador->roUltimoArquivo->addBloco($rsEstornoLiquidacao );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_liquidacao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(255);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obTEmpenhoNotaLiquidacaoPaga = new TEmpenhoNotaLiquidacaoPaga;
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoNotaLiquidacaoPaga->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoNotaLiquidacaoPaga->recuperaPagamentoEmpenhoEsfinge( $rsPagamentoEmpenho );
    
    $obExportador->addArquivo("pagamentoempenho.txt");
    $obExportador->roUltimoArquivo->addBloco($rsPagamentoEmpenho);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_vencimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
    
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obTEmpenhoNotaLiquidacaoContaPagadora = new TEmpenhoNotaLiquidacaoContaPagadora;
    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoNotaLiquidacaoContaPagadora->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoNotaLiquidacaoContaPagadora->recuperaDesembolsoEsfinge( $rsDesembolso );

    $obExportador->addArquivo("desembolso.txt");
    $obExportador->roUltimoArquivo->addBloco($rsDesembolso);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("exercicio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_estrutural");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_pago");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obTEmpenhoNotaLiquidacaoPagaAnulada = new TEmpenhoNotaLiquidacaoPagaAnulada;
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'exercicio'   , $arFiltroRelatorio['stExercicio'] );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'cod_entidade', implode(",", $arFiltroRelatorio['inCodEntidade'] ) );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'dt_inicial'  , $stDataInicial );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->setDado( 'dt_final'  , $stDataFinal );
    $obTEmpenhoNotaLiquidacaoPagaAnulada->recuperaEstornoPagamentoEsfinge( $rsEstornoPagamento );

    $obExportador->addArquivo("estornopagamento.txt");
    $obExportador->roUltimoArquivo->addBloco($rsEstornoPagamento );

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_empenho");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("timestamp_anulada");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("DATA_YYYYMMDD");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setCampoObrigatorio( false );
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vl_anulado");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(16);

}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>

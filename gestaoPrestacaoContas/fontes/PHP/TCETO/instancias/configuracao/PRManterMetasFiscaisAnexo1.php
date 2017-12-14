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
    * Pacote de configuração do TCETO - Processamento Configurar Metas Fiscais Anexo 1
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterMetasFiscaisAnexo1.php 60820 2014-11-17 18:51:54Z lisiane $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'configurar' :
        $obTExportacaoConfiguracao = new TAdministracaoConfiguracao;
        $obTExportacaoConfiguracao->setDado("cod_modulo", 64);
        $obTExportacaoConfiguracao->setDado("exercicio",  Sessao::getExercicio());

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_metas_receitas_anuais");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuMetasReceitasAnuais']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_receitas_primarias");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuReceitasPrimarias']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_metas_despesas_anuais");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuMetasDespesasAnuais']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_despesas_primarias");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuDespesasPrimarias']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_resultado_primario");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuResultadoPrimario']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_resultado_nominal");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuResultadoNominal']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_divida_publica_consolidada");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuDividaPublicaConsolidada']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_divida_consolidada_liquida");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['nuDividaConsolidadaLiquida']);
        $obTExportacaoConfiguracao->alteracao();

        SistemaLegado::alertaAviso('FMManterMetasFiscaisAnexo1.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
    break;
}

Sessao::encerraExcecao();
?>

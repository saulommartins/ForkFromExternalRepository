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
    * Pacote de configuração do TCETO - Processamento Configurar Orçamento
    * Data de Criação   : 05/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterOrcamento.php 60778 2014-11-14 17:55:19Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCETO_MAPEAMENTO."TTCETOAlteracaoLeiPPA.class.php";
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'configurar' :
        $obTExportacaoConfiguracao = new TAdministracaoConfiguracao;
        $obTExportacaoConfiguracao->setDado("cod_modulo", 64);
        $obTExportacaoConfiguracao->setDado("exercicio",  Sessao::getExercicio());

        $obTExportacaoConfiguracao->setDado("parametro", "tceto_config_cod_norma");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['inCodNorma']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_complementacao_loa");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['stComplementacaoLoa']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_adicional");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['vlCreditoAdicional']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_antecipacao");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['vlCreditoAntecipacao']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_interno");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['vlCreditoInterno']);
        $obTExportacaoConfiguracao->alteracao();

        $obTExportacaoConfiguracao->setDado("parametro",  "tceto_config_credito_externo");
        $obTExportacaoConfiguracao->setDado("valor", $_REQUEST['vlCreditoExterno']);
        $obTExportacaoConfiguracao->alteracao();
        
        if ($_REQUEST['hidden'] == -1) {            
            SistemaLegado::alertaAviso('FMManterOrcamento.php?'.Sessao::getId().'&stAcao=configurar', 'Deve haver ao menos um item na lista de Alteração da PPA','n_incluir','erro', Sessao::getId(), '../');
            Sessao::encerraExcecao();
            exit();
        }else{
            $obTTCETOAlteracaoLeiPPA = new TTCETOAlteracaoLeiPPA();
            
            $arAlteracaoLei = Sessao::read('arAlteracaoLei');
            foreach ($arAlteracaoLei as $key => $lei) {
                $obTTCETOAlteracaoLeiPPA->setDado('cod_norma'     , $lei['cod_norma']          );
                $obTTCETOAlteracaoLeiPPA->setDado('data_alteracao', $lei['data_alteracao_lei'] );
                $obTTCETOAlteracaoLeiPPA->inclusao( $boTransacao );
            }
        }

        SistemaLegado::alertaAviso('FMManterOrcamento.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
    break;
}

Sessao::encerraExcecao();
?>

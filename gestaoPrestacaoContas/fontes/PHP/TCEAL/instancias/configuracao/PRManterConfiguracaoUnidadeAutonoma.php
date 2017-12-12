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

    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
    * 
    * $id: 
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php");
include_once(CAM_GPC_TCEAL_MAPEAMENTO.'TTCEALConfiguracaoUnidadeAutonoma.class.php');

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'manter' :
        //Consulta
        foreach ($_REQUEST as $key => $value) {
            if (strstr($key, 'inCodigo_')) {
                $obTExportacaoConfiguracao = new TTCEALConfiguracaoUnidadeAutonoma;
                $obTExportacaoConfiguracao->setDado("cod_modulo", 62);
                $obTExportacaoConfiguracao->setDado("parametro",  "tceal_configuracao_unidade_autonoma");
                $obTExportacaoConfiguracao->setDado("exercicio",  Sessao::getExercicio());
                $obTExportacaoConfiguracao->setDado("cod_entidade",  substr($key, 9));
                $obTExportacaoConfiguracao->setDado("valor", $value);
                $obTExportacaoConfiguracao->alteracao();

            }
        }

        SistemaLegado::alertaAviso('FMManterConfiguracaoUnidadeAutonoma.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
    break;
}

Sessao::encerraExcecao();
?>

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
    * Titulo do arquivo : Arquivo de processamento da configuração do arquivo de licitações
    * Data de Criação   : 29/03/2011

    * @author : Eduardo Paculski Schitz

    * @package URBEM
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMConfiguracaoArquivoLicitacao.class.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'incluir' :
        foreach ($_REQUEST as $stKey => $stValue) {
            $obTTCEAMConfiguracaoArquivoLicitacao = new TTCEAMConfiguracaoArquivoLicitacao;
            $boIncluir = false;
            if (strpos($stKey,'stDiarioOficial') !== false) {
                $boIncluir = true;
                $obTTCEAMConfiguracaoArquivoLicitacao->setDado('diario_oficial', $stValue);
            } elseif (strpos($stKey,'dtPublicacaoHomologacao') !== false) {
                $boIncluir = true;
                $obTTCEAMConfiguracaoArquivoLicitacao->setDado('dt_publicacao_homologacao', $stValue);
            }

            if ($boIncluir) {
                $arLicitacao = explode('_',$stKey);
                $obTTCEAMConfiguracaoArquivoLicitacao->setDado('cod_mapa' , $arLicitacao[1]);
                $obTTCEAMConfiguracaoArquivoLicitacao->setDado('exercicio', $arLicitacao[2]);
                $obTTCEAMConfiguracaoArquivoLicitacao->recuperaPorChave($rsVinculo);

                if ($rsVinculo->getNumLinhas() < 1) {
                    $obTTCEAMConfiguracaoArquivoLicitacao->inclusao();
                } else {
                    $obTTCEAMConfiguracaoArquivoLicitacao->alteracao();
                }
            }
        }

        if (!$obErro->ocorreu) {
            SistemaLegado::alertaAviso('FLConfigurarArquivoLicitacao.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
        } else {
            sistemaLegado::exibeAviso('', 'n_incluir', 'erro');
        }
}

Sessao::encerraExcecao();

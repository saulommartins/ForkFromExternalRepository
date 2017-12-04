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

    * Pacote de configuração do TCEPE
    * Data de Criação   : 26/09/2014

    * @author Analista: Silvia Martins
    * @author Desenvolvedor: Lisiane Morais
    * 
    * $id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GPC_TCEPE_MAPEAMENTO.'TTCEPEConfiguracaoUnidadeGestora.class.php');

$stAcao = $request->get('stAcao');
Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'configurar' :
        //Consulta
        foreach ($_REQUEST as $key => $value) {
            if (strstr($key, 'inCodigo_')) {
                $obTTCEPEConfiguracaoUnidadeGestora = new TTCEPEConfiguracaoUnidadeGestora;
                $obTTCEPEConfiguracaoUnidadeGestora->setDado("cod_modulo", 63);
                $obTTCEPEConfiguracaoUnidadeGestora->setDado("parametro",  "tcepe_codigo_unidade_gestora");
                $obTTCEPEConfiguracaoUnidadeGestora->setDado("exercicio",  Sessao::getExercicio());
                $obTTCEPEConfiguracaoUnidadeGestora->setDado("cod_entidade",  substr($key, 9));
                $obTTCEPEConfiguracaoUnidadeGestora->setDado("valor", $value);
                $obTTCEPEConfiguracaoUnidadeGestora->alteracao();

            }
        }

        SistemaLegado::alertaAviso('FMManterConfiguracaoUnidadeGestora.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
    break;
}

Sessao::encerraExcecao();
?>

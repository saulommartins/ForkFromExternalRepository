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
    * Titulo do arquivo : Arquivo de processamento do vinculo do elenco de contas do TCE
    * Data de Criação   : 22/03/2011

    * @author : Eduardo Paculski Schitz

    * @package URBEM
    * $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEAM_MAPEAMENTO.'TTCEAMVinculoElencoPlanoContas.class.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

switch ($stAcao) {
    case 'incluir' :
        $obTTCEAMVinculoElencoPlanoContas = new TTCEAMVinculoElencoPlanoContas;
        $stChave = $obTTCEAMVinculoElencoPlanoContas->getComplementoChave();
        $obTTCEAMVinculoElencoPlanoContas->setComplementoChave('exercicio_plano');
        $obTTCEAMVinculoElencoPlanoContas->setDado('exercicio_plano', Sessao::getExercicio());
        $obTTCEAMVinculoElencoPlanoContas->exclusao();
        $obTTCEAMVinculoElencoPlanoContas->setComplementoChave($stChave);

        foreach ($_REQUEST as $stKey => $stValue) {
            if (strpos($stKey,'slElenco') !== false AND $stValue != '') {
                $arConta  = explode('_',$stKey);
                $arElenco = explode('_',$stValue);

                $obTTCEAMVinculoElencoPlanoContas->setDado('cod_plano'       , $arConta[1]);
                $obTTCEAMVinculoElencoPlanoContas->setDado('exercicio_plano' , $arConta[2]);
                $obTTCEAMVinculoElencoPlanoContas->setDado('cod_elenco'      , $arElenco[0]);
                $obTTCEAMVinculoElencoPlanoContas->setDado('exercicio_elenco', $arElenco[1]);
                $obErro = $obTTCEAMVinculoElencoPlanoContas->inclusao();
            }
        }

        if (!$obErro->ocorreu) {
            SistemaLegado::alertaAviso('FLVincularPlanoTCE.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
        } else {
            sistemaLegado::exibeAviso('', 'n_incluir', 'erro');
        }
}

Sessao::encerraExcecao();

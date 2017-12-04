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
include_once TTGO.'TTCMGOVinculoPlanoContas.class.php';
include_once TTGO.'TTCMGORecuperaPlanoConfiguracaoTCM.class.php';

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao( true );

$arContasConfiguradas = array();
foreach ($_REQUEST as $stKey => $stValue) {
    if (strpos($stKey,'slPlano') !== false AND $stValue != '') {
        $arConta  = explode('_',$stKey);
        $arElenco = explode('_',$stValue);

        $arContasConfiguradas[$arConta[1]] = array(
            'cod_conta'       => $arConta[1],
            'cod_plano_tcmgo' => $arElenco[0]
        );
    }
}

switch ($stAcao) {
    case 'incluir' :
        $obTTCMGORecuperaPlanoConfiguracaoTCM = new TTCMGORecuperaPlanoConfiguracaoTCM;
        $obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('exercicio'   , Sessao::getExercicio());
        $obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('entidades'   , $_REQUEST['inCodEntidade']);
        $obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('data_inicial', '01/'.$_REQUEST['inMes'].'/'.Sessao::getExercicio());
        $obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('data_final'  , SistemaLegado::retornaUltimoDiaMes($_REQUEST['inMes'], Sessao::getExercicio()));
        $obTTCMGORecuperaPlanoConfiguracaoTCM->setDado('grupo'       , $_REQUEST['stGrupo']);
        $obTTCMGORecuperaPlanoConfiguracaoTCM->recuperaTodos($rsEstrutural);

        $stExercicio = Sessao::getExercicio();

        $obTTCMGOVinculoPlanoContas = new TTCMGOVinculoPlanoContas;
        $obTTCMGOVinculoPlanoContas->setDado('exercicio'      , $stExercicio);
        $obTTCMGOVinculoPlanoContas->setDado('exercicio_tcmgo', $stExercicio);

        while (!$rsEstrutural->EOF()) {
            $inCodConta = $rsEstrutural->getCampo('cod_conta');

            if (array_key_exists($inCodConta, $arContasConfiguradas)) {
                $obTTCMGOVinculoPlanoContas->setDado('cod_conta'      , $arContasConfiguradas[$inCodConta]['cod_conta']);
                $obTTCMGOVinculoPlanoContas->setDado('cod_plano_tcmgo', $arContasConfiguradas[$inCodConta]['cod_plano_tcmgo']);
                
                $obTTCMGOVinculoPlanoContas->recuperaPorChave($rsVinculoPlanoContas);

                if ($rsVinculoPlanoContas->getNumLinhas() > 0) {
                    $obErro = $obTTCMGOVinculoPlanoContas->alteracao();
                } else {
                    $obErro = $obTTCMGOVinculoPlanoContas->inclusao();
                }
            } else {
                $obTTCMGOVinculoPlanoContas->setDado('cod_conta', $inCodConta);
                $obTTCMGOVinculoPlanoContas->exclusao();
            }

            $rsEstrutural->proximo();
        }

        if (!$obErro->ocorreu) {
            SistemaLegado::alertaAviso('FLVincularPlanoTCE.php?'.Sessao::getId().'&stAcao='.$stAcao, 'Configuração ','incluir','incluir_n', Sessao::getId(), '../');
        } else {
            sistemaLegado::exibeAviso('', 'n_incluir', 'erro');
        }
}

Sessao::encerraExcecao();

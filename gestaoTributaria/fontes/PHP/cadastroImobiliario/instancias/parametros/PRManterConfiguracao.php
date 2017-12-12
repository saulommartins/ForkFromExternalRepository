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
 * Página de processamento para a configuração do módulo
 * Data de Criação   : 31/08/2004

 * @author Analista: Ricardo Lopes de Alencar
 * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

 * @ignore

 * $Id: PRManterConfiguracao.php 63672 2015-09-28 19:24:31Z arthur $

 * Casos de uso: uc-05.01.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgProc = "PR".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

$obRCIMConfiguracao = new RCIMConfiguracao();
$obErro  = new Erro();
$boTransacao = new Transacao();

switch ($stAcao) {
    case "alterar":
        if ( count($request->get("inCodOrdemSelecionados") ) < $request->get("inNumeroOrdens") ) {
            $obErro->setDescricao( "Campo Ordem de entrega inválido!(Todos os itens devem ser selecionados)");
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"unica","aviso");
            exit();
        } else {
            $obRCIMConfiguracao->setNavegacaoAutomatico ( $request->get("stNavegacaoAuto") );
            $obRCIMConfiguracao->setCodigoLocal  ( $request->get("boCodigoLocal") );
            $obRCIMConfiguracao->setMascaraLote  ( $request->get("stMascaraLote") );
            $obRCIMConfiguracao->setMascaraIM    ( $request->get("stMascaraIM")   );
            $obRCIMConfiguracao->setNumeroIM     ( $request->get("boNumeroIM")    );
            $obRCIMConfiguracao->setAnoExercicio ( Sessao::getExercicio()     );

            if ($request->get("inCodAtributosLoteUrbanoSelecionados")) {
                foreach ($request->get("inCodAtributosLoteUrbanoSelecionados") as $stValor) {
                    $obRCIMConfiguracao->addAtbLotUrbano( $stValor );
                }
            }

            if ($request->get("inCodAtributosLoteRuralSelecionados")) {
                foreach ($request->get("inCodAtributosLoteRuralSelecionados") as $stValor) {
                    $obRCIMConfiguracao->addAtbLotRural( $stValor );
                }
            }

            if ($request->get("inCodAtributosImovelSelecionados")) {
                foreach ($request->get("inCodAtributosImovelSelecionados") as $stValor) {
                    $obRCIMConfiguracao->addAtbImovel( $stValor );
                }
            }

            if ($request->get("inCodAtributosEdificacaoSelecionados")) {
                foreach ($request->get("inCodAtributosEdificacaoSelecionados") as $stValor) {
                    $obRCIMConfiguracao->addAtbEdificacao( $stValor );
                }
            }

            if ($request->get("inCodOrdemSelecionados")) {
                foreach ($request->get("inCodOrdemSelecionados") as $stOrdemEntrega) {
                    $obRCIMConfiguracao->addOrdemEntrega( $stOrdemEntrega );
                }
            }

            if ($request->get("inCodValorMDSelecionados")) {
                foreach ($request->get("inCodValorMDSelecionados") as $stValorMD) {
                    $obRCIMConfiguracao->addValorMD( $stValorMD );
                }
            }

            if ($request->get("inCodAliquotasSelecionados")) {
                foreach ($request->get("inCodAliquotasSelecionados") as $stValorMD) {
                    $obRCIMConfiguracao->addAliquota( $stValorMD );
                }
            }

            $obErro = $obRCIMConfiguracao->alterarConfiguracao($boTransacao);

        }

        if ( !$obErro->ocorreu() )
            SistemaLegado::alertaAviso($pgForm,"Configuração","alterar","aviso", Sessao::getId(), "../");
        else
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");

    break;
}

?>
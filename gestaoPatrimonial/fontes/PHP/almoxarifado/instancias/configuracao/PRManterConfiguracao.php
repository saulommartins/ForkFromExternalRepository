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
    * Página de Processamento de Configuração para Relatórios MODELOS
    * Data de Criação   : 22/05/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Id: PRManterConfiguracao.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.04.08

*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(TALM."TAlmoxarifadoConfiguracao.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracao";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$pgJS      = "JS".$stPrograma.".js";

$obTConfiguracao = new TAlmoxarifadoConfiguracao();

$stAcao = $request->get('stAcao');

$stExercicio = Sessao::read('exercicio');

switch ($stAcao) {

    default:
        $obTConfiguracao->setDado("parametro","numeracao_lancamento_estoque");
        $obTConfiguracao->setDado("valor"    ,$_REQUEST['stNumeracao']);
        $obTConfiguracao->recuperaPorChave($rsConf);
        if ( $rsConf->eof() ) {
            $obTConfiguracao->inclusao();
        } else {
            $obTConfiguracao->alteracao();
        }

        $obTConfiguracao->setDado("exercicio","$stExercicio");
        $obTConfiguracao->setDado("cod_modulo","29");
        $obTConfiguracao->setDado("parametro","demonstrar_saldo_estoque");
        $obTConfiguracao->setDado("valor"    ,$_REQUEST['stDemonstrarSaldo']);
        $obTConfiguracao->recuperaPorChave($rsConf);

        if ( $rsConf->eof() ) {
            $obTConfiguracao->inclusao();
        } else {
            $obTConfiguracao->alteracao();
        }

        $obTConfiguracao->setDado("exercicio","$stExercicio");
        $obTConfiguracao->setDado("cod_modulo","29");
        $obTConfiguracao->setDado("parametro","anular_saldo_pendente");
        $obTConfiguracao->setDado("valor"    ,$_REQUEST['stAnularSaldo']);
        $obTConfiguracao->recuperaPorChave($rsConf);
        if ( $rsConf->eof() ) {
            $obTConfiguracao->inclusao();
        } else {
            $obTConfiguracao->alteracao();
        }

        $obTConfiguracao->setDado("exercicio","$stExercicio");
        $obTConfiguracao->setDado("cod_modulo","29");
        $obTConfiguracao->setDado("parametro","homologacao_automatica_requisicao");
        $obTConfiguracao->setDado("valor"    ,$_REQUEST['stHomologacaoAutomatica']);
        $obTConfiguracao->recuperaPorChave($rsConf);
        if ( $rsConf->eof() ) {
            $obTConfiguracao->inclusao();
        } else {
            $obTConfiguracao->alteracao();
        }

        $stAcao = Sessao::read('acao')        ;

        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&acao=$stAcao","Configuração","alterar","aviso", Sessao::getId(), "../");
    break;
}

?>

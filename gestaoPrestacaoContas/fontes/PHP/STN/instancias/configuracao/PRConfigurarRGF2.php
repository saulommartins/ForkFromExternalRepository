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
    * Processamento de Configuração do Anexo 2 RGF
    * Data de Criação   : 28/05/2013

    * @author Desenvolvedor: Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage Configuração
*/

//inclui os arquivos necessarios
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNContasRGF2.class.php" );
require_once( CAM_GPC_STN_MAPEAMENTO."TSTNVinculoContasRGF2.class.php" );

$stPrograma = "ConfigurarRGF2";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTSTNContasRGF2 = new TSTNContasRGF2;
$obTSTNVinculoContasRGF2 = new TSTNVinculoContasRGF2;

$obTSTNContasRGF2->listarContasRGF2($rsContas);

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obErro = new Erro;
$stMensagem = "";
while (!$rsContas->eof()) {
    $arVinculos = Sessao::read("arVinculoContas_".$rsContas->getCampo('cod_conta'));

    foreach ($arVinculos as $arVinculo) {
        $obTSTNVinculoContasRGF2->setDado('cod_conta', $arVinculo['cod_conta']);
        $obTSTNVinculoContasRGF2->setDado('cod_plano', $arVinculo['cod_plano']);
        $obTSTNVinculoContasRGF2->setDado('exercicio', Sessao::getExercicio());

        $obErro = $obTSTNVinculoContasRGF2->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            $stMensagem = "Erro ao incluir!";
            break 2;
        }
    }

    $rsContas->proximo();
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSTNVinculoContasRGF2 );
if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso($stMensagem);
} else {
    SistemaLegado::alertaAviso($pgForm, "Configuração realizada com sucesso!", 'incluir', 'aviso');
}

?>

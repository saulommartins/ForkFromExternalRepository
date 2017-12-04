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
    * Processamento de Configuração do Anexo 4
    * Data de Criação   : 05/04/2013

    * @author Desenvolvedor: Davi Ritter Aroldi

    * @package URBEM
    * @subpackage Configuração

    * Casos de uso: uc-02.08.07
*/

//inclui os arquivos necessarios
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_STN_MAPEAMENTO."TSTNAporteRecursoRPPSReceita.class.php";
require_once CAM_GPC_STN_MAPEAMENTO."TSTNAporteRecursoRPPS.class.php";

$stPrograma = "ConfigurarAnexo4";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$obTSTNAporteRecursoRPPS = new TSTNAporteRecursoRPPS;
$obTSTNAporteRecursoRPPSReceita = new TSTNAporteRecursoRPPSReceita;

$obTSTNAporteRecursoRPPS->listarAporteRecursoRPPS($rsAportes);

$obTransacao = new Transacao;
$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

$obErro = new Erro;
$stMensagem = "";
while (!$rsAportes->eof()) {
    $arReceitaAporte = Sessao::read("arReceitaAporte_".$rsAportes->getCampo('cod_aporte'));

    foreach ($arReceitaAporte as $receitaAporte) {
        $obTSTNAporteRecursoRPPSReceita->setDado('cod_aporte', $receitaAporte['cod_aporte']);
        $obTSTNAporteRecursoRPPSReceita->setDado('cod_receita', $receitaAporte['cod_receita']);
        $obTSTNAporteRecursoRPPSReceita->setDado('exercicio', Sessao::getExercicio());

        $obErro = $obTSTNAporteRecursoRPPSReceita->inclusao($boTransacao);

        if ($obErro->ocorreu()) {
            $stMensagem = "Erro ao incluir!";
            break 2;
        }
    }

    $rsAportes->proximo();
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTSTNAporteRecursoRPPSReceita );
if ($obErro->ocorreu()) {
    SistemaLegado::exibeAviso($stMensagem);
} else {
    SistemaLegado::alertaAviso($pgForm, "Configuração realizada com sucesso!", 'incluir', 'aviso');
}

?>

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
    * Página de Processamento de Encerramento Mês
    * Data de Criação   : 06/11/2012

    * @author Analista:
    * @author Desenvolvedor: Davi Ritter Aroldi

    * @ignore

    * Casos de uso: uc-02.02.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterEncerramentoMes";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

$arMeses   = array(
    1 => "Janeiro",
    2 => "Fevereiro",
    3 => "Março",
    4 => "Abril",
    5 => "Maio",
    6 => "Junho",
    7 => "Julho",
    8 => "Agosto",
    9 => "Setembro",
    10 => "Outubro",
    11 => "Novembro",
    12 => "Dezembro"
);

switch ($stAcao) {
    case 'encerrar':
            $obTransacao = new Transacao;
            $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
            $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
            $obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
            for ($i = $_REQUEST['inCodMes']; $i > 0; $i--) {
                $obTContabilidadeEncerramentoMes->setDado('mes', $i);

                $obErro = $obTContabilidadeEncerramentoMes->inclusao($boTransacao);

                if ( $obErro->ocorreu() ) {
                    break;
                }
            }

            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeEncerramentoMes);

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm."?stAcao=".$stAcao,"Mês ".$arMeses[$_REQUEST['inCodMes']],"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        break;

    case 'reabrir':
            $obTransacao = new Transacao;
            $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

            $obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
            $obTContabilidadeEncerramentoMes->setDado('mes', $_REQUEST['inCodMes']);
            $obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
            $obTContabilidadeEncerramentoMes->setDado('situacao', 'A');
            $obErro = $obTContabilidadeEncerramentoMes->inclusao($boTransacao);

            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeEncerramentoMes);

            if ( !$obErro->ocorreu() ) {
                SistemaLegado::alertaAviso($pgForm."?stAcao=".$stAcao,"Mês ".$arMeses[$_REQUEST['inCodMes']],"alterar","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
            }
        break;
}
?>

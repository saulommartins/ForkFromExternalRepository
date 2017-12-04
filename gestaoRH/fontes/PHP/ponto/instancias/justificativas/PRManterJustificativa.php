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
/*
 * Página de Processamento para Manter Justificativa
 * Data de Criação: 29/09/2008

 * @author Analista      Dagiane Vieira
 * @author Desenvolvedor Alex Cardoso

 * @package URBEM
 * @subpackage

 * @ignore

 $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativa.class.php"                                   );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoJustificativaHoras.class.php"                              );

//Define o nome dos arquivos PHP
$stPrograma = "ManterJustificativa";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stAcao             = ($_POST['stAcao']!="") ? $_POST['stAcao'] : $_GET['stAcao'];
$stLink             = "?stAcao=$stAcao";

$obTPontoJustificativa      = new TPontoJustificativa();
$obTPontoJustificativaHoras = new TPontoJustificativaHoras();

$obTPontoJustificativaHoras->obTPontoJustificativa = &$obTPontoJustificativa;
switch ($stAcao) {
    case "incluir":
        Sessao::setTrataExcecao(true);
        $stFiltro = " WHERE descricao ilike '".$_POST['stDescricao']."'";
        $obTPontoJustificativa->recuperaTodos($rsJustificativa, $stFiltro);
        if ($rsJustificativa->getNumLinhas() == 1) {
            Sessao::getExcecao()->setDescricao("A descrição ".$_POST['stDescricao']." já foi inserida no banco.");
        }

        $obTPontoJustificativa->setDado('descricao', $_POST['stDescricao']);
        $obTPontoJustificativa->setDado('anular_faltas', ($_POST['boAnularFaltas'] == 1)?'true':'');
        $obTPontoJustificativa->setDado('lancar_dias_trabalho', ($_POST['boLancarDias'] == 1)?'true':'');
        $obTPontoJustificativa->inclusao();

        if (!$_POST['boAnularFaltas'] && !Sessao::getExcecao()->ocorreu()) {
            $stHorasFalta = formataQuantidadeHoras($_POST['stHorasFalta']);
            $stHorasAbono = formataQuantidadeHoras($_POST['stHorasAbono']);

            $obTPontoJustificativaHoras->setDado('horas_abono', $stHorasAbono);
            $obTPontoJustificativaHoras->setDado('horas_falta', $stHorasFalta);
            $obTPontoJustificativaHoras->inclusao();
        }

        $inCodJustificativa = $obTPontoJustificativa->getDado('cod_justificativa');
        $stDescricao        = $obTPontoJustificativa->getDado('descricao');

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgForm.$stLink,"Justificativa $inCodJustificativa - $stDescricao","incluir","aviso", Sessao::getId(), "../");
        break;
    case "alterar":
        Sessao::setTrataExcecao(true);
        $stFiltro  = " WHERE descricao ilike '".$_POST['stDescricao']."'";
        $stFiltro .= "   AND cod_justificativa != ".$_POST['inCodJustificativa'];
        $obTPontoJustificativa->recuperaTodos($rsJustificativa, $stFiltro);
        if ($rsJustificativa->getNumLinhas() == 1) {
            Sessao::getExcecao()->setDescricao("A descrição ".$_POST['stDescricao']." já foi inserida no banco.");
        }

        $obTPontoJustificativa->setDado('descricao', $_POST['stDescricao']);
        $obTPontoJustificativa->setDado('anular_faltas', ($_POST['boAnularFaltas'] == 1)?'true':'');
        $obTPontoJustificativa->setDado('lancar_dias_trabalho', ($_POST['boLancarDias'] == 1)?'true':'');
        $obTPontoJustificativa->setDado('cod_justificativa', $_POST['inCodJustificativa']);
        $obTPontoJustificativa->alteracao();
        $obTPontoJustificativaHoras->exclusao();

        if (!$_POST['boAnularFaltas'] && !Sessao::getExcecao()->ocorreu()) {
            $stHorasFalta = formataQuantidadeHoras($_POST['stHorasFalta']);
            $stHorasAbono = formataQuantidadeHoras($_POST['stHorasAbono']);

            $obTPontoJustificativaHoras->setDado('horas_abono', $stHorasAbono);
            $obTPontoJustificativaHoras->setDado('horas_falta', $stHorasFalta);
            $obTPontoJustificativaHoras->inclusao();
        }

        $inCodJustificativa = $obTPontoJustificativa->getDado('cod_justificativa');
        $stDescricao        = $obTPontoJustificativa->getDado('descricao');

        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList.$stLink,"Justificativa $inCodJustificativa - $stDescricao","incluir","aviso", Sessao::getId(), "../");
        break;
    case "excluir":
        Sessao::setTrataExcecao(true);
        $obTPontoJustificativaHoras->setDado('cod_justificativa',$_GET['inCodJustificativa']);
        $obTPontoJustificativaHoras->exclusao();
        $obTPontoJustificativa->exclusao();
        Sessao::encerraExcecao();
        SistemaLegado::alertaAviso($pgList.$stLink,"Justificativa ".$_GET['inCodJustificativa'],"excluir","aviso", Sessao::getId(), "../");
        break;
}

function formataQuantidadeHoras($stHoras)
{
//     $stHoras = str_replace(":", "",$stHoras);
//     $stHoras = str_pad($stHoras, "5", "0", STR_PAD_RIGHT);
//     $stHoras = substr($stHoras, 0, strlen($stHoras)-2).":".substr($stHoras, -2);
    $stHoras = ($stHoras != "") ? $stHoras : "00:00";

    return $stHoras;
}
?>

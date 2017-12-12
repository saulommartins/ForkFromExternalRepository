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
* Definir grande de horário
* Data de Criação: 13/09/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30860 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.41
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalGradeHorario.class.php"                                     );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalGradeHorario.class.php"                                  );

$arGrade = Sessao::read("Grade");
$stAcao = $request->get('stAcao');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"].'&inCodGradeFiltro='.$_REQUEST['inCodGradeFiltro'].'&stDescricaoFiltro='.$_REQUEST['stDescricaoFiltro'];

//Define o nome dos arquivos PHP
$stPrograma = "ManterGrade";
$pgFilt     = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList     = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm     = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc     = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul     = "OC".$stPrograma.".php?stAcao=$stAcao";

$obRPessoalGradeHorario  = new RPessoalGradeHorario;

function validaDescricaoGrade()
{
    GLOBAL $arGrade;
    $obErro = new erro();

    if ( count($arGrade)==0 ) {
        $obErro->setDescricao("Informe no minímo um dia da semana na grade de turnos!");
    }

    if ( !$obErro->ocorreu() ) {
        $stFiltro = " WHERE TRIM(UPPER(descricao)) = TRIM(UPPER('".$_POST['stDescricao']."'))";
        if (trim($_REQUEST["inCodGrade"])!="") {
            $stFiltro .= " AND cod_grade != ".$_REQUEST["inCodGrade"];
        }

        $obTPessoalGradeHorario = new TPessoalGradeHorario();
        $obTPessoalGradeHorario->recuperaTodos($rsGradeHorario, $stFiltro);

        if ($rsGradeHorario->getNumLinhas() > 0) {
            $obErro->setDescricao("A descrição ".$_POST['stDescricao']." já foi criada para outra grade!");
        }
    }

    return $obErro;
}

switch ($stAcao) {
    case "incluir":
        $obErro = validaDescricaoGrade();
        if ( !$obErro->ocorreu() ) {
            $obRPessoalGradeHorario->setDescricao( $_POST['stDescricao'] );
            foreach ($arGrade as $campo => $valor) {
                $obRPessoalGradeHorario->addFaixaTurno();
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraEntrada($valor["stHoraEntrada1"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraSaida($valor["stHoraSaida1"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraEntrada2($valor["stHoraEntrada2"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraSaida2($valor["stHoraSaida2"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setCodDia($valor["inDiaSemana"]);
            }
            $obErro = $obRPessoalGradeHorario->incluirGrade();
        }
        if ( !$obErro->ocorreu() ) {
            Sessao::remove("Grade");
            sistemaLegado::alertaAviso($pgForm,"Grade de Horário: ".$_POST['stDescricao'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
    case "alterar":
        $obErro = validaDescricaoGrade();
        if ( !$obErro->ocorreu() ) {
            $obRPessoalGradeHorario->setCodGrade ( $_POST['inCodGrade'] );
            $obRPessoalGradeHorario->setDescricao( $_POST['stDescricao'] );
            foreach ($arGrade as $campo => $valor) {
                $obRPessoalGradeHorario->addFaixaTurno();
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraEntrada($valor["stHoraEntrada1"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraSaida($valor["stHoraSaida1"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraEntrada2($valor["stHoraEntrada2"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setHoraSaida2($valor["stHoraSaida2"]);
                $obRPessoalGradeHorario->roRPessoalFaixaTurno->setCodDia($valor["inDiaSemana"]);
            }
            $obErro = $obRPessoalGradeHorario->alterarGrade();
        }
        if ( !$obErro->ocorreu() ) {
            Sessao::remove("Grade");
            sistemaLegado::alertaAviso($pgList, "Grade de Horário: ".$_POST['stDescricao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir":
        $obRPessoalGradeHorario->setCodGrade ( $_REQUEST['inCodGrade'] );
        $obErro = $obRPessoalGradeHorario->excluirGrade();
        if ( !$obErro->ocorreu() ) {
            Sessao::remove("Grade");
            sistemaLegado::alertaAviso($pgList,"Grade de Horário: ".$_GET['stDescQuestao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList,"Grade de Horário: ".$_GET['stDescQuestao'].", ".$obErro->getDescricao(),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;
}
?>

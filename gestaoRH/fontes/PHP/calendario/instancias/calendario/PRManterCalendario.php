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
    * Página de Processamento de Calendario
    * Data de Criação   : 18/08/2004

    * @author Eduardo Martins

    * @ignore

    $Revision: 30566 $
    $Name$
    $Author: vandre $
    $Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

    * Caso de uso: uc-04.02.02

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GRH_CAL_NEGOCIO."RCalendario.class.php"      );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriado.class.php"         );
include_once( CAM_GRH_CAL_NEGOCIO."RCalendarioFeriadoVariavel.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCalendario";
$pgFilt = "FL".$stPrograma.".php?".Sessao::getId().'&stAcao='.$request->get('stAcao');
$pgList = "LS".$stPrograma.".php?".Sessao::getId().'&stAcao='.$request->get('stAcao');
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$obRegra  = new RCalendario;

$stAcao                    = $request->get('stAcao');
$inCodFeriadosSelecionados = $request->get('inCodFeriadosSelecionados');
$inCodCalendar             = $request->get('inCodCalendar');
$stDescricao               = str_replace("'", "", $_REQUEST['stDescricao']);

switch ($stAcao) {
    case "incluir":
        $obRegra->setDescricao  ( $stDescricao );

        if ( is_array($inCodFeriadosSelecionados) ) {
            foreach ($inCodFeriadosSelecionados as $inCodFeriado) {
                $arFeriadoTipo = explode("_",$inCodFeriado);
                if ($arFeriadoTipo[1] == 'Variável') {
                   $obRegra->addFeriadoVariavel();
                   $obRegra->ultimoFeriadoVariavel->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitFeriadoVariavel();
                }else
                if ($arFeriadoTipo[1] == 'Ponto facultativo') {
                   $obRegra->addPontoFacultativo();
                   $obRegra->ultimoPontoFacultativo->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitPontoFacultativo();
                }
                if ($arFeriadoTipo[1] == 'Dia compensado') {
                   $obRegra->addDiaCompensado();
                   $obRegra->ultimoDiaCompensado->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitDiaCompensado();
                }
            }
        }

        $obErro = $obRegra->Salvar();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm,"Calendário inserido com sucesso!".$request->get('inCodFeriado'),"inserir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_inserir","erro");
        }
    break;

    case "alterar":
        $obRegra->setCodCalendar( $inCodCalendar );
        $obRegra->setDescricao  ( $stDescricao   );

        if ( is_array($inCodFeriadosSelecionados) ) {
            foreach ($inCodFeriadosSelecionados as $inCodFeriado) {
                $arFeriadoTipo = explode("_",$inCodFeriado);
                if ($arFeriadoTipo[1] == 'Variável') {
                   $obRegra->addFeriadoVariavel();
                   $obRegra->ultimoFeriadoVariavel->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitFeriadoVariavel();
                }else
                if ($arFeriadoTipo[1] == 'Ponto facultativo') {
                   $obRegra->addPontoFacultativo();
                   $obRegra->ultimoPontoFacultativo->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitPontoFacultativo();
                }
                if ($arFeriadoTipo[1] == 'Dia compensado') {
                   $obRegra->addDiaCompensado();
                   $obRegra->ultimoDiaCompensado->setCodFeriado( $arFeriadoTipo[0]);
                   $obRegra->commitDiaCompensado();
                }

            }

        }

        $obErro = $obRegra->salvar();

        if ( !$obErro->ocorreu() ) {
          sistemaLegado::alertaAviso($pgList,"Calendário alterado com sucesso!".$request->get('inCodFeriado'),"alterar","aviso", Sessao::getId(), "../");
        } else {
          sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }

    break;
    case "excluir":
        $obRegra->setCodCalendar( $inCodCalendar );
        $obErro = $obRegra->excluir();

        if ( !$obErro->ocorreu()  ) {
          sistemaLegado::alertaAviso($pgList,"".$inCodCalendar,"excluir","aviso", Sessao::getId(), "../");
        } else {
          sistemaLegado::alertaAviso($pgList,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(),"../");
        }

    break;

}
?>

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
    * Página de Processamento definições de atividades
    * Data de Criação   : 17/01/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRDefinirAtividades.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.11  2007/03/30 20:09:09  cassiano
Bug #8771#

Revision 1.10  2007/03/19 15:36:56  cassiano
Bug #8771#

Revision 1.9  2007/02/12 11:29:31  rodrigo
#6485#

Revision 1.8  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeFato.class.php"      );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAutonomo.class.php"           );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "DefinirAtividades" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LSManterInscricao.php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
$pgDefResp  = "FMDefinirResponsaveis.php";

$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
$obErro = new Erro;

switch ($stAcao) {

    case "def_ativ":
        $arAtividadesSessao = Sessao::read( "Atividades" );
        if ( count( $arAtividadesSessao > 0 ) ) {
            $inPrincipal = 0;
            foreach ($arAtividadesSessao as $key => $arValor) {
                $obRCEMInscricaoEconomica->addInscricaoAtividade();
                if ($arValor["stPrincipal"] == "sim") {
                    $boPrincipal = true;
                    $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->setPrincipal( $boPrincipal );
                    $inPrincipal++;
                }
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->setDataInicio( $arValor['dtDataInicio']);
                if ($arValor['dtDataTermino'] == "&nbsp;") {
                    $stDataTermino = "";
                }
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->setDataTermino($stDataTermino);
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->addAtividade();
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $arValor['inCodigoAtividade'] );
            }
        }

        if ($inPrincipal == 0) {
            $obErro->setDescricao( "É necessário pelo menos uma atividade principal." );
        }

        if ($_REQUEST['inNumProcesso']) {
            list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
            $obRCEMInscricaoEconomica->setCodigoProcesso( $inProcesso );
            $obRCEMInscricaoEconomica->setAnoExercicio( $inExercicio );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMInscricaoEconomica->definirAtividade();
        }

        if ( !$obErro->ocorreu() ) {
            $arHorariosSessao = Sessao::read( "horarios" );
            if ( count( $arHorariosSessao > 0 ) ) {

                $obRCEMInscricaoEconomica->setHorarioAtividade( $arHorariosSessao );
                $obRCEMInscricaoEconomica->setInscricaoEconomica ( $_REQUEST[ 'inInscricaoEconomica' ] );
                $obErro = $obRCEMInscricaoEconomica->alterarHorarios(false);
            }
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boSegueResponsaveis']) {
                $pgProx  = $pgDefResp."?inCodigoEnquadramento=".$_REQUEST['inCodigoEnquadramento']."&inInscricaoEconomica=".$_REQUEST['inInscricaoEconomica'];
                $pgProx .= "&stAcao=def_resp&stDescQuestao=".$_REQUEST['inInscricaoEconomica']."&inCGM=".$_REQUEST['inCGM']."&stCGM=".$_REQUEST['stCGM']."&acao=826";
            } else {
                $pgProx = $pgList;
            }
            sistemaLegado::alertaAviso($pgProx,"Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "horario":
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST[ 'inInscricaoEconomica' ] );
        $arHorariosSessao = Sessao::read( "horarios" );
        if ( count( $arHorariosSessao ) > 0 ) {
            $obRCEMInscricaoEconomica->setHorarioAtividade( $arHorariosSessao );
        } else {
            $obErro->setDescricao( "É necessário incluir ao menos um horário." );
        }
        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMInscricaoEconomica->alterarHorarios();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Alterar Horários concluído com sucesso! Inscrição Econômica: ".$_REQUEST['inInscricaoEconomica']."","horario","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_horario","erro");
        }
    break;
}

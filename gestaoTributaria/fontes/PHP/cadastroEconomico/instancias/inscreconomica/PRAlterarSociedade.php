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
    * Página de Processamento Alteração de Sociedade
    * Data de Criação   : 10/05/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRAlterarSociedade.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.4  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMEmpresaDeDireito.class.php"   );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "AlterarSociedade" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LSManterInscricao.php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
//$pgDefResp  = "FMDefinirResponsaveis.php";

$obErro = new Erro;
$obRCEMEmpresaDeDireito = new RCEMEmpresaDeDireito;
switch ($stAcao) {
    case "sociedade":
        $obRCEMEmpresaDeDireito->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $arSociosSessao = Sessao::read( "socios" );
        $stSocios = "";
        if ( count( $arSociosSessao ) != 0 ) {
            foreach ($arSociosSessao as $inChave => $arSocios) {
                $obRCEMEmpresaDeDireito->addSociedade();
                $obRCEMEmpresaDeDireito->roUltimaSociedade->addInscricao      ( new RCEMInscricaoEconomica );
                $obRCEMEmpresaDeDireito->roUltimaSociedade->obRCGM->setNumCGM ( $arSocios['inCodigoSocio'] );
                $obRCEMEmpresaDeDireito->roUltimaSociedade->setQuotaSocios    ( $arSocios['flQuota']       );
                if ($stSocios) {
                    $stSocios .= ", ";
                }

                $stSocios .= "CGM: ".$arSocios['inCodigoSocio'];
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um sócio." );
        }

        //-----------------------------------------------------------------------------------------------------
        if ($_REQUEST['inNumProcesso']) {
            list($inProcesso,$inExercicio) = explode("/", $_REQUEST['inNumProcesso']);
            $obRCEMEmpresaDeDireito->setCodigoProcesso( $inProcesso );
            $obRCEMEmpresaDeDireito->setAnoExercicio( $inExercicio );
        }
        //-----------------------------------------------------------------------------------------------------

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMEmpresaDeDireito->alterarEmpresaDireitoSociedade();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Sócios ( ".$stSocios." ) para inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }

    break;
}

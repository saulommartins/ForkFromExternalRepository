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
    * Página de Processamento definições de responsáveis
    * Data de Criação   : 25/04/2005

    * @author Tonismar Régis Bernardo

    * @ignore

    * $Id: PRDefinirResponsaveis.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.6  2006/09/15 14:33:07  fabio
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
$stPrograma = "DefinirResponsaveis" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LSManterInscricao.php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
//$pgDefResp  = "FMDefinirResponsaveis.php";
$pgDefElem  = "FMDefinirElementos.php";

$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;
$obErro = new Erro;

switch ($stAcao) {
    case "def_resp":
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $arResponsaveisSessao = Sessao::read( "responsaveis" );
        if ( count( $arResponsaveisSessao ) <= 0 ) {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um responsável." );
        } else {
            $obRCEMInscricaoEconomica->definirResponsavel();
        }

        if ( !$obErro->ocorreu() ) {
            if ($_REQUEST['boSegueElementos']) {
                $pgProx  = $pgDefElem."?inCodigoEnquadramento=".$_REQUEST['inCodigoEnquadramento']."&inInscricaoEconomica=".$_REQUEST['inInscricaoEconomica'];
                $pgProx .= "&stAcao=def_elem&stDescQuestao=".$_REQUEST['inInscricaoEconomica']."&inCGM=".$_REQUEST['inCGM']."&stCGM=".$_REQUEST['stCGM']."&acao=827";
            } else {
                $pgProx = $pgList;
            }
            sistemaLegado::alertaAviso($pgProx,"Inscrição econômica: ".$_REQUEST['inInscricaoEconomica'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

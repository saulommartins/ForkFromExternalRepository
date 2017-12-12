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

    * $Id: PRDefinirElementos.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.02.10

*/

/*
$Log$
Revision 1.10  2007/02/12 12:39:13  rodrigo
#6482#

Revision 1.9  2006/11/17 12:43:15  domluc
Correção Bug #7437#

Revision 1.8  2006/09/15 14:33:07  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoEconomica.class.php" );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "DefiniriElementos" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LSManterInscricao.php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;
//$pgDefResp  = "FMDefinirResponsaveis.php";

$obErro = new Erro;
$obRCEMInscricaoEconomica = new RCEMInscricaoEconomica;

$obAtributos = new MontaAtributos;
$obAtributos->setName('AtributoLicenca_');
$obAtributos->recuperaVetor( $arChave );

switch ($stAcao) {
    case "def_elem":
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $arElementosSessao = Sessao::read( "elementos" );
        if ( count( $arElementosSessao ) > 0 ) {
            foreach ($arElementosSessao as $inChave => $arElementos) {
                $obRCEMInscricaoEconomica->addElementoAtividade();
                $obRCEMInscricaoEconomica->roUltimoElemento->setCodigoElemento( $arElementos['inCodigoElemento'] );
                $obRCEMInscricaoEconomica->roUltimoElemento->setOcorrenciaElemento( $arElementos['inOcorrencia'] );
                $obRCEMInscricaoEconomica->roUltimoElemento->roCEMAtividade->setCodigoAtividade($arElementos["inCodigoAtividade"]);
                $obRCEMInscricaoEconomica->roUltimoElemento->roCEMAtividade->setOcorrenciaAtividade($arElementos["inOcorrenciaAtividade"]);
                $obRCEMInscricaoEconomica->roUltimoElemento->setArrayElemento($arElementos['arElementos']);
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um elemento." );
        }

        if ( !$obErro->ocorreu() ) {
            $obErro = $obRCEMInscricaoEconomica->definirElementos();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList,"Inscrição Econômica: ".$_REQUEST['inInscricaoEconomica'],"incluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
case "elemento":
        // ALTERAÇÃO ELEMENTOS
        $obRCEMInscricaoEconomica->setInscricaoEconomica( $_REQUEST['inInscricaoEconomica'] );
        $arElementosSessao = Sessao::read( "elementos" );
        if ( count( $arElementosSessao ) > 0 ) {
            foreach ($arElementosSessao as $inChave => $arElementos) {
                $obRCEMInscricaoEconomica->addElemento();
                $obRCEMInscricaoEconomica->roUltimoElemento->setCodigoElemento( $arElementos['inCodigoElemento'] );
                $obRCEMInscricaoEconomica->roUltimoElemento->setOcorrenciaElemento( $arElementos['inOcorrencia'] );
                $obRCEMInscricaoEconomica->addInscricaoAtividade();
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->addAtividade();
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setCodigoAtividade( $arElementos['inCodigoAtividade'] );
                $obRCEMInscricaoEconomica->roUltimaInscricaoAtividade->roUltimaAtividade->setOcorrenciaAtividade( $arElementos['inOcorrenciaAtividade'] );
                $obRCEMInscricaoEconomica->roUltimoElemento->setArrayElemento( $arElementos['arElementos'] );
            }
        } else {
            $obErro->setDescricao( "É necessário a inclusão de pelo menos um elemento." );
        }

        if ( !$obErro->ocorreu() ) {
            //$obErro = $obRCEMInscricaoEconomica->alterarElementos();
            $obErro = $obRCEMInscricaoEconomica->definirElementos();
        }

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList."?stAcao=".$stAcao,"Inscrição econômica: ".$tmpNumInscricao,"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

}

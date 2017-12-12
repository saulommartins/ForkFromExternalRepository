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
    * Página de Processamento de Inclusao/Alteracao de Atividade
    * Data de Criação: 11/04/2005

    * @author Fernando Zank Correa Evangelista

    * @ignore

    * $Id: PRManterNatureza.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.9  2007/03/08 19:54:24  rodrigo
Bug #8345#

Revision 1.8  2007/02/27 14:14:14  rodrigo
Bug #8420#

Revision 1.7  2007/02/26 20:24:06  cassiano
Bug #8431#

Revision 1.6  2007/02/26 19:58:52  cassiano
Bug #8430#

Revision 1.5  2007/02/14 12:14:12  rodrigo
#6474#

Revision 1.4  2006/09/15 14:33:22  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_CEM_NEGOCIO."RCEMNaturezaJuridica.class.php"   );

$stAcao = $request->get('stAcao');
$link = Sessao::read( "link" );
$stLink = "&pg=".$link["pg"]."&pos=".$link["pos"];

//Define o nome dos arquivos PHP
$stPrograma = "ManterNatureza";
$pgFilt      = "FL".$stPrograma.".php?stAcao=$stAcao";
$pgList      = "LS".$stPrograma.".php?stAcao=$stAcao".$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=$stAcao";
$pgProc      = "PR".$stPrograma.".php?stAcao=$stAcao";
$pgOcul      = "OC".$stPrograma.".php?stAcao=$stAcao";
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma."Baixar.php";

$obRCEMNatureza = new RCEMNaturezaJuridica;
$obErro          = new Erro;

switch ($stAcao) {

    case "incluir":
      $cod = $_REQUEST['inCodigoNatureza'];
      if (!($cod=="0" || $cod=="-0" || $cod=="00" || $cod=="000" || $cod=="000-" || $cod=="000-0" || $cod=="0000" || $cod=="00000")) {
        //seta a natureza
        $obRCEMNatureza->setCodigoNatureza ( $_REQUEST["inCodigoNatureza"]);
        $obRCEMNatureza->setNomeNatureza   ( ltrim( $_REQUEST["stNomeNatureza"] ));
        $obErro = $obRCEMNatureza->incluirNaturezaJuridica();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Natureza ".$_POST['stNomeNatureza'],"incluir","aviso",Sessao::getId(),"../");
        } else {
            sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
        }
      } else {
        $obErro->setDescricao(" Código da Natureza inválido - ".$_REQUEST['inCodigoNatureza']."" );
        sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "aviso", Sessao::getId(), "../");
      }
    break;

    case "alterar":
        //seta a natureza
        $arCodigoNatureza = explode("-",$_REQUEST["inCodigoNatureza"]);
        $obRCEMNatureza->setCodigoNatureza ( $arCodigoNatureza[0].$arCodigoNatureza[1] );
        $obRCEMNatureza->setNomeNatureza   ( ltrim( $_REQUEST["stNomeNatureza"] ));
        $obErro = $obRCEMNatureza->alterarNaturezaJuridica();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Natureza ".$_POST['stNomeNatureza'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

    case "excluir":
        //seta a natureza
        $obRCEMNatureza->setCodigoNatureza ( $_REQUEST["inCodigoNatureza"] );
        $obRCEMNatureza->setNomeNatureza ( $_REQUEST["stNomeNatureza"]);
        $obErro = $obRCEMNatureza->excluirNaturezaJuridica();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Natureza ".$_REQUEST['stDescQuestao'],"excluir","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,urlencode($obErro->getDescricao()),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

    case "baixar":
        //seta a natureza
        $arCodigoNatureza = explode("-",$_REQUEST["inCodigoNatureza"]);
        $obRCEMNatureza->setCodigoNatureza ( $arCodigoNatureza[0].$arCodigoNatureza[1] );
        $obRCEMNatureza->setMotivoBaixa ( $_REQUEST["stMotivoBaixa"]);
        $obErro = $obRCEMNatureza->baixarNaturezaJuridica();
        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $stAcao,"Natureza ".$_REQUEST['stNomeNatureza'],"baixar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_baixar","erro");
        }
    break;
}
?>

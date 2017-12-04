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
    * Página de Processamento de Inclusao/Alteracao de Carteira
    * Data de Criação: 04/10/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho

    * @ignore

    * $Id: PRManterCarteira.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.05.05

*/

/*
$Log$
Revision 1.8  2006/09/15 14:57:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_MON_NEGOCIO."RMONCarteira.class.php"   );

$stAcao = $request->get('stAcao');

$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterCarteira";
$pgFilt      = "FL".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgList      = "LS".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'].$stLink;
$pgForm      = "FM".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgProc      = "PR".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgOcul      = "OC".$stPrograma.".php?stAcao=".$_REQUEST['stAcao'];
$pgJS        = "JS".$stPrograma.".js";
$pgFormBaixa = "FM".$stPrograma.".php";

$obRMONCarteira = new RMONCarteira;
$obErro      = new Erro;

switch ($_REQUEST['stAcao']) {

    case "incluir":
        $obRMONCarteira->obRMONConvenio->setNumeroConvenio( trim($_REQUEST["inNumConvenio"]) );
        $obRMONCarteira->obRMONConvenio->listarConvenio( $rsConvenio );
        if ( $rsConvenio->eof() ) {
            $js = "alertaAviso('@Convênio não encontrado.','form','erro','".Sessao::getId()."');";
            sistemaLegado::executaFrameOculto($js);
        } else {
            $obRMONCarteira->obRMONConvenio->setCodigoConvenio  ( $rsConvenio->getCampo("cod_convenio") );
            $obRMONCarteira->setNumeroCarteira  ( trim($_REQUEST["inNumCarteira"]) );
            $obRMONCarteira->setVariacao        ( trim($_REQUEST["flVariacao"]) );
            $obErro = $obRMONCarteira->incluirCarteira();
            if ( !$obErro->ocorreu() ) {
                sistemaLegado::alertaAviso($pgForm . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Carteira ".$_REQUEST['inNumCarteira'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::alertaAviso($pgForm, urlencode($obErro->getDescricao()),"n_incluir", "erro", Sessao::getId(), "../");
            }
        }
    break;

    case "alterar":
        $obRMONCarteira->obRMONConvenio->setCodigoConvenio  ( trim($_REQUEST["inCodConvenio"]) );
        $obRMONCarteira->setNumeroCarteira  ( trim($_REQUEST["inNumCarteira"]) );
        $obRMONCarteira->setCodigoCarteira  ( trim($_REQUEST["inCodCarteira"]) );
        $obRMONCarteira->setVariacao        ( trim($_REQUEST["flVariacao"]) );
        $obErro = $obRMONCarteira->alterarCarteira();

        if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Carteira ".$_REQUEST['inNumCarteira'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            sistemaLegado::alertaAviso($pgList, urlencode($obErro->getDescricao()),"n_alterar", "erro", Sessao::getId(), "../");
        }
    break;

    case "excluir":

        $obRMONCarteira->setNumeroCarteira  ( trim($_REQUEST["inNumCarteira"]) );
        $obRMONCarteira->setCodigoCarteira  ( trim($_REQUEST["inCodCarteira"]) );
        $obRMONCarteira->setVariacao        ( trim($_REQUEST["flVariacao"]) );
        $obErro = $obRMONCarteira->excluirCarteira();

    if ( !$obErro->ocorreu() ) {
            sistemaLegado::alertaAviso($pgList . "?" . Sessao::getId() . "&stAcao=" . $_REQUEST['stAcao'],"Carteira ".$_REQUEST['inNumCarteira'],"excluir","aviso", Sessao::getId(), "../");

        } else {
            sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_excluir","erro");
        }
    break;

}
?>

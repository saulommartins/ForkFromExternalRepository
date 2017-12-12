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
* Arquivo de instância para manutenção de locais
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 21837 $
$Name$
$Author: cassiano $
$Date: 2007-04-13 15:09:00 -0300 (Sex, 13 Abr 2007) $

Casos de uso: uc-01.05.03
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GA_ORGAN_NEGOCIO."ROrganogramaLocal.class.php"     );

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::getId()."&pg=".Sessao::read('link_pg')."&pos=".Sessao::read('link_pos')."&stAcao=".$stAcao;

//Define o nome dos arquivos PHP
$stPrograma = "ManterLocal" ;
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php?".$stLink;
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js" ;

$obRLocal = new ROrganogramaLocal;

switch ($stAcao) {

    case "incluir":
        $obRLocal->setDescricao        ( $_REQUEST["stDescricao"]                     );
        $obRLocal->setCodLogradouro    ( $_REQUEST["inNumLogradouro"]                 );
        $obRLocal->setNumero           ( $_REQUEST["inNumero"]                        );
        $obRLocal->setFone             ( $_REQUEST["inPrefixo"] . $_REQUEST["inFone"] );
        $obRLocal->setRamal            ( $_REQUEST["inRamal"]                         );
        $obRLocal->setDificilAcesso    ( $_REQUEST["boDificilAcesso"]                 );
        $obRLocal->setInsalubre        ( $_REQUEST["boInsalubre"]                     );
        $obErro = $obRLocal->incluirLocal();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Local: ".$_REQUEST["stDescricao"],"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;

    case "alterar":
        $obRLocal->setCodLocal         ( $_REQUEST["inCodLocal"]                      );
        $obRLocal->setDescricao        ( $_REQUEST["stDescricao"]                     );
        $obRLocal->setCodLogradouro    ( $_REQUEST["inNumLogradouro"]                 );
        $obRLocal->setNumero           ( $_REQUEST["inNumero"]                        );
        $obRLocal->setFone             ( $_REQUEST["inPrefixo"] . $_REQUEST["inFone"] );
        $obRLocal->setRamal            ( $_REQUEST["inRamal"]                         );
        $obRLocal->setDificilAcesso    ( $_REQUEST["boDificilAcesso"]                 );
        $obRLocal->setInsalubre        ( $_REQUEST["boInsalubre"]                     );
        $obErro = $obRLocal->alterarLocal();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."&inCodigo=".$_REQUEST['inCodigo'],"Local: ".$_REQUEST["stDescricao"],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
        }
    break;

    case "excluir";
        $obRLocal->setCodLocal($_REQUEST["inCodLocal"]);
        $obErro = $obRLocal->excluirLocal();

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgList."&inCodigo=".$_REQUEST['inCodigo'],"Local: ".$_REQUEST["stDescricao"],"excluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::alertaAviso($pgList.$stFiltro,urlencode( "Local: ".$_REQUEST['stDescricao']." - ".$obErro->getDescricao() ),"n_excluir","erro", Sessao::getId(), "../");
        }
    break;

}

?>

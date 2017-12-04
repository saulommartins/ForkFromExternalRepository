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
    * Data de Criação: 04/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 26154 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-10-17 11:42:13 -0200 (Qua, 17 Out 2007) $

    * Casos de uso: uc-03.01.07
*/

/*
$Log$
Revision 1.1  2007/10/17 13:42:13  hboaventura
correção dos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioApoliceBem.class.php");

$stPrograma = "ManterBemSegurado";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioApoliceBem = new TPatrimonioApoliceBem();
Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioApoliceBem );

switch ($stAcao) {
    case 'incluir' :
        //exclui das apolices os bens excluidos
        $obTPatrimonioApoliceBem->setDado('cod_apolice', $_REQUEST['inCodApolice'] );

        $arBensExcluidos = Sessao::read('bensExcluidos');
        if ( count( $arBensExcluidos[$_REQUEST['inCodApolice']] ) > 0 ) {
            foreach ($arBensExcluidos[$_REQUEST['inCodApolice']] as $arTemp) {
                $obTPatrimonioApoliceBem->setDado('cod_bem',$arTemp['cod_bem']);
                $obTPatrimonioApoliceBem->exclusao();
            }
        }
        //inclui as apolices inclusas no banco
        $arBens = Sessao::read('bens');
        if ( count( $arBens[$_REQUEST['inCodApolice']] ) > 0 ) {
            foreach ($arBens[$_REQUEST['inCodApolice']] as $arTemp) {
                if ($arTemp['novo'] == true) {
                    $obTPatrimonioApoliceBem->setDado('cod_bem',$arTemp['cod_bem']);
                    $obTPatrimonioApoliceBem->inclusao();
                }
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Apólice - ".$_REQUEST['inCodApólice'],"alterar","aviso", Sessao::getId(), "../");

        break;
}
Sessao::encerraExcecao();

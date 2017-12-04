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
    * Data de Criação: 04/09/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage

    $Revision: 25675 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-27 09:57:24 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.2  2007/09/27 12:57:13  hboaventura
adicionando arquivos

Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioSituacaoBem.class.php" );

$stPrograma = "ManterSituacao";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioSituacaoBem = new TPatrimonioSituacaoBem();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioSituacaoBem );

switch ($stAcao) {
    case 'incluir':
        $obTPatrimonioSituacaoBem->setDado( 'nom_situacao', $_REQUEST['stDescricaoSituacaoBem'] );
        $obTPatrimonioSituacaoBem->recuperaSituacaoBem( $rsPatrimonioSituacaoBem );

        if ( $rsPatrimonioSituacaoBem->getNumLinhas() <= 0 ) {
            $obTPatrimonioSituacaoBem->proximoCod( $inCodSituacaoBem );

            $obTPatrimonioSituacaoBem->setDado( 'cod_situacao', $inCodSituacaoBem );
            $obTPatrimonioSituacaoBem->inclusao();
            SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=".$stAcao,"Situação do Bem - ".$inCodSituacaoBem,"incluir","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma situação com esta descrição'),"n_incluir","erro");
        }

        break;

    case 'alterar' :
        $stFiltro = "
            AND cod_situacao <> ".$_REQUEST['inCodSituacao']."
        ";
        $obTPatrimonioSituacaoBem->setDado( 'nom_situacao', $_REQUEST['stDescricaoSituacaoBem'] );
        $obTPatrimonioSituacaoBem->recuperaSituacaoBem( $rsPatrimonioSituacaoBem, $stFiltro );
        if ( $rsPatrimonioSituacaoBem->getNumLinhas() <= 0 ) {
            $obTPatrimonioSituacaoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
            $obTPatrimonioSituacaoBem->alteracao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Situação do Bem - ".$_REQUEST['inCodSituacao'],"alterar","aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma situação com esta descrição'),"n_incluir","erro");
        }
        break;

    case 'excluir' :
        $obTPatrimonioSituacaoBem->setDado( 'cod_situacao', $_REQUEST['inCodSituacao'] );
        $obTPatrimonioSituacaoBem->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Situação do Bem - ".$_REQUEST['inCodSituacao'],"excluir","aviso", Sessao::getId(), "../");

        break;

}
Sessao::encerraExcecao();

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

    $Revision: 25536 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-18 12:11:18 -0300 (Ter, 18 Set 2007) $

    * Casos de uso: uc-03.01.03
*/

/*
$Log$
Revision 1.1  2007/09/18 15:11:11  hboaventura
Adicionando ao repositório

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioNatureza.class.php" );
include_once( CAM_GP_PAT_MAPEAMENTO."TPatrimonioGrupo.class.php" );

$stPrograma = "ManterNatureza";
$pgFilt   = "FL".$stPrograma.".php";
$pgList   = "LS".$stPrograma.".php";
$pgForm   = "FM".$stPrograma.".php";
$pgProc   = "PR".$stPrograma.".php";
$pgOcul   = "OC".$stPrograma.".php";
$pgJs     = "JS".$stPrograma.".js";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTPatrimonioNatureza = new TPatrimonioNatureza();

Sessao::setTrataExcecao( true );
Sessao::getTransacao()->setMapeamento( $obTPatrimonioNatureza );

switch ($stAcao) {
    case 'incluir':

        $stFiltro = " WHERE  nom_natureza = '".$_REQUEST['stDescricaoNatureza']."' ";
        $obTPatrimonioNatureza->recuperaNatureza( $rsPatrimonioNatureza, $stFiltro );

        if ( $rsPatrimonioNatureza->getNumLinhas() <= 0 ) {
            $obTPatrimonioNatureza->proximoCod( $inCodNatureza );

            $obTPatrimonioNatureza->setDado( 'cod_natureza', $inCodNatureza );
            $obTPatrimonioNatureza->setDado( 'cod_tipo'    , $_REQUEST['inTipoNtureza'] );
            $obTPatrimonioNatureza->setDado( 'nom_natureza', $_REQUEST['stDescricaoNatureza'] );
            
            $obTPatrimonioNatureza->inclusao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao, "Natureza - ".$inCodNatureza." ".$_REQUEST['stDescricaoNatureza'], "incluir", "aviso", Sessao::getId(), "../");
            
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma natureza com esta descrição'),"n_incluir","erro");
        }

        break;

    case 'alterar' :

        $stFiltro = " WHERE  nom_natureza = '".$_REQUEST['stDescricaoNatureza']."'
                        AND  cod_natureza <> ".$_REQUEST['inCodNatureza'];
        $obTPatrimonioNatureza->recuperaNatureza( $rsPatrimonioNatureza, $stFiltro );
                
        if ( $rsPatrimonioNatureza->getNumLinhas() <= 0 ) {
            
            $obTPatrimonioNatureza->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
            $obTPatrimonioNatureza->setDado( 'cod_tipo'    , $_REQUEST['inTipoNtureza'] );
            $obTPatrimonioNatureza->setDado( 'nom_natureza', $_REQUEST['stDescricaoNatureza'] );
            
            $obTPatrimonioNatureza->alteracao();
            SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao, "Natureza - ".$_REQUEST['inCodNatureza']." ".$_REQUEST['stDescricaoNatureza'], "alterar", "aviso", Sessao::getId(), "../");
            
        } else {
            SistemaLegado::exibeAviso(urlencode('Já existe uma natureza com esta descrição'),"n_incluir","erro");
        }
        
        break;

    case 'excluir' :
        
        $obTPatrimonioNatureza->setDado( 'cod_natureza', $_REQUEST['inCodNatureza'] );
        $obTPatrimonioNatureza->exclusao();
        SistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=".$stAcao,"Natureza - ".$_REQUEST['inCodNatureza'],"excluir","aviso", Sessao::getId(), "../");

        break;

}
Sessao::encerraExcecao();

?>

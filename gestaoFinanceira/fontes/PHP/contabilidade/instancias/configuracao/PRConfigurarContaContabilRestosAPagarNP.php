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
    * Página de Formulario
    * Data de Criação   : 09/02/2008

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @ignore

    $Revision: 25612 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-09-24 17:03:30 -0300 (Seg, 24 Set 2007) $

    * Casos de uso: uc-02.08.15
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabiliadadeContaContabilRpNp.class.php"                             );
include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadePlanoConta.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ConfigurarContaContabilRestosAPagarNP";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTContaContabilRnNp = new TContabiliadadeContaContabilRpNp();
$obErro = new Erro;
$arValores = Sessao::read('arValores');

switch ($stAcao) {
    case 'incluir':
    default:
        if (is_array($arValores)) {
            $obErro = $obTContaContabilRnNp->exclusao();
            foreach ($arValores as $k => $v) {
                $obTContaContabilRnNp->setDado('exercicio'    , $arValores[$k]['exercicio']   );
                $obTContaContabilRnNp->setDado('cod_conta'    , $arValores[$k]['cod_conta']   );
                $obTContaContabilRnNp->setDado('cod_entidade' , $arValores[$k]['cod_entidade']);
                $obErro = $obTContaContabilRnNp->inclusao( $boTransacao );
            }
        }

        if ( !$obErro->ocorreu() ) {
            SistemaLegado::alertaAviso($pgForm,"Conta ".$_POST['stCodEstrutural']." configurada", "incluir", "aviso", Sessao::getId(), "../");
        } else {
            SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        }
    break;
}

?>

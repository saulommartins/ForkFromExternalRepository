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
    * Pacote de configuração do TCEAL
    * Data de Criação   : 08/10/2013

    * @author Analista: Carlos Adriano
    * @author Desenvolvedor: Carlos Adriano
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCEAL_NEGOCIO."RExportacaoTCEALArqCredor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoCredor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulaáio adicionando os credores que possuem tipo informado
//$obRegra = new RExportacaoTCEALArqCredor();
$obRegra = new RExportacaoTCEALCredor();

$cont=0;
foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inTipoConversao" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $cont++;
           // $obRegra->addCredor();
           // $obRegra->roUltimoCredor->setExercicio  ( $arCredor[2] );
           // $obRegra->roUltimoCredor->setNumCGM     ( $arCredor[1] );
           // $obRegra->roUltimoCredor->setTipoCredor  ( $value );
           $obRegra->setExercicio  ( $arCredor[2] );
           $obRegra->setNumCGM     ( $arCredor[1] );
           $obRegra->setTipoCredor  ( $value );
           $obErro = $obRegra->salvar();
           if($obErro->ocorreu()) break;
        }
    } elseif ( strstr( $key , "inTipo" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $cont++;
            //$obRegra->addCredor();
            //$obRegra->roUltimoCredor->setExercicio   ( Sessao::getExercicio() );
            //$obRegra->roUltimoCredor->setNumCGM      ( $arCredor[1] );
            //$obRegra->roUltimoCredor->setTipoCredor  ( $value );
            $obRegra->setExercicio   ( Sessao::getExercicio() );
            $obRegra->setNumCGM      ( $arCredor[1] );
            $obRegra->setTipoCredor  ( $value );
            $obErro = $obRegra->salvar();
            if($obErro->ocorreu()) break;
        }
    }

}

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt."?".$stFiltro, " ".$cont." credores incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>

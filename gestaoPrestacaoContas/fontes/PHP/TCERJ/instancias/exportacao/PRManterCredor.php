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
    * Página de Processamento - Parâmetros do Arquivo CREDOR
    * Data de Criação   : 11/02/2005

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Cleisson da Silva Barboza

    * @ignore

    $Revision: 59612 $
    $Name$
    $Autor: $
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-02.08.06
*/

/*
$Log$
Revision 1.1  2007/09/24 20:03:12  hboaventura
Ticket#10234#

Revision 1.5  2006/07/05 20:46:20  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCERJ_NEGOCIO."RExportacaoTCERSArqCredor.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulaáio adicionando os credores que possuem tipo informado
$obRegra = new RExportacaoTCERSArqCredor();

foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inTipo" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $obRegra->addCredor();
            $obRegra->roUltimoCredor->setExercicio   ( Sessao::getExercicio() );
            $obRegra->roUltimoCredor->setNumCGM      ( $arCredor[1] );
            $obRegra->roUltimoCredor->setTipoCredor  ( $value );
            $cont++;
        }
    } elseif ( strstr( $key , "inTipoConversao" ) ) {
        $arCredor = explode( "_" , $key );
        if ($value<>"") {
            $obRegra->addCredor();
            $obRegra->roUltimoCredor->setExercicio  ( $arCredor[2] );
            $obRegra->roUltimoCredor->setNumCGM     ( $arCredor[1] );
            $obRegra->roUltimoCredor->setTipoCredor  ( $value );
            $cont++;
        }
    }
}

//salva as inclusoes/alterações dos credores
$obErro = $obRegra->salvar() ;
if ( !$obErro->ocorreu() ) {
    alertaAviso($pgForm."?".$stFiltro, " ".$cont." credores incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>

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
set_time_limit(0);
/**
    * Pacote de configuração do TCETO - Processamento Configurar Credor
    * Data de Criação   : 06/11/2014

    * @author Analista: Silvia Martins Silva
    * @author Desenvolvedor: Michel Teixeira
    * $Id: PRManterCredor.php 60660 2014-11-06 16:28:53Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCETO_NEGOCIO.'RExportacaoTCETOArqCredor.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterCredor";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

//Percorre os resultados do formulario adicionando os credores que possuem tipo informado
$obRegra = new RExportacaoTCETOCredor();
$obErro = new Erro();

$obTransacao = new Transacao;
$obTransacao->begin();
$boTransacao = $obTransacao->getTransacao();

$cont=0;
foreach ($_POST as $key=>$value) {
    if ( strstr( $key , "inTipoConversao" ) ) {
        $arCredor = explode( "_" , $key );
        $stExercicio = $arCredor[2];
        if ($value<>"") {
            $cont++;
            $obRegra->setExercicio  ( $arCredor[2] );
            $obRegra->setNumCGM     ( $arCredor[1] );
            $obRegra->setTipoCredor  ( $value );
            $obErro = $obRegra->salvar($boTransacao);
            if($obErro->ocorreu()) break;
        }
        else{
            $obRegra->setExercicio  ( $arCredor[2]          );
            $obRegra->setNumCGM     ( $arCredor[1]          );
            $obRegra->buscarCredor( $rsCredor, $boTransacao );
            
            if ($rsCredor->getNumLinhas() > 0) {
                $cont++;
                $obErro = $obRegra->excluirCredor($boTransacao);
                if($obErro->ocorreu()) break;
            }
        }
    } elseif ( strstr( $key , "inTipo" ) ) {
        $arCredor = explode( "_" , $key );
        $stExercicio = Sessao::getExercicio();
        if ($value<>"") {
            $cont++;
            $obRegra->setExercicio   ( Sessao::getExercicio() );
            $obRegra->setNumCGM      ( $arCredor[1] );
            $obRegra->setTipoCredor  ( $value );
            $obErro = $obRegra->salvar($boTransacao);
            if($obErro->ocorreu()) break;
        }
        else{
            $obRegra->setExercicio  ( Sessao::getExercicio());
            $obRegra->setNumCGM     ( $arCredor[1]          );
            $obRegra->buscarCredor( $rsCredor, $boTransacao );
            
            if ($rsCredor->getNumLinhas() > 0) {
                $cont++;
                $obErro = $obRegra->excluirCredor($boTransacao);
                if($obErro->ocorreu()) break;
            }
        }
    }
}

if ( !$obErro->ocorreu() )
    $obErro = $obTransacao->commitAndClose();
else
    $obTransacao->rollbackAndClose();

if ( !$obErro->ocorreu() ) {
    SistemaLegado::alertaAviso($pgFilt."?".$stFiltro, " ".$cont." credores incluídos/alterados ", "incluir", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
}

?>

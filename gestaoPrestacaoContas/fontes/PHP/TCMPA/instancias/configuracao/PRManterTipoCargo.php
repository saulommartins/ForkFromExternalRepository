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
    * Página de Processamento - Parâmetros do Arquivo
    * Data de Criação   : 18/12/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPATipoCargo.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoCargo";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";
$stAcao = 'incluir';

$obTTPATipoCargo = new TTPATipoCargo();
$obErro = $obTTPATipoCargo->excluirTodos();

if ( !$obErro->ocorreu() ) {
    foreach ($_POST as $chave => $inValor) {
        $arCargos = explode('_', $chave);
        $inCodCargo = $arCargos[1];

        if ((!empty($inCodCargo)) && (!empty($inValor)) ) {
            $obTTPATipoCargo->setDado('cod_cargo', $inCodCargo);
            $obTTPATipoCargo->setDado('tipo' , $inValor   );
            $obErro = $obTTPATipoCargo->inclusao();
            if ($obErro->ocorreu()) {
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
                break;
            }
        }
        $inCodCargo = "";
    }

} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
}

SistemaLegado::LiberaFrames();

?>

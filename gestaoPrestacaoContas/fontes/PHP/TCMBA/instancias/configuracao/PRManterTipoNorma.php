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

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO ."TTCMBAVinculoTipoNorma.class.php";

$stPrograma = "ManterTipoNorma";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTCMBAVinculoTipoNorma = new TTCMBAVinculoTipoNorma();

# Inicia o controle de transação
$obTransacao = new Transacao();
$obErro = new Erro();

$obErro = $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);

# Exclui todos os registros da tabela e insere novamente.

if (!$obErro->ocorreu()) {
    $obErro = $obTTCMBAVinculoTipoNorma->excluirTodos($boTransacao);
    if (!$obErro->ocorreu()) {
        foreach ($_REQUEST as $stKey => $stValue) {
            $stKey = explode("_",$stKey);
            if (($stKey[0] == "inTipo") && ($stKey[1] != '') && ($stValue != '')) {
                $inCodTipoNormaUrbem = $stKey[1];
                $inCodTipoNorma = $stValue;

                $obTTCMBAVinculoTipoNorma->setDado('cod_tipo_norma', $inCodTipoNormaUrbem);
                $obTTCMBAVinculoTipoNorma->setDado('cod_tipo'      , $inCodTipoNorma);
                $obErro = $obTTCMBAVinculoTipoNorma->inclusao($boTransacao);

                if ($obErro->ocorreu()) {
                    break;
                }
            }
        }   
    }
}

$obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTTCMBAVinculoTipoNorma );

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Configurar Tipos de Norma ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()) , "n_alterar" , "erro");
}

?>

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

/**
 * Página de Processamento - Parâmetros do Arquivo
 * Data de Criação: 07/10/2014
 * @author Desenvolvedor: Diogo Zarpelon <diogo.zarpelon@cnm.org.br>
 * @ignore
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCEPE_MAPEAMENTO ."TTCEPEVinculoTipoNorma.class.php";

$stPrograma = "ManterTipoNorma";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obTTCEPEVinculoTipoNorma = new TTCEPEVinculoTipoNorma();

# Inicia o controle de transação
Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento( $obTTCEPEVinculoTipoNorma );

# Exclui todos os registros da tabela e insere novamente.
$obTTCEPEVinculoTipoNorma->excluirTodos();

$obErro = new Erro();

foreach ($_REQUEST as $stKey => $stValue) {

    if (strpos($stKey,'inTipo') !== false && $stValue == '') {
        $obErro->setDescricao('É necessário informar todos os campos na tela');
        break;
    } elseif (strpos($stKey,'inTipo') !== false) {

        $arIdentificador = explode('_', $stKey);

        $inCodTipoNormaUrbem = $arIdentificador[1];
        $inCodTipoNorma = $stValue;

        $obTTCEPEVinculoTipoNorma->setDado('cod_tipo_norma', $inCodTipoNormaUrbem);
        $obTTCEPEVinculoTipoNorma->setDado('cod_tipo'      , $inCodTipoNorma);
        $obErro = $obTTCEPEVinculoTipoNorma->inclusao();

        if ($obErro->ocorreu())
            break;
    }
}

# Encerra o controle de transação
Sessao::encerraExcecao();

if (!$obErro->ocorreu()) {
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Configurar Tipos de Norma ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()) , "n_alterar" , "erro");
}

?>

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
    * Página de Processamento - configurações do Arquivo TCMPA
    * Data de Criação   : 02/06/2008

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Luiz Felipe Prestes Teixeira

    * @ignore

    * $Id:$

    * Casos de uso: uc-06.07.00
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
require_once CAM_GPC_TCMPA_MAPEAMENTO."TTPALotacao.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterCargoSituacaoFuncional";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$arLotacao = Sessao::read('arLotacao');

$obTTPALotacao = new TTPALotacao();
$obTTPALotacao->setDado( 'cod_sub_divisao', $arLotacao[0]['incodsubdivisao']);
$obTTPALotacao->setDado( 'cod_regime', $arLotacao[0]['incodregime']);
$boErro = $obTTPALotacao->deletaLotacao();

if (!$boErro->ocorreu()) {

    foreach ($arLotacao as $chave =>$valor) {
        $obTTPALotacao->setDado( 'cod_situacao'   , $valor['incodsituacao']);
        $obTTPALotacao->setDado( 'cod_tipo', $valor['incodtipocargo']);

        foreach ($valor['cargos'] as $key =>$codCargo) {
            $obTTPALotacao->setDado( 'cod_cargo', $codCargo);
            $obTTPALotacao->inclusao();
        }
    }
    SistemaLegado::alertaAviso($pgForm, " ".$cont." Dados incluídos ", "alterar", "aviso", Sessao::getId(), "../");

} else {
    SistemaLegado::exibeAviso(urlencode($bobErro->getDescricao()),"n_incluir","erro");
}

SistemaLegado::LiberaFrames();

?>

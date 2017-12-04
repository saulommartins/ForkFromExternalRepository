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
    * Data de Criação: 19/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: PRManterTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoVeiculoVinculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoVeiculo";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();

Sessao::setTrataExcecao(true);

//Instancia os objetos de mapeamento
$obTTipoVeiculoVinculo = new TTBATipoVeiculoVinculo();

//Remove todos os vinculos anteriores
$obTTipoVeiculoVinculo->recuperaTodos($rsTipoVeiculoVinculo);
while (!$rsTipoVeiculoVinculo->eof()) {
    $obTTipoVeiculoVinculo->setDado('cod_tipo_tcm',$rsTipoVeiculoVinculo->getCampo('cod_tipo_tcm'));
    $obTTipoVeiculoVinculo->setDado('cod_tipo',$rsTipoVeiculoVinculo->getCampo('cod_tipo'));
    $obErro = $obTTipoVeiculoVinculo->exclusao();

    $rsTipoVeiculoVinculo->proximo();
}

//Se tudo estiver ok, insere na base os dados
if ( !$obErro->ocorreu() ) {
    //Recupera da sessao os dados a serem incluidos
    $arTipoVeiculo = Sessao::read('arTipoVeiculo');
    //Faz a insercao
    if ( is_array($arTipoVeiculo) ) {
        foreach ($arTipoVeiculo as $arTipoVeiculoAux) {
            $obTTipoVeiculoVinculo->setDado('cod_tipo_tcm',$arTipoVeiculoAux['cod_tipo_tcm']);
            $obTTipoVeiculoVinculo->setDado('cod_tipo',$arTipoVeiculoAux['cod_tipo_sw']);
            $obErro = $obTTipoVeiculoVinculo->inclusao();
        }
    }
}

if ( !$obErro->ocorreu() ) {
    Sessao::encerraExcecao();
    SistemaLegado::alertaAviso($pgForm."?".$stFiltro, " ".$cont." Dados alterados ", "alterar", "aviso", Sessao::getId(), "../");
} else {
    SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_alterar","erro");
}

SistemaLegado::LiberaFrames();

?>

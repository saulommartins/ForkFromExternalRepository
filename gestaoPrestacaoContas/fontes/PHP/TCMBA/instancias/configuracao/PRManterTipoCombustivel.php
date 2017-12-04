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

    * $Id: PRManterTipoCombustivel.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.05.00
*/
include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GPC_TCMBA_MAPEAMENTO ."TTBATipoCombustivelVinculo.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterTipoCombustivel";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$obErro = new Erro();

Sessao::setTrataExcecao(true);

//Instancia os objetos de mapeamento
$obTTipoCombustivelVinculo = new TTBATipoCombustivelVinculo();

//Remove todos os vinculos anteriores
$obTTipoCombustivelVinculo->recuperaTodos($rsTipoCombustivelVinculo);
while (!$rsTipoCombustivelVinculo->eof()) {
    $obTTipoCombustivelVinculo->setDado('cod_tipo_tcm',$rsTipoCombustivelVinculo->getCampo('cod_tipo_tcm'));
    $obTTipoCombustivelVinculo->setDado('cod_combustivel',$rsTipoCombustivelVinculo->getCampo('cod_combustivel'));
    $obErro = $obTTipoCombustivelVinculo->exclusao();

    $rsTipoCombustivelVinculo->proximo();
}

//Se tudo estiver ok, insere na base os dados
if ( !$obErro->ocorreu() ) {
    //Recupera da sessao os dados a serem incluidos
    $arTipoCombustivel = Sessao::read('arTipoCombustivel');
    //Faz a insercao
    if ( is_array($arTipoCombustivel) ) {
        foreach ($arTipoCombustivel as $arTipoCombustivelAux) {
            $obTTipoCombustivelVinculo->setDado('cod_tipo_tcm',$arTipoCombustivelAux['cod_tipo_tcm']);
            $obTTipoCombustivelVinculo->setDado('cod_combustivel',$arTipoCombustivelAux['cod_tipo_sw']);
            $obErro = $obTTipoCombustivelVinculo->inclusao();
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

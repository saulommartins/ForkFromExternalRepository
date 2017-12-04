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
 * Data de Criação: 19/11/2007

 * @author Analista: Gelson W. Gonçalves
 * @author Desenvolvedor: Henrique Boaventura

 * $Id: LSProcurarVeiculo.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-03.02.00

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaVeiculo.class.php";

$stPrograma = "ProcurarVeiculo";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

# Resgata os valores do filtro da sessão.
$arFiltro = Sessao::read('arFiltro');

# Atribui os valores do $_REQUEST para o array.
foreach ($_REQUEST as $key => $value) {
    $arFiltro[$key] = $value;
}

if (isset($arFiltro['pg']) != '') {
    Sessao::write('pg'  , $arFiltro['pg']);
    Sessao::write('pos' , $arFiltro['pos']);
}

# Armazena na sessão os filtros.
Sessao::write('arFiltro'  , $arFiltro );
Sessao::write('paginando' , true      );

# Seta o caminho para a popup de exclusão.
$stCaminho = CAM_GP_FRO_INSTANCIAS."veiculo/";

# Função Javascript que retorna o veículo selecionado para o formulário.
$stFncJavaScript  = " function insereVeiculo(num,nom) {  \n";
$stFncJavaScript .= " var sNum;                  \n";
$stFncJavaScript .= " var sNom;                  \n";
$stFncJavaScript .= " sNum = num;                \n";
$stFncJavaScript .= " sNom = nom;                \n";
$stFncJavaScript .= " if ( window.opener.parent.frames['telaPrincipal'].document.getElementById('".$arFiltro["campoNom"]."') ) { window.opener.parent.frames['telaPrincipal'].document.getElementById('".$arFiltro["campoNom"]."').innerHTML = sNom; } \n";
$stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$arFiltro["nomForm"].".".$arFiltro["campoNum"].".value = sNum; \n";

$inner = isset($inner) ? $inner : null;

if ($inner != 0) {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$arFiltro["nomForm"].".Hdn".$arFiltro["campoNum"].".value = sNum; \n";
}

if ($arFiltro["campoNom"]) {
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$arFiltro["nomForm"].".".$arFiltro["campoNom"].".value = sNom; \n";
    $stFncJavaScript .= " window.opener.parent.frames['telaPrincipal'].document.".$arFiltro["nomForm"].".".$arFiltro["campoNum"].".focus(); \n";
}

$stFncJavaScript .= " window.close();            \n";
$stFncJavaScript .= " } ";

# Filtros para a consulta de Veiculos.
$stFiltro = " AND NOT EXISTS
                  (
                    SELECT 1
                      FROM frota.veiculo_baixado
                     WHERE veiculo_baixado.cod_veiculo = veiculo.cod_veiculo
                  ) \n";

# Filtro por código.
if (isset($arFiltro['inCodVeiculo']) && is_numeric($arFiltro['inCodVeiculo'])) {
    $stFiltro .= " AND veiculo.cod_veiculo = ".$arFiltro['inCodVeiculo']." ";
}

# Filtro por marca.
if (isset($arFiltro['inCodMarca']) && is_numeric($arFiltro['inCodMarca'])) {
    $stFiltro .= " AND veiculo.cod_marca = ".$arFiltro['inCodMarca']." ";
}

# Filtro por modelo.
if (isset($arFiltro['inCodModelo']) && is_numeric($arFiltro['inCodModelo'])) {
    $stFiltro .= " AND veiculo.cod_modelo = ".$arFiltro['inCodModelo']." ";
}

# Filtro por tipo.
if (isset($arFiltro['slTipoVeiculo']) && is_numeric($arFiltro['slTipoVeiculo'])) {
    $stFiltro .= " AND veiculo.cod_tipo_veiculo = ".$arFiltro['slTipoVeiculo']." ";
}

# Filtro por prefixo.
if ($arFiltro['stPrefixo'] != '') {
    $stFiltro .= " AND veiculo.prefixo = '".$arFiltro['stPrefixo']."' ";
}

# Filtro por placa.
if ($arFiltro['stNumPlaca'] != '') {

    $placaVeiculo = trim(str_replace("-","",$arFiltro['stNumPlaca']));

    $stFiltro .= "\n AND SUBSTR(veiculo.placa,1,3) || '-' || SUBSTR(veiculo.placa,4,4) ILIKE '%".$placaVeiculo."%' ";
}

# Filtro por origem.
if (isset($arFiltro['stOrigem']) == 'proprio') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM frota.proprio
                                 WHERE proprio.cod_veiculo = veiculo.cod_veiculo
                              ) ";
} elseif (isset($arFiltro['stOrigem']) == 'terceiros') {
    $stFiltro .= " AND EXISTS ( SELECT 1
                                  FROM frota.terceiros
                                 WHERE terceiros.cod_veiculo = veiculo.cod_veiculo
                              ) ";
}

# Filtro por combustível.
$combustiveis = "";

if (isset($arFiltro['inCodCombustivelSelecionados']) != "") {
    foreach ($arFiltro['inCodCombustivelSelecionados'] as $chave => $dados) {
        if ($combustiveis !="") {
            $combustiveis.= ' , ';
        }
        $combustiveis.= $dados;
    }

    $stFiltro .= "\n AND EXISTS
                         (
                            SELECT  1
                              FROM  frota.veiculo_combustivel
                             WHERE  veiculo_combustivel.cod_veiculo = veiculo.cod_veiculo
                               AND  veiculo_combustivel.cod_combustivel in (".$combustiveis.")
                         ) ";
}

if (!empty($arFiltro['stOrdenacao'])) {
    $stOrder = " ORDER BY ";

    # Define o Order By da consulta.
    switch (strtolower($arFiltro['stOrdenacao'])) {
        case 'codigo': $stOrder .= ' veiculo.cod_veiculo ';                  break;
        case 'placa' : $stOrder .= ' veiculo.placa ';                        break;
        case 'marca' : $stOrder .= ' marca.nom_marca, veiculo.cod_modelo ';  break;
        case 'modelo': $stOrder .= ' modelo.nom_modelo, veiculo.cod_marca '; break;
    }
}

# Recupera os dados do banco de acordo com o filtro.
$obTFrotaVeiculo = new TFrotaVeiculo;
$obTFrotaVeiculo->recuperaVeiculoSintetico ($rsVeiculo, $stFiltro, $stOrder);

# Cria objeto Lista
$obLista = new Lista;
$stLink = "&stAcao=".$arFiltro['stAcao']."&nomForm=".$arFiltro['nomForm']."&campoNum=".$arFiltro['campoNum']."&campoNom=".$arFiltro['campoNom'];

$obLista->obPaginacao->setFiltro("&stLink=".$stLink );

$obLista->setRecordSet ($rsVeiculo);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo("&nbsp;");
$obLista->ultimoCabecalho->setWidth( 5 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Código" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Placa" );
$obLista->ultimoCabecalho->setWidth( 15 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Marca" );
$obLista->ultimoCabecalho->setWidth( 20 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Modelo" );
$obLista->ultimoCabecalho->setWidth( 50 );
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo( "Ação" );
$obLista->ultimoCabecalho->setWidth( 10 );
$obLista->commitCabecalho();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento("ESQUERDA");
$obLista->ultimoDado->setCampo( "cod_veiculo" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "placa_masc" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_marca" );
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setCampo( "nom_modelo" );
$obLista->commitDado();

$stAcao = "SELECIONAR";
$obLista->addAcao();
$obLista->ultimaAcao->setAcao( $stAcao );
$obLista->ultimaAcao->setFuncao( true );
$obLista->ultimaAcao->setLink( "JavaScript:insereVeiculo();" );
$obLista->ultimaAcao->addCampo("1","cod_veiculo");
$obLista->ultimaAcao->addCampo("2","nom_modelo");
$obLista->commitAcao();
$obLista->show();

$obFormulario = new Formulario;
$obFormulario->obJavaScript->addFuncao( $stFncJavaScript);
$obFormulario->show();

?>

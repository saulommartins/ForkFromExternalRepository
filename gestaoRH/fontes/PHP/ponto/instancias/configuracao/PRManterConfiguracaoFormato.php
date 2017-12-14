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
    * Formulário
    * Data de Criação: 03/10/2008

    * @author Desenvolvedor: Rafael Garbin

    * Casos de uso: uc-04.10.12

    $Id:
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoDelimitadorColunas.class.php"                              );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoDelimitador.class.php"                              );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoTamanhoFixo.class.php"                              );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoFormatoImportacao.class.php"                               );
include_once ( CAM_GRH_PON_MAPEAMENTO."TPontoImportacaoPonto.class.php"                                 );

$arFormatosCadastrados = Sessao::read("arFormatosCadastrados");
$stAcao = $request->get('stAcao');
$stLink = "&pg=".$arLink["pg"]."&pos=".$arLink["pos"];

$stPrograma = "ManterConfiguracaoFormato";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

//Inserindo configuração
$obTPontoFormatoImportacao  = new TPontoFormatoImportacao();
$obTPontoFormatoTamanhoFixo = new TPontoFormatoTamanhoFixo();
$obTPontoFormatoDelimitador = new TPontoFormatoDelimitador();
$obTPontoDelimitadorColunas = new TPontoDelimitadorColunas();
$obTPontoImportacaoPonto    = new TPontoImportacaoPonto();

$obTPontoFormatoDelimitador->obTPontoFormatoImportacao  = &$obTPontoFormatoImportacao;
$obTPontoFormatoTamanhoFixo->obTPontoFormatoImportacao  = &$obTPontoFormatoImportacao;
$obTPontoDelimitadorColunas->obTPontoFormatoDelimitador = &$obTPontoFormatoDelimitador;

if (count($arFormatosCadastrados)>0) {
    // Excluir os formatos de importação
    $obTPontoFormatoImportacao->recuperaTodos($rsFormatoImportacao);
    while (!$rsFormatoImportacao->eof()) {

        $inCodFormato = $rsFormatoImportacao->getCampo("cod_formato");
        $boEncontrou  = false;

        foreach ($arFormatosCadastrados as $chave => $arFormato) {
            if ($inCodFormato == $arFormato["cod_formato"]) {
                $boEncontrou  = true;
            }
        }

        if ($boEncontrou === false) {
            $stFiltro = " WHERE cod_formato = ".$inCodFormato;
            $obTPontoImportacaoPonto->recuperaTodos($rsImportacaoPonto, $stFiltro);

            if ($rsImportacaoPonto->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("Existem horários importados para o formato ".$rsFormatoImportacao->getCampo("descricao").", não é possível excluir o formato.");
            }

            $obTPontoFormatoImportacao->setDado("cod_formato", $inCodFormato);

            if ($rsFormatoImportacao->getCampo("formato_colunas") == "D") {
                $obTPontoDelimitadorColunas->exclusao();
                $obTPontoFormatoDelimitador->exclusao();
            } else {
                $obTPontoFormatoTamanhoFixo->exclusao();
            }
            $obTPontoFormatoImportacao->exclusao();
        }

        $rsFormatoImportacao->proximo();
    }

    foreach ($arFormatosCadastrados as $chave => $arFormato) {
        // Formato_Importacao
        $obTPontoFormatoImportacao->setDado("cod_formato"        , $arFormato["cod_formato"]                                );
        $obTPontoFormatoImportacao->setDado("descricao"          , $arFormato["stDescricao"]                                );
        $obTPontoFormatoImportacao->setDado("referencia_cadastro", ($arFormato["boReferenciaCadastro"]=="MATRICULA"?"M":"C"));
        $obTPontoFormatoImportacao->setDado("formato_colunas"    , ($arFormato["boFormatoColuna"]=="FIXO"?"T":"D")          );
        if ( trim($arFormato["cod_formato"]) == "" ) {
            $obTPontoFormatoImportacao->inclusao();
        } else {
            $obTPontoFormatoImportacao->alteracao();
        }

        // Formato_Tamanho_Fixo
        if (is_array($arFormato["fixo"]["posicao_inicial"])) {
            if ( trim($arFormato["cod_formato"]) != "" ) {
                $obTPontoFormatoTamanhoFixo->setDado("cod_campo"      , "");
                $obTPontoFormatoTamanhoFixo->setDado("cod_formato"    , $arFormato["cod_formato"]);
                $obTPontoFormatoTamanhoFixo->exclusao();
            }

            foreach ($arFormato["fixo"]["posicao_inicial"] as $inCodCampo => $valorPosInicial) {
                if ( trim($valorPosInicial)!="" && trim($arFormato["fixo"]["posicao_final"][$inCodCampo])!="") {
                    $obTPontoFormatoTamanhoFixo->setDado("cod_campo"      , $inCodCampo);
                    $obTPontoFormatoTamanhoFixo->setDado("posicao_inicial", $valorPosInicial);
                    $obTPontoFormatoTamanhoFixo->setDado("posicao_final"  , $arFormato["fixo"]["posicao_final"][$inCodCampo]);
                    $obTPontoFormatoTamanhoFixo->inclusao();
                }
            }
        }

        // Formato_Delimitador
        if (is_array($arFormato["delimitador"])) {
            if ( trim($arFormato["cod_formato"]) != "" ) {
                $obTPontoDelimitadorColunas->setDado("cod_campo"     , "");
                $obTPontoDelimitadorColunas->setDado("cod_formato"   , $arFormato["cod_formato"]);
                $obTPontoDelimitadorColunas->exclusao();
            }

            $obTPontoFormatoDelimitador->setDado("formato_delimitador", $arFormato["delimitador"]["delimitador"]);
            if ( trim($arFormato["cod_formato"]) == "" ) {
                $obTPontoFormatoDelimitador->inclusao();
            } else {
                $obTPontoFormatoDelimitador->alteracao();
            }

            foreach ($arFormato["delimitador"]["coluna"] as $inCodCampo => $coluna) {
                if ( trim($coluna)!="" ) {
                    #$obTPontoDelimitadorColunas->setDado("cod_formato" , $arFormato["cod_formato"]);
                    $obTPontoDelimitadorColunas->setDado("cod_campo"   , $inCodCampo);
                    $obTPontoDelimitadorColunas->setDado("coluna"      , $coluna);
                    $obTPontoDelimitadorColunas->inclusao();
                }
            }
        }
    }
} else {
    // Limpa Todas as tabelas de configuracao
    $obTPontoFormatoImportacao->recuperaTodos($rsFormatoImportacao);
    while (!$rsFormatoImportacao->eof()) {
            $stFiltro = " WHERE cod_formato = ".$rsFormatoImportacao->getCampo("cod_formato");
            $obTPontoImportacaoPonto->recuperaTodos($rsImportacaoPonto, $stFiltro);

            if ($rsImportacaoPonto->getNumLinhas() > 0) {
                Sessao::getExcecao()->setDescricao("Existem horários importados para o formato ".$rsFormatoImportacao->getCampo("descricao").", não é possível excluir o formato.");
            }

            $obTPontoFormatoImportacao->setDado("cod_formato", $rsFormatoImportacao->getCampo("cod_formato"));

            if ($rsFormatoImportacao->getCampo("formato_colunas") == "D") {
                $obTPontoDelimitadorColunas->exclusao();
                $obTPontoFormatoDelimitador->exclusao();
            } else {
                $obTPontoFormatoTamanhoFixo->exclusao();
            }
            $obTPontoFormatoImportacao->exclusao();

        $rsFormatoImportacao->proximo();
    }
}
Sessao::encerraExcecao();
Sessao::remove("arFormatosCadastrados");
sistemaLegado::alertaAviso($pgForm, "Formatos de Importação","incluir","aviso", Sessao::getId(), "../");
?>

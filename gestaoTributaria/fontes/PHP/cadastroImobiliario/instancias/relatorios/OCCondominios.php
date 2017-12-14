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
    * Frame Oculto para relatorio de condominios
    * Data de Criação: 13/02/2008

    * @author Analista: Fabio Bertoldi
    * @author Desenvolvedor: Fernando Piccini Cercato

    * $Id: OCCondominios.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.01.27
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_PDF."RRelatorio.class.php" );
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMCondominio.class.php" );

// INSTANCIA OBJETO
$obRRelatorio = new RRelatorio;

$arFiltroSessao = Sessao::read('filtroRelatorio');
$stFiltro = "";
if ( ( $arFiltroSessao["inCodCondominioInicial"] ) && ( $arFiltroSessao["inCodCondominioFinal"] ) ) {
    $stFiltro = " condominio.cod_condominio BETWEEN ".$arFiltroSessao["inCodCondominioInicial"]." AND ".$arFiltroSessao["inCodCondominioFinal"]." AND ";
}else
    if ($arFiltroSessao["inCodCondominioInicial"]) {
        $stFiltro = " condominio.cod_condominio = ".$arFiltroSessao["inCodCondominioInicial"]." AND ";
    }

if ($arFiltroSessao["stNomCondominio"]) {
    $stFiltro = " condominio.nom_condominio LIKE %".$arFiltroSessao["stNomCondominio"]."% AND ";
}

if ($stFiltro) {
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
}

$arCondominios = array();
$arAtributosSelecionados2 = $arFiltroSessao["inCodAtributosSelecionados2"];
$arAtributosSelecionados4 = $arFiltroSessao["inCodAtributosSelecionados4"];

$obTCIMCondominio = new TCIMCondominio;
$obTCIMCondominio->recuperaCondominio( $rsCondominios, $stFiltro );
while ( !$rsCondominios->Eof() ) {
    $stFiltro = " WHERE imovel_condominio.cod_condominio = ".$rsCondominios->getCampo("cod_condominio");
    $obTCIMCondominio->recuperaImoveisDoCondominio( $rsImoveisCondominio, $stFiltro );

    $arDadosImoveis = $rsImoveisCondominio->getElementos();

    for ( $inX=0; $inX<count( $arDadosImoveis ); $inX++ ) {
        $arChaveAtributoImovel = array( "inscricao_municipal" => $arDadosImoveis[$inX]['inscricao_municipal'] );
        $arChaveAtributoLote = array( "cod_lote" => $arDadosImoveis[$inX]['cod_lote'] );
        $inQtdAtribs = 0;
        if ($arAtributosSelecionados2) {
            unset( $obRCadastroDinamico );
            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
            $obRCadastroDinamico->setCodCadastro ( 2 );
            $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoLote );
            $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            while ( !$rsAtributos->eof() ) {
                if ( in_array( $rsAtributos->getCampo('cod_atributo'), $arAtributosSelecionados2 ) ) {
                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {
                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":
                                $valor = $rsAtributos->getCampo('valor');
                                break;

                            case "Numerico":
                                $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' );
                                break;

                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                                break;

                            default:
                                $valor = $rsAtributos->getCampo('valor');
                                break;
                        }
                    }

                    $inQtdAtribs++;
                    $stNome = "atribn_".$inQtdAtribs;
                    $arDadosImoveis[$inX][$stNome] = $rsAtributos->getCampo('nom_atributo');
                    $stNome = "atribv_".$inQtdAtribs;
                    $arDadosImoveis[$inX][$stNome] = $valor;
                    $arDadosImoveis[$inX]["qtd_atributo"] = $inQtdAtribs;
                }

                $rsAtributos->proximo();
            }
        }

        if ($arAtributosSelecionados4) {
            unset( $obRCadastroDinamico );
            $obRCadastroDinamico = new RCadastroDinamico;
            $obRCadastroDinamico->obRModulo->setCodModulo ( 12 );
            $obRCadastroDinamico->setCodCadastro ( 4 );
            $obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributoImovel );
            $obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
            while ( !$rsAtributos->eof() ) {
                if ( in_array( $rsAtributos->getCampo('cod_atributo'), $arAtributosSelecionados4 ) ) {
                    $valor = "";
                    if ( $rsAtributos->getCampo('valor') ) {
                        //monta array com o valor dos atributos
                        switch ( $rsAtributos->getCampo('nom_tipo') ) {
                            case "Texto":
                                $valor = $rsAtributos->getCampo('valor');
                                break;

                            case "Numerico":
                                $valor = number_format( $rsAtributos->getCampo('valor'), 2, ',' , '.' );
                                break;

                            case "Lista":
                                $arValorPadrao = explode( '[][][]' , $rsAtributos->getCampo('valor_padrao_desc'));
                                $inPosicao     = $rsAtributos->getCampo('valor') - 1;
                                $valor         = $arValorPadrao[$inPosicao];
                                break;

                            default:
                                $valor = $rsAtributos->getCampo('valor');
                                break;
                        }
                    }

                    $inQtdAtribs++;
                    $stNome = "atribn_".$inQtdAtribs;
                    $arDadosImoveis[$inX][$stNome] = $rsAtributos->getCampo('nom_atributo');
                    $stNome = "atribv_".$inQtdAtribs;
                    $arDadosImoveis[$inX][$stNome] = $valor;
                    $arDadosImoveis[$inX]["qtd_atributo"] = $inQtdAtribs;
                }

                $rsAtributos->proximo();
            }
        }
    }

    $arCondominios[] = array(
        "cod_condominio" => $rsCondominios->getCampo("cod_condominio"),
        "nom_condominio" => $rsCondominios->getCampo("nom_condominio"),
        "cod_tipo" => $rsCondominios->getCampo("cod_tipo"),
        "nom_tipo" => $rsCondominios->getCampo("nom_tipo"),
        "imoveis" => $arDadosImoveis
    );

    $rsCondominios->proximo();
}

Sessao::write('arDados', $arCondominios);

$obRRelatorio->executaFrameOculto( "OCGeraRelatorioCondominios.php" );
?>

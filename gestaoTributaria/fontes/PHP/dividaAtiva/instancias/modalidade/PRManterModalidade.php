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
  * Página de Processamento da Modalidade
  * Data de criação : 22/09/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PRManterModalidade.php 59612 2014-09-02 12:00:51Z gelson $

    Caso de uso: uc-05.04.07
**/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeVigencia.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeCredito.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducaoCredito.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeReducaoAcrescimo.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeDocumento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModeloDocumento.class.php" );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );

//MANTEM O FILTRO E A PAGINACAO
$stLink = Sessao::read('stLink');

//Define o nome dos arquivos PHP
$stPrograma = "ManterModalidade";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".$stLink;
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$_REQUEST['stAcao'];
$pgOcul = "OC".$stPrograma.".php";

switch ($_REQUEST['stAcao']) {
    case "incluir":
        $arDataInicial = explode( "/", $_REQUEST["dtVigenciaInicio"] );
        $arDataFinal = explode( "/", $_REQUEST["dtVigenciaFim"] );
        if ($arDataInicial[2].$arDataInicial[1].$arDataInicial[0] > $arDataFinal[2].$arDataFinal[1].$arDataFinal[0]) {
            SistemaLegado::exibeAviso( "O campo 'Vigência' está com a data final inferior a data inicial.", "n_incluir", "erro" );
            exit;
        }

        if ($_REQUEST["inTipo"] == 1) {
            if (!$_REQUEST["stFormaInscricao"]) {
                SistemaLegado::exibeAviso( "O campo 'Forma de Inscrição' não está preenchido.", "n_incluir", "erro" );
                exit;
            }
        }

        $arCreditoSessao = Sessao::read('credito');
        $inRegistros = count ( $arCreditoSessao );
        if ($_REQUEST["inCodCredito"]) {
            $inCodCredito = $_REQUEST["inCodCredito"];
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arCreditoSessao[$inX]['inCodCredito'] == $inCodCredito) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arCreditoSessao[$inRegistros]['inCodCredito'] = $inCodCredito;
                $inRegistros++;
            }
        }

        Sessao::write('credito', $arCreditoSessao);
        if (!$inRegistros) {
            SistemaLegado::exibeAviso( "O campo 'Crédito' não está preenchido.", "n_incluir", "erro" );
            exit;
        }

        $arAcrescimoSessao = Sessao::read('acrescimo');
        $inRegistros = count ( $arCreditoSessao );
        if ($_REQUEST["inCodAcrescimo"] && $_REQUEST["inCodFuncaoAC"]) {
            $boIncluir = true;
            $arDados = explode( ".", $_REQUEST["inCodAcrescimo"] );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ( ($arAcrescimoSessao[$inX]['inCodAcrescimo'] == $_REQUEST["inCodAcrescimo"] )
                  && ($arAcrescimoSessao[$inX]['inCodFuncaoAC']  == $_REQUEST["inCodFuncaoAC"] )
                  && ( ($_GET["stAcrescimoIncidencia"]=="ambos")?true:($arAcrescimoSessao[$inX]['stAcrescimoIncidencia'] == $_GET["stAcrescimoIncidencia"]) )           ) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                if ( $_REQUEST["stAcrescimoIncidencia"] == "ambos" )
                    $inX = 2;
                else
                    $inX = 1;

                for ($inY=0; $inY<$inX; $inY++) {
                    $arAcrescimoSessao[$inRegistros]['cod_tipo']       = $rsRecordSet->getCampo('cod_tipo');
                    $arAcrescimoSessao[$inRegistros]['inCodFuncaoAC']  = $_REQUEST["inCodFuncaoAC"];
                    $arAcrescimoSessao[$inRegistros]['inCodAcrescimo'] = $_REQUEST["inCodAcrescimo"];

                    if ($_REQUEST["stAcrescimoIncidencia"] == "ambos") {
                        if ( $inY )
                            $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = true;
                        else
                            $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = false;
                    }else
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = $_REQUEST["stAcrescimoIncidencia"];

                    $inRegistros++;
                }
            }
        }
        Sessao::write('acrescimo', $arAcrescimoSessao);

/*        if (!$inRegistros) {
//            if ( !$_REQUEST["stAcrescimoIncidencia"] )
  //              SistemaLegado::exibeAviso( "O campo 'Incidência' não está preenchido.", "n_incluir", "erro" );
    //        else
            if ( !$_REQUEST["inCodAcrescimo"] )
                SistemaLegado::exibeAviso( "O campo 'Acréscimo' não está preenchido.", "n_incluir", "erro" );
            else
                SistemaLegado::exibeAviso( "O campo 'Regra de Utilização' não está preenchido.", "n_incluir", "erro" );

            exit;
        }
*/
        $arReducaoSessao = Sessao::read('reducao');
        if ($_REQUEST["inTipo"] != 1) {
            $inRegistros = count ( $arReducaoSessao );
            if ($_REQUEST["inCodFuncaoRD"] && $_REQUEST["stValor"] && $_REQUEST["stTipoReducao"]) {
                $boIncluir = true;
                for ($inX=0; $inX<$inRegistros; $inX++) {
                    if ($arReducaoSessao[$inX]['inCodFuncaoRD'] == $_REQUEST["inCodFuncaoRD"]
                    && $arReducaoSessao[$inX]['stValor'] == $_REQUEST["stValor"]
                    && $arReducaoSessao[$inX]['stTipoReducao'] == $_REQUEST["stTipoReducao"]) {
                        $boIncluir = false;
                        break;
                    }
                }

                if ($boIncluir) {
                    $arReducaoSessao[$inRegistros]['inCodFuncaoRD'] = $_REQUEST["inCodFuncaoRD"];
                    $arReducaoSessao[$inRegistros]['stValor']       = $_REQUEST["stValor"];
                    $arReducaoSessao[$inRegistros]['stTipoReducao'] = $_REQUEST["stTipoReducao"];
                    $inRegistros++;
                }
            }

            $arParcelasSessao = Sessao::read('parcelas');

            if ($_REQUEST["inTipo"] != 2) {
                $inRegistros = count ( $arParcelasSessao );
                if ($_REQUEST["flLimiteValorInicial"] && $_REQUEST["flLimiteValorFinal"] && $_REQUEST["inQtdParcelas"] && $_REQUEST["flValorMinimo"]) {
                    $boIncluir = true;
                    for ($inX=0; $inX<$inRegistros; $inX++) {
                        if ($arParcelasSessao[$inX]['flLimiteValorInicial'] >= $_REQUEST["flLimiteValorInicial"]  && $arParcelasSessao[$inX]['flLimiteValorFinal'] <= $_REQUEST["flLimiteValorFinal"]) {
                            $boIncluir = false;
                            break;
                        }else
                        if ($arParcelasSessao[$inX]['flLimiteValorInicial'] == $_REQUEST["flLimiteValorInicial"]  && $arParcelasSessao[$inX]['flLimiteValorFinal'] == $_REQUEST["flLimiteValorFinal"]
                        && $arParcelasSessao[$inX]['inQtdParcelas'] == $_REQUEST["inQtdParcelas"]
                        && $arParcelasSessao[$inX]['flValorMinimo'] == $_REQUEST["flValorMinimo"]) {
                            $boIncluir = false;
                            break;
                        }
                    }

                    if ($boIncluir) {
                        $arParcelasSessao[$inRegistros]['flLimiteValorInicial'] = $_REQUEST["flLimiteValorInicial"];
                        $arParcelasSessao[$inRegistros]['flLimiteValorFinal']   = $_REQUEST["flLimiteValorFinal"];
                        $arParcelasSessao[$inRegistros]['inQtdParcelas']        = $_REQUEST["inQtdParcelas"];
                        $arParcelasSessao[$inRegistros]['flValorMinimo']        = $_REQUEST["flValorMinimo"];
                        $inRegistros++;
                    }
                }

                if (!$inRegistros) {
                    if ( !$_REQUEST["flLimiteValorInicial"] )
                        SistemaLegado::exibeAviso( "O campo 'Limite Valor Inicial' não está preenchido.", "n_incluir", "erro" );
                    else
                    if ( !$_REQUEST["flLimiteValorFinal"] )
                        SistemaLegado::exibeAviso( "O campo 'Limite Valor Final' não está preenchido.", "n_incluir", "erro" );
                    else
                    if ( !$_REQUEST["inQtdParcelas"] )
                        SistemaLegado::exibeAviso( "O campo 'Quantidade de Parcelas' não está preenchido.", "n_incluir", "erro" );
                    else
                        SistemaLegado::exibeAviso( "O campo 'Valor Mínimo' não está preenchido.", "n_incluir", "erro" );

                    exit;
                }
            } else {
                $inRegistros = 0;
                $arParcelasSessao[$inRegistros]['flLimiteValorInicial'] = 0.00;
                $arParcelasSessao[$inRegistros]['flLimiteValorFinal']   = 9999999999.99;
                $arParcelasSessao[$inRegistros]['inQtdParcelas']        = 1;
                $arParcelasSessao[$inRegistros]['flValorMinimo']        = 0.00;
                $inRegistros++;
            }

            Sessao::write('parcelas', $arParcelasSessao);
        }

        $arDocumentosSessao = Sessao::read('documentos');
        $inRegistros = count ( $arDocumentosSessao );
        if ($_REQUEST["stCodDocumento"]) {
            $stCodDocumento = $_REQUEST["stCodDocumento"];
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arDocumentosSessao[$inX]['stCodDocumento'] == $stCodDocumento) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $stFiltro = "where a.cod_acao = ".$stCodDocumento;
                $obTModeloDocumento = new TAdministracaoModeloDocumento;
                $obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);
                if ( !$rsDocumentos->Eof() ) {
                    $arDocumentosSessao[$inRegistros]['cod_tipo_documento'] = $rsDocumentos->getCampo( "cod_tipo_documento" );
                    $arDocumentosSessao[$inRegistros]['stCodDocumento']     = $stCodDocumento;
                    $inRegistros++;
                }
            }
        }

        if (!$inRegistros) {
            SistemaLegado::exibeAviso( "O campo 'Documento' não está preenchido.", "n_incluir", "erro" );
            exit;
        }

        $obTDATModalidade = new TDATModalidade;
        $obTDATModalidadeVigencia = new TDATModalidadeVigencia;
        $obTDATModalidadeCredito = new TDATModalidadeCredito;
        $obTDATModalidadeAcrescimo = new TDATModalidadeAcrescimo;
        $obTDATModalidadeReducao = new TDATModalidadeReducao;
        $obTDATModalidadeParcela = new TDATModalidadeParcela;
        $obTDATModalidadeDocumento = new TDATModalidadeDocumento;
        $obTDATModalidadeReducaoCredito = new TDATModalidadeReducaoCredito;
        $obTDATModalidadeReducaoAcrescimo = new TDATModalidadeReducaoAcrescimo;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATModalidade );

            //inclusao da modalidade
            $obTDATModalidade->proximoCod( $inCodModalidade );
            $obTDATModalidade->setDado( "ativa", true );
            $obTDATModalidade->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidade->setDado( "descricao", $_REQUEST["stDescricao"] );
            $obTDATModalidade->inclusao();

            $inCodTipoModalidade = $_REQUEST["inTipo"];

            switch ($_REQUEST["stFormaInscricao"]) {
                case "valor_total":
                    $inCodFormaInscricao = 1;
                    break;

                case "valor_total_credito":
                    $inCodFormaInscricao = 2;
                    break;

                case "parcela_individual":
                    $inCodFormaInscricao = 3;
                    break;

                case "parcela_individual_credito":
                    $inCodFormaInscricao = 4;
                    break;

                default:
                    $inCodFormaInscricao = 5;
                    break;
            }

            //inclusao da modalidade vigencia
            $obTDATModalidadeVigencia->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeVigencia->setDado( "cod_tipo_modalidade", $inCodTipoModalidade );
            $obTDATModalidadeVigencia->setDado( "cod_forma_inscricao", $inCodFormaInscricao );
            $obTDATModalidadeVigencia->setDado( "vigencia_inicial", $_REQUEST["dtVigenciaInicio"] );
            $obTDATModalidadeVigencia->setDado( "vigencia_final", $_REQUEST["dtVigenciaFim"] );
            $obTDATModalidadeVigencia->setDado( "cod_norma", $_REQUEST["inCodNorma"] );

            $arCodFuncao = explode('.', $_REQUEST["inCodFuncao"] );
            $obTDATModalidadeVigencia->setDado( "cod_funcao", $arCodFuncao[2] );
            $obTDATModalidadeVigencia->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTDATModalidadeVigencia->setDado( "cod_modulo", $arCodFuncao[0] );

            $obTDATModalidadeVigencia->inclusao();

            $stFiltro = " WHERE cod_modalidade = ".$inCodModalidade." ORDER BY timestamp desc limit 1";
            $obTDATModalidadeVigencia->recuperaTodos( $rsListaVigencia, $stFiltro );

            //inclusao do credito
            $arCreditoSessao = Sessao::read('credito');
            $inRegistros = count ( $arCreditoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeCredito->setDado( "cod_modalidade", $inCodModalidade );
                $inCodCreditoComposto = explode('.', $arCreditoSessao[$inX]['inCodCredito'] );
                $obTDATModalidadeCredito->setDado( "cod_especie", $inCodCreditoComposto[1] );
                $obTDATModalidadeCredito->setDado( "cod_genero", $inCodCreditoComposto[2] );
                $obTDATModalidadeCredito->setDado( "cod_natureza", $inCodCreditoComposto[3] );
                $obTDATModalidadeCredito->setDado( "cod_credito", $inCodCreditoComposto[0] );
                $obTDATModalidadeCredito->setDado( "timestamp", $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeCredito->inclusao();
            }

            //inclusao do acrescimo
            $arAcrescimoSessao = Sessao::read('acrescimo');
            $inRegistros = count ( $arAcrescimoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
                $obTDATModalidadeAcrescimo->setDado( "cod_acrescimo" , $arAcrescimoSessao[$inX]['inCodAcrescimo'] );

                $obTDATModalidadeAcrescimo->setDado( "cod_tipo" , $arAcrescimoSessao[$inX]['cod_tipo'] );
                $obTDATModalidadeAcrescimo->setDado( "pagamento", $arAcrescimoSessao[$inX]['stAcrescimoIncidencia'] );

                $arCodFuncao = explode('.', $arAcrescimoSessao[$inX]['inCodFuncaoAC'] );
                $obTDATModalidadeAcrescimo->setDado( "cod_funcao"    , $arCodFuncao[2] );
                $obTDATModalidadeAcrescimo->setDado( "cod_biblioteca", $arCodFuncao[1] );
                $obTDATModalidadeAcrescimo->setDado( "cod_modulo"    , $arCodFuncao[0] );
                $obTDATModalidadeAcrescimo->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeAcrescimo->inclusao();
            }

            //inclusao da reducao
            $arReducaoSessao = Sessao::read('reducao');
            $inRegistros = count ( $arReducaoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arReducaoSessao[$inX]['stTipoReducao'] == "valor_percentual" )
                    $boPercentual = true;
                else
                    $boPercentual = false;

                $obTDATModalidadeReducao->setDado( "cod_modalidade", $inCodModalidade );
                $arCodFuncao = explode('.', $arReducaoSessao[$inX]['inCodFuncaoRD'] );
                $obTDATModalidadeReducao->setDado( "cod_funcao"    , $arCodFuncao[2] );
                $obTDATModalidadeReducao->setDado( "cod_biblioteca", $arCodFuncao[1] );
                $obTDATModalidadeReducao->setDado( "cod_modulo"    , $arCodFuncao[0] );
                $obTDATModalidadeReducao->setDado( "percentual"    , $boPercentual );
                $obTDATModalidadeReducao->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                $obTDATModalidadeReducao->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );
                $obTDATModalidadeReducao->inclusao();

                for ( $inY=0; $inY<count($arReducaoSessao[$inX]['reducaoinc']); $inY++ ) {
                    if ($arReducaoSessao[$inX]['reducaoinc'][$inY]['tipo'] == "Acréscimo") {
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_modulo"    , $arCodFuncao[0] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_biblioteca", $arCodFuncao[1] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_funcao"    , $arCodFuncao[2] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_tipo"      , $arReducaoSessao[$inX]['reducaoinc'][$inY]['cod_tipo'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_acrescimo" , $arReducaoSessao[$inX]['reducaoinc'][$inY]['valor'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "percentual"    , $boPercentual );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "pagamento"     , $arReducaoSessao[$inX]['reducaoinc'][$inY]['pagamento'] );

                        $obTDATModalidadeReducaoAcrescimo->inclusao();
                    } else {
                        $arDadosCredito = explode(".", $arReducaoSessao[$inX]['reducaoinc'][$inY]['valor'] );

                        $obTDATModalidadeReducaoCredito->setDado( "cod_modulo"    , $arCodFuncao[0] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_biblioteca", $arCodFuncao[1] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_funcao"    , $arCodFuncao[2] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_modalidade", $inCodModalidade );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_credito"   , $arDadosCredito[0] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_natureza"  , $arDadosCredito[3] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_genero"    , $arDadosCredito[2] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_especie"   , $arDadosCredito[1] );
                        $obTDATModalidadeReducaoCredito->setDado( "percentual"    , $boPercentual );
                        $obTDATModalidadeReducaoCredito->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                        $obTDATModalidadeReducaoCredito->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );

                        $obTDATModalidadeReducaoCredito->inclusao();
                    }

                }
            }

            //inclusao da parcela
            $arParcelasSessao = Sessao::read('parcelas');
            $inRegistros = count ( $arParcelasSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeParcela->setDado( "num_regra"         , $inX+1 );
                $obTDATModalidadeParcela->setDado( "cod_modalidade"    , $inCodModalidade );
                $obTDATModalidadeParcela->setDado( "vlr_limite_inicial", $arParcelasSessao[$inX]['flLimiteValorInicial'] );
                $obTDATModalidadeParcela->setDado( "vlr_limite_final"  , $arParcelasSessao[$inX]['flLimiteValorFinal'] );
                $obTDATModalidadeParcela->setDado( "qtd_parcela"       , $arParcelasSessao[$inX]['inQtdParcelas'] );
                $obTDATModalidadeParcela->setDado( "vlr_minimo"        , $arParcelasSessao[$inX]['flValorMinimo'] );
                $obTDATModalidadeParcela->setDado( "timestamp"         , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeParcela->inclusao();
            }

            $arDocumentosSessao = Sessao::read('documentos');
            //inclusao de documentos
            $inRegistros = count ( $arDocumentosSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeDocumento->setDado( "cod_modalidade"    , $inCodModalidade );
                $obTDATModalidadeDocumento->setDado( "cod_documento"     , $arDocumentosSessao[$inX]['stCodDocumento'] );
                $obTDATModalidadeDocumento->setDado( "cod_tipo_documento", $arDocumentosSessao[$inX]['cod_tipo_documento'] );
                $obTDATModalidadeDocumento->setDado( "timestamp"         , $rsListaVigencia->getCampo("timestamp") );
                $obTDATModalidadeDocumento->inclusao();
            }

            sistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=incluir","Modalidade: ".$obTDATModalidade->getDado('cod_modalidade'),"incluir","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
        break;

/*
#################################################################################
                                ALTERAR
#################################################################################
*/
    case "alterar":
        $inCodModalidade = $_REQUEST["inCodModalidade"];

        if ($_REQUEST["inTipo"] == 1) {
            if (!$_REQUEST["stFormaInscricao"]) {
                SistemaLegado::exibeAviso( "O campo 'Forma de Inscrição' não está preenchido.", "n_alterar", "erro" );
                exit;
            }
        }

        $arCreditoSessao = Sessao::read('credito');
        $inRegistros = count ( $arCreditoSessao );
        if ($_GET["inCodCredito"]) {
            $inCodCredito = $_REQUEST["inCodCredito"];
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arCreditoSessao[$inX]['inCodCredito'] == $inCodCredito) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arCreditoSessao[$inRegistros]['inCodCredito'] = $inCodCredito;
                $inRegistros++;
            }
        }

        Sessao::write('credito', $arCreditoSessao);
        if (!$inRegistros) {
            SistemaLegado::exibeAviso( "O campo 'Crédito' não está preenchido.", "n_alterar", "erro" );
            exit;
        }

        $arAcrescimoSessao = Sessao::read('acrescimo');
        $inRegistros = count ( $arAcrescimoSessao );
        if ($_REQUEST["inCodAcrescimo"] && $_REQUEST["inCodFuncaoAC"] && $_REQUEST["stAcrescimoIncidencia"]) {
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ( ($arAcrescimoSessao[$inX]['inCodAcrescimo'] == $_REQUEST["inCodAcrescimo"] )
                    && ( $arAcrescimoSessao[$inX]['inCodFuncaoAC'] == $_REQUEST["inCodFuncaoAC"] )
                    && ( ($_GET["stAcrescimoIncidencia"]=="ambos")?true:($arAcrescimoSessao[$inX]['stAcrescimoIncidencia'] == $_GET["stAcrescimoIncidencia"]) )
                ) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $obTMONAcrescimo = new TMONAcrescimo();
                $obTMONAcrescimo->setDado('cod_acrescimo', $_REQUEST["inCodAcrescimo"] );
                $obTMONAcrescimo->recuperaPorChave($rsRecordSet);

                if ( $_REQUEST["stAcrescimoIncidencia"] == "ambos" )
                    $inX = 2;
                else
                    $inX = 1;

                for ($inY=0; $inY<$inX; $inY++) {
                    if ($_REQUEST["stAcrescimoIncidencia"] == "ambos") {
                        if ( $inY )
                            $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = true;
                        else
                            $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = false;
                    }else
                        $arAcrescimoSessao[$inRegistros]['stAcrescimoIncidencia'] = $_REQUEST["stAcrescimoIncidencia"];

                    $arAcrescimoSessao[$inRegistros]['cod_tipo']       = $rsRecordSet->getCampo('cod_tipo');
                    $arAcrescimoSessao[$inRegistros]['inCodFuncaoAC']  = $_REQUEST["inCodFuncaoAC"];
                    $arAcrescimoSessao[$inRegistros]['inCodAcrescimo'] = $_REQUEST["inCodAcrescimo"];
                    $inRegistros++;
                }

            }
        }
        Sessao::write('acrescimo', $arAcrescimoSessao);

/*        if (!$inRegistros) {
//            if ( !$_REQUEST["stAcrescimoIncidencia"] )
  //              SistemaLegado::exibeAviso( "O campo 'Incidência' não está preenchido.", "n_incluir", "erro" );
    //        else
            if ( !$_REQUEST["inCodAcrescimo"] )
                SistemaLegado::exibeAviso( "O campo 'Acréscimo' não está preenchido.", "n_alterar", "erro" );
            else
                SistemaLegado::exibeAviso( "O campo 'Regra de Utilização' não está preenchido.", "n_alterar", "erro" );

            exit;
        }
*/
        $arReducaoSessao = Sessao::read('reducao');
        $inRegistros = count ( $arReducaoSessao );
        if ($_REQUEST["inCodFuncaoRD"] && $_REQUEST["stValor"] && $_REQUEST["stTipoReducao"]) {
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arReducaoSessao[$inX]['inCodFuncaoRD'] == $_REQUEST["inCodFuncaoRD"]
                  && $arReducaoSessao[$inX]['stValor']       == $_REQUEST["stValor"]
                  && $arReducaoSessao[$inX]['stTipoReducao'] == $_REQUEST["stTipoReducao"]) {
                    $boIncluir = false;
                    break;
                 }
            }

            if ($boIncluir) {
                $arReducaoSessao[$inRegistros]['inCodFuncaoRD'] = $_REQUEST["inCodFuncaoRD"];
                $arReducaoSessao[$inRegistros]['stValor']       = $_REQUEST["stValor"];
                $arReducaoSessao[$inRegistros]['stTipoReducao'] = $_REQUEST["stTipoReducao"];
                $inRegistros++;
            }
        }
Sessao::write('reducao', $arReducaoSessao);
/*
        if ($inRegistros) {
            if ( !$_REQUEST["inCodFuncaoRD"] )
                SistemaLegado::exibeAviso( "O campo 'Regra de Utilização' não está preenchido.", "n_alterar", "erro" );
            else
            if ( !$_REQUEST["stValor"] )
                SistemaLegado::exibeAviso( "O campo 'Valor' não está preenchido.", "n_alterar", "erro" );
            else
                SistemaLegado::exibeAviso( "O campo 'Tipo da Redução' não está preenchido.", "n_alterar", "erro" );
        }
*/
        $arParcelasSessao  = Sessao::read('parcelas');
        $inRegistros = count ( $arParcelasSessao );
        if ($_REQUEST["flLimiteValorInicial"] && $_REQUEST["flLimiteValorFinal"] && $_REQUEST["inQtdParcelas"] && $_REQUEST["flValorMinimo"]) {
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arParcelasSessao[$inX]['flLimiteValorInicial'] >= $_REQUEST["flLimiteValorInicial"]  && $arParcelasSessao[$inX]['flLimiteValorFinal'] <= $_REQUEST["flLimiteValorFinal"]) {
                    $boIncluir = false;
                    break;
                }else
                if ($arParcelasSessao[$inX]['flLimiteValorInicial'] == $_REQUEST["flLimiteValorInicial"]  && $arParcelasSessao[$inX]['flLimiteValorFinal'] == $_REQUEST["flLimiteValorFinal"]
                 && $arParcelasSessao[$inX]['inQtdParcelas'] == $_REQUEST["inQtdParcelas"]
                 && $arParcelasSessao[$inX]['flValorMinimo'] == $_REQUEST["flValorMinimo"]) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $arParcelasSessao[$inRegistros]['flLimiteValorInicial'] = $_REQUEST["flLimiteValorInicial"];
                $arParcelasSessao[$inRegistros]['flLimiteValorFinal']   = $_REQUEST["flLimiteValorFinal"];
                $arParcelasSessao[$inRegistros]['inQtdParcelas']        = $_REQUEST["inQtdParcelas"];
                $arParcelasSessao[$inRegistros]['flValorMinimo']        = $_REQUEST["flValorMinimo"];
                $inRegistros++;
            }
        }
        Sessao::write('parcelas', $arParcelasSessao);

        if (!$inRegistros) {
            if ($_REQUEST["inTipo"] == 3) {
                if ( !$_REQUEST["flLimiteValorInicial"] )
                    SistemaLegado::exibeAviso( "O campo 'Limite Valor Inicial' não está preenchido.", "n_alterar", "erro" );
                else
                if ( !$_REQUEST["flLimiteValorFinal"] )
                    SistemaLegado::exibeAviso( "O campo 'Limite Valor Final' não está preenchido.", "n_alterar", "erro" );
                else
                if ( !$_REQUEST["inQtdParcelas"] )
                    SistemaLegado::exibeAviso( "O campo 'Quantidade de Parcelas' não está preenchido.", "n_alterar", "erro" );
                else
                    SistemaLegado::exibeAviso( "O campo 'Valor Mínimo' não está preenchido.", "n_alterar", "erro" );

                exit;
            } else {
                $inRegistros = 0;
                $arParcelasSessao[$inRegistros]['flLimiteValorInicial'] = 0.00;
                $arParcelasSessao[$inRegistros]['flLimiteValorFinal'] = 9999999999.99;
                $arParcelasSessao[$inRegistros]['inQtdParcelas'] = 1;
                $arParcelasSessao[$inRegistros]['flValorMinimo'] = 0.00;
                $inRegistros++;
            }
        }
        Sessao::write('parcelas', $arParcelasSessao);
        $arDocumentosSessao = Sessao::read('documentos');
        $inRegistros = count ( $arDocumentosSessao );
        if ($_REQUEST["stCodDocumento"]) {
            $stCodDocumento = $_REQUEST["stCodDocumento"];
            $boIncluir = true;
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arDocumentosSessao[$inX]['stCodDocumento'] == $stCodDocumento) {
                    $boIncluir = false;
                    break;
                }
            }

            if ($boIncluir) {
                $stFiltro = "where a.cod_acao = ".$stCodDocumento;
                $obTModeloDocumento = new TAdministracaoModeloDocumento;
                $obTModeloDocumento->recuperaRelacionamento($rsDocumentos, $stFiltro);
                if ( !$rsDocumentos->Eof() ) {
                    $arDocumentosSessao[$inRegistros]['cod_tipo_documento'] = $rsDocumentos->getCampo( "cod_tipo_documento" );
                    $arDocumentosSessao[$inRegistros]['stCodDocumento']     = $stCodDocumento;
                    $inRegistros++;
                }
            }
        }

        Sessao::write('documentos', $arDocumentosSessao);
        if ($inRegistros) {
            SistemaLegado::exibeAviso( "O campo 'Documento' não está preenchido.", "n_alterar", "erro" );
        }

        $obTDATModalidadeVigencia         = new TDATModalidadeVigencia;
        $obTDATModalidadeCredito          = new TDATModalidadeCredito;
        $obTDATModalidadeAcrescimo        = new TDATModalidadeAcrescimo;
        $obTDATModalidadeReducao          = new TDATModalidadeReducao;
        $obTDATModalidadeParcela          = new TDATModalidadeParcela;
        $obTDATModalidadeDocumento        = new TDATModalidadeDocumento;
        $obTDATModalidadeReducaoCredito   = new TDATModalidadeReducaoCredito;
        $obTDATModalidadeReducaoAcrescimo = new TDATModalidadeReducaoAcrescimo;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATModalidadeVigencia );

            $inCodTipoModalidade = $_REQUEST["inTipo"];

            switch ($_REQUEST["stFormaInscricao"]) {
                case "valor_total":
                    $inCodFormaInscricao = 1;
                    break;

                case "valor_total_credito":
                    $inCodFormaInscricao = 2;
                    break;

                case "parcela_individual":
                    $inCodFormaInscricao = 3;
                    break;

                case "parcela_individual_credito":
                    $inCodFormaInscricao = 4;
                    break;

                default:
                    $inCodFormaInscricao = 5;
                    break;
            }

            //inclusao da modalidade vigencia
            $obTDATModalidadeVigencia->setDado( "cod_modalidade"     , $inCodModalidade );
            $obTDATModalidadeVigencia->setDado( "cod_tipo_modalidade", $inCodTipoModalidade );
            $obTDATModalidadeVigencia->setDado( "cod_forma_inscricao", $inCodFormaInscricao );
            $obTDATModalidadeVigencia->setDado( "vigencia_inicial"   , $_REQUEST["dtVigenciaInicio"] );
            $obTDATModalidadeVigencia->setDado( "vigencia_final"     , $_REQUEST["dtVigenciaFim"] );
            $obTDATModalidadeVigencia->setDado( "cod_norma"          , $_REQUEST["inCodNorma"] );

            $arCodFuncao = explode('.', $_REQUEST["inCodFuncao"] );
            $obTDATModalidadeVigencia->setDado( "cod_funcao"    , $arCodFuncao[2] );
            $obTDATModalidadeVigencia->setDado( "cod_biblioteca", $arCodFuncao[1] );
            $obTDATModalidadeVigencia->setDado( "cod_modulo"    , $arCodFuncao[0] );

            $obTDATModalidadeVigencia->inclusao();

            $stFiltro = " WHERE cod_modalidade = ".$inCodModalidade." ORDER BY timestamp desc limit 1";
            $obTDATModalidadeVigencia->recuperaTodos( $rsListaVigencia, $stFiltro );

            //inclusao do credito
            $arCreditoSessao = Sessao::read('credito');
            $inRegistros = count ( $arCreditoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeCredito->setDado( "cod_modalidade" , $inCodModalidade );
                $inCodCreditoComposto = explode('.', $arCreditoSessao[$inX]['inCodCredito'] );
                $obTDATModalidadeCredito->setDado( "cod_especie"    , $inCodCreditoComposto[1] );
                $obTDATModalidadeCredito->setDado( "cod_genero"     , $inCodCreditoComposto[2] );
                $obTDATModalidadeCredito->setDado( "cod_natureza"   , $inCodCreditoComposto[3] );
                $obTDATModalidadeCredito->setDado( "cod_credito"    , $inCodCreditoComposto[0] );
                $obTDATModalidadeCredito->setDado( "timestamp"      , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeCredito->inclusao();
            }

            //inclusao do acrescimo
            $arAcrescimoSessao = Sessao::read('acrescimo');
            $inRegistros = count ( $arAcrescimoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
                $obTDATModalidadeAcrescimo->setDado( "cod_acrescimo" , $arAcrescimoSessao[$inX]['inCodAcrescimo'] );
                $obTDATModalidadeAcrescimo->setDado( "pagamento"     , $arAcrescimoSessao[$inX]['stAcrescimoIncidencia'] );
                $obTDATModalidadeAcrescimo->setDado( "cod_tipo"      , $arAcrescimoSessao[$inX]['cod_tipo'] );

                $arCodFuncao = explode('.', $arAcrescimoSessao[$inX]['inCodFuncaoAC'] );
                $obTDATModalidadeAcrescimo->setDado( "cod_funcao"    , $arCodFuncao[2] );
                $obTDATModalidadeAcrescimo->setDado( "cod_biblioteca", $arCodFuncao[1] );
                $obTDATModalidadeAcrescimo->setDado( "cod_modulo"    , $arCodFuncao[0] );
                $obTDATModalidadeAcrescimo->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeAcrescimo->inclusao();
            }

            //inclusao da reducao
            $arReducaoSessao = Sessao::read('reducao');
            $inRegistros = count ( $arReducaoSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                if ($arReducaoSessao[$inX]['stTipoReducao'] == "valor_percentual" )
                    $boPercentual = true;
                else
                    $boPercentual = false;

                $obTDATModalidadeReducao->setDado( "cod_modalidade", $inCodModalidade );
                $arCodFuncao = explode('.', $arReducaoSessao[$inX]['inCodFuncaoRD'] );
                $obTDATModalidadeReducao->setDado( "cod_funcao"    , $arCodFuncao[2] );
                $obTDATModalidadeReducao->setDado( "cod_biblioteca", $arCodFuncao[1] );
                $obTDATModalidadeReducao->setDado( "cod_modulo"    , $arCodFuncao[0] );
                $obTDATModalidadeReducao->setDado( "percentual"    , $boPercentual );
                $obTDATModalidadeReducao->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                $obTDATModalidadeReducao->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeReducao->inclusao();

                for ( $inY=0; $inY<count($arReducaoSessao[$inX]['reducaoinc']); $inY++ ) {
                    if ($arReducaoSessao[$inX]['reducaoinc'][$inY]['tipo'] == "Acréscimo") {
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_modulo"    , $arCodFuncao[0] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_biblioteca", $arCodFuncao[1] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_funcao"    , $arCodFuncao[2] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_tipo"      , $arReducaoSessao[$inX]['reducaoinc'][$inY]['cod_tipo'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "cod_acrescimo" , $arReducaoSessao[$inX]['reducaoinc'][$inY]['valor'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "percentual"    , $boPercentual );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "timestamp"     , $rsListaVigencia->getCampo("timestamp") );
                        $obTDATModalidadeReducaoAcrescimo->setDado( "pagamento"     , false );

                        $obTDATModalidadeReducaoAcrescimo->inclusao();
                    } else {
                        $arDadosCredito = explode(".", $arReducaoSessao[$inX]['reducaoinc'][$inY]['valor'] );

                        $obTDATModalidadeReducaoCredito->setDado( "cod_modulo"     , $arCodFuncao[0] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_biblioteca" , $arCodFuncao[1] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_funcao"     , $arCodFuncao[2] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_modalidade" , $inCodModalidade );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_credito"    , $arDadosCredito[0] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_natureza"   , $arDadosCredito[3] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_genero"     , $arDadosCredito[2] );
                        $obTDATModalidadeReducaoCredito->setDado( "cod_especie"    , $arDadosCredito[1] );
                        $obTDATModalidadeReducaoCredito->setDado( "percentual"    , $boPercentual );
                        $obTDATModalidadeReducaoCredito->setDado( "valor"         , $arReducaoSessao[$inX]['stValor'] );
                        $obTDATModalidadeReducaoCredito->setDado( "timestamp"      , $rsListaVigencia->getCampo("timestamp") );

                        $obTDATModalidadeReducaoCredito->inclusao();
                    }

                }
            }

            //inclusao da parcela
            $arParcelasSessao = Sessao::read('parcelas');
            $inRegistros      = count ( $arParcelasSessao );

            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeParcela->setDado( "num_regra"         , $inX+1 );
                $obTDATModalidadeParcela->setDado( "cod_modalidade"    , $inCodModalidade );
                $obTDATModalidadeParcela->setDado( "vlr_limite_inicial", $arParcelasSessao[$inX]['flLimiteValorInicial'] );
                $obTDATModalidadeParcela->setDado( "vlr_limite_final"  , $arParcelasSessao[$inX]['flLimiteValorFinal'] );
                $obTDATModalidadeParcela->setDado( "qtd_parcela"       , $arParcelasSessao[$inX]['inQtdParcelas'] );
                $obTDATModalidadeParcela->setDado( "vlr_minimo"        , $arParcelasSessao[$inX]['flValorMinimo'] );
                $obTDATModalidadeParcela->setDado( "timestamp"         , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeParcela->inclusao();
            }
            //inclusao de documentos
            $arDocumentosSessao = Sessao::read('documentos');
            $inRegistros        = count ( $arDocumentosSessao );
            for ($inX=0; $inX<$inRegistros; $inX++) {
                $obTDATModalidadeDocumento->setDado( "cod_modalidade"    , $inCodModalidade );
                $obTDATModalidadeDocumento->setDado( "cod_documento"     , $arDocumentosSessao[$inX]['stCodDocumento'] );
                $obTDATModalidadeDocumento->setDado( "cod_tipo_documento", $arDocumentosSessao[$inX]['cod_tipo_documento'] );
                $obTDATModalidadeDocumento->setDado( "timestamp"         , $rsListaVigencia->getCampo("timestamp") );

                $obTDATModalidadeDocumento->inclusao();
            }

            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=alterar","Modalidade: ".$obTDATModalidadeVigencia->getDado('cod_modalidade'),"alterar","aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
        break;

    case "excluir":
        $inCodModalidade = $_REQUEST["inCodModalidade"];
        $stFiltro = " WHERE cod_modalidade = ".$inCodModalidade;
        $obTDATDividaParcelamento = new TDATParcelamento;
        $obTDATDividaParcelamento->recuperaTodos( $rsDivida, $stFiltro );
        if ( !$rsDivida->eof() ) {
            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", "A modalidade está sendo utilizada em alguma inscrição em dívida ativa.", "n_excluir", "erro" );
            exit;
        }

        $obTDATModalidade = new TDATModalidade;
        $obTDATModalidadeVigencia = new TDATModalidadeVigencia;
        $obTDATModalidadeCredito = new TDATModalidadeCredito;
        $obTDATModalidadeAcrescimo = new TDATModalidadeAcrescimo;
        $obTDATModalidadeReducao = new TDATModalidadeReducao;
        $obTDATModalidadeReducaoCredito = new TDATModalidadeReducaoCredito;
        $obTDATModalidadeReducaoAcrescimo = new TDATModalidadeReducaoAcrescimo;
        $obTDATModalidadeParcela = new TDATModalidadeParcela;
        $obTDATModalidadeDocumento = new TDATModalidadeDocumento;

        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTDATModalidade );

            $obTDATModalidadeReducaoAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeReducaoAcrescimo->exclusao();

            $obTDATModalidadeReducaoCredito->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeReducaoCredito->exclusao();

            $obTDATModalidadeReducao->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeReducao->exclusao();

            $obTDATModalidadeCredito->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeCredito->exclusao();

            $obTDATModalidadeAcrescimo->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeAcrescimo->exclusao();

            $obTDATModalidadeDocumento->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeDocumento->exclusao();

            $obTDATModalidadeParcela->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeParcela->exclusao();

            $obTDATModalidadeVigencia->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidadeVigencia->exclusao();

            $obTDATModalidade->setDado( "cod_modalidade", $inCodModalidade );
            $obTDATModalidade->exclusao();

            sistemaLegado::alertaAviso($pgList."?".Sessao::getId()."&stAcao=excluir", "Modalidade: ".$obTDATModalidade->getDado('cod_modalidade'), "excluir", "aviso", Sessao::getId(), "../");

        Sessao::encerraExcecao();
        break;
}

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
    * Página de Frame Oculto de Autoridade
    * Data de Criação   : 03/01/2007

    * @author Analista: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato

    * @ignore

    * $Id: OCManterCobranca.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaParcelamento.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidade.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATModalidadeParcela.class.php" );

switch ($_REQUEST['stCtrl']) {
    case "buscaModalidade":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ($_GET[$stNomCampo]) {

            $obTDATModalidade = new TDATModalidade;
            $stFiltro = " WHERE dm.ativa = 't' AND dm.cod_modalidade = ".$_GET[$stNomCampo]." AND dmv.vigencia_inicial <= '".date('Y-m-d')."' AND dmv.vigencia_final >= '".date('Y-m-d')."'";
            if ($_GET["tipoModalidade"]) {
                if ($_GET["tipoModalidade"] == 4) {
                    $stFiltro .= " AND ( dmv.cod_tipo_modalidade = 2 OR dmv.cod_tipo_modalidade = 3 ) ";
                } else {
                    $stFiltro .= " AND dmv.cod_tipo_modalidade = ".$_GET['tipoModalidade'];
                }
            }

            $obTDATModalidade->recuperaListaModalidade( $rsModalidade, $stFiltro, " ORDER BY dm.cod_modalidade " );
            if ( !$rsModalidade->Eof() ) {
                $stDescricao = $rsModalidade->getCampo("descricao");
                $stJs = "retornaValorBscInner( '".$_GET['stNomCampoCod']."', '".$_GET['stIdCampoDesc']."', 'frm', '".$stDescricao."');";

                $stFiltro = " WHERE cod_modalidade = ".$rsModalidade->getCampo("cod_modalidade")." AND timestamp = '".$rsModalidade->getCampo("timestamp")."'";
                $obTDATModalidadeParcela = new TDATModalidadeParcela;
                $obTDATModalidadeParcela->recuperaTodos( $rsListaParcelas, $stFiltro, " ORDER BY qtd_parcela DESC " );

                $stJs .= "limpaSelect(f.cmbParcelas,1); \n";
                $stJs .= "f.cmbParcelas[0] = new Option('Selecione','', 'selected');\n";
                if ( !$rsListaParcelas->eof() ) {
                    for ( $inContador=1; $inContador<=$rsListaParcelas->getCampo("qtd_parcela"); $inContador++ )
                        $stJs .= "f.cmbParcelas.options[$inContador] = new Option('".$inContador."','".$inContador."'); \n";
                }

                if ( $rsListaParcelas->getCampo("qtd_parcela") == 1 )
                    $stJs .= "f.cmbParcelas.value='1'; \n";
            } else {
                $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
                $stJs .= "d.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
                $stJs .= "limpaSelect(f.cmbParcelas,1); \n";
                $stJs .= "f.cmbParcelas[0] = new Option('Selecione','', 'selected');\n";
                $stJs .= "alertaAviso('@Código modalidade inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            }
        } else {
            $stJs .= "d.getElementById('".$_GET['stIdCampoDesc']."').innerHTML = '&nbsp;';\n";
        }

        echo $stJs;
        break;

    case "atualizaParcelas":
        $obTDATModalidade = new TDATModalidade;
        $stFiltro = " WHERE dm.ativa = 't' AND dm.cod_modalidade = ".$_REQUEST["inCodModalidade"]." AND dmv.vigencia_inicial <= '".date('Y-m-d')."' AND dmv.vigencia_final >= '".date('Y-m-d')."'";
        $obTDATModalidade->recuperaListaModalidade( $rsModalidade, $stFiltro, " ORDER BY dm.cod_modalidade " );
        if ( !$rsModalidade->Eof() ) {
            $stFiltro = " WHERE cod_modalidade = ".$rsModalidade->getCampo("cod_modalidade")." AND timestamp = '".$rsModalidade->getCampo("timestamp")."'";
            $obTDATModalidadeParcela = new TDATModalidadeParcela;
            $obTDATModalidadeParcela->recuperaTodos( $rsListaParcelas, $stFiltro, " ORDER BY qtd_parcela DESC " );
            $stJs = "limpaSelect(f.cmbParcelas,1); \n";
            $stJs .= "f.cmbParcelas[0] = new Option('Selecione','', 'selected');\n";
            if ( !$rsListaParcelas->eof() ) {
                for ( $inContador=1; $inContador<=$rsListaParcelas->getCampo("qtd_parcela"); $inContador++ )
                    $stJs .= "f.cmbParcelas.options[$inContador] = new Option('".$inContador."','".$inContador."'); \n";
            }

            if ( $rsListaParcelas->getCampo("qtd_parcela") == 1 )
                $stJs .= "f.cmbParcelas.value='1'; \n";

            sistemaLegado::executaFrameOculto( $stJs );
        }
        break;

    case "buscaCobranca":
        $stDescricao = '&nbsp;';
        $stNomCampo = $_GET["stNomCampoCod"];
        if ( strlen($_GET[$stNomCampo]) ) {
            $obTDATParcelamento = new TDATParcelamento;
            $obTDATDividaParcelamento = new TDATDividaParcelamento;
            $obTDATDividaParcelamento->recuperaCodigoCobrancaComponente( $rsInscricao );
            if ( $rsInscricao->Eof() )
                $inTamanho = 1;
            else
                $inTamanho = strlen( $rsInscricao->getCampo( "max_inscricao" ) );

            $stDados = str_replace( "/", "", $_GET[$stNomCampo] );
            $inTamanhoOrigem = strlen( $stDados );
            if ($inTamanhoOrigem >= 5) {
                $stAno = substr( $stDados, $inTamanhoOrigem-4, 4 );
                $stCodigo = substr( $stDados, 0, $inTamanhoOrigem-4 );
                $stFiltro = " WHERE numero_parcelamento = '".$stCodigo."' AND exercicio = '".$stAno."'";
                $obTDATParcelamento->recuperaTodos( $rsInscricao, $stFiltro );
                if ( $rsInscricao->getNumLinhas() < 1 )
                    $boErro= true;
                else {
                    $stValor = "";
                    $inTamanhoOrigem -= 4;
                    for ($inX=0; $inX<$inTamanho-$inTamanhoOrigem; $inX++) {
                        $stValor .= "0";
                    }

                    $stValor .= $stCodigo;
                    $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "'.$stValor.'/'.$stAno.'";';
                    echo $stJs;
                }
            }else
                $boErro= true;
        }

        if ($boErro) {
            $stJs = 'f.'.$_GET["stNomCampoCod"].'.value = "";';
            $stJs .= 'f.'.$_GET["stNomCampoCod"].'.focus();';
            $stJs .= "alertaAviso('@Código de cobrança de dívida ativa inválido (".$_GET[$stNomCampo] .").','form','erro','".Sessao::getId()."');";
            echo $stJs;
        }
        break;

    case "baixarArquivo":
        $content_type = 'application/sxw';
        $download = $_REQUEST["stDisposition"];

        header ( "Content-Length: ".$_REQUEST["stLength"] );
        header ( "Content-type: $content_type" );
        header ( "Content-Disposition: attachment; filename=\"$download\"" );

        readfile( $_REQUEST["stArquivo"] );
        break;

    case "alteraData":
        $arParcelasSessao = Sessao::read('parcelas');
        $inTotalParcelas = count($arParcelasSessao);
        $js  = "";

        for ($inX=0; $inX < $inTotalParcelas; $inX++) {

            $stString = "Vencimento_".($inX + 1);
            $stData = $_REQUEST[$stString];

            if ($stData == '') {
                $stData = $arParcelasSessao[$inX][data_vencimento];
            }
            $arParcelasSessao[$inX]["data_vencimento"] = $stData;

            $js .= 'f.'.$stString.'.value = "'.$stData.'";';
            Sessao::write('parcelas', $arParcelasSessao);
            sistemaLegado::executaFrameOculto($js);
        }

    break;
}

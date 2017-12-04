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
  * Data de criação : 03/10/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * $Id: PREmitirDocumento.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GT_ARR_MAPEAMENTO."FARRRelatorioExtratoDebitos.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRParcelaDocumento.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRDocumentoEmissao.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRDocumentoCGM.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRDocumentoImovel.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRDocumentoEmpresa.class.php";

$stAcao = $request->get('stAcao');

//MANTEM O FILTRO E A PAGINACAO
$link = Sessao::read('link');
$stLink = "";
if (!is_null($link) ) {
    if (isset($link["pg"]) && isset($link["pos"])) {
        $stLink = "&pg=".$link["pg"]."&pos=".$link["pos"]."&stAcao=".$stAcao;
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "EmitirDocumento";
$pgFilt = "FL".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao";
$pgOcul = "OC".$stPrograma.".php";

switch ($stAcao) {
    case "emitir":

        $stFiltroSQL = "";

        if (!$_REQUEST["inCodImovel"] && !$_REQUEST["inCGM"] && !$_REQUEST["inInscricaoEconomica"]) {
            SistemaLegado::exibeAviso( "Ao menos o campo Contribuinte ou Inscrição Imobiliária ou Inscrição Econômica deve ser informado!", "n_incluir", "erro" );
            exit;
        }

        if ($_REQUEST["inCGM"]) {
            $stFiltroSQL .= " accgm.numcgm = ".$_REQUEST["inCGM"];
        }

        if ( $_REQUEST["inCGM"] && (($_REQUEST["inCodImovel"])||($_REQUEST["inInscricaoEconomica"]))) {
            $stFiltroSQL .= " AND ";
        }

        if ($_REQUEST["inCodImovel"]) {
            $stFiltroSQL .= "\n divida_remissao.cod_inscricao IS NULL";
            $stFiltroINNER = "
                INNER JOIN arrecadacao.imovel_calculo as aic
                ON aic.cod_calculo = ac.cod_calculo
        AND aic.inscricao_municipal = ".$_REQUEST["inCodImovel"]."
                LEFT JOIN divida.divida_remissao
        ON aic.inscricao_municipal = divida_remissao.cod_inscricao
                AND ac.exercicio = divida_remissao.exercicio
            ";
        }

        if ($_REQUEST["inInscricaoEconomica"]) {
            $stFiltroSQL .= " cec.inscricao_economica = ".$_REQUEST["inInscricaoEconomica"];
            $stFiltroINNER = "
                INNER JOIN arrecadacao.cadastro_economico_calculo as cec
                ON cec.cod_calculo = ac.cod_calculo
            ";
        }

        $boFARRRelatorioExtratoDebitos = new FARRRelatorioExtratoDebitos;
        $boFARRRelatorioExtratoDebitos->recuperaRelatorioOrigem ( $rsListaOrigem, $stFiltroINNER, $stFiltroSQL );
        $boFARRRelatorioExtratoDebitos->recuperaRelatorioOrigemFiltro ( $rsListaOrigemFiltro, $stFiltroINNER, $stFiltroSQL, " busca_parcelas.vencimento < '".date("Y-m-d")."' AND" ); //puscando parcelas vencidas (certidao positiva)

        $arDados = $rsListaOrigemFiltro->getElementos();
        $arDadosFinais = array();
        $inTotalLancamentos = 0;
        $boPositivo = false;
        $boPosNeg = false;
        while ( !$rsListaOrigem->Eof() ) {
            $boEncontrado = false;
            for ( $inX=0; $inX<count($arDados); $inX++ ) {
                if ( $rsListaOrigem->getCampo("cod_lancamento") == $arDados[$inX]["cod_lancamento"] ) { //encontrando lancamento com parcelas vencidas (certidao positiva)
                    $arDadosFinais[$inTotalLancamentos]["cod_lancamento"] = $rsListaOrigem->getCampo("cod_lancamento");
                    $arDadosFinais[$inTotalLancamentos]["valor"] = $rsListaOrigem->getCampo("valor");
                    $arDadosFinais[$inTotalLancamentos]["qtde_parcelas_nao_pagas"] = $rsListaOrigem->getCampo("qtde");
                    $arDadosFinais[$inTotalLancamentos]["qtde_parcelas_nao_pagas_vencidas"] = $arDados[$inX]["qtde"];
                    $arDadosFinais[$inTotalLancamentos]["origem"] = $rsListaOrigem->getCampo("origem");
                    $arDadosFinais[$inTotalLancamentos]["exercicio"] = $rsListaOrigem->getCampo("exercicio");
                    $arDadosFinais[$inTotalLancamentos]["situacao"] = "positivo";
                    $boPositivo = true;
                    $inTotalLancamentos++;
                    $boEncontrado = true;
                    break;
                }
            }

            if (!$boEncontrado) {
                $arDadosFinais[$inTotalLancamentos]["cod_lancamento"] = $rsListaOrigem->getCampo("cod_lancamento");
                $arDadosFinais[$inTotalLancamentos]["valor"] = $rsListaOrigem->getCampo("valor");
                $arDadosFinais[$inTotalLancamentos]["qtde_parcelas_nao_pagas"] = $rsListaOrigem->getCampo("qtde");
                $arDadosFinais[$inTotalLancamentos]["qtde_parcelas_nao_pagas_vencidas"] = $rsListaOrigem->getCampo("qtde");
                $arDadosFinais[$inTotalLancamentos]["origem"] = $rsListaOrigem->getCampo("origem");
                $arDadosFinais[$inTotalLancamentos]["exercicio"] = $rsListaOrigem->getCampo("exercicio");
                $arDadosFinais[$inTotalLancamentos]["situacao"] = "posneg";
                $boPosNeg = true;
                $inTotalLancamentos++;
            }

            $rsListaOrigem->proximo();
        }

        $obTARRParcelaDocumento = new TARRParcelaDocumento;
        $obTARRDocumentoEmissao = new TARRDocumentoEmissao;
        $obTARRDocumentoCGM = new TARRDocumentoCGM;
        $obTARRDocumentoImovel = new TARRDocumentoImovel;
        $obTARRDocumentoEmpresa = new TARRDocumentoEmpresa;

        $arTipoDoc = explode( "§", $_REQUEST["cmbTipoDocumento"] );
        Sessao::setTrataExcecao( true );
        Sessao::getTransacao()->setMapeamento( $obTARRDocumento );

        $obTARRDocumentoEmissao->recuperaProxNumDocumento( $rsProxNumDoc, $arTipoDoc[0], Sessao::getExercicio() );
        if ( $rsProxNumDoc->Eof() )
            $inNumDoc = 1;
        else
            $inNumDoc = $rsProxNumDoc->getCampo("numdoc") + 1;

        // $sessao->transf4["dados"][0] //neste caso soh existe um documento para impressao
        unset( $arDados );
        $arDados = array();
        $arDados[0] = $inNumDoc."§".Sessao::getExercicio()."§".$arTipoDoc[0]."§".$arTipoDoc[2]; //[num_documento]§[exercicio]§[cod_documento]§[cod_tipo_documento]
        Sessao::write( 'dados', $arDados );

        $obTARRDocumentoEmissao->setDado( "num_documento", $inNumDoc );
        $obTARRDocumentoEmissao->setDado( "cod_documento", $arTipoDoc[0] );
        $obTARRDocumentoEmissao->setDado( "exercicio", Sessao::getExercicio() );
        $obTARRDocumentoEmissao->setDado( "numcgm", Sessao::read('numCgm') );
        $obTARRDocumentoEmissao->inclusao();

        if ($_REQUEST["inCGM"]) {
            $obTARRDocumentoCGM->setDado( "numcgm", $_REQUEST["inCGM"] );
            $obTARRDocumentoCGM->setDado( "cod_documento", $arTipoDoc[0] );
            $obTARRDocumentoCGM->setDado( "num_documento", $inNumDoc );
            $obTARRDocumentoCGM->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRDocumentoCGM->inclusao();
        }

        if ($_REQUEST["inCodImovel"]) {
            $obTARRDocumentoImovel->setDado( "inscricao_municipal", $_REQUEST["inCodImovel"] );
            $obTARRDocumentoImovel->setDado( "cod_documento", $arTipoDoc[0] );
            $obTARRDocumentoImovel->setDado( "num_documento", $inNumDoc );
            $obTARRDocumentoImovel->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRDocumentoImovel->inclusao();
        }

        if ($_REQUEST["inInscricaoEconomica"]) {
            $obTARRDocumentoEmpresa->setDado( "inscricao_economica", $_REQUEST["inInscricaoEconomica"] );
            $obTARRDocumentoEmpresa->setDado( "cod_documento", $arTipoDoc[0] );
            $obTARRDocumentoEmpresa->setDado( "num_documento", $inNumDoc );
            $obTARRDocumentoEmpresa->setDado( "exercicio", Sessao::getExercicio() );
            $obTARRDocumentoEmpresa->inclusao();
        }

        if ($inTotalLancamentos) { //eh positivo
            for ($inX=0; $inX<$inTotalLancamentos; $inX++) {
                if ( preg_match( "^/^", $arDadosFinais[$inX]["cod_lancamento"] ) ) { //eh divida ativa
                    $arNumeracao = explode( "/", $arDadosFinais[$inX]["cod_lancamento"] );
                    $boFARRRelatorioExtratoDebitos->recuperaListaDeParcelasDivida( $rsListaParcelas, $arNumeracao[0], $arNumeracao[1] );
                } else { //lancamento normal
                    $boFARRRelatorioExtratoDebitos->recuperaListaDeParcelasLancamento( $rsListaParcelas, $arDadosFinais[$inX]["cod_lancamento"] );
                }

                while ( !$rsListaParcelas->Eof() ) {
                    $obTARRParcelaDocumento->setDado( "cod_parcela", $rsListaParcelas->getCampo("cod_parcela") );
                    $obTARRParcelaDocumento->setDado( "cod_documento", $arTipoDoc[0] );
                    $obTARRParcelaDocumento->setDado( "exercicio", Sessao::getExercicio() );
                    $obTARRParcelaDocumento->setDado( "num_documento", $inNumDoc );

                    if ( $rsListaParcelas->getCampo("vencimento") < date("Y-m-d") ) // ta aqui
                        $obTARRParcelaDocumento->setDado( "cod_situacao", 2 );
                    else
                        $obTARRParcelaDocumento->setDado( "cod_situacao", 1 );

                  $obTARRParcelaDocumento->inclusao();

                    $rsListaParcelas->proximo();
                 }
            }
        }

       Sessao::encerraExcecao();
       sistemaLegado::alertaAviso($pgForm, "Certidao: ".$inNumDoc,"incluir","aviso", Sessao::getId(), "../");
       break;
}

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
  * Página de Processamento
  * Data de criação : 13/04/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

    * $Id: PREmitirCarnes.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

  Caso de uso: uc-05.04.04
**/

/*
$Log$
Revision 1.4  2007/08/16 12:51:34  cercato
correcao de emissao das parcelas unicas.

Revision 1.3  2007/08/02 21:02:09  cercato
adicionando observacao.

Revision 1.2  2007/04/20 20:00:48  cercato
correcao para utilizar a funcao de gerar numeracao da divida.

Revision 1.1  2007/04/16 18:11:29  cercato
adicionando funcoes para emitir carne pela divida.

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarneConsolidacao.class.php");
include_once (CAM_GT_ARR_FUNCAO."FNumeracaoConsolidacao.class.php");
include_once (CAM_GT_ARR_FUNCAO."FNumeracaoFebraban.class.php");

//Define o nome dos arquivos PHP
$stPrograma    = "EmitirCarnes";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php";
$pgJs          = "JS".$stPrograma.".js";

$obErro = new Erro;
$obRARRCarne = new RARRCarne;
$arEmissao = array();
$inNumCarnes = 0;
$inCE = 0;

;

$stUltimaChave = "";
$boPrimeiro = true; $ii = 0;
$request = $_REQUEST;

foreach ($request as $valor => $key) {
    if ( preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {
        $arKey = explode('§',$key);
        $stMd5 = $arKey[13];
        if ($boPrimeiro === true) {
            $stUltimaChave = $stMd5;
            $boPrimeiro = false;
        } else {
            if ( $stMd5 !== $stUltimaChave ) $ii++;
        }
    }
}

$arArqMod = explode( "§", $_REQUEST["cmbModelo"] );
$stArquivoModelo = $arArqMod[0];
$inCodModelo = $arArqMod[1];

if ($ii < 0) {
    $obErro->setDescricao('Dever ser selecionado somente parcelas com o mesmo vinculo!');
} else {
    $inNumCarnes = 0;
    $idVinculo   = false;
    $arNaoImpressas = array();

    include_once( CAM_GT_ARR_MAPEAMENTO."TARRLancamento.class.php" );
    $obTARRLancamento = new TARRLancamento;

    if ( !$obErro->ocorreu() ) {
        foreach ($_POST as $valor => $key) {
            if ( preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {

                $inNumCarnes++;
                $arKey = explode('§',$key);

                $inLancamento       = $arKey[0];
                $inParcela          = $arKey[1];
                $inCodConvenio      = -1;//$arKey[2];
                $inCodCarteira      = $arKey[3];
                $stExercicio        = $arKey[4];
                $inCodConvenioAtual = -1;//$arKey[5];
                $inCodCarteiraAtual = $arKey[6];
                $numeracao          = $arKey[7];
                $dtVencimento       = $arKey[8];
                $flValorAnterior    = str_replace(',','.',str_replace('.','',$arKey[9]));
                $stInfoParcela      = $arKey[10];
                $inNumCgm           = $arKey[11];
                $boImpresso         = $arKey[12];
                $idVinculo          = $arKey[14];
                $inInscricao        = $arKey[15];

                $md5 = $arKey[13];

                $hoje = date('Ymd');
                $arTmp = explode('/',$dtVencimento);
                $stData = $arTmp[2].$arTmp[1].$arTmp[0];

                $stFiltro = " WHERE cod_lancamento = ".$inLancamento;
                $obTARRLancamento->recuperaTodos( $rsListaLancamento, $stFiltro );
                if ( !$rsListaLancamento->Eof() ) {
                    $obTARRLancamento->setDado( "vencimento", $rsListaLancamento->getCampo("vencimento") );
                    $obTARRLancamento->setDado( "total_parcelas", $rsListaLancamento->getCampo("total_parcelas") );
                    $obTARRLancamento->setDado( "divida", $rsListaLancamento->getCampo("divida") );
                    $obTARRLancamento->setDado( "valor", $rsListaLancamento->getCampo("valor") );
                    $obTARRLancamento->setDado( "ativo", $rsListaLancamento->getCampo("ativo") );
                    $obTARRLancamento->setDado( "cod_lancamento", $rsListaLancamento->getCampo("cod_lancamento") );
                    $obTARRLancamento->setDado( "observacao", $_REQUEST["stObservacao"] );
                    $obTARRLancamento->alteracao();
                }

                if ( ( $boImpresso == 't' && $hoje <= $stData ) || ( $hoje > $stData ) ) {
                    if (!$inCodConvenioAtual || $inCodConvenioAtual == "") {
                        $obErro->setDescricao ( "Convênio Atual dos Créditos dos Carnês marcados para reemissão não informado." );
                    }

                    if (( $stInfoParcela == "Única" )||( $stInfoParcela == "única" )) {
                        $nrParcela = "0";
                    } else {
                        $arParcela = explode( "/" , $stInfoParcela );
                        $nrParcela = $arParcela[0];
                    }

                    //pegar novo vencimento ********************************************************
                    $arTmp = explode("_",$valor);
                    $stTmp = "dtNovoVencimento_".$arTmp[1];
                    if ($_REQUEST[$stTmp])
                        $dtNovoVencimento = $_REQUEST[$stTmp];
                    else
                        $dtNovoVencimento = $dtVencimento;

                    // data br para int
                    $arTmp = explode('/',$dtNovoVencimento);
                    $dtNovoVencimentoUs = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                    // aplica juro e multa caso necessario *****************************************
                    include_once(CAM_GT_ARR_MAPEAMENTO."Faplica_juro.class.php");
                    include_once(CAM_GT_ARR_MAPEAMENTO."Faplica_multa.class.php");
                    include_once(CAM_GT_ARR_MAPEAMENTO."Ftotal_parcelas.class.php");

                    $obJuro         = new Faplica_juro   ;
                    $obMulta        = new Faplica_multa  ;
                    $obTotalParcela = new Ftotal_parcelas;

                    $obErro          = $obTotalParcela->executaFuncao($rsTmp, $inLancamento);
                    $inTotalParcelas = $rsTmp->getCampo('valor'); // total de parcelas

                    $stParametros   = '\''.$numeracao.'\','.$stExercicio.','.$inParcela.',\''.$dtNovoVencimentoUs.'\'';
                    $obErro         = $obJuro->executaFuncao($rsTmp, $stParametros) ;
                    if ($nrParcela == 0) {
                        $flJuros        = round($rsTmp->getCampo('valor'),2) / 1; // valor dos juros
                    } else {
                        $flJuros        = round($rsTmp->getCampo('valor'),2) / $inTotalParcelas; // valor dos juros
                    }
                    $obErro         = $obMulta->executaFuncao($rsTmp, $stParametros) ;
                    $flMulta        = round($rsTmp->getCampo('valor'),2); // valor da multa

                    $flValor = round($flValorAnterior + $flJuros + $flMulta,2);

                    // *****************************************************************************

                    $stFNumeracaoMap = "../../classes/funcao/FNumeracaoDivida.class.php";
                    $stFNumeracao = "FNumeracaoDivida";
                    include_once ( $stFNumeracaoMap );
                    $obFNumeracao = new $stFNumeracao;

                    if (!$inCodCarteiraAtual) {
                        $inCodCarteiraAtual = '0';
                    }

                    $stParametros = '-1';
                    $obRARRCarne->setExercicio( $stExercicio );
                    $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);

                    if ( !$obErro->ocorreu() ) {
                        $inNumeracao = $rsRetorno->getCampo( "valor" );
                        $obRARRCarne->setNumeracao( $inNumeracao );
                        $obRARRCarne->setExercicio( $stExercicio );

                        $obRARRCarne->inCodContribuinteInicial= $inNumCgm;
                        $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );
                        $obRARRCarne->obRARRParcela->setVencimento( $dtNovoVencimento );
                        $obRARRCarne->obRARRParcela->setNrParcela( $nrParcela);
                        $obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenioAtual);
                        $obRARRCarne->obRMONCarteira->setCodigoCarteira( $inCodCarteiraAtual );
                        $arReemissao = array( "cod_convenio"   => $inCodConvenio,
                                            "cod_carteira"   => $inCodCarteira,
                                            "cod_lancamento" => $inLancamento,
                                            "info_parcela"   => $nrParcela,
                                            "cod_parcela"    => $inParcela,
                                            "vencimento"     => $dtVencimento,
                                            "valor_anterior" => $flValorAnterior,
                                            "valor"          => $flValor,
                                            "numeracao"      => $numeracao,
                                            "numcgm"         => $inNumCgm );

                        $obErro = $obRARRCarne->efetuaReemitirCarne( $arReemissao, $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                        // adicionar para impressao
                        $arEmissao[$inLancamento][] = array (
                            "cod_parcela" => $inParcela,
                            "exercicio"   => $stExercicio,
                            "numcgm"      => $inNumCgm,
                            "numeracao"   => $numeracao,
                            "inscricao"   => $inInscricao,
                            "cod_modelo"  => $inCodModelo
                        );
                    }
                } else { // NAO IMPRESSO // fim if impresso
                    $arEmissao[$inLancamento][] = array (
                        "cod_parcela" => $inParcela,
                        "exercicio"   => $stExercicio,
                        "numcgm"      => $inNumCgm,
                        "numeracao"   => $numeracao,
                        "inscricao"   => $inInscricao,
                        "cod_modelo"  => $inCodModelo
                    );

                    $arNaoImpressas[] = array (
                        "numeracao"     => $numeracao,
                        "cod_convenio"  => $inCodConvenio,
                        "cod_parcela"   => $inParcela,
                        "exercicio"     => $stExercicio,
                        "inscricao"     => $inInscricao
                    );
                }

                $inNumCarnes++;
            }
        }// fim do foreach
    }//fim do verifica $obErro
}

    // imprimir carnes
    if ( !$obErro->ocorreu() ) {

        $rsEmissaoCarne = new Recordset;
        $rsEmissaoCarne->preenche($arNaoImpressas);
        $boExec = TRUE;

        /**
        *   grava nome pdf e parametro para salvar em disco
        *   usado tambem no objeto pdf
        */

        Sessao::write('stNomPdf', ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf");
        Sessao::write('stParamPdf', 'F');

    // merge fernando-dibueno
        if (!$stArquivoModelo) {
            sistemaLegado::exibeAviso("Nenhum modelo foi configurado para a Origem: '".$idVinculo."'.", "n_erro", "erro");
            exit;
        } else {
            $arTmp = explode( ".", $stArquivoModelo );
            $stObjModelo = $arTmp[0];
        }

        include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

        $obRModeloCarne = new $stObjModelo( $arEmissao );

        if ($boConsolidacao) {
            $obRModeloCarne->setConsolidacao ( true );
            $obRModeloCarne->setNumeracaoConsolidacao ( $numeracaoConsolidacao );
            $obRModeloCarne->setVencimentoConsolidacao ( $dtVencimentoConsolidacao );
        }

        $obRModeloCarne->imprimirCarne();

        //merge fernando-dibueno

        include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
        include_once (CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php");
        $rsEmissaoCarne->setPrimeiroElemento();
        while ( !$rsEmissaoCarne->eof() ) {
            $obTARRCarne = new TARRCarne;
            $obTARRCarne->setDado ( "numeracao"     , $rsEmissaoCarne->getCampo('numeracao')        );
            $obTARRCarne->setDado ( "cod_convenio"  , $rsEmissaoCarne->getCampo('cod_convenio')     );
            $obTARRCarne->setDado ( "cod_parcela"   , $rsEmissaoCarne->getCampo('cod_parcela')      );
            $obTARRCarne->setDado ( "exercicio"     , $rsEmissaoCarne->getCampo('exercicio')        );
            $obTARRCarne->setDado ( "impresso"      , TRUE                                          );
            $obErro = $obTARRCarne->alteracao();

            $rsEmissaoCarne->proximo();
        }
    }

    include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

    if (!$obErro->ocorreu() ) {
        $inNumCarnes = $inNumCarnes/2;
        SistemaLegado::alertaAviso($pgList."?stAcao=reemitir","$inNumCarnes Carnês Emitidos","reemitir","aviso", Sessao::getId(), "../");
    } else {
        SistemaLegado::alertaAviso($pgList."?stAcao=reemitir",urlencode($obErro->getDescricao()),"n_incluir","erro",Sessao::getId(), "../");
    }

    if (!$obErro->ocorreu() &&  $boExec ) {
        echo "<script type=\"text/javascript\">\r\n";
        echo "    var sAux = window.open('OCImpressaoPDFEmissao.php?".Sessao::getId()."','','width=20,height=10,resizable=1,scrollbars=1,left=100,top=100');\r\n";
        echo "    eval(sAux)\r\n";
        echo "</script>\r\n";
    }

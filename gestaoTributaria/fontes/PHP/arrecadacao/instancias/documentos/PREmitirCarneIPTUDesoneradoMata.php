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
  * Data de criação : 07/11/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * $Id: PREmitirCarne.php 45894 2011-10-20 17:37:01Z davi.aroldi $

  Caso de uso: uc-05.03.11

  **/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GT_ARR_NEGOCIO."RARRCarne.class.php";
include_once CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONBanco.class.php";
include_once CAM_GT_MON_NEGOCIO."RMONCredito.class.php";
include_once CAM_GT_ARR_MAPEAMENTO."TARRCarneConsolidacao.class.php";
include_once CAM_GT_ARR_FUNCAO."FNumeracaoConsolidacao.class.php";
include_once CAM_GA_CGM_NEGOCIO."RCGM.class.php";
include_once CAM_GT_ARR_CLASSES."boletos/RRelatorioCarnePetropolis.class.php";

$stAcao = $request->get('stAcao');

//Define o nome dos arquivos PHP
$stPrograma = "EmitirCarneIPTUDesoneradoMata";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

$obErro = new Erro;
$obRARRCarne = new RARRCarne;
$arEmissao = array();
$inNumCarnes = 0;
$inCE = 0;

//SistemaLegado::debugRequest();
switch ($stAcao) {

    case "reemitir":
        // verificar grupos
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

        if ($ii < 0) {
            $obErro->setDescricao('Dever ser selecionado somente parcelas com o mesmo vinculo!');
        } else {

            $inNumCarnes = 0;
            $idVinculo   = false;
            $arNaoImpressas = array();

            if ( !$obErro->ocorreu() ) {
            $arVinculo = Sessao::read( 'vinculo' );
            foreach ($_POST as $valor => $key) {
                if ( preg_match("/cmbModelo_[0-9]/", $valor) ) {
                    $arDados = explode( "_", $valor );
                    $arArqMod = explode( "§", $key );
                    $arVinculo[$arDados[1]-1]["arquivo"] = $arArqMod[0];
                    $arVinculo[$arDados[1]-1]["modelo"] = $arArqMod[1];
                }
            }

            Sessao::write( 'vinculo', $arVinculo );

            foreach ($_POST as $valor => $key) {

                if ( preg_match("/^[a-z]boReemitir_[0-9]/",$valor) ) {
                    $inNumCarnes++;
                    $arKey = explode('§',$key);

                    $inLancamento       = $arKey[0];
                    $inParcela          = $arKey[1];
                    $inCodConvenio      = $arKey[2];
                    $inCodCarteira      = $arKey[3];
                    $stExercicio        = $arKey[4];
                    $inCodConvenioAtual = $arKey[5];
                    $inCodCarteiraAtual = $arKey[6];
                    $numeracao          = $arKey[7];
                    $dtVencimento       = $arKey[8];
                    $flValorAnterior    = str_replace(',','.',str_replace('.','',$arKey[9]));
                    $stInfoParcela      = $arKey[10];
                    $inNumCgm           = $arKey[11];
                    $boImpresso         = $arKey[12];
                    $idVinculo          = $arKey[14];
                    $inInscricao        = $arKey[15];

                    $arNumCgm = explode('/', $inNumCgm);

                    foreach ($arNumCgm as $key => $inCodCgm) {

                        $obRCGM = new RCGM;
                        $obRCGM->setNumCGM     ( $inCodCgm );
                        $obRCGM->listar( $rsCGM , $boTransacao="" );

                        $obRARRConfiguracao = new RARRConfiguracao;
                        $obRARRConfiguracao->setCodModulo("");
                        $obRARRConfiguracao->setExercicio( Sessao::getExercicio() );
                        $obRARRConfiguracao->consultar();
                        $stEmitirCarne  = $obRARRConfiguracao->getEmissaoCarne();

                        if ( ($rsCGM->getCampo( "cpf") == "") AND ($rsCGM->getCampo( "cnpj") == "") AND ( $stEmitirCarne == "naoemitir")) {
                            sistemaLegado::exibeAviso("CGM ".$inCodCgm." não possui dados preenchidos referente ao seu CPF/CNPJ! Emissão cancelada.", "n_erro", "erro");
                            exit;
                        }
                    }

                    $md5 = $arKey[13];

                    $hoje = date('Ymd');
                    $arTmp = explode('/',$dtVencimento);
                    $stData = $arTmp[2].$arTmp[1].$arTmp[0];

                    if ( ( $boImpresso == 't' && $hoje <= $stData ) || ( $hoje > $stData ) ) {

                        $nrParcela = "0";

                        //pegar novo vencimento ********************************************************
                        $arTmp = explode("_",$valor);
                        $stTmp = "dtNovoVencimento_".$arTmp[1];

                        if ($_REQUEST[$stTmp] && preg_macth("/vboReemitir_[0-9]/",$valor) ) {
                            $dtNovoVencimento = $_REQUEST[$stTmp];
                        } else {
                            $dtNovoVencimento = $dtVencimento;
                        }

                        // data br para int
                        $arTmp = explode('/',$dtNovoVencimento);
                        $dtNovoVencimentoUs = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                        $flValor = $flValorAnterior;

                        // *****************************************************************************
                        $obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenioAtual );
                        $obRARRCarne->obRMONCarteira->setCodigoCarteira( $inCodCarteiraAtual );
                        $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );

                        $obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco );
                        $obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                        $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( $rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                        $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
                        $obRARRCarne->obRMONConvenio->obRFuncao->consultar();

                        if ($inCodConvenio == -1) {
                            $inCodConvenioAtual = $inCodConvenio;
                            $stFNumeracaoMap = CAM_GT_DAT_FUNCAO."FNumeracaoDivida.class.php";
                            $stFNumeracao = "FNumeracaoDivida";
                            include_once ( $stFNumeracaoMap );
                            $obFNumeracao = new $stFNumeracao;

                            if (!$inCodCarteiraAtual) {
                                $inCodCarteiraAtual = '0';
                            }

                            $stParametros = '-1';
                            $obRARRCarne->setExercicio( $stExercicio );
                            $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                        } else {
                            $stFNumeracao = "F".$obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();
                            $stFNumeracaoMap = "../../classes/funcao/".$stFNumeracao.".class.php";
                            include_once ( $stFNumeracaoMap );
                            $obFNumeracao = new $stFNumeracao;

                            if (!$inCodCarteiraAtual) {
                                $inCodCarteiraAtual = '0';
                            }

                            $stParametros = "'".$inCodCarteiraAtual."','".$inCodConvenioAtual."'";

                            $obRARRCarne->setExercicio( $stExercicio );
                            $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                        }

                        if ( !$obErro->ocorreu() ) {
                            $inNumeracao = $rsRetorno->getCampo( "valor" );
                            $obRARRCarne->setNumeracao( $inNumeracao );
                            $obRARRCarne->setExercicio( $stExercicio );

                            $obRARRCarne->stCodContribuinteConjunto= str_replace('/', ',', $inNumCgm);
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
                                                  "numcgm"         => $inNumCgm
                                                );

                            // adicionar para impressao
                            $arEmissao[$inLancamento][]= array(
                                "cod_parcela" => $inParcela,
                                "exercicio"   => $stExercicio,
                                "numcgm"      => $inNumCgm,
                                "numeracao"     => $numeracao,
                                "inscricao"     => $inInscricao,
                                "cod_lancamento" => $inLancamento,
                            );
                        }

                    } else { // NAO IMPRESSO // fim if impresso

                        $arEmissao[$inLancamento][] = array(
                            "cod_parcela" => $inParcela,
                            "exercicio"   => $stExercicio,
                            "numcgm"      => $inNumCgm,
                            "numeracao"     => $numeracao,
                            "inscricao"     => $inInscricao,
                            "cod_lancamento" => $inLancamento,
                        );

                        $arNaoImpressas[] = array(
                            "numeracao"     => $numeracao,
                            "cod_convenio"  => $inCodConvenio,
                            "cod_parcela"   => $inParcela,
                            "exercicio"     => $stExercicio,
                            "inscricao"     => $inInscricao,
                            "cod_lancamento" => $inLancamento,
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

        $stNomPdf = ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf";
        Sessao::write( 'stNomPdf', $stNomPdf );
        Sessao::write( 'stParamPdf', "F" );

        $inQtdVinculo = Sessao::read( 'qtd_vinculo' );
        $arVinculo = Sessao::read( 'vinculo' );
        for ($inX=0; $inX<$inQtdVinculo; $inX++) {
            if ($arVinculo[$inX]["vinculo"] == $idVinculo) {
                $stArquivoModelo = $arVinculo[$inX]["arquivo"];
                $inCodModeloArquivo = $arVinculo[$inX]["modelo"];
                $arTmp = explode( ".", $stArquivoModelo );
                $stObjModelo = $arTmp[0];
                $boEncontrouModelo = true;
                break;
            }
        }

        if (!$stArquivoModelo) {
            sistemaLegado::exibeAviso("Nenhum modelo foi configurado para a Origem: '".$idVinculo."'.", "n_erro", "erro");
            exit;
        }

        foreach ($arEmissao as $valor => $chave) {
            $arEmissao[$valor][0]["cod_modelo"] = $inCodModeloArquivo;
        }

        include_once( CAM_GT_ARR_CLASSES."boletos/".$stArquivoModelo );

        $obRModeloCarne = new $stObjModelo( $arEmissao );
        $obRModeloCarne->imprimirCarneDesonerado();

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
    break;
}

?>

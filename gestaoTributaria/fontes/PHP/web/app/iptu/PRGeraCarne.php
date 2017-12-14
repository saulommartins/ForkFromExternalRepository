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
include_once '../../../../../../web/IniciaSessao.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';
include_once '../../../../../../gestaoTributaria/fontes/PHP/arrecadacao/classes/negocio/RARRCarne.class.php';
include_once( CAM_GT_ARR_CLASSES."boletos/RRelatorioCarnePetropolis.class.php" );
include_once( CAM_FW_INCLUDE."IMapeamentoFuncaoGerador.class.php" );

$obErro = new Erro;
$obRARRCarne = new RARRCarne;

 $inNumCarnes = 0;

                $inNumCarnes++;

                $arKey = explode('-',$key);
                $inLancamento       = $_REQUEST['cod_lancamento'];
                $inParcela          = $_REQUEST['cod_parcela'];
                $inCodConvenio      = $_REQUEST['cod_convenio'];
                $inCodCarteira      = $_REQUEST['cod_carteira'];
                $stExercicio        = $_REQUEST['exercicio'];
                $inCodConvenioAtual = $_REQUEST['convenio_atual'];
                $inCodCarteiraAtual = $_REQUEST['carteira_atual'];
                $numeracao          = $_REQUEST['numeracao'];
                $dtVencimento       = $_REQUEST['vencimento'];
                $flValorAnterior    = str_replace(',','.',$_REQUEST['valor']);
                $stInfoParcela      = $_REQUEST['info_parcela'];
                $inNumCgm           = $_REQUEST['numcgm'];

                if ($stInfoParcela == "Única") {
                    $nrParcela = "0";
                } else {
                    $arParcela = explode( "/" , $stInfoParcela );
                    $nrParcela = $arParcela[0];
                }

                // verifica se esta vencida
                $arTmpV     = explode("/",$dtVencimento);
                $stTmpVenc  = $arTmpV[2].$arTmpV[1].$arTmpV[0];
                $stHoje     = date(Ymd);
                if ($stHoje <= $stTmpVenc) { // a vencer
                    $dtNovoVencimento = $dtVencimento;
                    $arTmp = explode('/',$dtNovoVencimento);
                    $dtNovoVencimentoUs = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];
                } else { // vencida  nao web é sempre ultimo dia do mes
                    $dataVenc = SistemaLegado::ultimaDiaUtilMes();
                    $convert = explode('-',$dataVenc);
                    $dtNovoVencimento   = $convert[2]."/".$convert[1]."/".$convert[0];
                    $dtNovoVencimentoUs = $dataVenc;
                }

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
                $flJuros        = round($rsTmp->getCampo('valor'),2) / $inTotalParcelas; // valor dos juros

                $obErro         = $obMulta->executaFuncao($rsTmp, $stParametros) ;
                $flMulta        = round($rsTmp->getCampo('valor'),2); // valor da multa

                $flValor = round($flValorAnterior + $flJuros + $flMulta,2);

                // *****************************************************************************
                $obRARRCarne->obRMONConvenio->setCodigoConvenio( $inCodConvenioAtual );
                $obRARRCarne->obRMONCarteira->setCodigoCarteira( $inCodCarteiraAtual );
                $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );

                $obRARRCarne->obRMONConvenio->listarConvenioBanco( $rsConvenioBanco );
                $obRARRCarne->obRMONConvenio->obRFuncao->setCodFuncao( $rsConvenioBanco->getCampo( "cod_funcao" ) );
                $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->setCodigoBiblioteca( $rsConvenioBanco->getCampo( "cod_biblioteca" ) );
                $obRARRCarne->obRMONConvenio->obRFuncao->obRBiblioteca->roRModulo->setCodModulo(25);
                $obRARRCarne->obRMONConvenio->obRFuncao->consultar();

                $stFNumeracao = "F".$obRARRCarne->obRMONConvenio->obRFuncao->getNomeFuncao();

                $obFNumeracao = new $stFNumeracao;

                if (!$inCodCarteiraAtual) {
                    $inCodCarteiraAtual = '0';
                }
                $stParametros = "'".$inCodCarteiraAtual."','".$inCodConvenioAtual."'";

                $obRARRCarne->setExercicio( $stExercicio );
                $obErro = $obFNumeracao->executaFuncao($rsRetorno,$stParametros,$boTransacao);
                if ( !$obErro->ocorreu() ) {
                    $inNumeracao = $rsRetorno->getCampo( "valor" );
                    $obRARRCarne->setNumeracao( $inNumeracao );
                    $obRARRCarne->setExercicio( $stExercicio );

                    $obRARRCarne->inCodContribuinteInicial= $inNumCgm;
                    $obRARRCarne->obRARRParcela->setCodParcela( $inParcela );
                    $obRARRCarne->obRARRParcela->setVencimento( $dtNovoVencimento);
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
                                          "numcgm"         => $inNumCgm);
                    $obErro = $obRARRCarne->efetuaReemitirCarne( $arReemissao,$boTransacao );
                    if ( $obErro->ocorreu() ) {
                        break;
                    }
                }

        if ( !$obErro->ocorreu() ) {
            $arEmissao[$_REQUEST['cod_lancamento']][] = array(
                                                                'cod_parcela' => $_REQUEST['cod_parcela'],
                                                                'exercicio' => $_REQUEST['exercicio'],
                                                                'numcgm' => $_REQUEST['numcgm']
                                                             );
            $obRRelatorioCarnePetropolis = new RRelatorioCarnePetropolis( $arEmissao );
            $obRRelatorioCarnePetropolis->stLocal = "WEB";
            $obErro = $obRRelatorioCarnePetropolis->imprimirCarne();
        } // fim do teste de erro

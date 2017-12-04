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
    * Frame Ocult de Definição de Vencimentos
    * Data de Criação   : 20/05/2005

    * @author Desenvolvedor: Fábio Bertoldi Rodrigues
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * $Id: OCManterVencimentos.php 63839 2015-10-22 18:08:07Z franver $

    * Casos de uso: uc-05.03.03

*/

/*
$Log$
Revision 1.13  2007/09/28 14:27:03  vitor
Ticket#10276#

Revision 1.12  2007/01/23 15:57:54  fabio
Bug #8055#

Revision 1.11  2006/11/23 09:38:14  cercato
Bug #7568#

Revision 1.10  2006/09/15 11:50:32  fabio
corrigidas tags de caso de uso

Revision 1.9  2006/09/15 11:02:23  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_ARR_NEGOCIO."RARRCalendarioFiscal.class.php");

//Define o nome dos arquivos PHP
$stPrograma = "ManterVencimentos";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once ( $pgJS );

function montaListaParcelamento(&$rsListaParcelamento)
{
    if ( !$rsListaParcelamento->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaParcelamento   );
        $obLista->setTitulo                    ( "Lista de Parcelas"    );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Vencimento"   );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Desconto"             );
        $obLista->ultimoCabecalho->setWidth    ( 20                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Vencimento Desconto" );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 15                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( CENTER                 );
        $obLista->ultimoDado->setCampo         ( "dtVencimento"         );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( CENTER                 );
        $obLista->ultimoDado->setCampo         ( "flDesconto"           );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( CENTER                 );
        $obLista->ultimoDado->setCampo         ( "dtVencimentoDesc"     );
        $obLista->commitDado                   (                        );
        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarParcelamento('alterarParcelamento');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinhaPar"   );
        $obLista->commitAcao                   (                        );
        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirParcelamento('excluirParcelamento');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinhaPar"   );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }

    $stJs .= "f.flDescontoParc.value   = '';\n";
    $stJs .= "f.dtVencimentoParc.value = '';\n";
    $stJs .= "f.dtVencimentoDesc.value = '';\n";
    $stJs .= "f.cmbQtdParcelas.value   = '';\n";
    $stJs .= "d.getElementById('lsParcelamento').innerHTML = '".$stHTML."';\n";

    return $stJs;
}

function montaListaVencimento(&$rsListaVencimento)
{
    if ( !$rsListaVencimento->eof() ) {
        $obLista = new Lista;
        $obLista->setRecordSet                 ( $rsListaVencimento     );
        $obLista->setTitulo                    ( "Lista de Vencimentos" );
        $obLista->setMostraPaginacao           ( false                  );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Data de Vencimento"   );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "Desconto"             );
        $obLista->ultimoCabecalho->setWidth    ( 25                     );
        $obLista->commitCabecalho              (                        );
        $obLista->addCabecalho                 (                        );
        $obLista->ultimoCabecalho->addConteudo ( "&nbsp;"               );
        $obLista->ultimoCabecalho->setWidth    ( 5                      );
        $obLista->commitCabecalho              (                        );

        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setAlinhamento   ( CENTER                 );
        $obLista->ultimoDado->setCampo         ( "dtVencimento"         );
        $obLista->commitDado                   (                        );
        $obLista->addDado                      (                        );
        $obLista->ultimoDado->setCampo         ( "flDesconto"           );
        $obLista->commitDado                   (                        );

        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "ALTERAR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:alterarVencimento('alterarVencimento');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"          );
        $obLista->commitAcao                   (                        );
        $obLista->addAcao                      (                        );
        $obLista->ultimaAcao->setAcao          ( "EXCLUIR"              );
        $obLista->ultimaAcao->setFuncao        ( true                   );
        $obLista->ultimaAcao->setLink( "JavaScript:excluirVencimento('excluirVencimento');" );
        $obLista->ultimaAcao->addCampo         ( "1","inLinha"   );
        $obLista->commitAcao                   (                        );

        $obLista->montaHTML                    (                        );
        $stHTML =  $obLista->getHtml           (                        );
        $stHTML = str_replace                  ( "\n","",$stHTML        );
        $stHTML = str_replace                  ( "  ","",$stHTML        );
        $stHTML = str_replace                  ( "'","\\'",$stHTML      );
    } else {
        $stHTML = "&nbsp;";
    }

    $stJs .= "f.flDesconto.value = '';\n";
    $stJs .= "f.dtVencimento.value = '';\n";
    $stJs .= "d.getElementById('lsVencimento').innerHTML = '".$stHTML."';\n";

    return $stJs;
}

function AjustaData($dtVencimento)
{
    $dataOrdenacao = explode ('/', $dtVencimento);

    if ($dataOrdenacao[1] > 12) {
        $dataOrdenacao[1] = 01;
        $dataOrdenacao[2] = $dataOrdenacao[2] + 1;
    }

    $inDiaInicial = $dataOrdenacao[0];
    $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]),
                                     sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
    $inNroDiasMes = date("t", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), 01,
                                     sprintf("%04d", $dataOrdenacao[2])));

    if ($inNroDiasMes <= $inDiaInicial) {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]),
                                         sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inNroDiasMes - 2;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inNroDiasMes - 1;
        } else {
            $dtDiaVencimento = $inNroDiasMes;
        }

    } elseif ( ($inNroDiasMes - 1) == $inDiaInicial ) {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]),
                                         sprintf("%02d", $inNroDiasMes), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inNroDiasMes + 1;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inNroDiasMes - 1;
        } else {
            $dtDiaVencimento = $inNroDiasMes;
        }

    } else {
        $inDiaSemana  = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]),
                                         sprintf("%02d", $inDiaInicial), sprintf("%04d", $dataOrdenacao[2])));
        if ($inDiaSemana == 0) {
            $dtDiaVencimento = $inDiaInicial + 1;
        } elseif ($inDiaSemana == 6) {
            $dtDiaVencimento = $inDiaInicial + 2;
        } else {
            $dtDiaVencimento = $inDiaInicial;
        }
    }

    $newDTVencimento = sprintf("%02d/%02d/%04d", $dtDiaVencimento, $dataOrdenacao[1], $dataOrdenacao[2]);

    return $newDTVencimento;

//    $inDiaSemana = date("w", mktime(0, 0, 0, sprintf("%02d", $dataOrdenacao[1]), sprintf("%02d", $dataOrdenacao[0]), sprintf("%04d", $dataOrdenacao[2])));
//    if ($inDiaSemana == 0 || $inDiaSemana == 6) { //o dia caiu num domingo ou sabado
//        $arDiaMes = array(31,28,31,30,31,30,31,31,30,31,30,31);
//
//        if ( $inDiaSemana == 0 )
//            $inDiasMais = 1;
//        else
//            $inDiasMais = 2;
//
//        for ($inY=0; $inY < $inDiasMais; $inY++) {
//            if ($dataOrdenacao[0]+1 > $arDiaMes[$dataOrdenacao[1]-1]) {
//                $dataOrdenacao[0] = 1;
//                if ($dataOrdenacao[1] <  12)
//                    $dataOrdenacao[1]++;
//                else {
//                    $dataOrdenacao[1] = 1;
//                    $dataOrdenacao[2]++;
//                }
//            }else
//                $dataOrdenacao[0]++;
//        }
//    }
//
//    $stData = sprintf("%02d/%02d/%04d", $dataOrdenacao[0], $dataOrdenacao[1], $dataOrdenacao[2] );
//
//    return $stData;
}

switch ($_REQUEST["stCtrl"]) {
    case "montaVencimento":
        $stMensagem = "";
        $rsVencimento = new RecordSet;

        //VERIFICA SE A DATA NÃO ESTÁ NULA
        if ($_REQUEST['dtVencimento'] == "") {
            $stMensagem = "Erro. Data de vencimento inválida.";
        }

        //VERIFICA SE É DESCONTO EM PERCENTUAL OU EM VALOR ABSOLUTO
        if ($_REQUEST['stFormaDesconto'] == 'per') {
            $boPercentagem = true;
            $arTmp = array( ",","." );
            $flDesconto = $_REQUEST['flDesconto'].'%';
          //  if ((str_replace($arTmp,"",$_REQUEST['flDesconto']) > 10000 ) || (str_replace($arTmp,"",$_REQUEST['flDesconto']) < 001)) {
         //       $stMensagem = "Erro. Desconto deve ser de 0.01% até a 100%";
         //   }

        } elseif ($_REQUEST['stFormaDesconto'] == 'abs') {
            $boPercentagem = false;
            $flDesconto = 'R$'.$_REQUEST['flDesconto'];
        }

        if (!$stMensagem) {
            $arVencimento = array( "dtVencimento"  => $_REQUEST['dtVencimento'],
                                   "flDesconto"    => $flDesconto,
                                   "boPercentagem" => $boPercentagem );

            $arVenc = Sessao::read( 'vencimentos' );
            if ($_POST["inLinha"]) {
                $arVencimento["inLinha"] = $_POST["inLinha"];
                $arVenc[ $_POST["inLinha"] -1 ] = $arVencimento;
            } else {
                $arVencimento["inLinha"] = count( $arVenc ) +1;
                $arVenc[] = $arVencimento;
            }

            Sessao::write( 'vencimentos', $arVenc );
            $rsVencimento->preenche( $arVenc );
            $stJs .= "f.inLinha.value = '';\n";
            $stJs .= montaListaVencimento( $rsVencimento );

        } else {

            $stJs = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";

        }

        sistemaLegado::executaFrameOculto( $stJs );
    break;
    case "excluirVencimento":
        $inLinha = $_GET["inLinha"] ? $_GET["inLinha"] : 0;
        $arNovaListaVencimento = array();
        $inContLinha = 0;
        $arVenc = Sessao::read( 'vencimentos');
        foreach ($arVenc as $inChave => $arVencimento) {
            if ( ($inChave+1) != $inLinha ) {
                $arVencimento["inLinha"] = ++$inContLinha;
                $arNovaListaVencimento[] = $arVencimento;
            }
        }

        Sessao::write( 'vencimentos', $arNovaListaVencimento);
        $rsListaVencimento = new RecordSet;
        $rsListaVencimento->preenche( $arNovaListaVencimento );
        $stJs = montaListaVencimento( $rsListaVencimento );
        sistemaLegado::executaFrameOculto($stJs);
    break;

    case "montaParcelamento":
        $stMensagem = "";
        $rsParcelamento = new RecordSet;
        $boPercentagem = true;

        //VERIFICA SE A DATA NÃO ESTÁ NULA
        if ($_REQUEST['dtVencimentoParc'] == "") {
            $stMensagem = "Erro. Data de vencimento inválida.";
        }

        //VERIFICA SE É PARCELAMENTO EM PERCENTUAL OU EM VALOR ABSOLUTO
        if ($_REQUEST['stFormaParcelamento'] == 'perparc') {
            $boPercentagem = true;
            $arTmp = array( ",","." );
            if (((str_replace($arTmp,"",$_REQUEST['flDescontoParc']) > 10000) || (str_replace($arTmp,"",$_REQUEST['flDescontoParc']) < 001))) {
                //$stMensagem = "Erro. Desconto deve ser de 0.01% até 100%";
                $flDesconto         = '0.00%';
                $stDtVencimentoDesc = '';
            } else {
                $flDesconto = $_REQUEST['flDescontoParc'].'%';
            }

        } elseif ($_REQUEST['stFormaParcelamento'] == 'absparc') {
            $boPercentagem = false;
            $flDesconto = 'R$'.$_REQUEST['flDescontoParc'];
        }
        if (!$stMensagem) {
            $stDtVencimentoParc        = $_REQUEST['dtVencimentoParc'];
            $stDtVencimentoDesc        = $_REQUEST['dtVencimentoDesc'];
            $arDtVencimentoParcOrig    = explode( "/", $stDtVencimentoParc );
            $arDtVencimentoDescOrig    = explode( "/", $stDtVencimentoDesc );
            $diaVencimentoOriginalParc = $arDtVencimentoParcOrig[0];
            $diaVencimentoOriginalDesc = $arDtVencimentoDescOrig[0];

            $arParcel = Sessao::read( 'parcelamentos' );
            if ($_REQUEST['flagEditar'] == "incluir") {
                $arParcelamento = array( );
                for ($inX=0; $inX<$_REQUEST["cmbQtdParcelas"]; $inX++) {

                    $stDtVencimentoParc = AjustaData( $stDtVencimentoParc );

                    if ($stDtVencimentoDesc != '' &&  $stDtVencimentoDesc != '-') {
                        $stDtVencimentoDesc = AjustaData( $stDtVencimentoDesc );
                    } else {
                        $stDtVencimentoDesc = '-';
                    }

                    $arParcelamento = array( "dtVencimento"     => $stDtVencimentoParc,
                                             "dtVencimentoDesc" => $stDtVencimentoDesc,
                                             "flDesconto"       => $flDesconto,
                                             "boPercentagem"    => $boPercentagem );

                    if ($_POST["inLinhaPar"]) {
                        $arParcelamento["inLinhaPar"] = $_POST["inLinhaPar"];
                        $arParcel[$_POST["inLinhaPar"]-1] = $arParcelamento;
                    } else {
                        $arParcelamento["inLinhaPar"] = count( $arParcel ) +1;
                        $arParcel[] = $arParcelamento;
                    }

                    $arDtVencimentoParc = explode( "/", $stDtVencimentoParc );
                    $mesProxParcela     = $arDtVencimentoParc[1] + 1;
                    $stDtVencimentoParc = sprintf("%02d/%02d/%04d", $diaVencimentoOriginalParc, $mesProxParcela, $arDtVencimentoParc[2]);

                    if ($stDtVencimentoDesc != "-") {
                        $arDtVencimentoDesc = explode( "/", $stDtVencimentoDesc );
                        $mesProxDesconto    = $arDtVencimentoDesc[1] + 1;
                        $stDtVencimentoDesc = sprintf("%02d/%02d/%04d", $diaVencimentoOriginalDesc, $mesProxDesconto, $arDtVencimentoDesc[2]);
                    }
                }
            } else {

                if ($_REQUEST["stDtVencimentoDesc"] == "") {
                    $stDtVencimentoDesc = "-";
                }
                $arParcelamento = array( "dtVencimento"     => $stDtVencimentoParc,
                                         "dtVencimentoDesc" => $stDtVencimentoDesc,
                                         "flDesconto"       => $flDesconto,
                                         "boPercentagem"    => $boPercentagem );
                $arParcelamento["inLinhaPar"] = $_POST["inLinhaPar"];
                $arParcel[$_POST["inLinhaPar"]-1] = $arParcelamento;
            }

            Sessao::write( 'parcelamentos', $arParcel );
            $rsParcelamento->preenche( $arParcel );
            $stJs .= "f.inLinhaPar.value = '';\n";
            $stJs .= "f.flagEditar.value = 'incluir';\n";
            $stJs .= montaListaParcelamento( $rsParcelamento );

        } else {
            $stJs = "alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "excluirParcelamento":
        $inLinha = $_GET["inLinhaPar"] ? $_GET["inLinhaPar"] : 0;
        $arNovaListaParcelamento = array();
        $inContLinha = 0;
        $arParcel = Sessao::read( 'parcelamentos' );
        foreach ($arParcel as $inChave => $arParcelamento) {
            if ( ($inChave+1) != $inLinha ) {
                $arParcelamento["inLinhaPar"] = ++$inContLinha;
                $arNovaListaParcelamento[] = $arParcelamento;
            }
        }

        Sessao::write( 'parcelamentos', $arNovaListaParcelamento );
        $rsListaParcelamento = new RecordSet;
        $rsListaParcelamento->preenche( $arNovaListaParcelamento );
        $stJs = montaListaParcelamento( $rsListaParcelamento );
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limparVencimento":
        $stJs .= "f.inLinha.value = '';\n";
        $stJs .= "f.dtVencimento.value = '';\n";
        $stJs .= "f.flDesconto.value = '';\n";
        $stJs .= "f.stFormaDesconto.value = '';\n";
        $stJs .= "d.getElementById('stFormaDescontoPer').checked = true;\n";
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limparParcelamento":
        $stJs .= "f.inLinhaPar.value = '';\n";
        $stJs .= "f.dtVencimentoParc.value = '';\n";
        $stJs .= "f.dtVencimentoDesc.value = '';\n";
        $stJs .= "f.flDescontoParc.value = '';\n";
        $stJs .= "f.stFormaParcelamento.value = '';\n";
        $stJs .= "d.getElementById('stFormaParcelamentoPer').checked = true;\n";
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "limpaVencParc":
        Sessao::write( 'parcelamentos', array() );
        Sessao::write( 'vencimentos', array() );
        $stJs .= "d.getElementById('lsVencimento').innerHTML = '&nbsp;';\n";
        $stJs .= "d.getElementById('lsParcelamento').innerHTML = '&nbsp;';\n";
        sistemaLegado::executaFrameOculto($stJs);
    break;
    case "listaTodos":
        $rsVencimento = new RecordSet;
        $rsParcelamento = new RecordSet;

        $arVenc = Sessao::read( 'vencimentos' );
        $rsVencimento->preenche( $arVenc );
        $stJs .= montaListaVencimento( $rsVencimento );

        $arParcel = Sessao::read( 'parcelamentos' );
        $rsParcelamento->preenche( $arParcel );
        $stJs .= montaListaParcelamento( $rsParcelamento );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "listaParcelamento":
        $rsParcelamento = new RecordSet;
        $arParcel = Sessao::read( 'parcelamentos' );
        $rsParcelamento->preenche( $arParcel );
        $stJs .= montaListaParcelamento( $rsParcelamento );
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "alterarVencimento":
        $arVenc = Sessao::read( 'vencimentos' );
        $arVencimento = $arVenc[$_GET["inLinha"] -1];
        $arTira = array("R$","%");
        $arVai  = array("","");
        $arVencimento["flDesconto"] = str_replace($arTira,$arVai,$arVencimento["flDesconto"]);

        if ($arVencimento) {
            $stJs .= "f.inLinha.value = '".$_GET["inLinha"]."' ;\n";
            $stJs .= "f.dtVencimento.value = '".$arVencimento["dtVencimento"]."' ;\n";
            $stJs .= "f.flDesconto.value = '".$arVencimento["flDesconto"]."' ;\n";
            if ($arVencimento["boPercentagem"] == 't' )
                $stJs .= "d.getElementById('stFormaDescontoPer').checked = true ;\n";
            else
                $stJs .= "d.getElementById('stFormaDescontoAbs').checked = true ;\n";
        } else {
            $stJs = "sistemaLegado::alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "alterarParcelamento":
        $arParcel = Sessao::read( 'parcelamentos' );
        $arParcelamento = $arParcel[$_GET["inLinhaPar"] -1];
        $arTira = array("R$","%");
        $arVai  = array("","");
        $arParcelamento["flDesconto"] = str_replace($arTira,$arVai,$arParcelamento["flDesconto"]);

        if ($arParcelamento) {
            $stJs .= "f.inLinhaPar.value = '".$_GET["inLinhaPar"]."' ;\n";
            $stJs .= "f.flagEditar.value = 'editar';\n";
            $stJs .= "f.dtVencimentoParc.value = '".$arParcelamento["dtVencimento"]."' ;\n";
            $stJs .= "f.flDescontoParc.value = '".$arParcelamento["flDesconto"]."' ;\n";
            $stJs .= "f.dtVencimentoDesc.value = '".$arParcelamento["dtVencimentoDesc"]."' ;\n";
            if ($arParcelamento["boPercentagem"] == 't' )
                $stJs .= "d.getElementById('stFormaParcelamentoPer').checked = true ;\n";
            else
                $stJs .= "d.getElementById('stFormaParcelamentoAbs').checked = true ;\n";
        } else {
            $stJs = "sistemaLegado::alertaAviso('@Valor inválido. ($stMensagem)','form','erro','".Sessao::getId()."');";
        }
        SistemaLegado::executaFrameOculto($stJs);
    break;
}

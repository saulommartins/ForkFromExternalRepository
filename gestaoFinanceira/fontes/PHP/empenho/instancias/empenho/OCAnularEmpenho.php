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
    * Classe Oculta de Empenho
    * Data de Criação   : 05/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor:$
    $Date: 2008-01-04 12:27:45 -0200 (Sex, 04 Jan 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.15
*/

/*
$Log$
Revision 1.9  2007/09/12 13:29:43  luciano
Ticket#10084#

Revision 1.8  2007/01/16 21:48:07  cleisson
Bug #7905#

Revision 1.7  2006/07/17 12:56:52  jose.eduardo
Bug #6546#

Revision 1.6  2006/07/05 20:48:34  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoAutorizacaoEmpenho.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoEmpenho.class.php" );
include_once( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAutorizacao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_GET['stCtrl'] ?  $_GET['stCtrl'] : $_POST['stCtrl'];

$obREmpenhoAutorizacaoEmpenho = new REmpenhoPreEmpenho;
$obREmpenhoAutorizacaoEmpenho->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet , $boExecuta = true)
{
//        $obRContabilidadePlanoBanco->setCodPlano     ( $_POST['inCodPlano']      );
//        $obRContabilidadePlanoBanco->setCodEstrutural( $_POST['stCodEstrutural'] );
//        $obRContabilidadePlanoBanco->obROrcamentoEntidade->obRCGMPessoaFisica->setNumCGM( Sessao::read('numCgm') );
//        $obRContabilidadePlanoBanco->buscaSaldo( $rsLista );
//        $rsLista->addFormatacao( "valor", "NUMERIC_BR" );
        $rsLista = new RecordSet;
        $rsLista->preenche( $arRecordSet );
        $rsLista->addFormatacao( "vl_total", "NUMERIC_BR" );
        $rsLista->addFormatacao( "vl_empenhado_anulado", "NUMERIC_BR" );
        $rsLista->addFormatacao( "vl_liquidado_anulado", "NUMERIC_BR" );
        $rsLista->addFormatacao( "vl_liquidado", "NUMERIC_BR" );
        $obLista = new Lista;
        $obLista->setMostraPaginacao( false );
        $obLista->setRecordSet( $rsLista );
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("&nbsp;");
        $obLista->ultimoCabecalho->setWidth( 5 );
        $obLista->commitCabecalho();
//        $obLista->addCabecalho();
//        $obLista->ultimoCabecalho->addConteudo("Item");
//        $obLista->ultimoCabecalho->setWidth( 10 );
//        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Descrição ");
        $obLista->ultimoCabecalho->setWidth( 35 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Empenhado");
        $obLista->ultimoCabecalho->setWidth( 13 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Anulado");
        $obLista->ultimoCabecalho->setWidth( 13 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("Liquidado");
        $obLista->ultimoCabecalho->setWidth( 13 );
        $obLista->commitCabecalho();
        $obLista->addCabecalho();
        $obLista->ultimoCabecalho->addConteudo("A Anular");
        $obLista->ultimoCabecalho->setWidth( 26 );
        $obLista->commitCabecalho();

//        $obLista->addDado();
//        $obLista->ultimoDado->setCampo( "" );
//        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
//        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "nom_item" );
        $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_total" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_empenhado_anulado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();
        $obLista->addDado();
        $obLista->ultimoDado->setCampo( "vl_liquidado" );
        $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
        $obLista->commitDado();

        $inCount = 1;
        echo "<script>";
        echo 'var f = window.parent.frames["telaPrincipal"].document.frm;';
        echo "f.boValidacao.value = 'var boOk = false;';";
        echo "f.boValidacao.value += 'var f = window.parent.frames[\"telaPrincipal\"].document.frm;';";
        foreach ($arRecordSet as $value) {
//            $vl_total = str_replace('.','',$value['vl_total']);
            $vl_total = str_replace('.','',$value['vl_a_anular']);
            $vl_total = str_replace(',','.',$vl_total);
            $nuVlTotal += $vl_total;
//            $nuVlTotal += $value['vl_total'];
            echo "f.boValidacao.value +=' var nuValor = f.nuValor_".$value['num_item']."_".$inCount.".value.replace( new  RegExp(\"[.]\",\"g\") ,\"\");';";
            echo "f.boValidacao.value +=' nuValor = nuValor.replace( \",\" ,\".\");';";
            echo "f.boValidacao.value += 'if (nuValor > 0 ) boOk = true;';";
            $inCount++;
        }
        echo "f.boValidacao.value += 'if (! boOk) { mensagem +=\"@Pelo menos um item precisa ser anulado.\"; erro = true; }';";
        echo "</script>";
        $nuVlTotal = number_format($nuVlTotal,2,',','.');

        // Define Objeto para Valor
        $obTxtValor = new Moeda;
        $obTxtValor->setName     ( "nuValor_[num_item]_" );
        $obTxtValor->setAlign    ( 'RIGHT');
        $obTxtValor->setTitle    ( "" );
        $obTxtValor->setMaxLength( 19 );
        $obTxtValor->setSize     ( 21 );
        $obTxtValor->setValue    ( "vl_a_anular" );

        $obLista->addDadoComponente( $obTxtValor );
        $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
        $obLista->commitDadoComponente();

        $obLista->montaHTML();
        $stHTML = $obLista->getHTML();
        $stHTML = str_replace( "\n" ,"" ,$stHTML );
        $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
        $stHTML = str_replace( "  " ,"" ,$stHTML );
        $stHTML = str_replace( "'","\\'",$stHTML );
        $stHTML = str_replace( "\\\'","\\'",$stHTML );

        if ($boExecuta) {
            SistemaLegado::executaFrameOculto("d.getElementById('spnLista').innerHTML = '".$stHTML."'; d.getElementById('nuValorTotal').innerHTML='".$nuVlTotal."';");
        } else {
            return $stHTML;
        }

}

switch ($stCtrl) {
    case 'montaListaItemPreEmpenho':
        montaLista( Sessao::read('arItens') );
    break;
    case 'buscaFornecedorDiverso':
        if ($_POST["inCodFornecedor"] != "") {
            $obREmpenhoAutorizacaoEmpenho->obRCGM->setNumCGM( $_POST["inCodFornecedor"] );
            $obREmpenhoAutorizacaoEmpenho->obRCGM->listar( $rsCGM );
            $stNomFornecedor = $rsCGM->getCampo( "nom_cgm" );
            if (!$stNomFornecedor) {
                $js .= 'f.inCodFornecedor.value = "";';
                $js .= 'f.inCodFornecedor.focus();';
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
                $js .= "SistemaLegado::alertaAviso('@Valor inválido. (".$_POST["inCodFornecedor"].")','form','erro','".Sessao::getId()."');";
            } else {
                $js .= 'd.getElementById("stNomFornecedor").innerHTML = "'.$stNomFornecedor.'";';
            }
        } else $js .= 'd.getElementById("stNomFornecedor").innerHTML = "&nbsp;";';
        SistemaLegado::executaFrameOculto($js);
    break;
    case 'verificaDataAnulacaoEmpenho':
    if ($_POST["stDtAnulacao"] != "" and $_REQUEST['inCodEntidade'] != "") {

        $obREmpenhoNotaLiquidacao = new REmpenhoNotaLiquidacao(new REmpenhoEmpenho);
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade']);
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho($_REQUEST['inCodEmpenho']);
        $obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio ($_REQUEST['stDtExercicioEmpenho']);
        $obREmpenhoNotaLiquidacao->listarMaiorDataAnulacaoEmpenho($rsMaiorDataLiquidacao);

        $obREmpenhoEmpenho = new REmpenhoEmpenho;
        $obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade($_REQUEST['inCodEntidade'] );
        $obREmpenhoEmpenho->setExercicio( $_REQUEST['stDtExercicioEmpenho']);
        $obREmpenhoEmpenho->listarMaiorDataAnulacao( $rsMaiorData);

        $stMaiorDataLiquidacaoEmpenho = $rsMaiorDataLiquidacao->getCampo("dataanulacao");
        $stMaiorDataEmpenho = $rsMaiorData->getCampo("dataanulacao");

        if (SistemaLegado::comparaDatas($stMaiorDataLiquidacaoEmpenho, $stMaiorDataEmpenho)) {
            $stMaiorDataAnulacao = $stMaiorDataLiquidacaoEmpenho;
        } else {
            $stMaiorDataAnulacao = $stMaiorDataEmpenho;
        }

        if ( $stMaiorDataAnulacao && strlen($stMaiorDataAnulacao) > 0 ) {
            $anoUltimaAnulacao = (int) substr($stMaiorDataAnulacao, 6, strlen($stMaiorDataAnulacao));
            if ($anoUltimaAnulacao > Sessao::getExercicio()) {
                $stMaiorData = '31/12/' . Sessao::getExercicio();
            } elseif ($anoUltimaAnulacao < Sessao::getExercicio()) {
                $stMaiorData = '01/01/' . Sessao::getExercicio();
            } else {
                $stMaiorData = $stMaiorDataAnulacao;
            }
        } else {
            $stMaiorData = '01/01/'.Sessao::getExercicio();
        }

        if ( SistemaLegado::comparaDatas($stMaiorData, $_POST["stDtAnulacao"]) ) {
            $js .= "f.stDtAnulacao.value = '" . $stMaiorData . "';";
            $js .= 'window.parent.frames["telaPrincipal"].document.frm.stDtAnulacao.focus();';
            $js .= "alertaAviso('@Data de Anulação de Empenho deve ser maior ou igual a " . $stMaiorData . " !','form','erro','" . Sessao::getId() . "');";
        }
    }
    SistemaLegado::executaFrameOculto($js);
    break;

}
?>

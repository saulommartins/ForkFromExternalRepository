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
   /*
    * Arquivo oculto da Anulação de Solicitação
    * Data de Criação   : 04/09/2009

    * @author Analista      Gelson Gonçalves
    * @author Desenvolvedor Alexandre Melo

    * @package URBEM
    * @subpackage

    * @ignore

    $Id:$
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

function montaListaDotacoesAnular($arRecordSetItem)
{
    include_once CAM_FW_COMPONENTES."Table/TableTree.class.php";

    $stPrograma = "ManterAnulacaoSolicitacaoCompra";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsDotacoesItem = new RecordSet;
    $rsDotacoesItem->preenche( $arRecordSetItem );

    $table = new Table;
    $table->setRecordset( $rsDotacoesItem );
    $table->setSummary('Itens da Solicitação');

    $table->Head->addCabecalho('Item'                , 25);
    $table->Head->addCabecalho('Unidade'             ,  8);
    $table->Head->addCabecalho('Centro de Custo'     , 20);
    $table->Head->addCabecalho('Quantidade Pendente' , 10);
    $table->Head->addCabecalho('Quantidade Anular'   , 10);
    $table->Head->addCabecalho('Valor Pendente'      , 10);
    $table->Head->addCabecalho('Valor Anular'        , 10);

    $obQuantidadeTotalAnulada = new Numerico();
    $obQuantidadeTotalAnulada->setName ( "nuQtTotalAnulada");
    $obQuantidadeTotalAnulada->setNull ( false );
    $obQuantidadeTotalAnulada->setDefinicao("NUMERIC");
    $obQuantidadeTotalAnulada->setSize ( 14 );
    $obQuantidadeTotalAnulada->setMaxLength( 13 );
    $obQuantidadeTotalAnulada->setDecimais( 4 );
    $obQuantidadeTotalAnulada->obEvento->setOnBlur ("floatDecimal(this, '4', event ); ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&id='+this.id+'&nuQtTotalAnulada='+this.value, 'validaQntTotalAnulada' );");

    $obValorTotalAnulada = new TextBox;
    $obValorTotalAnulada->setName ( "nuVlTotalAnulado" );
    $obValorTotalAnulada->setNull ( false );
    $obValorTotalAnulada->setDisabled( true );
    $obValorTotalAnulada->setDecimais ( 2 );
    $obValorTotalAnulada->setSize     ( 23 );
    $obValorTotalAnulada->setMaxLength( 21 );
    
    $table->Body->addCampo("[cod_item] - [descricao_resumida]" , "E", "Item");
    $table->Body->addCampo('nom_unidade'             , "E", "Unidade");
    $table->Body->addCampo('descricao'               , "E", "Centro de Custo");
    $table->Body->addCampo('qnt_pendente'            , "D");
    $table->Body->addCampo($obQuantidadeTotalAnulada , "C");
    $table->Body->addCampo('vl_pendente'             , "D");
    $table->Body->addCampo($obValorTotalAnulada      , "C");

    $table->montaHTML(true);
    $stHTML = $table->getHtml();

    return "jQuery('#spnListaItens').html('".$stHTML."'); ";
}

function calculaValores($rsRecordSet)
{
    $inCount = 0;
    $inId    = 1;
    $arDados = array();

    while (!$rsRecordSet->eof()) {
        $nuQntPendente = ($rsRecordSet->getCampo('qnt_dotacao_solicitacao') - $rsRecordSet->getCampo('qnt_dotacao_mapa'));
        $nuVlPendente  = ($rsRecordSet->getCampo('vl_dotacao_solicitacao') - $rsRecordSet->getCampo('vl_dotacao_mapa'));

        if ($nuQntPendente > 0 && $nuVlPendente > 0) {
            $arDados[$inCount]['id']                 = $inId;
            $arDados[$inCount]['cod_item']           = $rsRecordSet->getCampo('cod_item');
            $arDados[$inCount]['descricao_resumida'] = $rsRecordSet->getCampo('descricao_resumida');
            $arDados[$inCount]['nom_unidade']        = $rsRecordSet->getCampo('nom_unidade');
            $arDados[$inCount]['cod_centro']         = $rsRecordSet->getCampo('cod_centro');
            $arDados[$inCount]['descricao']          = $rsRecordSet->getCampo('descricao');
            $arDados[$inCount]['cod_conta']          = $rsRecordSet->getCampo('cod_conta');
            $arDados[$inCount]['cod_despesa']        = $rsRecordSet->getCampo('cod_despesa');
            $arDados[$inCount]['cod_reserva']        = $rsRecordSet->getCampo('cod_reserva');
            $arDados[$inCount]['qnt_pendente']       = number_format( $nuQntPendente , 4, ",",".");
            $arDados[$inCount]['vl_pendente']        = number_format( $nuVlPendente  , 2, ",",".");

            $inId++;
            $inCount++;
        }
        $rsRecordSet->proximo();
    }
    if (count($arDados) > 0) {
        Sessao::write('arItens', $arDados);
        $stJs = montaListaDotacoesAnular($arDados);
    }

    return $stJs;
}

function consultaItens($inCodSolicitacao, $inCodEntidade, $stExercicio)
{
    include_once CAM_GP_COM_MAPEAMENTO."TComprasSolicitacao.class.php";
    $obTComprasSolicitacao = new TComprasSolicitacao;
    $obTComprasSolicitacao->setDado('exercicio'       , $stExercicio      );
    $obTComprasSolicitacao->setDado('cod_entidade'    , $inCodEntidade    );
    $obTComprasSolicitacao->setDado('cod_solicitacao' , $inCodSolicitacao );
    $obTComprasSolicitacao->recuperaSolicitacaoItensAnulacao($rsRecordSet);

    if ($rsRecordSet->getNumLinhas() > 0) {
        $stJs = calculaValores($rsRecordSet);
    }

    return $stJs;
}

function validaQntTotalAnulada($id, $nuQtTotalAnulada)
{
    $arDados = Sessao::read('arItens');

    $id = explode('_',$id);
    $id = $id[1];

    foreach ($arDados as $value) {
        if ($value['id'] == $id) {
            $qnt_pendente     = str_replace(",",".", str_replace(".","", $value['qnt_pendente']));
            $nuQtTotalAnulada = str_replace(",",".", str_replace(".","", $nuQtTotalAnulada));

            if ($qnt_pendente < $nuQtTotalAnulada) {
                $stJs .= "jq('#nuQtTotalAnulada_".$id."').val('');";
                $stJs .= "jq('#nuVlTotalAnulado_".$id."').val('');";
            } else {
                $vl_pendente  = str_replace(",",".",str_replace(".","",$value['vl_pendente']));
                $qnt_pendente = str_replace(",",".",str_replace(".","",$value['qnt_pendente']));

                $nuVlUnitario = $vl_pendente / $qnt_pendente;
                $nuVlAnular   = $nuVlUnitario * $nuQtTotalAnulada;
                $nuVlAnular   = number_format( $nuVlAnular , 2, ",",".");
                $nuQtTotalAnulada = number_format( $nuQtTotalAnulada, 4, ",", "." );

                $stJs .= atualizaValorQuantidadeAnular($id, $nuVlAnular, $nuQtTotalAnulada);
                $stJs .= "jq('#nuVlTotalAnulado_".$id."').val('".$nuVlAnular."');";
            }
        }
    }

    return $stJs;
}

function atualizaValorQuantidadeAnular($id, $nuVlAnular, $nuQtAnular)
{
    $arDados = Sessao::read('arItens');

    foreach ($arDados as $key => $value) {
        if ($value['id'] == $id) {
                $arDados[$key]['vl_anular']  = $nuVlAnular;
                $arDados[$key]['qnt_anular'] = $nuQtAnular;
        }
    }
    Sessao::write('arItens', $arDados);
}

function bloqueiaBtnOk($boBloqueia)
{
    if ($boBloqueia) {
        $stJs .= "jq('#Ok').attr('disabled', 'disabled');";
    } else {
        $stJs .= "jq('#Ok').removeAttr('disabled');";
    }

    return $stJs;
}

function liberaBtnFormulario()
{
    $arDados = Sessao::read('arItens');

    foreach ($arDados as $value) {
        $nuVlAnular = str_replace(",",".",str_replace(".","",$value['vl_anular']));

        if ($nuVlAnular > 0.00) {
            $boLibera = false;
        }
    }
    $stJs .= bloqueiaBtnOk($boLibera);

    return $stJs;
}

switch ($_REQUEST['stCtrl']) {
    case 'carregaLista':
        $inCodSolicitacao = $_REQUEST['stCodSolicitacao'];
        $inCodEntidade    = $_REQUEST['stCodEntidade'];
        $stExercicio      = $_REQUEST['stExercicio'];

        $stJs  = consultaItens($inCodSolicitacao, $inCodEntidade, $stExercicio);
//        $stJs .= bloqueiaBtnOk(true);
    break;

    case 'validaQntTotalAnulada':
        $id = $_REQUEST['id'];
        $nuQtTotalAnulada = $_REQUEST['nuQtTotalAnulada'];

        if ($id && $nuQtTotalAnulada) {
            $stJs  = validaQntTotalAnulada($id, $nuQtTotalAnulada);
//            $stJs .= liberaBtnFormulario();
        }
    break;
}

if (!empty($stJs)) {
    echo $stJs;
}

?>

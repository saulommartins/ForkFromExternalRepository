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
    * Página de
    * Data de criação : 13/03/2006

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    Caso de uso: uc-03.02.17

    $Id: OCRelatorioManutencao.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                   );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaRelatorioControleIndividual.class.php"                    );

function consultaDadosVeiculo($obRegra)
{
    $obRegra->listarDadosVeiculo($rsLista,$stOrder,$boTransacao);

    if ( $rsLista->getNumLinhas() > 0 ) {
        $stJs  = "f.inCodVeiculo.value = ".$rsLista->getCampo("cod_veiculo").";\n";
        if ($rsLista->getCampo("placa") != null)
            $stJs .= "f.stPlaca.value = '".substr($rsLista->getCampo("placa"),0,3)."-".substr($rsLista->getCampo("placa"),-4)."';\n";
        $stJs .= "f.stPrefixo.value = '".$rsLista->getCampo("prefixo")."';\n";
        $stJs .= "f.stMarca.value = '".$rsLista->getCampo("marca")."';\n";
        $stJs .= "f.stModelo.value = '".$rsLista->getCampo("modelo")."';\n";
        $stJs .= "f.stTipoVeiculo.value = '".$rsLista->getCampo("tipo")."';\n";
        SistemaLegado::executaFrameOculto( $stJs );
    } else {
        $stJs  = "f.inCodVeiculo.value = '';";
        $stJs .= "f.stPlaca.value = '';";
        $stJs .= "f.stPrefixo.value = '';";
        $stJs .= "f.stMarca.value = '';";
        $stJs .= "f.stModelo.value = '';";
        $stJs .= "f.stTipoVeiculo.value = '';";
        $stJs .= "alertaAviso('@Nenhum veículo encontrado para este filtro.','form','erro','".Sessao::getId()."');";

        SistemaLegado::executaFrameOculto( $stJs );
    }
}

function preencheTela($campoPesquisa)
{
    if ($_POST[$campoPesquisa]) {
        $obRegra = new RFrotaRelatorioControleIndividual;

        switch ($campoPesquisa) {
            case "inCodVeiculo":
                $obRegra->setCodVeiculo($_POST['inCodVeiculo']);
                consultaDadosVeiculo($obRegra);
            break;
            case "stPlaca":
                $_POST['stPlaca'] = substr($_POST['stPlaca'],0,3).substr($_POST['stPlaca'],-4);
                $obRegra->setPlacaVeiculo($_POST['stPlaca']);
                consultaDadosVeiculo($obRegra);
            break;
            case "stPrefixo":
                $obRegra->setPrefixo($_POST['stPrefixo']);
                consultaDadosVeiculo($obRegra);
            break;

        }
    } else {
        $stJs  = "f.inCodVeiculo.value = '';";
        $stJs .= "f.stPlaca.value = '';";
        $stJs .= "f.stPrefixo.value = '';";
        $stJs .= "f.stMarca.value = '';";
        $stJs .= "f.stModelo.value = '';";
        $stJs .= "f.stTipoVeiculo.value = '';";
        $stJs .= "f.tipo.value = '';";

        SistemaLegado::executaFrameOculto( $stJs );
    }
}

//SistemaLegado::debugRequest();
switch ($_REQUEST['stCtrl']) {

    case "MontaDadosCodVeiculo":
        preencheTela( "inCodVeiculo" );
    break;
    case "MontaDadosPrefixo":
        preencheTela( "stPrefixo" );
    break;
    case "MontaDadosPlaca":
        preencheTela( "stPlaca" );
    break;

    default:
        #sessao->filtro['stPlaca'] = substr($sessao->filtro['stPlaca'],0,3).substr($sessao->filtro['stPlaca'],-4);
        #sessao->filtro['stDataInicial'] = "01/".$sessao->filtro['stMes']."/".Sessao::getExercicio();

        $filtro = Sessao::read('filtro');

        $filtro['stPlaca'] = substr($filtro['stPlaca'],0,3).substr($filtro['stPlaca'],-4);
        $filtro['stDataInicial'] = "01/".$filtro['stMes']."/".Sessao::getExercicio();

        //array com os meses que tem 31 dias
        $arMes = array("01","03","05","07","08","10","12");
        //verifica se o mês é fevereiro
        if ($filtro['stMes'] == '02') {
        //verifica se o ano é bisexto
            $diaFinal = (Sessao::getExercicio() % 4 == 0 ) ? "29" : "28";
        //verifica se o mês tem 31 dias
        } elseif (in_array($filtro['stMes'] , $arMes)) {
            $diaFinal = "31";
        } else {
            $diaFinal = "30";
        }
        $filtro['stDataFinal'] = $diaFinal."/".$filtro['stMes']."/".Sessao::getExercicio();
        //seta as variaveis que serão utilizadas para gerar o relatório
        $obRegra = new RFrotaRelatorioControleIndividual;
        $obRegra->setCodVeiculo   ($filtro["inCodVeiculo"]);
        $obRegra->setPrefixo      ($filtro["stPrefixo"]);
        $obRegra->setPlacaVeiculo ($filtro["stPlaca"]);
        $obRegra->setDataInicial  ($filtro["stDataInicial"]);
        $obRegra->setDataFinal    ($filtro["stDataFinal"]);
        $obRegra->listarDadosVeiculo($rsDadosVeiculo,$stOrder,$boTransacao);
        $obRegra->KmMensal($rsKmLimite,$boTransacao);
        if ($rsKmLimite->getCampo("min") != null) {
            $obRegra->listaDadosControleIndividual($rsDados,$boTransacao);
            $inCount = 0;
            while (!$rsDados->eof()) {
                $arData = explode("-",$rsDados->getCampo('data'));
                $arDados[$inCount]['data'] = $arData[2]."/".$arData[1]."/".$arData[0];
                $arDados[$inCount]['quilometragem'] = $rsDados->getCampo('quilometragem');
                $arDados[$inCount]['descricao'] = $rsDados->getCampo('descricao');
                $arDados[$inCount]['tipo'] = $rsDados->getCampo('tipo');
                $arDados[$inCount]['quantidade'] = $rsDados->getCampo('quantidade');
                $arDados[$inCount]['valor'] = $rsDados->getCampo('valor');
                $arDados[$inCount]['valor_medio'] = bcdiv($rsDados->getCampo('valor'),$rsDados->getCampo('quantidade'),3);
                $arDados[$inCount]['unidade_medida'] = $rsDados->getCampo('unidade_medida');
                $inCount++;
                $rsDados->proximo();
            }
//gera total de itens
            $obRegra->listaValorTotalItemManutencao($rsTotalItem,$boTransacao);
        } else {
            $obRegra->KmAnterior($rsKmLimite,$boTransacao);
            $arDados[0]['data'] = "";
            $arDados[0]['quilometragem'] = "";
            $arDados[0]['descricao'] = "";
            $arDados[0]['tipo'] = "";
            $arDados[0]['quantidade'] = "";
            $arDados[0]['valor'] = "";
            $arDados[0]['valor_medio'] = "";
            $arDados[0]['unidade_medida'] = "";
            $rsTotalItem = new RecordSet;
            $rsTotalItem->preenche($arDados);

        }
        $inCount = 0;
        $arData = explode("-",$rsDadosVeiculo->getCampo('data_aquisicao'));

        $arDadosVeiculo[$inCount]['titulo1'] = "PLACA:";
        if ($rsDadosVeiculo->getCampo('placa') != null)
            $arDadosVeiculo[$inCount]['valor1' ] = substr($rsDadosVeiculo->getCampo('placa'),0,3)."-".substr($rsDadosVeiculo->getCampo('placa'),-4);
        $arDadosVeiculo[$inCount]['titulo2'] = "DATA DE AQUISIÇÃO:";
        $arDadosVeiculo[$inCount]['valor2' ] = $arData[2]."/".$arData[1]."/".$arData[0];
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "VEÍCULO:";
        $arDadosVeiculo[$inCount]['valor1'  ] = $rsDadosVeiculo->getCampo('modelo');
        $arDadosVeiculo[$inCount]['titulo2'] = "ANO:";
        $arDadosVeiculo[$inCount]['valor2' ] = $rsDadosVeiculo->getCampo('ano_fabricacao');
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "MARCA:";
        $arDadosVeiculo[$inCount]['valor1' ] = $rsDadosVeiculo->getCampo('marca');
        $arDadosVeiculo[$inCount]['titulo2'] = "NOTA FISCAL N°:";
        $arDadosVeiculo[$inCount]['valor2' ] = "";//$rsDadosVeiculo->getCampo(nota_fiscal);
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "COMBUSTÍVEL:";
        $arDadosVeiculo[$inCount]['valor1' ] = $rsDadosVeiculo->getCampo('combustivel');
        $arDadosVeiculo[$inCount]['titulo2'] = "RENAVAM:";
        $arDadosVeiculo[$inCount]['valor2' ] = $rsDadosVeiculo->getCampo('renavam');
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "POTÊNCIA:";
        $arDadosVeiculo[$inCount]['valor1' ] = $rsDadosVeiculo->getCampo('potencia');
        $arDadosVeiculo[$inCount]['titulo2'] = "KM INICIAL:";
        $arDadosVeiculo[$inCount]['valor2' ] = $rsKmLimite->getCampo('min');
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "CILINDRADA:";
        $arDadosVeiculo[$inCount]['valor1' ] = $rsDadosVeiculo->getCampo('cilindrada');
        $arDadosVeiculo[$inCount]['titulo2'] = "KM FINAL:";
        $arDadosVeiculo[$inCount]['valor2' ] = $rsKmLimite->getCampo('max');
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "LOTADO EM:";
        $arDadosVeiculo[$inCount]['valor1' ] = $rsDadosVeiculo->getCampo('lotado');
        $arDadosVeiculo[$inCount]['titulo2'] = "";
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "";
        $arDadosVeiculo[$inCount]['valor1' ] = "";
        $arDadosVeiculo[$inCount]['titulo2'] = "";
        $inCount++;
//        $arDadosVeiculo[$inCount]['titulo1'] = "DADOS DA MANUTENÇÃO";
//        $arDadosVeiculo[$inCount]['titulo2'] = "";
        $inCount++;
        $arDadosVeiculo[$inCount]['titulo1'] = "";
        $arDadosVeiculo[$inCount]['titulo2'] = "";
        $inCount++;

        $rsDados = new RecordSet;
        $rsDadosVeiculo = new RecordSet;
        $rsDados->preenche($arDados);
        $rsDadosVeiculo->preenche($arDadosVeiculo);

        $rsKmLimite->addFormatacao("max","KM");
        $rsKmLimite->addFormatacao("min","KM");
        $rsTotalItem->addFormatacao("valor","NUMERIC_BR_NULL");
        $rsDados->addFormatacao("valor","NUMERIC_BR_NULL");
        $rsDados->addFormatacao("quantidade","NUMERIC_BR_NULL");
        $rsDados->addFormatacao("valor_medio","NUMERIC_BR_NULL");
        $rsDados->addFormatacao("quilometragem","KM");

        Sessao::write('filtro' , $filtro);

        Sessao::write('transf6' , $rsDadosVeiculo);
        Sessao::write('transf5' , $rsDados);
        Sessao::write('transf7' , $rsTotalItem);

        $obRegra->obRRelatorio->executaFrameOculto(" OCGeraControleIndividual.php");

    break;
}

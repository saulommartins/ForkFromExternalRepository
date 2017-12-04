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

    Caso de uso: uc-03.02.18

    $Id: OCControleQuilometragem.php 59612 2014-09-02 12:00:51Z gelson $
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                   );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaItem.class.php"                                           );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaModelo.class.php"                                         );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaMarca.class.php"                                          );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaTipoVeiculo.class.php"                                    );
include_once (CAM_GP_FRO_NEGOCIO."RFrotaRelatorioControleQuilometragem.class.php"                 );

$obFrotaMarca = new  RFrotaMarca;
$obFrotaModelo = new RFrotaModelo;

switch ($_REQUEST['stCtrl']) {

        case "MontaModelo":
            $stModelo  = "inCodModelo";
            $stJs .= "limpaSelect(f.$stModelo,0); \n";
            $stJs .= "f.$stModelo.options[0] = new Option('Selecione','', 'selected');\n";

        if ($_REQUEST["inCodMarca"]) {
            $obFrotaModelo->obRFrotaMarca->setCodMarca($_REQUEST["inCodMarca"]);
             $obFrotaModelo->listar( $rsModelo, $stFiltro,"", $boTransacao );

            $inCount = 0;
            while (!$rsModelo->eof()) {
                $inCount++;
                $inId   = $rsModelo->getCampo("cod_modelo");
                $stDesc = $rsModelo->getCampo("nom_modelo");
                if( $stSelecionado == $inId )
                    $stSelected = 'selected';
                else
                    $stSelected = '';
                $stJs .= "f.$stModelo.options[$inCount] = new Option('".$stDesc."','".$inId."','".$stSelected."'); \n";
                $rsModelo->proximo();
            }
        }

    $stJs .= $js;
    SistemaLegado::executaFrameOculto( $stJs );

        break;

        default:
            $filtro = Sessao::read('filtro');

            if ($filtro['inCodTipoCombustivel']) {
                $tipoCombustivelFrota = new RFrotaItem;
                $tipoCombustivelFrota->setCodigo($filtro['inCodTipoCombustivel']);
                $tipoCombustivelFrota->listarCombustivel($tipoCombustivel);
                $filtro['stNomeCombustivel'] = $tipoCombustivel->getCampo('combustivel');
            }

            if ($filtro['inCodMarca']) {
                $nomeMarcaFrota     = new RFrotaMarca;
                $nomeMarcaFrota->setCodMarca($filtro['inCodMarca']);
                $nomeMarcaFrota->listar($nomeMarca);
                $filtro['stNomeMarca'] = $nomeMarca->getCampo('nom_marca');
            }
            if ($filtro['inCodModelo']) {
                $nomeModeloFrota    = new RFrotaModelo;
                $nomeModeloFrota->obRFrotaMarca->setCodMarca($filtro['inCodMarca']);
                $nomeModeloFrota->setCodModelo($filtro['inCodModelo']);
                $nomeModeloFrota->listar( $rsModelo, $stFiltro,"", $boTransacao );
                $filtro['stNomeModelo'] = $rsModelo->getCampo('nom_modelo');
            }
            if ($filtro['inCodTipoVeiculo']) {
                $nomeTipoVeiculoFrota = new RFrotaTipoVeiculo;
                $nomeTipoVeiculoFrota->setCodTipoVeiculo($filtro['inCodTipoVeiculo']);
                $nomeTipoVeiculoFrota->listar( $rsTipoVeiculo, $stFiltro="nom_tipo", $boTransacao );
                $filtro['stNomeTipoVeiculo'] = $rsTipoVeiculo->getCampo('nom_tipo');
            }

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
            $arMesExtenso = array('','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO');
            $filtro['stNomeMes'] = $arMesExtenso[intval($filtro['stMes'])];

           $obRegra = new RFrotaRelatorioControleQuilometragem;
           //seta as variaveis que serão utilizadas para gerar o relatório
           $obRegra->setCodVeiculo         ($filtro["inCodVeiculo"]);
           $obRegra->setCodMarca           ($filtro["inCodMarca"]);
           $obRegra->setCodModelo          ($filtro["inCodModelo"]);
           $obRegra->setCodTipoVeiculo     ($filtro["inCodTipoVeiculo"]);
           $obRegra->setCodTipoCombustivel ($filtro["inCodTipoCombustivel"]);
           $obRegra->setPrefixo            ($filtro["stPrefixo"]);
           $obRegra->setPlaca              ($filtro["stPlaca"]);
           $obRegra->setCGMResponsavel     ($filtro["inCGMRespnsavel"]);
           $obRegra->setNomeCGMResponsavel ($filtro["stNomeCGMRespnsavel"]);
           $obRegra->setCodOrdenacao       ($filtro["inCodOrdenacao"]);
           $obRegra->setCodOrigemVeiculo   ($filtro["inCodOrigemVeiculo"]);
           $obRegra->setCodVeiculoBaixado  ($filtro["inCodVeiculoBaixado"]);
           $obRegra->setDataInicial        ($filtro["stDataInicial"]);
           $obRegra->setDataFinal          ($filtro["stDataFinal"]);
           $obRegra->setNomeMes            ($filtro["stNomeMes"]);
           $obRegra->listarDadosControleQuilometragem($rsLista,$stOrder,$boTransacao);

       $inCount = 0;
       while (!$rsLista->eof()) {
            $combustivel = $rsLista->getCampo('combustivel');
            $unidadeMedida = $rsLista->getCampo('unidade_medida');
            if ($combustivel == $combustivelAnterior) {
                $arTotal[$inCount]['combustivel'] = $combustivel;
                $arTotal[$inCount]['valor'] =      $arTotal[$inCount]['valor']+$rsLista->getCampo('valor');
                $arTotal[$inCount]['quantidade'] = $arTotal[$inCount]['quantidade']+$rsLista->getCampo('quantidade');
                $arTotal[$inCount]['unidade_medida'] = $unidadeMedida;
                $arTotal[$inCount]['valor_medio'] = bcdiv($arTotal[$inCount]['valor'],$arTotal[$inCount]['quantidade'],3);
            } else {
                $arTotal[$inCount]['combustivel'] = $combustivel;
                $arTotal[$inCount]['valor'] = $rsLista->getCampo('valor');
                $arTotal[$inCount]['quantidade'] = $rsLista->getCampo('quantidade');
                $arTotal[$inCount]['unidade_medida'] = $unidadeMedida;
                $arTotal[$inCount]['valor_medio'] = bcdiv($arTotal[$inCount]['valor'],$arTotal[$inCount]['quantidade'],3);
                $inCount++;
            }
            $rsLista->proximo();
            $combustivelAnterior = $rsLista->getCampo('combustivel');
       }
       $rsLista->addFormatacao("km_final","KM");
       $rsLista->addFormatacao("km_inicial","KM");
       $rsLista->addFormatacao("valor","NUMERIC_BR_NULL");
       $rsLista->addFormatacao("valor_medio","NUMERIC_BR_NULL");
       $rsLista->addFormatacao("quantidade","NUMERIC_BR_NULL");
       $rsLista->setPrimeiroElemento();
       $rsTotal = new RecordSet;
       $rsTotal->preenche($arTotal);
       $rsTotal->addFormatacao("valor","NUMERIC_BR_NULL");
       $rsTotal->addFormatacao("quantidade","NUMERIC_BR_NULL");
       $rsTotal->addFormatacao("valor_medio","NUMERIC_BR_NULL");
       $rsTotal->addFormatacao("consumo","NUMERIC_BR_NULL");

       Sessao::write('filtro' , $filtro);

       Sessao::write('transf5' , $rsLista);
       Sessao::write('transf6' , $rsTotal);

       $obRegra->obRRelatorio->executaFrameOculto(" OCGeraControleQuilometragem.php");
       break;
}

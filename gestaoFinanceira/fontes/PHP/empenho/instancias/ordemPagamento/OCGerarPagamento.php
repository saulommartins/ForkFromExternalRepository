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
    * Pagina executada no frame oculto para retornar valores para o principal
    * Data de Criação   : 16/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    $Revision: 30805 $
    $Name$
    $Autor: $
    $Date: 2006-07-18 14:56:34 -0300 (Ter, 18 Jul 2006) $

    * Casos de uso: uc-02.03.05
*/

/*
$Log$
Revision 1.5  2006/07/18 17:51:58  jose.eduardo
Bug #6588#

Revision 1.4  2006/07/05 20:48:56  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php" );
include_once ( CAM_GF_EMP_NEGOCIO."REmpenhoNotaLiquidacao.class.php" );

function montaListaLiquidacao($rsListaLiquidacao , $newValorTotal , $newValorAnulado , $valorParaAnular , $cgmFornecedor , $boRetorna = false)
{
    if ( $rsListaLiquidacao->getNumLinhas() != 0 ) {
        $obLista3 = new Lista;
        $obLista3->setRecordSet                 ( $rsListaLiquidacao   );
        $obLista3->setTitulo                    ( "Registros"          );
        $obLista3->setMostraPaginacao           ( false                );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "&nbsp;"             );
        $obLista3->ultimoCabecalho->setWidth    ( 5                    );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Empenho"            );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data do Empenho"    );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Liquidação"         );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Data da Liquidação" );
        $obLista3->ultimoCabecalho->setWidth    ( 15                   );
        $obLista3->commitCabecalho              (                      );
        $obLista3->addCabecalho                 (                      );
        $obLista3->ultimoCabecalho->addConteudo ( "Valor a Pagar"      );
        $obLista3->ultimoCabecalho->setWidth    ( 30                   );
        $obLista3->commitCabecalho              (                      );
        if ($_REQUEST["stAcao"] == "incluir") {
            $obLista3->addCabecalho                 (                  );
            $obLista3->ultimoCabecalho->addConteudo ( "&nbsp;"         );
            $obLista3->ultimoCabecalho->setWidth    ( 5                );
            $obLista3->commitCabecalho              (                  );
        }

        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "[cod_empenho]/[ex_empenho]" );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "dt_empenho"         );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"             );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "[cod_nota]/[ex_nota]" );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "dt_nota"            );
        $obLista3->ultimoDado->setAlinhamento   ( "CENTRO"             );
        $obLista3->commitDado                   (                      );
        $obLista3->addDado                      (                      );
        $obLista3->ultimoDado->setCampo         ( "valor_pagar"        );
        $obLista3->ultimoDado->setAlinhamento   ( "DIREITA"            );
        $obLista3->commitDado                   (                      );

        if ($_REQUEST["stAcao"] == "incluir") {
            $obLista3->addAcao                      (                      );
            $obLista3->ultimaAcao->setAcao          ( "EXCLUIR"            );
            $obLista3->ultimaAcao->setFuncao        ( true                 );
            $obLista3->ultimaAcao->setLink   ( "JavaScript:excluirItem();" );
            $obLista3->ultimaAcao->addCampo        ( "inIndice","cod_nota" );
            $obLista3->commitAcao                   (                      );
        }

        $obLista3->montaHTML                     (                      );
        $stHTML =  $obLista3->getHtml            (                      );
        $stHTML = str_replace                   ( "\n","",$stHTML      );
        $stHTML = str_replace                   ( chr(13),"<br>",$stHTML      );
        $stHTML = str_replace                   ( "  ","",$stHTML      );
        $stHTML = str_replace                   ( "'","\\'",$stHTML    );
    } else {
        $stHTML = "&nbsp";
    }
    $js .= "d.getElementById('spnListaItem').innerHTML = '".$stHTML."';\n";
    $js .= "d.frm.flValorTotal.value = '$newValorTotal';";
    if ($_REQUEST["stAcao"] == "incluir") {
        $js .= "d.frm.stFornecedor.value = '".$cgmFornecedor."';";
    } else {
        $js .= "d.frm.flValorAnulado.value = '$newValorAnulado';";
        $js .= "d.frm.flValorAnular.value = '$valorParaAnular';";
    }

    if ($boRetorna) {
        return $js;
    } else {
        SistemaLegado::executaFrameOculto($js);
    }
}

$stCtrl = $_REQUEST['stCtrl'];

switch ($_REQUEST ["stCtrl"]) {
    case "buscaLiquidacoes":
        $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
        $js .= "limpaSelect(f.cmbLiquidacao,0); \n";
        $js .= "f.cmbLiquidacao[0] = new Option('Selecione','', 'selected');\n";
        if ($_REQUEST["inCodigoEmpenho"] && $_REQUEST["inCodigoEntidade"]) {
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $_REQUEST["inCodigoEmpenho"]);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST["inCodigoEntidade"]);
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setCodEmpenho( $_REQUEST["inCodigoEmpenho"] );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->setExercicio( $_REQUEST['stExercicioEmpenho'] );
            $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->roREmpenhoEmpenho->checarImplantado( $boImplantado );
            if ($boImplantado) {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasDisponiveisImplantadas( $rsLiquidacoes );
            } else {
                $obREmpenhoOrdemPagamento->obREmpenhoNotaLiquidacao->listarNotasDisponiveis( $rsLiquidacoes );
            }
            $inContador = 1;
            while ( !$rsLiquidacoes->eof() ) {
                if ( $rsLiquidacoes->getCampo("cod_empenho") == $_REQUEST["inCodigoEmpenho"] ) {
                    $flValorNota        = $rsLiquidacoes->getCampo( "vl_nota" );
                    $flValorNotaTMP     = str_replace( '.','',$flValorNota );
                    $flValorNotaTMP     = str_replace( ',','.',$flValorNotaTMP );
                    if ($flValorNotaTMP > 0) {
                        $inCodigoLiquidacao = $rsLiquidacoes->getCampo( "cod_nota"          );
                        $exercicioNota      = $rsLiquidacoes->getCampo( "exercicio_nota"    );
                        $dtDataLiquidacao   = $rsLiquidacoes->getCampo( "dt_liquidacao"     );
                        $inCodigoEmpenho    = $rsLiquidacoes->getCampo( "cod_empenho"       );
                        $dtDataEmpenho      = $rsLiquidacoes->getCampo( "dt_empenho"        );
                        $exercicioEmpenho   = $rsLiquidacoes->getCampo( "exercicio_empenho" );
                        $numCGM             = $rsLiquidacoes->getCampo( "cgm_beneficiario"  );
                        $nomeCGM            = $rsLiquidacoes->getCampo( "beneficiario"      );
                        $boImplantado       = $rsLiquidacoes->getCampo( "implantado"        );
                        $nuVlOrdem          = $rsLiquidacoes->getCampo("vl_ordem") - $rsLiquidacoes->getCampo( "vl_ordem_anulada" );
                        $nuVlLiquidado      = $rsLiquidacoes->getCampo( "vl_itens" ) - $rsLiquidacoes->getCampo( "vl_itens_anulados" );
//                        if ($boImplantado) {
//                            $nuVlAPagar = $nuVlLiquidado - ( $nuVlPago + $nuVlOrdemPago );
//                        } else {
                            $nuVlAPagar = $nuVlLiquidado - $nuVlOrdem;
//                        }
                        $nuVlAPagar         = number_format( $nuVlAPagar, 2, ',','.' );
                        $mixCombo = $inCodigoLiquidacao." - ".$dtDataLiquidacao;
                        $mixComboValor = $inCodigoLiquidacao."||".$dtDataLiquidacao."||".$nuVlAPagar."||".$inCodigoEmpenho."||".$dtDataEmpenho."||".$exercicioEmpenho."||".$numCGM."||".$nomeCGM."||".$exercicioNota."||".$boImplantado;
                        $js .= "f.cmbLiquidacao.options[$inContador] = new Option('".$mixCombo."','".$mixComboValor."'); \n";
                        $inContador++;
                    }
                }
                $rsLiquidacoes->proximo();
            }
        } else {
            $js = "f.inCodigoEmpenho.value='';";
        }
        SistemaLegado::executaFrameOculto($js);
    break;
    case "incluirItem":
        $mixLiquidacao = explode("||", $_REQUEST["cmbLiquidacao"]);
        $inCodigoLiquidacao = $mixLiquidacao[0];
        $dtDataLiquidacao   = $mixLiquidacao[1];
        $flValorNota        = $mixLiquidacao[2];
        $inCodigoEmpenho    = $mixLiquidacao[3];
        $dtDataEmpenho      = $mixLiquidacao[4];
        $exercicioEmpenho   = $mixLiquidacao[5];
        $inNumCGM           = $mixLiquidacao[6];
        $stNomeCGM          = $mixLiquidacao[7];
        $exercicioNota      = $mixLiquidacao[8];

        $arItens = Sessao::read('itemOrdem');
        $arValorTotalOrdem = Sessao::read('valorTotalOrdem');
        $arCgmFornecedor = Sessao::read('cgmFornecedor');
        $stInsere = false;
        if ($arItens) {
            $inCountSessao = count ($arItens);
        } else {
            $inCountSessao = 0;
            $stInsere = true;
        }
        for ($iCount = 0; $iCount < $inCountSessao; $iCount++) {
            if ($arItens[$iCount]["num_cgm"] != $inNumCGM) {
                $obErro = new Erro;
                $obErro->setDescricao( "As notas de liquidação informadas devem ser do mesmo fornecedor!" );
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                $stInsere = false;
                $iCount = $inCountSessao;
            } elseif ($arItens[$iCount]["cod_nota"]    == $inCodigoLiquidacao) {
                $obErro = new Erro;
                $obErro->setDescricao( "Nota de liquidação já informada!" );
                SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
                $stInsere = false;
                $iCount = $inCountSessao;
            } else {
                $stInsere = true;
            }
        }
        if ($stInsere) {
            if ($arItens) {
               $inLast = count ($arItens);
            } else {
                $inLast = 0;
                $arItens = array ();
                $arValorTotalOrdem = 0;
                $arCgmFornecedor = "";
            }
            $arItens[$inLast]["cod_empenho"    ] = $inCodigoEmpenho;
            $arItens[$inLast]["dt_empenho"     ] = $dtDataEmpenho;
            $arItens[$inLast]["ex_empenho"     ] = $exercicioEmpenho;
            $arItens[$inLast]["cod_nota"       ] = $inCodigoLiquidacao;
            $arItens[$inLast]["ex_nota"        ] = $exercicioNota;
            $arItens[$inLast]["dt_nota"        ] = $dtDataLiquidacao;
            $arItens[$inLast]["valor_pagar"    ] = $_REQUEST['flValorPagar'];
            $arItens[$inLast]["max_valor_pagar"] = $flValorNota;
            $arItens[$inLast]["num_cgm"        ] = $inNumCGM;
            $arItens[$inLast]["nom_cgm"        ] = $stNomeCGM;

            $somaTemp = str_replace(".","",$_REQUEST['flValorPagar']);
            $somatorio = str_replace(",",".",$somaTemp);
            $arValorTotalOrdem += $somatorio;
            $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');

            $arCgmFornecedor = $inNumCGM." - ".$stNomeCGM;
        } else {
            $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');
        }
        Sessao::write('itemOrdem', $arItens);
        Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
        Sessao::write('cgmFornecedor', $arCgmFornecedor);
        $rsListaItemOrdem = new RecordSet;
        $rsListaItemOrdem->preenche ( $arItens );
        $rsListaItemOrdem->ordena("cod_nota");
            montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , "" , "" , $arCgmFornecedor );
        exit (0);
    break;
    case "recuperaItem":
        $obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
        $obREmpenhoOrdemPagamento->setCodigoOrdem($_REQUEST["hdnCodigoOrdem"]);
        $obREmpenhoOrdemPagamento->setExercicio($_REQUEST["hdnExercicioOrdem"]);
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["hdnCodigoEntidade"]);
        $obREmpenhoOrdemPagamento->listarItem($rsItens);

        $arItens = Sessao::read('itemOrdem');

        $inCountItem = 0;
        $newValorTotal = 0;
        $arItens = array ();
        while ( !$rsItens->eof() ) {
            $arItens[$inCountItem]["cod_empenho" ] = $rsItens->getCampo("cod_empenho"         );
            $arItens[$inCountItem]["ex_empenho" ]  = $rsItens->getCampo("exercicio_empenho"   );
            $arItens[$inCountItem]["dt_empenho" ]  = $rsItens->getCampo("dt_empenho"          );
            $arItens[$inCountItem]["cod_nota" ]    = $rsItens->getCampo("cod_nota"            );
            $arItens[$inCountItem]["ex_nota" ]     = $rsItens->getCampo("exercicio_liquidacao");
            $arItens[$inCountItem]["dt_nota"]      = $rsItens->getCampo("dt_liquidacao"       );
            $arItens[$inCountItem]["valor_pagar"]  = $rsItens->getCampo("vl_pagamento"        );
            $arItens[$inCountItem]["num_cgm" ]     = $rsItens->getCampo("cgm_beneficiario"    );
            $arItens[$inCountItem]["nom_cgm" ]     = $rsItens->getCampo("beneficiario"        );
            $inCountItem++;
            $rsItens->proximo();
        }
        $newValorTotal   = number_format($_REQUEST["hdnValorTotal"], 2, ',', '.');
        $newValorAnulado = number_format($_REQUEST["hdnValorAnulado"], 2, ',', '.');

        $valorAnularTemp = $_REQUEST["hdnValorTotal"] - $_REQUEST["hdnValorAnulado"];
        $ValorParaAnular = number_format($valorAnularTemp, 2, ',', '.');

        Sessao::write('itemOrdem', $arItens);

        $rsListaItemOrdem = new RecordSet;
        $rsListaItemOrdem->preenche ( $arItens );
        $rsListaItemOrdem->ordena("cod_nota");
        montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , $newValorAnulado , $ValorParaAnular , "" );
        exit (0);
    break;
    case "excluirItem":
        $arTmpItem = array ();
        $arItens = Sessao::read('itemOrdem');
        $arValorTotalOrdem = Sessao::read('valorTotalOrdem');
        $arCgmFornecedor = Sessao::read('cgmFornecedor');
        $inCountSessao = count ($arItens);
        $inCountArray = 0;
        $newValorTotal = 0;
        $arValorTotalOrdem = 0;
        for ($inCount = 0; $inCount < $inCountSessao; $inCount++) {
            if ($arItens[$inCount][ "cod_nota" ] != $_REQUEST[ "inIndice" ]) {
                $arTmpItem[$inCountArray]["cod_empenho"] = $arItens[$inCount][ "cod_empenho" ];
                $arTmpItem[$inCountArray]["ex_empenho"]  = $arItens[$inCount][ "ex_empenho"  ];
                $arTmpItem[$inCountArray]["dt_empenho"]  = $arItens[$inCount][ "dt_empenho"  ];
                $arTmpItem[$inCountArray]["cod_nota"]    = $arItens[$inCount][ "cod_nota"    ];
                $arTmpItem[$inCountArray]["ex_nota"]     = $arItens[$inCount][ "ex_nota"     ];
                $arTmpItem[$inCountArray]["dt_nota"]     = $arItens[$inCount][ "dt_nota"     ];
                $arTmpItem[$inCountArray]["valor_pagar"] = $arItens[$inCount][ "valor_pagar" ];
                $arTmpItem[$inCountArray]["num_cgm"]     = $arItens[$inCount][ "num_cgm"     ];
                $arTmpItem[$inCountArray]["nom_cgm"]     = $arItens[$inCount][ "nom_cgm"     ];

                $somaTemp = str_replace(".","",$arItens[$inCount][ "valor_pagar" ]);
                $somatorio = str_replace(",",".",$somaTemp);
                $arValorTotalOrdem += $somatorio;
                $newValorTotal = number_format($arValorTotalOrdem, 2, ',', '.');

                $inCountArray++;
            }
        }
        $arItens = array();
        if ( count($arTmpItem) != 0 ) {
            $arItens = $arTmpItem;
            Sessao::write('itemOrdem', $arItens);
            Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
            $rsListaItemOrdem = new RecordSet;
            $rsListaItemOrdem->preenche ( $arTmpItem );
            $rsListaItemOrdem->ordena("cod_nota");
            montaListaLiquidacao( $rsListaItemOrdem , $newValorTotal , "" , "" , $arCgmFornecedor );
        } else {
            Sessao::write('itemOrdem', $arItens);
            Sessao::write('valorTotalOrdem', $arValorTotalOrdem);
            $js  = "d.frm.flValorTotal.value = '';";
            $js .= "d.frm.stFornecedor.value = '';";
            $js .= "d.getElementById('spnListaItem').innerHTML = '';";
            SistemaLegado::executaFrameOculto($js);
        }
        exit (0);
    break;
    case "limparItem":
        $stJs .= "limpaSelect(f.cmbLiquidacao,0); \n";
        $stJs .= "d.frm.flValorPagar.value = '';";
        $stJs .= "d.frm.inCodigoEmpenho.value = '';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
    case "limparOrdem":
        $arItens = Sessao::read('itemOrdem');
        $arItens = array();
        Sessao::write('itemOrdem', $arItens);
        $stJs .= "d.frm.reset();";
        $stJs .= "d.getElementById('spnListaItem').innerHTML = '';";
        SistemaLegado::executaFrameOculto($stJs);
    break;
}
?>

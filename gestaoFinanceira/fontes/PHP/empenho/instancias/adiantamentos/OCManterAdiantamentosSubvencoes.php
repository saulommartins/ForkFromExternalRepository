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
    * Página Oculto de Lancamento Partida Dobrada
    * Data de Criação   : 19/10/2006

    $Id: OCManterAdiantamentosSubvencoes.php 59612 2014-09-02 12:00:51Z gelson $

    * @author Analista      : Gelson Gonçalves
    * @author Desenvolvedor : Rodrigo

    * @ignore

    * Casos de uso: uc-02.03.31
*/

//include padrão do framework
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_EMP_MAPEAMENTO."TEmpenhoTipoDocumento.class.php"                              );
include_once( TEMP."TEmpenhoItemPrestacaoContas.class.php" );
include_once( CAM_GA_CGM_NEGOCIO . "RCGM.class.php" );

$stCtrl = $_POST["stCtrl"] ? $_POST["stCtrl"] : $_GET["stCtrl"];

$stPrograma = "ManterAdiantamentosSubvencoes";

$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$js = "";

switch ($_REQUEST['stCtrl']) {

    case 'montaListaPrestacaoContas':

        $rsRecordSetItemPrestacao      = new RecordSet();
        $obTEmpenhoItemPrestacaoContas = new TEmpenhoItemPrestacaoContas;
        $obTEmpenhoItemPrestacaoContas->setDado('exercicio'   ,Sessao::getExercicio());
        $obTEmpenhoItemPrestacaoContas->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
        $obTEmpenhoItemPrestacaoContas->setDado('cod_empenho' ,$_REQUEST['inCodEmpenho']);
        $obTEmpenhoItemPrestacaoContas->recuperaListagemPrestacao( $rsRecordSetItemPrestacao);

        $arValores = Sessao::read('arValores');
        $arValoresmd5 = Sessao::read('arValoresmd5');
        if (!($rsRecordSetItemPrestacao->EOF())) {
            $Cont = 0;

            while (!($rsRecordSetItemPrestacao->EOF())) {
                $obRCGM = new RCGM();
                $obRCGM->setNumCGM($rsRecordSetItemPrestacao->getCampo("credor"));
                $obRCGM->consultar($rsCGM);

                // Gera uma chave para depois verificar se a base foi modificada
                $chave = $rsRecordSetItemPrestacao->getCampo("num_item")
                        .$rsRecordSetItemPrestacao->getCampo("cod_documento")
                        .$rsRecordSetItemPrestacao->getCampo("num_documento")
                        .$rsRecordSetItemPrestacao->getCampo("data_item")
                        .$rsRecordSetItemPrestacao->getCampo("valor_item")
                        .$rsRecordSetItemPrestacao->getCampo("justificativa")
                        .$rsRecordSetItemPrestacao->getCampo("credor");

                $arValoresmd5[$Cont]['md5'] = md5($chave);

                $arValores[$Cont]['id'                 ]=$Cont + 1;
                $arValores[$Cont]['numItem'            ]=$rsRecordSetItemPrestacao->getCampo("num_item"       );
                $arValores[$Cont]['stDtPrestacaoContas']=$rsRecordSetItemPrestacao->getCampo("data"           );
                $arValores[$Cont]['inCodTipoDocumento' ]=$rsRecordSetItemPrestacao->getCampo("cod_documento"  );
                $arValores[$Cont]['stDataDocumento'    ]=$rsRecordSetItemPrestacao->getCampo("data_item"      );
                $arValores[$Cont]['inNroDocumento'     ]=$rsRecordSetItemPrestacao->getCampo("num_documento"  );
                $arValores[$Cont]['inCodFornecedor'    ]=$rsRecordSetItemPrestacao->getCampo("credor"         );
                $arValores[$Cont]['stNomCredor'        ]=$obRCGM->getNomCGM();
                $arValores[$Cont]['stJustificativa'    ]=$rsRecordSetItemPrestacao->getCampo("justificativa"  );
                $arValores[$Cont]['nuValor'            ]=number_format($rsRecordSetItemPrestacao->getCampo("valor_item"     ),2,',','.');
                $arValores[$Cont]['exercicioConta'     ]=$rsRecordSetItemPrestacao->getCampo("exercicio_conta");
                $arValores[$Cont]['inCodContraPartida' ]=$rsRecordSetItemPrestacao->getCampo("conta_contrapartida");
                $arValores[$Cont]['inCodEmpenho'       ]=$rsRecordSetItemPrestacao->getCampo("cod_empenho"    );
                $arValores[$Cont]['inCodEntidade'      ]=$rsRecordSetItemPrestacao->getCampo("cod_entidade"   );

                $obTTipoDocumento = new TEmpenhoTipoDocumento();
                $obTTipoDocumento->setDado('cod_documento',$rsRecordSetItemPrestacao->getCampo("cod_documento"  ));
                $obTTipoDocumento->recuperaPorChave($rsTipoDocumento);
                $arValores[$Cont]['stTipoDocumento'    ] = $rsTipoDocumento->getCampo("descricao" );

                $inX     = str_replace(',','.',str_replace('.','',$arValores[$Cont]['nuValor']));
                $inTotal+= $inX;
                $Cont++;
                $rsRecordSetItemPrestacao->proximo();
            }
        }

        if (count($arValores) == 0) {
            $js .= "d.getElementById('stDtPrestacaoContas').readOnly = false ;";
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '0,00'; ";
            $js .= "jq('#inVlPagoTMP').html('0.00');";
        } else {
            $js .= "d.getElementById('stDtPrestacaoContas').value='".$arValores[0]['stDtPrestacaoContas']."';";
            $js .= "d.getElementById('stDtPrestacaoContas').readOnly = true ;";
            $js .= "d.getElementById('stDataDocumento').focus();";
            $js .= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($inTotal,2,',','.')."'; ";
            $js .= "jq('#inVlPagoTMP').html('".number_format($inTotal,2,',','.')."');";
        }

        Sessao::write('arValores', $arValores);
        Sessao::write('arValoresmd5', $arValoresmd5);
        Sessao::write('inCountValores', count($arValores));
        echo $js.montaListaPrestacaoContas($arValores);

    break;

    case "validaDataPrestacao":

        $inDtPagamento = $_REQUEST['stDataPagamentoEmpenho'];
        $inDtPrestacao = $_REQUEST['stDtPrestacaoContas'];

        if ($inDtPrestacao != '') {
            if (!SistemaLegado::comparaDatas($inDtPrestacao,$inDtPagamento, true)) {
                $mensagem = "A data de prestação de contas deve ser igual ou posterior a data de pagamento do empenho (".$inDtPagamento.").";
                echo "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";

                $js = "jq('#stDtPrestacaoContas').val('');";
                $js .= "jq('#stDtPrestacaoContas').focus();";
                echo $js;
            }
            $stDataAtual = date("d/m/Y");
            if (!SistemaLegado::comparaDatas($stDataAtual, $inDtPrestacao, true)) {
                $mensagem = "A data da prestação de contas deve ser menor ou igual que a data atual.";
                echo "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";

                $js = "jq('#stDtPrestacaoContas').val('');";
                $js .= "jq('#stDtPrestacaoContas').focus();";
                echo $js;
            }
        } else {
            $js .= "jq('#stDataDocumento').focus();";
            echo $js;
        }

    break;

    case "validaDataDocumento":

        $stDtPagamento = $_REQUEST['stDataPagamentoEmpenho'];
        $stDtDocumento = $_REQUEST['stDataDocumento'];
        $stDtPrestacao = $_REQUEST['stDtPrestacaoContas'];

        if ($stDtDocumento != '') {
            if (!SistemaLegado::comparaDatas($stDtDocumento,$stDtPagamento, true)) {
                $mensagem = "A data do documento deve ser igual ou posterior a data de pagamento do empenho (".$stDtPagamento.").";
                echo "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";

                $js = "jq('#stDataDocumento').val('');";
                $js .= "jq('#stDataDocumento').focus();";
                echo $js;
            }
            if (!SistemaLegado::comparaDatas($stDtPrestacao, $stDtDocumento, true)) {
                $mensagem = "A data do documento deve ser menor ou igual que a data da prestação (".$stDtPrestacao.").";
                echo "alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";

                $js = "jq('#stDataDocumento').val('');";
                $js .= "jq('#stDataDocumento').focus();";
                echo $js;
            }
        } else {
            $js .= "jq('#inNroDocumento').focus();";
            echo $js;
        }

    break;

    case "alterar":

        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arItem) {

            if ($arItem['id'] == $_REQUEST['id']) {

                $i = $_REQUEST['id']-1;

                $js.="f.stDataDocumento.value                   = '".$arItem['stDataDocumento'    ]."'            ;";
                $js.="f.inCodTipoDocumento.value                = '".$arItem['inCodTipoDocumento' ]."'            ;";
                $js.="f.inNroDocumento.value                    = '".$arItem['inNroDocumento'     ]."'            ;";
                $js.="f.inCodFornecedor.value                   = '".$arItem['inCodFornecedor'    ]."'            ;";
                $js.="jq('#stNomCredor').html('".$arItem['stNomCredor']."');";
                $js.="jq('#stNomCredor').val('".$arItem['stNomCredor']."');";
                $js.="jq('#stJustificativa').val('".$arItem['stJustificativa']."');";
                $js.="f.nuValor.value                           = '".$arItem['nuValor'            ]."'            ;";
                $js.="f.HdnCodItem.value                        = '".$arItem['id'                 ]."'            ;";

                $js.="f.stDataDocumento.focus();                                                                   ";
                $js.="d.getElementById('incluirNota').value     = 'Alterar';                                       ";
                $js.='jq("#stCtrl").val("alteradoListaPrestacaoContas"); ';

                $js.="f.stJustificativa.value = f.stJustificativa.value.unescapeHTML();                            ";
                $js.="f.stNomCredor.value     = f.stNomCredor.value.unescapeHTML();                                ";

                echo $js;

                break;
            }
        }

    break;

    case "mudaVisibilidadeON":
        $js  = "jq('#stDataDocumento').attr('disabled', 'disabled');";
        $js .= "jq('#inCodTipoDocumento').attr('disabled', 'disabled');";
        $js .= "jq('#inNroDocumento').attr('disabled', 'disabled');";
        $js .= "jq('#inCodFornecedor').attr('disabled', 'disabled');";
        $js .= "jq('#stJustificativa').attr('disabled', 'disabled');";
        $js .= "jq('#nuValor').attr('disabled', 'disabled');";
        $js .= "jq('#incluirNota').attr('disabled', 'disabled');";
        $js .= "jq('#Limpar').attr('disabled', 'disabled');";
        $js .= "jq('#flTotalPrestacaoContas').html(jq('#flPago').html()); ";
        echo $js;
    break;

    case "mudaVisibilidadeOFF":
        $js  = "jq('#stDataDocumento').removeAttr('disabled');";
        $js .= "jq('#inCodTipoDocumento').removeAttr('disabled');";
        $js .= "jq('#inNroDocumento').removeAttr('disabled');";
        $js .= "jq('#inCodFornecedor').removeAttr('disabled');";
        $js .= "jq('#stJustificativa').removeAttr('disabled');";
        $js .= "jq('#nuValor').removeAttr('disabled');";
        $js .= "jq('#incluirNota').removeAttr('disabled');";
        $js .= "jq('#Limpar').removeAttr('disabled');";
        $js .= "jq('#flTotalPrestacaoContas').html(jq('#inVlPagoTMP').html()); ";
        echo $js;
    break;

    case "alteradoListaPrestacaoContas":

        $arValores = Sessao::read('arValores');
        foreach ($arValores as $arItem) {
            if ($arItem['id'] != $_REQUEST['HdnCodItem']) {
                $nuTotalValor = bcadd($nuTotalValor,str_replace(',','.',str_replace('.','',$arItem['nuValor'])),2);
            }
        }

        $nuValor          = str_replace(',','.',str_replace('.','',$_REQUEST['nuValor']));
        $nuTotalPrestacao = bcadd($nuTotalValor,$nuValor,2);

        if ($_REQUEST['stDtPrestacaoContas'] != '' && $_REQUEST['stDataDocumento']
            && $_REQUEST['inCodTipoDocumento'] != '' && $_REQUEST['inCodFornecedor']
            && $_REQUEST['nuValor']) {

            if ($nuValor > 0.00) {

                foreach ($arValores as $key => $arItem) {
                    if ($arItem['id'] == $_REQUEST['HdnCodItem']) {

                        $arValores[$key]['inCodTipoDocumento'  ] = $_REQUEST[ "inCodTipoDocumento"  ];
                        $arValores[$key]['stDataDocumento'     ] = $_REQUEST[ "stDataDocumento"     ];
                        $arValores[$key]['inNroDocumento'      ] = $_REQUEST[ "inNroDocumento"      ];
                        $arValores[$key]['inCodFornecedor'     ] = $_REQUEST[ "inCodFornecedor"     ];
                        $arValores[$key]['stNomCredor'         ] = stripslashes($_REQUEST[ "stNomCredor"         ]);
                        $arValores[$key]['stJustificativa'     ] = stripslashes($_REQUEST[ "stJustificativa"     ]);
                        $arValores[$key]['nuValor'             ] = $_REQUEST[ "nuValor"             ];

                        $obTTipoDocumento = new TEmpenhoTipoDocumento();
                        $obTTipoDocumento->setDado('cod_documento',$_REQUEST["inCodTipoDocumento"]);
                        $obTTipoDocumento->recuperaPorChave($rsTipoDocumento);
                        $arValores[$key]['stTipoDocumento'] = $rsTipoDocumento->getCampo("descricao" );

                        break;
                    }
                }

                $js = "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($nuTotalPrestacao,2,',','.')."';";
                $js.= "d.getElementById('incluirNota').value                = 'Incluir';                                       ";
                $js.= 'jq("#stCtrl").val("incluir"); ';
                $js.= "limpaDado();                                                                                            ";

                Sessao::write('arValores', $arValores);
                echo $js.montaListaPrestacaoContas($arValores);

            } else {
                $mensagem = "O valor a prestar não pode ser zero.";
                echo"alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $mensagem = "Preencha todos os campos obrigatórios.";
            echo"alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
        }

    break;

    case 'incluir':

        $arValores = Sessao::read('arValores');
        if ($arValores != '') {
            foreach ($arValores as $arItem) {
                $nuTotalValor = bcadd($nuTotalValor,str_replace(',','.',str_replace('.','',$arItem['nuValor'])),2);
            }
        }

        $nuValor          = str_replace(',','.',str_replace('.','',$_REQUEST['nuValor']));
        $nuTotalPrestacao = bcadd($nuTotalValor,$nuValor,2);

        if ($_REQUEST['stDtPrestacaoContas'] != '' && $_REQUEST['stDataDocumento']
            && $_REQUEST['inCodTipoDocumento'] != '' && $_REQUEST['inCodFornecedor']
            && $_REQUEST['nuValor']) {
            if ($nuValor > 0.00) {
                $inCount = sizeof($arValores);
                $id      = ( $inCount != 0 ) ? $arValores[$inCount-1]['id'] : 0 ;

                $arValores[$inCount]['id'                  ] = $id+1;
                $arValores[$inCount]['inCodTipoDocumento'  ] = $_REQUEST[ "inCodTipoDocumento"  ];
                $arValores[$inCount]['stDataDocumento'     ] = $_REQUEST[ "stDataDocumento"     ];
                $arValores[$inCount]['inNroDocumento'      ] = $_REQUEST[ "inNroDocumento"      ];
                $arValores[$inCount]['inCodFornecedor'     ] = $_REQUEST[ "inCodFornecedor"     ];
                $arValores[$inCount]['stNomCredor'         ] = stripslashes($_REQUEST[ "stNomCredor"         ]);
                $arValores[$inCount]['stJustificativa'     ] = stripslashes($_REQUEST[ "stJustificativa"     ]);
                $arValores[$inCount]['nuValor'             ] = $_REQUEST[ "nuValor"             ];
                $arValores[$inCount]['inCodEmpenho'        ] = $_REQUEST[ "inCodEmpenho"        ];
                $arValores[$inCount]['inCodEntidade'       ] = $_REQUEST[ "inCodEntidade"       ];

                $obTTipoDocumento = new TEmpenhoTipoDocumento();
                $obTTipoDocumento->setDado('cod_documento',$_REQUEST["inCodTipoDocumento"]);
                $obTTipoDocumento->recuperaPorChave($rsTipoDocumento,$stFiltro);

                $arValores[$inCount]['stTipoDocumento'     ] = $rsTipoDocumento->getCampo("descricao" );

                $js = "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($nuTotalPrestacao,2,',','.')."';";
                $js.= "jq('#inVlPagoTMP').html('".number_format($nuTotalPrestacao,2,',','.')."');";
                $js.= "d.getElementById('stDtPrestacaoContas').readOnly = true;                                       ";
                $js.= 'jq("#stCtrl").val("inclir"); ';
                $js.= "f.inNroDocumento.focus();                                                                     ";
                $js.= "limpaDado();                                                                                   ";

                Sessao::write('arValores', $arValores);
                echo $js.montaListaPrestacaoContas( $arValores );

            } else {
                $mensagem = "O valor a prestar não pode ser zero.";
                echo"alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
            }
        } else {
            $mensagem = "Preencha todos os campos obrigatórios.";
            echo"alertaAviso('".$mensagem."','form','erro','".Sessao::getId()."');";
        }

    break;

    case 'excluir' :

        $arValores = Sessao::read('arValores');
        $arValoresTMP = array();
        $arItem = array();
        $inCountArray = 0;
        foreach ($arValores as $arItem) {
            if ($arItem['id'] != $_REQUEST['id']) {
                $arValoresTMP[$inCountArray]['id'] = $inCountArray + 1;
                $arValoresTMP[$inCountArray]['numItem'] = $inCountArray + 1;
                $arValoresTMP[$inCountArray]['stDtPrestacaoContas'] = $arItem['stDtPrestacaoContas'];
                $arValoresTMP[$inCountArray]['inCodTipoDocumento']  = $arItem['inCodTipoDocumento'];
                $arValoresTMP[$inCountArray]['stDataDocumento']     = $arItem['stDataDocumento'];
                $arValoresTMP[$inCountArray]['inNroDocumento']      = $arItem['inNroDocumento'];
                $arValoresTMP[$inCountArray]['inCodFornecedor']     = $arItem['inCodFornecedor'];
                $arValoresTMP[$inCountArray]['stNomCredor']         = $arItem['stNomCredor'];
                $arValoresTMP[$inCountArray]['stJustificativa']     = $arItem['stJustificativa'];
                $arValoresTMP[$inCountArray]['nuValor']             = $arItem['nuValor'];
                $arValoresTMP[$inCountArray]['exercicioConta']      = $arItem['exercicioConta'];
                $arValoresTMP[$inCountArray]['inCodContraPartida']  = $arItem['inCodContraPartida'];
                $arValoresTMP[$inCountArray]['inCodEmpenho']        = $arItem['inCodEmpenho'];
                $arValoresTMP[$inCountArray]['inCodEntidade']       = $arItem['inCodEntidade'];
                $arValoresTMP[$inCountArray]['stTipoDocumento']     = $arItem['stTipoDocumento'];
                $inCountArray++;
            }
        }

        foreach ($arValoresTMP as $arItem) {
            $nuTotalValor = bcadd($nuTotalValor,str_replace(',','.',str_replace('.','',$arItem['nuValor'])),2);
        }

        // Libera o campo data prestacao de contas se não haver prestações na lista.
        if ( count( $arValoresTMP ) == 0) {
            $js .= "d.getElementById('stDtPrestacaoContas').readOnly = false ;";
        }

        $js.= "f.stDataDocumento.focus();";
        $js.= "d.getElementById('flTotalPrestacaoContas').innerHTML = '".number_format($nuTotalValor,2,',','.')."';";
        $js.= "jq('#inVlPagoTMP').html('".number_format($nuTotalValor,2,',','.')."');";
        $js.= "limpaDado();  ";

        Sessao::write('arValores', $arValoresTMP);
        echo $js.montaListaPrestacaoContas( $arValoresTMP );

    break;

}

 function montaListaPrestacaoContas($arRecordSet , $boExecuta = true)
 {
    $stPrograma = "ManterAdiantamentosSubvencoes";
    $pgOcul     = "OC".$stPrograma.".php";

    $rsNotasFiscais = new RecordSet;
    if ($arRecordSet != '') {
        $rsNotasFiscais->preenche( $arRecordSet );
    }

    $obLista = new Lista;

    $obLista->setTitulo('Itens da Prestação de Contas');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsNotasFiscais );
    $obLista->addCabecalho();

    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data Emissão");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Tipo Docto");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Nr.");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Fornecedor");
    $obLista->ultimoCabecalho->setWidth( 30 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor");
    $obLista->ultimoCabecalho->setWidth( 10 );
    $obLista->commitCabecalho();

    $obLista->ultimoCabecalho->addConteudo("Ação");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stDataDocumento" );
    $obLista->ultimoDado->setTitle( "Nome" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stTipoDocumento" );
    $obLista->ultimoDado->setTitle( "Conta Lançamento" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "inNroDocumento" );
    $obLista->ultimoDado->setTitle( "Situação." );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "stNomCredor" );
    $obLista->ultimoDado->setTitle( "" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nuValor");
    $obLista->ultimoDado->setTitle( "" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterar');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluir');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaInnerHTML();
    $stHTML = $obLista->getHTML();

    if ($boExecuta) {
        return "d.getElementById('spnListaNotaFiscal').innerHTML = '".$stHTML."';";
    } else {
        return $stHTML;
    }
}

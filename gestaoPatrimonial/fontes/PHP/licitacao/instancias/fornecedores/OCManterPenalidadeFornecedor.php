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
    * Pagina de formulário para Incluir Penalidade a Fornecedor
    * Data de Criação   : 10/10/2006

    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @ignore

    * Casos de uso: uc-03.05.28

    $Id: OCManterPenalidadeFornecedor.php 59612 2014-09-02 12:00:51Z gelson $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterPenalidadeFornecedor";
$pgFilt       = "FL".$stPrograma.".php";
$pgList       = "LS".$stPrograma.".php";
$pgForm       = "FM".$stPrograma.".php";
$pgProc       = "PR".$stPrograma.".php";
$pgOcul       = "OC".$stPrograma.".php";
$pgJS         = "JS".$stPrograma.".js";

function montaListaPenalidade($arRecordSet, $stAcao = '')
{
    $rsPenalidade = new RecordSet;
    $rsPenalidade->preenche( $arRecordSet );

    $obLista = new Lista;
    $obLista->setTitulo('');
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsPenalidade );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Penalidade");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data de Publicação");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Data de Validade");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_penalidade]-[descricao]" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_publicacao" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "dt_validade" );
    $obLista->ultimoDado->setAlinhamento( 'CENTRO' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('alterarPenalidade');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirPenalidade');" );
    $obLista->ultimaAcao->addCampo("1","id");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs = "d.getElementById('spnPenalidade').innerHTML = '".$stHTML."';";

    return $stJs;
}

function limpaDadosFornecedor()
{
    $stJs  = "d.getElementById('stNumCertificacao').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('dtDataRegistro').innerHTML = '&nbsp;';\n";
    $stJs .= "d.getElementById('dtDataVigencia').innerHTML = '&nbsp;';\n";
    $stJs .= "f.inCertificacao.value = '';\n";

    return $stJs;
}

function limpaDadosPenalidade()
{
    $stJs = "limpaFormularioPenalidade();\n";
}

function bloquearFornecedor($acao = '')
{
    if ($acao == 'bloquear') {
        $stJs .= "f.inCodFornecedor.readonly = true;\n";
        //$stJs .= "d.getElementById('imgFornecedor').style.display = 'none';\n";
        //$stJs .= "f.stCodDocumentoTxt.readonly = true;\n";
        //$stJs .= "f.stCodDocumento.readonly = true;\n";
    } elseif ($acao == 'liberar') {
        $stJs .= "f.inCodFornecedor.readonly = false;\n";
        //$stJs .= "d.getElementById('imgFornecedor').style.display = 'inline';\n";
        //$stJs .= "f.stCodDocumentoTxt.readonly = false;\n";
        //$stJs .= "f.stCodDocumento.readonly = false;\n";
    }

    return $stJs;
}

function bloquearDocumento($acao = '')
{
    if ($acao == 'bloquear') {
        //$stJs .= "f.stCodDocumentoTxt.readonly = true;\n";
        //$stJs .= "f.stCodDocumento.readonly = true;\n";
    } elseif ($acao == 'liberar') {
        //$stJs .= "f.stCodDocumentoTxt.readonly = false;\n";
        //$stJs .= "f.stCodDocumento.readonly = false;\n";
    }
}

switch ($_REQUEST['stCtrl']) {
    case 'listarCertificacao':
            if ($_REQUEST['inCodFornecedor']) {

                $arPen = Sessao::read('arPen');

                include_once ( TLIC."TLicitacaoParticipanteCertificacao.class.php" );
                $obLicitacaoParticipanteCertificacao = new TLicitacaoParticipanteCertificacao();
                $obLicitacaoParticipanteCertificacao->setDado( 'cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
                $obLicitacaoParticipanteCertificacao->setDado( 'exercicio', Sessao::getExercicio() );
                $obLicitacaoParticipanteCertificacao->recuperaListaCertificacao( $rsListaCertificacao );

                if ( $rsListaCertificacao->getNumLinhas() > 0 ) {

                        $stNumCertificacao = str_pad($rsListaCertificacao->getCampo('num_certificacao'), 6, "0", STR_PAD_LEFT).'/'.$rsListaCertificacao->getCampo('exercicio');

                        $stJs  = "d.getElementById('stNumCertificacao').innerHTML = '".$stNumCertificacao."';\n";
                        $stJs .= "d.getElementById('dtDataRegistro').innerHTML = '".$rsListaCertificacao->getCampo('dt_registro')."';\n";
                        $stJs .= "d.getElementById('dtDataVigencia').innerHTML = '".$rsListaCertificacao->getCampo('final_vigencia')."';\n";
                        $stJs .= "f.inCertificacao.value = '".$rsListaCertificacao->getCampo('num_certificacao')."';\n";
                        $stJs .= "f.stExercicio.value = '".$rsListaCertificacao->getCampo('exercicio')."';\n";

                        /* Inicio */
                        include_once ( TLIC."TLicitacaoPenalidade.class.php"              );
                        include_once ( TLIC."TLicitacaoPenalidadesCertificacao.class.php" );

                        $rsPenalidade             = new RecordSet();
                        $rsPenalidadeCertificacao = new RecordSet();

                        $obLicitacaoPenalidade               = new TLicitacaoPenalidade();
                        $obTLicitacaoPenalidadesCertificacao = new TLicitacaoPenalidadesCertificacao();

                        $obTLicitacaoPenalidadesCertificacao->setDado('num_certificacao',$rsListaCertificacao->getCampo('num_certificacao'));
                        $obTLicitacaoPenalidadesCertificacao->setDado('cgm_fornecedor'  ,$rsListaCertificacao->getCampo('cgm_fornecedor'));
                        $obTLicitacaoPenalidadesCertificacao->setDado('exercicio'       ,$rsListaCertificacao->getCampo('exercicio'));
                        $obTLicitacaoPenalidadesCertificacao->recuperaPorChave($rsPenalidadeCertificacao);
                        //          $obTLicitacaoPenalidadesCertificacao->debug();
                        $inCount = 0;
                        while (!($rsPenalidadeCertificacao->EOF())) {
                                $arPen[$inCount]['id'              ]=$inCount+1;
                                $arPen[$inCount]['cod_penalidade'  ]=$rsPenalidadeCertificacao->getCampo('cod_penalidade'  );
                                $arPen[$inCount]['dt_publicacao'   ]=$rsPenalidadeCertificacao->getCampo('dt_publicacao'   );
                                $arPen[$inCount]['dt_validade'     ]=$rsPenalidadeCertificacao->getCampo('dt_validade'     );
                                $arPen[$inCount]['cgm_fornecedor'  ]=$rsPenalidadeCertificacao->getCampo('cgm_fornecedor'  );
                                $arPen[$inCount]['num_certificacao']=$rsPenalidadeCertificacao->getCampo('num_certificacao');
                                $arPen[$inCount]['observacao'      ]=$rsPenalidadeCertificacao->getCampo('observacao'      );
                                $arPen[$inCount]['valor'           ]=$rsPenalidadeCertificacao->getCampo('valor'           );
                                $arPen[$inCount]['ano_exercicio'   ]=$rsPenalidadeCertificacao->getCampo('ano_exercicio'   );
                                $arPen[$inCount]['processo'        ]=$rsPenalidadeCertificacao->getCampo('cod_processo'    );
                                $obLicitacaoPenalidade->setDado( 'cod_penalidade',$rsPenalidadeCertificacao->getCampo('cod_penalidade'));
                                $obLicitacaoPenalidade->recuperaPorChave( $rsPenalidade );
                                $arPen[$inCount]['descricao'       ]=$rsPenalidade->getCampo('descricao');
                                $inCount++;
                                $rsPenalidadeCertificacao->proximo();
                        }
                        Sessao::write('arPen', $arPen);
                        $stJs.= montaListaPenalidade( $arPen );
                        /* Fim */
                        } else {
                            ///$stJs .= "f.stNomFornecedor.value = ''; \n ";
                            $stJs.= "document.getElementById('stNomFornecedor').innerHTML = '&nbsp;' ; \n ";
                            $stJs.= "alertaAviso('Fornecedor sem certificação (".$_REQUEST['inCodFornecedor'].").','form','erro','".Sessao::getId()."');";
                            $stJs.= limpaDadosFornecedor();
                            $stJs.= montaListaPenalidade( Sessao::read('arPen') );
                        }
                    } else {
                        $stJs = limpaDadosFornecedor();
                    }
        break;

    case 'mostraValor':
        if ($_REQUEST['inPenalidade'] == 2) {
            $obTxtValor = new Moeda();
            $obTxtValor->setName  ( "flValor" );
            $obTxtValor->setRotulo( "*Valor" );
            $obTxtValor->setTitle ( "Informe o valor da multa aplicada." );
            $obTxtValor->setValue ( $flValor );
            $obTxtValor->setNull(false);

            $obFormulario = new Formulario();
            $obFormulario->addComponente( $obTxtValor );
            $obFormulario->montaInnerHTML();
            $stHTML = $obFormulario->getHTML();

            $stJs  = "d.getElementById('spnValor').innerHTML = '".$stHTML."';\n";
            $stJs .= "f.flValor.focus();\n";
        } else {
            $stJs = "d.getElementById('spnValor').innerHTML = '';\n";
        }
        break;

    case 'incluirPenalidade':

            $arPen = Sessao::read('arPen');

            if ($_REQUEST['inCodFornecedor']) {
                foreach ($arPen as $chave => $valor) {
                    if(( $_REQUEST['inCodFornecedor'] == $valor['cgm_fornecedor'] ) &&
                    ( $_REQUEST['inCertificacao'] == $valor['num_certificacao']  ) &&
                    ( $_REQUEST['inPenalidade'] == $valor['cod_penalidade'] ) &&
                    ( $_REQUEST['stAcaoSessao'] != 'alterar')){
                            $stMensagem = "Já existe Penalidade(".$_REQUEST['inPenalidade'].") para esta certificação.";
                            break;
                    }
                }

                include_once( CAM_GA_PROT_MAPEAMENTO.'TProcesso.class.php');

                $arProcesso = explode('/',$_REQUEST['stChaveProcesso']);

                $obTProcesso = new TProcesso();
                $obTProcesso->setDado('cod_processo',$arProcesso[0]);
                $obTProcesso->setDado('ano_exercicio',$arProcesso[1]);
                $obTProcesso->recuperaPorChave( $rsProcesso );

                if ( $rsProcesso->getNumLinhas() == -1 ) {
                    $stMensagem = 'Número do processo inválido('.$_REQUEST['stChaveProcesso'].').';
                }

                if (!$stMensagem) {
                    if (!(SistemaLegado::comparaDatas( $_REQUEST['dtDataValidade']    ,date("d/m/Y")))) {
                        $stMensagem = "A data de validade deve ser posterior a data atual.";
                    } else {
                        if ($_REQUEST['stAcaoSessao'] == 'alterar') {
                            $inCount = $_REQUEST['id']-1;
                        } else {
                            $inCount = count($arPen);
                        }

                        include_once ( TLIC."TLicitacaoPenalidade.class.php" );
                        $obLicitacaoPenalidade = new TLicitacaoPenalidade();
                        $obLicitacaoPenalidade->setDado( 'cod_penalidade', $_REQUEST['inPenalidade'] );
                        $obLicitacaoPenalidade->recuperaPorChave( $rsPenalidade );

                        /*Sessao::write("arPen[".$inCount."]['id']", $inCount+1);
                        Sessao::write("arPen[".$inCount."]['cod_penalidade']", $_REQUEST['inPenalidade']);
                        Sessao::write("arPen[".$inCount."]['dt_publicacao']", $_REQUEST['dtDataPublicacao']);
                        Sessao::write("arPen[".$inCount."]['dt_validade']", $_REQUEST['dtDataValidade']);
                        Sessao::write("arPen[".$inCount."]['cgm_fornecedor']", $_REQUEST['inCodFornecedor']);
                        Sessao::write("arPen[".$inCount."]['num_certificacao']", $_REQUEST['inCertificacao']);
                        Sessao::write("arPen[".$inCount."]['observacao']", $_REQUEST['stObservacao']);
                        Sessao::write("arPen[".$inCount."]['descricao']", $rsPenalidade->getCampo('descricao'));
                        if ($_REQUEST['flValor']) {
                            Sessao::write("arPen[".$inCount."]['valor']", $_REQUEST['flValor']);
                        } else {
                            Sessao::write("arPen[".$inCount."]['valor']", '');
                        }
                        $arProcesso = explode('/',$_REQUEST['stChaveProcesso']);
                        Sessao::write("arPen[".$inCount."]['processo']", $arProcesso[0]);
                        Sessao::write("arPen[".$inCount."]['ano_exercicio']", $arProcesso[1]);

                        $stJs .= montaListaPenalidade( Sessao::read('arPen') );*/

                        $arPen[$inCount]['id'] = $inCount+1;
                        $arPen[$inCount]['cod_penalidade'] = $_REQUEST['inPenalidade'];
                        $arPen[$inCount]['dt_publicacao'] = $_REQUEST['dtDataPublicacao'];
                        $arPen[$inCount]['dt_validade'] = $_REQUEST['dtDataValidade'];
                        $arPen[$inCount]['cgm_fornecedor'] = $_REQUEST['inCodFornecedor'];
                        $arPen[$inCount]['num_certificacao'] = $_REQUEST['inCertificacao'];
                        $arPen[$inCount]['observacao'] = $_REQUEST['stObservacao'];
                        $arPen[$inCount]['descricao'] = $rsPenalidade->getCampo('descricao');
                        if ($_REQUEST['flValor']) {
                            $arPen[$inCount]['valor'] = $_REQUEST['flValor'];
                        } else {
                            $arPen[$inCount]['valor'] = '';
                        }
                        $arProcesso = explode('/',$_REQUEST['stChaveProcesso']);
                        $arPen[$inCount]['processo'] = $arProcesso[0];
                        $arPen[$inCount]['ano_exercicio'] = $arProcesso[1];

                        Sessao::write('arPen',$arPen);

                        $stJs .= montaListaPenalidade( $arPen );

                        $stJs .= "limpaFormularioPenalidade();                 \n";
                        $stJs .= "f.stAcaoSessao.value = '';                   \n";
                        $stJs .= "d.getElementById('spnValor').innerHTML = ''; \n";
                        $stJs .= "f.btIncluirPenalidade.value = 'Incluir';     \n";
                        if ($stAcao == 'incluir') {
                                $stJs .= bloquearFornecedor( 'bloquear' );
                        } else {
                                $stJs .= bloquearDocumento( 'bloquear' );
                        }
                    }
                }
            } else {
                $stMensagem = "É necessário selecionar um fornecedor.";
            }

            if ($stMensagem) {
                $stJs .= "alertaAviso('".$stMensagem."','form','erro','".Sessao::getId()."');";
            }
        break;

    case 'alterarPenalidade':
        $arPen = Sessao::read('arPen');

            foreach ($arPen as $key => $value) {
                if ($_REQUEST['id'] == $value['id']) {
                    $stJs  = "f.inPenalidade.value        = '".$value['cod_penalidade']."'; \n";
                    $stJs .= "f.stPenalidade.value        = '".$value['cod_penalidade']."'; \n";
                    $stJs .= "f.btIncluirPenalidade.value = 'Alterar';                      \n";

                    if ( floatval($value['valor']) > 0 ) {
                        $obTxtValor = new Moeda();
                        $obTxtValor->setName  ( "flValor" );
                        $obTxtValor->setRotulo( "*Valor" );
                        $obTxtValor->setTitle ( "Informe o valor da multa aplicada." );
                        $obTxtValor->setValue ( $flValor );
                        $obTxtValor->setNull(false);

                        $obFormulario = new Formulario();
                        $obFormulario->addComponente( $obTxtValor );
                        $obFormulario->montaInnerHTML();
                        $stHTML = $obFormulario->getHTML();

                        $stJs .= "d.getElementById('spnValor').innerHTML = '".$stHTML."';\n";

                        $stJs .= "f.flValor.value = '".$value['valor']."';\n";
                    } else {
                        $stJs .= "d.getElementById('spnValor').innerHTML = '';\n";
                    }
                    $stJs .= "f.dtDataPublicacao.value = '".$value['dt_publicacao']."';\n";
                    $stJs .= "f.dtDataValidade.value  = '".$value['dt_validade']."';\n";
                    $stJs .= "f.stObservacao.value = '".$value['observacao']."';\n";
                    $stJs .= "f.stChaveProcesso.value = '".$value['processo']."/".$value['ano_exercicio']."';\n";
                    $stJs .= "preencheProcessoComZeros( f.stChaveProcesso.value, '99999/9999','".Sessao::getExercicio()."');\n";
                }
            }
            $stJs .= "f.stAcaoSessao.value = 'alterar';\n";
            $stJs .= "f.id.value = ".$_REQUEST['id'].";\n";
        break;

    case 'excluirPenalidade':
        $arTemp  = array();
        $arTemp2 = array();

        $inCount  = 0;
        $inCount2 = 0;

        foreach ( Sessao::read('arPen') as $key => $value ) {
            if ($_REQUEST['id'] != $value['id']) {
                $arTemp[$inCount]['id'              ] = $inCount+1;
                $arTemp[$inCount]['cod_penalidade'  ] = $value['cod_penalidade'  ];
                $arTemp[$inCount]['dt_publicacao'   ] = $value['dt_publicacao'   ];
                $arTemp[$inCount]['dt_validade'     ] = $value['dt_validade'     ];
                $arTemp[$inCount]['cgm_fornecedor'  ] = $value['cgm_fornecedor'  ];
                $arTemp[$inCount]['num_certificacao'] = $value['num_certificacao'];
                $arTemp[$inCount]['observacao'      ] = $value['observacao'      ];
                $arTemp[$inCount]['descricao'       ] = $value['descricao'       ];
                $arTemp[$inCount]['valor'           ] = $value['valor'           ];
                $arTemp[$inCount]['processo'        ] = $value['processo'        ];
                $arTemp[$inCount]['ano_exercicio'   ] = $value['ano_exercicio'   ];
                $inCount++;
            } else {
                $arTemp2[$inCount2]['id'              ] = $inCount2+1;
                $arTemp2[$inCount2]['cod_penalidade'  ] = $value['cod_penalidade'  ];
                $arTemp2[$inCount2]['dt_publicacao'   ] = $value['dt_publicacao'   ];
                $arTemp2[$inCount2]['dt_validade'     ] = $value['dt_validade'     ];
                $arTemp2[$inCount2]['cgm_fornecedor'  ] = $value['cgm_fornecedor'  ];
                $arTemp2[$inCount2]['num_certificacao'] = $value['num_certificacao'];
                $arTemp2[$inCount2]['observacao'      ] = $value['observacao'      ];
                $arTemp2[$inCount2]['descricao'       ] = $value['descricao'       ];
                $arTemp2[$inCount2]['valor'           ] = $value['valor'           ];
                $arTemp2[$inCount2]['processo'        ] = $value['processo'        ];
                $arTemp2[$inCount2]['ano_exercicio'   ] = $value['ano_exercicio'   ];
                $inCount2++;
            }
        }
        Sessao::write('arPen', $arTemp);

        // Não achei nenhuma utilização para o transf2 no arquivo.
                #sessao->transf2['arPen'] = $arTemp2;

        $stJs  .= montaListaPenalidade( $arTemp );
        if ( count(Sessao::read('arPen')) == 0 ) {
            $stJs .= bloquearFornecedor( 'liberar' );
        }
        break;

    case 'limpar':
        Sessao::write('arPen', array());
    break;

    case 'montaAlteracao':
        if ($_REQUEST['inCertificacao'] && $_REQUEST['stExercicio'] && $_REQUEST['inCodFornecedor']) {
            $arPen = array();
            $inCount = 0;
            include_once( TLIC."TLicitacaoPenalidadesCertificacao.class.php" );
            $obTLicitacaoPenalidadesCertificacao = new TLicitacaoPenalidadesCertificacao();
            $obTLicitacaoPenalidadesCertificacao->setDado('num_certificacao', intval($_REQUEST['inCertificacao']) );
            $obTLicitacaoPenalidadesCertificacao->setDado('exercicio', $_REQUEST['stExercicio'] );
            $obTLicitacaoPenalidadesCertificacao->setDado('cgm_fornecedor', $_REQUEST['inCodFornecedor'] );
            $obTLicitacaoPenalidadesCertificacao->recuperaListaPenalidadeFornecedor( $rsPenalidades );
            while ( !$rsPenalidades->eof() ) {
                $arPen[$inCount]['id'] = $inCount+1;
                $arPen[$inCount]['cod_penalidade'] = $rsPenalidades->getCampo('cod_penalidade');
                $arPen[$inCount]['dt_publicacao'] = $rsPenalidades->getCampo('dt_publicacao');
                $arPen[$inCount]['dt_validade'] = $rsPenalidades->getCampo('dt_validade');
                $arPen[$inCount]['cgm_fornecedor'] = $rsPenalidades->getCampo('cgm_fornecedor');
                $arPen[$inCount]['num_certificacao'] = $rsPenalidades->getCampo('num_certificacao');
                $arPen[$inCount]['observacao'] = $rsPenalidades->getCampo('observacao');
                $arPen[$inCount]['descricao'] = $rsPenalidades->getCampo('descricao');
                $arPen[$inCount]['valor'] = $rsPenalidades->getCampo('valor');
                $arPen[$inCount]['processo'] = $rsPenalidades->getCampo('cod_processo');
                $arPen[$inCount]['ano_exercicio'] = $rsPenalidades->getCampo('ano_exercicio');
                $rsPenalidades->proximo();
                $inCount++;
            }
            Sessao::write('arPen', $arPen);
            $stJs  = montaListaPenalidade( $arPen );
            //$stJs .= "f.stCodDocumento.value = ".$_REQUEST['stCodDocumento'].";\n";
            //$stJs .= "f.stCodDocumentoTxt.value = ".$_REQUEST['stCodDocumento'].";\n";
            //$stJs .= "f.inCodTipoDocumento.value = ".$_REQUEST['inCodTipoDocumento'].";\n";

            Sessao::getExercicio();
        }
        break;
}
echo $stJs;

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
    * Paginae Oculta para funcionalidade Manter Arrecadacao
    * Data de Criação   : 21/10/2005

    * @author Analista: Lucas Leusin
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @ignore

    $Revision: 18920 $
    $Name$
    $Autor:$
    $Date: 2006-12-21 11:07:06 -0200 (Qui, 21 Dez 2006) $

    * Casos de uso: uc-02.04.04

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_TES_NEGOCIO."RTesourariaBoletim.class.php";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CLA_IAPPLETTERMINAL;

//Define o nome dos arquivos PHP
$stPrograma = "ManterArrecadacaoReceita";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stCtrl = $_REQUEST['stCtrl'];

$obRTesourariaBoletim = new RTesourariaBoletim();
$obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );

function montaLista($arRecordSet, $boExecuta = true)
{
    $rsLista = new RecordSet;
    $rsLista->preenche( $arRecordSet );

    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setRecordSet( $rsLista );
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Carnê");
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Reduzido Banco");
    $obLista->ultimoCabecalho->setWidth( 12 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 51 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Valor Total");
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();
    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth(  5 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_receita" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "cod_plano" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "nom_conta" );
    $obLista->ultimoDado->setAlinhamento( 'ESQUERDA' );
    $obLista->commitDado();
    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "vl_total" );
    $obLista->ultimoDado->setAlinhamento( 'DIREITA' );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( 'EXCLUIR' );
    $obLista->ultimaAcao->setFuncao( true );
    $obLista->ultimaAcao->setLink( "JavaScript:excluirItem('excluirItem');" );
    $obLista->ultimaAcao->addCampo( "1", "exercicio" );
    $obLista->ultimaAcao->addCampo( "2", "cod_receita" );
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    $stJs .= "d.getElementById('spnLista').innerHTML = '".$stHTML."';";

    if (!$boExecuta) {
        return $stJs;
    }
}

switch ($stCtrl) {
    case 'montaLista':
        //SistemaLegado::executaFrameOculto( $stJs );
    break;
    case 'alteraBoletim':
        $obRTesourariaBoletim = new RTesourariaBoletim();
        list( $inCodBoletim , $stDataBoletim ) = explode ( ':' , $_REQUEST['inCodBoletim'] );
        $obRTesourariaBoletim->setCodBoletim ( $inCodBoletim );
        $obRTesourariaBoletim->setExercicio( Sessao::getExercicio() );
        $obRTesourariaBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodEntidade'] );
        $obErro = $obRTesourariaBoletim->listarBoletimAberto ( $rsBoletimAberto );

        if ( !$obErro->ocorreu() && $rsBoletimAberto->getNumLinhas() == 1 ) {
            $stJs  = "f.inCodBoletim.value = '" . $rsBoletimAberto->getCampo( 'cod_boletim' ) . "';\r\n";
            $stJs .= "f.stDtBoletim.value = '" . $rsBoletimAberto->getCampo( 'dt_boletim' ) . "';\r\n";
            $stJs .= "LiberaFrames(true,false);".$stJs;
        } else {
            $stJs  = "f.inCodBoletim.value = '';\r\n";
            $stJs .= "f.stDtBoletim.value = '';\r\n";
            $stJs .= "LiberaFrames(true,false);".$stJs;
        }

    break;
    case 'buscaBoletim':
        if ($_REQUEST['inCodigoEntidade']) {
            require_once( CAM_GF_TES_COMPONENTES . 'ISelectBoletim.class.php' );
            include_once CAM_GF_TES_COMPONENTES . 'ISaldoCaixa.class.php';
            $obISelectBoletim = new ISelectBoletim;
            $obISelectBoletim->obBoletim->obROrcamentoEntidade->setCodigoEntidade( $_REQUEST['inCodigoEntidade']  );
            $obISelectBoletim->obBoletim->setExercicio( Sessao::getExercicio() );
            $obISelectBoletim->obEvento->setOnChange ( "montaParametrosGET('alteraBoletim');");

            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obISelectBoletim );
            $obFormulario->montaInnerHtml();
            $stHTML = $obFormulario->getHTML();
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\'","\\'",$stHTML );

            $ISaldoCaixa = new ISaldoCaixa();
            $ISaldoCaixa->inCodEntidade = $_REQUEST['inCodigoEntidade'];
            $stJs = $ISaldoCaixa->montaSaldo();

            $stJs .= "$('spnBoletim').innerHTML = '".$stHTML."';\r\n";
            if ($_REQUEST['inCodBoletim']) {
                $obISelectBoletim->obBoletim->listarBoletimAberto($rsBoletim);
                if ($rsBoletim->getNumLinhas() > 0) {
                    $stJs .= "$('inCodBoletim').value = '".$_REQUEST['inCodBoletim']."'; ";
                }
            }
            $stJs .= "jq('#stNomReceita').parent().parent().closest('tr').show() ;";
            $stJs .= "jq('#stNomConta').parent().parent().closest('tr').show();";
            $stJs .= "jq('#inCodEntidade').val(".$_REQUEST['inCodigoEntidade'].");";
            $stJs .= "jq('#inCodigoEntidade').attr('disabled',true);";

        }
    break;

    case 'incluirItem':
        include_once( CAM_GF_CONT_NEGOCIO."RContabilidadeDesdobramentoReceita.class.php" );
        $obRTesourariaBoletim->addArrecadacao();
        $obRContabilidadeDesdobramentoReceita = new RContabilidadeDesdobramentoReceita( $obRTesourariaBoletim->roUltimaArrecadacao->obROrcamentoReceita );
        $arItens = Sessao::read('arItens');
        $inCount = count($arItens);
        if ($inCount > 0) {
            foreach ($arItens as $arTemp) {
                if ($_POST['inCodReceita'] == $arTemp['cod_receita']) {
                    SistemaLegado::exibeAviso("Esta receita já está na lista!( ".$_POST['inCodReceita']." )","n_estornar","erro");
                    die;
                }
            }
        }

        $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setCodReceita( $_POST['inCodReceita']);
        $obRContabilidadeDesdobramentoReceita->roROrcamentoReceitaPrincipal->setExercicio ( Sessao::getExercicio() );
        $obErro = $obRContabilidadeDesdobramentoReceita->verificaReceitaSecundaria( $rsReceita );
        if ( !$obErro->ocorreu() ) {
            if ( !$rsReceita->eof() ) {
               SistemaLegado::exibeAviso("Esta receita é secundária!( ".$_POST['inCodReceita']." )","n_estornar","erro");
               die;
            }

            $arItens[$inCount]['cod_receita']  = $_POST['inCodReceita'];
            $arItens[$inCount]['exercicio']    = Sessao::getExercicio();
            $arItens[$inCount]['cod_plano']    = $_POST['inCodPlano'];
            $arItens[$inCount]['nom_conta']    = $_POST['stNomConta'];
            $arItens[$inCount]['vl_total']     = $_POST['nuValor'];
            $arItens[$inCount]['observacao']   = $_POST['stObservacoes'];
        }
        Sessao::write('arItens',$arItens);
        $stJs = montaLista( Sessao::read('arItens'), false );
        $stJs .= $stJs." somaValorGeral( '".$_POST['nuValor']."' ); limparItem();";

    break;

    case 'excluirItem':
        $arItens = Sessao::read('arItens');
        if ( count($arItens) > 0 ) {
            $inCount    = 0;
            $arItensTMP = array();
            foreach ($arItens as $arValue) {
                if ($_GET['inCodReceita'] != $arValue['cod_receita']) {
                    $arItensTMP[$inCount]['cod_receita'] = $arValue['cod_receita'];
                    $arItensTMP[$inCount]['exercicio'  ] = $arValue['exercicio'  ];
                    $arItensTMP[$inCount]['cod_plano'  ] = $arValue['cod_plano'  ];
                    $arItensTMP[$inCount]['nom_conta'  ] = $arValue['nom_conta'  ];
                    $arItensTMP[$inCount]['vl_total'   ] = $arValue['vl_total'   ];
                    $arItensTMP[$inCount]['observacao' ] = $arValue['observacao' ];
                    $inCount++;
                } else {
                    $nuVlSubtrai = str_replace( '.', '' , $arItens['vl_total'] );
                    $nuVlSubtrai = str_replace( ',', '.', $nuVlSubtrai );
                    $nuVlSubtrai = number_format( $nuVlSubtrai*(-1), 2, ',', '.' );
                }
            }
            Sessao::write('arItens', $arItensTMP);
            $stJs  = montaLista( Sessao::read('arItens'), false );
            $stJs .= "somaValorGeral( '".$nuVlSubtrai."' );";
            //SistemaLegado::executaFrameOculto( $stJs );
        }
    break;

    case 'montaTipo':

        $obErro = new Erro();

        //Define objeto formulario
        $obFormulario = new Formulario();

        //De acordo com o que for selecionado no formulario, monta o formulario alternativo
        $rsReceita = new RecordSet();
        $stJs .= "setLabel('nuValor',false);";
        $stJs .= "$('stCodBarraOtico').disabled = false;";
        $stJs .= "$('stCodBarraManual').disabled = false;";
        $stJs .= "$('inCodReceita').disabled = false;";
        $stJs .= "$('imgReceita').style.display='inline';";
        $stJs .= "$('nuValor_label').innerHTML = '';";
        $stJs .= "$('nuValor').value = '';";
        if ( ($_REQUEST['inCodReceita'] != '') AND ($_REQUEST['inCodEntidade'] != '')) {
            include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
            $obTOrcamentoReceita = new TOrcamentoReceita();
            $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'";
            $stFiltro .= " AND RECEITA.cod_receita = ".$_REQUEST['inCodReceita']." ";
            $stFiltro .= " AND RECEITA.cod_entidade = ".$_REQUEST['inCodEntidade']." ";
            $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                               FROM contabilidade.desdobramento_receita as dr
                                              WHERE receita.cod_receita = dr.cod_receita_secundaria
                                                AND receita.exercicio   = dr.exercicio ) ";

            if ( Sessao::getExercicio() > '2012' ) {
                $stFiltro .= "AND CLR.estorno = 'f'";
                $obTOrcamentoReceita->recuperaReceitaAnaliticaTCE($rsReceita, $stFiltro);
            } else {
                $obTOrcamentoReceita->recuperaReceitaAnalitica($rsReceita, $stFiltro);
            }
            if ( $rsReceita->getNumLinhas() == 1 ) {
                //Buscainner para as receitas
                $obBuscaReceitaDeducao =  new BuscaInner ;
                $obBuscaReceitaDeducao->setRotulo ( "Conta Dedução"              );
                $obBuscaReceitaDeducao->setTitle  ( "Informe o Reduzido da Conta Dedução.");
                $obBuscaReceitaDeducao->setId     ( "stNomReceitaDeducao"               );
                $obBuscaReceitaDeducao->setValue  ( $stNomReceitaDeducao                );
                $obBuscaReceitaDeducao->obCampoCod->setName     ( "inCodReceitaDeducao" );
                $obBuscaReceitaDeducao->obCampoCod->setSize     ( 10              );
                $obBuscaReceitaDeducao->obCampoCod->setNull     ( true            );
                $obBuscaReceitaDeducao->obCampoCod->setMaxLength( 8               );
                $obBuscaReceitaDeducao->obCampoCod->setValue    ( $inCodReceitaDeducao  );
                $obBuscaReceitaDeducao->obCampoCod->setAlign    ( "left"          );
                $obBuscaReceitaDeducao->setFuncaoBusca("abrePopUp('".CAM_GF_CONT_POPUPS."receita/FLReceita.php','frm','inCodReceitaDeducao','stNomReceitaDeducao','receitaDeducaoArrec&inCodEntidade='+document.frm.inCodEntidade.value,'".Sessao::getId()."','800','550');");
                $obBuscaReceitaDeducao->setValoresBusca(CAM_GF_CONT_POPUPS.'receita/OCReceita.php?'.Sessao::getId(),'frm','receitaDeducaoArrec');

                //Valor da deducao da arrecadacao
                $obTxtValorDeducao = new Numerico();
                $obTxtValorDeducao->setRotulo   ("Valor Dedução"   );
                $obTxtValorDeducao->setTitle    ("Informe o Valor.");
                $obTxtValorDeducao->setName     ("nuValorDeducao"  );
                $obTxtValorDeducao->setId       ("nuValorDeducao"  );
                $obTxtValorDeducao->setDecimais (2                 );
                $obTxtValorDeducao->setNegativo (false             );
                $obTxtValorDeducao->setSize     (17                );
                $obTxtValorDeducao->setMaxLength(17                );
                $obTxtValorDeducao->setMinValue (0.01              );

                //Adiciona os componentes ao formulario
                $obFormulario->addComponente($obBuscaReceitaDeducao);
                $obFormulario->addComponente($obTxtValorDeducao);

                //Bloqueia os outros campos de codigo de barra
                $stJs  = "$('stCodBarraOtico').disabled = true;";
                $stJs .= "$('stCodBarraManual').disabled = true;";
                //Muda de label para text o valor a ser arrecadado
                $stJs .= "setLabel('nuValor',true);";
                $stJs .= "$('nuValor').value = '0,00';";

            }
        } elseif ( ($_REQUEST['stCodBarraOtico'] != '') OR ($_REQUEST['stCodBarraManual'] !='') ) {
            if ( str_replace(' ','',$_REQUEST['stCodBarraOtico']) != '' && strlen(str_replace(' ','',$_REQUEST['stCodBarraOtico'])) != 44 ) {
                $obErro->setDescricao('Código de Barras inválido');
            } elseif ( $_REQUEST['stCodBarraManual'] != '' && strlen($_REQUEST['stCodBarraManual']) != 55 ) {
                $obErro->setDescricao('Código de Barras inválido');
            }

            if ($_REQUEST['stCodBarraManual'] != '') {
                //Se foi digitado manualmente, verifica os digitos verificadores
                $arCodBarra = explode(' ',$_REQUEST['stCodBarraManual']);

                //Regra para calcular o digito verificador
                $stCodBarra = $arCodBarra[0].$arCodBarra[2].$arCodBarra[4].$arCodBarra[6];

                for ($i=0;$i<=6;$i+=2) {
                    unset($stDigitosMultiplicados);
                    unset($inSomaDigitos);
                    for ($j=0;$j<strlen($arCodBarra[$i]);$j++) {
                        $inMultiplicador = (($j%2) == 0) ? 2 : 1;
                        $stDigitosMultiplicados .= $arCodBarra[$i][$j]*$inMultiplicador;
                    }
                    for ($k=0;$k<strlen($stDigitosMultiplicados);$k++) {
                        $inSomaDigitos += $stDigitosMultiplicados[$k];
                    }

                    $inDigitoVerificador = (($inSomaDigitos%10)==0) ? 0 : (10-($inSomaDigitos%10));

                    if ($inDigitoVerificador != $arCodBarra[$i+1]) {
                        $obErro->setDescricao('Código de Barras inválido');
                    }
                }
            } else {
                //Se foi via leitor otico
                $stCodBarra = $_REQUEST['stCodBarraOtico'];
            }

            $stNumeracao = substr($stCodBarra,-17);

            if ( !$obErro->ocorreu() ) {
                include_once( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
                $obRARRCarne = new RARRCarne ();
                $obRARRCarne->setNumeracao   ($stNumeracao);
                $obRARRCarne->setExercicio   (Sessao::getExercicio());
                $obRARRCarne->listarDetalheCreditosBaixa( $rsConsultaCredito, $boTransacao, date('Y-m-d') );

                //Verifica se o carne e do tipo cota unica
                if (substr($stCodBarra,0,3) == '816') {
                    //Se for, verifica se aceita pagar cota unica apos o vencimento
                    $stFiltro = "
                        WHERE cod_modulo = 25
                          AND exercicio  = 2008
                          AND parametro  = 'baixa_manual_unica'
                    ";
                    $stBaixaManualUnica = SistemaLegado::pegaDado('valor','administracao.configuracao',$stFiltro);
                    if ($stBaixaManualUnica == '' OR $stBaixaManualUnica == 'nao') {
                        if (str_replace('-','',$rsConsultaCredito->getCampo('vencimento')) < date('Ymd')) {
                            $obErro->setDescricao('O carnê informado não pode ser pago após o vencimento');
                        }
                    }
                }

                //Verifica se ja nao foi dado baixa no carne
                if ( !$obErro->ocorreu() ) {
                    $obRARRCarne->verificaPagamento($rsPagamento,$boTransacao);
                    if ( $rsPagamento->getNumLinhas() > 0 ) {
                        $obErro->setDescricao('O carnê informado já está pago');
                    }
                }

                //Verifica se o carne existe no sistema
                if ( !$obErro->ocorreu() ) {

                    if ( $rsConsultaCredito->getNumLinhas() == 0 ) {
                        $obErro->setDescricao('Código de Barras inválido');
                    }
                }

                //Verifica se todas os creditos do carne estao vinculados com uma receita
                if ( !$obErro->ocorreu() ) {
                    include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoVerificaReceitaCredito.class.php" );
                    $obFOrcamentoVerificaReceitaCredito = new FOrcamentoVerificaReceitaCredito();
                    $obFOrcamentoVerificaReceitaCredito->setDado('exercicio',Sessao::getExercicio());
                    $obFOrcamentoVerificaReceitaCredito->setDado('numeracao',$stNumeracao);
                    $obFOrcamentoVerificaReceitaCredito->executaFuncao($rsReceitaCredito);

                    if ( $rsReceitaCredito->getCampo('vinculado') == 'f' ) {
                        $obErro->setDescricao('Existem créditos não vinculados no carnê');
                    }

                }

                if ( !$obErro->ocorreu() ) {

                    $arCarne = array();
                    $arReceitas = array();

                    $arElementos = $rsConsultaCredito->getElementos();

                    while ( !$rsConsultaCredito->eof() ) {

                        //Recupera a receita para o credito principal
                        include_once(CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceitaCredito.class.php");
                        $obTOrcamentoReceitaCredito = new TOrcamentoReceitaCredito();
                        $obTOrcamentoReceitaCredito->setDado('cod_genero'  ,$rsConsultaCredito->getCampo('cod_genero'  ));
                        $obTOrcamentoReceitaCredito->setDado('cod_especie' ,$rsConsultaCredito->getCampo('cod_especie' ));
                        $obTOrcamentoReceitaCredito->setDado('cod_natureza',$rsConsultaCredito->getCampo('cod_natureza'));
                        $obTOrcamentoReceitaCredito->setDado('cod_credito' ,$rsConsultaCredito->getCampo('cod_credito' ));
                        $obTOrcamentoReceitaCredito->setDado('exercicio'   ,Sessao::getExercicio());
                        $obTOrcamentoReceitaCredito->recuperaRelacionamento($rsReceita,' ORDER BY cod_receita' );

                        $arReceitas[$rsReceita->getCampo('cod_receita')]['cod_receita'] = $rsReceita->getCampo('cod_receita');
                        $arReceitas[$rsReceita->getCampo('cod_receita')]['descricao'] = $rsReceita->getCampo('descricao');
                        $arReceitas[$rsReceita->getCampo('cod_receita')]['valor'] += $rsConsultaCredito->getCampo('valor');

                        $flTotalCarne += $rsConsultaCredito->getCampo('valor');

                        //Seta os valores no recordset dos creditos
                        $inTotalJuros += $rsConsultaCredito->getCampo('valor_credito_juros');
                        $inTotalMulta += $rsConsultaCredito->getCampo('valor_credito_multa');
                        $inTotalCorrecao += $rsConsultaCredito->getCampo('valor_credito_correcao');
                        $inTotalDescontos += $rsConsultaCredito->getCampo('desconto');
                        $arCarne[] = $arElementos[$rsConsultaCredito->getCorrente()-1];

                        $rsConsultaCredito->proximo();
                    }

                    $rsConsultaCredito->setPrimeiroElemento();

                    //Salva na sessao os creditos do carne para poder acessar no PR
                    Sessao::write('arCarne',$arCarne);

                    /**
                     * Calcula a porcentagem que deve ir em cada credito do valor da parcela
                     */
                    foreach ($arReceitas as $arCredito) {
                        $flPorcReceita = ($arCredito['valor'] * 100) / $flTotalCarne;
                        $arReceitas[$arCredito['cod_receita']]['valor'] = round(($rsConsultaCredito->getCampo('valor_parcela') * $flPorcReceita) / 100,2);
                        $arReceitas[$arCredito['cod_receita']]['valor'] = number_format($arReceitas[$arCredito['cod_receita']]['valor'],2,',','.');
                    }

                    /**
                     * Cria uma table tree para demonstrar os valores que irão em cada receita
                     */
                    //recordset
                    $rsLista = new RecordSet;
                    $rsLista->preenche(array_values($arReceitas));
                    // table
                    $obTable = new Table();
                    $obTable->setRecordset( $rsLista );
                    $obTable->setSummary        ('Lista Receitas'   );

                    $obTable->Head->addCabecalho('Cód. Reduzido',10 );
                    $obTable->Head->addCabecalho('Descrição'    ,60 );
                    $obTable->Head->addCabecalho('Valor'        ,10 );

                    $obTable->Body->addCampo    ('cod_receita'  ,'C');
                    $obTable->Body->addCampo    ('descricao'    ,'E');
                    $obTable->Body->addCampo    ('valor'        ,'E');

                    $obTable->montaHTML();

                    $stHTML = $obTable->getHTML();
                    $stHTML = str_replace( "\n" ,"" ,$stHTML );
                    $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
                    $stHTML = str_replace( "  " ,"" ,$stHTML );
                    $stHTML = str_replace( "'","\\'",$stHTML );
                    $stHTML = str_replace( "\\\'","\\'",$stHTML );

                    $inValorCarne = round( ( $rsConsultaCredito->getCampo('valor_parcela') + $inTotalJuros + $inTotalMulta + $inTotalCorrecao - $inTotalDescontos) , 2 );

                    $inTotalJuros = number_format ( $inTotalJuros, 2, ',','.' );
                    $inTotalMulta = number_format ( $inTotalMulta, 2, ',','.' );
                    $inTotalDescontos = number_format( $inTotalDescontos,2,',','.');
                    $inTotalCorrecao = number_format ( $inTotalCorrecao, 2, ',', '.' );
                    $inValorCarne = number_format($inValorCarne,2,',','.');

                    //Objeto text para a data de vencimento
                    $obDtVencimento = new Data();
                    $obDtVencimento->setName('dtVencimento');
                    $obDtVencimento->setRotulo('Data de Vencimento');
                    $obDtVencimento->setTitle('Data de vencimento do recibo');
                    $obDtVencimento->setLabel(true);
                    $obDtVencimento->setValue(implode('/',array_reverse(explode('-',$rsConsultaCredito->getCampo('vencimento')))));

                    //Objeto text para o valor do documento
                    $obTxtDocumento = new TextBox();
                    $obTxtDocumento->setName('stDocumento');
                    $obTxtDocumento->setRotulo('Valor do Documento');
                    $obTxtDocumento->setTitle('Valor do recibo');
                    $obTxtDocumento->setLabel(true);
                    $obTxtDocumento->setValue(number_format($rsConsultaCredito->getCampo('valor_parcela'),2,',','.'));

                    //Objeto text para os descontos
                    $obTxtDesconto = new TextBox();
                    $obTxtDesconto->setName('stDesconto');
                    $obTxtDesconto->setRotulo('Descontos');
                    $obTxtDesconto->setTitle('Descontos do recibo');
                    $obTxtDesconto->setLabel(true);
                    $obTxtDesconto->setValue($inTotalDescontos);

                    //Objeto text para as multas
                    $obTxtMulta = new TextBox();
                    $obTxtMulta->setName('stMulta');
                    $obTxtMulta->setRotulo('Multas');
                    $obTxtMulta->setTitle('Multas do recibo');
                    $obTxtMulta->setLabel(true);
                    $obTxtMulta->setValue($inTotalMulta);

                    //Objeto text para os juros
                    $obTxtJuros = new TextBox();
                    $obTxtJuros->setName('stJuros');
                    $obTxtJuros->setRotulo('Juros');
                    $obTxtJuros->setTitle('Juros do recibo');
                    $obTxtJuros->setLabel(true);
                    $obTxtJuros->setValue($inTotalJuros);

                    //Objeto text para a correcao
                    $obTxtCorrecao = new TextBox();
                    $obTxtCorrecao->setName('stJuros');
                    $obTxtCorrecao->setRotulo('Correção');
                    $obTxtCorrecao->setTitle('Correção do recibo');
                    $obTxtCorrecao->setLabel(true);
                    $obTxtCorrecao->setValue($inTotalCorrecao);

                    //Adiciona os componentes ao formulario
                    $obFormulario->addComponente($obDtVencimento);
                    $obFormulario->addComponente($obTxtDocumento);
                    $obFormulario->addComponente($obTxtDesconto);
                    $obFormulario->addComponente($obTxtMulta);
                    $obFormulario->addComponente($obTxtJuros);
                    $obFormulario->addComponente($obTxtCorrecao);

                    //Atribui o valor corrigido para o formulario
                    $stJs .= "$('nuValor_label').innerHTML = '".$inValorCarne."';";
                    $stJs .= "$('nuValor').value = '".$inValorCarne."';";

                    //Bloqueia os outros campos
                    if ($_REQUEST['stCodBarraManual'] != '') {
                        $stJs .= "$('stCodBarraOtico').disabled = true;";
                    } else {
                        $stJs .= "$('stCodBarraManual').disabled = true;";
                    }
                    $stJs .= "$('inCodReceita').disabled = true;";
                    $stJs .= "$('imgReceita').style.display='none';";
                }
            }
        }

        if ( $obErro->ocorreu() ) {
            $stJs .= "alertaAviso('".$obErro->getDescricao()."','frm','erro','".Sessao::getId()."'); \n";
            $stJs .= "$('stCodBarraOtico').value = '';";
            $stJs .= "$('stCodBarraManual').value = '';";
        }

        //Monta o formulario
        $obFormulario->montaInnerHTML();
        $stHTML .= $obFormulario->getHTML();
        $stJs .= "$('spnModalidade').innerHTML = '".$stHTML."';";

        break;
        
        case 'montaBemAlienacao':
            
        if ( $request->get('inCodReceita') != "" && $request->get('inCodEntidade') != "" ) {
            include_once CAM_GP_PAT_MAPEAMENTO."TPatrimonioBem.class.php";
            include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
            
            $obTPatrimonioBem = new TPatrimonioBem();
            $obTPatrimonioBem->recuperaTodos( $rsBem );
            
            $obTOrcamentoReceita = new TOrcamentoReceita();
           
            $stFiltro  = " AND receita.exercicio    = '".Sessao::getExercicio()."'
                           AND receita.cod_entidade IN (".$request->get('inCodEntidade').")
                           AND receita.cod_receita  = ".$request->get('inCodReceita')."
                           AND classificacao.mascara_classificacao ILIKE '2.2.%'
                           AND CLR.estorno = 'false'
                           AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                              FROM contabilidade.desdobramento_receita as dr
                                             WHERE receita.cod_receita = dr.cod_receita_secundaria
                                               AND receita.exercicio   = dr.exercicio )
                           \n";
            
            $obTOrcamentoReceita->recuperaReceitaAnaliticaTCE($rsReceita, $stFiltro, " ORDER BY mascara_classificacao");

            // Caso a gestão patrimonial esteja sendo utilziada e a receita pertença ao grupo de alienação, mostra o campo para selecionar o bem
            if ( $rsReceita->getNumLinhas() > 0 && $rsBem->getNumLinhas() > 0 ){
                include_once CAM_GP_PAT_COMPONENTES.'IPopUpBem.class.php';

                $obForm = new Form;                
                $obIPopUpBemAlienacao = new IPopUpBem( $obForm );
                $obIPopUpBemAlienacao->setId     ( 'stNomBemAlienacao' );
                $obIPopUpBemAlienacao->setRotulo ( 'Bem' );
                $obIPopUpBemAlienacao->setTitle  ( 'Informe o código do bem.' );
                $obIPopUpBemAlienacao->setNull   ( true );
                $obIPopUpBemAlienacao->obCampoCod->setName ( 'inCodBemAlienacao' );
                $obIPopUpBemAlienacao->obCampoCod->setId   ( 'inCodBemAlienacao' );
                $obIPopUpBemAlienacao->setFuncaoBusca ("abrePopUp('".CAM_GP_PAT_POPUPS."bem/FLManterBem.php','".$obIPopUpBemAlienacao->obForm->getName()."', '".$obIPopUpBemAlienacao->obCampoCod->getName()."','".$obIPopUpBemAlienacao->getId()."','','".Sessao::getId()."&boBemBaixado=false&inCodEntidade=".$request->get('inCodEntidade')."','800','550');");
                $obIPopUpBemAlienacao->obCampoCod->obEvento->setOnChange ("ajaxJavaScript('".CAM_GP_PAT_POPUPS.'bem/OCManterBem.php?'.Sessao::getId()."&boBemBaixado=false&inCodEntidade=".$request->get('inCodEntidade')."&stNomCampoCod=".$obIPopUpBemAlienacao->obCampoCod->getName()."&boMostrarDescricao=".$obIPopUpBemAlienacao->getMostrarDescricao()."&stIdCampoDesc=".$obIPopUpBemAlienacao->getId()."&stNomForm=".$obIPopUpBemAlienacao->obForm->getName()."&inCodigo='+this.value, 'buscaPopup');".$obIPopUpBemAlienacao->obCampoCod->obEvento->getOnChange());

                $obFormulario = new Formulario;
                $obFormulario->addForm       ( $obForm );            
                $obFormulario->addComponente ($obIPopUpBemAlienacao);

                $stHTML = $obFormulario->montaInnerHTML();
                $stHTML .= $obFormulario->getHTML();
                $stJs .= "$('spnBemAlienacao').innerHTML = '".$stHTML."';";  
            } else {
                $stJs .= "$('spnBemAlienacao').innerHTML = '';";  
            }
        }

        break;

}
echo $stJs;
?>

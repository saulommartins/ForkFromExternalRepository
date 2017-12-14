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
    * Pï¿½gina de processamento para Movimentaï¿½ï¿½o por Requisiï¿½ï¿½o
    * Data de criaï¿½ï¿½o : 02/03/2006

    * @author Analista: Diego Victoria
    * @author Programador: Leandro Andrï¿½ Zis

    * @ignore

    Caso de uso: uc-03.03.11

    $Id: PRMovimentacaoRequisicao.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once( CAM_GP_ALM_NEGOCIO."RAlmoxarifadoLancamentoRequisicao.class.php"                        );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoControleEstoque.class.php"                          );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoInventarioItens.class.php"    		              );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoItensAnulacao.class.php"         	      );
include_once( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoRequisicaoAnulacao.class.php"    	                  );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
include_once( CAM_GF_CONT_MAPEAMENTO."FContabilidadeAlmoxarifadoLancamento.class.php"                 );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoContaDespesaItem.class.php" );
include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoDebito.class.php" );

function br2us($flNumber, $inDecimal=4)
{
    $flNumber = str_replace( '.', '', $flNumber);

    return number_format( $flNumber, $inDecimal, '.', '');
}

//Define o nome dos arquivos PHP
$stPrograma = "MovimentacaoRequisicao";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once($pgJS);

function montaTabelaLightBox($rsItens, $stLista)
{
    if ($stLista == "Ponto Pedido") {
        $stTitulo = 'Ítens que Entraram em Ponto de Pedido';
        $stConteudo = 'Ponto de Pedido';
        $stDado = '[ponto_pedido]';
    } else {
        $stTitulo = 'Ítens que Entraram no Estoque Mínimo';
        $stConteudo = 'Estoque Mínimo';
        $stDado = '[estoque_minimo]';
    }
    $obLista = new Lista;
    $obLista->setMostraPaginacao( false );
    $obLista->setTitulo ($stTitulo);
    $obLista->setRecordSet( $rsItens );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( '&nbsp' );
    $obLista->ultimoCabecalho->setWidth( 4 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Item' );
    $obLista->ultimoCabecalho->setWidth( 21 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Unidade de Medida' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Marca' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Centro de Custo' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( $stConteudo );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo( 'Saldo Atual' );
    $obLista->ultimoCabecalho->setWidth( 15 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_item]-[desc_item]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[desc_unidade]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_marca]-[desc_marca]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[cod_centro]-[desc_centro]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( $stDado );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setCampo( "[saldo_atual]" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHTML = $obLista->getHTML();
    $stHTML = str_replace( "\n" ,"" ,$stHTML );
    $stHTML = str_replace( "  " ,"" ,$stHTML );
    $stHTML = str_replace( "'","\\'",$stHTML );

    return  $stHTML;
}

// Codigos para o Lightbox
function lightbox($arItens, $inCodLancamento = "", $stAcao)
{
    $inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
    $inCodRequisicao   = $_REQUEST['inCodRequisicao'];

    //Lighbox do Ponto de Pedido
    $arItensEstoqueMinimo = array();
    $arItensPontoPedido = array();
    $inEstMin=0;
    $i = 0;

    foreach ($arItens as $key => $item) {
        $inQuantidadeAtendida = $_REQUEST['nuQuantidade_'.($key+1) ];

        $obTAlmoxarifadoControleEstoque = new TAlmoxarifadoControleEstoque;
        $obTAlmoxarifadoControleEstoque->setDado( "cod_item", $item["cod_item"] );
        $obTAlmoxarifadoControleEstoque->recuperaPorChave( $rsControle );

        $item["saldo_atual"]  = str_replace(array(".",","), array("","."), $item["saldo_atual"] );
        $inQuantidadeAtendida = str_replace(array(".",","), array("","."), $inQuantidadeAtendida);
        $flSaldoCorrigido = $item["saldo_atual"] - $inQuantidadeAtendida;

        if ( $rsControle->getCampo("estoque_minimo") > 0 AND $flSaldoCorrigido<=(float) $rsControle->getCampo("estoque_minimo")) {
            $arItensEstoqueMinimo[$inEstMin]["cod_item"]     = $item["cod_item"];
            $arItensEstoqueMinimo[$inEstMin]["desc_item"]    = $item["desc_item"];
            $arItensEstoqueMinimo[$inEstMin]["desc_unidade"] = $item["desc_unidade"];
            $arItensEstoqueMinimo[$inEstMin]["cod_marca"]    = $item["cod_marca"];
            $arItensEstoqueMinimo[$inEstMin]["desc_marca"]   = $item["desc_marca"];
            $arItensEstoqueMinimo[$inEstMin]["cod_centro"]   = $item["cod_centro"];
            $arItensEstoqueMinimo[$inEstMin]["desc_centro"]  = $item["desc_centro"];
            $arItensEstoqueMinimo[$inEstMin]["estoque_minimo"] = number_format($rsControle->getCampo("estoque_minimo"),'4',',','.');
            $arItensEstoqueMinimo[$inEstMin]["saldo_atual"]  = $flSaldoCorrigido;
            $inEstMin++;
        } elseif ( $rsControle->getCampo("ponto_pedido") > 0 AND $flSaldoCorrigido<= (float) $rsControle->getCampo("ponto_pedido") ) {
//            $vlSaldoAtual = ($item["saldo_atual"] - $inQuantidadeAtendida);
            $arItensPontoPedido[$i]["cod_item"]     = $item["cod_item"];
            $arItensPontoPedido[$i]["desc_item"]    = $item["desc_item"];
            $arItensPontoPedido[$i]["desc_unidade"] = $item["desc_unidade"];
            $arItensPontoPedido[$i]["cod_marca"]    = $item["cod_marca"];
            $arItensPontoPedido[$i]["desc_marca"]   = $item["desc_marca"];
            $arItensPontoPedido[$i]["cod_centro"]   = $item["cod_centro"];
            $arItensPontoPedido[$i]["desc_centro"]  = $item["desc_centro"];
            $arItensPontoPedido[$i]["ponto_pedido"] = number_format( $rsControle->getCampo("ponto_pedido"),'4',',','.');
            $arItensPontoPedido[$i]["saldo_atual"]  = $flSaldoCorrigido;
            $i++;
        }
    }

    $stHTML = "";
    //DEFINICAO DA LISTA DE ITENS DO PONTO DE PEDIDO
    $rsItens = new RecordSet();
    $rsItens->preenche( $arItensPontoPedido );
    $rsItens->addFormatacao('saldo_atual', 'NUMERIC_BR_4');

    if ( !$rsItens->eof() ) {
        $stHTML .= montaTabelaLightBox($rsItens, "Ponto Pedido" );
    }

    //DEFINICAO DA LISTA DE ITENS DO ESTOQUE MINIMO
    $rsItensEstoqueMinimo = new RecordSet();
    $rsItensEstoqueMinimo->preenche( $arItensEstoqueMinimo );
    $rsItensEstoqueMinimo->addFormatacao('saldo_atual', 'NUMERIC_BR_4');

    if ( !$rsItensEstoqueMinimo->eof() ) {
        $stHTML .=  montaTabelaLightBox($rsItensEstoqueMinimo, "Estoque Mínimo" );
    }

    $stCaminhoLighbox = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraRelatorioSaida.php?'.Sessao::getId().'&stAcao='.$stAcao;
    $stCaminhoLighbox .= '&inCodAlmoxarifado='.$inCodAlmoxarifado.'&inNumLancamento='.$inCodLancamento.'&inCodRequisicao='.$inCodRequisicao.'&stExercicioRequisicao='.$_REQUEST['stExercicio'];

    $obBtnOk  = new Ok();
    $obBtnOk->obEvento->setOnClick( "window.parent.frames['telaPrincipal'].location.href = '".$stCaminhoLighbox."';" );

    $obFormulario = new Formulario();
    $obFormulario->defineBarra(array($obBtnOk), "left", "");
    $obFormulario->montaInnerHtml();
    $stHTML .= $obFormulario->getHTML();

    $stJs = "d.getElementById('conteudolightbox').innerHTML = '".$stHTML."';";

    if ( count( $arItensPontoPedido ) > 0 or count($arItensEstoqueMinimo) > 0 ) {
        echo '<script type="text/javascript">criaFundo(); criaLightbox("'.$stCaminhoLighbox.'");'.$stJs.'</script>';

        return true;
    } else {
        echo "<script>jQuery(window.parent.frames['telaPrincipal'].document).find(':button').removeAttr('disabled');</script>";

        return false;
    }
}

$stAcao            = $_REQUEST['stAcao'];
$inCodRequisicao   = $_REQUEST['inCodRequisicao'];
$inCodAlmoxarifado = $_REQUEST['inCodAlmoxarifado'];
$arrayValores = Sessao::read('Valores');

switch ($stAcao) {
    case "entrada":
    case "saida":
    $erro = 0;
    if ($stAcao == "saida") {
        //valida configuração para lançamento contábil
        include_once(CAM_GF_CONT_MAPEAMENTO."TContabilidadeConfiguracaoLancamentoDebito.class.php");
        $arCodEstrutural = array();

        // validacao dos valores digitados pelo usuario para todos os itens
        foreach ($arrayValores as $chave =>$dados) {
            $valorQuantidadeOriginal      = $_REQUEST['nuQuantidade_'.($chave+1)];
            $valorSaldoAtualOriginal      = $dados['saldo_atual'];
            $valorSaldoRequeridoOriginal  = $dados['saldo_req'];
            $valorSaldoAtendidoOriginal   = $dados['saldo_atend'];
            $boItemFrota                  = $dados['boItemFrota'];

            $valorQuantidade = str_replace('.','',$valorQuantidadeOriginal);
            $valorQuantidade = str_replace(',','.',$valorQuantidade);

            if ($valorQuantidade > 0) {
                $valorSaldoAtual = str_replace('.','',$valorSaldoAtualOriginal);
                $valorSaldoAtual = str_replace(',','.',$valorSaldoAtual);

                $saldoRequerido = str_replace('.','',$valorSaldoRequeridoOriginal);
                $saldoRequerido = str_replace(',','.',$saldoRequerido);
                $saldoRequerido = $saldoRequerido + 0; // apenas para formatar no mesmo formato da soma abaixo

                $saldoAtendido = str_replace('.','',$valorSaldoAtendidoOriginal);
                $saldoAtendido = str_replace(',','.',$saldoAtendido);

                $valorTotalSaldoSolicitado = $saldoAtendido + $valorQuantidade;

                if ($valorSaldoAtual < $valorQuantidade) {
                    SistemaLegado::exibeAviso(urlencode('Valor inválido. (Quantidade de Saída não pode ser maior que saldo atual.)'),"n_incluir","erro");
                    $erro++;
                }

                if ($saldoRequerido < $valorTotalSaldoSolicitado) {
                    SistemaLegado::exibeAviso(urlencode('Valor inválido. (Quantidade de Saída não pode ser maior que saldo requisitado.)'),"n_incluir","erro");
                    $erro++;
                }

                if ($boItemFrota == true) {

                    //necessario informar veiculo e quilometragem para itens que tenham quantidade de saida maior que zero.
                    if (!$_REQUEST['nmKm'] || !$_REQUEST['inCodVeiculo'] and (int) $valorQuantidade > 0) {
                        if (!$_REQUEST['nmKm']) {
                            sistemaLegado::exibeAviso(urlencode('Informe a quilometragem nos itens que pertencem ao frota.'),"n_incluir","erro");
                        }
                        if (!$_REQUEST['inCodVeiculo']) {
                            sistemaLegado::exibeAviso(urlencode('Informe o veículo nos itens que pertencem ao frota.'),"n_incluir","erro");
                        }
                        $erro++;
                    }
                }

                //pega o cod conta despesa referente ao lançamento
                $inCodContaDespesa = ($_REQUEST['inCodContaDespesa_'.($chave+1).''] ? $_REQUEST['inCodContaDespesa_'.($chave+1).''] : $_REQUEST['inCodContaDespesa_'.($chave+1).'_hidden']);

                if ($inCodContaDespesa != "") {
                    $stFiltroContas = " WHERE configuracao_lancamento_debito.estorno = false
                                    AND configuracao_lancamento_debito.tipo = 'almoxarifado'
                                    AND configuracao_lancamento_debito.cod_conta_despesa = ".$inCodContaDespesa."
                                    AND configuracao_lancamento_debito.exercicio = '".$_REQUEST['stExercicio']."' ";
                    $obTContabilidadeConfiguracaoLancamentoDebito = new TContabilidadeConfiguracaoLancamentoDebito;
                    $obErro = $obTContabilidadeConfiguracaoLancamentoDebito->recuperaContasDebitoCredito( $rsContasDebitoCredito, $stFiltroContas );

                    if ($rsContasDebitoCredito->getNumLinhas() < 1) {
                        include_once CAM_GF_ORC_NEGOCIO."ROrcamentoDespesa.class.php";
                        $obROrcamentoDespesa = new ROrcamentoDespesa;
                        $obROrcamentoDespesa->obROrcamentoClassificacaoDespesa->setCodConta($inCodContaDespesa);
                        $obROrcamentoDespesa->listarCodEstruturalDespesa($rsContaDespesa, "ORDER BY conta_despesa.cod_estrutural");
                        $arCodEstrutural[] = $rsContaDespesa->getCampo('cod_estrutural');
                    }
                } else {
                    if ($dados['cod_tipo_item'] == 1 || $dados['cod_tipo_item'] == 2) {
                        sistemaLegado::exibeAviso(urlencode('O Desdobramento para lançamento deve ser informado quando a quantidade for maior do que 0 (Item '.($chave+1).').'),"n_incluir","erro");
                    }
                }

                verificaItensEmInventarioNaoProcessado($dados);
            }
        }
        if (!empty($arCodEstrutural)) {
            SistemaLegado::exibeAviso(urlencode('Os desdobramentos ('.implode(', ', $arCodEstrutural).') não estão configurados para lançamento contábil.'),"n_incluir","erro");
            $erro++;
        }
    } elseif ($stAcao == "entrada") {

        $arItem = array();
        $arItem = explode('-', $_REQUEST['boDetalharItem']);
        $inCodItem = $arItem[0];
        $inItensValidos = 0;

        // validação dos valores digitados pelo usuário para todos os itens
        foreach ($arrayValores as $chave =>$dados) {
            if ($dados['disabled'] == false) {
                $valorQuantidadeOriginal = $_REQUEST['nuQuantidade_'.($chave+1)];
                $valorSaldoAtendidoOriginal = $dados['saldo_atend'];

                $valorQuantidade = str_replace('.','',$valorQuantidadeOriginal);
                $valorQuantidade = str_replace(',','.',$valorQuantidade);

                $saldoAtendido = str_replace('.','',$valorSaldoAtendidoOriginal);
                $saldoAtendido = str_replace(',','.',$saldoAtendido);

                if ($saldoAtendido < $valorQuantidade) {
                    SistemaLegado::exibeAviso(urlencode('Valor inválido. (Quantidade de Entrada não pode ser maior que saldo atendido.)'),"n_incluir","erro");
                    $erro++;
                }
                verificaItensEmInventarioNaoProcessado($dados);
                $inItensValidos++;
            }
        }
    }
    if ($erro == 0) {
        $nmQuantidade = 0;
        $stQtde = '';

        for ($i=1;$i<count($arrayValores)+1;$i++) {
           $stQtde = str_replace('.','',$_REQUEST['nuQuantidade_'.$i]);
           $stQtde = str_replace(',','.', $stQtde );
           $nmQuantidade += $stQtde;
        }

        # Validação para ítens ativos.
        if ((float) $nmQuantidade <= 0) {
            if ($inItensValidos > 0) {
                // Em caso de erro, libera o OK para o usuario.
                SistemaLegado::exibeAviso(urlencode('Valor inválido. (Quantidade de Saída não pode ser nula.)'),"n_incluir","erro");
                echo "<script>jQuery(window.parent.frames['telaPrincipal'].document).find(':button').removeAttr('disabled');</script>";
                break;
            } else {
                // Em caso de erro, libera o OK para o usuario.
                SistemaLegado::exibeAviso(urlencode('Erro ao efetuar Devolução com Requisição! A movimentação foi interrompida, pois possui apenas Ítens inativos.'),"unica","erro");
                echo "<script>jQuery(window.parent.frames['telaPrincipal'].document).find(':button').removeAttr('disabled');</script>";
                break;
            }
        }
        if ($stAcao == 'saida') {
            include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencao.class.php";

            $obTFrotaManutencao = new TFrotaManutencao;
            $obTFrotaManutencao->proximoCod($inCodManutencao);
            $arItensManutencao = array();
            $arTEMP = array();
            foreach ( Sessao::read('Valores') as $key => $arLancamentos ) {
                if ($arLancamentos['boItemFrota'] == true) {
                    $boInclui = true;
                    foreach ($arItensManutencao as $arTEMP) {
                        if ($arLancamentos['inCodVeiculo'] == $arTEMP['inCodVeiculo']) {
                            $boInclui = false;
                        }
                    }
                    $valorQuantidadeOriginal      = $_REQUEST['nuQuantidade_'.($key+1)];
                    $valorQuantidade = str_replace('.','',$valorQuantidadeOriginal);
                    $valorQuantidade = str_replace(',','.',$valorQuantidade);
                    if ($boInclui and (int) $valorQuantidade > 0) {
                        $obTFrotaManutencao = new TFrotaManutencao;
                        $obTFrotaManutencao->setDado    ( 'exercicio'      , Sessao::getExercicio()            );
                        $obTFrotaManutencao->setDado    ( 'cod_manutencao' , $inCodManutencao                  );
                        $obTFrotaManutencao->setDado    ( 'cod_veiculo'    , $_REQUEST['inCodVeiculo']    );
                        $obTFrotaManutencao->setDado    ( 'dt_manutencao'  , date('d/m/Y')                     );
                        $obTFrotaManutencao->setDado    ( 'km'             , $_REQUEST['nmKm']            );
                        $obTFrotaManutencao->setDado    ( 'observacao'     , $arLancamentos['complemento']     );
                        $obTFrotaManutencao->inclusao();

                        $arTEMP['stExercicio']     = $arLancamentos['stExercicio'];
                        $arTEMP['inCodVeiculo']    = $_REQUEST['inCodVeiculo'];
                        $arTEMP['inCodManutencao'] = $inCodManutencao;
                        $arItensManutencao[] = $arTEMP;
                        $inCodManutencao++;
                    }
                }
            }
        }

        include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoNaturezaLancamento.class.php";
        include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoMaterial.class.php";
        include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoPerecivel.class.php";
        include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoRequisicao.class.php";
        include_once CAM_GP_FRO_MAPEAMENTO."TFrotaManutencaoItem.class.php";
        include_once CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoLancamentoManutencaoFrota.class.php";
        include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUsuario.class.php");

        Sessao::setTrataExcecao( true );

        # $obTConfiguracao = new TAlmoxarifadoConfiguracao;
        # $obTConfiguracao->setDado('parametro','numeracao_lancamento_estoque');
        # $obTConfiguracao->recuperaPorChave($rsNumLanc);
        # $stNumeracao = ( trim($rsNumLanc->getCampo('valor'))=="" ? 'N' : $rsNumLanc->getCampo('valor') );

       if ($_REQUEST['stCGMUsuario']) {
         $obTAdministracaoUsuario = new TAdministracaoUsuario();
         $stFiltroUsuario = " WHERE usuario.status = 'A'
                         AND usuario.username = '".$_REQUEST['stCGMUsuario']."' ";
         $obTAdministracaoUsuario->recuperaUsuario($rsUsuario, $stFiltroUsuario);
         $rsUsuario->setPrimeiroElemento();
       }

        $inCodLancamento = 0;

        $obTAlmoxarifadoNaturezaLancamento = new TAlmoxarifadoNaturezaLancamento;
        $obTAlmoxarifadoLancamentoManutencaoFrota = new TAlmoxarifadoLancamentoManutencaoFrota;

        if ($stAcao == 'saida')
           $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'S');
        else
           $obTAlmoxarifadoNaturezaLancamento->setDado('tipo_natureza' , 'E');

        $obTAlmoxarifadoNaturezaLancamento->setDado('cod_natureza' , 7);

        # Recupera o num_lancamento considerando as configuraï¿½ï¿½es do Almoxarifado.
        $obTAlmoxarifadoNaturezaLancamento->recuperaNumNaturezaLancamento($rsNumLancamento);

        $inCodLancamento = $rsNumLancamento->getCampo('num_lancamento');

        $obTAlmoxarifadoNaturezaLancamento->setDado ( 'num_lancamento'      , $inCodLancamento            );
        $obTAlmoxarifadoNaturezaLancamento->setDado ( 'cgm_almoxarife'      , Sessao::read('numCgm') );
        if ($stAcao == 'saida') {
           $obTAlmoxarifadoNaturezaLancamento->setDado ( 'numcgm_usuario'      , $rsUsuario->getCampo('numcgm') );
        } else {
           $obTAlmoxarifadoNaturezaLancamento->setDado ( 'numcgm_usuario'      , Sessao::read('numCgm') );
        }
        $obTAlmoxarifadoNaturezaLancamento->setDado('timestamp', date('Y-m-d H:i:s'));
        $obTAlmoxarifadoNaturezaLancamento->inclusao();

        $obTAlmoxarifadoLancamentoMaterial =  new TAlmoxarifadoLancamentoMaterial;
        $obTAlmoxarifadoLancamentoMaterial->obTAlmoxarifadoNaturezaLancamento = & $obTAlmoxarifadoNaturezaLancamento;

        $obTAlmoxarifadoLancamentoRequisicao = new TAlmoxarifadoLancamentoRequisicao;

        $obTFrotaManutencaoItem = new TFrotaManutencaoItem;

        $arMsgLancamentos = array();

        foreach ( Sessao::read('Valores') as $key => $arLancamentos ) {
            $valorQuantidadeOriginal      = $_REQUEST['nuQuantidade_'.($key+1)];
            $valorQuantidade = str_replace('.','',$valorQuantidadeOriginal);
            $valorQuantidade = str_replace(',','.',$valorQuantidade);
            if ($valorQuantidade > 0) {
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_item'            , $arLancamentos['cod_item']          );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_marca'           , $arLancamentos['cod_marca']         );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_almoxarifado'    , $arLancamentos['inCodAlmoxarifado'] );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_centro'          , $arLancamentos['cod_centro']        );
                $obTAlmoxarifadoLancamentoMaterial->setDado( 'complemento'         , $arLancamentos['complemento']       );

                if ( !is_array( $arLancamentos['ValoresLotes'] )) {
                    $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                    $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat           );

                    $obTAlmoxarifadoLancamentoMaterial->recuperaRestoValor($rsItemValorUnitario);
                    $vlResto = $rsItemValorUnitario->getCampo('resto');

                    $stQtde = str_replace('.','',$_REQUEST['nuQuantidade_'.($key+1) ]);
                    $stQtde = str_replace(',','.', $stQtde );

                    if ($stAcao == 'saida') {
                        $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsLancamentoMaterialValor, $stFiltro);
                        $vlItemUnitarioTruncado = $rsLancamentoMaterialValor->getCampo('valor_unitario');

                        //$vlResto = ($vlResto * -1); //inverte sinal pq é saída.
                        $valorMercadoTotal = (($vlItemUnitarioTruncado * $stQtde)+$vlResto)*-1;

                        // Grava o valor monetï¿½rio negativo na base, facilitando os cï¿½lculos de saldo.
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade' , ($stQtde * -1));
                    } else {
                        $obTAlmoxarifadoLancamentoRequisicao->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ('cod_requisicao' , $arLancamentos['inCodRequisicao'] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ('exercicio'      , $arLancamentos['stExercicio']     );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ('cod_item'       , $arLancamentos['cod_item']        );

                        if ($arLancamentos['quantidade'] == $arLancamentos['saldo_atend']) {
                            $obTAlmoxarifadoLancamentoRequisicao->recuperaSaldoValorUnitarioRequisicao($rsLancamentoMaterialValor, $stFiltro);
                            $vlResto = 0;
                        } else {
                            $obTAlmoxarifadoLancamentoRequisicao->recuperaSaldoValorUnitarioRequisicaoTruncado($rsLancamentoMaterialValor, $stFiltro);
                        }
                        $valorMercadoTotal = ($rsLancamentoMaterialValor->arElementos[0]['valor_unitario'] * $stQtde) + $vlResto;
                        $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade' , $stQtde );
                    }

                    $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado' , $valorMercadoTotal);

                    $obTAlmoxarifadoLancamentoMaterial->inclusao();

                    //lançamento para contabilidade
                    include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php" );
                    $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
                    $obErro = $obTAlmoxarifadoCentroCustoEntidade->recuperaTodos($rsCentroCustoEntidade, " WHERE cod_centro = ".$arLancamentos['cod_centro'], " ORDER BY exercicio DESC");

                    $inCodContaDespesa = ($_REQUEST['inCodContaDespesa_'.($key+1).''] ? $_REQUEST['inCodContaDespesa_'.($key+1).''] : $_REQUEST['inCodContaDespesa_'.($key+1).'_hidden']);

                    if ($inCodContaDespesa != "") {
                        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem = new TContabilidadeConfiguracaoLancamentoContaDespesaItem;
                        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_item', $arLancamentos['cod_item']);
                        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('exercicio', Sessao::getExercicio());
                        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->setDado('cod_conta_despesa', $inCodContaDespesa);
                        $obTContabilidadeConfiguracaoLancamentoContaDespesaItem->salvar();
                    } else {
                        if ($arLancamentos['cod_tipo_item'] == 1 || $arLancamentos['cod_tipo_item'] == 2) {
                            // $obErro->setDescricao('O Desdobramento para lançamento deve ser informado quando a quantidade for maior do que 0 (Item '.($key+1).').');
                            $obErro->setDescricao('O Desdobramento para lançamento deve ser informado quando a quantidade for maior do que 0.');
                        }
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obFContabilidadeAlmoxarifadoLancamento = new FContabilidadeAlmoxarifadoLancamento;
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "exercicio"         , Sessao::getExercicio()                                     );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_conta_despesa" , $inCodContaDespesa                                         );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"             , $valorMercadoTotal                                         );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "complemento"       , 'Saída por Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "tipo_lote"         , 'X'                                                        );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "nom_lote"          , 'Saída por Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "dt_lote"           , date('d/m/Y')                                              );
                        $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_entidade"      , $rsCentroCustoEntidade->getCampo('cod_entidade')           );
                        if ($stAcao == 'entrada') {
                            $obFContabilidadeAlmoxarifadoLancamento->setDado( "estorno"      , 'true'           );
                            $obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"        , $valorMercadoTotal                     );
                            $obFContabilidadeAlmoxarifadoLancamento->setDado( "complemento"       , 'Devolução com Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                            $obFContabilidadeAlmoxarifadoLancamento->setDado( "nom_lote"          , 'Devolução com Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                        }
                        $obErro = $obFContabilidadeAlmoxarifadoLancamento->executaFuncao( $rsRecordSet );
                    }

                    if ( !$obErro->ocorreu() ) {
                        $obTAlmoxarifadoLancamentoRequisicao->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_requisicao'  , $arLancamentos['inCodRequisicao']   );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'exercicio'       , $arLancamentos['stExercicio']       );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_almoxarifado', $arLancamentos['inCodAlmoxarifado'] );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_marca'       , $arLancamentos['cod_marca']         );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_centro'      , $arLancamentos['cod_centro']        );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_item'        , $arLancamentos['cod_item']          );
                        $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_lancamento'  , $inCodLancMat                       );
                        $obTAlmoxarifadoLancamentoRequisicao->inclusao();

                        if (count($arItensManutencao) > 0) {
                            foreach ($arItensManutencao as $arTEMP) {
                                if ($arLancamentos['boItemFrota'] == true) {
                                    if ($arLancamentos['inCodVeiculo'] == $arTEMP['inCodVeiculo']) {
                                        $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsRecordSet);
                                        $nuVlTotal = ($rsRecordSet->getCampo('valor_unitario') * $arLancamentos['quantidade']) + $vlResto;

                                        $obTFrotaManutencaoItem->setDado( 'cod_manutencao' , $arTEMP['inCodManutencao']               );
                                        $obTFrotaManutencaoItem->setDado( 'cod_item'       , $arLancamentos['cod_item']               );
                                        $obTFrotaManutencaoItem->setDado( 'exercicio'      , Sessao::getExercicio()                   );
                                        $obTFrotaManutencaoItem->setDado( 'quantidade'     , $arLancamentos['quantidade']             );
                                        $obTFrotaManutencaoItem->setDado( 'valor'          , $nuVlTotal                               );
                                        $obTFrotaManutencaoItem->inclusao();
                                        $arMsgLancamentos[] = $arTEMP['inCodManutencao'];

                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_lancamento'   , $inCodLancMat                       );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_item'         , $arLancamentos['cod_item']          );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_marca'        , $arLancamentos['cod_marca']         );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_almoxarifado' , $arLancamentos['inCodAlmoxarifado'] );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_centro'       , $arLancamentos['cod_centro']        );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'cod_manutencao'   , $arTEMP['inCodManutencao']          );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->setDado( 'exercicio'        , Sessao::getExercicio()              );
                                        $obTAlmoxarifadoLancamentoManutencaoFrota->inclusao();
                                    }
                                }
                            }
                        }
                    }
                } else {
                    foreach ($arLancamentos['ValoresLotes'] as $arItensLotes) {
                        if ($arItensLotes['quantidade'] > 0) {
                            $obTAlmoxarifadoLancamentoMaterial->proximoCod( $inCodLancMat );
                            $obTAlmoxarifadoLancamentoMaterial->setDado( 'cod_lancamento' , $inCodLancMat               );

                            $stQtde = str_replace('.', '', $arItensLotes['quantidade']);
                            $stQtde = str_replace(',','.', $stQtde );

                            $obTAlmoxarifadoLancamentoMaterial->recuperaRestoValor($rsItemValorUnitario);
                            $vlResto = $rsItemValorUnitario->getCampo('resto');

                            // Provisoriamente comentado! Serï¿½ reformulado na prï¿½xima versï¿½o.
                            if ($stAcao == 'saida') {
                                $obTAlmoxarifadoLancamentoMaterial->recuperaSaldoValorUnitarioTruncado($rsLancamentoMaterialValor, $stFiltro);

                                $valorMercadoTotal = ($rsLancamentoMaterialValor->arElementos[0]['valor_unitario'] * $stQtde) + $vlResto;

                                // Grava o valor monetï¿½rio negativo na base, facilitando os cï¿½lculos de saldo.
                                $valorMercadoTotal = ($valorMercadoTotal * -1);

                                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade'    , ($stQtde * -1) );
                                $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado' , ($valorMercadoTotal * -1) );
                            } else {
                                $obTAlmoxarifadoLancamentoRequisicao->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                                $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_requisicao' , $arLancamentos['inCodRequisicao'] );
                                $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'exercicio'      , $arLancamentos['stExercicio']     );
                                $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_item'       , $arLancamentos['cod_item']        );
                                $obTAlmoxarifadoLancamentoRequisicao->recuperaSaldoValorUnitarioRequisicao($rsLancamentoMaterialValor, $stFiltro);

                                $valorMercadoTotal = ($rsLancamentoMaterialValor->arElementos[0]['valor_unitario'] * $stQtde) + $vlResto;

                                $obTAlmoxarifadoLancamentoMaterial->setDado( 'quantidade' , $stQtde );
                            }

                            $obTAlmoxarifadoLancamentoMaterial->setDado( 'valor_mercado' , $valorMercadoTotal);

                            //lançamento para contabilidade
                            if ($stAcao == "saida") {
                                include_once ( CAM_GP_ALM_MAPEAMENTO."TAlmoxarifadoCentroCustoEntidade.class.php" );
                                $obTAlmoxarifadoCentroCustoEntidade = new TAlmoxarifadoCentroCustoEntidade;
                                $obErro = $obTAlmoxarifadoCentroCustoEntidade->recuperaTodos($rsCentroCustoEntidade, " WHERE cod_centro = ".$arLancamentos['cod_centro']);
                                if ( !$obErro->ocorreu() ) {
                                    $obFContabilidadeAlmoxarifadoLancamento = new FContabilidadeAlmoxarifadoLancamento;
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "exercicio"       , $arLancamentos['stExercicio']                              );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_centro"      , $arLancamentos['cod_centro']                               );

                                    //FAZENDO TESTE PARA VALOR DO RELATORIO
                                    //$obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"           , ($valorMercadoTotal * -1) * ($stQtde)                      );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "valor"           , $valorMercadoTotal * -1                                      );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "complemento"     , 'Saída por Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "tipo_lote"       , 'X'                                                        );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "nom_lote"        , 'Saída por Requisição do item '.$arLancamentos['cod_item'].', Requisição '.$arLancamentos['inCodRequisicao'] );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "dt_lote"         , date('d/m/Y')                                         );
                                    $obFContabilidadeAlmoxarifadoLancamento->setDado( "cod_entidade"    , $rsCentroCustoEntidade->getCampo('cod_entidade')           );
                                    $obErro = $obFContabilidadeAlmoxarifadoLancamento->executaFuncao( $rsRecordSet );
                                }
                            }

                            $obTAlmoxarifadoLancamentoMaterial->inclusao();

                            // inclusï¿½o na tabela lancamento_perecivel
                            $obTAlmoxarifadoLancamentoPerecivel = new TAlmoxarifadoLancamentoPerecivel;
                            $obTAlmoxarifadoLancamentoPerecivel->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_item'        , $arLancamentos['cod_item']          );
                            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_marca'       , $arLancamentos['cod_marca']         );
                            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_almoxarifado', $arLancamentos['inCodAlmoxarifado'] );
                            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'lote'            , $arItensLotes['lote']               );
                            $obTAlmoxarifadoLancamentoPerecivel->setDado ( 'cod_centro'      , $arLancamentos['cod_centro']        );
                            $obTAlmoxarifadoLancamentoPerecivel->inclusao();

                            $obTAlmoxarifadoLancamentoRequisicao->obTAlmoxarifadoLancamentoMaterial = &$obTAlmoxarifadoLancamentoMaterial;
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_requisicao'  , $arLancamentos['inCodRequisicao']   );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'exercicio'       , $arLancamentos['stExercicio']       );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_almoxarifado', $arLancamentos['inCodAlmoxarifado'] );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_marca'       , $arLancamentos['cod_marca']         );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_centro'      , $arLancamentos['cod_centro']        );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_item'        , $arLancamentos['cod_item']          );
                            $obTAlmoxarifadoLancamentoRequisicao->setDado ( 'cod_lancamento'  , $inCodLancMat                       );
                            $obTAlmoxarifadoLancamentoRequisicao->inclusao();
                        }
                    }
                }
            }
        }

        // Anular saldo pendente da requisiï¿½ï¿½o se for setado na tabela administracao.configuracao.
        if ($stAcao == "saida") {
            $obTAdministracaoConfiguracao           = new TAdministracaoConfiguracao;

            $stFiltro  = "";
            $stFiltro .= " WHERE exercicio  = '".Sessao::read('exercicio')."'" ;
            $stFiltro .= "   AND cod_modulo = 29";
            $stFiltro .= "   AND parametro  = 'anular_saldo_pendente'";

            $obTAdministracaoConfiguracao->recuperaTodos($rsAlmoxarifadoConfiguracao,$stFiltro);
            //Verifica se anular_saldo_pendente ï¿½ true.
            if ($rsAlmoxarifadoConfiguracao->getCampo('valor') == 'true') {
                $obTAlmoxarifadoRequisicaoItensAnulacao = new TAlmoxarifadoRequisicaoItensAnulacao;
                $obTAlmoxarifadoRequisicaoAnulacao      = new TAlmoxarifadoRequisicaoAnulacao;

                $inAnula = 0;
                $i = 0;
                //Verifica se possui saldo pendente.
                foreach (Sessao::read('Valores') as $valoresAnulacao) {
                    $i = $valoresAnulacao['inId'] + 1;
                    if ($_REQUEST["nuQuantidade_$i"] < $valoresAnulacao['saldo_req'] - $valoresAnulacao['saldo_atend']) {
                        $inAnula++;
                    }
                }

                //Anula a requisiï¿½ï¿½o.
                if ($inAnula != 0) {
                    $obTAlmoxarifadoRequisicaoAnulacao->setDado('exercicio', $valoresAnulacao['stExercicio']);
                    $obTAlmoxarifadoRequisicaoAnulacao->setDado('cod_requisicao', $valoresAnulacao['inCodRequisicao']);
                    $obTAlmoxarifadoRequisicaoAnulacao->setDado('cod_almoxarifado', $valoresAnulacao['inCodAlmoxarifado']);
                    $obTAlmoxarifadoRequisicaoAnulacao->setDado('motivo', 'Anulação automática');
                    $obTAlmoxarifadoRequisicaoAnulacao->setDado('timestamp', date('Y-m-d H:i:s'));
                    $obTAlmoxarifadoRequisicaoAnulacao->inclusao();

                    $i = 1;
                    //Anula os itens da requisiï¿½ï¿½o.
                    foreach (Sessao::read('Valores') as $valoresAnulacao) {
                        $valorQuantidadeOriginal      = $_REQUEST['nuQuantidade_'.$i];
                        $valorQuantidade = str_replace('.','',$valorQuantidadeOriginal);
                        $valorQuantidade = str_replace(',','.',$valorQuantidade);
                        if ((int) $valorQuantidade > 0) {
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_item', $valoresAnulacao['cod_item']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_marca', $valoresAnulacao['cod_marca']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_centro', $valoresAnulacao['cod_centro']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('exercicio', $valoresAnulacao['stExercicio']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_requisicao', $valoresAnulacao['inCodRequisicao']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('cod_almoxarifado', $valoresAnulacao['inCodAlmoxarifado']);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('quantidade', ($valoresAnulacao['saldo_req'] - $valoresAnulacao['saldo_atend']) - $_REQUEST["nuQuantidade_$i"]);
                            $obTAlmoxarifadoRequisicaoItensAnulacao->setDado('timestamp', date('Y-m-d H:i:s'));
                            $obTAlmoxarifadoRequisicaoItensAnulacao->inclusao();
                            $i++;
                        }
                    }
                }
            }
        }

        if ($stAcao == "saida") {
            // Quando for saï¿½da poderï¿½ exibir o Ponto de Pedido.
            $boLightbox   = lightbox($arrayValores, $inCodLancamento, $stAcao);
            $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'saida/OCGeraRelatorioSaida.php?'.Sessao::getId().'&stAcao='.$stAcao;
            $stParametros = '&inCodAlmoxarifado='.$inCodAlmoxarifado.'&inNumLancamento='.$inCodLancamento.'&inCodRequisicao='.$_REQUEST['inCodRequisicao'];
            $stParametros .= '&stExercicioRequisicao='.$_REQUEST['stExercicio'];
            if (!$boLightbox) {
                SistemaLegado::alertaAviso($stCaminho.$stParametros, "Requisição: ".$_REQUEST['inCodRequisicao'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                SistemaLegado::exibeAviso("Requisição: ".$_REQUEST['inCodRequisicao'].$stMsg,"incluir","aviso");
            }
        } else {
            if (!$obErro->ocorreu()) {
                $stCaminho    = CAM_GP_ALM_INSTANCIAS . 'entrada/OCGeraMovimentacaoDiversa.php?'.Sessao::getId().'&stAcao='.$stAcao;
                $stParametros = "&inNumLancamento=".$inCodLancamento."&inCodNatureza=7&inCodAlmoxarifado=".$inCodAlmoxarifado;

                SistemaLegado::alertaAviso($stCaminho.$stParametros, "Requisição: ".$_REQUEST['inCodRequisicao'],"incluir","aviso", Sessao::getId(), "../");
            } else {
                sistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
            }
        }
        Sessao::encerraExcecao();
    } else {
        echo "<script>jQuery(window.parent.frames['telaPrincipal'].document).find(':button').removeAttr('disabled');</script>";
    }

    break;
}

function verificaItensEmInventarioNaoProcessado($arItemVerificacao)
{
    $obTAlmoxarifadoInventario = new TAlmoxarifadoInventarioItens;

    $obTAlmoxarifadoInventario->setDado('cod_item', $arItemVerificacao['cod_item']);
    $obTAlmoxarifadoInventario->setDado('cod_almoxarifado', $_REQUEST['inCodAlmoxarifado']);
    $obTAlmoxarifadoInventario->setDado('exercicio', Sessao::getExercicio());
    $obTAlmoxarifadoInventario->setDado('cod_marca',$arItemVerificacao['cod_marca'] );
    $obTAlmoxarifadoInventario->setDado('cod_centro',$arItemVerificacao['cod_centro'] );

    $obTAlmoxarifadoInventario->verificaItensInventarioNaoProcessado($rsItensInventario);

    if ($rsItensInventario->getNumLinhas()>0) {
        $boIncluir = false;
        SistemaLegado::exibeAviso('O item '.$rsItensInventario->getCampo('cod_item').'-'.$rsItensInventario->getCampo('descricao').' não pode ser utilizado pois está em processo de inventário.','form','erro',Sessao::getId() );
        exit;
    }
}

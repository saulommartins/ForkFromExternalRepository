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
    * Pagina de processamento para Empenho - Ordem de Pagamento
    * Data de Criação   : 17/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @ignore

    * $Id: PRManterOrdemPagamento.php 63012 2015-07-16 17:20:41Z carlos.silva $

    * Casos de uso: uc-02.03.05
                    uc-02.03.04
                    uc-02.03.20
                    uc-02.03.28
*/

//Define o nome dos arquivos PHP
$stPrograma      = "ManterOrdemPagamento";
$pgFilt          = "FL".$stPrograma.".php";
$pgList          = "LS".$stPrograma.".php";
$pgForm          = "FM".$stPrograma.".php";
$pgProc          = "PR".$stPrograma.".php";
$pgOcul          = "OC".$stPrograma.".php";
$pgJs            = "JS".$stPrograma.".js";

/* includes de sistema */
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

/* includes de regra de negocio*/
include_once CAM_GF_EMP_NEGOCIO."REmpenhoOrdemPagamento.class.php";

/* includes de mapeamento */
include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoLiquidacaoAnulada.class.php";

/* includes de arquivo */
include_once $pgJs;

//valida a utilização da rotina de encerramento do mês contábil
$boUtilizarEncerramentoMes = SistemaLegado::pegaConfiguracao('utilizar_encerramento_mes', 9);
include_once CAM_GF_CONT_MAPEAMENTO."TContabilidadeEncerramentoMes.class.php";
$obTContabilidadeEncerramentoMes = new TContabilidadeEncerramentoMes;
$obTContabilidadeEncerramentoMes->setDado('exercicio', Sessao::getExercicio());
$obTContabilidadeEncerramentoMes->setDado('situacao', 'F');
$obTContabilidadeEncerramentoMes->recuperaEncerramentoMes($rsUltimoMesEncerrado, '', ' ORDER BY mes DESC LIMIT 1 ');

$obREmpenhoOrdemPagamento = new REmpenhoOrdemPagamento;
switch ($_REQUEST["stAcao"]) {
case "incluir":
    $boFlagTransacao = true;
    $obErro = new Erro();
    $obTransacao = new Transacao();
    $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
    //valida a utilização da rotina de encerramento do mês contábil
    $arDtAutorizacao = explode('/', $_POST['stDtOrdem']);
    if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= $arDtAutorizacao[1]) {
        SistemaLegado::LiberaFrames(true,False);
        SistemaLegado::exibeAviso(urlencode("Mês da Ordem encerrado!"),"n_incluir","erro");
        exit;
    }
    if ( strlen($_REQUEST["stDescricaoOrdem"]) > 600 ){
        $obErro->setDescricao(" A descrição da ordem ultrapassou 600 caracteres!");
    }
    $arItens = Sessao::read('itemOrdem');
    $arItensRetencao = Sessao::read('itemRetencao');
    if (SistemaLegado::ComparaDatas($_REQUEST['stDtOrdem'],'01/01/'.Sessao::getExercicio())) {
        $obREmpenhoOrdemPagamento->setExercicio(Sessao::getExercicio());
        $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["inCodEntidade"]);
        $obREmpenhoOrdemPagamento->setObservacao($_REQUEST["stDescricaoOrdem"]);
        $obREmpenhoOrdemPagamento->setDataEmissao($_REQUEST["stDtOrdem"]);
        $obREmpenhoOrdemPagamento->setDataVencimento($_REQUEST["dtDataVencimento"]);
        $obREmpenhoOrdemPagamento->setNotaLiquidacao($arItens);
        if (count($arItensRetencao) > 0) {
            $obREmpenhoOrdemPagamento->setRetencao(true);
            $obREmpenhoOrdemPagamento->setRetencoes($arItensRetencao);
        }
        $cmbLiquidacao = explode('||', $_REQUEST['cmbLiquidacao']);
    } else {
        $obErro->setDescricao("A data da OP deve ser maior que '01/01/".Sessao::getExercicio()."'");
    }
    
    $arAssinaturas = Sessao::read('assinaturas');
    $arAssinatura = $arAssinaturas['selecionadas'];
    if (is_array($arAssinatura)) {
    $arAssinaturaTemp = array_reverse($arAssinatura);
        foreach ($arAssinaturaTemp as $arAssina) {
            if (!isset($arAssina['papel'])) {
                $obErro->setDescricao("Selecione um papel para cada nome selecionado");
                SistemaLegado::exibeAviso("Selecione um papel para cada nome selecionado!","n_incluir","erro");
                SistemaLegado::LiberaFrames(true,False);
                exit;
            }
        }
    }

    if (!$obErro->ocorreu()) {
        $obErro = $obREmpenhoOrdemPagamento->incluir($boTransacao, $boFlagTransacao);
    }
    /* Salvar assinaturas configuráveis se houverem */

    $arAssinaturas = array();
    if (!$obErro->ocorreu()) {
        $arAssinaturas = Sessao::read('assinaturas');
        if (isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0) {
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoOrdemPagamentoAssinatura.class.php";
            $arAssinatura = $arAssinaturas['selecionadas'];
            $obTOPAssinatura = new TEmpenhoOrdemPagamentoAssinatura;
            $obTOPAssinatura->setDado('exercicio', Sessao::getExercicio());
            $obTOPAssinatura->setDado('cod_entidade', $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade());
            $obTOPAssinatura->setDado('cod_ordem', $obREmpenhoOrdemPagamento->getCodigoOrdem());
            $arPapel = $obTOPAssinatura->arrayPapel();

            $boInserir = 'true';
            $inCount = 0;
            $arAssinaInseridos = array();
            $arAssinaturaTemp = array_reverse($arAssinatura);
            foreach ($arAssinaturaTemp as $arAssina) {
                if (!isset($arAssina['papel'])) {
                    $obErro->setDescricao("Selecione um papel para cada nome selecionado");
                    SistemaLegado::exibeAviso("Selecione um papel para cada nome selecionado!","n_incluir","erro");
                    SistemaLegado::LiberaFrames(true,False);
                    exit;
                } else {
                    $stPapel = $arAssina['papel'];
                }

                if (array_key_exists($stPapel, $arPapel)) {
                    $inNumAssina = $arPapel[$stPapel];
                } elseif (array_search($stPapel, $arPapel)) {
                    $inNumAssina = $stPapel;
                }

                foreach ($arAssinaInseridos as $inCGMTemp => $stPapelTemp) {
                    if ($arAssina['inCGM'] != $inCGMTemp && $inNumAssina != $stPapelTemp) {
                                $boInserir = 'true';
                    } else {
                        $boInserir = 'false';
                        break;
                    }
                }
                if (!$obErro->ocorreu() && $boInserir == 'true') {
                    $obTOPAssinatura->setDado('num_assinatura', $inNumAssina);
                    $obTOPAssinatura->setDado('numcgm', $arAssina['inCGM']);
                    $obTOPAssinatura->setDado('cargo', $arAssina['stCargo']);
                    $obErro = $obTOPAssinatura->inclusao( $boTransacao );
                    $arAssinaInseridos[$arAssina['inCGM']] = $inNumAssina;
                }
                $inCount++;
            }
            unset($obTOPAssinatura);
            // Limpa Sessao->assinaturas
            $sessao->assinaturas = array('disponiveis'=>array(), 'papeis'=>array(), 'selecionadas'=>array());
        }
    }
    // Limpa Sessao->assinaturas
    Sessao::write('assinaturas', $arAssinaturas);

    // Caso haja mais de um item selecioinado, o sistema bloqueia a parte de retenções, então não há necessidade de se fazer esse processamento

    if (!$obErro->ocorreu() && count($arItens) == 1 && count($arItensRetencao) > 0) {
        // faz a inclusão do recibo extra, caso tenha sido incluido algum item na listagem extra-orçamentária
        incluirReciboExtra($obREmpenhoOrdemPagamento, $boTransacao);

        //Faz a verificação na configuração para saber se deve gerar ou não o carne pela configuração feita em Empenho::Alterar Configuração
        $stEmitirCarneOp = SistemaLegado::pegaDado('valor', 'administracao.configuracao', "WHERE exercicio='".Sessao::getExercicio()."' AND cod_modulo=".Sessao::getModulo()." AND parametro='emitir_carne_op'", $boTransacao);
        if ($stEmitirCarneOp == 'true') {
            // faz a inclusão de lançamento, caso tenha sido incluido algum item na listagem orçamentária
            incluirLancamento();
        } else {
            $arItemRetencao = Sessao::read('itemRetencao');
            foreach ($arItemRetencao as $arDadosRetencao) {
                if ($arDadosRetencao['stTipo'] == 'O') {
                    $stCodReceita .= $arDadosRetencao['cod_reduzido'].',';
                }
            }
            // Pega os cógigos do lancamento e da receita para que possam ser enviados para o birt posteriormente (quando monta o link na hora dentro do PR mesmo)
            $stCodReceita = substr($stCodReceita, 0, (strlen($stCodReceita)-1));
            Sessao::write('stCodReceita', $stCodReceita);
        }
    }

    Sessao::write('acao', 816,true);
    if (!$obErro->ocorreu()) {
        if ($_REQUEST['stLiq']) {
            if ($_REQUEST['stEmitirEmpenho']) {
                print '<script type="text/javascript">
                            mudaMenu         ( "Empenho","82",816);
                       </script>';
                $stFiltro = "&acao=".$_REQUEST['acaoEmpenho']."&modulo=".$_REQUEST['moduloEmpenho']."&funcionalidade=".$_REQUEST['funcionalidadeEmpenho']."&acaoOrdem=816";
                if ($_REQUEST['pgDespesasFixas'] != "") {
                    SistemaLegado::alertaAviso(CAM_GF_EMP_INSTANCIAS."empenho/".$_REQUEST['pgDespesasFixas']."?".Sessao::getId()."&stAcao=".$stAcaoEmpenho.$stFiltro,"Código da Ordem: ".$obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obREmpenhoOrdemPagamento->getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                } else {
                    SistemaLegado::alertaAviso($_REQUEST['pgProxEmpenho']."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcaoEmpenho'].$stFiltro,"Código da Ordem: ".$obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obREmpenhoOrdemPagamento->getExercicio(),"incluir","aviso", Sessao::getId(), "../");
                }
            } else {
                print '<script type="text/javascript">
                            mudaMenu         ( "Liquidação","202",816);
                       </script>';
                $stFiltro  = "&acao=".$_REQUEST['acaoLiquidacao']."&modulo=".$_REQUEST['moduloLiquidacao']."&funcionalidade=".$_REQUEST['funcionalidadeLiquidacao']."&acaoOrdem=816";;
                $stFiltro .= "&pg=".$_REQUEST['inPg']."&pos=".$_REQUEST['inPos'];
                SistemaLegado::alertaAviso($_REQUEST['pgProxLiquidacao']."?".Sessao::getId()."&stAcao=".$_REQUEST['stAcaoLiquidacao'].$stFiltro,"Código da Ordem: ".$obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obREmpenhoOrdemPagamento->getExercicio(),"incluir","aviso", Sessao::getId(), "../");
            }
        } else {
            SistemaLegado::alertaAviso($pgForm."?stAcao=incluir","Código da Ordem: ".$obREmpenhoOrdemPagamento->getCodigoOrdem()."/".$obREmpenhoOrdemPagamento->getExercicio(),"incluir","aviso", Sessao::getId(), "../");
        }
        Sessao::geraURLRandomica();

        // Caso tenha mais de um item ou não tenha nenhuma retenção, não precisa gerar os outros relatórios, então continuará gerando pelo FPDF
        // Caso contrário, gera pelo Birt onde há feito os outros 2 relatórios necessários
        if (count($arItens) > 1 || count($arItensRetencao) == 0) {
            $stCaminho = CAM_GF_EMP_INSTANCIAS."ordemPagamento/OCRelatorioOrdemPagamento.php";
            $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodigoOrdem=".$obREmpenhoOrdemPagamento->getCodigoOrdem()."&stDtOrdem=".$obREmpenhoOrdemPagamento->getDataEmissao();
            $stCampos .= "&stExercicio=".$obREmpenhoOrdemPagamento->getExercicio()."&inCodEntidade=";
            $stCampos .= $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade()."&boImplantado=". Sessao::read('bo_implantado');
            $stCampos .= "&dtDataVencimento=".$_REQUEST["dtDataVencimento"]."&stExercicioEmpenho=".$_REQUEST['stExercicioEmpenho'];

            $arFiltro['inCodOrdem'] = $obREmpenhoOrdemPagamento->getCodigoOrdem();
            $arFiltro['stExercicioEmpenho'] = $_REQUEST['stExercicioEmpenho'];
            Sessao::write('filtroRelatorio', $arFiltro);
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."&acao=816','oculto');" );
        } else {

            // envia somente os dados necessários para o arquivo que gera o relatório do birt, colocando-os em um array ($arDados).
            $arFornecedor = explode(' - ', $_REQUEST['stFornecedor']);
            $arDados['inCodFornecedor'] = $arFornecedor[0];
            $arDados['inCodEntidade'] = $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade();
            $arDados['inCodOrdem'] = $obREmpenhoOrdemPagamento->getCodigoOrdem();
            $arDados['stCodReciboExtra'] = Sessao::read('stCodigoRecibo');
            $arDados['stCodLancamento'] = Sessao::read('stCodLancamento');
            $arDados['stCodReceita'] = Sessao::read('stCodReceita');
            $arDados['acao'] = 816;
            Sessao::remove('stCodigoRecibo');
            Sessao::remove('stCodLancamento');
            Sessao::remove('stCodReceita');

            $stCaminho = CAM_GF_EMP_INSTANCIAS."ordemPagamento/OCGeraRelatorioOrdemPagamentoBirt.php";
            $stCampos = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&";
            $stCampos .= http_build_query($arDados); //pega o array $arDados e monta o caminho correto para passar no href
            SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."&acao=816','oculto');" );
        }
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
        SistemaLegado::LiberaFrames();
    }
    
    $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $obREmpenhoOrdemPagamento->obTEmpenhoOrdemPagamento);
    
    break;

case "anular":
    if ($boUtilizarEncerramentoMes == 'true' AND $rsUltimoMesEncerrado->getCampo('mes') >= date('m')) {
        SistemaLegado::LiberaFrames(true,False);
        SistemaLegado::exibeAviso(urlencode("Mês da Anulação encerrado!"),"n_incluir","erro");
        exit;
    }

    // somar valores a anular
    $nuSoma = 0.00;
    foreach ($_REQUEST as $key => $value) {
        if (substr($key,0,8) == 'nuValor_') {
            $nuSoma += str_replace( ',' , '.' , str_replace( '.' ,'' , $value ) );
        }
    }

    $obREmpenhoOrdemPagamento->setCodigoOrdem($_REQUEST["hdnCodigoOrdem"]);
    $obREmpenhoOrdemPagamento->setExercicio($_REQUEST["hdnExercicioOrdem"]);
    $obREmpenhoOrdemPagamento->obROrcamentoEntidade->setCodigoEntidade($_REQUEST["hdnCodigoEntidade"]);
    $obREmpenhoOrdemPagamento->setMotivo($_REQUEST["stMotivoAnulacao"]);
    $obREmpenhoOrdemPagamento->setValorAnulado(number_format( $nuSoma , 2 , ',' , '.' ));

    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada = new TEmpenhoOrdemPagamentoLiquidacaoAnulada();
    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado('cod_ordem', $obREmpenhoOrdemPagamento->getCodigoOrdem());
    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado('cod_entidade', $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade());
    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado('exercicio', $obREmpenhoOrdemPagamento->getExercicio());
    $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->setDado('verifica_saldo','true');
    $obErro = $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->recuperaValorAnular($rsValores, $boTransacao);

    $i = 1;
    $arValoresAnular = array();
    while (!$rsValores->Eof()) {
        $inCodNota = $rsValores->getCampo( 'cod_nota' );
        $stCampo = 'nuValor_' . $inCodNota . '_' . $i++;
        $nuValor = $_REQUEST[$stCampo];

        $nuValor = str_replace('.' , '' , $nuValor);
        $nuValor = str_replace(',' , '.', $nuValor);

        if ($rsValores->getCampo('vl_a_anular') < $nuValor) {
            $obErro->setDescricao( 'Valor a anular maior que o valor disponível!' );
            break;
        } elseif ($nuValor <= "0.00" && $nuSoma == 0) {
            $obErro->setDescricao( 'Valor da Anulação deve ser maior que zero!' );
            break;
        } else {
            $obOPLA = new TEmpenhoOrdemPagamentoLiquidacaoAnulada();
            $obOPLA->setDado('exercicio', $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->getDado('exercicio'));
            $obOPLA->setDado('cod_entidade', $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->getDado('cod_entidade'));
            $obOPLA->setDado('cod_ordem', $obTEmpenhoOrdemPagamentoLiquidacaoAnulada->getDado('cod_ordem'));
            $obOPLA->setDado('exercicio_liquidacao', $rsValores->getCampo('exercicio_liquidacao'));
            $obOPLA->setDado('cod_nota', $rsValores->getCampo('cod_nota'));
            $obOPLA->setDado('vl_anulado', $nuValor);

            // Array com as informações das anulações de liquidacao
            $arOPLA[] = $obOPLA;
        }

        $rsValores->proximo();
    }

    if ( !$obErro->ocorreu() ) {
        $obREmpenhoOrdemPagamento->setOrdemPagamentoLiquidacaoAnulada($arOPLA);
        $obErro = $obREmpenhoOrdemPagamento->anular();
    }

    if (!$obErro->ocorreu()) {
        SistemaLegado::alertaAviso($pgList."?stAcao=anular","Código da Ordem: ".$_REQUEST["hdnCodigoOrdem"]. "/". $_REQUEST["hdnExercicioOrdem"],"anular","aviso", Sessao::getId(), "../");
        $stCaminho = CAM_GF_EMP_INSTANCIAS."ordemPagamento/OCRelatorioOrdemPagamentoAnulado.php";
        $stCampos  = "?".Sessao::getId()."&stAcao=imprimir&stCaminho=".$stCaminho."&inCodigoOrdem=".$obREmpenhoOrdemPagamento->getCodigoOrdem();
        $stCampos .= "&stExercicioOrdem=".$obREmpenhoOrdemPagamento->getExercicio()."&inCodEntidade=";
        $stCampos .= $obREmpenhoOrdemPagamento->obROrcamentoEntidade->getCodigoEntidade()."&boImplantado=".$_REQUEST['boImplantado'];
        $stCampos .= "&dtDataVencimento=". $_REQUEST["dtDataVencimento"];
        $stCampos .= "&stTimestampAnulado=". $obREmpenhoOrdemPagamento->getTimestampAnulacao();
        SistemaLegado::executaFrameOculto( "var x = window.open('".CAM_FW_POPUPS."relatorio/OCRelatorio.php".$stCampos."','oculto');" );
    } else {
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_anular","erro");
        SistemaLegado::LiberaFrames();
    }
    break;
}

/**
 * incluirReciboExtra
 *
 * realiza o processamento de inclusao de recibo de receita direto
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @return void
 */
function incluirReciboExtra($obREmpenhoOrdemPagamento, $boTransacao = "")
{
    /* includes de mapeamento necessários */
    include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtra.class.php';
    include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtraCredor.class.php';
    include_once CAM_GF_TES_MAPEAMENTO.'TTesourariaReciboExtraRecurso.class.php';
    include_once CAM_GF_EMP_MAPEAMENTO.'TEmpenhoOrdemPagamentoReciboExtra.class.php';

    global $request;
    
    $obErro = new Erro;
    $obTReciboExtra = new TTesourariaReciboExtra;
    $obTReciboExtra->setDado('cod_entidade', $_POST['inCodEntidade']);
    $obTReciboExtra->setDado('tipo_recibo','R');
    $obTReciboExtra->setDado('exercicio',Sessao::getExercicio());
    $obTReciboExtra->recuperaUltimaDataRecibo($rsDataRecibo, $boTransacao);
    
    //$boFlagTransacao = false;
    $obTransacao = new Transacao;

    $obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
    $stCodigoRecibo = '';
    $arItemRetencao = Sessao::read('itemRetencao');
    foreach ($arItemRetencao as $arDadosRetencao) {
        if ($arDadosRetencao['stTipo'] == 'E') {
            $obTReciboExtra->proximoCod($inCodigoRecibo, 'P');
            $obTReciboExtra->setDado('cod_recibo_extra', $inCodigoRecibo);
            $obTReciboExtra->setDado('cod_plano', $arDadosRetencao['cod_reduzido']);
            $obTReciboExtra->setDado('valor', str_replace(",", ".", str_replace(".", "", $arDadosRetencao['nuValor'])));
            $obTReciboExtra->setDado('timestamp', substr($request->get('stDtOrdem'), 6, 4).'-'.substr($request->get('stDtOrdem'), 3, 2).'-'.substr($request->get('stDtOrdem'),0,2).date(' H:i:s.ms'));
            $obErro = $obTReciboExtra->inclusao($boTransacao);

            if (!$obErro->ocorreu() && $_REQUEST['stFornecedor'] != "") {
                $arCodCredor = explode(" - ", $_REQUEST['stFornecedor']);
                $inCodCredor = $arCodCredor[0];

                $obCredor = new TTesourariaReciboExtraCredor;
                $obCredor->obTTesourariaReciboExtra = &$obTReciboExtra;
                $obCredor->setDado('numcgm', $inCodCredor);
                $obErro = $obCredor->inclusao($boTransacao);
            }

            $inCodRecurso = SistemaLegado::pegaDado('cod_recurso', 'contabilidade.plano_recurso', 'WHERE exercicio = \''.Sessao::getExercicio().'\' AND cod_plano = '.$arDadosRetencao['cod_reduzido']);
            if (!$obErro->ocorreu() && $inCodRecurso != "") {
                $obRecurso = new TTesourariaReciboExtraRecurso;
                $obRecurso->obTTesourariaReciboExtra = $obTReciboExtra;
                $obRecurso->setDado('cod_recurso', $inCodRecurso);
                $obErro = $obRecurso->inclusao($boTransacao);
            }

            $stCodigoRecibo .= $inCodigoRecibo.',';

            if (!$obErro->ocorreu()) {
                $obTEmpenhoOrdemPagamentoReciboExtra = new TEmpenhoOrdemPagamentoReciboExtra;
                $obTEmpenhoOrdemPagamentoReciboExtra->setDado('cod_entidade'    , $_POST['inCodEntidade']);
                $obTEmpenhoOrdemPagamentoReciboExtra->setDado('cod_ordem'       , $obREmpenhoOrdemPagamento->getCodigoOrdem());
                $obTEmpenhoOrdemPagamentoReciboExtra->setDado('tipo_recibo'     , 'R');
                $obTEmpenhoOrdemPagamentoReciboExtra->setDado('exercicio'       , Sessao::getExercicio());
                $obTEmpenhoOrdemPagamentoReciboExtra->setDado('cod_recibo_extra', $inCodigoRecibo);
                $obTEmpenhoOrdemPagamentoReciboExtra->inclusao($boTransacao);
            }
        }
    }

    // Pega os cógigos do recibo para que possam ser enviados para o birt posteriormente (quando monta o link na hora dentro do PR mesmo)
    $stCodigoRecibo = substr($stCodigoRecibo, 0, (strlen($stCodigoRecibo)-1));
    Sessao::write('stCodigoRecibo', $stCodigoRecibo);

    // Adiciona a assinatura selecionada no ordem de pagamento para as assintaturas do recibo extra,
    // porém como o tipo é 'R' então somente poderá ser o papel=tesoureiro, sendo o num_assinatura=1
    // para que possa ser adicionada alguma assinatura
    $arAssinaturas = Sessao::read('assinaturas');
    if ( isset($arAssinaturas) && count($arAssinaturas['selecionadas']) > 0 ) {
        foreach ($arAssinaturas['selecionadas'] as $arDados) {
            if ($arDados['papel'] == 'tesoureiro') {
                include_once CAM_GF_TES_MAPEAMENTO."TTesourariaReciboExtraAssinatura.class.php";
                $obTTesReciboExtraAssinatura = new TTesourariaReciboExtraAssinatura;
                //Sessao::getTransacao()->setMapeamento($obTTesReciboExtraAssinatura);
                $obTTesReciboExtraAssinatura->setDado( 'exercicio', $obTReciboExtra->getDado('exercicio') );
                $obTTesReciboExtraAssinatura->setDado( 'cod_entidade', $obTReciboExtra->getDado('cod_entidade') );
                $obTTesReciboExtraAssinatura->setDado( 'cod_recibo_extra', $obTReciboExtra->getDado('cod_recibo_extra') );
                $obTTesReciboExtraAssinatura->setDado( 'tipo_recibo', 'R' );
                $obTTesReciboExtraAssinatura->setDado( 'num_assinatura', 1 ); // numero 1 quando o tipo_recibo=R quer dizer tesoureiro
                $obTTesReciboExtraAssinatura->setDado( 'numcgm', $arDados['inCGM'] );
                $obTTesReciboExtraAssinatura->setDado( 'cargo', $arDados['stCargo'] );
                $obErro = $obTTesReciboExtraAssinatura->inclusao($boTransacao);
                break;
            }
        }
    }
    $obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTReciboExtra );
}

/**
 * incluirLancamento
 *
 * realiza o processamento de inclusao de lancamento
 *
 * @author Henrique Girardi dos Santos <henrique.santos@cnm.org.br>
 * @return void
 */
function incluirLancamento()
{
    /* includes de regra de negocio necessarios */
    include_once CAM_GT_ARR_NEGOCIO."RARRLancamento.class.php";
    include_once CAM_GT_ARR_NEGOCIO."RARRCalculo.class.php";

    /* includes de mapeamento necessarios */
    include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoFuncao.class.php";
    include_once CAM_GT_MON_MAPEAMENTO."TMONTipoConvenio.class.php";

    // codigo do fornecedor
    $arNumCgm = explode(" - ", $_REQUEST['stFornecedor']);
    $inNumCgm = $arNumCgm[0];

    $obRARRLancamento = new RARRLancamento(new RARRCalculo);
    $obRARRLancamento->obRCgm->setNumCGM($inNumCgm);
    $obRARRLancamento->obRARRCarne->inCodContribuinteInicial = $inNumCgm;

    // Necessário passar esses dados para a sessao para que seja pego os dados na regra de negocio do lancamento
    $arParcelas[0]['data_vencimento'] = $_REQUEST["dtDataVencimento"];
    $arParcelas[0]['stTipoParcela'] = 'Única';
    Sessao::write('parcelas', $arParcelas);

    $stCodReceita = '';
    $stCodLancamento = '';
    $arItemRetencao = Sessao::read('itemRetencao');
    foreach ($arItemRetencao as $arDadosRetencao) {
        if ($arDadosRetencao['stTipo'] == 'O') {
            // PEGAR O CREDITO RELACIONADO COM A RECEITA
            $arCredito = array();
            $arCredito = explode ('.', $arDadosRetencao['inCodCredito'] );
            $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodCredito($arCredito[0]);
            $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodEspecie($arCredito[1]);
            $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodGenero($arCredito[2]);
            $obRARRLancamento->roRARRCalculo->obRMONCredito->setCodNatureza($arCredito[3]);

            $obRARRLancamento->obRARRCarne->setExercicio($_REQUEST['stExercicioEmpenho']);
            $obRARRLancamento->setValor($arDadosRetencao['nuValor']);
            $obRARRLancamento->setDataVencimento($_REQUEST["dtDataVencimento"]);

            $obRARRLancamento->setObservacao('');
            $obRARRLancamento->setObservacaoSistema('');
            $obRARRLancamento->setTotalParcelas(1);
            $obRARRLancamento->setTotalParcelasUnicas(1);

            $obErro = $obRARRLancamento->efetuarLancamentoManualCredito();
            $stCodLancamento .= $obRARRLancamento->getCodLancamento().',';
            $stCodReceita .= $arDadosRetencao['cod_reduzido'].',';
        }
    }

    // Pega os cógigos do lancamento e da receita para que possam ser enviados para o birt posteriormente (quando monta o link na hora dentro do PR mesmo)
    $stCodLancamento = substr($stCodLancamento, 0, (strlen($stCodLancamento)-1));
    $stCodReceita = substr($stCodReceita, 0, (strlen($stCodReceita)-1));
    Sessao::write('stCodLancamento', $stCodLancamento);
    Sessao::write('stCodReceita', $stCodReceita);
}

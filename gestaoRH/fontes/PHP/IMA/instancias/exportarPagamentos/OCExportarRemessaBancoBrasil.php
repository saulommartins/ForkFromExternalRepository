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
    * Página de Oculto do Exportação Remessa Banco do Brasil
    * Data de Criação: 01/12/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.01

    $Id: OCExportarRemessaBancoBrasil.php 65862 2016-06-22 18:50:14Z michel $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroPensionista.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php";
include_once CAM_GRH_PES_COMPONENTES."IFiltroCGMPensionista.class.php";
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php";
include_once CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRemessaBancoBrasil";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

###########################LIMPA SPANS#####################################

function limparSpans()
{
    #Cadastro
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '';\n";

    #Ativos / Aposentados / Pensionistas
    $stJs .= gerarSpanComplementar(false);

    return $stJs;
}

###########################LIMPA SPANS#####################################

function gerarSpan($stSituacao)
{
    switch ($stSituacao) {
        case "pensionistas"://pensionistas
            return gerarSpanPensionistas();
        break;

        case "estagiarios"://estagiarios
            return gerarSpanEstagiarios();
        break;

        case "pensao_judicial"://pensao judicial
            return gerarSpanPensaoJudicial();
        break;
    
        default:
            return gerarSpanGeral($stSituacao);
        break;
    }
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanGeral($stSituacao)
{
    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";
    $obIFiltroComponentes = new IFiltroComponentes();

    if ($stSituacao != 'todos') {
       $obIFiltroComponentes->setMatricula();
       $obIFiltroComponentes->setCGMMatricula();
       $obIFiltroComponentes->setLocal();
       $obIFiltroComponentes->setAtributoServidor();
    }

    $obIFiltroComponentes->setCargo();
    $obIFiltroComponentes->setFuncao();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'ativos':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentados':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindidos':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
        case 'todos':
                $obFormulario->addTitulo("Todos");
                $obIFiltroComponentes->setTodos();
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true);

    return $stJs;
}

###########################PENSIONISTAS#####################################

function gerarSpanPensionistas()
{
    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatriculaPensionista();
    $obIFiltroComponentes->setCGMMatriculaPensionista();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true, true);

    return $stJs;
}

###########################ESTAGIARIOS#####################################

function gerarSpanEstagiarios()
{
    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php";
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setCGMCodigoEstagio();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setAtributoEstagiario();
    $obIFiltroComponentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Estagiários");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################ATIVOS E APOSENTADOS E PENSIONISTAS #####################################

function gerarSpanComplementar($boGerar = true)
{
    if ($boGerar) {
        include_once CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php";
        $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
        $stOnChange  = $obIFiltroCompetencia->obCmbMes->obEvento->getOnChange();
        $stOnChange .= " montaParametrosGET('atualizarGrupoConta','inAno,inCodMes');";
        $obIFiltroCompetencia->obCmbMes->obEvento->setOnChange($stOnChange);

        include_once CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php";
        $obIFiltroTipoFolha = new IFiltroTipoFolha();
        $obIFiltroTipoFolha->setMostraDesdobramento(true,"D");
        $obIFiltroTipoFolha->setValorPadrao(1);

        $obFormulario = new Formulario();
        $obIFiltroCompetencia->geraFormulario($obFormulario);
        $obIFiltroTipoFolha->geraFormulario($obFormulario);
        $obFormulario->montaInnerHTML();
        $stHtml = $obFormulario->getHTML();
    }
    $stJs .= "d.getElementById('spnAtivosAposentadosPensionistas').innerHTML = '$stHtml';\n";

    return $stJs;
}

########################### PENSAO JUDICIAL #####################################

function gerarSpanPensaoJudicial()
{
    $stJs .= limparSpans();

    include_once CAM_GRH_PES_COMPONENTES."IFiltroComponentesDependentes.class.php";
    $obIFiltroComponentesDependentes = new IFiltroComponentesDependentes();
    $obIFiltroComponentesDependentes->setCGMDependente();
    $obIFiltroComponentesDependentes->setCGMMatriculaServidorDependente();
    $obIFiltroComponentesDependentes->setFiltroPadrao('geral');

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensão Judicial");

    $obIFiltroComponentesDependentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";
    $stJs .= gerarSpanComplementar(true);

    return $stJs;
}

###########################UTILS##########################

function submeter(Request $request)
{
    $obErro = new Erro();

    $stSituacao = $request->get("stSituacao", "");

    if ($stSituacao == "")
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");

    if ($stSituacao == 'ativos' or $stSituacao == 'aposentados' or $stSituacao == 'pensionistas' or $stSituacao == 'rescindidos' or $stSituacao == 'todos' or $stSituacao == 'pensao_judicial') {
        $inCodMes          = $request->get("inCodMes");
        $inAno             = $request->get("inAno");
        $inCodComplementar = $request->get("inCodComplementar");
        $stDesdobramento   = $request->get("stDesdobramento");

        if ( empty($inCodMes) )
            $obErro->setDescricao($obErro->getDescricao()."@Campo Mês da Competência inválido!()");
        else {
            if ( empty($inAno) )
                $obErro->setDescricao($obErro->getDescricao()."@Campo Ano da Competência inválido!()");
        }

        //Tipo Folha
        if ($request->get("inCodConfiguracao", "") == '')
            $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Cálculo inválido!()");
        else {
            if ($request->get("inCodConfiguracao") == 0) {
                if ( empty($inCodComplementar) )
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Folha Complementar inválido!()");
            } elseif ($request->get("inCodConfiguracao") == 3) {//Decimo
               if ( empty($stDesdobramento) )
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Desdobramento inválido!()");
            }
        }

        switch ($request->get("stTipoFiltro")) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro inválido!()");
                break;
            case "contrato":
            case "contrato_rescisao":
            case "cgm_contrato":
            case "cgm_contrato_rescisao":
                if ( count(Sessao::read("arContratos")) == 0 )
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                break;
            case "contrato_pensionista":
            case "cgm_contrato_pensionista":
                if ( count(Sessao::read("arPensionistas")) == 0 )
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                break;
            case "atributo_servidor":
                if ($request->get("inCodAtributo", "") == "")
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                break;
            case "atributo_pensionista":
                if ($request->get("inCodAtributo", "") == "")
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                break;
        }
    }

    if ($stSituacao == 'estagiarios') {
        switch ($request->get("stTipoFiltro")) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Opções do Estagiário inválido!()");
                break;
            case "cgm_codigo_estagio":
                if ( count(Sessao::read("arEstagios")) == 0 )
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de estagiários deve possuir pelo menos um estágio!()");
                break;
            case "atributo_estagiario":
                if ($request->get("inCodAtributo", "") == "")
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Estagiário inválido!()");
                break;
        }
    }

    $rsConfiguracaoConvenio = Sessao::read("rsConfiguracaoConvenio");
    if ($rsConfiguracaoConvenio->getNumLinhas() == -1)
        $obErro->setDescricao($obErro->getDescricao()."@Campo Código do Convênio no Banco inválido!()");

    $rsContas = Sessao::read("rsContas");
    if ($rsContas->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Agência do Convênio inválido!()");
        $obErro->setDescricao($obErro->getDescricao()."@Campo Conta do Convênio inválido!()");
    }

    $inNumeroSequencial = $request->get('inNumeroSequencial');
    if ( empty($inNumeroSequencial) )
        $obErro->setDescricao($obErro->getDescricao()."@Campo Número Seqüencial Arquivo inválido!()");

    if ( $obErro->ocorreu() )
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    else
        $stJs .= "parent.frames[2].Salvar(); BloqueiaFrames(true,false);\n";

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpan','stSituacao');";

    return $stJs;
}

function validarValores(Request $request)
{
    $nuValorLiquidoInicial = $request->get('nuValorLiquidoInicial');
    $nuValorLiquidoFinal = $request->get('nuValorLiquidoFinal');
    
    if ( !($nuValorLiquidoInicial === 0 and $nuValorLiquidoFinal === 0) ) {
        if ($nuValorLiquidoFinal === 0) {
            $nuValorLiquidoFinal = "";
            $stJs .= "f.nuValorLiquidoFinal.value = '';\n";
            if ($nuValorLiquidoInicial >= 0)
                $stJs .= "alertaAviso('@O valor líquido final deve ser maior que o valor líquido inicial!()','form','aviso','".Sessao::getId()."');";
        }
        if ($nuValorLiquidoInicial != "" and $nuValorLiquidoFinal != "") {
            $nuValorLiquidoInicial = str_replace('.','',$nuValorLiquidoInicial);
            $nuValorLiquidoInicial = str_replace(',','.',$nuValorLiquidoInicial);

            $nuValorLiquidoFinal = str_replace('.','',$nuValorLiquidoFinal);
            $nuValorLiquidoFinal = str_replace(',','.',$nuValorLiquidoFinal);
            if ($nuValorLiquidoInicial > $nuValorLiquidoFinal) {
                $stJs .= "f.nuValorLiquidoInicial.value = '';\n";
                $stJs .= "f.nuValorLiquidoFinal.value = '';\n";
                $stJs .= "alertaAviso('@O valor líquido inicial deve ser menos que o valor líquido final!()','form','aviso','".Sessao::getId()."');";
            }
        }
    }

    return $stJs;
}

function validarDataPagamento(Request $request)
{
    $obErro = new Erro();
    $arDtPagamento = explode("/",$request->get("dtPagamento"));
    $arDtGeracaoArquivo = explode("/",$request->get("dtGeracaoArquivo"));
    $dtPagamento = $arDtPagamento[2]."-".$arDtPagamento[1]."-".$arDtPagamento[0];
    $dtGeracaoArquivo = $arDtGeracaoArquivo[2]."-".$arDtGeracaoArquivo[1]."-".$arDtGeracaoArquivo[0];
    if ($dtPagamento<$dtGeracaoArquivo)
        $obErro->setDescricao("O campo Data do Pagamento deve ser superior a Data da Geração Arquivo");

    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('@".$obErro->getDescricao()."!()','form','aviso','".Sessao::getId()."');";
        $stJs .= "f.dtPagamento.value = '';\n";
    }

    return $stJs;
}

function atualizarGrupoConta(Request $request)
{
    include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
    include_once CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBbConta.class.php";

    $inCodMes = $request->get('inCodMes');
    $inAno = $request->get('inAno');
    
    if (trim($inCodMes)!="" && trim($inAno)!="") {
        
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$inCodMes);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$inAno);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao,$stFiltro);
    } else {
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    }

    $obTIMAConfiguracaoBbConta = new TIMAConfiguracaoBbConta();

    $stFiltro = " WHERE vigencia <= to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd/mm/yyyy')";
    $stOrdem = " ORDER BY dt_vigencia DESC LIMIT 1";
    $obTIMAConfiguracaoBbConta->recuperaVigencias($rsVigencia,$stFiltro,$stOrdem);

    $obTIMAConfiguracaoBbConta->setDado("vigencia",$rsVigencia->getCampo("vigencia"));
    $obTIMAConfiguracaoBbConta->recuperaRelacionamento($rsConfiguracaoBbConta);
    Sessao::write("rsContas",$rsConfiguracaoBbConta);

    $obLista = new Lista;
    $obLista->setTitulo("Grupos de Contas do Convênio");
    $obLista->setRecordSet( $rsConfiguracaoBbConta );
    $obLista->setMostraPaginacao( false );
    $obLista->setNumeracao(false);

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Agência");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Conta");
    $obLista->ultimoCabecalho->setWidth( 20 );
    $obLista->commitCabecalho();

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("Descrição");
    $obLista->ultimoCabecalho->setWidth( 40 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[num_agencia]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[num_conta_corrente]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[descricao]" );
    $obLista->commitDado();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = " jQuery('#spnGrupoContas').html('".$stHtml."');   \n";

    return $stJs;
}

switch ($request->get('stCtrl')) {
    case "gerarSpan":
        $stJs .= gerarSpan($request->get('stSituacao'));
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparForm":
        $stJs .= limparForm();
        break;
    case "submeter":
        $stJs .= submeter($request);
        break;
    case "validarValores":
        $stJs .= validarValores($request);
        break;
    case "validarDataPagamento":
        $stJs .= validarDataPagamento($request);
        break;
    case "atualizarGrupoConta":
        $stJs .= atualizarGrupoConta($request);
        break;
}

if ($stJs)
    echo $stJs;

?>

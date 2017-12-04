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
   * Exportação Banco HSBC
   * Data de Criação   : 14/12/2009

   * @author Analista      Dagiane Vieira
   * @author Desenvolvedor Diego Mancilha

   * @package URBEM
   * @subpackage Instancias

     $Id:$
   */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMContrato.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."IFiltroCGMPensionista.class.php"                                    );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"                                );
include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLocal.class.php"                                  );

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoHSBC";
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

function gerarSpan()
{
    switch ($_GET["stSituacao"]) {
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
            return gerarSpanGeral();
            break;
    }
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanGeral()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();

    if ($_GET["stSituacao"] != 'todos') {
       $obIFiltroComponentes->setMatricula();
       $obIFiltroComponentes->setCGMMatricula();
       $obIFiltroComponentes->setLocal();
       $obIFiltroComponentes->setAtributoServidor();
    }

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
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
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
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
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
        include_once(CAM_GRH_PES_COMPONENTES."IFiltroCompetencia.class.php");
        $obIFiltroCompetencia = new IFiltroCompetencia(true,"",true);
        $stOnChange  = $obIFiltroCompetencia->obCmbMes->obEvento->getOnChange();
        $stOnChange .= " montaParametrosGET('atualizarGrupoConta','inAno,inCodMes');";
        $obIFiltroCompetencia->obCmbMes->obEvento->setOnChange($stOnChange);

        include_once(CAM_GRH_PES_COMPONENTES."IFiltroTipoFolha.class.php");
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
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentesDependentes.class.php");
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

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stSituacao"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");
    }

    if ($_GET['stSituacao'] == 'ativos' or
        $_GET['stSituacao'] == 'aposentados' or
        $_GET['stSituacao'] == 'pensionistas' or
        $_GET['stSituacao'] == 'rescindidos' or
        $_GET['stSituacao'] == 'todos' or
        $_GET['stSituacao'] == 'pensao_judicial') {
        if ( empty($_GET["inCodMes"]) ) {
              $obErro->setDescricao($obErro->getDescricao()."@Campo Mês da Competência inválido!()");
        } else {
            if ( empty($_GET["inAno"]) ) {
                $obErro->setDescricao($obErro->getDescricao()."@Campo Ano da Competência inválido!()");
            }
        }

        //Tipo Folha
        if ($_GET["inCodConfiguracao"] == '') {
            $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Cálculo inválido!()");
        } else {
            if ($_GET["inCodConfiguracao"] == 0) {
                if ( empty($_GET["inCodComplementar"]) ) {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Folha Complementar inválido!()");
                }
            } elseif ($_GET["inCodConfiguracao"] == 3) {//Decimo
               if ( empty($_GET["stDesdobramento"]) ) {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Desdobramento inválido!()");
               }
            }
        }
    }

    if ($_GET["stSituacao"] == 'ativos' or
        $_GET["stSituacao"] == 'aposentados' or
        $_GET["stSituacao"] == 'pensionistas' or
        $_GET["stSituacao"] == 'rescindidos' or
        $_GET["stSituacao"] == 'todos' or
        $_GET["stSituacao"] == 'pensao_judicial') {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Tipo de Filtro inválido!()");
                break;
            case "contrato":
            case "contrato_rescisao":
            case "cgm_contrato":
            case "cgm_contrato_rescisao":
                if ( count(Sessao::read("arContratos")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "contrato_pensionista":
            case "cgm_contrato_pensionista":
                if ( count(Sessao::read("arPensionistas")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de contratos deve possuir pelo menos um contrato!()");
                }
                break;
            case "atributo_servidor":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                }
                break;
            case "atributo_pensionista":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                }
                break;
        }
    }

    if ($_GET["stSituacao"] == 'estagiarios') {
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Opções do Estagiário inválido!()");
                break;
            case "cgm_codigo_estagio":
                if ( count(Sessao::read("arEstagios")) == 0 ) {
                    $obErro->setDescricao($obErro->getDescricao()."@A lista de estagiários deve possuir pelo menos um estágio!()");
                }
                break;
            case "atributo_estagiario":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Estagiário inválido!()");
                }
                break;
        }
    }

    $rsConfiguracaoConvenio = Sessao::read("rsConfiguracaoConvenio");
    if ($rsConfiguracaoConvenio->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Código do Convênio no Banco inválido!()");
    }

    $rsContas = Sessao::read("rsContas");
    if ($rsContas->getNumLinhas() == -1) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Agência do Convênio inválido!()");
        $obErro->setDescricao($obErro->getDescricao()."@Campo Conta do Convênio inválido!()");
    }

    if (!preg_match('/^[1-9]{1,1}[0-9]*$/', trim($_GET['inNumeroSequencial']))) {
        $obErro->setDescricao($obErro->getDescricao()."@Número sequencial inválido!()");
    }

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $stJs .= "parent.frames[2].Salvar(); BloqueiaFrames(true,false);\n";
    }

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpan','stSituacao');";

    return $stJs;
}

function validarValores()
{
    if ( !($_GET['nuValorLiquidoInicial'] === 0 and $_GET['nuValorLiquidoFinal'] === 0) ) {
        if ($_GET['nuValorLiquidoFinal'] === 0) {
            $_GET['nuValorLiquidoFinal'] = "";
            $stJs .= "f.nuValorLiquidoFinal.value = '';\n";
            if ($_GET['nuValorLiquidoInicial'] >= 0) {
                $stJs .= "alertaAviso('@O valor líquido final deve ser maior que o valor líquido inicial!()','form','aviso','".Sessao::getId()."');";
            }
        }
        if ($_GET['nuValorLiquidoInicial'] != "" and $_GET['nuValorLiquidoFinal'] != "") {
            $nuValorLiquidoInicial = str_replace('.','',$_GET['nuValorLiquidoInicial']);
            $nuValorLiquidoInicial = str_replace(',','.',$nuValorLiquidoInicial);

            $nuValorLiquidoFinal = str_replace('.','',$_GET['nuValorLiquidoFinal']);
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

function validarDataPagamento()
{
    $obErro = new Erro();
    $arDtPagamento = explode("/",$_GET["dtPagamento"]);
    $arDtGeracaoArquivo = explode("/",$_GET["dtGeracaoArquivo"]);
    $dtPagamento = $arDtPagamento[2]."-".$arDtPagamento[1]."-".$arDtPagamento[0];
    $dtGeracaoArquivo = $arDtGeracaoArquivo[2]."-".$arDtGeracaoArquivo[1]."-".$arDtGeracaoArquivo[0];
    if ($dtPagamento<$dtGeracaoArquivo) {
        $obErro->setDescricao("O campo Data do Pagamento deve ser superior a Data da Geração Arquivo");
    }
    if ($obErro->ocorreu()) {
        $stJs  = "alertaAviso('@".$obErro->getDescricao()."!()','form','aviso','".Sessao::getId()."');";
        $stJs .= "f.dtPagamento.value = '';\n";
    }

    return $stJs;
}

function atualizarGrupoConta()
{
    if (trim($_REQUEST["inCodMes"])!="" && trim($_REQUEST["inAno"])!="") {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_REQUEST["inCodMes"]);
        $obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_REQUEST["inAno"]);
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao,$stFiltro);
    } else {
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
        $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
    }

    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoHSBCConta.class.php");
    $obTIMAConfiguracaoHSBCConta = new TIMAConfiguracaoHSBCConta();

    $stFiltro = " WHERE vigencia <= to_date('".$rsPeriodoMovimentacao->getCampo("dt_final")."','dd/mm/yyyy')";
    $stOrdem = " ORDER BY dt_vigencia DESC LIMIT 1";
    $obTIMAConfiguracaoHSBCConta->recuperaVigencias($rsVigencia,$stFiltro,$stOrdem);

    $obTIMAConfiguracaoHSBCConta->setDado("vigencia",$rsVigencia->getCampo("vigencia"));
    $obTIMAConfiguracaoHSBCConta->recuperaRelacionamento($rsConfiguracaoHSBCConta);
    Sessao::write("rsContas",$rsConfiguracaoHSBCConta);

    $obLista = new Lista;
    $obLista->setTitulo("Grupos de Contas do Convênio");
    $obLista->setRecordSet( $rsConfiguracaoHSBCConta );
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

switch ($_GET['stCtrl']) {
    case "gerarSpan":
        $stJs .= gerarSpan();
        break;
    case "limparSpans":
        $stJs .= limparSpans();
        break;
    case "limparForm":
        $stJs .= limparForm();
        break;
    case "submeter":
        $stJs .= submeter();
        break;
    case "validarValores":
        $stJs .= validarValores();
        break;
    case "validarDataPagamento":
        $stJs .= validarDataPagamento();
        break;
    case "atualizarGrupoConta":
        $stJs .= atualizarGrupoConta();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>

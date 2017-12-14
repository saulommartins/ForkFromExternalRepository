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
    * Página de oculto do IMA configuração - BESC
    * Data de Criação: 27/09/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: alex $
    $Date: 2007-09-27 16:08:17 -0300 (Qui, 27 Set 2007) $

    * Casos de uso: uc-04.08.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ExportacaoBancoBESC";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

function validarConfBESC()
{
    $obErro = new Erro;
    if (trim($_GET["stNumAgenciaTxt"]) == "") {
        $obErro->setDescricao("Campo Agência inválido!()");
    }
    if (trim($_GET["stContaCorrente"]) == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Conta Corrente inválido!()");
    }
    $arContasConvenio = Sessao::read("arContasConvenio");
    if (is_array($arContasConvenio)) {
        if ($_GET["stCtrl"] == "incluirConfBESC") {
            foreach ($arContasConvenio as $arTemp) {
                if ($arTemp["stContaCorrente"] == $_GET["stContaCorrente"]) {
                    $obErro->setDescricao($obErro->getDescricao()."@Valor do campo Conta Corrente já inserido na lista!(".$_GET["stContaCorrente"].")");
                    break;
                }
            }
        }
        if ($_GET["stCtrl"] == "alterarConfBESC") {
            foreach ($arContasConvenio as $arTemp) {
                if ($arTemp["stContaCorrente"] == $_GET["stContaCorrente"] and $arTemp["inId"] != Sessao::read("inId")) {
                    $obErro->setDescricao($obErro->getDescricao()."@Valor do campo Conta Corrente já inserido na lista!(".$_GET["stContaCorrente"].")");
                    break;
                }
            }
        }
    }

    return $obErro;
}

function montarListaConfBESC()
{
    $arContasConvenio = Sessao::read("arContasConvenio");
    $rsContasConvenio = new Recordset;
    $rsContasConvenio->preenche($arContasConvenio);

    $obLista = new Lista;
    $obLista->setTitulo("Lista de Contas do Convênio");
    $obLista->setRecordSet( $rsContasConvenio );
    $obLista->setMostraPaginacao( false );

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 5 );
    $obLista->commitCabecalho();

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

    $obLista->addCabecalho();
    $obLista->ultimoCabecalho->addConteudo("&nbsp;");
    $obLista->ultimoCabecalho->setWidth( 2 );
    $obLista->commitCabecalho();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[stNumAgenciaTxt]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("CENTRO");
    $obLista->ultimoDado->setCampo( "[stContaCorrente]" );
    $obLista->commitDado();

    $obLista->addDado();
    $obLista->ultimoDado->setAlinhamento("ESQUERDA");
    $obLista->ultimoDado->setCampo( "[stDescricaoConvenio]" );
    $obLista->commitDado();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "ALTERAR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:BloqueiaFrames(true,false);executaFuncaoAjax('montaAlterarConfBESC');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->addAcao();
    $obLista->ultimaAcao->setAcao( "EXCLUIR" );
    $obLista->ultimaAcao->setFuncaoAjax( true );
    $obLista->ultimaAcao->setLink( "JavaScript:executaFuncaoAjax('excluirConfBESC');" );
    $obLista->ultimaAcao->addCampo("1","inId");
    $obLista->commitAcao();

    $obLista->montaHTML();
    $stHtml = $obLista->getHTML();
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);

    $stJs = "jQuery('#spnContasConvenio').html('".$stHtml."');\n";

    return $stJs;
}

function incluirConfBESC()
{
    $obErro = validarConfBESC();
    if (!$obErro->ocorreu()) {
        $arContasConvenio = Sessao::read("arContasConvenio");
        $arTemp["inId"]                     = count($arContasConvenio);
        $arTemp["stNumAgenciaTxt"]          = $_GET["stNumAgenciaTxt"];
        $arTemp["stContaCorrente"]          = $_GET["stContaCorrente"];
        $arTemp["stDescricaoConvenio"]      = $_GET["stDescricaoConvenio"];
        $arTemp["inCodLotacaoSelecionados"] = $_GET["inCodLotacaoSelecionados"];
        $arTemp["inCodLocalSelecionados"]   = $_GET["inCodLocalSelecionados"];
        $arContasConvenio[] = $arTemp;
        Sessao::write("arContasConvenio",$arContasConvenio);
        $stJs .= montarListaConfBESC();
        $stJs .= "limpaFormularioConfBESC();";
        $stJs .= limparConfBESCExtra();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function alterarConfBESC()
{
    $obErro = validarConfBESC();
    if (!$obErro->ocorreu()) {
        $arContasConvenio = Sessao::read("arContasConvenio");
        $arTemp["inId"]                     = Sessao::read("inId");
        $arTemp["stNumAgenciaTxt"]          = $_GET["stNumAgenciaTxt"];
        $arTemp["stContaCorrente"]          = $_GET["stContaCorrente"];
        $arTemp["stDescricaoConvenio"]      = $_GET["stDescricaoConvenio"];
        $arTemp["inCodLotacaoSelecionados"] = $_GET["inCodLotacaoSelecionados"];
        $arTemp["inCodLocalSelecionados"]   = $_GET["inCodLocalSelecionados"];
        $arContasConvenio[Sessao::read("inId")] = $arTemp;

        Sessao::write("arContasConvenio",$arContasConvenio);
        $stJs .= montarListaConfBESC();
        $stJs .= "limpaFormularioConfBESC();";
        $stJs .= limparConfBESCExtra();
    } else {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."');\n";
    }

    return $stJs;
}

function excluirConfBESC()
{
    $arContasConvenio = Sessao::read("arContasConvenio");
    $arTemp = array();
    foreach ($arContasConvenio as $arConta) {
        if ($arConta["inId"] != $_GET["inId"]) {
            $arConta["inId"] = count($arTemp);
            $arTemp[] = $arConta;
        }
    }
    Sessao::write("arContasConvenio",$arTemp);
    $stJs .= montarListaConfBESC();

    return $stJs;
}

function montaAlterarConfBESC()
{
    Sessao::write("inId",$_GET["inId"]);
    $arContasConvenio = Sessao::read("arContasConvenio");
    $arConta = $arContasConvenio[$_GET["inId"]];
    $stVigencia = Sessao::read("dtVigencia");

    $stJs  = "jQuery('#stContaCorrente').val('".$arConta["stContaCorrente"]."');\n";
    $stJs .= "jQuery('#stNumAgenciaTxt').val('".$arConta["stNumAgenciaTxt"]."');\n";
    $stJs .= "jQuery('#stNumAgencia').val('".$arConta["stNumAgenciaTxt"]."');\n";
    $stJs .= "jQuery('#stDescricaoConvenio').val('".$arConta["stDescricaoConvenio"]."');\n";

    $stFiltro  = " WHERE dt_inicial <= to_date('".$stVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);
    list($inDia,$inMes,$inAno) = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $dtCompetencia = $inAno."-".$inMes."-".$inDia;

    include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaOrgao.class.php");
    $obTOrganogramaOrgao = new TOrganogramaOrgao();
    $obTOrganogramaOrgao->setDado("vigencia",$dtCompetencia);

    $stJs .= "passaItem('document.frm.inCodLotacaoSelecionados','document.frm.inCodLotacaoDisponiveis','tudo',''); \n";
    $stJs .= "jQuery('#inCodLotacaoSelecionados').removeOption(/./);\n";
    foreach ($arConta["inCodLotacaoSelecionados"] as $inCodOrgao) {
        $stFiltro = " AND orgao.cod_orgao = ".$inCodOrgao;
        $obTOrganogramaOrgao->recuperaOrgaos($rsOrgao,$stFiltro);
        $stDesc = $rsOrgao->getCampo("cod_estrutural")."-".$rsOrgao->getCampo("descricao");
        $stJs .= "jQuery('#inCodLotacaoSelecionados').addOption('".$inCodOrgao."','".$stDesc."');\n";
        $stJs .= "jQuery('#inCodLotacaoDisponiveis').removeOption('".$inCodOrgao."');\n";
    }

    include_once(CAM_GA_ORGAN_MAPEAMENTO."TOrganogramaLocal.class.php");
    $obTOrganogramaLocal = new TOrganogramaLocal();
    $stJs .= "jQuery('#inCodLocalSelecionados').removeOption(/./);\n";
    $stJs .= "jQuery('#inCodLocalDisponiveis').removeOption(/./);\n";
    $obTOrganogramaLocal->recuperaTodos($rsLocal, '', ' ORDER BY descricao ');
    while (!$rsLocal->eof()) {
        $stJs .= "jQuery('#inCodLocalDisponiveis').addOption('".$rsLocal->getCampo('cod_local')."','".$rsLocal->getCampo('cod_local')." - ".$rsLocal->getCampo('descricao')."', false);\n";
        $rsLocal->proximo();
    }
    if (is_array($arConta["inCodLocalSelecionados"])) {
        foreach ($arConta["inCodLocalSelecionados"] as $inCodLocal) {
            $obTOrganogramaLocal->setDado("cod_local",$inCodLocal);
            $obTOrganogramaLocal->recuperaPorChave($rsLocal);
            $stDesc = $rsLocal->getCampo("cod_local")."-".$rsLocal->getCampo("descricao");
            $stJs .= "jQuery('#inCodLocalSelecionados').addOption('".$inCodLocal."','".$stDesc."');\n";
            $stJs .= "jQuery('#inCodLocalDisponiveis').removeOption('".$inCodLocal."');\n";
        }
    }
    $stJs .= "jQuery('#btIncluirConfBESC').attr('disabled','disabled');\n";
    $stJs .= "jQuery('#btAlterarConfBESC').removeAttr('disabled');\n";
    $stJs .= "LiberaFrames();\n";

    return $stJs;
}

function limparConfBESCExtra()
{
    $stJs  = "jQuery('#stContaCorrente').val('');                       \n";
    $stJs .= "jQuery('#stNumAgenciaTxt').val('');                       \n";
    $stJs .= "jQuery('#stNumAgencia').val('');                          \n";
    $stJs .= "jQuery('#inCodLotacaoSelecionados').removeOption(/./);    \n";
    $stJs .= "jQuery('#inCodLotacaoDisponiveis').removeOption(/./);     \n";
    $stJs .= atualizarLotacao();

    return $stJs;
}

function processarForm()
{
    $stJs = "jQuery('#btLimparConfBESC').click( function () { montaParametrosGET('limparConfBESCExtra'); } );\n";

    include_once( CAM_GT_MON_MAPEAMENTO."TMONBanco.class.php");
    include_once( CAM_GT_MON_MAPEAMENTO."TMONContaCorrente.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoConvenioBesc.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescConta.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescLocal.class.php");
    include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoBescOrgao.class.php");

    $obTMONBanco = new TMONBanco;
    $obTMONContaCorrente = new TMONContaCorrente;
    $obTIMAConfiguracaoConvenioBesc = new TIMAConfiguracaoConvenioBesc;
    $obTIMAConfiguracaoBescConta = new TIMAConfiguracaoBescConta;
    $obTIMAConfiguracaoBescLocal = new TIMAConfiguracaoBescLocal;
    $obTIMAConfiguracaoBescOrgao = new TIMAConfiguracaoBescOrgao;

    $obTIMAConfiguracaoBescConta->setDado("vigencia", Sessao::read("dtVigencia"));
    $obTIMAConfiguracaoBescConta->recuperaRelacionamento($rsContas);

    $arContasConvenio = array();

    while (!$rsContas->eof()) {
        $stFiltro  = " WHERE configuracao_besc_orgao.cod_convenio = ".$rsContas->getCampo("cod_convenio");
        $stFiltro .= "   AND configuracao_besc_orgao.cod_banco = ".$rsContas->getCampo("cod_banco");
        $stFiltro .= "   AND configuracao_besc_orgao.cod_agencia = ".$rsContas->getCampo("cod_agencia");
        $stFiltro .= "   AND configuracao_besc_orgao.cod_conta_corrente = ".$rsContas->getCampo("cod_conta_corrente");
        $stFiltro .= "   AND configuracao_besc_orgao.timestamp = '".$rsContas->getCampo("timestamp")."'";
        $obTIMAConfiguracaoBescOrgao->recuperaTodos($rsOrgao,$stFiltro);
        $arOrgao = array();

        while (!$rsOrgao->eof()) {
            $arOrgao[] = $rsOrgao->getCampo("cod_orgao");
            $rsOrgao->proximo();
        }

        $stFiltro  = " WHERE configuracao_besc_local.cod_convenio = ".$rsContas->getCampo("cod_convenio");
        $stFiltro .= "   AND configuracao_besc_local.cod_banco = ".$rsContas->getCampo("cod_banco");
        $stFiltro .= "   AND configuracao_besc_local.cod_agencia = ".$rsContas->getCampo("cod_agencia");
        $stFiltro .= "   AND configuracao_besc_local.cod_conta_corrente = ".$rsContas->getCampo("cod_conta_corrente");
        $stFiltro .= "   AND configuracao_besc_local.timestamp = '".$rsContas->getCampo("timestamp")."'";
        $obTIMAConfiguracaoBescLocal->recuperaTodos($rslocal,$stFiltro);
        $arLocal = array();

        while (!$rslocal->eof()) {
            $arLocal[] = $rslocal->getCampo("cod_local");
            $rslocal->proximo();
        }

        $arTemp["inId"]                     = count($arContasConvenio);
        $arTemp["stNumAgenciaTxt"]          = $rsContas->getCampo("num_agencia");
        $arTemp["stContaCorrente"]          = $rsContas->getCampo("num_conta_corrente");
        $arTemp["stDescricaoConvenio"]      = $rsContas->getCampo("descricao");
        $arTemp["inCodLotacaoSelecionados"] = $arOrgao;
        $arTemp["inCodLocalSelecionados"]   = $arLocal;
        $arContasConvenio[] = $arTemp;

        $rsContas->proximo();
    }
    Sessao::write("arContasConvenio",$arContasConvenio);
    $stJs .= montarListaConfBESC();

    return $stJs;
}

function atualizarLotacao()
{
    include_once( CAM_GRH_PES_COMPONENTES."ISelectMultiploLotacao.class.php"			);
    include_once( CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorOrgao.class.php"	    );
    include_once( CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php" );

    $obTPessoalContratoServidorOrgao 	  = new TPessoalContratoServidorOrgao();
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

    $obTPessoalContratoServidorOrgao->recuperaDataPrimeiroCadastro($rsContratoServidorOrgao);

    $stJs = "";
    $stAcao = $_GET["stAcao"];
    $arSelectMultiploLotacao = Sessao::read("arSelectMultiploLotacao");
    $obErro = new Erro();

    if (trim($_GET["dtVigencia"])!="") {
        $stVigencia = $_GET["dtVigencia"];
        Sessao::write("dtVigencia",$stVigencia);
    } else {
        $stVigencia = Sessao::read("dtVigencia");
    }

    $stFiltro  = " WHERE dt_inicial <= to_date('".$stVigencia."','dd/mm/yyyy')	\n";
    $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1						\n";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

    if ($rsPeriodoMovimentacao->getNumLinhas() != -1) {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);

        if ($rsOrganograma->getNumLinhas() != -1) {
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        } else {
            $stFiltro  = " WHERE dt_inicial <= to_date('".$rsContratoServidorOrgao->getCampo("dt_cadastro")."','dd/mm/yyyy')	\n";
            $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1																\n";
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);

            $obErro->setDescricao("Não existem servidores vinculados a nenhum orgão em ".$stVigencia.". A vigência informada deve ser a partir de ".$rsPeriodoMovimentacao->getCampo("dt_inicial").".");
            $obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
            $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
            $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
            $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");
        }

        list($inDia,$inMes,$inAno)= explode("/", $rsPeriodoMovimentacao->getCampo("dt_final"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;

        // Atualizando o componente na tela!
        if (is_array($arSelectMultiploLotacao) && !empty($arSelectMultiploLotacao)) {
            foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
            }
        }
    } else {
        $stFiltro  = " WHERE dt_inicial <= to_date('".$rsContratoServidorOrgao->getCampo("dt_cadastro")."','dd/mm/yyyy')	\n";
        $stOrdem  = " ORDER BY dt_inicial::date DESC LIMIT 1																\n";
        $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro,$stOrdem);
        $obErro->setDescricao("Vigência anterior ao primeiro período de movimentação. A vigência informada deve ser a partir de ".$rsPeriodoMovimentacao->getCampo("dt_inicial").".");
    }

    if ($obErro->ocorreu() && $rsPeriodoMovimentacao->getNumLinhas()>0) {
        $obTPessoalContratoServidorOrgao->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
        $obTPessoalContratoServidorOrgao->recuperaOrganogramaVigente($rsOrganograma);
        $inCodOrganograma = $rsOrganograma->getCampo("cod_organograma");

        list($inDia,$inMes,$inAno)= explode("/", $rsContratoServidorOrgao->getCampo("dt_cadastro"));
        $stDataFinal = $inAno."-".$inMes."-".$inDia;

        // Atualizando o componente na tela!
        Sessao::write('dtVigencia',$rsPeriodoMovimentacao->getCampo("dt_inicial"));
        $stJs .= " jQuery('#dtVigencia').val('".$rsPeriodoMovimentacao->getCampo("dt_inicial")."'); \n";
        if (is_array($arSelectMultiploLotacao) && !empty($arSelectMultiploLotacao)) {
            foreach ($arSelectMultiploLotacao as $obSelectMultiploLotacao) {
                $stJs .= $obSelectMultiploLotacao->atualizarLotacao($stDataFinal, $inCodOrganograma);
            }
        }

        $stJs .= " alertaAviso('".$obErro->getDescricao()."','form','erro','".Sessao::getId()."'); 	\n";
    }
    else if($rsPeriodoMovimentacao->getNumLinhas()<0){
        $stJs .= " alertaAviso('Período de movimentação não existente.','form','erro','".Sessao::getId()."'); 	\n";
    }

    return $stJs;
}

switch ($_GET['stCtrl']) {
    case "incluirConfBESC":
        $stJs =  incluirConfBESC();
        break;
    case "alterarConfBESC":
        $stJs = alterarConfBESC();
        break;
    case "excluirConfBESC":
        $stJs = excluirConfBESC();
        break;
    case "montaAlterarConfBESC":
        $stJs = montaAlterarConfBESC();
        break;
    case "limparConfBESCExtra":
        $stJs = limparConfBESCExtra();
        break;
    case "processarForm":
        $stJs = processarForm();
        break;
     case "atualizarLotacao":
        $stJs = atualizarLotacao();
        break;
}
if ($stJs) {
    echo $stJs;
}

?>

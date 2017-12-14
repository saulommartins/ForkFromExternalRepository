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
    * Página de Oculto do Comprovante Rendimento IRRF
    * Data de Criação: 22/11/2007

    * @author Analista: Dagiane	Vieira
    * @author Desenvolvedor: <Alex Cardoso>

    * @ignore

    $Id: OCComprovanteRendimentosIRRF.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-04.05.37
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php"                                       );

//Define o nome dos arquivos PHP
$stPrograma = "ComprovanteRendimentosIRRF";
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

    return $stJs;
}

###########################ATIVOS / APOSENTADOS#####################################

function gerarSpanAtivosAposentados()
{
    $stSituacao = $_GET["stSituacao"];

    $stJs .= limparSpans();

    include_once(CAM_GRH_PES_COMPONENTES."IFiltroComponentes.class.php");
    $obIFiltroComponentes = new IFiltroComponentes();
    $obIFiltroComponentes->setMatricula();
    $obIFiltroComponentes->setCGMMatricula();
    $obIFiltroComponentes->setLocal();
    $obIFiltroComponentes->setLotacao();
    $obIFiltroComponentes->setRegSubFunEsp();
    $obIFiltroComponentes->setAtributoServidor();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoRegSubFunEsp();
    $obIFiltroComponentes->setGrupoAtributoServidor();
    $obIFiltroComponentes->setDisabledQuebra();

    $obFormulario = new Formulario();

    switch ($stSituacao) {
        case 'ativo':
                $obFormulario->addTitulo("Ativos");
                $obIFiltroComponentes->setAtivos();
            break;
        case 'aposentado':
                $obFormulario->addTitulo("Aposentados");
                $obIFiltroComponentes->setAposentados();
            break;
        case 'rescindido':
                $obFormulario->addTitulo("Rescindidos");
                $obIFiltroComponentes->setRescisao();
            break;
        case 'todos':
                $obIFiltroComponentes->setAtributoPensionista();
                $obIFiltroComponentes->setGrupoAtributoPensionista();
                $obIFiltroComponentes->setTodos();
                $obFormulario->addTitulo("Todos");
            break;
    }

    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

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
    $obIFiltroComponentes->setRegSubFunEsp();
    $obIFiltroComponentes->setAtributoPensionista();
    $obIFiltroComponentes->setGrupoLocal();
    $obIFiltroComponentes->setGrupoLotacao();
    $obIFiltroComponentes->setGrupoRegSubFunEsp();
    $obIFiltroComponentes->setGrupoAtributoPensionista();
    $obIFiltroComponentes->setDisabledQuebra();

    $obFormulario = new Formulario();
    $obFormulario->addTitulo("Pensionistas");
    $obIFiltroComponentes->geraFormulario($obFormulario);
    $obFormulario->montaInnerHTML();
    $stHtml = $obFormulario->getHTML();
    $stJs .= "d.getElementById('spnCadastro').innerHTML = '$stHtml';\n";

    return $stJs;
}

###########################UTILS##########################

function submeter()
{
    $obErro = new Erro();

    if ($_GET["stSituacao"] == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Cadastro inválido!()");
    }

    if(($_GET["stSituacao"] == 'a' or
        $_GET["stSituacao"] == 'b' or
        $_GET["stSituacao"] == 'c' or
        $_GET["stSituacao"] == 'e') && !$obErro->ocorreu() ){
        switch ($_GET["stTipoFiltro"]) {
            case "":
                $obErro->setDescricao($obErro->getDescricao()."@Campo Opções do Ativos/Aposentados inválido!()");
                break;
            case "contrato":
            case "contrato_rescisao":
            case "contrato_aposentado":
            case "contrato_todos":
            case "cgm_contrato":
            case "cgm_contrato_rescisao":
            case "cgm_contrato_aposentado":
            case "cgm_contrato_todos":
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
            case "atributo_servidor_grupo":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Ativos/Aposentados inválido!()");
                }
                break;
            case "atributo_pensionista_grupo":
                if ($_GET["inCodAtributo"] == "") {
                    $obErro->setDescricao($obErro->getDescricao()."@Campo Atributo Dinâmico do Pensionista inválido!()");
                }
                break;
        }
    }

    if ( $_GET["inAnoCompetencia"] == "" && !$obErro->ocorreu()) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Ano Calendário inválido!()");
    }

    if ( $_GET["inNumCGMResponsavel"] == "" && !$obErro->ocorreu()) {
        $obErro->setDescricao($obErro->getDescricao()."@Campo Responsável inválido!()");
    }

    /////////LIMPA AREA TRANSF.
    Sessao::write("comprovante_irrf",array());

    // DATA FINAL ULTIMA COMPETENCIA ANO SELECIONADO - UTILIZADO NA BUSCA DE DEPENDENTES
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stFiltroPeriodoMovimentacao = " AND to_char(FPM.dt_final,'yyyy') = '".$_REQUEST["inAnoCompetencia"]."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsUltimaMovimentacaoAno, $stFiltroPeriodoMovimentacao, " LIMIT 1");
    $stDataFinalUltimaMovimentacaoAno = $rsUltimaMovimentacaoAno->getCampo('dt_final');

    if ($stDataFinalUltimaMovimentacaoAno == "") {
        $obErro->setDescricao($obErro->getDescricao()."@Não existe Periodo de movimentação para o ano selecionado!");
    }

    if (!$obErro->ocorreu()) {
        /////////TABELA IRRF
        include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrf.class.php");
        $stFiltroTabelaIrrf = " AND tabela_irrf.vigencia <= to_date('".$stDataFinalUltimaMovimentacaoAno."','dd/mm/yyyy')";
        $obTFolhaPagamentoTabelaIrrf = new TFolhaPagamentoTabelaIrrf;
        $obTFolhaPagamentoTabelaIrrf->recuperaRelacionamento($rsTabelaIRRF, $stFiltroTabelaIrrf, ' ORDER BY tabela_irrf.timestamp DESC LIMIT 1');

        if ($rsTabelaIRRF->getNumLinhas() <= 0 && !$obErro->ocorreu()) {
            $obErro->setDescricao($obErro->getDescricao()."@Não existe Tabela de IRRF configurada para o Ano Calendário selecionado!");
        }
    }//

    if ( $obErro->ocorreu() ) {
        $stJs .= "alertaAviso('".$obErro->getDescricao()."','form','aviso','".Sessao::getId()."');";
    } else {
        $arComprovanteIRRF = Sessao::read("comprovante_irrf");
        $arComprovanteIRRF["tabela_irrf_cod_tabela"] = $rsTabelaIRRF->getCampo("cod_tabela");
        $arComprovanteIRRF["tabela_irrf_timestamp"]  = $rsTabelaIRRF->getCampo("timestamp");
        $arComprovanteIRRF["data_final_ultima_movimentacao_ano"] = $stDataFinalUltimaMovimentacaoAno;
        Sessao::write("comprovante_irrf",$arComprovanteIRRF);
        $stJs .= "BloqueiaFrames(true,false);\n";
        $stJs .= "parent.frames[2].Salvar();\n";
    }

    return $stJs;
}

function limparForm()
{
    $stJs .= "montaParametrosGET('gerarSpanAtivosAposentados','stSituacao');";

    return $stJs;
}

###############################################

function buscaCGM()
{
    $obRPessoalServidor = new RPessoalServidor;
    $rsContrato = new Recordset;
    $inNumCGM = $_REQUEST["inNumCGMResponsavel"];
    if ($inNumCGM  != "") {
        $obRPessoalServidor->obRCGMPessoaFisica->setNumCGM( $inNumCGM  );
        $obRPessoalServidor->obRCGMPessoaFisica->consultarCGM( $rsCGM );
        if ( $rsCGM->getCampo('nom_cgm') ) {
            $stJs .= "d.getElementById('inCampoInnerResponsavel').innerHTML             = '".  addslashes($rsCGM->getCampo('nom_cgm')) ."';\n";
            $stJs .= "f.inCampoInnerResponsavel.value = '" . addslashes($rsCGM->getCampo('nom_cgm')) ."';\n";
        } else {
            $stJs .= "d.getElementById('inCampoInnerResponsavel').innerHTML  = '&nbsp'  ;\n";
            $stJs .= "f.inCampoInnerResponsavel.value = '';\n";
            $stJs .= "f.inNumCGMResponsavel.value = '';\n";
            $stJs .= "alertaAviso( 'Número do CGM (".$inNumCGM.") não encontrado no cadastro de Pessoa fisica','form','erro','".Sessao::getId()."');";
        }
    } else {
        $stJs .= "d.getElementById('inCampoInnerResponsavel').innerHTML  = '&nbsp'  ;\n";
        $stJs .= "f.inCampoInnerResponsavel.value = '';\n";
    }

    return $stJs;
}

#################################################

switch ($_REQUEST['stCtrl']) {
    case "gerarSpanAtivosAposentados":
        $stJs .= gerarSpanAtivosAposentados();
        break;
    case "gerarSpanPensionistas":
        $stJs .= gerarSpanPensionistas();
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
    case "buscaCGM":
        $stJs .= buscaCGM();
        break;
}

if ($stJs) {
    echo $stJs;
}

?>

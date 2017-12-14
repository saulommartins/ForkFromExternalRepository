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
    * Arquivo de Processamento da DIRF.
    * Data de Criação: 22/11/2007

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.15

    $Id: PRExportarDIRF.php 66258 2016-08-03 14:25:21Z evandro $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php";
include_once CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirf.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php";
include_once CLA_EXPORTADOR;

$stAcao = $request->get('stAcao');
$arSessaoLink = Sessao::read('link');
if (!empty($arSessaoLink)) {
    $stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];
}

foreach ($request->getAll() as $key=>$value) {
    //Retira o JS que é inserido na URL
    if ($key != 'hdnValidaMatriculas') {
        $stLink .= $key."=".$value."&";
    }
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarDIRF";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
$arDtFinal = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));

Sessao::setTrataExcecao(true);
$obExportador = new Exportador();
$obExportador->setRetorno($pgForm);
$obExportador->addArquivo("DIRF.TXT");
$obExportador->roUltimoArquivo->setTipoDocumento("DIRF");

#Registro do tipo um
#Informações do declarante
$obTEntidade = new TEntidade();
$stFiltro  = " AND cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$obTEntidade->recuperaEntidades($rsEntidade,$stFiltro);

$obTCGMPessoaJuridica = new TCGMPessoaJuridica();
$stFiltro = " AND CGM.numcgm = ".$rsEntidade->getCampo("numcgm");
$obTCGMPessoaJuridica->recuperaRelacionamento($rsCGM,$stFiltro);

$obTIMAConfiguracaoDIRF = new TIMAConfiguracaoDirf();
$obTIMAConfiguracaoDIRF->setDado("exercicio",$request->get('inAnoCompetencia'));
$obTIMAConfiguracaoDIRF->recuperaPorChave($rsConfiguracao);

if ($rsConfiguracao->getNumLinhas() == -1) {
    SistemaLegado::LiberaFrames();
    Sessao::getExcecao()->setDescricao("A configuração da DIRF no exercício ".$request->get('inAnoCompetencia')." não foi realizada, essa configuração é necessária para a geração do arquivo.");
}

$obTCGMPessoaFisica = new TCGMPessoaFisica();
$stFiltro = " AND CGM.numcgm = ".$rsConfiguracao->getCampo("responsavel_entrega");
$obTCGMPessoaFisica->recuperaRelacionamento($rsCGMResponsavel,$stFiltro);

$stFiltro = " AND CGM.numcgm = ".$rsConfiguracao->getCampo("responsavel_prefeitura");
$obTCGMPessoaFisica->recuperaRelacionamento($rsCGMResponsavelPrefeitura,$stFiltro);

$inSequencia = 1;

$arRegistroTipoUm[0]["sequencia"]                       = $inSequencia;
$arRegistroTipoUm[0]["tipo"]                            = "1";
$arRegistroTipoUm[0]["cnpj_declarante"]                 = $rsCGM->getCampo("cnpj");
$arRegistroTipoUm[0]["nome_arquivo"]                    = "Dirf";
$arRegistroTipoUm[0]["ano_calendario"]                  = $request->get('inAnoCompetencia');
$arRegistroTipoUm[0]["or"]                              = $request->get('stIndicador');
$arRegistroTipoUm[0]["situacao_declaracao"]             = "1";
$arRegistroTipoUm[0]["tipo_declarante"]                 = "2";
$arRegistroTipoUm[0]["natureza_declarante"]             = $rsConfiguracao->getCampo("cod_natureza");
$arRegistroTipoUm[0]["tipo_rendimento"]                 = "0";
$arRegistroTipoUm[0]["ano_referencia"]                  = "2010";
$arRegistroTipoUm[0]["indicador_declarante"]            = "0";
$arRegistroTipoUm[0]["filer1"]                          = ($request->get('inAnoCompetencia') >= 2007 and $request->get('inAnoCompetencia') <= 2010)?'0':'';
$arRegistroTipoUm[0]["nome_empresarial"]                = $rsCGM->getCampo("nom_cgm");
$arRegistroTipoUm[0]["cpf_responsavel_prefeitura"]      = $rsCGMResponsavelPrefeitura->getCampo("cpf");
$arRegistroTipoUm[0]["data_evento"]                     = "";
$arRegistroTipoUm[0]["tipo_evento"]                     = "";
$arRegistroTipoUm[0]["filer2"]                          = "";
$arRegistroTipoUm[0]["numero_recibo"]                   = $request->get('inNumeroRecibo');
$arRegistroTipoUm[0]["filer3"]                          = "";
$arRegistroTipoUm[0]["cpf_responsavel"]                 = $rsCGMResponsavel->getCampo("cpf");
$arRegistroTipoUm[0]["nome_responsavel"]                = $rsCGMResponsavel->getCampo("nom_cgm");
$arRegistroTipoUm[0]["ddd_responsavel"]                 = substr($rsConfiguracao->getCampo("telefone"),0,2);
$arRegistroTipoUm[0]["fone_responsavel"]                = substr($rsConfiguracao->getCampo("telefone"),2,strlen($rsConfiguracao->getCampo("telefone")));
$arRegistroTipoUm[0]["ramal_responsavel"]               = $rsConfiguracao->getCampo("ramal");
$arRegistroTipoUm[0]["fax_responsavel"]                 = $rsConfiguracao->getCampo("fax");
$arRegistroTipoUm[0]["email_responsavel"]               = $rsConfiguracao->getCampo("email");
$arRegistroTipoUm[0]["uso_srf"]                         = "";
$arRegistroTipoUm[0]["uso_declarante"]                  = "";
$arRegistroTipoUm[0]["uso_declarante2"]                 = "9";
$rsRegistroTipoUm = new RecordSet();
$rsRegistroTipoUm->preenche($arRegistroTipoUm);

$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoUm);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_arquivo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_calendario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("or");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("situacao_declaracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_rendimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_referencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filer1");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empresarial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_responsavel_prefeitura");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_evento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_evento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filer2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(42);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_recibo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filer3");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(229);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ddd_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fone_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ramal_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fax_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(50);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_srf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(165);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declarante2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

#Registro do tipo dois
#Informações do beneficiário
$obTIMAConfiguracaoDirf = new TIMAConfiguracaoDirf();
$inCodAtributo = 0;
switch ($request->get('stTipoFiltro')) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stCodContratos = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodContratos .= $arContrato["cod_contrato"].",";
        }
        $stCodContratos = substr($stCodContratos,0,strlen($stCodContratos)-1);
        $stCodigos = $stCodContratos;
        break;
    case "lotacao":
        $stCodOrgao = implode(",",$request->get('inCodLotacaoSelecionados'));
        $stCodigos = $stCodOrgao;
        break;
    case "local":
        $stCodLocal = implode(",",$request->get('inCodLocalSelecionados'));
        $stCodigos = $stCodLocal;
        break;
    case "atributo_servidor":
    case "atributo_pensionista":
        $inCodAtributo = $request->get('inCodAtributo');
        $inCodCadastro = $request->get('inCodCadastro');
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if ( is_array($request->get($stNomeAtributo."_Selecionados")) ) {
            $inArray = 1;
            $stValores = implode(",",$request->get($stNomeAtributo."_Selecionados"));
        } else {
            $inArray = 0;
            $stValores = pg_escape_string($request->get($stNomeAtributo));
        }
        $stCodigos = $inArray."#".$inCodAtributo."#".$stValores;
        break;
    case "reg_sub_fun_esp":
        $stCodigos  = implode(",",$request->get('inCodRegimeSelecionadosFunc'))."#";
        $stCodigos .= implode(",",$request->get('inCodSubDivisaoSelecionadosFunc'))."#";
        $stCodigos .= implode(",",$request->get('inCodFuncaoSelecionados'))."#";
        if (is_array($request->get('inCodEspecialidadeSelecionadosFunc'))) {
            $stCodigos .= implode(",",$request->get('inCodEspecialidadeSelecionadosFunc'));
        }
        break;
}

    $stFiltroConfig = " AND exercicio = '". $request->get('inAnoCompetencia')."'";
    $obTIMAConfiguracaoDirf->recuperaRelacionamento($rsConfigDirf, $stFiltroConfig);
    $obTIMAConfiguracaoDirf->setDado("stTipoFiltro",$request->get('stTipoFiltro'));
    $obTIMAConfiguracaoDirf->setDado("stCodigos",$stCodigos);
    $obTIMAConfiguracaoDirf->setDado("inExercicio",$request->get('inAnoCompetencia'));

if ( $rsConfigDirf->getCampo('pagamento_mes_competencia') == 't') {    
    $obTIMAConfiguracaoDirf->recuperaExportarDirfPagamento($rsTemp1);
} else {
    $obTIMAConfiguracaoDirf->recuperaExportarDirf($rsTemp1);
}

if ($request->get('boPrestadoresServico')) {
    $obTIMAConfiguracaoDirf->setDado("inExercicio",$request->get('inAnoCompetencia'));
    if ( $rsConfigDirf->getCampo('pagamento_mes_competencia') == 't') {
        if($request->get('boPrestadoresServicoTodos'))
            $obTIMAConfiguracaoDirf->recuperaExportarDirfPrestadorServicoPagamentoComESemRetencao($rsTemp2);
        else
            $obTIMAConfiguracaoDirf->recuperaExportarDirfPrestadorServicoPagamento($rsTemp2);
    } else {
        $obTIMAConfiguracaoDirf->setDado('inExercicioAnterior', ($request->get('inAnoCompetencia')-1));
        $obTIMAConfiguracaoDirf->recuperaExportarDirfPrestadorServico($rsTemp2);
    }
} else {
    $rsTemp2 = new RecordSet;
}

$rsRegistroTipoDois1 = new RecordSet;

$inSequencia = $rsRegistroTipoDois1->getNumLinhas()+2;
$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoDois1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_retencao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ident_especie_beneficiario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("beneficiario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_beneficiario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("ALFANUMERICO_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jan");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fev");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mar");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("abr");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mai");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jun");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jul");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ago");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("set");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("out");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nov");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dez");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dec");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ident_situacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ident_especializacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_rfb");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(32);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declatante2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

#Registro do tipo tres
#Totalizações
$arRegistroTipoTres[0]["sequencia"]                = $inSequencia;
$arRegistroTipoTres[0]["tipo"]                     = "3";
$arRegistroTipoTres[0]["cnpj_declarante"]          = $rsCGM->getCampo("cnpj");
$arRegistroTipoTres[0]["codigo_retencao"]          = "0561";
$arRegistroTipoTres[0]["total_registros"]          = $inTotalRegistro;
$arRegistroTipoTres[0]["filer"]                    = "";
$arRegistroTipoTres[0]["jan"]                      = formataValor($arTotalBaseIRRFValor[0]).formataValor($arTotalBaseDeducoesValor[0]).formataValor($arTotalDescontoIRRFValor[0]);
$arRegistroTipoTres[0]["fev"]                      = formataValor($arTotalBaseIRRFValor[1]).formataValor($arTotalBaseDeducoesValor[1]).formataValor($arTotalDescontoIRRFValor[1]);
$arRegistroTipoTres[0]["mar"]                      = formataValor($arTotalBaseIRRFValor[2]).formataValor($arTotalBaseDeducoesValor[2]).formataValor($arTotalDescontoIRRFValor[2]);
$arRegistroTipoTres[0]["abr"]                      = formataValor($arTotalBaseIRRFValor[3]).formataValor($arTotalBaseDeducoesValor[3]).formataValor($arTotalDescontoIRRFValor[3]);
$arRegistroTipoTres[0]["mai"]                      = formataValor($arTotalBaseIRRFValor[4]).formataValor($arTotalBaseDeducoesValor[4]).formataValor($arTotalDescontoIRRFValor[4]);
$arRegistroTipoTres[0]["jun"]                      = formataValor($arTotalBaseIRRFValor[5]).formataValor($arTotalBaseDeducoesValor[5]).formataValor($arTotalDescontoIRRFValor[5]);
$arRegistroTipoTres[0]["jul"]                      = formataValor($arTotalBaseIRRFValor[6]).formataValor($arTotalBaseDeducoesValor[6]).formataValor($arTotalDescontoIRRFValor[6]);
$arRegistroTipoTres[0]["ago"]                      = formataValor($arTotalBaseIRRFValor[7]).formataValor($arTotalBaseDeducoesValor[7]).formataValor($arTotalDescontoIRRFValor[7]);
$arRegistroTipoTres[0]["set"]                      = formataValor($arTotalBaseIRRFValor[8]).formataValor($arTotalBaseDeducoesValor[8]).formataValor($arTotalDescontoIRRFValor[8]);
$arRegistroTipoTres[0]["out"]                      = formataValor($arTotalBaseIRRFValor[9]).formataValor($arTotalBaseDeducoesValor[9]).formataValor($arTotalDescontoIRRFValor[9]);
$arRegistroTipoTres[0]["nov"]                      = formataValor($arTotalBaseIRRFValor[10]).formataValor($arTotalBaseDeducoesValor[10]).formataValor($arTotalDescontoIRRFValor[10]);
$arRegistroTipoTres[0]["dez"]                      = formataValor($arTotalBaseIRRFValor[11]).formataValor($arTotalBaseDeducoesValor[11]).formataValor($arTotalDescontoIRRFValor[11]);
$arRegistroTipoTres[0]["dec"]                      = formataValor($arTotalBaseIRRFValor[12]).formataValor($arTotalBaseDeducoesValor[12]).formataValor($arTotalDescontoIRRFValor[12]);
$arRegistroTipoTres[0]["uso_srf"]                  = "";
$arRegistroTipoTres[0]["uso_declarante"]           = "";
$arRegistroTipoTres[0]["uso_declatante2"]          = "9";

$rsRegistroTipoTres = new RecordSet();
$rsRegistroTipoTres->preenche($arRegistroTipoTres);

$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoTres);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_retencao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_registros");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filer");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(67);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jan");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("fev");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mar");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("abr");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mai");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jun");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("jul");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ago");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("set");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("out");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nov");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dez");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dec");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_srf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declarante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_declatante2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);

//$obExportador->Show();

dirf2010($rsRegistroTipoUm, $rsTemp1, $rsTemp2, ($request->get('stIndicador') == 'O' ? 'N' : 'S'), $request->get('boPrestadoresServico'));

$arArquivosDownload[0]['stNomeArquivo'] = 'DIRF.TXT';
$arArquivosDownload[0]['stLink'       ] = '/tmp/DIRF.TXT';
Sessao::write('arArquivosDownload',$arArquivosDownload);
SistemaLegado::alertaAviso($pgForm,'Arquivo(s) gerado com sucesso','incluir','aviso', Sessao::getId(), "../");

Sessao::encerraExcecao();
SistemaLegado::LiberaFrames();

function formataValor($nuEntrada)
{
    $nuSaida = str_pad(str_replace(".","",number_format($nuEntrada,2,".","")),15,"0",STR_PAD_LEFT);

    return $nuSaida;
}

/* usa os dados da dirf do layout anterior para dirf 2010 */
function dirf2010($rsTipo1, $rsTipo2, $rsTipo3, $boRetificadora, $boPrestadoresServico = false)
{
    global $rsConfigDirf;
    global $request;

    /* Plano de Saúde */
    include_once CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoDirfPlano.class.php";
    $obTIMAConfiguracaoDirfPlano = new TIMAConfiguracaoDirfPlano();
    $stFiltro = " WHERE exercicio = '".$request->get('inAnoCompetencia')."' ";
    $obTIMAConfiguracaoDirfPlano->recuperaTodos($rsChecaPlano, $stFiltro);
    if ( $rsChecaPlano->getNumLinhas() > 0 ) {
        $hasPlano = 'S';
    } else {
        $hasPlano = 'N';
    }

    /* TO DO: adicionar na configuração um campo o ano-referência */
    $file = fopen('../../../../../../gestaoAdministrativa/fontes/PHP/framework/tmp/DIRF.TXT', 'w');
    $linha = 'Dirf|2015|'.$rsTipo1->getCampo('ano_calendario').'|'.$boRetificadora.'|'.$rsTipo1->getCampo('numero_recibo').'|M1LB5V2|'."\n";
    fputs($file, $linha);

    $stNomeResponsavel = $rsTipo1->getCampo('nome_responsavel');
    SistemaLegado::removeAcentosSimbolos($stNomeResponsavel);
    $linha = 'RESPO|'.str_pad($rsTipo1->getCampo('cpf_responsavel'),11,0,STR_PAD_LEFT).'|'.$stNomeResponsavel.'|'.
            str_pad($rsTipo1->getCampo('ddd_responsavel'),2,0,STR_PAD_LEFT).'|'.str_pad($rsTipo1->getCampo('fone_responsavel'),8,0,STR_PAD_LEFT).'|'.
            $rsTipo1->getCampo('ramal_responsavel').'|'.str_pad($rsTipo1->getCampo('fax_responsavel'),8,0,STR_PAD_LEFT).'|'.
            $rsTipo1->getCampo('email_responsavel')."|\n";
    fputs($file, $linha);

    $linha = 'DECPJ|'.str_pad($rsTipo1->getCampo('cnpj_declarante'),14,0,STR_PAD_LEFT).'|'.$rsTipo1->getCampo('nome_empresarial').'|2|'.
            str_pad($rsTipo1->getCampo('cpf_responsavel_prefeitura'),11,0,STR_PAD_LEFT)."|N|N|N|N|".$hasPlano."|N|N||\n";
    fputs($file, $linha);
    
    if ( $rsTipo2->getNumLinhas() > 0 ){
        $rsTipo2->ordena('beneficiario');
        $linha = "IDREC|0561|\n";
        fputs($file, $linha);
        $beneficiario = '';
    }

    $arCPFs = array();

    while (!$rsTipo2->eof()) {

        array_push($arCPFs, $rsTipo2->getCampo('beneficiario'));

        if ( $beneficiario != $rsTipo2->getCampo('beneficiario') ) {
            $linha = 'BPFDEC|'.str_pad($rsTipo2->getCampo('beneficiario'),11,0,STR_PAD_LEFT).'|'.rtrim(str_replace('.','',$rsTipo2->getCampo('nome_beneficiario')))."||\n";
            fputs($file, $linha);
        }

        /* to do - busca de moléstia grave */

        if ( $rsTipo2->getCampo('ident_especializacao') == 0) {
            $linhaRTRT = 'RTRT|'.substr($rsTipo2->getCampo('jan'),0,13).'|'.substr($rsTipo2->getCampo('fev'),0,13).'|'.
                    substr($rsTipo2->getCampo('mar'),0,13).'|'.substr($rsTipo2->getCampo('abr'),0,13).'|'.
                    substr($rsTipo2->getCampo('mai'),0,13).'|'.substr($rsTipo2->getCampo('jun'),0,13).'|'.
                    substr($rsTipo2->getCampo('jul'),0,13).'|'.substr($rsTipo2->getCampo('ago'),0,13).'|'.
                    substr($rsTipo2->getCampo('set'),0,13).'|'.substr($rsTipo2->getCampo('out'),0,13).'|'.
                    substr($rsTipo2->getCampo('nov'),0,13).'|'.substr($rsTipo2->getCampo('dez'),0,13).'|'.
                    substr($rsTipo2->getCampo('dec'),0,13)."|\n";
            if( doEscrever($linhaRTRT) ) fputs($file, $linhaRTRT);

            $linhaRTIRF = 'RTIRF|'.substr($rsTipo2->getCampo('jan'),26,13).'|'.substr($rsTipo2->getCampo('fev'),26,13).'|'.
                    substr($rsTipo2->getCampo('mar'),26,13).'|'.substr($rsTipo2->getCampo('abr'),26,13).'|'.
                    substr($rsTipo2->getCampo('mai'),26,13).'|'.substr($rsTipo2->getCampo('jun'),26,13).'|'.
                    substr($rsTipo2->getCampo('jul'),26,13).'|'.substr($rsTipo2->getCampo('ago'),26,13).'|'.
                    substr($rsTipo2->getCampo('set'),26,13).'|'.substr($rsTipo2->getCampo('out'),26,13).'|'.
                    substr($rsTipo2->getCampo('nov'),26,13).'|'.substr($rsTipo2->getCampo('dez'),26,13).'|'.
                    substr($rsTipo2->getCampo('dec'),26,13)."|\n";
            if( doEscrever($linhaRTIRF) ) fputs($file, $linhaRTIRF);

        } elseif ( $rsTipo2->getCampo('ident_especializacao') == 1) {
            $linhaRTPO = 'RTPO|'.substr($rsTipo2->getCampo('jan'),0,13).'|'.substr($rsTipo2->getCampo('fev'),0,13).'|'.
                    substr($rsTipo2->getCampo('mar'),0,13).'|'.substr($rsTipo2->getCampo('abr'),0,13).'|'.
                    substr($rsTipo2->getCampo('mai'),0,13).'|'.substr($rsTipo2->getCampo('jun'),0,13).'|'.
                    substr($rsTipo2->getCampo('jul'),0,13).'|'.substr($rsTipo2->getCampo('ago'),0,13).'|'.
                    substr($rsTipo2->getCampo('set'),0,13).'|'.substr($rsTipo2->getCampo('out'),0,13).'|'.
                    substr($rsTipo2->getCampo('nov'),0,13).'|'.substr($rsTipo2->getCampo('dez'),0,13).'|'.
                    substr($rsTipo2->getCampo('dec'),0,13)."|\n";
            if( doEscrever($linhaRTPO) ) fputs($file, $linhaRTPO);

            $linhaRTDP = 'RTDP|'.substr($rsTipo2->getCampo('jan'),13,13).'|'.substr($rsTipo2->getCampo('fev'),13,13).'|'.
                    substr($rsTipo2->getCampo('mar'),13,13).'|'.substr($rsTipo2->getCampo('abr'),13,13).'|'.
                    substr($rsTipo2->getCampo('mai'),13,13).'|'.substr($rsTipo2->getCampo('jun'),13,13).'|'.
                    substr($rsTipo2->getCampo('jul'),13,13).'|'.substr($rsTipo2->getCampo('ago'),13,13).'|'.
                    substr($rsTipo2->getCampo('set'),13,13).'|'.substr($rsTipo2->getCampo('out'),13,13).'|'.
                    substr($rsTipo2->getCampo('nov'),13,13).'|'.substr($rsTipo2->getCampo('dez'),13,13).'|'.
                    substr($rsTipo2->getCampo('dec'),13,13)."|\n";
            if( doEscrever($linhaRTDP) ) fputs($file, $linhaRTDP);

            $linhaRTPA = 'RTPA|'.substr($rsTipo2->getCampo('jan'),26,13).'|'.substr($rsTipo2->getCampo('fev'),26,13).'|'.
                    substr($rsTipo2->getCampo('mar'),26,13).'|'.substr($rsTipo2->getCampo('abr'),26,13).'|'.
                    substr($rsTipo2->getCampo('mai'),26,13).'|'.substr($rsTipo2->getCampo('jun'),26,13).'|'.
                    substr($rsTipo2->getCampo('jul'),26,13).'|'.substr($rsTipo2->getCampo('ago'),26,13).'|'.
                    substr($rsTipo2->getCampo('set'),26,13).'|'.substr($rsTipo2->getCampo('out'),26,13).'|'.
                    substr($rsTipo2->getCampo('nov'),26,13).'|'.substr($rsTipo2->getCampo('dez'),26,13).'|'.
                    substr($rsTipo2->getCampo('dec'),26,13)."|\n";
            if( doEscrever($linhaRTPA) ) fputs($file, $linhaRTPA);
        }

        $beneficiario = $rsTipo2->getCampo('beneficiario');
        $rsTipo2->proximo();
    }

    /* adiciona, caso seja selecionado, os prestadores de serviços */
    if ($boPrestadoresServico) {
        $retencao = null;
        $beneficiario = '0';
        $rsTipo3->ordena('beneficiario');

        while ( !$rsTipo3->eof() ) {

            if ( $retencao != $rsTipo3->getCampo('codigo_retencao') ) {
                $linha = 'IDREC|'.str_pad($rsTipo3->getCampo('codigo_retencao'),4,0,STR_PAD_LEFT)."|\n";
                fputs($file, $linha);
            }

            if ( $beneficiario != $rsTipo3->getCampo('beneficiario') ) {
                if ( $rsTipo3->getCampo('ident_especie_beneficiario') == '1' ) {
                    $beneficiario = str_pad(ltrim($rsTipo3->getCampo('beneficiario'),0),11,0,STR_PAD_LEFT);
                    $tag = 'BPFDEC';
                } elseif ( $rsTipo3->getCampo('ident_especie_beneficiario') == '2') {
                    $beneficiario = str_pad($rsTipo3->getCampo('beneficiario'),14,0,STR_PAD_LEFT);
                    $tag = 'BPJDEC';
                }
                if ( $rsTipo3->getCampo('codigo_retencao') == '1708' ) {
                    
                    $linha = $tag.'|'.$beneficiario.'|'.rtrim(str_replace('.','',$rsTipo3->getCampo('nome_beneficiario')))."|\n";
                } else {
                    $linha = $tag.'|'.$beneficiario.'|'.rtrim(str_replace('.','',$rsTipo3->getCampo('nome_beneficiario')))."||\n";
                }
                
                fputs($file, $linha);
            }

            if ( $rsTipo3->getCampo('ident_especializacao') == 0 ) {
                $linhaRTRT = 'RTRT|'.substr($rsTipo3->getCampo('jan'),0,13).'|'.substr($rsTipo3->getCampo('fev'),0,13).'|'.
                    substr($rsTipo3->getCampo('mar'),0,13).'|'.substr($rsTipo3->getCampo('abr'),0,13).'|'.
                    substr($rsTipo3->getCampo('mai'),0,13).'|'.substr($rsTipo3->getCampo('jun'),0,13).'|'.
                    substr($rsTipo3->getCampo('jul'),0,13).'|'.substr($rsTipo3->getCampo('ago'),0,13).'|'.
                    substr($rsTipo3->getCampo('set'),0,13).'|'.substr($rsTipo3->getCampo('out'),0,13).'|'.
                    substr($rsTipo3->getCampo('nov'),0,13).'|'.substr($rsTipo3->getCampo('dez'),0,13).'|';
                if ( $rsTipo3->getCampo('codigo_retencao') == '1708' ) {
                    $linhaRTRT .= "|\n";
                } else {
                    $linhaRTRT .= substr($rsTipo3->getCampo('dec'),0,13)."|\n";
                }
                if( doEscrever($linhaRTRT) ) fputs($file, $linhaRTRT);

                if ( $rsTipo3->getCampo('codigo_retencao') != '1708' ) {
                    $linhaRTPO = 'RTPO|'.substr($rsTipo3->getCampo('jan'),13,13).'|'.substr($rsTipo3->getCampo('fev'),13,13).'|'.
                        substr($rsTipo3->getCampo('mar'),13,13).'|'.substr($rsTipo3->getCampo('abr'),13,13).'|'.
                        substr($rsTipo3->getCampo('mai'),13,13).'|'.substr($rsTipo3->getCampo('jun'),13,13).'|'.
                        substr($rsTipo3->getCampo('jul'),13,13).'|'.substr($rsTipo3->getCampo('ago'),13,13).'|'.
                        substr($rsTipo3->getCampo('set'),13,13).'|'.substr($rsTipo3->getCampo('out'),13,13).'|'.
                        substr($rsTipo3->getCampo('nov'),13,13).'|'.substr($rsTipo3->getCampo('dez'),13,13).'|'.
                        substr($rsTipo3->getCampo('dec'),13,13)."|\n";
                    if( doEscrever($linhaRTPO) ) fputs($file, $linhaRTPO);
                }

                $linhaRTIRF = 'RTIRF|'.substr($rsTipo3->getCampo('jan'),26,13).'|'.substr($rsTipo3->getCampo('fev'),26,13).'|'.
                    substr($rsTipo3->getCampo('mar'),26,13).'|'.substr($rsTipo3->getCampo('abr'),26,13).'|'.
                    substr($rsTipo3->getCampo('mai'),26,13).'|'.substr($rsTipo3->getCampo('jun'),26,13).'|'.
                    substr($rsTipo3->getCampo('jul'),26,13).'|'.substr($rsTipo3->getCampo('ago'),26,13).'|'.
                    substr($rsTipo3->getCampo('set'),26,13).'|'.substr($rsTipo3->getCampo('out'),26,13).'|'.
                    substr($rsTipo3->getCampo('nov'),26,13).'|'.substr($rsTipo3->getCampo('dez'),26,13).'|';
                if ( $rsTipo3->getCampo('codigo_retencao') == '1708' ) {
                    $linhaRTIRF.= "|\n";
                }else{
                    $linhaRTIRF .= substr($rsTipo3->getCampo('dec'),26,13)."|\n";
                }
                
                if( doEscrever($linhaRTIRF) ) fputs($file, $linhaRTIRF);

            } elseif ( $rsTipo3->getCampo('ident_especializacao') == 1 ) {
                $linhaRTDP = 'RTDP|'.substr($rsTipo3->getCampo('jan'),13,13).'|'.substr($rsTipo3->getCampo('fev'),13,13).'|'.
                    substr($rsTipo3->getCampo('mar'),13,13).'|'.substr($rsTipo3->getCampo('abr'),13,13).'|'.
                    substr($rsTipo3->getCampo('mai'),13,13).'|'.substr($rsTipo3->getCampo('jun'),13,13).'|'.
                    substr($rsTipo3->getCampo('jul'),13,13).'|'.substr($rsTipo3->getCampo('ago'),13,13).'|'.
                    substr($rsTipo3->getCampo('set'),13,13).'|'.substr($rsTipo3->getCampo('out'),13,13).'|'.
                    substr($rsTipo3->getCampo('nov'),13,13).'|'.substr($rsTipo3->getCampo('dez'),13,13).'|'.
                    substr($rsTipo3->getCampo('dec'),13,13)."|\n";
                if( doEscrever($linhaRTDP) ) fputs($file, $linhaRTDP);

                $linhaRTPA = 'RTPA|'.substr($rsTipo3->getCampo('jan'),26,13).'|'.substr($rsTipo3->getCampo('fev'),26,13).'|'.
                    substr($rsTipo3->getCampo('mar'),26,13).'|'.substr($rsTipo3->getCampo('abr'),26,13).'|'.
                    substr($rsTipo3->getCampo('mai'),26,13).'|'.substr($rsTipo3->getCampo('jun'),26,13).'|'.
                    substr($rsTipo3->getCampo('jul'),26,13).'|'.substr($rsTipo3->getCampo('ago'),26,13).'|'.
                    substr($rsTipo3->getCampo('set'),26,13).'|'.substr($rsTipo3->getCampo('out'),26,13).'|'.
                    substr($rsTipo3->getCampo('nov'),26,13).'|'.substr($rsTipo3->getCampo('dez'),26,13).'|'.
                    substr($rsTipo3->getCampo('dec'),26,13)."|\n";
                if( doEscrever($linhaRTPA) ) fputs($file, $linhaRTPA);
            }

            $beneficiario = $rsTipo3->getCampo('beneficiario');
            $retencao = $rsTipo3->getCampo('codigo_retencao');
            $rsTipo3->proximo();
        }

    }

    /* Plano de Saúde */
    $CPFs = implode('\',\'',$arCPFs);
    $CPFs = '\''.$CPFs.'\'';
    $stFiltro = " WHERE configuracao_dirf_plano.exercicio = '".$request->get('inAnoCompetencia')."'";
    $obTIMAConfiguracaoDirfPlano->recuperaRelacionamento($rsPlanoSaude, $stFiltro);
    //$obTIMAConfiguracaoDirfPlano->setDado('stTipoFiltro', 'cgm_contrato_todos');
    $obTIMAConfiguracaoDirfPlano->setDado('inCodEvento', $rsPlanoSaude->getCampo('cod_evento'));
    $obTIMAConfiguracaoDirfPlano->setDado('inExercicio', $rsPlanoSaude->getCampo('exercicio'));
    $stFiltro = " WHERE cpf in (".$CPFs.") and valor > 0";

    if( $rsPlanoSaude->getNumLinhas() > 0 ) fputs($file, "PSE|\n");

    while ( !$rsPlanoSaude->eof() ) {
        $linha = 'OPSE|'.str_pad($rsPlanoSaude->getCampo('cnpj'),14,0,STR_PAD_LEFT).'|'.trim($rsPlanoSaude->getCampo('nom_cgm')).'|'.str_pad($rsPlanoSaude->getCampo('registro_ans'),6,0,STR_PAD_LEFT)."|\n";
        fputs($file, $linha);
        if ( $rsConfigDirf->getCampo('pagamento_mes_competencia') == 't') {
            $obTIMAConfiguracaoDirfPlano->recuperaPlanoSaudeDirfPagamento($rsPlanos,$stFiltro);
        } else {
            $obTIMAConfiguracaoDirfPlano->recuperaPlanoSaudeDirf($rsPlanos,$stFiltro);
        }
        $rsPlanos->ordena('cpf');

        while ( !$rsPlanos->eof() ) {
            $linha = 'TPSE|'.str_pad($rsPlanos->getCampo('cpf'),11,0,STR_PAD_LEFT).'|'.trim(str_replace('.','',$rsPlanos->getCampo('nom_cgm'))).'|'.str_pad(str_replace('.','',$rsPlanos->getCampo('valor')),13,0,STR_PAD_LEFT)."|\n";
            fputs($file, $linha);
            $rsPlanos->proximo();
        }

        $rsPlanoSaude->proximo();
    }

    /* Molestia Grave */

    fputs($file, 'FIMDirf|');
    fclose($file);
}

function doEscrever($stLinha)
{
    $inCount = 0;
    $arLinha = explode('|', $stLinha);
    foreach ($arLinha as $valor) {
        if ($inCount > 0) {
            $total += (int) $valor;
        }
        $inCount++;
    }
    if ( (int) $total > 0 ) {
        return true;
    } else {
        return false;
    }
}

?>

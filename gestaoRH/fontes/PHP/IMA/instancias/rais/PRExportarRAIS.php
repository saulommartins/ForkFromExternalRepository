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
    * Página de Processamento do Exportação RAIS
    * Data de Criação: 26/10/2007

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.08.13

    $Id: PRExportarRAIS.php 66258 2016-08-03 14:25:21Z evandro $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR                  );

Sessao::remove('filtroRelatorio');

$stAcao = $request->get('stAcao');
$arSessaoLink = Sessao::read('link');

if (empty($arSessaoLink)) {
    $stLink = "";
} else {
    $stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];
}

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarRAIS";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&stLink".$stLink;
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=".$stAcao."&stLink".$stLink;
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->recuperaUltimaMovimentacao($rsPeriodoMovimentacao);
$arDtFinal = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
$inExercicio = $_POST["inAnoCompetencia"];

if ((strpos($pgForm, "if") == true)) {
    $arURL = explode("&",$pgForm);
    foreach ($arURL as $chave=>$valor) {
        if (stristr($valor,'if') || stristr($valor,'trim') ) {
            unset($arURL[$chave]);
        }
    }
    $pgForm = implode('&',$arURL);
}

Sessao::setTrataExcecao(true);
$obExportador = new Exportador();
$obExportador->setRetorno($pgForm);
$obExportador->addArquivo("RAIS.TXT");
$obExportador->roUltimoArquivo->setTipoDocumento("RAIS");

#Registro do tipo zero
#Informações da empresa
include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltro = " AND cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$obTEntidade->recuperaEntidades($rsEntidade,$stFiltro);

include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
$obTCGMPessoaJuridica = new TCGMPessoaJuridica();
$stFiltro = " AND CGM.numcgm = ".$rsEntidade->getCampo("numcgm");
$obTCGMPessoaJuridica->recuperaRelacionamento($rsCGM,$stFiltro);
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoRais.class.php");
$obTIMAConfiguracaoRAIS = new TIMAConfiguracaoRais();
$obTIMAConfiguracaoRAIS->setDado("exercicio",$inExercicio);
$obTIMAConfiguracaoRAIS->recuperaPorChave($rsConfiguracao);

include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaFisica.class.php");
$obTCGMPessoaFisica = new TCGMPessoaFisica();
$stFiltro = " AND CGM.numcgm = ".$rsConfiguracao->getCampo("numcgm");
$obTCGMPessoaFisica->recuperaRelacionamento($rsCGMResponsavel,$stFiltro);

if ($rsCGMResponsavel->eof()) {
    SistemaLegado::LiberaFrames();
    Sessao::getExcecao()->setDescricao('CGM do Responsável inválido! Cadastre uma pessoa física como responsável no cadastro de configuração da RAIS!');
    Sessao::encerraExcecao();
}
if ( $rsCGMResponsavel->getCampo("dt_nascimento") == '' ) {
    SistemaLegado::LiberaFrames();
    Sessao::getExcecao()->setDescricao('A data de nascimento do responsável deve ser informada! Cadastre a data de nascimento do CGM '.$rsCGMResponsavel->getCampo("dt_nascimento").'!');
    Sessao::encerraExcecao();
}

$inInscricaoResponsavel = $rsCGMResponsavel->getCampo("cpf");
$stDataNascimentoResponsavel = $rsCGMResponsavel->getCampo("dt_nascimento");
$arDataNascimentoResponsavel = explode('-', $stDataNascimentoResponsavel);
$stDataNascimentoResponsavel = str_pad($arDataNascimentoResponsavel[2], 2, '0',STR_PAD_LEFT);
$stDataNascimentoResponsavel.= str_pad($arDataNascimentoResponsavel[1], 2, '0',STR_PAD_LEFT);
$stDataNascimentoResponsavel.= $arDataNascimentoResponsavel[0];
$stConstanteResponsavel      = '0550';
$nuCPFResponsavel            = $rsCGMResponsavel->getCampo("cpf");
$nuCREARetificado            = '';
$inTipoInscricaoResponsavel  = 4;

$inCodMunicipio = SistemaLegado::pegaConfiguracao("cod_municipio",2,Sessao::getExercicio());
$inCodUF        = SistemaLegado::pegaConfiguracao("cod_uf",2,Sessao::getExercicio());
include_once(CAM_GA_CSE_MAPEAMENTO."TMunicipio.class.php");
$obTMunicipio = new TMunicipio();
$obTMunicipio->setDado("cod_municipio",$inCodMunicipio);
$obTMunicipio->setDado("cod_uf",$inCodUF);
$obTMunicipio->recuperaPorChave($rsMunicipio);

include_once(CAM_GA_CSE_MAPEAMENTO."TUf.class.php");
$obTUF = new TUf();
$obTUF->setDado("cod_uf",$inCodUF);
$obTUF->recuperaPorChave($rsUF);

$inSequencial = 1;

$arRegistroTipoZero[0]["sequencia"]                     = $inSequencial;
$arRegistroTipoZero[0]["inscricao"]                     = $rsCGM->getCampo("cnpj");
$arRegistroTipoZero[0]["prefixo"]                       = "00";
$arRegistroTipoZero[0]["tipo_registro"]                 = "0";
$arRegistroTipoZero[0]["indicador"]                     = "1";
$arRegistroTipoZero[0]["inscricao_responsavel"]         = $inInscricaoResponsavel;
$arRegistroTipoZero[0]["tipo_inscricao_responsavel"]    = $inTipoInscricaoResponsavel;
$arRegistroTipoZero[0]["responsavel"]                   = removeAcentos($rsCGMResponsavel->getCampo("nom_cgm"));
$arRegistroTipoZero[0]["endereco_responsavel"]          = $rsCGMResponsavel->getCampo("logradouro");
$arRegistroTipoZero[0]["numero"]                        = $rsCGMResponsavel->getCampo("numero");
$arRegistroTipoZero[0]["complemento"]                   = $rsCGMResponsavel->getCampo("complemento");
$arRegistroTipoZero[0]["bairro"]                        = $rsCGMResponsavel->getCampo("bairro");
$arRegistroTipoZero[0]["cep"]                           = str_replace(" ","",preg_replace ("/[![:alpha:]|.|,|\/]/", "", $rsCGMResponsavel->getCampo("cep")));
$arRegistroTipoZero[0]["codigo_municipio"]              = $rsConfiguracao->getCampo("cod_municipio");
$arRegistroTipoZero[0]["nome_municipio"]                = $rsMunicipio->getCampo("nom_municipio");
$arRegistroTipoZero[0]["sigla_uf"]                      = $rsUF->getCampo("sigla_uf");
$arRegistroTipoZero[0]["ddd"]                           = substr($rsConfiguracao->getCampo("telefone"),0,2);
$arRegistroTipoZero[0]["telefone_fax"]                  = substr($rsConfiguracao->getCampo("telefone"),2,strlen($rsConfiguracao->getCampo("telefone")));
$arRegistroTipoZero[0]["indicador_retificacao"]         = $_POST["stIndicador"];
$arRegistroTipoZero[0]["data_retificacao"]              = str_replace("/","",$request->get("dtRetificacao"));
$arRegistroTipoZero[0]["data_geracao"]                  = str_replace("/","",$_POST["dtGeracaoArquivo"]);
$arRegistroTipoZero[0]["email_responsavel"]             = $rsConfiguracao->getCampo("email");
$arRegistroTipoZero[0]["nome_responsavel"]              = removeAcentos($rsCGMResponsavel->getCampo("nom_cgm"));
$arRegistroTipoZero[0]["constante_responsavel"]         = $stConstanteResponsavel;
$arRegistroTipoZero[0]["cpf_responsavel"]               = $nuCPFResponsavel;
$arRegistroTipoZero[0]["crea_retificado"]               = $nuCREARetificado;
$arRegistroTipoZero[0]["dt_nascimento_responsavel"]     = $stDataNascimentoResponsavel;
$arRegistroTipoZero[0]["brancos"]                       = "";

$rsRegistroTipoZero = new RecordSet();
$rsRegistroTipoZero->preenche($arRegistroTipoZero);

$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoZero);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prefixo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(19);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ddd");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("telefone_fax");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_retificacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_retificacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_geracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(52);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
//alteração para o ano base 2009
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("constante_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("crea_retificado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento_responsavel");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(159);

#Registro do tipo um
#Informações da empresa
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
$obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
$obTAdministracaoConfiguracao->setDado("cod_modulo",40);
$obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
$obTAdministracaoConfiguracao->setDado("parametro","cnae_fiscal".Sessao::getEntidade());
$obTAdministracaoConfiguracao->recuperaPorChave($rsCnaeFiscal);
if ( $rsCnaeFiscal->getCampo("valor") != "" ) {
    include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php" );
    $obTCEMCnaeFiscal = new TCEMCnaeFiscal;
    $stFiltro = " WHERE cod_cnae = ".$rsCnaeFiscal->getCampo("valor");
    $obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsCnaeFiscal,$stFiltro );
}

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidor.class.php");
$obTPessoalContratoServidor = new TPessoalContratoServidor();
$obTPessoalContratoServidor->recuperaContagemSindicatos($rsSindicatos);
$inCountSindicato = 0;
$inCNPJSindicato = "";
while (!$rsSindicatos->eof()) {
    if ($rsSindicatos->getCampo("contador") > $inCountSindicato) {
        $inCNPJSindicato = $rsSindicatos->getCampo("cnpj");
        $inCGMSindicato  = $rsSindicatos->getCampo("numcgm");
        $inCountSindicato = $rsSindicatos->getCampo("contador");
    }
    $rsSindicatos->proximo();
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSindicato.class.php");
$obTFolhaPagamentoSindicato = new TFolhaPagamentoSindicato();
$obTFolhaPagamentoSindicato->setDado("numcgm",$inCGMSindicato);
$obTFolhaPagamentoSindicato->recuperaPorChave($rsEventoSindicato);

$inTotalSindicato = 0;
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
$obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();

if ($rsSindicatos->getNumLinhas() > 0) {
    $stFiltroEventosCalculado  = " AND evento_calculado.cod_evento = ".$rsEventoSindicato->getCampo("cod_evento");
    $stFiltroEventosCalculado .= " AND to_char(dt_final,'yyyy') = '".$inExercicio."'";
    $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosRais($rsEventosCalculados,$stFiltroEventosCalculado);
    $inTotalSindicato += $rsEventosCalculados->getCampo("valor");
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php");
$obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();

if ($rsSindicatos->getNumLinhas() > 0) {
    $stFiltroEventosCalculado  = " AND evento_ferias_calculado.cod_evento = ".$rsEventoSindicato->getCampo("cod_evento");
    $stFiltroEventosCalculado .= " AND to_char(dt_final,'yyyy') = '".$inExercicio."'";
    $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculadosRais($rsEventosCalculados,$stFiltroEventosCalculado);
    $inTotalSindicato += $rsEventosCalculados->getCampo("valor");
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
$obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();

if ($rsSindicatos->getNumLinhas() > 0) {
    $stFiltroEventosCalculado  = " AND evento_rescisao_calculado.cod_evento = ".$rsEventoSindicato->getCampo("cod_evento");
    $stFiltroEventosCalculado .= " AND to_char(dt_final,'yyyy') = '".$inExercicio."'";
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventosCalculados,$stFiltroEventosCalculado);
    $inTotalSindicato += $rsEventosCalculados->getCampo("valor");
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php");
$obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();

if ($rsSindicatos->getNumLinhas() > 0) {
    $stFiltroEventosCalculado  = " AND evento_complementar_calculado.cod_evento = ".$rsEventoSindicato->getCampo("cod_evento");
    $stFiltroEventosCalculado .= " AND to_char(dt_final,'yyyy') = '".$inExercicio."'";
    $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculadosRais($rsEventosCalculados,$stFiltroEventosCalculado);
    $inTotalSindicato += $rsEventosCalculados->getCampo("valor");
    $inTotalSindicato = str_replace(".","",number_format($inTotalSindicato,2,".",""));
}

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php");
$obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();

$inSequencial++;

$arRegistroTipoUm[0]["sequencia"]                       = $inSequencial;
$arRegistroTipoUm[0]["inscricao"]                       = $rsCGM->getCampo("cnpj");
$arRegistroTipoUm[0]["prefixo"]                         = "00";
$arRegistroTipoUm[0]["tipo_registro"]                   = "1";
$arRegistroTipoUm[0]["nome_firma_razao"]                = removeAcentos($rsCGM->getCampo("nom_cgm"));
$arRegistroTipoUm[0]["endereco"]                        = $rsCGM->getCampo("logradouro");
$arRegistroTipoUm[0]["numero"]                          = $rsCGM->getCampo("numero");
$arRegistroTipoUm[0]["complemento"]                     = $rsCGM->getCampo("complemento");
$arRegistroTipoUm[0]["bairro"]                          = $rsCGM->getCampo("bairro");
$arRegistroTipoUm[0]["cep"]                             = str_replace(" ","",preg_replace ("/[![:alpha:]|.|,|\/]/", "", $rsCGM->getCampo("cep")));
$arRegistroTipoUm[0]["codigo_municipio"]                = $rsConfiguracao->getCampo("cod_municipio");
$arRegistroTipoUm[0]["nome_municipio"]                  = $rsMunicipio->getCampo("nom_municipio");
$arRegistroTipoUm[0]["sigla_uf"]                        = $rsUF->getCampo("sigla_uf");
$arRegistroTipoUm[0]["ddd"]                             = substr($rsConfiguracao->getCampo("telefone"),0,2);
$arRegistroTipoUm[0]["telefone"]                        = substr($rsConfiguracao->getCampo("telefone"),2,strlen($rsConfiguracao->getCampo("telefone")));
$arRegistroTipoUm[0]["email_estabelecimento"]           = $rsCGM->getCampo("e_mail");
$arRegistroTipoUm[0]["cnae"]                            = str_replace(" ","",preg_replace("/[A-Za-z\-\/\.]/","",$rsCnaeFiscal->getCampo("valor_composto")));
$arRegistroTipoUm[0]["natureza_juridica"]               = $rsConfiguracao->getCampo("natureza_juridica");
$arRegistroTipoUm[0]["numero_proprietarios"]            = "00";
$arRegistroTipoUm[0]["data_base"]                       = $rsConfiguracao->getCampo("dt_base_categoria");
$arRegistroTipoUm[0]["tipo_inscricao"]                  = "1";
$arRegistroTipoUm[0]["tipo_rais"]                       = "0";
$arRegistroTipoUm[0]["zeros"]                           = "00";
$arRegistroTipoUm[0]["matricula_cei"]                   = $rsConfiguracao->getCampo("numero_cei");
$arRegistroTipoUm[0]["2006"]                            = $_POST["inAnoCompetencia"];
$arRegistroTipoUm[0]["indicador_porte"]                 = "3";
$arRegistroTipoUm[0]["indicador_optante"]               = "2";
$arRegistroTipoUm[0]["indicador_participacao"]          = "2";
$arRegistroTipoUm[0]["vinculo_ate_5"]                   = "0";
$arRegistroTipoUm[0]["vinculo_acima_5"]                 = "0";
$arRegistroTipoUm[0]["porcentagem_servico"]             = "0";
$arRegistroTipoUm[0]["porcentagem_administracao"]       = "0";
$arRegistroTipoUm[0]["porcentagem_refeicao"]            = "0";
$arRegistroTipoUm[0]["porcentagem_transportadas"]       = "0";
$arRegistroTipoUm[0]["porcentagem_cesta"]               = "0";
$arRegistroTipoUm[0]["porcentagem_alimentacao"]         = "0";
$arRegistroTipoUm[0]["indicador_encerramento"]          = "2";
$arRegistroTipoUm[0]["data_encerramento"]               = "0";
$arRegistroTipoUm[0]["cnpj_patronal"]                   = "0";
$arRegistroTipoUm[0]["valor_patronal"]                  = "0";
$arRegistroTipoUm[0]["cnpj_sindical"]                   = $inCNPJSindicato;
$arRegistroTipoUm[0]["valor_sindical"]                  = $inTotalSindicato;
$arRegistroTipoUm[0]["cnpj_assistencial"]               = "0";
$arRegistroTipoUm[0]["valor_assistencial"]              = "0";
$arRegistroTipoUm[0]["cnpj_confederativa"]              = "0";
$arRegistroTipoUm[0]["valor_confederativa"]             = "0";
$arRegistroTipoUm[0]["atividade_ano_base"]              = "1";
$arRegistroTipoUm[0]["indicador_centralizacao"]         = "2";
$arRegistroTipoUm[0]["cnpj_centralizadora"]             = "0";
$arRegistroTipoUm[0]["indicador"]                       = "2";
$arRegistroTipoUm[0]["cod_tipo_controle_ponto"]         = str_pad($rsConfiguracao->getCampo("cod_tipo_controle_ponto"), 2, "0", STR_PAD_LEFT);
$arRegistroTipoUm[0]["brancos"]                         = "";
$arRegistroTipoUm[0]["uso_empresa"]                     = "";

$rsRegistroTipoUm = new RecordSet();
$rsRegistroTipoUm->preenche($arRegistroTipoUm);

$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoUm);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);//1 a 6
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//7 a 20
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prefixo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//21 a 22
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//23 a 23
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_firma_razao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(52);//24 a 75
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);//76 a 115
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);//116 a 121
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("complemento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(21);//122 a 142
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(19);//143 a 161
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);//162 a 169
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);//170 a 176
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);//177 a 206
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//207 a 208
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ddd");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//209 a 210
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("telefone");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);//211 a 219
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("email_estabelecimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(45);//220 a 264
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnae");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);//265 a 271
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("natureza_juridica");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);//272 a 275
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_proprietarios");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);//276 a 279
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_base");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//280 a 281
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_inscricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//282
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_rais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//283
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("zeros");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//284 a 285
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula_cei");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);//286 a 297
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("2006");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);//298 a 301
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_porte");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//302
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_optante");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//303
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_participacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//304
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vinculo_ate_5");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);//305 a 310
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vinculo_acima_5");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);//311 a 316
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_servico");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//317 a 319
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_administracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//320 a 322
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_refeicao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//323 a 325
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_transportadas");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//236 a 328
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_cesta");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//329 a 331
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("porcentagem_alimentacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);//332 a 334
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_encerramento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//335
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_encerramento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);//336 a 343
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_patronal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//344 a 357
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_patronal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);//358 a 366
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_sindical");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//367 a 380
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_sindical");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);//381 a 389
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_assistencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//390 a 403
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_assistencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);//404 a 412
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_confederativa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//412 a 426
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_confederativa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);//427 a 435
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("atividade_ano_base");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//436
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_centralizacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//437
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_centralizadora");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);//438 a 451
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);//452
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_tipo_controle_ponto");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);//453 a 454
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(85);//455 a 539
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_empresa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);//540 a 551

#Registro do tipo dois
#Informações das pessoas
switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stCodContratos = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodContratos .= $arContrato["cod_contrato"].",";
        }
        $stCodigosFiltroRais = substr($stCodContratos,0,strlen($stCodContratos)-1);
        break;
    case "lotacao":
        $stCodigosFiltroRais = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local":
        $stCodigosFiltroRais = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "atributo_servidor":
        $inCodAtributo = $_POST["inCodAtributo"];
        $inCodCadastro = $_POST["inCodCadastro"];
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;
        if (is_array($_POST[$stNomeAtributo."_Selecionados"])) {
            $inArray = 1;
            $stValores = implode(",",$_POST[$stNomeAtributo."_Selecionados"]);
        } else {
            $inArray = 0;
            $stValores = pg_escape_string($_POST[$stNomeAtributo]);
        }
        $stCodigosFiltroRais = $inArray."#".$inCodAtributo."#".$stValores;
        break;
    case "reg_sub_fun_esp":
        $stCodigosFiltroRais  = implode(",",$_REQUEST["inCodRegimeSelecionadosFunc"])."#";
        $stCodigosFiltroRais .= implode(",",$_REQUEST["inCodSubDivisaoSelecionadosFunc"])."#";
        $stCodigosFiltroRais .= implode(",",$_REQUEST["inCodFuncaoSelecionados"])."#";
        if (is_array($_REQUEST["inCodEspecialidadeSelecionadosFunc"])) {
            $stCodigosFiltroRais .= implode(",",$_REQUEST["inCodEspecialidadeSelecionadosFunc"]);
        }
        break;
}

$stFiltroRais = isset($stFiltroRais) ? $stFiltroRais : "";
$obTIMAConfiguracaoRAIS->setDado("stTipoFiltro",$_POST["stTipoFiltro"]);
$obTIMAConfiguracaoRAIS->setDado('stCodigos', $stCodigosFiltroRais);
$obTIMAConfiguracaoRAIS->setDado("exercicio",$inExercicio);
$obTIMAConfiguracaoRAIS->recuperaExportarArquivoRais($rsContratosRais,$stFiltroRais," ORDER BY nom_cgm");//consulta que retorna da dos

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAEventoComposicaoRemuneracao.class.php");
include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAEventoHorasExtras.class.php");

$obTIMAEventoHorasExtras = new TIMAEventoHorasExtras();
$obTIMAEventoHorasExtras->setDado("exercicio",$inExercicio);
$obTIMAEventoHorasExtras->recuperaPorChave($rsEventosHorasExtra);
$stEventosHorasExtra = "";
while (!$rsEventosHorasExtra->eof()) {
    $stEventosHorasExtra .= $rsEventosHorasExtra->getCampo("cod_evento").",";
    $rsEventosHorasExtra->proximo();
}
$stEventosHorasExtra = substr($stEventosHorasExtra,0,strlen($stEventosHorasExtra)-1);

$obTIMAEventoComposicaoRemuneracao = new TIMAEventoComposicaoRemuneracao();
$obTIMAEventoComposicaoRemuneracao->setDado("exercicio",$inExercicio);
$obTIMAEventoComposicaoRemuneracao->recuperaPorChave($rsEventosRemuneracao);
$stEventosRemuneracao = "";
while (!$rsEventosRemuneracao->eof()) {
    $stEventosRemuneracao .= $rsEventosRemuneracao->getCampo("cod_evento").",";
    $rsEventosRemuneracao->proximo();
}
$stEventosRemuneracao = substr($stEventosRemuneracao,0,strlen($stEventosRemuneracao)-1);

$stFiltroPeriodo = " AND to_char(dt_final,'yyyy') = '".$inExercicio."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodosMovimentacao,$stFiltroPeriodo," order by cod_periodo_movimentacao");

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAvisoPrevio.class.php");
$obTPessoalAvisoPrevio = new TPessoalAvisoPrevio();

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAssentamentoAfastamentoTemporario.class.php");
$obTPessoalAssentamentoAfastamentoTemporario = new TPessoalAssentamentoAfastamentoTemporario();

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoSindicato.class.php");
$obTFolhaPagamentoSindicato = new TFolhaPagamentoSindicato();

$inSequencial++;
$inIndex = 0;
$inTotalRegistroTipo2 = 0;
$arRegistroTipoDois = array();
while (!$rsContratosRais->eof()) {
    $arRemuneracao = array("01"=>"","02"=>"","03"=>"","04"=>"","05"=>"","06"=>"","07"=>"","08"=>"","09"=>"","10"=>"","11"=>"","12"=>"");
    $arHorasExtra  = array("01"=>"","02"=>"","03"=>"","04"=>"","05"=>"","06"=>"","07"=>"","08"=>"","09"=>"","10"=>"","11"=>"","12"=>"");
    $nuAdiantamentoDecimo = 0;
    $stMesAdiantamentoDecimo = 0;
    $nuDecimo = 0;
    $stMesDecimo = 0;
    $nuAvisoPrevio = 0;
    $inTotalDiasAfastamento = 0;
    $nuTotalFeriasIndenizadas = 0;
    $nuTotalEventoSindicato = 0;

    $obTPessoalAvisoPrevio->setDado("cod_contrato",$rsContratosRais->getCampo("cod_contrato"));
    $obTPessoalAvisoPrevio->recuperaPorChave($rsAvisoPrevio);

    $obTPessoalAssentamentoAfastamentoTemporario->setDado("dias",15);
    $obTPessoalAssentamentoAfastamentoTemporario->setDado("exercicio",$inExercicio);
    $obTPessoalAssentamentoAfastamentoTemporario->setDado("cod_contrato",$rsContratosRais->getCampo("cod_contrato"));
    $obTPessoalAssentamentoAfastamentoTemporario->recuperaAssentamentoTemporarioRais($rsAssentamentoRais);

    if ($rsSindicatos->getNumLinhas() > 0) {
        $obTFolhaPagamentoSindicato->setDado("numcgm",$rsContratosRais->getCampo("numcgm_sindicato"));
        $obTFolhaPagamentoSindicato->recuperaPorChave($rsEventoSindicato);
    }

    while (!$rsPeriodosMovimentacao->eof()) {

        $arDtFinal = explode("/",$rsPeriodosMovimentacao->getCampo("dt_final"));

        ##########REMUNERAÇÃO
        $stFiltroEventoCalculado  = " AND registro_evento_periodo.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_calculado.cod_evento IN ($stEventosRemuneracao)";
        $stFiltroEventoCalculado .= " AND (evento_calculado.desdobramento != 'A' OR evento_calculado.desdobramento IS NULL)";
        $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosRais($rsEventoCalculados,$stFiltroEventoCalculado);

        $rsEventoCalculadosSindicato = new RecordSet();
        if ($rsContratosRais->getCampo("numcgm_sindicato") != "" && $rsEventoSindicato->getCampo("cod_evento") != 0 && $rsEventoSindicato->getCampo("cod_evento") != '') {
            $stFiltroEventoCalculado  = " AND registro_evento_periodo.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_calculado.cod_evento = ".$rsEventoSindicato->getCampo("cod_evento");
            $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosRais($rsEventoCalculadosSindicato,$stFiltroEventoCalculado);
        }
        $nuTotalEventoSindicato += $rsEventoCalculadosSindicato->getCampo("valor");

        $rsEventoComplementarCalculadosSindicato = new RecordSet();
        if ($rsContratosRais->getCampo("numcgm_sindicato") != "" && $rsEventoSindicato->getCampo("cod_evento") != 0 && $rsEventoSindicato->getCampo("cod_evento") != '') {
            $stFiltroEventoCalculado  = " AND registro_evento_complementar.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_complementar_calculado.cod_evento =".$rsEventoSindicato->getCampo("cod_evento");
            $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculadosRais($rsEventoComplementarCalculadosSindicato,$stFiltroEventoCalculado);
        }
        $nuTotalEventoSindicato += $rsEventoComplementarCalculadosSindicato->getCampo("valor");

        $stFiltroEventoCalculado  = " AND registro_evento_complementar.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_complementar_calculado.cod_evento IN ($stEventosRemuneracao)";
        $stFiltroEventoCalculado .= " AND evento_complementar_calculado.cod_configuracao != 3";
        $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculadosRais($rsEventoComplementarCalculados,$stFiltroEventoCalculado);

        $stFiltroEventoCalculado  = " AND registro_evento_ferias.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_ferias_calculado.cod_evento IN ($stEventosRemuneracao)";
        $stFiltroEventoCalculado .= " AND evento_ferias_calculado.desdobramento != 'A'";
        $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculadosRais($rsEventoFeriasCalculados,$stFiltroEventoCalculado);

        $stFiltroEventoCalculado  = " AND registro_evento_rescisao.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.cod_evento IN ($stEventosRemuneracao)";
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento != 'D'";
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento != 'V'";
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento != 'P'";
        if ($rsAvisoPrevio->getCampo("aviso_previo") == "i") {
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento != 'A'";
        }
        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventoRescisaoCalculados,$stFiltroEventoCalculado);

        if ($nuAvisoPrevio==0 and $rsAvisoPrevio->getCampo("aviso_previo") == "i") {
            $stFiltroEventoCalculado  = " AND registro_evento_rescisao.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.cod_evento IN ($stEventosRemuneracao)";
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento = 'A'";
            $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventoRescisaoCalculadosAvisoPrevio,$stFiltroEventoCalculado);
            $nuAvisoPrevio = $rsEventoRescisaoCalculadosAvisoPrevio->getCampo("valor");
        }
        if ($nuTotalFeriasIndenizadas==0) {
            $stFiltroEventoCalculado  = " AND registro_evento_rescisao.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.cod_evento IN ($stEventosRemuneracao)";
            $stFiltroEventoCalculado .= " AND (evento_rescisao_calculado.desdobramento = 'V' OR evento_rescisao_calculado.desdobramento = 'P')";
            $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventoRescisaoCalculadosFeriasIndenizadas,$stFiltroEventoCalculado);
            $nuTotalFeriasIndenizadas = $rsEventoRescisaoCalculadosFeriasIndenizadas->getCampo("valor");
        }

        if ($nuAdiantamentoDecimo <= 0) {
            $stFiltroEventoCalculado  = " AND registro_evento_decimo.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_decimo_calculado.cod_evento IN ($stEventosRemuneracao)";
            $stFiltroEventoCalculado .= " AND evento_decimo_calculado.desdobramento = 'A'";
            $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosCalculadosRais($rsEventodecimoCalculadosAdiantamento,$stFiltroEventoCalculado);
            if ($rsEventodecimoCalculadosAdiantamento->getCampo("valor")>0) {
                $nuAdiantamentoDecimo = $rsEventodecimoCalculadosAdiantamento->getCampo("valor");
                $stMesAdiantamentoDecimo = $arDtFinal[1];
            }
        }

        if ($nuDecimo <= 0) {
            $stFiltroEventoCalculado  = " AND registro_evento_decimo.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_decimo.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_decimo_calculado.cod_evento IN ($stEventosRemuneracao)";
            $stFiltroEventoCalculado .= " AND evento_decimo_calculado.desdobramento = 'D'";
            $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosCalculadosRais($rsEventodecimoCalculados,$stFiltroEventoCalculado);
            if ($rsEventodecimoCalculados->getCampo("valor")>0) {
                $nuDecimo = $rsEventodecimoCalculados->getCampo("valor");
                $stMesDecimo = $arDtFinal[1];
            }

            $stFiltroEventoCalculado  = " AND registro_evento_rescisao.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
            $stFiltroEventoCalculado .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.cod_evento IN ($stEventosRemuneracao)";
            $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento = 'D'";
            $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventoRescisaoCalculadosDecimo,$stFiltroEventoCalculado);
            if ($rsEventoRescisaoCalculadosDecimo->getCampo("valor")>0) {
                $nuDecimo += $rsEventoRescisaoCalculadosDecimo->getCampo("valor");
                $stMesDecimo = substr($rsContratosRais->getCampo("dt_rescisao"),2,2);
            }
        }
        $arRemuneracao[$arDtFinal[1]] = $rsEventoCalculados->getCampo("valor")+$rsEventoComplementarCalculados->getCampo("valor")+$rsEventoFeriasCalculados->getCampo("valor")+$rsEventoRescisaoCalculados->getCampo("valor");
        ##########REMUNERAÇÃO

        ##########HORAS EXTRAS
        $stFiltroEventoCalculado  = " AND registro_evento_periodo.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_calculado.cod_evento IN ($stEventosHorasExtra)";
        $stFiltroEventoCalculado .= " AND (evento_calculado.desdobramento != 'A' OR evento_calculado.desdobramento IS NULL)";
        $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculadosRais($rsEventoCalculados,$stFiltroEventoCalculado);

        $stFiltroEventoCalculado  = " AND registro_evento_complementar.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_complementar.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_complementar_calculado.cod_evento IN ($stEventosHorasExtra)";
        $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculadosRais($rsEventoComplementarCalculados,$stFiltroEventoCalculado);

        $stFiltroEventoCalculado  = " AND registro_evento_ferias.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_ferias.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_ferias_calculado.cod_evento IN ($stEventosHorasExtra)";
        $stFiltroEventoCalculado .= " AND evento_ferias_calculado.desdobramento != 'A'";
        $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculadosRais($rsEventoFeriasCalculados,$stFiltroEventoCalculado);

        $stFiltroEventoCalculado  = " AND registro_evento_rescisao.cod_contrato = ".$rsContratosRais->getCampo("cod_contrato");
        $stFiltroEventoCalculado .= " AND registro_evento_rescisao.cod_periodo_movimentacao = ".$rsPeriodosMovimentacao->getCampo("cod_periodo_movimentacao");
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.cod_evento IN ($stEventosHorasExtra)";
        $stFiltroEventoCalculado .= " AND evento_rescisao_calculado.desdobramento = 'S'";
        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventosCalculadosRais($rsEventoRescisaoCalculados,$stFiltroEventoCalculado);

        $arHorasExtra[$arDtFinal[1]] = $rsEventoCalculados->getCampo("quantidade")+$rsEventoComplementarCalculados->getCampo("quantidade")+$rsEventoFeriasCalculados->getCampo("quantidade")+$rsEventoRescisaoCalculados->getCampo("quantidade");
        ##########HORAS EXTRAS

        $rsPeriodosMovimentacao->proximo();
    }
    $rsPeriodosMovimentacao->setPrimeiroElemento();

    //Verifica se exista alguma valor calculado no ano base para o servidor
    $nuVerificaAnoBase = $arRemuneracao["01"]+$arRemuneracao["02"]+$arRemuneracao["03"]+$arRemuneracao["04"]+$arRemuneracao["05"]+$arRemuneracao["06"]+$arRemuneracao["07"]+$arRemuneracao["08"]+$arRemuneracao["09"]+$arRemuneracao["10"]+$arRemuneracao["11"]+$arRemuneracao["12"]+$nuAdiantamentoDecimo+$nuDecimo+$nuAvisoPrevio+$nuTotalFeriasIndenizadas;
    if ($nuVerificaAnoBase > 0) {
        $arRegistroTipoDois[$inIndex]["sequencia"]                     = $inSequencial;
        $arRegistroTipoDois[$inIndex]["inscricao"]                     = $rsCGM->getCampo("cnpj");
        $arRegistroTipoDois[$inIndex]["prefixo"]                       = trim($rsConfiguracao->getCampo("prefixo"));
        $arRegistroTipoDois[$inIndex]["tipo_registro"]                 = "2";
        $arRegistroTipoDois[$inIndex]["codigo_pis_pasep"]              = str_replace("-","",str_replace(".","",$rsContratosRais->getCampo("servidor_pis_pasep")));
        $arRegistroTipoDois[$inIndex]["nome_empregado"]                = removeAcentos($rsContratosRais->getCampo("nom_cgm"));
        $arRegistroTipoDois[$inIndex]["data_nascimento"]               = $rsContratosRais->getCampo("dt_nascimento");
        $arRegistroTipoDois[$inIndex]["nacionalidade"]                 = $rsContratosRais->getCampo("nacionalidade");
        $arRegistroTipoDois[$inIndex]["ano_chegada"]                   = "0";
        $arRegistroTipoDois[$inIndex]["grau_instrucao"]                = $rsContratosRais->getCampo("cod_escolaridade");
        $arRegistroTipoDois[$inIndex]["cpf"]                           = $rsContratosRais->getCampo("cpf");
        $arRegistroTipoDois[$inIndex]["numero_ctps"]                   = $rsContratosRais->getCampo("numero");
        $arRegistroTipoDois[$inIndex]["serie_ctps"]                    = $rsContratosRais->getCampo("serie");
        $arRegistroTipoDois[$inIndex]["data_admissao"]                 = $rsContratosRais->getCampo("dt_admissao");
        $arRegistroTipoDois[$inIndex]["tipo_admissao"]                 = $rsContratosRais->getCampo("cod_tipo_admissao");
        $arRegistroTipoDois[$inIndex]["salario_contratual"]            = str_replace(".","",number_format($rsContratosRais->getCampo("salario"),2,".",""));
        $arRegistroTipoDois[$inIndex]["tipo_salario"]                  = $rsContratosRais->getCampo("cod_tipo_salario");
        $arRegistroTipoDois[$inIndex]["horas_semanais"]                = (int) $rsContratosRais->getCampo("horas_semanais");
        $arRegistroTipoDois[$inIndex]["cbo"]                           = $rsContratosRais->getCampo("numero_cbo");
        $arRegistroTipoDois[$inIndex]["vinculo"]                       = $rsContratosRais->getCampo("cod_vinculo");
        $arRegistroTipoDois[$inIndex]["codigo_desligamento"]           = $rsContratosRais->getCampo("num_causa");
        $arRegistroTipoDois[$inIndex]["data_desligamento"]             = $rsContratosRais->getCampo("dt_rescisao");
        $arRegistroTipoDois[$inIndex]["remuneracao_janeiro"]           = formataValor($arRemuneracao["01"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_fevereiro"]         = formataValor($arRemuneracao["02"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_março"]             = formataValor($arRemuneracao["03"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_abril"]             = formataValor($arRemuneracao["04"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_maio"]              = formataValor($arRemuneracao["05"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_junho"]             = formataValor($arRemuneracao["06"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_julho"]             = formataValor($arRemuneracao["07"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_agosto"]            = formataValor($arRemuneracao["08"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_setembro"]          = formataValor($arRemuneracao["09"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_outubro"]           = formataValor($arRemuneracao["10"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_novembro"]          = formataValor($arRemuneracao["11"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_dezembro"]          = formataValor($arRemuneracao["12"]);
        $arRegistroTipoDois[$inIndex]["remuneracao_decimo_adiant"]     = formataValor($nuAdiantamentoDecimo);
        $arRegistroTipoDois[$inIndex]["mes_decimo_adiantamento"]       = $stMesAdiantamentoDecimo;
        $arRegistroTipoDois[$inIndex]["remuneracao_decimo_final"]      = formataValor($nuDecimo-$nuAdiantamentoDecimo);
        $arRegistroTipoDois[$inIndex]["mes_decimo_final"]              = $stMesDecimo;
        $arRegistroTipoDois[$inIndex]["raca"]                          = $rsContratosRais->getCampo("raca");
        $arRegistroTipoDois[$inIndex]["indicador_deficiencia"]         = ($rsContratosRais->getCampo("cod_cid")!="" and $rsContratosRais->getCampo("cod_cid")!=0 )?"1":"2";
        $arRegistroTipoDois[$inIndex]["tipo_deficiencia"]              = $rsContratosRais->getCampo("cod_cid");
        $arRegistroTipoDois[$inIndex]["numero_alvara"]                 = "2";
        $arRegistroTipoDois[$inIndex]["aviso_previo"]                  = formataValor($nuAvisoPrevio);
        $arRegistroTipoDois[$inIndex]["sexo"]                          = ($rsContratosRais->getCampo("sexo")=="m")?1:2;
        $arRegistroTipoDois[$inIndex]["motivo_primeiro_afastamento"]   = $rsAssentamentoRais->getCampo("cod_rais");
        $arRegistroTipoDois[$inIndex]["data_inicio_prim_afast"]        = $rsAssentamentoRais->getCampo("dt_inicial");
        $arRegistroTipoDois[$inIndex]["data_final_prim_afast"]         = $rsAssentamentoRais->getCampo("dt_final");
        $inTotalDiasAfastamento += $rsAssentamentoRais->getCampo("dias");
        $rsAssentamentoRais->proximo();
        $arRegistroTipoDois[$inIndex]["motivo_segundo_afastamento"]    = $rsAssentamentoRais->getCampo("cod_rais");
        $arRegistroTipoDois[$inIndex]["data_inicio_seg_afast"]         = $rsAssentamentoRais->getCampo("dt_inicial");
        $arRegistroTipoDois[$inIndex]["data_final_seg_afast"]          = $rsAssentamentoRais->getCampo("dt_final");
        $inTotalDiasAfastamento += $rsAssentamentoRais->getCampo("dias");
        $rsAssentamentoRais->proximo();
        $arRegistroTipoDois[$inIndex]["motivo_terceiro_afastamento"]   = $rsAssentamentoRais->getCampo("cod_rais");
        $arRegistroTipoDois[$inIndex]["data_inicio_terc_afast"]        = $rsAssentamentoRais->getCampo("dt_inicial");
        $arRegistroTipoDois[$inIndex]["data_final_terc_afast"]         = $rsAssentamentoRais->getCampo("dt_final");
        while (!$rsAssentamentoRais->eof()) {
            $inTotalDiasAfastamento += $rsAssentamentoRais->getCampo("dias");
            $rsAssentamentoRais->proximo();
        }

        $arRegistroTipoDois[$inIndex]["quant_dias_afastamento"]        = $inTotalDiasAfastamento;
        $arRegistroTipoDois[$inIndex]["valor_ferias_indenizadas"]      = formataValor($nuTotalFeriasIndenizadas);
        $arRegistroTipoDois[$inIndex]["valor_banco_de_horas"]          = "0";
        $arRegistroTipoDois[$inIndex]["quant_comp_banco_horas"]        = "0";
        $arRegistroTipoDois[$inIndex]["dissidio"]                      = "0";
        $arRegistroTipoDois[$inIndex]["quant_comp_dissidio"]           = "0";
        $arRegistroTipoDois[$inIndex]["outras_grativicacoes"]          = "0";
        $arRegistroTipoDois[$inIndex]["quant_comp_outras_grat"]        = "0";
        $arRegistroTipoDois[$inIndex]["multa_rescisao"]                = "0";
        $arRegistroTipoDois[$inIndex]["cnpj_prim_ocorrencia"]          = "0";
        $arRegistroTipoDois[$inIndex]["valor_prim_ocorrencia"]         = "0";
        $arRegistroTipoDois[$inIndex]["cnpj_seg_ocorrencia"]           = "0";
        $arRegistroTipoDois[$inIndex]["valor_seg_ocorrencia"]          = "0";
        $arRegistroTipoDois[$inIndex]["cnpj_cont_sindical"]            = $rsContratosRais->getCampo("cnpj_sindicato");
        $arRegistroTipoDois[$inIndex]["valor_cont_sindical"]           = formataValor($nuTotalEventoSindicato);
        $arRegistroTipoDois[$inIndex]["cnpj_cont_assistencial"]        = "0";
        $arRegistroTipoDois[$inIndex]["valor_cont_assistencial"]       = "0";
        $arRegistroTipoDois[$inIndex]["cnpj_cont_confederativa"]       = "0";
        $arRegistroTipoDois[$inIndex]["valor_cont_confederativa"]      = "0";
        $arRegistroTipoDois[$inIndex]["municipio"]                     = $rsConfiguracao->getCampo("cod_municipio");
        $arRegistroTipoDois[$inIndex]["horas_extras_janeiro"]          = round($arHorasExtra["01"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_fevereiro"]        = round($arHorasExtra["02"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_marco"]            = round($arHorasExtra["03"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_abril"]            = round($arHorasExtra["04"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_maio"]             = round($arHorasExtra["05"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_junho"]            = round($arHorasExtra["06"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_julho"]            = round($arHorasExtra["07"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_agosto"]           = round($arHorasExtra["08"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_setembro"]         = round($arHorasExtra["09"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_outubro"]          = round($arHorasExtra["10"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_novembro"]         = round($arHorasExtra["11"]);
        $arRegistroTipoDois[$inIndex]["horas_extras_dezembro"]         = round($arHorasExtra["12"]);
        $arRegistroTipoDois[$inIndex]["indicador_sindicalizado"]       = "2";
        $arRegistroTipoDois[$inIndex]["uso_empresa"]                   = $rsContratosRais->getCampo("registro");

        $inIndex++;
        $inSequencial++;
        $inTotalRegistroTipo2++;
    }
    $rsContratosRais->proximo();
}

$rsRegistroTipoDois = new RecordSet();
$rsRegistroTipoDois->preenche($arRegistroTipoDois);
$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoDois);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prefixo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_pis_pasep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_empregado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(52);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_nascimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nacionalidade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ano_chegada");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("grau_instrucao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_ctps");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("serie_ctps");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_admissao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_admissao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_contratual");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_salario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_semanais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cbo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("vinculo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("codigo_desligamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_desligamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_janeiro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_fevereiro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_março");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_abril");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_maio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_junho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_julho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_agosto");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_setembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_outubro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_novembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_dezembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_decimo_adiant");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_decimo_adiantamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("remuneracao_decimo_final");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("mes_decimo_final");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("raca");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_deficiencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_deficiencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_alvara");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aviso_previo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(9);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("motivo_primeiro_afastamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio_prim_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final_prim_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("motivo_segundo_afastamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio_seg_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final_seg_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("motivo_terceiro_afastamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_inicio_terc_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("data_final_terc_afast");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_dias_afastamento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_ferias_indenizadas");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_banco_de_horas");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_comp_banco_horas");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dissidio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_comp_dissidio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("outras_grativicacoes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("quant_comp_outras_grat");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("multa_rescisao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_prim_ocorrencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_prim_ocorrencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_seg_ocorrencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_seg_ocorrencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cont_sindical");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_cont_sindical");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cont_assistencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_cont_assistencial");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cnpj_cont_confederativa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("valor_cont_confederativa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("municipio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_janeiro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_fevereiro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_marco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_abril");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_maio");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_junho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_julho");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_agosto");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_setembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_outubro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_novembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_extras_dezembro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("indicador_sindicalizado");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uso_empresa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(12);

#Registro do tipo nove
#fechamento do arquivo
$arRegistroTipoZero[0]["sequencia"]                     = $inSequencial;
$arRegistroTipoZero[0]["inscricao"]                     = $rsCGM->getCampo("cnpj");
$arRegistroTipoZero[0]["prefixo"]                       = $rsConfiguracao->getCampo("prefixo");
$arRegistroTipoZero[0]["tipo_registro"]                 = "9";
$arRegistroTipoZero[0]["total_registro_tipo_1"]         = "1";
$arRegistroTipoZero[0]["total_registro_tipo_2"]         = $inTotalRegistroTipo2;
$arRegistroTipoZero[0]["brancos"]                       = "";

$rsRegistroTipoNome = new RecordSet();
$rsRegistroTipoNome->preenche($arRegistroTipoZero);
$obExportador->roUltimoArquivo->addBloco($rsRegistroTipoNome);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("inscricao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("prefixo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_registro_tipo_1");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_registro_tipo_2");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("brancos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(516);

$arRegistroBranco[0]["branco"] = "";
$rsRegistroBranco = new RecordSet();
$rsRegistroBranco->preenche($arRegistroBranco);
$obExportador->roUltimoArquivo->addBloco($rsRegistroBranco);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("branco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(0);

$obExportador->Show();
Sessao::encerraExcecao();
SistemaLegado::LiberaFrames();

function removeAcentos($stCampo)
{
    $Acentos = "áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ";
    $Traducao ="aaaaaAAAAeeEEiIoooOOOuuUUcC";
    $TempLog = "";
    for ($i=0; $i < strlen($stCampo); $i++) {
        $Carac = $stCampo[$i];
        $Posic  = strpos($Acentos,$Carac);
        if ($Posic > -1) {
            $TempLog .= $Traducao[$Posic];
        } else {
            $TempLog .= $stCampo[$i];
        }
    }
    $TempLog = str_replace(".","",$TempLog);
    $TempLog = preg_replace("/[^0-9a-zA-Z ]/i","",$TempLog);

    return $TempLog;
}

function formataValor($nuEntrada)
{
    $nuSaida = str_pad(str_replace(".","",number_format($nuEntrada,2,".","")),15,"0",STR_PAD_LEFT);

    return $nuSaida;
}
?>

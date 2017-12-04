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
    * Arquivo de Processamento para exportação do CAGED
    * Data de Criação: 18/04/2008

    * @author Diego Lemos de Souza

    * Casos de uso: uc-04.08.21

    $Id: PRExportarCAGED.php 30829 2008-07-07 19:59:54Z alex $
*/

set_time_limit(0);
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR                  );

Sessao::remove('filtroRelatorio');

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arLink = Sessao::read('link');
$stLink = "&pg=".Sessao::read("pg")."&pos=".Sessao::read("pos");

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarCAGED";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$inExercicio = $_POST["inAno"];
$stMes = ($_POST["inCodMes"] > 10) ? $_POST["inCodMes"] : "0".$_POST["inCodMes"];
$stCompetencia = $inExercicio."-".$stMes;

if ($_POST["stTipoEmissao"] == "movimento") {
    $stNomeArquivo = "CGED".$inExercicio.".M".$stMes;
} else {
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stFiltro = " WHERE to_char(dt_final,'yyyy-mm') = '".$inExercicio."-".$stMes."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaTodos($rsPeriodoMovimentacao,$stFiltro);
    $arDtFinal = explode("/",$rsPeriodoMovimentacao->getCampo("dt_final"));
    $inDia = $arDtFinal[0];
    $stNomeArquivo = "A".$inDia.$inExercicio.".M".$stMes;
}

Sessao::setTrataExcecao(true);

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCgm.class.php");
$obTIMACagedAutorizadoCgm = new TIMACagedAutorizadoCgm();
$obTIMACagedAutorizadoCgm->recuperaRelacionamento($rsAutorizacaoCGM);

include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
$obTAdministracaoUF = new TUF();

include_once(CAM_GRH_ENT_MAPEAMENTO."TEntidade.class.php");
$obTEntidade = new TEntidade();
$stFiltro = " AND entidade.cod_entidade = ".Sessao::getCodEntidade($boTransacao);
$stFiltro .= " AND entidade.exercicio = '".Sessao::getExercicio()."'";
$obTEntidade->recuperaInformacoesCGMEntidade($rsEntidade,$stFiltro);

$rsCGM = new recordset();
$rsUF  = new recordset();
include_once(CAM_GA_CGM_MAPEAMENTO."TCGM.class.php");
$obTCGM = new TCGM();
if ($rsAutorizacaoCGM->getNumLinhas() > 0) {
    $obTCGM->setDado("numcgm",$rsAutorizacaoCGM->getCampo("numcgm"));

} else {
    $obTCGM->setDado("numcgm",$rsEntidade->getCampo("numcgm"));
}
$obTCGM->recuperaPorChave($rsCGM);
$obTAdministracaoUF->setDado("cod_uf",$rsCGM->getCampo("cod_uf"));
$obTAdministracaoUF->recuperaPorChave($rsUF);

if (strlen($rsCGM->getCampo("fone_comercial")) > 8) {
    $inDDD = substr($rsCGM->getCampo("fone_comercial"),0,2);
    $inFone = substr($rsCGM->getCampo("fone_comercial"),2,strlen($rsCGM->getCampo("fone_comercial")));
} else {
    $inDDD = 0;
    $inFone = $rsCGM->getCampo("fone_comercial");
}

if (trim($inFone) == "") {
    if (strlen($rsCGM->getCampo("fone_residencial")) > 8) {
        $inDDD = substr($rsCGM->getCampo("fone_residencial"),0,2);
        $inFone = substr($rsCGM->getCampo("fone_residencial"),2,strlen($rsCGM->getCampo("fone_residencial")));
    } else {
        $inDDD = 0;
        $inFone = $rsCGM->getCampo("fone_residencial");
    }
}

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedAutorizadoCei.class.php");
$obTIMACagedAutorizadoCei = new TIMACagedAutorizadoCei();
$obTIMACagedAutorizadoCei->recuperaTodos($rsAutorizacaoCei);

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMACagedEstabelecimento.class.php");
$obTIMACagedEstabelecimento = new TIMACagedEstabelecimento();
$obTIMACagedEstabelecimento->recuperaTodos($rsCagedEstabelecimento);
$inTipoIdentificador = ($rsCagedEstabelecimento->getNumLinhas() > 0) ? 2 : 1;
$inNumIdentEstabelecimento = ($rsCagedEstabelecimento->getNumLinhas() > 0) ? $rsCagedEstabelecimento->getCampo("num_cei") : $rsEntidade->getCampo("cnpj");

$inNumeroSequencial = 1;
$inCodAtributo = 0;
$boArray = 0;
switch ($_POST["stTipoFiltro"]) {
    case "contrato_todos":
    case "cgm_contrato_todos":
        $stCodigos = "";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stCodigos .= $arContrato["cod_contrato"].",";
        }
        $stCodigos = substr($stCodigos,0,strlen($stCodigos)-1);
        break;
    case "lotacao":
        $stCodigos = implode(",",$_POST["inCodLotacaoSelecionados"]);
        break;
    case "local":
        $stCodigos = implode(",",$_POST["inCodLocalSelecionados"]);
        break;
    case "atributo_servidor":
        $inCodAtributo  = $_POST["inCodAtributo"];
        $inCodCadastro  = $_POST["inCodCadastro"];
        $stNomeAtributo = "Atributo_".$inCodAtributo."_".$inCodCadastro;

        if (is_array($_POST[$stNomeAtributo."_Selecionados"])) {
            $arAtributos = $_POST["Atributo_".$inCodAtributo."_".$inCodCadastro."_Selecionados"];
            $stCodigos = implode(",",$arAtributos);
            $boArray = 1;
        } else {
            $stCodigos = $_POST[$stNomeAtributo];
        }
        break;
}

include_once(CAM_GRH_IMA_MAPEAMENTO."TIMAConfiguracaoCaged.class.php");
$obTIMAConfiguracaoCaged = new TIMAConfiguracaoCaged();
$obTIMAConfiguracaoCaged->setDado("sequencia",$inNumeroSequencial+2);
$obTIMAConfiguracaoCaged->setDado("competencia",$stCompetencia);
$obTIMAConfiguracaoCaged->setDado("tipo_filtro",$_POST["stTipoFiltro"]);
$obTIMAConfiguracaoCaged->setDado("codigos",$stCodigos);
$obTIMAConfiguracaoCaged->setDado("cod_atributo",$inCodAtributo);
$obTIMAConfiguracaoCaged->setDado("bo_array",$boArray);
$obTIMAConfiguracaoCaged->recuperaCaged($rsRegistroCX);

$inTotalMovimentacoes = ($rsRegistroCX->getNumLinhas() > 0) ? $rsRegistroCX->getNumLinhas() : 0;
if ($_POST["stTipoEmissao"] == "movimento") {
    $rsRegistroCX->setCampo("tipo_registro","C",true);
} else {
    $rsRegistroCX->setCampo("tipo_registro","X",true);
}
$rsRegistroCX->setCampo("tipo_identificador",$inTipoIdentificador,true);
$rsRegistroCX->setCampo("num_ident_estabelecimento",$inNumIdentEstabelecimento,true);

$obTIMAConfiguracaoCaged->recuperaRelacionamento($rsConfiguracaoCaged);
$inClasseCnae = substr(preg_replace("/[^0-9 ]/i","",$rsConfiguracaoCaged->getCampo("cod_estrutural")),0,5);
$inSubClasseCnae = substr(preg_replace("/[^0-9 ]/i","",$rsConfiguracaoCaged->getCampo("cod_estrutural")),5,2);

//Registro A (Autorizado)
$arRegistroA[0]["tipo_registro"]                = "A";
$arRegistroA[0]["meio_fisico"]                  = 2;
$arRegistroA[0]["autorizacao"]                  = $rsAutorizacaoCGM->getCampo("num_autorizacao");
$arRegistroA[0]["competencia"]                  = ($_POST["inCodMes"] > 10) ? $_POST["inCodMes"].$_POST["inAno"] : "0".$_POST["inCodMes"].$_POST["inAno"];
$arRegistroA[0]["alteracao"]                    = ($_POST["boAtualizarDados"]) ? 2 : 1;
$arRegistroA[0]["sequencia"]                    = $inNumeroSequencial;
$arRegistroA[0]["tipo_identificador"]           = ($rsAutorizacaoCei->getNumLinhas() > 0) ? 2 : 1;
$arRegistroA[0]["numero_identificador"]         = ($rsAutorizacaoCei->getNumLinhas() > 0) ? $rsAutorizacaoCei->getCampo("num_cei") : ($rsAutorizacaoCGM->getCampo("cnpj")) ? $rsAutorizacaoCGM->getCampo("cnpj") : $rsEntidade->getCampo("cnpj");
$arRegistroA[0]["nome_social"]                  = $rsCGM->getCampo("nom_cgm");
$arRegistroA[0]["endereco"]                     = $rsCGM->getCampo("tipo_logradouro")." ".$rsCGM->getCampo("logradouro")." ".$rsCGM->getCampo("numero")." ".$rsCGM->getCampo("complemento");
$arRegistroA[0]["cep"]                          = $rsCGM->getCampo("cep");
$arRegistroA[0]["uf"]                           = $rsUF->getCampo("sigla_uf");
$arRegistroA[0]["ddd"]                          = $inDDD;
$arRegistroA[0]["telefone"]                     = $inFone;
$arRegistroA[0]["ramal"]                        = $rsCGM->getCampo("ramal_comercial");
$arRegistroA[0]["total_estabelecimentos"]       = 1;
$arRegistroA[0]["total_movimentacoes"]          = $inTotalMovimentacoes;
$arRegistroA[0]["filler"]                       = "";

$rsRegistroA = new RecordSet();
$rsRegistroA->preenche($arRegistroA);

$obExportador = new Exportador();
//$obExportador->setRetorno($pgForm);
$obExportador->addArquivo($stNomeArquivo);
$obExportador->roUltimoArquivo->setTipoDocumento("CAGED");

$obExportador->roUltimoArquivo->addBloco($rsRegistroA);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("meio_fisico");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("autorizacao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("competencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("alteracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_identificador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero_identificador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_social");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(35);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ddd");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("telefone");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("ramal");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_estabelecimentos");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_movimentacoes");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filler");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);

//Registro B (Estabelecimento)
$inNumeroSequencial++;
$obTAdministracaoUF->setDado("cod_uf",$rsEntidade->getCampo("cod_uf"));
$obTAdministracaoUF->recuperaPorChave($rsUF);

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorNomeacaoPosse.class.php");
$obTPessoalContratoServidorNomeacaoPosse = new TPessoalContratoServidorNomeacaoPosse();
$obTPessoalContratoServidorNomeacaoPosse->setDado("competencia",$stCompetencia);
$stFiltro = " AND to_char(dt_admissao,'yyyy-mm-dd') < '".$stCompetencia."-01'";
$obTPessoalContratoServidorNomeacaoPosse->recuperaTotalContratosCaged($rsTotalContratos,$stFiltro);
Sessao::write("inTotalPrimeiroDia", $rsTotalContratos->getCampo("contador"));

$arRegistroB[0]["tipo_registro"]                = "B";
$arRegistroB[0]["tipo_identificador"]           = $inTipoIdentificador;
$arRegistroB[0]["num_ident_estabelecimento"]    = $inNumIdentEstabelecimento;
$arRegistroB[0]["sequencia"]                    = $inNumeroSequencial;
$arRegistroB[0]["primeira_declaracao"]          = $rsConfiguracaoCaged->getCampo("tipo_declaracao");
$arRegistroB[0]["alteracao"]                    = ($_POST["boAtualizarDados"]) ? 2 : 1;
$arRegistroB[0]["cep"]                          = $rsEntidade->getCampo("cep");
$arRegistroB[0]["classe_cnae"]                  = $inClasseCnae;
$arRegistroB[0]["nome_estabelecimento"]          = $rsEntidade->getCampo("nom_cgm");
$arRegistroB[0]["endereco"]                     = $rsEntidade->getCampo("tipo_logradouro")." ".$rsEntidade->getCampo("logradouro")." ".$rsEntidade->getCampo("numero")." ".$rsEntidade->getCampo("complemento");
$arRegistroB[0]["bairro"]                       = $rsEntidade->getCampo("bairro");
$arRegistroB[0]["uf"]                           = $rsUF->getCampo("sigla_uf");
$arRegistroB[0]["total_empregados"]             = $rsTotalContratos->getCampo("contador");
$arRegistroB[0]["micro_empresa"]                = 2;
$arRegistroB[0]["subclasse_cnae"]               = $inSubClasseCnae;
$arRegistroB[0]["filler"]                       = "";

$rsRegistroB = new RecordSet();
$rsRegistroB->preenche($arRegistroB);

$obExportador->roUltimoArquivo->addBloco($rsRegistroB);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_identificador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_ident_estabelecimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("primeira_declaracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("alteracao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("classe_cnae");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nome_estabelecimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("endereco");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("bairro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(20);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_empregados");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("micro_empresa");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sub_classe_cnae");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filler");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);

Sessao::write("inContratoPrazoDeterminado", 0);
Sessao::write("inPrimeiroEmprego", 0);
Sessao::write("inReemprego", 0);
Sessao::write("inReintegracao", 0);
Sessao::write("inTranferenciaEntrada", 0);
Sessao::write("inAposentado", 0);
Sessao::write("inDispensaPedido", 0);
Sessao::write("inDispensaJustaCausa", 0);
Sessao::write("inDispensaSemJustaCausa", 0);
Sessao::write("inFimContrato", 0);
Sessao::write("inMorte", 0);
Sessao::write("inTerminoContrato", 0);
Sessao::write("inTranferenciaSaida", 0);
Sessao::write("inTotalDesligamentosMes", 0);
Sessao::write("inTotalMovimentacoes", 0);

while (!$rsRegistroCX->eof()) {

    //Acerto Carteira Trabalho Tipo Novo (> 4 digitos)
    $inNumSerieCTPS = ltrim($rsRegistroCX->getCampo("serie"), "0");
    if (strlen($inNumSerieCTPS) > 4) {
        $inNumComplementarCTPS = substr($inNumSerieCTPS, -1);
        $inNumSerieCTPS = substr($inNumSerieCTPS, 0, strlen($inNumSerieCTPS)-1);

        $rsRegistroCX->setCampo("serie", $inNumSerieCTPS);
        $rsRegistroCX->setCampo("sigla_uf", $inNumComplementarCTPS);
    }

    switch ($rsRegistroCX->getCampo("tipo_movimento")) {
        case 31:
            Sessao::write("inDispensaSemJustaCausa", Sessao::read("inDispensaSemJustaCausa")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 32:
            Sessao::write("inDispensaJustaCausa", Sessao::read("inDispensaJustaCausa")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 40:
            Sessao::write("inDispensaPedido", Sessao::read("inDispensaPedido")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 43:
            Sessao::write("inFimContrato", Sessao::read("inFimContrato")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 45:
            Sessao::write("inTerminoContrato", Sessao::read("inTerminoContrato")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 50:
            Sessao::write("inAposentado", Sessao::read("inAposentado")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 60:
            Sessao::write("inMorte", Sessao::read("inMorte")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 80:
            Sessao::write("inTranferenciaSaida", Sessao::read("inTranferenciaSaida")+1);
            Sessao::write("inTotalDesligamentosMes", Sessao::read("inTotalDesligamentosMes")+1);
            break;
        case 10:
            Sessao::write("inPrimeiroEmprego", Sessao::read("inPrimeiroEmprego")+1);
            Sessao::write("inTotalMovimentacoes", Sessao::read("inTotalMovimentacoes")+1);
            break;
        case 20:
            Sessao::write("inReemprego", Sessao::read("inReemprego")+1);
            Sessao::write("inTotalMovimentacoes", Sessao::read("inTotalMovimentacoes")+1);
            break;
        case 25:
            Sessao::write("inContratoPrazoDeterminado", Sessao::read("inContratoPrazoDeterminado")+1);
            Sessao::write("inTotalMovimentacoes", Sessao::read("inTotalMovimentacoes")+1);
            break;
        case 35:
            Sessao::write("inReintegracao", Sessao::read("inReintegracao")+1);
            Sessao::write("inTotalMovimentacoes", Sessao::read("inTotalMovimentacoes")+1);
            break;
        case 70:
            Sessao::write("inTranferenciaEntrada", Sessao::read("inTranferenciaEntrada")+1);
            Sessao::write("inTotalMovimentacoes", Sessao::read("inTotalMovimentacoes")+1);
            break;
    }
    $rsRegistroCX->proximo();
}
$rsRegistroCX->setPrimeiroElemento();

$obExportador->roUltimoArquivo->addBloco($rsRegistroCX);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_registro");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_identificador");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_ident_estabelecimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(14);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sequencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("servidor_pis_pasep");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_nascimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_escolaridade");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filler");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("horas_semanais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dt_admissao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_movimento");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("dia_rescisao");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nom_cgm");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(40);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("numero");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(8);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("serie");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(4);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filler");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_rais");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("portador_deficiencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cbo");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(6);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("aprendiz");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ZEROS_ESQ");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sigla_uf");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(2);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_deficiencia");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
$obExportador->roUltimoArquivo->roUltimoBloco->addColuna("filler");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_DIR");
$obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);

$obExportador->show();
Sessao::encerraExcecao();
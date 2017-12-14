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
    * Página de Processamento do Exportação TCMBA
    * Data de Criação: 18/04/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30829 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-29 16:52:46 -0300 (Qua, 29 Ago 2007) $

    * Casos de uso: uc-04.08.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CLA_EXPORTADOR                  );

$stAcao = $_REQUEST["stAcao"] ? $_REQUEST["stAcao"] : $_GET["stAcao"];
$arSessaoLink = Sessao::read('link');
$stLink = "&pg=".$arSessaoLink["pg"]."&pos=".$arSessaoLink["pos"];

foreach ($_POST as $key=>$value) {
    $stLink .= $key."=".$value."&";
}

//Define o nome dos arquivos PHP
$stPrograma = "ExportarTCMBA";
$pgForm = "FM".$stPrograma.".php?".Sessao::getId()."&stAcao=$stAcao$stLink";
$pgFilt = "FL".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::setTrataExcecao(true);

include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$obTFolhaPagamentoPeriodoMovimentacao->setDado("mes",$_POST["inCodMes"]);
$obTFolhaPagamentoPeriodoMovimentacao->setDado("ano",$_POST["inAno"]);
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacaoDaCompetencia($rsPeriodoMovimentacao);

$arCompetencia = explode("-",$rsPeriodoMovimentacao->getCampo("dt_final"));
$boDezembro = ( $arCompetencia[1] == 12 ) ? true : false;

$inMesFinal =( $_POST["inCodMes"]<10 ) ? "0".$_POST["inCodMes"]:$_POST["inCodMes"];
$dtCompetenciaFinal = $inMesFinal."/".$_POST["inAno"];

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalServidor.class.php");
$obTPessoalServidor = new TPessoalServidor();
$stFiltroServidor = processarFiltro($obTPessoalServidor);

$obTPessoalServidor->recuperaServidoresExportaTCMBA( $rsServidores, $stFiltroServidor," nom_cgm" );

$inIndex = 0 ;

$arHeaderArquivo = array();
$arFileName = array();

$sunFile = "00001";
$obExportador = new Exportador();

include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBA.class.php"                                   );
$obTIMAExportacaoTCMBA = new TIMAExportacaoTCMBA;
$obTIMAExportacaoTCMBA->recuperaTodos($rsExportacaoTCMBA);

$inCodEntidade = $rsExportacaoTCMBA->getCampo("cod_entidade");
$inNumEntidade = $rsExportacaoTCMBA->getCampo("num_entidade");

##Traz a Configuração do TCMBA
include_once ( CAM_GRH_IMA_MAPEAMENTO."TIMAExportacaoTCMBASubDivisao.class.php"                         );
$obTIMAExportacaoTCMBASubDivisao = new TIMAExportacaoTCMBASubDivisao;
$obTIMAExportacaoTCMBASubDivisao->recuperaTodos( $rsExportacaoTCMBASubDivisao );

while ( !$rsExportacaoTCMBASubDivisao->eof() ) {
    switch ($rsExportacaoTCMBASubDivisao->getCampo("cod_tipo_servidor")) {
        case 1:
            $inIndex = count($arEfetivo) + 1;
            $arEfetivo[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
            break;
        case 2:
            $inIndex = count($arCeletista) + 1;
            $arCeletista[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
            break;
        case 3:
            $inIndex = count($arCargoComissao) + 1;
            $arCargoComissao[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
            break;
        case 4:
            $inIndex = count($arAposentado) + 1;
            $arAposentado[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao") ;
            break;
        case 5:
            $inIndex = count($arTrabalhadorTemporario) + 1;
            $arTrabalhadorTemporario[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
            break;
        case 6:
            $inIndex = count($arAgentePolitico) + 1;
            $arAgentePolitico[$inIndex] = $rsExportacaoTCMBASubDivisao->getCampo("cod_sub_divisao");
            break;
    }
    $rsExportacaoTCMBASubDivisao->proximo();
}
$inTotalServidores = 0;
$inTotalAposentado = 0;
$inTotalCeletista = 0;
$inTotalComissao = 0;
$inTotalEfetivo = 0;
$inTotalEventos = 0;
$inTotalPolitico = 0;
$inTotalTemporario = 0;

$inTotalAdmissaoEfetivo         = 0;
$inTotalDemissaoEfetivo         = 0;
$inTotalAdmissaoCeletista       = 0;
$inTotalDemissaoCeletista       = 0;
$inTotalAdmissaoCargoComissao   = 0;
$inTotalDemissaoCargoComissao   = 0;
$inTotalAdmissaoAposentado      = 0;
$inTotalDemissaoAposentado      = 0;
$inTotalAdmissaoEfetivo         = 0;
$inTotalDemissaoEfetivo         = 0;
$inTotalAdmissaoTemporario      = 0;
$inTotalDemissaoTemporario      = 0;
$inTotalAdmissaoPolitico        = 0;
$inTotalDemissaoPolitico        = 0;

$inIndex =0 ;
$stFileName = "";

$inNumOrdem = 1;
while (!$rsServidores->eof()) {
    $boContinuar =false;

    ##Consultar os eventos calculados
    include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php");
    $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
    $stFiltroEventoCalculado  = " AND registro_evento_periodo.cod_contrato = ".$rsServidores->getCampo("cod_contrato")." \n";
    $stFiltroEventoCalculado .= " AND (natureza = 'D' OR natureza = 'P') \n";
    $stFiltroEventoCalculado .= " AND registro_evento_periodo.cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
    $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventoCalculados, $stFiltroEventoCalculado);

    if ($rsEventoCalculados->getNumLinhas() > 0) {
        if (in_array($rsServidores->getCampo("cod_sub_divisao"), $arEfetivo)) {
            $inTipoServidor = 1;
            $inTotalEfetivo++;
            $boContinuar = true;
            if ($rsServidores->getCampo("dt_admissao_competencia") == $dtCompetenciaFinal) {
                $inTotalAdmissaoEfetivo++;
            }
            if ($rsServidores->getCampo("dt_rescisao") == $dtCompetenciaFinal) {
                $inTotalDemissaoEfetivo++;
            }
        }

        if (in_array($rsServidores->getCampo("cod_sub_divisao"), $arCeletista)) {
            $inTipoServidor = 2;
            $inTotalCeletista++;
            $boContinuar = true;
            if ($rsServidores->getCampo("dt_admissao_competencia") == $dtCompetenciaFinal) {
                $inTotalAdmissaoCeletista++;
            }
            if ($rsServidores->getCampo("dt_rescisao") == $dtCompetenciaFinal) {
                $inTotalDemissaoCeletista++;
            }
        }

        if (in_array($rsServidores->getCampo("cod_sub_divisao"), $arCargoComissao)) {
            $inTipoServidor = 3;
            $inTotalComissao++;
            $boContinuar = true;
            if ($rsServidores->getCampo("dt_admissao_competencia") == $dtCompetenciaFinal) {
                $inTotalAdmissaoCargoComissao++;
            }
            if ($rsServidores->getCampo("dt_rescisao") == $dtCompetenciaFinal) {
                $inTotalDemissaoCargoComissao++;
            }
        }

        if (in_array($rsServidores->getCampo("cod_sub_divisao"), $arTrabalhadorTemporario)) {
            $inTipoServidor = 5;
            $inTotalTemporario++;
            $boContinuar = true;
            if ($rsServidores->getCampo("dt_admissao_competencia") == $dtCompetenciaFinal) {
                $inTotalAdmissaoTemporario++;
            }
            if ($rsServidores->getCampo("dt_rescisao") == $dtCompetenciaFinal) {
                $inTotalDemissaoTemporario++;
            }
        }
        if (in_array($rsServidores->getCampo("cod_sub_divisao"), $arAgentePolitico)) {
            $inTipoServidor = 6;
            $inTotalPolitico++;
            $boContinuar = true;
            if ($rsServidores->getCampo("dt_admissao_competencia") == $dtCompetenciaFinal) {
                $inTotalAdmissaoPolitico++;
            }
            if ($rsServidores->getCampo("dt_rescisao") == $dtCompetenciaFinal) {
                $inTotalDemissaoPolitico++;
            }
        }
        include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAposentadoria.class.php");
        $obTPessoalAposentadoria = new TPessoalAposentadoria();
        $stFiltroAposentadoria = " AND aposentadoria.cod_contrato = ".$rsServidores->getCampo("cod_contrato");
        $obTPessoalAposentadoria->recuperaRelacionamento($rsAposentadoria,$stFiltroAposentadoria);
        if ($rsAposentadoria->getNumLinhas() == 1 and $boContinuar) {
            $inTipoServidor = 4;
            $inTotalAposentado++;
        }
    }

    if ($boContinuar == true and $rsEventoCalculados->getNumLinhas() > 0) {
        $inTotalServidores++;
        $arHeaderArquivo[$inIndex]["num_ordem"] = str_pad($inNumOrdem,5,"0",STR_PAD_LEFT);
        $inNumOrdem++;
        $arHeaderArquivo[$inIndex]["cod_entidade"] = $inCodEntidade;
        $arHeaderArquivo[$inIndex]["num_entidade"] = $inNumEntidade;

        $stCompetencia  = ( $_REQUEST['inCodMes'] >= 10 )? $_REQUEST['inCodMes'] : "0".$_REQUEST['inCodMes'];
        $stCompetencia .= "/".$_REQUEST['inAno'];

        $arHeaderArquivo[$inIndex]["referencia"] = $stCompetencia;
        $arHeaderArquivo[$inIndex]["tipo_envio"] = $_REQUEST["inTipoEnvio"];
        $arHeaderArquivo[$inIndex]["matricula"] = $rsServidores->getCampo("registro");
        $arHeaderArquivo[$inIndex]["nomcgm"] = $rsServidores->getCampo("nom_cgm");
        $arHeaderArquivo[$inIndex]["cpf"] = $rsServidores->getCampo("cpf");

        switch (strtoupper($rsServidores->getCampo("sexo"))) {
            case "M" :
                $sexoEvento = 1;
                break;
            case "F" :
                $sexoEvento = 2;
                break;
        }

        $arHeaderArquivo[$inIndex]["sexo"] = $sexoEvento;
        $arHeaderArquivo[$inIndex]["nascimento"] = $rsServidores->getCampo("dt_nascimento");
        $arHeaderArquivo[$inIndex]["admissao"] = $rsServidores->getCampo("dt_admissao");
        $arHeaderArquivo[$inIndex]["tipo_servidor"] = $inTipoServidor;
        $arHeaderArquivo[$inIndex]["cod_funcao"] = $rsServidores->getCampo("cod_cargo");
        $arHeaderArquivo[$inIndex]["cod_orgao"] = $rsServidores->getCampo("cod_orgao");
        $arHeaderArquivo[$inIndex]["salario_base"] = str_replace(".","",($rsServidores->getCampo("salario")));

        $inDebito   = 0;
        $inProvento = 0;

        while (!$rsEventoCalculados->eof()) {
            if ($rsEventoCalculados->getCampo("natureza")=="D") {
                $inDebito = $inDebito + $rsEventoCalculados->getCampo("valor") ;
            } else {
                $inProvento = $inProvento + $rsEventoCalculados->getCampo("valor") ;
            }
            $rsEventoCalculados->proximo();
        }

        $inTotalEventos = ($inProvento - $inDebito);
        $arHeaderArquivo[$inIndex]["total_vencimentos"] = str_replace(".", "",($inTotalEventos));

        if (strtoupper($rsServidores->getCampo("norma"))!="NÃO INFORMADO") {
            $arHeaderArquivo[$inIndex]["observacoes"] = $rsServidores->getCampo("num_norma");
        } else {
            $arHeaderArquivo[$inIndex]["observacoes"] = " ";
        }

        $inIndex++;

        if ($inIndex == 7220) { //7220
            $arHeaderArquivo[$inIndex]["num_ordem"]         = "";
            $arHeaderArquivo[$inIndex]["cod_entidade"]      = "";
            $arHeaderArquivo[$inIndex]["num_entidade"]      = "";
            $arHeaderArquivo[$inIndex]["referencia"]        = "";
            $arHeaderArquivo[$inIndex]["tipo_envio"]        = "";
            $arHeaderArquivo[$inIndex]["matricula"]         = "";
            $arHeaderArquivo[$inIndex]["nomcgm"]            = "";
            $arHeaderArquivo[$inIndex]["cpf"]               = "";
            $arHeaderArquivo[$inIndex]["sexo"]              = "";
            $arHeaderArquivo[$inIndex]["nascimento"]        = "";
            $arHeaderArquivo[$inIndex]["admissao"]          = "";
            $arHeaderArquivo[$inIndex]["tipo_servidor"]     = "";
            $arHeaderArquivo[$inIndex]["cod_funcao"]        = "";
            $arHeaderArquivo[$inIndex]["cod_orgao"]         = "";
            $arHeaderArquivo[$inIndex]["salario_base"]      = "";
            $arHeaderArquivo[$inIndex]["total_vencimentos"] = "";
            $arHeaderArquivo[$inIndex]["observacoes"]       = "CONTINUA";

            $stFileName = "FMF".$sunFile.".txt";
            $arFileName[$sunFile] = $stFileName;

            $arFiles[$sunFile] = $arHeaderArquivo;
            $arHeaderArquivo = null;

            $inIndex = 0;
            $sunFile++;
            $sunFile = str_pad($sunFile, 5, "0", STR_PAD_LEFT);
        }
    }
    $rsServidores->proximo();
}

$stFileName = "FMF".$sunFile.".txt";
$arFileName[$sunFile] = $stFileName;

$arFiles[$sunFile] = $arHeaderArquivo;

$arHeaderArquivo = null;

$inIndex2 = 1;
foreach ($arFileName as $stFileName) {
    $inIndex2 = str_pad($inIndex2, 5, "0", STR_PAD_LEFT);
    $rsHeaderArquivo = "ar".$inIndex2;
    $$rsHeaderArquivo = new RecordSet();
    $$rsHeaderArquivo->preenche($arFiles[$inIndex2]);

    $obExportador->addArquivo($stFileName);
    $obExportador->roUltimoArquivo->setTipoDocumento("TCMBA");
    $obExportador->roUltimoArquivo->addBloco($$rsHeaderArquivo);

    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_ordem");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(5);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("num_entidade");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(3);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("referencia");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(7);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_envio");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("matricula");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nomcgm");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(60);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cpf");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("sexo");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("nascimento");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("admissao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("tipo_servidor");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(1);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_funcao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("cod_orgao");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(10);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("salario_base");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("total_vencimentos");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("NUMERICO_ESPACOS_DIR");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(11);
    $obExportador->roUltimoArquivo->roUltimoBloco->addColuna("observacoes");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTipoDado("CARACTER_ESPACOS_ESQ");
    $obExportador->roUltimoArquivo->roUltimoBloco->roUltimaColuna->setTamanhoFixo(30);

    $inIndex2++;
}

Sessao::write( "inTotalServidores" , $inTotalServidores );
Sessao::write( "inTotalEfetivo"    , $inTotalEfetivo );
Sessao::write( "inTotalCeletista"  , $inTotalCeletista );
Sessao::write( "inTotalComissao"   , $inTotalComissao );
Sessao::write( "inTotalAposentado" , $inTotalAposentado );
Sessao::write( "inTotalEventos"    , $inTotalEventos );
Sessao::write( "inTotalPolitico"   , $inTotalPolitico );
Sessao::write( "inTotalTemporario" , $inTotalTemporario );

Sessao::write( "inTotalAdmissaoEfetivo"      , $inTotalAdmissaoEfetivo );
Sessao::write( "inTotalDemissaoEfetivo"      , $inTotalDemissaoEfetivo );
Sessao::write( "inTotalAdmissaoCeletista"    , $inTotalAdmissaoCeletista );
Sessao::write( "inTotalDemissaoCeletista"    , $inTotalDemissaoCeletista );
Sessao::write( "inTotalAdmissaoAposentado"   , $inTotalAdmissaoAposentado );
Sessao::write( "inTotalDemissaoAposentado"   , $inTotalDemissaoAposentado );
Sessao::write( "inTotalAdmissaoPolitico"     , $inTotalAdmissaoPolitico );
Sessao::write( "inTotalDemissaoPolitico"     , $inTotalDemissaoPolitico );
Sessao::write( "inTotalAdmissaoTemporario"   , $inTotalAdmissaoTemporario );
Sessao::write( "inTotalDemissaoTemporario"   , $inTotalDemissaoTemporario );
Sessao::write( "inTotalAdmissaoCargoComissao", $inTotalAdmissaoCargoComissao );
Sessao::write( "inTotalDemissaoCargoComissao", $inTotalDemissaoCargoComissao );

$obExportador->setNomeArquivoZip('Exporta.zip');
$obExportador->setRetorno($pgForm);

$obExportador->show();

Sessao::encerraExcecao();
SistemaLegado::LiberaFrames();

function separarDigito($stString)
{
    $inNumero = preg_replace( "/[^0-9a-zA-Z]/i","",$stString);
    $inDigito = $inNumero[strlen($inNumero)-1];
    $inNumero = substr($inNumero,0,strlen($inNumero)-1);

    return array($inNumero,$inDigito);
}

function removeAcentos($stCampo)
{
    $Acentos = "áàãââÁÀÃÂéêÉÊíÍóõôÓÔÕúüÚÜçÇ";
    $Traducao ="aaaaaAAAAeeeeiIoooOOOuuUUcC";
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
    $TempLog = preg_replace( "/[^0-9a-zA-Z ]/i","",$TempLog);

    return $TempLog;
}

function processarFiltro(&$obTMapeamento)
{
    switch ($_POST['stTipoFiltro']) {
        case "lotacao":
            foreach ($_POST['inCodLotacaoSelecionados'] as $inCodOrgao) {
                $stCodOrgao .= $inCodOrgao.",";
            }
            $stCodOrgao = substr($stCodOrgao,0,strlen($stCodOrgao)-1);
            $stFiltro = " AND contrato_servidor_orgao.cod_orgao in (".$stCodOrgao.")";
            break;
        case "local":
            foreach ($_POST['inCodLocalSelecionados'] as $inCodLocal) {
                $stCodLocal .= $inCodLocal.",";
            }
            $stCodLocal = substr($stCodLocal,0,strlen($stCodLocal)-1);
            $stJoin  =" INNER JOIN(SELECT contrato_servidor_local.*                                                                      \n";
            $stJoin .= "            FROM pessoal.contrato_servidor_local                                                                 \n";
            $stJoin .= "               , (SELECT cod_contrato                                                                            \n";
            $stJoin .= "                       , max(timestamp) as timestamp                                                             \n";
            $stJoin .= "                    FROM pessoal.contrato_servidor_local                                                         \n";
            $stJoin .= "                  GROUP BY cod_contrato) as max_contrato_servidor_local                                          \n";
            $stJoin .= "           WHERE pessoal.contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato         \n";
            $stJoin .= "             AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp) AS contrato_servidor_local \n";
            $stJoin .= "  ON contrato_servidor.cod_contrato = contrato_servidor_local.cod_contrato                              \n";
            $stJoin .= " AND contrato_servidor_local.cod_local IN (".$stCodLocal.")                                                 \n";
            $obTMapeamento->setDado("stJoin",$stJoin);
            break;
        case "atributos":
            include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php");
            include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoAtributoDinamico.class.php");
            $obTAdministracaoAtributoDinamico = new TAdministracaoAtributoDinamico();
            $obTAdministracaoAtributoDinamico->setDado("cod_atributo",$_POST["inCodAtributo"]);
            $obTAdministracaoAtributoDinamico->setDado("cod_cadastro",$_POST["inCodCadastro"]);
            $obTAdministracaoAtributoDinamico->setDado("cod_modulo",22);
            $obTAdministracaoAtributoDinamico->recuperaPorChave($rsAtributoDinamico);
            $stNomeCampo = "Atributo_".$_POST["inCodAtributo"]."_".$_POST["inCodCadastro"];
            $stJoin  = "     JOIN pessoal.atributo_contrato_servidor_valor                                      \n";
            $stJoin .= "       ON contrato_servidor.cod_contrato = atributo_contrato_servidor_valor.cod_contrato         \n";
            $stJoin .= "      AND atributo_contrato_servidor_valor.cod_atributo = ".$_POST["inCodAtributo"]."   \n";
            if ($rsAtributoDinamico->getCampo("cod_tipo") == 4) {
                $stValores = implode(",",$_POST[$stNomeCampo."_Selecionados"]);
                $stJoin .= "      AND atributo_contrato_servidor_valor.valor IN (".$stValores.")   \n";
            } else {
                $stJoin .= "      AND atributo_contrato_servidor_valor.valor = '".$_POST[$stNomeCampo]."'   \n";
            }
            $obTMapeamento->setDado("stJoin",$stJoin);
            break;
    }

    return $stFiltro;
}
?>

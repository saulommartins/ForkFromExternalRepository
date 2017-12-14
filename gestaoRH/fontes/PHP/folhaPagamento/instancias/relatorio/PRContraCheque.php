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
    * Página de Processamento do Contra Cheque
    * Data de Criação: 10/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Id: PRContraCheque.php 66258 2016-08-03 14:25:21Z evandro $

    * Casos de uso: uc-04.05.30
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once CAM_FW_PDF."RRelatorio.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoComplementarCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoDecimoCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoFeriasCalculado.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorSalario.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPrevidenciaEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFgtsEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoTabelaIrrfEvento.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoFaixaDescontoIrrf.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoGetDesdobramentoFolha.class.php";
include_once CAM_GRH_PES_MAPEAMENTO."TPessoalServidorDependente.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."FFolhaPagamentoContraCheque.class.php";
include_once CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoConfiguracaoContracheque.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";
include_once CAM_GA_CGM_MAPEAMENTO."TCGMCGM.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php";
include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoImpressora.class.php";
define       ('FPDF_FONTPATH','font/');

//Define o nome dos arquivos PHP
$stPrograma = "ContraCheque";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgJS       = "JS".$stPrograma.".js";

function processarLinha($stLinha,$stValor,$inTamanho)
{
    $stValor = removeAcentos($stValor);
    if ($inTamanho > 1) {
        if (strlen($stLinha) >= $inTamanho) {
            $stLinha = substr($stLinha,0,$inTamanho-1).$stValor;
        } else {
            $stLinha = str_pad($stLinha,$inTamanho-1," ").$stValor;
        }
    } else {
        $stLinha = $stValor;
    }

    return substr($stLinha,0,68);
}

function removeAcentos($stCampo)
{
    //Adicionando mapa de caracteres
    $stMapaCaracteres = array( 'á' => 'a','à' => 'a','ã' => 'a','â' => 'a'
                              ,'é' => 'e','ê' => 'e'
                              ,'í' => 'i'
                              ,'ó' => 'o','ô' => 'o','õ' => 'o'
                              ,'ú' => 'u','ü' => 'u'
                              ,'ç' => 'c'
                              ,'Á' => 'A','À' => 'A','Ã' => 'A','Â' => 'A'
                              ,'É' => 'E','Ê' => 'E'
                              ,'Í' => 'I'
                              ,'Ó' => 'O','Ô' => 'O','Õ' => 'O'
                              ,'Ú' => 'U','Ü' => 'U'
                              ,'Ç' => 'C'
                              ,'ñ' => 'n'
                              ,'Ñ' => 'N'
                            );

    //Buscando o tipo de dado que veio por parametro
    if ( is_array($stCampo) ) {
        $stTipoDado = "array";
    }elseif ( is_object($stCampo) ) {
        $stTipoDado = "objeto";
    }else{
        $stTipoDado = "string";
    }

    //De acordo com cara tipo realiza as funcoes certas
    switch ($stTipoDado) {
        case 'array':
            foreach ($stCampo as $key => $value) {
                $stCampo[$key] = strtr($value, $stMapaCaracteres);
            }
        break;

        case 'string':
            $stCampo = strtr($stCampo, $stMapaCaracteres);
        break;

        default:
            return $stCampo;
        break;
    }

    return $stCampo;
}

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
$inQuantEvento = 15;
SistemaLegado::BloqueiaFrames(true, false);
flush();

$arFiltro = Sessao::read('filtroRelatorio');

$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inCodMes = ( $arFiltro["inCodMes"] ) ? $arFiltro["inCodMes"] : $request->get('inCodMes');
$inAno    = ( $arFiltro["inAno"]    ) ? $arFiltro["inAno"]    : $request->get('inAno');
$inMes    = ( $inCodMes < 10        ) ? "0".$inCodMes         : $inCodMes;
$stCompetencia = $inMes."/".$inAno;
$stFiltro = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);

$stTipoFiltro             = ( $arFiltro["stTipoFiltro"]             ) ? $arFiltro["stTipoFiltro"]             : $request->get('stTipoFiltro');
$stOrdenacao              = ( $arFiltro["stOrdenacao"]              ) ? $arFiltro["stOrdenacao"]              : $request->get('stOrdenacao');
$stSituacao               = ( $arFiltro["stSituacao"]               ) ? $arFiltro["stSituacao"]               : $request->get('stSituacao');
$inCodLotacaoSelecionados = ( $arFiltro["inCodLotacaoSelecionados"] ) ? $arFiltro["inCodLotacaoSelecionados"] : $request->get('inCodLotacaoSelecionados');
$inCodLocalSelecionados   = ( $arFiltro["inCodLocalSelecionados"]   ) ? $arFiltro["inCodLocalSelecionados"]   : $request->get('inCodLocalSelecionados');
$boAgrupar                = ( $arFiltro["boAgrupar"]                ) ? $arFiltro["boAgrupar"]                : $request->get('boAgrupar');

switch ($stTipoFiltro) {
    case "contrato_todos":
    case "contrato":
    case "cgm_contrato_todos":
    case "cgm_contrato":    
        if ($stOrdenacao == "alfabetica") {
            $stOrdem = "nom_cgm";
        } else {
            $stOrdem = "registro";
        }
        $stFiltroContratos = " AND contrato.cod_contrato IN (";
        foreach (Sessao::read("arContratos") as $arContrato) {
            $stFiltroContratos .= $arContrato["cod_contrato"].",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "geral":
        if ($stOrdenacao == "alfabetica") {
            $stOrdem = "nom_cgm";
        } else {
            $stOrdem = "registro";
        }
    break;
    case "lotacao_grupo":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "orgao";
            $virgula = ", ";
        }
        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_lotacao,nom_cgm";
        } else {
            $stOrdem .= $virgula."orgao,registro";
        }
        $stFiltroContratos = " AND cadastros.cod_orgao IN (";
        foreach ($inCodLotacaoSelecionados as $inCodOrgao) {
            $stFiltroContratos .= $inCodOrgao.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
    case "local_grupo":
        $stOrdem = "";
        $virgula = "";

        if ($boAgrupar) {
            $stOrdem .= "local";
            $virgula = ", ";
        }
        if ($stOrdenacao == "alfabetica") {
            $stOrdem .= $virgula."descricao_local,nom_cgm";
        } else {
            $stOrdem .= $virgula."cod_local,registro";
        }
        $stFiltroContratos = " AND cadastros.cod_local IN (";
        foreach ($inCodLocalSelecionados as $inCodLocal) {
            $stFiltroContratos .= $inCodLocal.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
    break;
}

$boComplementar      = ( $arFiltro["boComplementar"]      ) ? $arFiltro["boComplementar"]      : $request->get('boComplementar');
$inCodConfiguracao   = ( $arFiltro["inCodConfiguracao"]   ) ? $arFiltro["inCodConfiguracao"]   : $request->get('inCodConfiguracao');
$inFolha             = ( $inCodConfiguracao != ""         ) ? $inCodConfiguracao               : 0;
$inCodComplementar   = ( $arFiltro["inCodComplementar"]   ) ? $arFiltro["inCodComplementar"]   : $request->get('inCodComplementar');
$stDesdobramento     = ( $arFiltro["stDesdobramento"]     ) ? $arFiltro["stDesdobramento"]     : $request->get('stDesdobramento');
$inContratoReemissao = ( $arFiltro["inContratoReemissao"] ) ? $arFiltro["inContratoReemissao"] : $request->get('inContratoReemissao');
$inCodComplementar   = ( $inCodComplementar               ) ? $inCodComplementar               : 0;
$stDesdobramento     = ( $stDesdobramento                 ) ? $stDesdobramento                 : "";
$inContratoReemissao = ( $inContratoReemissao             ) ? $inContratoReemissao             : 0;

if ( (isset($arFiltro["boDuplicar"]))?$arFiltro["boDuplicar"]:$request->get('boDuplicar') ) {
    $boDuplicar = 'true';
} else {
    $boDuplicar = 'false';
}

$obFFolhaPagamentoContraCheque = new FFolhaPagamentoContraCheque();
$obFFolhaPagamentoContraCheque->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
$obFFolhaPagamentoContraCheque->setDado("quant_evento",$inQuantEvento);
$obFFolhaPagamentoContraCheque->setDado("ordem",$stOrdem);
$obFFolhaPagamentoContraCheque->setDado("folha",(trim($inFolha)==""?0:$inFolha));
$obFFolhaPagamentoContraCheque->setDado("cod_complementar",$inCodComplementar);
$obFFolhaPagamentoContraCheque->setDado("filtro",$stFiltroContratos);
$obFFolhaPagamentoContraCheque->setDado("registro_reemissao",$inContratoReemissao);
if ($stDesdobramento != "") {
    $obFFolhaPagamentoContraCheque->setDado("desdobramento",$stDesdobramento);
}
$obFFolhaPagamentoContraCheque->setDado("duplicar",$boDuplicar);
$obFFolhaPagamentoContraCheque->setDado("situacao",$stSituacao);
$obFFolhaPagamentoContraCheque->contraCheque($rsContratos);

$obTFolhaPagamentoConfiguracaoContracheque = new TFolhaPagamentoConfiguracaoContracheque();
$obTFolhaPagamentoConfiguracaoContracheque->recuperaTodos($rsConfiguracao,"","linha,coluna");
$arConfiguracoes = $rsConfiguracao->getElementos();
$stLinha = "";
$stContraCheque = "";

$obTOrcamentoEntidade = new TOrcamentoEntidade();
$obTOrcamentoEntidade->setDado("cod_entidade",Sessao::getCodEntidade($boTransacao));
$obTOrcamentoEntidade->setDado("exercicio",Sessao::getExercicio());
$obTOrcamentoEntidade->recuperaRelacionamentoNomes($rsEntidade);

$obTCGMCGM = new TCGMCGM();
$stFiltro = " WHERE numcgm = ".$rsEntidade->getCampo("numcgm");
$obTCGMCGM->recuperaTodos($rsCgmEntidade,$stFiltro);

$obTUF = new TUF();
$obTUF->setDado("cod_uf",$rsCgmEntidade->getCampo("cod_uf"));
$obTUF->recuperaPorChave($rsUF);
$stNomeEntidade = $rsEntidade->getCampo("entidade");
SistemaLegado::removeAcentosSimbolos($stNomeEntidade);
$rsContratos->setCampo("nome_entidade",$stNomeEntidade,true);
$rsContratos->setCampo("estado_entidade",$rsUF->getCampo("nom_uf"),true);
$rsContratos->setCampo("competencia",$stCompetencia,true);

foreach ($arConfiguracoes as $value) {
    if ( $value['nom_campo'] == 'cnpj' ) {
        $stCNPJ = $rsEntidade->getCampo('cnpj_entidade');
        $stCNPJ = SistemaLegado::mask($stCNPJ,'##.###.###/####-##');        
        $rsContratos->setCampo("cnpj",$stCNPJ,true);
    }
}

switch ($inFolha) {
    case "1":
        $stTipoCalculo = "Salário";
    break;
    case "2":
        $stTipoCalculo = "Férias";
    break;
    case "3":
        $stTipoCalculo = "Décimo";
    break;
    case "4":
        $stTipoCalculo = "Rescisão";
    break;
    default:
        $stTipoCalculo = "Complementar";
        $inFolha = 0;
    break;
}
$rsContratos->setCampo("tipo_calculo",$stTipoCalculo,true);

$boMensagemAniversariante = ( $arFiltro["boMensagemAniversariante"] ) ? $arFiltro["boMensagemAniversariante"] : $request->get('boMensagemAniversariante');
$stMensagemAniversario    = ( $arFiltro["stMensagemAniversario"]    ) ? $arFiltro["stMensagemAniversario"]    : $request->get('stMensagemAniversario');
$stMensagem               = ( $arFiltro["stMensagem"]               ) ? $arFiltro["stMensagem"]               : $request->get('stMensagem');
if ( $boMensagemAniversariante and $rsContratos->getCampo("mes_aniversario") == $inMes ) {
    $stMensagem = $stMensagemAniversario;
}
$stMensagem = wordwrap($stMensagem,100,chr(13));
$arMensagem = explode(chr(13),$stMensagem);
$inLinhaMensagem = 0;
foreach ($arConfiguracoes as $arConfiguracao) {
    if ($arConfiguracao["nom_campo"] == "mensagem") {
        $inIndexMensagem = 0;
        $inLinhaMensagem  = $arConfiguracao["linha"];
        $inColunaMensagem = $arConfiguracao["coluna"];
        $stNomeCampo = $arConfiguracao["nom_campo"].$inIndexMensagem;
        $rsContratos->setCampo($stNomeCampo,trim($arMensagem[0]),true);
        $arConfiguracao = array("nom_campo"=>$stNomeCampo,
                                    "linha"=>$inLinhaMensagem,
                                   "coluna"=>$inColunaMensagem);
        array_shift($arMensagem);
    }
    if ($arConfiguracao["linha"] > $inLinhaMensagem and count($arMensagem) and isset($inColunaMensagem)) {
        do {
            $inIndexMensagem++;
            $inLinhaMensagem += 5;
            $stNomeCampo = "mensagem".$inIndexMensagem;
            $rsContratos->setCampo($stNomeCampo,trim($arMensagem[0]),true);
            $arTempMensagem = array("nom_campo"=>$stNomeCampo,
                                        "linha"=>$inLinhaMensagem,
                                       "coluna"=>$inColunaMensagem);
            array_shift($arMensagem);
            $arTemp[] = $arTempMensagem;
        } while ($inLinhaMensagem<$arConfiguracao["linha"]);
    }
    $arTemp[] = $arConfiguracao;
}
unset($arConfiguracoes);
$arConfiguracoes = $arTemp;

while (!$rsContratos->eof()) {
    $inLinha = "";
    foreach ($arConfiguracoes as $inIndexConfiguracao=>$arConfiguracao) {
        if ($arConfiguracao["linha"] != $inLinha and $inLinha != "") {
            $stContraCheque .= $stLinha;
            $inDiferenca = $arConfiguracao["linha"] - $inLinha;
            $inDivisor = $inDiferenca/5;
            for ($inIndex=1;$inIndex<=$inDivisor;$inIndex++) {
                $stContraCheque .= "\n";
            }
            $stLinha = "";
        }
        switch ($arConfiguracao["nom_campo"]) {
            case "desdobramento":
            case "desc_eventos":
            case "quantidades":
            case "proventos":
            case "descontos":
            break;
            case "eventos":
                $inDescEventos       = $arConfiguracoes[$inIndexConfiguracao+1]["coluna"];
                $inDescDesdobramento = $arConfiguracoes[$inIndexConfiguracao+2]["coluna"];
                $inQuantidade        = $arConfiguracoes[$inIndexConfiguracao+3]["coluna"];
                $inProventos         = $arConfiguracoes[$inIndexConfiguracao+4]["coluna"];
                $inDescontos         = $arConfiguracoes[$inIndexConfiguracao+5]["coluna"];
                $stFiltroEventos  = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroEventos .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroEventos .= " AND natureza IN ('P','D')";
                $stOrdem = "codigo LIMIT $inQuantEvento OFFSET ".$rsContratos->getCampo("inoffset");
                switch ($inFolha) {
                    case 0:
                        $obTFolhaPagamentoEventoComplementarCalculado = new TFolhaPagamentoEventoComplementarCalculado();
                        $stFiltroEventos .= " AND cod_complementar = ".$inCodComplementar;
                        $obTFolhaPagamentoEventoComplementarCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltroEventos,$stOrdem);
                    break;
                    case 1:
                        $obTFolhaPagamentoEventoCalculado = new TFolhaPagamentoEventoCalculado();
                        $obTFolhaPagamentoEventoCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltroEventos,$stOrdem);
                    break;
                    case 2:
                        if($stDesdobramento != "")
                            $stFiltroEventos .= " AND evento_ferias_calculado.desdobramento = '".$stDesdobramento."'";
                        $obTFolhaPagamentoEventoFeriasCalculado = new TFolhaPagamentoEventoFeriasCalculado();
                        $obTFolhaPagamentoEventoFeriasCalculado->recuperaEventosCalculados($rsEventosCalculados,$stFiltroEventos,$stOrdem);
                    break;
                    case 3:
                        if($stDesdobramento != "")
                            $stFiltroEventos .= " AND evento_decimo_calculado.desdobramento = '".$stDesdobramento."'";
                        $obTFolhaPagamentoEventoDecimoCalculado = new TFolhaPagamentoEventoDecimoCalculado();
                        $obTFolhaPagamentoEventoDecimoCalculado->recuperaEventosDecimoCalculado($rsEventosCalculados,$stFiltroEventos,$stOrdem);
                    break;
                    case 4:
                        if($stDesdobramento != "")
                            $stFiltroEventos .= " AND evento_rescisao_calculado.desdobramento = '".$stDesdobramento."'";
                        $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
                        $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventosCalculados,$stFiltroEventos,$stOrdem);
                    break;
                }
                $inCountEventos = 0;
                while (!$rsEventosCalculados->eof()) {
                    $stLinha = processarLinha($stLinha,$rsEventosCalculados->getCampo("codigo"),$arConfiguracao["coluna"]);
                    $stLinha = processarLinha($stLinha,$rsEventosCalculados->getCampo("nom_evento"),$inDescEventos);
                    $stLinha = processarLinha($stLinha,$rsEventosCalculados->getCampo("desdobramento_texto"), $inDescDesdobramento);
                    $stLinha = processarLinha($stLinha,str_pad(number_format($rsEventosCalculados->getCampo("quantidade"),2,',','.'),10," ",STR_PAD_LEFT),$inQuantidade,"LEFT");
                    if ($rsEventosCalculados->getCampo("natureza")=="D") {
                        $nuDescontos += $rsEventosCalculados->getCampo("valor");
                        $stLinha = processarLinha($stLinha,str_pad("0,00",10," ",STR_PAD_LEFT),$inProventos,"LEFT");
                        $stLinha = processarLinha($stLinha,str_pad(number_format($rsEventosCalculados->getCampo("valor"),2,',','.'),10," ",STR_PAD_LEFT),$inDescontos);
                    }
                    if ($rsEventosCalculados->getCampo("natureza")=="P") {
                        $nuProventos += $rsEventosCalculados->getCampo("valor");
                        $stLinha = processarLinha($stLinha,str_pad(number_format($rsEventosCalculados->getCampo("valor"),2,',','.'),10," ",STR_PAD_LEFT),$inProventos,"LEFT");
                        $stLinha = processarLinha($stLinha,str_pad("0,00",10," ",STR_PAD_LEFT),$inDescontos);
                    }
                    $stContraCheque .= $stLinha."\n";
                    $stLinha = "";
                    $inCountEventos++;
                    $rsEventosCalculados->proximo();
                }
                for ($inLinhaEventos=$inCountEventos;$inLinhaEventos<=14;$inLinhaEventos++) {
                    $stContraCheque .= "\n";
                }
                if ($rsContratos->getCampo("bocontinua") == "t") {
                    $stContraCheque .= processarLinha($inLinha,str_pad("Continua...",35," ",STR_PAD_LEFT),1);
                    $stContraCheque .= "\n";
                } else {
                    $stContraCheque .= "\n";
                    $rsContratos->setCampo("total_vencimentos",number_format($nuProventos,2,',','.'));
                    $rsContratos->setCampo("total_descontos",number_format($nuDescontos,2,',','.'));
                    $rsContratos->setCampo("liquido",number_format($nuProventos-$nuDescontos,2,',','.'));
                    $nuDescontos = 0;
                    $nuProventos = 0;
                }
                $inLinha = "";
            break;
            case "total_vencimentos":
            case "total_descontos":
            case "liquido":
                $inLinha = $arConfiguracao["linha"];
                $stValorCampo = str_pad($rsContratos->getCampo($arConfiguracao["nom_campo"]),10," ",STR_PAD_LEFT);
                $stLinha = processarLinha($stLinha,$stValorCampo,$arConfiguracao["coluna"]);
            break;
            case "recolhido_fgts":
                $inLinha = $arConfiguracao["linha"];
                $stFiltroFgts  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroFgts .= "   AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroFgts .= "   AND inFolha = ".$inFolha;
                $obTFolhaPagamentoFgtsEvento->setDado("cod_tipo",1);
                if($stDesdobramento != "")
                    $obTFolhaPagamentoFgtsEvento->setDado("desdobramento",$stDesdobramento);
                $obTFolhaPagamentoFgtsEvento->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoFgtsEvento->recuperaEventoCalculadoFgts($rsEventoFgts,$stFiltroFgts);

                $nuTotalEventoFgts = 0;
                while (!$rsEventoFgts->eof()) {
                    $nuTotalEventoFgts += $rsEventoFgts->getCampo("valor");
                    $rsEventoFgts->proximo();
                }

                if ($nuTotalEventoFgts != 0) {
                    $nuTotalEventoFgts = number_format($nuTotalEventoFgts, 2, ",", ".");
                }

                $stLinha = processarLinha($stLinha,$nuTotalEventoFgts,$arConfiguracao["coluna"]);
            break;
            case "dependentes":
                $inLinha = $arConfiguracao["linha"];
                $obTPessoalServidorDependente = new TPessoalServidorDependente();
                $stFiltroDependentes = " AND cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $obTPessoalServidorDependente->recuperaQuantDependentesServidor($rsDependentes,$stFiltroDependentes);
                $stLinha = processarLinha($stLinha,$rsDependentes->getCampo("contador"),$arConfiguracao["coluna"]);
            case "base_irrf":
                $inLinha = $arConfiguracao["linha"];
                $obTFolhaPagamentoTabelaIrrfEvento = new TFolhaPagamentoTabelaIrrfEvento();
                $stFiltroIrrf  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroIrrf .= "   AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroIrrf .= "   AND inFolha = ".$inFolha;
                $obTFolhaPagamentoTabelaIrrfEvento->setDado("cod_tipo",7);
                if($stDesdobramento != "")
                    $obTFolhaPagamentoTabelaIrrfEvento->setDado("desdobramento",$stDesdobramento);
                $obTFolhaPagamentoTabelaIrrfEvento->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoTabelaIrrfEvento->recuperaEventoCalculadoIrrf($rsEventoIrrf,$stFiltroIrrf);

                $nuTotalEventoIrrf = 0;
                while (!$rsEventoIrrf->eof()) {
                    $nuTotalEventoIrrf += $rsEventoIrrf->getCampo("valor");
                    $rsEventoIrrf->proximo();
                }

                if ($nuTotalEventoIrrf != 0) {
                    $nuTotalEventoIrrf = number_format($nuTotalEventoIrrf, 2, ",", ".");
                }

                $stLinha = processarLinha($stLinha,$nuTotalEventoIrrf,$arConfiguracao["coluna"]);
            break;
            case "base_fgts":
                $inLinha = $arConfiguracao["linha"];
                $obTFolhaPagamentoFgtsEvento = new TFolhaPagamentoFgtsEvento();
                $stFiltroFgts  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroFgts .= "   AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroFgts .= "   AND inFolha = ".$inFolha;
                $obTFolhaPagamentoFgtsEvento->setDado("cod_tipo",3);
                if($stDesdobramento != "")
                    $obTFolhaPagamentoFgtsEvento->setDado("desdobramento",$stDesdobramento);
                $obTFolhaPagamentoFgtsEvento->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoFgtsEvento->recuperaEventoCalculadoFgts($rsEventoFgts,$stFiltroFgts);

                $nuTotalEventoFgts = 0;
                while (!$rsEventoFgts->eof()) {
                    $nuTotalEventoFgts += $rsEventoFgts->getCampo("valor");
                    $rsEventoFgts->proximo();
                }

                if ($nuTotalEventoFgts != 0) {
                    $nuTotalEventoFgts = number_format($nuTotalEventoFgts, 2, ",", ".");
                }

                $stLinha = processarLinha($stLinha,$nuTotalEventoFgts,$arConfiguracao["coluna"]);
            break;
            case "base_inss":
                $inLinha = $arConfiguracao["linha"];
                $obTFolhaPagamentoPrevidenciaEvento = new TFolhaPagamentoPrevidenciaEvento();
                $stFiltroPrevidencia  = " WHERE cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $stFiltroPrevidencia .= "   AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
                $stFiltroPrevidencia .= "   AND inFolha = ".$inFolha;
                $obTFolhaPagamentoPrevidenciaEvento->setDado("cod_tipo",2);
                if($stDesdobramento != "")
                    $obTFolhaPagamentoPrevidenciaEvento->setDado("desdobramento",$stDesdobramento);
                $obTFolhaPagamentoPrevidenciaEvento->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoPrevidenciaEvento->recuperaEventoCalculadoPrevidencia($rsEventoPrevidencia,$stFiltroPrevidencia);

                $nuTotalEventoPrevidencia = 0;
                while (!$rsEventoPrevidencia->eof()) {
                    $nuTotalEventoPrevidencia += $rsEventoPrevidencia->getCampo("valor");
                    $rsEventoPrevidencia->proximo();
                }

                if ($nuTotalEventoPrevidencia != 0) {
                    $nuTotalEventoPrevidencia = number_format($nuTotalEventoPrevidencia, 2, ",", ".");
                }

                $stLinha = processarLinha($stLinha,$nuTotalEventoPrevidencia,$arConfiguracao["coluna"]);
            break;
            case "salario_base":
                $inLinha = $arConfiguracao["linha"];
                $obTPessoalContratoServidorSalario = new TPessoalContratoServidorSalario();
                $stFiltroSalario = " AND salario.cod_contrato = ".$rsContratos->getCampo("cod_contrato");
                $obTPessoalContratoServidorSalario->recuperaRelacionamento($rsSalario,$stFiltroSalario);
                $stLinha = processarLinha($stLinha,number_format($rsSalario->getCampo("salario"),2,",","."),$arConfiguracao["coluna"]);
            break;
            case "faixa_irrf":
                $inLinha = $arConfiguracao["linha"];
                $obTFolhaPagamentoFaixaDescontoIrrf = new TFolhaPagamentoFaixaDescontoIrrf();
                $obTFolhaPagamentoFaixaDescontoIrrf->setDado("cod_tipo",7);
                $obTFolhaPagamentoFaixaDescontoIrrf->setDado("cod_contrato",$rsContratos->getCampo("cod_contrato"));
                $obTFolhaPagamentoFaixaDescontoIrrf->setDado("cod_periodo_movimentacao",$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
                $obTFolhaPagamentoFaixaDescontoIrrf->setDado("inFolha",$inFolha);
                if($stDesdobramento != "")
                    $obTFolhaPagamentoFaixaDescontoIrrf->setDado("desdobramento",$stDesdobramento);
                $obTFolhaPagamentoFaixaDescontoIrrf->setDado("cod_complementar",$inCodComplementar);
                $obTFolhaPagamentoFaixaDescontoIrrf->recuperaFaixaDescontoIrrf($rsFaixaDesconto);
                $stAliquota = ( $rsFaixaDesconto->getNumLinhas() == 1 ) ? $rsFaixaDesconto->getCampo("aliquota")."%" : "";
                $stLinha = processarLinha($stLinha,$stAliquota,$arConfiguracao["coluna"]);
            break;
            case "dt_admissao":
                $inLinha = $arConfiguracao["linha"];
                $stValorCampo = "Admissão: ".$rsContratos->getCampo('dt_admissao');                
                $stLinha = processarLinha($stLinha,$stValorCampo,$arConfiguracao["coluna"]);
            break;
            case "dt_posse":
                $inLinha = $arConfiguracao["linha"];
                $stValorCampo = "Posse: ".$rsContratos->getCampo('dt_posse');
                $stLinha = processarLinha($stLinha,$stValorCampo,$arConfiguracao["coluna"]);
            break;
            default:
                $inLinha = $arConfiguracao["linha"];
                $stValorCampo = $rsContratos->getCampo($arConfiguracao["nom_campo"]);
                $stLinha = processarLinha($stLinha,$stValorCampo,$arConfiguracao["coluna"]);
            break;
        }
    }
    $stContraCheque .= $stLinha;
    $stContraCheque .= "\n\n\n\n\n";
    $rsContratos->proximo();
}

$stArquivo = "/tmp/contraCheque.txt";

$impressao = fopen($stArquivo,"w");
fwrite($impressao,$stContraCheque);
$inCodImpressora = SistemaLegado::pegaConfiguracao("impressora_contracheque",27,Sessao::getExercicio());

$obTAdministracaoImpressora = new TAdministracaoImpressora();
$obTAdministracaoImpressora->setDado("cod_impressora",$inCodImpressora);
$obTAdministracaoImpressora->recuperaPorChave($rsImpressora);
system("lpr -P ".$rsImpressora->getCampo("fila_impressao")." ".$stArquivo);
fclose($impressao);
unlink($stArquivo);
$stMensagem = "O contracheque foi gerado e será impresso na impressora matricial configurada.";
sistemaLegado::alertaAviso($pgFilt,$stMensagem,"incluir","aviso", Sessao::getId(), "../");
?>

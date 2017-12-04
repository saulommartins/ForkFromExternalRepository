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
    * Página de Filtro do Emitir Termo Rescisao
    * Data de Criação: 25/01/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 30952 $
    $Name$
    $Author: souzadl $
    $Date: 2008-02-27 13:36:12 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-04.05.39
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoMunicipio.class.php");
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoUF.class.php");
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php"                            );
include_once( CAM_GT_CEM_MAPEAMENTO."TCEMCnaeFiscal.class.php" );
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoEventoRescisaoCalculado.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalPensao.class.php");
include_once(CAM_GA_CGM_MAPEAMENTO."TCGMPessoaJuridica.class.php");
include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalAdidoCedido.class.php");
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                     );
define       ('FPDF_FONTPATH','font/');

$arFiltro = Sessao::read('filtroRelatorio');
include_once(CAM_GRH_FOL_MAPEAMENTO."TFolhaPagamentoPeriodoMovimentacao.class.php");
$obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
$inMes = ( $arFiltro["inCodMes"] < 10 ) ? "0".$arFiltro["inCodMes"] : $arFiltro["inCodMes"];
$stCompetencia = $inMes."/".$arFiltro["inAno"];
$stFiltro = " AND to_char(FPM.dt_final,'mm/yyyy') = '".$stCompetencia."'";
$obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltro);

switch ($arFiltro["stTipoFiltro"]) {
    case "contrato_rescisao":
    case "cgm_contrato_rescisao":
        if ($arFiltro["stOrdenacao"] == "alfabetica") {
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
        if ($arFiltro["stOrdenacao"] == "alfabetica") {
            $stOrdem = "nom_cgm";
        } else {
            $stOrdem = "registro";
        }
        break;
    case "lotacao":
        if ($arFiltro["stOrdenacao"] == "alfabetica") {
            $stOrdem = "desc_orgao,nom_cgm";
        } else {
            $stOrdem = "orgao,registro";
        }
        $stFiltroContratos = " AND contrato_servidor_orgao.cod_orgao IN (";
        foreach ($arFiltro["inCodLotacaoSelecionados"] as $inCodOrgao) {
            $stFiltroContratos .= $inCodOrgao.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        break;
    case "local":
        if ($arFiltro["stOrdenacao"] == "alfabetica") {
            $stOrdem = "desc_local,nom_cgm";
        } else {
            $stOrdem = "cod_local,registro";
        }
        $stFiltroContratos = " AND contrato_servidor_local.cod_local IN (";
        foreach ($arFiltro["inCodLocalSelecionados"] as $inCodLocal) {
            $stFiltroContratos .= $inCodLocal.",";
        }
        $stFiltroContratos = substr($stFiltroContratos,0,strlen($stFiltroContratos)-1).")";
        break;
}

function defineLayoutTermoRescisao(&$obPDF)
{
    //Retangulo
    $obPDF->Rect( 7, 8, 196, 10);
    //Retangulo Identificação do Empregador
    $obPDF->Rect( 7, 19, 196, 24);
    //Retangulo Identificação do Trabalhado
    $obPDF->Rect( 7, 44, 196, 32);
    //Retangulo Dados do Contrato
    $obPDF->Rect( 7, 77, 196, 25);
    //Retangulo Discriminação das verbas rescisórias
    $obPDF->Rect( 7, 103, 196, 119);
    //Retangulo 6
    $obPDF->Rect( 7, 223, 196, 60);

    //Linhas Horizontal
    //Identificação do Empregador
    $obPDF->Line( 17, 27, 203, 27 );
    $obPDF->Line( 17, 35, 203, 35 );

    //Identificação do Trabalhado
    $obPDF->Line( 17, 52, 203, 52 );
    $obPDF->Line( 17, 60, 203, 60 );
    $obPDF->Line( 17, 68, 203, 68 );

    //Dados do Contrato
    $obPDF->Line( 17, 85, 203, 85 );
	$obPDF->Line( 17, 93, 203, 93 );

    //Discriminação das verbas rescisórias
    $obPDF->Line( 17, 111, 203, 111 );
    $obPDF->Line( 17, 159, 203, 159 );
    $obPDF->Line( 17, 167, 203, 167 );
    $obPDF->Line( 17, 206, 203, 206 );
    $obPDF->Line( 17, 214, 203, 214 );

    //Formalização da rescisão
    $obPDF->Line( 17, 232, 203, 232 );
    $obPDF->Line( 17, 240, 203, 240 );
    $obPDF->Line( 17, 275, 203, 275 );

    //Linhas Verticais
    //Identificação do Empregador
    $obPDF->Line( 17 , 19 , 17 , 43 );
    $obPDF->Line( 77 , 19 , 77 , 27 );
    $obPDF->Line( 157 , 27 , 157 , 35 );
    $obPDF->Line( 97 , 35 , 97 , 43 );
    $obPDF->Line( 107 , 35 , 107 , 43 );
    $obPDF->Line( 130 , 35 , 130 , 43 );
    $obPDF->Line( 157 , 35 , 157 , 43 );

    //Identificação do Trabalhado
    $obPDF->Line( 17 , 44 , 17 , 76 );
    $obPDF->Line( 77 , 44 , 77 , 52 );
    $obPDF->Line( 157 , 52 , 157 , 60 );
    $obPDF->Line( 97 , 60 , 97 , 68 );
    $obPDF->Line( 107 , 60 , 107 , 68 );
    $obPDF->Line( 137 , 60 , 137 , 68 );
    $obPDF->Line( 67 , 68 , 67 , 76 );
    $obPDF->Line( 100 , 68 , 100 , 76 );

    //Dados do Contrato
    $obPDF->Line( 17 , 77 , 17 , 102 );
    $obPDF->Line( 83 , 77 , 83 , 85 );
    $obPDF->Line( 123 , 77 , 123 , 85 );
    $obPDF->Line( 163 , 77 , 163 , 85 );
    $obPDF->Line( 100 , 85 , 100 , 93 );
    $obPDF->Line( 136 , 85 , 136 , 93 );
    $obPDF->Line( 169 , 85 , 169 , 93 );

    //Discriminação das verbas rescisórias
    $obPDF->Line( 17 , 103 , 17 , 222 );
    $obPDF->Line( 107 , 103 , 107 , 222 );

    //Formalização da rescisão
    $obPDF->Line( 17 , 223 , 17 , 283 );
    $obPDF->Line( 107 , 223 , 107 , 283 );
    $obPDF->Line( 154 , 240 , 154 , 275 );

}

function TextWithDirection(&$obPDF,$x,$y,$txt,$direction='R')
{
    $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));
    if ($direction=='R')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$obPDF->k,($obPDF->h-$y)*$obPDF->k,$txt);
    elseif ($direction=='L')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$obPDF->k,($obPDF->h-$y)*$obPDF->k,$txt);
    elseif ($direction=='U')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$obPDF->k,($obPDF->h-$y)*$obPDF->k,$txt);
    elseif ($direction=='D')
        $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$obPDF->k,($obPDF->h-$y)*$obPDF->k,$txt);
    else
        $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$obPDF->k,($obPDF->h-$y)*$obPDF->k,$txt);
    if ($obPDF->ColorFlag)
        $s='q '.$obPDF->TextColor.' '.$s.' Q';
    $obPDF->_out($s);
}

function preencheTermoRescisao(&$obPDF,$arDados,$arEventos,$boPageBreak)
{
    GLOBAL $rsPeriodoMovimentacao,$stCompetencia;
    //    addLogotipo($obPDF,$inIncremento);
    $obPDF->setFont('Arial','B',14);     // fonte do primeiro titulo
    $obPDF->Text   ( 43, 15, "TERMO DE RESCISÃO DO CONTRATO DE TRABALHO" );
    $obPDF->setFont('Arial','',7);
    //Identificação do Empregador
    TextWithDirection($obPDF,10,41,utf8_decode('IDENTIFICAÇÃO'),'U');
    TextWithDirection($obPDF,13,34,'DO','U');
    TextWithDirection($obPDF,16,40,'EMPREGADOR','U');

    $obPDF->Text   ( 18, 22, "01 CNPJ/CEI" );
    $obPDF->Text   ( 78, 22, "02 Razão Social/Nome" );
    $obPDF->Text   ( 18, 30, "03 Endereço (logradouro, n°, andar, apartamento)" );
    $obPDF->Text   ( 158,30, "04 Bairro" );
    $obPDF->Text   ( 18, 38, "05 Município" );
    $obPDF->Text   ( 98, 38, "06 UF" );
    $obPDF->Text   ( 108,38, "07 CEP" );
    $obPDF->Text   ( 131,38, "08 CNAE" );
    $obPDF->Text   ( 158,38, "09 CNPJ/CEI Tomador/Obra" );

    //Identificação do Trabalhado
    TextWithDirection($obPDF,11,71,utf8_decode('IDENTIFICAÇÃO DO'),'U');
    TextWithDirection($obPDF,14,69,'TRABALHADOR','U');
    $obPDF->Text   ( 18, 47, "10 PIS - PASEP" );
    $obPDF->Text   ( 78, 47, "11 Nome" );
    $obPDF->Text   ( 18, 55, "12 Endereço (logradouro, n°, andar, apartamento)" );
    $obPDF->Text   ( 158,55, "13 Bairro" );
    $obPDF->Text   ( 18, 63, "14 Município" );
    $obPDF->Text   ( 98, 63, "15 UF" );
    $obPDF->Text   ( 108,63, "16 CEP" );
    $obPDF->Text   ( 138,63, "17 Carteira de Trabalho (n°, série, UF)" );
    $obPDF->Text   ( 18, 71, "18 CPF" );
    $obPDF->Text   ( 68, 71, "19 Data de Nascimento" );
    $obPDF->Text   ( 101,71, "20 Nome da Mãe" );

    //Dados do Contrato
    TextWithDirection($obPDF,11,99,'DADOS DO','U');
    TextWithDirection($obPDF,14,100,'CONTRATO','U');
    $obPDF->Text   ( 18, 80, "21 Remuneração p/fins rescisórios" );
    $obPDF->Text   ( 84, 80, "22 Data de admissão" );
    $obPDF->Text   ( 124,80, "23 Data do aviso prévio" );
    $obPDF->Text   ( 164,80, "24 Data de afastamento" );
    $obPDF->Text   ( 18,88, "25 Causa do afastamento" );
    $obPDF->Text   ( 101,88,"26 Cód. afastamento" );
    $obPDF->Text   ( 137,88,"27 Pensão alimentícia (%)" );
    $obPDF->Text   ( 170,88,"28 Categoria do trabalhador" );
	$obPDF->Text   ( 18,96,"Lotação" );

    //Discriminação das verbas rescisórias
    TextWithDirection($obPDF,13,185,utf8_decode('DISCRIMINAÇÃO DAS VERBAS RESCISÓRIAS'),'U');
    $obPDF->setFont('Arial','',10);
    $obPDF->Text   ( 40,108,"VENCIMENTOS (29 A 45)" );
    $obPDF->Text   ( 134,108,"DESCONTOS (47 A 53)" );
    $obPDF->Text   ( 55,164,"BASES" );
    $obPDF->Text   ( 140,164,"INFORMATIVOS" );
    if (!$boPageBreak) {
        $obPDF->Text   ( 18,211,"46 TOTAL BRUTO" );
        $obPDF->Text   ( 108,211,"54 TOTAL DAS DEDUÇÕES" );
        $obPDF->Text   ( 108,219,"55 LÍQUIDO A RECEBER" );
    }

    $obPDF->setFont('Arial','',7);
    //Formalização da rescissão
    TextWithDirection($obPDF,13,271,utf8_decode('FORMALIZAÇÃO DA RESCISÃO'),'U');
    $obPDF->Text   ( 18,226,"56 Local e data do recebimento" );
    $obPDF->Text   ( 108,226,"57 Carimbo e assinatura do empregador ou preposto" );
    $obPDF->Text   ( 18,235,"58 Assinatura do trabalhador" );
    $obPDF->Text   ( 108,235,"59 Assinatura do responsável legal do trabalhador" );
    $obPDF->Text   ( 18,243,"60 HOMOLOGAÇÃO" );
    $obPDF->Text   ( 108,243,"61 Digital do trabalhador" );
    $obPDF->Text   ( 155,243,"62 Digital do responsável legal" );
    $obPDF->Text   ( 18,246,"Foi prestada, gratuitamente, assistência ao trabalhador, nos termos do art." );
    $obPDF->Text   ( 18,249,"477, parágrafo 1°, da Consolidação das Leis do Trabalho - CLT, sendo" );
    $obPDF->Text   ( 18,252,"comprovado, neste ato, o efetivo pagamento das verbas rescisórias acima");
    $obPDF->Text   ( 18,255,"especificadas.");
    $obPDF->Text   ( 18,262,"_______________________________________");
    $obPDF->Text   ( 18,265,"Local e data");
    $obPDF->Text   ( 18,271,"_______________________________________");
    $obPDF->Text   ( 18,274,"Carimbo e assinatura do assistente");
    $obPDF->Text   ( 18,278,"63 Identificação do orgão homologador" );
    $obPDF->Text   ( 108,278,"64 Recepção pelo banco (data e carimbo)" );

    //Identificação do Empregador
    include_once( CAM_FW_PDF."RRelatorio.class.php"                                                         );
    $obRRelatorio           = new RRelatorio;
    $obRRelatorio->setExercicio  ( Sessao::getExercicio() );
    $obRRelatorio->setCodigoEntidade( Sessao::getCodEntidade($boTransacao) );
    $obRRelatorio->setExercicioEntidade( Sessao::getExercicio() );
    $obRRelatorio->recuperaCabecalho( $arConfiguracao );
    $obPDF->setFont('Arial','',10);
    $stCnpj = $arConfiguracao["cnpj"];
    $obPDF->Text   ( 18, 26, $stCnpj );
    $obPDF->Text   ( 78, 26, $arConfiguracao["nom_prefeitura"] );
    $stEndereco  = $arConfiguracao["logradouro"].", ";
    $stEndereco .= $arConfiguracao["numero"];
    $obPDF->Text   ( 18, 34, $stEndereco );
    $obPDF->Text   ( 158, 34, $arConfiguracao["bairro"] );
    $obPDF->Text   ( 18, 42, $arConfiguracao["nom_municipio"] );
    $obPDF->Text   ( 98, 42, $arConfiguracao["sigla_uf"] );
    $stCEP = $arConfiguracao["cep"];
    $obPDF->Text   ( 108, 42, $stCEP);

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->setDado("cod_modulo",40);
    $obTAdministracaoConfiguracao->setDado("exercicio",Sessao::getExercicio());
    $obTAdministracaoConfiguracao->setDado("parametro","cnae_fiscal".Sessao::getEntidade());
    $obTAdministracaoConfiguracao->recuperaPorChave($rsCnaeFiscal);

    $rsCnaeFiscal = new RecordSet();
    if ($rsCnaeFiscal->getNumLinhas() == 1) {
        $obTCEMCnaeFiscal = new TCEMCnaeFiscal;
        $stFiltro = " WHERE cod_cnae = ".$rsCnaeFiscal->getCampo("valor");
        $obTCEMCnaeFiscal->recuperaCnaeAtivo( $rsCnaeFiscal,$stFiltro );
    }
    $obPDF->Text   ( 131, 42, $rsCnaeFiscal->getCampo("valor_composto"));
    $obTPessoalAdidoCedido = new TPessoalAdidoCedido();
    $stFiltroAdido  = " AND adido_cedido.cod_contrato = ".$arDados["cod_contrato"];
    $stFiltroAdido .= " AND (to_char(adido_cedido.dt_inicial,'mm/yyyy') = '".$stCompetencia."'";
    $stFiltroAdido .= "  OR  to_char(adido_cedido.dt_final,'mm/yyyy') = '".$stCompetencia."')";
    $obTPessoalAdidoCedido->recuperaRelacionamento($rsAdidoCedido,$stFiltroAdido);
    if ( $rsAdidoCedido->getNumLinhas() == 1 ) {
        $obTCGMPessoaJuridica = new TCGMPessoaJuridica();
        $obTCGMPessoaJuridica->setDado("numcgm",$rsAdidoCedido->getCampo("cgm_cedente_cessionario"));
        $obTCGMPessoaJuridica->recuperaPorChave($rsCGMPessoaJuridica);
        $stCnpj = $rsCGMPessoaJuridica->getCampo("cnpj");
        $stCnpj = substr($stCnpj,0,2).".".substr($stCnpj,2,3).".".substr($stCnpj,5,3)."/".substr($stCnpj,8,4)."-".substr($stCnpj,12,2);
        $obPDF->Text   ( 158, 42, $stCnpj);
    }

    //Identificação do trabalhador
    $inIncremento = 4;
    $obPDF->Text   ( 18, 47+$inIncremento, $arDados["servidor_pis_pasep"] );
    $obPDF->Text   ( 78, 47+$inIncremento, $arDados["registro"]."-".$arDados["nom_cgm"] );
    $obPDF->Text   ( 18, 55+$inIncremento, $arDados["endereco"] );
    $obPDF->Text   ( 158,55+$inIncremento, $arDados["bairro"] );
    $obPDF->Text   ( 18, 63+$inIncremento, $arDados["nom_municipio"] );
    $obPDF->Text   ( 98, 63+$inIncremento, $arDados["sigla_uf"] );
    $obPDF->Text   ( 108,63+$inIncremento, $arDados["cep"] );
    $obPDF->Text   ( 138,63+$inIncremento, $arDados["ctps"] );
    $obPDF->Text   ( 18, 71+$inIncremento, $arDados["cpf"] );
    $obPDF->Text   ( 68, 71+$inIncremento, $arDados["dt_nascimento"] );
    $obPDF->Text   ( 101,71+$inIncremento, $arDados["nome_mae"] );

    //Dados do contrato
    $obPDF->Text   ( 18, 80+$inIncremento, number_format($arDados["salario"],2,',','.') );
    $obPDF->Text   ( 84, 80+$inIncremento, $arDados["dt_admissao"] );
    $obPDF->Text   ( 124,80+$inIncremento, $arDados["dt_aviso"] );
    $obPDF->Text   ( 164,80+$inIncremento, $arDados["dt_rescisao"] );
    $obPDF->Text   ( 18,88+$inIncremento,  $arDados["num_causa"]."-".$arDados["descricao"]);
    $obPDF->Text   ( 101,88+$inIncremento, $arDados["num_sefip"] );
	$obPDF->Text   ( 18,96+$inIncremento, $arDados["orgao"]."-".$arDados["desc_orgao"] );
			
    $obTPessoalPensao = new TPessoalPensao();

    $rsPensao = new RecordSet();
    $stFiltroPensao = " WHERE cod_servidor = ".$arDados["cod_servidor"];
    $obTPessoalPensao->recuperaRelacionamento($rsPensao,$stFiltroPensao);
    $arPensao = $rsPensao->getSomaCampo("percentual");

    $obPDF->Text   ( 137,88+$inIncremento, $arPensao["percentual"] );
    $obPDF->Text   ( 170,88+$inIncremento, $arDados["cod_categoria"] );

    //Discriminação das verbas rescisórias
    $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();
    $stFiltro  = " AND cod_contrato = ".$arDados["cod_contrato"];
    $stFiltro .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsEventosCalculados,$stFiltro,$stOrdem = "desdobramento_texto, descricao  ");
    $inEvento = 0;
    $obPDF->setFont('Arial','',7);
    $nuTotalProventos = 0;

    foreach ($arEventos["P"] as $arEventosProvento) {

        switch ($arEventosProvento["desdobramento"]) {
            case "S":
                $stDesdobramento = "Saldo Salário";
                break;
            case "A":
                $stDesdobramento = "Aviso Prévio Inden.";
                break;
            case "V":
                $stDesdobramento = "Férias Vencidas";
                break;
            case "P":
                $stDesdobramento = "Férias Proporc.";
                break;
            case "D":
                $stDesdobramento = "13° Salário";
                break;
        }

        $obPDF->Text   ( 18, 115+$inEvento, substr($arEventosProvento["descricao"], 0, 25) );
        $obPDF->Text   ( 58, 115+$inEvento, $stDesdobramento );
        $obPDF->SetXY(74,112+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosProvento["quantidade"],2,',','.'),0,'R',0);
        $obPDF->SetXY(86,112+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosProvento["valor"],2,',','.'),0,'R',0);
        $nuTotalProventos += $arEventosProvento["valor"];
        $inEvento += 3;
    }

    $inEvento = 0;
    $obPDF->setFont('Arial','',7);
    $nuTotalDescontos = 0;
    foreach ($arEventos["D"] as $arEventosDesconto) {
        switch ($arEventosDesconto["desdobramento"]) {
            case "S":
                $stDesdobramento = "Saldo Salário";
                break;
            case "A":
                $stDesdobramento = "Aviso Prévio Indenizado";
                break;
            case "V":
                $stDesdobramento = "Férias Vencidas";
                break;
            case "P":
                $stDesdobramento = "Férias Proporc.";
                break;
            case "D":
                $stDesdobramento = "13° Salário";
                break;
        }

        $obPDF->Text   ( 18+90, 115+$inEvento, substr($arEventosDesconto["descricao"], 0, 20) );
        $obPDF->Text   ( 60+90, 115+$inEvento, $stDesdobramento );
        $obPDF->SetXY(80+90,112+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosDesconto["quantidade"],2,',','.'),0,'R',0);
        $obPDF->SetXY(92+90,112+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosDesconto["valor"],2,',','.'),0,'R',0);
        $nuTotalDescontos += $arEventosDesconto["valor"];
        $inEvento += 3;
    }

    $inEvento = 0;
    $obPDF->setFont('Arial','',7);
    foreach ($arEventos["B"] as $arEventosBase) {
        switch ($arEventosBase["desdobramento"]) {
            case "S":
                $stDesdobramento = "Saldo Salário";
                break;
            case "A":
                $stDesdobramento = "Aviso Prévio Indenizado";
                break;
            case "V":
                $stDesdobramento = "Férias Vencidas";
                break;
            case "P":
                $stDesdobramento = "Férias Proporc.";
                break;
            case "D":
                $stDesdobramento = "13° Salário";
                break;
        }

        $obPDF->Text   ( 18, 171+$inEvento, substr($arEventosBase["descricao"], 0, 30) );
        $obPDF->Text   ( 58, 171+$inEvento, $stDesdobramento );
        $obPDF->SetXY(74,168+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosBase["quantidade"],2,',','.'),0,'R',0);
        $obPDF->SetXY(86,168+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosBase["valor"],2,',','.'),0,'R',0);
        $inEvento += 3;
    }

    $inEvento = 0;
    $obPDF->setFont('Arial','',7);
    foreach ($arEventos["I"] as $arEventosInformativo) {
        switch ($arEventosInformativo["desdobramento"]) {
            case "S":
                $stDesdobramento = "Saldo Salário";
                break;
            case "A":
                $stDesdobramento = "Aviso Prévio Indenizado";
                break;
            case "V":
                $stDesdobramento = "Férias Vencidas";
                break;
            case "P":
                $stDesdobramento = "Férias Proporc.";
                break;
            case "D":
                $stDesdobramento = "13° Salário";
                break;
        }

        $obPDF->Text   ( 18+90, 171+$inEvento, substr($arEventosInformativo["descricao"], 0, 30) );
        $obPDF->Text   ( 60+90, 171+$inEvento, $stDesdobramento );
        $obPDF->SetXY(80+90,168+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosInformativo["quantidade"],2,',','.'),0,'R',0);
        $obPDF->SetXY(92+90,168+$inEvento);
        $obPDF->Cell(20,4,number_format($arEventosInformativo["valor"],2,',','.'),0,'R',0);
        $inEvento += 3;
    }

    if (!$boPageBreak) {
        $nuTotalProventos += Sessao::read("nuTotalProventos");
        $nuTotalDescontos += Sessao::read("nuTotalDescontos");
        $obPDF->SetXY(86,208);
        $obPDF->Cell(20,4,number_format($nuTotalProventos,2,',','.'),0,'R',0);
        $obPDF->SetXY(92+90,208);
        $obPDF->Cell(20,4,number_format($nuTotalDescontos,2,',','.'),0,'R',0);
        $obPDF->SetXY(92+90,216);
        $obPDF->Cell(20,4,number_format($nuTotalProventos-$nuTotalDescontos,2,',','.'),0,'R',0);
        Sessao::remove("nuTotalProventos");
        Sessao::remove("nuTotalDescontos");
    } else {
        if (Sessao::read("nuTotalProventos") > 0) {
            Sessao::write("nuTotalProventos",$nuTotalProventos+Sessao::read("nuTotalProventos"));
        } else {
            Sessao::write("nuTotalProventos",$nuTotalProventos);
        }
        if (Sessao::read("nuTotalDescontos") > 0) {
            Sessao::write("nuTotalDescontos",$nuTotalDescontos);
        } else {
            Sessao::write("nuTotalDescontos",$nuTotalDescontos+Sessao::read("nuTotalDescontos"));
        }
    }
}

$fpdf =new FPDF();
$fpdf->open();
$fpdf->setTextColor(0);
$fpdf->addPage();
$fpdf->setLeftMargin(0);
$fpdf->setTopMargin(0);
$fpdf->SetLineWidth(0.03);

$fpdf->SetFont('Arial','',10);

if (strlen(trim($stFiltroContratos)) > 0) {
    $stFiltroContratos = substr_replace($stFiltroContratos, ' WHERE ', 0, 4);
}

if (sessao::read('incluirRescisaoContratoPensionista') != null) {
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoPensionistaCasoCausa.class.php");
    $obTPessoalContratoPensionistaCasoCausa = new TPessoalContratoPensionistaCasoCausa();
    $obTPessoalContratoPensionistaCasoCausa->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTPessoalContratoPensionistaCasoCausa->recuperaTermoRescisao($rsContratos,$stFiltroContratos,$stOrdem);
} else {
    include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalContratoServidorCasoCausa.class.php");
    $obTPessoalContratoServidorCasoCausa = new TPessoalContratoServidorCasoCausa();
    $obTPessoalContratoServidorCasoCausa->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao"));
    $obTPessoalContratoServidorCasoCausa->recuperaTermoRescisao($rsContratos,$stFiltroContratos,$stOrdem);
}

$arContratos = $rsContratos->getElementos();

$boAddPage = false;
foreach ($arContratos as $inIndex=>$arContrato) {
    $obTFolhaPagamentoEventoRescisaoCalculado = new TFolhaPagamentoEventoRescisaoCalculado();

    $stOrdemEvento = " desdobramento_texto, descricao  ";

    $stFiltroEvento  = " AND cod_contrato = ".$arContrato["cod_contrato"];
    $stFiltroEvento .= " AND cod_periodo_movimentacao = ".$rsPeriodoMovimentacao->getCampo("cod_periodo_movimentacao");
    $stFiltroEventoN = " AND natureza = 'P'";
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsProventosCalculados,$stFiltroEvento.$stFiltroEventoN,$stOrdemEvento);
    $stFiltroEventoN = " AND natureza = 'D'";
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsDescontosCalculados,$stFiltroEvento.$stFiltroEventoN,$stOrdemEvento);
    $stFiltroEventoN = " AND natureza = 'B'";
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsBasesCalculados,$stFiltroEvento.$stFiltroEventoN,$stOrdemEvento);
    $stFiltroEventoN = " AND natureza = 'I'";
    $obTFolhaPagamentoEventoRescisaoCalculado->recuperaEventoRescisaoCalculado($rsInformativosCalculados,$stFiltroEvento.$stFiltroEventoN,$stOrdemEvento);

    $inDivisor = 15;
    $inContador = $rsProventosCalculados->getNumLinhas();
    $inContador = ( $rsDescontosCalculados->getNumLinhas()    > $inContador ) ? $rsDescontosCalculados->getNumLinhas()    : $inContador;
    $inContador = ( $rsBasesCalculados->getNumLinhas()        > $inContador ) ? $rsBasesCalculados->getNumLinhas()        : $inContador;
    $inContador = ( $rsInformativosCalculados->getNumLinhas() > $inContador ) ? $rsInformativosCalculados->getNumLinhas() : $inContador;

    $inResto    = $inContador%$inDivisor;
    $inContador = $inContador/$inDivisor;
    $inContador = ( $inResto == 0 ) ? $inContador : $inContador + 1;
    $inContador = (int) $inContador;

    $arProventosCalculados      = $rsProventosCalculados->getElementos();
    $arDescontosCalculados      = $rsDescontosCalculados->getElementos();
    $arBasesCalculados          = $rsBasesCalculados->getElementos();
    $arInformativosCalculados   = $rsInformativosCalculados->getElementos();

    $inOffset = 0;
    if ($inContador > 0) {
        if ($boAddPage) {
            $fpdf->addPage();
        }
        for ($inPag=1;$inPag<=$inContador;$inPag++) {
            $arEventos['P'] = array_slice($arProventosCalculados,$inOffset,$inDivisor);
            $arEventos['D'] = array_slice($arDescontosCalculados,$inOffset,$inDivisor);
            $arEventos['B'] = array_slice($arBasesCalculados,$inOffset,$inDivisor);
            $arEventos['I'] = array_slice($arInformativosCalculados,$inOffset,$inDivisor);
            if ($inPag < $inContador) {
                $boPageBreak = true;
            } else {
                $boPageBreak = false;
            }
            defineLayoutTermoRescisao($fpdf);
            preencheTermoRescisao($fpdf,$arContrato,$arEventos,$boPageBreak);
            $fpdf->Text   ( 8, 285, "Página ".$inPag." de ".$inContador );
            if ($boPageBreak) {
                $fpdf->Text   ( 190, 285, "Continua..." );
                $fpdf->addPage();
            }
            $inOffset += $inDivisor;
        }
        $boAddPage = true;
    }
}

Sessao::write("obRelatorio",$fpdf);

$obRelatorio = new RRelatorio();
$obRelatorio->executaFrameOculto("OCGeraTermoRescisao.php");

//$fpdf->output('termoRescisao.pdf','D');

?>

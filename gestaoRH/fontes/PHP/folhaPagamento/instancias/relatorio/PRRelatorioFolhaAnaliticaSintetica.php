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
* Página relatório de Folha Analítica/Sintética
* Data de Criação   : 23/03/2006

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @ignore

$Revision: 30952 $
$Name$
$Author: alex $
$Date: 2008-03-05 11:37:33 -0300 (Qua, 05 Mar 2008) $

* Casos de uso: uc-04.05.50
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_FW_PDF.'RRelatorio.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'FFolhaPagamentoFolhaAnaliticaSintetica.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoPeriodoMovimentacao.class.php';
include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoComplementar.class.php';
include_once CAM_GRH_PES_MAPEAMENTO.'TPessoalContrato.class.php';
include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoAtributoValorPadrao.class.php';
include_once CAM_GA_ORGAN_MAPEAMENTO.'VOrganogramaOrgaoNivel.class.php';

$inAlturaLinha = 3;
$arFiltro = Sessao::read('filtroRelatorio');
function addCabecalho(&$fpdf)
{
    GLOBAL $inAlturaLinha;
    $obRRelatorio           = new RRelatorio;
    $obRRelatorio->setExercicio        (Sessao::getExercicio());
    $obRRelatorio->setCodigoEntidade   (Sessao::getCodEntidade($boTransacao));
    $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
    $obRRelatorio->recuperaCabecalho   ($arConfiguracao);

    $fpdf->addPage();
    $fpdf->setLeftMargin(0);
    $fpdf->setTopMargin(0);
    $fpdf->SetLineWidth(0.03);
    $fpdf->SetCreator = 'URBEM';
    $fpdf->SetFillColor(220);
    $tMargem = 10/*$fpdf->tMargin*/;
    $lMargem = 8/*$fpdf->lMargin*/;
    if (is_file(CAM_FW_IMAGENS.$arConfiguracao['logotipo'])) {
        $fpdf->Image( CAM_FW_IMAGENS.$arConfiguracao['logotipo']  ,$lMargem,$tMargem,20);
    } elseif (is_file($arConfiguracao['logotipo'])) {
        $fpdf->Image(  $arConfiguracao['logotipo'] ,$lMargem,$tMargem,20);
    }
    $fpdf->Cell(20,10,'');
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->SetFillColor(255);
    $X = 30.00125;
    $Y = 10.00125;
    $fpdf->SetXY($X,$Y);
    $fpdf->Cell(70,$inAlturaLinha, $arConfiguracao['nom_prefeitura']  ,0,'L',1);
    $fpdf->SetFont('Helvetica','',8);
    $fpdf->SetXY($X,$Y+4);
    $fpdf->Cell(70,$inAlturaLinha,'Fone/Fax: '.$arConfiguracao['fone'].' / '.$arConfiguracao['fax'],0,'L',1);
    $fpdf->SetXY($X,$Y+8);
    $fpdf->Cell(70,$inAlturaLinha,'E-mail: '.$arConfiguracao['e_mail'] ,0,'L',1);
    $fpdf->SetXY($X,$Y+12);
    $fpdf->Cell(70,$inAlturaLinha, $arConfiguracao['logradouro'].','.$arConfiguracao['numero'].' - '.$arConfiguracao['nom_municipio']  ,0,'L',1);
    $fpdf->SetXY($X,$Y+16);
    $fpdf->Cell(70,$inAlturaLinha,'CEP: '.$arConfiguracao['cep'],0,'L',1);
    $fpdf->SetXY($X,$Y+20);
    $fpdf->Cell(70,$inAlturaLinha,'CNPJ: '.$arConfiguracao['cnpj'],0,'L',1);
    $fpdf->SetFont('Helvetica','B',8);
    $sDisp = $fpdf->DefOrientation;
    $iAjus = 70;
    if ($sDisp=='L') {
        $iAjus = 160;
    }
    $fpdf->SetXY($X+$iAjus,$Y);
    $fpdf->Cell(56,5,$arConfiguracao['nom_modulo'],1,0,'L',1);
    $fpdf->Cell(0,5,'Versão: '.Sessao::read('flVersao') ,1,0,'L',1);
    $fpdf->SetXY($X+$iAjus,$Y+5);
    $fpdf->Cell(56,5,$arConfiguracao['nom_funcionalidade'],1,'TRL','L',1);
    $fpdf->Cell(0,5,'Usuário: '.Sessao::getUsername(),1,'RLB','L',1);
    $fpdf->SetXY($X+$iAjus,$Y+10);
    if ($fpdf->stAcao) {
        $arConfiguracao['nom_acao'] = trim($fpdf->stAcao);
    } else {
        if ($fpdf->stComplementoAcao) {
            $stNomAcao = trim($arConfiguracao['nom_acao']) .' '.$fpdf->stComplementoAcao;
        }
    }
    $stNomAcao = ( $stNomAcao ) ? $stNomAcao : $arConfiguracao['nom_acao'];
    $fpdf->Cell(0,5,$stNomAcao,1,'RLB','L',1);
    $fpdf->SetFont('Helvetica','',8);
    $fpdf->SetXY($X+$iAjus,$Y+15);
    $fpdf->Cell(0,5,$fpdf->stSubTitulo,1,'RLB','L',1);
    $fpdf->SetXY($X+$iAjus,$Y+20);
    $fpdf->Cell(33,5,'Emissão: '.date('d/m/Y', time()),1,0,'L',1);
    $fpdf->Cell(23,5,'Hora: '.date('H:i', time()),1,0,'L',1);
    $fpdf->AliasNbPages();
    if ($fpdf->inPaginaInicial == null) {
        $fpdf->Cell(0,5,'Página: '.$fpdf->PageNo().' de '.$fpdf->AliasNbPages,1,0,'L',1);
    } else {
        $fpdf->Cell(0,5,'Página: '.( $fpdf->PageNo() + $fpdf->inPaginaInicial ) ,1,0,'L',1);
    }
    $fpdf->Ln(4);
    $fpdf->Line(7, 35, 200, 35);
    $inLinha = 36;

    return $inLinha;
}

function incrementaLinha(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;

    $boTotalAgrupamento = Sessao::read('boTotalAgrupamento');
    $boAdicionaCabecalho = true;
    if ($inLinha == 999) {
        $boAdicionaCabecalho = false;
    }

    if ($boTotalAgrupamento === true) {
        $boAdicionaCabecalho = false;
    }

    $inLinha += ($inAlturaLinha+1);

    if ($inLinha >= 185) {
        $inLinha = addCabecalho($fpdf);
        switch ($arFiltro['stFolha']) {
            case 'analítica_resumida':
                if ($boAdicionaCabecalho) {
                    $inLinha = addCabecalhoRelatorio($fpdf,$inLinha);
                }
                break;
            case 'sintética':
                $inLinha = addCabecalhoRelatorioSintetica($fpdf,$inLinha);
                break;
            case 'analítica':
                $inLinha = addCabecalhoRelatorioAnalitica($fpdf,$inLinha);
                break;
        }
    }

    return $inLinha;
}

function addTipoFolha(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;
    switch ($arFiltro['inCodConfiguracao']) {
        case '1':
            $stFolha = 'Salário';
            break;
        case '2':
            $stFolha = 'Férias';
            break;
        case '3':
            $stFolha = 'Décimo';
            break;
        case '4':
            $stFolha = 'Rescisão';
            break;
        default:
            $stFolha = 'Complementar';
            break;
    }
    $inMes = ($arFiltro['inCodMes'] < 10 ) ? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $dtCompetência = $inMes.'/'.$arFiltro['inAno'];
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $stFiltro = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetência."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentaco,$stFiltro);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(60,$inAlturaLinha, 'Tipo da Folha: '.$stFolha ,0,0,'L',1);
    $fpdf->SetXY(67,$inLinha);
    $fpdf->Cell(60,$inAlturaLinha, 'Competência: '.$dtCompetência ,0,0,'L',1);
    $fpdf->SetXY(127,$inLinha);
    $fpdf->Cell(60,$inAlturaLinha, 'Periodo Movimentação: '.$rsPeriodoMovimentaco->getCampo('dt_inicial').' até '.$rsPeriodoMovimentaco->getCampo('dt_final') ,0,0,'L',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);
    $fpdf->Line( 7 , $inLinha-1 , 290 , $inLinha-1 );

    return $inLinha;
}

function addCabecalhoRelatorio(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;
    $inLinha  = addTipoFolha($fpdf,$inLinha);

    if (!$arFiltro['boEmitirRelatorio']) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, 'Contrato' ,0,0,'L',1);
        $fpdf->SetXY(27,$inLinha);
        $fpdf->Cell(140,$inAlturaLinha, 'Nome do Servidor' ,0,0,'L',1);
        $fpdf->SetXy(135,$inLinha);
        $fpdf->Cell(30,$inAlturaLinha, 'Hrs. Mensais', 0, 0, 'L', 1);
        $fpdf->SetXY(157,$inLinha);
        $fpdf->Cell(80,$inAlturaLinha, 'Regime/Função' ,0,0,'L',1);
        $fpdf->SetXY(265,$inLinha);

        include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
        $stCondicao =  ' WHERE cod_modulo = 22';
        $stCondicao .= "   AND exercicio = '".Sessao::getExercicio()."'";
        $stCondicao .= "   AND parametro = 'dtContagemInicial'";
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
        $obTAdministracaoConfiguracao->recuperaTodos($rsConfiguracao, $stCondicao);

        $stDescricaoDataInicioContagem = '';
        switch (trim($rsConfiguracao->getCampo('valor'))) {
            CASE 'dtAdmissao':  $stDescricaoDataInicioContagem = 'Data de Admissão'; break;
            CASE 'dtNomeacao':  $stDescricaoDataInicioContagem = 'Data de Nomeação'; break;
            CASE 'dtPosse':     $stDescricaoDataInicioContagem = 'Data de Posse';    break;
        }

        $fpdf->Cell(30,$inAlturaLinha, $stDescricaoDataInicioContagem ,0,0,'L',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(95,$inAlturaLinha, 'Lotação/Local' ,0,0,'L',1);
        $fpdf->SetXY(103,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, 'Banco' ,0,0,'L',1);
        $fpdf->SetXY(157,$inLinha);
        $fpdf->Cell(80,$inAlturaLinha, 'Padrão' ,0,0,'L',1);
        $fpdf->SetXY(265,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, 'Situação' ,0,0,'L',1);
        $fpdf->SetXY(7,$inLinha);
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }

    if ( !$arFiltro['boEmitirRelatorio'] ) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'PROVENTOS' ,0,0,'C',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'DESCONTOS' ,0,0,'C',1);
        $fpdf->SetXY(193,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'BASES/INFORMATIVOS' ,0,0,'C',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);
        $fpdf->Line( 7 , $inLinha-1 , 290 , $inLinha-1 );    
    }

    return $inLinha;
}

function addInformacoesContrato(&$fpdf,$inLinha,$arDados)
{
    global $inAlturaLinha;

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, $arDados['registro'] ,0,0,'L',1);
    $fpdf->SetXY(27,$inLinha);
    $fpdf->Cell(129,$inAlturaLinha, $arDados['nom_cgm'] ,0,0,'L',1);
    $fpdf->SetXY(135,$inLinha);
    $fpdf->Cell(17,$inAlturaLinha, $arDados['hr_mensais'], 0, 0, 'L', 1);
    $fpdf->SetXY(157,$inLinha);
    $fpdf->Cell(107,$inAlturaLinha, substr($arDados['regime'].'/'.$arDados['funcao'],0,58) ,0,0,'L',1);
    $fpdf->SetXY(265,$inLinha);

    include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
    $stCondicao =  " WHERE cod_modulo = 22";
    $stCondicao .= "   AND exercicio = '".Sessao::getExercicio()."'";
    $stCondicao .= "   AND parametro = 'dtContagemInicial'";
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->recuperaTodos($rsConfiguracao, $stCondicao);

    switch (trim($rsConfiguracao->getCampo('valor'))) {
    case 'dtAdmissao' : $data = $arDados['admissao']; break;
    case 'dtNomeacao' : $data = $arDados['nomeacao']; break;
    case 'dtPosse'    : $data = $arDados['posse']   ; break;
    }

    $fpdf->Cell(25,$inAlturaLinha, $data ,0,0,'L',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(95,$inAlturaLinha, substr($arDados['orgao'].'-'.$arDados['lotacao'].'/'.$arDados['local'],0,57) ,0,0,'L',1);
    $fpdf->SetXY(101,$inLinha);
    $fpdf->Cell(55,$inAlturaLinha, $arDados['num_banco'].'-'.$arDados['descricao_banco'] ,0,0,'L',1);
    $fpdf->SetXY(157,$inLinha);
    $fpdf->Cell(79,$inAlturaLinha, substr($arDados['padrao'],0,58) ,0,0,'L',1);
    $fpdf->SetXY(265,$inLinha);
    $fpdf->Cell(25,$inAlturaLinha, $arDados['situacao'] ,0,0,'L',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);

    return $inLinha;
}

function recuperaFiltro(&$obTFolhaPagamentoPeriodoMovimentacao, $inCodPeriodoMovimentacao = 0)
{
    global $arFiltro,$inAlturaLinha;
    $stTipoFiltro = ( $arFiltro['stTipoFiltro'] ) ? $arFiltro['stTipoFiltro'] : $arFiltro['hdnTipoFiltro'];
    switch ($stTipoFiltro) {
        case 'contrato':
        case 'cgm_contrato':
            $obTFolhaPagamentoPeriodoMovimentacao->setDado('cod_atributo',0);
            $obTFolhaPagamentoPeriodoMovimentacao->setDado('valor', '');
            $obTPessoalContrato = new TPessoalContrato();
            $stContratos = '(';
            foreach (Sessao::read('arContratos') as $arContrato) {
                $stFiltroContrato = ' WHERE registro = '.$arContrato['contrato'];
                $obTPessoalContrato->recuperaTodos($rsContrato,$stFiltroContrato);
                $stContratos .= $rsContrato->getCampo('cod_contrato').',';
            }
            $stContratos = substr($stContratos,0,strlen($stContratos)-1).')';
            $stFiltro = ' AND contrato.cod_contrato IN '.$stContratos;
            break;
        case 'atributo':
            $inCodAtributo = $arFiltro['inCodAtributo'];
            $stFiltro .= " AND cod_atributo = $inCodAtributo      \n";
            $obTFolhaPagamentoPeriodoMovimentacao->setDado('cod_atributo', $inCodAtributo);

            $stNomeVariavel = 'Atributo_'.$inCodAtributo.'_'.$arFiltro['inCodCadastro'];
            if (is_array($arFiltro[$stNomeVariavel.'_Selecionados'])) {
                $arValorAtributos = $arFiltro[$stNomeVariavel.'_Selecionados'];
                $stValorAtributo = '';
                foreach ($arValorAtributos as $stTemp) {
                    $stValorAtributo .= "''".$stTemp."'',";
                }
                $stValorAtributo = substr($stValorAtributo,0,strlen($stValorAtributo)-1);
                $obTFolhaPagamentoPeriodoMovimentacao->setDado('valor', $stValorAtributo);
                $stFiltro .= " AND valor IN ($stValorAtributo)      \n";
            } else {
                $stValorAtributo = $arFiltro[$stNomeVariavel];
                $obTFolhaPagamentoPeriodoMovimentacao->setDado("valor","''".$stValorAtributo."''");
                if (!empty($stValorAtributo) ) {
                    $stFiltro .= " AND valor = ''$stValorAtributo''      \n";
                }
            }
            break;
        case 'geral':
            $obTFolhaPagamentoPeriodoMovimentacao->setDado('cod_atributo', 0);
            $obTFolhaPagamentoPeriodoMovimentacao->setDado('valor', '');
            if ($arFiltro['boFiltrarRegSubCarEsp']) {
                if (is_array($arFiltro['inCodRegimeSelecionados'])) {
                    $stCodRegime = '(';
                    foreach ($arFiltro['inCodRegimeSelecionados'] as $inCodRegime) {
                        $stCodRegime .= $inCodRegime.',';
                    }
                    $stCodRegime = substr($stCodRegime,0,strlen($stCodRegime)-1).')';
                    $stFiltro .= ' AND cod_regime IN '.$stCodRegime;
                }
                if (is_array($arFiltro['inCodSubDivisaoSelecionados'])) {
                    $stCodSubDivisao = '(';
                    foreach ($arFiltro['inCodSubDivisaoSelecionados'] as $inCodSubDivisao) {
                        $stCodSubDivisao .= $inCodSubDivisao.',';
                    }
                    $stCodSubDivisao = substr($stCodSubDivisao,0,strlen($stCodSubDivisao)-1).')';
                    $stFiltro .= ' AND cod_sub_divisao IN '.$stCodSubDivisao;
                }
                if (is_array($arFiltro['inCodCargoSelecionados'])) {
                    $stCodCargo = '(';
                    foreach ($arFiltro['inCodCargoSelecionados'] as $inCodCargo) {
                        $stCodCargo .= $inCodCargo.',';
                    }
                    $stCodCargo = substr($stCodCargo,0,strlen($stCodCargo)-1).')';
                    $stFiltro .= ' AND cod_cargo IN '.$stCodCargo;
                }
                if (is_array($arFiltro['inCodEspecialidadeSelecionados'])) {
                    $stCodEspecialidade = '(';
                    foreach ($arFiltro['inCodEspecialidadeSelecionados'] as $inCodEspecialidade) {
                        $stCodEspecialidade .= $inCodEspecialidade.',';
                    }
                    $stCodEspecialidade = substr($stCodEspecialidade,0,strlen($stCodEspecialidade)-1).')';
                    $stFiltro .= ' AND cod_especialidade_cargo IN '.$stCodEspecialidade;
                }
            }

            if ($arFiltro['boFiltrarRegSubFunEsp']) {
                if (is_array($arFiltro['inCodRegimeSelecionadosFunc'])) {
                    $stCodRegime = '(';
                    foreach ($arFiltro['inCodRegimeSelecionadosFunc'] as $inCodRegime) {
                        $stCodRegime .= $inCodRegime.',';
                    }
                    $stCodRegime = substr($stCodRegime,0,strlen($stCodRegime)-1).')';
                    $stFiltro .= ' AND cod_regime_funcao IN '.$stCodRegime;
                }
                if (is_array($arFiltro['inCodSubDivisaoSelecionadosFunc'])) {
                    $stCodSubDivisao = '(';
                    foreach ($arFiltro['inCodSubDivisaoSelecionadosFunc'] as $inCodSubDivisao) {
                        $stCodSubDivisao .= $inCodSubDivisao.',';
                    }
                    $stCodSubDivisao = substr($stCodSubDivisao,0,strlen($stCodSubDivisao)-1).')';
                    $stFiltro .= ' AND cod_sub_divisao_funcao IN '.$stCodSubDivisao;
                }
                if (is_array($arFiltro['inCodFuncaoSelecionados'])) {
                    $stCodCargo = '(';
                    foreach ($arFiltro['inCodFuncaoSelecionados'] as $inCodCargo) {
                        $stCodCargo .= $inCodCargo.',';
                    }
                    $stCodCargo = substr($stCodCargo,0,strlen($stCodCargo)-1).')';
                    $stFiltro .= ' AND cod_funcao IN '.$stCodCargo;
                }
                if (is_array($arFiltro['inCodEspecialidadeSelecionadosFunc'])) {
                    $stCodEspecialidade = '(';
                    foreach ($arFiltro['inCodEspecialidadeSelecionadosFunc'] as $inCodEspecialidade) {
                        $stCodEspecialidade .= $inCodEspecialidade.',';
                    }
                    $stCodEspecialidade = substr($stCodEspecialidade,0,strlen($stCodEspecialidade)-1).')';
                    $stFiltro .= ' AND cod_especialidade_funcao IN '.$stCodEspecialidade;
                }
            }
            if ($arFiltro['boFiltrarPorPadrao']) {
                if (is_array($arFiltro['inCodPadraoSelecionados'])) {
                    $stCodPadrao = '(';
                    foreach ($arFiltro['inCodPadraoSelecionados'] as $inCodPadrao) {
                        $stCodPadrao .= $inCodPadrao.',';
                    }
                    $stCodPadrao = substr($stCodPadrao,0,strlen($stCodPadrao)-1).')';
                    $stFiltro .= ' AND cod_padrao IN '.$stCodPadrao;
                }
            }
            if ($arFiltro['boFiltrarPorLotacao']) {                
                if (is_array($arFiltro['inCodLotacaoSelecionados'])) {
                    //Pegar todos os estruturais para defiinir os filhos
                    if ( $arFiltro['boSubNivelLotacao'] ) {
                        $obVOrganogramaOrgaoNivel = new VOrganogramaOrgaoNivel();                        
                        
                        $stCodLotacao = $stCodLotacaoAux = '( ';
                        
                        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCodLotacao) {
                            $stCodLotacaoAux .= $inCodLotacao.',';
                        }
                        $stCodLotacaoAux = substr($stCodLotacaoAux,0,strlen($stCodLotacaoAux)-1).')';
                        $stFiltroAux = ' WHERE cod_orgao IN '.$stCodLotacaoAux;
                        $obErro = $obVOrganogramaOrgaoNivel->recuperaTodos($rsNivelLotacao,$stFiltroAux,'',$boTransacao);
                        //Buscando filhos de acordo com o estrutural
                        if ( $rsNivelLotacao->getNumLinhas() > 0 ) {
                            $stCodEstrutural = "('";
                            foreach ($rsNivelLotacao->getElementos() as $orgaoNivel ) {
                                $stCodEstrutural .= $orgaoNivel['orgao_reduzido'].'%|';
                            }
                            $stCodEstrutural = substr($stCodEstrutural,0,strlen($stCodEstrutural)-1)."')";                        
                            $stFiltroAux = " WHERE orgao SIMILAR TO ".$stCodEstrutural."";
                            $obErro = $obVOrganogramaOrgaoNivel->recuperaTodos($rsNivelLotacaoFilhos,$stFiltroAux,'',$boTransacao);
                            // atribuindo os cod_orgao dos niveis filhos
                            foreach ( $rsNivelLotacaoFilhos->getElementos() as $orgaoNivelFilhos ) {                                
                                $stCodLotacao .= $orgaoNivelFilhos['cod_orgao'].',';
                            }
                        }else{
                            foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCodLotacao) {
                                $stCodLotacao .= $inCodLotacao.',';
                            }
                        }
                        //salvando para o filtro da consulta final
                        $stCodLotacao = substr($stCodLotacao,0,strlen($stCodLotacao)-1).')';                        
                        $stFiltro .= ' AND cod_orgao IN '.$stCodLotacao;                        
                    }else{
                        $stCodLotacao = '(';
                        foreach ($arFiltro['inCodLotacaoSelecionados'] as $inCodLotacao) {
                            $stCodLotacao .= $inCodLotacao.',';
                        }
                        $stCodLotacao = substr($stCodLotacao,0,strlen($stCodLotacao)-1).')';
                        $stFiltro .= ' AND cod_orgao IN '.$stCodLotacao;
                    }
                }
            }
            if ($arFiltro['boFiltrarPorLocal']) {
                if (is_array($arFiltro['inCodLocalSelecionados'])) {
                    $stCodLocal = '(';
                    foreach ($arFiltro['inCodLocalSelecionados'] as $inCodLocal) {
                        $stCodLocal .= $inCodLocal.',';
                    }
                    $stCodLocal = substr($stCodLocal,0,strlen($stCodLocal)-1).')';
                    $stFiltro .= ' AND cod_local IN '.$stCodLocal;
                }
            }
            if ($arFiltro['boFiltrarPorBanco']) {
                if (is_array($arFiltro['inCodBancoSelecionados'])) {
                    if (count($arFiltro['inCodBancoSelecionados']) > 0) {
                        $stCodBancosSelecionados = implode(',', $arFiltro['inCodBancoSelecionados']);

                        $stFiltro .= ' AND cod_banco IN ('.$stCodBancosSelecionados.')';
                    }
                }
            }

            if ($arFiltro['boAtivo'] ||
               $arFiltro['boInativo'] ||
               $arFiltro['boPensionista'] ||
               $arFiltro['boRescindido']) {

                $arFiltroCadastro = array();
                $stEntidade = Sessao::getEntidade();

                if ($arFiltro['boAtivo']) {
                    $stFiltroCadastro = " servidor_pensionista.situacao = ''A'' ";

                    $arFiltroCadastro[] = $stFiltroCadastro;
                }
                if ($arFiltro['boRescindido']) {
                    $stFiltroCadastro = " servidor_pensionista.situacao = ''R'' ";

                    $arFiltroCadastro[] = $stFiltroCadastro;
                }
                if ($arFiltro['boInativo']) {
                    $stFiltroCadastro = " servidor_pensionista.situacao = ''P'' ";

                    $arFiltroCadastro[] = $stFiltroCadastro;
                }
                if ($arFiltro['boPensionista']) {
                    $stFiltroCadastro  = '     EXISTS ( SELECT 1                                                           ';
                    $stFiltroCadastro .= '                FROM pessoal'.$stEntidade.'.contrato_pensionista                 ';
                    $stFiltroCadastro .= '               WHERE contrato_pensionista.cod_contrato = contrato.cod_contrato ) ';
                    $arFiltroCadastro[] = $stFiltroCadastro;
                }

                $stFiltroCadastro = implode(' OR ', $arFiltroCadastro);

                if (strlen($stFiltroCadastro) > 0) {
                    $stFiltroCadastro = ' AND ('.$stFiltroCadastro.')';
                    $stFiltro .= $stFiltroCadastro;
                }
            }

            break;
    }

    return $stFiltro;
}

function recuperaOrdenacao()
{
    global $arFiltro,$inAlturaLinha;

    $stTipoFiltro = ( $arFiltro['stTipoFiltro'] ) ? $arFiltro['stTipoFiltro'] : $arFiltro['hdnTipoFiltro'];
    switch ($stTipoFiltro) {
        case 'contrato':
        case 'cgm_contrato':
            $stOrdenacao = ( $arFiltro['stOrdenacao'] == 'alfabetica' ) ? 'nom_cgm' : 'registro';
            break;
        case 'atributo':
            if ($arFiltro['boAtributoDinamico']) {
                $stOrdenacao = 'valor_label,';
            }
            if (!is_array($arFiltro['arrayOrdenacao'])) {
                $arFiltro['arrayOrdenacao'] = array();
            }
            $stOrdenacao .= ( $arFiltro['stOrdenacao'] == 'alfabetica' ) ? 'nom_cgm' : 'registro';
            break;
        case 'geral':
        
            $stOrdenacao = '';

            if (!is_array($arFiltro['arrayOrdenacao'])) {
                $arFiltro['arrayOrdenacao'] = array();
            }

            foreach ($arFiltro['arrayOrdenacao'] as $key => $value) {
                $arFiltro[$key] = $value;

                if ($key == 'boBanco') {
                    switch ($arFiltro['stAlfNumBanco']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_banco,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_banco,';
                            break;
                    }
                }
                if ($key == 'boLotacao') {
                    switch ($arFiltro['stAlfNumLotacao']) {
                        case 'numerica':
                            $stOrdenacao .= 'orgao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_lotacao,';
                            break;
                    }
                }
                if ($key == 'boLocal') {
                    switch ($arFiltro['stAlfNumLocal']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_local,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_local,';
                            break;
                    }
                }
                if ($key == 'boRegimedoCargo') {
                    switch ($arFiltro['stAlfNumRegimedoCargo']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_regime,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_regime,';
                            break;
                    }
                }
                if ($key == 'boSubdivisaodoCargo') {
                    switch ($arFiltro['stAlfNumSubdivisaodoCargo']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_sub_divisao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_sub_divisao,';
                            break;
                    }
                }
                if ($key == 'boCargo') {
                    switch ($arFiltro['stAlfNumCargo']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_cargo,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_cargo,';
                            break;
                    }
                }
                if ($key == 'boEspecialidadedoCargo') {
                    switch ($arFiltro['stAlfNumEspecialidadedoCargo']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_especialidade_cargo,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_especialidade,';
                            break;
                    }
                }
                if ($key == 'boRegimedaFuncao') {
                    switch ($arFiltro['stAlfNumRegimedaFuncao']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_regime_funcao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_regime_funcao,';
                            break;
                    }
                }
                if ($key == 'boSubdivisaodaFuncao') {
                    switch ($arFiltro['stAlfNumSubdivisaodaFuncao']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_sub_divisao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_sub_divisao_funcao,';
                            break;
                    }
                }
                if ($key == 'boFuncao') {
                    switch ($arFiltro['stAlfNumFuncao']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_funcao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_funcao,';
                            break;
                    }
                }
                if ($key == 'boEspecialidadedaFuncao') {
                    switch ($arFiltro['stAlfNumEspecialidadedaFuncao']) {
                        case 'numerica':
                            $stOrdenacao .= 'cod_especialidade_funcao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'descricao_especialidade_funcao,';
                            break;
                    }
                }

                if ($key == 'boSituacao') {
                    switch ($arFiltro['stAlfNumSituacao']) {
                        case 'numerica':
                            $stOrdenacao .= 'situacao,';
                            break;
                        case 'alfabetica':
                            $stOrdenacao .= 'situacao,';
                            break;
                    }
                }
            }//end foreach
            if ($arFiltro['boCgm']) {
                switch ($arFiltro['stAlfNumCgm']) {
                    case 'numerica':
                        $stOrdenacao .= 'numcgm,';
                        break;
                    case 'alfabetica':
                        $stOrdenacao .= 'nom_cgm,';
                        break;
                }
            }
            $stOrdenacao .= ( $arFiltro['stOrdenacao'] == 'alfabetica' ) ? 'nom_cgm' : 'registro';
            break;
    }

    return  $stOrdenacao;
}

function ordenacaoPosicaoAnterior($stPosicaoRefeferencia)
{
    global $arFiltro;

    $nuPosicao = 0;
    reset($arFiltro['arrayOrdenacao']);
    while (list($stChave, $stValor) = each($arFiltro['arrayOrdenacao'])) {
        if ($stChave == $stPosicaoRefeferencia) {
            if($nuPosicao == 0)
                $stChaveAnterior = $stChave;
            break;
        }

        $stChaveAnterior = $stChave;
        $nuPosicao++;
    }

    return $stChaveAnterior;
}

function analiticaResumida(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;
    $boCabecalhoTotaisGerais = false;
    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();

    $inMes = ($arFiltro['inCodMes'] < 10) ? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $dtCompetencia = $inMes.'/'.$arFiltro['inAno'];
    $stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);

    $obFFolhaPagamentoFolhaAnaliticaResumida = new FFolhaPagamentoFolhaAnaliticaResumida();
    $stFiltro = recuperaFiltro($obFFolhaPagamentoFolhaAnaliticaResumida, $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao') );
    $stOrdenacao = recuperaOrdenacao();

    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', 0);
        $inCodComplementar  = ( $arFiltro['inCodComplementar'] ) ? $arFiltro['inCodComplementar'] : 0;
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar', $inCodComplementar);
    } else {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', $arFiltro['inCodConfiguracao']);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar', 0);
    }
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('filtro',    $stFiltro);
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao', $stOrdenacao);
    $obFFolhaPagamentoFolhaAnaliticaResumida->folhaAnaliticaResumida($rsContratosCalculados);

    $arProvento = array();
    $arDesconto = array();
    $arBase     = array();
    $inContador = 0;

    $arProventoBanco = array();
    $arDescontoBanco = array();
    $arBaseBanco     = array();
    $inContBanco = 0;

    $arProventoLotacao = array();
    $arDescontoLotacao = array();
    $arBaseLotacao     = array();
    $inContLotacao = 0;

    $arProventoLocal = array();
    $arDescontoLocal = array();
    $arBaseLocal     = array();
    $inContLocal   = 0;

    $arProventoFuncao = array();
    $arDescontoFuncao = array();
    $arBaseFuncao     = array();
    $inContFuncao   = 0;

    $arProventoRegime = array();
    $arDescontoRegime = array();
    $arBaseRegime     = array();
    $inContRegime   = 0;

    $arProventoCGM = array();
    $arDescontoCGM = array();
    $arBaseCGM     = array();
    $inContCGM   = 0;

    $arProventoAtributo = array();
    $arDescontoAtributo = array();
    $arBaseAtributo     = array();
    $inContAtributo   = 0;

    $legenda = montaLegenda($arFiltro['inCodConfiguracao']);

    
    //Verifica se o filtro é geral e deve ser emitido realtorio de totais, setada variavel para nao repetir o cabecalho
    if ($arFiltro['boEmitirRelatorio'] ) {
        $boCabecalhoTotaisGerais = true;       
    }else{
        $inLinha = addCabecalhoRelatorio($fpdf,$inLinha);
    }
    $inLinhaP = $inLinha;
    $inLinhaD = $inLinha;
    $inLinhaB = $inLinha;
    $arLinhasTMP = array();
    $inCountP = 0;
    $inCountD = 0;
    $inCountB = 0;
    $stCGM = '';
    $stRegistro = '';
    $inTotalContratos = 0;

    while (!$rsContratosCalculados->eof()) {
        if ($stRegistro != $rsContratosCalculados->getCampo('registro')) {
            $inContador++;
            $inContBanco++;
            $inContLotacao++;
            $inContLocal++;
            $inContFuncao++;
            $inContRegime++;
            $inContCGM++;
            $inContAtributo++;
            $arDados['registro']            = $rsContratosCalculados->getCampo('registro');
            $arDados['nom_cgm']             = $rsContratosCalculados->getCampo('nom_cgm');
            $arDados['regime']              = $rsContratosCalculados->getCampo('descricao_regime_funcao');
            $arDados['funcao']              = $rsContratosCalculados->getCampo('descricao_funcao');
            $arDados['padrao']              = $rsContratosCalculados->getCampo('descricao_padrao');
            $arDados['orgao']               = $rsContratosCalculados->getCampo('orgao');
            $arDados['lotacao']             = $rsContratosCalculados->getCampo('descricao_lotacao');
            $arDados['local']               = $rsContratosCalculados->getCampo('descricao_local');
            $arDados['nomeacao']            = $rsContratosCalculados->getCampo('dt_nomeacao');
            $arDados['posse']               = $rsContratosCalculados->getCampo('dt_posse');
            $arDados['admissao']            = $rsContratosCalculados->getCampo('dt_admissao');
            $arDados['hr_mensais']          = $rsContratosCalculados->getCampo('hr_mensais');
            $arDados['num_banco']           = $rsContratosCalculados->getCampo('num_banco');
            $arDados['descricao_banco']     = $rsContratosCalculados->getCampo('descricao_banco');
            $arDados['dt_contagem_inicial'] = $rsContratosCalculados->getCampo('dt_contagem_inicial');

            $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_contrato', $rsContratosCalculados->getCampo('cod_contrato'));
            $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao'   , $arFiltro['stOrdenacaoEventos']);

            $inTotalContratos = $inTotalContratos + 1;

            $obFFolhaPagamentoFolhaAnaliticaResumida->situacaoServidorFolhaAnaliticaResumida($rsContratoSituacao);
            $arDados['situacao']  = $rsContratoSituacao->getCampo('situacao');

            if (!$arFiltro['boEmitirRelatorio']) {
                $inLinha = addInformacoesContrato($fpdf,$inLinha,$arDados);
            }

            $nuTotalProventos = 0;
            $nuTotalDescontos = 0;
            $nuTotalBase      = 0;
            $boQuebra = false;
            $inCountP = 0;
            $inCountD = 0;
            $inCountB = 0;
            $inPagina = 0;
            $inQuebra = 0;
            $boQuebraB = false;
            $boQuebraD = false;
            $arLinhasTMP = array();
        }

        /* TOTAL POR LOTAÇÃO -> CORPO DOS CONTRATOS CALCULADOS */

        if ($rsContratosCalculados->getCampo('codigop') != '') {
            if ($inCountP !== 0) {
                $inLinhaAnterior = $inLinha;
                if (!$arFiltro['boEmitirRelatorio']) {
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                }
                if ($inLinhaAnterior > $inLinha) {
                    $inQuebra = $inLinha;
                    $boQuebraD = true;
                    $boQuebraB = true;
                }
            }
            
            $codigoP        = $rsContratosCalculados->getCampo('codigop');
            $descricaoP     = $rsContratosCalculados->getCampo('descricaop');
            $desdobramentoP = $rsContratosCalculados->getCampo('desdobramentop');
            $valorP         = $rsContratosCalculados->getCampo('valorp');
            $quantidadeP    = $rsContratosCalculados->getCampo('quantidadep');
            $inQuantidadeParc    = $rsContratosCalculados->getCampo('parcela');

            $nuTotalProventos += $valorP;
            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(7,$inLinha);
                $fpdf->Cell(10,$inAlturaLinha, $codigoP,0,0,'L',1);
                $fpdf->SetXY(17,$inLinha);
                $fpdf->Cell(35,$inAlturaLinha, $descricaoP,0,0,'L',1);
                $fpdf->SetXY(52,$inLinha);
                $fpdf->Cell(9,$inAlturaLinha, $desdobramentoP,0,0,'L',1);
                $fpdf->SetXY(61,$inLinha);
                if ($inQuantidadeParc != "")
                    $fpdf->Cell(18, $inAlturaLinha, number_format($quantidadeP, 0).'/'.$inQuantidadeParc, 0, 0, 'R', 1);
                else
                    $fpdf->Cell(18,$inAlturaLinha, number_format($quantidadeP,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(79,$inLinha);
                $fpdf->Cell(18,$inAlturaLinha, number_format($valorP,2,',','.') ,0,0,'R',1);
            }

            $arProvento = '';
            $arProvento[] = $codigoP;
            $arProvento[] = $descricaoP;
            $arProvento[] = $desdobramentoP;
            $arProventos[$codigoP.$desdobramentoP][0] += $valorP;
            $arProventos[$codigoP.$desdobramentoP][1] += $quantidadeP;
            $arProventos[$codigoP.$desdobramentoP][2]  = $arProvento;
            $arProventos[$codigoP.$desdobramentoP][3]++;

            if ($arFiltro['boEmitirTotais']) {
                if ($arFiltro['boBanco']) {
                    $arProventoBanco = '';
                    $arProventoBanco[] = $codigoP;
                    $arProventoBanco[] = $descricaoP;
                    $arProventoBanco[] = $desdobramentoP;
                    $arProventosBanco[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosBanco[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosBanco[$codigoP.$desdobramentoP][2]  = $arProventoBanco;
                    $arProventosBanco[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boLotacao']) {
                    $arProventoLotacao = '';
                    $arProventoLotacao[] = $codigoP;
                    $arProventoLotacao[] = $descricaoP;
                    $arProventoLotacao[] = $desdobramentoP;
                    $arProventosLotacao[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosLotacao[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosLotacao[$codigoP.$desdobramentoP][2]  = $arProventoLotacao;
                    $arProventosLotacao[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boLocal']) {
                    $arProventoLocal = '';
                    $arProventoLocal[] = $codigoP;
                    $arProventoLocal[] = $descricaoP;
                    $arProventoLocal[] = $desdobramentoP;
                    $arProventosLocal[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosLocal[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosLocal[$codigoP.$desdobramentoP][2]  = $arProventoLocal;
                    $arProventosLocal[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boRegimedaFuncao']) {
                    $arProventoRegime = '';
                    $arProventoRegime[] = $codigoP;
                    $arProventoRegime[] = $descricaoP;
                    $arProventoRegime[] = $desdobramentoP;
                    $arProventosRegime[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosRegime[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosRegime[$codigoP.$desdobramentoP][2]  = $arProventoRegime;
                    $arProventosRegime[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boFuncao']) {
                    $arProventoFuncao = '';
                    $arProventoFuncao[] = $codigoP;
                    $arProventoFuncao[] = $descricaoP;
                    $arProventoFuncao[] = $desdobramentoP;
                    $arProventosFuncao[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosFuncao[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosFuncao[$codigoP.$desdobramentoP][2]  = $arProventoFuncao;
                    $arProventosFuncao[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boCgm']) {
                    $arProventoCGM = '';
                    $arProventoCGM[] = $codigoP;
                    $arProventoCGM[] = $descricaoP;
                    $arProventoCGM[] = $desdobramentoP;
                    $arProventosCGM[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosCGM[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosCGM[$codigoP.$desdobramentoP][2]  = $arProventoCGM;
                    $arProventosCGM[$codigoP.$desdobramentoP][3]++;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arProventoAtributo = '';
                    $arProventoAtributo[] = $codigoP;
                    $arProventoAtributo[] = $descricaoP;
                    $arProventoAtributo[] = $desdobramentoP;
                    $arProventosAtributo[$codigoP.$desdobramentoP][0] += $valorP;
                    $arProventosAtributo[$codigoP.$desdobramentoP][1] += $quantidadeP;
                    $arProventosAtributo[$codigoP.$desdobramentoP][2]  = $arProventoAtributo;
                    $arProventosAtributo[$codigoP.$desdobramentoP][3]++;
                }
            }
            if (!$arFiltro['boEmitirRelatorio']) {
                $arLinhasTMP[$inCountP] = $inLinha;
                $inCountP++;
            }
        } // FIM TOTAL POR LOTAÇÃO -> CORPO DOS CONTRATOS CALCULADOS

        //Descontos
        if ($rsContratosCalculados->getCampo('codigod') != '') {
            if (array_key_exists($inCountD, $arLinhasTMP)) {
                if ($inQuebra > 0 && $boQuebraD === true) {
                    $inPagina = $fpdf->PageNo() - 1;
                    $fpdf->page = $inPagina;
                } else {
                    $boQuebraD = false;
                }
                $inLinha = $arLinhasTMP[$inCountD];
            } else {
                $inLinhaAnterior = $inLinha;
                if (!$arFiltro['boEmitirRelatorio']) {
                    $inLinha = incrementaLinha($fpdf,$inLinha);
                }
                if ($inLinhaAnterior > $inLinha) {
                    $inQuebra = $inLinha;
                    $boQuebraB = true;
                }
                $arLinhasTMP[$inCountD] = $inLinha;
            }
            $inCountD++;

            $codigoD        = $rsContratosCalculados->getCampo('codigod');
            $descricaoD     = $rsContratosCalculados->getCampo('descricaod');
            $desdobramentoD = $rsContratosCalculados->getCampo('desdobramentod');
            $valorD         = $rsContratosCalculados->getCampo('valord');
            $quantidadeD    = $rsContratosCalculados->getCampo('quantidaded');
            $inQuantidadeParc    = $rsContratosCalculados->getCampo('parcela');

            $nuTotalDescontos += $valorD;
            if (!$arFiltro['boEmitirRelatorio']) {
                $inIncremento = 96;
                $fpdf->SetXY(7+$inIncremento,$inLinha);
                $fpdf->Cell(10,$inAlturaLinha, $codigoD,0,0,'L',1);
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(35,$inAlturaLinha, $descricaoD,0,0,'L',1);
                $fpdf->SetXY(52+$inIncremento,$inLinha);
                $fpdf->Cell(9,$inAlturaLinha, $desdobramentoD,0,0,'L',1);
                $fpdf->SetXY(61+$inIncremento,$inLinha);
                if ($inQuantidadeParc != "")
                    $fpdf->Cell(18,$inAlturaLinha, number_format($quantidadeD, 0).'/'.$inQuantidadeParc ,0,0,'R',1);
                else
                    $fpdf->Cell(18,$inAlturaLinha, number_format($quantidadeD,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(79+$inIncremento,$inLinha);
                $fpdf->Cell(18,$inAlturaLinha, number_format($valorD,2,',','.') ,0,0,'R',1);
            }

            $arDesconto = '';
            $arDesconto[] = $codigoD;
            $arDesconto[] = $descricaoD;
            $arDesconto[] = $desdobramentoD;
            $arDescontos[$codigoD.$desdobramentoD][0] += $valorD;
            $arDescontos[$codigoD.$desdobramentoD][1] += $quantidadeD;
            $arDescontos[$codigoD.$desdobramentoD][2]  = $arDesconto;
            $arDescontos[$codigoD.$desdobramentoD][3]++;

            if ($arFiltro['boEmitirTotais']) {
                if ($arFiltro['boBanco']) {
                    $arDescontoBanco = '';
                    $arDescontoBanco[] = $codigoD;
                    $arDescontoBanco[] = $descricaoD;
                    $arDescontoBanco[] = $desdobramentoD;
                    $arDescontosBanco[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosBanco[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosBanco[$codigoD.$desdobramentoD][2]  = $arDescontoBanco;
                    $arDescontosBanco[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boLotacao']) {
                    $arDescontoLotacao = '';
                    $arDescontoLotacao[] = $codigoD;
                    $arDescontoLotacao[] = $descricaoD;
                    $arDescontoLotacao[] = $desdobramentoD;
                    $arDescontosLotacao[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosLotacao[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosLotacao[$codigoD.$desdobramentoD][2]  = $arDescontoLotacao;
                    $arDescontosLotacao[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boLocal']) {
                    $arDescontoLocal = '';
                    $arDescontoLocal[] = $codigoD;
                    $arDescontoLocal[] = $descricaoD;
                    $arDescontoLocal[] = $desdobramentoD;
                    $arDescontosLocal[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosLocal[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosLocal[$codigoD.$desdobramentoD][2]  = $arDescontoLocal;
                    $arDescontosLocal[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boRegimedaFuncao']) {
                    $arDescontoRegime = '';
                    $arDescontoRegime[] = $codigoD;
                    $arDescontoRegime[] = $descricaoD;
                    $arDescontoRegime[] = $desdobramentoD;
                    $arDescontosRegime[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosRegime[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosRegime[$codigoD.$desdobramentoD][2]  = $arDescontoRegime;
                    $arDescontosRegime[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boFuncao']) {
                    $arDescontoFuncao = '';
                    $arDescontoFuncao[] = $codigoD;
                    $arDescontoFuncao[] = $descricaoD;
                    $arDescontoFuncao[] = $desdobramentoD;
                    $arDescontosFuncao[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosFuncao[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosFuncao[$codigoD.$desdobramentoD][2]  = $arDescontoFuncao;
                    $arDescontosFuncao[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boCgm']) {
                    $arDescontoCGM = '';
                    $arDescontoCGM[] = $codigoD;
                    $arDescontoCGM[] = $descricaoD;
                    $arDescontoCGM[] = $desdobramentoD;
                    $arDescontosCGM[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosCGM[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosCGM[$codigoD.$desdobramentoD][2]  = $arDescontoCGM;
                    $arDescontosCGM[$codigoD.$desdobramentoD][3]++;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arDescontoAtributo = '';
                    $arDescontoAtributo[] = $codigoD;
                    $arDescontoAtributo[] = $descricaoD;
                    $arDescontoAtributo[] = $desdobramentoD;
                    $arDescontosAtributo[$codigoD.$desdobramentoD][0] += $valorD;
                    $arDescontosAtributo[$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arDescontosAtributo[$codigoD.$desdobramentoD][2]  = $arDescontoAtributo;
                    $arDescontosAtributo[$codigoD.$desdobramentoD][3]++;
                }
            }

            if ($inPagina > 0) {
                $inPagina = $fpdf->PageNo() + 1;
                $fpdf->page = $inPagina;
                $inPagina = 0;
            }
        }

        //Bases e Informativos
        if ($rsContratosCalculados->getCampo('codigob') != '') {
            if (array_key_exists($inCountB, $arLinhasTMP)) {
                if ($inQuebra > 0 && $boQuebraB === true) {
                    $inPagina = $fpdf->PageNo() - 1;
                    $fpdf->page = $inPagina;
                } else {
                    $boQuebraB = false;
                }
                $inLinha = $arLinhasTMP[$inCountB];
            } else {
                if (!$arFiltro['boEmitirRelatorio']) {
                    $inLinha = incrementaLinha($fpdf,$inLinha);
                }
                $arLinhasTMP[$inCountB] = $inLinha;
            }
            $inCountB++;

            $codigoB        = $rsContratosCalculados->getCampo('codigob');
            $descricaoB     = $rsContratosCalculados->getCampo('descricaob');
            $desdobramentoB = $rsContratosCalculados->getCampo('desdobramentob');
            $valorB         = $rsContratosCalculados->getCampo('valorb');
            $quantidadeB    = $rsContratosCalculados->getCampo('quantidadeb');
            $inQuantidadeParc    = $rsContratosCalculados->getCampo('parcela');

            $nuTotalBase += $valorB;
            if (!$arFiltro['boEmitirRelatorio']) {
                $inIncremento = 97*2;
                $fpdf->SetXY(7+$inIncremento,$inLinha);
                $fpdf->Cell(10,$inAlturaLinha, $codigoB,0,0,'L',1);
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(35,$inAlturaLinha, $descricaoB,0,0,'L',1);
                $fpdf->SetXY(52+$inIncremento,$inLinha);
                $fpdf->Cell(9,$inAlturaLinha, $desdobramentoB,0,0,'L',1);
                $fpdf->SetXY(61+$inIncremento,$inLinha);
                if ($inQuantidadeParc != "")
                    $fpdf->Cell(18,$inAlturaLinha, number_format($quantidadeB, 0).'/'.$inQuantidadeParc ,0,0,'R',1);
                else
                    $fpdf->Cell(18,$inAlturaLinha, number_format($quantidadeB,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(79+$inIncremento,$inLinha);
                $fpdf->Cell(18,$inAlturaLinha, number_format($valorB,2,',','.') ,0,0,'R',1);
            }

            $arBase = '';
            $arBase[] = $codigoB;
            $arBase[] = $descricaoB;
            $arBase[] = $desdobramentoB;
            $arBases[$codigoB.$desdobramentoB][0] += $valorB;
            $arBases[$codigoB.$desdobramentoB][1] += $quantidadeB;
            $arBases[$codigoB.$desdobramentoB][2]  = $arBase;
            $arBases[$codigoB.$desdobramentoB][3]++;

            if ($arFiltro['boEmitirTotais']) {
                if ($arFiltro['boBanco']) {
                    $arBaseBanco = '';
                    $arBaseBanco[] = $codigoB;
                    $arBaseBanco[] = $descricaoB;
                    $arBaseBanco[] = $desdobramentoB;
                    $arBasesBanco[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesBanco[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesBanco[$codigoB.$desdobramentoB][2]  = $arBaseBanco;
                    $arBasesBanco[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boLotacao']) {
                    $arBaseLotacao = '';
                    $arBaseLotacao[] = $codigoB;
                    $arBaseLotacao[] = $descricaoB;
                    $arBaseLotacao[] = $desdobramentoB;
                    $arBasesLotacao[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesLotacao[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesLotacao[$codigoB.$desdobramentoB][2]  = $arBaseLotacao;
                    $arBasesLotacao[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boLocal']) {
                    $arBaseLocal = '';
                    $arBaseLocal[] = $codigoB;
                    $arBaseLocal[] = $descricaoB;
                    $arBaseLocal[] = $desdobramentoB;
                    $arBasesLocal[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesLocal[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesLocal[$codigoB.$desdobramentoB][2]  = $arBaseLocal;
                    $arBasesLocal[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boRegimedaFuncao']) {
                    $arBaseRegime = '';
                    $arBaseRegime[] = $codigoB;
                    $arBaseRegime[] = $descricaoB;
                    $arBaseRegime[] = $desdobramentoB;
                    $arBasesRegime[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesRegime[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesRegime[$codigoB.$desdobramentoB][2]  = $arBaseRegime;
                    $arBasesRegime[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boFuncao']) {
                    $arBaseFuncao = '';
                    $arBaseFuncao[] = $codigoB;
                    $arBaseFuncao[] = $descricaoB;
                    $arBaseFuncao[] = $desdobramentoB;
                    $arBasesFuncao[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesFuncao[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesFuncao[$codigoB.$desdobramentoB][2]  = $arBaseFuncao;
                    $arBasesFuncao[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boCgm']) {
                    $arBaseCGM = '';
                    $arBaseCGM[] = $codigoB;
                    $arBaseCGM[] = $descricaoB;
                    $arBaseCGM[] = $desdobramentoB;
                    $arBasesCGM[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesCGM[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesCGM[$codigoB.$desdobramentoB][2]  = $arBaseCGM;
                    $arBasesCGM[$codigoB.$desdobramentoB][3]++;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arBaseAtributo = '';
                    $arBaseAtributo[] = $codigoB;
                    $arBaseAtributo[] = $descricaoB;
                    $arBaseAtributo[] = $desdobramentoB;
                    $arBasesAtributo[$codigoB.$desdobramentoB][0] += $valorB;
                    $arBasesAtributo[$codigoB.$desdobramentoB][1] += $quantidadeB;
                    $arBasesAtributo[$codigoB.$desdobramentoB][2]  = $arBaseAtributo;
                    $arBasesAtributo[$codigoB.$desdobramentoB][3]++;
                }
            }

            if ($inPagina > 0) {
                $inPagina = $fpdf->PageNo() + 1;
                $fpdf->page = $inPagina;
                $inPagina = 0;
            }

        }

        $stLotacao  = $rsContratosCalculados->getCampo('descricao_lotacao');
        $stLocal    = $rsContratosCalculados->getCampo('descricao_local');
        $stRegime   = $rsContratosCalculados->getCampo('descricao_regime_funcao');
        $stFuncao   = $rsContratosCalculados->getCampo('descricao_funcao');
        $stCGM      = $rsContratosCalculados->getCampo('nom_cgm');
        $stRegistro = $rsContratosCalculados->getCampo('registro');
        $stAtributo = $rsContratosCalculados->getCampo('valor');
        $stBanco    = $rsContratosCalculados->getCampo('descricao_banco');
        $nuBanco    = $rsContratosCalculados->getCampo('num_banco');
        $rsContratosCalculados->proximo();

        if ($stRegistro != $rsContratosCalculados->getCampo('registro')) {
            $inLinha = $arLinhasTMP[COUNT($arLinhasTMP)-1];

            if ($inLinha < 37) {
                $inLinha = 37;
            }

            if (!$arFiltro['boEmitirRelatorio']) {
                $inLinha = incrementaLinha($fpdf,$inLinha);
            }

            if ($arFiltro['boEmitirTotais']) {
                if ($arFiltro['boCgm'] and $stCGM != $rsContratosCalculados->getCampo('nom_cgm')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boCgm              = true;
                }
                if ($arFiltro['boFuncao'] and $arFiltro['boEmitirTotais'] and $stFuncao != $rsContratosCalculados->getCampo('descricao_funcao')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boFuncao           = true;
                }
                if ($arFiltro['boRegimedaFuncao'] and $stRegime != $rsContratosCalculados->getCampo('descricao_regime_funcao')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boRegimedaFuncao   = true;
                }
                if ($arFiltro['boLocal'] and $stLocal != $rsContratosCalculados->getCampo('descricao_local')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boLocal            = true;
                }
                if ($arFiltro['boLotacao'] and $stLotacao != $rsContratosCalculados->getCampo('descricao_lotacao')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boLotacao          = true;
                }
                if ($arFiltro['boBanco'] and $stBanco != $rsContratosCalculados->getCampo('descricao_banco')) {
                    Sessao::write('boTotalAgrupamento', true);
                    $boBanco            = true;
                }
            }

            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(17,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                $fpdf->SetXY(82,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                $inIncremento = 96;
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                $fpdf->SetXY(82+$inIncremento,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                $inIncremento = 97*2;
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                $fpdf->SetXY(82+$inIncremento,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);
                $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                $inLinha = incrementaLinha($fpdf,$inLinha);
            }

            if ($arFiltro['boAtributoDinamico'] and $arFiltro['boEmitirTotais'] and $stAtributo != $rsContratosCalculados->getCampo('valor')) {
                $fpdf->SetFillColor(245,245,245);
                $fpdf->SetXY(7,$inLinha);

                $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
                $obTAdministracaoAtributoValorPadrao->setDado('cod_modulo'  , 22);
                $obTAdministracaoAtributoValorPadrao->setDado('cod_cadastro', $arFiltro['inCodCadastro']);
                $obTAdministracaoAtributoValorPadrao->setDado('cod_atributo', $arFiltro['inCodAtributo']);
                $obTAdministracaoAtributoValorPadrao->setDado('cod_valor'   , $stAtributo);
                $obTAdministracaoAtributoValorPadrao->recuperaPorChave($rsValorPadrao);
                $stAtributo = ( $rsValorPadrao->getNumLinhas() == 1 ) ? $rsValorPadrao->getCampo('valor_padrao') : $stAtributo;

                $fpdf->SetFont('Helvetica','B',8);
                $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR ATRIBUTO DINÂMICO: '.$stAtributo ,0,0,'C',1);
                $fpdf->SetFont('Helvetica','',8);

                $fpdf->SetFillColor(255,255,255);
                $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                $inLinha = incrementaLinha($fpdf,$inLinha);
                $nuTotalProventos = 0;
                $nuTotalDescontos = 0;
                ksort($arProventosAtributo);
                ksort($arDescontosAtributo);
                ksort($arBasesAtributo);
                $arTotalEvento = agruparEventos($arProventosAtributo,$arDescontosAtributo,$arBasesAtributo);
                foreach ($arTotalEvento as $arLinha) {
                    $nuTotalProventos += $arLinha['valorp'];
                    $nuTotalDescontos += $arLinha['valord'];
                    $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                }
                $arProventosAtributo = array();
                $arDescontosAtributo = array();
                $arBasesAtributo     = array();

                $fpdf->SetXY(17,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                $fpdf->SetXY(82,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                $inIncremento = 96;
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                $fpdf->SetXY(82+$inIncremento,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                $inIncremento = 97*2;
                $fpdf->SetXY(17+$inIncremento,$inLinha);
                $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                $fpdf->SetXY(82+$inIncremento,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                $inLinha = incrementaLinha($fpdf,$inLinha);
                $fpdf->SetXY(7,$inLinha);
                $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContAtributo ,0,0,'L',1);
                $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                $inLinha = incrementaLinha($fpdf,$inLinha);

                $inContAtributo = 0;

                $boQuebra = true;
            }

            if ($arFiltro['boEmitirTotais']) {

                foreach ($arFiltro['arrayOrdenacao'] as $stPosicaoOrdenacao => $value) {
                    if (${ordenacaoPosicaoAnterior($stPosicaoOrdenacao)}) {
                        $$stPosicaoOrdenacao = true;
                    }
                }

                if ($boCgm) {                    
                    $inLinha = addTipoFolha($fpdf,$inLinha);                    
                    $fpdf->SetFillColor(245,245,245);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->SetFont('Helvetica','B',8);
                    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR CGM: '.$stCGM ,0,0,'C',1);
                    $fpdf->SetFont('Helvetica','',8);

                    $fpdf->SetFillColor(255,255,255);
                    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                    $inLinha = incrementaLinha($fpdf,$inLinha);
                    $nuTotalProventos = 0;
                    $nuTotalDescontos = 0;
                    ksort($arProventosCGM);
                    ksort($arDescontosCGM);
                    ksort($arBasesCGM);
                    $arTotalEvento = agruparEventos($arProventosCGM,$arDescontosCGM,$arBasesCGM);
                    foreach ($arTotalEvento as $arLinha) {
                        $nuTotalProventos += $arLinha['valorp'];
                        $nuTotalDescontos += $arLinha['valord'];
                        $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                    }
                    $arProventosCGM = array();
                    $arDescontosCGM = array();
                    $arBasesCGM     = array();

                    $fpdf->SetXY(17,$inLinha);
                    $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                    $fpdf->SetXY(82,$inLinha);
                    $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                    $inIncremento = 96;
                    $fpdf->SetXY(17+$inIncremento,$inLinha);
                    $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                    $fpdf->SetXY(82+$inIncremento,$inLinha);
                    $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                    $inIncremento = 97*2;
                    $fpdf->SetXY(17+$inIncremento,$inLinha);
                    $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                    $fpdf->SetXY(82+$inIncremento,$inLinha);
                    $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                    $inLinha = incrementaLinha($fpdf,$inLinha);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContCGM ,0,0,'L',1);
                    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $inContCGM = 0;

                    $boQuebra = true;
                    $boCgm = false;
                }

                foreach (array_reverse($arFiltro['arrayOrdenacao'] ) as $stPosicaoOrdenacao => $value) {
                    if ($stPosicaoOrdenacao == 'boFuncao' && $boFuncao) {
                        if ($inLinha >= 56) {
                            $inLinha = incrementaLinha($fpdf,999);
                        }
                        $inLinha = addTipoFolha($fpdf,$inLinha);
                        $fpdf->SetFillColor(245,245,245);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->SetFont('Helvetica','B',8);
                        $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR FUNÇÃO: '.$stFuncao ,0,0,'C',1);
                        $fpdf->SetFont('Helvetica','',8);

                        $fpdf->SetFillColor(255,255,255);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $nuTotalProventos = 0;
                        $nuTotalDescontos = 0;
                        ksort($arProventosFuncao);
                        ksort($arDescontosFuncao);
                        ksort($arBasesFuncao);
                        $arTotalEvento = agruparEventos($arProventosFuncao,$arDescontosFuncao,$arBasesFuncao);
                        foreach ($arTotalEvento as $arLinha) {
                            $nuTotalProventos += $arLinha['valorp'];
                            $nuTotalDescontos += $arLinha['valord'];
                            $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                        }
                        $arProventosFuncao = array();
                        $arDescontosFuncao = array();
                        $arBasesFuncao     = array();

                        $fpdf->SetXY(17,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 96;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 97*2;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContFuncao ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $inContFuncao = 0;

                        $boQuebra = true;
                        $boFuncao = false;
                        Sessao::write('boTotalAgrupamento', false);
                    }
                    if ($stPosicaoOrdenacao == 'boRegimedaFuncao' && $boRegimedaFuncao) {
                        if ($inLinha >= 56) {
                            $inLinha = incrementaLinha($fpdf,999);
                        }
                        $inLinha = addTipoFolha($fpdf,$inLinha);
                        $fpdf->SetFillColor(245,245,245);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->SetFont('Helvetica','B',8);
                        $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR REGIME: '.$stRegime ,0,0,'C',1);
                        $fpdf->SetFont('Helvetica','',8);

                        $fpdf->SetFillColor(255,255,255);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $nuTotalProventos = 0;
                        $nuTotalDescontos = 0;
                        ksort($arProventosRegime);
                        ksort($arDescontosRegime);
                        ksort($arBasesRegime);
                        $arTotalEvento = agruparEventos($arProventosRegime,$arDescontosRegime,$arBasesRegime);
                        foreach ($arTotalEvento as $arLinha) {
                            $nuTotalProventos += $arLinha['valorp'];
                            $nuTotalDescontos += $arLinha['valord'];
                            $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                        }
                        $arProventosRegime = array();
                        $arDescontosRegime = array();
                        $arBasesRegime     = array();

                        $fpdf->SetXY(17,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 96;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 97*2;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContRegime ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $inContRegime = 0;

                        $boQuebra = true;
                        $boRegimedaFuncao = false;
                        Sessao::write('boTotalAgrupamento', false);
                    }
                    if ($stPosicaoOrdenacao == 'boLocal' && $boLocal) {
                        if ($inLinha >= 56) {
                            $inLinha = incrementaLinha($fpdf,999);
                        }
                        $inLinha = addTipoFolha($fpdf,$inLinha);
                        $fpdf->SetFillColor(245,245,245);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->SetFont('Helvetica','B',8);
                        $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR LOCAL: '.$stLocal ,0,0,'C',1);
                        $fpdf->SetFont('Helvetica','',8);

                        $fpdf->SetFillColor(255,255,255);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $nuTotalProventos = 0;
                        $nuTotalDescontos = 0;
                        ksort($arProventosLocal);
                        ksort($arDescontosLocal);
                        ksort($arBasesLocal);
                        $arTotalEvento = agruparEventos($arProventosLocal,$arDescontosLocal,$arBasesLocal);
                        foreach ($arTotalEvento as $arLinha) {
                            $nuTotalProventos += $arLinha['valorp'];
                            $nuTotalDescontos += $arLinha['valord'];
                            $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                        }
                        $arProventosLocal = array();
                        $arDescontosLocal = array();
                        $arBasesLocal     = array();

                        $fpdf->SetXY(17,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 96;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 97*2;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContLocal ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $inContLocal = 0;

                        $boQuebra = true;
                        $boLocal = false;
                        Sessao::write('boTotalAgrupamento', false);
                    }
                    if ($stPosicaoOrdenacao == 'boLotacao' && $boLotacao) {
                        if ($inLinha >= 56) {
                            $inLinha = incrementaLinha($fpdf,999);
                        } else {
                            $inLinha = addTipoFolha($fpdf,$inLinha);
                            $inLinha = 40;
                        }
                        $fpdf->SetFillColor(245,245,245);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->SetFont('Helvetica','B',8);
                        $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR LOTAÇÃO: '.$stLotacao ,0,0,'C',1);
                        $fpdf->SetFont('Helvetica','',8);

                        $fpdf->SetFillColor(255,255,255);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $nuTotalProventos = 0;
                        $nuTotalDescontos = 0;
                        ksort($arProventosLotacao);
                        ksort($arDescontosLotacao);
                        ksort($arBasesLotacao);
                        $arTotalEvento = agruparEventos($arProventosLotacao,$arDescontosLotacao,$arBasesLotacao);
                        foreach ($arTotalEvento as $arLinha) {
                            $nuTotalProventos += $arLinha['valorp'];
                            $nuTotalDescontos += $arLinha['valord'];
                            $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                        }
                        $arProventosLotacao = array();
                        $arDescontosLotacao = array();
                        $arBasesLotacao     = array();

                        $fpdf->SetXY(17,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 96;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 97*2;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContLotacao ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $inContLotacao = 0;

                        $boQuebra = true;
                        $boLotacao = false;
                        Sessao::write('boTotalAgrupamento', false);
                    }
                    if ($stPosicaoOrdenacao == 'boBanco' && $boBanco) {
                        if ($inLinha >= 56) {
                            $inLinha = incrementaLinha($fpdf,999);
                        }
                        $inLinha = addTipoFolha($fpdf,$inLinha);
                        $fpdf->SetFillColor(245,245,245);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->SetFont('Helvetica','B',8);
                        $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR BANCO: $nuBanco - '.$stBanco ,0,0,'C',1);
                        $fpdf->SetFont('Helvetica','',8);

                        $fpdf->SetFillColor(255,255,255);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $nuTotalProventos = 0;
                        $nuTotalDescontos = 0;
                        ksort($arProventosBanco);
                        ksort($arDescontosBanco);
                        ksort($arBasesBanco);
                        $arTotalEvento = agruparEventos($arProventosBanco,$arDescontosBanco,$arBasesBanco);
                        foreach ($arTotalEvento as $arLinha) {
                            $nuTotalProventos += $arLinha['valorp'];
                            $nuTotalDescontos += $arLinha['valord'];
                            $inLinha = addLinhaTotal($fpdf,$inLinha,$arLinha);
                        }
                        $arProventosBanco = array();
                        $arDescontosBanco = array();
                        $arBasesBanco     = array();

                        $fpdf->SetXY(17,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 96;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $inIncremento = 97*2;
                        $fpdf->SetXY(17+$inIncremento,$inLinha);
                        $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO' ,0,0,'L',1);
                        $fpdf->SetXY(82+$inIncremento,$inLinha);
                        $fpdf->Cell(15,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Total de Servidores: '.$inContBanco ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $fpdf->SetXY(7,$inLinha);
                        $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
                        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
                        $inLinha = incrementaLinha($fpdf,$inLinha);

                        $inContBanco = 0;

                        $boQuebra = true;
                        $boBanco = false;
                        Sessao::write('boTotalAgrupamento', false);
                    }
                }//end foreach array_reverse arrayOrdenacao
            }//end if boEmitirTotais

            if ($boQuebra) {
                #$inLinha = addCabecalho($fpdf);
                if ($rsContratosCalculados->getCorrente() >= ($rsContratosCalculados->getNumLinhas()-1)) {
                    $inLinha = incrementaLinha($fpdf, 999);  //sem cabeçalho
                } else {
                    $inLinha = incrementaLinha($fpdf, 185);  //com cabeçalho
                }
            }
        }
    }
    
    $inLinha = addTipoFolha($fpdf,$inLinha);
    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->Cell(283,$inAlturaLinha, 'RESUMO GERAL' ,0,0,'C',1);
    $fpdf->SetFont('Helvetica','',8);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    //Sefor false faz o cabelho
    //Para evitar que faca repetido os dados caso for geral e emitir relatorio com totais
    if (!$boCabecalhoTotaisGerais) {
        $fpdf->SetFillColor(255,255,255);
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'PROVENTOS' ,0,0,'C',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'DESCONTOS' ,0,0,'C',1);
        $fpdf->SetXY(193,$inLinha);
        $fpdf->Cell(93,$inAlturaLinha, 'BASES/INFORMATIVOS' ,0,0,'C',1);
        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
        $inLinha = incrementaLinha($fpdf,$inLinha);    
    }

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(10,$inAlturaLinha, 'COD' ,0,0,'L',1);
    $fpdf->SetXY(17,$inLinha);
    $fpdf->Cell(49,$inAlturaLinha, 'DESCRIÇÃO' ,0,0,'L',1);
    $fpdf->SetXY(66,$inLinha);
    $fpdf->Cell(1,$inAlturaLinha, '' ,0,0,'L',1);
    $fpdf->SetXY(67,$inLinha);
    $fpdf->Cell(12,$inAlturaLinha, 'OCORRÊNCIAS' ,0,0,'R',1);
    $fpdf->SetXY(79,$inLinha);
    $fpdf->Cell(18,$inAlturaLinha, 'VALOR' ,0,0,'R',1);

    $inIncremento = 96;
    $fpdf->SetXY(7+$inIncremento,$inLinha);
    $fpdf->Cell(10,$inAlturaLinha, 'COD' ,0,0,'L',1);
    $fpdf->SetXY(17+$inIncremento,$inLinha);
    $fpdf->Cell(49,$inAlturaLinha, 'DESCRIÇÃO' ,0,0,'L',1);
    $fpdf->SetXY(66+$inIncremento,$inLinha);
    $fpdf->Cell(1,$inAlturaLinha, '' ,0,0,'L',1);
    $fpdf->SetXY(67+$inIncremento,$inLinha);
    $fpdf->Cell(12,$inAlturaLinha, 'OCORRÊNCIAS' ,0,0,'R',1);
    $fpdf->SetXY(79+$inIncremento,$inLinha);
    $fpdf->Cell(18,$inAlturaLinha, 'VALOR' ,0,0,'R',1);

    $inIncremento = 97*2;
    $fpdf->SetXY(7+$inIncremento,$inLinha);
    $fpdf->Cell(10,$inAlturaLinha, 'COD' ,0,0,'L',1);
    $fpdf->SetXY(17+$inIncremento,$inLinha);
    $fpdf->Cell(49,$inAlturaLinha, 'DESCRIÇÃO' ,0,0,'L',1);
    $fpdf->SetXY(66+$inIncremento,$inLinha);
    $fpdf->Cell(1,$inAlturaLinha, '' ,0,0,'L',1);
    $fpdf->SetXY(67+$inIncremento,$inLinha);
    $fpdf->Cell(12,$inAlturaLinha, 'OCORRÊNCIAS' ,0,0,'R',1);
    $fpdf->SetXY(79+$inIncremento,$inLinha);
    $fpdf->Cell(18,$inAlturaLinha, 'VALOR' ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $nuTotalProventos = 0;
    $nuTotalDescontos = 0;

    // verifica se recebeu dados na competencia selecionada, se nao receber dados gerar relatorio com valor zero(branco) ao invez de gerar erro
    // Não deve considerar as bases
    if(empty($arBases)){
        $arBases = array();
    }else{
        ksort($arBases);
    }
    if(empty($arProventos)){
        $arProventos = array();
    }else{
        ksort($arProventos);
    }
    if(empty($arDescontos)){
        $arDescontos = array();
    }else{
        ksort($arDescontos);
    }

    $arTotalEvento = agruparEventos($arProventos,$arDescontos,$arBases);
    foreach ($arTotalEvento as $arLinha) {
        if ($arLinha['codigop'] != '') {
            $nuTotalProventos += $arLinha['valorp'];
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigop'] ,0,0,'L',1);
            $fpdf->SetXY(17,$inLinha);
            $fpdf->Cell(49,$inAlturaLinha, $arLinha['descricaop'] ,0,0,'L',1);
            $fpdf->SetXY(66,$inLinha);
            $fpdf->Cell(1,$inAlturaLinha, $arLinha['desdobramentop'] ,0,0,'R',1);
            $fpdf->SetXY(67,$inLinha);
            $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciap'] ,0,0,'R',1);
            $fpdf->SetXY(79,$inLinha);
            $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valorp'],2,',','.') ,0,0,'R',1);
        }
        if ($arLinha['codigod'] != '') {
            $nuTotalDescontos += $arLinha['valord'];
            $inIncremento = 96;
            $fpdf->SetXY(7+$inIncremento,$inLinha);
            $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigod'] ,0,0,'L',1);
            $fpdf->SetXY(17+$inIncremento,$inLinha);
            $fpdf->Cell(49,$inAlturaLinha, $arLinha['descricaod'] ,0,0,'L',1);
            $fpdf->SetXY(66+$inIncremento,$inLinha);
            $fpdf->Cell(1,$inAlturaLinha, $arLinha['desdobramentod'] ,0,0,'R',1);
            $fpdf->SetXY(67+$inIncremento,$inLinha);
            $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciad'] ,0,0,'R',1);
            $fpdf->SetXY(79+$inIncremento,$inLinha);
            $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valord'],2,',','.') ,0,0,'R',1);
        }
        if ($arLinha['codigob'] != '') {
            $nuTotalBase += $arLinha['valorb'];
            $inIncremento = 97*2;
            $fpdf->SetXY(7+$inIncremento,$inLinha);
            $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigob'] ,0,0,'L',1);
            $fpdf->SetXY(17+$inIncremento,$inLinha);
            $fpdf->Cell(49,$inAlturaLinha, $arLinha['descricaob'] ,0,0,'L',1);
            $fpdf->SetXY(66+$inIncremento,$inLinha);
            $fpdf->Cell(1,$inAlturaLinha, $arLinha['desdobramentob'] ,0,0,'R',1);
            $fpdf->SetXY(67+$inIncremento,$inLinha);
            $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciab'] ,0,0,'R',1);
            $fpdf->SetXY(79+$inIncremento,$inLinha);
            $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valorb'],2,',','.') ,0,0,'R',1);
        }
        if ($inLinha >= 180) {
            $inLinha = 999;
        }
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE PROVENTOS: ' ,0,0,'L',1);
    $fpdf->SetXY(45,$inLinha);
    $fpdf->Cell(17,$inAlturaLinha, number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);

    $inIncremento = 82;
    $fpdf->SetXY($inIncremento,$inLinha);
    $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE DESCONTOS:' ,0,0,'L',1);
    $fpdf->SetXY(35+$inIncremento,$inLinha);
    $fpdf->Cell(17,$inAlturaLinha, number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);

    $inIncremento = 154;
    $fpdf->SetXY($inIncremento,$inLinha);
    $fpdf->Cell(38,$inAlturaLinha, 'TOTAL DE SERVIDORES:' ,0,0,'L',1);
    $fpdf->SetXY(38+$inIncremento,$inLinha);
    $fpdf->Cell(17,$inAlturaLinha, $inTotalContratos);

    $inIncremento = 216;
    $fpdf->SetXY($inIncremento,$inLinha);
    $fpdf->Cell(38,$inAlturaLinha, 'LÍQUIDO:' ,0,0,'L',1);
    $fpdf->SetXY(15+$inIncremento,$inLinha);
    $fpdf->Cell(17,$inAlturaLinha, number_format($nuTotalProventos-$nuTotalDescontos,2,',','.') ,0,0,'R',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
}

function addLinhaTotal(&$fpdf,$inLinha,$arLinha)
{
    global $inAlturaLinha;
    if ($arLinha['codigop'] != '') {
        $nuTotalProventos += $arLinha['valorp'];
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigop'] ,0,0,'L',1);
        $fpdf->SetXY(17,$inLinha);
        $fpdf->Cell(35,$inAlturaLinha, $arLinha['descricaop'] ,0,0,'L',1);
        $fpdf->SetXY(52,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arLinha['desdobramentop'] ,0,0,'L',1);
        $fpdf->SetXY(67,$inLinha);
        $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciap'] ,0,0,'R',1);
        $fpdf->SetXY(79,$inLinha);
        $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valorp'],2,',','.') ,0,0,'R',1);
    }
    if ($arLinha['codigod'] != '') {
        $nuTotalDescontos += $arLinha['valord'];
        $inIncremento = 96;
        $fpdf->SetXY(7+$inIncremento,$inLinha);
        $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigod'] ,0,0,'L',1);
        $fpdf->SetXY(17+$inIncremento,$inLinha);
        $fpdf->Cell(35,$inAlturaLinha, $arLinha['descricaod'] ,0,0,'L',1);
        $fpdf->SetXY(52+$inIncremento,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arLinha['desdobramentod'] ,0,0,'L',1);
        $fpdf->SetXY(67+$inIncremento,$inLinha);
        $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciad'] ,0,0,'R',1);
        $fpdf->SetXY(79+$inIncremento,$inLinha);
        $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valord'],2,',','.') ,0,0,'R',1);
    }
    if ($arLinha['codigob'] != '') {
        $nuTotalBase += $arLinha['valorb'];
        $inIncremento = 97*2;
        $fpdf->SetXY(7+$inIncremento,$inLinha);
        $fpdf->Cell(10,$inAlturaLinha, $arLinha['codigob'] ,0,0,'L',1);
        $fpdf->SetXY(17+$inIncremento,$inLinha);
        $fpdf->Cell(35,$inAlturaLinha, $arLinha['descricaob'] ,0,0,'L',1);
        $fpdf->SetXY(52+$inIncremento,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arLinha['desdobramentob'] ,0,0,'L',1);
        $fpdf->SetXY(67+$inIncremento,$inLinha);
        $fpdf->Cell(12,$inAlturaLinha, $arLinha['ocorrenciab'] ,0,0,'R',1);
        $fpdf->SetXY(79+$inIncremento,$inLinha);
        $fpdf->Cell(18,$inAlturaLinha, number_format($arLinha['valorb'],2,',','.') ,0,0,'R',1);
    }
    $inLinha = incrementaLinha($fpdf,$inLinha);

    return $inLinha;
}

function agruparEventos($arProventos,$arDescontos,$arBases)
{
    global $arFiltro;
    global $inAlturaLinha;
    $arProventos = ( is_array($arProventos) ) ? $arProventos : array();
    $arDescontos = ( is_array($arDescontos) ) ? $arDescontos : array();
    $arBases     = ( is_array($arBases) )     ? $arBases     : array();
    $arTotalEventos = array();
    $inContador = count($arProventos);
    $inContador = ( count($arDescontos) > $inContador ) ? count($arDescontos) : $inContador;
    $inContador = ( count($arBases) > $inContador ) ? count($arBases) : $inContador;

    foreach ($arProventos as $arProvento) {
        $arLinha['codigop']         = $arProvento[2][0];
        $arLinha['descricaop']      = $arProvento[2][1];
        $arLinha['desdobramentop']  = $arProvento[2][2];
        $arLinha['valorp']          = $arProvento[0];
        $arLinha['quantidadep']     = $arProvento[1];
        $arLinha['ocorrenciap']     = $arProvento[3];
        $arTotalEventos[] = $arLinha;
    }
    $inIndex = 0;
    foreach ($arDescontos as $arDesconto) {
        $arTotalEventos[$inIndex]['codigod']         = $arDesconto[2][0];
        $arTotalEventos[$inIndex]['descricaod']      = $arDesconto[2][1];
        $arTotalEventos[$inIndex]['desdobramentod']  = $arDesconto[2][2];
        $arTotalEventos[$inIndex]['valord']          = $arDesconto[0];
        $arTotalEventos[$inIndex]['quantidaded']     = $arDesconto[1];
        $arTotalEventos[$inIndex]['ocorrenciad']     = $arDesconto[3];
        $inIndex++;
    }
    $inIndex = 0;
    foreach ($arBases as $arBase) {
        $arTotalEventos[$inIndex]['codigob']         = $arBase[2][0];
        $arTotalEventos[$inIndex]['descricaob']      = $arBase[2][1];
        $arTotalEventos[$inIndex]['desdobramentob']  = $arBase[2][2];
        $arTotalEventos[$inIndex]['valorb']          = $arBase[0];
        $arTotalEventos[$inIndex]['quantidadeb']     = $arBase[1];
        $arTotalEventos[$inIndex]['ocorrenciab']     = $arBase[3];
        $inIndex++;
    }

    return $arTotalEventos;
}

function agruparEventosAnalitica($arEventosE,$arEventosD)
{
    global $arFiltro;
    global $inAlturaLinha;
    $arEventosE = ( is_array($arEventosE) ) ? $arEventosE : array();
    $arEventosD = ( is_array($arEventosD) ) ? $arEventosD : array();
    $arTotalEventos = array();
    $inContador = count($arEventosE);
    $inContador = ( count($arEventosD) > $inContador ) ? count($arEventosD) : $inContador;

    foreach ($arEventosE as $arProvento) {
        $arLinha['codigoe']         = $arProvento[2][0];
        $arLinha['descricaoe']      = $arProvento[2][1];
        $arLinha['desdobramentoe']  = $arProvento[2][2];
        $arLinha['valore']          = $arProvento[0];
        $arLinha['quantidadee']     = $arProvento[1];
        $arTotalEventos[] = $arLinha;
    }
    $inIndex = 0;
    foreach ($arEventosD as $arDesconto) {
        $arTotalEventos[$inIndex]['codigod']         = $arDesconto[2][0];
        $arTotalEventos[$inIndex]['descricaod']      = $arDesconto[2][1];
        $arTotalEventos[$inIndex]['desdobramentod']  = $arDesconto[2][2];
        $arTotalEventos[$inIndex]['valord']          = $arDesconto[0];
        $arTotalEventos[$inIndex]['quantidaded']     = $arDesconto[1];
        $inIndex++;
    }

    return $arTotalEventos;
}

function addCabecalhoRelatorioSintetica(&$fpdf,$inLinha)
{
    global $inAlturaLinha;
    $inLinha  = addTipoFolha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(13,$inAlturaLinha, 'Contrato' ,0,0,'L',1);
    $fpdf->SetXY(20,$inLinha);
    $fpdf->Cell(60,$inAlturaLinha, 'CGM' ,0,0,'L',1);
    $fpdf->SetXY(80,$inLinha);
    $fpdf->Cell(80,$inAlturaLinha, 'Lotação' ,0,0,'L',1);

    $fpdf->SetXY(145,$inLinha);
    $fpdf->Cell(30,$inAlturaLinha, 'Banco' ,0,0,'L',1);

    $fpdf->SetXY(190,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, 'Proventos' ,0,0,'R',1);
    $fpdf->SetXY(210,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, 'Descontos' ,0,0,'R',1);
    $fpdf->SetXY(230,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, 'Previdência' ,0,0,'R',1);
    $fpdf->SetXY(250,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, 'IRRF' ,0,0,'R',1);
    $fpdf->SetXY(270,$inLinha);
    $fpdf->Cell(20,$inAlturaLinha, 'Líquido' ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    return $inLinha;
}

function sintetica(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;
    if (!$arFiltro['boEmitirRelatorio']) {
        $inLinha  = addCabecalhoRelatorioSintetica($fpdf,$inLinha);
    }

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $inMes = ( $arFiltro['inCodMes'] < 10 ) ? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $dtCompetencia = $inMes.'/'.$arFiltro['inAno'];
    $stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);

    $obFFolhaPagamentoFolhaAnaliticaResumida = new FFolhaPagamentoFolhaAnaliticaResumida();
    $stFiltro = recuperaFiltro($obFFolhaPagamentoFolhaAnaliticaResumida, $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
    $stOrdenacao = recuperaOrdenacao();

    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', 0);
        $inCodComplementar  = ( $arFiltro['inCodComplementar'] ) ? $arFiltro['inCodComplementar'] : 0;
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar', $inCodComplementar);
    } else {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', $arFiltro['inCodConfiguracao']);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar', 0);
    }
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('filtro',    $stFiltro);
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao', $stOrdenacao);
    $obFFolhaPagamentoFolhaAnaliticaResumida->folhaSintetica($rsContratosCalculados);

    $legenda = montaLegenda($arFiltro['inCodConfiguracao']);

    $nuTotalPrevidencia = 0;
    $nuTotalIRRF        = 0;
    $nuTotalProventos   = 0;
    $nuTotalDescontos   = 0;
    $nuTotalLiquido     = 0;
    $inTotalContratos   = ($rsContratosCalculados->getNumLinhas() == -1 ) ? 0 : $rsContratosCalculados->getNumLinhas();

    $nuTotalPrevidenciaBanco = 0;
    $nuTotalIRRFBanco        = 0;
    $nuTotalProventosBanco   = 0;
    $nuTotalDescontosBanco   = 0;
    $nuTotalLiquidoBanco     = 0;
    $inTotalContratosBanco   = 0;

    $nuTotalPrevidenciaLotacao = 0;
    $nuTotalIRRFLotacao        = 0;
    $nuTotalProventosLotacao   = 0;
    $nuTotalDescontosLotacao   = 0;
    $nuTotalLiquidoLotacao     = 0;
    $inTotalContratosLotacao   = 0;

    $nuTotalPrevidenciaCGM = 0;
    $nuTotalIRRFCGM        = 0;
    $nuTotalProventosCGM   = 0;
    $nuTotalDescontosCGM   = 0;
    $nuTotalLiquidoCGM     = 0;
    $inTotalContratosCGM   = 0;
    while (!$rsContratosCalculados->eof()) {
        $boQuebra = false;

        $nuPrevidencia = $rsContratosCalculados->getCampo('previdencia');
        $nuIRRF        = $rsContratosCalculados->getCampo('irrf');
        $nuProventos   = $rsContratosCalculados->getCampo('proventos');
        $nuDescontos   = $rsContratosCalculados->getCampo('descontos');
        $nuLiquido     = $rsContratosCalculados->getCampo('liquido');

        if (!$arFiltro['boEmitirRelatorio']) {
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(13,$inAlturaLinha, $rsContratosCalculados->getCampo('registro') ,0,0,'L',1);
            $fpdf->SetXY(20,$inLinha);
            $fpdf->Cell(60,$inAlturaLinha, $rsContratosCalculados->getCampo('numcgm').'-'.$rsContratosCalculados->getCampo('nom_cgm') ,0,0,'L',1);
            $fpdf->SetXY(80,$inLinha);
            $fpdf->Cell(80,$inAlturaLinha, $rsContratosCalculados->getCampo('orgao').'-'.$rsContratosCalculados->getCampo('descricao_lotacao') ,0,0,'L',1);
            $fpdf->SetXY(145,$inLinha);
            $fpdf->Cell(30,$inAlturaLinha, $rsContratosCalculados->getCampo('num_banco').'-'.$rsContratosCalculados->getCampo('descricao_banco') ,0,0,'L',1);

            $fpdf->SetXY(190,$inLinha);
            $fpdf->Cell(20,$inAlturaLinha, number_format($nuProventos,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(210,$inLinha);
            $fpdf->Cell(20,$inAlturaLinha, number_format($nuDescontos,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(230,$inLinha);
            $fpdf->Cell(20,$inAlturaLinha, number_format($nuPrevidencia,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(250,$inLinha);
            $fpdf->Cell(20,$inAlturaLinha, number_format($nuIRRF,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(270,$inLinha);
            $fpdf->Cell(20,$inAlturaLinha, number_format($nuLiquido,2,',','.') ,0,0,'R',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);
        }

        $nuTotalPrevidencia += $nuPrevidencia;;
        $nuTotalIRRF        += $nuIRRF;
        $nuTotalProventos   += $nuProventos;
        $nuTotalDescontos   += $nuDescontos;
        $nuTotalLiquido     += $nuLiquido;

        $nuTotalPrevidenciaBanco += $nuPrevidencia;
        $nuTotalIRRFBanco        += $nuIRRF;
        $nuTotalProventosBanco   += $nuProventos;
        $nuTotalDescontosBanco   += $nuDescontos;
        $nuTotalLiquidoBanco     += $nuLiquido;
        $inTotalContratosBanco++;

        $nuTotalPrevidenciaLotacao += $nuPrevidencia;
        $nuTotalIRRFLotacao        += $nuIRRF;
        $nuTotalProventosLotacao   += $nuProventos;
        $nuTotalDescontosLotacao   += $nuDescontos;
        $nuTotalLiquidoLotacao     += $nuLiquido;
        $inTotalContratosLotacao++;

        $nuTotalPrevidenciaCGM += $nuPrevidencia;
        $nuTotalIRRFCGM        += $nuIRRF;
        $nuTotalProventosCGM   += $nuProventos;
        $nuTotalDescontosCGM   += $nuDescontos;
        $nuTotalLiquidoCGM     += $nuLiquido;
        $inTotalContratosCGM++;

        $stLotacao = $rsContratosCalculados->getCampo('descricao_lotacao');
        $nuBanco   = $rsContratosCalculados->getCampo('num_banco');
        $stBanco   = $rsContratosCalculados->getCampo('descricao_banco');
        $stCGM     = $rsContratosCalculados->getCampo('nom_cgm');
        $rsContratosCalculados->proximo();

        if ($arFiltro['boCgm'] and $arFiltro['boEmitirTotais'] and $stCGM != $rsContratosCalculados->getCampo('nom_cgm')) {
            $fpdf->SetFillColor(245,245,245);
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR CGM: '.$stCGM ,0,0,'C',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            $fpdf->SetFillColor(255,255,255);
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido Previdência: '.number_format($nuTotalPrevidenciaCGM,2,',','.') ,0,0,'R',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido IRRF: '.number_format($nuTotalIRRFCGM,2,',','.') ,0,0,'R',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Proventos: '.number_format($nuTotalProventosCGM,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(107,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Descontos: '.number_format($nuTotalDescontosCGM,2,',','.') ,0,0,'R',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'Salário Líquido: '.number_format($nuTotalLiquidoCGM,2,',','.') ,0,0,'R',1);
            $fpdf->SetXY(107,$inLinha);
            $fpdf->Cell(100,$inAlturaLinha, 'N° Servidores: '.$inTotalContratosCGM ,0,0,'R',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            $boQuebra = true;
            $nuTotalPrevidenciaCGM = 0;
            $nuTotalIRRFCGM        = 0;
            $nuTotalProventosCGM   = 0;
            $nuTotalDescontosCGM   = 0;
            $nuTotalLiquidoCGM     = 0;
            $inTotalContratosCGM   = 0;
        }

        if ($arFiltro['boEmitirTotais']) {
            if ($arFiltro['boLotacao'] && $stLotacao != $rsContratosCalculados->getCampo('descricao_lotacao')) {
                $boLotacao = true;
            }

            if ($arFiltro['boBanco'] && $nuBanco != $rsContratosCalculados->getCampo('num_banco')) {
                $boBanco = true;
            }

            foreach ($arFiltro['arrayOrdenacao'] as $stPosicaoOrdenacao => $value) {
                if (${ordenacaoPosicaoAnterior($stPosicaoOrdenacao)}) {
                    $$stPosicaoOrdenacao = true;
                }
            }

            foreach (array_reverse($arFiltro['arrayOrdenacao'],true) as $stPosicaoOrdenacao => $value) {
                if ($stPosicaoOrdenacao == 'boLotacao' and $boLotacao) {
                    $fpdf->SetFillColor(245,245,245);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->SetFont('Helvetica','B',8);
                    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR LOTAÇÃO: '.$stLotacao ,0,0,'C',1);
                    $fpdf->SetFont('Helvetica','',8);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetFillColor(255,255,255);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido Previdência: '.number_format($nuTotalPrevidenciaLotacao,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido IRRF: '.number_format($nuTotalIRRFLotacao,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Proventos: '.number_format($nuTotalProventosLotacao,2,',','.') ,0,0,'R',1);
                    $fpdf->SetXY(107,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Descontos: '.number_format($nuTotalDescontosLotacao,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Salário Líquido: '.number_format($nuTotalLiquidoLotacao,2,',','.') ,0,0,'R',1);
                    $fpdf->SetXY(107,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'N° Servidores: '.$inTotalContratosLotacao ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $boLotacao= false;
                    $boQuebra = true;
                    $nuTotalPrevidenciaLotacao = 0;
                    $nuTotalIRRFLotacao        = 0;
                    $nuTotalProventosLotacao   = 0;
                    $nuTotalDescontosLotacao   = 0;
                    $nuTotalLiquidoLotacao     = 0;
                    $inTotalContratosLotacao   = 0;

                }

                if ($stPosicaoOrdenacao == 'boBanco' and $boBanco) {
                    $fpdf->SetFillColor(245,245,245);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->SetFont('Helvetica','B',8);
                    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR BANCO: '.$nuBanco.' - '.$stBanco ,0,0,'C',1);
                    $fpdf->SetFont('Helvetica','',8);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetFillColor(255,255,255);
                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido Previdência: '.number_format($nuTotalPrevidenciaBanco,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido IRRF: '.number_format($nuTotalIRRFBanco,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Proventos: '.number_format($nuTotalProventosBanco,2,',','.') ,0,0,'R',1);
                    $fpdf->SetXY(107,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Descontos: '.number_format($nuTotalDescontosBanco,2,',','.') ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $fpdf->SetXY(7,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'Salário Líquido: '.number_format($nuTotalLiquidoBanco,2,',','.') ,0,0,'R',1);
                    $fpdf->SetXY(107,$inLinha);
                    $fpdf->Cell(100,$inAlturaLinha, 'N° Servidores: '.$inTotalContratosBanco ,0,0,'R',1);
                    $inLinha = incrementaLinha($fpdf,$inLinha);

                    $boBanco  = false;
                    $boQuebra = true;
                    $nuTotalPrevidenciaBanco = 0;
                    $nuTotalIRRFBanco        = 0;
                    $nuTotalProventosBanco   = 0;
                    $nuTotalDescontosBanco   = 0;
                    $nuTotalLiquidoBanco     = 0;
                    $inTotalContratosBanco   = 0;
                }

            }//end foreach _array_reverse array_ordenacao
        }//end if boEmitirTotais

        if ($boQuebra) {
            $inLinha = addCabecalho($fpdf);
        }
    }

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL GERAL' ,0,0,'C',1);
    $fpdf->SetFont('Helvetica','',8);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido Previdência: '.number_format($nuTotalPrevidencia,2,',','.') ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'Soma Valor Recolhido IRRF: '.number_format($nuTotalIRRF,2,',','.') ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Proventos: '.number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);
    $fpdf->SetXY(107,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'Soma dos Descontos: '.number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'Salário Líquido: '.number_format($nuTotalLiquido,2,',','.') ,0,0,'R',1);
    $fpdf->SetXY(107,$inLinha);
    $fpdf->Cell(100,$inAlturaLinha, 'N° Servidores: '.$inTotalContratos ,0,0,'R',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $fpdf->SetXY(7 ,$inLinha);
    $fpdf->Cell( 283 ,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
    $fpdf->Line( 7  , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);
}

function addCabecalhoRelatorioAnalitica(&$fpdf,$inLinha)
{
    global $inAlturaLinha;
    $inLinha  = addTipoFolha($fpdf,$inLinha);

    return $inLinha;
}

function analitica(&$fpdf,$inLinha)
{
    global $arFiltro,$inAlturaLinha;
    if (!$arFiltro['boEmitirRelatorio']) {
        $inLinha  = addCabecalhoRelatorioAnalitica($fpdf,$inLinha);
    }

    $obTFolhaPagamentoPeriodoMovimentacao = new TFolhaPagamentoPeriodoMovimentacao();
    $inMes = ( $arFiltro['inCodMes'] < 10 ) ? '0'.$arFiltro['inCodMes'] : $arFiltro['inCodMes'];
    $dtCompetencia = $inMes.'/'.$arFiltro['inAno'];
    $stFiltroPeriodo = " AND to_char(dt_final,'mm/yyyy') = '".$dtCompetencia."'";
    $obTFolhaPagamentoPeriodoMovimentacao->recuperaPeriodoMovimentacao($rsPeriodoMovimentacao,$stFiltroPeriodo);

    $obFFolhaPagamentoFolhaAnaliticaResumida = new FFolhaPagamentoFolhaAnaliticaResumida();
    $stFiltro = recuperaFiltro($obFFolhaPagamentoFolhaAnaliticaResumida, $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
    $stOrdenacao = recuperaOrdenacao();

    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_periodo_movimentacao', $rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao'));
    if ($arFiltro['boFiltrarFolhaComplementar']) {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', 0);
        $inCodComplementar  = ( $arFiltro['inCodComplementar'] ) ? $arFiltro['inCodComplementar'] : 0;
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar',$inCodComplementar);
    } else {
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_configuracao', $arFiltro['inCodConfiguracao']);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar', 0);
    }
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('filtro'   , $stFiltro);
    $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao', $stOrdenacao);
    $obFFolhaPagamentoFolhaAnaliticaResumida->folhaAnalitica($rsContratosCalculados);
    $inTotalContratos   = ($rsContratosCalculados->getNumLinhas() == -1 ) ? 0 : $rsContratosCalculados->getNumLinhas();
    $legenda = montaLegenda($arFiltro['inCodConfiguracao']);

    $arProventos = array();
    $arDescontos = array();
    $arProventoLotacao = array();

    include_once CAM_GA_ADM_MAPEAMENTO.'TAdministracaoConfiguracao.class.php';
    $stCondicao =  ' WHERE cod_modulo = 22';
    $stCondicao .= "   AND exercicio = '".Sessao::getExercicio()."'";
    $stCondicao .= "   AND parametro = 'dtContagemInicial'";
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao();
    $obTAdministracaoConfiguracao->recuperaTodos($rsConfiguracao, $stCondicao);

    $stDescricaoDataInicioContagem = '';
    switch (trim($rsConfiguracao->getCampo('valor'))) {
        CASE 'dtAdmissao':  $stDescricaoDataInicioContagem = 'Data de Admissão'; break;
        CASE 'dtNomeacao':  $stDescricaoDataInicioContagem = 'Data de Nomeação'; break;
        CASE 'dtPosse':     $stDescricaoDataInicioContagem = 'Data de Posse';    break;
    }

    while (!$rsContratosCalculados->eof()) {
        $boQuebra = false;
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Contrato:',0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('registro') ,0,0,'L',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Regime:' ,0,0,'R',1);
        $fpdf->SetXY(115,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_regime') ,0,0,'L',1);
        $fpdf->SetXY(160,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Regime:' ,0,0,'R',1);
        $fpdf->SetXY(175,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_regime_funcao') ,0,0,'L',1);
        $fpdf->SetXY(240,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Padrão:' ,0,0,'R',1);
        $fpdf->SetXY(255,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_padrao') ,0,0,'L',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'CGM:' ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('numcgm').'-'.$rsContratosCalculados->getCampo('nom_cgm') ,0,0,'L',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Subdivisão:' ,0,0,'R',1);
        $fpdf->SetXY(115,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_sub_divisao') ,0,0,'L',1);
        $fpdf->SetXY(160,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Subdivisão:' ,0,0,'R',1);
        $fpdf->SetXY(175,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_sub_divisao_funcao') ,0,0,'L',1);
        $fpdf->SetXY(240,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Progressão:' ,0,0,'R',1);
        $fpdf->SetXY(255,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_progressao') ,0,0,'L',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Lotação:' ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('orgao').'-'.$rsContratosCalculados->getCampo('descricao_lotacao') ,0,0,'L',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Cargo:' ,0,0,'R',1);
        $fpdf->SetXY(115,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_cargo') ,0,0,'L',1);
        $fpdf->SetXY(160,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Função:' ,0,0,'R',1);
        $fpdf->SetXY(175,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_funcao') ,0,0,'L',1);
        $fpdf->SetXY(240,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $stDescricaoDataInicioContagem.':' ,0,0,'R',1);
        $fpdf->SetXY(255,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('dt_contagem_inicial') ,0,0,'L',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Local:' ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_local'),0,0,'L',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Especialidade:' ,0,0,'R',1);
        $fpdf->SetXY(115,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_especialidade') ,0,0,'L',1);
        $fpdf->SetXY(160,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Especialidade:' ,0,0,'R',1);
        $fpdf->SetXY(175,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('descricao_especialidade_funcao') ,0,0,'L',1);
        $fpdf->SetXY(240,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Multiplos Vínculos:' ,0,0,'R',1);
        $fpdf->SetXY(255,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('multiplos') ,0,0,'L',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'C.Horária:' ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('horas_mensais'),0,0,'L',1);
        $fpdf->SetXY(100,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Situação:' ,0,0,'R',1);
        $fpdf->SetXY(115,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('situacao') ,0,0,'L',1);
        $fpdf->SetXY(160,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Previd. Oficial:' ,0,0,'R',1);
        $fpdf->SetXY(175,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $rsContratosCalculados->getCampo('previdencia_oficial') ,0,0,'L',1);
        $fpdf->SetXY(240,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, 'Banco:' ,0,0,'R',1);
        $fpdf->SetXY(255,$inLinha);

        /* verifica tamanho da string de descricao do banco, para cortar enquanto ela no couber na caixa de descricao */
        $stDescricaoBanco = $rsContratosCalculados->getCampo('num_banco').' - '.$rsContratosCalculados->getCampo('descricao_banco');
        while ($fpdf->GetStringWidth($stDescricaoBanco) > 36) {
            $stDescricaoBanco = substr($stDescricaoBanco, 0, strlen($stDescricaoBanco)-1);
        }
        $fpdf->Cell(36,$inAlturaLinha,$stDescricaoBanco,0,0,'L',1);

        $inLinha = incrementaLinha($fpdf,$inLinha);

        if (!$arFiltro['boEmitirRelatorio']) {
            $fpdf->SetFillColor(245,245,245);
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(142,$inAlturaLinha, 'PROVENTOS' ,0,0,'C',1);
            $fpdf->SetXY(149,$inLinha);
            $fpdf->Cell(142,$inAlturaLinha, 'DESCONTOS' ,0,0,'C',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);
        }

        $fpdf->SetFillColor(255,255,255);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_contrato', $rsContratosCalculados->getCampo('cod_contrato'));
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao'   , $arFiltro['stOrdenacaoEventos']);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('naturezaE'   , 'P');
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('naturezaD'   , 'D');
        $obFFolhaPagamentoFolhaAnaliticaResumida->eventosCalculadosFolhaAnalitica($rsEventosCalculados);
        $nuTotalProventos = 0;
        $nuTotalDescontos = 0;
        while (!$rsEventosCalculados->eof()) {
            $codigoE        = $rsEventosCalculados->getCampo('codigoe');
            $descricaoE     = $rsEventosCalculados->getCampo('descricaoe');
            $desdobramentoE = $rsEventosCalculados->getCampo('desdobramentoe');
            $quantidadeE    = $rsEventosCalculados->getCampo('quantidadee');
            $valorE         = $rsEventosCalculados->getCampo('valore');

            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(7,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, $codigoE ,0,0,'R',1);
                $fpdf->SetXY(22,$inLinha);
                $fpdf->Cell(60,$inAlturaLinha, $descricaoE ,0,0,'L',1);
                $fpdf->SetXY(82,$inLinha);
                $fpdf->Cell(27,$inAlturaLinha, $desdobramentoE ,0,0,'L',1);
                $fpdf->SetXY(109,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($quantidadeE,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(129,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($valorE,2,',','.') ,0,0,'R',1);
            }
            $nuTotalProventos += $valorE;

            if ($codigoE != '') {
                $arProvento = '';
                $arProvento[] = $codigoE;
                $arProvento[] = $descricaoE;
                $arProvento[] = $desdobramentoE;
                $arProventos[$codigoE.$desdobramentoE][0] += $valorE;
                $arProventos[$codigoE.$desdobramentoE][1] += $quantidadeE;
                $arProventos[$codigoE.$desdobramentoE][2]  = $arProvento;
            }
            if ($arFiltro['boEmitirTotais'] and $codigoE != '') {
                if ($arFiltro['boBanco']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['banco'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['banco'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['banco'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boLotacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['lotacao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['lotacao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['lotacao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boLocal']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['local'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['local'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['local'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['regime_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['regime_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['regime_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['subdivisao_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['subdivisao_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['subdivisao_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['especialidade_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['especialidade_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['especialidade_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['regime_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['regime_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['regime_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['subdivisao_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['subdivisao_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['subdivisao_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['especialidade_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['especialidade_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['especialidade_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSituacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['situacao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['situacao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['situacao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boCgm']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['cgm'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['cgm'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['cgm'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparProventos['atributo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparProventos['atributo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparProventos['atributo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
            }

            $codigoD        = $rsEventosCalculados->getCampo('codigod');
            $descricaoD     = $rsEventosCalculados->getCampo('descricaod');
            $desdobramentoD = $rsEventosCalculados->getCampo('desdobramentod');
            $quantidadeD    = $rsEventosCalculados->getCampo('quantidaded');
            $valorD         = $rsEventosCalculados->getCampo('valord');

            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(149,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, $codigoD ,0,0,'R',1);
                $fpdf->SetXY(164,$inLinha);
                $fpdf->Cell(60,$inAlturaLinha, $descricaoD ,0,0,'L',1);
                $fpdf->SetXY(224,$inLinha);
                $fpdf->Cell(27,$inAlturaLinha, $desdobramentoD ,0,0,'L',1);
                $fpdf->SetXY(251,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($quantidadeD,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(271,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($valorD,2,',','.') ,0,0,'R',1);
                $inLinha = incrementaLinha($fpdf,$inLinha);
            }
            $nuTotalDescontos +=  $valorD;

            if ($codigoD != '') {
                $arDesconto = '';
                $arDesconto[] = $codigoD;
                $arDesconto[] = $descricaoD;
                $arDesconto[] = $desdobramentoD;
                $arDescontos[$codigoD.$desdobramentoD][0] += $valorD;
                $arDescontos[$codigoD.$desdobramentoD][1] += $quantidadeD;
                $arDescontos[$codigoD.$desdobramentoD][2]  = $arDesconto;
            }
            if ($arFiltro['boEmitirTotais'] and $codigoD != '') {
                if ($arFiltro['boBanco']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['banco'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['banco'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['banco'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boLotacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['lotacao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['lotacao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['lotacao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boLocal']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['local'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['local'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['local'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['regime_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['regime_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['regime_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['subdivisao_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['subdivisao_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['subdivisao_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['especialidade_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['especialidade_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['especialidade_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['regime_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['regime_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['regime_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['subdivisao_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['subdivisao_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['subdivisao_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['especialidade_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['especialidade_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['especialidade_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSituacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['situacao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['situacao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['situacao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boCgm']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['cgm'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['cgm'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['cgm'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparDescontos['atributo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparDescontos['atributo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparDescontos['atributo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
            }
            $rsEventosCalculados->proximo();
        }
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE PROVENTOS: '.number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(149,$inLinha);
        $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE DESCONTOS: '.number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(142,$inAlturaLinha, 'SALÁRIO LÍQUIDO: '.number_format(($nuTotalProventos-$nuTotalDescontos),2,',','.') ,0,0,'R',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);

        if (!$arFiltro['boEmitirRelatorio']) {
            $fpdf->SetFillColor(245,245,245);
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS DE BASE' ,0,0,'C',1);
            $fpdf->SetXY(149,$inLinha);
            $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS INFORMATIVOS' ,0,0,'C',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);
        }

        $fpdf->SetFillColor(255,255,255);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_contrato', $rsContratosCalculados->getCampo('cod_contrato'));
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('ordenacao'   , $arFiltro['stOrdenacaoEventos']);
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('naturezaE'   , 'B');
        $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('naturezaD'   , 'I');
        $obFFolhaPagamentoFolhaAnaliticaResumida->eventosCalculadosFolhaAnalitica($rsEventosCalculados);
        while (!$rsEventosCalculados->eof()) {
            $codigoE        = $rsEventosCalculados->getCampo('codigoe');
            $descricaoE     = $rsEventosCalculados->getCampo('descricaoe');
            $desdobramentoE = $rsEventosCalculados->getCampo('desdobramentoe');
            $quantidadeE    = $rsEventosCalculados->getCampo('quantidadee');
            $valorE         = $rsEventosCalculados->getCampo('valore');

            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(7,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, $codigoE ,0,0,'R',1);
                $fpdf->SetXY(22,$inLinha);
                $fpdf->Cell(60,$inAlturaLinha, $descricaoE ,0,0,'L',1);
                $fpdf->SetXY(82,$inLinha);
                $fpdf->Cell(27,$inAlturaLinha, $desdobramentoE ,0,0,'L',1);
                $fpdf->SetXY(109,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($quantidadeE,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(129,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($valorE,2,',','.') ,0,0,'R',1);
            }

            if ($codigoE) {
                $arBase = '';
                $arBase[] = $codigoE;
                $arBase[] = $descricaoE;
                $arBase[] = $desdobramentoE;
                $arBases[$codigoE.$desdobramentoE][0] += $valorE;
                $arBases[$codigoE.$desdobramentoE][1] += $quantidadeE;
                $arBases[$codigoE.$desdobramentoE][2]  = $arBase;
            }
            if ($arFiltro['boEmitirTotais'] and $codigoE != '') {
                if ($arFiltro['boBanco']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['banco'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['banco'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['banco'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boLotacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['lotacao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['lotacao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['lotacao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boLocal']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['local'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['local'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['local'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['regime_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['regime_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['regime_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['subdivisao_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['subdivisao_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['subdivisao_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['especialidade_cargo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['especialidade_cargo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['especialidade_cargo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['regime_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['regime_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['regime_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['subdivisao_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['subdivisao_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['subdivisao_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['especialidade_funcao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['especialidade_funcao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['especialidade_funcao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boSituacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['situacao'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['situacao'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['situacao'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boCgm']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['cgm'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['cgm'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['cgm'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arTemp = '';
                    $arTemp[] = $codigoE;
                    $arTemp[] = $descricaoE;
                    $arTemp[] = $desdobramentoE;
                    $arAgruparBases['atributo'][$codigoE.$desdobramentoE][0] += $valorE;
                    $arAgruparBases['atributo'][$codigoE.$desdobramentoE][1] += $quantidadeE;
                    $arAgruparBases['atributo'][$codigoE.$desdobramentoE][2]  = $arTemp;
                }
            }

            $codigoD        = $rsEventosCalculados->getCampo('codigod');
            $descricaoD     = $rsEventosCalculados->getCampo('descricaod');
            $desdobramentoD = $rsEventosCalculados->getCampo('desdobramentod');
            $quantidadeD    = $rsEventosCalculados->getCampo('quantidaded');
            $valorD         = $rsEventosCalculados->getCampo('valord');

            if (!$arFiltro['boEmitirRelatorio']) {
                $fpdf->SetXY(149,$inLinha);
                $fpdf->Cell(15,$inAlturaLinha, $codigoD ,0,0,'R',1);
                $fpdf->SetXY(164,$inLinha);
                $fpdf->Cell(60,$inAlturaLinha, $descricaoD ,0,0,'L',1);
                $fpdf->SetXY(224,$inLinha);
                $fpdf->Cell(27,$inAlturaLinha, $desdobramentoD ,0,0,'L',1);
                $fpdf->SetXY(251,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($quantidadeD,2,',','.') ,0,0,'R',1);
                $fpdf->SetXY(271,$inLinha);
                $fpdf->Cell(20,$inAlturaLinha, number_format($valorD,2,',','.') ,0,0,'R',1);
                $inLinha = incrementaLinha($fpdf,$inLinha);
            }

            if ($codigoD != '') {
                $arInformativo = '';
                $arInformativo[] = $codigoD;
                $arInformativo[] = $descricaoD;
                $arInformativo[] = $desdobramentoD;
                $arInformativos[$codigoD.$desdobramentoD][0] += $valorD;
                $arInformativos[$codigoD.$desdobramentoD][1] += $quantidadeD;
                $arInformativos[$codigoD.$desdobramentoD][2]  = $arInformativo;
            }
            if ($arFiltro['boEmitirTotais'] and $codigoD != '') {
                if ($arFiltro['boBanco']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['banco'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['banco'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['banco'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boLotacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['lotacao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['lotacao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['lotacao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boLocal']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['local'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['local'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['local'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['regime_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['regime_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['regime_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['subdivisao_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['subdivisao_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['subdivisao_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoCargo']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['especialidade_cargo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['especialidade_cargo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['especialidade_cargo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boRegimedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['regime_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['regime_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['regime_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSubdivisaodoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['subdivisao_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['subdivisao_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['subdivisao_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boEspecialidadedoFuncao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['especialidade_funcao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['especialidade_funcao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['especialidade_funcao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boSituacao']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['situacao'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['situacao'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['situacao'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boCgm']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['cgm'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['cgm'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['cgm'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
                if ($arFiltro['boAtributoDinamico']) {
                    $arTemp = '';
                    $arTemp[] = $codigoD;
                    $arTemp[] = $descricaoD;
                    $arTemp[] = $desdobramentoD;
                    $arAgruparInformativos['atributo'][$codigoD.$desdobramentoD][0] += $valorD;
                    $arAgruparInformativos['atributo'][$codigoD.$desdobramentoD][1] += $quantidadeD;
                    $arAgruparInformativos['atributo'][$codigoD.$desdobramentoD][2]  = $arTemp;
                }
            }
            $rsEventosCalculados->proximo();
        }

        $obTFolhaPagamentoComplementar = new TFolhaPagamentoComplementar();
        $stFiltroComplementar = ' AND complementar.cod_periodo_movimentacao = '.$rsPeriodoMovimentacao->getCampo('cod_periodo_movimentacao');
        $obTFolhaPagamentoComplementar->recuperaRelacionamento($rsComplementares,$stFiltroComplementar);

        if ($rsComplementares->getNumLinhas() > 0) {
            $fpdf->SetFillColor(245,245,245);
            $fpdf->SetXY(7,$inLinha);
            $fpdf->Cell(283,$inAlturaLinha, 'OUTRAS FOLHAS' ,0,0,'C',1);
            $inLinha = incrementaLinha($fpdf,$inLinha);

            while (!$rsComplementares->eof()) {
                $obFFolhaPagamentoFolhaAnaliticaResumida->setDado('cod_complementar',$rsComplementares->getCampo('cod_complementar'));
                $obFFolhaPagamentoFolhaAnaliticaResumida->eventosCalculadosComplementarFolhaAnalitica($rsEventosComplementaresCalculados);

                $fpdf->SetFillColor(255,255,255);
                $fpdf->SetXY(7,$inLinha);
                $fpdf->Cell(25,$inAlturaLinha, 'Complementar '.$rsComplementares->getCampo('cod_complementar') ,0,0,'L',1);
                $fpdf->SetXY(32,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Base Previdência: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $fpdf->SetXY(94,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Desc. INSS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $fpdf->SetXY(156,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Base IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $fpdf->SetXY(218,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Desc. IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $inLinha = incrementaLinha($fpdf,$inLinha);

                $fpdf->SetXY(32,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Base FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $fpdf->SetXY(94,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Valor Recolhido de FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $fpdf->SetXY(156,$inLinha);
                $fpdf->Cell(62,$inAlturaLinha, 'Valor Contribuição Social: '.number_format(0.00,2,',','.') ,0,0,'L',1);
                $inLinha = incrementaLinha($fpdf,$inLinha);

                $rsComplementares->proximo();
            }
        }
        $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
        $inLinha = incrementaLinha($fpdf,$inLinha);
        $stLotacao              = $rsContratosCalculados->getCampo('descricao_lotacao');
        $stLocal                = $rsContratosCalculados->getCampo('descricao_local');
        $stRegimeCargo          = $rsContratosCalculados->getCampo('descricao_regime');
        $stSubDivisaoCargo      = $rsContratosCalculados->getCampo('descricao_sub_divisao');
        $stCargo                = $rsContratosCalculados->getCampo('descricao_cargo');
        $stEspecialidadeCargo   = $rsContratosCalculados->getCampo('descricao_especialidade');
        $stRegimeFuncao         = $rsContratosCalculados->getCampo('descricao_regime_funcao');
        $stSubDivisaoFuncao     = $rsContratosCalculados->getCampo('descricao_sub_divisao_funcao');
        $stFuncao               = $rsContratosCalculados->getCampo('descricao_funcao');
        $stEspecialidadeFuncao  = $rsContratosCalculados->getCampo('descricao_especialidade');
        $stSituacao             = $rsContratosCalculados->getCampo('situacao');
        $stCGM                  = $rsContratosCalculados->getCampo('numcgm');
        $stAtributo             = $rsContratosCalculados->getCampo('valor');
        $stBanco                = $rsContratosCalculados->getCampo('descricao_banco');
        $nuBanco                = $rsContratosCalculados->getCampo('num_banco');
        $rsContratosCalculados->proximo();

        if ($arFiltro['boAtributoDinamico'] and $arFiltro['boEmitirTotais'] and $stAtributo != $rsContratosCalculados->getCampo('valor')) {
            $obTAdministracaoAtributoValorPadrao = new TAdministracaoAtributoValorPadrao();
            $obTAdministracaoAtributoValorPadrao->setDado('cod_modulo'  , 22);
            $obTAdministracaoAtributoValorPadrao->setDado('cod_cadastro', $arFiltro['inCodCadastro']);
            $obTAdministracaoAtributoValorPadrao->setDado('cod_atributo', $arFiltro['inCodAtributo']);
            $obTAdministracaoAtributoValorPadrao->setDado('cod_valor'   , $stAtributo);
            $obTAdministracaoAtributoValorPadrao->recuperaPorChave($rsValorPadrao);
            $stAtributo = ( $rsValorPadrao->getNumLinhas() == 1 ) ? $rsValorPadrao->getCampo('valor_padrao') : $stAtributo;

            $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'ATRIBUTO DINÂMICO: '.$stAtributo,$arAgruparProventos['atributo'],$arAgruparDescontos['atributo'],$arAgruparBases['atributo'],$arAgruparInformativos['atributo']);
            $boQuebra = true;
            $arAgruparProventos['atributo']       = array();
            $arAgruparDescontos['atributo']       = array();
            $arAgruparBases['atributo']           = array();
            $arAgruparInformativos['atributo']    = array();
        }

        if ($arFiltro['boEmitirTotais']) {
            /*
             * Verifica se deve mostrar totais do agrupamento (proxima var diferente da atual
             */

            if ($arFiltro['boCgm'] and $stCgm != $rsContratosCalculados->getCampo('descricao_cgm')) {
                $boCgm                      = true;
            }
            if ($arFiltro['boSituacao'] and $stCgm != $rsContratosCalculados->getCampo('descricao_situacao')) {
                $boSituacao                 = true;
            }
            if ($arFiltro['boEspecialidadedaFuncao'] and $stEspecialidadeFuncao != $rsContratosCalculados->getCampo('descricao_especialidade_funcao')) {
                $boEspecialidadedaFuncao    = true;
            }
            if ($arFiltro['boFuncao'] and $stFuncao != $rsContratosCalculados->getCampo('descricao_funcao')) {
                $boFuncao                   = true;
            }
            if ($arFiltro['boSubdivisaodaFuncao'] and $stSubDivisaoFuncao != $rsContratosCalculados->getCampo('descricao_sub_divisao_funcao')) {
                $boSubdivisaodaFuncao       = true;
            }
            if ($arFiltro['boRegimedaFuncao'] and $stRegimeCargo != $rsContratosCalculados->getCampo('descricao_regime_funcao')) {
                $boRegimedaFuncao           = true;
            }
            if ($arFiltro['boEspecialidadedoCargo'] and $stEspecialidadeCargo != $rsContratosCalculados->getCampo('descricao_especialidade')) {
                $boEspecialidadedoCargo     = true;
            }
            if ($arFiltro['boCargo'] and $stCargo != $rsContratosCalculados->getCampo('descricao_cargo')) {
                $boCargo                    = true;
            }
            if ($arFiltro['boSubdivisaodoCargo'] and $stSubDivisaoCargo != $rsContratosCalculados->getCampo('descricao_sub_divisao')) {
                $boSubdivisaodoCargo        = true;
            }
            if ($arFiltro['boRegimedoCargo'] and $stRegimeCargo != $rsContratosCalculados->getCampo('descricao_regime')) {
                $boRegimedoCargo            = true;
            }
            if ($arFiltro['boLocal'] and $stLocal != $rsContratosCalculados->getCampo('descricao_local')) {
                $boLocal                    = true;
            }
            if ($arFiltro['boLotacao'] and $stLotacao != $rsContratosCalculados->getCampo('descricao_lotacao')) {
                $boLotacao                  = true;
            }
            if ($arFiltro['boBanco'] and $stBanco != $rsContratosCalculados->getCampo('descricao_banco')) {
                $boBanco                    = true;
            }

            /*
             * Para evitar que agrupamento interno seja fechado após agrupamento externo,
             * verifica na ordem agrupamento externo -> agrupamento interno se o
             * agrupamento externo irá emitir totais.
             * Caso agrupamento externo emita totais, marca agrupamento interno para emitir totais.
             *
             * ex. agrupamento em ordem Banco -> Lotacao -> CGM
             * ------cgm
             * ------cgm
             * ---lotacao
             * -banco
             * ------cgm
             * ---lotacao
             * -banco
             * ------cgm
             * ---lotacao
             * ------cgm
             * ---lotacao
             * -banco
             */

            foreach ($arFiltro['arrayOrdenacao'] as $stPosicaoOrdenacao => $value) {
                if ( ${ordenacaoPosicaoAnterior($stPosicaoOrdenacao)} ) {
                    $$stPosicaoOrdenacao = true;
                }
            }

            /*
             * Mostra totais de agrupamentos na ordem natural que foram selecionados
             * internos p/ externos comecando por CGM
             *
             * Logica do menu monta o arrayOrdenacao de externos p/ internos
             */

            foreach ( array_reverse($arFiltro['arrayOrdenacao']) as $stPosicaoOrdenacao => $value ) {
                if ($stPosicaoOrdenacao == 'boCgm' && $boCgm) {
                    $stCgm = ( $stCgm == 'null' ) ? '' : $stCgm;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'CGM: '.$stCgm,$arAgruparProventos['cgm'],$arAgruparDescontos['cgm'],$arAgruparBases['cgm'],$arAgruparInformativos['cgm']);
                    $boQuebra = true;
                    $boCgm = false;
                    $arAgruparProventos['cgm']       = array();
                    $arAgruparDescontos['cgm']       = array();
                    $arAgruparBases['cgm']           = array();
                    $arAgruparInformativos['cgm']    = array();
                }

                if ($stPosicaoOrdenacao == 'boSituacao' && $boSituacao) {
                    $stSituacao = ( $stSituacao == 'null' ) ? '' : $stSituacao;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'SITUAÇÃO: '.$stSituacao,$arAgruparProventos['situacao'],$arAgruparDescontos['situacao'],$arAgruparBases['situacao'],$arAgruparInformativos['situacao']);
                    $boQuebra = true;
                    $boSituacao = false;
                    $arAgruparProventos['situacao']       = array();
                    $arAgruparDescontos['situacao']       = array();
                    $arAgruparBases['situacao']           = array();
                    $arAgruparInformativos['situacao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boEspecialidadedaFuncao' && $boEspecialidadedaFuncao) {
                    $stEspecialidadeFuncao = ( $stEspecialidadeFuncao == 'null' ) ? '' : $stEspecialidadeFuncao;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'ESPECIALIDADE DA FUNÇÃO: '.$stEspecialidadeFuncao,$arAgruparProventos['especialidade_funcao'],$arAgruparDescontos['especialidade_funcao'],$arAgruparBases['especialidade_funcao'],$arAgruparInformativos['especialidade_funcao']);
                    $boQuebra = true;
                    $boEspecialidadedaFuncao = false;
                    $arAgruparProventos['especialidade_funcao']       = array();
                    $arAgruparDescontos['especialidade_funcao']       = array();
                    $arAgruparBases['especialidade_funcao']           = array();
                    $arAgruparInformativos['especialidade_funcao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boFuncao' && $boFuncao) {
                    $stFuncao = ( $stFuncao == 'null' ) ? '' : $stFuncao;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'FUNÇÃO: '.$stFuncao,$arAgruparProventos['funcao'],$arAgruparDescontos['funcao'],$arAgruparBases['funcao'],$arAgruparInformativos['funcao']);
                    $boQuebra = true;
                    $boFuncao = false;
                    $arAgruparProventos['funcao']       = array();
                    $arAgruparDescontos['funcao']       = array();
                    $arAgruparBases['funcao']           = array();
                    $arAgruparInformativos['funcao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boSubdivisaodaFuncao' && $boSubdivisaodaFuncao) {
                    $stSubDivisaoFuncao = ( $stSubDivisaoFuncao == 'null' ) ? '' : $stSubDivisaoFuncao;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'SUBDIVISÃO DA FUNÇÃO: '.$stSubDivisaoFuncao,$arAgruparProventos['subdivisao_funcao'],$arAgruparDescontos['subdivisao_funcao'],$arAgruparBases['subdivisao_funcao'],$arAgruparInformativos['subdivisao_funcao']);
                    $boQuebra = true;
                    $boSubdivisaodaFuncao = false;
                    $arAgruparProventos['subdivisao_funcao']       = array();
                    $arAgruparDescontos['subdivisao_funcao']       = array();
                    $arAgruparBases['subdivisao_funcao']           = array();
                    $arAgruparInformativos['subdivisao_funcao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boRegimedaFuncao' && $boRegimedaFuncao) {
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'REGIME DA FUNÇÃO: '.$stRegimeFuncao,$arAgruparProventos['regime_funcao'],$arAgruparDescontos['regime_funcao'],$arAgruparBases['regime_funcao'],$arAgruparInformativos['regime_funcao']);
                    $boQuebra = true;
                    $boRegimedaFuncao = false;
                    $arAgruparProventos['regime_funcao']       = array();
                    $arAgruparDescontos['regime_funcao']       = array();
                    $arAgruparBases['regime_funcao']           = array();
                    $arAgruparInformativos['regime_funcao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boEspecialidadedoCargo' && $boEspecialidadedoCargo) {
                    $stEspecialidadeCargo = ( $stEspecialidadeCargo == 'null' ) ? '' : $stEspecialidadeCargo;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'ESPECIALIDADE DO CARGO: '.$stEspecialidadeCargo,$arAgruparProventos['especialidade_cargo'],$arAgruparDescontos['especialidade_cargo'],$arAgruparBases['especialidade_cargo'],$arAgruparInformativos['especialidade_cargo']);
                    $boQuebra = true;
                    $boEspecialidadedoCargo = false;
                    $arAgruparProventos['especialidade_cargo']       = array();
                    $arAgruparDescontos['especialidade_cargo']       = array();
                    $arAgruparBases['especialidade_cargo']           = array();
                    $arAgruparInformativos['especialidade_cargo']    = array();
                }
                if ($stPosicaoOrdenacao == 'boCargo' && $boCargo) {
                    $stCargo = ( $stCargo == 'null' ) ? '' : $stCargo;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'CARGO: '.$stCargo,$arAgruparProventos['cargo'],$arAgruparDescontos['cargo'],$arAgruparBases['cargo'],$arAgruparInformativos['cargo']);
                    $boQuebra = true;
                    $boCargo = false;
                    $arAgruparProventos['cargo']       = array();
                    $arAgruparDescontos['cargo']       = array();
                    $arAgruparBases['cargo']           = array();
                    $arAgruparInformativos['cargo']    = array();
                }
                if ($stPosicaoOrdenacao == 'boSubdivisaodoCargo' && $boSubdivisaodoCargo) {
                    $stSubDivisaoCargo = ( $stSubDivisaoCargo == 'null' ) ? '' : $stSubDivisaoCargo;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'SUBDIVISÃO DO CARGO: '.$stSubDivisaoCargo,$arAgruparProventos['subdivisao_cargo'],$arAgruparDescontos['subdivisao_cargo'],$arAgruparBases['subdivisao_cargo'],$arAgruparInformativos['subdivisao_cargo']);
                    $boQuebra = true;
                    $boSubdivisaodoCargo = false;
                    $arAgruparProventos['subdivisao_cargo']       = array();
                    $arAgruparDescontos['subdivisao_cargo']       = array();
                    $arAgruparBases['subdivisao_cargo']           = array();
                    $arAgruparInformativos['subdivisao_cargo']    = array();
                }
                if ($stPosicaoOrdenacao == 'boRegimedoCargo' && $boRegimedoCargo) {
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'REGIME DO CARGO: '.$stRegimeCargo,$arAgruparProventos['regime_cargo'],$arAgruparDescontos['regime_cargo'],$arAgruparBases['regime_cargo'],$arAgruparInformativos['regime_cargo']);
                    $boQuebra = true;
                    $boRegimedoCargo = false;
                    $arAgruparProventos['regime_cargo']       = array();
                    $arAgruparDescontos['regime_cargo']       = array();
                    $arAgruparBases['regime_cargo']           = array();
                    $arAgruparInformativos['regime_cargo']    = array();
                }
                if ($stPosicaoOrdenacao == 'boLocal' && $boLocal) {
                    $stLocal = ( $stLocal == 'null' ) ? '' : $stLocal;
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'LOCAL: '.$stLocal,$arAgruparProventos['local'],$arAgruparDescontos['local'],$arAgruparBases['local'],$arAgruparInformativos['local']);
                    $boQuebra = true;
                    $boLocal = false;
                    $arAgruparProventos['local']       = array();
                    $arAgruparDescontos['local']       = array();
                    $arAgruparBases['local']           = array();
                    $arAgruparInformativos['local']    = array();
                }
                if ($stPosicaoOrdenacao == 'boLotacao' && $boLotacao) {
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'LOTAÇÃO: '.$stLotacao,$arAgruparProventos['lotacao'],$arAgruparDescontos['lotacao'],$arAgruparBases['lotacao'],$arAgruparInformativos['lotacao']);
                    $boQuebra = true;
                    $boLotacao = false;
                    $arAgruparProventos['lotacao']       = array();
                    $arAgruparDescontos['lotacao']       = array();
                    $arAgruparBases['lotacao']           = array();
                    $arAgruparInformativos['lotacao']    = array();
                }
                if ($stPosicaoOrdenacao == 'boBanco' && $boBanco) {
                    $inLinha = addTotalAgrupagamentoAnalitica($fpdf,$inLinha,'BANCO: '.$stBanco,$arAgruparProventos['banco'],$arAgruparDescontos['banco'],$arAgruparBases['banco'],$arAgruparInformativos['banco']);
                    $boQuebra = true;
                    $boBanco = false;
                    $arAgruparProventos['banco']       = array();
                    $arAgruparDescontos['banco']       = array();
                    $arAgruparBases['banco']           = array();
                    $arAgruparInformativos['banco']    = array();
                }
            }//end foreach array_reverse arrayOrdenacao
        }//end if boEmitirTotais

        if ($boQuebra AND !$rsContratosCalculados->eof()) {
            $inLinha = addCabecalho($fpdf);
        }
    }
    $inLinha = addCabecalho($fpdf);
    $inLinha = addCabecalhoRelatorioAnalitica($fpdf,$inLinha);
    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL GERAL' ,0,0,'C',1);
    $fpdf->SetFont('Helvetica','',8);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'PROVENTOS' ,0,0,'C',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'DESCONTOS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $nuTotalProventos = 0;
    $nuTotalDescontos = 0;
    $arTotalEvento = agruparEventosAnalitica($arProventos,$arDescontos);
    foreach ($arTotalEvento as $arTotal) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigoe'] ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaoe'] ,0,0,'L',1);
        $fpdf->SetXY(82,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentoe'] ,0,0,'L',1);
        $fpdf->SetXY(109,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidadee'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(129,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valore'],2,',','.') ,0,0,'R',1);
        $nuTotalProventos += $arTotal['valore'];

        $fpdf->SetXY(149,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigod'] ,0,0,'R',1);
        $fpdf->SetXY(164,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaod'] ,0,0,'L',1);
        $fpdf->SetXY(224,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentod'] ,0,0,'L',1);
        $fpdf->SetXY(251,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidaded'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(271,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valord'],2,',','.') ,0,0,'R',1);
        $nuTotalDescontos += $arTotal['valord'];
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }

    $fpdf->SetFillColor(255,255,255);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE PROVENTOS: '.number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE DESCONTOS: '.number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'SALÁRIO LÍQUIDO: '.number_format(($nuTotalProventos-$nuTotalDescontos),2,',','.') ,0,0,'R',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE SERVIDORES: '.$inTotalContratos,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS DE BASE' ,0,0,'C',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS INFORMATIVOS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $arTotalEvento = agruparEventosAnalitica($arBases,$arInformativos);
    foreach ($arTotalEvento as $arTotal) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigoe'] ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaoe'] ,0,0,'L',1);
        $fpdf->SetXY(82,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentoe'] ,0,0,'L',1);
        $fpdf->SetXY(109,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidadee'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(129,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valore'],2,',','.') ,0,0,'R',1);

        $fpdf->SetXY(149,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigod'] ,0,0,'R',1);
        $fpdf->SetXY(164,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaod'] ,0,0,'L',1);
        $fpdf->SetXY(224,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentod'] ,0,0,'L',1);
        $fpdf->SetXY(251,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidaded'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(271,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valord'],2,',','.') ,0,0,'R',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(283,$inAlturaLinha, 'OUTRAS FOLHAS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(25,$inAlturaLinha, 'Complementar 1' ,0,0,'L',1);
    $fpdf->SetXY(32,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base Previdência: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(94,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Desc. INSS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(156,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(218,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Desc. IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(32,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(94,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Valor Recolhido de FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(156,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Valor Contribuição Social: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $fpdf->SetXY(7 ,$inLinha);
    $fpdf->Cell( 283 ,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
    $fpdf->Line( 7  , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);
}

function montaLegenda($inCodConfiguracao)
{
    # Caso folha complementar, pegar desdobramentos de férias
    if (trim($inCodConfiguracao)=='') {
        $inCodConfiguracao = 2;
    }

    include_once CAM_GRH_FOL_MAPEAMENTO.'TFolhaPagamentoConfiguracaoDesdobramento.class.php';
    $obTFolhaPagamentoConfiguracaoDesdobramento = new TFolhaPagamentoConfiguracaoDesdobramento();
    $stFiltro = ' WHERE cod_configuracao = '.$inCodConfiguracao;
    $obTFolhaPagamentoConfiguracaoDesdobramento->recuperaTodos($rsDesdobramento, $stFiltro, ' ORDER BY desdobramento');

    $legenda = '';
    $espaco  = str_repeat(' ',4);

    while (!$rsDesdobramento->eof()) {
        $legenda .= $rsDesdobramento->getCampo('desdobramento').' - '.$rsDesdobramento->getCampo('descricao').$espaco;
        $rsDesdobramento->proximo();
    }

    return $legenda;
}

function addTotalAgrupagamentoAnalitica(&$fpdf,$inLinha,$stRotulo,$arProventos,$arDescontos,$arBases,$arInformativos)
{
    global $arFiltro,$inAlturaLinha;

    $legenda = montaLegenda($arFiltro['inCodConfiguracao']);

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->Cell(283,$inAlturaLinha, 'TOTAL POR '.$stRotulo ,0,0,'C',1);
    $fpdf->SetFont('Helvetica','',8);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'PROVENTOS' ,0,0,'C',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'DESCONTOS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $nuTotalProventos = 0;
    $nuTotalDescontos = 0;
    $arTotalEvento = agruparEventosAnalitica($arProventos,$arDescontos);
    foreach ($arTotalEvento as $arTotal) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigoe'] ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaoe'] ,0,0,'L',1);
        $fpdf->SetXY(82,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentoe'] ,0,0,'L',1);
        $fpdf->SetXY(109,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidadee'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(129,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valore'],2,',','.') ,0,0,'R',1);
        $nuTotalProventos += $arTotal['valore'];

        $fpdf->SetXY(149,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigod'] ,0,0,'R',1);
        $fpdf->SetXY(164,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaod'] ,0,0,'L',1);
        $fpdf->SetXY(224,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentod'] ,0,0,'L',1);
        $fpdf->SetXY(251,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidaded'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(271,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valord'],2,',','.') ,0,0,'R',1);
        $nuTotalDescontos += $arTotal['valord'];
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE PROVENTOS: '.number_format($nuTotalProventos,2,',','.') ,0,0,'R',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'TOTAL DE DESCONTOS: '.number_format($nuTotalDescontos,2,',','.') ,0,0,'R',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'SALÁRIO LÍQUIDO: '.number_format(($nuTotalProventos-$nuTotalDescontos),2,',','.') ,0,0,'R',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS DE BASE' ,0,0,'C',1);
    $fpdf->SetXY(149,$inLinha);
    $fpdf->Cell(142,$inAlturaLinha, 'EVENTOS INFORMATIVOS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $arTotalEvento = agruparEventosAnalitica($arBases,$arInformativos);
    foreach ($arTotalEvento as $arTotal) {
        $fpdf->SetXY(7,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigoe'] ,0,0,'R',1);
        $fpdf->SetXY(22,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaoe'] ,0,0,'L',1);
        $fpdf->SetXY(82,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentoe'] ,0,0,'L',1);
        $fpdf->SetXY(109,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidadee'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(129,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valore'],2,',','.') ,0,0,'R',1);

        $fpdf->SetXY(149,$inLinha);
        $fpdf->Cell(15,$inAlturaLinha, $arTotal['codigod'] ,0,0,'R',1);
        $fpdf->SetXY(164,$inLinha);
        $fpdf->Cell(60,$inAlturaLinha, $arTotal['descricaod'] ,0,0,'L',1);
        $fpdf->SetXY(224,$inLinha);
        $fpdf->Cell(27,$inAlturaLinha, $arTotal['desdobramentod'] ,0,0,'L',1);
        $fpdf->SetXY(251,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['quantidaded'],2,',','.') ,0,0,'R',1);
        $fpdf->SetXY(271,$inLinha);
        $fpdf->Cell(20,$inAlturaLinha, number_format($arTotal['valord'],2,',','.') ,0,0,'R',1);
        $inLinha = incrementaLinha($fpdf,$inLinha);
    }

    $fpdf->SetFillColor(245,245,245);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(283,$inAlturaLinha, 'OUTRAS FOLHAS' ,0,0,'C',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetFillColor(255,255,255);
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(25,$inAlturaLinha, 'Complementar 1' ,0,0,'L',1);
    $fpdf->SetXY(32,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base Previdência: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(94,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Desc. INSS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(156,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(218,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Desc. IRRF: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->SetXY(32,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Base FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(94,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Valor Recolhido de FGTS: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->SetXY(156,$inLinha);
    $fpdf->Cell(62,$inAlturaLinha, 'Valor Contribuição Social: '.number_format(0.00,2,',','.') ,0,0,'L',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $fpdf->SetXY(7,$inLinha);
    $fpdf->Cell(283,$inAlturaLinha, 'Legenda para Desdobramentos: '.$legenda ,0,0,'L',1);
    $fpdf->Line( 7 , $inLinha+$inAlturaLinha , 290 , $inLinha+$inAlturaLinha );
    $inLinha = incrementaLinha($fpdf,$inLinha);

    return $inLinha;
}

$fpdf =new FPDF('L');
$fpdf->open();
$fpdf->setTextColor(0);
$fpdf->SetFont('Arial','',10);
$inLinha = addCabecalho($fpdf);

switch ($arFiltro['stFolha']) {
    case 'analítica_resumida':
        analiticaResumida($fpdf,$inLinha);
        break;
    case 'sintética':
        sintetica($fpdf,$inLinha);
        break;
    case 'analítica':
        analitica($fpdf,$inLinha);
        break;
}

Sessao::write('obRelatorio',$fpdf);

$obRelatorio = new RRelatorio();
$obRelatorio->executaFrameOculto('OCGeraRelatorioFolhaAnaliticaSintetica.php');

?>

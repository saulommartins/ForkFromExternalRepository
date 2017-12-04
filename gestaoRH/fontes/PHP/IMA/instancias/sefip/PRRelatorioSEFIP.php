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
    * Página de Processamento do Relatório Exportação SEFIP
    * Data de Criação: 13/03/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Diego Lemos de Souza

    * @ignore

    $Revision: 32866 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-09 08:32:58 -0300 (Qua, 09 Abr 2008) $

    * Casos de uso: uc-04.08.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once( CAM_FW_PDF."RRelatorio.class.php"                                                     );
define      ('FPDF_FONTPATH','font/');

$inAlturaLinha = 3;

function addCabecalho(&$fpdf)
{
    global $inAlturaLinha;
    $obRRelatorio = new RRelatorio;
    $obRRelatorio->setExercicio  ( Sessao::getExercicio() );
    $obRRelatorio->setCodigoEntidade(Sessao::getCodEntidade($boTransacao));
    $obRRelatorio->setExercicioEntidade(Sessao::getExercicio());
    $obRRelatorio->recuperaCabecalho( $arConfiguracao );

    //$fpdf->addPage();
    $fpdf->setLeftMargin(0);
    $fpdf->setTopMargin(0);
    $fpdf->SetLineWidth(0.03);
    $fpdf->SetCreator = "URBEM";
    $fpdf->SetFillColor(220);
    $tMargem = 10/*$fpdf->tMargin*/;
    $lMargem = 8/*$fpdf->lMargin*/;
    if ( is_file( CAM_FW_IMAGENS.$arConfiguracao["logotipo"] ) ) {
        $fpdf->Image( CAM_FW_IMAGENS.$arConfiguracao["logotipo"]  ,$lMargem,$tMargem,20);
    } elseif ( is_file( $arConfiguracao["logotipo"] ) ) {
        $fpdf->Image(  $arConfiguracao["logotipo"] ,$lMargem,$tMargem,20);
    }
    $fpdf->Cell(20,10,'');
    $fpdf->SetFont('Helvetica','B',8);
    $fpdf->SetFillColor(255);
    //$X = $fpdf->GetX();
    $X = 30.00125;
    //$Y = $fpdf->GetY();
    $Y = 10.00125;
    $fpdf->SetXY($X,$Y);
    $fpdf->Cell(70,$inAlturaLinha, $arConfiguracao["nom_prefeitura"]  ,0,'L',1);
    $fpdf->SetFont('Helvetica','',8);
    $fpdf->SetXY($X,$Y+4);
    $fpdf->Cell(70,$inAlturaLinha,"Fone/Fax: ".$arConfiguracao["fone"]." / ".$arConfiguracao["fax"],0,'L',1);
    $fpdf->SetXY($X,$Y+8);
    $fpdf->Cell(70,$inAlturaLinha,"E-mail: ".$arConfiguracao["e_mail"] ,0,'L',1);
    $fpdf->SetXY($X,$Y+12);
    $fpdf->Cell(70,$inAlturaLinha, $arConfiguracao["logradouro"].",".$arConfiguracao["numero"]." - ".$arConfiguracao["nom_municipio"]  ,0,'L',1);
    $fpdf->SetXY($X,$Y+16);
    $fpdf->Cell(70,$inAlturaLinha,"CEP: ".$arConfiguracao["cep"],0,'L',1);
    $fpdf->SetXY($X,$Y+20);
    $fpdf->Cell(70,$inAlturaLinha,"CNPJ: ".$arConfiguracao['cnpj'],0,'L',1);
    $fpdf->SetFont('Helvetica','B',8);
    $sDisp = $fpdf->DefOrientation;
    $iAjus = 70;
    if ($sDisp=='L') {
        $iAjus = 160;
    }
    $fpdf->SetXY($X+$iAjus,$Y);
    $fpdf->Cell(56,5,$arConfiguracao['nom_modulo'],1,0,'L',1);
    $fpdf->Cell(0,5,'Versão: '.Sessao::getVersao(),1,0,'L',1);
    $fpdf->SetXY($X+$iAjus,$Y+5);
    $fpdf->Cell(56,5,$arConfiguracao['nom_funcionalidade'],1,'TRL','L',1);
    $fpdf->Cell(0,5,"Usuário: ".Sessao::getUsername(),1,'RLB','L',1);
    $fpdf->SetXY($X+$iAjus,$Y+10);
    if ($fpdf->stAcao) {
        $arConfiguracao['nom_acao'] = trim($fpdf->stAcao);
    } else {
        if( $fpdf->stComplementoAcao )
        $stNomAcao = trim($arConfiguracao['nom_acao']) ." ".$fpdf->stComplementoAcao;
    }
    $stNomAcao = ( $stNomAcao ) ? $stNomAcao : $arConfiguracao['nom_acao'];
    $fpdf->Cell(0,5,$stNomAcao,1,'RLB','L',1);
    $fpdf->SetFont('Helvetica','',8);
    $fpdf->SetXY($X+$iAjus,$Y+15);
    $fpdf->Cell(0,5,$fpdf->stSubTitulo,1,'RLB','L',1);
    $fpdf->SetXY($X+$iAjus,$Y+20);
    $fpdf->Cell(33,5,'Emissão: '.date( "d/m/Y", time()),1,0,'L',1);
    $fpdf->Cell(23,5,'Hora: '.date( "H:i", time()),1,0,'L',1);
    $fpdf->AliasNbPages();
    if ($fpdf->inPaginaInicial == null) {
        $fpdf->Cell(0,5,'Página: '.$fpdf->PageNo().' de '.$fpdf->AliasNbPages,1,0,'L',1);
    } else {
        $fpdf->Cell(0,5,'Página: '.( $fpdf->PageNo() + $fpdf->inPaginaInicial ) ,1,0,'L',1);
    }
    $fpdf->Ln(4);
    $fpdf->Line( 7, 35, 200, 35 );
    $inLinha = 36;

    return $inLinha;
}

$fpdf =new FPDF();
$fpdf->open();
$fpdf->setTextColor(0);
$fpdf->addPage();
$fpdf->setLeftMargin(0);
$fpdf->setTopMargin(0);
$fpdf->SetLineWidth(0.03);

$inLinha = addCabecalho($fpdf);
$fpdf->SetFont('Arial','B',10);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "TOTAIS ARQUIVO SEFIP" ,0,0,'C',1);
$fpdf->SetFont('Arial','',10);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Competência: ".Sessao::read("stCompetencia") ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Competência 13°: ".Sessao::read("stCompetencia13") ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Código do Recolhimento: ".Sessao::read("stRecolhimento") ,0,0,'L',1);
//$inLinha += ($inAlturaLinha+1);
//$fpdf->SetXY(7,$inLinha);
//$fpdf->Cell(200,$inAlturaLinha, "Modalidade do Recolhimento: ".Sessao::read("stModalidade") ,0,0,'L',1);

$inLinha += ($inAlturaLinha+1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetFont('Arial','B',10);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Servidores Listados no Arquivo" ,0,0,'L',1);
$fpdf->SetFont('Arial','',10);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Base Previdência s/13°: ".number_format(Sessao::read("nuBasePrevidenciaS13"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Desc. Previdência s/13°  Ocorr. 05 e Maternidade: ".number_format(Sessao::read("nuDescontoPrevidenciaS13"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Desc. Previdência s/13°  Demais Servidores: ".number_format(Sessao::read("nuDescontoPrevidenciaS13DemaisOcor"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Base Previdência 13°: ".number_format(Sessao::read("nuBasePrevidencia13"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Salário Maternidade: ".number_format(Sessao::read("nuTotalSalarioMaternidade"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Salário Família: ".number_format(Sessao::read("nuSalarioFamilia"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Valor Patronal: ".number_format(Sessao::read("nuValorPatronal"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Base FGTS: ".number_format(Sessao::read("nuBaseFGTS"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Base FGTS 13°: ".number_format(Sessao::read("nuBaseFGTS13"),2,',','.') ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Total Servidores Listados no Arquivo: ".Sessao::read("inTotalServidoresArquivo") ,0,0,'L',1);

$inLinha += ($inAlturaLinha+1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetFont('Arial','B',10);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Quantidade de Afastamento Listados no Arquivo" ,0,0,'L',1);
$fpdf->SetFont('Arial','',10);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Doença + 15 dias: ".Sessao::read("inDoenca15Dias") ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Acidente Trabalho: ".Sessao::read("inAcidenteTrabalho") ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Licença Maternidade: ".Sessao::read("inLicencaMaternidade") ,0,0,'L',1);
$inLinha += ($inAlturaLinha+1);
$fpdf->SetXY(7,$inLinha);
$fpdf->Cell(200,$inAlturaLinha, "Movimentação Definitiva (Rescisões): ".Sessao::read("inRescisoes") ,0,0,'L',1);

Sessao::write("obRelatorio", $fpdf);

$obRelatorio = new RRelatorio();
$obRelatorio->executaFrameOculto("OCGeraRelatorioSEFIP.php");

?>

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
  * Página de Classe de Carnê Petrópolis
  * Data de criação : 21/12/2005

  * @author Analista: Fábio Bertoldi
  * @author Programador: tonismar R. Bernardo

  * @package URBEM

    * $Id: RCarnePetropolis.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-5.3.11
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once 'RProtocoloPetropolis.class.php';
//define       ('FPDF_FONTPATH','font/');

class RCarnePetropolis extends RProtocoloPetropolis
{
    /* labels */
    public $lblTitulo1 = 'P M Mata de São João';
    public $lblTitulo2 = 'IPTU';
    public $lblExercicio = 'EXERCÍCIO';
    public $lblInscricao = 'INSCRIÇÃO';
    public $lblCodDivida = 'CÓD. DÍVIDA';
    public $lblTributo   = 'TRIBUTO';
    public $lblParcela   = 'PARCELA';
    public $lblReferencia = 'REFERÊNCIA';
    public $lblDataProcessamento = 'DATA PROCESSAMENTO';
    public $lblVencimento = 'VENCIMENTO';
    public $lblValorCotaUnica = 'VALOR COTA ÚNICA';
    public $lblContribuinte = 'CONTRIBUINTE';
    public $lblData = 'DATA';
    public $lblCorrecao = 'CORREÇÃO';
    public $lblMonetaria = 'MONET(%)';
    public $lblMulta = 'MULTA(%)';
    public $lblJuros = 'JUROS(%)';
    public $lblValorParcela = 'VALOR PARCELA';
    public $lblReal = '(REAL)';
    public $lblMulta1 = '  ';
    public $lblMulta2 = '  ';
    public $lblMulta3 = '  ';
    public $lblMulta4 = '  ';
    public $lblJuros1 = '  ';
    public $lblJuros2 = '  ';
    public $lblJuros3 = '  ';
    public $lblJuros4 = '  ';
    public $lblNumeracao = 'NOSSO NÚMERO';

    /* variaveis */
    public $ImagemCarne;
    public $stExercicio;
    public $inInscricao;
    public $inCodDivida;
    public $stTributo;
    public $stParcela;
    public $inReferencia;
    public $dtProcessamento;
    public $dtVencimento;
    public $dtVencimentof1;
    public $dtVencimentof2;
    public $dtVencimentof3;
    public $flValorCotaUnica;
    public $flValor;
    public $flValorf1;
    public $flValorf2;
    public $flValorf3;
    public $stNomCgm;
    public $stBarCode;
    public $boParcelaUnica;
    public $stObservacaoL1;
    public $stObservacaoL2;
    public $stObservacaoL3;
    public $stNumeracao;

    /* setters */
    public function setImagemCarne($valor) { $this->ImagemCarne      = $valor; }
    public function setExercicio($valor) { $this->stExercicio      = $valor; }
    public function setInscricao($valor) { $this->inInscricao      = $valor; }
    public function setCodDivida($valor) { $this->inCodDivida      = $valor; }
    public function setTributo($valor) { $this->stTributo        = $valor; }
    public function setParcela($valor) { $this->stParcela        = $valor; }
    public function setReferencia($valor) { $this->inReferencia     = $valor; }
    public function setProcessamento($valor) { $this->dtProcessamento  = $valor; }
    public function setVencimento($valor) { $this->dtVencimento     = $valor; }
    public function setVencimento1($valor) { $this->dtVencimentof1   = $valor; }
    public function setVencimento2($valor) { $this->dtVencimentof2   = $valor; }
    public function setVencimento3($valor) { $this->dtVencimentof3   = $valor; }
    public function setValorCotaUnica($valor) { $this->flValorCotaUnica = $valor; }
    public function setValor($valor) { $this->flValor          = $valor; }
    public function setValor1($valor) { $this->flValorf1        = $valor; }
    public function setValor2($valor) { $this->flValorf2        = $valor; }
    public function setValor3($valor) { $this->flValorf3        = $valor; }
    public function setNomCgm($valor) { $this->stNomCgm         = $valor; }
    public function setBarCode($valor) { $this->stBarCode        = $valor; }
    public function setLinhaCode($valor) { $this->stLinhaCode      = $valor; }
    public function setParcelaUnica($valor) { $this->boParcelaUnica   = $valor; }
    public function setObservacaoL1($valor) { $this->stObservacaoL1   = $valor; }
    public function setObservacaoL2($valor) { $this->stObservacaoL2   = $valor; }
    public function setObservacaoL3($valor) { $this->stObservacaoL3   = $valor; }
    public function setNumeracao($valor) { $this->stNumeracao      = $valor; }

    /* getters */
    public function getImagemCarne() { return $this->ImagemCarne      ; }
    public function getExercicio() { return $this->stExercicio      ; }
    public function getInscricao() { return $this->inInscricao      ; }
    public function getCodDivida() { return $this->inCodDivida      ; }
    public function getTributo() { return $this->stTributo        ; }
    public function getParcela() { return $this->stParcela        ; }
    public function getReferencia() { return $this->inReferencia     ; }
    public function getProcessamento() { return $this->dtProcessamento  ; }
    public function getVencimento() { return $this->dtVencimento     ; }
    public function getVencimento1() { return $this->dtVencimentof1   ; }
    public function getVencimento2() { return $this->dtVencimentof2   ; }
    public function getVencimento3() { return $this->dtVencimentof3   ; }
    public function getValorCotaUnica() { return $this->flValorCotaUnica ; }
    public function getValor() { return $this->flValor          ; }
    public function getValor1() { return $this->flValorf1        ; }
    public function getValor2() { return $this->flValorf2        ; }
    public function getValor3() { return $this->flValorf3        ; }
    public function getNomCgm() { return $this->stNomCgm         ; }
    public function getBarCode() { return $this->stBarCode        ; }
    public function getLinhaCode() { return $this->stLinhaCode      ; }
    public function getParcelaUnica() { return $this->boParcelaUnica   ; }
    public function getObservacaoL1() { return $this->stObservacaoL1   ; }
    public function getObservacaoL2() { return $this->stObservacaoL2   ; }
    public function getObservacaoL3() { return $this->stObservacaoL3   ; }
    public function getNumeracao() { return $this->stNumeracao      ; }

    /* configura carne */
    public function configuraCarne()
    {
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
    }

    /* layout do carne */
    public function drawCarne($x, $y)
    {
        ;

        $inTamY = 0.9;
        $this->setFont( 'Arial','',10 );

        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, ($x+1), ($y+1), 20, 13.5, $stExt );
        }

        /* returna retangulo */
        $this->Rect( $x, $y, 92-20, (90*$inTamY) );
        $this->Rect( ($x+95-20), $y, 102+20, (74*$inTamY) );

        /* linha horizontais */
        $this->Line( $x, ($y+(16*$inTamY)), (92-20+$x), ($y+(16*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(16*$inTamY)), ($x+197), ($y+(16*$inTamY)) );

        $this->Line( $x, ($y+(23*$inTamY)), (92-20+$x), ($y+(23*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(23*$inTamY)), ($x+197), ($y+(23*$inTamY)) );

        $this->Line( $x, ($y+(30*$inTamY)), (92-20+$x), ($y+(30*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(30*$inTamY)), ($x+197), ($y+(30*$inTamY)) );

        $this->Line( $x, ($y+(37*$inTamY)), (92-20+$x), ($y+(37*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(37*$inTamY)), ($x+197), ($y+(37*$inTamY)) );

        /* linhas horizontais pontilhadas */
/*
        for ($a=0;$a<=10;($a+=5)) {
            for ($i=0;$i<=90;($i+=2)) {
                $this->Line( ($x+$i), ($y+((42+$a)*$inTamY)), ($x+$i+1), ($y+((42+$a)*$inTamY)) );
            }
            for ($i=95;$i<=196;($i+=2)) {
                $this->Line( ($x+$i), ($y+((42+$a)*$inTamY)), ($x+$i+1), ($y+((42+$a)*$inTamY)) );
            }
        }
*/
        $this->Line( $x, ($y+(57*$inTamY)), ($x+92-20), ($y+(57*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(57*$inTamY)), ($x+197), ($y+(57*$inTamY)) );

        // linha somnete na esquerda, sobre a observação
        $this->Line( $x, ($y+(66.5*$inTamY)), ($x+92-20), ($y+(66.5*$inTamY)) );
        $this->Line( ($x+95-20), ($y+(66.5*$inTamY)), ($x+197), ($y+(66.5*$inTamY)) );
        //
        $this->Line( ($x), ($y+(74*$inTamY)), ($x+92-20), ($y+(74*$inTamY)) );

        /* linhas verticais */
        $this->Line( ($x+27), ($y+(16*$inTamY)), ($x+27), ($y+(57*$inTamY)) );
        $this->Line( ($x+43), ($y+(16*$inTamY)), ($x+43), ($y+(57*$inTamY)) );
        $this->Line( ($x+56), ($y+(30*$inTamY)), ($x+56), ($y+(57*$inTamY)) );
        $this->Line( ($x+71), ($y+(30*$inTamY)), ($x+71), ($y+(57*$inTamY)) );

        $this->Line( ($x+122-20), ($y+(16*$inTamY)), ($x+122-20), ($y+(57*$inTamY)) );
        $this->Line( ($x+138-20), ($y+(16*$inTamY)), ($x+138-20), ($y+(57*$inTamY)) );
        $this->Line( ($x+151-20), ($y+(30*$inTamY)), ($x+151-20), ($y+(57*$inTamY)) );
        $this->Line( ($x+166-20), ($y+(30*$inTamY)), ($x+166-20), ($y+(57*$inTamY)) );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, (9*$inTamY), 25, 16.5, $stExt );
        }

        $this->setFont('Arial','B',8);
        $this->Text   ( ($x+27) , ($y+(4*$inTamY)) , $this->lblTitulo1 );
        $this->Text   ( ($x+122-20), ($y+(4*$inTamY)) , $this->lblTitulo1 );

        $this->Text   ( ($x+22) , ($y+(13*$inTamY)), $this->lblExercicio );
        $this->Text   ( ($x+117-20), ($y+(13*$inTamY)), $this->lblExercicio );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim')
            $this->lblTitulo2 = "";

        $this->setFont('Arial','',8);
        $this->Text   ( ($x+50) , ($y+(8*$inTamY)) , $this->lblTitulo2 );
        $this->Text   ( ($x+145-20), ($y+(8*$inTamY)) , $this->lblTitulo2 );

        $this->setFont('Arial'  ,'B',6);
        $this->Text   ( ($x+1)  , ($y+(18.5*$inTamY)), $this->lblInscricao    );
        $this->Text   ( ($x+96-20) , ($y+(18.5*$inTamY)), $this->lblInscricao    );
        $this->Text   ( ($x+1)  , ($y+(25.5*$inTamY)), $this->lblParcela      );
        $this->Text   ( ($x+96-20) , ($y+(25.5*$inTamY)), $this->lblParcela      );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( ($x+1)  , ($y+(59.5*$inTamY)), 'ADQUIRENTE'         );
            $this->Text   ( ($x+96-20) , ($y+(59.5*$inTamY)), 'ADQUIRENTE'         );
        } else {
            $this->Text   ( ($x+1)  , ($y+59.5), $this->lblContribuinte );
            $this->Text   ( ($x+96-20) , ($y+59.5), $this->lblContribuinte );
        }
        //nosso numero
        $this->Text   ( ($x+1)  , ($y+(69.0*$inTamY)), $this->lblNumeracao    );
        $this->Text   ( ($x+96-20) , ($y+(69.0*$inTamY)), $this->lblNumeracao    );

        $this->Text   ( ($x+28) , ($y+(18.5*$inTamY)), $this->lblCodDivida    );
        $this->Text   ( ($x+123-20), ($y+(18.5*$inTamY)), $this->lblCodDivida    );
        $this->Text   ( ($x+28) , ($y+(25.5*$inTamY)), $this->lblReferencia   );
        $this->Text   ( ($x+123-20), ($y+(25.5*$inTamY)), $this->lblReferencia   );

        $this->Text   ( ($x+44) , ($y+(18.5*$inTamY)), $this->lblTributo      );
        $this->Text   ( ($x+139-20), ($y+(18.5*$inTamY)), $this->lblTributo      );
        $this->Text   ( ($x+44) , ($y+(25.5*$inTamY)), $this->lblDataProcessamento );
        $this->Text   ( ($x+139-20), ($y+(25.5*$inTamY)), $this->lblDataProcessamento );

        if (!$this->boParcelaUnica) {

            $this->Text   ( ($x+1)  , ($y+(32.5*$inTamY)), $this->lblData         );
            $this->Text   ( ($x+96-20) , ($y+(32.5*$inTamY)), $this->lblData         );
            $this->Text   ( ($x+28) , ($y+(32.5*$inTamY)), $this->lblCorrecao     );
            $this->Text   ( ($x+29) , ($y+(35.5*$inTamY)), $this->lblMonetaria    );
            $this->Text   ( ($x+123-20), ($y+(32.5*$inTamY)), $this->lblCorrecao     );
            $this->Text   ( ($x+124-20), ($y+(35.5*$inTamY)), $this->lblMonetaria    );
            $this->Text   ( ($x+44) , ($y+(32.5*$inTamY)), $this->lblMulta        );
            $this->Text   ( ($x+139-20), ($y+(32.5*$inTamY)), $this->lblMulta        );
            $this->Text   ( ($x+59) , ($y+(32.5*$inTamY)), $this->lblJuros        );
            $this->Text   ( ($x+154-20), ($y+(32.5*$inTamY)), $this->lblJuros        );
            $this->Text   ( ($x+73) , ($y+(32.5*$inTamY)), $this->lblValorParcela );
            $this->Text   ( ($x+168-20), ($y+(32.5*$inTamY)), $this->lblValorParcela );
            $this->Text   ( ($x+77) , ($y+(34.5*$inTamY)), $this->lblReal         );
            $this->Text   ( ($x+172-20), ($y+(34.5*$inTamY)), $this->lblReal         );

            $this->Text   ( ($x+48) , ( $y+(40*$inTamY) ), $this->lblMulta1       );
            $this->Text   ( ($x+142-20), ( $y+(40*$inTamY) ), $this->lblMulta1       );
            $this->Text   ( ($x+48) , ( $y+(45*$inTamY) ), $this->lblMulta2       );
            $this->Text   ( ($x+142-20), ( $y+(45*$inTamY) ), $this->lblMulta2       );
            $this->Text   ( ($x+48) , ( $y+(50*$inTamY) ), $this->lblMulta3       );
            $this->Text   ( ($x+142-20), ( $y+(50*$inTamY) ), $this->lblMulta3       );
            $this->Text   ( ($x+48) , ( $y+(55*$inTamY) ), $this->lblMulta4       );
            $this->Text   ( ($x+142-20), ( $y+(55*$inTamY) ), $this->lblMulta4       );

            $this->Text   ( ($x+63) , ( $y+(40*$inTamY) ), $this->lblJuros1       );
            $this->Text   ( ($x+157-20), ( $y+(40*$inTamY) ), $this->lblJuros1       );
            $this->Text   ( ($x+63) , ( $y+(45*$inTamY) ), $this->lblJuros2       );
            $this->Text   ( ($x+157-20), ( $y+(45*$inTamY) ), $this->lblJuros2       );
            $this->Text   ( ($x+63) , ( $y+(50*$inTamY) ), $this->lblJuros3       );
            $this->Text   ( ($x+157-20), ( $y+(50*$inTamY) ), $this->lblJuros3       );
            $this->Text   ( ($x+63) , ( $y+(55*$inTamY) ), $this->lblJuros4       );
            $this->Text   ( ($x+157-20), ( $y+(55*$inTamY) ), $this->lblJuros4       );
        } else {
            /* retangulo para parcela unica */
            $this->setFillColor( 240 );
            $this->Rect   ( ($x), ($y+(30*$inTamY)), 92-20, 27*$inTamY, 'DF' );
            $this->Rect   ( ($x+95-20), ($y+(30*$inTamY)), 102+20, 27*$inTamY, 'DF' );
            $this->setFillColor( 0 );
        }
    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        $inTamY = 0.9;
        $this->setFont('Arial', 'B', 8 );

        $this->Text   ( ($x+38) , ($y+(13*$inTamY))  , $this->stExercicio );
        $this->Text   ( ($x+133-20), ($y+(13*$inTamY))  , $this->stExercicio );

        $this->Text   ( ($x+14) , ($y+(22*$inTamY))  , $this->inInscricao );
        $this->Text   ( ($x+110-20), ($y+(22*$inTamY))  , $this->inInscricao );
        $this->Text   ( ($x+14) , ($y+(29*$inTamY))  , $this->stParcela   );
        $this->Text   ( ($x+110-20), ($y+(29*$inTamY))  , $this->stParcela   );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( ($x+1)  , ($y+(64*$inTamY))  , $this->stAdquirente );
            $this->Text   ( ($x+96-20) , ($y+(64*$inTamY))  , $this->stAdquirente );
        } else {
            $this->Text   ( ($x+1)  , ($y+(64*$inTamY))  , $this->stNomCgm    );
            $this->Text   ( ($x+96-20) , ($y+(64*$inTamY))  , $this->stNomCgm    );
        }

        $this->Text   ( ($x+34) , ($y+(22*$inTamY))  , $this->inCodDivida );
        $this->Text   ( ($x+128-20), ($y+(22*$inTamY))  , $this->inCodDivida );
        $this->Text   ( ($x+34) , ($y+(29*$inTamY))  , $this->inReferencia);
        $this->Text   ( ($x+128-20), ($y+(29*$inTamY))  , $this->inReferencia);

        $this->Text   ( ($x+44) , ($y+(22*$inTamY))  , $this->stTributo   );
        $this->Text   ( ($x+139-20), ($y+(22*$inTamY))  , $this->stTributo   );
        $this->Text   ( ($x+44) , ($y+(29*$inTamY))  , $this->dtProcessamento );
        $this->Text   ( ($x+139-20), ($y+(29*$inTamY))  , $this->dtProcessamento );

        if (!$this->boParcelaUnica) {
            $this->setFont('Arial', 'B', 8 );
            $this->Text   ( ($x+10) , ($y+(40.5*$inTamY)), $this->dtVencimento);
            $this->Text   ( ($x+105-20), ($y+(40.5*$inTamY)), $this->dtVencimento);

            $this->setFont('Arial', '', 8 );
            if ($this->dtVencimentof1) {
                $this->Text   ( ($x+10) , ($y+(46.5*$inTamY)), $this->dtVencimentof1);
                $this->Text   ( ($x+105-20), ($y+(46.5*$inTamY)), $this->dtVencimentof1);
                if ($this->dtVencimentof2) {
                    $this->Text   ( ($x+10) , ($y+(51.5*$inTamY)), $this->dtVencimentof2);
                    $this->Text   ( ($x+105-20), ($y+(51.5*$inTamY)), $this->dtVencimentof2);
                    if ($this->dtVencimentof3) {
                        $this->Text   ( ($x+10) , ($y+(56.5*$inTamY)), $this->dtVencimentof3);
                        $this->Text   ( ($x+105-20), ($y+(56.5*$inTamY)), $this->dtVencimentof3);
                    }
                }
            }

            $this->setFont('Arial', 'B', 8 );
            $this->Text   ( ($x+80-20) , ($y+(40.5*$inTamY)), $this->flValor);
            $this->Text   ( ($x+177), ($y+(40.5*$inTamY)), $this->flValor);
            $this->setFont('Arial', '', 8 );
            $this->Text   ( ($x+80-20) , ($y+(46.5*$inTamY)), $this->flValorf1);
            $this->Text   ( ($x+177), ($y+(46.5*$inTamY)), $this->flValorf1);
            $this->Text   ( ($x+80-20) , ($y+(51.5*$inTamY)), $this->flValorf2);
            $this->Text   ( ($x+177), ($y+(51.5*$inTamY)), $this->flValorf2);
            $this->Text   ( ($x+80-20) , ($y+(56.5*$inTamY)), $this->flValorf3);
            $this->Text   ( ($x+177), ($y+(56.5*$inTamY)), $this->flValorf3);
        } else {
            $this->Text   ( ($x+1)  , ($y+(33*$inTamY)), $this->lblVencimento );
            $this->Text   ( ($x+96) , ($y+(33*$inTamY)), $this->lblVencimento );
            $this->Text   ( ($x+63-20) , ($y+(33*$inTamY)), $this->lblValorCotaUnica );
            $this->Text   ( ($x+167), ($y+(33*$inTamY)), $this->lblValorCotaUnica );

            $this->setFont('Arial', 'B', 12 );
            $this->Text   ( ($x+15) , ($y+(45*$inTamY)), $this->dtVencimento );
            $this->Text   ( ($x+110), ($y+(45*$inTamY)), $this->dtVencimento );
            $this->Text   ( ($x+70-20) , ($y+(45*$inTamY)), $this->flValor );
            $this->Text   ( ($x+174), ($y+(45*$inTamY)), $this->flValor );
            $this->setFont('Arial', 'B', 8 );

        }
        $this->setFont('Arial', 'B', 8 );
        $this->Text   ( ($x+52-20)  , ($y+(71.5*$inTamY))  , $this->stNumeracao    );
        $this->Text   ( ($x+157) , ($y+(71.5*$inTamY))  , $this->stNumeracao    );

        $this->setFont('Arial', '', 8 );

        if (Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( ($x+1)  , ($y+(77*$inTamY))   , 'Não receber após o vencimento ');
            $this->Text   ( ($x+1)  , ($y+(80.5*$inTamY)) , 'Pagável somente no Banco do Brasil');
        } else {
            $this->Text   ( ($x+1)  , ($y+(77*$inTamY))   , $this->stObservacaoL1 );
            $this->Text   ( ($x+1)  , ($y+(80.5*$inTamY)) , $this->stObservacaoL2 );
            $this->Text   ( ($x+1)  , ($y+(84*$inTamY))   , $this->stObservacaoL3 );
        }

        $this->setFont('Arial', '', 8 );
        $this->Text   ( ($x+95-20), ($y+(78*$inTamY)), $this->stLinhaCode );

        //$this->setFont('Arial', '', 5 );

        $this->defineCodigoBarras( ($x+95-20), ($y+(80*$inTamY)), $this->stBarCode );
    }

    /* adiciona nova pagina */
    public function novaPagina()
    {
        $this->addPage();
    }

    /* habilita e desabilita a quebra de pagina automatica */
    public function setQuebraPagina($valor)
    {
        $this->setAutoPageBreak( $valor, 1 );
    }

    /* picote entre os carnes */
    public function setPicote($x, $y, $firstPage = false)
    {
        $inTamY = 0.9;

        for ($i=0;$i<=196;($i+=2)) {
           $this->Line( ($x+$i), ($y+(93*$inTamY)), ($x+$i+1), ($y+(93*$inTamY)) );
        }
        for (($i=-3);$i<=92;($i+=2)) {
            $this->Line( ($x+93.5-20), ($y+($i*$inTamY)), ($x+93.5-20), ($y+(($i+1)*$inTamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

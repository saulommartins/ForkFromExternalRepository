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

    * $Id: RCarneDiversosPetropolis.class.php 59612 2014-09-02 12:00:51Z gelson $

  Caso de uso: uc-05.03.11
**/

/*
$Log$
Revision 1.14  2007/02/15 15:04:48  cercato
setando observacao nos carnes.

Revision 1.13  2007/02/06 18:40:02  cercato
alteracao no aviso do carne

Revision 1.12  2007/01/26 17:00:27  fabio
atualizado para versao de MATA DE SÃO JOÃO

Revision 1.11  2006/09/15 11:37:31  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

Revision 1.10  2006/09/15 10:26:13  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
include_once 'RProtocoloPetropolis.class.php';
define       ('FPDF_FONTPATH','font/');

class RCarneDiversosPetropolis extends RProtocoloPetropolis
{
    /* labels */
    public $lblTitulo1 = 'MATA DE SÃO JOÃO - Sec. de Adm. e Fin.';
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
    public $lblObs       = 'OBSERVAÇÃO';

    public $lblMulta = '(+) MULTA DE MORA';
    public $lblJuros = '(+) JUROS DE MORA';
    public $lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

    public $lblValorParcela = 'VALOR PARCELA';
    public $lblReal = '(REAL)';
    public $lblNumeracao = 'NOSSO NÚMERO';

    public $lblValorPrincipal = "(=) VALOR PRINCIPAL";
    public $lblValorTotal     = "(=) TOTAL";

    /* variaveis */
    public $ImagemCarne;
    public $stExercicio;
    public $inInscricao;
    public $inCodDivida;
    public $stTributo;
    public $stTributoAbrev;
    public $stTributoAbrev2;
    public $stTributoAbrev3;
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
    public $stObsVencimento; // = "Não receber após o vencimento.";
    public $stNumeracao;
    public $flValorMulta  = '0,00';
    public $flValorJuros  = '0,00';
    public $flValorOutros = '0,00';
    public $flValorTotal  = '0,00';
    public $tamY = 0.93;

    /* setters */
    public function setImagemCarne($valor) { $this->ImagemCarne      = $valor; }
    public function setExercicio($valor) { $this->stExercicio      = $valor; }
    public function setInscricao($valor) { $this->inInscricao      = $valor; }
    public function setCodDivida($valor) { $this->inCodDivida      = $valor; }
    public function setTributo($valor) { $this->stTributo        = $valor; }
    public function setTributoAbrev($valor) { $this->stTributoAbrev   = $valor; }
    public function setTributoAbrev2($valor) { $this->stTributoAbrev2  = $valor; }
    public function setTributoAbrev3($valor) { $this->stTributoAbrev3  = $valor; }
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
    public function setObsVencimento($valor) { $this->stObsVencimento  = $valor; }
    public function setNumeracao($valor) { $this->stNumeracao      = $valor; }
    public function setValorTotal($valor) { $this->flValorTotal     = $valor; }

    /* getters */
    public function getImagemCarne() { return $this->ImagemCarne      ; }
    public function getExercicio() { return $this->stExercicio      ; }
    public function getInscricao() { return $this->inInscricao      ; }
    public function getCodDivida() { return $this->inCodDivida      ; }
    public function getTributo() { return $this->stTributo        ; }
    public function getTributoAbrev() { return $this->stTributoAbrev   ; }
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
    public function getObsVencimento() { return $this->stObsVencimento  ; }
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
        // truncar tributo
        if ( !$this->stTributoAbrev )
            $this->stTributoAbrev = substr($this->stTributo,0,25);

        $this->stNomCgm  = substr($this->stNomCgm ,0,80);
        $this->setFont( 'Arial','',10 );

        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x, $y, 17, 10, $stExt );
        }

        /* returna retangulo */
        //$this->Rect( $x, $y, 92, 75 );
        $this->Rect( $x, $y, 92-20, 80*$this->tamY );
        $this->Rect( ($x+95-20), $y, 102+20, 59*$this->tamY );

        /* linha horizontais */
        $this->Line( $x, ($y+(11*$this->tamY)), (92-20+$x), ($y+(11*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(11*$this->tamY)), ($x+197), ($y+(11*$this->tamY)) );

        $this->Line( $x, ($y+(18*$this->tamY)), (92-20+$x), ($y+(18*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(18*$this->tamY)), ($x+197), ($y+(18*$this->tamY)) );

        $this->Line( $x, ($y+(25*$this->tamY)), (92-20+$x), ($y+(25*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(25*$this->tamY)), ($x+197), ($y+(25*$this->tamY)) );

        $this->Line( $x+46, ($y+(32*$this->tamY)), (92-20+$x), ($y+(32*$this->tamY)) );
        $this->Line( ($x+95-20), ($y+(32*$this->tamY)), ($x+197), ($y+(32*$this->tamY)) );

        $this->Line( $x+36, ($y+(39*$this->tamY)), (92-20+$x), ($y+(39*$this->tamY)) );
        $this->Line( ($x+151-20), ($y+(39*$this->tamY)), ($x+197), ($y+(39*$this->tamY)) );

        $this->Line( $x, ($y+(46*$this->tamY)), (92-20+$x), ($y+(46*$this->tamY)) );
        // linhas adicionais no carne no lado esquerdo

        $this->Line( $x, ($y+(68*$this->tamY)), (92-20+$x), ($y+(68*$this->tamY)) ); //nova

        $this->Line( $x, ($y+(53*$this->tamY)), (92-20+$x), ($y+(53*$this->tamY)) );
        $this->Line( $x, ($y+(60*$this->tamY)), (92-20+$x), ($y+(60*$this->tamY)) );

        $this->Line( ($x+95-20), ($y+(46*$this->tamY)), ($x+197), ($y+(46*$this->tamY)) );

        /* linhas verticais */
        $this->Line( ($x+56-10), ($y+(11*$this->tamY)), ($x+56-10), ($y+(46*$this->tamY)) );
        $this->Line( ($x+18), ($y+(18*$this->tamY)), ($x+18), ($y+(25*$this->tamY)) );

        $this->Line( ($x+151), ($y+(11*$this->tamY)), ($x+151), ($y+(35*$this->tamY)) );
        $this->Line( ($x+113), ($y+(18*$this->tamY)), ($x+113), ($y+(25*$this->tamY)) );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, 9, 25, 16.5, $stExt );
        }

        $this->setFont('Arial','B',8);
        $this->Text   ( ($x+27) , ($y+(4*$this->tamY)) , "MATA DE SÃO JOÃO" );
        $this->Text   ( ($x+27) , ($y+(7*$this->tamY)) , "Sec. de Adm. e Fin." );

        $this->Text   ( ($x+122-20), ($y+(4*$this->tamY)) , $this->lblTitulo1 );

        $this->Text   ( ($x+27) , ($y+(10*$this->tamY)), $this->lblExercicio );
        $this->Text   ( ($x+117-20), ($y+(10*$this->tamY)), $this->lblExercicio );

        $this->setFont('Arial'  ,'B',6);
        $this->Text   ( ($x+1)  , ($y+(13.5*$this->tamY)), $this->lblNumeracao    );
        $this->Text   ( ($x+96-20) , ($y+(13.5*$this->tamY)), $this->lblNumeracao    );

        $this->Text   ( ($x+1)  , ($y+(20.5*$this->tamY)), $this->lblParcela      );
        $this->Text   ( ($x+96-20) , ($y+(20.5*$this->tamY)), $this->lblParcela      );
        $this->Text   ( ($x+27) , ($y+(20.5*$this->tamY)), $this->lblInscricao      );
        $this->Text   ( ($x+122), ($y+(20.5*$this->tamY)), $this->lblInscricao    );

        $this->Text   ( ($x+1)  , ($y+(27.5*$this->tamY)),  $this->lblTributo   );
        $this->Text   ( ($x+96-20) , ($y+(27.5*$this->tamY)), $this->lblTributo   );

        /* retangulo para vencimento */
        $this->setFillColor( 240 );
        $this->Rect   ( ($x), ($y+(39*$this->tamY)), 46, 14*$this->tamY, 'DF' );
        $this->Rect   ( ($x+95-20), ($y+(32*$this->tamY)), 56+20, 14*$this->tamY, 'DF' );
        $this->setFillColor( 0 );

        $this->Text   ( ($x+1)  , ($y+(41.5*$this->tamY)), $this->lblVencimento   );
        $this->Text   ( ($x+96-20) , ($y+(34.5*$this->tamY)), $this->lblVencimento   );

        $this->Text   ( ($x+58-10) , ($y+(13.5*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+154), ($y+(13.5*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+58-10) , ($y+(20.5*$this->tamY)), $this->lblMulta    );
        $this->Text   ( ($x+154), ($y+(20.5*$this->tamY)), $this->lblMulta    );
        $this->Text   ( ($x+58-10) , ($y+(27.5*$this->tamY)), $this->lblJuros    );
        $this->Text   ( ($x+154), ($y+(27.5*$this->tamY)), $this->lblJuros    );
        $this->Text   ( ($x+58-10) , ($y+(34.5*$this->tamY)), "(+) AT. MONET."   );
        $this->Text   ( ($x+154), ($y+(34.5*$this->tamY)), $this->lblOutros   );
        $this->Text   ( ($x+58-10) , ($y+(41.5*$this->tamY)), $this->lblValorTotal );
        $this->Text   ( ($x+154), ($y+(41.5*$this->tamY)), $this->lblValorTotal );

        // nao receber apos vencimento
        //if ( substr( $this->stLinhaCode, 0, 3) == '817' ) { //diferente de unica aparece esta mensagem
        if ( !$this->getObsVencimento() ) {
            $this->setObsVencimento ( 'Receber até 31/12/2007.' );
        }
        //}

        $this->Text   ( ($x+1)  , ($y+(52*$this->tamY)), $this->getObsVencimento() );
        $this->Text   ( ($x+96-20) , ($y+(45*$this->tamY)), $this->getObsVencimento() );
//67.5, 60.5, 53.5
        // contribuinte
        $this->Text   ( ($x+1)  , ($y+(55.5*$this->tamY)), $this->lblContribuinte );
        $this->Text   ( ($x+96-20) , ($y+(48.5*$this->tamY)), $this->lblContribuinte );

        // endereço do imovel
        $this->Text   ( ($x+1)  , ($y+(62.5*$this->tamY)), 'ENDEREÇO DO IMÓVEL' );

        // obs
        $this->Text   ( ($x+1)  , ($y+(71*$this->tamY) ), $this->lblObs          );

        // vias
        $this->setFont( 'Arial','I',6 );
        $this->Text   ( ($x+70-20) , ($y+(71*$this->tamY)), 'VIA CONTRIBUINTE');
        $this->Text   ( ($x+178), ($y+(56.5*$this->tamY)), 'VIA PREFEITURA');

        $this->setFont( 'Arial','', 6 );
        $this->stObservacao .= "MULTA 5% Até 30 DIAS, 10% DE 30 A 60 DIAS E 15% SUPERIOR A 60 DIAS. JUROS DE 1% AO MÊS = 0,033%";
        $stObs = str_replace("\n\r"," ",$this->stObservacao);

        $this->Text   ( 8     , $y+(74*$this->tamY)  , substr($stObs,0      ,62 ));
        $this->Text   ( 8     , $y+(77*$this->tamY)  , substr($stObs,62     ,60 ));
        $this->Text   ( 8     , $y+(80*$this->tamY)  , substr($stObs,122    ,60 ));
    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        $this->setFont('Arial', 'B', 8 );
        //falta aki
        $this->Text   ( ($x+43) , ($y+(10*$this->tamY))  ,$this->stExercicio ); // ok
        $this->Text   ( ($x+133-20), ($y+(10*$this->tamY))  ,$this->stExercicio ); // ok

        $this->Text   ( ($x+72-20) , ($y+(17*$this->tamY)), $this->flValor ); // ok
        $this->Text   ( ($x+166), ($y+(17*$this->tamY)), $this->flValor ); // ok
        $this->Text   ( ($x+72-20) , ($y+(23*$this->tamY)), $this->flValorMulta ); // ok  multa
        $this->Text   ( ($x+166), ($y+(23*$this->tamY)), $this->flValorMulta ); // ok  multa
        $this->Text   ( ($x+72-20) , ($y+(31*$this->tamY)), $this->flValorJuros ); // ok  juros
        $this->Text   ( ($x+166), ($y+(31*$this->tamY)), $this->flValorJuros ); // ok  juros
        $this->Text   ( ($x+72-20) , ($y+(38*$this->tamY)), $this->flValorOutros); // ok  outros
        $this->Text   ( ($x+166), ($y+(38*$this->tamY)), $this->flValorOutros); // ok  outros
        $this->Text   ( ($x+72-20) , ($y+(45*$this->tamY)), $this->flValorTotal ); // ok  total
        $this->Text   ( ($x+166), ($y+(45*$this->tamY)), $this->flValorTotal ); // ok  total

//57
        $this->Text   ( ($x+2), ($y+(59*$this->tamY)), $this->getNomCgm() ); // ok  contribuinte
        $this->Text   ( ($x+106-20),($y+(52*$this->tamY)), $this->getNomCgm() ); // ok  contribuinte

        $this->Text   ( ($x+2), ($y+(66*$this->tamY)), substr($this->getRua(), 0, 46) ) ; // end . do imovel

        $this->Text   ( ($x+14) , ($y+(17*$this->tamY))  , $this->stNumeracao ); // ok
        $this->Text   ( ($x+110-20), ($y+(17*$this->tamY))  , $this->stNumeracao ); // ok

        $this->Text   ( ($x+2) , ($y+(31*$this->tamY))  , $this->stTributoAbrev ); // ok
        $this->Text   ( ($x+2) , ($y+(34*$this->tamY))  , $this->stTributoAbrev2 ); // ok
        $this->Text   ( ($x+2) , ($y+(37*$this->tamY))  , $this->stTributoAbrev3 ); // ok

        $this->Text   ( ($x+110-34), ($y+(31*$this->tamY))  , $this->stTributo ); // ok

        $this->Text   ( ($x+05) , ($y+(24*$this->tamY))  , $this->stParcela   ); // ok
        $this->Text   ( ($x+101-20), ($y+(24*$this->tamY))  , $this->stParcela   ); // ok

        $this->Text   ( ($x+20) , ($y+(24*$this->tamY))  , $this->inInscricao   ); // ok
        $this->Text   ( ($x+115), ($y+(24*$this->tamY))  , $this->inInscricao   ); // ok

        $this->setFont('Arial', 'B', 12 );
        $this->Text   ( ($x+15) , ($y+(47*$this->tamY)), $this->dtVencimento ); // ok
        $this->Text   ( ($x+110-20), ($y+(40*$this->tamY)), $this->dtVencimento ); // ok
        $this->setFont('Arial', '', 8 );

        $this->Text   ( ($x+95-20), ($y+(63*$this->tamY)), $this->stLinhaCode );
        $this->defineCodigoBarras( ($x+95-15), ($y+(65*$this->tamY)), $this->stBarCode );
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
        for ($i=0;$i<=196;($i+=2)) {
           $this->Line( ($x+$i), ($y+(82*$this->tamY)), ($x+$i+1), ($y+(82*$this->tamY)) );
        }

        for (($i=-3);$i<=81;($i+=2)) {
            $this->Line( ($x+93.5-20), ($y+($i*$this->tamY)), ($x+93.5-20), ($y+(($i+1)*$this->tamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

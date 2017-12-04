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
    * Carne Escrituracao de Mariana Pimentel
    * Data de Criação: 31/10/2006

    * @author Desenvolvedor: Fernando Piccini Cercato

    * @package URBEM
    * @subpackage Componentes

    * $Id: RCarneEscrituracaoMarianaPimentel.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.03.11
*/

require_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
define       ('FPDF_FONTPATH','/var/www/sw1/gestaoAdministrativa/fontes/PHP/FPDF/fonts/');

class RCarneMarianaPimentel extends FPDF
{
    /**
     * @access private
     * @var String Numero da Nota
     */
    public $stNumeroNota;

    /**
     * @access private
     * @var String Competencia
     */
    public $stCompetencia;

    /**
     * @access private
     * @var String Nome da Prefeitura
     */
    public $stNomePrefeitura;
    /**
     * @access private
     * @var String SubStitulo, abaixo da Prefeitura
     */
    public $stSubTitulo;
    /**
     * @access private
     * @var Integer Inscrição Municipal
     */
    public $stInscricaoMunicipal;
    /**
     * @access private
     * @var Integer Exercicio
     */
    public $stExercicio;
    /**
     * @access private
     * @var Integer Numero do Aviso
     */
    public $stNumAviso;
    /**
     * @access private
     * @var String Receita
     */
    public $stReceita;
    /**
     * @access private
     * @var String Numero da Parcela ( Unica ou 1 2 3 ...)
     */
    public $stParcela;
    /**
     * @access private
     * @var String Razão Social da EMpresa ou Nome
     */
    public $stRazaoSocial;
    /**
     * @access private
     * @var String Atividade
     */
    public $stAtividade;
    /**
     * @access private
     * @var String Endereço
     */
    public $stEndereco;
    /**
     * @access private
     * @var String Nome Fantasia
     */
    public $stNomeFantasia;
    /**
     * @access private
     * @var Array Observacoes
     */
    public $arObservacoes;
    /**
     * @access private
     * @var String Linha Digitavel do Codigo de Barras
     */
    public $stLinhaDigitavel;
    /**
     * @access private
     * @var String Codigo de Barras
     */
    public $stCodigoBarras;
    /**
     * @access private
     * @var Date Vencimento
     */
    public $dtVencimento;
    /**
     * @access private
     * @var Double Valor
     */
    public $doValor;
    /**
     * @access private
     * @var Double Multa
     */
    public $doMulta;
    /**
     * @access private
     * @var Double Juros
     */
    public $doJuros;
    /**
     * @access private
     * @var Double Total
     */
    public $doTotal;

    /**
     * Construtor
     */
    public function RCarneMarianaPimentel()
    {
        parent::FPDF();
        /**
         * Seta Informações Basicas da Prefeitura
         */
        $this->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MARIANA PIMENTEL';
        $this->stSubTitulo = 'DAM - Documento de Arrecadação Municipal';

        /**
         * Seta Configuração do PDF
         */
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
    }
    /**
     * @access public
     */
    public function desenhaCarne($x = 0 , $y = 0)
    {
        /* Inicializa Fonte*/
        $this->setFont( 'Arial','',10 );
        $tamX = 1.2;
        $tamY = 1.12;
        /**
         * Retangulos
         */
        /* Retangulo Maior */
        $this->Rect( $x, $y, 150*$tamX, 70*$tamY ); // via banco
        $this->Rect( $x, ($y*$tamY) + 80 , 150*$tamX, 70*$tamY ); // via contrb

        /* Retangulo do Vencimento, valor, etc ... */
        $this->Rect( $x+(123*$tamX) , $y + (8*$tamY) , 25*$tamX, 44*$tamY); // via banco
        $this->Rect( $x+(123*$tamX) , ($y*$tamY) + (88*$tamY) , 25*$tamX, 44*$tamY); // via contrib

        /* Retangulo da Inscrica, Exercicio, etc  ... */
        $this->Rect( $x+(5*$tamX) , $y + (10*$tamY) , 118*$tamX, 8*$tamY); // via banco
        $this->Rect( $x+(5*$tamX) , ($y*$tamY) + (90*$tamY) , 118*$tamX, 8*$tamY); // via contrib

        /* Retangulo da Razao, Atividade, Endereco .... */
        $this->Rect( $x+(5*$tamX) , $y + (18*$tamY) , 118*$tamX, 9*$tamY); // via banco
        $this->Rect( $x+(5*$tamX) , ($y*$tamY) + (98*$tamY) , 118*$tamX, 9*$tamY); // via contrib

        /* Retangulo do Nome Fantasia ... */
        $this->Rect( $x+(5*$tamX) , $y + (27*$tamY) , 118*$tamX, 9*$tamY); // via banco
        $this->Rect( $x+(5*$tamX) , ($y*$tamY) + (107*$tamY) , 118*$tamX, 9*$tamY); // via contrib

        /**
         *  Linhas Verticais
         */
        /* entre inscricao e exercicio */
        $this->Line( $x+(28*$tamX) , $y+(10*$tamY) , $x+(28*$tamX) , $y+(18*$tamY) ); // via banco
        $this->Line( $x+(28*$tamX) , ($y*$tamY)+(90*$tamY) , $x+(28*$tamX) , ($y*$tamY)+(98*$tamY) ); // via contrib

        /* entre exercicio e aviso */
        $this->Line( $x+(50*$tamX) , $y+(10*$tamY) , $x+(50*$tamX) , $y+(18*$tamY)); // via banco
        $this->Line( $x+(50*$tamX) , ($y*$tamY)+(90*$tamY) , $x+(50*$tamX) , ($y*$tamY)+(98*$tamY)); // via contrib

        /* entre aviso e receita */
        $this->Line( $x+(75*$tamX) , $y+(10*$tamY) , $x+(75*$tamX) , $y+(18*$tamY)); // via banco
        $this->Line( $x+(75*$tamX) , ($y*$tamY)+(90*$tamY) , $x+(75*$tamX) , ($y*$tamY)+(98*$tamY)); // via contrib

        /* entre receita e parcela */
        //$this->Line( $x+100 , $y+10 , $x+100 , $y+18); // via banco
        //$this->Line( $x+100 , $y+90 , $x+100 , $y+98); // via contrib

        /**
         * Linhas Horizontais
         */
        $this->Line( $x+(5*$tamX) , $y+(24*$tamY) , 131*$tamX , $y+ (24*$tamY) ); // via banco
        $this->Line( $x+(5*$tamX) , ($y*$tamY)+(104*$tamY) , 131*$tamX , ($y*$tamY)+(104*$tamY) ); // via banco

         /* entre parcela e vencimento */
        $this->Line( $x+(123*$tamX) , $y+(8*$tamY) , $x+(148*$tamX) , $y+ (8*$tamY) ); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+(88*$tamY) , $x+(148*$tamX) , ($y*$tamY)+(88*$tamY) ); // via contrib

        /* entre vencimento e valor */
        $this->Line( $x+(123*$tamX) , $y+(15*$tamY) , $x+(148*$tamX) , $y+(15*$tamY) ); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+(95*$tamY) , $x+(148*$tamX) , ($y*$tamY)+(95*$tamY) ); // via contrib

        /* entre valor e multa */
        $this->Line( $x+(123*$tamX) , $y+ (22*$tamY) , $x+(148*$tamX) , $y+ (22*$tamY)); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+ (102*$tamY) , $x+(148*$tamX) , ($y*$tamY)+ (102*$tamY)); // via contrib

        /* entre multa e juros */
        $this->Line( $x+(123*$tamX) , $y+ (30*$tamY) , $x+(148*$tamX) , $y+ (30*$tamY) ); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+(110*$tamY) , $x+(148*$tamX) , ($y*$tamY)+(110*$tamY) ); // via contrib

        /* entre jutos e total */
        $this->Line( $x+(123*$tamX) , $y+ (37*$tamY) , $x+(148*$tamX) , $y+ (37*$tamY)); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+(117*$tamY) , $x+(148*$tamX) , ($y*$tamY)+(117*$tamY)); // via contrib

        // entre o total e o numeracao
        $this->Line( $x+(123*$tamX) , $y+ (44*$tamY) , $x+(148*$tamX) , $y+ (44*$tamY) ); // via banco
        $this->Line( $x+(123*$tamX) , ($y*$tamY)+(124*$tamY) , $x+(148*$tamX) , ($y*$tamY)+(124*$tamY) ); // via contrib

        /**
         * Titulos
         */
        /* prefeitura */
        $this->setFont('Arial','B',8);
        $this->Text   ( ($x+(37*$tamX) ) , ($y+(3*$tamY) )  , $this->stNomePrefeitura ); // via banco
        $this->Text   ( ($x+(37*$tamX) ) , (($y*$tamY)+(83*$tamY) ) , $this->stNomePrefeitura ); // via contrib

        /* subtitulo*/
        $this->setFont('Arial','',7);
        $this->Text   ( ($x+(45*$tamX) ) , ($y+(6*$tamY))  , $this->stSubTitulo ); // via banco
        $this->Text   ( ($x+(45*$tamX) ) , (($y*$tamY)+(86*$tamY)) , $this->stSubTitulo ); // via contrib

        $this->setFont('Arial','',5);

        $this->Text   ( ($x+(6*$tamX) ) , ($y+(12*$tamY) ) , 'INSCRIÇÃO MUNICIPAL' ); // via banco
        $this->Text   ( ($x+(6*$tamX) ) , (($y*$tamY)+(92*$tamY) ) , 'INSCRIÇÃO MUNICIPAL' ); // via contrib

        $this->Text   ( ($x+(30*$tamX)) , ($y+(12*$tamY)) , 'EXERCICIO'); // via banco
        $this->Text   ( ($x+(30*$tamX)) , (($y*$tamY)+(92*$tamY)) , 'EXERCICIO'); // via contrib

        $this->Text   ( ($x+(52*$tamX)) , ($y+(12*$tamY)) , 'No DE AVISO'); // via banco
        $this->Text   ( ($x+(52*$tamX)) , (($y*$tamY)+(92*$tamY)) , 'No DE AVISO'); // via contrib

        $this->Text   ( ($x+(77*$tamX)) , ($y+(12*$tamY)) , 'RECEITA'); // via banco
        $this->Text   ( ($x+(77*$tamX)) , (($y*$tamY)+(92*$tamY)) , 'RECEITA'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (10*$tamY)) , 'COMPETÊNCIA'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (90*$tamY)) , 'COMPETÊNCIA'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (17*$tamY))  , 'VENCIMENTO'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (97*$tamY)) , 'VENCIMENTO'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (24*$tamY))  , 'VALOR em R$'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (104*$tamY)) , 'VALOR em R$'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (32*$tamY))  , 'MULTA'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (112*$tamY)) , 'MULTA'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (39*$tamY))  , 'JUROS'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (119*$tamY)) , 'JUROS'); // via contrib

        $this->Text   ( ($x+(124*$tamX)) , ($y + (46*$tamY))  , 'TOTAL'); // via banco
        $this->Text   ( ($x+(124*$tamX)) , (($y*$tamY) + (126*$tamY)) , 'TOTAL'); // via contrib

        /* nome fantasia */
        $this->Text   ( ($x+(6*$tamX)) , ($y+(26*$tamY))  , 'NOME FANTASIA'); // via banco
        $this->Text   ( ($x+(6*$tamX)) , (($y*$tamY)+(106*$tamY)) , 'NOME FANTASIA'); // via contrib

        /* numero da nota */
        $this->Text   ( ($x+(6*$tamX)) , ($y+(29*$tamY))  , 'NÚMERO DA NOTA' ); // via banco
        $this->Text   ( ($x+(6*$tamX)) , (($y*$tamY)+(109*$tamY)) , 'NÚMERO DA NOTA' ); // via contrib

        /* instruções */
        $this->Text   ( ($x+(5*$tamX)) , ($y+(40*$tamY))  , 'INSTRUÇÕES'); // via banco
        $this->Text   ( ($x+(5*$tamX)) , (($y*$tamY)+(120*$tamY)) , 'INSTRUÇÕES'); // via contrib

        /* vias */
        $this->Text   ( ($x+(15*$tamX)) , ($y+(67*$tamY))  , ' - VIA DO BANCO - '); // via banco
        $this->Text   ( ($x+(15*$tamX)) , (($y*$tamY)+(137*$tamY)) , ' - VIA DO CONTRIBUINTE - '); // via contrib

        /* autenticacao */
        $this->Text   ( ($x+(5*$tamX)) , ($y+(60*$tamY)) , ' AUTENTICAÇÃO NA OUTRA VIA ');

        /**
         * Dados
         */
        $this->setFont('Arial','B',6);

        /* inscricao */
        $this->Text   ( ($x+(10*$tamX)) , ($y+(16*$tamY)) , $this->stInscricaoMunicipal ); // via banco
        $this->Text   ( ($x+(10*$tamX)) , (($y*$tamY)+(96*$tamY)) , $this->stInscricaoMunicipal ); // via contrib

        /* exercicio */
        $this->Text   ( ($x+(34*$tamX)) , ($y+(16*$tamY)) , $this->stExercicio ); // via banco
        $this->Text   ( ($x+(34*$tamX)) , (($y*$tamY)+(96*$tamY)) , $this->stExercicio ); // via contrib

        /* aviso */
        $this->Text   ( ($x+(53*$tamX)) , ($y+(16*$tamY)) , $this->stNumAviso ); // via banco
        $this->Text   ( ($x+(53*$tamX)) , (($y*$tamY)+(96*$tamY)) , $this->stNumAviso ); // via contrib

        /* receita */
        $this->Text   ( ($x+(82*$tamX)) , ($y+(16*$tamY)) , $this->stReceita ); // via banco
        $this->Text   ( ($x+(82*$tamX)) , (($y*$tamY)+(96*$tamY)) , $this->stReceita ); // via contrib

        /* competencia*/
        $this->Text   ( ($x+(127*$tamX)) , ($y+(13*$tamY)) , $this->stCompetencia ); // via banco
        $this->Text   ( ($x+(127*$tamX)) , (($y*$tamY)+(93*$tamY)) , $this->stCompetencia ); // via contrib

        /* vencimento */
        $this->setFont('Arial','B',7);
        $this->Text   ( ($x+(128*$tamX)) , ($y+(20*$tamY))  , $this->dtVencimento ); // via banco
        $this->Text   ( ($x+(128*$tamX)) , (($y*$tamY)+(100*$tamY)) , $this->dtVencimento ); // via contrib
        $this->setFont('Arial','B',6);

        /* valor */
        $this->Text   ( ($x+(128*$tamX)) , ($y+(28*$tamY))  , $this->doValor ); // via banco
        $this->Text   ( ($x+(128*$tamX)) , (($y*$tamY)+(108*$tamY)) , $this->doValor ); // via contrib

        /* multa */
        $this->Text   ( ($x+(128*$tamX)) , ($y+(35*$tamY))  , $this->doMulta ); // via banco
        $this->Text   ( ($x+(128*$tamX)) , (($y*$tamY)+(115*$tamY)) , $this->doMulta ); // via contrib

        /* juros */
        $this->Text   ( ($x+(128*$tamX)) , ($y+(42*$tamY))  , $this->doJuros ); // via banco
        $this->Text   ( ($x+(128*$tamX)) , (($y*$tamY)+(122*$tamY)) , $this->doJuros ); // via contrib

        /* total */
        $this->Text   ( ($x+(128*$tamX)) , ($y+(49*$tamY))  , $this->doTotal ); // via banco
        $this->Text   ( ($x+(128*$tamX)) , (($y*$tamY)+(129*$tamY)) , $this->doTotal ); // via contrib

        /* razao social */
        $this->Text   ( ($x+(6*$tamX)) , ($y+(21*$tamY))  , $this->stRazaoSocial ); // via banco
        $this->Text   ( ($x+(6*$tamX)) , (($y*$tamY)+(101*$tamY)) , $this->stRazaoSocial ); // via contrib

        /* endereco */
        $this->Text   ( ($x+(6*$tamX)) , ($y+(23*$tamY))  , $this->stEndereco ); // via banco
        $this->Text   ( ($x+(6*$tamX)) , (($y*$tamY)+(103*$tamY)) , $this->stEndereco ); // via contrib

        /* nome fantasia */
        $this->Text   ( ($x+(25*$tamX)) , ($y+(26*$tamY))  , $this->stNomeFantasia ); // via banco
        $this->Text   ( ($x+(25*$tamX)) , (($y*$tamY)+(106*$tamY)) , $this->stNomeFantasia ); // via contrib

        /* numero nota*/
        $this->Text   ( ($x+(25*$tamX)) , ($y+(29*$tamY)) , substr( $this->stNumeroNota, 0, 104) ); // via banco
        $this->Text   ( ($x+(10*$tamX)) , ($y+(31*$tamY)) , substr( $this->stNumeroNota, 80, 120) ); // via banco
        $this->Text   ( ($x+(10*$tamX)) , ($y+(33*$tamY)) , substr( $this->stNumeroNota, 160, 120) ); // via banco
        $this->Text   ( ($x+(10*$tamX)) , ($y+(35*$tamY)) , substr( $this->stNumeroNota, 160, 120) ); // via banco

        $this->Text   ( ($x+(25*$tamX)) , (($y*$tamY)+(109*$tamY)) , substr( $this->stNumeroNota, 0, 104) ); // via contrib
        $this->Text   ( ($x+(10*$tamX)) , (($y*$tamY)+(111*$tamY)) , substr( $this->stNumeroNota, 80, 120) ); // via contrib
        $this->Text   ( ($x+(10*$tamX)) , (($y*$tamY)+(113*$tamY)) , substr( $this->stNumeroNota, 160, 120) ); // via contrib
        $this->Text   ( ($x+(10*$tamX)) , (($y*$tamY)+(115*$tamY)) , substr( $this->stNumeroNota, 160, 120) ); // via contrib

        /* instruções */
        $this->setFont('Arial','',5);
        $this->Text   ( ($x+(20*$tamX)) , ($y+(41*$tamY))  , $this->arObservacoes[0]); // via banco
        $this->Text   ( ($x+(20*$tamX)) , ($y+(43*$tamY))  , $this->arObservacoes[1]); // via banco
        $this->Text   ( ($x+(20*$tamX)) , ($y+(45*$tamY))  , $this->arObservacoes[2]); // via banco
        $this->Text   ( ($x+(20*$tamX)) , ($y+(47*$tamY))  , $this->arObservacoes[3]); // via banco

        $this->Text   ( ($x+(20*$tamX)) , (($y*$tamY)+(121*$tamY))  , $this->arObservacoes[0]); // via contrib
        $this->Text   ( ($x+(20*$tamX)) , (($y*$tamY)+(123*$tamY))  , $this->arObservacoes[1]); // via contrib
        $this->Text   ( ($x+(20*$tamX)) , (($y*$tamY)+(125*$tamY))  , $this->arObservacoes[2]); // via contrib
        $this->Text   ( ($x+(20*$tamX)) , (($y*$tamY)+(127*$tamY))  , $this->arObservacoes[3]); // via contrib

        /* linha digitavel */
        $this->setFont('Arial', '', 8 );
        //$this->setFont('Arial','B',6);
        $this->Text   ( ($x+(30*$tamX)) , ($y+(57*$tamY)) , $this->stLinhaDigitavel);

        /* autenticação */
        $this->Text   ( ($x+(70*$tamX)) , (($y*$tamY)+(130*$tamY)) , ' - AUTENTICAÇÃO DO BANCO - ');

        $this->setFont('Arial','',5);

        /* codigo barras*/
        $this->defineCodigoBarras( ($x+(30*$tamX)), ($y+(59*$tamY)), $this->stCodigoBarras );

    }
    public function novaPagina()
    {
        $this->addPage();
    }
    public function defineCodigoBarras($xpos, $ypos, $code, $basewidth = 0.7, $height = 10)
    {
        //global $pdf;
        $wide = $basewidth;
        $narrow = $basewidth / 2 ;

        // wide/narrow codes for the digits
        $barChar['0'] = 'nnwwn';
        $barChar['1'] = 'wnnnw';
        $barChar['2'] = 'nwnnw';
        $barChar['3'] = 'wwnnn';
        $barChar['4'] = 'nnwnw';
        $barChar['5'] = 'wnwnn';
        $barChar['6'] = 'nwwnn';
        $barChar['7'] = 'nnnww';
        $barChar['8'] = 'wnnwn';
        $barChar['9'] = 'nwnwn';
        $barChar['A'] = 'nn';
        $barChar['Z'] = 'wn';

        // add leading zero if code-length is odd
        if (strlen($code) % 2 != 0) {
            $code = '0' . $code;
        }

        $this->SetFont('Arial','',10);
        $this->SetFillColor(0);

        // add start and stop codes
        $code = 'AA'.strtolower($code).'ZA';

        for ($i=0; $i<strlen($code); $i=$i+2) {
            // choose next pair of digits
            $charBar = $code{$i};
            $charSpace = $code{$i+1};
            // check whether it is a valid digit
            if (!isset($barChar[$charBar])) {
                $this->Error('Invalid character in barcode: '.$charBar);
            }
            if (!isset($barChar[$charSpace])) {
                $this->Error('Invalid character in barcode: '.$charSpace);
            }
            // create a wide/narrow-sequence (first digit=bars, second digit=spaces)
            $seq = '';
            for ($s=0; $s<strlen($barChar[$charBar]); $s++) {
                $seq .= $barChar[$charBar]{$s} . $barChar[$charSpace]{$s};
            }
            for ($bar=0; $bar<strlen($seq); $bar++) {
                // set lineWidth depending on value
                if ($seq{$bar} == 'n') {
                    $lineWidth = $narrow;
                } else {
                    $lineWidth = $wide;
                }
                // draw every second value, because the second digit of the pair is represented by the spaces
                if ($bar % 2 == 0) {
                    $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
                }
                $xpos += $lineWidth;
            }
        }
    }
    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneEscrituracaoMarianaPimentel
{
/*
    * @var Array
    * @access Private
*/
var $arEmissao;

function RCarneEscrituracaoMarianaPimentel($arEmissao)
{
    $this->arEmissao = $arEmissao;
}

function imprimirCarne()
{
    global $inCodFebraban;
    ;
    //---------------------
    //para gerar carne escrituracao
    /* recuperar codigo febraban */
    require_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
    $obRARRConfiguracao = new RARRConfiguracao;
    $obRARRConfiguracao->setCodModulo ( 2 );
    $obRARRConfiguracao->consultar();
    $inCodFebraban = $obRARRConfiguracao->getCodFebraban();
    unset($obRARRConfiguracao);

    /* consulta dados para o carne */
    require_once( CAM_GT_ARR_MAPEAMENTO . 'TARRCadastroEconomicoCalculo.class.php');
    $obTARRCadastroEconomicoCalculo = new TARRCadastroEconomicoCalculo();

    foreach ($this->arEmissao as $valor => $chave) {
        foreach ($chave as $parcela) {
            $stFiltro  = " ece.inscricao_economica = ".$parcela["inscricao"]." and ";
            $stFiltro .= " acne.numeracao = '".$parcela["numeracao"]."'";

            $obTARRCadastroEconomicoCalculo->recuperaConsultaReqReceita( $rsDadosCarne , $stFiltro, "", "", date ("Y-m-d"));

            $stFiltro = " ans.timestamp = '".$rsDadosCarne->getCampo('timestamp')."' AND ";
            $stFiltro .= " ans.inscricao_economica = ".$rsDadosCarne->getCampo('inscricao_economica');
            $obTARRCadastroEconomicoCalculo->recuperaConsultaNumeroNota( $rsDadosCarneNumeracao , $stFiltro);
            $stNumeracaoCarne = "";
            $inX = 0;
            while ( !$rsDadosCarneNumeracao->Eof() ) {
                if ( $inX > 0 )
                    $stNumeracaoCarne = $stNumeracaoCarne.";";

                $stNumeracaoCarne = $stNumeracaoCarne.$rsDadosCarneNumeracao->getCampo("nro_nota");
                $rsDadosCarneNumeracao->proximo();
                $inX++;
            }

            if ( strlen( $stNumeracaoCarne ) > 20 ) {
                $stNumeracaoCarne = substr($stNumeracaoCarne, 0, 19 )."...";
            }

            /* recuperar informações de codigo de barras e linha digitavel */
            $obBarra = new RCodigoBarraFebraban;
            $arBarra = array();

            $arBarra['valor_documento'] = $rsDadosCarne->getCampo('valor_total');
            $arBarra['tipo_moeda']      = 7;
            $arBarra['vencimento']      = $rsDadosCarne->getCampo('vencimento');
            $arBarra['nosso_numero']    = $rsDadosCarne->getCampo('numeracao');
            $arBarra['cod_febraban']    = $inCodFebraban;
            /* gera codigio de barras e linha digitavel */
            $arCodigoBarra = $obBarra->geraFebraban( $arBarra );

            $obCarne = new RCarneMarianaPimentel();
            $obCarne->stNumeroNota = $stNumeracaoCarne;
            $obCarne->stCompetencia = $rsDadosCarne->getCampo('competencia') ;
            $obCarne->stInscricaoMunicipal = $rsDadosCarne->getCampo('inscricao_economica') ;
            $obCarne->stExercicio = $rsDadosCarne->getCampo('exercicio');
            $obCarne->stNumAviso = $rsDadosCarne->getCampo('numeracao');
            $obCarne->stReceita = $rsDadosCarne->getCampo('nom_grupo_credito');
            $obCarne->stParcela = $rsDadosCarne->getCampo('nro_parcela');
            $obCarne->stRazaoSocial = $rsDadosCarne->getCampo('nom_cgm');
            $obCarne->stAtividade = $rsDadosCarne->getCampo('nom_atividade');
            $obCarne->stEndereco = $rsDadosCarne->getCampo('endereco');
            $obCarne->stNomeFantasia = $rsDadosCarne->getCampo('nom_fantasia');
            /*$obCarne->arObservacoes =  array (   0 => 'Após o vencimento, cobrar Juros de 1% ao Mês e Multa de: ' ,
                                                1 => '5% para pagamentos realizados até 30 dias após o vencimento' ,
                                                2 => '10% para pagamentos realizados entre 30 e 60 dias após o vencimento' ,
                                                3 => '15% para pagamentos realizados acima de 60 dias após o vencimento' );
*/
            $obCarne->stLinhaDigitavel = $arCodigoBarra['linha_digitavel'] ;
            $obCarne->stCodigoBarras =  $arCodigoBarra['codigo_barras'] ;
            $obCarne->dtVencimento = $rsDadosCarne->getCampo('vencimento');
            $obCarne->doValor = number_format($rsDadosCarne->getCampo('valor_pagamento'),2,',','.');
            $obCarne->doMulta = number_format($rsDadosCarne->getCampo('valor_multa'),2,',','.');
            $obCarne->doJuros = number_format($rsDadosCarne->getCampo('valor_juro'),2,',','.');
            $obCarne->doTotal = number_format($rsDadosCarne->getCampo('valor_total'),2,',','.');
            $obCarne->desenhaCarne( 10 , 40 ) ;
        }
    } //foreach

    if ( Sessao::read('stNomPdf') )
        $stNome     = Sessao::read('stNomPdf');
    else
        $stNome     = ini_get("session.save_path")."/"."PdfEmissaoUrbem-".date("dmYHis").".pdf";

    if ( Sessao::read('stParamPdf') )
        $stParam    = Sessao::read('stParamPdf');
    else
        $stParam    = "F";

    $obCarne->show( $stNome, $stParam );
}

}

?>

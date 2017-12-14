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
  * Carnê diversos Urbem
  * Data de criação : 05/05/2008

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM

  $Revision: 25146 $
  $Name$
  $Author: cercato $
  $Date: 2007-08-30 13:00:11 +0000 (Qui, 30 Ago 2007) $

  Caso de uso: uc-05.03.11
*/

/*
$Log$
*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
define       ('FPDF_FONTPATH','font/');

class RProtocolo extends fpdf
{
    /* Labels  */
    public $Titulo1         = 'P M MATA DE SÃO JOÃO';
    public $Titulo2         = '';
    public $lblContribuinte = 'CONTRIBUINTE';
    public $lblInscricao    = 'INSCRIÇÃO IMOB.';
    public $lblCtmDci       = 'CTM/DCI';
    public $lblLogradouro   = 'CÓDIGO LOGRADOURO';
    public $lblDistrito     = 'DIST.';
    public $lblCt           = 'CT';
    public $lblCa           = 'CA';
    public $lblDtProcesso   = 'DATA PROCESSAMENTO';
    public $lblLocalizacao  = 'LOCALIZAÇÃO DO IMÓVEL';
    public $lblTerreno      = 'ÁREA DO TERRENO';
    public $lblEdificada    = 'ÁREA EDIFICADA';
    public $lblUtilizacaoIm = 'UTILIZAÇÃO DO IMÓVEL';
    public $lblTributo      = 'TRIBUTO';
    public $lblNroPlanta    = 'Nº DA PLANTA';
    public $lblExercicio    = 'EXERCÍCIO';
    public $lblVlTribReal   = 'VALOR TRIBUTÁVEL - REAL';
    public $lblImpAnualReal = 'IMPOSTO - REAL';
    public $lblObservacao   = 'OBSERVAÇÃO';
    public $lblLimpAnualRl  = 'TX. COLETA LIXO ANUAL - REAL';
    public $lblTotalAnualRl = 'TOTAL ANUAL - REAL';
    public $lblReferencia   = 'REFERÊNCIA';
    public $lblTxAverbacao  = 'ALIQUOTA';
    public $lblUrbem      = 'URBEM - Soluções Integradas de Administração Municipal - www.urbem.cnm.org.br';
    public $lblTotalLancado = 'TOTAL LANÇADO';

    /* Variaveis */
    public $Imagem;
    public $stNomCgm;
    public $stRua;
    public $stNumero;
    public $stComplemento;
    public $stCidade;
    public $stUf;
    public $stCep;
    public $inInscricao;
    public $inCtmDci;
    public $inCodLogradouro;
    public $inDistrito;
    public $inCt;
    public $inCa;
    public $dtProcessamento;
    public $flAreaTerreno;
    public $flAreaEdificada;
    public $stUtilizacaoImovel;
    public $stTributo;
    public $stTributo2;
    public $stTributo3;
    public $flValorTributoReal;
    public $flImpostoAnualReal;
    public $flTotalAnualReal;
    public $stObservacao;
    public $flTaxaLimpezaAnual;
    public $inReferencia;
    public $inNumeroPlanta;
    public $stQuadro1;
    public $stQuadro2;
    public $stQuadro3;
    public $stExercicio;
    public $stNomBairro;
    public $stLoginUsuario;
    public $stCodUsuario;
    public $stEnderecoEntrega;
    public $flTxAverbacao;
    public $flTotalLancado;
    public $inTamY = 30;
    public $stAdquirente;

    /* setters */
    public function setImagem($valor) { $this->Imagem             = $valor; }
    public function setNomCgm($valor) { $this->stNomCgm           = $valor; }
    public function setRua($valor) { $this->stRua              = $valor; }
    public function setNumero($valor) { $this->stNumero           = $valor; }
    public function setComplemento($valor) { $this->stComplemento      = $valor; }
    public function setCidade($valor) { $this->stCidade           = $valor; }
    public function setUf($valor) { $this->stUf               = $valor; }
    public function setCep($valor) { $this->stCep              = $valor; }
    public function setInscricao($valor) { $this->inInscricao        = $valor; }
    public function setCtmDci($valor) { $this->inCtmDci           = $valor; }
    public function setCodLogradouro($valor) { $this->inCodLogradouro    = $valor; }
    public function setDistrito($valor) { $this->inDistrito         = $valor; }
    public function setCt($valor) { $this->inCt               = $valor; }
    public function setCa($valor) { $this->inCa               = $valor; }
    public function setProcessamento($valor) { $this->dtProcessamento    = $valor; }
    public function setAreaTerreno($valor) { $this->flAreaTerreno      = $valor; }
    public function setAreaEdificada($valor) { $this->flAreaEdificada    = $valor; }
    public function setUtilizacaoImovel($valor) { $this->stUtilizacaoImovel = $valor; }
    public function setTributo($valor) { $this->stTributo          = $valor; }
    public function setTributo2($valor) { $this->stTributo2         = $valor; }
    public function setTributo3($valor) { $this->stTributo3         = $valor; }
    public function setValorTributoReal($valor) { $this->flValorTributoReal = $valor; }
    public function setImpostoAnualReal($valor) { $this->flImpostoAnualReal = $valor; }
    public function setObservacao($valor) { $this->stObservacao       = $valor; }
    public function setTaxaLimpezaAnual($valor) { $this->flTaxaLimpezaAnual = $valor; }
    public function setValorAnualReal($valor) { $this->flValorAnualReal   = $valor; }
    public function setReferencia($valor) { $this->inReferencia       = $valor; }
    public function setNumeroPlanta($valor) { $this->inNumeroPlanta     = $valor; }
    public function setQuadro1($valor) { $this->stQuadro1          = $valor; }
    public function setQuadro2($valor) { $this->stQuadro2          = $valor; }
    public function setQuadro3($valor) { $this->stQuadro3          = $valor; }
    public function setExercicio($valor) { $this->stExercicio        = $valor; }
    public function setNomBairro($valor) { $this->stNomBairro        = $valor; }
    public function setLoginUsuario($valor) { $this->stLoginUsuario     = $valor; }
    public function setCodUsuario($valor) { $this->stCodUsuario       = $valor; }
    public function setEndEntrega($valor) { $this->stEnderecoEntrega  = $valor; }
    public function setTotalLancado($valor) { $this->flTotalLancado     = $valor; }

    /* getters */
    public function getImagem() { return $this->Imagem             ; }
    public function getNomCgm() { return $this->stNomCgm           ; }
    public function getRua() { return $this->stRua              ; }
    public function getNumero() { return $this->stNumero           ; }
    public function getComplemento() { return $this->stComplemento      ; }
    public function getCidade() { return $this->stCidade           ; }
    public function getUf() { return $this->stUf               ; }
    public function getCep() { return $this->stCep              ; }
    public function getInscricao() { return $this->inInscricao        ; }
    public function getCtmDci() { return $this->inCtmDci           ; }
    public function getCodLogradouro() { return $this->inCodLogradouro    ; }
    public function getDistrito() { return $this->inDistrito         ; }
    public function getCt() { return $this->inCt               ; }
    public function getCa() { return $this->inCa               ; }
    public function getProcessamento() { return $this->dtProcessamento    ; }
    public function getAreaTerreno() { return $this->flAreaTerreno      ; }
    public function getAreaEdificada() { return $this->flAreaEdificada    ; }
    public function getUtilizacaoImovel() { return $this->stUtilizacaoImovel ; }
    public function getTributo() { return $this->stTributo          ; }
    public function getValorTributoReal() { return $this->flValorTributoReal ; }
    public function getImpostoAnualReal() { return $this->flImpostoAnualReal ; }
    public function getObservacao() { return $this->stObservacao       ; }
    public function getTaxaLimpezaAnual() { return $this->flTaxaLimpezaAnual ; }
    public function getValorAnualReal() { return $this->flValorAnualReal   ; }
    public function getReferencia() { return $this->inReferencia       ; }
    public function getNumeroPlanta() { return $this->inNumeroPlanta     ; }
    public function getQuadro1() { return $this->stQuadro1          ; }
    public function getQuadro2() { return $this->stQuadro2          ; }
    public function getQuadro3() { return $this->stQuadro3          ; }
    public function getExercicio() { return $this->stExercicio        ; }
    public function getNomBairro() { return $this->stNomBairro        ; }
    public function getLoginUsuario() { return $this->stLoginUsuario     ; }
    public function getCodUsuario() { return $this->stCodUsuario       ; }
    public function getEndEntrega() { return $this->stEnderecoEntrega  ; }
    public function getTotalLancado() { return $this->flTotalLancado     ; }

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

    /* configura a pagina de carne */
    public function configuraProtocolo()
    {
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
    }

    /* layout do protocolo */
    public function drawProtocolo()
    {
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->lblVlTribReal   = 'VALOR TRIBUTÁVEL';
            //$this->lblImpAnualReal = 'IMPOSTO ANUAL';
            $this->lblImpAnualReal = 'IMPOSTO - REAL';
            //$this->lblTotalAnualRl = 'TOTAL ANUAL';
            $this->lblTotalAnualRl = 'TOTAL - REAL';

        }

        $this->setLoginUsuario ( Sessao::read( 'nomCgm' ) );
        $this->setCodUsuario ( Sessao::read('numCgm') );

        $this->setFont('Arial','',10);

        //thisfineCodigoBarras(10,50,'816100000016143532732003601232006101100000000016');

        /* retangula mais externo */
        $this->Rect( 7, 8+$this->inTamY, 196, 82);

        /* linhas horizontais maiores */
        $this->Line( 7, 27+$this->inTamY, 203, 27+$this->inTamY );
        $this->Line( 7, 47+$this->inTamY, 203, 47+$this->inTamY );
        $this->Line( 7, 62+$this->inTamY, 203, 62+$this->inTamY );
        $this->Line( 7, 71+$this->inTamY, 203, 71+$this->inTamY );

        /* lisinhas horizontais menores */
        $this->Line( 115, 33.65+$this->inTamY, 203, 33.65+$this->inTamY  );
        $this->Line( 115, 40.3+$this->inTamY , 203, 40.3+$this->inTamY   );
        $this->Line( 115, 54.5+$this->inTamY, 203, 54.5+$this->inTamY );
        $this->Line( 115, 80+$this->inTamY, 203, 80+$this->inTamY );

        /* lisinhas verticais */
        $this->Line( 33 , 62+$this->inTamY , 33 , 71+$this->inTamY );
        $this->Line( 58 , 62+$this->inTamY , 58 , 71+$this->inTamY );
        $this->Line( 85 , 62+$this->inTamY , 85 , 71+$this->inTamY );
        $this->Line( 115, 27+$this->inTamY , 115, 90+$this->inTamY );
        $this->Line( 162, 40.3+$this->inTamY , 162, 90+$this->inTamY );
        $this->Line( 152, 40.3+$this->inTamY , 152, 47+$this->inTamY );
        $this->Line( 142, 40.3+$this->inTamY , 142, 47+$this->inTamY );
        $this->Line( 172, 40.3+$this->inTamY , 172, 47+$this->inTamY );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, 9+$this->inTamY, 25, 16.5, $stExt );
        }

        /* labels fixos */
        $this->setFont('Arial','B',13);     // fonte do primeiro titulo
        $this->Text   ( 60, 17+$this->inTamY, $this->Titulo1 );

        $this->setFont('Arial','B',11);     // fonte do titulo menor
        $this->Text   ( 65, 22+$this->inTamY, $this->Titulo2 );

        $this->setFont('Arial','B',6);       // fonte dos labels dos dados
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 8    , 29.5+$this->inTamY, 'ADQUIRENTE'      );
            $this->setFont('Arial','B',10);
            $this->Text   ( 95   , 26+$this->inTamY  , 'ITIV'      );
            $this->setFont('Arial','B',6);
        } else {
            $this->Text   ( 8    , 29.5, $this->lblContribuinte  );
        }
        $this->Text   ( 115.5, 29.5+$this->inTamY, $this->lblInscricao     );
        $this->Text   ( 115.5, 36+$this->inTamY  , $this->lblCtmDci        );
        $this->Text   ( 115.5, 42.5+$this->inTamY, $this->lblLogradouro    );
        $this->Text   ( 143  , 42.5+$this->inTamY, $this->lblDistrito      );
        $this->Text   ( 153  , 42.5+$this->inTamY, $this->lblCt            );
        $this->Text   ( 163  , 42.5+$this->inTamY, $this->lblCa            );
        $this->Text   ( 173  , 42.5+$this->inTamY, $this->lblDtProcesso    );

        $this->Text   ( 8    , 49.5+$this->inTamY, $this->lblLocalizacao   );
        $this->Text   ( 115.5, 49.5+$this->inTamY, $this->lblTerreno       );
        $this->Text   ( 163  , 49.5+$this->inTamY, $this->lblEdificada     );
        $this->Text   ( 115.5, 57+$this->inTamY  , $this->lblUtilizacaoIm  );
        $this->Text   ( 163  , 57+$this->inTamY  , $this->lblTributo       );

        $this->Text   ( 8    , 64+$this->inTamY  , $this->lblNroPlanta     );
        $this->Text   ( 86   , 64+$this->inTamY  , $this->lblExercicio     );
        $this->Text   ( 115.5, 64+$this->inTamY  , $this->lblVlTribReal    );
        $this->Text   ( 163  , 64+$this->inTamY  , $this->lblImpAnualReal  );

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 8    , 73+$this->inTamY  ,"REQUERIMENTO DE I.T.I.V." );
        } else {
            $this->Text   ( 8    , 73  , $this->lblObservacao    );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 115.5, 73+$this->inTamY  , "TAXA DE EXPEDIENTE"    );
        } else {
            $this->Text   ( 115.5, 73  , $this->lblLimpAnualRl   );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 163  , 73+$this->inTamY  , $this->lblTxAverbacao );
            $this->Text   ( 163  , 82+$this->inTamY  , $this->lblTotalAnualRl  );
        } else {
            $this->Text   ( 163  , 73  , $this->lblTotalAnualRl  );
            $this->Text   ( 163  , 82  , $this->lblTotalLancado  );
        }

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            //$this->Text   ( 115.5, 82+$this->inTamY  , 'MULTA DE MORA'    );
        } else {
            $this->Text   ( 115.5, 82  , $this->lblReferencia    );
        }

        $this->setFont('Arial',''  , 5                 );
        $this->Text   ( 8    , 92+$this->inTamY  , $this->lblUrbem       );

        if ($this->stLoginUsuario != "" && $this->stCodUsuario != "") {
            $this->Text   ( 115.5, 92+$this->inTamY  , $this->stCodUsuario." - ".$this->stLoginUsuario );
        }

        /* Fim do layout do quadrado superior */
    }

    /* Posicionamento das variáveis */
    public function posicionaVariaveisProtocolo()
    {
        ;

        $this->setFont('Arial', 'b', 7 );
        if ( !Sessao::read( 'itbi_observacao' ) ) {
            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($this->stNomCgm) );
        } else {
            require_once(CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php");
            $obImovelVVenal = new TARRImovelVVenal;
            $filtro = "and inscricao_municipal = ".$this->inInscricao;
            $obImovelVVenal->recuperaMensagemItbi($rsItbi,$filtro);
            $this->stAdquirente = $rsItbi->getCampo('adquirinte');

            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($this->stAdquirente) );
        }
        // array com dados do endereço;
        $arEnd = explode('|*|',$this->stEnderecoEntrega);
        $this->Text   ( 8     , 37.5+$this->inTamY , strtoupper($arEnd[0].' '.$arEnd[1]));
        $this->Text   ( 8     , 41+$this->inTamY   , strtoupper($arEnd[2]." - ".$arEnd[3]) );
        $this->Text   ( 8     , 44.5+$this->inTamY , strtoupper($arEnd[6].' '.$arEnd[5].' CEP:'.$arEnd[4]) );

        $this->Text   ( 156   , 32+$this->inTamY   , strtoupper($this->inInscricao) );
        $this->Text   ( 145   , 37.5+$this->inTamY , strtoupper($this->inCtmDci) );
        $this->Text   ( 122   , 46+$this->inTamY   , strtoupper($this->inCodLogradouro) );
        $this->Text   ( 145   , 46+$this->inTamY   , strtoupper($this->inDistrito)  );
        $this->Text   ( 155   , 46+$this->inTamY   , strtoupper($this->inCt)    );
        $this->Text   ( 165   , 46+$this->inTamY   , strtoupper($this->inCa)    );
        $this->Text   ( 180   , 46+$this->inTamY   , strtoupper($this->dtProcessamento) );

        $this->Text   ( 8     , 54+$this->inTamY   , strtoupper($this->stRua.' '.$this->stNumero) );
        $this->Text   ( 8     , 58+$this->inTamY   , strtoupper($this->stComplemento." - ".$this->stNomBairro) );
        $this->Text   ( 145   , 52.5+$this->inTamY , strtoupper($this->flAreaTerreno) );
        $this->Text   ( 185   , 52.5+$this->inTamY , strtoupper($this->flAreaEdificada) );

        $this->Text   ( 130   , 60.5+$this->inTamY , strtoupper($this->stUtilizacaoImovel) );
        $this->setFont('Arial', 'b'  , 6 );
        $this->Text   ( 165   , 60.5+$this->inTamY , strtoupper(substr($this->stTributo,0,28)) );
        $this->Text   ( 165   , 68.5+$this->inTamY , strtoupper(substr($this->stTributo2,0,28)) );
        $this->setFont('Arial', 'b', 7 );
        $this->Text   ( 90    , 68+$this->inTamY   , strtoupper($this->stExercicio)   );
//        $this->Text   ( 8     , 76.5 , strtoupper($this->stObservacao));

        // Observacao
        // 3 linhas de observacao
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            require_once(CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php");
            $obImovelVVenal = new TARRImovelVVenal;
            $filtro = "and inscricao_municipal = ".$this->inInscricao;
            $obImovelVVenal->recuperaMensagemItbi($rsItbi,$filtro);
            $rsItbi->addFormatacao ('base_calculo','NUMERIC_BR');
            $rsItbi->addFormatacao ('valor_financiado','NUMERIC_BR');
            $rsItbi->addFormatacao ('valor_pactuado','NUMERIC_BR');
            //$stObsL1 =    "Adquirinte       : ".$rsItbi->getCampo('adquirinte');
            //$stObsL2 =    "Transmitente     : ".$rsItbi->getCampo('transmitente');
            $stObsL3 =    "Base de Calculo  : ".$rsItbi->getCampo('base_calculo')."      ITIV: ".$this->flImpostoAnualReal;
            $stObsL4 =    "Valor Financiado : ".$rsItbi->getCampo('valor_financiado');
            //$stObsL5 =    "Valor Pactuado   : ".$rsItbi->getCampo('valor_pactuado');
            $stObsL5 =    "Natureza de Transferência: ".$rsItbi->getCampo('cod_natureza')." - ".$rsItbi->getCampo('descricao');
            if ( $rsItbi->getCampo('cod_processo') )
                $stObsL7 =    "Processo       : ".$rsItbi->getCampo('cod_processo')."/".$rsItbi->getCampo('exercicio');

            $this->Text   ( 8     , 75.5+$this->inTamY , $stObsL3 );
            $this->Text   ( 8     , 78+$this->inTamY   , $stObsL4 );
            $this->Text   ( 8     , 80.5+$this->inTamY , $stObsL5 );
            $this->Text   ( 8     , 83+$this->inTamY   , $stObsL6 );
            $this->Text   ( 8     , 85.5 +$this->inTamY, $stObsL7 );
//            $this->Text   ( 8     , 88   , $stObsL6 );
//            $this->Text   ( 70    , 73   , $stObsL7 );
            // coloca atributo TRANSMITENTE no cabeçaho
            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($rsItbi->getCampo('transmitente') ));

        } else {
            $stObs = str_replace("\n"," ",$this->stObservacao);
            $this->Text   ( 8     , 76.5+$this->inTamY , substr($stObs,0      ,70 ));
            $this->Text   ( 8     , 79+$this->inTamY   , substr($stObs,70     ,70 ));
            $this->Text   ( 8     , 81.5+$this->inTamY , substr($stObs,140    ,70 ));
            $this->Text   ( 8     , 84+$this->inTamY   , substr($stObs,210    ,70 ));
            $this->Text   ( 8     , 86.5+$this->inTamY , substr($stObs,280    ,70 ));
        }
        // caso seja itbi, mostra o valor de base da calculo como valor tributavel
        if ( Sessao::read( 'itbi_observacao' ) == 'sim')
            $this->Text   ( 145   , 68+$this->inTamY   , $rsItbi->getCampo('base_calculo'));
        else
            $this->Text   ( 145   , 68   , strtoupper($this->flValorTributoReal) );

        $this->Text   ( 183   , 68+$this->inTamY   , strtoupper($this->flImpostoAnualReal ) );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 145   , 76.5+$this->inTamY , $rsItbi->getCampo('taxa') );
            $this->Text   ( 145   , 85.5+$this->inTamY , $rsItbi->getCampo('multa') );
            $this->Text   ( 183   , 76.5+$this->inTamY , $this->flTxAverbacao       );
        } else {
            $this->Text   ( 145   , 76.5 , strtoupper($this->flTaxaLimpezaAnual) );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $vlrTaxa = str_replace(',','.',str_replace('.','',$rsItbi->getCampo('taxa')));
            $vlrImp  = str_replace(',','.',str_replace('.','',$this->flValorAnualReal));
            $vlrAnual = $vlrImp+$vlrTaxa;

            //$this->Text   ( 183   , 85.5+$this->inTamY , number_format($vlrAnual,2,',','.') );
            $this->Text   ( 183   , 85.5+$this->inTamY , $this->flImpostoAnualReal); //number_format($this->flImpostoAnualReal,2,',','.') );
        } else {
            $this->Text   ( 183   , 76.5 , strtoupper($this->flValorAnualReal) );
            $this->Text   ( 183   , 85.5 , number_format($this->flTotalLancado,2,',','.') );
        }
        $this->Text   ( 115.5 , 85.5+$this->inTamY , strtoupper($this->inReferencia) );
        //limpar memoria
        unset($rsItbi,$obImovelVVenal);
    }
    /* Fim do posicionamento das variáveis */

    /* gera o PDF */
    public function show()
    {
        $this->output('Carne.pdf','D');
    }

    /* adiciona nova pagina */
    public function novaPagina()
    {
        $this->addPage();
    }
    /*
    *  Desenha informações complementares
    */
    public function drawComplemento($x,$y)
    {
        ;
        $this->setFont('Arial','',10);

        /* retangulos */
        $this->Rect( $x, $y, 92, 74 );
        $this->Rect( ($x+95), $y, 102, 74 );

        $this->setFont('Arial','BU',8);
        /* Cada nova linha sao mais 3.5 */
        /* esquerda */
        $this->Text   ( ($x+5)  , ($y+3.5)  , strtoupper('REDE AUTORIZADA PARA ARRECADAÇÃO VENCIMENTO DO') );
        $this->Text   ( ($x+40)  , ($y+7)   , strtoupper('IPTU-2006') );

        $this->setFont('Arial','',8);
        $this->Text   ( ($x+7)  , ($y+12)   , strtoupper('BRADESCO - BANCO DO BRASIL - BANESPA - BCN -') );
        $this->Text   ( ($x+6)  , ($y+15.5) , strtoupper('BANERJ - CAIXA ECONOMICA - HSBC - ITAU -REAL -') );
        $this->Text   ( ($x+5)  , ($y+19)   , strtoupper('UNIBANCO - CASAS LOTÉRICAS - SUPERMERCADOS') );
        $this->Text   ( ($x+25)  , ($y+22.5), strtoupper('CREDENCIADOS - BANCO POSTAL') );

        $this->setFont('Arial','BU',8);
        $this->Text   ( ($x+30)  , ($y+26)  , strtoupper('FORMA DE PAGAMENTO') );

        $this->setFont('Arial','',8);
        $this->Text   ( ($x+9)  , ($y+31) , strtoupper('AGÊNCIA BANCÁRIA INTERNET TELEFONE REDE AUTO') );
        $this->Text   ( ($x+32)  , ($y+34.5)  , strtoupper('ATENDIMENTO(24h)') );
        $this->setFont('Arial','',6);
        $this->Text   ( ($x+2)  , ($y+38)   , 'Dependendo dos serviços disponibilizados pelo Banco escolhido para efetuar o pagamento' ) ;
//      $this->Text   ( ($x+35)  , ($y+41.5), 'efetuar o pagamento') ;

        // direita
        $this->setFont('Arial','BU',8);
        $x +=92;
        $this->Text   ( ($x+12)  , ($y+3.5) , strtoupper('FATORES DE ATUALIZAÇÃO APÓS O VENCIMENTO') );
        $this->setFont('Arial','',8);
        $this->Text   ( ($x+5)  , ($y+12)   , 'MULTA: 5% no primeiro mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+15.5) , '              10% no segundo mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+19)   , '              15% no terceiro mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+22.5) , '              20% a partir do quarto mês ou fração' );
        $this->Text   ( ($x+5)  , ($y+26)   , 'JUROS: ' );
        $this->Text   ( ($x+5)  , ($y+29.5) , '       1% ao mês ou fração, a partir do mês' );
        $this->Text   ( ($x+5)  , ($y+33)   , '       subsequente ao Vencimento ' );

        $this->setFont('Arial','BU',8);
        $this->Text   ( ($x+26)  , ($y+38)  , strtoupper('INFORMAÇÕES COMPLEMENTARES') );
        $this->setFont('Arial','',8);
        $this->Text   ( ($x+5)  , ($y+45)  , '- A segunda via do carnê poderá ser obtida via internet,ou na') ;
        $this->Text   ( ($x+5)  , ($y+48.5), '  Secretaria de fazenda, Rua 16 de Março, 183 - Centro.') ;
        $this->Text   ( ($x+5)  , ($y+52)  , '- Quando o pagamento ultrapassar o dia de vencimento previsto ') ;
        $this->Text   ( ($x+5)  , ($y+55.5), 'no carnê, o IPTU será devido com os acréscimos legais') ;
        $this->Text   ( ($x+5)  , ($y+59)  , '- Ao receber o carnê do IPTU o contribuinte deverá verificar ') ;
        $this->Text   ( ($x+5)  , ($y+63)  , '  se os dados estão corretos, e em caso de alguma divergência,') ;
        $this->Text   ( ($x+5)  , ($y+66.5), '  apresentar sua reclamação antes do vencimento da primeira') ;
        $this->Text   ( ($x+5)  , ($y+70)  , '  Cota Única, garantindo seus direitos') ;
    }
}

class RCarneDiversosPetropolis extends RProtocolo
{
    /* variaveis */
    public $ImagemCarne;
    public $arDados;
    public $stValorDesconto;
    public $stEnderecoEntrega;
    public $stLoteamento;
    public $stNatureza;
    public $stZoneamento;
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
    public $stNomeZoneamento;

    public $stNomPref;
    public $stEndPref;
    public $stFonePref;
    public $stCNPJPref;
    public $stDAMPref; //mudancas 03_04_08
    public $stDepartamentoPref; //mudancas 03_04_08
    public $stSecretariaPref; //mudancas 03_04_08

    /* setters */
    public function setImagemCarne($valor)
    {
        if (sistemaLegado::validaArquivo($valor)) {
            $this->ImagemCarne = $valor;
        }
    }
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

    public function TextWithDirection($x,$y,$txt,$direction='R') { //item novo/alterado 12_03_2008
        $txt=str_replace(')','\\)',str_replace('(','\\(',str_replace('\\','\\\\',$txt)));

        if ($direction=='R')
            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',1,0,0,1,$x*$this->k,($this->h-$y)*$this->k,$txt);
        elseif ($direction=='L')
            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',-1,0,0,-1,$x*$this->k,($this->h-$y)*$this->k,$txt);
        elseif ($direction=='U')
            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,1,-1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
        elseif ($direction=='D')
            $s=sprintf('BT %.2f %.2f %.2f %.2f %.2f %.2f Tm (%s) Tj ET',0,-1,1,0,$x*$this->k,($this->h-$y)*$this->k,$txt);
        else
            $s=sprintf('BT %.2f %.2f Td (%s) Tj ET',$x*$this->k,($this->h-$y)*$this->k,$txt);

        if ($this->ColorFlag)
            $s='q '.$this->TextColor.' '.$s.' Q';

        $this->_out($s);
    }

    public function drawCapaCarne($x, $y)
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
//eh aakiiiiii o carnets
        $this->Rect( $x, $y+20, 197, 158*$this->tamY );

        /* linha horizontais */
        //bloco a
        $this->Line( $x, $y+27, (197+$x), $y+27 );
        $this->Line( $x, $y+38, (197+$x), $y+38 );
        $this->Line( $x, $y+45, (197+$x), $y+45 );
        $this->Line( $x, $y+52, (197+$x), $y+52 );
        $this->Line( $x, $y+56, (197+$x), $y+56 );

        $this->Line( $x, $y+113, (197+$x), $y+113 );
        $this->Line( $x, $y+120, (197+$x), $y+120 );

        /* linha vertical*/
        //primeira linha
        $this->Line( $x+30, $y+20, (30+$x), $y+27 );
        $this->Line( $x+130, $y+20, (130+$x), $y+27 );
        $this->Line( $x+160, $y+20, (160+$x), $y+27 );

        //terceira linha
        $this->Line( $x+28, $y+38, (28+$x), $y+45 );
        $this->Line( $x+58, $y+38, (58+$x), $y+45 );
        $this->Line( $x+114, $y+38, (114+$x), $y+45 );

        //setima linha
        $this->Line( $x+28, $y+113, (28+$x), $y+120 );
        $this->Line( $x+58, $y+113, (58+$x), $y+120 );
        $this->Line( $x+88, $y+113, (88+$x), $y+120 );
        $this->Line( $x+118, $y+113, (118+$x), $y+120 );
        $this->Line( $x+148, $y+113, (148+$x), $y+120 );
        $this->Line( $x+170, $y+113, (170+$x), $y+120 );

        /* brazao */
        if ($this->Imagem) {
            $stExt = substr( $this->Imagem, strlen($this->Imagem)-3, strlen($this->Imagem) );
            $this->Image( $this->Imagem, 8, 9, 25, 16.5, $stExt );
        }

        $this->setFont('Arial','B',8);
        $this->Text   ( ($x+27) , ($y+(2*$this->tamY)) , $this->stNomPref );
        $this->Text   ( ($x+27) , ($y+(5*$this->tamY)) , $this->stSecretariaPref ); //mudancas 03_04_08
        $this->Text   ( ($x+27) , ($y+(8*$this->tamY)) , $this->stDepartamentoPref ); //mudancas 03_04_08
        $this->Text   ( ($x+27) , ($y+(11*$this->tamY)) , $this->stEndPref );
        $this->Text   ( ($x+27) , ($y+(14*$this->tamY)) , "Fone: ".$this->stFonePref );
        $this->Text   ( ($x+27) , ($y+(17*$this->tamY)) , "CNPJ: ".$this->stCNPJPref );
        $this->Text   ( ($x+27) , ($y+(20*$this->tamY)) , $this->stDAMPref ); //mudancas 03_04_08

        $this->setFont('Arial','',8);
        $this->Text   ( $x+2, $y+23, "Inscrição" );
        $this->Text   ( $x+32, $y+23, "Proprietário/Empresa" );
        $this->Text   ( $x+132, $y+23, "Natureza" );

        $this->Text   ( $x+2, $y+30, "Localização do Imóvel" );
        $this->Text   ( $x+2, $y+35, "Loteamento" );

        $this->Text   ( $x+2, $y+41, "Data Emissão" );
        $this->Text   ( $x+30, $y+41, "Válido Até" );
        $this->Text   ( $x+60, $y+41, "DAM Nº" );
        $this->Text   ( $x+116, $y+41, "Funcionário que emitiu o Documento" );

        $this->Text   ( $x+2, $y+48, "Endereço de Entrega" );

        $this->Text   ( $x+2, $y+55, "Tributo" );
        $this->Text   ( $x+68, $y+55, "Parc." );
        $this->Text   ( $x+78, $y+55, "Vencimento" );
        $this->Text   ( $x+98, $y+55, "Valor Lanc." );
        $this->Text   ( $x+118, $y+55, "Corr. Monet." );
        $this->Text   ( $x+139, $y+55, "Juros" );
        $this->Text   ( $x+152, $y+55, "Multa" );
        $this->Text   ( $x+166, $y+55, "Desc." );
        $this->Text   ( $x+181, $y+55, "Total" );

//esta linha vai subir um pouco
        $this->Text   ( $x+2, $y+116, "Valor Lançado:" );
        $this->Text   ( $x+30, $y+116, "Multa:" );
        $this->Text   ( $x+60, $y+116, "Juros:" );
        $this->Text   ( $x+90, $y+116, "Atua. Monet.:" );
        $this->Text   ( $x+120, $y+116, "Taxa de Expediente:" );
        $this->Text   ( $x+150, $y+116, "Desconto:" );
        $this->Text   ( $x+172, $y+116, "Total:" );

        for ($i=0; $i<=196; $i+=2) {
           $this->Line( ($x+$i), ($y+(182*$this->tamY)), ($x+$i+1), ($y+(182*$this->tamY)) );
        }
    }

    /* layout do carne */
    public function drawCarne($x, $y)
    {
        // truncar tributo
        if ( !$this->stTributoAbrev )
            $this->stTributoAbrev = substr($this->stTributo,0,25);

        $this->stNomCgm  = substr($this->stNomCgm ,0,80);
        $this->setFont( 'Arial','',10 );

        /* returna retangulo */
        $this->Rect( $x, $y+173, 197, 84*$this->tamY );

        /* linha horizontais */
        //bloco b
        $this->Line( $x, $y+180, (27+$x), $y+180 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+185, (27+$x), $y+185 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+192, (27+$x), $y+192 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+199, (27+$x), $y+199 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+206, (27+$x), $y+206 ); //item novo/alterado 12_03_2008

        $this->Line( $x+37, $y+180, (197+$x), $y+180 ); //item novo/alterado 12_03_2008
        $this->Line( $x+37, $y+185, (197+$x), $y+185 ); //item novo/alterado 12_03_2008
        $this->Line( $x+37, $y+192, (197+$x), $y+192 ); //item novo/alterado 12_03_2008
        $this->Line( $x+37, $y+199, (197+$x), $y+199 ); //item novo/alterado 12_03_2008
        $this->Line( $x+37, $y+206, (197+$x), $y+206 ); //item novo/alterado 12_03_2008

        $this->Line( $x+160, $y+213, (197+$x), $y+213 );
        $this->Line( $x+160, $y+220, (197+$x), $y+220 );
        $this->Line( $x+160, $y+227, (197+$x), $y+227 );

        $this->Line( $x, $y+213, (27+$x), $y+213 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+220, (27+$x), $y+220 ); //item novo/alterado 12_03_2008
        $this->Line( $x, $y+227, (27+$x), $y+227 ); //item novo/alterado 12_03_2008

        $this->Line( $x, $y+234, (27+$x), $y+234 ); //item novo/alterado 12_03_2008
        $this->Line( $x+37, $y+234, (197+$x), $y+234 ); //item novo/alterado 12_03_2008

        /* linha vertical*/
        $this->Line( $x+160, $y+173, (160+$x), $y+234 ); //linha  grande do vencimento ate o valor cobrado

        for ($i=174; $i<=249; $i+=2) { //item novo/alterado 12_03_2008
            $this->Line( $x+37, $y+$i, $x+37, $y+$i+1 ); //item novo/alterado 12_03_2008
        }

        $this->Line( $x+27, $y+173, (27+$x), $y+251 ); //item novo/alterado 12_03_2008

        $this->Line( $x+72, $y+185, (72+$x), $y+192 );//item novo/alterado 11_03_2008
        $this->Line( $x+92, $y+185, (92+$x), $y+192 ); //item novo/alterado 11_03_2008
        $this->Line( $x+112, $y+185, (112+$x), $y+192 );//item novo/alterado 11_03_2008
        $this->Line( $x+132, $y+185, (132+$x), $y+192 );//item novo/alterado 11_03_2008

        $this->Line( $x+72, $y+192, (72+$x), $y+199 ); //item novo/alterado 11_03_2008
        $this->Line( $x+92, $y+192, (92+$x), $y+199 ); //item novo/alterado 11_03_2008
        $this->Line( $x+112, $y+192, (112+$x), $y+199 ); //item novo/alterado 11_03_2008
        $this->Line( $x+132, $y+192, (132+$x), $y+199 );

        $this->setFont('Arial','',4); //item novo/alterado 12_03_2008
        $this->TextWithDirection( $x+30, $y+240, 'Autenticação Mecânica','U'); //item novo/alterado 12_03_2008

        $this->setFont( 'Arial','',7 );
        $this->Text   ( $x+38, $y+176, "Mensagem" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+38, $y+184, $this->stNomPref ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+114, $y+188, "Data do Doc." ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+38, $y+188, "Número do Documento" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+74, $y+188, "Parc." );
        $this->Text   ( $x+94, $y+188, "Inscrição" );
        $this->Text   ( $x+134, $y+188, "Data Processamento" ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+38, $y+195, "Uso do Banco" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+74, $y+195, "Mês" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+94, $y+195, "Espécie" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+114, $y+195, "Quantidade" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+134, $y+195, "Valor" ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+38, $y+202, "Relação de Tributos" ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+38, $y+209, "Exercícios Selecionados:" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+162, $y+240, "Procurador Responsável:" );

        $this->setFont('Arial','',5);
        $this->Text   ( $x+2, $y+176, "Vencimento" );  //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+182, "Nosso Número" );  //item novo/alterado 11_03_2008
        $this->setFont('Arial','',7);
        $this->Text   ( $x+2, $y+188, "Valor do Documento" );  //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+195, "Multa" );  //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+202, "Juros" );  //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+209, "Atualização Monetária" );  //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+216, "Desconto" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+222, "Honorários" ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+2, $y+230, "Valor Cobrado" ); //item novo/alterado 11_03_2008

        $this->setFont('Arial','',5);
        $this->Text   ( $x+162, $y+176, "Vencimento" );
        $this->Text   ( $x+162, $y+182, "Nosso Número" );
        $this->setFont('Arial','',7);
        $this->Text   ( $x+162, $y+188, "Valor do Documento" );
        $this->Text   ( $x+162, $y+195, "Multa" );
        $this->Text   ( $x+162, $y+202, "Juros" );
        $this->Text   ( $x+162, $y+209, "Atualização Monetária" );
        $this->Text   ( $x+162, $y+216, "Desconto" );
        $this->Text   ( $x+162, $y+222, "Honorários" );
        $this->Text   ( $x+162, $y+230, "Valor Cobrado" );
    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        ;

        $this->setFont('Arial', '', 6 );
        $this->Text   ( $x+40, $y+248, $this->getRua() ); // end . do imovel lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+40, $y+240, $this->getNomCgm() ); // proprietario lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+2, $y+240, substr( $this->getNomCgm(), 0, 16 ) ); // proprietario lado B //item novo/alterado 12_03_2008
        $this->Text   ( $x+2, $y+243, substr( $this->getNomCgm(), 16, 16 ) ); // proprietario lado B //item novo/alterado 12_03_2008
        $this->Text   ( $x+2, $y+246, substr( $this->getNomCgm(), 32, 16 ) ); // proprietario lado B //item novo/alterado 12_03_2008

        $this->Text   ( $x+94, $y+191, $this->inInscricao ); // inscricao lado B
        $this->Text   ( $x+2 , $y+179, $this->dtVencimento ); //vencimento do lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+162 , $y+179, $this->dtVencimento ); //vencimento do lado B

        $this->Text   ( $x+40, $y+191, $this->stNumeracao ); //numero do documento lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+162, $y+184, $this->stNumeracao ); //nosso numero lado B
        $this->Text   ( $x+2, $y+184, $this->stNumeracao ); //nosso numero lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+74, $y+191, $this->arDados[0]["parcela"] ); //nosso numero lado B

        $this->Text   ( $x+134, $y+191, $this->dtProcessamento ); //data de processamento lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+114, $y+191, $this->dtProcessamento ); //data do documento lado B //item novo/alterado 11_03_2008

//Totais vazios
        $this->Text   ( $x+162, $y+191, $this->flValorTotal ); //valor do documento lado B
        //$this->Text   ( $x+2, $y+191, $this->flValorTotal ); //valor do documento lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+162, $y+198, $this->flValorMulta ); //multa lado B
        $this->Text   ( $x+2, $y+198, $this->flValorMulta ); //multa lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+162, $y+205, $this->flValorJuros ); //juros lado B
        $this->Text   ( $x+2, $y+205, $this->flValorJuros ); //juros lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+162, $y+212, $this->flValorOutros); //atualizacao monetaria lado B
        $this->Text   ( $x+2, $y+212, $this->flValorOutros); //atualizacao monetaria lado B //item novo/alterado 11_03_2008

        $this->Text   ( $x+40, $y+205, $this->stTributo ); //relacao de tributos lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+40, $y+212, $this->stExercicio ); //exercicios selecionados lado B //item novo/alterado 11_03_2008

//        $stObs = "Intruções: Após Vencimento Cobrar: Multa de Mora 10% e Juros de 1% A.M.";
//        $this->Text   ( $x+40, $y+215, $stObs ); //item novo/alterado 11_03_2008

        $stObs = str_replace( "\n\r", " ", $this->stObservacao );
        $this->Text   ( $x+40, $y+218, substr( $stObs, 0, 120 ) ); //item novo/alterado 11_03_2008
        $this->Text   ( $x+40, $y+221, substr( $stObs, 120, 120 ) ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+40, $y+179, "ATÉ O DIA DO VENCIMENTO PAGÁVEL NOS BANCOS CONVENIADOS" ); //item novo/alterado 11_03_2008

        $this->Text   ( $x+2, $y+219, $this->stValorDesconto ); //desconto lado B //item novo/alterado 11_03_2008
        $this->Text   ( $x+162, $y+219, $this->stValorDesconto ); //desconto lado B

        $this->Text   ( $x+162, $y+233, $this->flValorTotal ); //valor cobrado lado B
        $this->Text   ( $x+2, $y+233, $this->flValorTotal ); //valor cobrado lado B //item novo/alterado 11_03_2008

        $this->setFont('Arial', '', 8 );
        $this->Text   ( ($x+85), ($y+(273*$this->tamY)), $this->stLinhaCode );
        $this->defineCodigoBarras( ($x+75), ($y+(275*$this->tamY)), $this->stBarCode );
    }

    public function posicionaVariaveisCapa($x, $y)
    {
        ;

        $this->Text   ( $x+162, $y+23, $this->stNomeZoneamento );

        //falta aki 22_08
        $this->Text   ( $x+32, $y+26, $this->getNomCgm() ); // proprietario lado A
        $this->Text   ( $x+2, $y+26, $this->inInscricao ); // id fisico lado A

        $this->Text   ( $x+32, $y+32, substr( $this->getRua(), 0, 100 ) ) ; // end . do imovel lado A
        $this->Text   ( $x+52, $y+35, substr( $this->getRua(), 100, 120 ) ) ; // end . do imovel lado A

        $this->Text   ( $x+31 , $y+44, $this->dtVencimento ); //valido ate

        $this->Text   ( $x+60, $y+44, $this->stNumeracao ); //dam numero lado A
        $this->Text   ( $x+2, $y+44, $this->dtProcessamento ); //data de emissao lado A

        $this->Text   ( $x+116, $y+44, Sessao::read( 'nomCgm' ) ); //funcionario que emitiu

        $this->Text   ( $x+2, $y+119, $this->flValor ); //valor lancado lado A
        $this->Text   ( $x+30, $y+119, $this->flValorMulta ); //multa lado A
        $this->Text   ( $x+60, $y+119, $this->flValorJuros ); //juros lado A
        $this->Text   ( $x+90, $y+119, $this->flValorOutros); //outros lado A
        $this->Text   ( $x+172, $y+119, $this->flValorTotal ); //total lado A

        $this->setFont('Arial', '', 6 );
        //$this->Text   ( $x+162, $y+191, $this->flValorTotal ); //valor do documento lado B
        //$this->Text   ( $x+2, $y+191, $this->flValorTotal ); //valor do documento lado B //item novo/alterado 11_03_2008

        //$this->Text   ( $x+162, $y+233, $this->flValorTotal ); //valor cobrado lado B
        //$this->Text   ( $x+2, $y+233, $this->flValorTotal ); //valor cobrado lado B //item novo/alterado 11_03_2008

        $this->setFont('Arial', '', 8 );
        $this->Text   ( $x+132, $y+26, $this->stNatureza );
        $this->Text   ( $x+162, $y+26, $this->stZoneamento );
        $this->Text   ( $x+32, $y+38, $this->stLoteamento );
        $this->Text   ( $x+2, $y+51, $this->stEnderecoEntrega );

        $this->Text   ( $x+150, $y+118, $this->stValorDesconto ); //desconto lado A

        for ( $inX=0; $inX<count($this->arDados); $inX++ ) {
            $this->Text   ( $x+2, $y+60+($inX*3), $this->arDados[$inX]["tributo"] );
            $this->Text   ( $x+68, $y+60+($inX*3), $this->arDados[$inX]["parcela"] );
            $this->Text   ( $x+78, $y+60+($inX*3), $this->arDados[$inX]["vencimento"] );
            $this->Text   ( $x+98, $y+60+($inX*3), $this->arDados[$inX]["valor_lanc"] );
            $this->Text   ( $x+118, $y+60+($inX*3), $this->arDados[$inX]["correcao"] );
            $this->Text   ( $x+138, $y+60+($inX*3), $this->arDados[$inX]["juros"] );
            $this->Text   ( $x+152, $y+60+($inX*3), $this->arDados[$inX]["multa"] );
            $this->Text   ( $x+166, $y+60+($inX*3), $this->arDados[$inX]["desc"] );
            $this->Text   ( $x+181, $y+60+($inX*3), $this->arDados[$inX]["total"] );
       }
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
           $this->Line( ($x+$i), ($y+(288*$this->tamY)), ($x+$i+1), ($y+(288*$this->tamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneMata extends RProtocolo
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
        $this->Rect( $x+20, $y, 194, (90*$inTamY) );

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
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
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

class RCarneDiversosUrbem
{
/*
    * @var Integer
    * @access Private
*/
var $inHorizontal;
/*
    * @var Integer
    * @access Private
*/
var $inVertical;
/*
    * @var Array
    * @access Private
*/
var $arEmissao;
/*
    * @var Object
    * @access Private
*/
var $obBarra;
/*
    * @var Array
    * @access Private
*/
var $arBarra;
/*
    * @var Boolean;
    * @access Private
*/
var $boPulaPagina;
/*
    * @var Object
    * @access Private
*/
var $obRARRCarne;
var $stLocal;
var $boConsolidacao;
var $stNumeracaoConsolidacao;
var $dtVencimentoConsolidacao;

/* setters */
function setHorizontal($valor) { $this->inHorizontal = $valor; }
function setVertical($valor) { $this->inVertical   = $valor; }
function setEmissao($valor) { $this->arEmissao    = $valor; }
function setBarra($valor) { $this->obBarra      = $valor; }
function setArBarra($valor) { $this->arBarra      = $valor; }
function setPulaPagina($valor) { $this->boPulaPagina = $valor; }
function setConsolidacao($valor) { $this->boConsolidacao = $valor;            }
function setVencimentoConsolidacao($valor) { $this->dtVencimentoConsolidacao = $valor;  }
function setNumeracaoConsolidacao($valor) { $this->stNumeracaoConsolidacao = $valor;   }

/* getters */
function getHorizontal() { return $this->inHorizontal;   }
function getVertical() { return $this->inVertical;     }
function getEmissao() { return $this->arEmissao;      }
function getBarra() { return $this->obBarra;        }
function getArBarra() { return $this->arBarra;        }
function getPulaPagina() { return $this->boPulaPagina;   }
function getConsolidacao() { return $this->boConsolidacao;   }
function getNumeracaoConsolidacao() { return $this->stNumeracaoConsolidacao; }
function getVencimentoConsolidacao() { return $this->dtVencimentoConsolidacao; }

/*
    * Metodo Construtor
    * @access Private
*/
function RCarneDiversosUrbem($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;

    $this->obRARRConfiguracao     = new RARRConfiguracao;
    $this->obRARRConfiguracao->setCodModulo ( 2 );
    $this->obRARRConfiguracao->consultar();
    $inCodFebraban = $this->obRARRConfiguracao->getCodFebraban();
    unset($this->obRARRConfiguracao);

    $inSaltaPagina = "";
    $this->obRCarneMata = new RCarneDiversosPetropolis;
    $this->obRCarneMata->configuraProtocolo();

    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );
    $stNomeImagem = $rsListaImagens->getCampo("valor");
    $inCountTemp = 0;

    foreach ($this->arEmissao as $valor => $chave) {
        $inSaltaPagina++;

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosPrefeituraIPTUGenerico( $rsListaCarnePrefeitura, Sessao::getExercicio() );

         //mudancas 03_04_08
        $this->obRCarneMata->stDAMPref = $rsListaCarnePrefeitura->getCampo( "carne_dam" ); //mudancas 03_04_08
        $this->obRCarneMata->stDepartamentoPref = $rsListaCarnePrefeitura->getCampo( "carne_departamento" ); //mudancas 03_04_08
        $this->obRCarneMata->stSecretariaPref = $rsListaCarnePrefeitura->getCampo( "carne_secretaria" ); //mudancas 03_04_08

        $this->obRCarneMata->stNomPref = $rsListaCarnePrefeitura->getCampo( "nom_prefeitura" );
        $this->obRCarneMata->stEndPref = $rsListaCarnePrefeitura->getCampo( "tipo_logradouro" )." ".$rsListaCarnePrefeitura->getCampo( "logradouro" ).", ".$rsListaCarnePrefeitura->getCampo( "numero" )." - ".$rsListaCarnePrefeitura->getCampo( "bairro" )." CEP ".$rsListaCarnePrefeitura->getCampo( "cep" )." ".$rsListaCarnePrefeitura->getCampo( "nom_municipio" )." ".$rsListaCarnePrefeitura->getCampo( "nom_uf" );
        $this->obRCarneMata->stFonePref = $rsListaCarnePrefeitura->getCampo( "ddd" )." ".$rsListaCarnePrefeitura->getCampo( "fone" );
        $this->obRCarneMata->stCNPJPref = $rsListaCarnePrefeitura->getCampo( "cnpj" );

        $obTARRCarne->recuperaNivelCarneIPTUGenerico( $rsListaNivelCarne );

        $stNivel = "";
        while ( !$rsListaNivelCarne->Eof() ) {

            if ( $stNivel )
                $stNivel .= " - ";

            $stNivel .= $rsListaNivelCarne->getCampo("nom_nivel");
            $rsListaNivelCarne->proximo();
        }

        $this->obRCarneMata->stNomeZoneamento = $stNivel;

        $this->obRCarneMata->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];

        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );

        $this->obRARRCarne->setNumeracao( $chave[0]['numeracao'] );
        $this->obRARRCarne->obRARRParcela->setCodParcela( $chave[0]['cod_parcela'] );

        if ( count( $chave ) > 1 ) {
            $stFiltro1 = " AND ap.cod_parcela IN ( ".$chave[0]['cod_parcela'];
            foreach ($chave as $parcela) {
                $stFiltro1 .= ", ".$parcela["cod_parcela"];
            }

            $stFiltro1 .= " )";

            $stFiltro2 = " AND carne.numeracao IN ( '".$chave[0]['numeracao']."'";
            foreach ($chave as $parcela) {
                $stFiltro2 .= ", '".$parcela["numeracao"]."' ";
            }

            $stFiltro2 .= " )";

            $obTARRCarne->ListaDadosPorGrupoParaCarneIPTU( $rsListaDetalheCreditos, " WHERE al.cod_lancamento = ".$valor.$stFiltro1.$stFiltro2 );

        } else {
            $obTARRCarne->ListaDadosPorCreditoParaCarneIPTU( $rsListaDetalheCreditos, " WHERE al.cod_lancamento = ".$valor." AND ap.cod_parcela = ".$chave[0]['cod_parcela']." AND carne.numeracao = '".$chave[0]['numeracao']."'");
            $arTMPDados = $rsListaDetalheCreditos->getElementos();

            $flTotalCreditosDesc = 0;
            $flTotalCreditosValor = 0;
            for ( $inX=0; $inX<count( $arTMPDados ); $inX++ ) {
                $flTotalCreditosValor += $arTMPDados[$inX]["valor_credito"];
                if ( $arTMPDados[$inX]["desconto"] == 't' )
                    $flTotalCreditosDesc += $arTMPDados[$inX]["valor_credito"];
            }

            if ( $arTMPDados[0]["desconto_parcela"] > 0 )
                $flTotalDesconto = $flTotalCreditosValor - $arTMPDados[0]["desconto_parcela"];
            else
                $flTotalDesconto = 0.00;

            for ( $inX=0; $inX<count( $arTMPDados ); $inX++ ) {
                if ($arTMPDados[$inX]["desconto"] == 't') {
                    $arTMPDados[$inX]["desconto_credito"] = ( $arTMPDados[$inX]["valor_credito"] * 100 ) / $flTotalCreditosDesc;
                    $arTMPDados[$inX]["desconto_credito"] = ( $flTotalDesconto * $arTMPDados[$inX]["desconto_credito"] ) / 100;
                }else
                    $arTMPDados[$inX]["desconto_credito"] = 0.00;
            }

            $rsListaDetalheCreditos->preenche( $arTMPDados );
        }

        $flValorTotal = 0.00;
        $flValorTotalDesc = 0.00;
        $flValorTotalJuros = 0.00;
        $flValorTotalMulta = 0.00;
        $flValorTotalCorrecao = 0.00;
        $flValorTotalGeral = 0.00;

        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarneMata->setObservacaoL1 ('Créditos: ');

        $this->obRCarneMata->arDados = array();

        if ( count( $chave ) > 1 ) {
            $boUsarUnica = false;
            $inDados = 0;
            while ( !$rsListaDetalheCreditos->Eof() ) {
                if (!$boUsarUnica) {
                    if ( $rsListaDetalheCreditos->getCampo("nr_parcela") == 'única' ) {
                        $flValorTotal = 0;
                        $flValorTotalJuros = 0;
                        $flValorTotalMulta = 0;
                        $flValorTotalCorrecao = 0;
                        $flValorTotalDesc = 0;
                        $boUsarUnica = true;
                    }

                    $flValorTotalDesc += $rsListaDetalheCreditos->getCampo("valor_desconto")>0?$rsListaDetalheCreditos->getCampo("valor_credito")-$rsListaDetalheCreditos->getCampo("valor_desconto"):0.00;
                    $flValorTotal += $rsListaDetalheCreditos->getCampo("valor_credito");
                    $flValorTotalJuros += $rsListaDetalheCreditos->getCampo("credito_juros_pagar");
                    $flValorTotalMulta += $rsListaDetalheCreditos->getCampo("credito_multa_pagar");
                    $flValorTotalCorrecao += $rsListaDetalheCreditos->getCampo("credito_correcao_pagar");
                }

                $this->obRCarneMata->arDados[$inDados]["tributo"] = $rsListaDetalheCreditos->getCampo("credito");
                $this->obRCarneMata->arDados[$inDados]["valor_lanc"] = number_format( $rsListaDetalheCreditos->getCampo("valor_credito"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["correcao"] = number_format( $rsListaDetalheCreditos->getCampo("credito_correcao_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["juros"] = number_format( $rsListaDetalheCreditos->getCampo("credito_juros_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["multa"] = number_format( $rsListaDetalheCreditos->getCampo("credito_multa_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["total"] = number_format( ($rsListaDetalheCreditos->getCampo("valor_desconto")>0?$rsListaDetalheCreditos->getCampo("valor_desconto"):$rsListaDetalheCreditos->getCampo("valor_credito")) + $rsListaDetalheCreditos->getCampo("credito_juros_pagar") + $rsListaDetalheCreditos->getCampo("credito_multa_pagar") + $rsListaDetalheCreditos->getCampo("credito_correcao_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["desc"] = number_format( ($rsListaDetalheCreditos->getCampo("valor_desconto")>0)?$rsListaDetalheCreditos->getCampo("valor_credito")-$rsListaDetalheCreditos->getCampo("valor_desconto"):0.00, 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["parcela"] = $rsListaDetalheCreditos->getCampo("nr_parcela");
                $this->obRCarneMata->arDados[$inDados]["vencimento"] = $rsListaDetalheCreditos->getCampo( 'vencimento' );

                $rsListaDetalheCreditos->proximo();
                $inDados++;
            }

        } else {
            $inDados = 0;
            $this->obRCarneMata->setNumeracao( $chave[0]['numeracao'] );
            $this->obRCarneMata->setVencimento( $rsListaDetalheCreditos->getCampo( 'vencimento' ) );
            $this->obRCarneMata->arDados[0]["vencimento"] = $rsListaDetalheCreditos->getCampo( 'vencimento' );
            $this->obRCarneMata->arDados[0]["tributo"] = $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao_credito' );

            while ( !$rsListaDetalheCreditos->Eof() ) {
                $flValorTotal += $rsListaDetalheCreditos->getCampo("valor_credito");
                $flValorTotalJuros += $rsListaDetalheCreditos->getCampo("credito_juros_pagar");
                $flValorTotalMulta += $rsListaDetalheCreditos->getCampo("credito_multa_pagar");
                $flValorTotalCorrecao += $rsListaDetalheCreditos->getCampo("credito_correcao_pagar");
                $flValorTotalDesc += $rsListaDetalheCreditos->getCampo("desconto_credito");
                //$flValorTotalDesc += $rsListaDetalheCreditos->getCampo("valor_desconto")>0?$rsListaDetalheCreditos->getCampo("valor_credito")-$rsListaDetalheCreditos->getCampo("valor_desconto"):0.00;

                $this->obRCarneMata->arDados[$inDados]["tributo"] = $rsListaDetalheCreditos->getCampo("descricao_credito");
                $this->obRCarneMata->arDados[$inDados]["valor_lanc"] = number_format( $rsListaDetalheCreditos->getCampo("valor_credito"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["correcao"] = number_format( $rsListaDetalheCreditos->getCampo("credito_correcao_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["juros"] = number_format( $rsListaDetalheCreditos->getCampo("credito_juros_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["multa"] = number_format( $rsListaDetalheCreditos->getCampo("credito_multa_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["total"] = number_format( ($rsListaDetalheCreditos->getCampo("valor_credito") - $rsListaDetalheCreditos->getCampo("desconto_credito")) + $rsListaDetalheCreditos->getCampo("credito_juros_pagar") + $rsListaDetalheCreditos->getCampo("credito_multa_pagar") + $rsListaDetalheCreditos->getCampo("credito_correcao_pagar"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["desc"] = number_format( $rsListaDetalheCreditos->getCampo("desconto_credito"), 2, ',', '.' );
                $this->obRCarneMata->arDados[$inDados]["parcela"] = $rsListaDetalheCreditos->getCampo("nr_parcela");
                $this->obRCarneMata->arDados[$inDados]["vencimento"] = $rsListaDetalheCreditos->getCampo( 'vencimento' );

                $rsListaDetalheCreditos->proximo();
                $inDados++;
            }

        }

        $rsListaDetalheCreditos->setPrimeiroElemento();
        $flValorTotalGeral += $flValorTotalCorrecao + $flValorTotalMulta + $flValorTotalJuros + ($flValorTotal-$flValorTotalDesc);

    //    while ( !$rsGeraCarneCabecalho->eof() ) {
            /* montagem cabecalho (protocolo) */
        $this->obRCarneMata->stEnderecoEntrega = "Endereco: ".$rsGeraCarneCabecalho->getCampo( 'endereco_entrega' )." Bairro: ".$rsGeraCarneCabecalho->getCampo( 'bairro_entrega' )." CEP: ".$rsGeraCarneCabecalho->getCampo( 'cep_entrega' )." Município: ".$rsGeraCarneCabecalho->getCampo( 'municipio_entrega' );
        $this->obRCarneMata->stLoteamento = $rsGeraCarneCabecalho->getCampo( 'loteamento' );
        $this->obRCarneMata->stZoneamento = $rsGeraCarneCabecalho->getCampo( 'zoneamento' );
        $this->obRCarneMata->stNatureza = $rsGeraCarneCabecalho->getCampo( 'natureza' );
        $this->obRCarneMata->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
        $this->obRCarneMata->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
        $this->obRCarneMata->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
        $this->obRCarneMata->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
        $this->obRCarneMata->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );

        $this->obRCarneMata->setRua               ( "Endereco: ".$rsGeraCarneCabecalho->getCampo( 'nom_logradouro' )." Bairro: ".$rsGeraCarneCabecalho->getCampo( 'bairro' )." CEP: ".$rsGeraCarneCabecalho->getCampo( 'cep' )." Município: ".$rsGeraCarneCabecalho->getCampo( 'municipio' ) );
        $this->obRCarneMata->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
        $this->obRCarneMata->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
        $this->obRCarneMata->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
        $this->obRCarneMata->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );

        $this->obRCarneMata->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
        $this->obRCarneMata->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
        $this->obRCarneMata->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
        $this->obRCarneMata->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
        $this->obRCarneMata->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
        $this->obRCarneMata->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
        $this->obRCarneMata->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
        $this->obRCarneMata->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );

/*

        if ( !$this->getConsolidacao() ) {
            $this->obRCarneMata->arDados[0]["tributo"] = $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' );
            $this->obRCarneMata->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' )              );
        } else {
            include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
            $obRARRGrupo = new RARRGrupo;
            $arGruposConsolidados = $sessao->transf4['grupos_consolidados'];
            $contGrupos = 0;
            $stGrupoTotal = null;
            while ( $contGrupos < 3 && $contGrupos < count ( $arGruposConsolidados ) ) {

                $stGrupo = $arGruposConsolidados[$contGrupos];
                $stGrupo = substr ( $stGrupo, 0, 20 );
                if ($contGrupos == 0) {
                    $this->obRCarneMata->setTributoAbrev ( $stGrupo );
                } elseif ($contGrupos == 1) {
                    $this->obRCarneMata->setTributoAbrev2 ( $stGrupo );
                } elseif ($contGrupos == 2) {
                    $this->obRCarneMata->setTributoAbrev3 ( $stGrupo );
                }

                $stGrupoTotal .= $stGrupo. " - ";
                $contGrupos ++;
            }
            $stGrupoTotal = substr ( $stGrupoTotal, 0, (strlen ($stGrupoTotal)-3 ) );
            $this->obRCarneMata->setTributo ( $stGrupoTotal );
            $this->obRCarneMata->arDados[0]["tributo"] = $stGrupoTotal;
        }
*/

        $this->obRCarneMata->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
        $this->obRCarneMata->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
        $this->obRCarneMata->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
        $this->obRCarneMata->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );

        // capturar creditos
        $this->obRCarneMata->setObservacaoL1 ( $this->obRCarneMata->getObservacaoL1().$rsGeraCarneCabecalho->getCampo( 'descricao_credito').": ".$rsGeraCarneCabecalho->getCampo( 'valor' )."  ");


  //      } //fim do loop de reemitirCarne
        $this->obRCarneMata->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarneMata->getTaxaLimpezaAnual() );
        // formatar
        if ($this->obRCarneMata->getValorAnualReal()>0) {
        $this->obRCarneMata->setValorAnualReal    ( number_format($this->obRCarneMata->getValorAnualReal(),2,',','.') );
        }
        if ($this->obRCarneMata->getTaxaLimpezaAnual()>0) {
        $this->obRCarneMata->setTaxaLimpezaAnual  ( number_format($this->obRCarneMata->getTaxaLimpezaAnual(),2,',','.') );
        }
        if ($this->obRCarneMata->getImpostoAnualReal()>0) {
        $this->obRCarneMata->setImpostoAnualReal  ( number_format($this->obRCarneMata->getImpostoAnualReal(),2,',','.') );
        }
        if ($this->obRCarneMata->getValorTributoReal()>0) {
        $this->obRCarneMata->setValorTributoReal  ( number_format($this->obRCarneMata->getValorTributoReal(),2,',','.') );
        }
/*        $this->obRCarneMata->drawProtocolo();
        $this->obRCarneMata->posicionaVariaveisProtocolo();
*/
        $inParcela = 0;
        if ( count( $chave ) > 1 ) {
            $inCount = 2;
        } else {
            $inCount = 0;
        }

        $this->inHorizontal = 7;
        $this->inVertical = 20;

        $this->obBarra = new RCodigoBarraFebraban;
        $this->arBarra = array();

        $this->obRCarneMata->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRCarneMata->setImagem("");

        $nuValorTotal = $flValorTotalGeral;
        $nuValorNormal = $flValorTotal;
        $stJuroNormal = $flValorTotalJuros;
        $stMultaNormal = $flValorTotalMulta;

        $this->obRCarneMata->stValorDesconto = $arValorNormal[4];

        /*if ( count( $chave ) == 1 ) {
            $this->obRCarneMata->arDados[0]["desc"] = $flValorTotalDesc;
            $this->obRCarneMata->arDados[0]["mes"] = $arTmp[1];
            $this->obRCarneMata->arDados[0]["exercicio"] = $this->obRARRCarne->stExercicio;

            //alteracao na parcela 11_03_2008
            $this->obRCarneMata->arDados[0]["valor_lanc"] = number_format( $nuValorNormal, 2, ',', '.' );
            $this->obRCarneMata->arDados[0]["correcao"] = number_format( $flValorTotalCorrecao, 2, ',', '.' );
            $this->obRCarneMata->arDados[0]["juros"] = number_format( $stJuroNormal, 2, ',', '.' );
            $this->obRCarneMata->arDados[0]["multa"] = number_format( $stMultaNormal, 2, ',', '.' );
            $this->obRCarneMata->arDados[0]["total"] = number_format( $nuValorTotal, 2, ',', '.' );
        }*/

        $this->obRCarneMata->flValorOutros = number_format( $flValorTotalCorrecao, 2, ',', '.' );
        $this->obRCarneMata->setValor ( number_format($nuValorNormal,2,',','.') );
        $this->obRCarneMata->setValorTotal   ( number_format($nuValorTotal,2,',','.') );
        $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');
        $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
        $this->obRCarneMata->stValorDesconto = number_format( $flValorTotalDesc, 2, ',', '.' );

        $this->obRCarneMata->drawCapaCarne( $this->inHorizontal, $this->inVertical );
        $this->obRCarneMata->posicionaVariaveisCapa( $this->inHorizontal, $this->inVertical );

        foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
            $obTARRCarne->ListaDadosPorGrupoParaCarneIPTU( $rsListaDetalheCreditos, " WHERE al.cod_lancamento = ".$valor." AND ap.cod_parcela = ".$parcela['cod_parcela']." AND carne.numeracao = '".$parcela['numeracao']."'" );
            $flValorTotal = 0.00;
            $flValorTotalJuros = 0.00;
            $flValorTotalMulta = 0.00;
            $flValorTotalCorrecao = 0.00;
            $nuDesconto = 0.00;
            while ( !$rsListaDetalheCreditos->Eof() ) {
                $flValorTotal += $rsListaDetalheCreditos->getCampo("valor_credito");
                $nuDesconto += ($rsListaDetalheCreditos->getCampo("valor_desconto")>0)?$rsListaDetalheCreditos->getCampo("valor_credito")-$rsListaDetalheCreditos->getCampo("valor_desconto"):0.00;
                $flValorTotalJuros += $rsListaDetalheCreditos->getCampo("credito_juros_pagar");
                $flValorTotalMulta += $rsListaDetalheCreditos->getCampo("credito_multa_pagar");
                $flValorTotalCorrecao += $rsListaDetalheCreditos->getCampo("credito_correcao_pagar");
                $rsListaDetalheCreditos->proximo();
            }

            $rsListaDetalheCreditos->setPrimeiroElemento();
            $flValorTotalGeral = $flValorTotalCorrecao + $flValorTotalMulta + $flValorTotalJuros + ($flValorTotal - $nuDesconto);

            $nuValorTotal = $flValorTotalGeral;
            $nuValorNormal = $flValorTotal;
            $stJuroNormal = $flValorTotalJuros;
            $stMultaNormal = $flValorTotalMulta;

            //setando dados de cada parcela
            $this->obRCarneMata->flValorOutros = number_format( $flValorTotalCorrecao, 2, ',', '.' );
            $this->obRCarneMata->setValor ( number_format($nuValorNormal,2,',','.') );
            $this->obRCarneMata->setValorTotal   ( number_format($nuValorTotal,2,',','.') );
            $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');
            $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
            $this->obRCarneMata->stValorDesconto = number_format( $nuDesconto, 2, ',', '.' );
            $this->obRCarneMata->arDados[0]["parcela"] = $rsListaDetalheCreditos->getCampo("nr_parcela");

            $this->obRCarneMata->setTributo ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' ) );
            $this->arBarra['valor_documento'] = number_format( $nuValorTotal, 2, '.', '' );

//            $this->arBarra['valor_documento'] = number_format( 112, 2, '.', '' );

            $this->arBarra['vencimento'] = (string) $rsListaDetalheCreditos->getCampo( 'vencimento' );
            $this->arBarra['nosso_numero'] = (string) $rsListaDetalheCreditos->getCampo( 'numeracao' );
            $this->obRCarneMata->setNumeracao( (string) $rsListaDetalheCreditos->getCampo( 'numeracao' ) );
            $this->arBarra['cod_febraban'] = $inCodFebraban;

            if ($diffBaixa) {
                $this->arBarra['tipo_moeda'] = 6;
                $this->obRCarneMata->setParcelaUnica ( true );
                $this->obRCarneMata->lblTitulo2        = ' ';
                $this->obRCarneMata->lblValorCotaUnica = 'VALOR TOTAL';
                $this->obRCarneMata->setVencimento   ( $rsListaDetalheCreditos->getCampo( 'vencimento' ) );
                $this->obRCarneMata->setValor        ( number_format($nuValorNormal,2,',','.') );
                $this->obRCarneMata->setParcela ( $rsListaDetalheCreditos->getCampo( 'info' ) );
            } else {
                if ( $rsListaDetalheCreditos->getCampo( 'nr_parcela' ) == 0 ) {
                    $this->arBarra['tipo_moeda'] = 6;
                    $this->obRCarneMata->setParcelaUnica ( true );
                    $this->obRCarneMata->setVencimento   ( $rsListaDetalheCreditos->getCampo( 'vencimento' ) );
                    $this->obRCarneMata->setValor        ( number_format($nuValorNormal,2,',','.') );
                    $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');
                    $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');

                    /**
                    * Recuperar Desconto
                    */
    //                include_once(CAM_GT_ARR_MAPEAMENTO."FARRParcentualDescontoParcela.class.php");

    //              $obPercentual = new FARRParcentualDescontoParcela;
        //            $obPercentual->executaFuncao($rsPercentual,"".$parcela["cod_parcela"].",'".$dtVencimento."'");
        //          $this->obRCarneMata->setObservacaoL1 ( 'Cota Única com '.$rsPercentual->getCampo('valor').'% de desconto.' );                      ;
            //        $this->obRCarneMata->setObservacaoL2 ( 'Desconto não incide sobre a Taxa de Coleta de Lixo' );
            //      $this->obRCarneMata->setObservacaoL3 ( 'Não receber após o vencimento.' );
    //                $this->obRCarneMata->setParcela ( 'ÚNICA' );
                } else {
                    $this->arBarra['tipo_moeda'] = 7;
                    $arVencimentos = $this->geraParcelas( $rsListaDetalheCreditos->getCampo( 'vencimento' ),4);
    //                $this->obRCarneMata->setParcela( $rsParcela->getCampo( 'info' ));
                    $this->obRCarneMata->setParcelaUnica( false );
                    $this->obRCarneMata->setVencimento  ( $rsListaDetalheCreditos->getCampo( 'vencimento' ) );

                    $arTmp = explode('/',$rsListaDetalheCreditos->getCampo( 'vencimento' ));

                    $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');
                    $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
                    $this->obRCarneMata->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                }
            }

            $this->obRCarneMata->setValorTotal   ( number_format($nuValorTotal,2,',','.') );

            $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
            $this->obRCarneMata->setBarCode( $this->arCodigoBarra['codigo_barras'] );
            $this->obRCarneMata->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );
            if (!$inParcela) {
                $this->obRCarneMata->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarneMata->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $inParcela = 1;
            } else {
                $this->obRCarneMata->drawCarne( $this->inHorizontal, $this->inVertical-160 );
                $this->obRCarneMata->posicionaVariaveis( $this->inHorizontal, $this->inVertical-160 );
                $this->obRCarneMata->setPicote( $this->inHorizontal, $this->inVertical-160 );
            }

            $this->inVertical += 100;
            if ($inCount == 2) {
                $this->obRCarneMata->novaPagina();
                $inCount = 0;
                $this->inVertical = 7;
                $this->boPulaPagina = false;
            } else {
                $this->boPulaPagina = true;
            }
            $inCount++;

            //if ( ( $inSaltaPagina != count($arEmissao) ) && ( ( count($chave) != 2 ) && ( count($chave) != 3 ) ) ) {
            if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
                $this->obRCarneMata->novaPagina();
            }
        }
    } // fim foreach $arEmissao

    if ( Sessao::read( 'stNomPdf' ) )
        $stNome     = Sessao::read( 'stNomPdf' );
    else
        $stNome     = "Carne.pdf";

    if ( Sessao::read( 'stParamPdf' ) )
        $stParam    = Sessao::read( 'stParamPdf' );
    else
        $stParam    = "I";
    $this->obRCarneMata->show($stNome,$stParam); // lanca o pdf
}

function geraParcelas($data, $iteracao)
{
    $arDataResult = array();

    for ($i=0;$i<$iteracao;$i++) {
        $arData = explode('/',$data);

        $mes = $arData[1];
        $dia = $arData[0];
        $ano = $arData[2];

        switch ( (int) $mes ) {
            case 2 :
                    if ($ano % 4 == 0) {
                        $dia = 29;
                    } else {
                        $dia = 28;
                    }
            break;
            case 1 :
            case 3 :
            case 5 :
            case 7 :
            case 8 :
            case 10: $dia = 31;
            break;

            case 4 :
            case 6 :
            case 9 :
            case 11: $dia = 30;
            break;
        }

        $data = str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$ano;
        array_push($arDataResult,$data);

        $mes++;
        if ($mes > 12) {
            $mes = 1;
            $ano++;
        }

        $data = $dia.'/'.$mes.'/'.$ano;
    }

    return $arDataResult;
}

}

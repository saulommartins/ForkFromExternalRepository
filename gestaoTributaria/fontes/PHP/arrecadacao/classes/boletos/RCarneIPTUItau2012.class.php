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
  * Carnê IPTU Manaquiri
  * Data de criação : 09/01/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Davi Ritter Aroldi

  * @package URBEM

  Caso de uso: uc-05.03.11
*/

// include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFichaCompensacaoCaixa.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkPDF.inc.php';
define       ('FPDF_FONTPATH','font/');

class RProtocolo extends fpdf
{
    /* Labels  */
    public $Titulo1         = 'Prefeitura Municipal de Manaquiri';
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
    public $flTaxaColetaLixo;
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
    public function setTaxaColetaLixo($valor) { $this->flTaxaColetaLixo   = $valor; }

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
    public function getTaxaColetaLixo() { return $this->flTaxaColetaLixo   ; }

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
        $this->DefOrientation='P';
        $this->w=$this->DefPageFormat[0];
        $this->h=$this->DefPageFormat[1];
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

        $this->setLoginUsuario ( Sessao::read("nomCgm") );
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
        if ( Sessao::read('itbi_observacao') == 'sim') {
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

        if ( Sessao::read('itbi_observacao') == 'sim') {
            $this->Text   ( 8    , 73+$this->inTamY  ,"REQUERIMENTO DE I.T.I.V." );
        } else {
            $this->Text   ( 8    , 73  , $this->lblObservacao    );
        }
        if ( Sessao::read('itbi_observacao') == 'sim') {
            $this->Text   ( 115.5, 73+$this->inTamY  , "TAXA DE EXPEDIENTE"    );
        } else {
            $this->Text   ( 115.5, 73  , $this->lblLimpAnualRl   );
        }
        if ( Sessao::read('itbi_observacao') == 'sim') {
            $this->Text   ( 163  , 73+$this->inTamY  , $this->lblTxAverbacao );
            $this->Text   ( 163  , 82+$this->inTamY  , $this->lblTotalAnualRl  );
        } else {
            $this->Text   ( 163  , 73  , $this->lblTotalAnualRl  );
            $this->Text   ( 163  , 82  , $this->lblTotalLancado  );
        }

        if ( Sessao::read('itbi_observacao') == 'sim') {
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
        if ( !Sessao::read('itbi_observacao') ) {
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
        if ( Sessao::read('itbi_observacao') == 'sim') {
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
        if ( Sessao::read('itbi_observacao') == 'sim')
            $this->Text   ( 145   , 68+$this->inTamY   , $rsItbi->getCampo('base_calculo'));
        else
            $this->Text   ( 145   , 68   , strtoupper($this->flValorTributoReal) );

        $this->Text   ( 183   , 68+$this->inTamY   , strtoupper($this->flImpostoAnualReal ) );
        if ( Sessao::read('itbi_observacao') == 'sim') {
            $this->Text   ( 145   , 76.5+$this->inTamY , $rsItbi->getCampo('taxa') );
            $this->Text   ( 145   , 85.5+$this->inTamY , $rsItbi->getCampo('multa') );
            $this->Text   ( 183   , 76.5+$this->inTamY , $this->flTxAverbacao       );
        } else {
            $this->Text   ( 145   , 76.5 , strtoupper($this->flTaxaLimpezaAnual) );
        }
        if ( Sessao::read('itbi_observacao') == 'sim') {
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
    /* labels */
    public $lblTitulo1 = 'Prefeitura Municipal de Itau';
    public $lblTitulo2 = 'IPTU';

    public $lblCedente = 'Cedente:';
    public $lblCodCedente = 'Ag./Cod. Cedente:';
    public $lblDtDocumento = 'Data do Documento:';
    public $lblNossoNumero = 'Nosso Número:';
    public $lblNumero = 'Nº do Documento:';
    public $lblEspecieDoc = 'Espécie Doc.:';
    public $lblCarteira = 'Carteira:';
    public $lblAceite = 'Aceite:';
    public $lblEspecie = 'Espécie:';

    public $lblSacado = 'Sacado:';
    public $lblEndereco = 'Endereço:';
    public $lblCidade = 'Cidade:';
    public $lblCEP = 'CEP:';
    public $lblRespCedente = 'Texto de Responsabilidade do Cedente:';

    public $lblVencimento = 'Vencimento';
    public $lblVlTitulo = 'Valor do Título';
    public $lblAutMecanica = 'Autenticação Mecânica';

    public $lblLocalPagamento = 'Local de Pagamento';
    public $lblDtProcessamento = 'Data de Processamento';

    // var $lblReceita = 'Receita:';
    // var $lblMatricula = 'Matricula:';
    // var $lblExercicio = 'Ano:';
    // var $lblParcela   = 'Parcela:';
    // var $lblEmissao = 'EMISSÃO:';

    // var $lblValorPrincipal = "Valor:";
    // var $lblMulta = 'Multa:';
    // var $lblJuros = 'Juros:';
    // var $lblDesconto = 'Desconto:';
    // var $lblValorTotal     = "Total:";

    // var $lblAgencia = 'Bradesco Ag 3727 c/c 3757-5';

    // var $lblObservacao = 'Parcela única já com desconto de 10%';

    // var $lblParcelaIPTU = 'Parcela-IPTU:';
    // var $lblTaxaServicos = 'Taxa de Serviços Públicos';
    // var $lblColetaLixo = 'Coleta de Lixo:';
    // var $lblTaxaLimpeza = 'Limpeza Pública:';
    // var $lblConservVias = 'Conserv de Vias:';

    /* variaveis */
    public $ImagemCarne;
    public $ImagemNotificacao;
    public $stExercicio;
    public $inInscricao;
    public $inCodDivida;
    public $stAtributoUso;
    public $stAtributoConserv;
    public $stAtributoTipo;
    public $stAtributoPadrao;
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
    public $stObservacao;
    public $stObservacaoL1;
    public $stObservacaoL2;
    public $stObservacaoL3;
    public $stObsVencimento;
    public $stNumeracao;
    public $arInfoParcelas;
    public $flAliquota;
    public $inExercicioCredito;
    public $stDocumento;
    public $flValorColetaLixoParcela = '0,00';
    public $flValorDesconto = '0,00';
    public $flValorMulta  = '0,00';
    public $flValorJuros  = '0,00';
    public $flValorOutros = '0,00';
    public $flValorTotal  = '0,00';
    public $tamY = 0.93;

    /* setters */
    public function setImagemCarne($valor) { $this->ImagemCarne      = $valor; }
    public function setImagemNotificacao($valor) { $this->ImagemNotificacao= $valor; }
    public function setExercicio($valor) { $this->stExercicio      = $valor; }
    public function setInscricao($valor) { $this->inInscricao      = $valor; }
    public function setCodDivida($valor) { $this->inCodDivida      = $valor; }
    public function setAtributoUso($valor) { $this->stAtributoUso    = $valor; }
    public function setAtributoConserv($valor) { $this->stAtributoConserv= $valor; }
    public function setAtributoTipo($valor) { $this->stAtributoTipo   = $valor; }
    public function setAtributoPadrao($valor) { $this->stAtributoPadrao = $valor; }
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
    public function setObservacao($valor) { $this->stObservacao     = $valor; }
    public function setObsVencimento($valor) { $this->stObsVencimento  = $valor; }
    public function setNumeracao($valor) { $this->stNumeracao      = $valor; }
    public function setValorTotal($valor) { $this->flValorTotal     = $valor; }
    public function setInfoParcelas($valor) { $this->arInfoParcelas   = $valor; }
    public function setAliquota($valor) { $this->flAliquota       = $valor; }
    public function setValorDesconto($valor) { $this->flValorDesconto  = $valor; }
    public function setParcelaIPTU($valor) { $this->flValorParcelaIPTU = $valor; }
    public function setTaxaColetaLixoParcela($valor) { $this->flValorColetaLixoParcela = $valor; }
    public function setExercicioCredito($valor) { $this->inExercicioCredito = $valor; }
    public function setDocumento($valor) { $this->stDocumento      = $valor; }

    /* getters */
    public function getImagemCarne() { return $this->ImagemCarne      ; }
    public function getImagemNotificacao() { return $this->ImagemNotificacao; }
    public function getExercicio() { return $this->stExercicio      ; }
    public function getInscricao() { return $this->inInscricao      ; }
    public function getCodDivida() { return $this->inCodDivida      ; }
    public function getAtributoUso() { return $this->stAtributoUso    ; }
    public function getAtributoConserv() { return $this->stAtributoConserv; }
    public function getAtributoTipo() { return $this->stAtributoTipo   ; }
    public function getAtributoPadrao() { return $this->stAtributoPadrao ; }
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
    public function getObservacao() { return $this->stObservacao     ; }
    public function getObsVencimento() { return $this->stObsVencimento  ; }
    public function getNumeracao() { return $this->stNumeracao      ; }
    public function getInfoParcelas() { return $this->arInfoParcelas   ; }
    public function getAliquota() { return $this->flAliquota       ; }
    public function getValorDesconto() { return $this->flValorDesconto  ; }
    public function getParcelaIPTU() { return $this->flValorParcelaIPTU; }
    public function getTaxaColetaLixoParcela() { return $this->flValorColetaLixoParcela; }
    public function getExercicioCredito() { return $this->inExercicioCredito; }
    public function getDocumento() { return $this->stDocumento      ; }

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
        // // truncar tributo
        // if ( !$this->stTributoAbrev )
        //     $this->stTributoAbrev = substr($this->stTributo,0,25);

        // $this->stNomCgm  = substr($this->stNomCgm ,0,80);
        // $this->setFont( 'Arial','',9 );

        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x+6, $y+5, 29, 8, $stExt );
        }

        // /* returna retangulo */
        // //via prefeitura

        // //via contribuinte

        /* linha horizontais */
        $this->SetLineWidth( 0.25 );
        $this->Line( $x+4, ($y), (196+$x), ($y) );
        $this->Line( $x+6, ($y+15), (196+$x), ($y+15) );
        $this->Line( $x+6, ($y+40), (154+$x), ($y+40) );
        $this->Line( $x+6, ($y+72), (196+$x), ($y+72) );
        $this->Line( $x+6, ($y+87), (196+$x), ($y+87) );

        /* linha verticais */
        $this->Line( $x+44, ($y+4), (44+$x), ($y+15) );
        $this->Line( $x+68, ($y+4), (68+$x), ($y+15) );
        $this->Line( $x+154, ($y+15), (154+$x), ($y+72) );

        /* linha horizontais */
        $this->SetLineWidth( 0.05 );
        $this->Line( $x+6, ($y+21.25), (196+$x), ($y+21.25) );
        $this->Line( $x+6, ($y+27.50), (196+$x), ($y+27.50) );
        $this->Line( $x+6, ($y+33.75), (196+$x), ($y+33.75) );
        $this->Line( $x+154, ($y+40), (196+$x), ($y+40) );
        $this->Line( $x+154, ($y+46.25), (196+$x), ($y+46.25) );
        $this->Line( $x+154, ($y+52.50), (196+$x), ($y+52.50) );
        $this->Line( $x+154, ($y+58.75), (196+$x), ($y+58.75) );
        $this->Line( $x+154, ($y+65), (196+$x), ($y+65) );

        /* linha verticais */
        $this->Line( $x+38, ($y+27.50), (38+$x), ($y+40) );
        $this->Line( $x+55, ($y+33.75), (55+$x), ($y+40) );
        $this->Line( $x+76, ($y+27.50), (76+$x), ($y+40) );
        $this->Line( $x+87, ($y+27.50), (87+$x), ($y+33.75) );
        $this->Line( $x+98, ($y+27.50), (98+$x), ($y+40) );

        $this->setFont('Arial','I',7);
        $this->Text   ( ($x+6)  , ($y+(18.5*$this->tamY)) , $this->lblLocalPagamento );
        $this->Text   ( ($x+6)  , ($y+(25*$this->tamY)) , $this->lblCedente );
        $this->Text   ( ($x+6)  , ($y+(31.75*$this->tamY)) , $this->lblDtDocumento );
        $this->Text   ( ($x+39) , ($y+(31.75*$this->tamY)) , $this->lblNumero );
        $this->Text   ( ($x+76) , ($y+(31.75*$this->tamY)) , $this->lblEspecie );
        $this->Text   ( ($x+87.5) , ($y+(31.75*$this->tamY)) , $this->lblAceite );
        $this->Text   ( ($x+98) , ($y+(31.75*$this->tamY)) , $this->lblDtProcessamento );
        $this->Text   ( ($x+6)  , ($y+(38.25*$this->tamY)) , 'Uso do Banco:' );
        $this->Text   ( ($x+39) , ($y+(38.25*$this->tamY)) , $this->lblCarteira );
        $this->Text   ( ($x+56) , ($y+(38.25*$this->tamY)) , 'Moeda:' );
        $this->Text   ( ($x+77) , ($y+(38.25*$this->tamY)) , 'Quantidade:' );
        $this->Text   ( ($x+98) , ($y+(38.25*$this->tamY)) , 'Valor:' );

        $this->Text   ( ($x+156) , ($y+(18.80*$this->tamY)) , $this->lblVencimento.':' );
        $this->Text   ( ($x+156) , ($y+(25.50*$this->tamY)) , $this->lblCodCedente );
        $this->Text   ( ($x+156) , ($y+(32.30*$this->tamY)) , $this->lblNossoNumero );
        $this->Text   ( ($x+156) , ($y+(38.90*$this->tamY)) , '(=)Valor do Documento:' );
        $this->Text   ( ($x+156) , ($y+(45.60*$this->tamY)) , '(-)Desconto:' );
        $this->Text   ( ($x+156) , ($y+(52.30*$this->tamY)) , '(-)Outras Deduções/Abatimento:' );
        $this->Text   ( ($x+156) , ($y+(59.00*$this->tamY)) , '(+)Mora/Multa/Juros:' );
        $this->Text   ( ($x+156) , ($y+(65.70*$this->tamY)) , '(+)Outros Acréscimos:' );
        $this->Text   ( ($x+156) , ($y+(72.40*$this->tamY)) , '(=)Valor Cobrado:' );

        $this->Text   ( ($x+6) , ($y+(81*$this->tamY)) , $this->lblSacado );
        $this->Text   ( ($x+6) , ($y+(92.5*$this->tamY)) , 'Sacador/Avalista:' );

        $this->setFont('Arial','I',8);
        $this->Text   ( ($x+6) , ($y+(46*$this->tamY)) , $this->lblRespCedente );

        $this->setFont('Arial','I',7);
        $this->Text   ( ($x+165), ($y+(97*$this->tamY)), 'Ficha de Compensação' );
        $this->Text   ( ($x+165), ($y+(99.5*$this->tamY)), 'Autenticação no verso' );

    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        $parcela = $this->getInfoParcelas();
        $valorJurosMulta =(float) str_replace( ',', '.', $this->flValorMulta) + (float) str_replace( ',', '.', $this->flValorJuros);

        $this->setFont('Arial','B',9);
        $this->Text   ( ($x+6) , ($y+(22*$this->tamY)) , 'CASAS LOTERICAS, AG.CAIXA E REDE BANCARIA' );
        $this->Text   ( ($x+6) , ($y+(28.5*$this->tamY)) , 'PREF MUNICIPAL DE ITAU' );
        $this->Text   ( ($x+6) , ($y+(35.25*$this->tamY)) , $parcela['vencimento'] );
        $this->Text   ( ($x+39) , ($y+(35.25*$this->tamY)) , 'IPTU/'.$this->getExercicioCredito().'/'.$this->getInscricao() );
        $this->Text   ( ($x+77) , ($y+(35.25*$this->tamY)) , 'OU' );
        $this->Text   ( ($x+88) , ($y+(35.25*$this->tamY)) , 'N' );
        $this->Text   ( ($x+99) , ($y+(35.25*$this->tamY)) , $this->getProcessamento() );
        $this->Text   ( ($x+39) , ($y+(42.25*$this->tamY)) , 'SR' );
        $this->Text   ( ($x+56) , ($y+(42.25*$this->tamY)) , 'R$' );
        $this->Text   ( ($x+99) , ($y+(42.25*$this->tamY)) , 'X' );

        $this->Text   ( ($x+164) , ($y+(22*$this->tamY)) , $parcela['vencimento'] );
        $this->Text   ( ($x+164) , ($y+(28.5*$this->tamY)) , '0763.870.00000058-3' );
        $this->Text   ( ($x+164) , ($y+(35.25*$this->tamY)) , $this->stNumeracao );
       //  $this->Text   ( ($x+164) , ($y+(42.25*$this->tamY)) , $parcela['valor'] );
        $this->Text   ( ($x+164) , ($y+(42.25*$this->tamY)) , $this->getValor() );
        $this->Text   ( ($x+164) , ($y+(62.25*$this->tamY)), $valorJurosMulta);
        $this->Text   ( ($x+164) , ($y+(68.95*$this->tamY)) , $this->flValorOutros );
        $this->Text   ( ($x+164) , ($y+(75.65*$this->tamY)) , $this->flValorTotal );

        $this->Text   ( ($x+9) , ($y+(50*$this->tamY)) , 'REFERENTE AO IPTU DO EXERCÍCIO DE 2012' );
        $this->Text   ( ($x+9) , ($y+(56*$this->tamY)) , 'MULTA DE 30% SOBRE O VALOR DEVIDO' );
        $this->Text   ( ($x+9) , ($y+(60*$this->tamY)) , 'JUROS DE 0,33% AO DIA' );
        $this->Text   ( ($x+9) , ($y+(65.7*$this->tamY)) , 'NÃO RECEBER APÓS 90 DIAS DE ATRASO' );
        $this->Text   ( ($x+9) , ($y+(69.7*$this->tamY)) , 'GOVERNO DA RECONSTRUÇÃO' );
        $this->Text   ( ($x+9) , ($y+(73.7*$this->tamY)) , 'INVISTA NA CIDADE, PAGUE SEU IPTU' );

        $this->Text   ( ($x+17) , ($y+(81*$this->tamY)) , $this->getNomCgm().' - '.$this->getDocumento() );//CPF ou CNPJ
        $this->Text   ( ($x+17) , ($y+(84.5*$this->tamY)) , 'RUA: '.$this->getRua().', '.$this->getNumero().' - '.$this->getNomBairro().' - '.$this->getCidade().'-'.$this->getUf() );
        $this->Text   ( ($x+17) , ($y+(88*$this->tamY)) , 'CEP: '.$this->getCep() );
        $this->Text   ( ($x+27) , ($y+(92.5*$this->tamY)) , '-' );//?

        $this->setFont('Arial','I',15);
        $this->Text   ( ($x+49), ($y+(14*$this->tamY)), '104-0' );// refere à caixa, agência

        $this->setFont('Arial','B',12);
        $this->Text   ( ($x+71), ($y+(13*$this->tamY)), $this->stLinhaCode );
        $this->setFont('Arial','B',9);
        $this->defineCodigoBarras( ($x+9), ($y+(97*$this->tamY)), $this->stBarCode, 0.82 );
    }

    public function drawNotificacao($x, $y)
    {
        /* posiciona imagem */
        if ($this->ImagemNotificacao) {
            $stExt = substr( $this->ImagemNotificacao, strlen($this->ImagemNotificacao)-3, strlen($this->ImagemNotificacao) );
            $this->Image( $this->ImagemNotificacao, $x+4, $y+3, 195.55, 31, $stExt );
        }

        /* returna retangulo */
        //via prefeitura
        $lw = $this->LineWidth;
        $this->SetLineWidth( 0.25 );
        $this->Rect( $x+4, ($y+(39*$this->tamY)), 192, 127 ); // grade externa
        $this->Rect( $x+6, ($y+(44*$this->tamY)), 101, 34 ); // informações prefeitura
        $this->Rect( $x+111, ($y+(44*$this->tamY)), 83, 34 ); // informações contribuinte
        $this->Rect( $x+6, ($y+(84*$this->tamY)), 187, 64 ); // observações
        $this->Rect( $x+6, ($y+(157*$this->tamY)), 187, 12.5 ); // valores e vencimento

        /* linha vertical */
        //informações
        $this->Line( $x+59, ($y+(157*$this->tamY)), (59+$x), ($y+(170.5*$this->tamY)) );
        $this->Line( $x+114, ($y+(157*$this->tamY)), (114+$x), ($y+(170.5*$this->tamY)) );

        //labels
        //via prefeitura
        $this->setFont('Arial', 'I', 8 );
        $this->Text   ( ($x+9) , ($y+(49*$this->tamY)) , $this->lblCedente );
        $this->Text   ( ($x+9) , ($y+(52.5*$this->tamY)) , $this->lblCodCedente );
        $this->Text   ( ($x+9) , ($y+(56*$this->tamY)) , $this->lblDtDocumento );
        $this->Text   ( ($x+9) , ($y+(59.5*$this->tamY)) , $this->lblNossoNumero );
        $this->Text   ( ($x+9) , ($y+(63*$this->tamY)) , $this->lblNumero );
        $this->Text   ( ($x+9) , ($y+(66.5*$this->tamY)) , $this->lblEspecieDoc );
        $this->Text   ( ($x+9) , ($y+(70*$this->tamY)) , $this->lblCarteira );
        $this->Text   ( ($x+9) , ($y+(73.5*$this->tamY)) , $this->lblAceite );
        $this->Text   ( ($x+9) , ($y+(77*$this->tamY)) , $this->lblEspecie );

        //via contribuinte
        $this->Text   ( ($x+116) , ($y+(49*$this->tamY)) , $this->lblSacado );
        $this->Text   ( ($x+116) , ($y+(56*$this->tamY)) , $this->lblEndereco );
        $this->Text   ( ($x+116) , ($y+(66.5*$this->tamY)) , $this->lblCidade );
        $this->Text   ( ($x+116) , ($y+(73.5*$this->tamY)) , $this->lblCEP );

        //observações
        $this->setFont('Arial', 'I', 12 );
        $this->Text   ( ($x+9) , ($y+(89*$this->tamY)) , $this->lblRespCedente );

        //valores e vencimento
        $this->setFont('Arial', 'I', 9 );
        $this->Text   ( ($x+9) , ($y+(161*$this->tamY)) , $this->lblVencimento );
        $this->Text   ( ($x+64) , ($y+(161*$this->tamY)) , $this->lblVlTitulo );
        $this->Text   ( ($x+119) , ($y+(161*$this->tamY)) , $this->lblAutMecanica );
    }

    public function posicionaVariaveisNotificacao($x, $y)
    {
        $parcela = $this->getInfoParcelas();
        //via prefeitura
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+37) , ($y+(49*$this->tamY)) , 'PREF MUNICIPAL DE ITAU' );
        $this->Text   ( ($x+37) , ($y+(52.5*$this->tamY)) , '0763.870.00000058-3' );
        $this->Text   ( ($x+37) , ($y+(56*$this->tamY)) , $parcela['vencimento'] );
        $this->Text   ( ($x+37) , ($y+(59.5*$this->tamY)) , $this->stNumeracao );
        $this->Text   ( ($x+37) , ($y+(63*$this->tamY)) , 'IPTU/'.$this->getExercicioCredito().'/'.$this->getInscricao() );
        $this->Text   ( ($x+37) , ($y+(66.5*$this->tamY)) , 'OU' );
        $this->Text   ( ($x+37) , ($y+(70*$this->tamY)) , 'SR' );
        $this->Text   ( ($x+37) , ($y+(73.5*$this->tamY)) , 'N' );
        $this->Text   ( ($x+37) , ($y+(77*$this->tamY)) , 'R$' );

        //via contribuinte
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+131) , ($y+(49*$this->tamY)) , $this->getNomCgm() );
        $this->Text   ( ($x+131) , ($y+(56*$this->tamY)) , $this->getRua().", ".$this->getNumero() );
        $this->Text   ( ($x+131) , ($y+(60*$this->tamY)) , $this->getNomBairro() );
        $this->Text   ( ($x+131) , ($y+(66.5*$this->tamY)) , $this->getCidade()." - ".$this->getUf() );
        $this->Text   ( ($x+131) , ($y+(73.5*$this->tamY)) , $this->getCep() );

        // observações
        $this->setFont('Arial', 'B', 9 );

        $this->Text   ( ($x+9) , ($y+(110*$this->tamY)) , 'REFERENTE AO IPTU DO EXERCÍCIO DE 2012' );
        $this->Text   ( ($x+9) , ($y+(116*$this->tamY)) , 'MULTA DE 30% SOBRE O VALOR DEVIDO' );
        $this->Text   ( ($x+9) , ($y+(120*$this->tamY)) , 'JUROS DE 0,33% AO DIA' );
        $this->Text   ( ($x+9) , ($y+(125.7*$this->tamY)) , 'NÃO RECEBER APÓS 90 DIAS DE ATRASO' );
        $this->Text   ( ($x+9) , ($y+(129.7*$this->tamY)) , 'GOVERNO DA RECONSTRUÇÃO' );
        $this->Text   ( ($x+9) , ($y+(133.7*$this->tamY)) , 'INVISTA NA CIDADE, PAGUE SEU IPTU' );

        // valores e vencimento
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+9) , ($y+(168*$this->tamY)) , $parcela['vencimento'] );
        // $this->Text   ( ($x+64) , ($y+(168*$this->tamY)) , $parcela['valor'] );
        $this->Text   ( ($x+64) , ($y+(168*$this->tamY)) , $this->getValor() );

        // $this->Text   ( ($x+178) , ($y+(35*$this->tamY)) , $this->getRua() );
        // $this->Text   ( ($x+178) , ($y+(40*$this->tamY)) , $this->getNomBairro() );

        // $this->Text   ( ($x+162) , ($y+(23*$this->tamY)) , $this->getNumero() );
        // $this->Text   ( ($x+155) , ($y+(29*$this->tamY)) , $this->getCep() );

        // $this->Text   ( ($x+259) , ($y+(35*$this->tamY)) , $this->getNumero() );
        // $this->Text   ( ($x+253) , ($y+(40*$this->tamY)) , $this->getCep() );

        // $this->Text   ( ($x+28) , ($y+(39*$this->tamY)) , $this->getAtributoUso() );
        // $this->Text   ( ($x+28) , ($y+(44*$this->tamY)) , $this->getAtributoTipo() );
        // $this->Text   ( ($x+28) , ($y+(49*$this->tamY)) , $this->getAtributoPadrao() );
        // $this->Text   ( ($x+28) , ($y+(54*$this->tamY)) , $this->getAtributoConserv() );
        // $this->Text   ( ($x+28) , ($y+(59*$this->tamY)) , str_replace('.', ',', $this->getAreaTerreno()).'m²' );
        // $this->Text   ( ($x+28) , ($y+(64*$this->tamY)) , str_replace('.', ',', $this->getAreaEdificada()).'m²' );

        // //valores de iptu
        // $this->Text   ( ($x+86) , ($y+(39*$this->tamY)) , $this->getValorTributoReal() );
        // $this->Text   ( ($x+86) , ($y+(44*$this->tamY)) , $this->getAliquota() );
        // $this->Text   ( ($x+86) , ($y+(49*$this->tamY)) , $this->getImpostoAnualReal() );
        // $this->Text   ( ($x+86) , ($y+(54*$this->tamY)) , $this->getTaxaColetaLixo() );
        // $this->Text   ( ($x+86) , ($y+(59*$this->tamY)) , '0,00' );//(Limp. Pública)manaquiri não possui este valor
        // $this->Text   ( ($x+86) , ($y+(64*$this->tamY)) , '0,00' );//(Cons. de Vias)manaquiri não possui este valor

        // //informações de todas as parcelas do carnê
        // $intY = 45;
        // foreach ($this->getInfoParcelas() as $parcela) {
        //     $this->Text   ( ($x+108) , ($y+($intY*$this->tamY)) , $parcela['parcela'] );
        //     $this->Text   ( ($x+131) , ($y+($intY*$this->tamY)) , $parcela['vencimento'] );
        //     $this->Text   ( ($x+156) , ($y+($intY*$this->tamY)) , str_replace('.', ',', $parcela['valor']) );

        //     //retirar após testes na base de mata
        //     if ($intY == 60) {
        //         break;
        //     }

        //     $intY += 5;
        // }

        // $this->Text   ( ($x+188) , ($y+(48*$this->tamY)) , '___/___/_____' );
        // $this->Text   ( ($x+197) , ($y+(56*$this->tamY)) , '______________________________________' );
        // $this->Text   ( ($x+202) , ($y+(64*$this->tamY)) , '___________________________________' );
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
        // //medidas do carnê de manaquiri
        // //linha inferior
        // for ($i=0;$i<=271;($i+=2)) {
        //    $this->Line( ($x+$i), ($y+(68*$this->tamY)), ($x+$i+1), ($y+(68*$this->tamY)) );
        // }

        // //linha superior
        // for ($i=0;$i<=271;($i+=2)) {
        //    $this->Line( ($x+$i), ($y-3), ($x+$i+1), ($y-3) );
        // }

        // //linha divisória
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x+136), ($y+($i*$this->tamY)), ($x+136), ($y+(($i+1)*$this->tamY)) );
        // }

        // //linha esquerda
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x), ($y+($i*$this->tamY)), ($x), ($y+(($i+1)*$this->tamY)) );
        // }

        // //linha direita
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x+272), ($y+($i*$this->tamY)), ($x+272), ($y+(($i+1)*$this->tamY)) );
        // }
    }

    public function setPicoteNotificacao($x, $y, $firstPage = false)
    {
        // //linha inferior
        // for ($i=0;$i<=271;($i+=2)) {
        //    $this->Line( ($x+$i), ($y+(68*$this->tamY)), ($x+$i+1), ($y+(68*$this->tamY)) );
        // }

        // //linha superior
        // for ($i=0;$i<=271;($i+=2)) {
        //    $this->Line( ($x+$i), ($y-3), ($x+$i+1), ($y-3) );
        // }

        // //linha divisória
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x+174), ($y+($i*$this->tamY)), ($x+174), ($y+(($i+1)*$this->tamY)) );
        // }

        // //linha esquerda
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x), ($y+($i*$this->tamY)), ($x), ($y+(($i+1)*$this->tamY)) );
        // }

        // //linha direita
        // for (($i=-3);$i<=67;($i+=2)) {
        //     $this->Line( ($x+272), ($y+($i*$this->tamY)), ($x+272), ($y+(($i+1)*$this->tamY)) );
        // }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneIPTUItau2012
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
function RCarneIPTUItau2012($arEmissao, $horizontal = 7, $vertical = 95)
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

    $boInicio = true;
    $this->obRCarneMata = new RCarneDiversosPetropolis;
    $this->obRCarneMata->configuraProtocolo();

    $stNomeImagemNotificacao = 'logotipo_iptu.png';

    $stNomeImagem = 'logotipo_iptu_boleto.png';

    $inCountTemp = 0;

    foreach ($this->arEmissao as $valor => $chave) {
        $this->obRCarneMata->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarne( $rsGeraCarneCabecalho );
        $flImpostoAnualReal = 0.00;
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarneMata->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {

            $endereco = explode("|*|", $rsGeraCarneCabecalho->getCampo('enderecoentrega'));

            /* montagem cabecalho (protocolo) */
            $this->obRCarneMata->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarneMata->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            //$this->obRCarneMata->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarneMata->setCep               ( $endereco[4]                                                );
            $this->obRCarneMata->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarneMata->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );
            $this->obRCarneMata->setDocumento         ( $rsGeraCarneCabecalho->getCampo( 'documento' )              );

            $this->obRCarneMata->setRua               ( $endereco[0]                                                );
            $this->obRCarneMata->setNumero            ( $endereco[1]                                                );
            $this->obRCarneMata->setComplemento       ( $endereco[2]                                                );
            $this->obRCarneMata->setCidade            ( $endereco[6]                                                );
            $this->obRCarneMata->setUf                ( $endereco[5]                                                );

            $this->obRCarneMata->setInscricao         ( $rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' )    );
            $this->obRCarneMata->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarneMata->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarneMata->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarneMata->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarneMata->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarneMata->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarneMata->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            $this->obRCarneMata->setExercicioCredito  ( $rsGeraCarneCabecalho->getCampo( 'exercicio_credito' )             );
            if ( !$this->getConsolidacao() ) {
            $this->obRCarneMata->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' )              );
            } else {
                include_once ( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );
                $obRARRGrupo = new RARRGrupo;
                $arGruposConsolidados = Sessao::read( 'grupos_consolidados' );
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
            }

            $this->obRCarneMata->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
            $this->obRCarneMata->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
            //$this->obRCarneMata->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarneMata->setNomBairro         ( $endereco[3]            );
            $this->obRCarneMata->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            $this->obRCarneMata->setAliquota          ( $rsGeraCarneCabecalho->getCampo( 'aliquota' )               );
            if ( preg_match('/COLETA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarneMata->setTaxaColetaLixo  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } elseif ( preg_match('/IMPOSTO.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $flImpostoAnualReal += $rsGeraCarneCabecalho->getCampo( 'valor' );
            }

            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        $nuColetaLixo = $this->obRCarneMata->getTaxaColetaLixo();
        $this->obRCarneMata->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarneMata->getTaxaLimpezaAnual() );
        // formatar
        $this->obRCarneMata->setAliquota          ( number_format($this->obRCarneMata->getAliquota(),2,',','.') );
        $this->obRCarneMata->setValorAnualReal    ( number_format($this->obRCarneMata->getValorAnualReal(),2,',','.') );
        $this->obRCarneMata->setTaxaColetaLixo    ( number_format($this->obRCarneMata->getTaxaColetaLixo(),2,',','.') );
        $this->obRCarneMata->setImpostoAnualReal  ( number_format($flImpostoAnualReal,2,',','.') );
        //$this->obRCarneMata->setImpostoAnualReal  ( number_format($this->obRCarneMata->getImpostoAnualReal(),2,',','.') );
        $this->obRCarneMata->setValorTributoReal  ( number_format($this->obRCarneMata->getValorTributoReal(),2,',','.') );
        $this->obRCarneMata->setAreaTerreno       ( number_format($this->obRCarneMata->getAreaTerreno(),2,',','.') );
        $this->obRCarneMata->setAreaEdificada     ( number_format($this->obRCarneMata->getAreaEdificada(),2,',','.') );

        $this->inHorizontal = 3;
        $this->inVertical = 3;

        $this->obBarra = new RCodigoBarraFichaCompensacaoCaixa;
        $this->arBarra = array();

    if ( $this->getConsolidacao() ) {

            foreach ($chave as $parcela) {

                $inParcela++;

                $this->obRCarneMata->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarneMata->setImagem("");
                $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
                $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

                // instanciar mapeamento da função de calculo de juro e multa
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
                // retorna parcela com juro e multa aplicados
                $obCalculaParcelas = new FARRCalculaParcelasReemissao;
                // retorna valores de juro e multa que foram aplicados
                $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;

                $arVencimento = explode ( '/', $this->getVencimentoConsolidacao() );
                $dtVencimento = $arVencimento[2].'-'.$arVencimento[1].'-'.$arVencimento[0];
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );

                $nuValorTotal += $arValorNormal[0];
                $nuValorNormal += $arValorNormal[1];
                $nuValorJuroNormal += $arValorNormal[3];
                $nuValorMultaNormal += $arValorNormal[2];

                //gera o DV do campo nosso número
                $stNumeracaoCarne = $rsParcela->getCampo( 'numeracao' )."-".$this->_DVmodulo11($rsParcela->getCampo( 'numeracao' ));

                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['vencimento'] = $rsParcela->getCampo( 'vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarneMata->stNumeracao = $stNumeracaoCarne;
                $this->arBarra['cod_febraban'] = $inCodFebraban;
                $this->arBarra['ag_cod_cedente'] = "076387000000058";
                $this->arBarra['convenio'] = 2523;
                $this->arBarra['tipo_moeda'] = 9;

                if ( !$obErro->ocorreu() ) {

                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_parcela"    , $rsParcela->getCampo('cod_parcela')   );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_lancamento" , $rsParcela->getCampo('cod_lancamento'));
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "nr_parcela"     , $rsParcela->getCampo('nr_parcela')    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "vencimento"     , $this->getVencimentoConsolidacao()    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "valor" , $nuValorTotal );
                    $obErro = $this->obRARRCarne->obRARRParcela->obTARRParcela->alteracao($boTransacao);

                }

            }

            if ( $inCountTemp == (count($this->arEmissao)-1) ) {
                $this->obRCarneMata->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                $this->obRCarneMata->setParcela ( "1/1" );
                $this->obRCarneMata->setVencimento  ( $this->getVencimentoConsolidacao() );
                $this->obRCarneMata->flValorJuros = ( number_format(round($nuValorJuroNormal,2),2,',',''));
                $this->obRCarneMata->flValorMulta = ( number_format(round($nuValorMultaNormal,2),2,',',''));
                $this->obRCarneMata->setValor       ( number_format(round($nuValorNormal,2),2,',',''));
                $this->obRCarneMata->setValorTotal(number_format(round($nuValorTotal,2),2,',',''));

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarneMata->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarneMata->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );
                $this->obRCarneMata->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarneMata->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarneMata->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 66;
            }

            $inCountTemp ++;

        } else {

            foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
                if (!$boInicio) {
                    $this->obRCarneMata->novaPagina();
                }
                $boInicio = false;
                $rsParcela = new RecordSet;
                $stFiltroParcelas = ' WHERE ap.cod_lancamento = '.$valor;
                $stOrdem = 'ORDER BY ap.nr_parcela desc';
                $this->obRARRCarne->obRARRParcela->obTARRParcela->recuperaInfoParcelaCarne( $rsParcela, $stFiltroParcelas, $stOrdem );

                $arInfoParcelas = array();
                $inCountParcelas = 0;
                while (!$rsParcela->eof()) {
                    if ($rsParcela->getCampo('cod_parcela') == $parcela['cod_parcela']) {
                        $arTemp = array(
                            'parcela' => $rsParcela->getCampo('info'),
                            'vencimento' => $rsParcela->getCampo('vencimento'),
                            'valor' => $rsParcela->getCampo('valor')
                        );
                        $arInfoParcelas = $arTemp;
                    }
                    if ($rsParcela->getCampo('nr_parcela') != 0) {
                        $inCountParcelas++;
                    }
                    $rsParcela->proximo();
                }

                ksort($arInfoParcelas);
                $this->obRCarneMata->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarneMata->setImagem("");
                $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
                $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

                // instanciar mapeamento da função de calculo de juro e multa
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
                // retorna parcela com juro e multa aplicados
                $obCalculaParcelas = new FARRCalculaParcelasReemissao;
                // retorna valores de juro e multa que foram aplicados
                $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;

                // data da reemissao
                $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );
                $nuValorTotal = $arValorNormal[0];
                $nuValorNormal = $arValorNormal[1];
                $stJuroNormal = $arValorNormal[3];
                $stMultaNormal = $arValorNormal[2];
                $nuValorDesconto = $arValorNormal[4];
                $stCorrecaoNormal = $arValorNormal[5];

                //gera o DV do campo nosso número
                $stNumeracaoCarne = $rsParcela->getCampo( 'numeracao' )."-".$this->_DVmodulo11($rsParcela->getCampo( 'numeracao' ));

                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['vencimento'] = (string) $rsParcela->getCampo( 'vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarneMata->stNumeracao = $stNumeracaoCarne;
                $this->arBarra['cod_febraban'] = $inCodFebraban;
                $this->arBarra['ag_cod_cedente'] = "076387000000058";
                $this->arBarra['convenio'] = 2523;
                $this->arBarra['tipo_moeda'] = 9;

                $this->obRCarneMata->setValor( number_format($nuValorNormal,2,',','.') );
                $this->obRCarneMata->setInfoParcelas($arInfoParcelas);

                $this->inHorizontal = 3;
                $this->inVertical = 3;

                $this->obRCarneMata->setImagemNotificacao( CAM_FW_TEMAS."imagens/".$stNomeImagemNotificacao );
                $this->obRCarneMata->drawNotificacao($this->inHorizontal, $this->inVertical);
                $this->obRCarneMata->posicionaVariaveisNotificacao($this->inHorizontal, $this->inVertical);
                $this->obRCarneMata->setPicoteNotificacao( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 175;

                if ( $obErro->ocorreu() ) {
                    break;
                }
                if ($diffBaixa) {
                    $this->obRCarneMata->setParcelaUnica ( true );
                    $this->obRCarneMata->lblTitulo2        = ' ';
                    $this->obRCarneMata->lblValorCotaUnica = 'VALOR TOTAL';
                    $this->obRCarneMata->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarneMata->setValor        ( number_format($nuValorNormal,2,',','.') );
                    $this->obRCarneMata->setParcela ( $rsParcela->getCampo( 'info' ) );
                } else {
                    if ( $rsParcela->getCampo( 'nr_parcela' ) == 0 ) {
                        $this->obRCarneMata->setParcelaUnica ( true );
                        $this->obRCarneMata->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                        $this->obRCarneMata->setValor        ( number_format($nuValorNormal,2,',','.') );
                        $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');
                        $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
                        $this->obRCarneMata->flValorOutros = number_format(round($stCorrecaoNormal,2),2,',','.');
                        $this->obRCarneMata->stObservacao = 'Parcela única já com desconto de 10%';
                        $this->obRCarneMata->flValorDesconto = number_format($nuValorDesconto,2,',','.');
                        $this->obRCarneMata->setTaxaColetaLixoParcela(number_format($nuColetaLixo,2,',','.'));
                        $this->obRCarneMata->setParcelaIPTU(number_format(($nuValorNormal - $nuColetaLixo),2,',','.'));
                        $this->obRCarneMata->setParcela ( 'ÚNICA' );

                        //para parcela única
                        // $this->arBarra['tipo_moeda'] = 6;
                    } else {
                        $inParcela++;
                        $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                        $this->obRCarneMata->setParcela( $rsParcela->getCampo( 'info' ));
                        $this->obRCarneMata->setParcelaUnica( false );
                        $this->obRCarneMata->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );
                        $this->obRCarneMata->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                        $this->obRCarneMata->setParcelaIPTU(number_format(($nuValorNormal - $nuColetaLixoParcela),2,',','.'));
                        $this->obRCarneMata->setTaxaColetaLixoParcela(number_format($nuColetaLixoParcela,2,',','.'));
                        $this->obRCarneMata->flValorDesconto = number_format($nuValorDesconto,2,',','.');
                        $this->obRCarneMata->stObservacao = 'Não receber após a data 31/12/'.Sessao::read('exercicio').'. Favor dirigir-se ao setor de Tributos da Prefeitura.';

                        if ($this->stLocal != "WEB") {

                            $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');

                            $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
                            $this->obRCarneMata->flValorOutros = number_format(round($stCorrecaoNormal,2),2,',','.');

                        } else {
                            $this->obRCarneMata->setValor       (number_format(round($nuValorNormal,2),2,',','.'));

                        }

                    }
                }
                $this->obRCarneMata->setValorTotal   ( number_format($nuValorTotal,2,',','.') );
                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarneMata->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarneMata->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );
                $this->obRCarneMata->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarneMata->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarneMata->setPicote( $this->inHorizontal, $this->inVertical );
            }// fim foreach parcelas
        }

        $arGruposValidos = explode(',','101,102,10120, 10121, 10122, 10123, 10124, 10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299');
        if( in_array($this->obRCarneMata->getCodDivida(),$arGruposValidos))
            $this->obRCarneMata->drawComplemento($this->inHorizontal, $this->inVertical);

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

    public function _DVmodulo11($codigo)
    {
        $len = strlen($codigo);
        $inSoma = 0;
        $inPosicao = 2;
        for ($i = $len-1; $i >= 0; $i--) {
            $inSoma += $codigo[$i] * $inPosicao;
            $inPosicao++;
            if ($inPosicao > 9)
                $inPosicao = 2;
        }

        $resto = $inSoma % 11;

        $resto = 11 - $resto;
        if ( ( $resto == 10 ) || ( $resto == 11 ) )
            return 0;
        else
            return $resto;
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

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

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebraban.class.php" );
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
        $this->DefOrientation='L';
        $this->w=$this->DefPageFormat[1];
        $this->h=$this->DefPageFormat[0];
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
    public $lblTitulo1 = 'Prefeitura Municipal de Manaquiri';
    public $lblTitulo2 = 'IPTU';

    public $lblReceita = 'Receita:';
    public $lblMatricula = 'Matricula:';
    public $lblExercicio = 'Ano:';
    public $lblParcela   = 'Parcela:';
    public $lblEmissao = 'EMISSÃO:';

    public $lblVencimento = 'Vencimento:';
    public $lblValorPrincipal = "Valor:";
    public $lblMulta = 'Multa:';
    public $lblJuros = 'Juros:';
    public $lblDesconto = 'Desconto:';
    public $lblValorTotal     = "Total:";

    public $lblNumero = 'Número:';
    public $lblCep = 'CEP:';

    public $lblAgencia = 'Bradesco Ag 3727 c/c 3757-5';

    public $lblObservasao = 'Parcela única já com desconto de 10%';

    public $lblParcelaIPTU = 'Parcela-IPTU:';
    public $lblTaxaServicos = 'Taxa de Serviços Públicos';
    public $lblColetaLixo = 'Coleta de Lixo:';
    public $lblTaxaLimpeza = 'Limpeza Pública:';
    public $lblConservVias = 'Conserv de Vias:';

    /* variaveis */
    public $ImagemCarne;
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
    public $flValorColetaLixoParcela = '0,00';
    public $flValorDesconto = '0,00';
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

    /* getters */
    public function getImagemCarne() { return $this->ImagemCarne      ; }
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
        $this->setFont( 'Arial','',9 );

        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x+5, $y, 17, 10, $stExt );
        }

        /* returna retangulo */
        //via prefeitura
        $this->Rect( $x+5, ($y+(16*$this->tamY)), 90, 19 );
        $this->Rect( $x+96, ($y+(16*$this->tamY)), 37, 44 );
        $this->Rect( $x+5, ($y+(45*$this->tamY)), 46, 17 );
        $this->Rect( $x+52, ($y+(45*$this->tamY)), 43, 17 );

        //via contribuinte
        $this->Rect( $x+139, ($y+(7*$this->tamY)), 92, 11 );
        $this->Rect( $x+232, ($y+(16*$this->tamY)), 37, 28 );
        $this->Rect( $x+139, ($y+(28*$this->tamY)), 48, 17 );
        $this->Rect( $x+188, ($y+(28*$this->tamY)), 43, 17 );

        /* linha horizontais */
        //via prefeitura
        $this->Line( $x+96, ($y+(25*$this->tamY)), (133+$x), ($y+(25*$this->tamY)) );//embaixo do vencimento
        $this->Line( $x+96, ($y+(33*$this->tamY)), (133+$x), ($y+(33*$this->tamY)) );//embaixo do valor
        $this->Line( $x+96, ($y+(41*$this->tamY)), (133+$x), ($y+(41*$this->tamY)) );//embaixo do correção
        $this->Line( $x+96, ($y+(49*$this->tamY)), (133+$x), ($y+(49*$this->tamY)) );//embaixo do juros
        $this->Line( $x+96, ($y+(57*$this->tamY)), (133+$x), ($y+(57*$this->tamY)) );//embaixo do desconto
        //via contribuinte
        $this->Line( $x+232, ($y+(21*$this->tamY)), (269+$x), ($y+(21*$this->tamY)) );//embaixo do vencimento
        $this->Line( $x+232, ($y+(26*$this->tamY)), (269+$x), ($y+(26*$this->tamY)) );//embaixo do valor
        $this->Line( $x+232, ($y+(31*$this->tamY)), (269+$x), ($y+(31*$this->tamY)) );//embaixo do correção
        $this->Line( $x+232, ($y+(36*$this->tamY)), (269+$x), ($y+(36*$this->tamY)) );//embaixo do juros
        $this->Line( $x+232, ($y+(41*$this->tamY)), (269+$x), ($y+(41*$this->tamY)) );//embaixo do desconto

        $this->setFont('Arial','B',9);
        $this->Text   ( ($x+23) , ($y+(4*$this->tamY)) , $this->lblTitulo1 );
        $this->Text   ( ($x+23) , ($y+(10*$this->tamY)) , $this->lblTitulo2 );

        $this->Text   ( ($x+139), ($y+(2*$this->tamY)) , $this->lblTitulo1 );
        $this->Text   ( ($x+139), ($y+(6*$this->tamY)) , $this->lblTitulo2 );

        //labels informações da parcela
        $this->Text   ( ($x+96) , ($y+(2*$this->tamY)) , $this->lblReceita );
        $this->Text   ( ($x+96) , ($y+(6*$this->tamY)) , $this->lblMatricula );
        $this->Text   ( ($x+96) , ($y+(10*$this->tamY)) , $this->lblExercicio );
        $this->Text   ( ($x+96) , ($y+(14*$this->tamY)) , $this->lblParcela );
        ////via do contribuinte
        $this->Text   ( ($x+232) , ($y+(2*$this->tamY)) , $this->lblReceita );
        $this->Text   ( ($x+232) , ($y+(6*$this->tamY)) , $this->lblMatricula );
        $this->Text   ( ($x+232) , ($y+(10*$this->tamY)) , $this->lblExercicio );
        $this->Text   ( ($x+232) , ($y+(14*$this->tamY)) , $this->lblParcela );

        //$this->setFont('Arial','B',7);
        $this->Text   ( ($x+62)  , ($y+(14*$this->tamY)), $this->lblEmissao    );
        $this->Text   ( ($x+197) , ($y+(6*$this->tamY)), $this->lblEmissao    );

        //via prefeitura
        $this->setFont('Arial'  ,'',9);
        $this->Text   ( ($x+97) , ($y+(22*$this->tamY)), $this->lblVencimento   );
        $this->Text   ( ($x+97) , ($y+(30*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+97) , ($y+(38*$this->tamY)), $this->lblMulta );
        $this->Text   ( ($x+97) , ($y+(46*$this->tamY)), $this->lblJuros );
        $this->Text   ( ($x+97) , ($y+(54*$this->tamY)), $this->lblDesconto );
        $this->Text   ( ($x+97) , ($y+(62*$this->tamY)), $this->lblValorTotal );

        //via contribuinte
        $this->Text   ( ($x+233) , ($y+(20*$this->tamY)), $this->lblVencimento   );
        $this->Text   ( ($x+233) , ($y+(25*$this->tamY)), $this->lblValorPrincipal   );
        $this->Text   ( ($x+233) , ($y+(30*$this->tamY)), $this->lblMulta );
        $this->Text   ( ($x+233) , ($y+(35*$this->tamY)), $this->lblJuros );
        $this->Text   ( ($x+233) , ($y+(40*$this->tamY)), $this->lblDesconto );
        $this->Text   ( ($x+233) , ($y+(45*$this->tamY)), $this->lblValorTotal );

        //endereco do contribuinte
        //via prefeitura
        $this->Text   ( ($x+71) , ($y+(28*$this->tamY)), $this->lblNumero );
        $this->Text   ( ($x+71) , ($y+(34*$this->tamY)), $this->lblCep );

        //via contribuinte
        $this->Text   ( ($x+207) , ($y+(14*$this->tamY)), $this->lblNumero );
        $this->Text   ( ($x+207) , ($y+(17*$this->tamY)), $this->lblCep );

        //informação do banco
        $this->setFont('Arial'  ,'I',8);
        //via prefeitura
        $this->Text   ( ($x+5)  , ($y+(39*$this->tamY)), $this->lblAgencia );
        //via contribuinte
        $this->Text   ( ($x+139)  , ($y+(22*$this->tamY)), $this->lblAgencia );

        // //via prefeitura
        // $this->setFont('Arial'  ,'',8);
        // $this->Text   ( ($x+6), ($y+(49*$this->tamY)), substr($this->lblObservasao, 0, 32) );
        // $this->Text   ( ($x+6), ($y+(52*$this->tamY)), substr($this->lblObservasao, 32, 64) );

        // //via contribuinte
        // $this->Text   ( ($x+140), ($y+(32*$this->tamY)), substr($this->lblObservasao, 0, 32) );
        // $this->Text   ( ($x+140), ($y+(35*$this->tamY)), substr($this->lblObservasao, 32, 64) );

        //via prefeitura
        $this->Text   ( ($x+53) , ($y+(48.5*$this->tamY)), $this->lblParcelaIPTU );
        $this->setFont('Arial'  ,'B',8);
        $this->Text   ( ($x+53) , ($y+(52*$this->tamY)), $this->lblTaxaServicos );
        $this->setFont('Arial'  ,'',8);
        $this->Text   ( ($x+53) , ($y+(55.5*$this->tamY)), $this->lblColetaLixo );
        $this->Text   ( ($x+53) , ($y+(59*$this->tamY)), $this->lblTaxaLimpeza );
        $this->Text   ( ($x+53) , ($y+(62.5*$this->tamY)), $this->lblConservVias );

        //via contribuinte
        $this->setFont('Arial'  ,'',8);
        $this->Text   ( ($x+189) , ($y+(31*$this->tamY)), $this->lblParcelaIPTU );
        $this->setFont('Arial'  ,'B',8);
        $this->Text   ( ($x+189) , ($y+(34.5*$this->tamY)), $this->lblTaxaServicos );
        $this->setFont('Arial'  ,'',8);
        $this->Text   ( ($x+189) , ($y+(38*$this->tamY)), $this->lblColetaLixo );
        $this->Text   ( ($x+189) , ($y+(41.5*$this->tamY)), $this->lblTaxaLimpeza );
        $this->Text   ( ($x+189) , ($y+(45*$this->tamY)), $this->lblConservVias );
    }

    /* posiciona variaveis no carne */
    public function posicionaVariaveis($x, $y)
    {
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+79)  , ($y+(14*$this->tamY)), date('d/m/Y')    );
        $this->Text   ( ($x+214) , ($y+(6*$this->tamY)), date('d/m/Y')    );

        //valores informações da parcela
        $this->Text   ( ($x+113) , ($y+(2*$this->tamY)), "1" );//receita do urbem
        $this->Text   ( ($x+113) , ($y+(6*$this->tamY)), $this->getInscricao() );
        $this->Text   ( ($x+113) , ($y+(10*$this->tamY)), $this->stExercicio );
        $this->Text   ( ($x+113) , ($y+(14*$this->tamY)), $this->stParcela );
        //via do contribuinte
        $this->Text   ( ($x+250) , ($y+(2*$this->tamY)), "1" );//receita do urbem
        $this->Text   ( ($x+250) , ($y+(6*$this->tamY)), $this->getInscricao() );
        $this->Text   ( ($x+250) , ($y+(10*$this->tamY)), $this->stExercicio );
        $this->Text   ( ($x+250) , ($y+(14*$this->tamY)), $this->stParcela );

        //via prefeitura
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+8), ($y+(22*$this->tamY)), $this->getNomCgm() );
        $this->setFont('Arial', '', 8 );
        $this->Text   ( ($x+8), ($y+(28*$this->tamY)), $this->getRua() );
        $this->Text   ( ($x+8), ($y+(34*$this->tamY)), $this->getNomBairro() );
        $this->Text   ( ($x+85) , ($y+(28*$this->tamY)), $this->stNumero );
        $this->Text   ( ($x+80) , ($y+(34*$this->tamY)), $this->stCep );
        //via contribuinte
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+141), ($y+(11*$this->tamY)), $this->getNomCgm() );
        $this->setFont('Arial', '', 8 );
        $this->Text   ( ($x+141), ($y+(14*$this->tamY)), $this->getRua() );
        $this->Text   ( ($x+141), ($y+(17*$this->tamY)), $this->getNomBairro() );
        $this->Text   ( ($x+221) , ($y+(14*$this->tamY)), $this->stNumero );
        $this->Text   ( ($x+216) , ($y+(17*$this->tamY)), $this->stCep );

        //informações das taxas
        //via prefeitura
        $this->Text   ( ($x+76), ($y+(48.5*$this->tamY)), $this->getParcelaIPTU() );//parcela-iptu
        $this->Text   ( ($x+76), ($y+(55.5*$this->tamY)), $this->getTaxaColetaLixoParcela() );//coleta de lixo
        $this->Text   ( ($x+76), ($y+(59*$this->tamY)), '0,00' );//limpeza pública (este valor é fixado)
        $this->Text   ( ($x+76), ($y+(62.5*$this->tamY)), '0,00' );//conservação de vias (este valor é fixado)

        //via contribuinte
        $this->Text   ( ($x+212), ($y+(31*$this->tamY)), $this->getParcelaIPTU() );//parcela-iptu
        $this->Text   ( ($x+212), ($y+(38*$this->tamY)), $this->getTaxaColetaLixoParcela() );//coleta de lixo
        $this->Text   ( ($x+212), ($y+(41.5*$this->tamY)), '0,00' );//limpeza pública (este valor é fixado)
        $this->Text   ( ($x+212), ($y+(45*$this->tamY)), '0,00' );//conservação de vias (este valor é fixado)

        //observação do carnê
        //via prefeitura
        $this->setFont('Arial'  ,'',7);
        $this->Text   ( ($x+6), ($y+(49*$this->tamY)), substr($this->stObservacao, 0, 36) );
        $this->Text   ( ($x+6), ($y+(52*$this->tamY)), substr($this->stObservacao, 36, 37) );
        $this->Text   ( ($x+6), ($y+(55*$this->tamY)), substr($this->stObservacao, 73, 36) );

        //via contribuinte
        $this->Text   ( ($x+140), ($y+(32*$this->tamY)), substr($this->stObservacao, 0, 36) );
        $this->Text   ( ($x+140), ($y+(35*$this->tamY)), substr($this->stObservacao, 36, 37) );
        $this->Text   ( ($x+140), ($y+(38*$this->tamY)), substr($this->stObservacao, 73, 36) );

        ////valores do iptu
        //via prefeitura
        $this->setFont('Arial', '', 9 );
        $this->Text   ( ($x+116) , ($y+(22*$this->tamY)), $this->dtVencimento ); // vencimento
        $this->Text   ( ($x+112) , ($y+(30*$this->tamY)), $this->flValor ); // valor
        $this->Text   ( ($x+112) , ($y+(38*$this->tamY)), $this->flValorMulta ); // multa
        $this->Text   ( ($x+112) , ($y+(46*$this->tamY)), $this->flValorJuros ); // Juros
        $this->Text   ( ($x+112) , ($y+(54*$this->tamY)), $this->flValorDesconto ); // Desconto
        $this->Text   ( ($x+112) , ($y+(62*$this->tamY)), $this->flValorTotal ); // Total

        //via contribuinte
        $this->setFont('Arial', '', 9 );
        $this->Text   ( ($x+252) , ($y+(20*$this->tamY)), $this->dtVencimento ); // vencimento
        $this->Text   ( ($x+249) , ($y+(25*$this->tamY)), $this->flValor ); // valor
        $this->Text   ( ($x+249) , ($y+(30*$this->tamY)), $this->flValorMulta ); // multa
        $this->Text   ( ($x+249) , ($y+(35*$this->tamY)), $this->flValorJuros ); // Juros
        $this->Text   ( ($x+249) , ($y+(40*$this->tamY)), $this->flValorDesconto ); // Desconto
        $this->Text   ( ($x+249) , ($y+(45*$this->tamY)), $this->flValorTotal ); // Total

        $this->Text   ( ($x+139), ($y+(50*$this->tamY)), $this->stLinhaCode );
        $this->defineCodigoBarras( ($x+139), ($y+(53*$this->tamY)), $this->stBarCode, 0.82 );
    }

    public function drawNotificacao($x, $y)
    {
        /* posiciona imagem */
        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x+4, $y+3, 17, 10, $stExt );
        }

        /* returna retangulo */
        //via prefeitura
        $this->Rect( $x+102, ($y+(3*$this->tamY)), 70, 13 );
        $this->Rect( $x+4, ($y+(18*$this->tamY)), 168, 14 );
        $this->Rect( $x+4, ($y+(34*$this->tamY)), 99, 30 );
        $this->Rect( $x+104, ($y+(34*$this->tamY)), 68, 30 );

        //via contribuinte
        $this->Rect( $x+177, ($y+(15*$this->tamY)), 93, 7 );
        $this->Rect( $x+177, ($y+(23.5*$this->tamY)), 93, 17 );
        $this->Rect( $x+177, ($y+(43*$this->tamY)), 93, 22 );

        /* linha horizontais */
        //via prefeitura
        $this->Line( $x+57, ($y+(34*$this->tamY)), (57+$x), ($y+(66*$this->tamY)) );

        //labels
        //via prefeitura
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+22) , ($y+(7*$this->tamY)) , 'Prefeitura Municipal de Manaquiri' );
        $this->Text   ( ($x+177) , ($y+(7*$this->tamY)) , 'Prefeitura Municipal de Manaquiri' );
        $this->setFont('Arial', 'B', 11 );
        $this->Text   ( ($x+22) , ($y+(14*$this->tamY)) , 'Imposto Predial e Territorial Urbano - IPTU' );
        $this->Text   ( ($x+177) , ($y+(12*$this->tamY)) , 'Imposto Predial e Territorial Urbano - IPTU' );
        $this->setFont('Arial', 'B', 12 );
        $this->Text   ( ($x+109) , ($y+(9*$this->tamY)) , 'NOTIFICAÇÃO - Ano:' );
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+180) , ($y+(20.5*$this->tamY)) , 'NOTIFICAÇÃO - Ano:' );
        $this->setFont('Arial', 'B', 11 );
        $this->Text   ( ($x+109) , ($y+(15*$this->tamY)) , 'EMISSÃO:' );
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+235) , ($y+(20.5*$this->tamY)) , 'EMISSÃO:' );

        $this->setFont('Arial', '', 9 );
        $this->Text   ( ($x+5) , ($y+(23*$this->tamY)) , 'Matrícula.....:' );
        $this->Text   ( ($x+156) , ($y+(23*$this->tamY)) , 'Nr:' );
        $this->Text   ( ($x+146) , ($y+(29*$this->tamY)) , 'CEP:' );

        $this->Text   ( ($x+245) , ($y+(35*$this->tamY)) , 'Número:' );
        $this->Text   ( ($x+245) , ($y+(40*$this->tamY)) , 'CEP:' );

        $this->Text   ( ($x+5) , ($y+(39*$this->tamY)) , 'Uso:' );
        $this->Text   ( ($x+5) , ($y+(44*$this->tamY)) , 'Tipo Const.:' );
        $this->Text   ( ($x+5) , ($y+(49*$this->tamY)) , 'Padrão Const.:' );
        $this->Text   ( ($x+5) , ($y+(54*$this->tamY)) , 'Conservação:' );
        $this->Text   ( ($x+5) , ($y+(59*$this->tamY)) , 'Terreno:' );
        $this->Text   ( ($x+5) , ($y+(64*$this->tamY)) , 'Área Const.:' );

        $this->Text   ( ($x+58) , ($y+(39*$this->tamY)) , 'Valor Venal.....R$:' );
        $this->Text   ( ($x+58) , ($y+(44*$this->tamY)) , 'Aliquota..........(%):' );
        $this->Text   ( ($x+58) , ($y+(49*$this->tamY)) , 'Imposto...........R$:' );
        $this->Text   ( ($x+58) , ($y+(54*$this->tamY)) , 'Coleta de Lixo R$:' );
        $this->Text   ( ($x+58) , ($y+(59*$this->tamY)) , 'Limp. Pública..R$:' );
        $this->Text   ( ($x+58) , ($y+(64*$this->tamY)) , 'Cons. de Vias..R$:' );

        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+108) , ($y+(38*$this->tamY)) , 'Parcela' );
        $this->Text   ( ($x+131) , ($y+(38*$this->tamY)) , 'Vencimento' );
        $this->Text   ( ($x+156) , ($y+(38*$this->tamY)) , 'Valor' );

        $this->setFont('Arial', '', 9 );
        $this->Text   ( ($x+181) , ($y+(48*$this->tamY)) , 'Data:' );
        $this->Text   ( ($x+181) , ($y+(56*$this->tamY)) , 'Assinatura:' );
        $this->Text   ( ($x+181) , ($y+(64*$this->tamY)) , 'Nome Legível:' );
    }

    public function posicionaVariaveisNotificacao($x, $y)
    {
        $this->setFont('Arial', 'B', 12 );
        $this->Text   ( ($x+155) , ($y+(9*$this->tamY)), $this->getExercicio() );
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+215) , ($y+(20.5*$this->tamY)), $this->getExercicio() );
        $this->setFont('Arial', 'B', 11 );
        $this->Text   ( ($x+138) , ($y+(15*$this->tamY)) , date('d/m/Y') );
        $this->setFont('Arial', 'B', 9 );
        $this->Text   ( ($x+253) , ($y+(20.5*$this->tamY)) , date('d/m/Y') );

        $this->Text   ( ($x+25) , ($y+(23*$this->tamY)) , $this->getInscricao() );
        $this->Text   ( ($x+5) , ($y+(29*$this->tamY)) , $this->getNomCgm() );

        $this->Text   ( ($x+178) , ($y+(29*$this->tamY)) , $this->getInscricao().' - '.$this->getNomCgm() );

        $this->setFont('Arial', '', 9 );
        $this->Text   ( ($x+87) , ($y+(23*$this->tamY)) , $this->getRua() );
        $this->Text   ( ($x+87) , ($y+(29*$this->tamY)) , $this->getNomBairro() );

        $this->Text   ( ($x+178) , ($y+(35*$this->tamY)) , $this->getRua() );
        $this->Text   ( ($x+178) , ($y+(40*$this->tamY)) , $this->getNomBairro() );

        $this->Text   ( ($x+162) , ($y+(23*$this->tamY)) , $this->getNumero() );
        $this->Text   ( ($x+155) , ($y+(29*$this->tamY)) , $this->getCep() );

        $this->Text   ( ($x+259) , ($y+(35*$this->tamY)) , $this->getNumero() );
        $this->Text   ( ($x+253) , ($y+(40*$this->tamY)) , $this->getCep() );

        $this->Text   ( ($x+28) , ($y+(39*$this->tamY)) , $this->getAtributoUso() );
        $this->Text   ( ($x+28) , ($y+(44*$this->tamY)) , $this->getAtributoTipo() );
        $this->Text   ( ($x+28) , ($y+(49*$this->tamY)) , $this->getAtributoPadrao() );
        $this->Text   ( ($x+28) , ($y+(54*$this->tamY)) , $this->getAtributoConserv() );
        $this->Text   ( ($x+28) , ($y+(59*$this->tamY)) , str_replace('.', ',', $this->getAreaTerreno()).'m²' );
        $this->Text   ( ($x+28) , ($y+(64*$this->tamY)) , str_replace('.', ',', $this->getAreaEdificada()).'m²' );

        //valores de iptu
        $this->Text   ( ($x+86) , ($y+(39*$this->tamY)) , $this->getValorTributoReal() );
        $this->Text   ( ($x+86) , ($y+(44*$this->tamY)) , $this->getAliquota() );
        $this->Text   ( ($x+86) , ($y+(49*$this->tamY)) , $this->getImpostoAnualReal() );
        $this->Text   ( ($x+86) , ($y+(54*$this->tamY)) , $this->getTaxaColetaLixo() );
        $this->Text   ( ($x+86) , ($y+(59*$this->tamY)) , '0,00' );//(Limp. Pública)manaquiri não possui este valor
        $this->Text   ( ($x+86) , ($y+(64*$this->tamY)) , '0,00' );//(Cons. de Vias)manaquiri não possui este valor

        //informações de todas as parcelas do carnê
        $intY = 45;
        foreach ($this->getInfoParcelas() as $parcela) {
            $this->Text   ( ($x+108) , ($y+($intY*$this->tamY)) , $parcela['parcela'] );
            $this->Text   ( ($x+131) , ($y+($intY*$this->tamY)) , $parcela['vencimento'] );
            $this->Text   ( ($x+156) , ($y+($intY*$this->tamY)) , str_replace('.', ',', $parcela['valor']) );

            //retirar após testes na base de mata
            if ($intY == 60) {
                break;
            }

            $intY += 5;
        }

        $this->Text   ( ($x+188) , ($y+(48*$this->tamY)) , '___/___/_____' );
        $this->Text   ( ($x+197) , ($y+(56*$this->tamY)) , '______________________________________' );
        $this->Text   ( ($x+202) , ($y+(64*$this->tamY)) , '___________________________________' );
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
        //medidas do carnê de manaquiri
        //linha inferior
        for ($i=0;$i<=271;($i+=2)) {
           $this->Line( ($x+$i), ($y+(68*$this->tamY)), ($x+$i+1), ($y+(68*$this->tamY)) );
        }

        //linha superior
        for ($i=0;$i<=271;($i+=2)) {
           $this->Line( ($x+$i), ($y-3), ($x+$i+1), ($y-3) );
        }

        //linha divisória
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x+136), ($y+($i*$this->tamY)), ($x+136), ($y+(($i+1)*$this->tamY)) );
        }

        //linha esquerda
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x), ($y+($i*$this->tamY)), ($x), ($y+(($i+1)*$this->tamY)) );
        }

        //linha direita
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x+272), ($y+($i*$this->tamY)), ($x+272), ($y+(($i+1)*$this->tamY)) );
        }
    }

    public function setPicoteNotificacao($x, $y, $firstPage = false)
    {
        //linha inferior
        for ($i=0;$i<=271;($i+=2)) {
           $this->Line( ($x+$i), ($y+(68*$this->tamY)), ($x+$i+1), ($y+(68*$this->tamY)) );
        }

        //linha superior
        for ($i=0;$i<=271;($i+=2)) {
           $this->Line( ($x+$i), ($y-3), ($x+$i+1), ($y-3) );
        }

        //linha divisória
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x+174), ($y+($i*$this->tamY)), ($x+174), ($y+(($i+1)*$this->tamY)) );
        }

        //linha esquerda
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x), ($y+($i*$this->tamY)), ($x), ($y+(($i+1)*$this->tamY)) );
        }

        //linha direita
        for (($i=-3);$i<=67;($i+=2)) {
            $this->Line( ($x+272), ($y+($i*$this->tamY)), ($x+272), ($y+(($i+1)*$this->tamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneIPTUManaquiri
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
function RCarneIPTUManaquiri($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    ;

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
        $this->obRCarneMata->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarneManaquiri( $rsGeraCarneCabecalho );
        $flImpostoAnualReal = 0.00;
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarneMata->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {
            /* montagem cabecalho (protocolo) */
            $this->obRCarneMata->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarneMata->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarneMata->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarneMata->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarneMata->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );

            $this->obRCarneMata->setRua               ( str_replace ( "Não Informado ", "", $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) )  );
            $this->obRCarneMata->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
            $this->obRCarneMata->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
            $this->obRCarneMata->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
            $this->obRCarneMata->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );
            $this->obRCarneMata->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ), 6, '0', STR_PAD_LEFT) );
            $this->obRCarneMata->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarneMata->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarneMata->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarneMata->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarneMata->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarneMata->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarneMata->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
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
            $this->obRCarneMata->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarneMata->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            $this->obRCarneMata->setAliquota          ( $rsGeraCarneCabecalho->getCampo( 'aliquota' )               );
            if ( preg_match( '/COLETA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarneMata->setTaxaColetaLixo  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } elseif ( preg_match( '/IMPOSTO.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $flImpostoAnualReal += $rsGeraCarneCabecalho->getCampo( 'valor' );
            }

            //busca os atributos dinamicos para o carne de manaquiri
            include_once (CAM_GT_ARR_FUNCAO.'FRecuperaAtributoCarneManaquiri.class.php');
            $rsAtributosMata = new RecordSet();

            $obFRecuperaAtributoCarneManaquiri = new FRecuperaAtributoCarneManaquiri();
            $obFRecuperaAtributoCarneManaquiri->executaFuncao($rsAtributosMata, $rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ));
            $this->obRCarneMata->setAtributoUso($rsAtributosMata->getCampo('uso'));
            $this->obRCarneMata->setAtributoConserv($rsAtributosMata->getCampo('conservacao'));
            $this->obRCarneMata->setAtributoTipo($rsAtributosMata->getCampo('tipo'));
            $this->obRCarneMata->setAtributoPadrao($rsAtributosMata->getCampo('padrao'));

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

        //informações das parcelas que são demonstradas na notificação
        $stFiltroParcelas = ' WHERE ap.cod_lancamento = '.$valor;
        $stOrdem = 'ORDER BY ap.nr_parcela desc';
        $this->obRARRCarne->obRARRParcela->obTARRParcela->recuperaInfoParcelaCarne( $rsParcela, $stFiltroParcelas, $stOrdem );

        $arInfoParcelas = array();
        $inCountParcelas = 0;
        while (!$rsParcela->eof()) {
            $arTemp = array(
                'parcela' => $rsParcela->getCampo('info'),
                'vencimento' => $rsParcela->getCampo('vencimento'),
                'valor' => $rsParcela->getCampo('valor')
            );
            $arInfoParcelas[$rsParcela->getCampo('nr_parcela')] = $arTemp;

            if ($rsParcela->getCampo('nr_parcela') != 0) {
                $inCountParcelas++;
            }
            $rsParcela->proximo();
        }

        ksort($arInfoParcelas);
        $this->obRCarneMata->setInfoParcelas($arInfoParcelas);
        $nuColetaLixoParcela = $nuColetaLixo/$inCountParcelas;

        $inParcela = 0;
        $inCount = 1;//devido à notificação inicia em 1

        $this->inHorizontal = 10;
        $this->inVertical = 10;

        $this->obRCarneMata->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRCarneMata->drawNotificacao($this->inHorizontal, $this->inVertical);
        $this->obRCarneMata->posicionaVariaveisNotificacao($this->inHorizontal, $this->inVertical);
        $this->obRCarneMata->setPicoteNotificacao( $this->inHorizontal, $this->inVertical );
        $this->inVertical += 66;

        $this->obBarra = new RCodigoBarraFebraban;
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
                #$obCalculaParcelas->debug();

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );

                $nuValorTotal += $arValorNormal[0];
                $nuValorNormal += $arValorNormal[1];
                $nuValorJuroNormal += $arValorNormal[3];
                $nuValorMultaNormal += $arValorNormal[2];

                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['cod_febraban'] = $inCodFebraban;
                $this->arBarra['convenio'] = 2523;
                $this->arBarra['tipo_moeda'] = 6;

                if ( !$obErro->ocorreu() ) {

                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_parcela"    , $rsParcela->getCampo('cod_parcela')   );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "cod_lancamento" , $rsParcela->getCampo('cod_lancamento'));
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "nr_parcela"     , $rsParcela->getCampo('nr_parcela')    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "vencimento"     , $this->getVencimentoConsolidacao()    );
                    $this->obRARRCarne->obRARRParcela->obTARRParcela->setDado ( "valor" , $nuValorTotal );
                    $obErro = $this->obRARRCarne->obRARRParcela->obTARRParcela->alteracao($boTransacao);

                    #$this->obRARRCarne->obRARRParcela->obTARRParcela->debug();
                    #exit;
                }

                /*$nuValorTotal = $nuValorTotal;
                $nuValorNormal += $nuValorNormal;
                $nuValorJuroNormal += $nuValorJuroNormal;
                $nuValorMultaNormal += $nuValorMultaNormal;
                */

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

                if ( ( $inParcela == 3 ) || ( $inCount == 3 ) ) {
                    $this->obRCarneMata->novaPagina();
                    $inCount = 1;
                    $this->inVertical = 10;
                    $this->boPulaPagina = true;
                } else {
                    $this->boPulaPagina = true;
                }
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

                $this->arBarra['valor_documento'] = $nuValorNormal;
                //$this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['vencimento'] = (string) $rsParcela->getCampo( 'vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['cod_febraban'] = $inCodFebraban;
                $this->arBarra['convenio'] = 2523;
                //$this->arBarra['tipo_moeda'] = 6;

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
                        $this->arBarra['tipo_moeda'] = 6;
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

                        //para parcelado
                        $this->arBarra['tipo_moeda'] = 7;

                        if ($this->stLocal != "WEB") {
                            //$stMes = $arTmp[1];
                            //$arTmp = explode('/',$arVencimentos[0]);
                            //if ($arTmp[1] >= $stMes) {
                            //    $stMes = $arTmp[1];
                            //    $boVenc1 = true;
                            //    $this->obRCarneMata->setVencimento1 ( $arVencimentos[0] );
                            //    $arTmp = explode('/',$arVencimentos[1]);
                            //    if ($arTmp[1] >= $stMes) {
                            //        $stMes = $arTmp[1];
                            //        $boVenc2 = true;
                            //        $this->obRCarneMata->setVencimento2 ( $arVencimentos[1] );
                            //        $arTmp = explode('/',$arVencimentos[2]);
                            //        if ($arTmp[1] >= $stMes) {
                            //            $boVenc3 = true;
                            //            $this->obRCarneMata->setVencimento3 ( $arVencimentos[2] );
                            //        }
                            //    }
                            //}
                            //// converter vencimentos para formato americano
                            //
                            //$arTmp = explode('/',$arVencimentos[0]);
                            //$dtVencimento1 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];
                            //
                            //$arTmp = explode('/',$arVencimentos[1]);
                            //$dtVencimento2 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];
                            //
                            //$arTmp = explode('/',$arVencimentos[2]);
                            //$dtVencimento3 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];
                            //
                            //$stParametro2 = $stParametro.$dtVencimento1."'";
                            //$stParametro3 = $stParametro.$dtVencimento2."'";
                            //$stParametro4 = $stParametro.$dtVencimento3."'";

                            // valor, % de juro, % de multa para valor normal do carne --------------
                            // valor
                            // % de juro

                            $this->obRCarneMata->flValorJuros = number_format($stJuroNormal,2,',','.');

                            // % de multa
                            $this->obRCarneMata->flValorMulta = number_format($stMultaNormal,2,',','.');
                            $this->obRCarneMata->flValorOutros = number_format(round($stCorrecaoNormal,2),2,',','.');
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 1 do carne --------------
                            // valor
                            //if ($boVenc1 == true) {
                            //    $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro2);
                            //    $nuValor1 = $rsTmp->getCampo('valor');
                            //    // % de juro
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'j'");
                            //    $stJuro1 = $rsTmp->getCampo('valor');
                            //    $this->obRCarneMata->lblJuros2 = $stJuro1;
                            //    // % de multa
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'m'");
                            //
                            //    $stMulta1 = $rsTmp->getCampo('valor');
                            //    $this->obRCarneMata->lblMulta2 = $stMulta1;
                            //} else {
                            //    $this->obRCarneMata->lblJuros2 = "";
                            //    $this->obRCarneMata->lblMulta2 = "";
                            //}
                            ////-----------------------------------------------------------------------
                            //
                            //// valor, % de juro, % de multa para valor vencimento 2 do carne --------------
                            //// valor
                            //if ($boVenc2 == true) {
                            //    $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro3);
                            //    $nuValor2 = $rsTmp1->getCampo('valor');
                            //    // % de juro
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro3.",'j'");
                            //
                            //    $stJuro2 = $rsTmp2->getCampo('valor');
                            //
                            //    $this->obRCarneMata->lblJuros3 = $stJuro2;
                            //
                            //    // % de multa
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro3.",'m'");
                            //    $stMulta2 = $rsTmp3->getCampo('valor');
                            //    $this->obRCarneMata->lblMulta3 = $stMulta2;
                            //} else {
                            //    $this->obRCarneMata->lblJuros3 = "";
                            //    $this->obRCarneMata->lblMulta3 = "";
                            //}
                            ////-----------------------------------------------------------------------
                            //
                            //// valor, % de juro, % de multa para valor vencimento 3 do carne --------------
                            //// valor
                            //if ($boVenc3 == true) {
                            //    $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro4);
                            //    $nuValor3 = $rsTmp1->getCampo('valor');
                            //    // % de juro
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro4.",'j'");
                            //
                            //    $stJuro3 = $rsTmp2->getCampo('valor');
                            //    $this->obRCarneMata->lblJuros4 = $stJuro3;
                            //    // % de multa
                            //    $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro4.",'m'");
                            //    $stMulta3 = $rsTmp3->getCampo('valor');
                            //
                            //    $this->obRCarneMata->lblMulta4 = $stMulta3;
                            //} else {
                            //    $this->obRCarneMata->lblJuros4 = "";
                            //    $this->obRCarneMata->lblMulta4 = "";
                            //}
                            ////-----------------------------------------------------------------------
                            //
                            //
                            //// repassa valores para pdf
                            //$this->obRCarneMata->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                            //if ($boVenc1 == true) {
                            //    $this->obRCarneMata->setValor1      (number_format(round($nuValor1,2),2,',','.')) ;
                            //    if ($boVenc2 == true) {
                            //        $this->obRCarneMata->setValor2      (number_format(round($nuValor2,2),2,',','.')) ;
                            //        if ($boVenc3 == true) {
                            //            $this->obRCarneMata->setValor3      (number_format(round($nuValor3,2),2,',','.')) ;
                            //        }
                            //    }
                            //}
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
                $this->inVertical += 66;
                $inCount++;
            }// fim foreach parcelas
        }

        if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
            $this->obRCarneMata->novaPagina();
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

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
  * Carnê Divida Refis para Mata de Sao Joao
  * Data de criação : 09/01/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM

  Caso de uso: uc-05.03.11
  Caso de uso: uc-05.04.03
*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebrabanCompensacaoBB-Anexo5.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
include_once ( CAM_GT_DAT_MAPEAMENTO."TDATDividaAtiva.class.php" );
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
    public $lblTributo      = 'TRIBUTO DÍVIDA ATIVA';
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
    public $inInscricaoDivida;
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

        /* linhas horizontais menores */
        $this->Line( 115, 33.65+$this->inTamY, 203, 33.65+$this->inTamY  );
        $this->Line( 115, 40.3+$this->inTamY , 203, 40.3+$this->inTamY   );
        $this->Line( 115, 54.5+$this->inTamY, 203, 54.5+$this->inTamY );
        $this->Line( 115, 80+$this->inTamY, 203, 80+$this->inTamY );

        /* linhas verticais */
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
    /* labels */
    public $lblTitulo1 = 'MATA DE SÃO JOÃO - Sec. de Adm. e Fin.';
    public $lblTitulo2 = 'IPTU';
    public $lblExercicio = 'EXERCÍCIO';
    public $lblInscricao = 'INSCRIÇÃO';
    public $lblInscricaoDivida = 'INSC. DIV.';
    public $lblCodDivida = 'CÓD. DÍVIDA';
    public $lblTributo   = 'TRIBUTO DÍVIDA ATIVA';
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
    public $lblMultaI = '(+) MULTA DE INFRAÇÃO';
    public $lblJuros = '(+) JUROS DE MORA';
    public $lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

    public $lblValorParcela = 'VALOR PARCELA';
    public $lblReal = '(REAL)';
    public $lblNumeracao = 'NOSSO NÚMERO';
    public $lblNumeroAcordo = 'COBRANÇA';

    public $lblValorPrincipal = "(=) VALOR PRINCIPAL";
    public $lblValorTotal     = "(=) TOTAL";

    /* variaveis */
    public $ImagemCarne;
    public $stExercicio;
    public $inInscricao;
    public $inAcordo;
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
    public $flValorMultaJuros = '0,00';
    public $flValorOutros = '0,00';
    public $flValorTotal = '0,00';
    public $stCarteira = '000/000';
    public $stEspecieDoc = 'OU(Outros)';
    public $stEspecie = 'REAL';
    public $stAceite = 'N';
    public $stDataProcessamento = '01/10/2009';
    public $stAgenciaCodCedente = '001-1/001';
    public $stLocalPagamento = 'Pagável em qualquer banco até o vencimento';
    public $stCedente = 'Prefeitura Municipal de Mata de São João';
    public $stDataDocumento = '01/10/2009';
    public $stQuantidade = "200";
    public $tamY = 0.93;

    /* setters */
    public function setImagemCarne($valor) { $this->ImagemCarne      = $valor; }
    public function setExercicio($valor) { $this->stExercicio      = $valor; }
    public function setInAcordo($valor) { $this->inAcordo         = $valor; }
    public function setInscricaoDivida($valor) { $this->inInscricaoDivida= $valor; }
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

        $inAlteracaoMata = 48;

        $stBB = "../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/bb.jpg";
        $stExt = substr( $stBB, strlen($stBB)-3, strlen($stBB) );
        $this->Image( $stBB, $x+28+$inAlteracaoMata, $y+(6*$this->tamY)+0.5, 35, 4, $stExt );

        if ($this->ImagemCarne) {
            $stExt = substr( $this->ImagemCarne, strlen($this->ImagemCarne)-3, strlen($this->ImagemCarne) );
            $this->Image( $this->ImagemCarne, $x, $y+6, 16, 9, $stExt );
        }
        $this->setFillColor( 240 );
        $this->Rect( $x, $y+(41*$this->tamY), 48, 5*$this->tamY, 'DF' );
        // linhas horizontais
        $this->Line( $x+27+$inAlteracaoMata, ($y+(11*$this->tamY)), (197+$x), ($y+(11*$this->tamY)) );

        $this->Line( $x, ($y+(17*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(17*$this->tamY)) );
        $this->Line( $x+27+$inAlteracaoMata, ($y+(17*$this->tamY)), (197+$x), ($y+(17*$this->tamY)) );

        $this->Line( $x, ($y+(23*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(23*$this->tamY)) );
        $this->Line( $x+27+$inAlteracaoMata, ($y+(23*$this->tamY)), (197+$x), ($y+(23*$this->tamY)) );

        $this->Line( $x, ($y+(29*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(29*$this->tamY)) );
        $this->Line( $x+27+$inAlteracaoMata, ($y+(29*$this->tamY)), (197+$x), ($y+(29*$this->tamY)) );

        $this->Line( $x+48, ($y+(35*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(35*$this->tamY)) );
        $this->Line( $x+27+$inAlteracaoMata, ($y+(35*$this->tamY)), (197+$x), ($y+(35*$this->tamY)) );

        $this->Line( $x, ($y+(41*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(41*$this->tamY)) );
        $this->Line( $x+158, ($y+(41*$this->tamY)), (197+$x), ($y+(41*$this->tamY)) );

        $this->Line( $x, ($y+(46*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(46*$this->tamY)) );
        $this->Line( $x+158, ($y+(46*$this->tamY)), (197+$x), ($y+(46*$this->tamY)) );

        $this->Line( $x, ($y+(51*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(51*$this->tamY)) );
        $this->Line( $x+158, ($y+(51*$this->tamY)), (197+$x), ($y+(51*$this->tamY)) );

        $this->Line( $x, ($y+(56*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(56*$this->tamY)) );
        $this->Line( $x+158, ($y+(57*$this->tamY)), (197+$x), ($y+(57*$this->tamY)) );

        $this->Line( $x, ($y+(61*$this->tamY)), (25+$x)+$inAlteracaoMata, ($y+(61*$this->tamY)) );
        $this->Line( $x+27+$inAlteracaoMata, ($y+(63*$this->tamY)), (197+$x), ($y+(63*$this->tamY)) );

        $this->Line( $x+27+$inAlteracaoMata, ($y+(75*$this->tamY)), (197+$x), ($y+(75*$this->tamY)) );

        $this->Line( $x+128, ($y+(69*$this->tamY)), (197+$x), ($y+(69*$this->tamY)) );

        // linhas verticais
        $this->Line( $x+48, $y+(17*$this->tamY), $x+48, $y+(46*$this->tamY) ); //nova do canhotinho
        $this->Line( $x+48, $y+(56*$this->tamY), $x+48, $y+(61*$this->tamY) ); //nova inscricao

        $this->Line( $x+66+$inAlteracaoMata, $y+(6*$this->tamY), 66+$x+$inAlteracaoMata, $y+(11*$this->tamY) );

        $this->Line( $x+42+$inAlteracaoMata, $y+(23*$this->tamY), 42+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+65+$inAlteracaoMata, $y+(23*$this->tamY), 65+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+80+$inAlteracaoMata, $y+(23*$this->tamY), 80+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+94+$inAlteracaoMata, $y+(23*$this->tamY), 94+$x+$inAlteracaoMata, $y+(35*$this->tamY) );

        $this->Line( $x+80+$inAlteracaoMata, $y+(63*$this->tamY), 80+$x+$inAlteracaoMata, $y+(75*$this->tamY) );

            //linhas verticalnosso numero parcela
        $this->Line( $x+24, $y+(17*$this->tamY), 24+$x, $y+(29*$this->tamY) );

        $this->Line( $x+158, $y+(11*$this->tamY), 158+$x, $y+(75*$this->tamY) );

        $this->setFont ( 'Arial', 'B', 7 );
        $this->Text ( $x+38, $y+(11*$this->tamY), "MATA DE SÃO JOÃO" );
        $this->Text ( $x+38, $y+(13*$this->tamY)+0.5, "Sec. de Adm. e Fin." );
        $this->setFont ( 'Arial', 'B', 6 );
        $this->Text ( $x+38, $y+(16*$this->tamY), "DÍVIDA ATIVA EXERCÍCIO " . Sessao::getExercicio() );

        $this->setFont ( 'Arial', '', 6 );
        $this->Text ( $x, $y+(19*$this->tamY), "Nosso Número" );
        $this->Text ( $x, $y+(22*$this->tamY), $this->stNumeracao );

        $this->Text ( $x+24.5, $y+(19*$this->tamY), "Parcela" );
        $this->Text ( $x+24.5, $y+(22*$this->tamY), $this->stParcela );

        $this->Text ( $x, $y+(25*$this->tamY), "Insc. Div." );
        $this->Text ( $x, $y+(28*$this->tamY), $this->inInscricaoDivida );

        $this->Text ( $x+24.5, $y+(25*$this->tamY), "Cobrança" );
        $this->Text ( $x+24.5, $y+(28*$this->tamY), $this->stNroCobrancaDA );

        $this->Text ( $x, $y+(43*$this->tamY), "Vencimento" );
        $stVnc = str_replace( "\n\r", " ", $this->stObsVencimento );
        $this->setFont ( 'Arial', '', 5 );
        $this->Text ( $x, $y+(45*$this->tamY), $stVnc );
        $this->setFont ( 'Arial', '', 6 );

        $this->Text ( $x+30, $y+(43*$this->tamY), $this->dtVencimento );

        $this->setFont ( 'Arial', '', 5 );
        $this->Text ( $x, $y+(48*$this->tamY), "Multa de Mora 10% após o vencimento" );
        $this->Text ( $x, $y+(50*$this->tamY), "e Juros de Mora 1% = 0,033% ao dia" );

        $this->setFont ( 'Arial', '', 4 );
        $this->Text ( $x, $y+(31*$this->tamY), "Tributo" );
        $stTrb = str_replace( "\n\r", " ", $this->stTributo );
        $this->Text ( $x, $y+(33*$this->tamY), substr( $stTrb, 0, 75 ) );
        $this->Text ( $x, $y+(35*$this->tamY), substr( $stTrb, 75, 75 ) );
        $this->Text ( $x, $y+(37*$this->tamY), substr( $stTrb, 150, 75 ) );
        $this->Text ( $x, $y+(39*$this->tamY), substr( $stTrb, 225, 75 ) );
        $this->Text ( $x, $y+(41*$this->tamY), substr( $stTrb, 300, 75 ) );

        $this->setFont ( 'Arial', '', 6 );

        $this->Text ( $x+48.5, $y+(19*$this->tamY), "(=) Valor Principal" );
        $this->Text ( $x+48.5, $y+(22*$this->tamY), $this->flValor );

        $this->Text ( $x+48.5, $y+(25*$this->tamY), "(+) Multa de Mora" );
        $this->Text ( $x+48.5, $y+(28*$this->tamY), $this->flValorMulta );

        $this->Text ( $x+48.5, $y+(31*$this->tamY), "(+) Juros de Mora" );
        $this->Text ( $x+48.5, $y+(34*$this->tamY), $this->flValorJuros );

        $this->Text ( $x+48.5, $y+(37*$this->tamY), "(+) At. Monet." );
        $this->Text ( $x+48.5, $y+(40*$this->tamY), $this->flValorOutros );

        $this->Text ( $x+48.5, $y+(43*$this->tamY), "(=) Total" );
        $this->Text ( $x+48.5, $y+(45*$this->tamY)+0.5, $this->flValorTotal );

        $this->Text ( $x, $y+(63*$this->tamY), "Observação" );
        $this->Text ( $x+38, $y+(63*$this->tamY), "Via Contribuinte" );

        $stObs = str_replace( "\n\r", " ", $this->stObservacao );
        for ($inY = 65,$inObs = 0;$inY<79;$inY += 2, $inObs +=65) {
            $this->Text ( $x, $y+($inY*$this->tamY), substr( $stObs,   $inObs, 65 ) );
        }

        $this->Text ( $x, $y+(53*$this->tamY), "Contribuinte" );
        $this->Text ( $x, $y+(55*$this->tamY), substr( $this->getNomCgm(), 0, 70 ) );

        $this->Text ( $x, $y+(58*$this->tamY), "Endereço do Imóvel" );
        $this->Text ( $x, $y+(60*$this->tamY), substr( $this->getRua(), 0, 48 ) );

        $this->Text ( $x+48.5, $y+(58*$this->tamY), "Inscrição" );
        $this->Text ( $x+48.5, $y+(60*$this->tamY), $this->inInscricao );

        $this->setFont ( 'Arial', '', 6 );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(13*$this->tamY), "Local de Pagamento" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(16*$this->tamY), $this->stLocalPagamento );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(19*$this->tamY), "Cedente" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(22*$this->tamY), $this->stCedente );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(25*$this->tamY), "Data do Doc." );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(28*$this->tamY), $this->stDataDocumento );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(31*$this->tamY), "Uso do Banco" );

        $stObs = str_replace( "\n\r", " ", str_pad($this->stObsVencimento,70,' ').$this->stObservacao );
        $stObs = str_pad( $stObs,((int) (strlen($stObs)/70)+1)*70,' ');
        $stTrbInstrucoes = str_pad('Tributo',70,' ');
        $stTrbInstrucoes = str_pad($stTrbInstrucoes,70,' ',STR_PAD_RIGHT);
        $stTrb = str_replace(' ','',$stTrb);
        $arTrb = explode(',',$stTrb);

        $inContTamanho = 1;
        foreach ($arTrb AS $stTributoExerc) {
            if (strlen($stTrbInstrucoes) + strlen($stTributoExerc) > $inContTamanho * 70 ) {
                //ajuste para montar sem quebras no boleto
                $stTrbInstrucoes = str_pad($stTrbInstrucoes,$inContTamanho * 70,' ');
                $inContTamanho++;
            }
            $stTrbInstrucoes .= $stTributoExerc.", ";
        }
        $stTrbInstrucoes = substr($stTrbInstrucoes, 0, -2);

        $stObs.=$stTrbInstrucoes;

        $this->Text ( $x+27+$inAlteracaoMata, $y+(37*$this->tamY), "Instruções" );
        for ($inY = 40,$inObs = 0;$inY<58;$inY += 2, $inObs +=70) {
            $this->Text ( $x+27+$inAlteracaoMata, $y+($inY*$this->tamY), substr( $stObs,   $inObs, 70 ) );
        }

        $this->Text ( $x+27+$inAlteracaoMata, $y+(65*$this->tamY), "Sacado" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(67*$this->tamY), substr( $this->getNomCgm(), 0, 120 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(69*$this->tamY), substr($this->getRua(), 0, 53) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(71*$this->tamY), substr($this->getRua(), 53, 53) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(73*$this->tamY), substr($this->getRua(), 106, 53) );

        $this->Text ( $x+128.5, $y+(65*$this->tamY), "Inscrição" );
        $this->Text ( $x+128.5, $y+(67*$this->tamY), $this->inInscricao );

        $this->Text ( $x+128.5, $y+(71*$this->tamY), "Nº Acordo" );
        $this->Text ( $x+128.5, $y+(73*$this->tamY), $this->stNroCobrancaDA );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(74*$this->tamY)+0.5, "Sacador/Avalista" );

        $this->Text ( $x+42.5+$inAlteracaoMata, $y+(25*$this->tamY), "Nosso Número" );
        $this->Text ( $x+42.5+$inAlteracaoMata, $y+(28*$this->tamY), $this->stNumeracao );

        $this->Text ( $x+42.5+$inAlteracaoMata, $y+(31*$this->tamY), "Carteira" );
        $this->Text ( $x+42.5+$inAlteracaoMata, $y+(34*$this->tamY), $this->stCarteira );

        $this->Text ( $x+65.5+$inAlteracaoMata, $y+(25*$this->tamY), "Espécie DOC" );
        $this->Text ( $x+65.5+$inAlteracaoMata, $y+(28*$this->tamY), $this->stEspecieDoc );

        $this->Text ( $x+65.5+$inAlteracaoMata, $y+(31*$this->tamY), "Espécie" );
        $this->Text ( $x+65.5+$inAlteracaoMata, $y+(34*$this->tamY), $this->stEspecie );

        $this->Text ( $x+80.5+$inAlteracaoMata, $y+(25*$this->tamY), "Aceite" );
        $this->Text ( $x+80.5+$inAlteracaoMata, $y+(28*$this->tamY), $this->stAceite );

        $this->Text ( $x+80.5+$inAlteracaoMata, $y+(31*$this->tamY), "Quantidade" );
        $this->Text ( $x+80.5+$inAlteracaoMata, $y+(34*$this->tamY), $this->stQuantidade );

        $this->Text ( $x+94.5+$inAlteracaoMata, $y+(25*$this->tamY), "Data de Proc." );
        $this->Text ( $x+94.5+$inAlteracaoMata, $y+(28*$this->tamY), $this->stDataProcessamento );

        $this->Text ( $x+94.5+$inAlteracaoMata, $y+(31*$this->tamY), "Valor" );

        $this->Text ( $x+158.5, $y+(13*$this->tamY), "Vencimento" );
        $this->Text ( $x+158.5, $y+(16*$this->tamY), $this->dtVencimento );

        $this->Text ( $x+158.5, $y+(19*$this->tamY), "Agência/Código do Cedente" );
        $this->Text ( $x+158.5, $y+(22*$this->tamY), $this->stAgenciaCodCedente );

        $this->Text ( $x+158.5, $y+(25*$this->tamY), "Nosso Número" );
        $this->Text ( $x+158.5, $y+(28*$this->tamY), $this->stNumeracao );

        $this->Text ( $x+158.5, $y+(31*$this->tamY), "(=) Valor do Documento" );
        $this->Text ( $x+158.5, $y+(34*$this->tamY), $this->flValor );

        $this->Text ( $x+158.5, $y+(37*$this->tamY), "(-) Desconto" );
        $this->Text ( $x+158.5, $y+(43*$this->tamY), "(-) Outras Deduções/Abatimento" );

        $this->Text ( $x+158.5, $y+(48*$this->tamY), "(+) Mora/Multa/Juros" );
        $this->Text ( $x+158.5, $y+(50*$this->tamY)+0.5, $this->flValorMultaJuros );

        $this->Text ( $x+158.5, $y+(53*$this->tamY), "(+) Outros Acréscimos" );
        $this->Text ( $x+158.5, $y+(56*$this->tamY), $this->flValorOutros );

        $this->Text ( $x+158.5, $y+(59*$this->tamY), "(=) Valor Cobrado" );
        $this->Text ( $x+158.5, $y+(62*$this->tamY), $this->flValorTotal );

        $this->Text ( $x+85.5+$inAlteracaoMata, $y+(79*$this->tamY), "Autenticação Mecânica - Ficha de Compensação . . . . . . . . . . . . . . ." );

        $this->setFont ( 'Arial', 'B', 7 );
        $this->Text ( $x+68+$inAlteracaoMata, $y+(10*$this->tamY), "|001-9|" );

        $this->Text ( $x+78+$inAlteracaoMata, $y+(10*$this->tamY), $this->stLinhaCode );
        $this->defineCodigoBarras( $x+31+$inAlteracaoMata, $y+(83*$this->tamY), $this->stBarCode );
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
           $this->Line( ($x+$i), ($y+(102*$this->tamY)), ($x+$i+1), ($y+(102*$this->tamY)) );
        }

        for (($i=-3);$i<=106;($i+=2)) {
            $this->Line( ($x+74), ($y+($i*$this->tamY)), ($x+74), ($y+(($i+1)*$this->tamY)) );
        }
    }

    /* mostra o pdf */
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneDadosCadastraisMataSaoJoao extends RCarneDiversosPetropolis
{
    public $stComposicaoCalculo;
    public $stNomePrefeitura;
    public $stSubTitulo;
    public $stExercicio;
    public $stContribuinte;
    public $stInscricaoImobiliaria;
    public $stInscricaoEconomica;
    public $stImpostoTaxa;
    public $stCodigoLogradouro;
    public $stNomeLogradouro;
    public $stComplemento;
    public $stQuadra;
    public $stLote;
    public $stDistrito;
    public $stRegiao;
    public $stCep;
    public $stCidade;
    public $stEstado;
    public $stNroCobrancaDA;
    public $stDataAcordo;
    public $stCondominio;
    public $stReducao;
    public $arDemonstrativoParcelas;
    public $arDetalhamentoInscricao;
    public $arVencimentosDemonstrativos;
    public $stCamLogo;
    public $stObservacoesComplementares;

    public function RCarneDadosCadastraisMataSaoJoao()
    {
        ;

        parent::RCarneDiversosPetropolis();
        /**
         * Seta Informações Basicas da Prefeitura
         */
        $this->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->stSubTitulo = 'Secretaria de Administração e Finanças';
        $this->lblMulta = '(+) MULTA DE MORA';
        $this->lblJuros = '(+) JUROS DE MORA';
        $this->lblOutros = '(+) ATUALIZAÇÃO MONETÁRIA';

        /**
         * Seta Configuração do PDF
         */
        $this->open();
        $this->setTextColor(0);
        $this->addPage();
        $this->setLeftMargin(0);
        $this->setTopMargin(0);
        $this->SetLineWidth(0.01);
        include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
        $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
        $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
        $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );

        $stNomeImagem = $rsListaImagens->getCampo("valor");
        $this->stCamLogo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/'.$stNomeImagem;
    }

    public function desenhaCarne($x , $y)
    {
$stComposição =
"                                                                        O B S E R V A Ç Õ E S   I M P O R T A N T E S\n

01) Após a quitação deste carnê o contribuinte ou o seu procurador (legalmente constituído) deverá comparecer ao setor para dar baixa no processo de parcelamento;\n

02) Fica ressalvado o direito de a Secretaria Municipal de Administração e Finanças cobrar dívidas que forem apuradas de responsabilidade do Contribuinte identificado;\n

03) O atraso no pagamento de 02 (duas) prestações anula o parcelamento inicial e obriga a inscrição do débito em dívida ativa ou, se nela já se encontra inscrito, sua remessa imediata à cobrança judicial;\n

04) Para os débitos em execução fiscal, procurar o cartório cível para pagamento das custas cartorárias;\n

05) Mantenha seus dados atualizados no cadastro fiscal do município (endereço para correspondência, Telefone de contato, e-mail, entre outros);\n

06) A Coordenadoria Fazendária do Município é o órgão controlador do Setor de Registro e Cobrança da Dívida Ativa e do Setor de Tributos e Fiscalização, vinculado à Secretaria de Administração e Finanças - SECAF (Prefeitura Municipal de Mata de São João/BA);\n

07) Quaisquer dúvidas favor entrar em contato com o Setor de Registro e Cobrança da Dívida Ativa (órgão vinculado ao Setor de Tributos e Fiscalização Fazendária), na Rua Antônio Luiz Garcez, s/n, Centro - Centro Administrativo - Mata de São João/Ba. Tel/Fax: (71) 3635-1669 (71) 9617-723  ou e-mail: dividaativa.tributacao@pmsj.ba.gov.br. Acesse o nosso site e fique por dentro de todos os acontecimentos do município no endereço http://www.pmsj.ba.gov.br.\n

                                             P A G Á V E L    S O M E N T E    N A S   A G Ê N C I A S    B A N C O   D O   B R A S I L\n";

        $inTamY = 1.15;

        $this->stComposicaoCalculo = $stComposição;
        $this->SetXY($x+10,$y+4);
        /* Inicializa Fonte*/
        $this->setFont( 'Arial','',10 );

        /**
         * Retangulos
         */
        /* Retangulo da Composicação */
        $this->Rect( $x, $y, 189, 70*$inTamY );
        /* Retangulo Dados Cadastrais */
        $this->Rect( $x, $y + (78*$inTamY) , 189, 69*$inTamY );

        $this->Rect( $x, $y + (155*$inTamY) , 189, 69*$inTamY );

        /* Composição do Calculo */
        $this->setFont( 'Arial','',7 );
        $this->setLeftMargin(10);
        $this->Write( 2.5 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        /**
         * Montar Estrutura dos Dados Cadastrais
         */

        /* Linhas Horizontais */
        $this->Line( $x , $y +  (90*$inTamY) , $x +189, $y +  (90*$inTamY) );
        $this->Line( $x , $y +  (98*$inTamY) , $x +189, $y +  (98*$inTamY) );
        $this->Line( $x , $y + (103*$inTamY) , $x +189, $y + (103*$inTamY) );
        $this->Line( $x , $y + (108*$inTamY) , $x +189, $y + (108*$inTamY) );

        $this->Line( $x , $y + (117*$inTamY) , $x +134, $y + (117*$inTamY) );
        $this->Line( $x , $y + (120*$inTamY) , $x +134, $y + (120*$inTamY) );
        $this->Line( $x , $y + (123*$inTamY) , $x +134, $y + (123*$inTamY) );
        $this->Line( $x , $y + (126*$inTamY) , $x +134, $y + (126*$inTamY) );
        $this->Line( $x , $y + (129*$inTamY) , $x +134, $y + (129*$inTamY) );
        $this->Line( $x , $y + (132*$inTamY) , $x +134, $y + (132*$inTamY) );
        $this->Line( $x , $y + (135*$inTamY) , $x +134, $y + (135*$inTamY) );
        $this->Line( $x , $y + (138*$inTamY) , $x +134, $y + (138*$inTamY) );
        $this->Line( $x , $y + (141*$inTamY) , $x +134, $y + (141*$inTamY) );

        for ($inZ=0; $inZ<61; $inZ+=3) {
            $this->Line( $x , $y + (($inZ+158)*$inTamY) , $x +134, $y + (($inZ+158)*$inTamY) );
        }

        $this->Line( $x , $y + (144*$inTamY) , $x +134, $y + (144*$inTamY) );

        /* Linhas Verticais */
        /* Linha Ao lado do demonstrativo, aquela maior */
        $this->Line( $x + 134 , $y + (155*$inTamY), $x + 134 , $y  + (224*$inTamY));
        $this->Line( $x + 15 , $y + (155*$inTamY), $x + 15 , $y  + (218*$inTamY));
        $this->Line( $x + 30 , $y + (155*$inTamY), $x + 30 , $y  + (218*$inTamY));//era 28
        $this->Line( $x + 21 , $y + (155*$inTamY), $x + 21 , $y  + (218*$inTamY));//era 28
        $this->Line( $x + 41 , $y + (155*$inTamY), $x + 41 , $y  + (218*$inTamY));
        $this->Line( $x + 54 , $y + (155*$inTamY), $x + 54 , $y  + (218*$inTamY));
        $this->Line( $x + 67 , $y + (155*$inTamY), $x + 67 , $y  + (218*$inTamY));
        $this->Line( $x + 78 , $y + (155*$inTamY), $x + 78 , $y  + (218*$inTamY));
        $this->Line( $x + 90 , $y + (155*$inTamY), $x + 90 , $y  + (218*$inTamY));
        $this->Line( $x + 102 , $y + (155*$inTamY), $x + 102 , $y  + (224*$inTamY));
        $this->Line( $x + 118 , $y + (155*$inTamY), $x + 118 , $y  + (218*$inTamY));

        $this->Line( $x + 134 , $y + (108*$inTamY), $x + 134 , $y  + (147*$inTamY));
        $this->Line( $x + 152 , $y + (111*$inTamY), $x + 152 , $y  + (147*$inTamY));
        $this->Line( $x + 171 , $y + (111*$inTamY), $x + 171 , $y  + (147*$inTamY));

        $this->Line( $x + 15 , $y + (111*$inTamY), $x + 15 , $y  + (147*$inTamY));
        $this->Line( $x + 30 , $y + (111*$inTamY), $x + 30 , $y  + (147*$inTamY));//era 28
        $this->Line( $x + 21 , $y + (111*$inTamY), $x + 21 , $y  + (147*$inTamY));//era 28
        $this->Line( $x + 41 , $y + (111*$inTamY), $x + 41 , $y  + (147*$inTamY));
        $this->Line( $x + 54 , $y + (111*$inTamY), $x + 54 , $y  + (147*$inTamY));
        $this->Line( $x + 67 , $y + (111*$inTamY), $x + 67 , $y  + (147*$inTamY));
        $this->Line( $x + 78 , $y + (111*$inTamY), $x + 78 , $y  + (147*$inTamY));
        $this->Line( $x + 90 , $y + (111*$inTamY), $x + 90 , $y  + (147*$inTamY));
        $this->Line( $x + 102 , $y + (111*$inTamY), $x + 102 , $y  + (147*$inTamY)); //era o unico 147 os outros eram 141
        $this->Line( $x + 118 , $y + (111*$inTamY), $x + 118 , $y  + (147*$inTamY));

        /* Linha 1*/
        $this->Line( $x + 88 , $y +  (78*$inTamY) , $x + 88 , $y  +  (90*$inTamY));

        /* Linha 2*/
        $this->Line( $x +  64 , $y +  (90*$inTamY) , $x +  64 , $y  +  (98*$inTamY));
        $this->Line( $x +  91 , $y +  (90*$inTamY) , $x +  91 , $y  +  (98*$inTamY));
        $this->Line( $x + 115 , $y +  (90*$inTamY) , $x + 115 , $y  +  (98*$inTamY));
        $this->Line( $x + 139 , $y +  (90*$inTamY) , $x + 139 , $y  +  (98*$inTamY));

        /* Linha 3*/
        $this->Line( $x +  34 , $y + (98*$inTamY) , $x + 34 , $y  + (103*$inTamY)); //5
        $this->Line( $x + 105 , $y + (98*$inTamY) , $x + 105 , $y  + (103*$inTamY));
        $this->Line( $x + 160 , $y + (98*$inTamY) , $x + 160 , $y  + (103*$inTamY));

        /* Linha 4*/
        $this->Line( $x +  25  , $y + (103*$inTamY) , $x +  25 , $y  + (108*$inTamY)); //6
        $this->Line( $x +  44  , $y + (103*$inTamY) , $x +  44 , $y  + (108*$inTamY));
        $this->Line( $x +  81  , $y + (103*$inTamY) , $x +  81 , $y  + (108*$inTamY));
        $this->Line( $x + 105  , $y + (103*$inTamY) , $x + 105 , $y  + (108*$inTamY));
        $this->Line( $x + 127  , $y + (103*$inTamY) , $x + 127 , $y  + (108*$inTamY));
        $this->Line( $x + 163  , $y + (103*$inTamY) , $x + 163 , $y  + (108*$inTamY));

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt);
        /* dados */
        $this->setFont( 'Arial','',14 );
        $this->Text( $x+112 , $y+(84*$inTamY) , 'DÍVIDA ATIVA - REFIS' );
        /* exercicio */
        $this->setFont( 'Arial','',10 );
        $this->Text( $x+122 , $y+(88*$inTamY) , 'EXERCÍCIO  ' . $this->stExercicio );

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+  1 , $y+(92*$inTamY) , 'CONTRIBUINTE:' );
        $this->Text( $x+ 65 , $y+(92*$inTamY) , 'CÓDIGO LOGRADOURO:' );
        $this->Text( $x+ 92 , $y+(92*$inTamY) , 'INSCRIÇÃO IMOBILÍARIA:' );
        $this->Text( $x+ 116 , $y+(92*$inTamY), 'INSCRIÇÃO ECONÔMICA:' );
        $this->Text( $x+141 , $y+(92*$inTamY) , 'IMPOSTO / TAXA:' );

        $this->setFont( 'Arial','',5 );

        $this->Text( $x+    1 , $y+(100*$inTamY) , 'NOME DO LOGRADOURO:' );
        $this->Text( $x+   35 , $y+(100*$inTamY) , 'COMPLEMENTO:' );
        $this->Text( $x+  106 , $y+(100*$inTamY) , 'Nº DA COBRANÇA DA DIVIDA ATIVA (Nº Acordo):' );
        $this->Text( $x+  162 , $y+(100*$inTamY) , 'DATA DO OCORDO:' );

        $this->Text( $x+    1 , $y+(105*$inTamY) , 'CONDOMÍNIO:' );
        $this->Text( $x+   26 , $y+(105*$inTamY) , 'QUADRA:' );
        $this->Text( $x+   45 , $y+(105*$inTamY) , 'LOTE:' );
        $this->Text( $x+   82 , $y+(105*$inTamY) , 'DISTRITO:' );
        $this->Text( $x+  106 , $y+(105*$inTamY) , 'REGIÃO:' );
        $this->Text( $x+  128 , $y+(105*$inTamY) , 'CEP:' );
        $this->Text( $x+  164 , $y+(105*$inTamY) , 'CIDADE / ESTADO:' );

        $this->setFont( 'Arial','',5 );
        $this->SetFillColor(240,240,240);
        $this->Rect( $x, $y+(108*$inTamY), 134, 3*$inTamY, 'DF' ); //2.2
        $this->Text( $x+1 , $y+(110.2*$inTamY) , ' D A D O S   D E   C O M P O S I Ç Ã O   D A   D I V I D A   A T I V A' );
        $this->Rect( $x+134, $y+(108*$inTamY) , 55 , 3*$inTamY , 'DF');
        $this->Text( $x+138 , $y+(110.2*$inTamY) , ' D E M O N S T R A T I V O    D A S    P A R C E L A S' );

        $this->setFont( 'Arial','',4 );
        $this->Text( $x+   2 , $y+(113*$inTamY) , 'Nº de Inscrição' );
        $this->Text( $x+   2 , $y+(115*$inTamY) , 'Divida Ativa:' );

        $this->Text( $x+  16 , $y+(113*$inTamY) , 'Ano' );
        $this->Text( $x+  16 , $y+(115*$inTamY) , 'Devido:' );

        $this->Text( $x+  22 , $y+(113*$inTamY) , 'Imposto /' );
        $this->Text( $x+  22 , $y+(115*$inTamY) , 'Taxa:' );

        $this->Text( $x+  32 , $y+(113*$inTamY) , 'Valor Origem' );
        $this->Text( $x+  32 , $y+(115*$inTamY) , 'Devido:' );

        $this->Text( $x+  43 , $y+(113*$inTamY) , 'Multa de Mora:' );

        $this->Text( $x+  56 , $y+(113*$inTamY) , 'Juros de Mora:' );

        $this->Text( $x+  69 , $y+(113*$inTamY) , 'Juros de' );
        $this->Text( $x+  69 , $y+(115*$inTamY) , 'Parcelamento' );

        $this->Text( $x+  80 , $y+(113*$inTamY) , 'Honorários' );
        $this->Text( $x+  80 , $y+(115*$inTamY) , 'Advocatícios:' );

        $this->Text( $x+  91 , $y+(113*$inTamY) , 'At. Montetária' );
        $this->Text( $x+  91 , $y+(115*$inTamY) , '(IPCA-E):' );

        $this->Text( $x+  104 , $y+(113*$inTamY) , 'Multa de Infração:' );
        $this->Text( $x+  120 , $y+(113*$inTamY) , 'Valor Atualização' );
        $this->Text( $x+  120 , $y+(115*$inTamY) , 'a Pagar:' );

        /* parcelas */
        $this->setFont( 'Arial','', 4 );
        for ($inX=1; $inX<17; $inX++) {
            if ($this->arVencimentosDemonstrativos[$inX-1])
                $this->Text( $x+135 , $y+((112+($inX*2))*$inTamY) , $inX.') ' . $this->arVencimentosDemonstrativos[$inX-1] . ' ' . $this->arDemonstrativoParcelas[$inX-1] );

            if ($this->arVencimentosDemonstrativos[$inX+15])
                $this->Text( $x+153 , $y+((112+($inX*2))*$inTamY) , ($inX+16).') ' . $this->arVencimentosDemonstrativos[$inX+15] . ' ' . $this->arDemonstrativoParcelas[$inX+15] );

            if ($this->arVencimentosDemonstrativos[$inX+31])
                $this->Text( $x+172 , $y+((112+($inX*2))*$inTamY) , ($inX+32).') ' . $this->arVencimentosDemonstrativos[$inX+31] . ' ' . $this->arDemonstrativoParcelas[$inX+31] );
        }

        $this->setFont( 'Arial','B',7 );
        $this->Text( $x+14 , $y+(85*$inTamY) , "PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO" );
        $this->setFont( 'Arial','',6 );
        $this->Text( $x+14 , $y+(88*$inTamY) , "Secretaria de Administração e Finanças" );

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+3 , $y+(220*$inTamY) , 'OBSERVAÇÕES COMPLEMENTARES:' );

        $this->setFont('Arial','',4);
        $this->Text   ( $x+2, $y+(226*$inTamY), 'Data de emissão: '.date("d/m/Y h:i:s"));

        /* contribuibte */
        $this->Text( $x+ 2, $y+(96*$inTamY) , $this->stContribuinte );

        /* codigo logradouro */
        $this->Text( $x+68, $y+(96*$inTamY) , $this->stCodigoLogradouro );

        /* inscricao imobiliaria */
        $this->Text( $x+95, $y+(96*$inTamY) , $this->stInscricaoImobiliaria );

        /* inscricao economica */
        $this->Text( $x+118, $y+(96*$inTamY) , $this->stInscricaoEconomica );

        /* imposto/taxa */
        $arTMP = explode( ";", $this->stImpostoTaxa );

        if (  count($arTMP)-1 >= 0 ) {
            $this->Text( $x+156, $y+(92*$inTamY) , $arTMP[count($arTMP)-1] );
            for ( $inX=count($arTMP)-2; $inX>=0; $inX-- )
                $this->Text( $x+144, $y+((94+((count($arTMP)-2)-$inX)*1.5)*$inTamY) , $arTMP[$inX] );
        }

        /* nome do logradouro */
        $this->Text( $x+2 , $y+(102*$inTamY) , $this->stNomeLogradouro );

        /* complemento */
        $this->Text( $x+36 , $y+(102*$inTamY) , $this->stComplemento );

        /* numero da cobranca da divida ativa */
        $this->Text( $x+ 108 , $y+(102*$inTamY) , $this->stNroCobrancaDA );

        /*data do acordo */
        $this->Text( $x+ 162 , $y+(102*$inTamY) , $this->stDataAcordo );

        /* condominio */
        $this->Text( $x+ 2 , $y+(107*$inTamY) , $this->stCondominio );

        /* quadra */
        $this->Text( $x+ 27 , $y+(107*$inTamY) , $this->stQuadra );

        /* lote */
        $this->Text( $x+ 46 , $y+(107*$inTamY) , $this->stLote );

        /* distrito */
        $this->Text( $x+ 82 , $y+(107*$inTamY) , $this->stDistrito );

        /* regiao */
        $this->Text( $x+109 , $y+(107*$inTamY) , $this->stRegiao );

        /* cep */
        $this->Text( $x+130 , $y+(107*$inTamY) , $this->stCep );

        /* cidade/estado */
        $this->Text( $x+165 , $y+(107*$inTamY) , $this->stCidade." / ".$this->stEstado );

//cada linha sao +3
        $flTotalPagar = 0.00;
        for ($inX=0; $inX<10; $inX++) {
            $this->Text( $x+3 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["inscricao"] ); //numero de inscricao divida ativa
            $this->Text( $x+16 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["ano devido"] ); //ano devido
            $this->Text( $x+31 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["valor"] ); //valor origem devido

            $this->setFont('Arial','',3);
            $this->Text( $x+21 , $y+((119+($inX*3))*$inTamY) , substr($this->arDetalhamentoInscricao[$inX]["credito"], 0, 19) ); //credito original
            $this->setFont('Arial','',4);

            $this->Text( $x+44 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["multa"] ); //multa de mora
            $this->Text( $x+57 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros'] ); //juros de mora

            $this->Text( $x+70 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros2'] ); //juros 0,5
            $this->Text( $x+81 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['honorario'] ); //honorarios advocaticios

            $this->Text( $x+95 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['atualizacao'] ); //atualizacao monetaria (ipca-e)
            $this->Text( $x+107 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['multa2'] ); //multa de infracao
            if ($this->arDetalhamentoInscricao[$inX]['valor2'] != "") {
                $this->Text( $x+124 , $y+((119+($inX*3))*$inTamY) , number_format($this->arDetalhamentoInscricao[$inX]['valor2'], 2, ',', '.' ) ); //valor atualizacao a pagar
                $flTotalPagar += $this->arDetalhamentoInscricao[$inX]['valor2'];
            }
        }

        for ( $inX=10; $inX<count($this->arDetalhamentoInscricao); $inX++ ) {
            $this->Text( $x+3 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["inscricao"] ); //numero de inscricao divida ativa
            $this->Text( $x+16 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["ano devido"] ); //ano devido
            $this->Text( $x+31 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["valor"] ); //valor origem devido

            $this->Text( $x+23 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["credito"] ); //credito original

            $this->Text( $x+44 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["multa"] ); //multa de mora
            $this->Text( $x+57 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros'] ); //juros de mora

            $this->Text( $x+70 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros2'] ); //juros 0,5
            $this->Text( $x+81 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['honorario'] ); //honorarios advocaticios

            $this->Text( $x+95 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['atualizacao'] ); //atualizacao monetaria (ipca-e)
            $this->Text( $x+107 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['multa2'] ); //multa de infracao
            if ($this->arDetalhamentoInscricao[$inX]['valor2'] != "") {
                $this->Text( $x+124 , $y+((127+($inX*3))*$inTamY) , number_format($this->arDetalhamentoInscricao[$inX]['valor2'], 2, ',', '.' ) ); //valor atualizacao a pagar
                $flTotalPagar += $this->arDetalhamentoInscricao[$inX]['valor2'];
            }
        }

        $flTotalPagar -= $this->stReducao;

        $this->Text( $x+107 , $y+(220*$inTamY) , "Redução:" ); //multa de infracao
        $this->Text( $x+124 , $y+(220*$inTamY) , number_format($this->stReducao, 2, ',', '.' ) ); //valor atualizacao a pagar

        $this->Text( $x+107 , $y+(223*$inTamY) , "Total a pagar:" ); //multa de infracao
        $this->Text( $x+124 , $y+(223*$inTamY) , number_format($flTotalPagar, 2, ',', '.' ) ); //valor atualizacao a pagar

        $this->Text( $x+3 , $y+(222*$inTamY), substr($this->stObservacoesComplementares, 0, 150 ) );
        $this->Text( $x+3 , $y+(224*$inTamY), substr($this->stObservacoesComplementares, 150, 150 ) );
    }

//  function novaPagina() {
//      $this->addPage();
//  }
//
    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}
//     function desenhaCarne($x , $y) {

// $stComposição =
// "                                                                        O B S E R V A Ç Õ E S   I M P O R T A N T E S\n

// 01) Após a quitação deste carnê o contribuinte ou o seu procurador (legalmente constituído) deverá comparecer ao setor para dar baixa no processo de parcelamento;\n

// 02) Fica ressalvado o direito de a Secretaria Municipal de Administração e Finanças cobrar dívidas que forem apuradas de responsabilidade do Contribuinte identificado;\n

// 03) O atraso no pagamento de 02 (dois) prestações anula o parcelamento inicial e obriga a inscrição do débito em dívida ativa ou, se nela já se encontra inscrito, sua remessa imediata à cobrança judicial;\n

// 04) Para os débitos em execução fiscal, procurar o cartório cível para pagamento das custas cartorárias;\n

// 05) Mantenha seus dados atualizados no cadastro fiscal do município (endereço para correspondência, Telefone de contato, e-mail, entre outros);\n

// 06) A Coordenadoria Fazendária do Município é o órgão controlador do Setor de Registro e Cobrança da Dívida Ativa e do Setor de Tributos e Fiscalização, vinculado à Secretaria de Administração e Finanças - SECAF (Prefeitura Municipal de Mata de São João/BA);\n

// 07) Quaisquer dúvidas favor entrar em contato com o Setor de Registro e Cobrança da Dívida Ativa (órgão vinculado ao Setor de Tributos e Fiscalização Fazendária), na Rua Antônio Luiz Garcez, s/n, Centro - Centro Administrativo - Mata de São João/Ba. Tel/Fax: (71) 3635-1669 (71) 9617-723  ou e-mail: dividaativa.tributacao@pmsj.ba.gov.br. Acesse o nosso site e fique por dentro de todos os acontecimentos do município no endereço http://www.pmsj.ba.gov.br.\n

//                                                              P A G Á V E L    E M    Q U A L Q U E R    B A N C O   A T É   O   V E N C I M E N T O\n";

//         $inTamY = 1.15;

//         $this->stComposicaoCalculo = $stComposição;
//         $this->SetXY($x+10,$y+4);
//         /* Inicializa Fonte*/
//         $this->setFont( 'Arial','',10 );

//         /**
//          * Retangulos
//          */
//         /* Retangulo da Composicação */
//         $this->Rect( $x, $y, 189, 70*$inTamY );
//         // $y += 18;
//         /* Retangulo Dados Cadastrais */
//         $this->Rect( $x, $y + (78*$inTamY) , 189, 69*$inTamY );

//         $this->Rect( $x, $y + (155*$inTamY) , 189, 69*$inTamY );

//         /* Composição do Calculo */
//         $this->setFont( 'Arial','',7 );
//         $this->setLeftMargin(10);
//         $this->Write( 2.5 , $this->stComposicaoCalculo );
//         $this->setLeftMargin(0);

//         /**
//          * Montar Estrutura dos Dados Cadastrais
//          */

//         /* Linhas Horizontais */
//         $this->Line( $x , $y +  (90*$inTamY) , $x +189, $y +  (90*$inTamY) );
//         $this->Line( $x , $y +  (98*$inTamY) , $x +189, $y +  (98*$inTamY) );
//         $this->Line( $x , $y + (103*$inTamY) , $x +189, $y + (103*$inTamY) );
//         $this->Line( $x , $y + (108*$inTamY) , $x +189, $y + (108*$inTamY) );

//         $this->Line( $x , $y + (117*$inTamY) , $x +134, $y + (117*$inTamY) );
//         $this->Line( $x , $y + (120*$inTamY) , $x +134, $y + (120*$inTamY) );
//         $this->Line( $x , $y + (123*$inTamY) , $x +134, $y + (123*$inTamY) );
//         $this->Line( $x , $y + (126*$inTamY) , $x +134, $y + (126*$inTamY) );
//         $this->Line( $x , $y + (129*$inTamY) , $x +134, $y + (129*$inTamY) );
//         $this->Line( $x , $y + (132*$inTamY) , $x +134, $y + (132*$inTamY) );
//         $this->Line( $x , $y + (135*$inTamY) , $x +134, $y + (135*$inTamY) );
//         $this->Line( $x , $y + (138*$inTamY) , $x +134, $y + (138*$inTamY) );
//         $this->Line( $x , $y + (141*$inTamY) , $x +134, $y + (141*$inTamY) );
//         $this->Line( $x , $y + (147*$inTamY) , $x +134, $y + (147*$inTamY) );
//         $this->Line( $x , $y + (150*$inTamY) , $x +134, $y + (150*$inTamY) );
//         $this->Line( $x , $y + (153*$inTamY) , $x +134, $y + (153*$inTamY) );
//         $this->Line( $x , $y + (156*$inTamY) , $x +134, $y + (156*$inTamY) );
//         $this->Line( $x , $y + (159*$inTamY) , $x +134, $y + (159*$inTamY) );

//         /*
//         for ($inZ=0; $inZ<61; $inZ+=3) {
//             $this->Line( $x , $y + (($inZ+158)*$inTamY) , $x +134, $y + (($inZ+158)*$inTamY) );
//         }
// */

//         $this->Line( $x , $y + (144*$inTamY) , $x +134, $y + (144*$inTamY) );

//         /* Linhas Verticais */
//         $this->Line( $x + 134 , $y + (108*$inTamY), $x + 134 , $y  + (165*$inTamY));
//         $this->Line( $x + 152 , $y + (111*$inTamY), $x + 152 , $y  + (165*$inTamY));
//         $this->Line( $x + 171 , $y + (111*$inTamY), $x + 171 , $y  + (165*$inTamY));

//         $this->Line( $x + 15 , $y + (111*$inTamY), $x + 15 , $y  + (159*$inTamY));
//         $this->Line( $x + 30 , $y + (111*$inTamY), $x + 30 , $y  + (159*$inTamY));//era 28
//         $this->Line( $x + 21 , $y + (111*$inTamY), $x + 21 , $y  + (159*$inTamY));//era 28
//         $this->Line( $x + 41 , $y + (111*$inTamY), $x + 41 , $y  + (159*$inTamY));
//         $this->Line( $x + 54 , $y + (111*$inTamY), $x + 54 , $y  + (159*$inTamY));
//         $this->Line( $x + 67 , $y + (111*$inTamY), $x + 67 , $y  + (159*$inTamY));
//         $this->Line( $x + 78 , $y + (111*$inTamY), $x + 78 , $y  + (159*$inTamY));
//         $this->Line( $x + 90 , $y + (111*$inTamY), $x + 90 , $y  + (159*$inTamY));
//         $this->Line( $x + 102 , $y + (111*$inTamY), $x + 102 , $y  + (165*$inTamY)); //era o unico 147 os outros eram 141
//         $this->Line( $x + 118 , $y + (111*$inTamY), $x + 118 , $y  + (159*$inTamY));

//         /* Linha 1*/
//         $this->Line( $x + 88 , $y +  (78*$inTamY) , $x + 88 , $y  +  (90*$inTamY));

//         /* Linha 2*/
//         $this->Line( $x +  64 , $y +  (90*$inTamY) , $x +  64 , $y  +  (98*$inTamY));
//         $this->Line( $x +  91 , $y +  (90*$inTamY) , $x +  91 , $y  +  (98*$inTamY));
//         $this->Line( $x + 115 , $y +  (90*$inTamY) , $x + 115 , $y  +  (98*$inTamY));
//         $this->Line( $x + 139 , $y +  (90*$inTamY) , $x + 139 , $y  +  (98*$inTamY));

//         /* Linha 3*/
//         $this->Line( $x +  34 , $y + (98*$inTamY) , $x + 34 , $y  + (103*$inTamY)); //5
//         $this->Line( $x + 105 , $y + (98*$inTamY) , $x + 105 , $y  + (103*$inTamY));
//         $this->Line( $x + 160 , $y + (98*$inTamY) , $x + 160 , $y  + (103*$inTamY));

//         /* Linha 4*/
//         $this->Line( $x +  25  , $y + (103*$inTamY) , $x +  25 , $y  + (108*$inTamY)); //6
//         $this->Line( $x +  44  , $y + (103*$inTamY) , $x +  44 , $y  + (108*$inTamY));
//         $this->Line( $x +  81  , $y + (103*$inTamY) , $x +  81 , $y  + (108*$inTamY));
//         $this->Line( $x + 105  , $y + (103*$inTamY) , $x + 105 , $y  + (108*$inTamY));
//         $this->Line( $x + 127  , $y + (103*$inTamY) , $x + 127 , $y  + (108*$inTamY));
//         $this->Line( $x + 163  , $y + (103*$inTamY) , $x + 163 , $y  + (108*$inTamY));

//         /**
//          * Titulos dos Dados
//          */
//         /* imagem*/
//         $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
//         $this->Image( $this->stCamLogo , $x+1 , $y+(79*$inTamY) , 10 , 10 , $stExt);
//         /* dados */
//         $this->setFont( 'Arial','',14 );
//         $this->Text( $x+112 , $y+(84*$inTamY) , 'DÍVIDA ATIVA - REFIS' );
//         /* exercicio */
//         $this->setFont( 'Arial','',10 );
//         $this->Text( $x+122 , $y+(88*$inTamY) , 'EXERCÍCIO  ' . $this->stExercicio );

//         $this->setFont( 'Arial','',5 );
//         $this->Text( $x+  1 , $y+(92*$inTamY) , 'CONTRIBUINTE:' );
//         $this->Text( $x+ 65 , $y+(92*$inTamY) , 'CÓDIGO LOGRADOURO:' );
//         $this->Text( $x+ 92 , $y+(92*$inTamY) , 'INSCRIÇÃO IMOBILÍARIA:' );
//         $this->Text( $x+ 116 , $y+(92*$inTamY), 'INSCRIÇÃO ECONÔMICA:' );
//         $this->Text( $x+141 , $y+(92*$inTamY) , 'IMPOSTO / TAXA:' );

//         $this->setFont( 'Arial','',5 );

//         $this->Text( $x+    1 , $y+(100*$inTamY) , 'NOME DO LOGRADOURO:' );
//         $this->Text( $x+   35 , $y+(100*$inTamY) , 'COMPLEMENTO:' );
//         $this->Text( $x+  106 , $y+(100*$inTamY) , 'Nº DA COBRANÇA DA DIVIDA ATIVA (Nº Acordo):' );
//         $this->Text( $x+  162 , $y+(100*$inTamY) , 'DATA DO ACORDO:' );

//         $this->Text( $x+    1 , $y+(105*$inTamY) , 'CONDOMÍNIO:' );
//         $this->Text( $x+   26 , $y+(105*$inTamY) , 'QUADRA:' );
//         $this->Text( $x+   45 , $y+(105*$inTamY) , 'LOTE:' );
//         $this->Text( $x+   82 , $y+(105*$inTamY) , 'DISTRITO:' );
//         $this->Text( $x+  106 , $y+(105*$inTamY) , 'REGIÃO:' );
//         $this->Text( $x+  128 , $y+(105*$inTamY) , 'CEP:' );
//         $this->Text( $x+  164 , $y+(105*$inTamY) , 'CIDADE / ESTADO:' );

//         $this->setFont( 'Arial','',5 );
//         $this->SetFillColor(240,240,240);
//         $this->Rect( $x, $y+(108*$inTamY), 134, 3*$inTamY, 'DF' ); //2.2
//         $this->Text( $x+1 , $y+(110.2*$inTamY) , ' D A D O S   D E   C O M P O S I Ç Ã O   D A   D I V I D A   A T I V A' );
//         $this->Rect( $x+134, $y+(108*$inTamY) , 55 , 3*$inTamY , 'DF');
//         $this->Text( $x+138 , $y+(110.2*$inTamY) , ' D E M O N S T R A T I V O    D A S    P A R C E L A S' );

//         $this->setFont( 'Arial','',4 );
//         $this->Text( $x+   2 , $y+(113*$inTamY) , 'Nº de Inscrição' );
//         $this->Text( $x+   2 , $y+(115*$inTamY) , 'Divida Ativa:' );

//         $this->Text( $x+  16 , $y+(113*$inTamY) , 'Ano' );
//         $this->Text( $x+  16 , $y+(115*$inTamY) , 'Devido:' );

//         $this->Text( $x+  22 , $y+(113*$inTamY) , 'Imposto /' );
//         $this->Text( $x+  22 , $y+(115*$inTamY) , 'Taxa:' );

//         $this->Text( $x+  32 , $y+(113*$inTamY) , 'Valor Origem' );
//         $this->Text( $x+  32 , $y+(115*$inTamY) , 'Devido:' );

//         $this->Text( $x+  43 , $y+(113*$inTamY) , 'Multa de Mora:' );

//         $this->Text( $x+  56 , $y+(113*$inTamY) , 'Juros de Mora:' );

//         $this->Text( $x+  69 , $y+(113*$inTamY) , 'Juros de' );
//         $this->Text( $x+  69 , $y+(115*$inTamY) , 'Parcelamento' );

//         $this->Text( $x+  80 , $y+(113*$inTamY) , 'Honorários' );
//         $this->Text( $x+  80 , $y+(115*$inTamY) , 'Advocatícios:' );

//         $this->Text( $x+  91 , $y+(113*$inTamY) , 'At. Montetária' );
//         $this->Text( $x+  91 , $y+(115*$inTamY) , '(IPCA-E):' );

//         $this->Text( $x+  104 , $y+(113*$inTamY) , 'Multa de Infração:' );
//         $this->Text( $x+  120 , $y+(113*$inTamY) , 'Valor Atualização' );
//         $this->Text( $x+  120 , $y+(115*$inTamY) , 'a Pagar:' );

//         /* parcelas */
//         $this->setFont( 'Arial','', 4 );
//         for ($inX=1; $inX<17; $inX++) {
//             if ($this->arVencimentosDemonstrativos[$inX-1])
//                 $this->Text( $x+135 , $y+((112+($inX*2))*$inTamY) , $inX.') ' . $this->arVencimentosDemonstrativos[$inX-1] . ' ' . $this->arDemonstrativoParcelas[$inX-1] );

//             if ($this->arVencimentosDemonstrativos[$inX+15])
//                 $this->Text( $x+153 , $y+((112+($inX*2))*$inTamY) , ($inX+16).') ' . $this->arVencimentosDemonstrativos[$inX+15] . ' ' . $this->arDemonstrativoParcelas[$inX+15] );

//             if ($this->arVencimentosDemonstrativos[$inX+31])
//                 $this->Text( $x+172 , $y+((112+($inX*2))*$inTamY) , ($inX+32).') ' . $this->arVencimentosDemonstrativos[$inX+31] . ' ' . $this->arDemonstrativoParcelas[$inX+31] );
//         }

//         $this->setFont( 'Arial','B',7 );
//         $this->Text( $x+14 , $y+(85*$inTamY) , "PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO" );
//         $this->setFont( 'Arial','',6 );
//         $this->Text( $x+14 , $y+(88*$inTamY) , "Secretaria de Administração e Finanças" );

//         $this->setFont( 'Arial','',5 );
//         $this->Text( $x+3 , $y+(161*$inTamY) , 'OBSERVAÇÕES COMPLEMENTARES:' );

//         $this->setFont('Arial','',4);
//         $this->Text   ( $x+2, $y+(167*$inTamY), 'Data de emissão: '.date("d/m/Y h:i:s"));

//         /* contribuibte */
//         $this->Text( $x+ 2, $y+(96*$inTamY) , $this->stContribuinte );

//         /* codigo logradouro */
//         $this->Text( $x+68, $y+(96*$inTamY) , $this->stCodigoLogradouro );

//         /* inscricao imobiliaria */
//         $this->Text( $x+95, $y+(96*$inTamY) , $this->stInscricaoImobiliaria );

//         /* inscricao economica */
//         $this->Text( $x+118, $y+(96*$inTamY) , $this->stInscricaoEconomica );

//         /* imposto/taxa */
//         $arTMP = explode( ";", $this->stImpostoTaxa ); //teste aqui

//         $inContImpostTaxa = 0;
//         $stImpostoTaxaTmp = '';
//         foreach ($arTMP AS $stImpostoTaxa) {
//             $arImpostoTaxaTmp = explode(' ',$stImpostoTaxa);
//             if ($stImpostoTaxaTmp !='') {
//                 $stImpostoTaxaTmp = trim($stImpostoTaxaTmp).', ';
//             }
//             foreach ($arImpostoTaxaTmp as $stImpTx) {
//                 if (strlen($stImpostoTaxaTmp.$stImpTx.' ') <  70 ) {
//                     $stImpostoTaxaTmp .= $stImpTx.' ';
//                 } else {
//                     if ($inContImpostTaxa ==2) {
//                         break 2;
//                     }
//                     $this->Text( $x+140, $y+((94+($inContImpostTaxa++)*1.5)*$inTamY) , trim($stImpostoTaxaTmp) );
//                     $stImpostoTaxaTmp= $stImpTx.' ';
//                 }
//             }
//         }
//         if ( trim($stImpostoTaxaTmp) ) {
//             $this->Text( $x+140, $y+((94+($inContImpostTaxa++)*1.5)*$inTamY) , substr(trim($stImpostoTaxaTmp),0,-1) );
//         }
//         /* nome do logradouro */
//         $this->Text( $x+2 , $y+(102*$inTamY) , $this->stNomeLogradouro );

//         /* complemento */
//         $this->Text( $x+36 , $y+(102*$inTamY) , $this->stComplemento );

//         /* numero da cobranca da divida ativa */
//         $this->Text( $x+ 108 , $y+(102*$inTamY) , $this->stNroCobrancaDA );

//         /*data do acordo */
//         $this->Text( $x+ 162 , $y+(102*$inTamY) , $this->stDataAcordo );

//         /* condominio */
//         $this->Text( $x+ 2 , $y+(107*$inTamY) , $this->stCondominio );

//         /* quadra */
//         $this->Text( $x+ 27 , $y+(107*$inTamY) , $this->stQuadra );

//         /* lote */
//         $this->Text( $x+ 46 , $y+(107*$inTamY) , $this->stLote );

//         /* distrito */
//         $this->Text( $x+ 82 , $y+(107*$inTamY) , $this->stDistrito );

//         /* regiao */
//         $this->Text( $x+109 , $y+(107*$inTamY) , $this->stRegiao );

//         /* cep */
//         $this->Text( $x+130 , $y+(107*$inTamY) , $this->stCep );

//         /* cidade/estado */
//         $this->Text( $x+165 , $y+(107*$inTamY) , $this->stCidade." / ".$this->stEstado );

// //cada linha sao +3
//         $flTotalPagar = 0.00;
//         for ($inX=0; $inX<10; $inX++) {
//             $this->Text( $x+3 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["inscricao"] ); //numero de inscricao divida ativa
//             $this->Text( $x+16 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["ano devido"] ); //ano devido
//             $this->Text( $x+31 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["valor"] ); //valor origem devido

//             $this->setFont('Arial','',3);
//             $this->Text( $x+21 , $y+((119+($inX*3))*$inTamY) , substr($this->arDetalhamentoInscricao[$inX]["credito"], 0, 19) ); //credito original
//             $this->setFont('Arial','',4);

//             $this->Text( $x+44 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["multa"] ); //multa de mora
//             $this->Text( $x+57 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros'] ); //juros de mora

//             $this->Text( $x+70 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros2'] ); //juros 0,5
//             $this->Text( $x+81 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['honorario'] ); //honorarios advocaticios

//             $this->Text( $x+95 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['atualizacao'] ); //atualizacao monetaria (ipca-e)
//             $this->Text( $x+107 , $y+((119+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['multa2'] ); //multa de infracao
//             if ($this->arDetalhamentoInscricao[$inX]['valor2'] != "") {
//                 $this->Text( $x+124 , $y+((119+($inX*3))*$inTamY) , number_format($this->arDetalhamentoInscricao[$inX]['valor2'], 2, ',', '.' ) ); //valor atualizacao a pagar
//                 $flTotalPagar += $this->arDetalhamentoInscricao[$inX]['valor2'];
//             }
//         }

//         for ( $inX=10; $inX<count($this->arDetalhamentoInscricao); $inX++ ) {
//             $this->Text( $x+3 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["inscricao"] ); //numero de inscricao divida ativa
//             $this->Text( $x+16 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["ano devido"] ); //ano devido
//             $this->Text( $x+31 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["valor"] ); //valor origem devido

//             $this->setFont('Arial','',3);
//             $this->Text( $x+21 , $y+((127+($inX*3))*$inTamY) , substr($this->arDetalhamentoInscricao[$inX]["credito"], 0, 19) ); //credito original
//             $this->setFont('Arial','',4);

//             $this->Text( $x+44 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]["multa"] ); //multa de mora
//             $this->Text( $x+57 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros'] ); //juros de mora

//             $this->Text( $x+70 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['juros2'] ); //juros 0,5
//             $this->Text( $x+81 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['honorario'] ); //honorarios advocaticios

//             $this->Text( $x+95 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['atualizacao'] ); //atualizacao monetaria (ipca-e)
//             $this->Text( $x+107 , $y+((127+($inX*3))*$inTamY) , $this->arDetalhamentoInscricao[$inX]['multa2'] ); //multa de infracao
//             if ($this->arDetalhamentoInscricao[$inX]['valor2'] != "") {
//                 $this->Text( $x+124 , $y+((127+($inX*3))*$inTamY) , number_format($this->arDetalhamentoInscricao[$inX]['valor2'], 2, ',', '.' ) ); //valor atualizacao a pagar
//                 $flTotalPagar += $this->arDetalhamentoInscricao[$inX]['valor2'];
//             }
//         }

//         $flTotalPagar -= $this->stReducao;
//         $this->Text( $x+107 , $y+(161*$inTamY) , "Redução:" ); //multa de infracao
//         $this->Text( $x+124 , $y+(161*$inTamY) , number_format($this->stReducao, 2, ',', '.' ) ); //valor atualizacao a pagar

//         $this->Text( $x+107 , $y+(164*$inTamY) , "Total a pagar:" ); //multa de infracao
//         $this->Text( $x+124 , $y+(164*$inTamY) , number_format($flTotalPagar, 2, ',', '.' ) ); //valor atualizacao a pagar

//         $this->Text( $x+35 , $y+(161*$inTamY), substr($this->stObservacoesComplementares, 0, 88 ) );
//         $this->Text( $x+3 , $y+(164*$inTamY), substr($this->stObservacoesComplementares, 88, 130 ) );
//     }

// //  function novaPagina() {
// //      $this->addPage();
// //  }
// //
//     function show($stNome = "Carne.pdf", $stOpcao="D") {
//         $this->output($stNome,$stOpcao);
//     }
// }

class RCarneDividaRefis2010MataSaoJoao
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
var $obRCarneDadosCadastrais;
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
function RCarneDividaRefis2010MataSaoJoao($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    $this->boConsolidacao = false;
    //$obRProtocolo = new RProtocolo;
    //$obRCarnePetropolis     = new RCarnePetropolis;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    //---------------------
    $this->obRARRConfiguracao     = new RARRConfiguracao;
    $this->obRARRConfiguracao->setCodModulo ( 2 );
    $this->obRARRConfiguracao->consultar();
    $inCodFebraban = $this->obRARRConfiguracao->getCodFebraban();
    unset($this->obRARRConfiguracao);

    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );
    $stNomeImagem = $rsListaImagens->getCampo("valor");

    $inSaltaPagina = "";
    $this->obRCarnePetropolis = new RCarneDadosCadastraisMataSaoJoao();
    $this->obRCarnePetropolis->stCamLogo = CAM_FW_TEMAS."imagens/".$stNomeImagem;
    $this->obRCarnePetropolis->lblTitulo1 = "MATA DE SÃO JOÃO - Sec. de Adm. e Fin.";

    $nuValorTotal = $nuValorNormal = $nuValorJuroNormal = $nuValorMultaNormal = 0.00;

    $stImposto = "";
    foreach ($this->arEmissao as $valor => $chave) {
        /* imprimir duas folhas com dados cadastrais */
        /* buscar informações para dados cadastrais*/
        $stFiltro = " ddc.numcgm = ".$chave[0]['numcgm']." AND ap.cod_parcela = ".$chave[0]['cod_parcela']." \n";
        $obTDATDividaAtiva = new TDATDividaAtiva;
            $rsListaCarne =new RecordSet();
            $obTDATDividaAtiva->RecuperaCapaCarneDividaMataSaoJoao( $rsListaCarne, $stFiltro );

            $rsListaCarne->ordena( "exercicio_original" );
            $arDadosParcelas = array();
            $inTotalParcelas = 0;

            if ( !$rsListaCarne->Eof() ) {
                $rsListaCarne->setPrimeiroElemento();

                $this->obRCarnePetropolis->arDemonstrativoParcelas = array();
                $this->obRCarnePetropolis->arVencimentosDemonstrativos = array();
                $this->obRCarnePetropolis->arDetalhamentoInscricao = array();
                $arInscricoesNaLista = array();
                $arParcelasNaLista = array();
                $inTotalParcelas = 0;
                $inTotalInscricoes = 0;
                $stImpostos = "";
                $arExercicioImposto = array();
                while ( !$rsListaCarne->Eof() ) {
                    $boIncluirInscricao = true;
                    for ($inD=0; $inD<$inTotalParcelas; $inD++) {
                        if ( $arParcelasNaLista[$inD]['num_parcela'] == $rsListaCarne->getCampo("num_parcela") ) {
                            $boIncluirInscricao = false;
                            break;
                        }
                    }

                    if ($boIncluirInscricao) {
                        $this->obRCarnePetropolis->arDemonstrativoParcelas[] = $rsListaCarne->getCampo("vlr_parcela");
                        $this->obRCarnePetropolis->arVencimentosDemonstrativos[] = $rsListaCarne->getCampo("dt_vencimento_parcela");
                        $arParcelasNaLista[$inTotalParcelas]['num_parcela'] = $rsListaCarne->getCampo("num_parcela");
                        $arParcelasNaLista[$inTotalParcelas]['vlr_parcela'] = $rsListaCarne->getCampo("vlr_parcela");
                        $arParcelasNaLista[$inTotalParcelas]['dt_vencimento_parcela'] = $rsListaCarne->getCampo("dt_vencimento_parcela");
                        $inTotalParcelas++;
                    }

                    $boIncluirInscricao = true;
                    for ($inD=0; $inD<$inTotalInscricoes; $inD++) {
                        if ( $arInscricoesNaLista[$inD]["inscricao"] == $rsListaCarne->getCampo("cod_inscricao")."/".$rsListaCarne->getCampo("exercicio_da") ) {
                            $boIncluirInscricao = false;
                            break;
                        }
                    }

                    if ($boIncluirInscricao) {
                        $stFiltro = " dda.cod_inscricao = ".$rsListaCarne->getCampo("cod_inscricao")." AND dda.exercicio = ".$rsListaCarne->getCampo("exercicio_da")." \n";
                        $obTDATDividaAtiva->setDado('judicial', Sessao::read("cobrancaJudicial"));
                        $obTDATDividaAtiva->RecuperaValoresCapaCarneDividaRefis2009MataSaoJoao( $rsListaCarne2, $stFiltro );

                        $arTMP2 = explode( "/", $rsListaCarne2->getCampo("imposto_taxa") );
                        $arTMP = explode( ";", $arTMP2[0] );
                        $inPosInicial = count($arTMP);
                        if ( ( $inPosInicial - 2 ) > 0 )
                            $inPosInicial -= 2;
                        else
                            $inPosInicial--;

                        for ($inX=$inPosInicial; $inX>=0; $inX--) {
                            if (!preg_match($arTMP[$inX], $stImpostos))
                                $stImpostos = $stImpostos.$arTMP[$inX].";";
                        }

                        $arInscricoesNaLista[$inTotalInscricoes]["inscricao"] = $rsListaCarne->getCampo("cod_inscricao")."/".$rsListaCarne->getCampo("exercicio_da");
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["inscricao"] = $rsListaCarne->getCampo("cod_inscricao")."/".$rsListaCarne->getCampo("exercicio_da");
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["ano devido"] = $rsListaCarne->getCampo("exercicio_original");
                        if ($stImposto) {
                            $stImposto .= ", ".$rsListaCarne->getCampo("exercicio_original");
                        }else
                            $stImposto = $rsListaCarne->getCampo("exercicio_original");

                       $arImpostoRegistro = explode( ";", $rsListaCarne2->getCampo("imposto_taxa"));

                        $arTeste[] = $rsListaCarne2->getCampo("imposto_taxa");
                        $inCountImposto = 0;
                        foreach ($arImpostoRegistro AS $arRegistro) {
                            $inCountImposto++;
                            if ($inCountImposto%2==0) {
                                $arDataGrupo = explode( "/", $arRegistro);
                                $arExercicioImposto[trim($arDataGrupo[1])][trim($arCredito[0])] = trim($arCredito[0]).'/'.trim($arDataGrupo[1]).$arCredito[1];
                            } else {
                                $arCredito = explode( "-", $arRegistro);
                            }
                        }
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["credito"] = $arImposto[0];
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["valor"] = number_format($rsListaCarne2->getCampo("valor_origem_devido"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["multa"] = number_format($rsListaCarne2->getCampo("multa_mora"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["juros"] = number_format($rsListaCarne2->getCampo("juros_mora"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["juros2"] = number_format($rsListaCarne2->getCampo("juros2_mora")-$rsListaCarne2->getCampo("juros_mora"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["honorario"] = number_format($rsListaCarne2->getCampo("atualizacao2_mora"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["atualizacao"] = number_format($rsListaCarne2->getCampo("atualizacao_mora"), 2, ',', '.' );
                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["multa2"] = number_format($rsListaCarne2->getCampo("multa2_mora"), 2, ',', '.' );
                        $arInscricoesNaLista[$inTotalInscricoes]["valor_total"] = $rsListaCarne2->getCampo("valor_origem_devido")+$rsListaCarne2->getCampo("multa_mora")+$rsListaCarne2->getCampo("multa2_mora")+$rsListaCarne2->getCampo("juros2_mora")+$rsListaCarne2->getCampo("atualizacao2_mora")+$rsListaCarne2->getCampo("atualizacao_mora");

                        $this->obRCarnePetropolis->arDetalhamentoInscricao[$inTotalInscricoes]["valor2"] =  $arInscricoesNaLista[$inTotalInscricoes]["valor_total"];
                        $inTotalInscricoes++;
                    }

                    $rsListaCarne->proximo();
        }
        }

        $stTributoExercicio = '';
        $inContTamanho = 1;
        foreach ($arExercicioImposto AS $stTributoExerc => $arImposto) {
            foreach ($arImposto as $stimposto) {
                if (strlen($stTributoExercicio) + strlen($stimposto.'/'.$stTributoExerc) > $inContTamanho * 75 ) {
                    //ajuste para montar sem quebras no boleto
                    $stTributoExercicio = str_pad($stTributoExercicio,$inContTamanho * 75,' ');
                    $inContTamanho++;
                }
                $stTributoExercicio .= $stimposto.'/'.$stTributoExerc.",";
            }
        }
        $stTributoExercicio = substr($stTributoExercicio,0,-1);
        $rsListaCarne->setPrimeiroElemento();
        $this->obRCarnePetropolis->stObservacoesComplementares  = (string) $rsListaCarne->getCampo("observacao");
        $this->obRCarnePetropolis->stNomePrefeitura             = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->obRCarnePetropolis->stSubTitulo                  = 'Secretaria de Administração e Finanças';
        $this->obRCarnePetropolis->stExercicio                  = (string) Sessao::getExercicio();
        $this->obRCarnePetropolis->stContribuinte               = (string) $rsListaCarne2->getCampo("nom_cgm");
        $this->obRCarnePetropolis->stCodigoLogradouro           = (string) $rsListaCarne2->getCampo("cod_logradouro");
        $this->obRCarnePetropolis->stNomeLogradouro             = (string) $rsListaCarne2->getCampo("endereco");
        $this->obRCarnePetropolis->stComplemento                = (string) $rsListaCarne2->getCampo("complemento");
        $this->obRCarnePetropolis->stQuadra                     = (string) $rsListaCarne2->getCampo("numero_quadra");
        $this->obRCarnePetropolis->stLote                       = (string) $rsListaCarne2->getCampo("numero_lote");
        $this->obRCarnePetropolis->stDistrito                   = (string) $rsListaCarne2->getCampo("distrito");
        $this->obRCarnePetropolis->stRegiao                     = (string) $rsListaCarne2->getCampo("regiao");
        $this->obRCarnePetropolis->stCep                        = (string) $rsListaCarne2->getCampo("cep");
        $this->obRCarnePetropolis->stCidade                     = 'MATA DE SÃO JOÃO' ;
        $this->obRCarnePetropolis->stEstado                     = 'BAHIA' ;
        $this->obRCarnePetropolis->stCondominio                 = (string) $rsListaCarne2->getCampo("condominio");
        $this->obRCarnePetropolis->stInscricaoImobiliaria       = (string) $rsListaCarne->getCampo("inscricao_municipal");
        $this->obRCarnePetropolis->stInscricaoEconomica         = (string) $rsListaCarne->getCampo("inscricao_economica");
        $this->obRCarnePetropolis->stImpostoTaxa                = $stImpostos;
        $this->obRCarnePetropolis->stNroCobrancaDA              = (string) $rsListaCarne->getCampo("numero_parcelamento")."/".$rsListaCarne->getCampo("exercicio_cobranca");
        $this->obRCarnePetropolis->stDataAcordo                 = (string) $rsListaCarne->getCampo("dt_acordo");
        $this->obRCarnePetropolis->stReducao                    = $rsListaCarne2->getCampo("total_reducao");
        $this->obRCarnePetropolis->setInscricaoDivida   ( $rsListaCarne->getCampo("cod_inscricao")."/".$rsListaCarne->getCampo("exercicio_da") );

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
        $stFiltro = " WHERE aivv.inscricao_municipal = ".$chave[0]['inscricao'];

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosIPTUMata( $rsListaCarne, $stFiltro, $chave[0]['cod_parcela'] );
        $rsListaCarne->addFormatacao ('area_lote','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupt','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('vupc','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_territorial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_territorial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('imposto_predial','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_imovel','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_predial_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('aliquota','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('venal_total_calculado','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_imposto','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_m2_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_limpeza_publica','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_total_tributos','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('taxa_luz','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_parcela','NUMERIC_BR');

        $rsListaCarne->addFormatacao ('vupcd','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_total','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_venal_construcao_descoberta','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('valor_venal_construcao_coberta','NUMERIC_BR');
        $rsListaCarne->addFormatacao ('area_descoberta','NUMERIC_BR');

        $this->obRCarnePetropolis->stReducao = $rsListaCarne2->getCampo("total_reducao");

        $arDadosParcelas = array();
        $inTotalParcelas = 0;
        if ( !$rsListaCarne->Eof() ) {
                    $rsListaCarne->setPrimeiroElemento();

            while ( !$rsListaCarne->Eof() ) {
                if ( $rsListaCarne->getCampo("nro_parcela") != "única" ) {
                            $arDadosParcelas[] = array( "data" => $rsListaCarne->getCampo("vencimento_parcela"),
                                                        "valor"=> $rsListaCarne->getCampo("valor_parcela") );
                } else {
                    $arDadosParcelas[0]["data"] = $rsListaCarne->getCampo("vencimento_parcela");
                    $arDadosParcelas[0]["valor"] = $rsListaCarne->getCampo("valor_parcela");
                }
                $inTotalParcelas++;
                if ( $inTotalParcelas > 11 )
                    break;
                $rsListaCarne->proximo();
            }

        }

        $rsListaCarne->setPrimeiroElemento();

        /* setar todos os dados necessarios */
        $this->obRCarnePetropolis->stVupcd = $rsListaCarne->getCampo("vupcd");
        $this->obRCarnePetropolis->stValorVenalConstrucaoDescoberta = $rsListaCarne->getCampo("valor_venal_construcao_descoberta");
        $this->obRCarnePetropolis->stValorVenalConstrucaoCoberta = $rsListaCarne->getCampo("valor_venal_construcao_coberta");
        $this->obRCarnePetropolis->stValorVenalConstrucaoTotal = $rsListaCarne->getCampo("venal_predial_calculado"); //'10.800,00' ;
        $this->obRCarnePetropolis->stAreaConstruidaTotal = $rsListaCarne->getCampo("area_total");
        $this->obRCarnePetropolis->stAreaUsoPrivativoDescoberta = $rsListaCarne->getCampo("area_descoberta");

        $this->obRCarnePetropolis->stAreaUsoPrivativoTerreno  = (string) $rsListaCarne->getCampo("area_lote"); //'114,52' ;
        $this->obRCarnePetropolis->stVupt = (string) $rsListaCarne->getCampo("vupt"); //'4,5' ;
        $this->obRCarnePetropolis->stVupc = (string) $rsListaCarne->getCampo("vupc"); //'180,00' ;
        $this->obRCarnePetropolis->stValorVenalTerreno = (string) $rsListaCarne->getCampo("venal_territorial_calculado"); //'526,50' ;
        $this->obRCarnePetropolis->stImpostoTerritorial = (string) $rsListaCarne->getCampo("imposto_territorial"); //'5,27' ;
        $this->obRCarnePetropolis->stAreaUsoPrivativoCoberta = (string) $rsListaCarne->getCampo("area_imovel"); //'50,00' ;
        $this->obRCarnePetropolis->stImpostoPredial = (string) $rsListaCarne->getCampo("imposto_predial"); //'108,00' ;
        $this->obRCarnePetropolis->stAliquota = (string) $rsListaCarne->getCampo("aliquota"); //'1,00 %' ;
        $this->obRCarnePetropolis->stValorVenalImovel = (string) $rsListaCarne->getCampo("venal_total_calculado"); //'11.326,50' ;
        $this->obRCarnePetropolis->stValorImposto = (string) $rsListaCarne->getCampo("valor_imposto"); //'113,27' ;
        $this->obRCarnePetropolis->stTipoUnidade = (string) $rsListaCarne->getCampo("categoria_utilizacao_imovel"); //'RESIDENCIAL';
        $this->obRCarnePetropolis->stZona = (string) $rsListaCarne->getCampo("zona"); //'POPULAR' ;
        $this->obRCarnePetropolis->stAreaM2 = (string) $rsListaCarne->getCampo("area_m2_limpeza_publica"); //'11.656.220,00' ;
        $this->obRCarnePetropolis->stValorM2 = (string) $rsListaCarne->getCampo("valor_m2_limpeza_publica"); //'1,01' ;
        $this->obRCarnePetropolis->stValorTaxa = (string) $rsListaCarne->getCampo("taxa_limpeza_publica"); //'28,93' ;
        $this->obRCarnePetropolis->stValorTotalTributos = (string) $rsListaCarne->getCampo("valor_total_tributos"); //'162,20' ;
        $this->obRCarnePetropolis->stContribIlumPublica = (string) $rsListaCarne->getCampo("taxa_luz"); //'20,00';
        // $this->obRCarnePetropolis->arDemonstrativoParcelas = array 	(
        // 												 0 => $arDadosParcelas[0]["valor"], //'140,87',
        // 												 1 => $arDadosParcelas[1]["valor"], //'16,22' ,
        // 												 2 => $arDadosParcelas[2]["valor"], //'16,22' ,
        // 												 3 => $arDadosParcelas[3]["valor"], //'16,22' ,
        // 												 4 => $arDadosParcelas[4]["valor"], //'16,22' ,
        // 												 5 => $arDadosParcelas[5]["valor"], //'16,22' ,
        // 												 6 => $arDadosParcelas[6]["valor"], //'16,22' ,
        // 												 7 => $arDadosParcelas[7]["valor"], //'16,22' ,
        // 												 8 => $arDadosParcelas[8]["valor"], //'16,22' ,
        // 											 	 9 => $arDadosParcelas[9]["valor"], //'16,22' ,
        // 											 	 10 => $arDadosParcelas[10]["valor"], //'16,22'
  //                                                        11 => $arDadosParcelas[11]["valor"], //'16,22'
  //                                                        12 => $arDadosParcelas[12]["valor"] //'16,22'
        // 											) ;
        // $this->obRCarnePetropolis->arVencimentosDemonstrativos = array 	(
        // 												 0 => $arDadosParcelas[0]["data"], //'05/02/2007',
        // 												 1 => $arDadosParcelas[1]["data"], //'05/02/2007' ,
        // 												 2 => $arDadosParcelas[2]["data"], //'05/03/2007' ,
        // 												 3 => $arDadosParcelas[3]["data"], //'05/04/2007' ,
        // 												 4 => $arDadosParcelas[4]["data"], //'05/05/2007' ,
        // 												 5 => $arDadosParcelas[5]["data"], //'05/06/2007' ,
        // 												 6 => $arDadosParcelas[6]["data"], //'05/07/2007' ,
        // 												 7 => $arDadosParcelas[7]["data"], //'05/08/2007' ,
        // 												 8 => $arDadosParcelas[8]["data"], //'05/09/2007' ,
        // 											 	 9 => $arDadosParcelas[9]["data"], //'05/10/2007' ,
        // 											 	 10 => $arDadosParcelas[10]["data"], //'05/11/2007'
  //                                                        11 => $arDadosParcelas[11]["data"], //'05/11/2007'
  //                                                        12 => $arDadosParcelas[12]["data"] //'05/11/2007'
        // 											) ;

        $this->obRCarnePetropolis->desenhaCarne(10,20);
        $this->obRCarnePetropolis->novaPagina();

        $inSaltaPagina++;

        $this->obRCarnePetropolis->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" );
                //limpando o stExercico para o filtro não ser afetado pelo loop
        $this->obRARRCarne->stExercicio = '';
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );
        $this->stExercicio = $rsGeraCarneCabecalho->getCampo( 'descricao' );
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarnePetropolis->setObservacaoL1 ('Créditos: ');

        $arrCodGrupo = explode('.', $rsGeraCarneCabecalho->getCampo( "cod_grupo" ));
        if (count($arrCodGrupo) == 1) {
        $stFiltroComp = " WHERE credito_grupo.cod_grupo = ".$rsGeraCarneCabecalho->getCampo( "cod_grupo" )." AND credito_grupo.ano_exercicio = ".$rsGeraCarneCabecalho->getCampo( "ano_exercicio" );
        } else {
            $stFiltroComp = " WHERE credito_grupo.cod_credito = ".$arrCodGrupo[0]
                           ." AND credito_grupo.cod_natureza = ".$arrCodGrupo[1]
                           ." AND credito_grupo.cod_genero = ".$arrCodGrupo[2]
                           ." AND credito_grupo.cod_especie = ".$arrCodGrupo[3]
                           ." AND credito_grupo.ano_exercicio = ".$rsGeraCarneCabecalho->getCampo( "exercicio" );
        }

        $obTARRCarne->retornaDadosCompensacao( $rsDadosCompensacao, $stFiltroComp );

        $this->obRCarnePetropolis->stCarteira = $rsDadosCompensacao->getCampo("carteira");
        $this->obRCarnePetropolis->stEspecieDoc = $rsDadosCompensacao->getCampo("especie_doc");
        $this->obRCarnePetropolis->stEspecie =  $rsDadosCompensacao->getCampo("especie");
        $this->obRCarnePetropolis->stAceite = $rsDadosCompensacao->getCampo("aceite");
        $this->obRCarnePetropolis->stDataDocumento = date("d/m/Y");
        $this->obRCarnePetropolis->stDataProcessamento = date("d/m/Y");
        $this->obRCarnePetropolis->stAgenciaCodCedente = $rsDadosCompensacao->getCampo("agencia")."/".$rsDadosCompensacao->getCampo("codigo_cedente");
        $this->obRCarnePetropolis->stLocalPagamento = $rsDadosCompensacao->getCampo("local_pagamento");
        //$this->obRCarnePetropolis->stCedente = 'Prefeitura Municipal de Mata de São João';
        $this->obRCarnePetropolis->stQuantidade = $rsDadosCompensacao->getCampo("quantidade");
        //limpando o campo tributo para não repetir nos carnes seguintes do loop
        $this->obRCarnePetropolis->setTributo('');
        while ( !$rsGeraCarneCabecalho->eof() ) {
            /* montagem cabecalho (protocolo) */
            $this->obRCarnePetropolis->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarnePetropolis->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarnePetropolis->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarnePetropolis->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarnePetropolis->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );
            $this->obRCarnePetropolis->setRua               ( str_replace ( "Não Informado ", "",  $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) ) );
            $this->obRCarnePetropolis->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
            $this->obRCarnePetropolis->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
            $this->obRCarnePetropolis->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
            $this->obRCarnePetropolis->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );
            $this->obRCarnePetropolis->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
            $this->obRCarnePetropolis->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarnePetropolis->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarnePetropolis->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarnePetropolis->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarnePetropolis->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarnePetropolis->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarnePetropolis->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            //$stTributo = $this->obRCarnePetropolis->getTributo().str_pad($rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' ),45,' ');
            //$this->obRCarnePetropolis->setTributo           ( $stTributo );

            $this->obRCarnePetropolis->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
            $this->obRCarnePetropolis->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
            $this->obRCarnePetropolis->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarnePetropolis->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            if ( preg_match( '/LIMPEZA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } else {
                $flImpostoAnualReal = $rsGeraCarneCabecalho->getCampo( 'valor' );
                $this->obRCarnePetropolis->setImpostoAnualReal  ( $flImpostoAnualReal                                     );
            }
            $this->obRCarnePetropolis->setReferencia        ( ""                                                          );
            $this->obRCarnePetropolis->setNumeroPlanta      ( ""                                                          );

            // capturar creditos
            $this->obRCarnePetropolis->setObservacaoL1 ( $this->obRCarnePetropolis->getObservacaoL1().$rsGeraCarneCabecalho->getCampo( 'descricao_credito').": ".$rsGeraCarneCabecalho->getCampo( 'valor' )."  ");

            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        $this->obRCarnePetropolis->setTributo           ( $stTributoExercicio );
        $this->obRCarnePetropolis->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarnePetropolis->getTaxaLimpezaAnual() );
        // formatar
        $this->obRCarnePetropolis->setValorAnualReal    ( number_format($this->obRCarnePetropolis->getValorAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( number_format($this->obRCarnePetropolis->getTaxaLimpezaAnual(),2,',','.') );
        $this->obRCarnePetropolis->setImpostoAnualReal  ( number_format($this->obRCarnePetropolis->getImpostoAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setValorTributoReal  ( number_format($this->obRCarnePetropolis->getValorTributoReal(),2,',','.') );
/*        $this->obRCarnePetropolis->drawProtocolo();
        $this->obRCarnePetropolis->posicionaVariaveisProtocolo();
*/
        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 8;

        $this->obBarra = new RCodigoBarraFebrabanCompensacaoBB_Anexo5;
        $this->arBarra = array();

        /*********************** CONSOLIDACAO */
        if ( $this->getConsolidacao() ) {

        #echo '<h2>CONSOLIDACAO </h2>'; exit;


            foreach ($chave as $parcela) {

                $inParcela++;

                $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarnePetropolis->setImagem("");
                $this->obRARRCarne->obRARRParcela->setCodParcela( $parcela["cod_parcela"] );
                $obErro = $this->obRARRCarne->obRARRParcela->listarParcelaCarne( $rsParcela );

                // instanciar mapeamento da função de calculo de juro e multa
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaParcelasReemissao.class.php');
                require_once(CAM_GT_ARR_MAPEAMENTO.'FARRCalculaJuroOrMultaParcelasReemissao.class.php');
                // retorna parcela com juro e multa aplicados
                $obCalculaParcelas = new FARRCalculaParcelasReemissao;
                // retorna valores de juro e multa que foram aplicados
                $obCalculaJM = new FARRCalculaJuroOrMultaParcelasReemissao;


                $arTmp = explode('/',$this->getVencimentoConsolidacao());
                $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaCalculaValoresParcelasReemissao($rsTmp,$stParametro1);

                $arValorNormal = explode ( "§", $rsTmp->getCampo('valor') );

                $this->arBarra['valor_documento'] = $nuValorTotal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $this->getNumeracaoConsolidacao();
                $this->obRCarnePetropolis->stNumeracao = $this->getNumeracaoConsolidacao();
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;
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

                $nuValorTotal += $arValorNormal[0];
                $nuValorNormal += $arValorNormal[1];
                $nuValorJuroNormal += $arValorNormal[3];
                $nuValorMultaNormal += $arValorNormal[2];
                $nuValorCorrecaoNormal += $arValorNormal[4];
            }


                $this->obRCarnePetropolis->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                $this->obRCarnePetropolis->setParcela ( "1/1" );
                $this->obRCarnePetropolis->setVencimento  ( $this->getVencimentoConsolidacao() );


                $this->obRCarnePetropolis->flValorMultaJuros = ( number_format(round($nuValorCorrecaoNormal+$nuValorMultaNormal+$nuValorJuroNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorJuros = ( number_format(round($nuValorJuroNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorMulta = ( number_format(round($nuValorMultaNormal,2),2,',',''));
                $this->obRCarnePetropolis->flValorOutros = ( number_format(round($nuValorCorrecaoNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValor       ( number_format(round($nuValorNormal,2),2,',',''));
                $this->obRCarnePetropolis->setValorTotal(number_format(round($nuValorTotal,2),2,',',''));

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

                $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
                //$this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 96;


        } else {

            foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
                $inParcela++;

                $this->obRCarnePetropolis->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem ); //logoCarne.png" ); //imagem mudar
                $this->obRCarnePetropolis->setImagem("");
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
                $stCorrecaoNormal = $arValorNormal[5];

                $this->obRCarnePetropolis->setNumeracao( (string) $rsParcela->getCampo( 'numeracao' ) );
                $this->arBarra['valor_documento'] = $nuValorTotal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;

                if ( $obErro->ocorreu() ) {
                    break;
                }
                if ($diffBaixa) {
                    $this->obRCarnePetropolis->setParcelaUnica ( true );
                    $this->obRCarnePetropolis->lblTitulo2        = ' ';
                    $this->obRCarnePetropolis->lblValorCotaUnica = 'VALOR TOTAL';
                    $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
                    $this->obRCarnePetropolis->setParcela ( $rsParcela->getCampo( 'info' ) );
                } else {
                    if ( $rsParcela->getCampo( 'nr_parcela' ) == 0 ) {
                        $this->obRCarnePetropolis->setParcelaUnica ( true );
                        $this->obRCarnePetropolis->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                        $this->obRCarnePetropolis->setValor        ( number_format($nuValorNormal,2,',','.') );
                        // Recuperar Desconto
                        include_once(CAM_GT_ARR_MAPEAMENTO."FARRParcentualDescontoParcela.class.php");
                        $obPercentual = new FARRParcentualDescontoParcela;
                        $obPercentual->executaFuncao($rsPercentual,"".$parcela["cod_parcela"].",'".$dtVencimento."'");

                        $this->obRCarnePetropolis->stObsVencimento = "Não receber após 60(sessenta) dias do vencimento.";
                        $this->obRCarnePetropolis->setParcela ( 'ÚNICA' );
                    } else {
                        $this->obRCarnePetropolis->stObsVencimento = "Não receber após 60(sessenta) dias do vencimento.";
                        $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                        $this->obRCarnePetropolis->setParcela( $rsParcela->getCampo( 'info' ));
                        $this->obRCarnePetropolis->setParcelaUnica( false );
                        $this->obRCarnePetropolis->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );

                        $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                        $boVenc1 = false;
                        $boVenc2 = false;
                        $boVenc3 = false;

                        if ($this->stLocal != "WEB") {
                            $stMes = $arTmp[1];
                            $arTmp = explode('/',$arVencimentos[0]);
                            if ($arTmp[1] >= $stMes) {
                                $stMes = $arTmp[1];
                                $boVenc1 = true;
                                $this->obRCarnePetropolis->setVencimento1 ( $arVencimentos[0] );
                                $arTmp = explode('/',$arVencimentos[1]);
                                if ($arTmp[1] >= $stMes) {
                                    $stMes = $arTmp[1];
                                    $boVenc2 = true;
                                    $this->obRCarnePetropolis->setVencimento2 ( $arVencimentos[1] );
                                    $arTmp = explode('/',$arVencimentos[2]);
                                    if ($arTmp[1] >= $stMes) {
                                        $boVenc3 = true;
                                        $this->obRCarnePetropolis->setVencimento3 ( $arVencimentos[2] );
                                    }
                                }
                            }
                            // converter vencimentos para formato americano

                            $arTmp = explode('/',$arVencimentos[0]);
                            $dtVencimento1 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $arTmp = explode('/',$arVencimentos[1]);
                            $dtVencimento2 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $arTmp = explode('/',$arVencimentos[2]);
                            $dtVencimento3 = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                            $stParametro2 = $stParametro.$dtVencimento1."'";
                            $stParametro3 = $stParametro.$dtVencimento2."'";
                            $stParametro4 = $stParametro.$dtVencimento3."'";

                            // valor, % de juro, % de multa para valor normal do carne --------------
                            // valor
                            // % de juro
                            //$obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'j'");
                            //$stJuroNormal = $rsTmp->getCampo('valor');

                            $this->obRCarnePetropolis->flValorJuros = number_format(round($stJuroNormal,2),2,',','');

                            // % de multa
    //                        $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'m'");
    //                        $stMultaNormal = $rsTmp->getCampo('valor');
                            $this->obRCarnePetropolis->flValorMulta = number_format(round($stMultaNormal,2),2,',','');

                            $this->obRCarnePetropolis->flValorOutros = number_format(round($stCorrecaoNormal,2),2,',','');
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 1 do carne --------------
                            // valor
                            if ($boVenc1 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro2);
                                $nuValor1 = $rsTmp->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'j'");
                                $stJuro1 = $rsTmp->getCampo('valor');
                                $this->obRCarnePetropolis->lblJuros2 = $stJuro1;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'m'");

                                $stMulta1 = $rsTmp->getCampo('valor');
                                $this->obRCarnePetropolis->lblMulta2 = $stMulta1;
                            } else {
                                $this->obRCarnePetropolis->lblJuros2 = "";
                                $this->obRCarnePetropolis->lblMulta2 = "";
                            }
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 2 do carne --------------
                            // valor
                            if ($boVenc2 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro3);
                                $nuValor2 = $rsTmp1->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro3.",'j'");

                                $stJuro2 = $rsTmp2->getCampo('valor');

                                $this->obRCarnePetropolis->lblJuros3 = $stJuro2;

                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro3.",'m'");
                                $stMulta2 = $rsTmp3->getCampo('valor');
                                $this->obRCarnePetropolis->lblMulta3 = $stMulta2;
                            } else {
                                $this->obRCarnePetropolis->lblJuros3 = "";
                                $this->obRCarnePetropolis->lblMulta3 = "";
                            }
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 3 do carne --------------
                            // valor
                            if ($boVenc3 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp1,$stParametro4);
                                $nuValor3 = $rsTmp1->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp2,$stParametro4.",'j'");

                                $stJuro3 = $rsTmp2->getCampo('valor');
                                $this->obRCarnePetropolis->lblJuros4 = $stJuro3;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro4.",'m'");
                                $stMulta3 = $rsTmp3->getCampo('valor');

                                $this->obRCarnePetropolis->lblMulta4 = $stMulta3;
                            } else {
                                $this->obRCarnePetropolis->lblJuros4 = "";
                                $this->obRCarnePetropolis->lblMulta4 = "";
                            }
                            //-----------------------------------------------------------------------

                            // repassa valores para pdf
                            $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));
                            if ($boVenc1 == true) {
                                $this->obRCarnePetropolis->setValor1      (number_format(round($nuValor1,2),2,',','.')) ;
                                if ($boVenc2 == true) {
                                    $this->obRCarnePetropolis->setValor2      (number_format(round($nuValor2,2),2,',','.')) ;
                                    if ($boVenc3 == true) {
                                        $this->obRCarnePetropolis->setValor3      (number_format(round($nuValor3,2),2,',','.')) ;
                                    }
                                }
                            }
                        } else {
                            $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));

                        }

                    }
                }

                $this->obRCarnePetropolis->flValorMultaJuros = ( number_format(round($stJuroNormal+$stMultaNormal+$stCorrecaoNormal, 2 ),2,',',''));
                $this->obRCarnePetropolis->setValorTotal( number_format(round($nuValorTotal,2),2,',','.') );
                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

                $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
                //$this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 96;

                if ($inCount == 2) {
                    $this->obRCarnePetropolis->novaPagina();
                    $inCount = 0;
                    $this->inVertical = 7;
                    $this->boPulaPagina = false;
                } else {
                    $this->boPulaPagina = true;
                    $inCount++;
                }

            }// fim foreach parcelas
        }

        //if ( ( $inSaltaPagina != count($arEmissao) ) && ( ( count($chave) != 2 ) && ( count($chave) != 3 ) ) ) {
        if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
            $this->obRCarnePetropolis->novaPagina();
        }
        $arGruposValidos = explode(',','101,102,10120, 10121, 10122, 10123, 10124, 10125, 10198, 10199, 10220, 10221, 10222, 10223, 10224, 10225, 10298,10299');
        if( in_array($this->obRCarnePetropolis->getCodDivida(),$arGruposValidos))
            $this->obRCarnePetropolis->drawComplemento($this->inHorizontal, $this->inVertical);

    } // fim foreach $arEmissao

    if ( Sessao::read( 'stNomPdf' ) )
        $stNome     = Sessao::read( 'stNomPdf' );
    else
        $stNome     = "Carne.pdf";

    if ( Sessao::read( 'stParamPdf' ) )
        $stParam    = Sessao::read( 'stParamPdf' );
    else
        $stParam    = "I";
    $this->obRCarnePetropolis->show($stNome,$stParam); // lanca o pdf
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

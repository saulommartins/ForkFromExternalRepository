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
  * Carne de ISS Mata de Sao Joao
  * Data de criação : 19/01/2007

  * @author Analista: Fábio Bertoldi
  * @author Programador: Fernando Piccini Cercato

  * @package URBEM

  Caso de uso: uc-05.03.11
*/

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebrabanCompensacaoBB-Anexo5.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
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

        // linhas verticais
        $this->Line( $x+48, $y+(17*$this->tamY), $x+48, $y+(46*$this->tamY) ); //nova do canhotinho

        $this->Line( $x+66+$inAlteracaoMata, $y+(6*$this->tamY), 66+$x+$inAlteracaoMata, $y+(11*$this->tamY) );

        $this->Line( $x+42+$inAlteracaoMata, $y+(23*$this->tamY), 42+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+65+$inAlteracaoMata, $y+(23*$this->tamY), 65+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+80+$inAlteracaoMata, $y+(23*$this->tamY), 80+$x+$inAlteracaoMata, $y+(35*$this->tamY) );
        $this->Line( $x+94+$inAlteracaoMata, $y+(23*$this->tamY), 94+$x+$inAlteracaoMata, $y+(35*$this->tamY) );

        $this->Line( $x+20, $y+(23*$this->tamY), 20+$x, $y+(29*$this->tamY) );

        $this->Line( $x+158, $y+(11*$this->tamY), 158+$x, $y+(63*$this->tamY) );

        $this->setFont ( 'Arial', 'B', 7 );
        $this->Text ( $x+38, $y+(11*$this->tamY), "MATA DE SÃO JOÃO" );
        $this->Text ( $x+38, $y+(13*$this->tamY)+0.5, "Sec. de Adm. e Fin." );
        $this->Text ( $x+38, $y+(16*$this->tamY), "EXERCÍCIO ".$this->getExercicio() );

        $this->setFont ( 'Arial', '', 6 );
        $this->Text ( $x+20.5, $y+(25*$this->tamY), "Inscrição" );
        $this->Text ( $x+20.5, $y+(28*$this->tamY), $this->inInscricao );

        $this->Text ( $x, $y+(19*$this->tamY), "Nosso Número" );
        $this->Text ( $x, $y+(22*$this->tamY), $this->stNumeracao );

        $this->Text ( $x, $y+(25*$this->tamY), "Parcela" );
        $this->Text ( $x, $y+(28*$this->tamY), $this->stParcela );

        $this->Text ( $x, $y+(43*$this->tamY), "Vencimento" );
        $stVnc = str_replace( "\n\r", " ", $this->stObsVencimento );
        $this->Text ( $x, $y+(45*$this->tamY), substr( $stVnc, 0, 45 ) );
        $this->Text ( $x, $y+(47*$this->tamY), substr( $stVnc, 45, 45 ) );
        $this->Text ( $x, $y+(49*$this->tamY), substr( $stVnc, 90, 45 ) );

        $this->Text ( $x+30, $y+(43*$this->tamY), $this->dtVencimento );

        $this->setFont ( 'Arial', '', 5 );
        $this->Text ( $x, $y+(48*$this->tamY), "MULTA 5% ATÉ 30 DIAS. 10% DE 30 A 60 DIAS E 15% SUPERIOR A 60 DIAS." );
        $this->Text ( $x, $y+(50*$this->tamY), "JUROS DE 1% AO MÊS = 0,003%" );

        $this->setFont ( 'Arial', '', 6 );
        $this->Text ( $x, $y+(31*$this->tamY), "Tributo" );
        $stTrb = str_replace( "\n\r", " ", $this->stTributo );
        $this->Text ( $x, $y+(33*$this->tamY), substr( $stTrb, 0, 45 ) );
        $this->Text ( $x, $y+(35*$this->tamY), substr( $stTrb, 45, 45 ) );
        $this->Text ( $x, $y+(37*$this->tamY), substr( $stTrb, 90, 45 ) );
        $this->Text ( $x, $y+(39*$this->tamY), substr( $stTrb, 135, 45 ) );
        $this->Text ( $x, $y+(41*$this->tamY), substr( $stTrb, 180, 45 ) );

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

        $stObs = str_replace( "\n\r", " ", $this->stObsVencimento.$this->stObservacao );

        $this->Text ( $x, $y+(65*$this->tamY), substr( $stObs, 0, 70 ) );
        $this->Text ( $x, $y+(67*$this->tamY), substr( $stObs, 70, 70 ) );
        $this->Text ( $x, $y+(69*$this->tamY), substr( $stObs, 140, 70 ) );
        $this->Text ( $x, $y+(71*$this->tamY), substr( $stObs, 210, 70 ) );
        $this->Text ( $x, $y+(73*$this->tamY), substr( $stObs, 280, 70 ) );
        $this->Text ( $x, $y+(75*$this->tamY), substr( $stObs, 350, 70 ) );
        $this->Text ( $x, $y+(77*$this->tamY), substr( $stObs, 420, 70 ) );
        $this->Text ( $x, $y+(79*$this->tamY), substr( $stObs, 490, 70 ) );

        $this->Text ( $x, $y+(53*$this->tamY), "Contribuinte" );
        $this->Text ( $x, $y+(55*$this->tamY), substr( $this->getNomCgm(), 0, 70 ) );

        $this->Text ( $x, $y+(58*$this->tamY), "Endereço do Imóvel" );
        $this->Text ( $x, $y+(60*$this->tamY), substr( $this->getRua(), 0, 70 ) );

        $this->setFont ( 'Arial', '', 6 );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(13*$this->tamY), "Local de Pagamento" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(16*$this->tamY), $this->stLocalPagamento );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(19*$this->tamY), "Cedente" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(22*$this->tamY), $this->stCedente );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(25*$this->tamY), "Data do Doc." );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(28*$this->tamY), $this->stDataDocumento );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(31*$this->tamY), "Uso do Banco" );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(37*$this->tamY), "Instruções" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(40*$this->tamY), substr( $stObs, 0, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(42*$this->tamY), substr( $stObs, 150, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(44*$this->tamY), substr( $stObs, 225, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(46*$this->tamY), substr( $stObs, 300, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(48*$this->tamY), substr( $stObs, 375, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(50*$this->tamY), substr( $stObs, 450, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(52*$this->tamY), substr( $stObs, 525, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(54*$this->tamY), substr( $stObs, 600, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(56*$this->tamY), substr( $stObs, 675, 75 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(58*$this->tamY), substr( $stObs, 750, 75 ) );

        $this->Text ( $x+27+$inAlteracaoMata, $y+(65*$this->tamY), "Sacado" );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(67*$this->tamY), substr( $this->getNomCgm(), 0, 120 ) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(69*$this->tamY), substr($this->getRua(), 0, 120) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(71*$this->tamY), substr($this->getRua(), 120, 120) );
        $this->Text ( $x+27+$inAlteracaoMata, $y+(73*$this->tamY), substr($this->getRua(), 240, 120) );

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

class RCarneDadosISSEstimadoMataSaoJoao extends RCarneDiversosPetropolis
{
    /**
     * @access public
     * @var String
     */
    public $stComposicaoCalculo ;
    public $stNomePrefeitura ;
    public $stSubTitulo ;
    public $stExercicio ;
    public $stCodigoLogradouro ;
    public $stNomeLogradouro ;
    public $stComplemento ;
    public $stQuadra ;
    public $stLote ;
    public $stDistrito ;
    public $stRegiao ;
    public $stCep ;
    public $stAliquota;
    public $stCidade ;
    public $stEstado ;
    public $stCamLogo;
    public $stInscricaoEconomica;
    public $stNumeroAviso;
    public $stTipoImposto;
    public $stRazaoSocial;
    public $stNomeFantasia;
    public $stCNPJ;
    public $stAtividade;
    public $stResponsavel;
    public $stParcelaUnica;
    public $stParcelaUm;
    public $stParcelaDois;
    public $stParcelaTres;
    public $stParcelaQuatro;
    public $stParcelaCinco;
    public $stParcelaSeis;
    public $stParcelaSete;
    public $stParcelaOito;
    public $stParcelaNove;
    public $stParcelaDez;
    public $stParcelaOnze;
    public $stParcelaDoze;
    public $stBaseCalculo;

    public function RCarneDadosISSEstimadoMataSaoJoao()
    {
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
        $this->stCamLogo = '../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/logoCarne.png';
    }
    public function desenhaCarne($x , $y)
    {
        $stComposição =
"                                                                        OBSERVAÇÕES\n\n
  * O pagamento deste carnê não quita débitos anteriores.\n\n
  * As parcelas vencidas estão sujeitas a acréscimos legais de multa e juros de mora descritos na Legislação Municipal em vigor.\n\n
  * O prazo para reclamação do lançamento será de 5 (cinco) dias contados da data de entrega do carnê constante no Protocolo da Prefeitura.\n\n
         - P A G A V E L  E M  Q U A L Q U E R  B A N C O  A T É  O  V E N C I M E N T O -    ";

        $inTamY = 1.14;
        /* Inicializa Fonte*/
        $this->setFont( 'Arial','',10 );

        /**
         * Retangulos
         */
        /* Retangulo da Composicação */
        $this->Rect( $x, $y, 189, 88*$inTamY );
        /* Retangulo Dados Cadastrais */
        $this->Rect( $x, $y + (88*$inTamY) + 8 , 189, 88*$inTamY );

        /* Composição do Calculo */
        $this->stComposicaoCalculo = $stComposição;
        $this->SetXY($x+10,$y+10);
        $this->setFont( 'Arial', '', 11 );
        $this->setLeftMargin(10);
        $this->Write( 3.5 , $this->stComposicaoCalculo );
        $this->setLeftMargin(0);

        $this->setFont( 'Arial','',5 );
        $this->Text( $x+  1 , $y+(68*$inTamY) , 'Quaisquer dúvidas, consulte o Setor de Tributos e Fiscalização, na Rua Luiz Antônio Garcez, s/nº - Centro - CEP:48.280-000 Mata de São João/BA, Tel/Fax(71)3635-3009/(71)96177231/(71)9957-6798.' );

        $deslocamento = 18;

        /**
         * Montar Estrutura dos Dados Cadastrais
         */
        /* Linhas Horizontais */
        $this->Line( $x , $y + $deslocamento +  (89*$inTamY) , $x +189, $y + $deslocamento +  (89*$inTamY) );

        $this->Line( $x , $y + $deslocamento +  (97*$inTamY) , $x +104, $y + $deslocamento +  (97*$inTamY) );

        $this->Line( $x , $y + $deslocamento + (112*$inTamY) , $x +104, $y + $deslocamento + (112*$inTamY) );
        $this->Line( $x , $y + $deslocamento + (121*$inTamY) , $x +104, $y + $deslocamento + (121*$inTamY) );

        $this->Line( $x , $y + ($deslocamento*2) + (141*$inTamY) , $x +104, $y + ($deslocamento*2) + (141*$inTamY) );

        /* Linhas Verticais */
        $this->Line( $x + 29 , $y + $deslocamento +  (89*$inTamY) , $x + 29 , $y + $deslocamento  +  (97*$inTamY));
        $this->Line( $x + 48 , $y + $deslocamento +  (89*$inTamY) , $x + 48 , $y + $deslocamento  +  (97*$inTamY));
        $this->Line( $x + 70 , $y + $deslocamento +  (89*$inTamY) , $x + 70 , $y + $deslocamento  +  (97*$inTamY));
        $this->Line( $x + 104 , $y + $deslocamento +  (89*$inTamY) , $x + 104 , $y + ($deslocamento*2) + 5 + (147*$inTamY)); //linha grande central

        $this->Line( $x + 48  , $y + ($deslocamento*2) + (141*$inTamY) , $x + 48 , $y + ($deslocamento*2) + 5 + (147*$inTamY)); //ultima linha

        /**
         * Titulos dos Dados
         */
        /* imagem*/
        $stExt = substr( $this->stCamLogo, strlen($this->stCamLogo)-3, strlen($this->stCamLogo) );
        $this->Image( $this->stCamLogo , $x+1 , $y + $deslocamento+(79*$inTamY)+1, 10 , 10 , $stExt );
        /* dados */
        $this->setFont( 'Arial','',5 );

        $this->Text( $x+  1 , $y + $deslocamento+(91*$inTamY) , 'INSCRIÇÃO CADASTRAL' );
        $this->Text( $x+ 30 , $y + $deslocamento+(91*$inTamY) , 'NÚMERO DE AVISO' );
        $this->Text( $x+ 49 , $y + $deslocamento+(91*$inTamY) , 'TIPO DE IMPOSTO' );
        $this->Text( $x+ 71 , $y + $deslocamento+(91*$inTamY) , 'EXERCÍCIO' );

        $this->Text( $x+  1 , $y + $deslocamento+(100*$inTamY) , 'CONTRIBUINTE' );
        $this->Text( $x+  1 , $y + $deslocamento+(114*$inTamY) , 'LOCAL DO ESTABELECIMENTO' );
        $this->Text( $x+  1 , $y + $deslocamento+(123*$inTamY) , 'ENDEREÇO DO CONTRIBUINTE' );
        $this->Text( $x+  1 , $y + ($deslocamento*2)+(143*$inTamY) , 'BASE DE CÁLCULO MENSAL' );
        $this->Text( $x+ 50 , $y + ($deslocamento*2)+(143*$inTamY) , 'ALÍQUOTA (%)' );

        $this->Text( $x+   121 , $y + $deslocamento+(91*$inTamY)  , 'DEMONSTRATIVO DAS PARCELAS' );
        $this->Text( $x+   130 , $y + $deslocamento+(95*$inTamY)  , 'Unica' );
        $this->Text( $x+   132 , $y + $deslocamento+(99*$inTamY)  , '01' );
        $this->Text( $x+   132 , $y + $deslocamento+(103*$inTamY) , '02' );
        $this->Text( $x+   132 , $y + $deslocamento+(107*$inTamY) , '03' );
        $this->Text( $x+   132 , $y + $deslocamento+(111*$inTamY) , '04' );
        $this->Text( $x+   132 , $y + $deslocamento+(115*$inTamY) , '05' );
        $this->Text( $x+   132 , $y + $deslocamento+(119*$inTamY) , '06' );
        $this->Text( $x+   132 , $y + $deslocamento+(123*$inTamY) , '07' );
        $this->Text( $x+   132 , $y + $deslocamento+(127*$inTamY) , '08' );
        $this->Text( $x+   132 , $y + $deslocamento+(131*$inTamY) , '09' );
        $this->Text( $x+   132 , $y + $deslocamento+(135*$inTamY) , '10' );
        $this->Text( $x+   132 , $y + $deslocamento+(139*$inTamY) , '11' );
        $this->Text( $x+   132 , $y + $deslocamento+(143*$inTamY) , '12' );

        /* mostrar dados */
        $this->setFont( 'Arial','B',9 );
        $this->Text( $x+44 , $y + $deslocamento+(83*$inTamY) , $this->stNomePrefeitura );

        $this->setFont( 'Arial','',7 );
        $this->Text( $x+50 , $y + $deslocamento+(88*$inTamY) , $this->stSubTitulo );

        $this->setFont( 'Arial','',5 );
        /* inscricao economica */
        $this->Text( $x+3 , $y + $deslocamento+(94*$inTamY) , $this->stInscricaoEconomica );

        /* numero de aviso */
        $this->Text( $x+30 , $y + $deslocamento+(94*$inTamY) , $this->stNumeroAviso );

        /* tipo de imposto */
        $this->Text( $x+51 , $y + $deslocamento+(94*$inTamY) ,$this->stTipoImposto );

        /* exercicio */
        $this->Text( $x+75 , $y + $deslocamento+(94*$inTamY) , $this->stExercicio );

        /* contribuinte */
        $this->Text( $x+1 , $y + $deslocamento+(104*$inTamY) , $this->stRazaoSocial );
        $this->Text( $x+1 , $y + $deslocamento+(108*$inTamY) , $this->stNomeFantasia );

        /* local do estabelecimento */
        $this->Text( $x+1 , $y + $deslocamento+(117*$inTamY) , $this->stNomeLogradouro.' '.$this->stComplemento );

        /* endereco do contribuinte */
        $this->Text( $x+1 , $y + $deslocamento+(128*$inTamY) , $this->stNomeLogradouro.' '.$this->stComplemento.' '.$this->stCep );
        $this->Text( $x+1 , $y + $deslocamento+(132*$inTamY) , $this->stCidade.' '.$this->stEstado );

        /* base de calculo mensal */
        $this->Text( $x+4 , $y + ($deslocamento*2)+(146*$inTamY) , $this->stBaseCalculo );

        /* aliquota */
        $this->Text( $x+54 , $y + ($deslocamento*2)+(146*$inTamY) , $this->stAliquota );

        /* Parcela Unica */
        $this->Text( $x+135 , $y + $deslocamento+(95*$inTamY) , $this->stParcelaUnica );

        /* Parcela 1 */
        $this->Text( $x+135 , $y + $deslocamento+(99*$inTamY) , $this->stParcelaUm );

        /* Parcela 2 */
        $this->Text( $x+135 , $y + $deslocamento+(103*$inTamY) , $this->stParcelaDois );

        /* Parcela 3 */
        $this->Text( $x+135 , $y + $deslocamento+(107*$inTamY) , $this->stParcelaTres );

        /* Parcela 4 */
        $this->Text( $x+135 , $y + $deslocamento+(111*$inTamY) , $this->stParcelaQuatro );

        /* Parcela 5 */
        $this->Text( $x+135 , $y + $deslocamento+(115*$inTamY) , $this->stParcelaCinco );

        /* Parcela 6 */
        $this->Text( $x+135 , $y + $deslocamento+(119*$inTamY) , $this->stParcelaSeis );

        /* Parcela 7 */
        $this->Text( $x+135 , $y + $deslocamento+(123*$inTamY) , $this->stParcelaSete );

        /* Parcela 8 */
        $this->Text( $x+135 , $y + $deslocamento+(127*$inTamY) , $this->stParcelaOito );

        /* Parcela 9 */
        $this->Text( $x+135 , $y + $deslocamento+(131*$inTamY) , $this->stParcelaNove );

        /* Parcela 10 */
        $this->Text( $x+135 , $y + $deslocamento+(135*$inTamY) , $this->stParcelaDez );

        /* Parcela 11 */
        $this->Text( $x+135 , $y + $deslocamento+(139*$inTamY) , $this->stParcelaOnze );

        /* Parcela 12 */
        $this->Text( $x+135 , $y + $deslocamento+(143*$inTamY) , $this->stParcelaDoze );
    }

    public function show($stNome = "Carne.pdf", $stOpcao="D")
    {
        $this->output($stNome,$stOpcao);
    }
}

class RCarneISSMataSaoJoao2011
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

/* setters */
function setHorizontal($valor) { $this->inHorizontal = $valor; }
function setVertical($valor) { $this->inVertical   = $valor; }
function setEmissao($valor) { $this->arEmissao    = $valor; }
function setBarra($valor) { $this->obBarra      = $valor; }
function setArBarra($valor) { $this->arBarra      = $valor; }
function setPulaPagina($valor) { $this->boPulaPagina = $valor; }

/* getters */
function getHorizontal() { return $this->inHorizontal;   }
function getVertical() { return $this->inVertical;     }
function getEmissao() { return $this->arEmissao;      }
function getBarra() { return $this->obBarra;        }
function getArBarra() { return $this->arBarra;        }
function getPulaPagina() { return $this->boPulaPagina;   }

/*
    * Metodo Construtor
    * @access Private
*/
function RCarneISSMataSaoJoao2011($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    //$obRProtocolo = new RProtocolo;
    //$obRCarnePetropolis     = new RCarnePetropolis;
}

function imprimirCarne($diffBaixa = FALSE)
{
    global $inCodFebraban;
    ;
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
    $this->obRCarnePetropolis = new RCarneDadosISSEstimadoMataSaoJoao();
    $this->obRCarnePetropolis->stCamLogo = CAM_FW_TEMAS."imagens/".$stNomeImagem;
    $this->obRCarnePetropolis->lblTitulo1 = "MATA DE SÃO JOÃO - Sec. de Adm. e Fin.";

    foreach ($this->arEmissao as $valor => $chave) {
        /* imprimir duas folhas com dados cadastrais */
        /* buscar informações para dados cadastrais*/

        include_once ( CAM_GT_ARR_MAPEAMENTO."TARRCarne.class.php" );
        $stFiltro = " AND ece.inscricao_economica = ". $chave[0]['inscricao'] ." AND ac.exercicio = ".$chave[0]['exercicio']." \n";

        $rsListaCarne = new RecordSet;

        $obTARRCarne = new TARRCarne;
        $obTARRCarne->recuperaDadosISSEstimativaMata( $rsListaCarne, $stFiltro, $chave[0]['cod_parcela'] );

        $flMaiorValor = 0;
        while ( !$rsListaCarne->Eof() ) {
            if ( ($rsListaCarne->getCampo("nr_parcela") != 0) && ($flMaiorValor < $rsListaCarne->getCampo("valor")) )
                $flMaiorValor = $rsListaCarne->getCampo("valor");

            $rsListaCarne->proximo();
        }

        $rsListaCarne->setPrimeiroElemento();
        $rsListaCarne->addFormatacao ('valor','NUMERIC_BR');

        $arDadosParcelas = array();
        $inTotalParcelas = 0;
        if ( !$rsListaCarne->Eof() ) {
            while ( !$rsListaCarne->Eof() ) {
                $arDadosParcelas[$rsListaCarne->getCampo("nr_parcela")]["data"] = $rsListaCarne->getCampo("vencimento");
                $arDadosParcelas[$rsListaCarne->getCampo("nr_parcela")]["valor"] = $rsListaCarne->getCampo("valor");

                $rsListaCarne->proximo();
                $inTotalParcelas++;
            }
        }

        $rsListaCarne->setPrimeiroElemento();

        $this->obRCarnePetropolis->stParcelaUnica = $arDadosParcelas[0]["data"].' R$ '.$arDadosParcelas[0]["valor"]; //'05/02/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaUm = $arDadosParcelas[1]["data"].' R$ '.$arDadosParcelas[1]["valor"]; //'05/02/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaDois = $arDadosParcelas[2]["data"].' R$ '.$arDadosParcelas[2]["valor"]; //'05/03/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaTres = $arDadosParcelas[3]["data"].'  R$ '.$arDadosParcelas[3]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaQuatro = $arDadosParcelas[4]["data"].'  R$ '.$arDadosParcelas[4]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaCinco = $arDadosParcelas[5]["data"].'  R$ '.$arDadosParcelas[5]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaSeis = $arDadosParcelas[6]["data"].'  R$ '.$arDadosParcelas[6]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaSete = $arDadosParcelas[7]["data"].'  R$ '.$arDadosParcelas[7]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaOito = $arDadosParcelas[8]["data"].'  R$ '.$arDadosParcelas[8]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaNove = $arDadosParcelas[9]["data"].'  R$ '.$arDadosParcelas[9]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaDez = $arDadosParcelas[10]["data"].'  R$ '.$arDadosParcelas[10]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaOnze = $arDadosParcelas[11]["data"].'  R$ '.$arDadosParcelas[11]["valor"]; //'05/04/2007  R$ 140,87';
        $this->obRCarnePetropolis->stParcelaDoze = $arDadosParcelas[12]["data"].'  R$ '.$arDadosParcelas[12]["valor"]; //'05/04/2007  R$ 140,87';

        $this->obRCarnePetropolis->stInscricaoEconomica = $rsListaCarne->getCampo("inscricao_economica"); //'540857';
        $this->obRCarnePetropolis->stRazaoSocial = $rsListaCarne->getCampo("razao_social"); //'JOSE DIONIZIO DOS SANTOS';
        $this->obRCarnePetropolis->stNomeFantasia = $rsListaCarne->getCampo("nome_fantasia"); //'DIONIZIO MATERIAIS DE CONSTRUCAO';
        $this->obRCarnePetropolis->stCNPJ = $rsListaCarne->getCampo("cpf_cnpj"); //'09.018.561/0001-80';
        $this->obRCarnePetropolis->stAtividade = $rsListaCarne->getCampo("atividade"); //'Ajudante de pedreiro';
        $this->obRCarnePetropolis->stResponsavel = $rsListaCarne->getCampo("resposavel"); //'Capitao Jonas Vasconcelus';

        /* setar todos os dados necessarios */
        $this->obRCarnePetropolis->stNomePrefeitura = 'PREFEITURA MUNICIPAL DE MATA DE SÃO JOÃO';
        $this->obRCarnePetropolis->stSubTitulo = 'IMPOSTO SOBRE SERVIÇOS DE QUALQUER NATUREZA';
        $this->obRCarnePetropolis->stExercicio  = $chave[0]["exercicio"];

        $arEndereco = explode ( "§", $rsListaCarne->getCampo("endereco") );

        $this->obRCarnePetropolis->stNumeroAviso = $rsListaCarne->getCampo("numeracao_carne"); //alterar aki por numero da unica

        $this->obRCarnePetropolis->stTipoImposto = "ISS-ESTIMATIVA";
        $this->obRCarnePetropolis->stNomeLogradouro  = $arEndereco[0].'   '.$arEndereco[2].'   '.$arEndereco[3]; //'AV DO FAROL 50.003' ;
        $this->obRCarnePetropolis->stComplemento  = $arEndereco[4]; //'CONDOMINIO SOLAR DOS ARCOS' ;
        $this->obRCarnePetropolis->stCep  = $arEndereco[6]; //'48.820-000' ;
        $this->obRCarnePetropolis->stCidade = $arEndereco[8];
        $this->obRCarnePetropolis->stEstado = $arEndereco[10];

        $flMaiorValor = ( $flMaiorValor * 100 ) / 5;
        $this->obRCarnePetropolis->stBaseCalculo = number_format( $flMaiorValor, 2, ',', '.' ); //alterar aqui pelo valor base de calculo
        $this->obRCarnePetropolis->stAliquota = '5,00';

        $this->obRCarnePetropolis->desenhaCarne(10,20);
        $this->obRCarnePetropolis->novaPagina();
        $inSaltaPagina++;

        $this->obRCarnePetropolis->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarneDiverso( $rsGeraCarneCabecalho );
        if ( $obErro->ocorreu() ) {
            break;
        }
        $this->obRCarnePetropolis->setObservacaoL1 ('Créditos: ');

        while ( !$rsGeraCarneCabecalho->eof() ) {
            // montagem cabecalho (protocolo)
            $this->obRCarnePetropolis->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarnePetropolis->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarnePetropolis->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarnePetropolis->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarnePetropolis->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );

            $arData = explode( "§", $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) );
            $this->obRCarnePetropolis->setRua               ( $arData[0].' '.$arData[2] );
            $this->obRCarnePetropolis->setNumero            ( $arData[3]                );
            $this->obRCarnePetropolis->setComplemento       ( $arData[4]                );
            $this->obRCarnePetropolis->setCidade            ( $arData[8]                );
            $this->obRCarnePetropolis->setUf                ( $arData[10]               );
            $this->obRCarnePetropolis->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
            $this->obRCarnePetropolis->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarnePetropolis->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarnePetropolis->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarnePetropolis->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarnePetropolis->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarnePetropolis->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarnePetropolis->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            $this->obRCarnePetropolis->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo')." - ".$rsGeraCarneCabecalho->getCampo( 'descricao' )              );
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

            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        $this->obRCarnePetropolis->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarnePetropolis->getTaxaLimpezaAnual() );
        // formatar
        $this->obRCarnePetropolis->setValorAnualReal    ( number_format($this->obRCarnePetropolis->getValorAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setTaxaLimpezaAnual  ( number_format($this->obRCarnePetropolis->getTaxaLimpezaAnual(),2,',','.') );
        $this->obRCarnePetropolis->setImpostoAnualReal  ( number_format($this->obRCarnePetropolis->getImpostoAnualReal(),2,',','.') );
        $this->obRCarnePetropolis->setValorTributoReal  ( number_format($this->obRCarnePetropolis->getValorTributoReal(),2,',','.') );

        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 8;

        $this->obBarra = new RCodigoBarraFebrabanCompensacaoBB_Anexo5;
        $this->arBarra = array();
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
                    $this->obRCarnePetropolis->setObservacaoL1 ( 'Cota Única com '.$rsPercentual->getCampo('valor').'% de desconto.' );                      ;
                    $this->obRCarnePetropolis->setObservacaoL2 ( 'Desconto não incide sobre a Taxa de Coleta de Lixo' );
                    $this->obRCarnePetropolis->setObservacaoL3 ( 'Não receber após o vencimento.' );
                    $this->obRCarnePetropolis->setObservacao ( 'Não receber após o vencimento.' );
                    $this->obRCarnePetropolis->setParcela ( 'ÚNICA' );
                } else {
                    $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ),4);
                    $this->obRCarnePetropolis->setParcela( $rsParcela->getCampo( 'info' ));
                    $this->obRCarnePetropolis->setParcelaUnica( false );
                    $this->obRCarnePetropolis->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarnePetropolis->setObservacao ( '' );//setado valor '' pois ficava armazenado o valor anterior
                }
            }

            $this->obRCarnePetropolis->setValor       (number_format(round($nuValorNormal,2),2,',','.'));

            $this->obRCarnePetropolis->flValorJuros = number_format(round($stJuroNormal,2),2,',','.');
            $this->obRCarnePetropolis->flValorMulta = number_format(round($stMultaNormal,2),2,',','.');
            $this->obRCarnePetropolis->flValorOutros = number_format(round($stCorrecaoNormal,2),2,',','.');
            $this->obRCarnePetropolis->flValorMultaJuros = ( number_format(bcadd($stJuroNormal, $stMultaNormal, 2),2,',',''));

            $this->obRCarnePetropolis->setValorTotal   ( number_format(round($nuValorTotal,2),2,',','.'));
            $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
            $this->obRCarnePetropolis->setBarCode( $this->arCodigoBarra['codigo_barras'] );
            $this->obRCarnePetropolis->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );

            $this->obRCarnePetropolis->drawCarne( $this->inHorizontal, $this->inVertical );
            //$this->obRCarnePetropolis->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
            $this->obRCarnePetropolis->setPicote( $this->inHorizontal, $this->inVertical );
            $this->inVertical += 96;
            if ( ( $inParcela == 3 ) || ( $inCount == 3 ) ) {
                $this->obRCarnePetropolis->novaPagina();
                $inCount = 0;
                $this->inVertical = 7;
                $this->boPulaPagina = false;
            } else {
                $this->boPulaPagina = true;
            }
            $inCount++;
        }// fim foreach parcelas

//*/
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

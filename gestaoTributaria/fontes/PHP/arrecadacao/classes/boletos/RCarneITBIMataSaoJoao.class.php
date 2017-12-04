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
  * Carnê de ITBI para Mata Sao Joao
  * Data de criação : 09/01/2006

  * @author Analista: Fábio Bertoldi
  * @author Programador: Tonismar R. Bernardo

  * @package URBEM

  Caso de uso: uc-5.3.11
**/

include_once ( CAM_GT_ARR_CLASSES."boletos/RCodigoBarraFebrabanCompensacaoBB-Anexo5.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRCarne.class.php" );
include_once ( CAM_GT_ARR_NEGOCIO."RARRConfiguracao.class.php" );
include_once ( CAM_GT_ARR_MAPEAMENTO."TARRParcela.class.php" );
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
    public $stQuadra;
    public $stLote;
    public $stCondomino;

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
            $this->Text   ( 8    , 29.5+$this->inTamY, $this->lblContribuinte  );
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
        $this->Text   ( 34   , 64+$this->inTamY  , "QUADRA"                );
        $this->Text   ( 60   , 64+$this->inTamY  , "LOTE"                  );
        $this->Text   ( 115.5, 64+$this->inTamY  , $this->lblVlTribReal    );
        $this->Text   ( 163  , 64+$this->inTamY  , $this->lblImpAnualReal  );

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 8    , 73+$this->inTamY  ,"REQUERIMENTO DE I.T.I.V." );
        } else {
            $this->Text   ( 8    , 73+$this->inTamY  , $this->lblObservacao    );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 115.5, 73+$this->inTamY  , "TAXA DE EXPEDIENTE"    );
        } else {
            $this->Text   ( 115.5, 73+$this->inTamY  , $this->lblLimpAnualRl   );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 163  , 73+$this->inTamY  , $this->lblTxAverbacao );
            $this->Text   ( 163  , 82+$this->inTamY  , $this->lblTotalAnualRl  );
        } else {
            $this->Text   ( 163  , 73+$this->inTamY  , $this->lblTotalAnualRl  );
            $this->Text   ( 163  , 82+$this->inTamY  , $this->lblTotalLancado  );
        }

        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            //$this->Text   ( 115.5, 82+$this->inTamY  , 'MULTA DE MORA'    );
        } else {
            $this->Text   ( 115.5, 82+$this->inTamY  , $this->lblReferencia    );
        }

        $this->setFont('Arial',''  , 5                 );
        $this->Text   ( 8    , 92+$this->inTamY  , $this->lblUrbem       );

        if ($this->stLoginUsuario != "" && $this->stCodUsuario != "") {
            $this->Text   ( 115.5, 92+$this->inTamY  , $this->stCodUsuario." - ".$this->stLoginUsuario );
        }

        /* Fim do layout do quadrado superior */
    }

    /* Posicionamento das variáveis */
    public function posicionaVariaveisProtocolo($inCodLancamento)
    {
        $this->setFont('Arial', 'b', 7 );
        if ( !Sessao::read( 'itbi_observacao' ) ) {
            $this->Text   ( 8     , 34+$this->inTamY   , strtoupper($this->stNomCgm) );
        } else {
            require_once(CAM_GT_ARR_MAPEAMENTO."TARRImovelVVenal.class.php");
            require_once(CAM_GT_CIM_MAPEAMENTO."TCIMTransferenciaAdquirente.class.php");
            $obImovelVVenal = new TARRImovelVVenal;
            $filtro = "and inscricao_municipal = ".$this->inInscricao;
            $obImovelVVenal->recuperaMensagemItbi($rsItbi,$filtro, "", "", $inCodLancamento, $this->inInscricao );

            $obTransfAdquirente = new TCIMTransferenciaAdquirente;
            $obTransfAdquirente->setCodLancamento($inCodLancamento);
            $obTransfAdquirente->recuperaAdquirentes($rsAdquirentes);

            foreach ($rsItbi->arElementos as $key => $field) {
                if ($rsAdquirentes->getCampo('cod_transferencia') == $field['cod_transferencia']) {
                    $stCgmAdquirente = $field['adquirinte'];
                    break;
                }
            }

            if (Sessao::read('Adquirentes') != '') {
                $arAdquirintes = Sessao::read('Adquirentes');
                foreach ($rsItbi->arElementos as $campo  => $chave) {
                    foreach ($arAdquirintes as $campo2) {
                        if ($campo2['codigo'] == $chave['numcgm']) {
                            $this->stAdquirente = $chave['adquirinte'];
                        }
                    }
                }
            } else {
                if ($stCgmAdquirente != '') {
                    $this->stAdquirente = $stCgmAdquirente;
                } else {
                    $this->stAdquirente = $rsItbi->getCampo('adquirinte');
                }
            }

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

        $this->Text   ( 8     , 53+$this->inTamY   , strtoupper($this->stRua.' '.$this->stNumero) );
        $this->Text   ( 8     , 57+$this->inTamY   , strtoupper($this->stComplemento." - ".$this->stNomBairro) );
        $this->Text   ( 8     , 60+$this->inTamY   , strtoupper($this->stCondomino) );

        $this->Text   ( 145   , 52.5+$this->inTamY , strtoupper($this->flAreaTerreno) );
        $this->Text   ( 185   , 52.5+$this->inTamY , strtoupper($this->flAreaEdificada) );

        $this->Text   ( 130   , 60.5+$this->inTamY , strtoupper($this->stUtilizacaoImovel) );
        $this->setFont('Arial', 'b'  , 6 );
        $this->Text   ( 165   , 60.5+$this->inTamY , strtoupper(substr($this->stTributo,0,28)) );
        $this->Text   ( 165   , 68.5+$this->inTamY , strtoupper(substr($this->stTributo2,0,28)) );
        $this->setFont('Arial', 'b', 7 );
        $this->Text   ( 90    , 68+$this->inTamY   , strtoupper($this->stExercicio)   );

        $this->Text   ( 38   , 68+$this->inTamY  , strtoupper($this->stQuadra) );
        $this->Text   ( 64   , 68+$this->inTamY  , strtoupper($this->stLote) );

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

            if (Sessao::read('Adquirentes') != '') {
                foreach ($rsItbi->arElementos as $campo  => $chave) {
                    foreach ($arAdquirintes as $campo2) {
                        if ($campo2['codigo'] == $chave['numcgm']) {
                            $itbiBase_calculo = $chave['base_calculo'];
                            $itbiValor_financiado = $chave['valor_financiado'];
                            $itbiCod_natureza = $chave['cod_natureza'];
                            $itbiDescricao = $chave['descricao'];
                            $itbiCod_processo = $chave['cod_processo'];
                            $itbiExercicio = $chave['exercicio'];
                        }
                    }
                }
            } else {
                $itbiBase_calculo = $rsItbi->getCampo('base_calculo');
                $itbiValor_financiado = $rsItbi->getCampo('valor_financiado');
                $itbiCod_natureza = $rsItbi->getCampo('cod_natureza');
                $itbiDescricao = $rsItbi->getCampo('descricao');
                $itbiCod_processo = $rsItbi->getCampo('cod_processo');
                $itbiExercicio = $rsItbi->getCampo('exercicio');
            }

            $stObsL3 =    "Base de Calculo  : ".$itbiBase_calculo."      ITIV: ".$this->flImpostoAnualReal;
            $stObsL4 =    "Valor Financiado : ".$itbiValor_financiado;

            $stObsL5 =    "Natureza de Transferência: ".$itbiCod_natureza." - ".$itbiDescricao;
            if ( $itbiCod_processo )
                $stObsL7 =    "Processo       : ".$itbiCod_processo."/".$itbiExercicio;

            $this->Text   ( 8     , 75.5+$this->inTamY , $stObsL3 );
            $this->Text   ( 8     , 78+$this->inTamY   , $stObsL4 );
            $this->Text   ( 8     , 80.5+$this->inTamY , $stObsL5 );
            $this->Text   ( 8     , 83+$this->inTamY   , $stObsL6 );
            $this->Text   ( 8     , 85.5 +$this->inTamY, $stObsL7 );

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
            $this->Text   ( 145   , 68+$this->inTamY   , strtoupper($this->flValorTributoReal) );

        $this->Text   ( 183   , 68+$this->inTamY   , strtoupper($this->flImpostoAnualReal ) );
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $this->Text   ( 145   , 76.5+$this->inTamY , $rsItbi->getCampo('taxa') );
            $this->Text   ( 145   , 85.5+$this->inTamY , $rsItbi->getCampo('multa') );
            $this->Text   ( 183   , 76.5+$this->inTamY , $this->flTxAverbacao       );
        } else {
            $this->Text   ( 145   , 76.5+$this->inTamY , strtoupper($this->flTaxaLimpezaAnual) );
        }
        if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
            $vlrTaxa = str_replace(',','.',str_replace('.','',$rsItbi->getCampo('taxa')));
            $vlrImp  = str_replace(',','.',str_replace('.','',$this->flValorAnualReal));
            $vlrAnual = $vlrImp+$vlrTaxa;

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

class RCarne extends RProtocolo
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
            $this->Text   ( ($x+1)  , ($y+(77*$inTamY))   , 'PAGÁVEL EM QUALQUER AGÊNCIA BANCÁRIA');
            $this->Text   ( ($x+1)  , ($y+(80.5*$inTamY)) , 'ATÉ O VENCIMENTO');
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

class RCarneITBIMataSaoJoao
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
function setConsolidacao($valor) { $this->boConsolidacao = $valor; }
function setVencimentoConsolidacao($valor) { $this->dtVencimentoConsolidacao = $valor; }
function setNumeracaoConsolidacao($valor) { $this->stNumeracaoConsolidacao = $valor; }

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
function RCarneITBIMataSaoJoao($arEmissao, $horizontal = 7, $vertical = 95)
{
    $this->obRARRCarne      = new RARRCarne;
    $this->arEmissao        = $arEmissao;
    $this->inHorizontal     = $horizontal;
    $this->inVertical       = $vertical;
    $this->boConsolidacao = false;
    $obRCarne     = new RCarne;
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
    Sessao::write( 'itbi_observacao', 'sim' );
    include_once ( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $obTAdministracaoConfiguracao = new TAdministracaoConfiguracao;
    $stFiltro = " WHERE exercicio = '".Sessao::getExercicio()."' AND parametro = 'logotipo' ";
    $obTAdministracaoConfiguracao->recuperaTodos( $rsListaImagens, $stFiltro );

    $stNomeImagem = $rsListaImagens->getCampo("valor");
    $inSaltaPagina = "";
    $this->obRCarne = new RCarne;
    $this->obRCarne->configuraProtocolo();

    foreach ($this->arEmissao as $valor => $chave) {
        $inSaltaPagina++;
        $this->obRCarne->setImagem(CAM_FW_TEMAS."imagens/".$stNomeImagem );
        $this->obRARRCarne->obRARRParcela->roRARRLancamento->setCodLancamento( $valor );
        if (strpos($chave[0]["numcgm"], '/') !== false) {
            $arCgm = preg_split( '/\//', $chave[0]["numcgm"]);
            $this->obRARRCarne->inCodContribuinteInicial = $arCgm[0];
        } else {
            $this->obRARRCarne->inCodContribuinteInicial = $chave[0]["numcgm"];
        }
        $this->obRARRCarne->stExercicio = $chave[0]["exercicio"];
        $obErro = $this->obRARRCarne->reemitirCarne( $rsGeraCarneCabecalho );
        if ( $obErro->ocorreu() ) {
            break;
        }

        //include_once ( CAM_GA_CGM_MAPEAMENTO."TCGM.class.php" );

        while ( !$rsGeraCarneCabecalho->eof() ) {

            /* montagem cabecalho (protocolo) */
            $this->obRCarne->stCondomino = $rsGeraCarneCabecalho->getCampo( 'condominio' );
            $this->obRCarne->stQuadra = $rsGeraCarneCabecalho->getCampo( 'numero_quadra' );
            $this->obRCarne->stLote = $rsGeraCarneCabecalho->getCampo( 'numero_lote' );

            $this->obRCarne->setCt                ( $rsGeraCarneCabecalho->getCampo( 'ano_aquisicao' )          );
            $this->obRCarne->setCa                ( $rsGeraCarneCabecalho->getCampo( 'ca' )                     );
            $this->obRCarne->setCep               ( $rsGeraCarneCabecalho->getCampo( 'cep')                     );
            $this->obRCarne->setExercicio         ( $rsGeraCarneCabecalho->getCampo( 'exercicio' )              );
            $this->obRCarne->setNomCgm            ( $rsGeraCarneCabecalho->getCampo( 'nom_cgm' )                );
            $this->obRCarne->setRua               ( str_replace ( "Não Informado ", "", $rsGeraCarneCabecalho->getCampo( 'nom_logradouro' ) )  );
            $this->obRCarne->setNumero            ( $rsGeraCarneCabecalho->getCampo( 'numero' )                 );
            $this->obRCarne->setComplemento       ( $rsGeraCarneCabecalho->getCampo( 'complemento' )            );
            $this->obRCarne->setCidade            ( $rsGeraCarneCabecalho->getCampo( 'nom_municipio' )          );
            $this->obRCarne->setUf                ( $rsGeraCarneCabecalho->getCampo( 'sigla_uf' )               );
            $this->obRCarne->setInscricao         ( str_pad($rsGeraCarneCabecalho->getCampo( 'inscricao_municipal' ),strlen( $stMascaraInscricao ), '0', STR_PAD_LEFT) );
            $this->obRCarne->setCtmDci            ( $rsGeraCarneCabecalho->getCampo( 'ctm_dci' )                );
            $this->obRCarne->setCodLogradouro     ( $rsGeraCarneCabecalho->getCampo( 'cod_logradouro' )         );
            $this->obRCarne->setDistrito          ( $rsGeraCarneCabecalho->getCampo( 'distrito' )               );
            $this->obRCarne->setProcessamento     ( $rsGeraCarneCabecalho->getCampo( 'data_processamento' )     );
            $this->obRCarne->setAreaTerreno       ( $rsGeraCarneCabecalho->getCampo( 'area_real' )              );
            $this->obRCarne->setAreaEdificada     ( $rsGeraCarneCabecalho->getCampo( 'area_edificada' )         );
            $this->obRCarne->setUtilizacaoImovel  ( $rsGeraCarneCabecalho->getCampo( 'utilizacao' )             );
            $this->obRCarne->setTributo           ( $rsGeraCarneCabecalho->getCampo( 'descricao' )              );
            $this->obRCarne->setValorTributoReal  ( $rsGeraCarneCabecalho->getCampo( 'valor_venal_total' )      );
            $this->obRCarne->setObservacao        ( wordwrap($rsGeraCarneCabecalho->getCampo('observacao' ),40,chr(13)) );
            $this->obRCarne->setNomBairro         ( $rsGeraCarneCabecalho->getCampo( 'nom_bairro' )             );
            $this->obRCarne->setCodDivida         ( $rsGeraCarneCabecalho->getCampo( 'cod_grupo' )              );
            $this->obRCarne->setEndEntrega        ( $rsGeraCarneCabecalho->getCampo('enderecoentrega')               );
            $this->obRCarne->setTotalLancado      ( $rsGeraCarneCabecalho->getCampo( 'valor_lancado' )          );
            if (preg_match('/LIMPEZA.*/i',$rsGeraCarneCabecalho->getCampo( 'descricao_credito' ) ) ) {
                $this->obRCarne->setTaxaLimpezaAnual  ( $rsGeraCarneCabecalho->getCampo( 'valor' )              );
            } else {
                $flImpostoAnualReal = $rsGeraCarneCabecalho->getCampo( 'valor' );
                $this->obRCarne->setImpostoAnualReal  ( $flImpostoAnualReal                                     );
            }

            $this->obRCarne->flTxAverbacao = $rsGeraCarneCabecalho->getCampo( 'aliquota' );

            $this->obRCarne->setReferencia        ( ""                                                          );
            $this->obRCarne->setNumeroPlanta      ( ""                                                          );
            $rsGeraCarneCabecalho->proximo();

        } //fim do loop de reemitirCarne
        if ($this->obRCarne->flTxAverbacao > 0) {
            $flImpostoAnualReal +=  $this->obRCarne->flTxAverbacao;
            $this->obRCarne->flTxAverbacao = number_format( $this->obRCarne->flTxAverbacao , 2 , ',' , '.' );
        }
        $this->obRCarne->setValorAnualReal        ( $flImpostoAnualReal + $this->obRCarne->getTaxaLimpezaAnual() );

        // formatar campos
        $this->obRCarne->setAreaTerreno       ( number_format($this->obRCarne->getAreaTerreno(),2,',','.') );
        $this->obRCarne->setAreaEdificada     ( number_format($this->obRCarne->getAreaEdificada(),2,',','.') );
        $this->obRCarne->setValorAnualReal    ( number_format($this->obRCarne->getValorAnualReal(),2,',','.') );
        $this->obRCarne->setTaxaLimpezaAnual  ( number_format($this->obRCarne->getTaxaLimpezaAnual(),2,',','.') );
        $this->obRCarne->setImpostoAnualReal  ( number_format($this->obRCarne->getImpostoAnualReal(),2,',','.') );
        $this->obRCarne->setValorTributoReal  ( number_format($this->obRCarne->getValorTributoReal(),2,',','.') );
        $this->obRCarne->drawProtocolo();
        $this->obRCarne->posicionaVariaveisProtocolo($valor);

        $inParcela = $inCount = "";

        $this->inHorizontal = 7;
        $this->inVertical = 125;

        $this->obBarra = new RCodigoBarraFebrabanCompensacaoBB_Anexo5;
        $this->arBarra = array();

        if ( $this->getConsolidacao() ) {

            $nuValorNormalTotal = 0.00;

            foreach ($chave as $parcela) {

                $inParcela++;

                $this->obRCarne->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarne->setImagem("");
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
                $arTmp = explode ( '/', $rsParcela->getCampo( 'vencimento' ) );
                $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
                $nuValorNormal = $rsTmp->getCampo('valor');

                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;

                $this->obRCarne->setNumeracao($rsParcela->getCampo('numeracao'));

                $nuValorNormalTotal += $nuValorNormal;
            }

                $this->obRCarne->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                $this->obRCarne->setParcela ( "1/1" );
                $this->obRCarne->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );
                $this->obRCarne->lblJuros1 = '-';
                $this->obRCarne->lblMulta1 = '-';
                $this->obRCarne->setValor       ( number_format(round($nuValorNormalTotal,2),2,',',''));

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarne->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarne->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );
                $this->obRCarne->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarne->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarne->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 95;

        } else {

            foreach ($chave as $parcela) { // impressao das parcelas selecionadas para cada codigo de lancamento
                $inParcela++;
                $this->obRCarne->setImagemCarne( CAM_FW_TEMAS."imagens/".$stNomeImagem );
                $this->obRCarne->setImagem("");
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
                $arTmp = explode ( '/', $rsParcela->getCampo( 'vencimento' ) );
                $dtVencimento = $arTmp[2].'-'.$arTmp[1].'-'.$arTmp[0];

                // parametro padrao
                $stParametro  = "'".$rsParcela->getCampo('numeracao')."',".$this->obRARRCarne->stExercicio;
                $stParametro .= ",".$parcela["cod_parcela"].",'";

                // monta paramentros com as datas
                $stParametro1 = $stParametro.$dtVencimento."'";

                // valor atualizado
                $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
                $nuValorNormal = $rsTmp->getCampo('valor');

                $this->arBarra['valor_documento'] = $nuValorNormal;
                $this->arBarra['fator_vencimento'] = (string) $rsParcela->getCampo( 'fator_vencimento' );
                $this->arBarra['nosso_numero'] = (string) $rsParcela->getCampo( 'numeracao' );
                $this->obRCarnePetropolis->stNumeracao = $rsParcela->getCampo( 'numeracao' );
                $this->arBarra['convenio'] = 960663;
                $this->arBarra['tipo_moeda'] = 9;

                $this->obRCarne->setNumeracao($rsParcela->getCampo('numeracao'));

                if ( $obErro->ocorreu() ) {
                    break;
                }
                if ( Sessao::read( 'itbi_observacao' ) == 'sim') {
                    $rsParcela->setCampo( 'nr_parcela', 0 ) ;
                }
                if ($diffBaixa) {
                    $this->obRCarne->setParcelaUnica ( true );
                    $this->obRCarne->lblTitulo2        = ' ';
                    $this->obRCarne->lblValorCotaUnica = 'VALOR TOTAL';
                    $this->obRCarne->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );
                    $this->obRCarne->setValor        ( number_format($nuValorNormal,2,',','') );
                    $this->obRCarne->setObservacaoL1 ( 'Não receber após o vencimento. ' );
                    $this->obRCarne->setObservacaoL2 ( ' ' );
                    $this->obRCarne->setObservacaoL3 ( ' ' );
                    $this->obRCarne->setParcela ( $rsParcela->getCampo( 'info' ) );
                } else {

                    if ( $rsParcela->getCampo( 'nr_parcela' ) == 0 ) {
                        $this->obRCarne->setParcelaUnica ( true );
                        $this->obRCarne->setVencimento   ( $rsParcela->getCampo( 'vencimento' ) );

                        $rsTmp->addFormatacao ('valor','NUMERIC_BR');
                        $this->obRCarne->setValor        ( $rsTmp->getCampo('valor') );
                        /**
                        * Recuperar Desconto
                        */
                        include_once(CAM_GT_ARR_MAPEAMENTO."FARRParcentualDescontoParcela.class.php");
                        $obPercentual = new FARRParcentualDescontoParcela;
                        $obPercentual->executaFuncao($rsPercentual,"".$parcela["cod_parcela"].",'".$dtVencimento."'");
                        $this->obRCarne->setObservacaoL1 ( 'Cota Única com '.$rsPercentual->getCampo('valor').'% de desconto.' );
                        $this->obRCarne->setObservacaoL2 ( 'Desconto não incide sobre a Taxa de Coleta de Lixo' );
                        $this->obRCarne->setObservacaoL3 ( 'Não receber após o vencimento.' );
                        $this->obRCarne->setParcela ( 'ÚNICA' );
                    } else {
                        $arVencimentos = array();
                        $arVencimentos = $this->geraParcelas($rsParcela->getCampo( 'vencimento' ), 4);
                        $this->obRCarne->setParcela( $rsParcela->getCampo( 'info' ));
                        $this->obRCarne->setObservacaoL1( 'Após os vencimentos previstos nesta guia, retirar 2ª via na' );
                        $this->obRCarne->setObservacaoL2( 'Secretaria de Fazenda' );
                        $this->obRCarne->setObservacaoL3( ' ' );
                        $this->obRCarne->setParcelaUnica( false );
                        $this->obRCarne->setVencimento  ( $rsParcela->getCampo( 'vencimento' ) );

                        $arTmp = explode('/',$rsParcela->getCampo( 'vencimento' ));
                        $boVenc1 = false;
                        $boVenc2 = false;
                        $boVenc3 = false;
                        $this->obRCarne->setVencimento1(NULL);
                        $this->obRCarne->setVencimento2(NULL);
                        $this->obRCarne->setVencimento3(NULL);
                        $this->obRCarne->setValor1(NULL);
                        $this->obRCarne->setValor2(NULL);
                        $this->obRCarne->setValor3(NULL);

                        $inArCount = count($arVencimentos);

                        if ($this->stLocal != "WEB") {
                            $stMes = $arTmp[1];
                            $arTmp = explode('/',$arVencimentos[0]);
                            if ($arTmp[1] >= $stMes AND $inArCount >= 1) {
                                $stMes = $arTmp[1];
                                $boVenc1 = true;
                                $this->obRCarne->setVencimento1 ( $arVencimentos[0] );
                                $arTmp = explode('/',$arVencimentos[1]);
                                if ($arTmp[1] >= $stMes AND $inArCount >= 2) {
                                    $stMes = $arTmp[1];
                                    $boVenc2 = true;
                                    $this->obRCarne->setVencimento2 ( $arVencimentos[1] );
                                    $arTmp = explode('/',$arVencimentos[2]);
                                    if ($arTmp[1] >= $stMes AND $inArCount >= 3) {
                                    $boVenc3 = true;
                                    $this->obRCarne->setVencimento3 ( $arVencimentos[2] );
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
    //                        $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro1);
    //                        $nuValorNormal = $rsTmp->getCampo('valor');
                            // % de juro
                            $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'j'");
                            $stJuroNormal = $rsTmp->getCampo('valor');
                            $this->obRCarne->lblJuros1 = $stJuroNormal;
                            // % de multa
                            $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro1.",'m'");
                            $stMultaNormal = $rsTmp->getCampo('valor');
                            $this->obRCarne->lblMulta1 = $stMultaNormal;
                            //-----------------------------------------------------------------------

                            // valor, % de juro, % de multa para valor vencimento 1 do carne --------------
                            // valor
                            if ($boVenc1 == true) {
                                $obErro = $obCalculaParcelas->executaFuncao($rsTmp,$stParametro2);
                                $nuValor1 = $rsTmp->getCampo('valor');
                                // % de juro
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'j'");
                                $stJuro1 = $rsTmp->getCampo('valor');
                                $this->obRCarne->lblJuros2 = $stJuro1;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp,$stParametro2.",'m'");

                                $stMulta1 = $rsTmp->getCampo('valor');
                                $this->obRCarne->lblMulta2 = $stMulta1;
                            } else {
                                $this->obRCarne->lblJuros2 = "";
                                $this->obRCarne->lblMulta2 = "";
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

                                $this->obRCarne->lblJuros3 = $stJuro2;

                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro3.",'m'");
                                $stMulta2 = $rsTmp3->getCampo('valor');
                                $this->obRCarne->lblMulta3 = $stMulta2;
                            } else {
                                $this->obRCarne->lblJuros3 = "";
                                $this->obRCarne->lblMulta3 = "";
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
                                $this->obRCarne->lblJuros4 = $stJuro3;
                                // % de multa
                                $obErro = $obCalculaJM->executaFuncao($rsTmp3,$stParametro4.",'m'");
                                $stMulta3 = $rsTmp3->getCampo('valor');

                                $this->obRCarne->lblMulta4 = $stMulta3;
                            } else {
                                $this->obRCarne->lblJuros4 = "";
                                $this->obRCarne->lblMulta4 = "";
                            }
                            //-----------------------------------------------------------------------

                            // repassa valores para pdf
                            $this->obRCarne->setValor       ( number_format(round($nuValorNormal,2),2,',',''));
                            if ($boVenc1 == true) {
                                $this->obRCarne->setValor1      ( number_format(round($nuValor1,2),2,',','')) ;
                                if ($boVenc2 == true) {
                                    $this->obRCarne->setValor2      ( number_format(round($nuValor2,2),2,',','')) ;
                                    if ($boVenc3 == true) {
                                        $this->obRCarne->setValor3      ( number_format(round($nuValor3,2),2,',','')) ;
                                    }
                                }
                            }
                        } else {
                            $this->obRCarne->setValor       ( number_format(round($nuValorNormal,2),2,',','.'));

                        }

                    }
                }

                $this->arCodigoBarra = $this->obBarra->geraFebraban( $this->arBarra );
                $this->obRCarne->setBarCode( $this->arCodigoBarra['codigo_barras'] );
                $this->obRCarne->setLinhaCode( $this->arCodigoBarra['linha_digitavel'] );
                $this->obRCarne->drawCarne( $this->inHorizontal, $this->inVertical );
                $this->obRCarne->posicionaVariaveis( $this->inHorizontal, $this->inVertical );
                $this->obRCarne->setPicote( $this->inHorizontal, $this->inVertical );
                $this->inVertical += 95;

                if ( ( $inParcela == 2 ) || ( $inCount == 3 ) ) {
                    $this->obRCarne->novaPagina();
                    $inCount = 0;
                    $this->inVertical = 7;
                    $this->boPulaPagina = false;
                } else {
                    $this->boPulaPagina = true;
                }
                $inCount++;
            }// fim foreach parcelas
        }
        //if ( ( $inSaltaPagina != count($arEmissao) ) && ( ( count($chave) != 2 ) && ( count($chave) != 3 ) ) ) {
        if (( $this->boPulaPagina ) && ( $inSaltaPagina != count($this->arEmissao) )) {
            $this->obRCarne->novaPagina();
        }
        $arGruposValidos = explode(',','101,102,10120, 10121, 10122, 10123, 10124, 10125, 10198, 10199, 10220, 10221, 10222, 10223,10224,10225,10298,10299');
        if(in_array($this->obRCarne->getCodDivida(),$arGruposValidos))
            $this->obRCarne->drawComplemento($this->inHorizontal, $this->inVertical);

    } // fim foreach $arEmissao
    if ( Sessao::read( 'stNomPdf' ) )
        $stNome     = Sessao::read( 'stNomPdf' );
    else
        $stNome     = "Carne.pdf";

    if ( Sessao::read( 'stParamPdf' ) )
        $stParam    = Sessao::read( 'stParamPdf' );
    else
        $stParam    = "D";
    $this->obRCarne->show($stNome,$stParam); // lanca o pdf
}

function geraParcelas($data, $iteracao)
{
    $arDataResult = array();
    $arData = explode('/',$data);
    $anoOriginal = $arData[2];

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
            case 10:
            case 12: $dia = 31;
            break;

            case 4 :
            case 6 :
            case 9 :
            case 11: $dia = 30;
            break;
        }
        if ($anoOriginal == $ano) {
            $data = str_pad($dia,2,'0',STR_PAD_LEFT).'/'.str_pad($mes,2,'0',STR_PAD_LEFT).'/'.$ano;
            array_push($arDataResult,$data);
        }
        $mes++;
        if ($mes > 12) {
            $mes = 1;
            $ano++;
            break;
        }

        $data = $dia.'/'.$mes.'/'.$ano;
    }

    return $arDataResult;
}

}

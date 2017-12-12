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
*
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

if ( !defined('FPDF_FONTPATH') ) {
    define('FPDF_FONTPATH',CAM_FPDF.'font/');
}

/**
    * Classe de geração de documento PDF
    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
*/
class DocumentoPDF extends DocumentoDinamico
{
/**#@+
    * @var Array
    * @access Private
*/
var $arRecordSet;
var $arCampos;
var $arTexto;
/**#@-*/

/**#@+
    * @var Integer
    * @access Private
*/
var $inTamanhoFonte;
var $inCorFonte;
var $inIdent;
var $inAlturaLinha;
var $inComprimentoLinha;
var $inMargemDir;
/**#@-*/

/**#@+
    * @var String
    * @access Private
*/
var $stTitulo;
var $stTag;
var $stFonte;
var $stEstilo;
var $arEnderecoPrefeitura;

/**#@-*/

/**#@+
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
/**#@-*/

function DocumentoPDF($orientation='P',$unit='mm',$format='A4')
{
    parent::FPDF( $orientation,$unit,$format );

    $this->stFonte = "courier";
    $this->inTamanhoFonte = 12;

    $this->SetFont($this->stFonte,'',$this->inTamanhoFonte);
    $this->SetStyle("p","times","N",12,"",15);
    $this->SetStyle("b","times","B",0,"");
    $this->SetStyle("u","times","U",0,"");
    $this->SetStyle("i","times","I",0,"");
    $this->inAlturaLinha = 8;
    $this->inMargemDir   = 10;
    $this->obTConfiguracao = new TAdministracaoConfiguracao;
    $this->arTexto = array();
}

/**
    * Insere um objeto do tipo RecordSet para ser listado
    * @access Public
    * @param  Object $rsRecordSet Objeto a ser listado
*/
function addRecordSet(&$obClasse)
{
    $this->arRecordSet = $obClasse;
}

/**
    * Insere um titulo para ser listado
    * @access Public
    * @param  $stTitulo, $stFonte, $stEstilo e $inTamanho
*/
function addTitulo($stTitulo, $stFonte="times", $stEstilo="N", $inTamanho=16)
{
    $this->stTitulo = $stTitulo;
    $this->SetStyle("ti",$stFonte,$stEstilo,$inTamanho,"",0);
}

/**
    * Insere texto para ser listado
    * @access Public
    * @param  Array $arTexto e Char $stAlinhamento a ser listado
*/
function addTexto($stTexto, $stAlinhamento="J")
{
    $inLast = count($this->arTexto);
    $this->arTexto[$inLast]["texto"]       = $stTexto;
    $this->arTexto[$inLast]["alinhamento"] = $stAlinhamento;
}

/**
    * Define a altura da linha
    * @access Public
    * @param  Integer $inValor Parâmetro com o valor da altura
*/
function setAlturaLinha($inValor)
{
    $this->inAlturaLinha=$inValor;
}

/**
    * Define Margens
    * @access Public
    * @param  integer $inLeft, $inTop, $inRight
*/
function setMargens($inLeft=10, $inTop=15, $inRight=10)
{
    $this->setMargemDir($inRight);
    $this->setMargins ($inLeft, $inTop, $inRight);
}
/**
    * Define Margem direita
    * @access Public
    * @param  integer $inRight
*/
function setMargemDir($inRight=10)
{
    $this->inMargemDir = $inRight;
}

/**
    * Define comprimento da linha
    * @access Public
    * @param  Integer $inValor Parâmetro com o valor do comprimento
*/
function setComprimentoLinha($inValor)
{
    $this->inComprimentoLinha = $inValor;
}

/**
    * Define campo do recordSet
    * @access Public
    * @param  String $stCampo Parâmetro com o nome do campo
*/
function setCampo($stCampo)
{
    $this->arCampos[] = $stCampo;
}

/**
    * Define Fonte do texto
    * @access Public
    * @param  String $stCampo Parâmetro com o nome do campo
*/
function setFonte($stFonte, $inTamanho)
{
    $this->stFonte = $stFonte;
    $this->inTamanhoFonte = $inTamanho;

    $this->setFont($this->stFonte,'', $this->inTamanhoFonte);
}

function setEnderecoPrefeitura($valor) { $this->arEnderecoPrefeitura = $valor; }

   public function Header()
   {
        $this->SetCreator = "URBEM";
        $this->SetFillColor(220);
        $tMargem = $this->tMargin;
        $lMargem = $this->lMargin;
        if ( is_file( "../../../".$this->arEnderecoPrefeitura["logotipo"] ) ) {
            $this->Image( "../../../".$this->arEnderecoPrefeitura["logotipo"]  ,$lMargem,$tMargem,20);
        } elseif ( is_file( $this->arDadosCabecalho["logotipo"] ) ) {
            $this->Image(  $this->arDadosCabecalho["logotipo"] ,$lMargem,$tMargem,20);
        }
        $this->Cell(20,10,'');
        $this->SetFont('Helvetica','B',8);
        $this->SetFillColor(255);
        $X = $this->GetX();
        $Y = $this->GetY() + 10;
        $this->SetXY($X,$Y);
        $this->Cell(70,4, $this->arEnderecoPrefeitura["nom_prefeitura"]  ,0,'L',1);
        $this->SetFont('Helvetica','',8);
        $this->SetXY($X,$Y+4);
        $this->Cell(70,4,"Fone/fax ".$this->arEnderecoPrefeitura["fone"]." / ".$this->arEnderecoPrefeitura["fax"],0,'L',1);
        //$this->Cell(70,4,"Fone/fAX (051)339-1122 / 339-2419",0,'L',1);
        $this->SetXY($X,$Y+8);
        $this->Cell(70,4,"E-mail: ".$this->arEnderecoPrefeitura["e_mail"] ,0,'L',1);
        $this->SetXY($X,$Y+12);
        $this->Cell(70,4, $this->arEnderecoPrefeitura["logradouro"].",".$this->arEnderecoPrefeitura["numero"]." - ".$this->arEnderecoPrefeitura["nom_municipio"]  ,0,'L',1);

        $this->SetXY($X,$Y+16);
        $this->Cell(70,4,"CEP ".$this->arEnderecoPrefeitura["cep"]."      CNPJ ".$this->arEnderecoPrefeitura['cnpj'],0,'L',1);
        $this->SetXY($this->inMargemDir,$Y+25);
    }

    //Rodapé
    public function Footer()
    {
        $sDisp = $this->DefOrientation;
        $sDisp = 'L';
        if ($this->inCodBarras) {
            $iAjus = -37;
            if ($sDisp=='L') {
                $iAjus = -38;
            }
        } else {
            $iAjus = -10;
            if ($sDisp=='L') {
                $iAjus = -15;
            }
        }
        $this->SetY($iAjus);
        $this->SetFont('Helvetica','',6);
        $this->Cell(0,5,'URBEM - CNM Confederação Nacional de Municípios - www.cnm.org.br','T',0,'L');
        $this->Cell(0,5,basename($this->sNomeRelatorio),'T',0,'R');
        if ($this->inCodBarras) {
            $this->defineCodigoBarras( 10,267,$this->inCodBarras );
        }
    }

/**
    * Monta o PDF conforme as propriedades setadas
    * @access Public
*/
function montaPDF()
{
    $this->open();

    $arElementos = array ();
    $arElementos = $this->arRecordSet->getElementos();
    $inPaginas = count($arElementos);

    $this->stTitulo = "<ti>".$this->stTitulo."</ti>";

    for ($iCount = 0; $iCount < $inPaginas; $iCount++) {
        $this->AddPage();
        $arTextoAux = $this->arTexto;
        $this->WriteTag (0,8,$this->stTitulo,"","C",0,5);
        $this->Ln(8);

        for ($iCount3 = 0; $iCount3 < count($this->arTexto); $iCount3++) {
            for ($iCount2 = 0; $iCount2 < count($this->arCampos); $iCount2++) {
                $arTextoAux[$iCount3]["texto"] = str_replace ("[".$this->arCampos[$iCount2]."]", $arElementos[$iCount][$this->arCampos[$iCount2]], $arTextoAux[$iCount3]["texto"]);
            }
            $this->WriteTag ($this->inComprimentoLinha, $this->inAlturaLinha, $arTextoAux[$iCount3]["texto"], 0,$arTextoAux[$iCount3]["alinhamento"]);
            $this->ln(0);
        }
    }

}

/**
    * Mostra PDF conforme as propriedades setadas
    * @access Public
*/

function show()
{
    $this->montaPDF();

    $arFiltroRelatorio = Sessao::read('filtroRelatorio');
    $this->stFilaImpressao =    $arFiltroRelatorio['stFilaImpressao'];
    $this->inNumeroImpressoes = $arFiltroRelatorio['inNumCopias'];

    if ($this->stFilaImpressao) {
        $stParams = '';
        if (  strtolower($this->DefOrientation) == 'l' ) {
            $stParams .= '-landscape ';
        }
        $sParams .= '-size '.$this->PageFormat;
        $sFile = "../../../tmp/doc_".date("Y-m-d",time()).'_'.date("His",time()).'_'.substr(Sessao::getId(),10,6);
        $sFilePDF = $sFile.".pdf";
        $sFilePS  = $sFile.".ps";
        $this->Output($sFilePDF);
        $cmdo  = " pdf2ps ".$sFilePDF." ".$sFilePS." && ";
        $cmdo .= " lpr -r -P$this->stFilaImpressao ".$sFilePS." -#$this->inNumeroImpressoes";
        $aAux = array();
        exec($cmdo, $aAux);
        exec("rm $sFilePDF", $aAux);
        exec("rm $sFilePS", $aAux);
    } else {
       $this->OutPut();

    }
}

}
?>

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

$Id: ListaPDF.class.php 62808 2015-06-22 11:44:14Z gelson $

Casos de uso: uc-01.01.00
*/

include_once( CAM_FPDF."fpdf.php");

define('FPDF_FONTPATH',CAM_FPDF.'fonts/');

/**
    * Classe de geração de listas PDF
    * @author Analista: Ricardo Alencar Lopes
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
*/
class ListaPDF extends fpdf
{
/**#@+
    * @var Array
    * @access Private
*/
var $arRecordSet;
var $arCabecalho;
var $arLarguraColuna;
var $arCampos;
var $arIndentaColuna;//campo, coluna, espaco
var $arQuebraLinha;//campo, valor, altura
var $arQuebraProximaLinha;//campo, valor, altura
var $arQuebraPagina;//campo, valor
var $arQuebraPaginaLista;
var $arDadosCabecalho;
var $arFiltro;
var $arComponenteAgrupado;
var $boQuebraContagemPorSubTitulo;
var $inCodBarras;
var $sNomeRelatorio;
var $inNumAssinaturasPorLinha; 	// Número de Assinaturas por Linha
var $arAssinaturasDefinidas;	// Assinaturas definidas para o Relatório

/**#@-*/
/**#@+
    * @var String
    * @access Private
*/
var $stAlinhamento;

var $stData;
var $stHora;

var $stModulo;
var $stAcao;
var $stComplementoAcao;
var $stTitulo;
var $stSubTitulo;
var $stSubTituloAtual; //// essa var será usada para reiniciar o numero de pagina qdo a contagem por por subtitulo
var $pageSubTitulo;
var $stUsuario;
var $stFilaImpressao;
var $PageFormat;
/**#@-*/
/**#@+
    * @var Boolean
    * @access Private
*/
var $boBordas;
var $boMultiplo;
/**#@-*/
/**#@+
    * @var Integer
    * @access Private
*/
var $inPaginaInicial;
var $inLarguraUtil;
var $inAlturaUtil;
var $inAlturaLinha;
var $inAlturaCabecalho;
var $inIndiceLista;
var $inNumeroImpressoes;
var $inCodigoBarras;
/**#@-*/
/**#@+
    * @var Object
    * @access Private
*/
var $obTConfiguracao;
/**#@-*/

/**#@+
    * @access Public
    * @param Array $valor
*/
function setRecordSet($valor) { $this->arRecordSet     = $valor; }
function setCabecalho($valor) { $this->arCabecalho     = $valor; }
function setLarguraColuna($valor) { $this->arLarguraColuna = $valor; }
function setCampos($valor) { $this->arCampos        = $valor; }
function setIndentaColuna($valor) { $this->arIndentaColuna = $valor; }
function setQuebraLinha($valor) { $this->arQuebraLinha   = $valor; }
function setQuebraPagina($valor) { $this->arQuebraPagina  = $valor; }
function setEnderecoPrefeitura($valor) { $this->arDadosCabecalho = $valor; }
function setDadosCabecalho($valor) { $this->arDadosCabecalho = $valor; }
function setFiltro($valor) { $this->arFiltro        = $valor; }
function setComponenteAgrupado($valor) { $this->arComponenteAgrupado[ $this->inIndiceLista ] = $valor; }

function setQuebraContagemPorSubTitulo($valor) { $this->boQuebraContagemPorSubTitulo = $valor;}
function getQuebraContagemPorSubTitulo() { return $this->boQuebraContagemPorSubTitulo; }

/**#@-*/
/**#@+
    * @access Public
    * @param String $valor
*/
function setAlinhamento($valor) { $this->stAlinhamento   = $valor; }
function setModulo($valor) { $this->stModulo          = $valor; }
function setTitulo($valor) { $this->stTitulo          = $valor; }
function setSubTitulo($valor) { $this->stSubTitulo       = $valor; }
function setUsuario($valor) { $this->stUsuario         = $valor; }
function setAcao($valor) { $this->stAcao            = $valor; }
function setComplementoAcao($valor) { $this->stComplementoAcao = $valor; }
function setData($valor) { $this->stData            = $valor; }
function setFilaImpressao($valor) { $this->stFilaImpressao   = $valor; }
/*#@-*/
/**#@+
    * @access Public
    * @param Boolean $valor
*/
function setBordas($valor) { $this->boBordas        = $valor; }
function setMultiplo($valor) { $this->boMultiplo      = $valor; }
/*#@-*/
/**#@+
    * @access Public
    * @param Integer $valor
*/
function setPaginaInicial($valor) { $this->inPaginaInicial = $valor; }
function setLarguraUtil($valor) { $this->inLarguraUtil   = $valor; }
function setAlturaUtil($valor) { $this->inAlturaUtil    = $valor; }
function setIndiceLista($valor) { $this->inIndiceLista   = $valor; }
function setNumeroImpressoes($valor) { $this->inNumeroImpressoes = $valor; }
function setCodigoBarras($valor) { $this->inCodBarras     = $valor; }
/**#@-*/

// Métodos para Assinaturas Configuráveis
/**
    * Definir o número de assinaturas por Linha
    * @access Public
    * @param Integer
    * @return void
*/
function setNumAssinaturasPorLinha($integer)
{
    $this->inNumAssinaturasPorLinha = $integer;
}
/**
    * Definir as assinaturas que vão compor o relatório
    * @access Public
    * @param Array
    * @return void
*/
function setAssinaturasDefinidas($array)
{
    $this->arAssinaturasDefinidas = $array; // Assinaturas definidas para o Relatório
}

/**#@+
    * @access Public
    * @return Array
*/
function getRecordSet() { return $this->arRecordSet;     }
function getCabecalho() { return $this->arCabecalho;     }
function getLarguraColuna() { return $this->arLarguraColuna; }
function getCampos() { return $this->arCampos;        }
function getIndentaColuna() { return $this->arIndentaColuna; }
function getQuebraLinha() { return $this->arQuebraLinha;   }
function getQuebraPagina() { return $this->arQuebraPagina;  }
function getFiltro() { return $this->arFiltro;        }
function getComponenteAgrupado() { return $this->arComponenteAgrupado[ $this->inIndiceLista ]; }
/**#@-*/
/**#@+
    * @access Public
    * @return String
*/

function getAlinhamento() { return $this->stAlinhamento;     }
function getAcao() { return $this->stAcao; }
function getComplementoAcao() { return $this->stComplementoAcao; }
/*#@-*/
/**#@+
    * @access Public
    * @return Boolean
*/
function getBordas() { return $this->boBordas;        }
function getMultiplo() { return $this->boMultiplo;      }
/*#@-*/
/**#@+
    * @access Public
    * @return Integer
*/
function getPaginaInicial() { return $this->inPaginaInicial; }
function getLarguraUtil() { return $this->inLarguraUtil;   }
function getAlturaUtil() { return $this->inAlturaUtil;    }
function getAlturaLinha() { return $this->inAlturaLinha;   }
function getIndiceLista() { return $this->inIndiceLista;   }
function getCodigoBarras() { return $this->inCodBarras;     }
/**#@-*/

/**
    * Recuperar o número de assinaturas por Linha
    * @access Public
    * @param void
    * @return Integer
*/
function getNumAssinaturasPorLinha() { return $this->inNumAssinaturasPorLinha; }

/**
    * Recuperar as assinaturas que vão compor o relatório
    * @access Public
    * @param void
    * @return Array
*/
function getAssinaturasDefinidas() { return $this->arAssinaturasDefinidas; }

/**
    * Método Construtor
    * @access Private
*/
function ListaPDF($orientation='P',$unit='mm',$format='A4')
{
    parent::FPDF( $orientation,$unit,$format );
    $this->inLarguraUtil = $this->w - ( $this->lMargin + $this->rMargin );
    $this->inAlturaUtil  = $this->h - ( $this->tMargin + $this->bMargin );
    $this->stIndentEspaco = "  ";
    $this->arRecordSet = $this->arCabecalho = $this->arLarguraColuna = $this->arQuebraLinha = $this->arIndentaColuna = array();
    $this->boBordas = false;
    $this->inIndiceLista = 0;
    $this->arDadosCabecalho = array();
    $this->arQuebraPaginaLista = array();
    $this->inAlturaCabecalho[$this->inIndiceLista] = 5;
    $this->stHora = date( "H:i:s", time()) ;
    include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );
    $this->obTConfiguracao = new TAdministracaoConfiguracao;
    $this->PageFormat = $format;
    $this->inPaginaInicial = null;
    // Assinaturas Configuráveis
    $this->inNumAssinaturasPorLinha = 3; 	 // Número de Assinaturas por Linha
    $this->arAssinaturasDefinidas = array(); // Assinaturas definidas para o Relatório
}

function recalculaDimensoes()
{
    $this->inLarguraUtil = $this->w - ( $this->lMargin + $this->rMargin );
    $this->inAlturaUtil  = $this->h - ( $this->tMargin + $this->bMargin );
}

function insereData()
{
    $this->stData = date( "d/m/Y", time());
}

/**
    * Insere um objeto do tipo RecordSet para ser listado
    * @access Public
    * @param  Object $rsRecordSet Objeto a ser listado
*/
function addRecordSet(&$obClasse)
{
    if ( count( $this->arRecordSet ) ) {
        $this->inIndiceLista++;
    }
    $this->arRecordSet[] = $obClasse;
    $this->inAlturaLinha[$this->inIndiceLista] = 5;
    $this->arQuebraPaginaLista[$this->inIndiceLista] = true;
}

function setQuebraPaginaLista($boQuebra)
{
    $this->arQuebraPaginaLista[$this->inIndiceLista] = $boQuebra;
}

/**
    * Define a altura que a linha da lista corrente ira ter
    * @access Public
    * @param  Integer $inValor Parâmetro com o valor sa altura
*/
function setAlturaLinha($inValor)
{
    $this->inAlturaLinha[$this->inIndiceLista] = $inValor;
}

/**
    * Define a altura que o cabecalho da lista corrente ira ter
    * @access Public
    * @param  Integer $inValor Parâmetro com o valor sa altura
*/
function setAlturaCabecalho($inValor)
{
    $this->inAlturaCabecalho[$this->inIndiceLista] = $inValor;
}

/**
    * Define o cabeçalho de uma lista
    * @access Public
    * @param  String $stRotulo Valor que sera impresso no cabeçao da lista
    * @param  Integer $inLargura Determina a largura da coluna
    * @param  Integer $inTamanho Determina o tamnanho da fonte
    * @param  String $stStyle Determina a formatação da fonte
    * @param  Strind $stFont Determina qual a fonte sera usada
*/
function addCabecalho($stRotulo, $inLargura = "",  $inTamanho = 8 , $stStyle = "" , $stFont = "Arial")
{
    $stAlinhamento = $this->stAlinhamento ? $this->stAlinhamento : "C";
    $this->arCabecalho[$this->inIndiceLista][] = array( "rotulo" => $stRotulo , "tamanho" => $inTamanho ,"style" => $stStyle , "font" => $stFont , "alinhamento" => $stAlinhamento );
    $this->arLarguraColuna[$this->inIndiceLista][] =  $inLargura  * ( $this->inLarguraUtil / 100 );
}

/**
    * Define quais campos o do objeto recordset serao usados assim como sua formatação
    * @access Public
    * @param  String $stCampo Valor que sera impresso no corpo da lista
    * @param  Integer $inTamanho Determina o tamnanho da fonte
    * @param  String $stStyle Determina a formatação da fonte
    * @param  String $stFont Determina qual a fonte sera usada
    * @param  Boolean $boPreenchimento Determina se o fundo da celula será ou não preenchido de branco
*/
function addCampo($stCampo , $inTamanho = 8 , $stStyle = "" , $stFont = "Arial", $boPreenchimento = true)
{
    $stAlinhamento = $this->stAlinhamento ? $this->stAlinhamento : "L";
    $this->arCampo[$this->inIndiceLista][] = array( "campo" => $stCampo, "tamanho" => $inTamanho ,"style" => $stStyle , "font" => $stFont , "alinhamento" => $stAlinhamento, "preenchimento" => $boPreenchimento );
}

/**
    * Define quais campos o do objeto recordset serao usados assim como sua formatação
    * @access Public
    * @param  String $stCampo Valor que sera impresso no corpo da lista
    * @param  Integer $inLargura Determina a largura do campo
    * @param  Integer $inTamanho Determina o tamanho da fonte
    * @param  String $stStyle Determina a formatação da fonte
    * @param  String $stFont Determina qual a fonte sera usada
    * @param  Boolean $boPreenchimento Determina se o fundo da celula será ou não preenchido de branco
*/
function addCampoComLargura($stCampo , $inLargura = "", $inTamanho = 8 , $stStyle = "" , $stFont = "Arial", $boPreenchimento = true)
{
    $stAlinhamento = $this->stAlinhamento ? $this->stAlinhamento : "L";
    $this->arCampo[$this->inIndiceLista][] = array( "campo" => $stCampo, "tamanho" => $inTamanho ,"style" => $stStyle , "font" => $stFont , "alinhamento" => $stAlinhamento, "preenchimento" => $boPreenchimento );
    $this->arLarguraColuna[$this->inIndiceLista][] =  $inLargura  * ( $this->inLarguraUtil / 100 );
}

/**
    * Define em quais campos havera indentação
    * @access Public
    * @param  String $stCampo Determina qual campo sera usado como referencia para a indentação
    * @param  String $stColuna Determina qual coluna sera indentada
    * @param  String $stEspaco Determina quantos e quais caracteres serão usados na indentação
*/
function addIndentacao($stCampo, $stColuna, $stEspaco = "  ")
{
    $this->arIndentaColuna[$this->inIndiceLista][] = array( "campo" => $stCampo , "coluna" => $stColuna , "espaco" => $stEspaco );
}

/**
    * Define em quais filtros deverão ser mostrados no relatorio
    * @access Public
    * @param  String $stRotulo Determina o rotulo que sera usado como referencia para o filtro
    * @param  String $stFiltro Determina qual foi o criterio para o filtro
*/
function addFiltro($stRotulo, $stFiltro)
{
    $this->arFiltro[] = array( "rotulo" => $stRotulo , "filtro" => $stFiltro );
}

/**
    * Define código de barras 2 de 5 intercalado no rodapé
    * @access Public
    * @param  String $stCampo Determina qual campo sera usado como referencia para a indentação
    * @param  String $stColuna Determina qual coluna sera indentada
    * @param  String $stEspaco Determina quantos e quais caracteres serão usados na indentação
*/
function defineCodigoBarras($xpos, $ypos, $code, $basewidth = 0.7, $height = 15)
{
    $wide = $basewidth;
    $narrow = $basewidth / 3 ;

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
    //$this->Text($xpos, $ypos + $height + 4, $code);
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

/**
    * Determina quando havera uma quebra de linha em função do valor do campo setado
    * @access Public
    * @param  String $stCampo Determina qual campo sera utilizado como referencia para a quebra
    * @param  String $stValor Determina qual o valor que o campo de ter para que ocorra a quebra
    * @param  String $inAltura Determina qual a altura tera a quebra de linha
*/
function addQuebraLinha($stCampo , $stValor, $inAltura = "")
{
    $inAltura = $inAltura ? $inAltura :  $this->inAlturaLinha[$this->inIndiceLista];
    $this->arQuebraLinha[$this->inIndiceLista][] = array( "campo" => $stCampo, "valor" => $stValor, "altura" =>  $inAltura );
}

/**
    * Determina quando havera uma quebra na proxima linha em função do valor do campo setado
    * @access Public
    * @param  String $stCampo Determina qual campo sera utilizado como referencia para a quebra
    * @param  String $stValor Determina qual o valor que o campo de ter para que ocorra a quebra
    * @param  String $inAltura Determina qual a altura tera a quebra de linha
*/
function addQuebraProximaLinha($stCampo , $stValor, $inAltura = "")
{
    $inAltura = $inAltura ? $inAltura :  $this->inAlturaLinha[$this->inIndiceLista];
    $this->arQuebraProximaLinha[$this->inIndiceLista][] = array( "campo" => $stCampo, "valor" => $stValor, "altura" =>  $inAltura );
}

/**
    * Determina quando havera uma quebra de pagina em função do valor do campo setado
    * @access Public
    * @param  String $stCampo Determina qual campo sera utilizado como referencia para a quebra
    * @param  String $stValor Determina qual o valor que o campo de ter para que ocorra a quebra
*/
function addQuebraPagina($stCampo , $stValor)
{
    $this->arQuebraPagina[$this->inIndiceLista][] = array( "campo" => $stCampo, "valor" => $stValor );
}

/**
    * Monta o cabeçalho da lista corrente
    * @access Private
    * @param  $inIndiceLista Determina o indice da lista corrente
*/
function montaCabecalhoLista($inIndiceLista)
{
    if ( is_array($this->arLarguraColuna[$inIndiceLista]) ) {
        reset( $this->arLarguraColuna[$inIndiceLista] );
        $x = $this->getX();
        $inAltura = $this->inAlturaCabecalho[$this->inIndiceLista];
        if ( $this->getMultiplo() ) {
            $this->ln($inAltura + 4);
        }
        if ($this->verificaQuebraPagina(isset($this->inAlturaCabecalho[$inIndiceLista]) ? $this->inAlturaCabecalho[$inIndiceLista] : 5)) {
            $this->addPage();
        }
        $in = '';
        foreach ($this->arCabecalho[$inIndiceLista] as $arCabecalho) {
           $y = $this->getY();
           $arLarguraColuna = each($this->arLarguraColuna[$inIndiceLista]  );
           $this->SetFont( $arCabecalho["font"], $arCabecalho["style"], $arCabecalho["tamanho"] );
           $this->MultiCell( $arLarguraColuna["value"] , (isset($this->inAlturaCabecalho[$inIndiceLista]) ? $this->inAlturaCabecalho[$inIndiceLista] : 5), $arCabecalho["rotulo"].$in, 0,$arCabecalho["alinhamento"],0 );
           $x += $arLarguraColuna["value"];
           if ( $this->getY() - $y > $inAltura ) {
               $inAltura = $this->getY() - $y;
           }
          $this->setXY($x, $y );
        }

        if ( $this->getMultiplo() ) {
            $this->ln($inAltura + 3);
        } else {
            $this->ln($inAltura);
        }
        reset( $this->arLarguraColuna[$inIndiceLista] );
        $this->setMultiplo( true );
    }
}

/**
    * Verifica se a página ira quebrar na próxima inserção de dado
    * @access Private
    * @param  $inAlturaLinha Determina a altura do proximo dado a que sera inserido
    * @return Boolean
*/
function verificaQuebraPagina($inAlturaLinha = 0)
{
    if ($this->y > $this->PageBreakTrigger - $inAlturaLinha) {
        return true;
    } else {
        return false;
    }
}

/**
    * Verifica se a lista corrente tem indentaçãa
    * @access Private
    * @param $inIndiceLista Indice da lista corrente
*/
function verificaIndentacao($inIndiceLista)
{
    $boRetorno = false;
    if (array_key_exists($inIndiceLista, $this->arIndentaColuna)) {
        if ( count( $this->arIndentaColuna[$inIndiceLista] ) ) {
            $boRetorno = true;
        }
    }

    return $boRetorno;
}

/**
    * Verifica se a lista corrente tem quebra de linha
    * @access Private
    * @param $inIndiceLista Indice da lista corrente
*/
function verificaQuebraLinha($inIndiceLista)
{
    $boRetorno = false;
    if (array_key_exists($inIndiceLista, $this->arQuebraLinha)) {
        if ( count( $this->arQuebraLinha[$inIndiceLista] ) ) {
            $boRetorno = true;
        }
    }

    return $boRetorno;
}

/**
    * Verifica se a lista corrente tem quebra de linha
    * @access Private
    * @param $inIndiceLista Indice da lista corrente
*/
function verificaQuebraProximaLinha($inIndiceLista)
{
    $boRetorno = false;
    if ( count( $this->arQuebraProximaLinha[$inIndiceLista] ) ) {
        $boRetorno = true;
    }

    return $boRetorno;
}

/**
    * Verifica se a lista corrente tem quebra de linha
    * @access Private
    * @param $inIndiceLista Indice da lista corrente
*/
function verificaQuebraPagina2($inIndiceLista)
{
    $boRetorno = false;
    if ( count( $this->arQuebraPagina[$inIndiceLista] ) ) {
        $boRetorno = true;
    }

    return $boRetorno;
}

/**
    * Método para verificar a quebra de pagina de um grupo de recordSets
    * @access Private
    * @param
    * @return Boolean
*/
function verificaQuebraPaginaGrupo($inCodGrupoUsado)
{
    $inAlturaTotal = 0;
    $arAux         = array();
    foreach ($this->arComponenteAgrupado as $inIndiceLista => $inCodGrupo) {
        if ($inCodGrupo == $inCodGrupoUsado) {
            if ( $this->arRecordSet[$inIndiceLista]->getNumLinhas() > 0 ) {
                $inAlturaTotal += ( $this->arRecordSet[$inIndiceLista]->getNumLinhas() * $this->inAlturaLinha[$inIndiceLista] );
            }

            $inAlturaTotal += (isset($this->inAlturaCabecalho[$inIndiceLista]) ? $this->inAlturaCabecalho[$inIndiceLista] : '');
        } else {
            $arAux[$inIndiceLista] = $inCodGrupo;
        }
    }
    $this->arComponenteAgrupado = $arAux;

    return $this->verificaQuebraPagina( $inAlturaTotal );
}

   public function Header()
   {
        $this->SetCreator = "URBEM";
        $this->SetFillColor(220);
        $tMargem = $this->tMargin;
        $lMargem = $this->lMargin;
        if ( is_file( CAM_FW_IMAGENS.$this->arDadosCabecalho["logotipo"] ) ) {
            $this->Image( CAM_FW_IMAGENS.$this->arDadosCabecalho["logotipo"]  ,$lMargem,$tMargem,20);
        } elseif ( is_file( $this->arDadosCabecalho["logotipo"] ) ) {
            $this->Image(  $this->arDadosCabecalho["logotipo"] ,$lMargem,$tMargem,20);
        }
        $this->Cell(20,10,'');
        $this->SetFont('Helvetica','B',8);
        $this->SetFillColor(255);
        $X = $this->GetX();
        $Y = $this->GetY();
        $this->Cell(70,4, $this->arDadosCabecalho["nom_prefeitura"]  ,0,'L',1);
        $this->SetFont('Helvetica','',8);
        $this->SetXY($X,$Y+4);
        $this->Cell(70,4,"Fone/Fax: ".$this->arDadosCabecalho["fone"]." / ".$this->arDadosCabecalho["fax"],0,'L',1);
        $this->SetXY($X,$Y+8);
        $this->Cell(70,4,"E-mail: ".$this->arDadosCabecalho["e_mail"] ,0,'L',1);
        $this->SetXY($X,$Y+12);
        $this->Cell(70,4, $this->arDadosCabecalho["logradouro"].",".$this->arDadosCabecalho["numero"]." - ".$this->arDadosCabecalho["nom_municipio"]  ,0,'L',1);

        $this->SetXY($X,$Y+16);
        $this->Cell(70,4,"CEP: ".$this->arDadosCabecalho["cep"],0,'L',1);
        $this->SetXY($X,$Y+20);
        $this->Cell(70,4,"CNPJ: ".$this->arDadosCabecalho['cnpj'],0,'L',1);
        $this->SetFont('Helvetica','B',8);
        $sDisp = $this->DefOrientation;
        $iAjus = 70;
        if ($sDisp=='L') {
            $iAjus = 160;
        }
        $this->SetXY($X+$iAjus,$Y);
        //$this->SetFillColor(220);

        $this->Cell(56,5,$this->arDadosCabecalho['nom_modulo'],1,0,'L',1);
        $this->Cell(0,5,'Versão: '.Sessao::getVersao(),1,0,'L',1);
        $this->SetXY($X+$iAjus,$Y+5);
        $this->Cell(56,5,$this->arDadosCabecalho['nom_funcionalidade'],1,'TRL','L',1);
        $this->Cell(0,5,"Usuário: ".Sessao::getUsername(),1,'RLB','L',1);
        $this->SetXY($X+$iAjus,$Y+10);
        $stNomAcao = '';
        if ($this->stAcao) {
            $this->arDadosCabecalho['nom_acao'] = trim($this->stAcao);
        } else {
            if( $this->stComplementoAcao )
                $stNomAcao = trim($this->arDadosCabecalho['nom_acao']) ." ".$this->stComplementoAcao;
        }
        $stNomAcao = ( $stNomAcao ) ? $stNomAcao : $this->arDadosCabecalho['nom_acao'];
        $this->Cell(0,5,$stNomAcao,1,'RLB','L',1);
        $this->SetFont('Helvetica','',8);
        $this->SetXY($X+$iAjus,$Y+15);
        $this->Cell(0,5,$this->stSubTitulo,1,'RLB','L',1);
        $this->SetXY($X+$iAjus,$Y+20);
        if(!$this->stData)
            $this->insereData();
        $this->Cell(33,5,'Emissão: '.$this->stData,1,0,'L',1);
        $this->Cell(23,5,'Hora: '.$this->stHora,1,0,'L',1);
        $this->AliasNbPages();

        if ($this->boQuebraContagemPorSubTitulo) {
            if ($this->inPaginaInicial == null) {
                if ( !$this->stSubTituloAtual or ( $this->stSubTituloAtual != $this->stSubTitulo  ) ) {
                    $this->stSubTituloAtual = $this->stSubTitulo;
                    $this->pageSubTitulo = 1;
                } else {
                    $this->pageSubTitulo++;
                }
                $this->Cell(0,5,'Página: '.$this->pageSubTitulo.' de '.$this->arTotalPaginasSubTitulo[$this->stSubTitulo] ,1,0,'L',1);

            } else {
                $this->Cell(0,5,'Página: '.( $this->PageNo() + $this->inPaginaInicial ) ,1,0,'L',1);
            }
        } else {
            if ($this->inPaginaInicial == null) {
                $this->Cell(0,5,'Página: '.$this->PageNo().' de '.$this->AliasNbPages ,1,0,'L',1);
            } else {
                $this->Cell(0,5,'Página: '.( $this->PageNo() + $this->inPaginaInicial ) ,1,0,'L',1);
            }
        }
        $this->Ln(4);
        $this->Cell(0,1,' ','B',0,'C');
        $this->Ln(3);
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
        if ($this->stFilaImpressao) {
            $iAjus -= 5;
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
    * Método para montar os filtros no relatorio
    * @access Private
*/
function montaFiltro()
{
    $rsRecordSet = new RecordSet();
    $inStrlen = 0;
    $arNovoFiltro = array();

    foreach ($this->arFiltro as $arFiltro) {
        // Elimina registros que não possuem valor de filtro
        if ($arFiltro['filtro'] != "") {
            if ( is_array( $arFiltro['filtro'] ) ) {
                $inCount = 0;
                foreach ($arFiltro['filtro'] as $stFiltro) {
                    $stRotulo = ( $inCount != 0 ) ? '' : $arFiltro['rotulo'] ;
                    $arNovoFiltro[] = array( 'rotulo' => $stRotulo, 'filtro' => $stFiltro );
                    $inCount++;
                }
            } else {
                $arNovoFiltro[] = array( 'rotulo' => $arFiltro['rotulo'], 'filtro' => $arFiltro['filtro'] );
           }
        }

        // Conta número de caracteres da maior string para determinar a largura da coluna
        if( strlen( $arFiltro['rotulo'] ) > $inStrlen )
            $inStrlen = strlen( $arFiltro['rotulo'] );
    }

    // Determina largura da coluna
    $inStrlen = $inStrlen / 1.4;
    $rsRecordSet->preenche( $arNovoFiltro );

    $this->addRecordSet( new RecordSet );
    $this->setQuebraPaginaLista( false );
    $this->setComponenteAgrupado( 9999 );
    $this->setAlinhamento ( "L" );
    $this->addCabecalho   ( "Filtros Utilizados" );
    $this->addRecordSet( $rsRecordSet );
    $this->setQuebraPaginaLista( false );
    $this->setComponenteAgrupado( 9999 );
    if ( strtolower(get_class( $this )) == 'listapdf' ) {
        $this->setAlturaCabecalho( -4 );
    } else {
        $this->setAlturaCabecalho( 2 );
    }
    $this->setAlinhamento ( "R" );
    $this->addCabecalho   ( "", $inStrlen, 10 );
    $this->setAlinhamento ( "" );
    $this->addCabecalho   ( "", 100-$inStrlen, 10 );
    $this->addCampo       ( "rotulo"    , 8 );
    $this->addCampo       ( ": [filtro]", 8 );
}

function montaAssinaturas()
{
    $rsVazio = new RecordSet;
    $stAlinhamento = $this->getAlinhamento();
    $nomeClasse = strtolower(get_class( $this ));
    $inLarg = (int) ( 100 / $this->getNumAssinaturasPorLinha() );
    $inTamFonte = 8;
    $stEstilo = '';

    foreach ( $this->getAssinaturasDefinidas() as $rsBlocoAssina ) {
        if ( $this->getNumAssinaturasPorLinha() > $rsBlocoAssina->inNumColunas ) {
            $inLarg = (int) ( 100 / $rsBlocoAssina->inNumColunas );
        } else {
            $inLarg = (int) ( 100 / $this->getNumAssinaturasPorLinha() );
        }
        $this->addRecordSet( $rsVazio );
        $this->setComponenteAgrupado( true );
        $this->setQuebraPaginaLista( false );
        $this->setAlinhamento( "C" );
        $this->setAlturaCabecalho( -4 );
        $this->addCabecalho( '', $inLarg, $inTamFonte, '', '', '0' );
        $this->addCampo( '', $inTamFonte, $stEstilo, '', '0');
        $this->addRecordSet( $rsBlocoAssina );
        $this->setComponenteAgrupado( 9999 );
        $this->setQuebraPaginaLista( false );
        $this->setAlinhamento( "C" );
        $this->setAlturaCabecalho( -1 );
        for ($col=0; $col<($rsBlocoAssina->inNumColunas); $col++) {
            if ($nomeClasse == 'listaformpdf') {
                $this->addCabecalho( '', $inLarg, $inTamFonte, '', '', '0' );
                $this->addCampo( $col, $inTamFonte, $stEstilo, '', '0');
            } else {
                $this->addCabecalho( '', $inLarg, $inTamFonte);
                $this->addCampo($col, $inTamFonte, $stEstilo);
            }
        }
    }
    // Restaura valores originais
    $this->setAlinhamento($stAlinhamento);
}

/**
    * Monta o PDF conforme as propriedades setadas
    * @access Public
*/
function montaPDF()
{
    $rsRecordSet = new RecordSet;

    if ($this->stFilaImpressao) {
        if( $this->tMargin < 15 )
            $this->tMargin= 15 ;
        if( $this->bMargin < 15 )
            $this->bMargin= 15 ;
    }

    $this->open();

    if ( count( $this->arFiltro ) ) {
        $this->montaFiltro();
    }

    if ( count( $this->getAssinaturasDefinidas() ) > 0 ) {
        $this->montaAssinaturas();
    }

    foreach ($this->arRecordSet as $inIndiceLista => $rsIndiceLista) {
        if ($rsIndiceLista == '' || $rsIndiceLista == null) {
            $rsRecordSet->preenche($rsIndiceLista);
        } else {
            $rsRecordSet = $rsIndiceLista;
        }

        $boFlagQuebraLinha = false;
        $boFlagQuebraProximaLinha = false;
        if ($this->arQuebraPaginaLista[$inIndiceLista]) {
            $this->addPage();
        }

        // Checa se é componente agrupado

        if (isset($this->arComponenteAgrupado[$inIndiceLista])) {
            if ( $this->verificaQuebraPaginaGrupo( $this->arComponenteAgrupado[ $inIndiceLista ] ) ) {
                $this->addPage();
                $boFlagQuebraLinha = false;
            }
        }

        $this->montaCabecalhoLista( $inIndiceLista );
        $boIndenta = $this->verificaIndentacao( $inIndiceLista );
        $boQuebraLinha = $this->verificaQuebraLinha( $inIndiceLista );
        $boQuebraProximaLinha = $this->verificaQuebraProximaLinha( $inIndiceLista );
        $boQuebraPagina = $this->verificaQuebraPagina2( $inIndiceLista );
        while ( !$rsRecordSet->eof()  ) {
            reset( $this->arLarguraColuna[$inIndiceLista] );
            if ($boQuebraLinha and $boFlagQuebraLinha) {
                foreach ($this->arQuebraLinha[$inIndiceLista] as $arQuebraLinha) {
                    if ( $rsRecordSet->getCampo( $arQuebraLinha["campo"] ) == $arQuebraLinha["valor"] ) {
                        $this->ln( $arQuebraLinha["altura"] );
                    }
                    if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                        $this->boQuebraForcada = true;
                   }
                }
            }
            if ( is_array( $this->arCampo[$inIndiceLista] ) ) {
                $flBufferY = 0;
                foreach ($this->arCampo[$inIndiceLista] as $arCampo) {
                    $arLarguraColuna = each( $this->arLarguraColuna[$inIndiceLista] );
                    $stCampoId = $arCampo["campo"];
                    $stId = "";
                    if (strstr($stCampoId,'[') || strstr($stCampoId,']')) {
                        for ($inCount=0; $inCount<strlen($stCampoId); $inCount++) {
                            if ($stCampoId[ $inCount ] == '[') $inInicialId = $inCount;
                            if (($stCampoId[ $inCount ] == ']') && isset($inInicialId) ) {
                                $stId .= $rsRecordSet->getCampo( trim( substr($stCampoId,$inInicialId+1,(($inCount-$inInicialId)-1)) ) );
                                unset($inInicialId);
                            }elseif( !isset($inInicialId) )
                                $stId .= $stCampoId[ $inCount ];
                        }
                    } else {
                        $stId = $rsRecordSet->getCampo( $stCampoId );
                    }
                    $stConteudo = $stId;
                    $stBordas = "0";
                    $this->SetFont( $arCampo["font"], $arCampo["style"], $arCampo["tamanho"] );
                    $stAlinhamento = $arCampo["alinhamento"];
                    $boPreenchimento = ( $arCampo["preenchimento"] ) ? 1 : 0;
                    if ($boIndenta) {
                        foreach ($this->arIndentaColuna[$inIndiceLista] as $arIndentaColuna) {
                            if ($arCampo["campo"] == $arIndentaColuna["coluna"]) {
                                $stIndenta = "";
                                $inNivel = $rsRecordSet->getCampo( $arIndentaColuna["campo"] );
                                for ($inContNivel = 1 ; $inContNivel < $inNivel; $inContNivel++) {
                                    $stIndenta .= $arIndentaColuna["espaco"];
                                }
                                if ($stAlinhamento == 'R') {
                                    $stConteudo = $stConteudo.$stIndenta;
                                } else {
                                    $stConteudo = $stIndenta.$stConteudo;
                                }
                            }
                        }
                    }
                    $this->SetFillColor(255, 255, 255 );
                    //VERIFICA SE O TEXTO IRA CABER NO ESPAÇO DEFINIDO PRA ELE
                    $flLarguraTexto = $this->GetStringWidth($stConteudo) / $arLarguraColuna["value"];
                    if ($flLarguraTexto > 1) {
                        $flBufferY = $flBufferY < $this->GetY() ? $this->GetY() : $flBufferY;
                        $inTamanhoString = (int) (strlen($stConteudo) / $flLarguraTexto );
                        $stConteudoTmp = substr($stConteudo ,0, $inTamanhoString );
                        while ( $this->GetStringWidth($stConteudoTmp) < $arLarguraColuna["value"] ) {
                            $stConteudoTmp = substr($stConteudo ,0, ++$inTamanhoString );
                        }
                        $stConteudo = $stConteudoTmp;
                    }
                    if ($stConteudo == 'Vale-Transporte por Empresa' || $stConteudo== 'Concessões' || $stConteudo== 'Filtros Utilizados' || $stConteudo== 'Matrículas pertencentes ao Grupo') {
                          $this->Cell( $arLarguraColuna["value"] , $this->inAlturaLinha[$inIndiceLista] , $stConteudo ,'T' ,0 ,$stAlinhamento, $boPreenchimento);
                    } else {
                         $this->Cell( $arLarguraColuna["value"] , $this->inAlturaLinha[$inIndiceLista] , $stConteudo ,$stBordas ,0 ,$stAlinhamento, $boPreenchimento);
                    }
                 }
            }
            if ($boQuebraProximaLinha) {
                foreach ($this->arQuebraProximaLinha[$inIndiceLista] as $arQuebraProximaLinha) {
                    if ( $rsRecordSet->getCampo( $arQuebraProximaLinha["campo"] ) == $arQuebraProximaLinha["valor"] ) {
                        $this->ln( $arQuebraProximaLinha["altura"] );
                    }
                    if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                   }
                }
            }
            $boFlagQuebraLinha = true;
            $this->ln( $this->inAlturaLinha[$inIndiceLista] );
            if ($boQuebraPagina) {
                foreach ($this->arQuebraPagina[$inIndiceLista] as $arQuebraPagina) {
                    if ( $rsRecordSet->getCampo( $arQuebraPagina["campo"] ) == $arQuebraPagina["valor"] ) {
                        $this->addPage();
                        $this->montaCabecalhoLista( $inIndiceLista );
                        $boFlagQuebraLinha = false;
                    }
                }
            }
            $rsRecordSet->proximo();
            if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                $this->addPage();
                $this->montaCabecalhoLista( $inIndiceLista );
                $boFlagQuebraLinha = false;
            }

            if (!isset($this->arComponenteAgrupado[$inIndiceLista])) {
                if ( $this->verificaQuebraPagina( $this->inAlturaLinha[$inIndiceLista] ) and !$rsRecordSet->eof() ) {
                    $this->addPage();
                    $this->montaCabecalhoLista( $inIndiceLista );
                    $boFlagQuebraLinha = false;
                }
            }
        }
    }
}

function show()
{
    $arFiltroRelatorio        = Sessao::read('filtroRelatorio');
    $this->stFilaImpressao    = isset($arFiltroRelatorio['stFilaImpressao']) ? $arFiltroRelatorio['stFilaImpressao'] : "";
    $this->inNumeroImpressoes = $arFiltroRelatorio['inNumCopias'];

    $this->montaPDF();
    if ($this->stFilaImpressao) {
        $stParams = '';
        if (  strtolower($this->DefOrientation) == 'l' ) {
            $stParams .= '-landscape ';
        }
        $stParams .= '-size '.$this->PageFormat;
        $sFile = CAM_FRAMEWORK."tmp/doc_".date("Y-m-d",time()).'_'.date("His",time()).'_'.substr(Sessao::getId(),10,6);
        $sFilePDF = $sFile.".pdf";
        $sFilePS  = $sFile.".ps";
        $this->Output($sFilePDF);
        $cmdo  = " pdf2ps ".$sFilePDF." ".$sFilePS." && ";
        $cmdo .= " lpr -r -P$this->stFilaImpressao ".$sFilePS." -#$this->inNumeroImpressoes";
        exec($cmdo, $aAux);
        exec("rm $sFilePDF", $aAux);
        exec("rm $sFilePS", $aAux);
    } else {
       $caracteres = array('a' => '/À|Á|Â|Ã|Ä|Å/','a' => '/à|á|â|ã|ä|å/','c' => '/Ç/','c' => '/ç/','e' => '/È|É|Ê|Ë/','e' => '/è|é|ê|ë/','i' => '/Ì|Í|Î|Ï/','i' => '/ì|í|î|ï/','n' => '/Ñ/','n' => '/ñ/','o' => '/Ò|Ó|Ô|Õ|Ö/','o' => '/ò|ó|ô|õ|ö/','u' => '/Ù|Ú|Û|Ü/','u' => '/ù|ú|û|ü/','y' => '/Ý/','y' => '/ý|ÿ/','a.' => '/ª/','o.' => '/º/');

       $stNomaAcao = preg_replace($caracteres, array_keys($caracteres), $this->arDadosCabecalho['nom_acao']);
       $stNomeArquivo = preg_replace("/[^a-zA-Z0-9]/","", ucwords( $stNomaAcao ) )."_".date("Y-m-d",time())."_".date("His",time()).".pdf";
       $this->OutPut( $stNomeArquivo, 'D' );
    }
}
}
?>

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
* A classe RecordSet contêm uma estrutura de manipulação dos dados oriundos de uma consulta.
* Data de Criação: 05/02/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Diego Barbosa Victoria

* @package bancoDados
* @subpackage postgreSQL

Casos de uso: uc-01.01.00

*/
ini_set("memory_limit",-1);
/**
    * Classe de RecordSet
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Diego Barbosa Victoria
*/
class RecordSet extends Objeto
{
/**
    * @var Array
    * @access Private
*/
var $arElementos;
/**
    * @var Array
    * @access Private
*/
var $arFormatacao;
/**
    * @var Integer
    * @access Private
*/
var $inNumLinhas;
/**
    * Total de Linhas independente de paginação para exibição em listas e grids
    * @var Integer
    * @access Private
*/
var $inTotalLinhas;
/**
    * @var Integer
    * @access Private
*/
var $inNumColunas;
/**
    * @var Integer
    * @access Private
*/
var $inCorrente;
/**
    * @var Integer
    * @access Private
*/
var $boInicio;
/**
    * @var Array
    * @access Private
*/
var $arStrPad;

/**
    * @var Boolean
    * @access Private
*/
var $boIgnoraNumericVazio;

/**
    * @access Public
    * @param Array $valor
*/
function setElementos($valor) { $this->arElementos      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumLinhas($valor) { $this->inNumLinhas      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setNumColunas($valor) { $this->inNumColunas     = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCorrente($valor) { $this->inCorrente       = $valor; }

/**
    * @access Public
    * @return Array
*/
function getElementos() { return $this->arElementos;  }
/**
    * @access Public
    * @return Integer
*/
function getNumLinhas() { return $this->inNumLinhas;  }
/**
    * @access Public
    * @return Integer
*/
function getNumColunas() { return $this->inNumColunas; }
/**
    * @access Public
    * @return Integer
*/
function getCorrente() { return $this->inCorrente;   }

/**
    * @access Public
    * @param Integer $valor
*/
function setTotalLinhas($valor) { $this->inTotalLinhas      = $valor; }
function getTotalLinhas() { return $this->inTotalLinhas ; }

/**
    * Método Construtor
    * @access Private
*/
function RecordSet()
{
    $this->setElementos     ( 0 );
    $this->setNumLinhas     ( -1 );
    $this->setNumColunas    ( 0 );
    $this->setCorrente      ( 0 );
    $this->boInicio     = true;
    $this->arFormatacao = array();
    $this->arStrPad     = array();
    $this->arElementos  = array();
    $this->boIgnoraNumericVazio = false;
}

/**
    * A partir de um array de elementos, é preenchido o objeto RecordSet
    * @access Public
    * @param Array $arElementos
*/
function preenche($arElementos)
{
    $this->setElementos( $arElementos );
    $inNumElementos = count( $arElementos ) ? count( $arElementos ) : -1 ;
    $this->setNumLinhas( $inNumElementos );
    if ($inNumElementos) {
        $inNumColunas = 0;
        if (is_array($arElementos)) {
            $inNumColunas = count( current( $arElementos ) );
        }
        $this->setNumColunas( $inNumColunas );
        $this->proximo();
    }
    $this->setPrimeiroElemento();
}

/**
    * A partir de um array de elementos, é preenchido o objeto RecordSet
    * @access Public
    * @param Array $arElementos
*/
function add($arRegistro)
{
    /* verifica se recordset esta vazio */
    if ( $this->getNumLinhas() < 1 ) {
        /* se estiver adiciona o registro em um array */
        $arElementos = array();
        $arElementos[] = $arRegistro;
        /* preenche recordset*/
        $this->preenche ( $arElementos );

        return null;
    }
    /* adiciona novo registro */
    $this->arElementos[] = $arRegistro ;

    /* atualiza numero de linhas */
    $inNumElementos = count( $this->arElementos ) ;
    $this->setNumLinhas( $inNumElementos );

    return null;

}

/**
    * Gera um array com com os campos que devem ser formatados.
    * @access Public
    * @param  String $stCampo Nome do campo
    * @param  String $stFormatacao Tipo da formatação que o campo tera
*/
function addFormatacao($stCampo, $stFormatacao)
{
    if (strpos($stFormatacao,"MB_SUBSTRING") === 0 ) {
        $stParametros = substr($stFormatacao,13);
        $stParametros = str_replace(")","",$stParametros);
        $this->arFormatacao[$stCampo]        = "MB_SUBSTRING";
        $this->arFormatacao[$stCampo."PSUB"] = $stParametros;
    } elseif (strpos($stFormatacao,"SUBSTRING") === 0) {
        $stParametros = substr($stFormatacao,10);
        $stParametros = str_replace(")","",$stParametros);
        $this->arFormatacao[$stCampo]        = "SUBSTRING";
        $this->arFormatacao[$stCampo."PSUB"] = $stParametros;
    } else {
        $this->arFormatacao[$stCampo] = $stFormatacao;
    }
}

function addStrPad($stCampo, $inTamanho = 1, $stString = "0", $stAlinhamento = "L")
{
    $this->arStrPad[$stCampo]["tamanho"] = $inTamanho;
    $this->arStrPad[$stCampo]["string"] = $stString;
    $this->arStrPad[$stCampo]["alinhamento"] = $stAlinhamento;
}

function getObjeto()
{
    $arElementos = $this->getElementos();
    $inCorrente = $this->getCorrente() - 1 ;

    return $arElementos[$inCorrente];
}
/**
    * Adicionar valor no registro corrente
    * @access Public
    * @param  String $stCampo Nome do Campo
    * @param  Mixed $mixedValor Valor a ser inserido no campo
    * @return void
*/
function setCampo($stCampo , $mixedValor, $boTodos = FALSE)
{
    if ($boTodos) {
        $this->setPrimeiroElemento();
        while ( !$this->eof() ) {
            $this->setCampo( $stCampo, $mixedValor );
            $this->proximo();
        }
        $this->setPrimeiroElemento();
    } else {
        $inCorrente = $this->getCorrente() - 1 ;
        $this->arElementos[ $inCorrente ][ $stCampo ] = $mixedValor;
    }

    return null;

}

/**
    * Retorna o valor do campo informado, referente a linha corrente.
    * @access Public
    * @param  String $stCampo Nome do campo
    * @return Mixed  Valor do campo
*/
function getCampo($stCampo)
{
    $stCampoRetorno="";
    $arElementos = $this->getElementos();
    $inCorrente = $this->getCorrente() - 1 ;
    if ( array_key_exists($stCampo,$this->arFormatacao)) {
        switch ($this->arFormatacao[$stCampo]) {
            case "CONTABIL":
                if ($arElementos[$inCorrente][$stCampo] > 0) {
                    $stCampoRetorno = number_format ( abs($arElementos[$inCorrente][$stCampo]), 2, ",", ".")." D";
                } elseif ($arElementos[$inCorrente][$stCampo] < 0) {
                    $stCampoRetorno = number_format ( abs($arElementos[$inCorrente][$stCampo]), 2, ",", ".")." C";
                } elseif ($arElementos[$inCorrente][$stCampo] == 0 && $arElementos[$inCorrente][$stCampo] != '') {
                    $stCampoRetorno = number_format(abs($arElementos[$inCorrente][$stCampo]), 2, ',', '.');
                } else {
                    $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
                }
            break;
            case "NUMERIC_BR":
                    if ($this->getIgnoraNumericVazio() && strlen ($arElementos[$inCorrente][$stCampo]) == 0 ) {
                            $stCampoRetorno = '';
                    } else {
                            if (isset($arElementos[$inCorrente][$stCampo]) && $arElementos[$inCorrente][$stCampo] != '') {
                                $stCampoRetorno = number_format ( $arElementos[$inCorrente][$stCampo], 2, ",", ".");
                            }
                    }
            break;
            case "NUMERIC_BR_4":
                    if ($this->getIgnoraNumericVazio() && strlen ($arElementos[$inCorrente][$stCampo]) == 0 ) {
                        $stCampoRetorno = '';
                    } else {
                        if ($arElementos[$inCorrente][$stCampo] != '') {
                            $stCampoRetorno = number_format ( $arElementos[$inCorrente][$stCampo], 4, ",", ".");
                        }
                    }
            break;
            case "NUMERIC_BR_NULL":
                    if ($this->getIgnoraNumericVazio() && strlen ($arElementos[$inCorrente][$stCampo]) == 0 ) {
                            $stCampoRetorno = '';
                    } else {
                            if (isset($arElementos[$inCorrente][$stCampo]) && $arElementos[$inCorrente][$stCampo] != '') {
                                $stCampoRetorno = number_format ( $arElementos[$inCorrente][$stCampo], 2, ",", ".");
                            }
                    }
                $stCampoRetorno = !$stCampoRetorno ? $stCampoRetorno : number_format ( $arElementos[$inCorrente][$stCampo], 2, ",", ".");
            break;
            case "NUMERIC_EN":
                if ($this->getIgnoraNumericVazio() && strlen ($arElementos[$inCorrente][$stCampo]) == 0 ) {
                            $stCampoRetorno = '';
                    } else {
                    $stCampoRetorno = number_format ( $arElementos[$inCorrente][$stCampo], 2, ".");
                }
            break;
            case "NUMERIC_EN_NULL":
                    if ($this->getIgnoraNumericVazio() && strlen ($arElementos[$inCorrente][$stCampo]) == 0 ) {
                            $stCampoRetorno = '';
                    } else {
                    $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
                    $stCampoRetorno = !$stCampoRetorno ? $stCampoRetorno : number_format ( $arElementos[$inCorrente][$stCampo], 2, ".");
                            }
            break;
            case "N_TO_BR":
                $stCampoRetorno = str_replace(chr(13).chr(10),"<br>", $arElementos[$inCorrente][$stCampo]);
            break;
            case "BR_TO_N":
                $stCampoRetorno = str_replace("<br>",chr(13).chr(10), $arElementos[$inCorrente][$stCampo]);
            break;
            case "HTML":
                 $stCampoRetorno = htmlspecialchars( $arElementos[$inCorrente][$stCampo], ENT_QUOTES );
            break;
            case "SLASHES":
                $stCampoRetorno = addslashes( $arElementos[$inCorrente][$stCampo] );
            break;
            case "STRIPSLASHES":
                $stCampoRetorno = stripslashes( $arElementos[$inCorrente][$stCampo] );
            break;
            case "KM":
                $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
                $stCampoRetorno = !$stCampoRetorno ? $stCampoRetorno : number_format ( $arElementos[$inCorrente][$stCampo], 1, ",", ".");
            break;
            case "SUBSTRING":
                $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
                $arParametros   = explode(",",$this->arFormatacao[$stCampo."PSUB"]);
                $stCampoRetorno = substr($stCampoRetorno,$arParametros[0],$arParametros[1]);
            break;
            case "MB_SUBSTRING":
                $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
                $arParametros   = explode(",",$this->arFormatacao[$stCampo."PSUB"]);
                $stCampoRetorno = mb_substr($stCampoRetorno,$arParametros[0],$arParametros[1],'utf-8');
            break;
            case "SEM_ASPAS":
                $stCampoRetorno = str_replace( '\'', '', $arElementos[$inCorrente][$stCampo] );
                $stCampoRetorno = str_replace( '"' , '', $stCampoRetorno );
            break;
            case "HTMLENTITIES":
                $stCampoRetorno =  htmlentities( $arElementos[$inCorrente][$stCampo] );
            break;
            case "DATA_BR":
                if ($arElementos[$inCorrente][$stCampo]<>'') {
                    $stCampoRetorno = date('d/m/Y',strtotime($arElementos[$inCorrente][$stCampo]));
                } else {
                    $stCampoRetorno = "";
                }
            break;
            default:
                $stCampoRetorno = $arElementos[$inCorrente][$stCampo];
            break;
        }
    } else {
        ## bkp -> $stCampoRetorno = $arElementos[$inCorrente][$stCampo];

        $stCampoRetorno = (array_key_exists($inCorrente,$arElementos) && (!empty($stCampo) && array_key_exists($stCampo, $arElementos[$inCorrente]))) ? $arElementos[$inCorrente][$stCampo] : '';
    }
    if ( array_key_exists($stCampo,$this->arStrPad)) {
        if ($this->arStrPad[$stCampo]) {
            switch ( strtolower( $this->arStrPad[$stCampo]["alinhamento"] ) ) {
                case "e" :
                case "l" :
                       $stCampoRetorno = str_pad($arElementos[$inCorrente][$stCampo] , $this->arStrPad[$stCampo]["tamanho"], $this->arStrPad[$stCampo]["string"], STR_PAD_LEFT );
                break;
                case "d" :
                case "r" :
                        $stCampoRetorno = str_pad($arElementos[$inCorrente][$stCampo] , $this->arStrPad[$stCampo]["tamanho"], $this->arStrPad[$stCampo]["string"], STR_PAD_RIGHT );
                break;
                case "c" :
                        $stCampoRetorno = str_pad($arElementos[$inCorrente][$stCampo] , $this->arStrPad[$stCampo]["tamanho"], $this->arStrPad[$stCampo]["string"], STR_PAD_BOTH );
                break;
                default: $stAlinhamento = $this->arStrPad[$stCampo]["alinhamento"];
            }

        }
    }

    return $stCampoRetorno;
}
/**
    * Efetua verificação na linha corrente.
    * Retorna true caso o RecordSet esteja posicionado no início.
    * @access Public
    * @return Boolean
*/
function bof()
{
    if ( $this->getCorrente() < 1 ) {
        $boRetorno = true;
    } else {
        $boRetorno = false;
    }

    return $boRetorno;
}
/**
    * Efetua verificação na linha corrente.
    * Retorna true caso o RecordSet esteja posicionado no final.
    * @access Public
    * @return Boolean
*/
function eof()
{
    if ( $this->getNumLinhas() < $this->getCorrente() ) {
        $boRetorno  = true;
    } else {
        $boRetorno = false;
    }

    return $boRetorno;
}
/**
    * Avança uma linha do RecordSet.
    * Retorna false caso não existam mais elementos ou já esteja na última linha.
    * @access Public
    * @return Boolean
*/
function proximo()
{
    $this->setCorrente( $this->getCorrente() + 1 );
    if ( $this->eof() ) {
        $boRetorno = false;
    } else {
        $boRetorno = true;
    }

    return $boRetorno;
}

function each()
{
    if ($this->boInicio) {
        $this->setCorrente( 0 );
        $this->boInicio = false;
    }

    return $this->proximo();
}

/**
    * Recua uma linha do RecordSet.
    * Retorna false caso já seja a primeira linha.
    * @access Public
    * @return Boolean
*/
function anterior()
{
    if ( $this->bof() ) {
        $boRetorno = false;
    } else {
        $this->setCorrente( $this->getCorrente() - 1 );
        $boRetorno = true;
    }

    return $boRetorno;
}
/**
    * Seta o ponteiro do RecordSet para a primeira linha.
    * @access Public
    * @return Boolean
*/
function setPrimeiroElemento()
{
    $this->setCorrente( 1 );

    return true;
}
/**
    * Seta o ponteiro do RecordSet para a última linha.
    * @access Public
    * @return Boolean
*/
function setUltimoElemento()
{
    $inUltimo = $this->getNumLinhas();
    $this->setCorrente( $inUltimo );

    return true;
}
/**
    * Ordena o RecordSet conforme o campo informado.
    * @access Public
    * @param  String $stCampo Campo pelo qual será ordenado o RecordSet
    * @param  String $stOrdem Modo de ordenação do RecordSet. ASC ou DESC
*/
function ordena($stCampo , $stOrdem = "ASC", $stTipo = SORT_NUMERIC)
{
    if ($stCampo) {
        $inCorrente = $this->getCorrente();
        $arIndices  = array();
        $arRecordSet = array();
        $this->setPrimeiroElemento();

        while (!$this->eof()) {
            $arIndices[] = $this->getCampo( $stCampo );
            $this->proximo();
        }

        if ($stOrdem=="ASC") {
            sort($arIndices, $stTipo );
        } elseif ($stOrdem=="DESC") {
            rsort($arIndices, $stTipo );
        }

        reset($arIndices);
        $arElementos = $this->getElementos();

        for ($inCount=0; $inCount<count($arIndices); $inCount++) {
            for ($inElem=0; $inElem<count($arElementos); $inElem++) {

                if ((array_key_exists($inCount, $arIndices) && array_key_exists($inElem, $arElementos)) && ($arIndices[$inCount] == $arElementos[$inElem][$stCampo] && $arElementos[$inElem][$stCampo] != NULL) ) {
                    $arRecordSet[] = $arElementos[$inElem];
                    $arElementos[$inElem][$stCampo] = NULL;
                    break;
                }
            }
        }
        $this->setCorrente(0);
        $this->preenche( is_array($arRecordSet) ? $arRecordSet : array() );
    }
}

function retornaValoresRecordSet($stCampo)
{
    $stOut = "";

    if (strstr($stCampo,'[') || strstr($stCampo,']')) {
        for ($inCount=0; $inCount<strlen($stCampo); $inCount++) {
            if ($stCampo[ $inCount ] == '[') $inInicial = $inCount;
            if (($stCampo[ $inCount ] == ']') && isset($inInicial) ) {
                $stOut .= $this->getCampo( trim( substr($stCampo,$inInicial+1,(($inCount-$inInicial)-1)) ) );
                unset($inInicial);
            }elseif( !isset($inInicial) )
                $stOut .= $stCampo[ $inCount ];
        }
    } else {
        $stOut = $this->getCampo( $stCampo );
    }

    return $stOut;
}

/*
    * Soma os campos do recordSet
    * @access Public
    * @param $stCampo Campos que deseja somar
*/
function getSomaCampo($stCampo)
{
    $arChave = explode(',',str_replace(' ','',$stCampo));
    $arElementos = $this->getElementos();
    foreach ($arElementos as $arCampos) {
        foreach ($arCampos as $stNomes => $inValores) {
            if ( in_array($stNomes, $arChave) ) {
                if ( is_numeric($inValores) ) {
                    $arRetorno[$stNomes] += $inValores;
                } else {
                    $arRetorno[$stNomes] = 'CAMPO NÃO NUMÉRICO';
                }
            }
        }
    }

    return $arRetorno;
}

/**
    *
*/

function getJsonString()
{
    if ( function_exists ( "json_encode" ) ) {
        $this->setPrimeiroElemento();
        //return json_encode( $this->arElementos  );
        return json_encode( $this );
    } else {
        echo "<strong>Extensão Json não encontrada</strong><br>";
        echo "É necessario a instalação da extensão Json, utilize o comando:<br>";
        echo "<pre># pecl install json</pre>";
    }

    return "<strong>Extensão Json não encontrada</strong><br>";
}

function setLarguraOption($stCampo, $inMaxTam = 85)
{
    $inCount = 0;
    foreach ($this->arElementos as $key => $item) {
        $arTmp[$key] = $item;
        foreach ($item as $campo => $value) {
            if (strtolower($campo) == strtolower($stCampo)) {
                $arTmp[$key][$campo] = substr($value,0,$inMaxTam);
            }
        }
    }
    $this->arElementos = $arTmp;

}

/**
    * Define o atributo para ignorar numéricos vazios ('')
    * @access Public
    * @param Boolean
    * @return Void
    *
    * O atributo bo IgnoraNumericVazio é utilizado no método getCampo
    * para retornar os campos de tipo NUMERIC.
    * Se boIgnoraNumericVazio estiver definido como true, então
    * os campos tipo numeric que não tiverem conteúdo não passarão pelo
    * number_format, o que faz retornar sempre 0,00.
*/
function setIgnoraNumericVazio($boValor)
{
    $this->boIgnoraNumericVazio = (boolean) $boValor;
}

/**
    * Retorna o atributo para ignorar numéricos vazios ('')
    * @access Public
    * @return Boolean
*/
function getIgnoraNumericVazio()
{
    return $this->boIgnoraNumericVazio;
}

}

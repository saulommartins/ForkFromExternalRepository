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
* Gerar os links de paginação, baseado num objeto recordset, de acordo
* com os valores setados pelo usuário
* Data de Criação: 05/02/2003

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera os links de paginação

    * @package framework
    * @subpackage componentes
*/
class Paginacao extends Objeto
{
/**
    * @access Private
    * @var Integer
*/
var $inMaxLinhas;       //DETERMINA O MÁXIMO DE LINHAS DE RESULTADOS QUE A PAGINA TERA

/**
    * @access Private
    * @var Integer
*/
var $inNumeroLinhas;    //DETERMINA O NÚMERO DE REGISTROS QUE SERA PAGINADO

/**
    * @access Private
    * @var Integer
*/
var $inMaxPaginas;      //DETERMINA O MÁXIMO DE LINKS QUE SERAO MOSTRADOS NA PAGINA

/**
    * @access Private
    * @var Integer
*/
var $inMaxCaracteres;   //DETERMINA O NUMERO MAX. DE CARACTERES DOS LINKS. 47 CORRESPONDE A 10 LINKS DE 2 CARAC.

/**
    * @access Private
    * @var String
*/
var $stFiltro;          //CASO EXISTAO FILTROS DERVERAO SER SETADOS NESTA VARIAVEL

/**
    * @access Private
    * @var Integer
*/
var $inPagAtual;        //NUMERO DA PAGINA QUE ESTA SENDO VISUALIZADA

/**
    * @access Private
    * @var Integer
*/
var $inPosPagina;       //DETERMINA A POGICAO DA PAGINA ATUAL NOS LINKS

/**
    * @access Private
    * @var String
*/
var $stClass;           //CASO EXISTA ALGUMA CLASSE DEFINIDA PARA LINKS NO SISTEMA, ESTA DEVERA SER ATRIBUIDA A ESTA PROPIEDADE

/**
    * @access Private
    * @var Object
*/
var $obRecordSet;       //OBJETO RECORDSET QUE SERA PAGINADO

/**
    * @access Private
    * @var String
*/
var $stLink;

/**
    * @access Private
    * @var Integer
*/
var $inBlocoAnt;

/**
    * @access Private
    * @var Integer
*/
var $inBlocoPos;

/**
    * @access Private
    * @var Integer
*/
var $inContador;        //CONTADOR DE REGISTROS POR PAGINA, TEM SEMPRE O VALOR DO PRIMEIRO REGISTRO DE CADA PAGINA

/**
    * @access Private
    * @var String
*/
var $stHTML;

//SETTERS
/**
    * @access Public
    * @param Integer $Valor
*/
function setMaxLinhas($valor) { $this->inMaxLinhas          = $valor; }
/**
    * @access Public
    * @param Integer $Valor
*/
function setNumeroLinhas($valor) { $this->inNumeroLinhas       = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setMaxPaginas($valor) { $this->inMaxPaginas         = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setMaxCaracteres($valor) { $this->inMaxCaracteres      = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setFiltro($valor) { $this->stFiltro             = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setPagAtual($valor) { $this->inPagAtual           = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setPosPagina($valor) { $this->inPosPagina          = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setClass($valor) { $this->stClass              = $valor; }

/**
    * @access Public
    * @param Object $Valor
*/
function setRecordSet(&$valor)
{
    $this->obRecordSet = $valor;
    global $boPaginacao;

    if ($boPaginacao) {
        $this->setNumeroLinhas( Sessao::getNumeroLinhas() );
    } else {
        $this->setNumeroLinhas( $this->obRecordSet->getNumLinhas() );
    }
}

/**
    * @access Public
    * @param String $Valor
*/
function setLinks($valor) { $this->stLinks              = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setBlocoAnt($valor) { $this->inBlocoAnt           = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setBlocoPos($valor) { $this->inBlocoPos           = $valor; }

/**
    * @access Public
    * @param Integer $Valor
*/
function setContador($valor) { $this->inContador           = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setHTML($valor) { $this->stHTML               = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getMaxLinhas() { return $this->inMaxLinhas;          }
/**
    * @access Public
    * @return Integer
*/
function getNumeroLinhas() { return $this->inNumeroLinhas;       }

/**
    * @access Public
    * @return Integer
*/
function getMaxPaginas() { return $this->inMaxPaginas;         }

/**
    * @access Public
    * @return Integer
*/
function getMaxCaracteres() { return $this->inMaxCaracteres;      }

/**
    * @access Public
    * @return String
*/
function getFiltro() { return $this->stFiltro;             }

/**
    * @access Public
    * @return Integer
*/
function getPagAtual() { return $this->inPagAtual;           }

/**
    * @access Public
    * @return Integer
*/
function getPosPagina() { return $this->inPosPagina;          }

/**
    * @access Public
    * @return String
*/
function getClass() { return $this->stClass;              }

/**
    * @access Public
    * @return Object
*/
function getRecordSet() { return $this->obRecordSet;          }

/**
    * @access Public
    * @return String
*/
function getLinks() { return $this->stLinks;              }

/**
    * @access Public
    * @return Integer
*/
function getBlocoAnt() { return $this->inBlocoAnt;           }

/**
    * @access Public
    * @return Integer
*/
function getBlocoPos() { return $this->inBlocoPos;           }

/**
    * @access Public
    * @return Integer
*/
function getContador() { return $this->inContador;           }

/**
    * @access Public
    * @return String
*/
function getHTML() { return $this->stHTML;               }

/**
    * Método construtor
    * @access Public
*/
function Paginacao()
{
    $stFiltro = "";

    if (is_array($_REQUEST)) {
        foreach ($_REQUEST as $stIndice => $stValor) {
            if ( $stIndice != 'pg' and $stIndice != 'pos' and $stIndice != 'PHPSESSID' and $stIndice != 'sw_Max_Linhas' and !empty( $stValor ) ) {
                if (is_array($stValor)) {
                    $stValor = implode(',', $stValor);
                }
                $stFiltro .= "&".$stIndice."=".$stValor;
            }
        }
    }
    if ( empty($_GET['pg']) ) {
        $_GET['pg'] = $_GET['pos'] = 1;
    }

    $this->setMaxLinhas     ( isset($_COOKIE['sw_Max_Linhas']) ? $_COOKIE['sw_Max_Linhas']  : 10 );
    $this->setMaxPaginas    ( 10 );
    $this->setMaxCaracteres ( 47 );
    $this->setContador      ( 1 );
    $this->setPagAtual      ( $_GET['pg'] );
    $this->setPosPagina     ( $_GET['pos'] );
    $this->setFiltro        ( $stFiltro );

    return true;
}

//METODOS DA CLASSE
/**
    * FALTA DESCRICAO
    * @access Public
    * @return Integer
*/
function geraContador()
{
    global $boPaginacao;
    if (!$boPaginacao) {
        $inPagAtual = $this->getPagAtual();
        if ($inPagAtual > 1) {
            if ( $this->getNumeroLinhas() < ( ( ( $inPagAtual - 1 ) * $this->getMaxLinhas() ) + 1 ) ) {
                $inPagAtual--;
                $this->setPosPagina( $this->getPosPagina() - 1 );
                $this->setPagAtual( $inPagAtual );
            }
        }
        $inContador = ( ( $inPagAtual - 1 ) * $this->getMaxLinhas() ) + 1 ;
    } else {
         $inContador = 1;
    }
    $this->setContador( $inContador );

    return $inContador;
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function geraStrLinks()
{
    $stLink = "";
    $inPagAtual = $this->getPagAtual();
    for ( $inCount = $this->getPosPagina(); $inCount >= 1; $inCount-- ) {
        if ( str_pad( $inPagAtual, 2, "0", STR_PAD_LEFT )." | ".$stLink ) {
            if ( !strlen( $stLink ) ) {
                $stLink = str_pad( $inPagAtual, 2, "0", STR_PAD_LEFT );
             } else {
                $stLink = str_pad( $inPagAtual, 2, "0", STR_PAD_LEFT )." | ".$stLink;
            }
            $inPagAtual--;
        } else {
            break;
        }
    }
    $this->setBlocoAnt( $inPagAtual );
    $inPagAtual = $this->getPagAtual() + 1;
    $inCount = $this->getPosPagina() + 1;
    while ( $inCount <= $this->getMaxPaginas() and  ( ( $inPagAtual - 1 ) * $this->getMaxLinhas() ) < $this->getNumeroLinhas() ) {
        if ( $this->validaMaxCaracteres( $stLink.str_pad($inPagAtual, 2, "0", STR_PAD_LEFT)." | " ) ) {
            $stLink .= " | ".str_pad($inPagAtual, 2, "0", STR_PAD_LEFT);
            $inPagAtual++;
        } else {
            break;
        }
        $inCount++;
    }
    $this->setBlocoPos( $inPagAtual );
    $this->setLinks( trim( $stLink ) );
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function geraHrefLinks()
{
    $arNumPaginas = explode( "|", $this->getLinks() );
    $stLink = "";
    $inPosicao = 1;
    // parte do mini-hack abaixo
    $this->arLinksPaginas = array();

    if ( $this->getFiltro() ) {
        $stFiltro = $this->getFiltro();
    } else {
        $stFiltro = "";
    }
    
    # Foreach para montar o número de páginas, ex: 1|2|3|4...
    foreach ($arNumPaginas as $stLinks) {
        if ( (integer) $stLinks == $this->getPagAtual() ) {
            $stLink .= "<li class='active'><a>".trim( $stLinks )."</a></li>";
        } else {
            $stLink .= "<li><a href=\"".$_SERVER["PHP_SELF"]."?".Sessao::getId()."&pg=".(integer) $stLinks."&pos=".$inPosicao.$stFiltro."\">".trim( $stLinks )."</a></li> ";
        }
        // mini hack, salva todos os links em um array
        $this->arLinksPaginas[] = $_SERVER["SCRIPT_NAME"]."?".Sessao::getId()."&pg=".(integer) $stLinks."&pos=".$inPosicao.$stFiltro."";
        $inPosicao++;
    }

    #$stLink = substr($stLink, 0, strlen( $stLink ) - 3);

    $stLinkRecua = "";

    # Link para voltar diversas páginas, conforme o retorno de getMaxPaginas()
    if ( $this->getBlocoAnt() > 1 ) {
        $stLinkRecua .= "<li><a href=\"".$_SERVER["PHP_SELF"]."?".Sessao::getId()."&pg=".(integer) $this->getBlocoAnt()."&pos=".$this->getMaxPaginas().$stFiltro."\"> Voltar ".$this->getMaxPaginas()." </a></li> \n";
    }

    if ( $this->getPosPagina() > 1 or $this->getBlocoAnt() > 1  ) {
        $inPosPagina = ( (integer) $this->getPosPagina() - 1 );
        if ($inPosPagina  <= 0) {
            $inPosPagina = $this->getMaxPaginas();
        }
        $stLinkRecua .= "<li><a href=\"".$_SERVER["PHP_SELF"]."?".Sessao::getId()."&pg=".( (integer) $this->getPagAtual() - 1)."&pos=".$inPosPagina.$stFiltro."\"> Anterior </a></li> ";
    } else {
        $stLinkRecua .= "<li class='disabled'><a> Anterior </a></li>";
    }

    $stLinkAvanca = "";
    if ( $this->getPagAtual() == ( $this->getBlocoPos() - 1 ) ) {
        $inPosPagina = 1;
    } else {
        $inPosPagina = (integer) $this->getPosPagina() + 1;
    }

    if ( $this->getPosPagina() and ( (integer) $this->getPagAtual() * $this->getMaxLinhas() ) < $this->getNumeroLinhas() ) {
        $stLinkAvanca .= " <li><a href=\"".$_SERVER["PHP_SELF"]."?".Sessao::getId()."&pg=".( (integer) $this->getPagAtual() + 1)."&pos=".$inPosPagina.$stFiltro."\"> Próximo </a></li> ";
    } else {
        $stLinkAvanca .= " <li class='disabled'><a> Próximo </a></li>";
    }
    
    # Link para avançar diversas páginas, conforme o retorno de getMaxPaginas()
    if ( $this->getBlocoPos() > 1 and  ( (integer) $this->getBlocoPos() * $this->getMaxLinhas() ) < $this->getNumeroLinhas() ) {
        $stLinkAvanca .= " <li><a href=\"".$_SERVER["PHP_SELF"]."?".Sessao::getId()."&pg=".(integer) $this->getBlocoPos()."&pos=1".$stFiltro."\"> Avançar ".$this->getMaxPaginas()." </a></li> ";
    }

    # Adiciona o estilo de paginação
    $stLink = "<ul class='pagination'>".$stLinkRecua.trim( $stLink ).$stLinkAvanca."</ul>";

    $this->setHTML( $stLink );
}

/**
    * Monta o HTML do Objeto Paginacao
    * @access Public
*/
function montaHTML()
{
    /* se tiver sido montado os links, para trocar numero de registros
    nao precisa rodar novamente esses metodos*/
    if ( count($this->arLinksPaginas) < 1 ) {
      $this->geraStrLinks();
      $this->geraHrefLinks();
    }
}

/**
    * Imprime o HTML do Objeto Paginacao na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    echo $this->getHTML();
}

/**
    * Verifica se o tamanho da string de links é válido
    * @access Public
    * @param String $stLink
    * @return Boolean
*/
function validaMaxCaracteres($stLink)
{
    if ( strlen ( $stLink ) > $this->getMaxCaracteres() ) {
        $boRetorno = false;
    } else {
        $boRetorno = true;
    }

    return $boRetorno;
}

}

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
* Montar o HTML de um datagrid de acordo com os valores setados pelo usuário
* Data de Criação: 02/08/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do DataGrid

    * @package framework
    * @subpackage componentes
*/
class DataGrid extends Formulario
{
/**
    * @access Private
    * @var Integer
*/
var $inQtdLinhas;

/**
    * @access Private
    * @var Integer
*/
var $inColunaCorrente;

/**
    * @access Private
    * @var Integer
*/
var $inLinhaCorrente;

/**
    * @access Private
    * @var Object
*/
var $obComponente;

/**
    * @access Private
    * @var Object
*/
var $obComponenteFuncao;

/**
    * @access Private
    * @var Boolean
*/
var $boFuncao;

/**
    * @access Private
    * @var Boolean
*/
var $boFuncaoLinha;

/**
    * @access Private
    * @var Array
*/
var $arLabelColuna;

/**
    * @access Private
    * @var Array
*/
var $arLabelLinha;

/**
    * @access Private
    * @var Array
*/
var $arID;

/**
    * @access Private
    * @var Array
*/
var $arValor;

/**
    * @access Private
    * @var Array
*/
var $arValorFuncaoCol;

/**
    * @access Private
    * @var String
*/
var $stLabelFuncao;

/**
    * @access Private
    * @var String
*/
var $stLabelFuncaoLinha;

/**
    * @access Private
    * @var String
*/
var $stRotuloLinha;

/**
    * @access Private
    * @var Array
*/
var $arColunas;

/**
    * @access Private
*/
var $funcao;

//SETTERS
/**
    * @access Public
    * @param Integer $valor
*/
function setQtdLinhas($valor) { $this->inQtdLinhas          = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setComponente($valor) { $this->obComponente         = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setComponenteFuncao($valor) { $this->obComponenteFuncao   = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setLabelColuna($valor) { $this->arLabelColuna        = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setLabelLinha($valor) { $this->arLabelLinha         = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setID($valor) { $this->arID                 = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setIDColunas($valor) { $this->arIDColunas          = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setValor($valor) { $this->arValor              = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setValorFuncaoCol($valor) { $this->arValorFuncaoCol     = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncaoColuna($valor) { $this->boFuncao             = $valor; }

/**
    * @access Public
    * @param Boolean $valor
*/
function setFuncaoLinha($valor) { $this->boFuncaoLinha        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLabelFuncaoColuna($valor) { $this->stLabelFuncao        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setLabelFuncaoLinha($valor) { $this->stLabelFuncaoLinha   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setRotuloLinha($valor) { $this->stRotuloLinha        = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setRotuloColunas($valor) { $this->stRotuloColunas      = $valor; }

//GETTERS
/**
    * @access Public
    * @return Integer
*/
function getQtdLinhas() { return $this->inQtdLinhas;          }

/**
    * @access Public
    * @return Object
*/
function getComponente() { return $this->obComponente;         }

/**
    * @access Public
    * @return Object
*/
function getComponenteFuncao() { return $this->obComponenteFuncao;   }

/**
    * @access Public
    * @return Array
*/
function getLabelColuna() { return $this->arLabelColuna;        }

/**
    * @access Public
    * @return Array
*/
function getLabelLinha() { return $this->arLabelLinha;         }

/**
    * @access Public
    * @return Array
*/
function getID() { return $this->arID;                 }

/**
    * @access Public
    * @return Array
*/
function getIDColunas() { return $this->arIDColunas;          }

/**
    * @access Public
    * @return Array
*/
function getValor() { return $this->arValor;              }

/**
    * @access Public
    * @return Array
*/
function getValorFuncaoCol() { return $this->arValorFuncaoCol;     }

/**
    * @access Public
    * @return Boolean
*/
function getFuncaoColuna() { return $this->boFuncao;             }

/**
    * @access Public
    * @return Boolean
*/
function getFuncaoLinha() { return $this->boFuncaoLinha;        }

/**
    * @access Public
    * @return String
*/
function getLabelFuncaoColuna() { return $this->stLabelFuncao;        }

/**
    * @access Public
    * @return String
*/
function getLabelFuncaoLinha() { return $this->stLabelFuncaoLinha;   }

/**
    * @access Public
    * @return String
*/
function getRotuloLinha() { return $this->stRotuloLinha;        }

/**
    * @access Public
    * @return String
*/
function getRotuloColunas() { return $this->stRotuloColunas;      }

/**
    * Método Construtor
    * @access Public
*/
function DataGrid()
{
    parent::Formulario();
    $this->setFuncaoColuna ( false );
    $this->setFuncaoLinha  ( false );
}

//METODOS DA CLASSE
function setLinhasColunas($inLinhas, $inColunas)
{
    $this->inQtdLinhas  = $inLinhas;
    $this->inQtdColunas = $inColunas;
}

function addTituloGrid($stTitulo)
{
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass( $this->getClassTitulo() );
    // Caso seja setada a $this->setFuncaoColuna( true ) adiciona mais dois na quantidade de colunas
    // Caso contrário adiciona mais um na quantidade de colunas
    if ( $this->getFuncaoColuna() ) {
        $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() + 2 );
    } else {
        $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() + 1 );
    }
    $this->ultimaLinha->ultimaCelula->addConteudo( $stTitulo );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();
}

function addRotulo($stTitle, $stRotulo, $inRowspan = "")
{
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( "labelcentercabecalho" );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    $this->ultimaLinha->ultimaCelula->setTitle       ( $stTitle );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    if ($inRowspan) {
        $this->ultimaLinha->ultimaCelula->setRowSpan ( $inRowspan );
    }
    $this->ultimaLinha->commitCelula();
}

/**
    * Rotulo que identifica as colunas
    * @access Public
*/
function addRotuloColunas($stTitle, $stRotulo, $inRowspan = "")
{
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( "labelcentercabecalho" );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    // Caso seja setada a $this->setFuncaoColuna( true ) adiciona mais um na quantidade de colunas
    if ( $this->getFuncaoColuna() ) {
        $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() + 1 );
    } else {
        $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() );
    }
    $this->ultimaLinha->ultimaCelula->setTitle       ( $stTitle );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    if ($inRowspan) {
        $this->ultimaLinha->ultimaCelula->setRowSpan ( $inRowspan );
    }
    $this->ultimaLinha->commitCelula();
}

/**
    * Adiciona o componente no DataGrid
    * @access Public
*/
function addComponenteGrid($obComponente ,  $inRowspan = "")
{
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setAlign       ( "center" );
    $obComponente->montaHTML();
    $stHTML = $obComponente->getHTML();
    //$this->ultimaLinha->ultimaCelula->addComponente  ( $obComponente );
    $this->ultimaLinha->ultimaCelula->addConteudo  ( $stHTML );
    $this->obJavaScript->addComponente  ( $obComponente );
    if ($inRowspan) {
        $this->ultimaLinha->ultimaCelula->setRowSpan ( $inRowspan );
    }
    $this->ultimaLinha->commitCelula();
}

/**
    * Adiciona uma linha para cálculos através de uma função javascript
    * @access Public
*/
function addFuncaoLinha($obComponente ,  $inRowspan = "")
{
    $this->addLinha();
    // Adiciona o rotulo na linha da função caso $this->setFuncaoLinha( true )
    // pegando a informação que é setada em $this->setLabelFuncaoLinha( "Nome do rótulo" )
    if ( $this->getFuncaoLinha() ) {
        $this->addRotulo( "", $this->getLabelFuncaoLinha() );
    }
    for ($inContColunas = 0; $inContColunas < $this->inQtdColunas; $inContColunas++) {
        $this->obComponente->setName( "stFuncaoLinha_".$inContColunas );
        $this->addComponenteGrid( $this->obComponenteFuncao );
    }
    // Adiciona a coluna para função "stFuncaoLinha_(n° da linha)" caso $this->setFuncaoColuna( true )
    if ( $this->getFuncaoColuna() ) {
        $this->obComponente->setName( "stFuncaoColuna_".$inContColunas );
        $this->addComponenteGrid( $this->obComponenteFuncao );
    }
}

/**
    * Monta o DataGrid conforme informações da interface
    * @access Public
*/
function montaGrid()
{
    if ( $this->getRotuloColunas() ) {
        $this->addLinha();
        $this->addRotulo( "" , "&nbsp" );
        $this->addRotuloColunas( "", $this->getRotuloColunas() );
        $this->commitLinha();
    }
    if ( count( $this->arLabelColuna ) ) {
        $this->addLinha();
        if ( $this->getRotuloLinha() ) {
            $this->addRotulo( "" , $this->getRotuloLinha() );
        } else {
            $this->addRotulo( "" , "&nbsp" );
        }
        foreach ($this->arLabelColuna as $stLabelColuna) {
            $this->addRotulo( "", $stLabelColuna );
        }
        // Adiciona o rotulo para a coluna função caso $this->setFuncaoColuna( true )
        // pegando a informação que é setada em $this->setLabelFuncaoColuna( "Nome do rótulo" )
        if ( $this->getFuncaoColuna() ) {
            $this->addRotulo( "", $this->getLabelFuncaoColuna() );
        }
        $this->commitLinha();
    }
    $stNomeComponente       = $this->obComponente->getName();
    $stNomeComponenteFuncao = $this->obComponenteFuncao->getName();
    $stFuncao  = $this->funcao;
    $stFuncao2 = $this->funcao;
    //$stFuncao2 = $this->obComponenteFuncao->obEvento->getOnChange();
    for ($inContLinhas = 0; $inContLinhas < $this->inQtdLinhas; $inContLinhas++) {
        $this->addLinha();
        if ( count( $this->arLabelLinha ) ) {
             $this->addRotulo( "", $this->arLabelLinha[$inContLinhas] );
        }
        for ($inContColunas = 0; $inContColunas < $this->inQtdColunas; $inContColunas++) {
            $this->obComponente->setName( $stNomeComponente."_".$this->arID[$inContLinhas]."_".$inContColunas."_".$inContLinhas );
            $stFuncaoN = str_replace("[inQtdColunas]"     , $this->inQtdColunas , $stFuncao );
            $stFuncaoN = str_replace("[inQtdLinhas]"      , $this->inQtdLinhas  , $stFuncaoN );
            $stFuncaoN = str_replace("[inLinhaCorrente]"  , $inContLinhas       , $stFuncaoN );
            $stFuncaoN = str_replace("[inColunaCorrente]" , $inContColunas      , $stFuncaoN );
            $stFuncaoN = str_replace("[stNomCelula]"      , $stNomeComponente   , $stFuncaoN );
            $stValor = number_format( $this->arValor[$inContLinhas][$inContColunas] , 2 , ',' , '.' );
            $this->obComponente->setValue ( $stValor );
            $this->obComponente->obEvento->setOnChange( $stFuncaoN );
            $this->addComponenteGrid( $this->obComponente );
        }
//         Adiciona a coluna para função "stFuncaoColuna_(n° da linha)" caso $this->setFuncaoColuna( true )
        if ( $this->getFuncaoColuna() ) {
            $this->obComponenteFuncao->setName( $stNomeComponenteFuncao."_".$this->arID[$inContLinhas]."_".$inContLinhas );
            $stFuncaoN2 = str_replace("[inQtdColunas]"     , $this->inQtdColunas      , $stFuncao2 );
            $stFuncaoN2 = str_replace("[inQtdLinhas]"      , $this->inQtdLinhas       , $stFuncaoN2 );
            $stFuncaoN2 = str_replace("[inLinhaCorrente]"  , $inContLinhas            , $stFuncaoN2 );
            $stFuncaoN2 = str_replace("[inColunaCorrente]" , $inContColunas           , $stFuncaoN2 );
            $stFuncaoN2 = str_replace("[stNomCelula]"      , $stNomeComponenteFuncao  , $stFuncaoN2 );
            $stValor = number_format( $this->arValorFuncaoCol[$inContLinhas] , 2 , ',' , '.' );
            $this->obComponenteFuncao->setValue ( $stValor );
            $this->obComponenteFuncao->obEvento->setOnChange( $stFuncaoN2 );
            $this->addComponenteGrid( $this->obComponenteFuncao );
        }
        $this->commitLinha();
    }
//    $this->obComponenteFuncao->obEvento->setOnChange( "Se mudar deu problema" );
//     Adiciona uma Linha para função "stFuncaoLinha_(n° da coluna)" caso $this->setFuncaoLinha( true )
    if ( $this->getFuncaoLinha() ) {
        $this->addFuncaoLinha( $this->obComponente );
    }
}

/**
    * Monta o HTML do Objeto Arvore
    * @access Private
*/
function montaHTML()
{
    $stHtml = "";
    $this->obJavaScript->montaJavaScript();
    $stHtml .= $this->getArquivoJS();
    $stHtml .= $this->obJavaScript->getJavaScript();
    if ( $this->getForm() ) {
        $obForm = $this->getForm();
        $stHtml .= "\n".$obForm->abreForm();
    }
    $arHidden = $this->getHidden();
    if ( count( $arHidden ) ) {
        foreach ($arHidden as $obHidden) {
            $obHidden->montaHTML();
            $stHtml .= $obHidden->getHTML()."\n";
        }
    }
    parent::montaHTML();
    $stHtml .= parent::getHTML();
    if ( $this->getForm() ) {
        $stHtml .= $obForm->fechaForm();
    }
    if ( is_object($this->getIFrame()) ) {
        $this->obIFrame->montaHtml();
        $stHtml .= $this->obIFrame->getHtml();
    }
    parent::setHTML( $stHtml );
}

/**
    * FALTA DESCRICAO
    * @access Private
*/
function montaInnerHTML()
{
    $stHtml = "";
    $arHidden = $this->getHidden();
    if ( count( $arHidden ) ) {
        foreach ($arHidden as $obHidden) {
            $obHidden->montaHTML();
            $stHtml .= $obHidden->getHTML()."\n";
        }
    }
    parent::montaHTML();
    $stHtml .= parent::getHTML();
    if ( is_object($this->getIFrame()) ) {
        $this->obIFrame->montaHtml();
        $stHtml .= $this->obIFrame->getHtml();
    }
    $stHtml = str_replace("\n","",$stHtml);
    $stHtml = str_replace("  ","",$stHtml);
    $stHtml = str_replace("'","\\'",$stHtml);
    parent::setHTML( $stHtml );
}

/**
    * Imprime o HTML do Objeto DataGrid na tela (echo)
    * @access Private
*/
function show()
{
    $this->montaHTML();
    echo parent::getHTML();
}

}

?>

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
* Gerar o componente tipo select multiplo de acordo com os valores setados pelo usuário.
* Data de Criação: 19/02/2003

* @author Lucas Teixeira Stephanou

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML do componente de Ordenação

    * @author  Lucas Teixeira Stephanou
*/
class Ordenacao extends Objeto
{
/*Atributos necessários para adicionar componente*/
/**
    * @access Private
    * @var String
*/
var $stName;
/**
    * @access Private
    * @var String
*/
var $stRotulo;

/**
    * @access Private
    * @var String
*/
var $stTitle;

/**
    * @access Private
    * @var Boolean
*/
var $boNull;

/*Atributos efetivamente utilizados*/
/**
    * @access Private
    * @var String
*/
var $stHtml;
/**
    * @access Private
    * @var String
*/
var $stCampoId;
/**
    * @access Private
    * @var String
*/
var $stCampoDesc;

/**
    * @access Private
    * @var Object
*/
var $obTabela;

/**
    * @access Private
    * @var String
*/
var $rsRecordset;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor) { $this->boNull = $valor;               }

/**
    * @access Public
    * @param String $Valor
*/
function setTitle($valor) { $this->stTitle = $valor;              }

/**
    * @access Public
    * @param String $Valor
*/
function setName($valor) { $this->stName = $valor;               }
/**
    * @access Public
    * @param String $Valor
*/
function setId($valor) { $this->stId   = $valor;               }

/**
    * @access Public
    * @param String $Valor
*/
function setRotulo($valor) { $this->stRotulo = $valor;             }

/**
    * @access Public
    * @param String $Valor
*/
function setHtml($Valor) { $this->stHtml = $Valor;               }
/**
    * @access Public
    * @param String $Valor
*/
function setDefinicao($valor) { $this->stDefinicao  = $valor;         }
/**
    * @access Public
    * @param String $Valor
*/
function setRecordset($valor) { $this->rsRecordset  = $valor;         }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setCampoId($valor) { $this->stCampoId = $valor;               }
/**
    * @access Public
    * @param Boolean $Valor
*/
function setCampoDesc($valor) { $this->stCampoDesc = $valor;               }

/*******************/

/**
    * @access Public
    * @return Boolean
*/
function getNull() { return $this->boNull;                }

/**
    * @access Public
    * @return String
*/
function getTitle() { return $this->stTitle;               }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;                }
/**
    * @access Public
    * @return String
*/
function getId() { return $this->stId  ;                }

/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo;              }
/**
    * @access Public
    * @return String
*/
function getHtml() { return $this->stHtml;                }

/**
    * @access Public
    * @return String
*/
function getDefinicao() { return $this->stDefinicao;           }
/**
    * @access Public
    * @return String
*/
function getRecordset() { return $this->rsRecordset;         }
/**
    * @access Public
    * @return String
*/
function getCampoId() { return $this->stCampoId;         }
/**
    * @access Public
    * @return String
*/
function getCampoDesc() { return $this->stCampoDesc;         }
/**
    * Método construtor
    * @access Public
*/
function Ordenacao()
{
    $this->SetNull(true);
    $this->setName ( "ordenacao" );
    $this->setId   ( "ordenacao" );
    $this->obTabela = new Tabela;

    $this->setDefinicao('ORDENACAO');
}

/**
    * Monta o HTML do Objeto SelectMultiplo
    * @access Protected
*/
function montaHtml()
{
    if ( $this->stName != $this->stId && isset($this->stName) )
        $this->stId = $this->stName;

    // titulos
    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign( 'center' );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle( 'font-size: 13px' );
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( 'Disponíveis' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( '&nbsp;' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign( 'center' );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle( 'font-size: 13px' );
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( 'Selecionados' );
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    // lista 1
    $stHtml = "<ul id=\"" . $this->getId() . "\" class=\"select_ordenado\">\r\n";

    $i = 1;
    while ( !$this->rsRecordset->eof() ) {
        $stId = $this->rsRecordset->getCampo ( $this->getCampoId() ) . "_" . $i++ ;
        $imgSetas = "<img src=\"" . IMG_DRAGDROP . "\" />";
        $stDesc = $imgSetas. " &nbsp; &nbsp; " .$this->rsRecordset->getCampo ( $this->getCampoDesc() );

        $stHtml .="\t <li class=\"orange\" id=\"" . $stId . "\"> " . $stDesc . " </li>\r\n";

        $this->rsRecordset->proximo();
    }
    $stHtml .= "</ul>\n";

    // coloca lista 1 na tabela
    $this->obTabela->inBorder = 1;
    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHtml );
    $this->obTabela->ultimaLinha->commitCelula();

    // entre listas
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( '&laquo; &raquo;' );
    $this->obTabela->ultimaLinha->commitCelula();

    // lista 2
    $stHtml = "<ul id=\"" . $this->getId() . "_2\" class=\"select_ordenado\" style=\"margin-left:15px;margin-right:0px;\">\r\n";

    $i = 1;
    $this->rsRecordset->setPrimeiroElemento();
    while ( !$this->rsRecordset->eof() ) {
        $stId = $this->rsRecordset->getCampo ( $this->getCampoId() ) . "_" . $i++ ;
        $imgSetas = "<img src=\"" . IMG_DRAGDROP . "\" />";
        $stDesc = $imgSetas. " &nbsp; &nbsp; " .$this->rsRecordset->getCampo ( $this->getCampoDesc() );

        $stHtml .="\t <li class=\"orange\" id=\"" . $stId . "\"> " . $stDesc . " </li>\r\n";

        $this->rsRecordset->proximo();
    }

    $stHtml .= "</ul>\n";

    // coloca lista 2 na tabela
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $stHtml );
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    $this->obTabela->montaHtml();
    $stHtml = $this->obTabela->getHtml() ;

    $stHtml .= "<script type=\"text/javascript\" >\r\n";
    $stHtml .= "\tSortable.create('" . $this->getId() . "',
                                    {  dropOnEmpty:true,
                                        containment:[\"" . $this->getId() . "\",\"" . $this->getId() . "_2\"],
                                        ghosting:true,
                                        constraint:false,
                                        onUpdate:function () {
                                                new Ajax.Updater('msg_select_ordenado', '../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/ajax/OASelectOrdenado.php?".Sessao::getId()."',
                                                {
                                                    parameters:Sortable.serialize('" . $this->getId() . "'),
                                                    evalScripts:true,
                                                    asynchronous:true
                                            }               )
                                        }
                                    }
                                )\r\n";
    $stHtml .= "\tSortable.create('" . $this->getId() . "_2',{ dropOnEmpty:true,handle:'handle',containment:[\"" . $this->getId() . "\",\"" . $this->getId() . "_2\"],ghosting:false,constraint:false})\r\n";
    $stHtml .= "</script>\n";

    $stHtml .= "<div id=\"msg_select_ordenado\" style=\"clear:left;\">&nbsp;</div>";

/*
 // <![CDATA[
   Sortable.create("firstlist",
     {dropOnEmpty:true,containment:["firstlist","secondlist"],constraint:false});
   Sortable.create("secondlist",
     {dropOnEmpty:true,handle:'handle',containment:["firstlist","secondlist"],constraint:false});
 // ]]>

Sortable.create('list',
 {
    onUpdate:function () {    new Ajax.Updater('list-info', '/ajax/order', {
                                                                            onComplete:function (request) {
                                                                                new Effect.Highlight('list',{});
                                                                            },
                                                                            parameters:Sortable.serialize('list'),
                                                                            evalScripts:true,
                                                                            asynchronous:true
                                                                         }
                                            )
                    }
 }
)
*/

    $this->setHtml( $stHtml );
}

/**
    * Imprime o HTML do Objeto Label na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHtml();
    echo  $this->getHtml();
}
}

?>

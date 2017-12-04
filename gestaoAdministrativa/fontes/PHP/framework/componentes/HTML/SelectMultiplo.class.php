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

* @author Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gera o HTML do Select multiplo

    * @author Diego Barbosa Victoria
*/
class SelectMultiplo extends ComponenteBase
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
var $stOrdenacao;

/**
    * @access Private
    * @var String
*/
var $stNomeLista1;

/**
    * @access Private
    * @var String
*/
var $stNomeLista2;

/**
    * @access Private
    * @var String
*/
var $stCampoId1;

/**
    * @access Private
    * @var String
*/
var $stCampoId2;

/**
    * @access Private
    * @var String
*/
var $stCampoDesc1;

/**
    * @access Private
    * @var String
*/
var $stCampoDesc2;

/**
    * @access Private
    * @var Object
*/
var $rsRecord1;

/**
    * @access Private
    * @var Object
*/
var $rsRecord2;

/**
    * @access Private
    * @var Object
*/
var $obSelect1;

/**
    * @access Private
    * @var Object
*/
var $obSelect2;

/**
    * @access Private
    * @var Object
*/
var $obTabela;

/**
    * @access Private
    * @var Object
*/
var $obGerenciaSelects;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Private
    * @var String
*/
var $stDefinicao;

/**
    * @access Private
    * @var String
*/
var $stValorPadrao;

/**
 * @access Private
 * @var String
 */
var $stValorLista1;

/**
 * @access Private
 * @var String
 */
var $stValorLista2;

/**
    * @access Public
    * @param Boolean $Valor
*/
function setNull($valor) { $this->boNull = $valor;               }

/**
    * @access Public
    * @param String $Valor
*/
function setTitle($valor) { $this->stTitle = mb_check_encoding($valor, 'UTF-8') ? utf8_decode($valor) : $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setName($valor) { $this->stName = $valor;               }

/**
    * @access Public
    * @param String $Valor
*/
function setRotulo($valor) { $this->stRotulo = $valor;             }

/**
    * @access Public
    * @param String $Valor
*/
function setOrdenacao($Valor) { $this->stOrdenacao = $Valor;           }

/**
    * @access Public
    * @param String $Valor
*/
function setNomeLista1($Valor) { $this->stNomeLista1 = $Valor;         }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoId1($Valor) { $this->stCampoId1 = $Valor;           }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoDesc1($Valor) { $this->stCampoDesc1 = $Valor;         }

/**
    * @access Public
    * @param String $Valor
*/
function setValorLista1($Valor) { $this->stValorLista1 = $Valor;        }

/**
    * @access Public
    * @param String $Valor
*/
function setStyle1($Valor) { $this->obSelect1->setStyle( $Valor ); }

/**
    * @access Public
    * @param String $Valor
*/
function setNomeLista2($Valor) { $this->stNomeLista2 = $Valor;         }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoDesc2($Valor) { $this->stCampoDesc2 = $Valor;         }

/**
    * @access Public
    * @param String $Valor
*/
function setCampoId2($Valor) { $this->stCampoId2 = $Valor;           }

/**
    * @access Public
    * @param String $Valor
*/
function setValorLista2($Valor) { $this->stValorLista2 = $Valor;        }

/**
    * @access Public
    * @param String $Valor
*/
function setStyle2($Valor) { $this->obSelect2->setStyle( $Valor ); }

/**
    * @access Public
    * @param String $Valor
*/
function setRecord1($Valor) { $this->rsRecord1 = $Valor;            }

/**
    * @access Public
    * @param Object $Valor
*/
function setRecord2($Valor) { $this->rsRecord2 = $Valor;            }

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
function setValorPadrao($valor) { $this->stValorPadrao = $valor;        }

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
function getRotulo() { return $this->stRotulo;              }

/**
    * @access Public
    * @return String
*/
function getOrdenacao() { return $this->stOrdenacao;            }

/**
    * @access Public
    * @return String
*/
function getNomeLista1() { return $this->stNomeLista1;          }

/**
    * @access Public
    * @return String
*/
function getCampoId1() { return $this->stCampoId1;            }

/**
    * @access Public
    * @return String
*/
function getCampoDesc1() { return $this->stCampoDesc1;          }

/**
    * @access Public
    * @return String
*/
function getValorLista1() { return $this->stValorLista1;         }

/**
    * @access Public
    * @return String
*/
function getStyle1() { return $this->obSelect1->getStyle(); }

/**
    * @access Public
    * @return String
*/
function getNomeLista2() { return $this->stNomeLista2;          }

/**
    * @access Public
    * @return String
*/
function getCampoDesc2() { return $this->stCampoDesc2;          }

/**
    * @access Public
    * @return String
*/
function getCampoId2() { return $this->stCampoId2;            }

/**
    * @access Public
    * @return String
*/
function getValorLista2() { return $this->stValorLista2;         }

/**
    * @access Public
    * @return String
*/
function getStyle2() { return $this->obSelect2->getStyle(); }

/**
    * @access Public
    * @return Object
*/
function getRecord1() { return $this->rsRecord1;             }

/**
    * @access Public
    * @return Object
*/
function getRecord2() { return $this->rsRecord2;             }

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
function getValorPadrao() { return $this->stValorPadrao;         }

function setDisabled($valor)
{
    $this->obGerenciaSelects->setDisabled( $valor );
    $this->obSelect1->setDisabled( $valor );
    $this->obSelect2->setDisabled( $valor );
}

/**
    * Método construtor
    * @access Public
*/
function SelectMultiplo()
{
    $this->SetNull(true);
    $this->setDefinicao('MULTIPLO');
    $this->obTabela = new Tabela;
    $this->obTabela->setWidth(100);
    $this->obTabela->setBorder(0);
    $this->obSelect1 = new Select;
    $this->obSelect1->setStyle      ( "width: 300px" );
    $this->obSelect1->setMultiple   ( true );
    $this->obSelect1->setSize       ( 10 );
    $this->obSelect2 = new Select;
    $this->obSelect2->setStyle      ( "width: 300px" );
    $this->obSelect2->setMultiple   ( true );
    $this->obSelect2->setSize       ( 10 );
    $this->obGerenciaSelects = new GerenciaSelects;
    $this->stValorPadrao = "";
}

/**
    * Monta o HTML do Objeto SelectMultiplo
    * @access Protected
*/
function montaHtml()
{
    $this->obSelect1->setName       ( $this->getNomeLista1().'[]' );
    $this->obSelect1->setId         ( $this->getNomeLista1() );
    $this->obSelect1->setNull       ( false );
    $this->obSelect1->setCampoId    ( $this->getCampoId1() );
    $this->obSelect1->setCampoDesc  ( $this->getCampoDesc1() );
    $this->obSelect1->setValue      ( $this->getValorLista1() );
    $rsRecord1 = $this->getRecord1();
    $this->obSelect1->preencheCombo ( $rsRecord1 );
    $stOnDblClickSelect1 = $this->obSelect1->obEvento->getOnDblClick();
    $this->obSelect1->obEvento->setOnDblClick("passaItem('document.".$this->obSelect1->getForm().".".$this->getNomeLista1()."','document.".$this->obSelect1->getForm().".".$this->getNomeLista2()."','selecao','".$this->stOrdenacao."');".$stOnDblClickSelect1 );

    $this->obSelect2->setName       ( $this->getNomeLista2().'[]' );
    $this->obSelect2->setId         ( $this->getNomeLista2() );
    $this->obSelect2->setNull       ( false );
    $this->obSelect2->setCampoId    ( $this->getCampoId2() );
    $this->obSelect2->setCampoDesc  ( $this->getCampoDesc2() );
    $this->obSelect2->setValue      ( $this->getValorLista2() );
    $rsRecord2 = $this->getRecord2();
    $this->obSelect2->preencheCombo ( $rsRecord2 );
    $stOnDblClickSelect2 = $this->obSelect2->obEvento->getOnDblClick();
    $this->obSelect2->obEvento->setOnDblClick("passaItem('document.".$this->obSelect2->getForm().".".$this->getNomeLista2()."','document.".$this->obSelect2->getForm().".".$this->getNomeLista1()."','selecao','".$this->stOrdenacao."');".$stOnDblClickSelect2 );

    $this->obGerenciaSelects->setOrdenacao ( $this->getOrdenacao () );
    $this->obGerenciaSelects->setNomeLista1( $this->getNomeLista1() );
    $this->obGerenciaSelects->setNomeLista2( $this->getNomeLista2() );

    $this->obGerenciaSelects->montaHtml();

    $this->obSelect1->montaHtml();
    $this->obSelect2->MontaHtml();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setWidth(20);
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign( 'center' );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle( 'font-size: 13px' );
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( 'Disponíveis' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign( 'center' );
    $this->obTabela->ultimaLinha->ultimaCelula->setWidth(4);
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( '&nbsp;' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setWidth(20);
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign( 'center' );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle( 'font-size: 13px' );
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( 'Selecionados' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setWidth(50);
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( '&nbsp;' );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->commitLinha();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle('padding-top: 5px');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obSelect1->getHTML() );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obGerenciaSelects->getHTML() );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obSelect2->getHTML() );
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo("&nbsp;");
    $this->obTabela->ultimaLinha->commitCelula();

    $this->obTabela->commitLinha();

    $this->obTabela->montaHtml();
    $this->setHtml( $this->obTabela->getHtml() );
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

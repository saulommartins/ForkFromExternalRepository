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
* Gerenciar componentes selects através de botôes
* Data de Criação: 18/02/2003

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe que gerencia selects através de botôes

    * @package framework
    * @subpackage componentes
*/
class GerenciaSelects extends Objeto
{
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
    * @var Object
*/
var $obTabela;

/**
    * @access Private
    * @var Object
*/
var $obBotao1;

/**
    * @access Private
    * @var Object
*/
var $obBotao2;

/**
    * @access Private
    * @var Object
*/
var $obBotao3;

/**
    * @access Private
    * @var Object
*/
var $obBotao4;

/**
    * @access Private
    * @var Integer
*/
var $inTabIndex;

/**
    * @access Private
    * @var Boolean
*/
var $boDisabled;

/**
    * @access Private
    * @var String
*/
var $stStyle;

/**
    * @access Private
    * @var String
*/
var $stHtml;

/**
    * @access Public
    * @param String $valor
*/
function setOrdenacao($Valor) {$this->stOrdenacao = $Valor;   }

/**
    * @access Public
    * @param String $valor
*/
function setNomeLista1($Valor) {$this->stNomeLista1 = $Valor;  }

/**
    * @access Public
    * @param String $valor
*/
function setNomeLista2($Valor) {$this->stNomeLista2 = $Valor;  }

/**
    * @access Public
    * @param Integer $valor
*/
function setTabIndex($Valor) {$this->inTabIndex= $Valor;      }

/**
    * @access Public
    * @param Boolean $valor
*/
function setDisabled($valor)
{
    $this->boDisabled = $valor;
    $this->obBotao1->setDisabled( $valor );
    $this->obBotao2->setDisabled( $valor );
    $this->obBotao3->setDisabled( $valor );
    $this->obBotao4->setDisabled( $valor );
}

/**
    * @access Public
    * @param String $valor
*/
function setEstilo($Valor) {$this->stStyle = $Valor;       }

/**
    * @access Public
    * @param String $valor
*/
function setHtml($Valor) {$this->stHtml = $Valor;        }

/**
    * @access Public
    * @param String $valor
*/
function getOrdenacao() {return $this->stOrdenacao;     }

/**
    * @access Public
    * @param String $valor
*/
function getNomeLista1() {return $this->stNomeLista1;    }

/**
    * @access Public
    * @param String $valor
*/
function getNomeLista2() {return $this->stNomeLista2;    }

/**
    * @access Public
    * @param String $valor
*/
function getTabIndex() {return $this->inTabIndex;        }

/**
    * @access Public
    * @param Boolean $valor
*/
function getDisabled() {return $this->boDisabled;        }

/**
    * @access Public
    * @param String $valor
*/
function getEstilo() {return $this->stStyle;         }

/**
    * @access Public
    * @param String $valor
*/
function getHtml() {return $this->stHtml;          }

/**
    * Método Construtor
    * @access Public
*/
function GerenciaSelects()
{
    $this->obComponente = new Componente();

    $this->obTabela = new Tabela;
    $this->obTabela->setWidth(100);
    $this->obTabela->setBorder(0);

    $this->obBotao1 = new Button;
    $this->obBotao1->setTabIndex($this->getTabIndex());
    $this->obBotao1->setValue(">");
    $this->obBotao1->setDisabled($this->getDisabled());
    $this->obBotao1->setStyle( "width: 35px" );

    $this->obBotao2 = new Button;
    $this->obBotao2->setTabIndex($this->getTabIndex());
    $this->obBotao2->setDisabled($this->getDisabled());
    $this->obBotao2->setStyle( "width: 35px" );
    $this->obBotao2->setValue(">>");

    $this->obBotao3 = new Button;
    $this->obBotao3->setTabIndex($this->getTabIndex());
    $this->obBotao3->setDisabled($this->getDisabled());
    $this->obBotao3->setValue("<<");
    $this->obBotao3->setStyle( "width: 35px" );

    $this->obBotao4 = new Button;
    $this->obBotao4->setTabIndex($this->getTabIndex());
    $this->obBotao4->setDisabled($this->getDisabled());
    $this->obBotao4->setValue("<");
    $this->obBotao4->setStyle( "width: 35px" );
}

/**
    * Monta o HTML do Objeto GerenciaSelects
    * @access Public
*/
function montaHtml()
{
    $stOnclickBt1 = $this->obBotao1->obEvento->getOnClick();
    $stOnclickBt2 = $this->obBotao2->obEvento->getOnClick();
    $stOnclickBt3 = $this->obBotao3->obEvento->getOnClick();
    $stOnclickBt4 = $this->obBotao4->obEvento->getOnClick();

    $this->obBotao1->obEvento->setOnClick("passaItem('document.".$this->obComponente->getForm().".".$this->getNomeLista1()."','document.".$this->obComponente->getForm().".".$this->getNomeLista2()."','selecao','".$this->getOrdenacao()."');".$stOnclickBt1 );
    $this->obBotao2->obEvento->setOnClick("passaItem('document.".$this->obComponente->getForm().".".$this->getNomeLista1()."','document.".$this->obComponente->getForm().".".$this->getNomeLista2()."','tudo','".$this->getOrdenacao()."');".$stOnclickBt2 );
    $this->obBotao3->obEvento->setOnClick("passaItem('document.".$this->obComponente->getForm().".".$this->getNomeLista2()."','document.".$this->obComponente->getForm().".".$this->getNomeLista1()."','tudo','".$this->getOrdenacao()."');".$stOnclickBt3 );
    $this->obBotao4->obEvento->setOnClick("passaItem('document.".$this->obComponente->getForm().".".$this->getNomeLista2()."','document.".$this->obComponente->getForm().".".$this->getNomeLista1()."','selecao','".$this->getOrdenacao()."');".$stOnclickBt4 );

    $this->obBotao1->montaHtml();
    $this->obBotao2->MontaHtml();
    $this->obBotao3->MontaHtml();
    $this->obBotao4->MontaHtml();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obBotao1->getHTML() );
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obBotao2->getHTML() );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle('padding-top:2px');
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obBotao3->getHTML() );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle('padding-top:2px');
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    $this->obTabela->addLinha();
    $this->obTabela->ultimaLinha->addCelula();
    $this->obTabela->ultimaLinha->ultimaCelula->setAlign('center');
    $this->obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obBotao4->getHTML() );
    $this->obTabela->ultimaLinha->ultimaCelula->setStyle('padding-top:2px');
    $this->obTabela->ultimaLinha->commitCelula();
    $this->obTabela->commitLinha();

    $this->obTabela->montaHtml();

    $this->setHtml( $this->obTabela->getHtml() );
}

/**
    * Imprime o HTML do Objeto GerenciaSelects na tela (echo)
    * @access Private
*/
function show()
{
    $this->montaHtml();
    echo  $this->getHtml();
}
}
?>

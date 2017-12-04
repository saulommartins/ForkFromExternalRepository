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
    * Página de Formulário para incluir processo licitatório
    * Data de Criação   : %date%

    * @author Analista: domluc
    * @author Desenvolvedor: domluc

    * @package Table

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.3  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.2  2007/01/25 16:43:46  domluc
Melhorias

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

/**
 * class TableElement
 * Classe Abstrata para qualquer elemento de uma Tabela
 */

class TableElement
{

    /**
     * @access private
     */
    public $name;
    /**
     * @access private
     */
    public $id;
    /**
     * @access private
     */
    public $title;
    /**
     * @access private
     */
    public $class;
    /**
     * @access private
     */
    public $style;
    /**
     * @access private
     */
    public $tag;
    /**
     * @access private
     */
    public $conteudo;
    /**
     * @access private
     */
    public $quebraLinha;
    /**
     * @access private
     */
    public $html;
    /**
     * @access private
     */
    public $onclick;
    /**
     * @access private
     */
    public $ondblclick;
    /**
     * @access private
     */
    public $onblur;
    /**
     * @access private
     */
    public $onmouseover;
    /**
     * @access private
     */
    public $link;
    /**
     * @access private
     */
    public $arCelulas;

    public function TableElement()
    {
        $this->setQuebraLinha("\n");
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getName()
    {
        return $this->name;
    } // end of member function getName

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setName($stValor)
    {
        $this->name = $stValor;
    } // end of member function setName

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setId($stValor)
    {
        $this->id = $stValor;
    } // end of member function setId

    /**
     *
     * @return string
     * @access public
     */
    public function getId()
    {
        return $this->id;
    } // end of member function getId

    /**
     *
     * @return string
     * @access public
     */
    public function getClass()
    {
        return $this->class;
    } // end of member function getClass

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setClass($stValor)
    {
        $this->class = $stValor;
    } // end of member function setClass

    /**
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setTitle($stValor)
    {
        $this->title = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setStyle($stValor)
    {
        $this->style = $stValor;
    }
    /**
     *
     * @return string
     * @access public
     */
    public function getTag()
    {
        return $this->tag;
    } // end of member function getClass

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setTag($stValor)
    {
        $this->tag = $stValor;
    } // end of member function setClass

    /**
     *
     * @return string
     * @access public
     */
    public function getConteudo()
    {
        return $this->conteudo;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setConteudo($stValor)
    {
        $this->conteudo = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getQuebraLinha()
    {
        return $this->quebraLinha;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setQuebraLinha($stValor)
    {
        $this->quebraLinha = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getOnClick()
    {
        return $this->onclick;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setOnClick($stValor)
    {
        $this->onclick = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getOnDblClick()
    {
        return $this->ondblclick;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setOnDblClick($stValor)
    {
        $this->ondblclick = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getOnBlur()
    {
        return $this->onblur;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setOnBlur($stValor)
    {
        $this->onblur = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getOnMouseOver()
    {
        return $this->onmouseover;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setOnMouseOver($stValor)
    {
        $this->onmouseover = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setHtml($stValor)
    {
        $this->html = $stValor;
    }

    public function tagInicial()
    {
        $stHtml  = "<";
        $stHtml .= $this->getTag();

        return $stHtml;
    }
    public function tagFinal()
    {
        $stHtml   = ">";

        return $stHtml;
    }
    public function abreElemento()
    {
        $stHtml  = "<";
        $stHtml .= $this->getTag();
        $stHtml .= $this->montaHtmlAtributosBasicos();
        $stHtml .= ">";

        return $stHtml;
    }

    public function fechaElemento()
    {
        $stHtml  = "</";
        $stHtml .= $this->getTag();
        $stHtml .= ">";

        return $stHtml;
    }

    public function montaHtmlAtributosBasicos()
    {
        $stHtml  = "";

        if ( $this->getName() ) {
            $stHtml .= " name=\"";
            $stHtml .= $this->getName();
            $stHtml .= "\"";
        }
        if ( $this->getTitle() ) {
            $stHtml .= " title=\"";
            $stHtml .= $this->getTitle();
            $stHtml .= "\"";
        }

        if ( $this->getId() ) {
            $stHtml .= " id=\"";
            $stHtml .= $this->getId();
            $stHtml .= "\"";
        }

        if ( $this->getClass() ) {
            $stHtml .= " class=\"";
            $stHtml .= $this->getClass();
            $stHtml .= "\"";
        }
        if ( $this->getStyle() ) {
            $stHtml .= " style=\"";
            $stHtml .= $this->getStyle();
            $stHtml .= "\"";
        }

        //eventos
        if ( $this->getOnClick() ) {
            $stHtml .= " onclick=\"";
            $stHtml .= $this->getOnClick();
            $stHtml .= "\"";
        }
        if ( $this->getOnBlur() ) {
            $stHtml .= " onblur=\"";
            $stHtml .= $this->getOnBlur();
            $stHtml .= "\"";
        }
        if ( $this->getOnDblClick() ) {
            $stHtml .= " ondblclick=\"";
            $stHtml .= $this->getOnDblClick();
            $stHtml .= "\"";
        }
        if ( $this->getOnMouseOver() ) {
            $stHtml .= " onmouseover=\"";
            $stHtml .= $this->getOnMouseOver();
            $stHtml .= "\"";
        }

        return $stHtml;
    }

    public function montaHTML()
    {
        // codigo html
        $stHtml = $this->tagInicial();

        // atributos basicos
        $stHtml .= $this->montaHtmlAtributosBasicos();

        //fecha tag
        $stHtml .= $this->tagFinal();

        // adiciona conteudo
        $stHtml .= $this->getConteudo();

        // fecha elemento
        $stHtml .= $this->fechaElemento();

        $this->setHtml( $stHtml );
    }

} // end of TableElement
?>

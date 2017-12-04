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
    * Classe Table
    * Data de Criação   : %date%

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table
*/

require_once 'TableElement.class.php';

class TablePaging extends TableElement
{

    /**
     * Valor da Cor Condicional padrao
     * @type String
     * @access private
     */
    public $defaultConditionalColor;

    /**
    * Construtor
    */
    public function TablePaging($Table)
    {
        parent::TableElement();
        $this->setTag( 'tpaging' );
        $this->Table = &$Table;

        $this->setId    ( $this->Table->getId() . '_paging' );
        $this->setName  ( $this->Table->getId() . '_paging' );

        $this->setPaginaAtual( 1 );
    }
    /**
     * Seta o valor
     * @param  Integer $inValor
     * @return void
     * @access public
     */
    public function setPaginaAtual($inValor) { $this->inPaginaAtual = $inValor; }
    /**
     * Seta o valor
     * @param  Integer $inValor
     * @return void
     * @access public
     */
    public function setTamanhoPagina($inValor) { $this->inTamanhoPagina = $inValor; }

    /**
     *
     * @return Integer
     * @access public
     */
    public function getPaginaAtual() { return $this->inPaginaAtual; }
    /**
     *
     * @return Integer
     * @access public
     */
    public function getTamanhoPagina() { return $this->inTamanhoPagina; }
    /**
     *
     * @return Integer
     * @access public
     */
    public function getInicio() { return ( $this->inTamanhoPagina * ( $this->inPaginaAtual -1 ) + 1 ); }
    public function addLinha($obLinha)
    {
        $arLinhas= $this->getLinhas();
        $arLinhas[] = $obLinha;
        $this->setLinhas( $arLinhas );
        $this->Linha = $obLinha;
    }
    public function getLinhas()
    {
        return $this->arLinhas;
    }
    public function setLinhas($arValor)
    {
       $this->arLinhas = $arValor;
    }

    public function montaHTML()
    {
        ##$stHtml  = "";
        ##$stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        ##// adiciona tab
        ##$stHtml .= " \t ";

        // abre linha
        $this->addLinha( new TableRow( $this ) );
        $this->Linha->montaHTMLPaging();
        $stHtml .= $this->Linha->getHtml();

        ##$stHtml .= $this->fechaElemento() . $this->getQuebraLinha();

        $this->setHtml( $stHtml );
    }

    public function montaTagInicial()
    {
        $stHtml  = '<div id="'.$this->getId().'" name="'.$this->getName().'" >';

        return $stHtml;
    }

    public function montaTagFinal()
    {
        $stHtml  = '</div>';

        return $stHtml;
    }

} // end of Table

?>

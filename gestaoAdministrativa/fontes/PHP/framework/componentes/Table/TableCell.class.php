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

require_once 'TableElement.class.php';

/**
 * class TableCell
 */
class TableCell extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/
    /**
    * Referencia a Linha a qual pertence
    */
    public $Linha;

    /**
    * Numero de merge em relação as outras linhas
    */
    public $colSpan;

    /**
    * Numero de merge em relação as outras linhas
    */
    public $rowSpan;

    public function TableCell(&$Linha)
    {
        parent::TableElement();
        $this->setTag ( "td" );
        $this->Linha = &$Linha;

        $this->setId     ( $this->Linha->getId() . '_cell_' . ( 1 + count( $this->Linha->arCelulas ) ) ) ;
        $this->setName   ( $this->Linha->getId() . '_cell_' . ( 1 + count( $this->Linha->arCelulas ) ) ) ;
    }

    /**
     *
     * @return integer
     * @access public
     */
    public function getColSpan()
    {
        return $this->colSpan;
    }

    /**
     *
     * @param  integer $inValor
     * @return null
     * @access public
     */
    public function setColSpan($inValor)
    {
        $this->colSpan= $inValor;
    }

    /**
     *
     * @return integer
     * @access public
     */
    public function getRowSpan()
    {
        return $this->rowSpan;
    }

    /**
     *
     * @param  integer $inValor
     * @return null
     * @access public
     */
    public function setRowSpan($inValor)
    {
        $this->rowSpan= $inValor;
    }

    /**
    * Captura Campo e Recordset para ler campos compostos, seta o conteudo normal ao final.
    * @return void
    * @param $Campo String
    * @param $rsRegistros Recordset
    */
    public function montaConteudoComposto($Campo , $rsRegistros)
    {
        $stComposto = "";
        for ($inCount=0; $inCount<strlen($Campo); $inCount++) {
            if ($Campo[ $inCount ] == '[') $inInicialId = $inCount;
            if (($Campo[ $inCount ] == ']') && isset($inInicialId) ) {
                 $stValor = $rsRegistros->getCampo( trim( substr($Campo,$inInicialId+1,(($inCount-$inInicialId)-1)) ) )	;
                 $stComposto .= trim($stValor) != "" ? $stValor : "&nbsp;" ;
                unset($inInicialId);
            } elseif ( !isset($inInicialId) ) {
                $stComposto .= $Campo[ $inCount ];
            }
        }

        return $stComposto;
    }

    public function montaHTML()
    {
        // codigo html
        $stHtml = $this->tagInicial();

        // atributos basicos
        $stHtml .= $this->montaHtmlAtributosBasicos();

        // atributos especificos
        if ( $this->getColSpan() ) {
            $stHtml .= " colspan = \"" . $this->getColSpan() . "\" ";
        }

        //fecha tag
        $stHtml .= $this->tagFinal();

        // adiciona conteudo
        $stHtml .= $this->getConteudo();

        // fecha elemento
        $stHtml .= $this->fechaElemento();

        $this->setHtml( $stHtml );
    }

} // end of TableCell

?>

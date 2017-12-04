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
Revision 1.2  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

require_once 'TableElement.class.php';

/**
 * class TableLink
 */
class TableLink extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
     * @access private
     */
    public $arquivo;
    /**
     * @access private
     */
    public $acao;
    /**
     * @access private
     */
    public $href;

    /**
    * Construtor, recebe referencia a Table a qual pertence
    * @return void
    */
    public function TableLink(&$Cell)
    {
        parent::TableElement();
        $this->setTag ( "a" );
        $this->Cell = &$Cell;

        $this->setHref( '#' );

        $this->setId     ( $this->Cell->getId() . '_Link' ) ;
        $this->setName   ( $this->Cell->getId() . '_Link' ) ;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getArquivo()
    {
    } // end of member function getArquivo

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setArquivo($stValor)
    {
    } // end of member function setArquivo

    /**
     *
     * @return string
     * @access public
     */
    public function getAcao()
    {
    } // end of member function getAcao

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setAcao($stValor)
    {
    } // end of member function setAcao
    /**
     *
     * @return string
     * @access public
     */
    public function getHref()
    {
        return $this->href;
    }

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setHref($stValor)
    {
        $this->href = $stValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function montaHTML()
    {
        // codigo html
        $stHtml = $this->tagInicial();

        // atributos basicos
        $stHtml .= $this->montaHtmlAtributosBasicos();

        // atributos especificos
        $stHtml .= " href=\"" . $this->getHref() . "\"";

        //fecha tag
        $stHtml .= $this->tagFinal();

        // adiciona conteudo
        $stHtml .= $this->getConteudo();

        // fecha elemento
        $stHtml .= $this->fechaElemento();

        $this->setHtml( $stHtml );
    }

} // end of TableLink
?>

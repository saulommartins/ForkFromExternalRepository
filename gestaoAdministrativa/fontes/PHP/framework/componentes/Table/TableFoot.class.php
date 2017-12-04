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
Revision 1.5  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.4  2007/01/26 12:50:05  domluc
Ai

Revision 1.3  2007/01/25 16:43:46  domluc
Melhorias

Revision 1.2  2007/01/24 15:44:27  domluc
Opa

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

require_once 'TableElement.class.php';

/**
 * class TableFoot
 */
class TableFoot extends TableElement
{

    /*** Attributes: ***/

    /**
    * Array de LInhas da Table Body
    * @return Array
    * @access public
    */
    public $arLinhas;

    /**
    * Array de Somas da Linha
    * @access private
    * @var Array
    */
    public $arSomas;

    /**
    * Construtor, recebe referencia a Table a qual pertence
    * @return void
    */
    public function TableFoot(&$Table)
    {
        parent::TableElement();
        $this->setTag( 'tfoot' );
        $this->Table = &$Table;

        $this->setId    ( $this->Table->getId() . '_foot' );
        $this->setName  ( $this->Table->getId() . '_foot' );

        $this->arSomas = array();
    }

    /**
    * @return Array
    * @access public
    */
    public function getLinhas()
    {
        return $this->arLinhas;
    }

    /**
    * @return null
    * @access public
    */
    public function setLinhas($arValor)
    {
       $this->arLinhas = $arValor;
    }

    /**
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    */
    public function addLinha($obLinha)
    {
        $arLinhas= $this->getLinhas();
        $arLinhas[] = $obLinha;
        $this->setLinhas( $arLinhas );
        $this->Linha = $obLinha;
    }

    /**
     * Retorna Array de Somas
     * @return Array
     * @access public
     */
    public function getSomas()
    {
        return $this->arSomas;
    }

    /**
     * Seta Array de Somas
     * @return null
     * @access public
     */
    public function setSomas($arValor)
    {
        $this->arSomas= $arValor;
    }
    /**
     * Adiciona Celula a Linha
     * @return null
     * @access public
     */
    public function addSoma($stCampo , $stAlinhamento = "D")
    {
        $arSomas= $this->getSomas();
        switch ($stAlinhamento) {
                    case "E":
                    case "ESQUERDA":
                    case "L":
                    case "LEFT":
                        $stAlign = "left";
                        break;
                    case "D":
                    case "DIREITA":
                    case "R":
                    case "RIGHT":
                        $stAlign = "right";
                        break;
                    case "C":
                    case "CENTRO":
                    case "CENTER":
                        $stAlign = "center";
                        break;
        }

        $arSomas[] = array( "campo" => $stCampo , "alinhamento" => $stAlign ) ;
        $this->setSomas( $arSomas );
    }

    public function montaHTML()
    {
        // inicializa conteiner html
        if ( !count($this->arSomas) ) {
            $this->setHtml(null);

            return false;
        }

        $stHtml  = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // adiciona tab
        $stHtml .= " \t ";

        // abre linha
        $this->addLinha( new TableRow( $this ) );
        $this->Linha->montaHTMLFoot();
        $stHtml .= $this->Linha->getHtml();

        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();

        $this->setHtml( $stHtml );
    }

} // end of TableFoot
?>

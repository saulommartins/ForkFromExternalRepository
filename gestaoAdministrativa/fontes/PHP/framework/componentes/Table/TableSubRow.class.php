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
    * Classe TableSubRow, responsavel pelo controle das linhas das tabelas do Pacote Table
    * Data de Criação   : %date%

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table
    * @uses TableCell

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.2  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

*/

require_once 'TableElement.class.php';
require_once 'TableCellAction.class.php';

/**
 * class TableRow
 * @access public
 * @see Table
 */
class TableSubRow extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/
    /**
    * Celula da Linha
    * @access public
    * @var Reference Object
    */
    public $Celula;
    /**
    * Array de Celulas da Linha
    * @access private
    * @var Array
    */
    public $arCelulas;
    /**
    * Cabeçalho da Linha
    * @access public
    * @var Reference Object
    */
    public $Cabecalho;
    /**
    * Array de Cabecalhos da Linha
    * @access private
    * @var Array
    */
    public $arCabecalhos;
    /**
    * Guarda referencia ao Pai ( TableBody ou TableHead)
    * @access public
    * @var Reference Object
    */
    public $TBody;

    /**
    *   Construtor da Classe TableRow
    * @access public
    * @see Table
    */
    public function TableSubRow(&$TBody)
    {
        parent::TableElement();
        $this->setTag ( "tr" );

        $this->TBody = &$TBody;

        $this->setId     ( $this->TBody->Table->getId() . '_row_' . ( count( $this->TBody->arLinhas ) ) . '_sub' ) ;
        $this->setName   ( $this->TBody->Table->getId() . '_row_' . ( count( $this->TBody->arLinhas ) ) . '_sub' ) ;

        $this->setStyle( 'display:none' );

    }
    /**
     * Retorna Array de Celulas
     * @return Array
     * @access public
     */
    public function getCelulas()
    {
        return $this->arCelulas;
    }

    /**
     * Seta Array de Celulas
     * @return null
     * @access public
     */
    public function setCelulas($arValor)
    {
        $this->arCelulas = $arValor;
    }
    /**
     * Adiciona Celula a Linha
     * @return null
     * @access public
     */
    public function addCelula($obCelula)
    {
        $arCelulas= $this->getCelulas();
        $arCelulas[] = $obCelula;
        $this->setCelulas( $arCelulas );
        $this->Celula = $obCelula;
    }

    /**
     * Retorna Array de Cabecalhos
     * @return Array
     * @access public
     */
    public function getCabecalhos()
    {
        return $this->arCabecalhos;
    }

    /**
     * Seta Array de Cabecalhos
     * @return null
     * @access public
     */
    public function setCabecalhos($arValor)
    {
        $this->arCabecalhos = $arValor;
    }
    /**
     * Adiciona Cabecalho a Linha
     * @return null
     * @access public
     */
    public function addCabecalho($obCabecalho)
    {
        $arCabecalhos= $this->getCabecalhos();
        $arCabecalhos[] = $obCabecalho;
        $this->setCabecalhos( $arCabecalhos );
        $this->Cabecalho = $obCabecalho;
    }

    /**
     * MontaHtml para Criação de Linha num Container Body
     * @return String
     * @see MontaHTML
     */
    public function montaHTML()
    {
        $stHtml = "";

        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // cria celula de expansão
        $obCell_1 = new TableCell( $this );
        $obCell_1->setId ( $this->getId() . '_cell_1' );
        $obCell_1->setName ( $this->getId() . '_cell_1' );
        $obCell_1->setColSpan( 1 );
        $obCell_1->setConteudo( '&nbsp;');
        $obCell_1->setClass( "_num_sub" );
        $obCell_1->montaHTML();
        $stHtml .= $obCell_1->getHtml() . $this->getQuebraLinha();

        // contar numero de colunas
        $inNumCol = 0;
        $inNumCol += count( $this->TBody->arCampos );

        // celula container
        $obCellContainer = new TableCell( $this );
        $obCellContainer->setId ( $this->getId() . '_cell_2' );
        $obCellContainer->setName ( $this->getId() . '_cell_2' );
        $obCellContainer->setColSpan( $inNumCol );
        $obCellContainer->setConteudo( '&nbsp;');
        $obCellContainer->setClass( "_container_sub" );
        $obCellContainer->montaHTML();
        $stHtml .= $obCellContainer->getHtml() . $this->getQuebraLinha();

        // fecha linha e seta html gerado
        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();
        $this->setHtml( $stHtml );
    }

} // end of TableSubRow
?>

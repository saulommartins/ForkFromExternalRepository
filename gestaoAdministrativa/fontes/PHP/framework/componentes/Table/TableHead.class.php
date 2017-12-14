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
    * Classe TableHead, implementa Tag THead da Table
    * Data de Criação   : 30/11/2006

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table
    * @uses Table
    * @uses TableRow
    * @uses TableElement

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.3  2007/10/15 18:18:20  leandro.zis
correção php5

Revision 1.2  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

require_once 'TableElement.class.php';
require_once 'TableRow.class.php';
require_once 'TableHeader.class.php';

/**
 * class TableHead
 */
class TableHead extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
    * Atributo que guarda referencia a table a qual pertence
    * @type Object
    * @access Public
    */
    public $Table;
    /**
    * Colunas a agrupar , array( 'Titulo' => 15)
    * @return Array
    * @access public
    */
    public $arColunas;
    /**
    * Array de LInhas da Table Body
    * @return Array
    * @access public
    */
    public $arLinhas;
    /**
    * Linhas da Table Body
    * @return Array
    * @access public
    */
    public $Linha;

    public function TableHead(&$Table)
    {
        parent::TableElement();
        $this->setTag( "thead" );

        $this->Table = &$Table;

        if ( strtolower(get_class( $this->Table )) == 'tabletree' ) {
            $this->addCabecalho( 'tabletree' , 1);
        }

        $this->addCabecalho( '&nbsp;' , 3);

    }

    /**
    * @return Array
    * @access public
    */
    public function getColunas()
    {
        return $this->arColunas;
    }

    /**
    * @return null
    * @access public
    */
    public function setColunas($arValor)
    {
       $this->arColunas = $arValor;
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
        $this->Linha = &$obLinha;
    }

    /**
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    */
    public function addCabecalho($stTitulo , $inLargura)
    {
        $arColunas = $this->getColunas();
        $arColunas[ $stTitulo ] = $inLargura;
        $this->setColunas( $arColunas );
    }

    public function montaHTML()
    {
        // inicializa conteiner html
        $stHtml  = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // adiciona tab
        $stHtml .= " \t ";

        // abre linha
        $this->addLinha( new TableRow( $this ) );
        $this->Linha->montaHTMLHead();
        $stHtml .= $this->Linha->getHtml();

        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();

        $this->setHtml( $stHtml );
    }

} // end of TableHead
?>

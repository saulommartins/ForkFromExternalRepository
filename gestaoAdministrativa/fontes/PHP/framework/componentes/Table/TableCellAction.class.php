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
    * Classe TableCellAction, implementa Actions para Table
    * Data de Criação   : 06/12/2006

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
Revision 1.3  2007/07/05 21:51:57  domluc
Melhorias

Revision 1.2  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

*/

require_once 'TableElement.class.php';
require_once 'TableAction.class.php';

/**
 * class TableCellAction
 */
class TableCellAction extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
    * Referencia a Linha a qual pertence
    */
    public $Linha;

    public function TableCellAction(&$Linha)
    {
        parent::TableElement();
        $this->setTag ( "td" );
        $this->Linha = &$Linha;

        $this->setId     ( $this->Linha->getId() . '_Action_' . count( $this->Linha->arCelulas ) ) ;
        $this->setName   ( $this->Linha->getId() . '_Action_' . count( $this->Linha->arCelulas ) ) ;
    }

    /**
    * MOnta Html e Seta Codigo Gerado!
    * @return void
    */
    public function montaHTML()
    {
        // inicializa var html
        $stHtml = "";

        $stHtml .= $this->abreElemento();

        // varre actions table body
        $i = 1;;
        foreach ($this->Linha->TableRef->arActions as $arAction) {
            $Action = new TableAction( $this , $i++);
            $Action->setTipo( $arAction[ 0 ] );
            $Action->setFuncao( $arAction[ 1 ] );
            $Action->setValores( $arAction[ 2 ] );
            $Action->setCondicional( $arAction[ 3 ] );

            $Action->montaHTML();
            $stHtml .= "&nbsp;" . $Action->getHtml();
        }

        $stHtml .= $this->fechaElemento();

        $this->setHtml( $stHtml );
    }

} // end of TableAction

?>

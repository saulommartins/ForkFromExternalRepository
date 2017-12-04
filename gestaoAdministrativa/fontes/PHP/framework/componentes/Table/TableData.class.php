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

/**
 * class TableData
 */
class TableData extends Table
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
     * @access private
     */
    public $mapeamento;
    /**
     * @access private
     */
    public $editar;
    /**
     * @access private
     */
    public $excluir;
    /**
     * @access private
     */
    public $inserir;

    /**
     * Construtor
     * @return
     * @access public
     */
     function TableData ( $stMapeamento = null , $arAcoes= array() )
     {
        // inicializa mapeamento
        $this->mapeamento = $stMapeamento;

        if (!$arAcoes) {
            $this->iniciarAtributos();
        } else {
            $this->setInserir   ( $arAcoes[0] );
            $this->setEditar    ( $arAcoes[1] );
            $this->setExcluir   ( $arAcoes[2] );
        }
     }

    /**
     *
     * @param bool boValor      * @return
     * @access public
     */
    public function setInserir($boValor = true)
    {
        $this->inserir = $boValor;
    } // end of member function setInserir

    /**
     *
     * @return bool
     * @access public
     */
    public function getInserir()
    {
        return $this->inserir;
    } // end of member function getInserir

    /**
     *
     * @param bool boValor      * @return
     * @access public
     */
    public function setEditar($boValor = true)
    {
        $this->editar = $boValor;
    } // end of member function setEditar

    /**
     *
     * @return bool
     * @access public
     */
    public function getEditar()
    {
        return $this->inserir;
    } // end of member function getEditar

    /**
     *
     * @param bool boValor      * @return
     * @access public
     */
    public function setExcluir($boValor = true)
    {
        $this->excluir = $boValor;
    } // end of member function setExcluir

    /**
     *
     * @return bool
     * @access public
     */
    public function getExcluir()
    {
        return $this->excluir;
    } // end of member function getEditar

    /**
     * seta os valores padrao para os atributos da classe
     */
    public function iniciarAtributos()
    {
        $this->editar = true;
        $this->excluir = true;
        $this->inserir = true;
    }

} // end of TableData
?>

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
    * Classe GridPaging, habilita recurso de paginação na Classe Grid
    * Data de Criação   : 30/03/2007

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Grid
    * @component Grid

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.1  2007/04/17 19:49:53  domluc
Grid

*/

/**
 * class Grid
 */
class GridPaging
{
    /**
     * Registros por Pagina
     * @access private
     * @return Integer
     */
    public $inRecordsPerPage;

    /**
     * @access private
     * @return Boolean
     */
    public $boDisplayInfo;

    /**
     * @access private
     * @return String
     */
    public $stDisplayMsg;

    /**
     * @access private
     * @return String
     */
    public $stEmptyMsg;

    /**
     * Referencia ao Objeto Grid
     * @access public
     * @return Grid
     */
    public $Grid;

// SET

    /**
     * Seta Registros por Pagina
     * @access public
     * @param  Integer $value Valor que a o atributo recebera
     * @return null
     */
    public function setRecordsPerPage($value)
    {
        $this->inRecordsPerPage = $value;
    }

    /**
     * @access public
     * @param  Boolean $value Valor que a o atributo recebera
     * @return null
     */
    public function setDisplayInfo($value)
    {
        $this->boDisplayInfo = $value;
    }

    /**
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setDisplayMsg($value)
    {
        $this->stDisplayMsg = $value;
    }

    /**
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setEmptyMsg($value)
    {
        $this->stEmptyMsg = $value;
    }

// GET

    /**
     * Retorna Registros por Pagina
     * @access public
     * @return String
     */
    public function getRecordsPerPage()
    {
        return $this->inRecordsPerPage;
    }

    /**
     * Retorna se mensagem vai ser exibida
     * @access public
     * @return Boolean
     */
    public function getDisplayInfo()
    {
        return $this->boDisplayInfo;
    }

    /**
     * Retorna Mensagem de Exibição
     * @access public
     * @return String
     */
    public function getDisplayMsg()
    {
        return $this->stDisplayMsg;
    }

    /**
     * Retorna Mensagem de Exibição quando vazio
     * @access public
     * @return String
     */
    public function getEmptyMsg()
    {
        return $this->stEmptyMsg;
    }

    /**
     * Construtor <br>
     * Recebe o Grid a ser paginado
     * @access public
     * @param  Grid $Grid Referencia a Objeto Grid
     * @return Grid
     */

    public function GridPaging()
    {
        //padrão atributos
        $this->setRecordsPerPage ( 10 );
        $this->setDisplayInfo ( true );
        $this->setDisplayMsg ( "Mostrando registro {0} - {1} de {2}" );
        $this->setEmptyMsg ( "Nenhum registro encontrado!" );
    }

    /**
     * Função Helper Estatica que persiste paginção a classe de conexão<br>
     * Usada em Arquivos ocultos de forma estatica,Ex: <br>
     * <code>GridPaging::init();</code>
     * @access Public
     * @return null
     */
    public function init()
    {
        global $GridPaging;
        $GridPaging[ 'dir' ]     = $_REQUEST[ 'dir' ];
        $GridPaging[ 'start' ]   = $_REQUEST[ 'start' ];
        $GridPaging[ 'limit' ]   = $_REQUEST[ 'limit' ];
        $GridPaging[ 'sort' ]    = $_REQUEST[ 'sort' ];
    }
}

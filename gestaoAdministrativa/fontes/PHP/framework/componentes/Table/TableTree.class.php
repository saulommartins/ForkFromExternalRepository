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
    * Arquivo que implementa class TableTree, responsavel por estender a Table e dar funcionalidades de expansão
    * Data de Criação   : 12/12/2006

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table

    * Casos de uso : uc-01.01.00
*/

/*
$Log$
Revision 1.8  2007/04/27 14:27:21  domluc
Adicionado Condicional para Exibição do [+]

Revision 1.7  2007/03/12 14:18:11  domluc
*** empty log message ***

Revision 1.6  2007/03/12 14:01:15  domluc
Add opção de mostrar ou não botão que exibe todos os detalhes de uma TableTree

Revision 1.5  2007/02/06 16:56:37  cassiano
Inclusão do id da sessão na URL no arquivo informado.

Revision 1.4  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.3  2007/01/25 16:43:46  domluc
Melhorias

Revision 1.2  2007/01/24 15:44:27  domluc
Opa

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

require_once 'Table.class.php';

/**
 * class TableTree
 */
class TableTree extends Table
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
     * Arquivo de Controle
     *
     * @return null
     */
    public $arquivo;

    /**
     * Campos a enviar
     *
     * @return TableTree
     */
    public $arCamposParametros;

    /**
     * Complemento
     *
     * @return TableTree
     */
    public $stComplementoParametros;

    /**
     * Botao Abrir Todos
     *
     * @return null
     */
    public $boMostrarTodos;

    /**
     * Condicional para Exibição do ([+]) para Expansão
     * Recebe 2 parametros, o primeiro o campo que sera verificado ,
     * e depois um array ou string com o valor necessario para exibição
     *
     * @param  String $stCampo     Campo a ser verificado para exibição
     * @param  Mixed  $mxValorCond Valor Array/String a ser verificado
     * @return null
     */
    public $arCondicionalTree;

    public function TableTree()
    {
        // recupera contexto de execução

        // construtor da mae
        parent::Table();

        // recupera contexto de execução
        $tableId = (integer) Sessao::read('TableContext_Table');
        $tableId = $tableId + 1;
        Sessao::write('TableContext_Table',$tableId);

        // alterar name e id para identifcar que é uma table tree
        $this->setId ( 'sw_tabletree_' . $tableId );
        $this->setName ( 'sw_tabletree_' . $tableId );

        // head
        $this->Head = new TableHead( $this );
        // body
        $this->Body = new TableBody( $this );
        // foot
        $this->Foot = new TableFoot( $this );

        $this->setMostrarTodos( false );

        $this->setCondicionalTree ( null) ;

    }

    /**
     *
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     *
     */
    public function setArquivo($stValor)
    {
        if ( !strpos($stValor, 'SESSION_ID') ) {
            $stValor .= '?'.Sessao::getId();
        }
        $this->arquivo = $stValor;
    }
    /**
     *
     */
    public function getMostrarTodos()
    {
        return $this->boMostrarTodos;
    }

    /**
     *
     */
    public function setMostrarTodos($boValor = true)
    {
        $this->boMostrarTodos = $boValor;
    }

    /**
     *
     */
    public function getParametros()
    {
        return $this->arCamposParametros;
    }

    /**
     *
     */
    public function setParametros($stValor)
    {
        $this->arCamposParametros = $stValor;
    }

    /**
     *
     */
    public function getComplementoParametros()
    {
        return $this->stComplementoParametros;
    }

    /**
     *
     */
    public function setComplementoParametros($stValor)
    {
        $this->stComplementoParametros = $stValor;
    }

    /**
     *
     */
    public function setCondicionalTree($arValor)
    {
        $this->arCondicionalTree = $arValor;
    }

    /**
     *
     */
    public function getCondicionalTree()
    {
        return $this->arCondicionalTree;
    }

    /**
     * Condicional para Exibição do ([+]) para Expansão
     * Recebe 2 parametros, o primeiro o campo que sera verificado ,
     * e depois um array ou string com o valor necessario para exibição
     *
     * @param  String $stCampo     Campo a ser verificado para exibição
     * @param  Mixed  $mxValorCond Valor Array/String a ser verificado
     * @return null
     */
    public function addCondicionalTree($stCampo , $mxValorCond)
    {
        $arCondTree[0] = $stCampo ;
        if ( !is_array( $mxValorCond ) ) {
            $mxValorCond = array( $mxValorCond );
        }
        $arCondTree[1] = $mxValorCond;
        $this->setCondicionalTree ( $arCondTree );
    }

    /**
     *
     */
    public function montaUrlAjax()
    {
        $rsRecordset = $this->registros;

        $stUrl  = $this->getArquivo();
        $stUrl .= "?componente=table_tree";

        foreach ($this->arCamposParametros as $stCampo) {
            $stValorCampo = $rsRecordset->getCampo($stCampo);
            $stUrl .= "&" . $stCampo . "=" . $stValorCampo;
        }

        $stUrl .= "&" . $this->getComplementoParametros();

        return $stUrl;
    }

} // end of TableTree
?>

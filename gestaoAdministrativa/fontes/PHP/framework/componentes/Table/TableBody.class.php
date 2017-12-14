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
    * Classe TableBody, implementa Tag TBody da Table
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
Revision 1.10  2007/10/15 18:18:07  leandro.zis
correção php5

Revision 1.9  2007/07/05 21:51:57  domluc
Melhorias

Revision 1.8  2007/03/15 19:05:49  domluc
Melhorias DIversas

Revision 1.7  2007/02/16 18:17:25  domluc
Add Componente em Linha

Revision 1.6  2007/02/06 13:05:09  cassiano
Alteração para o caso de uso uc-01.01.00.

Revision 1.5  2007/01/25 16:43:46  domluc
Melhorias

Revision 1.4  2007/01/24 15:44:27  domluc
Opa

Revision 1.3  2007/01/24 12:39:58  domluc
Add Alinhamento

Revision 1.1  2006/12/14 16:45:48  domluc
Componente Table/TableTree Movido para Lugar Correto

Revision 1.1  2006/12/04 19:03:02  domluc
Pré-Commit do Componente Table*

*/

require_once 'TableElement.class.php';
require_once 'TableRow.class.php';
require_once 'TableSubRow.class.php';
require_once 'TableCell.class.php';

/**
 * class TableBody
 */
class TableBody extends TableElement
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
    * Campos que serão exibidos
    * @return Array
    * @access public
    */
    public $arCampos;
    /**
    * Array de LInhas da Table Body
    * @return Array
    * @access public
    */
    public $arLinhas;
    /**
    * Array de Sub LInhas da Table Body usadas na TableTree
    * @return Array
    * @access public
    */
    public $arSubLinhas;
    /**
    * Ações da Table Body
    * @return Array
    * @access public
    */
    public $SubLinha;
    /**
    * Ações da Table Body
    * @return Array
    * @access public
    */
    public $Action;
    /**
    * Array de Ações da Table Body
    * @return Array
    * @access public
    */
    public $arActions;
    /**

    /**
    * Construtor, recebe referencia a Table a qual pertence
    * @return void
    */
    public function TableBody(&$Table)
    {
        parent::TableElement();
        $this->setTag( 'tbody' );
        $this->Table = &$Table;

        if ( strtolower(get_class( $this->Table )) == 'tabletree' ) {
            $this->addCampo( 'tabletree');
        }

        $this->addCampo( 'oculto' , 'C');

        $this->setId    ( $this->Table->getId() . '_body' );
        $this->setName  ( $this->Table->getId() . '_body' );

        $this->arActions = array();
    }

    /**
    * @return Array
    * @access public
    */
    public function getCampos()
    {
        return $this->arCampos;
    }

    /**
    * @return null
    * @access public
    */
    public function setCampos($arValor)
    {
       $this->arCampos = $arValor;
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
    * @return Array
    * @access public
    */
    public function getSubLinhas()
    {
        return $this->arSubLinhas;
    }

    /**
    * @return null
    * @access public
    */
    public function setSubLinhas($arValor)
    {
       $this->arSubLinhas = $arValor;
    }

    /**
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    */
    public function addCampo($mxNome , $stAlinhamento = "E" , $stHint = null , $stCampoCondicional = null)
    {
        $arCampos = $this->getCampos();
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
        $arCampos[] = array ( "nome" => $mxNome , "alinhamento" => $stAlign , "hint" => $stHint , "campo_condicional" => $stCampoCondicional );
        $this->setCampos( $arCampos );
    }
    /**
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    */
    public function addComponente($obComponente, $stCampoCondicional = null)
    {
        $this->addCampo( $obComponente , "E" , $obComponente->getTitle() , $stCampoCondicional);
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
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    */
    public function addSubLinha($obSubLinha)
    {
        $arSubLinhas= $this->getSubLinhas();
        $arSubLinhas[] = $obSubLinha;
        $this->setSubLinhas( $arSubLinhas );
        $this->SubLinha = $obSubLinha;
    }
    /**
    * @return Array
    * @access public
    */
    public function getActions()
    {
        return $this->arActions;
    }

    /**
    * @return null
    * @access public
    */
    public function setActions($arValor)
    {
       $this->arActions = $arValor;
    }

    /**
    * Adiciona Cabeçalho a Table
    * @return null
    * @access public
    * @param String $stTipoAcao Tipo de Ação (incluir, alterar, etc...)
    * @param String $stFuncaoAcao Função javascript com parametros ( jsFunction( %04d , %s , %s ) )
    * @param Array $arValores Array de Valores a serem substituidos na Funcao ( array ('campo1','campo2','valor_fixo' ) )
    * @example $table->Body->addAcao( 'jsFuncion(%04d,%s,%s)' , array( 'codigo' , 'nome' , 'fixo') );
    */
    public function addAcao($stTipoAcao , $stFuncaoAcao , $arValores ,$stCampoCondicional = null)
    {
        $arActions= $this->getActions();
        $arActions[] = array( $stTipoAcao , $stFuncaoAcao , $arValores , $stCampoCondicional);
        $this->setActions( $arActions );
        $this->Action = array( $stTipoAcao , $stFuncaoAcao , $arValores , $stCampoCondicional);
    }

    public function montaHTML()
    {
        // inicializa conteiner html
        $stHtml  = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // validação para o PHP 4 RecordSet vazio!
        if ( $this->Table->registros->getNumLinhas() > 0) {

            if ( is_object($this->Table->Paging) ) {
                $this->Table->registros->setCorrente( $this->Table->Paging->getInicio() );
            } else {
                $this->Table->registros->setPrimeiroElemento();
            }
            $inCount=0;
            while ( !$this->Table->registros->eof() && ( is_object($this->Table->Paging)?$inCount<$this->Table->Paging->getTamanhoPagina():true ) ) {
                    // linha
                    if ( !isset($this->arLinhas[ $this->Table->registros->getCorrente()-1 ]) || $this->arLinhas[ $this->Table->registros->getCorrente()-1 ] == NULL ) {
                        $this->addLinha ( new TableRow( $this ) );
                        $this->Linha->montaHTMLBody( $this->Table->registros );
                        $stHtml .= $this->Linha->getHtml();

                        // linha oculta no caso de ser uma tabletree
                        if ( strtolower(get_class( $this->Table )) == 'tabletree') {
                                $this->addSubLinha( new TableSubRow( $this ) );
                                $this->SubLinha->setClass( "_sub" );
                                $this->SubLinha->montaHTML();
                                $stHtml .= $this->SubLinha->getHtml();
                        }
                    } else {
                        $this->arLinhas[ $this->Table->registros->getCorrente()-1 ]->montaHTMLBody( $this->Table->registros, $this->Table->registros->getCorrente() );
                        $stHtml .= $this->arLinhas[ $this->Table->registros->getCorrente()-1 ]->getHtml();
                        if ( strtolower(get_class( $this->Table )) == 'tabletree') {
                            $this->arSubLinhas[ $this->Table->registros->getCorrente()-1 ]->montaHTML();
                            $stHtml .= $this->arSubLinhas[ $this->Table->registros->getCorrente()-1 ]->getHTML();
                        }
                    }

                    $this->Table->registros->proximo();
                    $inCount++;
            }
        } else {
            # Monta uma linha com a informação de nenhum registro encontrado.
            $this->addLinha(new TableCell($this)) ;
            $this->Linha->setColSpan(count($this->Table->Head->arColunas));
            $this->Linha->setStyle('text-align: center;');
            $this->Linha->setConteudo($this->Table->getMensagemNenhumRegistro());
            $this->Linha->montaHTML();
            $stHtml .= $this->Linha->getHtml();
        }

        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();

        $this->setHtml( $stHtml );
    }

} // end of TableBody
?>

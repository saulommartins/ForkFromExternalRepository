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
    * Classe Table
    * Data de Criação   : %date%

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table

    * Casos de uso : uc-01.01.00
*/

require_once 'TableElement.class.php';
require_once 'TableHead.class.php';
require_once 'TableBody.class.php';
require_once 'TableFoot.class.php';
require_once 'TablePaging.class.php';

class Table extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
     * Valor da Cor Condicional padrao
     * @type String
     * @access private
     */
    public $defaultConditionalColor;

    /**
     * Guarda valor de condicional
     * @type String
     * @access private
     */
    public $Cond;
    /**
     * Campo de condicional
     * @type String
     * @access private
     */
    public $CondField;
    /**
     * Valores de Campos Condicional
     * @type String
     * @access private
     */
    public $CondFieldValue;
    /**
     * Guarda LinhasCondicionados
     * @type String
     * @access private
     */
    public $CondLines;

    /**
     * @type boolean
     * @access private
     */
    public $ordenavel;
    /**
     * @access private
     */
    public $summary;
    /**
     * @access private
     */
    public $registros;
    /**
     * Referencia ao Head da Table
     * @access private
     */
    public $Head;
    /**
     * Referencia ao Body da Table
     * @access private
     */
    public $Body;
    /**
     * Referencia ao Foot da Table
     * @access private
     */
    public $Foot;
    /**
     * Array de Callbacks de campos
     * @access private
     */
    public $arCallbacks;
    /**
     * Boleano que verifica se os ids dos componentes receberao o numero da linha no final do id.
     * @access private
     */
    public $boLineNumber;
    /**
     * Boleano que verifica se o cabeçalho deve ser fixo (flutuante) ou não.
     * @access private
     */
    public $boHeadFixed;
    /**
     * Caso o cabeçalho seja fixo, inicializa o tamanho do container de Body.
     * @access private
     */
    public $inBodyHeight = 300;

    /**
     * Mensagem exibida quando não tem registros no recordset
     * @access private
     */
    public $stMensagemNenhumRegistro = 'Nenhum registro encontrado!';

    /**
    * Construtor
    */
    public function Table()
    {
        parent::TableElement();
        $this->setTag( "table" );

        Sessao::write('TableSession',null);
        $this->Paging = null;

        // recupera contexto de execução

        #$tableId = (integer) sessao->transf1['TableContext']['Table'];
        $tableId = (integer) Sessao::read('TableContext_Table');
        $tableId = $tableId + 1;
        #sessao->transf1['TableContext']['Table'] = $tableId;
        Sessao::write('TableContext_Table',$tableId);

        $this->setId ( 'sw_table_' . $tableId );
        $this->setName ( 'sw_table_' . $tableId );

        $this->setClass( 'tabela' );

        // head
        $this->Head = new TableHead( $this );
        // body
        $this->Body = new TableBody( $this );
        // foot
        $this->Foot = new TableFoot( $this);

        // cor condicional padrao
        $this->setDefaultConditionalColor( "#d0e4f2" );

        // condicional ligado
        $this->Cond = true;

        $this->setLineNumber(true);
    }
    /**
     * Seta o valor na variavel inPaginacao
     * @param  Boolean $inPaginacao
     * @return void
     * @access public
     */
    public function setPaginacao($inPaginacao)
    {
        $this->Paging = new TablePaging($this);
        $this->Paging->setTamanhoPagina( $inPaginacao );
    }

    /**
     * Adiciona um valor para saber se deve ou não receber o numero da linha ao final do id de um componente da tabela
     * @return String
     * @access public
     */
    public function addLineNumber($boOpcao)
    {
        $this->setLineNumber($boOpcao);
    }

    /**
     * Seta o valor na variavel boLineNumber
     * @param  Boolean $boOpcao
     * @return void
     * @access private
     */
    public function setLineNumber($boOpcao)
    {
        $this->boLineNumber = $boOpcao;
    }

    /**
     * Retorna o valor na variavel boLineNumber
     * @return boolean
     * @access private
     */
    public function getLineNumber()
    {
        return $this->boLineNumber;
    }

    /**
     *
     * @return String
     * @access public
     */
    public function getDefaultConditionalColor()
    {
        return $this->defaultConditionalColor;
    }

    /**
     *
     * @param  String $stValor
     * @return null
     * @access public
     */
    public function setDefaultConditionalColor($stValor)
    {
        $this->defaultConditionalColor = $stValor;
    }
    /**
     *
     * @return boolean
     * @access public
     */
    public function getOrdenavel()
    {
        return $this->ordenavel;
    }

    /**
     *
     * @param  boolean $boValor
     * @return string
     * @access public
     */
    public function setOrdenavel($boValor)
    {
        $this->ordenavel = $boValor;
    }

    /**
     *
     * @return string
     * @access public
     */
    public function getSummary()
    {
        return $this->summary;
    } // end of member function getSummary

    /**
     *
     * @param string stValor
     * @return string
     * @access public
     */
    public function setSummary($stValor)
    {
        $this->summary = $stValor;
    } // end of member function setSummary

    /**
     *
     * @param Recordset rsRecordset
     * @return
     * @access public
     */
    public function setRecordset($rsRecordset)
    {
        $this->registros = $rsRecordset;
    } // end of member function setRecordset

    /**
     * Registra uma determinada funcao como callback de um campo
     * Exemplo:
     *     $table->registerCallback('nome','callBackNome');
     *     function callBackNome($valor) {
     *         return 'Alterado ' . $valor;
     *     }
     *
     * @param  string    $campo  Campo que sera aplicado o callback
     * @param  string    $funcao Funcao de callback a ser chamada
     * @return Recordset
     * @access public
     */
    public function registerCallback($campo, $funcao)
    {
        $this->arCallbacks[$campo] = $funcao;
    }

    /**
     *
     * @return Recordset
     * @access public
     */
    public function getRecordset()
    {
        return $this->registros;
    } // end of member function getRecordset

    /**
     * Define algumas alterações no visual da tabela
     * Exemplos:
     * 	1a - Lista Simplesmente Alternada
     *  	$table->setCondColor ( true );
     * 	1b - Lista Simplesmente Alternada mudando cor
     *  	$table->setCondColor ( true , '#ccc' );
     *  2a - Alternar por valor de campo do recordset( campo deve ser booleano)
     * 		$table->setCondColor ( 'ativo' );
     *  2b - Alternar por valor de campo do recordset( campo deve ser booleano) mudando cor
     * 		$table->setCondColor ( 'ativo' , '#bcd');
     *  3a - Alternar cor nos registros de numero (array) AINDA NAO IMPLEMENTADA
     * 		$table->setCondColor ( array( 1,5) );
     *  3b - Alternar cor nos registros de numero (array) mudando cor  AINDA NAO IMPLEMENTADA
     * 		$table->setCondColor ( array( 1,5) , '#369' );
     *  4 - Alternar cor de acordo com o valor de um campo do recordset, no exemplo abaixo,
     * sera em todos os codigos iguais a 27 e 42, sendo o ultimo parametro opcional para mudar cor de alternação
     * 		$table->setCondColor ( 'codigo', array( 27,42) , '#369' );
     * @return null
     * @access public
     */
    public function setConditional()
    {
        //LIMPAR VARS
        if ( func_num_args() == 0 ) {
            // chamada sem parametros
            return null;

        } elseif ( func_num_args() == 1 && is_bool( func_get_arg(0) ) ) {
            // chamada para zebra padrao
            $this->Cond = true ;

        } elseif ( func_num_args() == 2 && is_bool( func_get_arg(0) ) && is_string( func_get_arg(1) ) ) {
            // chamada para zebra com cor personalizada
            $this->Cond = true ;
            $this->setDefaultConditionalColor( func_get_arg(1) );

        } elseif ( func_num_args() == 1 && is_string( func_get_arg(0) ) ) {
            // alterar de acordo com campo boolean do recordset
            $this->Cond = 2 ;
            $this->CondField = func_get_arg(0);
        } elseif ( func_num_args() == 2 && is_string( func_get_arg(0) ) && is_string( func_get_arg(1)) ) {
            // alterar de acordo com campo boolean do recordset
            $this->Cond = 2 ;
            $this->CondField = func_get_arg(0);
            $this->setDefaultConditionalColor( func_get_arg(1) );
        } elseif ( func_num_args() == 2 && is_string( func_get_arg(0)) && is_array( func_get_arg(1) )  ) {
            // alterar de acordo com valores passados campo do recordset
            $this->Cond = 4 ;
            $this->CondField = func_get_arg(0);
            $this->CondFieldValue = func_get_arg(1);
            $this->setDefaultConditionalColor( func_get_arg(1) );
        } elseif ( func_num_args() == 3 && is_string( func_get_arg(0)) && is_array( func_get_arg(1) ) &&  is_string( func_get_arg(2)) ) {
            // alterar de acordo com valores passados campo do recordset
            $this->Cond = 4 ;
            $this->CondField = func_get_arg(0);
            $this->CondFieldValue = func_get_arg(1);
            $this->setDefaultConditionalColor( func_get_arg(2) );
        }

        return true;

    } // end of member function getRecordset

  /**
    * Seta o valor na variavel boHeadFixed.
    * Com esse valor é definido se o cabeçalho será fixo ou não.
    * @param Boolean $boHeadFixed
    * @return void
    * @access private
    */
    public function setHeadFixed($boHeadFixed = false)
    {
        $this->boHeadFixed = $boHeadFixed;
    }

    public function getHeadFixed()
    {
        return $this->boHeadFixed;
    }

  /**
    * Seta o valor na variavel inBodyHeight.
    * Com esse valor é montado a altura do <tbody>.
    * @param Int $inBodyHeight
    * @return void
    * @access private
    */
    public function setBodyHeight($inBodyHeight)
    {
        $this->inBodyHeight = $inBodyHeight;
    }

    public function getBodyHeight()
    {
        return $this->inBodyHeight;
    }

    public function setMensagemNenhumRegistro($mensagem)
    {
        $this->stMensagemNenhumRegistro = $mensagem;
    }

    public function getMensagemNenhumRegistro()
    {
        return $this->stMensagemNenhumRegistro;
    }

    /**
     * Este metodo sobreescreve do pai
     * Cria codigo html
     * @return void
     * @access public
     */
    public function montaHTML($boEscapeChars = false, $showCaption = true)
    {
        // inicializa conteiner html

        $stHtml  = "";

        if( is_object($this->Paging) && Sessao::read('TableSession')==null )
            $stHtml .= $this->Paging->montaTagInicial();

        $stHtml .= $this->tagInicial();

        // atributos basicos
        $stHtml .= $this->montaHtmlAtributosBasicos();

        /**
        * atributos da tabela
        */
        // summary
        $stHtml .= " summary=\"" . $this->getSummary() . "\" ";

        $stHtml .= $this->tagFinal() . $this->getQuebraLinha();

        // caption ( caption é igual a summary )
        if ($showCaption) {
            $stHtml .= $this->getQuebraLinha();
            $stHtml .= "<caption>" . $this->getQuebraLinha();
            $stHtml .= $this->getSummary() . $this->getQuebraLinha() ;
            $stHtml .= "</caption>" . $this->getQuebraLinha();
        }

        // head
        $this->Head->montaHTML();
        $stHtml .= $this->Head->getHtml();

        // Caso opte por fixar o cabeçalho, injeta como flutuante.
        if ($this->getHeadFixed()) {
            $this->Body->setStyle('height:'.$this->getBodyHeight().'; overflow-y:scroll; overflow-x:hidden;');
        }

        //body
        $this->Body->Table = $this;
        $this->Body->montaHTML();
        $stHtml .= $this->Body->getHtml();

        // foot
        $this->Foot->montaHTML();
        $stHtml .= $this->Foot->getHtml();

        // paging
        if ( is_object($this->Paging) ) {
            $this->Paging->montaHTML();
            $stHtml .= $this->Paging->getHtml();
        }

        //fecha elemento
        $stHtml .= $this->fechaElemento();

        if( is_object($this->Paging) && Sessao::read('TableSession')==null )
            $stHtml .= $this->Paging->montaTagFinal();

        if ($boEscapeChars) {
          $stHtml = str_replace( "\n" ,"" ,$stHtml );
          $stHtml = str_replace( chr(13) ,"<br>" ,$stHtml );
          $stHtml = str_replace( "  " ,"" ,$stHtml );
          $stHtml = str_replace( "'","\\'",$stHtml );
        }

        if ( is_object($this->Paging) && Sessao::read('TableSession')==null ) {
            $obTable = clone $this;
            Sessao::write('TableSession',$obTable);
        }

        $this->setHtml( $stHtml );
    }

} // end of Table

?>

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
    * Componente Grid
    * Data de Criação   : 23/03/2007

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

require_once 'GridPaging.class.php';

/**
 * class Grid
 */
class Grid
{
    /**
     * Objeto de Paginação
     * @access private
     * @return GridPaging
     */
    public $GridPaging;
    /**
     * Url/Arquivo acessado para retornar os dados
     * @access private
     * @return String
     */
    public $stUrl;

    /**
     * Objeto/Item Principal da String Json
     * @access private
     * @return String
     */
    public $stRoot;

    /**
     * Identificador da Camada do Grid
     * @access private
     * @return String
     */
    public $stId;
    /**
     * Identificador de Registro no Recordset
     * @access private
     * @return String
     */
    public $stRowId;

    /**
     * Define Ordenamento Remoto
     * access private
     * @return Boolean
     */
    public $boRemoteSorting;

    /**
     * Define Campo Padrao de Ordenação
     * access private
     * @return String
     */
    public $stDefaultSorting;
    /**
     * Array de Renders
     * access private
     * @return Array
     */
    public $arRenders;
    /**
     * Array de Renders
     * access private
     * @return Array
     */
    public $arColumnModel;
    /**
     * Guarda saida HTML do Grid
     * access private
     * @return String
     */
    public $stHtml;
    /**
     * Estilos inline da div container do Grid
     * @access private
     * @return String
     */
    public $stDivStyle;
    /**
     * Titulo do Grid
     * @access private
     * @return String
     */
    public $stTitle;
    /**
     * Objeto Grid Editor
     * @access private
     * @return GridEditor
     */
    public $geGridEditor;

// SET
    /**
     * Seta Valor da Url
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setUrl($value)
    {
        $this->stUrl = $value;
    }

    /**
     * Seta Registros por Pagina
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setGridPaging($value)
    {
        $this->GridPaging = $value;
    }

    /**
     * Seta Valor do Root
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setRoot($value)
    {
        $this->stRoot = $value;
    }

    /**
     * Seta Valor do Identificador da Camada do Grid
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setId($value)
    {
        $this->stId = $value;
    }

    /**
     * Seta Valor do Identificador
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setRowId($value)
    {
        $this->stRowId = $value;
    }

    /**
     * Seta Valor do RemoteSorting
     * @access public
     * @param  mixed $value Valor que a o atributo recebera
     * @return null
     */
    public function setRemoteSorting($boRemoteSorting = true)
    {
        $this->boRemoteSorting = $boRemoteSorting;
    }

    /**
     * Seta Valor de Ordenação Padrao
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setDefaultSorting($value)
    {
        $this->stDefaultSorting = $value;
    }

    /**
     * Seta Valor da Url
     * @access public
     * @param  Array $value Valor que a o atributo recebera
     * @return null
     */
    public function setRenders($value)
    {
        $this->arRenders = $value;
    }

    /**
     * Seta Array de CMs
     * @access public
     * @param  Array $value Valor que a o atributo recebera
     * @return null
     */
    public function setColumnModel($value)
    {
        $this->arColumnModel = $value;
    }
    /**
     * Seta Html do Grid
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setHtml($value)
    {
        $this->stHtml = $value;
    }

    /**
     * Seta Estilo da Div do Grid
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setDivStyle($value)
    {
        $this->stDivStyle = $value;
    }

    /**
     * Setar Titulo do Grid
     * @access public
     * @param  String $value Valor que a o atributo recebera
     * @return null
     */
    public function setTitle($value)
    {
        $this->stTitle= $value;
    }

    /**
     * Seta Objeto GridEditor
     * @access public
     * @param  GridEditor $value Valor que a o atributo recebera
     * @return null
     */
    public function setGridEditor($value)
    {
        $this->geGridEditor= $value;
    }

// GET
    /**
     * Retorna Url
     * @access public
     * @return String
     */
    public function getUrl()
    {
        return $this->stUrl;
    }

    /**
     * Retorna Objeto de Paginação
     * @access public
     * @return GridPaging
     */
    public function getGridPaging()
    {
        return $this->GridPaging;
    }

    /**
     * Retorna Root
     * @access public
     * @return String
     */
    public function getRoot()
    {
        return $this->stRoot;
    }

    /**
     * Retorna Id
     * @access public
     * @return String
     */
    public function getId()
    {
        return $this->stId;
    }

    /**
     * Retorna RowId
     * @access public
     * @return String
     */
    public function getRowId()
    {
        return $this->stRowId;
    }

    /**
     * Retorna se faz remote sorting
     * @access public
     * @return boolean
     */
    public function getRemoteSorting()
    {
        return $this->boRemoteSorting;
    }

    /**
     * Retorna campo padrao de ordenação
     * @access public
     * @return boolean
     */
    public function getDefaultSorting()
    {
        return $this->stDefaultSorting;
    }

    /**
     * Retorna Array de Renders
     * @access public
     * @return boolean
     */
    public function getRenders()
    {
        return $this->arRenders;
    }

    /**
     * Retorna Array de CM's
     * @access public
     * @return boolean
     */
    public function getColumnModel()
    {
        return $this->arColumnModel;
    }

    /**
     * Retorna String Html do Grid
     * @access public
     * @return String
     */
    public function getHtml()
    {
        return $this->stHtml;
    }

    /**
     * Retorna String do Estilo inline da div do Grid
     * @access public
     * @return String
     */
    public function getDivStyle()
    {
        return $this->stDivStyle;
    }

    /**
     * Retorna Titulo
     * @access public
     * @return String
     */
    public function getTitle()
    {
        return $this->stTitle;
    }

    /**
     * Retorna Objeto GridEditor
     * @access public
     * @return GridEditor
     */
    public function getGridEditor()
    {
        return $this->getGridEditor;
    }

    /**
     * Construtor <br>
     *
     * @access public
     * @param  String $stUrl Url que busca dados para preencher Grid
     * @return Grid
     */

    public function Grid($stUrl = null)
    {
        $this->setUrl( $stUrl );
        $this->setRemoteSorting ( false );
        $this->setDivStyle( "border:0px solid #99bbe8;overflow: hidden; width: 670px; height: 350px" );
    }

    /**
     * Registra função render pra campo<br>
     * A função de render recebe os parametros: value, p e record<br>
     * Respectivamente valor a ser renderizado, p, e registro da linha, ou seja , acesso aos campos do mesmo registro.<br>
     * Exemplo:<br>
     * <code>
     * function renderCgm(value , p , record) {
     *      return String.format( {0} - {1}) , value, record.data['nom_cgm'];
     * }
     * </code>
     * @access public
     * @return boolean
     * @param  String  $stFunctionName Nome da Função que ira renderizar
     * @param  String  $stField        Nome do Campo que utiliza a função de renderizaçao
     * @return null;
     */
    public function registerRender($stFunctionName , $stField)
    {
        $arRenders = $this->getRenders();
        $arRenders[] = array( 'function_name' => $stFunctionName , 'field' => $stField );
        $this->setRenders ( $arRenders );
    }

    /**
     * Adiciona Modelo de Coluna ao Grid, passando a referencia de qual
     * campo do record esta sendo referenciado.
     *
     * @access public
     * @param  String  $stHeader    Titulo da Coluna
     * @param  String  $stDataIndex Campo do Record a qual esta ligado.
     * @param  Integer $inWidth     Largura da Coluna em Pixels
     * @param  String  $stAlign     Alinhamento da Coluna
     * @return null
     */
    public function addColumnModel($stHeader , $stDataIndex , $inWidth = 50, $stAlign = 'left', $stOptions = '')
    {
        $arColumnModel= $this->getColumnModel();
        $arColumnModel[] = array( 'header' => $stHeader, 'data_index' => $stDataIndex , 'width' => $inWidth , 'align' => $stAlign , 'options' => $stOptions);
        $this->setColumnModel( $arColumnModel );
    }

    /**
     * Wrapper que Adiciona Ação ao Grid <br>
     * Todos os links ja levam o <code >$session->id </code> <br>
     * Sendo necessario somente passar os parametros da ação em si
     * @access public
     * @param String $stTitle    Titulo da Coluna
     * @param String $stAction   Ação a ser executada, um identificador utilizado pelo framework <br>para colocar a imagem da ação correta, Ex: EDITAR, ALTERAR
     * @param String $stFunction Função JavaScript a ser executada.
     */
    public function addAction($stTitle, $stAction , $stFunction = 'renderPadrao')
    {
        /* Adiciona Coluna */
        $this->addColumnModel( $stTitle, $stAction , 30 , 'center' , 'sortable: false, resizable: false');

        /* Adiciona render padrao */
        $this->registerRender( $stFunction, $stAction );

    }

    /**
     * Monta Saida HTML do Grid
     * @access public
     * @var String $stHtml Guarda temporariamente Html do Grid
     * @param  Boolean $boShow Imprimir ou Não na tela!
     * @return null;
     */
    public function montaHTML($boShow = FALSE)
    {
       $stHtml = "
       <div style=\"width:694px;\" class=\"x-box-blue\">
            <div class=\"x-box-tl\">
                <div class=\"x-box-tr\">
                    <div class=\"x-box-tc\"></div>
                </div>
            </div>
            <div class=\"x-box-ml\">
                <div class=\"x-box-mr\">
                    <div class=\"x-box-mc\">
                        <h3 style=\"margin-bottom:5px;\">" . $this->getTitle() . "</h3>";
        $stHtml .= "        <div id=\"" . $this->getId() . "\" style=\"" . $this->getDivStyle() . "\"> \n";
        $stHtml .= "        \n";
        $stHtml .= "        </div> \n";
        $stHtml .= "</div>
                </div>
            </div>
            <div class=\"x-box-bl\">
                <div class=\"x-box-br\">
                    <div class=\"x-box-bc\"></div>
                </div>
            </div>
        </div>";

        $stHtml .= "<script type='text/javascript'>                                                              \n";


        $stHtml .= "Ext.onReady ( function () {                                                                 \n";
        $stHtml .= "    var ds = new Ext.data.Store ( {                                                         \n";

        // url
        $stHtml .= "        proxy: new Ext.data.HttpProxy( {                                                    \n";
        $stHtml .= "            url: '" . $this->getUrl() . "'                                                  \n";
        $stHtml .= "        } ) ,                                                                               \n";

        // reader json
        $stHtml .= "        reader: new Ext.data.JsonReader ( {                                                 \n";
        $stHtml .= "            root: '" . $this->getRoot() . "' ,                                              \n";
        $stHtml .= "            totalProperty: 'inTotalLinhas' ,                                                \n";
        $stHtml .= "            id: '" . $this->getRowId() . "',                                                \n";
        $stHtml .= "            fields:[";
        foreach ($this->arColumnModel as $ColumnModel) {
            $stHtml .= "'" . $ColumnModel['data_index'] . "'," ;
        }
        $stHtml  = substr( $stHtml , 0 , strlen( $stHtml ) - 1 );
        $stHtml .= "                    ]                                                                       \n";
        $stHtml .= "        }, [                                                                                \n";

        // mapeamento das colunas
        foreach ($this->arColumnModel as $Column) {
            $stHtml .= " {name: '" . $Column['data_index'] . "', mapping: '" . $Column['data_index'] . "'},     \n";
        }
        $stHtml  = substr( $stHtml , 0 , strlen( $stHtml ) - 1 );
        $stHtml .= "        ]),                                                                                 \n";

        // remote sorting
        $stHtml .= "        remoteSort:" . ( $this->getRemoteSorting() ? 'true' : 'false' ) . "                 \n";
        $stHtml .= "  });                                                                                       \n";

        // default sorting
        if ( $this->getDefaultSorting() ) {
            $stHtml .= " ds.setDefaultSort ( '" . $this->getDefaultSorting() . "' , 'desc' )                    \n";
        }

        $stHtml .= "                                                                                            \n";

        // column model
        $stHtml .= "    var cm = new Ext.grid.ColumnModel ( [                                                   \n";
        $boFirst = true;
        foreach ($this->arColumnModel as $ColumnModel) {
            $stHtml .= "{ header: \"". $ColumnModel['header'] ."\",                                             \n";
            $stHtml .= "  dataIndex: '" . $ColumnModel['data_index'] . "',                                      \n";
            if ($boFirst) { // automaticamente a primeira coluna sera id
                $stHtml .= "  id: '" . $ColumnModel['data_index'] . "',                                         \n";
                $boFirst = false;
            }
            $stHtml .= "  width: " . $ColumnModel['width'] . ",                                                 \n";
            $stHtml .= "  align: '" . $ColumnModel['align'] . "'                                                \n";

            // adicionais
            if ($ColumnModel['options']) {
                $stHtml .= ", " . $ColumnModel['options'] . "                                                   \n";
            }

            // render
            if ($this->arRenders) {
                foreach ($this->arRenders as $Render) {
                    if ($Render['field'] == $ColumnModel['data_index']) {
                        $stHtml .= ", renderer: " . $Render['function_name'] . "                                \n";
                    }
                }
            }
            $stHtml .= "  } ,                                                                                   \n";
        }

        $stHtml  = substr( $stHtml , 0 , strlen( $stHtml ) - 1 );
        $stHtml .= "\n    ]);                                                                                   \n";
        $stHtml .= "    cm.defaultSortable = true;                                                              \n";

        // criar grid
        $stHtml .= "    var grid = new Ext.grid.Grid ( '" . $this->getId() . "', {                                          \n";
        $stHtml .= "        ds: ds,                                                                             \n";
        $stHtml .= "        cm: cm,                                                                             \n";
        $stHtml .= "        autoSizeColumns: true,                                                              \n";
        //$stHtml .= "        autoExpandColumn: true                                                       \n";

        $stHtml .= "    });                                                                                     \n";

        // renderizar
        $stHtml .= "    grid.render();                                                                          \n";

        // paginação
        if ( $this->getGridPaging() ) {
            $obGridPaging = $this->getGridPaging();
            $stHtml .= "    var gridFoot = grid.getView().getFooterPanel(true);                                 \n";
            $stHtml .= "    var paging = new Ext.PagingToolbar( gridFoot,                                       \n";
            $stHtml .= "        ds,                                                                             \n";
            $stHtml .= "        {   pageSize: "     . $obGridPaging->getRecordsPerPage(). ",                    \n";
            $stHtml .= "            displayInfo: true ,                                                         \n";
            $stHtml .= "            displayMsg: '"   . $obGridPaging->getDisplayMsg()    . "',                  \n";
            $stHtml .= "            emptyMsg: '"     . $obGridPaging->getEmptyMsg()      . "',                  \n";
            $stHtml .= "            beforePageText: 'Página',                                                   \n";
            $stHtml .= "            afterPageText: 'de {0}',                                                    \n";
            $stHtml .= "            firstText: 'Primeira',                                                      \n";
            $stHtml .= "            prevText: 'Anterior',                                                       \n";
            $stHtml .= "            nextText: 'Próxima',                                                        \n";
            $stHtml .= "            lastText: 'Ultima',                                                         \n";
            $stHtml .= "            refreshText: 'Atualizar'                                                    \n";
            $stHtml .= "        });                                                                             \n";
            $stHtml .= "    ds.load( { params: {start:0, limit:" . $obGridPaging->getRecordsPerPage() . "} } ); \n";
        } else {
            $stHtml .= "    ds.load();                                                                          \n";
        }
        $stHtml .= " ds.on('load', function () {
                        grid.getView().autoSizeColumns();
                    }, false, {single:true});
                    ";

        $stHtml .= "});                                                                                         \n";
        $stHtml .= "</script>                                                                                   \n";

        $this->setHtml ( $stHtml );

        if ($boShow) {
            $this->show();
        }
    }

    /**
     * Imprimi na tela
     * @access public
     * @param  Boolean $boShow Imprimir ou Não na tela!
     * @return null;
     */
    public function show()
    {
        echo $this->getHtml();
    }

}

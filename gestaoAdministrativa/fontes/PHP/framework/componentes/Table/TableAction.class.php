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
    * Data de Criação   : 05/12/2006

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table
    * @uses Table
    * @uses TableRow
    * @uses TableElement

    * Casos de uso : uc-01.01.00
*/

/*
*/

require_once 'TableElement.class.php';
require_once 'TableLink.class.php';

/**
 * class TableAction
 */
class TableAction extends TableElement
{

    /** Aggregations: */

    /** Compositions: */

    /*** Attributes: ***/

    /**
    * String com a Funcao e o formato de substrituição
    * @example jsFunciont( %s , %s , %s )
    */
    public $Funcao;

    /**
    * Array com os Valores para Substituir na Funcao
    * @example array( 'campo' , 'campo2' , 'fixo' )
    * @see TableAction::Funcao
    */
    public $Valores;

    /**
    * Tipo de Ação
    * @example 'incluir'
    */
    public $Tipo;
    /**
    * Tipo de Ação
    * @example 'incluir'
    */
    public $FuncaoPreenchida;

    /**
    * Condição de Exibição
    */
    public $Condicional;

    /**
    * Referencia a Linha a qual pertence
    */
    public $CellAction;

    public function TableAction(&$CellAction , $suffix)
    {
        parent::TableElement();
        $this->setTag ( "td" );
        $this->CellAction = &$CellAction;

        $this->setId     ( $this->CellAction->Linha->getId() . '_Action_' . $suffix ) ;
        $this->setName   ( $this->CellAction->Linha->getId() . '_Action_' . $suffix ) ;
    }
    /**
    * @return String
    * @access public
    */
    public function getFuncao()
    {
        return $this->Funcao;
    }

    /**
    * @return null
    * @access public
    */
    public function setFuncao($stValor)
    {
       $this->Funcao = $stValor;
    }

    /**
    * @return Array
    * @access public
    */
    public function getValores()
    {
        return $this->Valores;
    }

    /**
    * @return null
    * @access public
    */
    public function setValores($arValores)
    {
       $this->Valores = $arValores;
    }

    /**
    * @return String
    * @access public
    */
    public function getTipo()
    {
        return $this->Tipo;
    }

    /**
    * @return null
    * @access public
    */
    public function setTipo($stTipo)
    {
       $this->Tipo = $stTipo;
    }

    /**
    * @return String
    * @access public
    */
    public function getCondicional()
    {
        return $this->Condicional;
    }

    /**
    * @return null
    * @access public
    */
    public function setCondicional($boCondicional)
    {
       $this->Condicional = $boCondicional;
    }

    /**
    * Monta Html e Seta Codigo Gerado!
    * @return void
    */
    public function montaHTML()
    {
        // inicializa var html
          $stHtml = "";

        $boMostrarAcao = true;
        // verifica se tem condiciona e se tiver validar
        if ( $this->getCondicional() ) {

          $CampoCondicionalComponente = $this->getCondicional();
          $rsRegistros = $this->CellAction->Linha->TableRef->Table->registros;

            if ( trim( $rsRegistros->getCampo( $CampoCondicionalComponente ) ) ) {

                switch ( trim($rsRegistros->getCampo( $CampoCondicionalComponente ) ) ) {
                    case 't':
                        $boMostrarAcao = true;
                        break;
                    case 'f':
                        $boMostrarAcao = false;
                        break;
                    default:
                        $boMostrarAcao = (boolean) $rsRegistros->getCampo( $CampoCondicionalComponente );
                        break;
                }

            }

        }

     if ($boMostrarAcao) {

       $arTipos = explode ( '(', $this->Funcao );
       $arTipos = explode ( ')', $arTipos[1] );
       $arTipos = explode (',', $arTipos[0] );

        // varrer array e procurar os campos do recordset
        $arTroca = array();
        foreach ($this->Valores as $chave => $Valor) {
            $rtRs = $this->CellAction->Linha->TableRef->Table->registros->getCampo($Valor);
            $nValor = trim($rtRs) != "" ? $rtRs : $Valor ;
              if ( trim ($arTipos[$chave]) == '%s' )
                  $arTroca[$chave] = "'".$nValor."'";
              else
                  $arTroca[$chave] = $nValor;
        }

        // deixa função pronta pra uso
        $this->FuncaoPreenchida = vsprintf($this->Funcao , $arTroca ) ;
        #echo '<br><b>'.$this->FuncaoPreenchida.'</b>';

        // link
        $Link = new TableLink( $this );
        $Link->setOnClick( $this->FuncaoPreenchida );

        // verifica a img a inserir
        //INCLUI O ARRAY arAcao COM AS ACOES CADASTRADAS
          include ( CAM_FW_INCLUDE."acaoBotao.inc.php");
          if ( isset( $arAcao[strtoupper($this->Tipo)] ) ) {
              if (isset($arTitle[$this->Tipo])) {
                  $stTitle = $arTitle[strtoupper($this->Tipo)];
              } else {
                  $stTitle = ucfirst($this->Tipo);
              }
              $stImg = "<img src=\"".$arAcao[strtoupper($this->Tipo)]."\" alt=\"".$stTitle."\" title=\"".$stTitle."\" />";
          }

        $Link->setConteudo( $stImg );
        $Link->montaHTML();

        $stHtml = $Link->getHtml();
        } else {
          $stHtml = "";
        }
        $this->setHtml($stHtml);

    }

} // end of TableAction

?>

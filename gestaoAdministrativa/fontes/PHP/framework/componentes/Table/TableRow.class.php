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
    * Classe TableRow, responsavel pelo controle das linhas das tabelas do Pacote Table
    * Data de CriaÃ§Ã£o   : %date%

    * @author Analista: Lucas Stephanou
    * @author Desenvolvedor: Lucas Stephanou

    * @package Table
    * @uses TableCell

    * Casos de uso : uc-01.01.00
*/

require_once 'TableElement.class.php';
require_once 'TableCellAction.class.php';

/**
 * class TableRow
 * @access public
 * @see Table
 */
class TableRow extends TableElement
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
    * CabeÃ§alho da Linha
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
    public $TableRef;

    /**
    *   Construtor da Classe TableRow
    * @access public
    * @see Table
    */
    public function TableRow(&$TableRef)
    {
        parent::TableElement();
        $this->setTag ( "tr" );

        $this->TableRef = &$TableRef;

        $this->inNumComponentes = 1;

        switch (strtolower(get_class( $this->TableRef ))) {
            case 'tablehead':
                $stNom = 'head';
                break;
            case 'tablebody':
                $stNom = 'row';
                break;
            case 'tablefoot':
                $stNom = 'foot';
                break;
        }

        $this->setId     ( $this->TableRef->Table->getId() . '_' . $stNom  . '_' . ( 1 + count( $this->TableRef->arLinhas ) ) ) ;
        $this->setName   ( $this->TableRef->Table->getId() . '_' . $stNom  . '_' . ( 1 + count( $this->TableRef->arLinhas ) ) ) ;

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
     * MontaHtml para CriaÃ§Ã£o de Linha num Container Body
     * @return String
     * @see MontaHTML
     */
    public function montaCondicional()
    {
        if ( $this->TableRef->Table->Cond == true && is_bool( $this->TableRef->Table->Cond )) {
            if ( count( $this->TableRef->arLinhas ) % 2 == 0 ) {
                $this->setStyle( "background: " . $this->TableRef->Table->getDefaultConditionalColor()  . "" );
            }
        } elseif ($this->TableRef->Table->Cond == 2) {
            if ( $this->TableRef->Table->registros->getCampo(  $this->TableRef->Table->CondField  ) == true ) {
                $this->setStyle( "background: " . $this->TableRef->Table->getDefaultConditionalColor()  . "" );
            }
        } elseif ($this->TableRef->Table->Cond == 4) {
            if ( in_array($this->TableRef->Table->registros->getCampo(  $this->TableRef->Table->CondField  ) , $this->TableRef->Table->CondFieldValue ) ) {
                $this->setStyle( "background: " . $this->TableRef->Table->getDefaultConditionalColor()  . "" );
            }
        }
    }

    /**
     * MontaHtml para CriaÃ§Ã£o de Linha num Container Body
     * @return String
     * @see MontaHTML
     */
    public function montaHTMLBody($rsRegistros, $inIndice=null)
    {
        $stHtml = "";

        $inIndice = ($inIndice==null) ? count( $this->TableRef->arLinhas ) : $inIndice;

        /* Condicional */
        if ($this->TableRef->Table->Cond) {
            $this->montaCondicional();
        }

        /* FIM - Condidicional */

        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // celulas campos
        foreach ( $this->TableRef->getCampos() as $arCampo ) {
            $boCampoComponente = FALSE;

            // verifica se campo, Ã© um objeto componente ou nome de um campo no recordset
            if ( is_object( $arCampo["nome"] ) ) {
                $boCampoComponente = TRUE;
                $Campo = $arCampo["nome"];
            } else {
                $Campo = $arCampo["nome"] == "" ? "&nbsp;" : $arCampo["nome"] ;
            }

            $Alinhamento = $arCampo["alinhamento"];
            $Hint = $arCampo["hint"];
            $CampoCondicionalComponente = $arCampo["campo_condicional"];

            $this->addCelula( new TableCell( $this ) ) ;

            if ($Campo == 'oculto') {
                // oculto
                $this->Celula->setStyle( $this->Celula->getStyle() . "color:#000;font-weight:bold;");
                ##$this->Celula->setConteudo ( count( $this->TableRef->arLinhas ) );
                $this->Celula->setConteudo ( $inIndice );
            } elseif ($Campo == 'tabletree') {
                // verificar se linha atende condição para exibir expansão
                $arCondTree = $this->TableRef->Table->getCondicionalTree();
                $valorCampo = trim($rsRegistros->getCampo( $arCondTree[0] ) );

                if ($arCondTree) {
                    if ( in_array( $valorCampo , $arCondTree[1] ) ) {
                        $boTree = true;
                    } else {
                        $boTree = false;
                    }
                } else {
                    $boTree = true;
                }

                if ($boTree) {
                    // monta expande/encolhe ([+]) para table tree
                    $stLinkAdicional  = $this->TableRef->Table->montaUrlAjax();
                    $stLinkAdicional .= "&linha_table_tree=" . $this->getId();

                    $stConteudo = <<<CAMPO
<a href="#{$this->id}_mais" id="{$this->id}_mais" name="{$this->id}_mais" onclick="TableTreeReq ('{$this->id}' , '{$stLinkAdicional}')"><img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/mais.gif" /></a>
<a href="#{$this->id}_mais" id="{$this->id}_menos" onclick="TableTreeLineControl( '{$this->id}' , 'none', '', 'none')" style="display:none;"><img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/menos.gif" />
</a>
CAMPO;

                    $this->Celula->setConteudo ( $stConteudo );
                } else {
                    $this->Celula->setConteudo ( "&nbsp;" );
                }
            } elseif ( isset($this->TableRef->Table->arCallbacks[$Campo])) {
                $arElementos = $rsRegistros->arElementos[$rsRegistros->getCorrente() - 1];
                $stConteudo = call_user_func($this->TableRef->Table->arCallbacks[$Campo],$arElementos);
                unset($arElementos);
                $this->Celula->setConteudo ( $stConteudo );
            } else {
                // monta celula em tabela normal
                // campo normal
                if (!$boCampoComponente) {
                    $stConteudo = trim($rsRegistros->getCampo( $Campo )) != "" ? $rsRegistros->getCampo( $Campo ) : "&nbsp;";

                    if ( strstr( $Campo , '[' ) || strstr( $Campo,']' ) ) {
                        $stConteudo = $this->Celula->montaConteudoComposto ( $Campo , $rsRegistros );
                    }
                } else { // campo componente
                    $boMostrarComponente = true;
                    // verifica se tem condiciona e se tiver validar
                    if ($CampoCondicionalComponente) {
                        if ( trim( $rsRegistros->getCampo( $CampoCondicionalComponente ) ) ) {
                            switch ( trim($rsRegistros->getCampo( $CampoCondicionalComponente ) ) ) {
                                case 't':
                                    $boMostrarComponente = true;
                                    break;
                                case 'f':
                                    $boMostrarComponente = false;
                                    break;
                                default:
                                    $boMostrarComponente = (boolean) $rsRegistros->getCampo( $CampoCondicionalComponente );
                                    break;
                            }
                        }
                    }

                    if ($boMostrarComponente) {
                        $obComponente = clone $Campo;

                        // concatenar ao name/id identificador da linha
                        $stNameOld = $obComponente->getName();
                        $stName = $this->Celula->montaConteudoComposto($obComponente->getName(),$rsRegistros);
                        $stId   = $this->Celula->montaConteudoComposto($obComponente->getId()  ,$rsRegistros);
                        if ($this->TableRef->Table->getLineNumber()) {
                            $stNewName_Id = $stName . "_" . ( $inIndice );
                            $stNewId      = $stId . '_' . ($inIndice);
                        } else {
                            $stNewName_Id = $stName;
                            $stNewId      = $stId;
                        }

                        if ($stId != '') {
                            $obComponente->setId ( $stId ) ;
                        } else {
                            $obComponente->setId ( $stNewName_Id ) ;
                        }
                        $obComponente->setName ( $stNewName_Id ) ;

                        // verifica o valor
                        if ( $obComponente->getValue() ) {
                            $stValueOld = $obComponente->getValue();
                            $stCompValue = $this->Celula->montaConteudoComposto( $obComponente->getValue() , $rsRegistros );
                            $obComponente->setValue($stCompValue);
                        }

                        if (strtolower($obComponente->stDefinicao) == 'buscainner') {
                            $stNameCampoCodOld = $obComponente->obCampoCod->getName();
                            $stValueOld = $obComponente->obCampoCod->getValue();

                            // monta um array com os valores originais dos eventos para poder passa-los na
                            // hora de zerar os valores dos componentes de definição 'buscainner'
                            $arObEvento = array();
                            foreach ($obComponente->obCampoCod->obEvento as $chave => $valor) {
                                $arObEvento[$chave] = $valor;
                            }

                            $stName = $this->Celula->montaConteudoComposto ( $obComponente->obCampoCod->getName() , $rsRegistros );
                            if ($this->TableRef->Table->getLineNumber()) {
                                $stNewName_Id = $stName . "_" . ( $inIndice );
                            } else {
                                $stNewName_Id = $stName;
                            }
                            $obComponente->obCampoCod->setName ( $stNewName_Id ) ;
                            $obComponente->obCampoCod->setId ( $stNewName_Id ) ;
                            // verifica o valor
                            if ( $obComponente->obCampoCod->getValue() ) {
                                $stCompValue = $this->Celula->montaConteudoComposto( $obComponente->obCampoCod->getValue() , $rsRegistros );
                                $obComponente->obCampoCod->setValue($stCompValue);
                            }
                        } elseif (strtolower($obComponente->stDefinicao) == 'checkbox') {
                            $stCheckedOld  = $obComponente->getChecked();
                            $stCompChecked = $this->Celula->montaConteudoComposto( $obComponente->getChecked() , $rsRegistros );
                            $obComponente->setChecked( ($stCompChecked=='true') );
                        }

                        $obComponente->montaHTML();
                        $stConteudo = $obComponente->getHTML();

                        if (strtolower($obComponente->stDefinicao) == 'buscainner') {
                            $obComponente->setName ( $stNameOld ) ;
                            $obComponente->setId ( $stNameOld ) ;
                            $obComponente->obCampoCod->setName ( $stNameCampoCodOld ) ;
                            $obComponente->obCampoCod->setId ( $stNameCampoCodOld ) ;
                            $obComponente->obCampoCod->setValue( $stValueOld );

                            // remonta os valores dos eventos com os valores originais.
                            foreach ($obComponente->obCampoCod->obEvento as $chave => $valor) {
                                if ($chave != "stDebug") {
                                    $stFuncao = "set".ucfirst($chave);
                                    eval('$obComponente->obCampoCod->obEvento->$stFuncao( $arObEvento[$chave] );');
                                }
                            }
                        }
                        $stValueOld = isset($stValueOld) ? $stValueOld : null;
            $obComponente->setId($stNameOld);
                        $obComponente->setName($stNameOld);
                        $obComponente->setValue($stValueOld);
                        if (strtolower($obComponente->stDefinicao) == 'checkbox') {
                            $obComponente->setChecked($stCheckedOld);
                        }
                    } else {
                        $stConteudo = "&nbsp;";
                    }

                }

                $this->Celula->setConteudo ( $stConteudo );
            }

            // alinhamento
            if ($Alinhamento) {
                $this->Celula->setStyle ( $this->Celula->getStyle() . "; text-align: ". $Alinhamento ." ");
            }

            // hint
            if ($Hint) {
                $stHint = $this->Celula->montaConteudoComposto ( $Hint , $rsRegistros );
                $stHint = $stHint == '&nbsp;' ? '' : $stHint;
                $this->Celula->setTitle( $stHint );
            }

            $this->Celula->montaHTML();

            $stHtml .= "\t" . $this->Celula->getHtml() . $this->getQuebraLinha();
        }

        // acoes
        if ( count( $this->TableRef->Table->Body->arActions ) > 0 ) {
            $this->addCelula( new TableCellAction( $this ) ) ;
            $this->Celula->montaHTML();
            $stHtml .= $this->Celula->getHtml();
        }

        // fecha linha e seta html gerado
        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();
        $this->setHtml( $stHtml );
    }

    /**
     * MontaHtml para CriaÃ§Ã£o de Linha num Container Head
     * @return String
     * @see MontaHTML
     */
    public function montaHTMLHead()
    {
        $stHtml = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        // verificar actions, caso tenha, insere uma coluna para aÃ§Ãµes
        if ( count( $this->TableRef->Table->Body->arActions ) > 0 ) {
            $this->TableRef->arColunas['Ações'] = 5;
        }

        // agrupamento
        $stHtml .= "<colgroup>" . $this->getQuebraLinha();
        foreach ($this->TableRef->arColunas as $inTamanho) {
            $stHtml .= "\t <col width=\"" . $inTamanho . "%\" />" . $this->getQuebraLinha();
        }
        $stHtml .= "</colgroup>".$this->getQuebraLinha();

        // cabeçalhos
        if (count($this->TableRef->getColunas()) > 2) {
            foreach ($this->TableRef->arColunas as $stTitulo => $inTamanho) {
                $stHtml .= "\t";
                // verificar se é table tree
                if ($stTitulo == 'tabletree') {
                    if ( $this->TableRef->Table->getMostrarTodos()) {
                        $stTitulo = <<<TITULO
<a href="#" id="{$this->TableRef->Table->id}_openAll" onclick="hiddenController('{$this->TableRef->Table->id}',false , '{$this->TableRef->Table->arquivo}' );"><img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/mais.gif" /></a>
<a href="#" id="{$this->TableRef->Table->id}_closeAll" onclick="hiddenController('{$this->TableRef->Table->id}',true , '{$this->TableRef->Table->arquivo}' );" style="display:none;"><img src="../../../../../../gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/menos.gif" />
</a>
TITULO;
                    } else {
                        $stTitulo = "&nbsp;";
                    }
                }

                $this->addCabecalho( new TableHeader( $this) );
                $this->Cabecalho->setConteudo( $stTitulo );
                $this->Cabecalho->montaHTML();
                $stHtml .= $this->Cabecalho->getHtml() . $this->getQuebraLinha();
            }
        }
        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();
        $this->setHtml( $stHtml );
    }

    /**
     * MontaHtml para CriaÃ§Ã£o de Linha num Container Head
     * @return String
     * @see MontaHTML
     */
    public function montaHTMLFoot()
    {
        $stHtml = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        $arSomas = $this->TableRef->getSomas();

        //Verificação da existencia de ações na tabela
        //Se houver alguma ação é diminuído uma coluna no colspan do total
        //para ajustar os totais em suas respectivas colunas
        if (count($this->TableRef->Table->Body->getActions()) >= 1) {
            $inCountSomas = (count($arSomas)+1);
        } else {
            $inCountSomas = count($arSomas);
        }
        $numColunas = count($this->TableRef->Table->Head->arColunas) - $inCountSomas;

        // label para totallizador
        $this->addCelula( new TableCell( $this ) ) ;
        $this->Celula->setColSpan ( $numColunas );
        $this->Celula->setStyle( "text-align: right ; font-weight:bold;" );
        $this->Celula->setConteudo ( "Total: " );
        $this->Celula->montaHTML();
        $stHtml .= $this->Celula->getHtml();

        // totalizacao
        $rsRecordset  = $this->TableRef->Table->registros ;
        //$rsRecordset->setPrimeiroElemento();
        foreach ($arSomas as $arSoma) {
            $rsRecordset->setPrimeiroElemento();
            $nuSoma = 0.00;
            $stCampo = $arSoma['campo'];
            while ( !$rsRecordset->eof() ) {
                $stValor = $rsRecordset->getCampo($stCampo);
                $stValor = str_replace('.','',$stValor);
                $stValor = str_replace(',','.',$stValor);

                $nuSoma += doubleval($stValor);
                $rsRecordset->proximo();
            }

            // usa recordset temporario para formatar valor de soma
            $arValor = array();
            $arValor[] = array("valor" => $nuSoma);
            $rsTemp = new Recordset;
            $rsTemp->preenche($arValor);
            $rsTemp->addFormatacao("valor",'NUMERIC_BR');
            $rsTemp->setPrimeiroElemento();
            $nuSoma = $rsTemp->getCampo("valor");

            // mostra total
            $this->addCelula( new TableCell( $this ) ) ;
            //$this->Celula->setColSpan ( $numColunas );
            $this->Celula->setStyle( "text-align: " . $arSoma['alinhamento'] . " ; font-weight:bold;" );
            $this->Celula->setConteudo ( $nuSoma );
            $this->Celula->montaHTML();
            $stHtml .= $this->Celula->getHtml();
        }

        //Célula adicionada no Foot para ficar a baixo da coluna de ações
        //e posicionar os totais abaixo de suas respectivas colunas
        if (count($this->TableRef->Table->Body->getActions()) >= 1) {
            $this->addCelula( new TableCell( $this ) ) ;
            $this->Celula->setConteudo ( "" );
            $this->Celula->montaHTML();
            $stHtml .= $this->Celula->getHtml();
        }

        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();
        $this->setHtml( $stHtml );
    }

    /**
     * MontaHtml para CriaÃ§Ã£o de Linha num Container Head
     * @return String
     * @see MontaHTML
     */
    public function montaHTMLPaging()
    {
        $stHtml = "";
        $stHtml .= $this->abreElemento() . $this->getQuebraLinha();

        $numColunas = count($this->TableRef->Table->Head->arColunas);
        $inPaginaAtual      = $this->TableRef->Table->Paging->getPaginaAtual();
        $inTamanhoPagina    = $this->TableRef->Table->Paging->getTamanhoPagina();
        $inInicio           = $this->TableRef->Table->Paging->getInicio();
        $inQtdRegistros     = $this->TableRef->Table->registros->getNumLinhas();
        $inQtdMaximaLinks   = (int) ($inQtdRegistros / $inTamanhoPagina);
        if( ($inQtdRegistros % $inTamanhoPagina) > 0 )
            $inQtdMaximaLinks++;

        $inLinkAntes        = $inPaginaAtual - 1;
        $inLinkAntes        = ($inLinkAntes<=0)?null:$inLinkAntes;
        $inLinkDepois       = $inPaginaAtual + 1;
        $inLinkDepois       = ($inLinkDepois>$inQtdMaximaLinks)?null:$inLinkDepois;

        $stLink = "JavaScript:ajaxJavaScript('../../../../../../gestaoAdministrativa/fontes/PHP/framework/instancias/processamento/OCTableTree.php?".Sessao::getId()."&inLine='+this.id+'&table_id=".$this->TableRef->Table->Paging->id."','montaPaging');";
        if($inLinkAntes)
            $stJs .= "<a href='#".$this->TableRef->Table->Paging->id."' id='".$inLinkAntes."' onclick=\"$stLink\"> < </a> | ";
        $stJs .= "<a href='#".$this->TableRef->Table->Paging->id."' id='".$inPaginaAtual."' onclick=\"$stLink\">".$inPaginaAtual."</a> | ";
        if($inLinkDepois)
            $stJs .= "<a href='#".$this->TableRef->Table->Paging->id."' id='".$inLinkDepois."' onclick=\"$stLink\"> > </a> ";

        // label para totallizador
        $this->addCelula( new TableCell( $this ) ) ;
        $this->Celula->setColSpan ( $numColunas );
        $this->Celula->setStyle( "text-align: center ; font-weight:bold;" );
        $this->Celula->setConteudo ( "$stJs" );
        $this->Celula->montaHTML();
        $stHtml .= $this->Celula->getHtml();

        $stHtml .= $this->fechaElemento() . $this->getQuebraLinha();
        $this->setHtml( $stHtml );
    }
} // end of TableRow
?>

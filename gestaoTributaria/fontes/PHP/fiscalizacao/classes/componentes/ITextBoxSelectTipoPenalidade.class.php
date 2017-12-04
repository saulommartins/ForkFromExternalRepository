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
 * Arquivo que monta o combo de tipo de penalidade
 * Data de Criacao: 25/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Componente

 $Id: ITextBoxSelectTipoPenalidade.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once( CAM_GT_FIS_MAPEAMENTO . "TFISTipoPenalidade.class.php" );

/**
 * Gera o componente composto de TextBox e ComboBox para seleção do tipo de penalidade.
 */
class ITextBoxSelectTipoPenalidade extends Objeto
{
    public $obTxtTipoPenalidade;
    public $obCmbTipoPenalidade;

    /**
     * Construtor do componente.
     */
    public function __construct()
    {
        $obTFISTipoPenalidade = new TFISTipoPenalidade();
        $obTFISTipoPenalidade->recuperaTodos( $rsTiposPenalidade );

        # Define TextBox do componente
        $this->obTxtTipoPenalidade = new TextBox();
        $this->obTxtTipoPenalidade->setRotulo( "Tipo de Penalidade" );
        $this->obTxtTipoPenalidade->setInteiro( true );
        $this->obTxtTipoPenalidade->setValue( $this->stValue );
        $this->obTxtTipoPenalidade->obEvento->setOnChange( $stComando );

        # Define ComboBox do componente
        $this->obCmbTipoPenalidade = new Select();
        $this->obCmbTipoPenalidade->setCampoId( "cod_tipo" );
        $this->obCmbTipoPenalidade->setCampoDesc( "descricao" );
        $this->obCmbTipoPenalidade->addOption( '', "Selecione" );
        $this->obCmbTipoPenalidade->preencheCombo( $rsTiposPenalidade );
        $this->obCmbTipoPenalidade->setValue( $this->stValue );
    }

    /**
     * Define se componente é obrigatório ou não.
     * @param boolean $boNull
     */
    public function setNull($boNull = false)
    {
        $this->obTxtTipoPenalidade->setNull( $boNull );
        $this->obCmbTipoPenalidade->setNull( $boNull );
    }

    /**
     * Define o título do componente.
     * @param string $stTitle o título
     */
    public function setTitle($stTitle)
    {
        $this->obTxtTipoPenalidade->setTitle( $stTitle );
        $this->obCmbTipoPenalidade->setTitle( $stTitle );
    }

    /**
     * Define o nome do componente
     * @param string $stName o nome
     */
    public function setName($stName)
    {
        $this->obTxtTipoPenalidade->setName( $stName );
    }

    /**
     * Define o valor padrão do componente.
     * @param string $stValue o valor
     */
    public function setValue($stValue)
    {
        $this->obCmbTipoPenalidade->setValue( $stValue );
        $this->obTxtTipoPenalidade->setValue( $stValue );
    }

    /**
     * Define ação para mudança de valor do componente.
     * @param string $stCommand comando javascript
     */
    public function setOnChange($stComando)
    {
        $this->obCmbTipoPenalidade->obEvento->setOnChange( $stComando );
        $this->obTxtTipoPenalidade->obEvento->setOnChange( $stComando );
    }

    /**
     * Retorna o valor definido para o componente.
     * @return string
     */
    public function getValue()
    {
        return $this->obCmbTipoPenalidade->getValue();
    }

    /**
     * Adiciona os elementos deste componente no formulário.
     * @param Formulario $obFormulario
     */
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponenteComposto( $this->obTxtTipoPenalidade, $this->obCmbTipoPenalidade );
    }
}

?>

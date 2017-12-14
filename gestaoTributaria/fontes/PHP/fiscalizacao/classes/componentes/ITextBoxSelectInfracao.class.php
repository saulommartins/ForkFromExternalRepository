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
 * Arquivo que monta o combo de infrações
 * Data de Criacao: 21/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Componente

 $Id: ITextBoxSelectInfracao.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once( CAM_GT_FIS_MAPEAMENTO . "TFISInfracao.class.php" );

/**
 * Gera o componente composto de TextBox e ComboBox para seleção da infração.
 */
class ITextBoxSelectInfracao extends Objeto
{
    private $obTxtCodInfracao;
    private $obCmbNomInfracao;
    private $obLblFundamentacaoLegal;
    private $boInicializado;
    //private $stPaginaOculta;

    /**
     * Construtor do componente.
     */
    public function __construct()
    {
        //$this->stPaginaOculta = CAM_GT_FIS_COMPONENTES . "js/OCTextBoxSelectInfracao.php?" . Sessao::getID();
        //$this->stPaginaOculta = CAM_GT_FIS_COMPONENTES . "js/OCTextBoxSelectInfracao.php?" . Sessao::getID();
        $this->boInicializado = false;
    }

    private function inicializaComponente()
    {
        if ($this->boInicializado) {
            return;
        }

        $obTFISInfracao = new TFISInfracao();
        $obTFISInfracao->recuperaListaInfracoes( $rsInfracao );

        # Define TextBox do componente
        $this->obTxtCodInfracao = new TextBox();
        $this->obTxtCodInfracao->setRotulo( "Infração" );
        $this->obTxtCodInfracao->setInteiro( true );
        $this->obTxtCodInfracao->setValue( $this->stValue );

        $this->obHdnNomeInfracao = new Hidden();
        $this->obHdnNomeInfracao->setName( "stHdnNomeInfracao" );
        $this->obHdnNomeInfracao->setValue( "" );

        # Define ComboBox do componente
        $this->obCmbNomInfracao = new Select();
        $this->obCmbNomInfracao->setName( "inSelInfracao" );
        $this->obCmbNomInfracao->setCampoId( "cod_infracao" );
        $this->obCmbNomInfracao->setCampoDesc( "nom_infracao" );
        $this->obCmbNomInfracao->addOption( '', "Selecione" );
        $this->obCmbNomInfracao->preencheCombo( $rsInfracao );
        $this->obCmbNomInfracao->setValue( $this->stValue );
        $this->obCmbNomInfracao->obEvento->setOnChange( "montaParametrosGET('preencherFundamentacaoLegal');" );

        $this->obLblFundamentacaoLegal = new Label();
        $this->obLblFundamentacaoLegal->setID( "stFundamentacaoLegal" );
        $this->obLblFundamentacaoLegal->setRotulo( "Fundamentação Legal" );

        //$this->obCodTipoPenalidade = new Hidden();
        $this->boInicializado = true;
    }

    public function preencherFundamentacaoLegal($inCodInfracao = "")
    {
        if ($inCodInfracao) {
            $stCondicao = "cod_infracao = " . $inCodInfracao;
            $obTFISInfracao = new TFISInfracao();
            $obTFISInfracao->recuperaDadosInfracao( $rsInfracao, $stCondicao );

            if (! $rsInfracao->eof() ) {
                $inCodNorma = $rsInfracao->getCampo( 'cod_norma' );
                $stNomNorma = $rsInfracao->getCampo( 'nom_norma' );

                return "$('stFundamentacaoLegal').innerHTML = '" . $inCodNorma . " - " . $stNomNorma . "';";
            }
        }

        return "$('stFundamentacaoLegal').innerHTML = '&nbsp;';";
    }

    public function setRotulo($stRotulo)
    {
        $this->inicializaComponente();
        $this->obTxtCodInfracao->setRotulo( $stRotulo );
    }

    /**
     * Define se componente é obrigatório ou não.
     * @param boolean $boNull
     */
    public function setNull($boNull = false)
    {
        $this->inicializaComponente();
        $this->obTxtCodInfracao->setNull( $boNull );
        $this->obCmbNomInfracao->setNull( $boNull );
    }

    /**
     * Define o título do componente.
     * @param string $stTitle o título
     */
    public function setTitle($stTitle)
    {
        $this->inicializaComponente();
        $this->obTxtCodInfracao->setTitle( $stTitle );
        $this->obCmbNomInfracao->setTitle( $stTitle );
    }

    /**
     * Define o nome do componente
     * @param string $stName o nome
     */
    public function setName($stName)
    {
        $this->inicializaComponente();
        $this->obTxtCodInfracao->setName( $stName );
    }

    /**
     * Define o valor padrão do componente.
     * @param string $stValue o valor
     */
    public function setValue($stValue)
    {
        $this->inicializaComponente();
        $this->obCmbNomInfracao->setValue( $stValue );
        $this->obTxtCodInfracao->setValue( $stValue );
    }

    /**
     * Define ação para mudança de valor do componente.
     * @param string $stCommand comando javascript
     */
    public function setOnChange($stComando)
    {
        $this->inicializaComponente();
        $this->obCmbNomInfracao->obEvento->setOnChange( $stComando );
        $this->obTxtCodInfracao->obEvento->setOnChange( $stComando );
    }

    /**
     * Retorna o valor definido para o componente.
     * @return string
     */
    public function getValue()
    {
        $this->inicializaComponente();

        return $this->obCmbNomInfracao->getValue();
    }

    /**
     * Adiciona os elementos deste componente no formulário.
     * @param Formulario $obFormulario
     */
    public function geraFormulario(&$obFormulario)
    {
        $this->inicializaComponente();
        $obFormulario->addComponenteComposto( $this->obTxtCodInfracao, $this->obCmbNomInfracao );
        $obFormulario->addComponente( $this->obLblFundamentacaoLegal );
        $obFormulario->addHidden( $this->obHdnNomeInfracao );
    }
}

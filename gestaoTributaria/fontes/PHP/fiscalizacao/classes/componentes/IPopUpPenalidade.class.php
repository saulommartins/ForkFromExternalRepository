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
 * Arquivo de popup de busca de FISCALIZACAO.PENALIDADE
 * Data de Criacao: 11/08/2008

 * @author Analista      : Henelo Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros
 * @ignore

 * $Id: IPopUpPenalidade.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

include_once( CLA_BUSCAINNER );
include_once( CAM_GT_FIS_MAPEAMENTO . "TFISPenalidade.class.php" );

class IPopUpPenalidade extends BuscaInner
{
    /**
     * @access Private
     * @private Object
     */
    private $obForm;

    /**
     * O código da penalidade
     * @access private
     */
    private $inCodPenalidade;

    /**
     * A descrição da penalidade
     * @access private
     */
    private $stDescricao;

    private $inTipo;

    //private $boNull = false;

    /**
     * Metodo Construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->obForm = $obForm;

        $this->setRotulo( 'Penalidade' );
        $this->setId( 'stPenalidade' );

        $this->obCampoCod->setName( 'inCodPenalidade' );
        $this->obCampoCod->setSize( 6 );
        $this->obCampoCod->setMaxLength( 10 );
        $this->obCampoCod->setAlign( "left" );

        $this->inTipo = 0;
    }

    /**
     * Define o nome do componente.
     * @param string $stName nome do componente
     */
    public function setName($stName)
    {
        $this->obCampoCod->setName( $stName );
    }

    /**
     * Define o código da penalidade no componente.
     * @param integer $inValor valor do código da penalidade
     */
    public function setCodPenalidade($inValor)
    {
        $this->inCodPenalidade = $inValor;
    }

    /**
     * Retorna o código da penalidade definido no componente
     * @return integer
     */
    public function getCodPenalidade()
    {
        return $this->inCodPenalidade;
    }

    public function setCodTipoPenalidade($inCodTipo)
    {
        $this->inTipo = $inCodTipo; //setando um tipo especifico de penalidade
    }

    public function geraFormulario(&$obFormulario)
    {
        if ( $this->getCodPenalidade() ) {
            $obTFISPenalidade = new TFISPenalidade();
            $obTFISPenalidade->setDado( 'cod_penalidade', $this->inCodPenalidade );
            $obTFISPenalidade->recuperaPorChave( $rsRecordSet );

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('cod_penalidade') );
            $this->setValue( $rsRecordSet->getCampo('nom_penalidade') );
        }

        $pgOcul = "'" . CAM_GT_FIS_POPUPS . "penalidade/OCPenalidade.php?" . Sessao::getId();
        $pgOcul.= "&" . $this->obCampoCod->getName() . "='+this.value+'&stNomCampoCod=" . $this->obCampoCod->getName();
        $pgOcul.= "&stIdCampoDesc=" . $this->getId() . "&tipoBusca=".$this->inTipo."'";

        $this->obCampoCod->obEvento->setOnChange( "ajaxJavaScript(" . $pgOcul . ", 'buscaPenalidade' );" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_FIS_POPUPS . "penalidade/FLPenalidade.php', 'frm', '" . $this->obCampoCod->stName . "', '" . $this->stId . "','" . $this->inTipo . "','" . Sessao::getId() . "','800','550');");

        $obFormulario->addComponente ( $this );
    }
}

?>

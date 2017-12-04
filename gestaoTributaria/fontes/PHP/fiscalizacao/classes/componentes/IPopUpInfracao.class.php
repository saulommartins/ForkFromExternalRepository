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
 * Arquivo de popup de busca de FISCALIZACAO.INFRACAO
 * Data de Criacao: 25/08/2008

 * @author Analista      : Henelo Menezes dos Santos
 * @author Desenvolvedor : Fellipe Esteves dos Santos
 * @ignore

 * Casos de uso:
 */

include_once( CLA_BUSCAINNER );
include_once( CAM_GT_FIS_MAPEAMENTO . "TFISInfracao.class.php" );

class IPopUpInfracao extends BuscaInner
{
    /**
    * @access Private
    * @private Object
    */
    private $obForm;

    /**
    * O codigo da infracao
    * @access private
    */
    private $inCodInfracao;

    /**
    * A descrição da infracao
    * @access private
    */
    private $stDescricao;

    /**
    * O descrição do tipo da infracao
    * @access private
    */
    private $stTipo;

    /**
    * Metodo Construtor
    * @access public
    */
    public function __construct()
    {
        parent::__construct();

        $this->obForm = $obForm;

        $this->setRotulo( 'Infracao' );
        $this->setId( 'stInfracao' );

        $this->obCampoCod->setName( 'inCodInfracao' );
        $this->obCampoCod->setSize( 6 );
        $this->obCampoCod->setMaxLength( 10 );
        $this->obCampoCod->setAlign( "left" );

        $this->stTipo = 'geral';
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
    * Define o codigo da infracao no componente.
    * @param integer $inValor valor do cdigo da infracao
    */
    public function setCodInfracao($inValor)
    {
        $this->inCodInfracao = $inValor;
    }

    /**
    * Retorna o codigo da infracao definido no componente
    * @return integer
    */
    public function getCodInfracao()
    {
        return $this->inCodInfracao;
    }

    public function geraFormulario(&$obFormulario)
    {
        if ( $this->getCodInfracao() ) {
            $obTFISInfracao = new TFISInfracao();
            $obTFISInfracao->setDado( 'cod_infracao', $this->inCodInfracao );
            $obTFISInfracao->recuperaPorChave( $rsRecordSet );

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('cod_infracao') );
            $this->setValue( $rsRecordSet->getCampo('nom_infracao') );
        }

        $pgOcul = "'" . CAM_GT_FIS_POPUPS . "infracao/OCInfracao.php?" . Sessao::getId();
        $pgOcul.= "&inCodInfracao='+this.value+'&CampoNum=" . $this->obCampoCod->getName();
        $pgOcul.= "&CampoNom=" . $this->getId() . "'";

        $this->obCampoCod->obEvento->setOnChange( "ajaxJavaScript(" . $pgOcul . ", 'buscaInfracao' );" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_FIS_POPUPS . "infracao/FLInfracao.php', 'frm', '" . $this->obCampoCod->stName . "', '" . $this->stId . "','" . $this->stTipo . "','" . Sessao::getId() . "','800','550');");

        $obFormulario->addComponente ( $this );
    }
}
?>

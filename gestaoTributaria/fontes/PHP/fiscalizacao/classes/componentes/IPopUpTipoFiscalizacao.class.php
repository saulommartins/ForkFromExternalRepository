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
    * Arquivo de popup de busca de FISCALIZACAO.TIPO_FISCALIZACAO
    * Data de Criacao: 17/07/2007

    * @author Analista      : Fábio Bertoldi Rodrigues
    * @author Desenvolvedor : Rodrigo D.S.
    * @ignore

    * $Id: IPopUpTipoFiscalizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    *Casos de uso: uc-05.07.02
*/

include_once( CLA_BUSCAINNER 				                      );
include_once( CAM_GT_FIS_MAPEAMENTO."TFISTipoFiscalizacao.class.php" );

class  IPopUpTipoFiscalizacao extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $inCodFiscal;
    public $stDescricao;
    public $stTipo;
    public $boNull = false;

    public function setNull($valor) { $this->boNull 	  = $valor;   }
    public function setCodFiscal($inValor) { $this->inCodFiscal = $inValor; }

    public function getNull() { return $this->boNull; 	    }
    public function getCodFiscal() { return $this->inCodFiscal; }
    /**
        * Metodo Construtor
        * @access Public
    */
    public function IPopUpTipoFiscalizacao()
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo( 'Tipo de Fiscalização' );
        $this->setTitle ( ''                     );
        $this->setId    ( 'stTipoFiscalizacao'   );
        $this->setNull  ( $this->getNull()       );

        $this->obCampoCod->setName     ( "inTipoFiscalizacao" );
        $this->obCampoCod->setSize     ( 6                    );
        $this->obCampoCod->setMaxLength( 10                   );
        $this->obCampoCod->setAlign    ( "left"               );

        $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {
        ;
        if ( $this->getCodFiscal() ) {
            $obTMONAcrescimo = new TFISTipoFiscalizacao();
            $obTMONAcrescimo->setDado( 'cod_fiscal', $this->inCodAcrescimo );
            $obTMONAcrescimo->recuperaPorChave( $rsRecordSet );

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('cod_acrescimo') );
            $this->setValue( $rsRecordSet->getCampo('descricao_acrescimo') );
        }
        $pgOcul = "'".CAM_GT_FIS_POPUPS."fiscalizacao/OCTipoFiscalizacao.php?".Sessao::getId();
        $pgOcul.= "&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName();
        $pgOcul.= "&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnKeyUp ( "ajaxJavaScript(".$pgOcul.",'buscaTipoFiscalizacao' );" );
        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_FIS_POPUPS . "fiscalizacao/FLTipoFiscalizacao.php','frm', '".$this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        $obFormulario->addComponente ( $this );
    }
}
?>

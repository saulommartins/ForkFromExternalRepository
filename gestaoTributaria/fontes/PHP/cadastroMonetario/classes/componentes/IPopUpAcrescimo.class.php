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
* Arquivo de popup de busca de Acréscimo
* Data de Criação: 11/09/2006

* @author Analista: Fabio Bertoldi
* @author Desenvolvedor: Diego Bueno Coelho

* @package URBEM
* @subpackage

    * $Id: IPopUpAcrescimo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.05.11
*/

/*
$Log$
Revision 1.5  2006/09/26 11:27:34  dibueno
ajuste para valor já setado no componente

Revision 1.4  2006/09/19 15:51:32  domluc
Correção para o Bug #7009

Revision 1.3  2006/09/15 14:46:06  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once ( CLA_BUSCAINNER );
include_once ( CAM_GT_MON_MAPEAMENTO."TMONAcrescimo.class.php" );

class  IPopUpAcrescimo extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;
    public $inCodAcrescimo;
    public $stMascaraAcrescimo;
    public $stDescricaoAcrescimo;
    public $stTipo;
    public $boNull = false;

    public function setNull($valor) { $this->boNull = $valor; }
    public function getNull() { return $this->boNull; }
    public function setCodAcrescimo($inValor) { $this->inCodAcrescimo = $inValor; }
    public function getCodAcrescimo() { return $this->inCodAcrescimo; }

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpAcrescimo()
    {

        parent::BuscaInner();

        $this->obForm = $obForm;

        $obTMONAcrescimo = new TMONAcrescimo();
        $obTMONAcrescimo->listaTopAcrescimo( $inCodAcrescimo, $inCodTipo );
        $this->stMascaraAcrescimo = "";
        for ( $inX=0; $inX<strlen( $inCodAcrescimo ); $inX++)
            $this->stMascaraAcrescimo .= "9";

        $this->stMascaraAcrescimo .= ".";
        for ( $inX=0; $inX<strlen( $inCodTipo ); $inX++)
            $this->stMascaraAcrescimo .= "9";

        $this->setRotulo                 ( 'Acréscimo' );
        $this->setTitle                  ( '' );
        $this->setId                     ( 'stDescricaoAcrescimo' );
        $this->setNull                   ( $this->getNull() );

        $this->obCampoCod->setName       ( "inCodAcrescimo" );
        $this->obCampoCod->setSize       ( 6 );
        $this->obCampoCod->setAlign      ( "left" );
        $this->obCampoCod->setInteiro  ( false );
        $this->obCampoCod->setCaracteresAceitos( "[0-9.]" );

        $this->obCampoCod->setMaxLength ( strlen($this->stMascaraAcrescimo) );
        $this->obCampoCod->setMinLength ( strlen($this->stMascaraAcrescimo) );
        $this->obCampoCod->setMascara   ( $this->stMascaraAcrescimo );
        $this->stTipo = 'geral';
    }

    public function geraFormulario(&$obFormulario)
    {
        ;

        if ( $this->getCodAcrescimo() ) {
            $obTMONAcrescimo = new TMONAcrescimo();
            $obTMONAcrescimo->setDado('cod_acrescimo', $this->inCodAcrescimo );
            $obTMONAcrescimo->recuperaPorChave($rsRecordSet);

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('cod_acrescimo') );
            $this->setValue( $rsRecordSet->getCampo('descricao_acrescimo') );
        }

        //$this->obCampoCod->setValue( $this->inCodAcrescimo );
        //$this->setValue ( $this->inCodAcrescimo );

        $pgOcul = "'".CAM_GT_MON_INSTANCIAS."acrescimo/OCManterAcrescimo.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."'";

        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaAcrescimo' );" );

        $this->setFuncaoBusca("abrePopUp('" . CAM_GT_MON_POPUPS . "acrescimo/FLProcurarAcrescimo.php','frm', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        //parent::montaHTML();
        $obFormulario->addComponente ( $this );

    }
}
?>

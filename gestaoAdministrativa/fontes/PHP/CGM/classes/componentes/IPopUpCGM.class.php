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
* Arquivo de popup de busca de CGM
* Data de Criação: 27/02/2003

* @author Analista:
* @author Desenvolvedor: Ricardo Lopes de Alencar

* @package URBEM
* @subpackage

$Revision: 16516 $
$Name$
$Author: souzadl $
$Date: 2006-10-09 05:51:44 -0300 (Seg, 09 Out 2006) $

* Casos de uso: uc-01.02.92
                uc-03.05.15
*/

include_once ( CLA_BUSCAINNER );

class  IPopUpCGM extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */

    public $obForm;
    public $inNumCGM;

    /**
        * Metodo Construtor
        * @access Public

    */

    public function IPopUpCGM($obForm)
    {
        parent::BuscaInner();

        $this->obForm = $obForm;

        $this->setRotulo               ( 'CGM'              );
        $this->setTitle                ( ''                 );
        $this->setId                   ( 'stNomCGM'         );
        $this->setNull                 ( false              );

        $this->obCampoCod->setName     ( "inCGM"            );
        $this->obCampoCod->setId       ( "inCGM"            );
        $this->obCampoCod->setSize     ( 6                  );
        $this->obCampoCod->setMaxLength( 10                 );
        $this->obCampoCod->setAlign    ( "left"             );
        $this->obCampoCod->setExpReg   ('[^0-9]');

        $this->stTipo = 'geral';
    }

    public function setTipo($stTipo='geral')
    {
        /*
        Permite colocar seguintes tipos:
            fisica - permite apenas pessoa física.
            juridica - permite apenas pessoa jurídica.
            geral - permite qualquer campo de pessoa tanto física ou jurídica ou ambos.
        */
        $this->stTipo = $stTipo;
    }

    public function getTipo()
    {
        return $this->stTipo;
    }
    public function setNumCGM($inValor) { $this->inNumCGM = $inValor; }

    public function montaHTML()
    {

        if ( !$this->getValue() && !$this->obCampoCod->getValue() ) {
            include_once(TCGM."TCGM.class.php");
            $obTCGM = new TCGM();
            $obTCGM->setDado('numcgm', $this->inNumCGM );
            $obTCGM->recuperaPorChave($rsRecordSet);

            $this->obCampoCod->setValue( $rsRecordSet->getCampo('numcgm') );
            $this->setValue( $rsRecordSet->getCampo('nom_cgm') );
        }

        $pgOcul = "'".CAM_GA_CGM_PROCESSAMENTO."OCProcurarCgm.php?".Sessao::getId()."&".$this->obCampoCod->getName()."='+this.value+'&stNomCampoCod=".$this->obCampoCod->getName()."&stIdCampoDesc=".$this->getId()."&stTipoBusca=".$this->getTipo()."'";
        $this->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'buscaPopup');".$this->obCampoCod->obEvento->getOnChange() );

        if($this->getFuncaoBusca() == "")
            $this->setFuncaoBusca("abrePopUp('" . CAM_GA_CGM_POPUPS . "cgm/FLProcurarCgm.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");

        parent::montaHTML();
    }
}
?>

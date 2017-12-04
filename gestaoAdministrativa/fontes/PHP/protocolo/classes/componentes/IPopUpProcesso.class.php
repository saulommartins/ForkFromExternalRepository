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
    * Data de Criação: 30/08/2003

    * @author Analista: Casssiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Casssiano de Vasconcellos Ferreira

    * @package URBEM
    * @subpackage

    * Casos de uso: uc-01.06.98

    $Id: IPopUpProcesso.class.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once( CLA_BUSCAINNER );
include_once( CAM_GA_PROT_CLASSES."componentes/ITextChaveProcesso.class.php" );

class  IPopUpProcesso extends BuscaInner
{
    /**
        * @access Private
        * @var Object
    */
    public $obForm;
    public $boValidar;

    /**
        * Metodo Construtor
        * @access Public

    */
    public function IPopUpProcesso($obForm)
    {
        parent::BuscaInner();
        $this->obForm = $obForm;
        $this->setRotulo                ( 'Processo'          );
        $this->setTitle                 ( 'Selecione o processo.'  );
        $this->setId                    ( 'stIDChaveProcesso'   );
        $this->setMostrarDescricao		( false		       );

        $this->setCampoCod				( new ITextChaveProcesso );
        $this->obCampoCod->setMinLength ( 1 );
        $this->obCampoCod->setRotulo	( $this->getRotulo() );
        $this->obCampoCod->setName      ( "stChaveProcesso"  );
        $this->obCampoCod->setId        ( "stChaveProcesso"  );

        $this->stTipo = 'geral';
    }

    public function setTipo($stTipo='geral') { $this->stTipo = $stTipo; }

    public function setValidar($stValidar = false) {$this->boValidar = $stValidar;}

    public function getValidar() {return $this->boValidar;}

    public function montaHTML()
    {
        $this->setFuncaoBusca("abrePopUp('" . CAM_GA_PROT_POPUPS . "processo/FLBuscaProcessos.php','".$this->obForm->getName()."', '". $this->obCampoCod->stName ."','". $this->stId . "','". $this->stTipo . "','" . Sessao::getId() ."','800','550');");
        $this->setValoresBusca( CAM_GA_PROT_POPUPS.'processo/FLBuscaProcessos.php?'.Sessao::getId(), $this->obForm->getName(), $this->stTipo );
        $this->obCampoCod->obEvento->setOnChange( "ajaxJavaScript('".CAM_GA_PROT_INSTANCIAS."processamento/OCIPopUpProcesso.php?".Sessao::getId()."&stNomCampo=".$this->obCampoCod->getId()."&stNumProcesso='+this.value,'preencheProcesso');");

        if ($this->getValidar()) {
            $this->obCampoCod->setValidarComponente( true );
        } else {
            $this->obCampoCod->setValidarComponente( false );
        }

        parent::montaHTML();
    }
}

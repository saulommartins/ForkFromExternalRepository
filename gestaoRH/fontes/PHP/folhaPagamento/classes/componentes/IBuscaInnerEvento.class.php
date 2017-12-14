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
/*
    * Titulo do arquivo (Ex.: "Formulario de configuração do IPERS")
    * Data de Criação   : 23/06/2008

    * @author Analista      Dagiane
    * @author Desenvolvedor Rafael Garbin

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: IBuscaInnerEvento.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/HTML/BuscaInner.class.php';

/**
* Cria o componente BuscaInner para Evento
* @author Desenvolvedor: Rafael Garbin

* @package framework
* @subpackage componentes
*/
class IBuscaInnerEvento extends BuscaInner
{
    public $stNaturezasInformativo = '';
    public $stNaturezasBase        = '';
    public $stNaturezasProvento    = '';
    public $stNaturezasDesconto    = '';
    public $stNaturezaChecked      = '';
    public $boEventoSistema        = '';
    public $stTipo                 = '';

    /**
    * Método Construtor
    * @access Public
    */
    public function IBuscaInnerEvento()
    {
        parent::BuscaInner();
        include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoConfiguracao.class.php" );

        //Define a mascara do campo Evento
        $obRFolhaPagamentoConfiguracao = new RFolhaPagamentoConfiguracao;
        $obRFolhaPagamentoConfiguracao->consultar();
        $stMascaraEvento = $obRFolhaPagamentoConfiguracao->getMascaraEvento();

        $this->setRotulo                       ( "Evento" );
        $this->setId                           ( "stEvento" );
        $this->setTitle                        ( "Informe o evento a ser lançado." );
        $this->obCampoCod->setName             ( "inCodigoEvento" );
        $this->obCampoCod->setId               ( "inCodigoEvento" );
        $this->obCampoCod->setValue            ( $inCodigoEvento );
        $this->obCampoCod->setPreencheComZeros ( "E" );
        $this->obCampoCod->setMascara          ( $stMascaraEvento );
        $this->obCampoDescrHidden->setName     ( "hdnDescEvento" );
        $this->montaOnChange();
        $this->montaPopUp();
    }

    /**
    * Método montaOnChange
    * @access Public
    */
    public function montaOnChange()
    {
        $stEventoSistema = "";
        if ($this->getEventoSistema() === true) {
            $stEventoSistema = "evento_sistema";
        } elseif ($this->getEventoSistema() === false) {
            $stEventoSistema = "n_evento_sistema";
        }
        $this->obCampoCod->obEvento->setOnChange( "ajaxJavaScript( '".CAM_GRH_FOL_PROCESSAMENTO."OCBuscaInnerEvento.php?".Sessao::getId().
                                                                                                "&inCodigoEvento='+this.value+
                                                                                                '&stIdDesc=".$this->getId()."'+
                                                                                                '&stNameCodigo=".$this->obCampoCod->getName()."'+
                                                                                                '&boEventoSistema=".$this->getEventoSistema()."'+
                                                                                                '&stNaturezasAceitas=".$this->getNaturezasAceitas()."'+
                                                                                                '&stNatureza=".$this->getNaturezaChecked()."'+
                                                                                                '&stTipoEvento=".$stEventoSistema."'+
                                                                                                '&stTipo=".$this->getTipo()."'
                                                    , 'preencheDescEvento' );" );
    }

    public function montaPopUp()
    {
        $stEventoSistema = "";
        if ($this->getEventoSistema() === true) {
            $stEventoSistema = "evento_sistema";
        } elseif ($this->getEventoSistema() === false) {
            $stEventoSistema = "n_evento_sistema";
        }
        $this->setFuncaoBusca("abrePopUp('".CAM_GRH_FOL_POPUPS."evento/FLManterEvento.php','frm','".$this->obCampoCod->getName()."','".$this->getId()."','','".Sessao::getId()."&stNaturezasAceitas=".$this->getNaturezasAceitas()."&stNatureza=".$this->getNaturezaChecked()."&stTipoEvento=".$stEventoSistema."&stTipo=".$this->getTipo()."','800','550')" );
    }

    /**
    * @access Public
    * @param Void
    */
    public function setNaturezasDesconto()
    {
        $this->stNaturezasDesconto = "D";
    }

    /**
    * @access Public
    * @param Void
    */
    public function getNaturezasDesconto()
    {
        return $this->stNaturezasDesconto;
    }

    /**
    * @access Public
    * @param Void
    */
    public function setNaturezasProvento()
    {
        $this->stNaturezasProvento = "P";
    }

    /**
    * @access Public
    * @param Void
    */
    public function getNaturezasProvento()
    {
        return $this->stNaturezasProvento;
    }

    /**
    * @access Public
    * @param Void
    */
    public function setNaturezasBase()
    {
        $this->stNaturezasBase = "B";
    }

    /**
    * @access Public
    * @param Void
    */
    public function getNaturezasBase()
    {
        return $this->stNaturezasBase;
    }

    /**
    * @access Public
    * @param Void
    */
    public function setNaturezasInformativo()
    {
        $this->stNaturezasInformativo = "I";
    }

    /**
    * @access Public
    * @param Void
    */
    public function getNaturezasInformativo()
    {
        return $this->stNaturezasInformativo;
    }

    /**
    * @access Public
    * @param Char $valor
    */
    public function getNaturezasAceitas()
    {
        $naturezasAceitas = "";

        if (trim($this->getNaturezasDesconto()) != '') {
            $naturezasAceitas .= $this->getNaturezasDesconto()."-";
        }

        if (trim($this->getNaturezasProvento()) != '') {
            $naturezasAceitas .= $this->getNaturezasProvento()."-";
        }

        if (trim($this->getNaturezasBase()) != '') {
            $naturezasAceitas .= $this->getNaturezasBase()."-";
        }

        if (trim($this->getNaturezasInformativo()) != '') {
            $naturezasAceitas .= $this->getNaturezasInformativo()."-";
        }

        if (trim($naturezasAceitas)!="") {
            $naturezasAceitas = substr($naturezasAceitas, 0, -1);
        }

        return $naturezasAceitas;
    }

    /**
    * @access Public
    * @param Char $valor
    */
    public function setNaturezaChecked($valor)
    {
        $this->stNaturezaChecked = $valor;
    }

    /**
    * @access Public
    * @return String
    */
    public function getNaturezaChecked()
    {
        return $this->stNaturezaChecked;
    }

    /**
    * @access Public
    * @param Boolean $valor
    */
    public function setEventoSistema($valor)
    {
        $this->boEventoSistema = $valor;
    }

    /**
    * @access Public
    * @return Boolean
    */
    public function getEventoSistema()
    {
        return $this->boEventoSistema;
    }

    /**
    * @access Public
    * @param String $valor
    */
    public function setTipo($valor)
    {
        $this->stTipo = $valor;
    }

    /**
    * @access Public
    * @return String
    */
    public function getTipo()
    {
        return $this->stTipo;
    }

}
?>

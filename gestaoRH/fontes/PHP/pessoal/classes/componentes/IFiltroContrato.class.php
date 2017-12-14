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
    * Classe interface para Filtro de Contrato
    * Data de Criação: 25/11/2005

    * @author Desenvolvedor: Andre Almeida

    Casos de uso: uc-00.00.00

    $Id: IFiltroContrato.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                               );

class IFiltroContrato extends Objeto
{
    /**
        * @access Private
        * @var Object
    */
    public $obLblCGM;
    /**
        * @access Private
        * @var Object
    */
    public $obHdnCGM;
    /**
        * @access Private
        * @var Object
    */
    public $obIContratoDigitoVerificador;
    /**
        * @access Private
        * @var Boolean
    */
    public $boInformacoesFuncao;
    /**
        * @access Private
        * @var Object
    */
    public $obLblInformacoesFuncao;
    /**
        * @access Private
        * @var Object
    */
    public $obTituloFormulario;
    /**
        * @access Private
        * @var Varchar
    */
    public $stSituacao;

    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setInformacoesFuncao($valor) { $this->boInformacoesFuncao    = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setTituloFormulario($valor) { $this->obTituloFormulario     = $valor; }
    /**
        * @access Public
        * @param String $valor
    */
    public function setSituacao($valor) { $this->stSituacao     = $valor; }

    /**
        * @access Public
        * @return boolean
    */
    public function getInformacoesFuncao() { return $this->boInformacoesFuncao; }
     /**
        * @access Public
        * @return String
    */
    public function getTituloFormulario() { return $this->obTituloFormulario; }
     /**
        * @access Public
        * @return String
    */
    public function getSituacao() { return $this->stSituacao; }

    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroContrato($stSituacao=false,$boRegistroObrigatorio=false)
    {
        switch ($stSituacao) {
            case true:
                $this->setSituacao('rescindidos');
                break;
            case false:
                $this->setSituacao('ativos');
                break;
            case "todos":
                $this->setSituacao('todos');
                break;
        }

        $this->obLblCGM = new Label;
        $this->obLblCGM->setRotulo ( "CGM"      );
        $this->obLblCGM->setName   ( "inNomCGM" );
        $this->obLblCGM->setId     ( "inNomCGM" );

        $this->obHdnCGM = new Hidden;
        $this->obHdnCGM->setName                  ( "hdnCGM"   );
        $this->obHdnCGM->setValue                 ( ""         );

        $this->obIContratoDigitoVerificador = new IContratoDigitoVerificador("",$stSituacao,$boRegistroObrigatorio);
        $this->obIContratoDigitoVerificador->setPagFiltro(true);

        $this->setInformacoesFuncao( false );
        $this->setTituloFormulario ( "Filtro por Matrícula" );

    }

    /**
        * Monta os campos do filtro do contrato
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        $stOnChange = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnChange();
        $stOnBlur   = $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->getOnBlur();
        if ( $this->getInformacoesFuncao() ) {

            $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContrato='+this.value+'&stSituacao=".$this->getSituacao()."', 'preencheCGMContratoExtendido' );".$stOnChange );
            $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur  ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContrato='+this.value+'&stSituacao=".$this->getSituacao()."', 'preencheCGMContratoExtendido' );".$stOnBlur );

            $this->obLblInformacoesFuncao = new Label;
            $this->obLblInformacoesFuncao->setId      ( "stInformacoesFuncao"                 );
            $this->obLblInformacoesFuncao->setRotulo  ( "Informações da Função"               );
            $this->obLblInformacoesFuncao->setValue   ( ""                                    );
        } else {
            $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnChange ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContrato='+this.value+'&stSituacao=".$this->getSituacao()."', 'preencheCGMContrato' );".$stOnChange );
            $this->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur   ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContrato='+this.value+'&stSituacao=".$this->getSituacao()."', 'preencheCGMContrato' );".$stOnBlur );
        }

        if ($this->getTituloFormulario()!='') {
          $obFormulario->addTitulo            ( $this->getTituloFormulario() );
        }
        $obFormulario->addHidden            ( $this->obHdnCGM                             );
        $obFormulario->addComponente        ( $this->obLblCGM                             );
        $this->obIContratoDigitoVerificador->geraFormulario($obFormulario                 );
        if ( $this->getInformacoesFuncao() ) {
            $obFormulario->addComponente    ( $this->obLblInformacoesFuncao               );
        }
    }

}
?>

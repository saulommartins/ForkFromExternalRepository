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
    * Classe interface para Filtro de Atributo Dinamico
    * Data de Criação: 10/08/2007

    * @author Analista: Diego Lemos de Souza
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.04.00

    $Id $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GA_ADM_NEGOCIO."RCadastroDinamico.class.php"                          );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoContratoServidorValor.class.php" );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalAtributoContratoPensionista.class.php" );
include_once ( CAM_GRH_EST_MAPEAMENTO."TEstagioAtributoEstagiarioEstagio.class.php" );
class IFiltroAtributoDinamico extends Objeto
{
    /**
        * @access Private
        * @var Object
    */
    public $obCmbAtributo;
    /**
        * @access Private
        * @var Object
    */
    public $obSpnAtributo;
    /**
        * @access Private
        * @var Object
    */
    public $obRCadastroDinamico;
    /**
        * @access Private
        * @var String
    */
    public $stCadastro;
    /**
        * @access Private
        * @var String
    */
    public $obTituloFormulario;

    /**
        * @access Public
        * @param String $valor
    */
    public function setTituloFormulario($valor) { $this->obTituloFormulario     = $valor; }
    /**
        * @access Public
        * @return boolean
    */
    public function getCadastro() { return $this->stCadastro; }
     /**
        * @access Public
        * @return String
    */
    public function getTituloFormulario() { return $this->obTituloFormulario; }

    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroAtributoDinamico()
    {
        $this->obRCadastroDinamico = new RCadastroDinamico;

        $rsAtributos = new RecordSet();

        $this->obCmbAtributo = new Select();
        $this->obCmbAtributo->setRotulo("Atributo Dinâmico");
        $this->obCmbAtributo->setName("inCodAtributo");
        $this->obCmbAtributo->setTitle("Selecione o atributo dinâmico para filtro.");
        $this->obCmbAtributo->setNull(false);
        $this->obCmbAtributo->setCampoDesc("nom_atributo");
        $this->obCmbAtributo->setCampoId("cod_atributo");
        $this->obCmbAtributo->addOption("","Selecione");
        $this->obCmbAtributo->preencheCombo($rsAtributos);

        $this->obSpnAtributo = new Span();
        $this->obSpnAtributo->setId("spnAtributo");
    }

    public function setServidor() { $this->stCadastro = "servidor"; }
    public function setPensionista() { $this->stCadastro = "pensionista"; }
    public function setEstagiario() { $this->stCadastro = "estagiario"; }

    /**
        * Monta os campos do filtro do contrato
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        switch ($this->getCadastro()) {
            case "pensionista":
                $this->obRCadastroDinamico->setPersistenteValores   ( new TPessoalAtributoContratoPensionista );
                $this->obRCadastroDinamico->setCodCadastro          ( 7 );
                $this->obRCadastroDinamico->obRModulo->setCodModulo ( 22 );
                break;
            case "estagiario":
                $this->obRCadastroDinamico->setPersistenteValores   ( new TEstagioAtributoEstagiarioEstagio );
                $this->obRCadastroDinamico->setCodCadastro          ( 1 );
                $this->obRCadastroDinamico->obRModulo->setCodModulo ( 39 );
                break;
            default://servidor
                $this->obRCadastroDinamico->setPersistenteValores   ( new TPessoalAtributoContratoServidorValor );
                $this->obRCadastroDinamico->setCodCadastro          ( 5 );
                $this->obRCadastroDinamico->obRModulo->setCodModulo ( 22 );
                break;
        }

        $this->obCmbAtributo->obEvento->setOnChange("ajaxJavaScriptSincrono('".CAM_GRH_PES_PROCESSAMENTO."OCIFiltroAtributoDinamico.php?".Sessao::getId()."&cod_cadastro=".$this->obRCadastroDinamico->getCodCadastro()."&cod_modulo=".$this->obRCadastroDinamico->obRModulo->getCodModulo()."&inCodAtributo='+this.value,'gerarSpanAtributo' );");

        $this->obRCadastroDinamico->recuperaAtributosSelecionados($rsAtributos);
        $this->obCmbAtributo->preencheCombo($rsAtributos);

        if ($this->getTituloFormulario()!='') {
          $obFormulario->addTitulo            ( $this->getTituloFormulario() );
        }
        $obFormulario->addComponente($this->obCmbAtributo);
        $obFormulario->addSpan($this->obSpnAtributo);
    }

}
?>

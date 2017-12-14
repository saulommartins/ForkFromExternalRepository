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
* Classe de agrupamentos de objetos para o Filtro por Contrato
* Data de Criação: 10/11/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Andre Almeida

* @package framework
* @subpackage componentes

Casos de uso: uc-00.00.00

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IPopUpCGMServidor.class.php" );

class IFiltroCGMContrato extends Objeto
{
    /**
        * @access Private
        * @var Integer
        * Tipos: todos, vigente, rescindido
    */
    public $stTipoContrato;
    /**
        * @access Private
        * @var Boolean
    */
    public $boInformacoesFuncao;
    /**
        * @access Private
        * @var String
    */
    public $stTituloFormulario;
    /**
        * @access Private
        * @var Boolean
    */
    public $obBscCGM;
    /**
        * @access Private
        * @var Boolean
    */
    public $obCmbContrato;
    /**
        * @access Private
        * @var Boolean
    */
    public $obLblInformacoesFuncao;

    /**
        * @access Public
        * @param Boolean $valor
    */
    public function setInformacoesFuncao($valor) { $this->boInformacoesFuncao    = $valor; }
    /**
        * @access Public
        * @param Integer $Valor
        * Tipos: todos, vigente, rescindido
    */
    public function setTipoContrato($valor)
    {
        $this->stTipoContrato = $valor;

        if (is_object($this->obBscCGM)) {
            $this->obBscCGM->setTipoContrato($valor);
        }
    }
    /**
        * @access Public
        * @param String $Valor
    */
    public function setTituloFormulario($valor) { $this->stTituloFormulario = $valor; }

    /**
        * @access Public
        * @return Integer
        * Tipos: todos, vigente, rescindido
    */
    public function getTipoContrato() { return $this->stTipoContrato; }
    /**
        * @access Public
        * @return boolean
    */
    public function getInformacoesFuncao() { return $this->boInformacoesFuncao; }
    /**
        * @access Public
        * @return String
    */
    public function getTituloFormulario() { return $this->stTituloFormulario; }

    /**
        * Método construtor
        * @access Private
    */
    public function IFiltroCGMContrato($boRescindido=false)
    {
        $this->setTipoContrato("todos");

        $this->obBscCGM = new IPopUpCGMServidor($boRescindido);
        $this->obBscCGM->setTipoContrato( $this->stTipoContrato );
        $this->obBscCGM->setPreencheCombo( true );

        $this->obCmbContrato = new Select;
        $this->obCmbContrato->setRotulo                   ( "Matrícula"                               );
        $this->obCmbContrato->setTitle                    ( "Informe a matrícula do CGM selecionado." );
        $this->obCmbContrato->setName                     ( "inContrato"                              );
        $this->obCmbContrato->setId                       ( "inContrato"                              );
        $this->obCmbContrato->setValue                    ( ""                                        );
        $this->obCmbContrato->setStyle                    ( "width: 200px"                            );
        $this->obCmbContrato->addOption                   ( "", "Selecione"                           );

        $this->obLblInformacoesFuncao = new Label;
        $this->obLblInformacoesFuncao->setId              ( "stInformacoesFuncao"                     );
        $this->obLblInformacoesFuncao->setRotulo          ( "Informações da Função"                   );
        $this->obLblInformacoesFuncao->setValue           ( ""                                        );

        $this->setTituloFormulario("Filtro por CGM/Matrícula");
    }

    /**
        * Monta os combos de localização conforme o nível setado
        * @access Public
        * @param  Object $obFormulario Objeto formulario
    */
    public function geraFormulario(&$obFormulario)
    {
        if ( $this->getInformacoesFuncao() ) {
            $this->obCmbContrato->obEvento->setOnChange       ( "ajaxJavaScriptSincrono( '".CAM_GRH_PES_PROCESSAMENTO."OCFiltroCGM.php?".Sessao::getId()."&inContrato='+this.value, 'preencheInformacoesFuncao' );");
        }

        $obFormulario->addTitulo                    ( $this->getTituloFormulario()                    );
        $obFormulario->addComponente                ( $this->obBscCGM                                 );
        $obFormulario->addComponente                ( $this->obCmbContrato                            );
        if ( $this->getInformacoesFuncao() ) {
            $obFormulario->addComponente            ( $this->obLblInformacoesFuncao                   );
        }
    }

}
?>

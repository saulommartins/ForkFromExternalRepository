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
    * Classe do componente do código do estágio
    * Data de Criação: 05/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
//include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                 );
//include_once ( CAM_GRH_PES_NEGOCIO."RPessoalContrato.class.php"                                     );

class IPopUpCodigoEstagio extends Componente
{
    /**
    * @access Private
    * @var Objeto
    */
    public $obTxtCodigoEstagio;
    /**
    * @access Private
    * @var Objeto
    */
    public $obLblCGM;
    /**
    * @access Private
    * @var String
    */
    public $stFuncaoBusca;
    /**
    * @access Public
    * @param Objeto $Valor
    */
    public function setCodigoEstagio($valor) { $this->obTxtCodigoEstagio  = $valor; }
    /**
    * @access Public
    * @param Objeto $Valor
    */
    public function setLabelCGM($valor) { $this->obLblCGM  = $valor; }
    /**
    * @access Public
    * @param String $Valor
    */
    public function setFuncaoBusca($valor) { $this->stFuncaoBusca     = $valor; }
    /**
    * @access Public
    * @return Objeto
    */
    public function getCodigoEstagio() { return $this->obTxtCodigoEstagio; }
    /**
    * @access Public
    * @return Objeto
    */
    public function getLabelCGM() { return $this->obLblCGM; }
    /**
    * @access Public
    * @return String
    */
    public function getFuncaoBusca() { return $this->stFuncaoBusca; }

    /**
    * Método construtor
    * @access Private
    */
    public function IPopUpCodigoEstagio($inCodigoEstagio="")
    {
        parent::Componente();
        $this->setLabelCGM( new Label() );
        $this->obLblCGM->setRotulo("CGM");
        $this->obLblCGM->setId("stCGM");

        $this->setName("inCodigoEstagio");
        $this->setCodigoEstagio( new TextBox );
        $this->obTxtCodigoEstagio->setRotulo                   ( "Código Estágio"                                       );
        $this->obTxtCodigoEstagio->setTitle                    ( "Informe o código do estágio do estagiário."                );
        $this->obTxtCodigoEstagio->setName                     ( "inCodigoEstagio"                                      );
        $this->obTxtCodigoEstagio->setId                       ( "inCodigoEstagio"                                      );
        $this->obTxtCodigoEstagio->setValue                    ( $inCodigoEstagio                                       );
        $this->obTxtCodigoEstagio->setInteiro                  ( true                                              );
        $this->obTxtCodigoEstagio->setMaxLength                ( 10                                           );
        $this->obTxtCodigoEstagio->setMinLength                ( 1                                                 );
        $this->obTxtCodigoEstagio->setSize                     ( 10                                           );
        $this->obTxtCodigoEstagio->obEvento->setOnChange( "ajaxJavaScriptSincrono( '".CAM_GRH_EST_PROCESSAMENTO."OCIPopUpCodigoEstagio.php?".Sessao::getId()."&".$this->obTxtCodigoEstagio->getName()."='+document.frm.".$this->obTxtCodigoEstagio->getName().".value, 'validarCodigoEstagio' );");

        //DEFINICAO DA IMAGEM
        $this->obImagem    = new Img;
        $this->obImagem->setCaminho( CAM_FW_IMAGENS."botao_popup.png");
        $this->obImagem->setAlign  ( "absmiddle" );

        $this->setRotulo( "Código Estágio" );
        $this->setTitle ( "Informe o código do estágio do estagiário." );

        $this->setFuncaoBusca( "abrePopUp('".CAM_GRH_EST_POPUPS."estagiarios/FLProcurarCodigoEstagio.php','frm','inCodigoEstagio','".$this->obLblCGM->getId()."','','".Sessao::getId()."','800','550')" );
    }

    /**
    * Monta os combos de localização conforme o nível setado
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
    public function geraFormulario(&$obFormulario)
    {
        $obFormulario->addComponente($this->obLblCGM);
        $obFormulario->addComponente($this);
    }

    /**
    * Monta o HTML do Objeto Label
    * @access Private
*/
    public function montaHTML()
    {
        $this->obTxtCodigoEstagio->montaHTML();
        $this->obImagem->montaHTML();

        $stTitleImagem = strtolower(preg_replace("/\*/","",$this->stRotulo));
        $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$stTitleImagem."'>";
        $stLink .= $this->obImagem->getHTML();
        $stLink .= "</a>";

        $obTabela = new Tabela;
        $obTabela->setCellPadding( 0 );
        $obTabela->setCellSpacing( 0 );
        $obTabela->setWidth( 100 );
        $obTabela->addLinha();
        if ( $this->obTxtCodigoEstagio->getMinLength() > 0 ) {
            $obTabela->ultimaLinha->addCelula();
            $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
            $obTabela->ultimaLinha->ultimaCelula->setWidth( $this->obTxtCodigoEstagio->getSize() );
            $obTabela->ultimaLinha->ultimaCelula->addConteudo( $this->obTxtCodigoEstagio->getHTML() );
            $obTabela->ultimaLinha->commitCelula();
        }
        $obTabela->ultimaLinha->addCelula();
        $obTabela->ultimaLinha->ultimaCelula->setClass( "fieldleft" );
        $obTabela->ultimaLinha->ultimaCelula->setValign( "top" );
        $obTabela->ultimaLinha->ultimaCelula->addConteudo( $stLink );
        $obTabela->ultimaLinha->commitCelula();

        $obTabela->commitLinha();
        $obTabela->montaHTML();
        $this->setHTML( $obTabela->getHTML() );
    }

}
?>

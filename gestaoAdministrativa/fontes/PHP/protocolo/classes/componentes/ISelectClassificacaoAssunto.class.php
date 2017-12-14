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
    *
    * Data de Criação: 16/08/2006

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.06.98

    $Id: ISelectClassificacaoAssunto.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class ISelectClassificacaoAssunto extends Componente
{
var $obTxtChave;
var $obCmbClassificacao;
var $obCmbAssunto;
var $stMascaraAssunto;

function ISelectClassificacaoAssunto()
{
    parent::Componente();
    $this->setRotulo( "Classificação/Assunto" );

    $this->obTxtChave = new TextBox;
    $this->obTxtChave->setName( "stChaveClassificacaoAssunto" );

    $this->obCmbClassificacao = new Select;
    $this->obCmbClassificacao->setName( "inCodClassificacao" );

    $this->obCmbAssunto = new Select;
    $this->obCmbAssunto->setName( "inCodAssunto" );
}

function montarComponentes()
{
    $stCaminho = CAM_GA_PROT_INSTANCIAS."processamento/OCIMOntaClassificaoAssunto.php?".Sessao::getId();

    $this->obTxtChave->setRotulo( $this->getRotulo() );
    $stParametros = "'chave&".$this->obTxtChave->getName()."='+this.value";
    //$this->obTxtChave->obEvento->setOnChange( "ajaxJavaScript( '".$stCaminho."',".$stParametros.");".$this->obTxtChave->obEvento->getOnChange() );
    $this->obTxtChave->obEvento->setOnChange( "ajaxJavaScript( '".$stCaminho."',".$stParametros.");".$this->obTxtChave->obEvento->getOnChange() );

    $this->obCmbClassificacao->setRotulo    ( $this->getRotulo() );
    $this->obCmbClassificacao->setStyle     ( "width: 300px" );
    $this->obCmbClassificacao->setCampoId   ( "cod_classificacao" );
    $this->obCmbClassificacao->setCampoDesc ( "nom_classificacao" );
    $this->obCmbClassificacao->addOption    ( "","Selecione uma Classificação" );
    $stParametros = "'classificacao&".$this->obCmbClassificacao->getName()."='+this.value";
    $this->obCmbClassificacao->obEvento->setOnChange( "ajaxJavaScript( '".$stCaminho."',".$stParametros.");".$this->obCmbClassificacao->obEvento->getOnChange() );

    $this->obCmbAssunto->setRotulo    ( $this->getRotulo() );
    $this->obCmbAssunto->setStyle     ( "width: 300px" );
    $this->obCmbAssunto->setCampoId   ( "cod_assunto" );
    $this->obCmbAssunto->setCampoDesc ( "nom_assunto" );
    $this->obCmbAssunto->addOption    ( "", "Selecione um Assunto" );
    $stParametros = "'assunto&".$this->obCmbAssunto->getName()."='+this.value";
    $stParametros .= "+'&".$this->obCmbClassificacao->getName();
    $stParametros .= "='+document.frm.".$this->obCmbClassificacao->getName().".value";
    $this->obCmbAssunto->obEvento->setOnChange( "ajaxJavaScript( '".$stCaminho."',".$stParametros.");".$this->obCmbAssunto->obEvento->getOnChange() );
}

function montarMascaraAssunto()
{
    $this->stMascara =  SistemaLegado::pegaConfiguracao('mascara_assunto', 5, Sessao::getExercicio() );
    $this->obTxtChave->setMascara( $this->stMascara );
    $this->obTxtChave->setSize( strlen( $this->obTxtChave->getMascara() ) );
    $this->obTxtChave->setMaxLength( strlen( $this->obTxtChave->getMascara() ) );
    if ( $this->obCmbClassificacao->getValue() ) {
        $arMascara = preg_split( "/[^a-zA-Z0-9]/", $this->stMascara );
        $chSeparador = substr($this->stMascara, strlen($arMascara[0]), 1);
        $stChaveComposta  = str_pad($this->obCmbClassificacao->getValue(),strlen($arMascara[0]),'0', STR_PAD_LEFT);
        $stChaveComposta .= $chSeparador;
        $stChaveComposta .= str_pad($this->obCmbAssunto->getValue(),strlen($arMascara[1]),'0', STR_PAD_LEFT);
        $this->obTxtChave->setValue( $stChaveComposta );
    }
}

function preencherComboClassificacao()
{
    include_once( CAM_GA_PROT_MAPEAMENTO."TClassificacao.class.php" );
    $obTClassificacao = new TClassificacao;
    $obTClassificacao->recuperaTodos( $rsClassificacao, "","nom_classificacao" );
    $this->obCmbClassificacao->preencheCombo( $rsClassificacao );
}

function preencherComboAssunto()
{
    include_once( CAM_GA_PROT_MAPEAMENTO."TAssunto.class.php" );
    $obTAssunto = new TAssunto;
    $stFiltro = ' WHERE COD_CLASSIFICACAO = '.$this->obCmbClassificacao->getValue();
    $obTAssunto->recuperaTodos($rsAssunto,$stFiltro);
    $this->obCmbAssunto->preencheCombo($rsAssunto);
}

function montaHTML()
{
    $this->montarMascaraAssunto();
    $this->montarComponentes();
    $this->preencherComboClassificacao();
    if ( $this->obCmbClassificacao->getValue() ) {
        $this->preencherComboAssunto();
    }
    $obFormulario = new Formulario;
    $obFormulario->addComponente( $this->obTxtChave );
    $obFormulario->addComponente( $this->obCmbClassificacao);
    $obFormulario->addComponente( $this->obCmbAssunto );
    $obFormulario->montaInnerHTML();
    $this->setHTML( str_replace("\\", "", $obFormulario->getHTML() ) );
}

function show()
{
    $this->montaHTML();
    echo $this->getHTML();
}

function geraFormulario(&$obFormulario)
{
    $this->obTxtChave->setNull( $this->getNull() );
    $this->obCmbClassificacao->setNull( $this->getNull() );
    $this->obCmbAssunto->setNull( $this->getNull() );

    $this->montarMascaraAssunto();
    $this->montarComponentes();
    $this->preencherComboClassificacao();
    if ( $this->obCmbClassificacao->getValue() ) {
        $this->preencherComboAssunto();
    }
    $obFormulario->addComponente( $this->obTxtChave );
    $obFormulario->addComponente( $this->obCmbClassificacao);
    $obFormulario->addComponente( $this->obCmbAssunto );
}

}
?>

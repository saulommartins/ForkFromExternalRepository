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
    * Data de Criação: 18/08/2006

    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Documentor: Cassiano de Vasconcellos Ferreira

    * @package framework
    * @subpackage componentes

    Casos de uso: uc-01.06.98

    $Id: ITextChaveProcesso.class.php 59612 2014-09-02 12:00:51Z gelson $
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';

class ITextChaveProcesso extends TextBox
{
    public $stMascara;
    public $obValidar;

function setMascara($valor) { $this->stMascara = $valor; }

function getMascara() { return $this->stMascara; }

function setValidarComponente($stValidar = false) { $this->obValidar = $stValidar;}

function getValidarComponente() {return $this->obValidar;}

function ITextChaveProcesso()
{
    parent::TextBox();
    $this->setName    ( "inCodProcesso" );
    $this->setRotulo  ( "Processo" );
}

function montarMascaraProcesso()
{
    $stMascara = SistemaLegado::pegaConfiguracao('mascara_processo', 5, Sessao::getExercicio() );
    $inTamanhoMascara = strlen( $stMascara );
    $this->setMascara   ( $stMascara );
    $this->setSize      ( $inTamanhoMascara );
    $this->setMaxlength ( $inTamanhoMascara );
    $this->obEvento->setOnChange( "this.value = preencheProcessoComZeros( this.value, '".$stMascara."','".Sessao::getExercicio()."');" );
}

function montarValidacao()
{
    $stMascara = SistemaLegado::pegaConfiguracao('mascara_processo', 5, Sessao::getExercicio() );
    $inTamanhoMascara = strlen( $stMascara );
    $this->setMascara   ( $stMascara );
    $this->setSize      ( $inTamanhoMascara );
    $this->setMaxlength ( $inTamanhoMascara );
    $this->obEvento->setOnChange( "this.value = preencheProcessoComZeros( this.value, '".$stMascara."','".Sessao::getExercicio()."'); ajaxJavaScript('".CAM_GA_PROT_INSTANCIAS."processamento/OCIPopUpProcesso.php?".Sessao::getId()."&stNomCampo='+this.name+'&stNumProcesso='+this.value,'preencheProcesso'); " );
}

function montaHTML()
{
    $this->montarMascaraProcesso();

    if ($this->getValidarComponente()) {
        $this->montarValidacao();
    }

    parent::montaHTML();
}

function geraFormulario(&$obFormulario)
{
    $obFormulario->addComponente( $this );
}

}
?>

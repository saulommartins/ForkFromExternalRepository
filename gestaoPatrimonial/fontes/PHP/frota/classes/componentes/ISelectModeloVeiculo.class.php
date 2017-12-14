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
* Data de Criação: 14/07/2006

* @author Desenvolvedor: Gelson Wolowski Gonçalves
* @author Documentor: Gelson Wolowski Gonçalves

* $Id: ISelectModeloVeiculo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-03.02.04

*/

class  ISelectModeloVeiculo extends Select
{
var $obForm;
//var $obTFrotaModeloVeiculo;
var $obISelectMarcaVeiculo;

function ISelectModeloVeiculo(&$obForm)
{
    parent::Select();
    include_once (CAM_GP_FRO_COMPONENTES."ISelectMarcaVeiculo.class.php" );

    $this->obForm = &$obForm;
    $this->obISelectMarcaVeiculo  = new ISelectMarcaVeiculo($obForm);
    $this->obSelectModeloVeiculo  = new Select();
    $this->obSelectModeloVeiculo->setName   ('inCodModelo');
    $this->obSelectModeloVeiculo->setId     ('inCodModelo');
    $this->obSelectModeloVeiculo->setStyle  ( "width: 270px; ");
    $this->obSelectModeloVeiculo->setRotulo ( "Modelo" );
    $this->obSelectModeloVeiculo->setTitle  ( "Selecione o modelo do veículo." );
    $this->obSelectModeloVeiculo->setNull   ( false );
    $this->obSelectModeloVeiculo->addOption ("", "Selecione");
}

function geraFormulario(&$obFormulario)
{
    if ( $this->getNull() ) {
        $this->obSelectModeloVeiculo->setNull( true );
        $this->obISelectMarcaVeiculo->setNull( true );
    }

    $pgOcul  = CAM_GP_FRO_PROCESSAMENTO.'OCISelectModeloVeiculo.php?'.Sessao::getId();
    $pgOcul .= '&stMarca='.$this->obISelectMarcaVeiculo->getId();
    $pgOcul .= '&stModelo='.$this->obSelectModeloVeiculo->getId();
    $this->obISelectMarcaVeiculo->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."&inCodMarca='+this.value,'montaModelo');");
    if ( $this->obISelectMarcaVeiculo->getValue() != '' ) {
        include_once(CAM_GP_FRO_MAPEAMENTO."TFrotaModelo.class.php");
        $obTFrotaModelo = new TFrotaModelo();
        $obTFrotaModelo->recuperaTodos( $rsModelo, ' WHERE cod_marca = '.$this->obISelectMarcaVeiculo->getValue().' ');
        while ( !$rsModelo->eof() ) {
            $this->obSelectModeloVeiculo->addOption( $rsModelo->getCampo('cod_modelo'), $rsModelo->getCampo('nom_modelo') );
            $rsModelo->proximo();
        }
    }

    $obFormulario->addComponente($this->obISelectMarcaVeiculo);
    $obFormulario->addComponente($this->obSelectModeloVeiculo);
}

}
?>

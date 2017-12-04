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
* Gerar o componente de tipo de busca que o usuário pode fazer com um string
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Gera o componente tipo text de acordo com os valores setados pelo Usuário
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

    * @package Interface
    * @subpackage Componente
*/
class TipoBusca extends Componente
{
//PROPRIEDADES DA CLASSE
/**
    * @access Private
    * @var Object
*/
var $obCmbTipoBusca;
/**
    * @access Private
    * @var Object
*/
var $obHdnCampo;
/**
    * @access Private
    * @var Object
*/
var $obCmpCampo;

/**
    * @access Public
    * @param Object $valor
*/
function setCmbTipoBusca($valor) { $this->obCmbTipoBusca = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setHdnCampo($valor) { $this->obHdnCampo     = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setCmpCampo($valor) { $this->obCmpCampo     = $valor; }

/**
    * @access Private
    * @return Object
*/
function getCmbTipoBusca() { return $this->obCmbTipoBusca; }
/**
    * @access Private
    * @return Object
*/
function getHdnCampo() { return $this->obHdnCampo;     }
/**
    * @access Private
    * @return Object
*/
function getCmpCampo() { return $this->obCmpCampo;     }

/**
    * Método Construtor
    * @access Public
*/
function TipoBusca(&$obCmpCampo)
{
   parent::Componente();
   $stHdnName = $obCmpCampo->getName();
   $stHdnName = substr( $stHdnName, 0, 2 )."Hdn".substr( $stHdnName, 2, strlen( $stHdnName ) );

   $obHdnCampo = new Hidden;
   $obHdnCampo->setName( $stHdnName );
   $this->setHdnCampo( $obHdnCampo );

   $stNomeCampoBusca = 'stTipoBusca'.substr( $obCmpCampo->getName(), 2,strlen( $obCmpCampo->getName() ) );

   $obcmbTipoBusca = new Select;
   $obcmbTipoBusca->setName( $stNomeCampoBusca );
   $obcmbTipoBusca->setValue ( 'inicio' );
   $obcmbTipoBusca->addOption( 'inicio' , 'Início' );
   $obcmbTipoBusca->addOption( 'final'  , 'Final'  );
   $obcmbTipoBusca->addOption( 'contem' , 'Contém' );
   $obcmbTipoBusca->addOption( 'exata'       , 'Exata'  );
   $obcmbTipoBusca->obEvento->setOnChange( 'tipoBusca( this, \''.$obCmpCampo->getName().'\', \''.$stHdnName.'\', 1)' );

   $obCmpCampo->obEvento->setOnChange( 'tipoBusca( \''.$stNomeCampoBusca.'\', this,\''.$stHdnName.'\', 2 )' );

   $this->setCmbTipoBusca( $obcmbTipoBusca );
   $this->setCmpCampo( $obCmpCampo );
   $this->setDefinicao( 'TIPOBUSCA' );
   $this->setName( 'stTipoBusca' );

   $stTitle = $obCmpCampo->getTitle() ? $obCmpCampo->getTitle()." e selecione o tipo de busca." : "Selecione o tipo de busca.";

   $this->setTitle( $stTitle );
}

function getRotulo() { return $this->obCmpCampo->getRotulo(); }
function getNull() { return $this->obCmpCampo->getNull(); }

function montaHTML()
{
    $this->obCmpCampo->montaHTML();
    $this->obCmbTipoBusca->montaHTML();
    $this->obHdnCampo->montaHTML();
    $this->setHTML( $this->obCmpCampo->getHTML()."&nbsp;".$this->obCmbTipoBusca->getHTML().$this->obHdnCampo->getHTML() );
}
}

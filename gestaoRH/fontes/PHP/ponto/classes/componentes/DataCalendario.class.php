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
    * Classe do componente de data com calendário
    * Data de Criação   : 03/10/2008

    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Diego Lemos de Souza

    * @package URBEM
    * @subpackage

    $Id:$
*/

/**
    * Classe de que monta o HTML do text de Data

    * @package framework
    * @subpackage componentes
*/
class DataCalendario extends Data
{
/**
    * @access Private
    * @var String
*/
var $stFuncaoBusca;
/**
    * @access Private
    * @var Objeto
*/
var $obImagem;

function setFuncaoBusca($stValor) {$this->stFuncaoBusca = $stValor;}
function setImagem() {$this->obImagem = new Img();}

function getFuncaoBusca() {return $this->stFuncaoBusca;}
function getImagem() {return $this->obImagem;}

/**
    * Método Construtor
    * @access Public
*/
function DataCalendario()
{
    parent::Data();
    $this->setImagem();
    $this->getImagem()->setCaminho   ( CAM_FW_IMAGENS."calendario.gif");
    $this->getImagem()->setAlign     ( "absmiddle" );
    $this->montaFuncaoBusca();
}

function montaFuncaoBusca()
{
    $this->setFuncaoBusca("abrePopUp('".CAM_GRH_PON_POPUPS."compensacoes/FMBuscaDataCalendario.php','frm','".$this->getName()."','".$this->getId()."','','".Sessao::getId()."','800','550')" );
}

/**
    * Monta o HTML do Objeto Label
    * @access Private
*/
function montaHTML()
{
    parent::montaHTML();
    $this->getImagem()->montaHTML();

    $stLink  = "&nbsp;<a href=\"JavaScript: ".$this->getFuncaoBusca().";\" title='Buscar ".$this->getRotulo()."'>";
    $stLink .= $this->getImagem()->getHTML();
    $stLink .= "</a>";

    $obTabela = new Tabela;
    $obTabela->setCellPadding( 0 );
    $obTabela->setCellSpacing( 0 );
    $obTabela->setWidth( 100 );
    $obTabela->addLinha();
    $obTabela->ultimaLinha->addCelula();
    $obTabela->ultimaLinha->ultimaCelula->setClass( "field" );
    $obTabela->ultimaLinha->ultimaCelula->setWidth( $this->getSize() );
    $obTabela->ultimaLinha->ultimaCelula->addConteudo( parent::getHTML() );
    $obTabela->ultimaLinha->commitCelula();
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

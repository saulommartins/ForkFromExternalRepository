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
* Classe de Formulário para trabalhar com abas
* Data de Criação: 29/06/2004

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Diego Barbosa Victoria

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de Formulário que trabalha de maneira similar a Formulário, mas contêm recursos de Aba
    * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @package Interface
*/
class FormularioAbas extends Formulario
{
/**
    * @var Array
    * @access Private
*/
var $arAbas;
/**
    * @var String
    * @access Private
*/
var $stLayerAtivo;

/**
    * @access Public
    * @param String $valor
*/
function setLayerAtivo($valor) { $this->stLayerAtivo = $valor;}
/**
    * @access Public
    * @return String
*/
function getLayerAtivo() { return $this->stLayerAtivo;  }

/**
    * Método Construtor
    * @access Private
*/
function FormularioAbas()
{
    parent::Formulario();
    $this->stLayerAtivo = "layer_1";
}

/**
    * Adiciona uma aba no sistema, sendo indicado seu Label e se existe quebra ou não.
    * O limite dos componentes da aba, dá-se até a existência de outra chamada a este método.
    * @access Public
    * @param String  $stLabel  Label da Aba
    * @param Boolean $boQuebra Valor booleano que indica se esta nova aba não pertencerá a mesma linha das anteriores
*/
function addAba($stLabel = "", $boQuebra = false)
{
    $arAba = new Aba();
    $arAba->setLabel ( $stLabel  );
    $arAba->setQuebra( $boQuebra );
    $arAba->setReferencia( $arAba->getReferencia() . count($this->arAbas) );
    if ( count($this->arAbas)>0 ) {
        $inIndiceAnterior = $this->arAbas[ count($this->arAbas)-1 ]->getIndice();
        $arAba->setIndice( count($this->arLinha) - $inIndiceAnterior );
    }
    if ($stLabel) {
        $this->arAbas[] = $arAba;
        $stNomeLayer = "layer_".count($this->arAbas);
        $this->obJavaScript->addLayer( $stNomeLayer );
        $this->addDiv( count($this->arAbas), $stNomeLayer );
    } else {
        $stNomeLayer = "NULO";
        $this->fechaDiv();
    }
}

/**
    * Adiciona uma aba no sistema, sendo indicado seu Label e se existe quebra ou não.
    * O limite dos componentes da aba, dá-se até a existência de outra chamada a este método.
    * @access Public
    * @param String  $stFuncao  Label da Aba
*/
function addFuncaoAba($stFuncao)
{
    $inIndiceAba = count($this->arAbas) - 1;
    $this->arAbas[$inIndiceAba]->setFuncao( $stFuncao );
}

/**
    * Monta o Html do Formulário com Abas
    * @access Private
*/
function montaHTML()
{
    parent::montaHTML();
    $inContAbas = 1;
    $inIndiceAba = 0;
    $arColunasLinha = array();
    $arTabelaCabc = array();
    for ($inCount=0; $inCount<count($this->arAbas); $inCount++) {
        if ( ($this->arAbas[$inCount]->getQuebra()) || ($inCount==count($this->arAbas)-1) ) {
            $arColunasLinha[$inIndiceAba++] = $inContAbas;//RECEBE O NUMERO DE COLUNAS QUE CADA LINHA IRA TER
            $inContAbas = 0;
       }
       $inContAbas++;
    }

    $inCountAbas=0;
    foreach ($arColunasLinha as $inIndice => $inValor) {
        $inLargura = (int) ( 100 / $inValor );
        $arTabelaCabc[$inIndice] = new tabela;
        $arTabelaCabc[$inIndice]->addLinha();
        for ($inCount = 0 ; $inCount<$inValor; $inCount++) {
            $obLink = new Link;
            $obLink->setValue( $this->arAbas[$inCountAbas]->getLabel() );
            $obLink->setId( "id_layer_".($inCountAbas+1) );
            if ( $this->arAbas[$inCountAbas]->getFuncao() ) {
                $obLink->obEvento->setOnClick( $this->arAbas[$inCountAbas]->getFuncao() );
            }
            $stHref = "JavaScript:HabilitaLayer('"."layer_".($inCountAbas+1)."');";
            $obLink->setHref($stHref);
            $obLink->montaHtml();

            $arTabelaCabc[$inIndice]->ultimaLinha->addCelula();
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setClass    ( "labelcenter_aba" );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setId       ( "celula_".($inCountAbas+1) );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setWidth    ( $inLargura );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->addConteudo ( $obLink->getHtml() );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setTitleValue  ( $this->arAbas[$inCountAbas]->getTipTitle() );
            if ( $this->arAbas[$inCountAbas]->getFuncao() ) {
                $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->obEvento->setOnClick($this->arAbas[$inCountAbas]->getFuncao().$stHref );
            }
            $arTabelaCabc[$inIndice]->ultimaLinha->commitCelula();
            $inCountAbas++;
        }
        $arTabelaCabc[$inIndice]->commitLinha();
    }

    $stHTML = "";

    foreach ($arTabelaCabc as $obTabelaCabc) {
        $obTabelaCabc->montaHTML();
        $stHTML .= $obTabelaCabc->getHTML();
    }
    $stHTML .= $this->stHTML;
    $stHTML .= "\n<script>\n HabilitaLayer('".$this->stLayerAtivo."');\n</script>\n";
    $stHTML .= "\n<script type='text/javascript'>\nexecuta();\n</script>\n";
    $this->stHTML = $stHTML;
}

/**
    * Monta o Html do Formulário com Abas
    * @access Private
*/
function montaInnerHTML()
{
    parent::montaInnerHTML();
    $inContAbas = 1;
    $inIndiceAba = 0;
    $arColunasLinha = array();
    $arTabelaCabc = array();
    for ($inCount=0; $inCount<count($this->arAbas); $inCount++) {
        if ( ($this->arAbas[$inCount]->getQuebra()) || ($inCount==count($this->arAbas)-1) ) {
            $arColunasLinha[$inIndiceAba++] = $inContAbas;//RECEBE O NUMERO DE COLUNAS QUE CADA LINHA IRA TER
            $inContAbas = 0;
       }
       $inContAbas++;
    }

    $inCountAbas=0;
    foreach ($arColunasLinha as $inIndice => $inValor) {
        $inLargura = (int) ( 100 / $inValor );
        $arTabelaCabc[$inIndice] = new tabela;
        $arTabelaCabc[$inIndice]->addLinha();
        for ($inCount = 0 ; $inCount<$inValor; $inCount++) {
            $obLink = new Link;
            $obLink->setValue( $this->arAbas[$inCountAbas]->getLabel() );
            $obLink->setHref("JavaScript:HabilitaLayer('"."layer_".($inCountAbas+1)."');");
            if ( $this->arAbas[$inCountAbas]->getFuncao() ) {
                $obLink->obEvento->setOnClick( $this->arAbas[$inCountAbas]->getFuncao() );
            }
            $obLink->montaHtml();

            $arTabelaCabc[$inIndice]->ultimaLinha->addCelula();
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setClass       ( "labelcenter_aba" );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setId          ( "celula_".($inCountAbas+1) );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->setWidth       ( $inLargura );
            $arTabelaCabc[$inIndice]->ultimaLinha->ultimaCelula->addConteudo    ( $obLink->getHtml() );
            $arTabelaCabc[$inIndice]->ultimaLinha->commitCelula();
            $inCountAbas++;
        }
        $arTabelaCabc[$inIndice]->commitLinha();
    }

    $stHTML = "";

    foreach ($arTabelaCabc as $obTabelaCabc) {
        $obTabelaCabc->montaHTML();
        $stHTML .= $obTabelaCabc->getHTML();
    }
    $stHTML .= $this->stHTML;
    $stHTML .= "\n<script>\n HabilitaLayer('".$this->stLayerAtivo."');\n</script>\n";
    $this->stHTML = $stHTML;
}

}

?>

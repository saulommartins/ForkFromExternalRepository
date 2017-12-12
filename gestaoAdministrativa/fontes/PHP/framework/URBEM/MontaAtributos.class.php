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
* Data de Criação: 27/10/2005

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Documentor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00
*/

/**
    * Classe que monta os Atributos Dinamicos em um formulário
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class MontaAtributos extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stName;
/**
    * @access Private
    * @var String
*/
var $arNomeInput;

/**
    * @access Private
    * @var String
*/
var $stTitulo;
/**
    * @access Private
    * @var Object
*/
var $rsRecordSet;
/**
    * @access Private
    * @var Array
*/
var $arValores;
/**
    * @access Private
    * @var Object
*/
var $obFormulario;
/**
    * @access Private
    * @var Boolean
*/
var $boLabel;

/**
    * @access Public
    * @param String $valor
*/
function setName($valor) { $this->stName        = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRecordSet($valor) { $this->rsRecordSet   = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTitulo($valor) { $this->stTitulo      = $valor; }
/**
    * @access Public
    * @param Boolean $valor
*/
function setLabel($valor) { $this->boLabel       = $valor; }

/**
    * @access Public
    * @return String
*/
function getName() { return $this->stName;            }
/**
    * @access Public
    * @return Object
*/
function getRecordSet() { return $this->rsRecordSet;       }
/**
    * @access Public
    * @return String
*/
function getTitulo() { return $this->stTitulo;          }
/**
    * @access Public
    * @return Boolean
*/
function getLabel() { return $this->boLabel;           }

/**
    * Método Construtor
    * @access Private
*/
function MontaAtributos()
{
    $this->setTitulo( "Atributos" );
    $this->setLabel (false);
    $this->arNomeInput = array();
}

/**
    * Recupera os valores do RecordSet informado.
    * @access Public
*/
function recuperaValores()
{
    while (!$this->rsRecordSet->eof()) {
        if( $this->rsRecordSet->getCampo('cod_tipo') && !$this->rsRecordSet->getCampo('cod_cadastro') )
            $stAtributo  = $this->getName().$this->rsRecordSet->getCampo('cod_atributo').'_'.$this->rsRecordSet->getCampo('cod_tipo');
        else
            $stAtributo  = $this->getName().$this->rsRecordSet->getCampo('cod_atributo').'_'.$this->rsRecordSet->getCampo('cod_cadastro');
        $$stAtributo = $this->rsRecordSet->getCampo('valor');
        $this->arValores[ $stAtributo ] = $$stAtributo;
        $this->rsRecordSet->proximo();
    }
    $this->rsRecordSet->setPrimeiroElemento();
}
/**
    * Gera o formulário com os Atributos
    * @access Public
    * @param Object $obFormulario referência do objeto
    * @return Boolean
*/
function geraFormulario(&$obFormulario)
{
    if($this->rsRecordSet->eof())

        return false;

    $obFormulario->addTitulo( $this->getTitulo() );

    if (!$this->rsRecordSet->eof()) {
        $this->rsRecordSet->ordena('nom_atributo');
        $this->rsRecordSet->ordena('cod_tipo');
    }

    while (!$this->rsRecordSet->eof()) {
        if( $this->rsRecordSet->getCampo('cod_tipo') && !$this->rsRecordSet->getCampo('cod_cadastro') )
            $stAtributo = $this->getName().$this->rsRecordSet->getCampo('cod_atributo').'_'.$this->rsRecordSet->getCampo('cod_tipo');
        else
            $stAtributo  = $this->getName().$this->rsRecordSet->getCampo('cod_atributo').'_'.$this->rsRecordSet->getCampo('cod_cadastro');
//        $this->setName( $stAtributo );
        //publica para interface, qual o nome dos componentes que irao gerar o formulario

        array_push($this->arNomeInput, $stAtributo);
        global $stAtributo;

        //Identifica rotulo e adiciona espaços para não efetuar rowspan
        //-->
        $inCountEspacos = 0;
        $stEspacos      = '';
        for ($inCount=count($obFormulario->arLinha); $inCount>=0; $inCount--) {
            if ($obFormulario->arLinha[$inCount]->arCelula[1]->arComponente[0]->stRotulo) {
                $stRotuloAnterior = $obFormulario->arLinha[$inCount]->arCelula[1]->arComponente[0]->stRotulo;
                if(trim($stRotuloAnterior) == $this->rsRecordSet->getCampo('nom_atributo'))
                    $inCountEspacos++;
            }
        }
        for ($inCount=0; $inCount<$inCountEspacos; $inCount++) {
            $stEspacos .= ' ';
        }
        //<--
        switch ($this->rsRecordSet->getCampo('cod_tipo')) {
            //case 'Numerico':
            case '1':
                $obAtributo = new TextBox;
                $obAtributo->setName         ( $stAtributo );
                $stAtributoValor = ($this->arValores) ? $this->arValores[$stAtributo] : $$stAtributo;
                $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor');
                if ( $this->rsRecordSet->getCampo('timestamp') == "" ) {
                    $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor_padrao');
                }

                $stAtributoValor = (strlen(trim($stAtributoValor)) > 0) ? $stAtributoValor : "";
                $obAtributo->setValue        ( $stAtributoValor );
                $obAtributo->setRotulo       ( $stEspacos.$this->rsRecordSet->getCampo('nom_atributo') );
                $obAtributo->setInteiro      (true);
                $obAtributo->setSize         ( 30 );
                $obAtributo->setMaxLength    ( 30 );
                $obAtributo->setNull         ( ($this->rsRecordSet->getCampo('nao_nulo')=='t')?true:false );
            break;
            //case 'Texto':
            case '2':
                $obAtributo = new TextBox;
                $obAtributo->setName         ( $stAtributo );
                $stAtributoValor = ($this->arValores) ? $this->arValores[$stAtributo] : $$stAtributo;
                $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor');
                if ( $this->rsRecordSet->getCampo('timestamp') == "" ) {
                    $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor_padrao');
                }
                $stAtributoValor = (strlen(trim($stAtributoValor)) > 0) ? $stAtributoValor : "";
                $obAtributo->setValue        ( $stAtributoValor );
                $obAtributo->setRotulo       ( $stEspacos.$this->rsRecordSet->getCampo('nom_atributo') );
                $obAtributo->setInteiro      (false);
                $obAtributo->setSize         ( 30 );
                $obAtributo->setMaxLength    ( 30 );
                $obAtributo->setNull         ( ($this->rsRecordSet->getCampo('nao_nulo')=='t')?true:false );
            break;
            //case 'Lista':
            case '3':
                $arValorPadraoTMP = array();
                if ( $this->rsRecordSet->getCampo('valor_padrao') ) {
                    $arValorPadrao      = explode(","      , $this->rsRecordSet->getCampo('valor_padrao') );
                    $arValorPadraoDesc  = explode("[][][]" , $this->rsRecordSet->getCampo('valor_padrao_desc') );
                    foreach ($arValorPadrao as $key=>$value) {
                        $arValorPadraoTMP[$key]['inCodValor']  = $arValorPadrao[$key];
                        $arValorPadraoTMP[$key]['stDescValor'] = $arValorPadraoDesc[$key];
                    }
                } else {
                    $arValorPadraoTMP = array();
                }
                $rsValorPadrao = new RecordSet;
                $rsValorPadrao->preenche($arValorPadraoTMP);
                $rsValorPadrao->ordena('stDescValor');

                $obAtributo = new Select;
                $obAtributo->setName         ( $stAtributo );
                $stAtributoValor = ($this->arValores) ? $this->arValores[$stAtributo] : $$stAtributo;
                $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor');
                $obAtributo->setValue        ( $stAtributoValor );
                $obAtributo->setRotulo       ( $stEspacos.$this->rsRecordSet->getCampo('nom_atributo') );
                $obAtributo->addOption       ( "", "Selecione" );
                $obAtributo->setStyle        ( "width: 200px");
                $obAtributo->setCampoID      ( "inCodValor" );
                $obAtributo->setCampoDesc    ( "stDescValor" );
                $obAtributo->setNull         ( ($this->rsRecordSet->getCampo('nao_nulo')=='t')?true:false );
                $obAtributo->preencheCombo   ( $rsValorPadrao );
            break;
            //case 'Lista Múltipla':
            case '4':
                $arValorPadraoTMP = array();
                if ( $this->rsRecordSet->getCampo('valor_padrao') ) {
                    $arValorPadrao      = explode(","      , $this->rsRecordSet->getCampo('valor_padrao') );
                    $arValorPadraoDesc  = explode("[][][]" , $this->rsRecordSet->getCampo('valor_padrao_desc') );
                    foreach ($arValorPadrao as $key=>$value) {
                        $arValorPadraoTMP[$key]['inCodValor']  = $arValorPadrao[$key];
                        $arValorPadraoTMP[$key]['stDescValor'] = $arValorPadraoDesc[$key];
                    }
                }
                $arValorTMP = array();
                if ( $this->rsRecordSet->getCampo('valor') ) {
                    $arValor      = explode(","      , $this->rsRecordSet->getCampo('valor') );
                    $arValorDesc  = explode("[][][]" , $this->rsRecordSet->getCampo('valor_desc') );
                    foreach ($arValor as $key=>$value) {
                        $arValorTMP[$key]['inCodValor']  = $arValor[$key];
                        $arValorTMP[$key]['stDescValor'] = $arValorDesc[$key];
                    }
                }
                $rsValorPadrao = new RecordSet;
                $rsValorPadrao->preenche($arValorPadraoTMP);
                $rsValorPadrao->ordena('stDescValor');
                $rsValor = new RecordSet;
                $rsValor->preenche($arValorTMP);
                $rsValor->ordena('stDescValor');

                $obAtributo = new SelectMultiplo();
                $obAtributo->setName   ( $stAtributo );
                $obAtributo->setValorPadrao( " " );
                $obAtributo->setRotulo ( $stEspacos.$this->rsRecordSet->getCampo('nom_atributo') );
                $obAtributo->setNull   ( ($this->rsRecordSet->getCampo('nao_nulo')=='t')?true:false );
                $obAtributo->setTitle  ( $this->rsRecordSet->getCampo('nom_atributo') );
                // lista de atributos disponiveis
                $obAtributo->SetNomeLista1 ( $stAtributo . '_Disponiveis');
                $obAtributo->setCampoId1   ('inCodValor');
                $obAtributo->setCampoDesc1 ('stDescValor');
                $obAtributo->SetRecord1    ( $rsValorPadrao );
                // lista de atributos selecionados
                $obAtributo->SetNomeLista2 ( $stAtributo . '_Selecionados');
                $obAtributo->setCampoId2   ('inCodValor');
                $obAtributo->setCampoDesc2 ('stDescValor');
                $obAtributo->SetRecord2    ( $rsValor );
            break;
            //case 'Data':
            case '5':
                $obAtributo = new Data;
                $obAtributo->setName         ( $stAtributo );

                $stAtributoValor = ($this->arValores) ? $this->arValores[$stAtributo] : $$stAtributo;
                $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor');
                if ( $this->rsRecordSet->getCampo('timestamp') == "" ) {
                    $stAtributoValor = ($stAtributoValor) ? $stAtributoValor : $this->rsRecordSet->getCampo('valor_padrao');
                }

                $stAtributoValor = (strlen(trim($stAtributoValor)) > 0) ? $stAtributoValor : "";
                $obAtributo->setValue        ( $stAtributoValor );
                $obAtributo->setRotulo       ( $stEspacos.$this->rsRecordSet->getCampo('nom_atributo') );
                $obAtributo->setNull         ( ($this->rsRecordSet->getCampo('nao_nulo')=='t')?true:false );
            break;
        }

        if ( $this->getLabel() ) {
            $obLabel = new Label;
            $obLabel->setName         ( $obAtributo->getName() );
            if ($this->rsRecordSet->getCampo('cod_tipo')==4) {
                $stTMP = "";
                for ($inCount=0; $inCount<count($arValorTMP); $inCount++) {
                    $stTMP .= $arValorTMP[$inCount]['stDescValor']."<br>";
                }
                $obLabel->setValue( $stTMP );
            } elseif ($this->rsRecordSet->getCampo('cod_tipo')==3) {
                $obLabel->setValue( $arValorPadraoTMP[ ($obAtributo->getValue()-1) ]['stDescValor'] );
            } else {
                $obLabel->setValue        ( $obAtributo->getValue() );
            }
            $obLabel->setRotulo       ( $obAtributo->getRotulo() );
            $obAtributo = $obLabel;
        }
        $obFormulario->addComponente( $obAtributo );
        $this->rsRecordSet->proximo();
    }

}
/**
    * Recupera o vetor na página de processamento
    * @access Public
    * @param Array Chave de saída.
    * @param Array Matriz padrão é o _POST, mas pode ser informada outra.
    * @return Boolean
*/
function recuperaVetor(&$arChave, $arMatriz='')
{
    $arChave = array();
    if(!$arMatriz)  $arMatriz = $_POST;
    foreach ($arMatriz as $key=>$value) {
        if (strstr($key,$this->getName())) {
            $arTmp = explode('_',substr($key,strlen($this->getName()),strlen($key)) );
            $arChave[ $arTmp[0].'-'.$arTmp[1] ] = $value;
        }
    }

    return true;
}

}
/*--------------------------------------------------+
|FIM DA CLASSE CLASSE LINHA                         |
+--------------------------------------------------*/
?>

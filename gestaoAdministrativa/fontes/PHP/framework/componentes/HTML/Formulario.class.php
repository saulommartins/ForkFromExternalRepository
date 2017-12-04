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
* Montar o HTML de um formulario de acordo com os valores setados pelo usuário
* Data de Criação: 06/02/2003

$Id: Formulario.class.php 62838 2015-06-26 13:02:49Z diogo.zarpelon $

* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

* @package framework
* @subpackage componentes

Casos de uso: uc-01.01.00

*/

/**
    * Classe de que monta o HTML do formulario

    * @package Interface
    * @subpackage Componente
*/
class Formulario extends Tabela
{
/**
    * @access Private
    * @var String
*/
var $stClassTitulo;

/**
    * @access Private
    * @var String
*/
var $stClassRotulo;

/**
    * @access Private
    * @var String
*/
var $stClassRotuloE;//ESQUERDA

/**
    * @access Private
    * @var String
*/
var $stClassRotuloD;//DIREITA

/**
    * @access Private
    * @var String
*/
var $stClassRotuloC;//CENTRALIZADO

/**
    * @access Private
    * @var String
*/
var $stClassCampo;

/**
    * @access Private
    * @var String
*/
var $stClassCampoE;

/**
    * @access Private
    * @var String
*/
var $stClassCampoD;

/**
    * @access Private
    * @var String
*/
var $stClassCampoC;

/**
    * @access Private
    * @var Integer
*/
var $inLarguraRotulo;

/**
    * @access Private
    * @var Integer
*/
var $inLarguraCampo;

/**
    * @access Private
    * @var Integer
*/
var $inQtdColunas;//NUMERO DE COLUNAS DO FORMULARIO

/**
    * @access Private
    * @var Object
*/
var $obForm;

/**
    * @access Private
    * @var Object
*/
var $obJavaScript;

/**
    * @access Private
    * @var Array
*/
var $arHidden;

/**
    * @access Private
    * @var String
*/
var $stArquivoJS;

/**
    * @access Private
    * @var Object
*/
var $obIFrame;
/**
    * @access Private
    * @var String
*/
var $stAjuda;

/**
    * @access Private
    * @var array
*/
var $arJavaScript;

//SETTERS
/**
    * @access Public
    * @param String $valor
*/

function setClassTitulo($valor) { $this->stClassTitulo    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassRotulo($valor) { $this->stClassRotulo    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassRotuloE($valor) { $this->stClassRotuloE   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassRotuloD($valor) { $this->stClassRotuloD   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassRotuloC($valor) { $this->stClassRotuloC   = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassCampo($valor) { $this->stClassCampo     = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassCampoE($valor) { $this->stClassCampoE    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassCampoD($valor) { $this->stClassCampoD    = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setClassCampoC($valor) { $this->stClassCampoC    = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLarguraRotulo($valor) { $this->inLarguraRotulo  = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setLarguraCampo($valor) { $this->inLargutaCampo   = $valor; }

/**
    * @access Public
    * @param Integer $valor
*/
function setQtdColunas($valor) { $this->inQtdColunas     = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setForm($valor) { $this->obForm           = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setJavaScript($valor) { $this->obJavaScript     = $valor; }

/**
    * @access Public
    * @param Array $valor
*/
function setHidden($valor) { $this->arHidden         = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setArquivoJS($valor) { $this->stArquivoJS      = $valor; }

/**
    * @access Public
    * @param Object $valor
*/
function setIFrame($valor) { $this->obIFrame         = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setAjuda($valor) { $this->stAjuda         = $valor; }

//GETTERS
/**
    * @access Public
    * @return String
*/
function getClassTitulo() { return $this->stClassTitulo;    }

/**
    * @access Public
    * @return String
*/
function getClassRotulo() { return $this->stClassRotulo;    }

/**
    * @access Public
    * @return String
*/
function getClassRotuloE() { return $this->stClassRotuloE;   }

/**
    * @access Public
    * @return String
*/
function getClassRotuloD() { return $this->stClassRotuloD;   }

/**
    * @access Public
    * @return String
*/
function getClassRotuloC() { return $this->stClassRotuloC;   }

/**
    * @access Public
    * @return String
*/
function getClassCampo() { return $this->stClassCampo;     }

/**
    * @access Public
    * @return String
*/
function getClassCampoE() { return $this->stClassCampoE;    }

/**
    * @access Public
    * @return String
*/
function getClassCampoD() { return $this->stClassCampoD;    }

/**
    * @access Public
    * @return String
*/
function getClassCampoC() { return $this->stClassCampoC;    }

/**
    * @access Public
    * @return Integer
*/
function getLarguraRotulo() { return $this->inLarguraRotulo;  }

/**
    * @access Public
    * @return Integer
*/
function getLarguraCampo() { return $this->inLarguraCampo;   }

/**
    * @access Public
    * @return Integer
*/
function getQtdColunas() { return $this->inQtdColunas;     }

/**
    * @access Public
    * @return Object
*/
function getForm() { return $this->obForm;           }

/**
    * @access Public
    * @return Object
*/
function getJavaScript() { return $this->obJavaScript;     }

/**
    * @access Public
    * @return Array
*/
function getHidden() { return $this->arHidden;         }

/**
    * @access Public
    * @return String
*/
function getArquivoJS() { return $this->stArquivoJS;      }

/**
    * @access Public
    * @return Object
*/
function getIFrame() { return $this->obIFrame;         }
/**
    * @access Public
    * @return Object
*/
function getAjuda() { return $this->stAjuda;         }

/**
    * Método Construtor
    * @access Public
*/
function Formulario()
{
    parent::Tabela();
    $this->setCellPadding   ( 2 );
    $this->setCellSpacing   ( 2 );
    $this->setClassTitulo   ( "alt_dados" );
    $this->setClassRotulo   ( "label" );
    $this->setClassRotuloE  ( "labelleft" );
    $this->setClassRotuloD  ( "label" );
    $this->setClassRotuloC  ( "labelcenter" );
    $this->setClassCampo    ( "field" );
    $this->setClassCampoE   ( "field" );
    $this->setClassCampoD   ( "fieldright" );
    $this->setClassCampoC   ( "fieldcenter" );
    $this->setLarguraRotulo ( 20 );
    $this->setLarguraCampo  ( 80 );
    $this->setQtdColunas    ( 2 );
    $arHidden               = array();
    $this->setHidden        ( $arHidden );
    $obForm                 = new Form;
    $this->addForm          ( $obForm );
    $obJavaScript           = new JavaScript;
    $this->setJavaScript    ( $obJavaScript );
    $this->arJavaScript = array();
    $this->setArquivoJS     ( "" );
}

//METODOS DA CLASSE
/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stTitulo
*/
function addTitulo($stTitulo , $stAlinhamento = '')
{
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass( $this->getClassTitulo() );
    $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() );
    if ( strtolower($stAlinhamento) == 'right') {
        $this->ultimaLinha->ultimaCelula->setClass( 'alt_dados_dir' );
    } elseif ( strtolower($stAlinhamento) == 'center' ) {
         $this->ultimaLinha->ultimaCelula->setClass( 'alt_dados_center' );
    }
    $this->ultimaLinha->ultimaCelula->addConteudo( $stTitulo );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obForm
*/
function addForm($obForm)
{
    $this->setForm( $obForm );
}
/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obHidden
    * @param Boolean $boValida
*/

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponente
    * @param Boolean $boAbreComponente
    * @param Boolean $boFechaComponente
*/
function addComponente($obComponente , $boAbreComponente = true, $boFechaComponente = true)
{
    if ($boAbreComponente) {
        $this->addLinha();
    }
    $stRotuloAnterior = null;
    $boRowSpan = null;

    $stRotulo = $obComponente->getRotulo();
    if ( !$obComponente->getNull() ) {
        $stRotulo = "*".$stRotulo;
    }
    if ( method_exists($obComponente,'getObrigatorioBarra') && $obComponente->getObrigatorioBarra() ) {
        $stRotulo = "**".$stRotulo;
    }
    //Recupera Ultima posição corrente.
    $inPos = count($this->arLinha);
    //Busca o rótulo anterior
    for ($inCount=$inPos; $inCount>=0; $inCount--) {
        $linha = $this->arLinha;
        if ( (is_array($linha) || is_object($linha)) && array_key_exists($inCount, $linha)) {

            if (!$this->arLinha[$inCount] instanceof Div) {
                $celula = $this->arLinha[$inCount]->arCelula;
            } else {
                $celula = null;
            }

            if ( (is_array($celula) || is_object($celula)) && array_key_exists(1,$celula)) {
                if (isset($this->arLinha[$inCount]->arCelula[1]->arComponente[0]->stRotulo)) {
                    $stRotuloAnterior = $this->arLinha[$inCount]->arCelula[1]->arComponente[0]->stRotulo;
                    break;
                }
            }
        }
    }
    //Caso o rótulo anterior for igual ao corrente, é incrementado o rowspan da celula.
    if ($obComponente->getRotulo() == $stRotuloAnterior) {
        for ($inCount=$inPos; $inCount>=0; $inCount--) {
            if ( array_key_exists($inCount, $this->arLinha)) {
                if ( array_key_exists(1,$this->arLinha[$inCount]->arCelula)) {
                    if ($this->arLinha[$inCount]->arCelula[1]->arComponente[0]->stRotulo == $obComponente->getRotulo() ) {
                        if(!$this->arLinha[$inCount]->arCelula[0]->inRowSpan)
                            $this->arLinha[$inCount]->arCelula[0]->inRowSpan = 1;
                        $this->arLinha[$inCount]->arCelula[0]->inRowSpan++;
                        $boRowSpan = true;
                        break;
                    }
                }
            }
        }
    }
    //Adiciona o rótulo quando o anterior for diferente do atual
    if(!$boRowSpan)
        $this->addRotulo( $obComponente->getTitle() , $stRotulo );

    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->addComponente  ( $obComponente );
    if (strtolower(get_class($this)) == "formularioabas" ) {
        $obComponente->setRotulo( $obComponente->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
    }
    $this->obJavaScript->addComponente  ( $obComponente );
    $this->ultimaLinha->commitCelula();
    if ($boFechaComponente) {
        $this->commitLinha();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stArquivoJS
*/
function addArquivoJS($stArquivoJS)
{
    $stScript = $this->getArquivoJS();
    $stScript .= "\n<script src=\"".$stArquivoJS."\" type=\"text/javascript\"></script>\n";
    $this->setArquivoJS( $stScript );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stName
*/
function addIFrameOculto($stName)
{
    $this->obIFrame = new IFrame;
    $this->obIFrame->setName( $stName );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obSpan
*/
function addSpan($obSpan)
{
    $obSpan->montaHTML();
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setColSpan   ( 2 );
    $this->ultimaLinha->ultimaCelula->addConteudo  ( $obSpan->getHTML() );
    $this->ultimaLinha->commitCelula();

    $this->obJavaScript->addComponente  ( $obSpan );

    $this->commitLinha();
}

/**
    * Adiciona um objeto de lista ao formulário.
    * @access Public
    * @param Object $obLista
*/
function addLista($obLista)
{
    $this->addSpan( $obLista );
    $this->obJavaScript->addComponente  ( $obLista );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponente
    * @param Boolean $boAbreComponente
    * @param Boolean $boFechaComponente
*/
function addBusca($obComponente , $boAbreComponente = true, $boFechaComponente = true)
{
    if ($boAbreComponente) {
        $this->addLinha();
    }
    $stRotulo = $obComponente->getRotulo();
    if ( !$obComponente->getNull() ) {
        $stRotulo = "*".$stRotulo;
    }
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassRotulo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    $this->ultimaLinha->ultimaCelula->setTitle       ( $obComponente->getTitle() );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    $this->ultimaLinha->commitCelula();

    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->addComponente  ( $obComponente );
    if ( $obComponente->getCampoCod() ) {
        if (strtolower(get_class($this)) == "formularioabas" ) {
            $obComponente->setRotulo( $obComponente->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
        }
        $this->obJavaScript->addComponente  ( $obComponente->getCampoCod() );
    }
    $this->ultimaLinha->commitCelula();
    if ($boFechaComponente) {
        $this->commitLinha();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponenteCod
    * @param Object $obComponenteSel
    * @param Boolean $boAbreComponente
    * @param Boolean $boFechaComponente
*/
function addComponenteComposto($obComponenteCod , $obComponenteSel ,$boAbreComponente = true, $boFechaComponente = true)
{
    if ($boAbreComponente) {
        $this->addLinha();
    }
    $stRotulo = $obComponenteCod->getRotulo();
    if ( !$obComponenteSel->getNull() ) {
        $stRotulo = "*".$stRotulo;
    }

    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassRotulo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    $this->ultimaLinha->ultimaCelula->setTitle       ( $obComponenteCod->getTitle() );

    if ( method_exists($obComponenteCod,'getObrigatorioBarra') && $obComponenteCod->getObrigatorioBarra()) {
        $stRotulo = "**".$stRotulo;
    }

    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    $this->ultimaLinha->commitCelula();
    $stPreenchido = "document.".$this->obForm->getName().".".$obComponenteSel->getName();
    $stOnChangeCod  = "preencheCampo( this, ".$stPreenchido.", '".Sessao::getId()."');";
    $stOnChangeCod .= $obComponenteCod->obEvento->getOnChange();
    $obComponenteCod->obEvento->setOnChange( $stOnChangeCod );
    $obComponenteCod->montaHTML();
    $stPreenchido = "document.".$this->obForm->getName().".".$obComponenteCod->getName();

    $stOnChangeSel  = "preencheCampo( this, ".$stPreenchido.", '".Sessao::getId()."' );";

    if (!is_null($obComponenteSel->obEvento)) {
        $stOnChangeSel .= $obComponenteSel->obEvento->getOnChange();
        $obComponenteSel->obEvento->setOnChange( $stOnChangeSel );
    }

    $obComponenteSel->montaHTML();
    $stConteudo = $obComponenteCod->getHTML()."&nbsp;".$obComponenteSel->getHTML()."";

    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stConteudo );
    if (strtolower(get_class($this)) == "formularioabas" ) {
        $obComponenteCod->setRotulo( $obComponenteCod->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
    }
    $this->obJavaScript->addComponente  ( $obComponenteCod );
    if (strtolower(get_class($this)) == "formularioabas" ) {
        $obComponenteSel->setRotulo( $obComponenteSel->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
    }
    $this->obJavaScript->addComponente  ( $obComponenteSel );
    $this->ultimaLinha->commitCelula();
    if ($boFechaComponente) {
        $this->commitLinha();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function abreLinha()
{
    $this->addLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function fechaLinha()
{
    $this->commitLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stTitle
    * @param String $stTitle
    * @param Integer $inRowspan
*/
function addRotulo($stTitle, $stRotulo, $inRowspan = "")
{
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassRotulo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    $this->ultimaLinha->ultimaCelula->setTitle       ( $stTitle );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    if ($inRowspan) {
        $this->ultimaLinha->ultimaCelula->setRowSpan ( $inRowspan );
    }
    $this->ultimaLinha->commitCelula();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obProgressBar
*/
function addProgressBar($obProgresBar)
{
    $obProgresBar->montaHTML();
    $this->abreLinha();
    $this->addConteudo( $obProgresBar->getHTML() );
    $this->fechaLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Array $arComponentes
*/
function agrupaComponentes($arComponentes)
{
    $this->abreLinha();
    $inNumComponentes = $inCountComponentes = count( $arComponentes );
    foreach ($arComponentes AS $obComponente) {
        if ($inNumComponentes == $inCountComponentes) {
            $stRotulo = $obComponente->getRotulo();
            if ( !$obComponente->getNull() ) {
                $stRotulo = "*".$stRotulo;
            }
            if ( method_exists($obComponente,'getObrigatorioBarra') && $obComponente->getObrigatorioBarra()) {
                $stRotulo = "**".$stRotulo;
            }
            $this->addRotulo( $obComponente->getTitle() , $stRotulo );
            $this->addCampo( $obComponente, true, false );
            $this->obJavaScript->addComponente  ( $obComponente );
            $inCountComponentes--;
        } elseif ($inCountComponentes > 1) {
            $this->addCampo( $obComponente, false, false );
            $inCountComponentes--;
        } else {
            $this->addCampo( $obComponente, false, true );
            $inCountComponentes--;
        }
    }
    $this->fechaLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obComponente
    * @param Boolean $boAbreCampo
    * @param Boolean $boFechaCampo
*/
function addCampo($obComponente, $boAbreCampo = true, $boFechaCampo = true)
{
    if ($boAbreCampo) {
        $this->ultimaLinha->addCelula();
    }
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->setColSpan     ( $this->getQtdColunas() );
    $this->ultimaLinha->ultimaCelula->addComponente  ( $obComponente );
    if (strtolower(get_class($this)) == "formularioabas" ) {
        $obComponente->setRotulo( $obComponente->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
    }
    $this->obJavaScript->addComponente  ( $obComponente );
    if ($boFechaCampo) {
        $this->ultimaLinha->commitCelula();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stTitle
    * @param Boolean $boAbreCampo
    * @param Boolean $boFechaCampo
*/
function addConteudo($stConteudo, $boAbreCampo = true, $boFechaCampo = true)
{
    if ($boAbreCampo) {
        $this->ultimaLinha->addCelula();
    }
    $this->ultimaLinha->ultimaCelula->setClass      ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth      ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->setColSpan    ( $this->getQtdColunas() );
    $this->ultimaLinha->ultimaCelula->addConteudo   ( $stConteudo );
    if ($boFechaCampo) {
        $this->ultimaLinha->commitCelula();
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obTabela
    * @param Boolean $boCriaDiv
*/
function addBotao($obTabela, $boCriaDiv = true)
{
    if ($boCriaDiv) {
        $this->addDiv(2,"BOTAO");
    }
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass      ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth      ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->setColSpan    ( $this->getQtdColunas() );
    $this->ultimaLinha->ultimaCelula->addTabela     ( $obTabela );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();
    $this->fechaDiv();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obFormulario
*/
function addFormulario(&$obFormulario)
{
    $obFormulario->setForm("");
    $arComponentes = $obFormulario->obJavaScript->getComponente();
    $obFormulario->obJavaScript->JavaScript();
    $obFormulario->obJavaScript->setValida( "" );
    $obFormulario->obJavaScript->setSalvar( "" );
    if ( count( $arComponentes ) ) {
        foreach ($arComponentes as $obComponente) {
            if ( strtolower(get_class($this)) == "formularioabas" ) {
                $obComponente->setRotulo( $obComponente->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
            }
            $this->obJavaScript->addComponente  ( $obComponente );
        }
    }
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() );
    $this->ultimaLinha->ultimaCelula->addTabela( $obFormulario );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obFormulariosAbas
*/
function addFormularioAbas(&$obFormularioAbas)
{
    $obFormularioAbas->setForm("");
    $arComponentes = $obFormularioAbas->obJavaScript->getComponente();
    $obFormularioAbas->obJavaScript->setValida( "" );
    $obFormularioAbas->obJavaScript->setSalvar( "" );
    $obFormularioAbas->obJavaScript->setComponente( array() );
    if ( count( $arComponentes ) ) {
        foreach ($arComponentes as $obComponente) {
            if ( strtolower(get_class($this)) == "formularioabas" and method_exists( $obComponente, 'getRotulo' ) ) {
                $obComponente->setRotulo( $obComponente->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
            }
            $this->obJavaScript->addComponente  ( $obComponente );
        }
    }
    $obFormularioAbas->montaInnerHTML();
    $stHTML  = "\n<script type='text/javascript'>\n".$obFormularioAbas->obJavaScript->geraHabilitaLayer()."\n</script>\n";
    $stHTML .= str_replace( "\\'","'",$obFormularioAbas->getHTML());
    if ( strtolower(get_class($this)) == "formularioabas" ) {
        $stHTML  = str_replace( "layer_", "layer_".count($this->arAbas)."_", $stHTML );
        $stHTML  = str_replace( "celula_", "celula_".count($this->arAbas)."_", $stHTML );
    }
    $stHTML  = str_replace( "HabilitaLayer", "HabilitaLayer".count($this->arAbas), $stHTML );
    $stHTML  = str_replace( "document.getElementById('BOTAO').style.display = 'none';", "", $stHTML );
    $stHTML  = str_replace( "document.getElementById('BOTAO').style.display = 'block';", "", $stHTML );
    $this->addLinha();
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setColSpan( $this->getQtdColunas() );
    $this->ultimaLinha->ultimaCelula->addConteudo( $stHTML );
    $this->ultimaLinha->commitCelula();
    $this->commitLinha();
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Array $arBotoes
    * @param String $stAlinhamento
    * @param Strubg $stMensagem
*/
function defineBarra( $arBotoes = array(), $stAlinhamento = "left", $stMensagem = "<b>*Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;" )
{
    $obFrmOk = new Tabela;
    $obFrmOk->addLinha();
    $obFrmOk->ultimaLinha->addCelula();
    $obFrmOk->ultimaLinha->ultimaCelula->setAlign( $stAlinhamento );
    $obFrmOk->ultimaLinha->ultimaCelula->setNoWrap( true );
    foreach ($arBotoes as $obBotao) {
        $obFrmOk->ultimaLinha->ultimaCelula->addComponente( $obBotao );
    }
    $obFrmOk->ultimaLinha->commitCelula();
    if ($stMensagem) {
        $obFrmOk->ultimaLinha->addCelula();
        $obFrmOk->ultimaLinha->ultimaCelula->setClass( "fieldright_noborder" );
        $obFrmOk->ultimaLinha->ultimaCelula->addConteudo( $stMensagem );
        $obFrmOk->ultimaLinha->commitCelula();
    }
    $obFrmOk->commitLinha();
    $this->addBotao( $obFrmOk );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Array $arBotoes
    * @param String $stAlinhamento
    * @param String $stMensagem
*/
function defineBarraAba( $arBotoes = array(), $stAlinhamento = "left", $stMensagem = "<b>* Campo obrigatório</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  " )
{
    $obFrmOk = new Tabela;
    $obFrmOk->addLinha();
    $obFrmOk->ultimaLinha->addCelula();
    $obFrmOk->ultimaLinha->ultimaCelula->setAlign( $stAlinhamento );
    $obFrmOk->ultimaLinha->ultimaCelula->setNoWrap( true );
    foreach ($arBotoes as $obBotao) {
        $obFrmOk->ultimaLinha->ultimaCelula->addComponente( $obBotao );
    }
    $obFrmOk->ultimaLinha->commitCelula();
    if ($stMensagem) {
        $obFrmOk->ultimaLinha->addCelula();
        $obFrmOk->ultimaLinha->ultimaCelula->setClass( "fieldright_noborder" );
        $obFrmOk->ultimaLinha->ultimaCelula->addConteudo( $stMensagem );
        $obFrmOk->ultimaLinha->commitCelula();
    }
    $obFrmOk->commitLinha();
    $this->addBotao( $obFrmOk , false );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Boolean $boBlock
*/
function Ok($boBlock=false)
{
    $obOk  = new Ok($boBlock);
    $obLimpar  = new Limpar;
    $this->defineBarra( array( $obOk, $obLimpar ) );
}

/**
    * Função que adiciona automaticamente a barra de incluir e limpar
    * @access Public
    * @param String  $stName         Nome do botão e das funções da barra
    * @param Array   $arComponentes  Array de componentes válidos (extensão da classe Componente)
    * @param Boolean $boLimpar       Adiciona a função de limpar depois de pressionar o botão incluir
    * @param Boolean $boAbas         Informa se o formulário utiliza abas
    * @param String  $idFields       Informa os campos específicos para serem recuperados via GET.
*/
function Incluir($stName, $arComponentes , $boLimpar = false, $boAbas = false, $idFields = '', $boShowLimpar = true)
{
    $this->geraBarraBotoes( 'incluir', $stName, $arComponentes, $boLimpar, $boAbas, $idFields, $boShowLimpar);
}
/**
    * Função que adiciona automaticamente a barra de incluir, alterar e limpar
    * @access Public
    * @param String  $stName         Nome do botão e das funções da barra
    * @param Array   $arComponentes  Array de componentes válidos (extensão da classe Componente)
    * @param Boolean $boLimpar       Adiciona a função de limpar depois de pressionar o botão incluir/alterar
    * @param Boolean $boAbas         Informa se o formulário utiliza abas
    * @param String  $idFields       Informa os campos específicos para serem recuperados via GET.
*/
function IncluirAlterar($stName, $arComponentes , $boLimpar = false, $boAbas = false, $idFields = '', $metodoAjax='get')
{
    $this->geraBarraBotoes( 'incluiralterar', $stName, $arComponentes, $boLimpar, $boAbas, $idFields, true, $metodoAjax);
}

function geraBarraBotoes($stBotoes, $stName, $arComponentes, $boLimpar, $boAbas = false , $idFields = '', $boShowLimpar = true, $metodoAjax='get')
{
//  $this->arJavaScript = array();
//  $arBarra            = array();
    $obJavaScript       = new JavaScript($stName);
    $boObrigatorioBarra = false;
    foreach ($arComponentes as $obComponente) {
        if (method_exists($obComponente,'getObrigatorioBarra') && $obComponente->getObrigatorioBarra()) {
           $boObrigatorioBarra = true;
        }
        $obJavaScript->addComponente( $obComponente );
        foreach ($this->obJavaScript->arComponente as $obComponenteJS) {
            $stIdent = null;
            if ($obComponenteJS->stId) {
                if ($obComponenteJS->stId == $obComponente->stId) {
                    $stIdent = 'Id';
                }
            } elseif ($obComponenteJS->stName) {
                if ($obComponenteJS->stName == $obComponente->stName) {
                    $stIdent = 'Name';
                }
            }
            if ($stIdent == null) {
                $arComponentesNew[] = $obComponenteJS;
            }
        }
    }
    //Array de componentes sem os da barra de incluir
    $this->obJavaScript->arComponente = $arComponentesNew;

    $obJavaScript->montaJavaScript();
    //Array dos componentes da barra de incluir
    $this->arJavaScript[] = $obJavaScript;

    if( $boLimpar )
        $stComplementoJS = "limpaFormulario$stName();";

    $obBtnIncluir = new Button;
    $obBtnIncluir->setName              ( "btIncluir$stName"    );
    $obBtnIncluir->setId                ( "btIncluir$stName"    );
    $obBtnIncluir->setValue             ( "Incluir"             );

    $stComplementoJS = isset($stComplementoJS) ? $stComplementoJS : "";

    if ($metodoAjax == 'get') {
        $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { montaParametrosGET( 'incluir$stName', '$idFields', true  ); $stComplementoJS }" );
    } else {
        //Se $metodoAjax for POST
        $obBtnIncluir->obEvento->setOnClick ( "if ( Valida$stName() ) { montaParametrosPOST( 'incluir$stName', '$idFields', true  ); $stComplementoJS }" );
    }
    $arBarra[] = $obBtnIncluir;

    if ($stBotoes == 'incluiralterar') {
        $obBtnAlterar = new Button;
        $obBtnAlterar->setName              ( "btAlterar$stName"    );
        $obBtnAlterar->setId                ( "btAlterar$stName"    );
        $obBtnAlterar->setValue             ( "Alterar"             );
        $obBtnAlterar->setDisabled          ( true                  );
        if ($metodoAjax == 'get') {
            $obBtnAlterar->obEvento->setOnClick ( "if ( Valida$stName() ) { montaParametrosGET( 'alterar$stName', '$idFields', true  ); $stComplementoJS }" );
        } else {
            $obBtnAlterar->obEvento->setOnClick ( "if ( Valida$stName() ) { montaParametrosPOST( 'alterar$stName', '$idFields', true  ); $stComplementoJS }" );
        }
        $arBarra[] = $obBtnAlterar;
    }

    if ($boShowLimpar == true) {
        $obBtnLimpar = new Button;
        $obBtnLimpar->setName              ( "btLimpar$stName"          );
        $obBtnLimpar->setId                ( "btLimpar$stName"          );
        $obBtnLimpar->setValue             ( "Limpar"                   );
        $obBtnLimpar->obEvento->setOnClick ( "limpaFormulario$stName();");
        $arBarra[] = $obBtnLimpar;
    }

    $boAbas ? $stDefine = "defineBarraAba" : $stDefine = "defineBarra";

    if ($boObrigatorioBarra)
        $this->$stDefine( $arBarra , "left", "<b>**Campo obrigatório.</b>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;");
    else
        $this->$stDefine($arBarra);

}

function getInnerJavaScript()
{
    $this->montaInnerHTML();
    $this->obJavaScript->montaJavaScript();

    $stEval  = $this->obJavaScript->getInnerJavaScript();

    return $stEval;
}

function getInnerJavascriptBarra()
{
    $stJs = "obScript = document.createElement(\"script\");";
    for ($inCount=0; $inCount<count($this->arJavaScript); $inCount++) {

        $stEval  = $this->arJavaScript[$inCount]->getValida();
        $stEval .= $this->arJavaScript[$inCount]->getLimpar();
        $stEval  = str_replace("\n","",$stEval);
        $stEval  = str_replace("  ","",$stEval);
        $stEval  = str_replace("'","\\'",$stEval);
        $stEval  = str_replace(chr(13),"",$stEval);
        $stEval  = str_replace(chr(13).chr(10),"",$stEval);

        $stJs .= "obScript.text = '".$stEval."';";
    }
    $stJs .= "document.body.appendChild(obScript);";

    return $stJs;
}

/**
    * FALTA DESCRICAO
    * @access Public
*/
function Voltar()
{
    $obVoltar  = new Voltar;
    $obFrmOk   = new Tabela;
    $this->defineBarra( array( $obVoltar, $obFrmOk ) );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $stLocation
*/
function Cancelar($stLocation="", $boBlock=false)
{
    $obOk  = new Ok($boBlock);
    $obCancelar  = new Cancelar;
    if ($stLocation != "") {
        $obCancelar->obEvento->setOnClick("Cancelar('".$stLocation."','telaPrincipal');");
    }
    $this->defineBarra( array( $obOk, $obCancelar ) );
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param Object $obHidden
    * @param Boolean $boValida
*/
function addHidden($obHidden , $boValida = false)
{
    $arHidden = $this->getHidden();
    $arHidden[] = $obHidden;
    $this->setHidden( $arHidden );
    if ($boValida) {
        if ( strtolower(get_class($this)) == "formularioabas" ) {
            $obHidden->setRotulo( $obHidden->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
        }
        $this->obJavaScript->addComponente( $obHidden );
    }
}

/**
    * FALTA DESCRICAO
    * @access Public
    * @param String $obDataInicial
    * @param String $obDataFinal
    * @param Boolean $boAbrePeriodo
    * @param Boolean $boFechaPeriodo
*/
function periodo($obDataInicial = "", $obDataFinal = "", $boAbrePeriodo = true, $boFechaPeriodo = true)
{
    //CRIA OS COMPONENTES CASO O USUARIO NAUM TENHA SETADO  OS MESMOS
    if ( empty( $obDataInicial ) ) {
        $obDataInicial = new Data;
        $obDataInicial->setName("dataInicial");
        $obDataInicial->setValue( date( "d/m/Y" ) );
    }
    if ( empty( $obDataFinal ) ) {
        $obDataFinal = new Data;
        $obDataFinal->setName("dataFinal");
        $obDataFinal->setValue( date( "d/m/Y" ) );
    }
    $obDataInicial->montaHTML();
    $obDataFinal->montaHTML();

    if ($boAbrePeriodo) {
        $this->addLinha();
    }
    $stRotulo = $obDataInicial->getRotulo();
    if ( empty( $stRotulo ) ) {
        $stRotulo = " Período";
    }
    if ( !$obDataInicial->getNull() ) {
        $stRotulo = "*".$stRotulo;
    }
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassRotulo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraRotulo() );
    $this->ultimaLinha->ultimaCelula->setTitle       ( $obDataInicial->getTitle() );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stRotulo );
    $this->ultimaLinha->commitCelula();

    $stConteudo = $obDataInicial->GetHTML()."&nbsp;a&nbsp;".$obDataFinal->GetHTML()."\n";
    $this->ultimaLinha->addCelula();
    $this->ultimaLinha->ultimaCelula->setClass       ( $this->getClassCampo() );
    $this->ultimaLinha->ultimaCelula->setWidth       ( $this->getLarguraCampo() );
    $this->ultimaLinha->ultimaCelula->addConteudo    ( $stConteudo );
    if ( strtolower(get_class($this)) == "formularioabas" ) {
        $obDataInicial->setRotulo( $obDataInicial->getRotulo()." da guia ".$this->arAbas[ count($this->arAbas) - 1 ]->stLabel );
    }
    $this->obJavaScript->addComponente  ( $obDataInicial );
    $this->ultimaLinha->commitCelula();
    if ($boFechaPeriodo) {
        $this->commitLinha();
    }
}

/**
    * Monta o HTML do Objeto Formulario
    * @access Protected
*/
function montaHTML()
{
    include_once 'Ajuda.class.php';

    $stHtml = "";

    if ( $this->getForm() ) {
        $obForm = $this->getForm();
        $stHtml = "\n".$obForm->abreForm();
    }

    $stHtml .= "<div id=\"layerFormulario\">\r\n";
    $this->obJavaScript->montaJavaScript();
    $stHtml .= $this->getArquivoJS();
    $stHtml .= $this->obJavaScript->getJavaScript();
    $inCountJsArray =  is_array($this->arJavaScript) ? count($this->arJavaScript) : 0;
    for ($inCount=0; $inCount<$inCountJsArray; $inCount++) {
        $stHtml .= "<script type=\"text/javascript\">\n";
        $stHtml .= $this->arJavaScript[$inCount]->getValida();
        $stHtml .= $this->arJavaScript[$inCount]->getLimpar();
        $stHtml .= "</script>\n";
    }
    $stCaso = substr( $this->getAjuda(),3 );
    $arCasoUso = explode(".",$stCaso);
    if ($this->getAjuda()) {
        $obAjuda = new Ajuda;
        $obAjuda->setCodGestao($arCasoUso[0]);
        $obAjuda->setCodModulo(Sessao::getModulo());
        $obAjuda->setCasoUso($this->getAjuda());
        $this->addHidden($obAjuda);
    }
    $arHidden = $this->getHidden();
    if ( count( $arHidden ) ) {
        foreach ($arHidden as $obHidden) {
            $obHidden->montaHTML();
            $stHtml .= $obHidden->getHTML()."\n";
        }
    }
    parent::montaHTML();
    $stHtml .= parent::getHTML();
    if ( is_object($this->getIFrame()) ) {
        $this->obIFrame->montaHtml();
        $stHtml .= $this->obIFrame->getHtml();
    }
    $stHtml .= "\r\n</div>";
    if ( $this->getForm() ) {
        $stHtml .= $obForm->fechaForm();
    }
    $stHtml .= "\n<script type='text/javascript'>\nexecuta();\n</script>\n";
    $stHtml .= "\n<script type='text/javascript'>\n".$this->obJavaScript->getMonitoraBuscaINNER()."\n</script>\n";
    parent::setHTML( $stHtml );
}

/**
    * Monta o HTML do Objeto Arvore
    * @access Private
*/
function montaInnerHTML()
{
    $stHtml = "";
    $arHidden = $this->getHidden();

    if ( count( $arHidden ) ) {
        foreach ($arHidden as $obHidden) {
            $obHidden->montaHTML();
            $stHtml .= $obHidden->getHTML()."\n";
        }
    }

    parent::montaHTML();
    $stHtml .= parent::getHTML();
    if ( is_object($this->getIFrame()) ) {
        $this->obIFrame->montaHtml();
        $stHtml .= $this->obIFrame->getHtml();
    }

    $stHtml = str_replace("\n","",$stHtml           );
    $stHtml = str_replace("  ","",$stHtml           );
    $stHtml = str_replace("'","\\'",$stHtml         );
    $stHtml = str_replace(chr(13),"",$stHtml        );
    $stHtml = str_replace(chr(13).chr(10),"",$stHtml);
    parent::setHTML( $stHtml );
}
/*
* Adicionado por Lucas Stephanou(domluc) en 16/02/2005
*/
/**
    * FALTA DESCRICAO
    * @param String $idComp
*/
function setFormFocus($idComp)
{
    $this->obForm->setFoco($idComp);
}

/**
    * Imprime o HTML do Objeto Formulario na tela (echo)
    * @access Public
*/
function show()
{
    $this->montaHTML();
    $stHTML = parent::getHTML();

    echo $stHTML;
}

}

?>

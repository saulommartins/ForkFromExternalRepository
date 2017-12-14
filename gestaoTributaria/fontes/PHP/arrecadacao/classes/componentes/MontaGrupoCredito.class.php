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
* Componente para montar grupo de credito
* Data de Criação: 14/08/2006

* @author Analista: Fabio Bertoldi
* @author Desenvolvedor: Fernando Piccini Cercato

* @package framework
* @subpackage componentes

    * $Id: MontaGrupoCredito.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.02
*/

/*
$Log$
Revision 1.7  2007/03/07 20:57:38  cassiano
Bug #8441#

Revision 1.6  2007/03/05 20:19:08  cassiano
Bug #8441#

Revision 1.5  2007/01/31 18:28:16  dibueno
inclusao do método setMonitorarCampoCod

Revision 1.4  2006/09/27 09:43:55  dibueno
Modificações necessárias para busca do grupo de credito

Revision 1.3  2006/09/26 17:16:18  dibueno
Adicionado alteração do titulo do componente

Revision 1.2  2006/09/15 10:26:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once( CAM_GT_ARR_NEGOCIO."RARRGrupo.class.php" );

class MontaGrupoCredito extends Objeto
{
/**
    * @access Private
    * @var String
*/
var $stRotulo;
/**
    * @access Private
    * @var String
*/
var $stTitulo;
/**
    * @access Private
    * @var String
*/
var $stTipo;

/**
    * @access Private
    * @var String
*/
var $stMascara;

/**
    * @access Private
    * @var Object
*/
var $obRARRGrupo;

/**
    * @access Private
    * @var Object
*/
var $obBscCodigoCredito;

/**
    * @access Public
    * @param String $valor
*/
function setRotulo($valor) { $this->stRotulo = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setTitulo($valor) { $this->stTitulo = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setTipo($valor) { $this->stTipo = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara = $valor; }

/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara; }

/**
    * @access Public
    * @return String
*/
function getRotulo() { return $this->stRotulo; }

/**
    * @access Public
    * @return String
*/
function getTitulo() { return $this->stTitulo; }

/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo; }

/**
     * Método construtor
     * @access Private
*/
function MontaGrupoCredito()
{
    $this->obRARRGrupo = new RARRGrupo;
    $this->stMascara = "";
    $this->stRotulo = "Grupo de Créditos";
    $this->stTitulo = "Grupo de créditos.";

    $this->obBscCodigoCredito = new BuscaInner;
    $this->obBscCodigoCredito->setId( "stGrupo" );
    $this->obBscCodigoCredito->obCampoCod->setName("inCodGrupo");
    $this->obBscCodigoCredito->obCampoCod->setValue( '' );
    $this->obBscCodigoCredito->obCampoCod->setId   ("inCodGrupo");
    $this->obBscCodigoCredito->setMonitorarCampoCod( true );
}

/**
    * @access Public
    * @param  Object $obFormulario Objeto formulario
*/
function geraFormulario(&$obFormulario, $boInner = true, $boNull = false)
{
    if ($boInner) {
        if ($this->stMascara == "") { //se estava vazio preenxer
            $this->obRARRGrupo->RecuperaMascaraGrupoCredito( $this->stMascara );
            $this->stMascara .= "/9999";
        }

        $stComponente = "document.".$obFormulario->obForm->getName().".inCodGrupo.value";
        if ( !$this->getTipo() ) {
            $pgOcul = "'".CAM_GT_ARR_INSTANCIAS."grupoCreditos/OCManterGrupo.php?" .Sessao::getId()."&inCodGrupo='+$stComponente+'&x=1'";
            $this->obBscCodigoCredito->obCampoCod->obEvento->setOnChange ( "ajaxJavaScript(".$pgOcul.",'BuscaGrupoCredito' );" );
        } else {

            $this->obBscCodigoCredito->obCampoCod->obEvento->setOnChange("buscaValor('BuscaCodCredito');");
        }

        $this->obBscCodigoCredito->setFuncaoBusca( "abrePopUp('".CAM_GT_ARR_POPUPS."grupoCreditos/FLProcurarGrupo.php','frm','inCodGrupo','stGrupo','geral','".Sessao::getId()."','800','550');" );
        $this->obBscCodigoCredito->obCampoCod->setMascara( $this->stMascara );
        $this->obBscCodigoCredito->setNull ( $boNull );
        $this->obBscCodigoCredito->setRotulo( $this->stRotulo );
        $this->obBscCodigoCredito->setTitle( $this->stTitulo );

        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addComponente ( $this->obBscCodigoCredito );
    } else {
        $obLblGrupo = new Label;
        $this->setTitulo ( "Grupo de créditos definido." );
        $obLblGrupo->setTitle ( $this->stTitulo );
        $obLblGrupo->setName  ( 'stGrupo' );
        $obLblGrupo->setValue ( $this->obRARRGrupo->getCodGrupo()." - ".$this->obRARRGrupo->getDescricao()." / ".$this->obRARRGrupo->getExercicio() );
        $obLblGrupo->setRotulo( 'Grupo'   );

        //ADICIONA OS COMPONENTES NO FORMULARIO
        $obFormulario->addComponente ( $obLblGrupo );
    }
}

} // end of class
?>

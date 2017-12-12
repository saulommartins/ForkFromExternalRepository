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
* Classe de negócio Modulo
* Data de Criação: 25/07/2005

* @author Analista: Cassiano
* @author Desenvolvedor: Cassiano

$Revision: 3476 $
$Name$
$Author: pablo $
$Date: 2005-12-06 13:51:37 -0200 (Ter, 06 Dez 2005) $

Casos de uso: uc-01.03.91
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GA_ADM_NEGOCIO."RCadastro.class.php"            );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoModulo.class.php"         );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoCadastro.class.php" );
/**
    * Classe de Regra de Negócio Modulo
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class RModulo
{
/**
    * @var Object
    * @access Private
*/
var $obTModulo;
/**
    * @var Object
    * @access Private
*/
var $obTAdministracaoCadastro;
/**
    * @var Integer
    * @access Private
*/
var $inCodModulo;
/**
    * @var Integer
    * @access Private
*/
var $inCodResponsavel;
/**
    * @var Integer
    * @access Private
*/
var $inOrdem;
/**
    * @var String
    * @access Private
*/
var $stNomModulo;
/**
    * @var String
    * @access Private
*/
var $stNomDiretorio;
/**
    * @var Array
    * @access Private
*/
var $arRCadastro;
/**
    * @var Object
    * @access Private
*/
var $roRCadastro;

/**
    * @access Public
    * @param Object $valor
*/
function setTModulo($valor) { $this->obTModulo        = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodModulo($valor) { $this->inCodModulo      = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setTAdministracaoCadastro($valor) { $this->obTAdministracaoCadastro = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodResponsavel($valor) { $this->inCodResponsavel = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodOrdem($valor) { $this->inCodOrdem       = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomModulo($valor) { $this->stNomModulo      = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomDiretorio($valor) { $this->stNomDiretorio   = $valor; }
/**
    * @access Public
    * @param Array $valor
*/
function setRCadastro($valor) { $this->arRCadastro      = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTModulo() { return $this->obTModulo        ; }
/**
    * @access Public
    * @return Integer
*/
function getCodModulo() { return $this->inCodModulo      ; }
/**
    * @access Public
    * @return Integer
*/
function getCodResponsavel() { return $this->inCodResponsavel ; }
/**
    * @access Public
    * @return Integer
*/
function getCodOrdem() { return $this->inCodOrdem       ; }
/**
    * @access Public
    * @return String
*/
function getNomModulo() { return $this->stNomModulo      ; }
/**
    * @access Public
    * @return String
*/
function getNomDiretorio() { return $this->stNomDiretorio   ; }

/**
    * Método Construtor
    * @access Private
*/
function RModulo()
{
    $this->setTModulo   ( new TModulo  );
    $this->setTAdministracaoCadastro( new TAdministracaoCadastro );
    $this->setRCadastro ( array() );
}
/**
    * Executa um recuperaTodos na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "nom_modulo", $boTransacao = "")
{
    $obErro = $this->obTModulo->recuperaTodos( $rsLista, '', $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Retorda todos os módulos do usuario responsável setado
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarModulosPorResponsavel(&$rsLista, $boTransacao = "")
{
    if ( $this->getCodResponsavel() == "" ) {

        $this->setCodResponsavel( Sessao::read('numCgm') );
    }
    $stFiltro = " WHERE cod_responsavel = ".$this->getCodResponsavel();
    $stOrdem = " ORDER BY nom_modulo ";
    $obErro = $this->obTModulo->recuperaTodos( $rsLista, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    $this->obTModulo->setDado( "cod_modulo" , $this->getCodModulo () );
    $obErro = $this->obTModulo->recuperaPorChave( $rsLista, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setCodResponsavel( $rsLista->getCampo('cod_responsavel') );
        $this->setCodOrdem      ( $rsLista->getCampo('cod_ordem') );
        $this->setNomModulo     ( $rsLista->getCampo('nom_modulo') );
        $this->setNomDiretorio  ( $rsLista->getCampo('nom_diretorio') );
    }

    return $obErro;
}
/**
    * Incrementa o Array de cadastros
    * @access Public
    * @return Void
*/
function addCadastro()
{
    $this->arRCadastro[] = new RCadastro( $this );
    $this->roRCadastro = &$this->arRCadastro[count($this->arRCadastro) - 1 ];
}

}

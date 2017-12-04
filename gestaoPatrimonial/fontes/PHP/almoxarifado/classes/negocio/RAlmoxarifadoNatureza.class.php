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
    * Classe de Regra de Negócio Marca
    * Data de Criação   : 01/11/2005

    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Regra

    * Casos de uso: uc-03.03.03
*/

/*
$Log$
Revision 1.3  2006/07/06 14:04:47  diego
Retirada tag de log com erro.

Revision 1.2  2006/07/06 12:09:32  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GP_ALM_MAPEAMENTO. "TAlmoxarifadoNatureza.class.php");

/**
    * Classe de Regra de Marca
    * @author Analista:
    * @author Desenvolvedor: Leandro André Zis
*/
class RAlmoxarifadoNatureza
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigo;

/**
    * @access Private
    * @var String
*/
var $stTipo;

/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
    * @access Public
    * @param Integer $Valor
*/
function setCodigo($valor) { $this->inCodigo = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setTipo($valor) { $this->stTipo = $valor; }

/**
    * @access Public
    * @param String $Valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodigo() { return $this->inCodigo; }

/**
    * @access Public
    * @return String
*/
function getTipo() { return $this->stTipo; }

/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao; }

/**
    * Metodo Construtor
    * @access Public
*/
function RAlmoxarifadoNatureza()
{
    $this->obTransacao  =  new Transacao;
}
/**
    * @access Public
    * @param RecordSet $obRecordSet
    * @param String $stOrdem
    * @param Boolean $boTransacao
    * @return Erro
*/
function listar(&$rsRecordSet, $stOrdem="", $boTransacao = "")
{
   $stFiltro = "";
   if ($this->getDescricao()) {
       $stFiltro .= " WHERE descricao like '". $this->getDescricao() ."'";
   }
   $obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza();
   $obErro = $obTAlmoxarifadoNatureza->recuperaTodos($rsRecordSet, $stFiltro, $stOrdem, $boTransacao);

   return $obErro;
}

function consultar($boTransacao = "")
{
    $obTAlmoxarifadoNatureza = new TAlmoxarifadoNatureza();
    $obTAlmoxarifadoNatureza->setDado( "cod_natureza" , $this->getCodigo() );
    $obTAlmoxarifadoNatureza->setDado( "tipo_natureza", $this->getTipo()   );
    $obErro = $obTAlmoxarifadoNatureza->recuperaPorChave( $rsRecordSet, $boTransacao );
    if (!$obErro) {
       $this->setDescricao( $rsRecordSet->getCampo("descricao") );
    }

    return $obErro;
}

}

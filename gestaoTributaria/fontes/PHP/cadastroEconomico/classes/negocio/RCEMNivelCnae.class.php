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
    * Classe de regra de negócio para Hierarquia de Serviços
    * Data de Criação: 19/11/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Tonismar Régis Bernardo

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMNivelCnae.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.02
*/

/*
$Log$
Revision 1.4  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelCnae.class.php"     );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMNivelCnaeValor.class.php");
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMVigenciaCnae.class.php"  );

class RCEMNivelCnae
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoNivel;
/**
    * @access Private
    * @var Integer
*/
var $inCodigoVigencia;
/**
    * @access Private
    * @var String
*/
var $stNomeNivel;
/**
    * @access Private
    * @var String
*/
var $dtInicioVigencia;

/**
    * @access Public
    * @param String $valor
*/
function setCodigoNivel($valor) { $this->inCodigoNivel     = $valor;          }
/**
    * @access Public
    * @param String $valor
*/
function setCodigoVigencia($valor) { $this->inCodigoVigencia  = $valor;          }
/**
    * @access Public
    * @param String $valor
*/
function setNomeNivel($valor) { $this->stNomeNivel  = $valor;               }
/**
    * @access Public
    * @param String $valor
*/
function setInicioVigencia($valor) { $this->dtInicioVigencia  = $valor;          }

/**
    * @access Public
    * @return Integer
*/
function getCodigoNivel() { return $this->inCodigoNivel;            }
/**
    * @access Public
    * @return Integer
*/
function getCodigoVigencia() { return $this->inCodigoVigencia;         }
/**
    * @access Public
    * @return String
*/
function getNomeNivel() { return $this->stNomeNivel;              }
/**
    * @access Public
    * @return String
*/
function getInicioVigencia() { return $this->dtInicioVigencia;         }

/**
     * Método construtor
     * @access Private
*/
function RCEMNivelCnae()
{
    $this->obTCEMNivelCnae           = new TCEMNivelCnae;
    $this->obTCEMNivelCnaeValor    = new TCEMNivelCnaeValor;
    $this->obTCEMVigenciaCnae      = new TCEMVigenciaCnae;
    $this->obTransacao              = new Transacao;
}

/**
    * Recupera do banco de dados os dados do Nivel Cnae selecionado
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarNivel($boTransacao = "")
{
    $this->obTCEMNivelCnae->setDado( "cod_nivel"   , $this->inCodigoNivel    );
    $this->obTCEMNivelCnae->setDado( "cod_vigencia", $this->inCodigoVigencia );
    $obErro = $this->obTCEMNivelCnae->recuperaPorChave( $rsNivel, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->stNomeNivel = $rsNivel->getCampo( "nom_nivel" );
        $this->stMascara   = $rsNivel->getCampo( "mascara"   );
    }

    return $obErro;
}

/**
    * Lista os Niveis Cnae segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveis(&$rsNivel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNivel) {
        $stFiltro .= " COD_NIVEL = ".$this->inCodigoNivel." AND";
    }
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
    $obErro = $this->obTCEMNivelCnae->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista os Niveis Cnae com Data de Vigência segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveisVigencia(&$rsNivel, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoNivel) {
        $stFiltro .= " AND NA.COD_NIVEL = ".$this->inCodigoNivel;
    }
    if ($this->inCodigoVigencia) {
        $stFiltro .= " AND VA.COD_VIGENCIA = ". $this->inCodigoVigencia;
    }

    if ($this->stNomeNivel) {
        $stFiltro .= " AND UPPER( NA.NOM_NIVEL ) LIKE UPPER('".$this->stNomeNivel."%')";
    }

    $stOrdem = " ORDER BY NA.COD_NIVEL ";
    $obErro = $this->obTCEMNivelCnae->recuperaNiveisVigencia( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Lista as Vigências Cnae segundo o filtro setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVigencia(&$rsVigencia, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " COD_VIGENCIA = ".$this->inCodigoVigencia." AND";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY COD_VIGENCIA ";
    $obErro = $this->obTCEMVigenciaCnae->recuperaTodos( $rsVigencia, $stFiltro, $stOrdem, $boTransacao );
    //$this->obTCEMVigenciaCnae->debug();
    return $obErro;
}

/**
    * Lista os Niveis Cnae anteriores segundo o nivel setado
    * @access Public
    * @param  Object $rsNivel Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNiveisAnteriores(&$rsNivel, $boTransacao = "")
{
   $stFiltro = "";
   if ($this->inCodigoNivel) {
       $stFiltro .= " COD_NIVEL < ".$this->inCodigoNivel." AND";
   }
   if ($this->inCodigoVigencia) {
       $stFiltro .= " COD_VIGENCIA = ". $this->inCodigoVigencia." AND";
   }
   if ($stFiltro) {
       $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
   }
   $stOrdem = " ORDER BY COD_VIGENCIA, COD_NIVEL ";
   $obErro = $this->obTCEMNivelCnae->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );

   return $obErro;
}

function validaNomeNivel($boTransacao = "")
{
    $stFiltro = " WHERE  NOM_NIVEL = '".$this->stNomeNivel."' ";
    $stFiltro .= " AND COD_VIGENCIA = ".$this->inCodigoVigencia;
    if ($this->inCodigoNivel AND $this->inCodigoVigencia) {
        $stFiltro .= " AND COD_NIVEL <> ".$this->inCodigoNivel;
    }
    $stOrdem = "";
    $obErro = $this->obTCEMNivelCnae->recuperaTodos( $rsNivel, $stFiltro, $stOrdem, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsNivel->eof() ) {
        $obErro->setDescricao( "Já existe outro nível de atividade cadastrado com o nome ".$this->stNomeNivel."!" );
    }

    return $obErro;
}

/**
    * Recupera o a vigencia atual
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaVigenciaAtual(&$rsRecordSet, $boTransacao = "")
{
    $obErro = $this->obTCEMVigenciaCnae->recuperaVigenciaAtual( $rsRecordSet,  $boTransacao );

    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia setada
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel < ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " DESC ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCEMNivelCnae->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Recupera o ultimo nivel da vigencia corrente
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaUltimoNivelAtual(&$rsRecordSet , $boTransacao = "")
{
    $rsRecordSet = new RecordSet;
    $obErro = $this->recuperaVigenciaAtual( $rsVigencia, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsVigencia->eof() ) {
        $this->inCodigoVigencia = $rsVigencia->getCampo( "cod_vigencia" );
        $obErro = $this->recuperaUltimoNivel( $rsRecordSet, $boTransacao );
    }

    return $obErro;
}

/**
    * Recupera o proximo nivel da vigencia em relacao ao nivel
    * @access Public
    * @param  Object $rsRecordSet Objeto RecrdSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaProximoNivel(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoVigencia) {
        $stFiltro .= " cod_vigencia = ".$this->inCodigoVigencia." AND ";
    }
    if ($this->inCodigoNivel) {
        $stFiltro .= " cod_nivel > ".$this->inCodigoNivel." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem  = " ORDER BY ";
    $stOrdem .= "     cod_nivel ";
    $stOrdem .= " LIMIT 1 ";
    $obErro = $this->obTCEMNivelCnae->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

}

?>

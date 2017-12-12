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
    * Classe de regra de negócio para Pessoal-SubDivisao
    * Data de Criação: 13/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.06
                 uc-04.05.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"        );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargo.class.php"             );
include_once ( CAM_GRH_PES_NEGOCIO   ."RPessoalCargo.class.php"             );

/**
    * Classe de regra de negócio para Pessoal-SubDivisao
    * Data de Criação: 13/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalSubDivisao
{
/**
    * @access Private
    * @var object
*/
var $roPessoalRegime;
/**
    * @access Private
    * @var object
*/
var $arRPessoalCargo;
/**
     * @access Private
     * @var object
*/
var $roUltimoCargo;
/**
    * @access Private
    * @var integer
*/
var $inCodSubDivisao;
/**
    * @access Private
    * @var String
*/
var $stDescricao;

/**
     * @access Public
     * @param Object $valor
 */
function setArRPessoalCargo($valor) { $this->arRPessoalCargo = $valor; }
/**
     * @access Public
     * @param Object $valor
 */
function setRoUltimoCargo($valor) { $this->roUltimoCargo = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodSubDivisao($valor) { $this->inCodSubDivisao = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao = $valor; }

/**
    * @access Public
    * @return Object
*/
function getArRPessoalCargo() { return $this->arRPessoalCargo; }
/**
    * @access Public
    * @return Object
*/
function getRoUltimoCargo() { return $this->roUltimoCargo; }
/**
    * @access Public
    * @return Integer
*/
function getCodSubDivisao() { return $this->inCodSubDivisao; }
/**
    * @access Public
    * @return String
*/
function getDescricao() { return $this->stDescricao;   }

function RPessoalSubDivisao(&$roRPessoalRegime)
{
    $this->obTPessoalSubDivisao     = new TPessoalSubDivisao;
    $this->obTransacao              = new Transacao;
    $this->roPessoalRegime          = &$roRPessoalRegime;
    $this->obTPessoalCargo          = new TPessoalCargo;
    $this->setArRPessoalCargo ( array() );
}

function addCargo()
{
    $this->arRPessoalCargo[] = new RPessoalCargo();
    $this->roUltimoCargo     = &$this->arRPessoalCargo[ count($this->arRPessoalCargo) - 1 ];
}

/**
    * Inclui os dados da Sub-Divisão
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirSubDivisao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    $obErro = $this->obTPessoalSubDivisao->proximoCod ( $this->inCodSubDivisao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalSubDivisao->setDado ( "cod_sub_divisao"                 , $this->inCodSubDivisao  );
        $this->obTPessoalSubDivisao->setDado ( "descricao"                       , $this->stDescricao    );
        $this->obTPessoalSubDivisao->setDado ( "cod_regime"                      , $this->roPessoalRegime->getCodRegime());
        $obErro = $this->obTPessoalSubDivisao->inclusao ( $boTransacao );
        }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalSubDivisao );

    return $obErro;
}
/**
    * Altera os dados da Sub-Divisão
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarSubDivisao($boTransacao = "")
{
  $boFlagTransacao = false;
  $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
  if ( !$obErro->ocorreu() ) {
        $this->obTPessoalSubDivisao->setDado ( "cod_sub_divisao"                 , $this->inCodSubDivisao  );
        $this->obTPessoalSubDivisao->setDado ( "descricao"                       , $this->stDescricao    );
        $this->obTPessoalSubDivisao->setDado ( "cod_regime"                      , $this->roPessoalRegime->getCodRegime());
        $obErro = $this->obTPessoalSubDivisao->alteracao( $boTransacao );
     }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalSubDivisao );

    return $obErro;
}
/**
    * Exclui os dados da Sub-Divisão
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirSubDivisao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if (!$obErro->ocorreu()) {
        $this->obTPessoalSubDivisao->setDado ( "cod_sub_divisao", $this->inCodSubDivisao  );
        $obErro = $this->obTPessoalSubDivisao->validaExclusao();
        if (!$obErro->ocorreu()) {
            $obErro = $this->obTPessoalSubDivisao->exclusao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalSubDivisao );

    return $obErro;
}

/**
   * Executa um recuperaTodos na classe Persistente PessoalSubDivisao
   * @access Public
   * @param  Object $rsSubDivisao Retorna o RecordSet preenchido
   * @param  String $stOrdem Parâmetro de Ordenação
   * @param  Object $boTransacao Parâmetro Transação
   * @return Object Objeto Erro
*/
function listarSubDivisao(&$rsSubDivisao, $stFiltro = "", $boTransacao = "")
{
    if ( $this->getCodSubDivisao() )
        $stFiltro .= " AND psd.cod_sub_divisao = ".$this->getCodSubDivisao()." ";
    if ($this->roPessoalRegime->inCodRegime) {
       if ($this->roPessoalRegime->getCodRegime()) {
          $stFiltro .= "  and pr.cod_regime = ".$this->roPessoalRegime->getCodRegime()."";
       }
    }
    $stOrdem = " nom_regime,nom_sub_divisao";
    $obErro = $this->obTPessoalSubDivisao->recuperaRelacionamento( $rsSubDivisao , $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
   * Executa um recuperaTodos na classe Persistente PessoalSubDivisao
   * @access Public
   * @param  Object $rsSubDivisao Retorna o RecordSet preenchido
   * @param  String $stCodigos Parâmetro de codigos nos seguinte formato: 42,12,74,85
   * @param  Object $boTransacao Parâmetro Transação
   * @return Object Objeto Erro
*/
function listarSubDivisaoDeCodigosRegime(&$rsSubDivisao, $stCodigos, $boTransacao = "")
{
    $stFiltro = " AND psd.cod_regime IN (".$stCodigos.")";
    $obErro = $this->obTPessoalSubDivisao->recuperaRelacionamento( $rsSubDivisao , $stFiltro, "", $boTransacao );

    return $obErro;
}

/**
    * Lista os cargos e especialidades associados a subdivisao
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCargoEspecialidade(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = '';
    if ($this->inCodSubDivisao) {
        $stFiltro .= " WHERE cod_sub_divisao IN ( ".$this->inCodSubDivisao. ")
                       GROUP BY
                           cod_cargo,
                           cod_especialidade
                       ORDER BY
                           descr_cargo, descr_espec";
    }
    $obErro = $this->obTPessoalCargo->recuperaCargoEspecialidade( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

}
?>

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
    * Classe de regra de negócio para Pessoal-Especialidade
    * Data de Criação: 07/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-13 17:15:30 -0300 (Qua, 13 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidade.class.php"       );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"          );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadePadrao.class.php"       );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                    );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidadeSubDivisao.class.php"  );

/**
    * Classe de regra de negócio para Pessoal-Cargo
    * Data de Criação: 02/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalEspecialidade
{
/**
    * @access Private
    * @var Integer
*/
var $inCodEspecialidade;

/**
    * @access Private
    * @var object
*/
var $roPessoalCargo;

/**
    * @access Private
    * @var Integer
*/
var $stDescricaoEspecialidade;

/**
    * @access Private
    * @var integer
*/
var $inCBO;

/**
    * @access Private
    * @var array
*/
var $arRPessoalEspecialidadeSubDivisao;

/**
    * @access Private
    * @var Object
*/
var $roUltimoEspecialidadeSubDivisao;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodEspecialidade($valor) { $this->inCodEspecialidade       = $valor; }

/**
    * @access Public
    * @param String $valor
*/
function setDescricaoEspecialidade($valor) { $this->stDescricaoEspecialidade = $valor; }

/**
    * @access Public
    * @param integer $valor
*/
function setCBOEspecialidade($valor) { $this->inCBOEspecialidade       = $valor; }

/**
    * @access Public
    * @return Integer
*/
function getCodEspecialidade() { return $this->inCodEspecialidade;         }

/**
    * @access Public
    * @return String
*/
function getDescricaoEspecialidade() { return $this->stDescricaoEspecialidade;   }

/**
    * @access Public
    * @return Integer
*/
function getCBOEspecialidade() { return $this->inCBOEspecialidade;         }

/**
     * Método construtor
     * @access Private
*/

function RPessoalEspecialidade(&$roRPessoalCargo)
{
    $this->obTPessoalEspecialidade             = new TPessoalEspecialidade;
    $this->obTPessoalEspecialidadePadrao       = new TPessoalEspecialidadePadrao;
    $this->roPessoalCargo                      = &$roRPessoalCargo;
    $this->obTransacao                         = new Transacao;
    $this->obRFolhaPagamentoPadrao             = new RFolhaPagamentoPadrao;
    $this->obRNorma                            = new RNorma;
    $this->arRPessoalEspecialidadeSubDivisao   = array ();
}

/**
    * Inclui os dados do CargoEspecialidade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTPessoalEspecialidade->proximoCod ( $inCodEspecialidade, $boTransacao );
        $this->inCodEspecialidade = $inCodEspecialidade;
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalEspecialidade->setDado ( "cod_cargo"         , $this->roPessoalCargo->getCodCargo ());
            $this->obTPessoalEspecialidade->setDado ( "cod_especialidade" , $this->inCodEspecialidade       );
            $this->obTPessoalEspecialidade->setDado ( "descricao"         , $this->stDescricaoEspecialidade );
            $obErro = $this->obTPessoalEspecialidade->inclusao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRFolhaPagamentoPadrao->listarPadrao( $rsPadrao,$boTransacao );
                $this->obTPessoalEspecialidadePadrao->setDado( 'cod_especialidade', $this->getCodEspecialidade() );
                $this->obTPessoalEspecialidadePadrao->setDado( 'cod_padrao'       , $this->obRFolhaPagamentoPadrao->getCodPadrao() );
                $this->obTPessoalEspecialidadePadrao->inclusao ( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->roUltimoEspecialidadeSubDivisao->incluirVagasEspecialidade ( $boTransacao );
                }
            }
            if ( !$obErro->ocorreu() ) {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCboEspecialidade.class.php");
                $obTPessoalCboEspecialidade = new TPessoalCboEspecialidade();
                $obTPessoalCboEspecialidade->setDado    ( "cod_cbo", $this->inCBOEspecialidade);
                $obTPessoalCboEspecialidade->setDado    ( "cod_especialidade", $this->getCodEspecialidade());
                $obErro = $obTPessoalCboEspecialidade->inclusao ( $boTransacao );
            }
        }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidade );
    }

    return $obErro;
}

/**
    * Adiciona um CargoSubDivisao ao array de referencia-objeto
    * @access Public
*/
function addEspecialidadeSubDivisao()
{
    $this->arRPessoalEspecialidadeSubDivisao[] = new RPessoalEspecialidadeSubDivisao ( $this );
    $this->roUltimoEspecialidadeSubDivisao     = &$this->arRPessoalEspecialidadeSubDivisao[ count($this->arRPessoalEspecialidadeSubDivisao) - 1 ];
}

/**
    * Retira um CargoSubDivisao do array de referencia-objeto
    * @access Public
*/
function commitEspecialidadeSubDivisao()
{
    $this->arRPessoalEspecialidadeSubDivisao = array_pop ($this->arRPessoalEspecialidadeSubDivisao);
}

/**
    * Busca Especialidades do cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultaEspecialidadeCargo(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";
    if (count($this->roPessoalCargo->arRPessoalCargoSubDivisao)) {
        $this->obTPessoalEspecialidade->setDado('cod_sub_divisao',$this->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao() );
    } else {
        $this->obTPessoalEspecialidade->setDado('cod_sub_divisao', '');
    }

    $this->obTPessoalEspecialidade->setDado('cod_cargo',$this->roPessoalCargo->getCodCargo ());
    if ($this->getCodEspecialidade() ) {
        $this->obTPessoalEspecialidade->setDado('cod_especialidade',$this->getCodEspecialidade());
    } else {
        $this->obTPessoalEspecialidade->setDado('cod_especialidade','');
    }

    $obErro = $this->obTPessoalEspecialidade->recuperaRelacionamento( $rsRecordSet, $stFiltro, "ORDER BY descricao_especialidade, cod_cargo, esp.cod_especialidade, esp.cod_sub_divisao", $boTransacao );

    return $obErro;
}

/**
    * Busca Especialidades do cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultaEspecialidadeCargoMaxVigencia(&$rsRecordSet , $boTransacao = "")
{
    $stFiltro = "";

    $this->obTPessoalEspecialidade->setDado('cod_cargo',$this->roPessoalCargo->getCodCargo ());

    if ($_REQUEST['boEspecialidade'] || $_REQUEST['hdnboEspecialidade'] || $this->getCodEspecialidade() ) {
       $this->obTPessoalEspecialidade->setDado('cod_especialidade',$this->getCodEspecialidade());
    }
    $obErro = $this->obTPessoalEspecialidade->recuperaCargoEspecialidade( $rsRecordSet, $stFiltro, $stOrder = "", $boTransacao );

    return $obErro;
}

/**
    * Lista todas as especialidade de uma lista de códigos
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @param  Array  $arCodCargos Parâmetro array de códigos de especialidades
    * @return Object Objeto Erro
*/
function listarEspecialidadesPorCodigo(&$rsRecordSet , $arCodEspecialidades, $boTransacao = "")
{
    if ( is_array($arCodEspecialidades) ) {
        foreach ($arCodEspecialidades as $inCodEspecialidade) {
            $stCodEspecialidade .= $inCodEspecialidade . ",";
        }
        $stCodEspecialidade = substr($stCodEspecialidade,0,strlen($stCodEspecialidade)-1);
        $stFiltro = " WHERE cod_especialidade IN (".$stCodEspecialidade.")";
        $stOrdem = " ORDER BY descricao";
        $obErro = $this->obTPessoalEspecialidade->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
    } else {
        $obErro = new erro;
    }

    return $obErro;

}

/**
    * Busca Especialidades do cargo conforme filtro
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEspecialidadeDeCodigosCargo(&$rsRecordSet, $stCodigos, $boTransacao = "")
{
    $stFiltro = " AND especialidade.cod_cargo IN (".$stCodigos.")";
    $stOrder  = " ORDER BY descricao";
    $obErro = $this->obTPessoalEspecialidade->recuperaEspecialidadeDeCodigosCargo( $rsRecordSet, $stFiltro, $stOrder = "", $boTransacao );

    return $obErro;
}

/**
    * Altera os dados da Especialidade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/

function alterarEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( strlen($this->getCodEspecialidade() <= 0) ) {
            $obErro = $this->obTPessoalEspecialidade->proximoCod ( $inCodEspecialidade, $boTransacao );
            $this->setCodEspecialidade($inCodEspecialidade);
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTPessoalEspecialidade->setDado ( "cod_cargo"         , $this->roPessoalCargo->getCodCargo ());
            $this->obTPessoalEspecialidade->setDado ( "cod_especialidade" , $this->inCodEspecialidade       );
            $this->obTPessoalEspecialidade->setDado ( "descricao"         , $this->stDescricaoEspecialidade );
            $obErro = $this->obTPessoalEspecialidade->alteracao ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obRFolhaPagamentoPadrao->listarPadrao( $rsPadrao,$boTransacao );
                $this->obTPessoalEspecialidadePadrao->setDado( 'cod_especialidade', $this->getCodEspecialidade() );
                $this->obTPessoalEspecialidadePadrao->setDado( 'cod_padrao'       , $this->obRFolhaPagamentoPadrao->getCodPadrao() );
                $this->obTPessoalEspecialidadePadrao->inclusao ( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->roUltimoEspecialidadeSubDivisao->incluirVagasEspecialidade ( $boTransacao );
                }
            }
            if ( !$obErro->ocorreu() ) {
                include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCboEspecialidade.class.php");
                $obTPessoalCboEspecialidade = new TPessoalCboEspecialidade();
                $obTPessoalCboEspecialidade->setDado    ( "cod_cbo", $this->inCBOEspecialidade);
                $obTPessoalCboEspecialidade->setDado    ( "cod_especialidade", $this->getCodEspecialidade());
                $obErro = $obTPessoalCboEspecialidade->inclusao ( $boTransacao );
            }
        }
       $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidade );
    }

    return $obErro;
}

/**
    * Excluir os dados da Especialidade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu () ) {
      if ( !$obErro->ocorreu () ) {
         $this->addEspecialidadeSubDivisao();
         $obErro = $this->roUltimoEspecialidadeSubDivisao->excluirVagasEspecialidade($boTransacao);
         if ( !$obErro->ocorreu() ) {
             include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalCboEspecialidade.class.php");
             $obTPessoalCboEspecialidade = new TPessoalCboEspecialidade();
             $obTPessoalCboEspecialidade->setDado    ( "cod_especialidade", $this->getCodEspecialidade());
             $obErro = $obTPessoalCboEspecialidade->exclusao ( $boTransacao );
         }
         if ( !$obErro->ocorreu () ) {
               $this->obTPessoalEspecialidadePadrao->setDado( "cod_especialidade" , $this->getCodEspecialidade() );
               $obErro = $this->obTPessoalEspecialidadePadrao->exclusao( $boTransacao );
               if ( !$obErro->ocorreu () ) {
                  $this->obTPessoalEspecialidade->setDado( "cod_especialidade" , $this->getCodEspecialidade() );
                  $this->obTPessoalEspecialidade->exclusao( $boTransacao );
               }
         }
      }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidade );

    return $obErro;
}

function validaExclusao()
{
    $this->obTPessoalEspecialidade->setDado( "cod_especialidade" , $this->getCodEspecialidade() );
    $obErro = $this->obTPessoalEspecialidade->validaExclusao();

    return $obErro;
}

/**
    * Lista Especialidade pos cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarEspecialidadesPorCargo(&$rsRecordSet , $boTransacao = "")
{
    if ( $this->roPessoalCargo->getCodCargo () ) {
        $stFiltro .= " WHERE cod_cargo = ".$this->roPessoalCargo->getCodCargo ();
    }
    $stOrder = " descricao";
    $obErro = $this->obTPessoalEspecialidade->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

}

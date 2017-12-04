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
    * Classe de regra de negócio para Pessoal-EspecialidadeSubDivisao
    * Data de Criação: 03/01/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"                );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalEspecialidadeSubDivisao.class.php"   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalEspecialidade.class.php"                  );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"                     );

/**
    * Classe de regra de negócio para Pessoal-CargoSubDivisao
    * Data de Criação: 03/01/2005

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalEspecialidadeSubDivisao
{
/**
    * @access Private
    * @var date
*/
var $dtVigencia;
/**
    * @access Private
    * @var array
*/
var $arNroVagas;
/**
    * @access Private
    * @var array
*/
var $arNroVagasCriada;

/**
    * @access Private
    * @var object
*/
var $roPessoalEspecialidade;
/**
    * @access Public
    * @param date $valor
*/
function setVigencia($valor) { $this->dtVigencia  = $valor; }
/**
    * @access Public
    * @param array $valor
*/
function setNroVagas($valor) { $this->arNroVagas  = $valor; }
/**
    * @access Public
    * @param array $valor
*/
function setNroVagasCriada($valor) { $this->arNroVagasCriada  = $valor; }

/**
    * @access Public
    * @return date
*/
function getVigencia() { return $this->dtVigencia; }
/**
    * @access Public
    * @return array
*/
function getNroVagas() { return $this->arNroVagas; }
/**
    * @access Public
    * @return array
*/
function getNroVagasCriada() { return $this->arNroVagasCriada; }

/**
     * Método construtor
     * @access Private
*/
function RPessoalEspecialidadeSubdivisao(&$roRPessoalEspecialidade)
{
    $this->roPessoalEspecialidade            = &$roRPessoalEspecialidade;
    $this->obTransacao                       = new Transacao;
    $this->obTPessoalEspecialidadeSubDivisao = new TPessoalEspecialidadeSubDivisao;
    $this->obTPessoalSubDivisao              = new TPessoalSubDivisao;
    $this->obRPessoalSubDivisao              = new RPessoalSubDivisao( $obRRegime );
}

/**
    * Inclui os dados do CargoSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirVagasEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() and is_array($this->getNroVagas())) {
        foreach ($this->getNroVagas() as $stCampo=>$inValor) {
            $arCodSubDivisaoNrVagas = explode("_", $stCampo);
            if ($inValor >= 0) {
                $this->obTPessoalEspecialidadeSubDivisao->setDado( 'cod_especialidade', $this->roPessoalEspecialidade->getCodEspecialidade()                   );
                $this->obTPessoalEspecialidadeSubDivisao->setDado( 'cod_sub_divisao',   $arCodSubDivisaoNrVagas[2]                                             );
                $this->obTPessoalEspecialidadeSubDivisao->setDado( 'cod_norma',         $this->roPessoalEspecialidade->obRNorma->getCodNorma()                 );
                $this->obTPessoalEspecialidadeSubDivisao->setDado( 'nro_vaga_criada',   $inValor                                             );
                $obErro = $this->obTPessoalEspecialidadeSubDivisao->inclusao ( $boTransacao );
            }
        }
    }
    if ( !$obErro->ocorreu() ) {
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidade);
    }

    return $obErro;
}

/**
    * Excluir os dados do EspecialidadeSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirVagasEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalEspecialidadeSubDivisao->setDado ( "cod_especialidade"      , $this->roPessoalEspecialidade->getCodEspecialidade ());

        $obErro = $this->obTPessoalEspecialidadeSubDivisao->exclusao ( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidadeSubDivisao );

    return $obErro;
}

/**
    * Altera número de vagas do EspecialidadeSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarVagasEspecialidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalEspecialidadeSubDivisao->setDado ( "cod_especialidade", $this->roPessoalEspecialidade->getCodEspecialidade ());
        $this->obTPessoalEspecialidadeSubDivisao->setDado ( "cod_norma"        , $this->roPessoalEspecialidade->roPessoalCargo->obRNorma->getCodNorma ());
        $this->obTPessoalEspecialidadeSubDivisao->setDado ( "cod_sub_divisao",   $this->roPessoalEspecialidade->roPessoalCargo->roUltimoCargoSubDivisao->obRPessoalSubDivisao->getCodSubDivisao ());
        $this->obTPessoalEspecialidadeSubDivisao->setDado ( "nro_vaga_criada",   $this->getNroVagasCriada() );
        $obErro = $this->obTPessoalEspecialidadeSubDivisao->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalEspecialidadeSubDivisao );

    return $obErro;
}

/**
    * Lista todos cargo_sub_divisao para um determinado especialidade
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVagasEspecialidade(&$rsRecordSet, $inCodEspecialidade,$inCodSubDivisao,$inCodCargo, $boTransacao = "")
{
    $stFiltro  = " AND especialidade_sub_divisao.cod_sub_divisao   = ".$inCodSubDivisao;
    $stFiltro .= " AND especialidade_sub_divisao.cod_especialidade = ".$inCodEspecialidade;
    $stFiltro .= " AND especialidade.cod_cargo = ".$inCodCargo;
    $stOrdem = "";
    $obErro = $this->obTPessoalEspecialidadeSubDivisao->recuperaVagasEspecialidade( $rsRecordSet,$stFiltro,$stOrdem, $boTransacao );

    return $obErro;
}

}
?>

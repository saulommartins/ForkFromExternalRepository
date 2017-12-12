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
    * Classe de regra de negócio para Pessoal-CargoSubDivisao
    * Data de Criação: 09/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2008-04-02 10:05:18 -0300 (Qua, 02 Abr 2008) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalSubDivisao.class.php"        );
include_once ( CAM_GRH_PES_MAPEAMENTO."TPessoalCargoSubDivisao.class.php"   );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalCargo.class.php"                  );
include_once ( CAM_GRH_PES_NEGOCIO."RPessoalSubDivisao.class.php"             );

/**
    * Classe de regra de negócio para Pessoal-CargoSubDivisao
    * Data de Criação: 09/12/2004

    * @author Analista: Leandro Oliveira
    * @author Desenvolvedor: Gustavo Passos Tourinho
    * @author Desenvolvedor: Vandre Miguel Ramos

    * @package URBEM
    * @subpackage Regra
*/
class RPessoalCargoSubDivisao
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
var $roPessoalCargo;
/**
    * @var Objeto
    * @access Private
    */
var $obRCargo;

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
    * @param Object $valor
    */
function setRPessoalCargo($valor) { $this->obRPessoalCargo    = $valor; }

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
    * @access Public
    * @param Object $valor
*/
function getRPessoalCargo() { return $this->obRPessoalCargo;   }

/**
     * Método construtor
     * @access Private
*/
function RPessoalCargoSubdivisao(&$roRPessoalCargo)
{
    $this->obTPessoalCargoSubDivisao = new TPessoalCargoSubDivisao;
    $this->roPessoalCargo            = &$roRPessoalCargo;
    $this->obTransacao               = new Transacao;

    $this->obTPessoalSubDivisao      = new TPessoalSubDivisao;
    $this->setRPessoalCargo          ( new RPessoalCargo                       );
    // em função da implementacao posterior da regra REGIME esta sendo passado a string vazia no "Objeto".
    $this->obRPessoalSubDivisao      = new RPessoalSubDivisao($obRRegime);
}

/**
    * Inclui os dados do CargoSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirVagas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCargoSubDivisao->setDado ( "cod_cargo"      , $this->roPessoalCargo->getCodCargo ());
        $this->obTPessoalCargoSubDivisao->setDado ( "cod_norma"      , $this->roPessoalCargo->obRNorma->getCodNorma ());

        for ($inCount=0; $inCount<=count($this->arNroVagasCriada)-1;$inCount++) {
            $arCodSubDivisaoNrVagasCriadas = explode("_", $this->arNroVagasCriada[$inCount] );
            $this->obTPessoalCargoSubDivisao->setDado ( "cod_sub_divisao", $arCodSubDivisaoNrVagasCriadas[0] );
            $this->obTPessoalCargoSubDivisao->setDado ( "nro_vaga_criada", $arCodSubDivisaoNrVagasCriadas[1] );
            $obErro = $this->obTPessoalCargoSubDivisao->inclusao ( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargoSubDivisao );

    return $obErro;
}

/**
    * Consulta a existência de um cargo na mesma vigencia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    $this->obTPessoalCargoSubDivisao->setDado( "cod_cargo"       , $this->roPessoalCargo->getCodCargo () );
    $this->obTPessoalCargoSubDivisao->setDado( "cod_sub_divisao" , $this->obRPessoalSubDivisao->getCodSubDivisao());
    $this->obTPessoalCargoSubDivisao->setDado( "vigencia"        , $this->dtVigencia                     );
    $obErro = $this->obTPessoalCargoSubDivisao->recuperaPorChave( $rsCargoVigencia, $boTransacao );
    if ( !$obErro->ocorreu() ) {
       $this->obRPessoalCargo->setCodCargo($rsCargoVigencia->getCampo("cod_cargo"));
       $this->obRPessoalSubDivisao->setCodSubDivisao($rsCargoVigencia->getCampo("cod_sub_divisao"));
       $this->dtVigencia = $rsCargoVigencia->getCampo("vigencia");
    }

    return $obErro;
}

/**
    * Alterar os dados do CargoSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarVagas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCargoSubDivisao->setDado ( "cod_cargo"      , $this->roPessoalCargo->getCodCargo ());
        $this->obTPessoalCargoSubDivisao->setDado ( "cod_norma"      , $this->roPessoalCargo->obRNorma->getCodNorma ());
        if ( is_array( $this->arNroVagas )) {
           for ( $inCount = 0; ($inCount < count ($this->arNroVagas)) && (!$obErro->ocorreu());  $inCount++ ) {
                $arCodSubDivisao = explode("_",$this->arNroVagas[$inCount]);
                $this->obTPessoalCargoSubDivisao->setDado ( "cod_sub_divisao", $arCodSubDivisao[0]);
                $this->obTPessoalCargoSubDivisao->setDado ( "nro_vaga_criada", ($arCodSubDivisao[2]? $arCodSubDivisao[2] : 0));
                $obErro = $this->obTPessoalCargoSubDivisao->alteracao( $boTransacao );
            }
        } else {
            $this->obTPessoalCargoSubDivisao->setDado ("cod_sub_divisao", $this->obRPessoalSubDivisao->getCodSubDivisao());
            $this->obTPessoalCargoSubDivisao->setDado ( "nro_vaga_criada",$this->getNroVagasCriada() );
            $obErro = $this->obTPessoalCargoSubDivisao->alteracao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargoSubDivisao );
    }

    return $obErro;
}

/**
    * Excluir os dados do CargoSubDivisao
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirVagas($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTPessoalCargoSubDivisao->setDado ( "cod_cargo"      , $this->roPessoalCargo->getCodCargo ());
        $obErro = $this->obTPessoalCargoSubDivisao->exclusao ( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTPessoalCargoSubDivisao );
    }

    return $obErro;
}
/**
    * Lista todos cargo_sub_divisao para um determinado cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVagas(&$rsRecordSet, $stFiltro = "", $stOrder = "" , $boTransacao = "")
{
    if ( $this->obRPessoalSubDivisao->getCodSubDivisao() != "" ) {
        $stFiltro = " and cargo_sub_divisao.cod_sub_divisao =  ".$this->obRPessoalSubDivisao->getCodSubDivisao();
    }
    $this->obTPessoalCargoSubDivisao->setDado ("cod_cargo", $this->roPessoalCargo->inCodCargo);

    $obErro = $this->obTPessoalCargoSubDivisao->recuperaRelacionamento( $rsRecordSet,$stFiltro,$stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista todos cargo_sub_divisao para um determinado cargo
    * @access Public
    * @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarVagasServidor(&$rsRecordSet, $stFiltro = "", $stOrder = "" , $boTransacao = "")
{
    if ( $this->obRPessoalSubDivisao->getCodSubDivisao() ) {
        $stFiltro .= " AND cargo_sub_divisao.cod_sub_divisao = ".$this->obRPessoalSubDivisao->getCodSubDivisao();
    }
    $stFiltro .= " AND cargo_sub_divisao.cod_cargo = ".$this->roPessoalCargo->inCodCargo;
    $obErro = $this->obTPessoalCargoSubDivisao->recuperaVagasServidor( $rsRecordSet,$stFiltro,"", $boTransacao );

    return $obErro;
}

function listarVagasSubDivisao(&$rsRecordSet, $boTransacao = "")
{
    $arVagasSubdivisao = array();
    $rsRecordSet       = new RecordSet;

    $this->obRPessoalCargo->listarVagas( $rsSubDivisoes, "", "cod_sub_divisao", $boTransacao );

    $inCount = 0;
    $inCodNormaMinima = '';
    $inCodNormaMaxima = '';
    while (!$rsSubDivisoes->eof()) {
        $this->obRPessoalSubDivisao->setCodSubDivisao($rsSubDivisoes->getCampo('cod_sub_divisao'));
        $this->listarVagas( $rsVagas, "", "", $boTransacao );
        $arVagasSubdivisao[$inCount]['cod_regime'     ] = $rsSubDivisoes->getCampo('cod_regime'     );
        $arVagasSubdivisao[$inCount]['nom_regime'     ] = $rsSubDivisoes->getCampo('nom_regime'     );
        $arVagasSubdivisao[$inCount]['nom_sub_divisao'] = $rsSubDivisoes->getCampo('nom_sub_divisao');
        $arVagasSubdivisao[$inCount]['cod_sub_divisao'] = $rsSubDivisoes->getCampo('cod_sub_divisao');
        $arVagasSubdivisao[$inCount]['timestamp'      ] = $rsVagas->getCampo('timestamp'      );
        $arVagasSubdivisao[$inCount]['cod_cargo'      ] = $rsVagas->getCampo('cod_cargo'      );
        $arVagasSubdivisao[$inCount]['nro_vagas'      ] = ($rsVagas->getCampo('nro_vagas'      ) != "") ? $rsVagas->getCampo('nro_vagas'      ) : 0;
        $arVagasSubdivisao[$inCount]['nro_vaga_criada'] = ($rsVagas->getCampo('nro_vaga_criada') != "") ? $rsVagas->getCampo('nro_vaga_criada') : 0;
//         $arVagasSubdivisao[$inCount]['norma_minima'   ] = $rsVagas->getCampo('norma_minima'   );
//         $arVagasSubdivisao[$inCount]['norma_maxima'   ] = $rsVagas->getCampo('norma_maxima'   );

        if ($inCodNormaMinima == "") {
            $inCodNormaMinima = $rsVagas->getCampo('norma_minima');
        }
        if ($inCodNormaMaxima == "") {
            $inCodNormaMaxima = $rsVagas->getCampo('norma_maxima');
        }
        $rsSubDivisoes->proximo();
        $inCount++;
    }
    $rsRecordSet->preenche($arVagasSubdivisao);
    $rsRecordSet->setCampo("norma_minima",$inCodNormaMinima,true);
    $rsRecordSet->setCampo("norma_maxima",$inCodNormaMaxima,true);
}

}
?>

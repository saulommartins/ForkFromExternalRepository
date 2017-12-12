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
    * Classe de regra de negócio para Modalidade de Lançamento - Atividade
    * Data de Criação: 03/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMModalidadeAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.10  2006/12/07 18:09:50  cercato
Bug #7770#

Revision 1.9  2006/11/10 17:46:44  cercato
bug #7358#

Revision 1.8  2006/11/08 10:34:45  fabio
alteração do uc_05.02.13

Revision 1.7  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeModalidadeLancamento.class.php"                  );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeModalidadeMoeda.class.php"                       );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeModalidadeIndicador.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php"                              );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMAtividade.class.php"                                         );
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php"                                             );
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php"                                );

/**
* Classe de regra de negócio para Modalidade de Lançamento - Atividade
* Data de Criação: 30/01/2005

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMModalidadeAtividade
{
/**
* @access Private
* @var Date
*/
var $dtDataInicio;
/**
* @access Private
* @var String
*/
var $stMotivo;
/**
* @access Private
* @var Numeric
*/
var $nuValor;
/**
* @access Private
* @var Boolean
*/
var $boPercentual;
/**
* @access Private
* @var Object
*/
var $obTCEMAtividadeModalidadeLancamento;
/**
* @access Private
* @var Object
*/
var $obTCEMAtividadeModalidadeMoeda;
/**
* @access Private
* @var Object
*/
var $obTCEMAtividadeModalidadeIndicador;
/**
* @access Private
* @var Object
*/
var $obRCEMModalidadeLancamento;
/**
* @access Private
* @var Object
*/
var $obRCEMAtividade;
/**
* @access Private
* @var Object
*/
var $obRMONMoeda;
/**
* @access Private
* @var Object
*/
var $obRMONIndicadorEconomico;

//SETTERS
/**
* @access Public
* @param Date $valor
*/
function setDataInicio($valor) { $this->dtDataInicio = $valor; }
/**
* @access Public
* @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;     }
/**
* @access Public
* @param Numeric $valor
*/
function setValor($valor) { $this->nuValor = $valor;      }
/**
* @access Public
* @param Boolean $valor
*/
function setPercentual($valor) { $this->boPercentual = $valor; }

//GETTERS
/**
* @access Public
* @return Date
*/
function getDataInicio() { return $this->dtDataInicio; }
/**
* @access Public
* @return String
*/
function getMotivo() { return $this->stMotivo;     }
/**
* @access Public
* @return Numeric
*/
function getValor() { return $this->nuValor;      }
/**
* @access Public
* @return Boolean
*/
function getPercentual() { return $this->boPercentual; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMModalidadeAtividade()
{
    $this->obTCEMAtividadeModalidadeLancamento = new TCEMAtividadeModalidadeLancamento;
    $this->obTCEMAtividadeModalidadeMoeda      = new TCEMAtividadeModalidadeMoeda;
    $this->obTCEMAtividadeModalidadeIndicador  = new TCEMAtividadeModalidadeIndicador;
    $this->obRCEMModalidadeLancamento          = new RCEMModalidadeLancamento;
    $this->obRMONMoeda                         = new RMONMoeda;
    $this->obRMONIndicadorEconomico            = new RMONIndicadorEconomico;
    $this->obRCEMAtividade                     = new RCEMAtividade;
    $this->obTransacao                         = new Transacao;
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
    * Seta dados na tabela para inclusao, alteracao e exclusao
    * @access Private
*/
function setarDados()
{
    $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_atividade" , $this->obRCEMAtividade->getCodigoAtividade()             );
    $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_modalidade", $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
    $this->obTCEMAtividadeModalidadeLancamento->setDado( "dt_inicio"     , $this->dtDataInicio                                      );
}

/**
* Inclui os dados setados na tabela de Modalidade de Lancamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function definirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMAtividadeModalidadeLancamento->inclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeModalidadeLancamento );

    return $obErro;
}

/**
* Exclui os dados da Modalidade de Lancamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMAtividadeModalidadeMoeda->setDado( "cod_atividade", $this->obRCEMAtividade->getCodigoAtividade() );
        $obErro = $this->obTCEMAtividadeModalidadeMoeda->exclusao             ( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_atividade", $this->obRCEMAtividade->getCodigoAtividade() );
            $obErro = $this->obTCEMAtividadeModalidadeIndicador->exclusao     ( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $stFiltro = " WHERE cod_atividade = ".$this->obRCEMAtividade->getCodigoAtividade()." AND dt_baixa IS NULL ";
                $this->obTCEMAtividadeModalidadeLancamento->recuperaTodos ( $rsLista, $stFiltro, $stOrder, $boTransacao );
                while ( !$rsLista->Eof() ) {
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_atividade", $rsLista->getCampo("cod_atividade") );

                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_modalidade", $rsLista->getCampo("cod_modalidade") );

                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "dt_inicio", $rsLista->getCampo("dt_inicio") );

                    $obErro = $this->obTCEMAtividadeModalidadeLancamento->exclusao( $boTransacao );

                    $rsLista->proximo();
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeModalidadeLancamento );

    return $obErro;
}

/**
* Cadastra os dados da Modalidade de Lancamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cadastrarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodigoModalidadeTemp = $this->obRCEMModalidadeLancamento->getCodigoModalidade();
        $this->consultarModalidade();
        if ( $this->obRCEMModalidadeLancamento->getCodigoModalidade() != "" ) {
            $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_atividade" , $this->obRCEMAtividade->getCodigoAtividade() );

            $obErro = $this->excluirModalidade();

            $this->obRCEMModalidadeLancamento->setCodigoModalidade( $inCodigoModalidadeTemp );
            $this->setarDados();
            $this->obTCEMAtividadeModalidadeLancamento->setDado( "valor"         , $this->nuValor      );
            $this->obTCEMAtividadeModalidadeLancamento->setDado( "percentual"    , $this->boPercentual );
            $obErro = $this->definirModalidade();
            if ( !$obErro->ocorreu() ) {
                if ( $this->obRMONMoeda->getCodMoeda() ) {
                    $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                    $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                    $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "dt_inicio"      , $this->getDataInicio()                                   );
                    $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_moeda"      , $this->obRMONMoeda->getCodMoeda()                        );
                    $this->obTCEMAtividadeModalidadeMoeda->inclusao( $boTransacao );
                } elseif ( $this->obRMONIndicadorEconomico->getCodIndicador() ) {
                    $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                    $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                    $this->obTCEMAtividadeModalidadeIndicador->setDado( "dt_inicio"      , $this->getDataInicio()                                   );
                    $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_indicador"  , $this->obRMONIndicadorEconomico->getCodIndicador()       );
                    $this->obTCEMAtividadeModalidadeIndicador->inclusao( $boTransacao );
                }
            }
        } else {
            $this->obRCEMModalidadeLancamento->setCodigoModalidade( $inCodigoModalidadeTemp );
            $this->setarDados();
            $this->obTCEMAtividadeModalidadeLancamento->setDado( "valor"         , $this->nuValor      );
            $this->obTCEMAtividadeModalidadeLancamento->setDado( "percentual"    , $this->boPercentual );
            $this->verificaBaixados( $rsBaixados );
            if ( $rsBaixados->getNumLinhas() < 1 ) {
                $obErro = $this->definirModalidade();
                if ( !$obErro->ocorreu() ) {
                    if ( $this->obRMONMoeda->getCodMoeda() ) {
                        $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                        $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                        $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "dt_inicio"      , $this->getDataInicio()                                   );
                        $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_moeda"      , $this->obRMONMoeda->getCodMoeda()                        );
                        $this->obTCEMAtividadeModalidadeMoeda->inclusao( $boTransacao );
                    } elseif ( $this->obRMONIndicadorEconomico->getCodIndicador() ) {
                        $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                        $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                        $this->obTCEMAtividadeModalidadeIndicador->setDado( "dt_inicio"      , $this->getDataInicio()                                   );
                        $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_indicador"  , $this->obRMONIndicadorEconomico->getCodIndicador()       );
                        $this->obTCEMAtividadeModalidadeIndicador->inclusao( $boTransacao );
                    }
                }
            } else {
                $obErro->setDescricao("A Modalidade de Lançamento para esta Atividade nesta DATA DE INÍCIO foi dada como BAIXADA recentemente!");
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeModalidadeLancamento );

    return $obErro;
}

/**
* Cadastra os dados da Modalidade de Lancamento em lote
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cadastrarModalidadeLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
    if ( !$obErro->ocorreu() ) {
        $obErro=$this->obRCEMAtividade->listarAtividadesUltimoNivel($rsAtividades,$boTransacao);
        if ( !$obErro->ocorreu() ) {
            while (!$rsAtividades->eof()) {
                $this->obRCEMAtividade->setCodigoAtividade($rsAtividades->getCampo("cod_atividade"));
                $inCodigoModalidadeTemp = $this->obRCEMModalidadeLancamento->getCodigoModalidade();

                $this->consultarModalidade($boTrasacao);
                if ( $this->obRCEMModalidadeLancamento->getCodigoModalidade() != "" ) {
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "cod_atividade" , $this->obRCEMAtividade->getCodigoAtividade() );

                    $obErro = $this->excluirModalidade($boTransacao);

                    $this->obRCEMModalidadeLancamento->setCodigoModalidade( $inCodigoModalidadeTemp );
                    $this->setarDados();
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "valor"         , $this->nuValor      );
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "percentual"    , $this->boPercentual );
                    $obErro = $this->definirModalidade($boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRMONMoeda->getCodMoeda() ) {
                            $this->obTCEMAtividadeModalidadeMoeda->setDado("cod_atividade" ,$this->obRCEMAtividade->getCodigoAtividade() );
                            $this->obTCEMAtividadeModalidadeMoeda->setDado("cod_modalidade",$this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                            $this->obTCEMAtividadeModalidadeMoeda->setDado("dt_inicio"     ,$this->getDataInicio() );
                            $this->obTCEMAtividadeModalidadeMoeda->setDado("cod_moeda"     ,$this->obRMONMoeda->getCodMoeda() );
                            $this->obTCEMAtividadeModalidadeMoeda->inclusao( $boTransacao );
                        } elseif ( $this->obRMONIndicadorEconomico->getCodIndicador() ) {
                            $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                            $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                            $this->obTCEMAtividadeModalidadeIndicador->setDado( "dt_inicio"      , $this->getDataInicio()                                   );
                            $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_indicador"  , $this->obRMONIndicadorEconomico->getCodIndicador()       );
                            $this->obTCEMAtividadeModalidadeIndicador->inclusao( $boTransacao );
                        }
                    }
                } else {
                    $this->obRCEMModalidadeLancamento->setCodigoModalidade( $inCodigoModalidadeTemp );
                    $this->setarDados();
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "valor"         , $this->nuValor      );
                    $this->obTCEMAtividadeModalidadeLancamento->setDado( "percentual"    , $this->boPercentual );
                    $this->verificaBaixados( $rsBaixados );
                    if ( $rsBaixados->getNumLinhas() < 1 ) {
                        $obErro = $this->definirModalidade($boTransacao);
                        if ( !$obErro->ocorreu() ) {
                            if ( $this->obRMONMoeda->getCodMoeda() ) {
                                $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                                $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                                $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "dt_inicio"      , $this->getDataInicio()                                   );
                                $this->obTCEMAtividadeModalidadeMoeda->setDado    ( "cod_moeda"      , $this->obRMONMoeda->getCodMoeda()                        );
                                $this->obTCEMAtividadeModalidadeMoeda->inclusao( $boTransacao );
                            } elseif ( $this->obRMONIndicadorEconomico->getCodIndicador() ) {
                                $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_atividade"  , $this->obRCEMAtividade->getCodigoAtividade()             );
                                $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_modalidade" , $this->obRCEMModalidadeLancamento->getCodigoModalidade() );
                                $this->obTCEMAtividadeModalidadeIndicador->setDado( "dt_inicio"      , $this->getDataInicio()                                   );
                                $this->obTCEMAtividadeModalidadeIndicador->setDado( "cod_indicador"  , $this->obRMONIndicadorEconomico->getCodIndicador()       );
                                $this->obTCEMAtividadeModalidadeIndicador->inclusao( $boTransacao );
                            }
                        }
                    } else {
                        $obErro->setDescricao("A Modalidade de Lançamento para esta Atividade nesta DATA DE INÍCIO foi dada como BAIXADA recentemente!");
                    }
                }
                $rsAtividades->proximo();
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMAtividadeModalidadeLancamento );

    return $obErro;
}

/**
* Baixa a Modalidade de Lancamento setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->setarDados();
        $this->obTCEMAtividadeModalidadeLancamento->setDado( "dt_baixa"     , date('d/m/Y')   );
        $this->obTCEMAtividadeModalidadeLancamento->setDado( "motivo_baixa" , $this->stMotivo );
        $obErro = $this->obTCEMAtividadeModalidadeLancamento->alteracao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMBaixaAtividadeModalidadeLancamento );

    return $obErro;
}

/**
    * Lista as Modalidades de Lancamento - Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarModalidadeAtividade(&$rsLista, $boTransacao = "" , $stOrder = 'aml.cod_atividade')
{
    $stFiltro = "";
    if ( $this->obRCEMAtividade->getValorComposto() ) {
        $this->obRCEMAtividade->setValorComposto($this->obRCEMAtividade->getValorComposto());
        $this->obRCEMAtividade->listarAtividade($rsAtividade,$boTransacao);
        //VERIFICA SE O RECODSET SÓ TEM UM REGISTO, CASO RETORNE MAIS DE UM MONTA A CONSULTA COM O IN PARA RETORNAR TODAS ATIVIDADES
        if ($rsAtividade->getNumLinhas()==1) {
            $stFiltro .= " aml.cod_atividade = ".$rsAtividade->getCampo("cod_atividade")." AND ";
        } else {
            $stFiltro .= " aml.cod_atividade IN (";
            while (!$rsAtividade->eof()) {
                $stFiltro .= $rsAtividade->getCampo("cod_atividade").",";
                $rsAtividade->proximo();
            }
             $stFiltro =  substr($stFiltro,0,-1).") AND ";
        }
    }
    if ( $this->obRCEMAtividade->getCodigoAtividade() ) {
        $stFiltro .= " aml.cod_atividade = ".$this->obRCEMAtividade->getCodigoAtividade()." AND ";
    }
    $stFiltro .= " aml.dt_baixa is null AND ";
    $stFiltro .= " aml.motivo_baixa is null AND ";
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY ".$stOrder."";
    $obErro = $this->obTCEMAtividadeModalidadeLancamento->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );
    //$this->obTCEMAtividadeModalidadeLancamento->debug();
    return $obErro;
}

/**
    * Recupera do banco de dados a Modalidade de Lançamento da Atividade Setada
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarModalidade($boTransacao = "")
{
    $stFiltro .= " AND aml.cod_atividade = ".$this->obRCEMAtividade->getCodigoAtividade()." ";
    $obErro = $this->obTCEMAtividadeModalidadeLancamento->recuperaModalidadeAtividade( $rsModalidade, $stFiltro, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obRCEMModalidadeLancamento->setCodigoModalidade( $rsModalidade->getCampo( "cod_modalidade" ) );
    }

    return $obErro;
}

/**
    * Verifica se a Modalidade de Lancamento a ser cadastrada ja foi Baixada no mesmo dia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaBaixados(&$rsLista, $boTransacao = "")
{
    $stFiltro .= " aml.cod_atividade        = ".$this->obRCEMAtividade->getCodigoAtividade()." AND ";
    $stFiltro .= " aml.cod_modalidade       = ".$this->obRCEMModalidadeLancamento->getCodigoModalidade()." AND ";
    $stFiltro .= " aml.dt_inicio            = to_date('".$this->dtDataInicio."','dd/mm/yyyy') AND ";
    $stFiltro  = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $obErro = $this->obTCEMAtividadeModalidadeLancamento->recuperaBaixados( $rsLista, $stFiltro, $boTransacao );

    return $obErro;
}

}

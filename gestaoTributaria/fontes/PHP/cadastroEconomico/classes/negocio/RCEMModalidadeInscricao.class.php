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
    * Classe de regra de negócio para Modalidade de Lançamento - Inscrição Econômica
    * Data de Criação: 03/01/2005

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMModalidadeInscricao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.12  2007/03/02 14:50:59  dibueno
Bug #8560#

Revision 1.11  2006/12/07 16:42:02  cercato
Bug #7770#

Revision 1.10  2006/11/10 17:15:45  cercato
alteração do uc_05.02.13

Revision 1.9  2006/11/08 10:34:45  fabio
alteração do uc_05.02.13

Revision 1.8  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadastroEconomicoModalidadeLancamento.class.php"           );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMProcessoModLancInscEcon.class.php"                         );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadEconModalidadeMoeda.class.php"                          );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMCadEconModalidadeIndicador.class.php"                      );
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMAtividadeModalidadeLancamento.class.php"                   );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMModalidadeLancamento.class.php"                               );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"                                 );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONMoeda.class.php" );
include_once ( CAM_GT_MON_NEGOCIO."RMONIndicadorEconomico.class.php" );

/**
* Classe de regra de negócio para Modalidade de Lançamento - Inscrição Econômica
* Data de Criação: 30/01/2005

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMModalidadeInscricao
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
var $obRProcesso;
/**
* @access Private
* @var Object
*/
var $obTCEMProcessoModLancInscEcon;
/**
* @access Private
* @var Object
*/
var $obTCEMCadastroEconomicoModalidadeLancamento;
/**
* @access Private
* @var Object
*/
var $obTCEMCadEconModalidadeMoeda;
/**
* @access Private
* @var Object
*/
var $obTCEMCadEconModalidadeIndicador;
/**
* @access Private
* @var Object
*/
var $obRCEMModalidadeLancamento;
/**
* @access Private
* @var Object
*/
var $obRCEMInscricaoAtividade;
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
/**
* @access Private
* @var Array
*/
var $arAtividadesInscricao;

var $inCodModalidade;

//SETTERS
/**
* @access Public
* @param Date $valor
*/
function setDataInicio($valor) { $this->dtDataInicio = $valor;              }
/**
* @access Public
* @param String $valor
*/
function setMotivo($valor) { $this->stMotivo = $valor;                  }
/**
* @access Public
* @param Array $valor
*/
function setAtividadesInscricao($valor) { $this->arAtividadesInscricao = $valor;     }
/**
* @access Public
* @param Numeric $valor
*/
function setValor($valor) { $this->nuValor = $valor;                   }
/**
* @access Public
* @param Boolean $valor
*/
function setPercentual($valor) { $this->boPercentual = $valor;              }
function setCodModalidade($valor) { $this->inCodModalidade = $valor;           }

//GETTERS
/**
* @access Public
* @return Date
*/
function getDataInicio() { return $this->dtDataInicio;          }
/**
* @access Public
* @return String
*/
function getMotivo() { return $this->stMotivo;              }
/**
* @access Public
* @return Array
*/
function getAtividadesInscricao() { return $this->arAtividadesInscricao; }
/**
* @access Public
* @return Numeric
*/
function getValor() { return $this->nuValor;               }
/**
* @access Public
* @return Boolean
*/
function getPercentual() { return $this->boPercentual;          }
function getCodModalidade() { return $this->inCodModalidade;       }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMModalidadeInscricao()
{
    $this->obTCEMProcessoModLancInscEcon               = new TCEMProcessoModLancInscEcon;
    $this->obTCEMCadastroEconomicoModalidadeLancamento = new TCEMCadastroEconomicoModalidadeLancamento;
    $this->obTCEMCadEconModalidadeMoeda                = new TCEMCadEconModalidadeMoeda;
    $this->obTCEMCadEconModalidadeIndicador            = new TCEMCadEconModalidadeIndicador;
    $this->obRMONMoeda                                 = new RMONMoeda;
    $this->obRMONIndicadorEconomico                    = new RMONIndicadorEconomico;
    $this->obRCEMModalidadeLancamento                  = new RCEMModalidadeLancamento;
    $this->obRCEMInscricaoAtividade                    = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
    $this->obTransacao                                 = new Transacao;
    $this->obRProcesso                                 = new RProcesso;
    $this->arAtividadesInscricao                       = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)
/**
    * Seta dados na tabela para inclusao, alteracao e exclusao
    * @access Private
*/
function setarDados($arAtividadeInscricao)
{
    if ( $arAtividadeInscricao["stTipoValor"] == "percentual" )
        $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "percentual", true );
    else {
        if ($arAtividadeInscricao["stTipoValor"] == "moeda") {
            $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_modalidade", $arAtividadeInscricao["cod_modalidade"] );
            $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_atividade", $arAtividadeInscricao["cod_atividade"] );
            $this->obTCEMCadEconModalidadeMoeda->setDado( "inscricao_economica", $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
            $this->obTCEMCadEconModalidadeMoeda->setDado( "ocorrencia_atividade", $arAtividadeInscricao["ocorrencia_atividade"] );
            $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_moeda", $arAtividadeInscricao["inCodTipo"] );
            $this->obTCEMCadEconModalidadeMoeda->setDado( "dt_inicio", $this->dtDataInicio );
        } else {
            $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_modalidade", $arAtividadeInscricao["cod_modalidade"] );
            $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_atividade", $arAtividadeInscricao["cod_atividade"] );
            $this->obTCEMCadEconModalidadeIndicador->setDado( "inscricao_economica", $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
            $this->obTCEMCadEconModalidadeIndicador->setDado( "ocorrencia_atividade", $arAtividadeInscricao["ocorrencia_atividade"] );
            $this->obTCEMCadEconModalidadeIndicador->setDado( "dt_inicio", $this->dtDataInicio );
            $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_indicador", $arAtividadeInscricao["inCodTipo"] );
        }

        $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "percentual", false );
    }

    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "valor", $arAtividadeInscricao["nuValor"] );
    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "cod_modalidade"       , $arAtividadeInscricao["cod_modalidade"]                                            );
    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "cod_atividade"        , $arAtividadeInscricao["cod_atividade"]                                             );
    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "ocorrencia_atividade" , $arAtividadeInscricao["ocorrencia_atividade"]                                      );
    $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "dt_inicio"            , $this->dtDataInicio                                                                );
}

/**
* Inclui os dados setados na tabela de Modalidade da Inscrição Econômica de Lancamento
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function definirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->inclusao( $boTransacao );
    }

    //colocando dados na tabela cad_economico_modalidade_moeda
    if ($this->obTCEMCadEconModalidadeMoeda->getDado( "cod_moeda" )) {
        $this->obTCEMCadEconModalidadeMoeda->inclusao( $boTransacao );
    }else //colocando dados na tabela cad_economico_modalidade_indicador
        if ( $this->obTCEMCadEconModalidadeIndicador->getDado( "cod_indicador" ) ) {
            $this->obTCEMCadEconModalidadeIndicador->inclusao( $boTransacao );
        }

    //colocando dados na tabela processo_mod_lanc_insc_econ
    if (!$obErro->ocorreu() && $this->obRProcesso->getExercicio() && $this->obRProcesso->getCodigoProcesso() ) {
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_modalidade"      , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("cod_modalidade")      );
        $this->obTCEMProcessoModLancInscEcon->setDado("inscricao_economica" , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("inscricao_economica") );
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_atividade"       , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("cod_atividade")       );
        $this->obTCEMProcessoModLancInscEcon->setDado("ocorrencia_atividade", $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("ocorrencia_atividade"));
        $this->obTCEMProcessoModLancInscEcon->setDado("dt_inicio"           , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("dt_inicio")           );
        $this->obTCEMProcessoModLancInscEcon->setDado("ano_exercicio"       , $this->obRProcesso->getExercicio() );
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_processo"        , $this->obRProcesso->getCodigoProcesso() );
        $obErro = $this->obTCEMProcessoModLancInscEcon->inclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
* Altera os dados da Modalidade de Lancamento da Inscrição Econômica setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->alteracao( $boTransacao );
    }

    //colocando dados na tabela processo_mod_lanc_insc_econ
    if ($obErro->ocorreu() && $this->getAnoExercicio () && $this->getCodigoProcesso ()) {
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_modalidade"      , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("cod_modalidade")      );
        $this->obTCEMProcessoModLancInscEcon->setDado("inscricao_economica" , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("inscricao_economica") );
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_atividade"       , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("cod_atividade")       );
        $this->obTCEMProcessoModLancInscEcon->setDado("ocorrencia_atividade", $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("ocorrencia_atividade"));
        $this->obTCEMProcessoModLancInscEcon->setDado("dt_inicio"           , $this->obTCEMCadastroEconomicoModalidadeLancamento->getDado("dt_inicio")           );
        $this->obTCEMProcessoModLancInscEcon->setDado("ano_exercicio"       , $this->getAnoExercicio()                                                           );
        $this->obTCEMProcessoModLancInscEcon->setDado("cod_processo"        , $this->getCodigoProcesso()                                                         );
        $obErro = $this->obTCEMProcessoModLancInscEcon->inclusao( $boTransacao );
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
* Exclui os dados da Modalidade de Lancamento da Inscrição Econômica setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCEMCadEconModalidadeMoeda->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $obErro = $this->obTCEMCadEconModalidadeMoeda->exclusao( $boTransacao );
        if ( $obErro->ocorreu() ) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

            return $obErro;
        }

        $this->obTCEMCadEconModalidadeIndicador->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $obErro = $this->obTCEMCadEconModalidadeIndicador->exclusao( $boTransacao );
        if ( $obErro->ocorreu() ) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

            return $obErro;
        }

        $this->obTCEMProcessoModLancInscEcon->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $obErro = $this->obTCEMProcessoModLancInscEcon->exclusao( $boTransacao );
        if ( $obErro->ocorreu() ) {
            $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

            return $obErro;
        }

        $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
        $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
* Remove os dados da Modalidade de Lancamento da Inscrição Econômica setada para atualização
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function removerModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->exclusao( $boTransacao );
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
* Cadastra os dados da Modalidade de Lancamento da Inscrição Econômica setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function cadastrarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->verificaModalidadeAtividadeInscricao( $rsAtividadeCadastrada, $boTransacao );

        $arAtividadesCadastradas = array();
        $inCount = 0;
        while ( !$rsAtividadeCadastrada->eof() ) {
            $arAtividadesCadastradas[$inCount] = $rsAtividadeCadastrada->getCampo("cod_atividade");
            $rsAtividadeCadastrada->proximo();
            $inCount++;
        }

        foreach ($this->arAtividadesInscricao as $atividadeInscricao => $arAtividadeInscricao) {
            $rsAtividadeCadastrada->setPrimeiroElemento();
            if ($arAtividadeInscricao["cod_modalidade"] != "" && $arAtividadeInscricao["atualizar"] == true) {
                if ( in_array( $arAtividadeInscricao["cod_atividade"] , $arAtividadesCadastradas ) ) {
                    while ( !$rsAtividadeCadastrada->eof() ) {
                        if ( $rsAtividadeCadastrada->getCampo("cod_atividade") == $arAtividadeInscricao["cod_atividade"] && $rsAtividadeCadastrada->getCampo("inscricao_economica") == $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
                            if ( $rsAtividadeCadastrada->getCampo("stTipoValor") == "percentual" )
                                $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "percentual", true );
                            else {
                                if ( $rsAtividadeCadastrada->getCampo("stTipoValor") == "moeda" ) {
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_modalidade", $rsAtividadeCadastrada->getCampo("cod_modalidade") );
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_atividade", $rsAtividadeCadastrada->getCampo("cod_atividade") );
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "inscricao_economica", $rsAtividadeCadastrada->getCampo("inscricao_economica") );
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "ocorrencia_atividade", $rsAtividadeCadastrada->getCampo("ocorrencia_atividade") );
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "cod_moeda", $rsAtividadeCadastrada->getCampo("inCodTipo") );
                                    $this->obTCEMCadEconModalidadeMoeda->setDado( "dt_inicio", $rsAtividadeCadastrada->getCampo("dt-inicio") );

                                    $this->obTCEMCadEconModalidadeMoeda->exclusao( $boTransacao );
                                } else {
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_modalidade", $rsAtividadeCadastrada->getCampo("cod_modalidade") );
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_atividade", $rsAtividadeCadastrada->getCampo("cod_atividade") );
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "inscricao_economica", $rsAtividadeCadastrada->getCampo("inscricao_economica") );
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "ocorrencia_atividade", $rsAtividadeCadastrada->getCampo("ocorrencia_atividade") );
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "dt_inicio", $rsAtividadeCadastrada->getCampo("dt-inicio") );
                                    $this->obTCEMCadEconModalidadeIndicador->setDado( "cod_indicador", $rsAtividadeCadastrada->getCampo("inCodTipo") );

                                    $this->obTCEMCadEconModalidadeIndicador->exclusao( $boTransacao );
                                }

                                $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "percentual", false );
                            }

                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "valor", $rsAtividadeCadastrada->getCampo("nuValor") );

                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "cod_modalidade"       , $rsAtividadeCadastrada->getCampo("cod_modalidade")       );
                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "inscricao_economica"  , $rsAtividadeCadastrada->getCampo("inscricao_economica")  );
                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "cod_atividade"        , $rsAtividadeCadastrada->getCampo("cod_atividade")        );
                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "ocorrencia_atividade" , $rsAtividadeCadastrada->getCampo("ocorrencia_atividade") );
                            $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "dt_inicio"            , $rsAtividadeCadastrada->getCampo("dt-inicio")            );
                        }
                        $rsAtividadeCadastrada->proximo();
                    }

                    $obErro = $this->removerModalidade( $boTransacao );
                    $this->setarDados( $arAtividadeInscricao );
                    $obErro = $this->definirModalidade( $boTransacao );
                } else {
                    $this->setarDados( $arAtividadeInscricao );
                    $this->verificaBaixados( $arAtividadeInscricao, $rsBaixados, $boTransacao );
                    if ( $rsBaixados->getNumLinhas() < 1 ) {
                        $obErro = $this->definirModalidade();
                    } else {
                        $obErro->setDescricao("A Modalidade de Lançamento para esta Atividade de Inscrição Econômica nesta DATA DE INÍCIO foi dada como BAIXADA recentemente!");
                    }
                }
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
* Baixa a Modalidade de Lancamento da Inscrição Econômica setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarModalidade($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        foreach ($this->arAtividadesInscricao as $atividadeInscricao => $arAtividadeInscricao) {
            if ($arAtividadeInscricao["cod_modalidade"] != "") {
                $this->setarDados( $arAtividadeInscricao );
                $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "dt_baixa"     , date('d,m,Y')   );
                $this->obTCEMCadastroEconomicoModalidadeLancamento->setDado( "motivo_baixa" , $this->stMotivo );
                $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->alteracao( $boTransacao );
                if ( $obErro->ocorreu() ) {
                    break;
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMCadastroEconomicoModalidadeLancamento );

    return $obErro;
}

/**
    * Lista as Modalidades de Lancamento - Inscrição Econômica
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarModalidadeAtividadeInscricao(&$rsLista, $boTransacao = "", $boFiltro=true)
{
    $stFiltro = "";
    if ( $this->getCodModalidade() ) {
        $stFiltro .= " EML.COD_MODALIDADE = ".$this->getCodModalidade()." AND ";
    }

    if ( $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " ceml.inscricao_economica = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }

    if ($boFiltro == true) {
        $stFiltro .= " ceml.dt_baixa is null AND ";
        $stFiltro .= " ceml.motivo_baixa is null AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY ceml.inscricao_economica ";
    $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

function listarModalidadeAtividadeLancamento(&$rsLista, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodModalidade() ) {
        $stFiltro .= " atividade_modalidade_lancamento.cod_modalidade = ".$this->getCodModalidade()." AND ";
    }

    if ( $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() ) {
        $stFiltro .= " atividade_cadastro_economico.inscricao_economica = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obTCEMAtividadeModalidadeLancamento = new TCEMAtividadeModalidadeLancamento;
    $obErro = $obTCEMAtividadeModalidadeLancamento->listaAtividadeModalidadeLancamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Verifica a Modalidade de Lancamento por Inscrição Economica e Atividade
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaModalidadeAtividadeInscricao(&$rsLista, $boTransacao = "")
{
    $stFiltro .= " AND ceml.inscricao_economica = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." ";
    $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->recuperaModalidadeInscricao( $rsLista, $stFiltro, "", $boTransacao );

    return $obErro;
}

/**
    * Verifica se a Modalidade de Lancamento a ser cadastrada ja foi Baixada no mesmo dia
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaBaixados($arCadastrar, &$rsLista, $boTransacao = "")
{
    $stFiltro .= " ceml.inscricao_economica  = ".$this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica()." AND ";
    $stFiltro .= " ceml.cod_atividade        = ".$arCadastrar["cod_atividade"       ]." AND ";
    $stFiltro .= " ceml.cod_modalidade       = ".$arCadastrar["cod_modalidade"      ]." AND ";
    $stFiltro .= " ceml.ocorrencia_atividade = ".$arCadastrar["ocorrencia_atividade"]." AND ";
    $stFiltro .= " ceml.dt_inicio            = '".$this->dtDataInicio               ."' AND ";
    $stFiltro  = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    $obErro = $this->obTCEMCadastroEconomicoModalidadeLancamento->recuperaBaixados( $rsLista, $stFiltro, $boTransacao );

    return $obErro;
}

}

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
    * Classe de regra de negócio para Licença Especial
    * Data de Criação: 01/12/2004

    * @author Analista: Ricardo Lopes de Alencar
    * @author Desenvolvedor: Fábio Bertoldi Rodrigues

    * @package URBEM
    * @subpackage Regra

    * $Id: RCEMLicencaEspecial.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.12
*/

/*
$Log$
Revision 1.6  2006/09/15 12:13:58  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CEM_MAPEAMENTO."TCEMLicencaEspecial.class.php" );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMLicenca.class.php"              );
include_once ( CAM_GT_CEM_NEGOCIO."RCEMInscricaoAtividade.class.php"   );

/**
* Classe de regra de negócio para Licença Especial
* Data de Criação: 01/12/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCEMLicencaEspecial extends RCEMLicenca
{
/**
* @access Private
* @var Object
*/
var $obTCEMLicencaEspecial;
/**
* @access Private
* @var Object
*/
var $obRCEMInscricaoAtividade;
/**
* @access Private
* @var Array
*/
var $arAtividades;

//SETTERS
/**
* @access Public
* @param Array $valor
*/
function setAtividades($valor) { $this->arAtividades = $valor; }

//GETTERS
/**
* @access Public
* @return Array
*/
function getAtividades() { return $this->arAtividades; }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCEMLicencaEspecial()
{
    parent::RCEMLicenca();
    $this->obTCEMLicencaEspecial           = new TCEMLicencaEspecial;
    $this->obTransacao                     = new Transacao;
    $this->obRCEMInscricaoAtividade        = new RCEMInscricaoAtividade( new RCEMInscricaoEconomica );
    $this->arAtividades                    = array();
    $this->arHorario                       = array();
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Licença Especial
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function concederLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arHorario ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos um Horário!" );
        }
        if ( !$obErro->ocorreu() ) {
            if ( count( $this->arAtividades ) < 1  ) {
                $obErro->setDescricao( "Deve ser informado ao menos uma Atividade!" );
            }

            if ( !$obErro->ocorreu() ) {
                $obErro = parent::concederLicenca( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    foreach ($this->arAtividades as $novaAtividade => $arAtividades) {
                        $this->obTCEMLicencaEspecial->setDado( "cod_licenca"          , $this->inCodigoLicenca                );
                        $this->obTCEMLicencaEspecial->setDado( "exercicio"            , $this->stExercicio                    );
                        $this->obTCEMLicencaEspecial->setDado( "cod_atividade"        , $arAtividades["cod_atividade"]        );
                        $this->obTCEMLicencaEspecial->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                        $this->obTCEMLicencaEspecial->setDado( "ocorrencia_atividade" , $arAtividades["ocorrencia_atividade"] );
                        $this->obTCEMLicencaEspecial->setDado( "ocorrencia_licenca"   , "1"                                   );
                        $this->obTCEMLicencaEspecial->setDado( "dt_inicio"            , $this->dtDataInicio                   );
                        if ($this->dtDataTermino) {
                            $this->obTCEMLicencaEspecial->setDado( "dt_termino"       , $this->dtDataTermino                  );
                        }
                        $obErro = $this->obTCEMLicencaEspecial->inclusao( $boTransacao );
                        if ( $obErro->ocorreu() ) {
                            break;
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaEspecial );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Licença Especial
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarLicenca($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( count( $this->arHorario ) < 1  ) {
            $obErro->setDescricao( "Deve ser informado ao menos um Horário!" );
        }
        if ( !$obErro->ocorreu() ) {
            if ( count( $this->arAtividades ) < 1  ) {
                $obErro->setDescricao( "Deve ser informado ao menos uma Atividade!" );
            }
            if ( !$obErro->ocorreu() ) {
                $obErro = parent::alterarLicenca( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $stFiltro  = "";
                    $stFiltro .= " WHERE cod_licenca = ".$this->inCodigoLicenca;
                    $stFiltro .= " AND exercicio = '".$this->stExercicio."'";
                    $obErro = $this->obTCEMLicencaEspecial->recuperaTodos( $rsAtividadesCadastradas, $stFiltro, "", $boTransacao );
                    if ( !$obErro->ocorreu() ) {
                        $arAtividadesExclusao  = array();
                        $arAtividadesAlteracao = array();
                        $inCount = 0;
                        foreach ($this->arAtividades as $atividades => $arAtividades) {
                            $arAtividadesCadastrar[$inCount] = $arAtividades["cod_atividade"];
                            $inCount++;
                        }
                        $inCountExc = 0;
                        $inCountAlt = 0;
                        while ( !$rsAtividadesCadastradas->eof() ) {
                            if ( !in_array( $rsAtividadesCadastradas->getCampo("cod_atividade"),$arAtividadesCadastrar) ) {
                                $this->obTCEMLicencaEspecial->setDado( "cod_licenca"          , $this->inCodigoLicenca                                     );
                                $this->obTCEMLicencaEspecial->setDado( "exercicio"            , $this->stExercicio                                         );
                                $this->obTCEMLicencaEspecial->setDado( "cod_atividade"        , $rsAtividadesCadastradas->getCampo("cod_atividade")        );
                                $this->obTCEMLicencaEspecial->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                                $this->obTCEMLicencaEspecial->setDado( "ocorrencia_atividade" , $rsAtividadesCadastradas->getCampo("ocorrencia_atividade") );
                                $this->obTCEMLicencaEspecial->setDado( "ocorrencia_licenca"   , $rsAtividadesCadastradas->getCampo("ocorrencia_licenca")   );
                                $this->obTCEMLicencaEspecial->setDado( "dt_inicio"            , $rsAtividadesCadastradas->getCampo("dt_inicio")            );
                                $this->obTCEMLicencaEspecial->setDado( "dt_termino"           , date("d/m/Y")                                              );
                                $obErro = $this->obTCEMLicencaEspecial->alteracao( $boTransacao );
                                if ( $obErro->ocorreu() ) {
                                    break;
                                }
                                $arAtividadesExclusao[ $inCountExc ]  = $rsAtividadesCadastradas->getCampo("cod_atividade");
                                $inCountExc++;
                            } else {
                                $arAtividadesAlteracao[ $inCountAlt ] = $rsAtividadesCadastradas->getCampo("cod_atividade");
                                $inCountAlt++;
                            }
                            $rsAtividadesCadastradas->proximo();
                        }
                        if ( !$obErro->ocorreu() ) {
                            foreach ($this->arAtividades as $atividades => $arAtividades) {
                                if ( !in_array( $arAtividades["cod_atividade"],$arAtividadesAlteracao) ) {
                                    $this->obTCEMLicencaEspecial->setDado( "cod_licenca"          , $this->inCodigoLicenca                );
                                    $this->obTCEMLicencaEspecial->setDado( "exercicio"            , $this->stExercicio                    );
                                    $this->obTCEMLicencaEspecial->setDado( "cod_atividade"        , $arAtividades["cod_atividade"]        );
                                    $this->obTCEMLicencaEspecial->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                                    $this->obTCEMLicencaEspecial->setDado( "ocorrencia_atividade" , $arAtividades["ocorrencia_atividade"] );
                                    $this->obTCEMLicencaEspecial->setDado( "ocorrencia_licenca"   , "1"                                   );
                                    $this->obTCEMLicencaEspecial->setDado( "dt_inicio"            , $this->dtDataInicio                   );
                                    if ($this->dtDataTermino) {
                                        $this->obTCEMLicencaEspecial->setDado( "dt_termino"       , $this->dtDataTermino                  );
                                    }
                                    $obErro = $this->obTCEMLicencaEspecial->inclusao( $boTransacao );
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }
                                } else {
                                    $novaOcorrencia = $arAtividades["ocorrencia_atividade"];
                                    $novaOcorrencia++;
                                    $this->obTCEMLicencaEspecial->setDado( "cod_licenca"          , $this->inCodigoLicenca                );
                                    $this->obTCEMLicencaEspecial->setDado( "exercicio"            , $this->stExercicio                    );
                                    $this->obTCEMLicencaEspecial->setDado( "cod_atividade"        , $arAtividades["cod_atividade"]        );
                                    $this->obTCEMLicencaEspecial->setDado( "inscricao_economica"  , $this->obRCEMInscricaoAtividade->roRCEMInscricaoEconomica->getInscricaoEconomica() );
                                    $this->obTCEMLicencaEspecial->setDado( "ocorrencia_atividade" , $arAtividades["ocorrencia_atividade"] );
                                    $this->obTCEMLicencaEspecial->setDado( "ocorrencia_licenca"   , $novaOcorrencia                       );
                                    $this->obTCEMLicencaEspecial->setDado( "dt_inicio"            , $this->dtDataInicio                   );
                                    if ($this->dtDataTermino) {
                                        $this->obTCEMLicencaEspecial->setDado( "dt_termino"       , $this->dtDataTermino                  );
                                    }
                                    $obErro = $this->obTCEMLicencaEspecial->alteracao( $boTransacao );
                                    if ( $obErro->ocorreu() ) {
                                        break;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCEMLicencaEspecial );

    return $obErro;
}

/**
* Recupera do banco de dados as atividades da Licença selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarAtividades(&$rsAtividades, $boTransacao = "")
{
    $stFiltro = "";
//    $stFiltro .= "lce.dt_termino >= now()::date AND ";
    if ($this->inCodigoLicenca) {
        $stFiltro .= " lce.cod_licenca = ".$this->inCodigoLicenca." AND ";
    }
    if ($this->stExercicio) {
        $stFiltro .= " lce.exercicio = '".$this->stExercicio."' AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY cod_licenca ";
    $obErro = $this->obTCEMLicencaEspecial->recuperaRelacionamento( $rsAtividades, $stFiltro, $stOrder, $boTransacao );
//              $this->obTCEMLicencaEspecial->debug();
    return $obErro;
}

}

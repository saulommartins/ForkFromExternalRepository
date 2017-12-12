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
     * Classe de regra de negócio para unidade dependente
     * Data de Criação: 16/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMUnidadeDependente.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
                     uc-05.01.12
*/

/*
$Log$
Revision 1.12  2006/09/18 09:12:39  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMUnidadeDependente.class.php"         );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaUnidadeDependente.class.php"    );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaUnidadeDependente.class.php"     );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMUnidades.class.php"                  );
include_once ( CAM_GT_CIM_MAPEAMENTO."VCIMConstrucaoEdificacao.class.php"      );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMUnidadeAutonoma.class.php"                );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMEdificacao.class.php"                     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConstrucaoOutros.class.php"               );

class RCIMUnidadeDependente
{
/**
* @access Private
* @var Float
*/
var $flAreaUnidade;

/**
* @access Private
* @var String
*/
var $stJustificativa;

/**
* @access Private
* @var String
*/
var $stJustificativaReativar;

/**
* @access Private
* @var String
*/
var $stTipoUnidade;
/**
* @access Private
* @var String
*/
var $stTipoConstrucao;
/**
* @access Private
* @var Object
*/
var $obTCIMUnidadeDependente;
/**
* @access Private
* @var Object
*/
var $obTCIMAreaUnidadeDependente;
/**
* @access Private
* @var Object
*/
var $obVCIMUnidades;
/**
* @access Private
* @var Object
*/
var $obVCIMEdificacao;
/**
* @access Private
* @var Object
*/
var $roRCIMUnidadeAutonoma;
/**
* @access Private
* @var Object
*/
var $obRCIMEdificacao;
/**
* @access Private
* @var Object
*/
var $obRCIMConstrucaoOutros;
/**
* @access Private
* @var Object
*/
var $inCodigoConstrucao;
/**
* @access Private
* @var Object
*/
var $inCodigoConstrucaoDependente;
/**
* @access Private
* @var Object
*/
var $inCodigoTipo;
/**
* @access Private
* @var Object
*/
var $inNumeroInscricao;

/**
* @access Private
* @var Timestamp
*/
var $tmTimestampBaixaUnidade;

//SETTERS
/**
* @access Public
* @param Timestamp $valor
*/
function setTimestampBaixaUnidade($valor) { $this->tmTimestampBaixaUnidade      = $valor;   }

/**
* @access Public
* @param Integer $valor
*/
function setCodigoConstrucao($valor) { $this->inCodigoConstrucao           = $valor;   }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoConstrucaoDependente($valor) { $this->inCodigoConstrucaoDependente = $valor;   }
/**
* @access Public
* @param Integer $valor
*/
function setNumeroInscricao($valor) { $this->inNumeroInscricao = $valor;   }
/**
* @access Public
* @param Integer $valor
*/
function setCodigoTipo($valor) { $this->inCodigoTipo  = $valor;   }
/**
* @access Public
* @param Integer $valor
*/
function setAreaUnidade($valor) { $this->flAreaUnidade = $valor;   }

/**
* @access Public
* @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa = $valor; }

/**
* @access Public
* @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar = $valor; }

/**
* @access Public
* @param String $valor
*/
function setTipoUnidade($valor) { $this->stTipoUnidade = $valor;   }
/**
* @access Public
* @param String $valor
*/
function setTipoConstrucao($valor) { $this->stTipoConstrucao = $valor;   }

//--------------------------------------
//              GETTERS
//--------------------------------------
/**
* @access Public
* @return Timestamp
*/
function getTimestampBaixaUnidade() { return $this->tmTimestampBaixaUnidade;   }

/**
* @access Public
* @return Float
*/
function getAreaUnidade() { return $this->flAreaUnidade;   }

/**
* @access Public
* @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
* @access Public
* @return String
*/
function getJustificativa() { return $this->stJustificativa; }

/**
* @access Public
* @return String
*/
function getTipoUnidade() { return $this->stTipoUnidade;   }
/**
* @access Public
* @return String
*/
function getTipoConstrucao() { return $this->stTipoConstrucao;   }
/**
* @access Public
* @return Object
*/
function getTCIMUnidadeDependente() { return $this->obTCIMUnidadeDependente; }
/*******************/
/**
* @access Public
* @param Integer $valor
*/
function getCodigoConstrucao() { return $this->inCodigoConstrucao            ; }
/**
* @access Public
* @param Integer $valor
*/
function getCodigoConstrucaoDependente() { return $this->inCodigoConstrucaoDependente  ; }
/**
* @access Public
* @param Integer $valor
*/
function getNumeroInscricao() { return $this->inNumeroInscricao             ; }
/**
* @access Public
* @param Integer $valor
*/
function getCodigoTipo() { return  $this->inCodigoTipo                 ;  }

/******************/

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMUnidadeDependente(&$roRCIMUnidadeAutonoma)
{
    $this->obTCIMBaixaUnidadeDependente = new TCIMBaixaUnidadeDependente;
    $this->obTCIMUnidadeDependente     = new TCIMUnidadeDependente;
    $this->obTCIMAreaUnidadeDependente = new TCIMAreaUnidadeDependente;
    $this->roRCIMUnidadeAutonoma       = &$roRCIMUnidadeAutonoma;
    $this->obRCIMEdificacao            = new RCIMEdificacao;
    $this->obRCIMConstrucaoOutros      = new RCIMConstrucaoOutros;
    $this->obTransacao                 = new Transacao;
    $this->obVCIMUnidades              = new VCIMUnidades;
    $this->obRProcesso                 = new RProcesso;
    $this->obTCIMConstrucaoProcesso    = new TCIMConstrucaoProcesso;

}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Unidade Dependente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirUnidadeDependente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ( $this->obRCIMConstrucaoOutros->getDescricao() ) {
            $obErro = $this->obRCIMConstrucaoOutros->incluirConstrucao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $inCodConstrucao = ($this->obRCIMEdificacao->getCodigoConstrucao()) ? $this->obRCIMEdificacao->getCodigoConstrucao() : $this->obRCIMConstrucaoOutros->getCodigoConstrucao();
            $this->obTCIMUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao );
            $this->obTCIMUnidadeDependente->setDado( "inscricao_municipal" , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() );

            $this->obTCIMUnidadeDependente->setDado( "cod_construcao", $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obTCIMUnidadeDependente->setDado( "cod_tipo"      , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()  );

            $obErro = $this->obTCIMUnidadeDependente->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMAreaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() );

                $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
                //$this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
                $this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );

                $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao                                                      );
                $this->obTCIMAreaUnidadeDependente->setDado( "area"                      , $this->flAreaUnidade                                                  );
                $obErro = $this->obTCIMAreaUnidadeDependente->inclusao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Unidade Dependente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarUnidadeDependente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->obRCIMConstrucaoOutros->getDescricao() ) {
//            $flAreaUnidade = str_replace( ".", "", $this->getAreaUnidade() );
//            $flAreaUnidade = str_replace( ",", ".", $flAreaUnidade );
//           $flAreaConstruida = str_replace( ".", "", $this->obRCIMConstrucaoOutros->getAreaConstruida() );
//            $flAreaConstruida = str_replace( ",", ".", $flAreaConstruida );
//            if ($flAreaUnidade <= $flAreaConstruida) {
                 $obErro = $this->obRCIMConstrucaoOutros->alterarConstrucao( $boTransacao );
//            } else {
//                $obErro->setDescricao( "A área da unidade deve ser menor ou igual à área da edificação!" );
//            }
//            $obErro = $this->obRCIMConstrucaoOutros->alterarConstrucao( $boTransacao );
        } else {
//            $flAreaUnidade = str_replace( ".", "", $this->getAreaUnidade() );
//            $flAreaUnidade = str_replace( ",", ".", $flAreaUnidade );
//            $flAreaConstruida = str_replace( ".", "", $this->obRCIMEdificacao->getAreaConstruida() );
//            $flAreaConstruida = str_replace( ",", ".", $flAreaConstruida );
//            if ($flAreaUnidade <= $flAreaConstruida) {
                 $obErro = $this->obRCIMEdificacao->alterarConstrucao( $boTransacao );
//            } else {
//                $obErro->setDescricao( "A área da unidade deve ser menor ou igual à área da edificação!" );
//            }
//            $obErro = $this->obRCIMEdificacao->alterarEdificacao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() );

            $this->obTCIMUnidadeDependente->setDado( "cod_construcao", $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obTCIMUnidadeDependente->setDado( "cod_tipo"      , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()  );
            $inCodConstrucao = ($this->obRCIMEdificacao->getCodigoConstrucao()) ? $this->obRCIMEdificacao->getCodigoConstrucao() : $this->obRCIMConstrucaoOutros->getCodigoConstrucao();
            $this->obTCIMUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao );
            $obErro = $this->obTCIMUnidadeDependente->alteracao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMAreaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() );
                $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao" , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
                $this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"       , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
                $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao      );
                $this->obTCIMAreaUnidadeDependente->setDado( "area"                      , $this->flAreaUnidade  );
                $obErro = $this->obTCIMAreaUnidadeDependente->alteracao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Unidade Dependente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirUnidadeDependente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMAreaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()  );
            $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
            $this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
            $inCodConstrucao = ($this->obRCIMEdificacao->getCodigoConstrucao()) ? $this->obRCIMEdificacao->getCodigoConstrucao() : $this->obRCIMConstrucaoOutros->getCodigoConstrucao();
            $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao   );
            $obErro = $this->obTCIMAreaUnidadeDependente->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTCIMBaixaUnidadeDependente = new TCIMBaixaUnidadeDependente;
                $obTCIMBaixaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()  );
                $obTCIMBaixaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
                $obTCIMBaixaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
                $obTCIMBaixaUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao );
                $obErro = $obTCIMBaixaUnidadeDependente->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()  );
                    $this->obTCIMUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
                    $this->obTCIMUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
                    $this->obTCIMUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao );
                    $obErro = $this->obTCIMUnidadeDependente->exclusao( $boTransacao );
                    if ( $this->obRCIMConstrucaoOutros->getCodigoConstrucao() ) {
                        $obErro = $this->obRCIMConstrucaoOutros->excluirConstrucao( $boTransacao );
                    } else {
                        $obErro = $this->obRCIMEdificacao->excluirEdificacao( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}

/**
* Reativa a Unidade Dependente setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function reativarUnidadeDependente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaUnidadeDependente->setDado( "dt_termino", $dtdiaHOJE );
        $this->obTCIMBaixaUnidadeDependente->setDado( "timestamp", $this->tmTimestampBaixaUnidade );
        $this->obTCIMBaixaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()        );
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao()   );
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()         );
        $inCodConstrucao = ($this->obRCIMEdificacao->getCodigoConstrucao()) ? $this->obRCIMEdificacao->getCodigoConstrucao() : $this->obRCIMConstrucaoOutros->getCodigoConstrucao();
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_construcao_dependente", $inCodConstrucao );
        $this->obTCIMBaixaUnidadeDependente->setDado( "justificativa", $this->stJustificativa );
        $this->obTCIMBaixaUnidadeDependente->setDado( "justificativa_termino", $this->stJustificativaReativar );
        $obErro = $this->obTCIMBaixaUnidadeDependente->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obRCIMConstrucaoOutros->setDataConstrucao( $this->tmTimestampBaixaUnidade );
            $this->obRCIMConstrucaoOutros->setCodigoConstrucao( $inCodConstrucao );
            $this->obRCIMConstrucaoOutros->setJustificativa( $this->stJustificativa );
            $this->obRCIMConstrucaoOutros->setJustificativaReativar( $this->stJustificativaReativar );
            $obErro = $this->obRCIMConstrucaoOutros->reativarConstrucao( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}

/**
* Baixa a Unidade Dependente setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarUnidadeDependente($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaUnidadeDependente->setDado( "dt_inicio", $dtdiaHOJE );
        $this->obTCIMBaixaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()        );
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao()   );
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()         );
//      $this->obTCIMBaixaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->getCodigoTipo()         );
        $inCodConstrucao = ($this->obRCIMEdificacao->getCodigoConstrucao()) ? $this->obRCIMEdificacao->getCodigoConstrucao() : $this->obRCIMConstrucaoOutros->getCodigoConstrucao();
        $this->obTCIMBaixaUnidadeDependente->setDado( "cod_construcao_dependente" , $inCodConstrucao                                                        );
        $this->obTCIMBaixaUnidadeDependente->setDado( "justificativa"             , $this->stJustificativa                                                  );
        $obErro = $this->obTCIMBaixaUnidadeDependente->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->getTipoConstrucao() == "Edificacao" ) {
                $this->obRCIMEdificacao->setCodigoConstrucao( $inCodConstrucao );
                $this->obRCIMEdificacao->setJustificativa( $this->stJustificativa );
                $obErro = $this->obRCIMEdificacao->baixarConstrucao( $boTransacao );
            } elseif ( $this->getTipoConstrucao() == "ConstrucaoOutras" ) {
                $this->obRCIMConstrucaoOutros->setCodigoConstrucao( $inCodConstrucao );
                $this->obRCIMConstrucaoOutros->setJustificativa( $this->stJustificativa );
                $obErro = $this->obRCIMConstrucaoOutros->baixarConstrucao( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}

/**
* Lista as Unidades Dependentes conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarUnidadesDependentes(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    $stFiltro .= " cod_construcao_dependente > 0 AND ";
    if ( $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " inscricao_municipal = ".$this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRCIMConstrucaoOutros->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao_dependente = ".$this->obRCIMConstrucaoOutros->getCodigoConstrucao()." AND ";
    }
    if ( $this->obRCIMEdificacao->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao_dependente = ".$this->obRCIMEdificacao->getCodigoConstrucao()." AND ";
    }
    if ( $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao = ".$this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao()." AND ";
    }
/*    if ( $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo() ) {
        $stFiltro .= " cod_tipo = ".$this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()." AND ";
    }      */
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_municipal ";
    $obErro = $this->obVCIMUnidades->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
* Recupera do banco de dados os dados da Unidade Dependente selecionada
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function consultarUnidadeDependente($boTransacao = "")
{
    $stFiltro = "";
    $stFiltro .= " cod_construcao_dependente > 0 AND ";
    if ( $this->obRCIMImovel->getInscricaoMunicipal() ) {
        $stFiltro .= " inscricao_municipal = ".$this->obRCIMImovel->getInscricaoMunicipal()." AND ";
    }
    if ( $this->obRCIMConstrucaoOutros->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao_dependente = ".$this->obRCIMConstrucaoOutros->getCodigoConstrucao()." AND ";
    }
    if ( $this->obRCIMEdificacao->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao_dependente = ".$this->obRCIMEdificacao->getCodigoConstrucao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_municipal ";
    $obErro = $this->obVCIMUnidades->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
        $this->roRCIMUnidadeAutonoma->setInscricaoMunicipal                 ( $rsRecordSet->getCampo( "inscricao_municipal"       ) );
        $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoTipo       ( $rsRecordSet->getCampo( "cod_tipo"                  ) );
        $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->setCodigoConstrucao ( $rsRecordSet->getCampo( "cod_construcao"            ) );
        $this->roRCIMUnidadeAutonoma->obRCIMTipoEdificacao->setNomeTipo     ( $rsRecordSet->getCampo( "nom_tipo"                  ) );
        if ( $this->obRCIMConstrucaoOutros->getCodigoConstrucao() ) {
            $this->obRCIMConstrucaoOutros->setCodigoConstrucao              ( $rsRecordSet->getCampo( "cod_construcao_dependente" ) );
        }
        if ( $this->obRCIMEdificacao->getCodigoConstrucao() ) {
            $this->obRCIMEdificacao->setCodigoConstrucao                    ( $rsRecordSet->getCampo( "cod_construcao_dependente" ) );
        }
        $this->flAreaUnidade                                                = $rsRecordSet->getCampo( "area"                        );
        $this->stTipoUnidade                                                = $rsRecordSet->getCampo( "tipo_unidade"                );

        return $obErro;
    }
}

function incluirReforma($boTransacao = "")
{
    $this->obTCIMAreaUnidadeDependente->setDado( "inscricao_municipal"       , $this->getNumeroInscricao            ()  );
    $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao"            , $this->getCodigoConstrucao           ()  );
    $this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"                  , $this->getCodigoTipo                 ()  );
    $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao_dependente" , $this->getCodigoConstrucaoDependente ()  );
    $this->obTCIMAreaUnidadeDependente->setDado( "area"                      , $this->getAreaUnidade                ()  );
    $obErro = $this->obTCIMAreaUnidadeDependente->inclusao( $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->salvarProcesso($boTransacao);
    }

    return $obErro;
}

/**
* Verifica de deve incluir, alterar ou excluir o processo informado
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarProcesso($boTransacao = "")
{
   $obErro = new Erro;
   if ( $this->obRProcesso->getCodigoProcesso() ) {
       $obErro = $this->obRProcesso->validarProcesso( $boTransacao );
   }
   if ( !$obErro->ocorreu() ) {
       $stFiltro  = " WHERE ";
       $stFiltro .= "     cod_construcao = ".$this->inCodigoConstrucaoDependente." ";
       $obErro = $this->obTCIMConstrucaoProcesso->recuperaTodos( $rsProcesso, $stFiltro, "", $boTransacao );
       if ( !$obErro->ocorreu() ) {
           $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao", $this->inCodigoConstrucaoDependente );
           $this->obTCIMConstrucaoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
           $this->obTCIMConstrucaoProcesso->setDado( "exercicio"     , $this->obRProcesso->getExercicio()  );
           if ( !$rsProcesso->eof()  and $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
               $obErro = $this->obTCIMConstrucaoProcesso->alteracao( $boTransacao );
           } elseif ( $rsProcesso->eof() and $this->obRProcesso->getCodigoProcesso() and $this->obRProcesso->getExercicio() ) {
               $obErro = $this->obTCIMConstrucaoProcesso->inclusao( $boTransacao );
           } elseif ( !$rsProcesso->eof() ) {
               $this->obTCIMConstrucaoProcesso->setCampoCod( "cod_construcao" );
               $this->obTCIMConstrucaoProcesso->setComplementoChave( "" );
               $obErro = $this->obTCIMConstrucaoProcesso->exclusao( $boTransacao );
           }
       }
   }

   return $obErro;
}

/**
* Incluir reforma de unidade dependente
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirReformaConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMAreaUnidadeDependente->setDado( "inscricao_municipal"       , $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() );
        $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao"            , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() );
        $this->obTCIMAreaUnidadeDependente->setDado( "cod_tipo"                  , $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()       );
        $this->obTCIMAreaUnidadeDependente->setDado( "cod_construcao_dependente" , $this->getCodigoConstrucaoDependente ()                                   );
        $this->obTCIMAreaUnidadeDependente->setDado( "area"                      , $this->flAreaUnidade                                                  );
        $obErro = $this->obTCIMAreaUnidadeDependente->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() )
            $obErro = $this->obRCIMConstrucaoOutros->salvarProcesso($boTransacao);
    }

    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMUnidadeDependente );

    return $obErro;
}
/**
* Lista as Unidades Dependentes conforme o filtro setado
* @access Public
* @param  Object $rsRecordSet Objeto RecordSet preenchido com os dados selecionados
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function listarUnidadesDependentesBaixa(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = " AND ";
    $stFiltro .= " cod_construcao > 0 AND ";
    if ( $this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao() ) {
        $stFiltro .= " inscricao_municipal = ".$this->roRCIMUnidadeAutonoma->roRCIMImovel->getNumeroInscricao()." AND ";
    }
    if ( $this->obRCIMConstrucaoOutros->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao = ".$this->obRCIMConstrucaoOutros->getCodigoConstrucao()." AND ";
    }
    if ( $this->obRCIMEdificacao->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao = ".$this->obRCIMEdificacao->getCodigoConstrucao()." AND ";
    }
    if ( $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao() ) {
        $stFiltro .= " cod_construcao_autonoma = ".$this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoConstrucao()." AND ";
    }
/*    if ( $this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo() ) {
        $stFiltro .= " cod_tipo = ".$this->roRCIMUnidadeAutonoma->obRCIMEdificacao->getCodigoTipo()." AND ";
    }      */
    if ($stFiltro) {
        $stFiltro = " ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }
    $stOrder = " ORDER BY inscricao_municipal ";
    $this->obVCIMEdificacao              = new VCIMConstrucaoEdificacao;
    $obErro = $this->obVCIMEdificacao->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    //$this->obVCIMEdificacao->debug();
    return $obErro;
}

} // fecha classe

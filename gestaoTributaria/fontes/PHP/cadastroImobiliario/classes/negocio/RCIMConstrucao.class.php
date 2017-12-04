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
     * Classe de regra de negócio para construção
     * Data de Criação: 105/11/2004

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
     * @author Desenvolvedor: Fábio Bertoldi Rodrigues

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMConstrucao.class.php 59612 2014-09-02 12:00:51Z gelson $

     * Casos de uso: uc-05.01.11
                     uc-05.01.12
*/

/*
$Log$
Revision 1.8  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucao.class.php"           );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoCondominio.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMConstrucaoProcesso.class.php"   );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMAreaConstrucao.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMBaixaConstrucao.class.php"      );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMDataConstrucao.class.php"       );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMCondominio.class.php"                );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                     );

/**
* Classe de regra de negócio para Construção
* Data de Criação: 05/11/2004

* @author Analista: Ricardo Lopes de Alencar
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Fábio Bertoldi Rodrigues

* @package URBEM
* @subpackage Regra
*/

class RCIMConstrucao
{
/**
* @access Private
* @var Integer
*/
var $inCodigoConstrucao;
/*
* @access Private
* @var Float
*/
var $flAreaConstruida;
/**
* @access Private
* @var Date
*/
var $dtDataBaixa;

/**
* @access Private
* @var String
*/
var $stJustificativaReativar;

/**
* @access Private
* @var String
*/
var $stJustificativa;
/**
* @access Private
* @var Integer
*/
var $inCodigoProcesso;
/**
* @access Private
* @var Integer
*/
var $inExercicioProcesso;
/**
* @access Private
* @var Object
*/
var $stDtConstrucao;
/**
* @access Private
* @var String
*/
var $stTipoUnidade;
/**
* @access Private
* @var String
*/
var $inCodigoUnidadeAutonoma;
/**
* @access Private
* @var Object
*/
var $obTCIMConstrucao;
/**
* @access Private
* @var Object
*/
var $obTCIMConstrucaoCondominio;
/**
* @access Private
* @var Object
*/
var $obTCIMConstrucaoProcesso;
/**
* @access Private
* @var Object
*/
var $obTCIMAreaConstrucao;
/**
* @access Private
* @var Object
*/
var $obTCIMBaixaConstrucao;
/**
* @access Private
* @var Object
*/
var $obTCIMDataConstrucao;
/**
* @access Private
    * @var Object
*/
var $obRCIMCondominio;
/**
* @access Private
* @var Object
*/
var $obRProcesso;
/**
* @access Private
* @var Timestamp
*/
var $tmTimestampConstrucao;
/**
* @access Private
* @var Boolean
*/
var $boSistema;

//SETTERS
/**
* @access Public
* @param Integer $valor
*/
function setCodigoConstrucao($valor) { $this->inCodigoConstrucao = $valor; }
/**
* @access Public
* @param Float $valor
*/
function setAreaConstruida($valor) { $this->flAreaConstruida   = $valor; }
/**
* @access Public
* @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa        = $valor; }
/**
* @access Public
* @param Date $valor
*/
function setDataConstrucao($valor) { $this->stDtConstrucao     = $valor; }
/**
* @access Public
* @param String $valor
*/
function setJustificativa($valor) { $this->stJustificativa    = $valor; }

/**
* @access Public
* @param String $valor
*/
function setJustificativaReativar($valor) { $this->stJustificativaReativar    = $valor; }

/**
* @access Public
* @param String $valor
*/
function setCodigoProcesso($valor) { $this->inCodigoProcesso = $valor; }
/**
* @access Public
* @param String $valor
*/
function setExercicioProcesso($valor) { $this->inExercicioProcesso = $valor; }
/**
* @access Public
* @param String $valor
*/
function setTimestampConstrucao($valor) { $this->tmTimestampConstrucao = $valor; }
/**
* @access Public
* @param String $valor
*/
function setTipoUnidade($valor) { $this->stTipoUnidade = $valor; }
/**
* @access Public
* @param String $valor
*/
function setUnidadeAutonoma($valor) { $this->inCodigoUnidadeAutonoma = $valor; }
/**
* @access Public
* @param String $valor
*/
function setSistema($valor) { $this->boSistema = $valor;   }

//GETTERS
/**
* @access Public
* @return Integer
*/
function getCodigoConstrucao() { return $this->inCodigoConstrucao; }
/**
* @access Public
* @return Integer
*/
function getAreaConstruida() { return $this->flAreaConstruida;   }
/**
* @access Public
* @return Float
*/
function getDataBaixa() { return $this->stDataBaixa;        }
/**
* @access Public
* @return Float
*/
function getDataConstrucao() { return $this->stDtConstrucao;        }

/**
* @access Public
* @return String
*/
function getJustificativaReativar() { return $this->stJustificativaReativar; }

/**
* @access Public
* @return String
*/
function getJustificativa() { return $this->stJustificativa;    }
/**
* @access Public
* @return String
*/
function getTimestampConstrucao() { return $this->tmTimestampConstrucao;    }
/**
* @access Public
* @return String
*/
function getTipoUnidade() { return $this->stTipoUnidade; }
/**
* @access Public
* @return String
*/
function getUnidadeAutonoma() { return $this->inCodigoUnidadeAutonoma; }
/**
* @access Public
* @param Integer $valor
*/
function getSistema() { return  $this->boSistema                    ;  }

//METODO CONSTRUTOR
/**
* Método construtor
* @access Private
*/
function RCIMConstrucao()
{
    $this->obTCIMConstrucao           = new TCIMConstrucao;
    $this->obTCIMConstrucaoCondominio = new TCIMConstrucaoCondominio;
    $this->obTCIMConstrucaoProcesso   = new TCIMConstrucaoProcesso;
    $this->obTCIMDataConstrucao       = new TCIMDataConstrucao;
    $this->obTCIMAreaConstrucao       = new TCIMAreaConstrucao;
    $this->obTCIMBaixaConstrucao      = new TCIMBaixaConstrucao;
    $this->obRCIMCondominio           = new RCIMCondominio;
    $this->obRProcesso                = new RProcesso;
    $this->obTransacao                = new Transacao;
    $obErro                           = new Erro;

    $this->setSistema(false);
}

// METODOS FUNCIONAIS (inclusao,alteracao,exclusao...)

/**
* Inclui os dados setados na tabela de Construcao
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMConstrucao->proximoCod( $this->inCodigoConstrucao, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $obErro = $this->obTCIMConstrucao->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMDataConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao);
                $this->obTCIMDataConstrucao->setDado( "data_construcao", $this->stDtConstrucao);
                $obErro = $this->obTCIMDataConstrucao->inclusao ( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    if ($this->flAreaConstruida) {
                        $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
                        $this->obTCIMAreaConstrucao->setDado( "area_real"      , $this->flAreaConstruida   );
                        if ( $this->getTipoUnidade() == 'Dependente' ) {
                            $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->getUnidadeAutonoma() );
                            $this->obTCIMAreaConstrucao->setDado( "area_real"      , $this->flAreaConstruida     );
                            $obErro = $this->obTCIMAreaConstrucao->alteracao( $boTransacao );
                        } else {
                            $obErro = $this->obTCIMAreaConstrucao->inclusao( $boTransacao );
                        }
                    }
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
                            $this->obTCIMConstrucaoCondominio->setDado( "cod_construcao", $this->inCodigoConstrucao                      );
                            $this->obTCIMConstrucaoCondominio->setDado( "cod_condominio", $this->obRCIMCondominio->getCodigoCondominio() );
                            $obErro = $this->obTCIMConstrucaoCondominio->inclusao( $boTransacao );
                        }
                        if ( !$obErro->ocorreu() ) {
                            $obErro = $this->salvarProcesso( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucao );

    return $obErro;
}

/**
* Altera os dados setados na tabela de Construcao
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function alterarConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
        $obErro = $this->obTCIMConstrucao->alteracao( $boTransacao );
        if (!$obErro->ocorreu() ) {
            $this->obTCIMDataConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
            $this->obTCIMDataConstrucao->setDado( "data_construcao", $this->stDtConstrucao   );
            $this->obTCIMDataConstrucao->recuperaPorChave( $rsTMPConst, $boTransacao );
            if ( $rsTMPConst->Eof() )
                $obErro = $this->obTCIMDataConstrucao->inclusao( $boTransacao );
            else
                $obErro = $this->obTCIMDataConstrucao->alteracao( $boTransacao );

            if ( !$obErro->ocorreu() ) {
                 if ($this->flAreaConstruida) {
                    $obErro = $this->consultarTimestamp($boTransacao);
                    if ( !$obErro->ocorreu() ) {
                        if ( $this->getTipoUnidade() == 'Dependente' ) {
                            $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->getUnidadeAutonoma() );
                            $this->obTCIMAreaConstrucao->setDado( "area_real"      , $this->flAreaConstruida     );
                            $obErro = $this->obTCIMAreaConstrucao->alteracao( $boTransacao );
                        } else {
                            $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao      );
                            $this->obTCIMAreaConstrucao->setDado( "timestamp"      , $this->getTimestampConstrucao()  );
                            $this->obTCIMAreaConstrucao->setDado( "area_real"      , $this->flAreaConstruida        );
                            $obErro = $this->obTCIMAreaConstrucao->alteracao( $boTransacao );
                        }
                    }
                 }
                 if ( !$obErro->ocorreu() ) {
                    if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
                        $this->obTCIMConstrucaoCondominio->setDado( "cod_construcao", $this->inCodigoConstrucao );
                        $this->obTCIMConstrucaoCondominio->setDado( "cod_condominio", $this->obRCIMCondominio->getCodigoCondominio() );
                        $obErro = $this->obTCIMConstrucaoCondominio->alteracao( $boTransacao );
                    }
                    if ( !$obErro->ocorreu() ) {
                        $obErro = $this->salvarProcesso( $boTransacao );
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucao );

    return $obErro;
}

/**
* Exclui os dados setados na tabela de Construcao
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function excluirConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $this->obRProcesso->getCodigoProcesso() ) {
            $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao", $this->inCodigoConstrucao               );
            $this->obTCIMConstrucaoProcesso->setDado( "cod_processo"  , $this->obRProcesso->getCodigoProcesso() );
            $this->obTCIMConstrucaoProcesso->setDado( "exercicio"     , $this->obRProcesso->getExercicio()      );
            $obErro = $this->obTCIMConstrucaoProcesso->exclusao( $boTransacao );
        }
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRCIMCondominio->getCodigoCondominio() ) {
                $this->obTCIMConstrucaoCondominio->setDado( "cod_construcao", $this->inCodigoConstrucao         );
                $this->obTCIMConstrucaoCondominio->setDado( "cod_condominio", $this->obRCIMCondominio->getCodigoCondominio() );
                $obErro = $this->obTCIMConstrucaoCondominio->exclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMDataConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
                $this->obTCIMDataConstrucao->setDado( "data_construcao", $this->stDtConstrucao );
                $obErro = $this->obTCIMDataConstrucao->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $stComplenmentoChave = $this->obTCIMAreaConstrucao->getComplementoChave();
                    $this->obTCIMAreaConstrucao->setComplementoChave('cod_construcao');
                    $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
                    $obErro = $this->obTCIMAreaConstrucao->exclusao( $boTransacao );
                    $this->obTCIMAreaConstrucao->setComplementoChave($stComplenmentoChave);
                    if ( !$obErro->ocorreu() ) {
                        $obTCIMBaixaConstrucao = new TCIMBaixaConstrucao;
                        $obTCIMBaixaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
                        $obErro = $obTCIMBaixaConstrucao->exclusao( $boTransacao );

                        if ( !$obErro->ocorreu() ) {
                            $this->obTCIMConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
                            $obErro = $this->obTCIMConstrucao->exclusao( $boTransacao );
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucao );

    return $obErro;
}

/**
* Baixa a Construção setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function baixarConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaConstrucao->setDado( "dt_inicio", $dtdiaHOJE );
        $this->obTCIMBaixaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
        $this->obTCIMBaixaConstrucao->setDado( "justificativa"  , $this->stJustificativa    );
        $this->obTCIMBaixaConstrucao->setDado( "sistema"        , $this->boSistema          );
        $obErro = $this->obTCIMBaixaConstrucao->inclusao( $boTransacao );
        if (!$obErro->ocorreu() ) {
            $this->salvarProcesso($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaConstrucao );

    return $obErro;
}

/**
* Reativa a Construção setada
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function reativarConstrucao($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $dtdiaHOJE = date ("d-m-Y");
        $this->obTCIMBaixaConstrucao->setDado( "dt_termino"     , $dtdiaHOJE                );
        $this->obTCIMBaixaConstrucao->setDado( "timestamp"      , $this->stDtConstrucao     );
        $this->obTCIMBaixaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
        $this->obTCIMBaixaConstrucao->setDado( "justificativa"  , $this->stJustificativa    );
        $this->obTCIMBaixaConstrucao->setDado( "justificativa_termino"  , $this->stJustificativaReativar );
        $this->obTCIMBaixaConstrucao->setDado( "sistema"        , $this->boSistema          );
        $obErro = $this->obTCIMBaixaConstrucao->alteracao( $boTransacao );

        if (!$obErro->ocorreu() ) {
            $this->salvarProcesso($boTransacao);
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMBaixaConstrucao );

    return $obErro;
}

/**
* Verifica de deve incluir, alterar ou excluir o processo informado
* para construção e edificação deve ser sempre inclusao
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function salvarProcesso($boTransacao = "")
{
    $obErro = new Erro;
    if ( $this->obRProcesso->getCodigoProcesso() ) {
        $obErro = $this->obRProcesso->validarProcesso( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMConstrucaoProcesso->setDado( "cod_construcao", $this->inCodigoConstrucao );
            $this->obTCIMConstrucaoProcesso->setDado( "cod_processo", $this->obRProcesso->getCodigoProcesso() );
            $this->obTCIMConstrucaoProcesso->setDado( "exercicio"     , $this->obRProcesso->getExercicio()  );
            $obErro = $this->obTCIMConstrucaoProcesso->inclusao( $boTransacao );
       }
   }

   return $obErro;
}
/**
* Inclui reforma
* @access Public
* @param  Object $obTransacao Parâmetro Transação
* @return Object Objeto Erro
*/
function incluirReforma($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMAreaConstrucao->setDado( "cod_construcao" , $this->inCodigoConstrucao );
        $this->obTCIMAreaConstrucao->setDado( "area_real"      , $this->flAreaConstruida   );
        $obErro = $this->obTCIMAreaConstrucao->inclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->salvarProcesso( $boTransacao );
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMConstrucao );

    return $obErro;
}

function listarProcessos(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ( $this->getCodigoLote()) {
        $stFiltro .= " cp.cod_construcao = ".$this->getCodigoConstrucao()." AND ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $stOrdem = " ORDER BY cp.timestamp";
    $obErro = $this->obTCIMConstrucao->recuperaRelacionamentoProcesso( $rsRecordSet, $stFiltro,$stOrdem, $boTransacao );

    return $obErro;
}
function consultarTimestamp($boTransacao = "")
{
    if (!$this->inCodigoConstrucao) {
        $obErro->setDescricao("A chave(cod_construcao) da tabela imobiliario.area_construcao deve estar setada para uso deste método");
    } else {
        $stFiltro = "";
        if ($this->inCodigoConstrucao) {
            $stFiltro .= "cod_construcao = ".$this->inCodigoConstrucao." AND ";
        }
        if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
        }
        $obErro = $this->obTCIMAreaConstrucao->recuperaTimestampConstrucao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        //$this->obTCIMAreaConstrucao->debug();
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $this->setTimestampConstrucao           ( $rsRecordSet->getCampo( "timestamp_construcao"    ) );
        }
    }

    return $obErro;
}

}

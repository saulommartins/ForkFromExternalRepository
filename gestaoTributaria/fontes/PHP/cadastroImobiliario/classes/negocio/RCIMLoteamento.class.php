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
     * Classe de regra de negócio para loteamento
     * Data de Criação: 16/03/2005

     * @author Analista: Ricardo Lopes de Alencar
     * @author Desenvolvedor: Tonismar Régis Bernardou

     * @package URBEM
     * @subpackage Regra

    * $Id: RCIMLoteamento.class.php 61291 2014-12-30 15:55:05Z evandro $

     * Casos de uso: uc-05.01.15
*/

/*
$Log$
Revision 1.12  2006/09/18 09:12:40  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteamento.class.php"           );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteamentoLoteOrigem.class.php" );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMLoteLoteamento.class.php"       );
include_once ( CAM_GT_CIM_MAPEAMENTO."TCIMProcessoLoteamento.class.php"   );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMConfiguracao.class.php"              );
include_once ( CAM_GA_PROT_NEGOCIO."RProcesso.class.php"                     );
include_once ( CAM_GT_CIM_NEGOCIO."RCIMLote.class.php"                      );

class RCIMLoteamento
{
/**
    * @access Private
    * @var Integer
*/
var $inCodigoLoteamento;
/**
    * @access Private
    * @var Integer
*/
var $inCodLote;
/**
    * @access Private
    * @var String
*/
var $stNomeLoteamento;
/**
    * @access Private
    * @var Integer
*/
var $inLoteOrigem;
/**
    * @access Private
    * @var Date
*/
var $dtDataInclusao;
/**
    * @access Private
    * @var Date
*/
var $dtDataLiberacao;
/**
    * @access Private
    * @var Date
*/
var $dtDataAprovacao;
/**
    * @access Private
    * @var Float
*/
var $flAreaLoteamento;
/**
    * @access Private
    * @var Float
*/
var $flAreaRemanescente;
/**
    * @access Private
    * @var Float
*/
var $flAreaTotalLotes;
/**
    * @access Private
    * @var Float
*/
var $flAreaLogradouro;
/**
    * @access Private
    * @var Float
*/
var $flAreaComunitaria;
/**
    * @access Private
    * @var Integer
*/
var $inQuantidadeLotes;
/**
    * @access Private
    * @var Integer
*/
var $inLotesCaucionados;
/**
    * @access Private
    * @var Date
*/
var $dtDataBaixa;
/**
    * @access Private
    * @var String
*/
var $stMotivoBaixa;
/**
    * @access Private
    * @var String
*/
var $inCodigoProcesso;
/**
    * @access Private
    * @var Array
*/
var $arLote;

/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoLoteamento($valor) { $this->inCodigoLoteamento = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setNomeLoteamento($valor) { $this->stNomeLoteamento   = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setLoteOrigem($valor) { $this->inLoteOrigem = $valor;       }
/**
    * @access Public
    * @param Date $valor
*/
function setDataInclusao($valor) { $this->dtDataInclusao = $valor;     }
/**
    * @access Public
    * @param Date $valor
*/
function setDataLiberacao($valor) { $this->dtDataLiberacao = $valor;    }
/**
    * @access Public
    * @param Date $valor
*/
function setDataAprovacao($valor) { $this->dtDataAprovacao = $valor;    }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaLoteamento($valor) { $this->flAreaLoteamento = $valor;   }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaRemanescente($valor) { $this->flAreaRemanescente = $valor; }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaTotalLotes($valor) { $this->flAreaTotalLotes = $valor;   }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaLogradouro($valor) { $this->flAreaLogradouro = $valor;  }
/**
    * @access Public
    * @param Float $valor
*/
function setAreaComunitaria($valor) { $this->flAreaComunitaria = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setQuantidadeLotes($valor) { $this->inQuantidadeLotes = $valor;  }
/**
    * @access Public
    * @param Integer $valor
*/
function setLotesCaucionados($valor) { $this->inLotesCaucionados = $valor; }
/**
    * @access Public
    * @param Date $valor
*/
function setDataBaixa($valor) { $this->dtDataBaixa = $valor;        }
/**
    * @access Public
    * @param String $valor
*/
function setMotivoBaixa($valor) { $this->stMotivoBaixa = $valor;      }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodigoProcesso($valor) { $this->inCodigoProcesso = $valor;   }

/**
     * @access Public
     * @return Integer
*/
function getCodigoLoteamento() { return $this->inCodigoLoteamento;   }
/**
     * @access Public
     * @return String
*/
function getNomeLoteamento() { return $this->stNomeLoteamento;     }
/**
     * @access Public
     * @return Integer
*/
function getLoteOrigem() { return $this->inLoteOrigem;         }
/**
     * @access Public
     * @return Date
*/
function getDataInclusao() { return $this->dtDataInclusao;       }
/**
     * @access Public
     * @return Date
*/
function getDataLiberacao() { return $this->dtDataLiberacao;      }
/**
     * @access Public
     * @return Date
*/
function getDataAprovacao() { return $this->dtDataAprovacao;      }
/**
     * @access Public
     * @return Float
*/
function getAreaLoteamento() { return $this->flAreaLoteamento;     }
/**
     * @access Public
     * @return Float
*/
function getAreaRemanescente() { return $this->flAreaRemanescente;   }
/**
     * @access Public
     * @return Float
*/
function getAreaTotalLotes() { return $this->flAreaTotalLotes;     }
/**
     * @access Public
     * @return Float
*/
function getAreaLogradouro() { return $this->flAreaLogradouro;    }
/**
     * @access Public
     * @return Float
*/
function getAreaComunitaria() { return $this->flAreaComunitaria;    }
/**
     * @access Public
     * @return Integer
*/
function getQuantidadeLotes() { return $this->inQuantidadeLotes;    }
/**
     * @access Public
     * @return Integer
*/
function getLotesCaucionados() { return $this->inLotesCaucionados;   }
/**
     * @access Public
     * @return Date
*/
function getDataBaixa() { return $this->dtDataBaixa;          }
/**
     * @access Public
     * @return String
*/
function getMotivoBaixa() { return $this->stMotivoBaixa;        }
/**
     * @access Public
     * @return Integer
*/
function getCodigoProcesso() { return $this->inCodigoProcesso;     }

/**
    * Metodo construtor
    * @access Private
*/

function RCIMLoteamento()
{
    $this->obTransacao                = new Transacao;
    $this->obTCIMLoteamento           = new TCIMLoteamento;
    $this->obTCIMLoteamentoLoteOrigem = new TCIMLoteamentoLoteOrigem;
    $this->obTCIMProcessoLoteamento   = new TCIMProcessoLoteamento;
    $this->obTCIMLoteLoteamento       = new TCIMLoteLoteamento;
    $this->obRCIMLote                 = new RCIMLote;
    $this->obRProcesso                = new RProcesso;
    $this->obErro                     = new Erro;
    $this->arLote                     = array();
}

/**
    * Adiciona um objeto de Lote
    * @access Public
*/
function addLote($arChaveLote)
{
    $this->obRCIMLote = new RCIMLote;
    $this->obRCIMLote->setCodigoLote( $arChaveLote['inCodLote'] );    
    $this->obRCIMLote->setNumeroLote( $arChaveLote['inNumLote'] );
    $this->obRCIMLote->obRCIMLocalizacao->setValorComposto( $arChaveLote['stLocalizacaoLoteamento'] );
    if ($arChaveLote['boCaucionado'] == "Sim") {
        $this->obRCIMLote->setCaucionado( true );
    } elseif ($arChaveLote['boCaucionado'] == "Não") {
        $this->obRCIMLote->setCaucionado( false );
    }

    $this->arLote[] = $this->obRCIMLote;

    return $obErro;
}

/**
    * Inclui os dados setados para Loteamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluirLoteamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->obTCIMLoteamento->proximoCod( $this->inCodigoLoteamento , $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMLoteamento->setDado( "cod_loteamento",   $this->inCodigoLoteamento  );
            $this->obTCIMLoteamento->setDado( "nom_loteamento",   $this->stNomeLoteamento    );
            $this->obTCIMLoteamento->setDado( "area_logradouro",  $this->flAreaLogradouro    );
            $this->obTCIMLoteamento->setDado( "area_comunitaria", $this->flAreaComunitaria   );
            $obErro = $this->obTCIMLoteamento->inclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( $this->obRProcesso->getCodigoProcesso() ) {
                    $this->obTCIMProcessoLoteamento->setDado( "cod_loteamento", $this->inCodigoLoteamento );
                    $this->obTCIMProcessoLoteamento->setDado( "cod_processo"  , $this->obRProcesso->getCodigoProcesso()   );
                    $this->obTCIMProcessoLoteamento->setDado( "exercicio"     , $this->obRProcesso->getExercicio()        );
                    $obErro = $this->obTCIMProcessoLoteamento->inclusao( $boTransacao );
                }
                if ( !$obErro->ocorreu() ) {
                    if ($this->inLoteOrigem) {
                        $arData1 = explode("/", $this->dtDataLiberacao);
                        $arData2 = explode("/", $this->dtDataAprovacao);
                        $inData1 = $arData1[0].$arData1[1].$arData1[2];
                        $inData2 = $arData2[0].$arData2[1].$arData2[2];
                        if ( ( empty($dataLiberacao) ) || ( $inData1 >= $inData2 ) ) {
                            $this->obTCIMLoteamentoLoteOrigem->setDado( "cod_loteamento", $this->inCodigoLoteamento );
                            $this->obTCIMLoteamentoLoteOrigem->setDado( "cod_lote"      , $this->inLoteOrigem       );
                            $this->obTCIMLoteamentoLoteOrigem->setDado( "dt_aprovacao"  , $this->dtDataAprovacao    );
                            $this->obTCIMLoteamentoLoteOrigem->setDado( "dt_liberacao"  , $this->dtDataLiberacao    );
                            $obErro = $this->obTCIMLoteamentoLoteOrigem->inclusao( $boTransacao );
                        } else {
                            $obErro->setDescricao("A Data de Liberação deve ser igual ou posterior à Data de Aprovação!");
                        }
                    }
                    
                    if ( !$obErro->ocorreu() ) {
                        foreach ($this->arLote as $obRCIMLote) {
                            $this->obTCIMLoteLoteamento->setDado( "cod_lote"      , $obRCIMLote->getCodigoLote() );
                            $this->obTCIMLoteLoteamento->setDado( "cod_loteamento", $this->inCodigoLoteamento    );
                            $this->obTCIMLoteLoteamento->setDado( "caucionado"    , $obRCIMLote->getCaucionado() );
                            $obErro = $this->obTCIMLoteLoteamento->inclusao( $boTransacao );
                            if ( $obErro->ocorreu() ) {
                                break;
                            }
                        }
                    }
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteamento );

    return $obErro;
}

/**
    * Altera os dados setados para Loteamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarLoteamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->obTCIMLoteamento->setDado( "cod_loteamento",   $this->inCodigoLoteamento  );
        $this->obTCIMLoteamento->setDado( "nom_loteamento",   $this->stNomeLoteamento    );
        $this->obTCIMLoteamento->setDado( "area_logradouro",  $this->flAreaLogradouro    );
        $this->obTCIMLoteamento->setDado( "area_comunitaria", $this->flAreaComunitaria   );
        $obErro = $this->obTCIMLoteamento->alteracao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if ( $this->obRProcesso->getCodigoProcesso() ) {
                $this->obTCIMProcessoLoteamento->setDado( "cod_loteamento", $this->inCodigoLoteamento                 );
                $this->obTCIMProcessoLoteamento->setDado( "cod_processo"  , $this->obRProcesso->getCodigoProcesso()   );
                $this->obTCIMProcessoLoteamento->setDado( "exercicio"     , $this->obRProcesso->getExercicio()        );
                $obErro = $this->obTCIMProcessoLoteamento->inclusao( $boTransacao );
            }
            if ( !$obErro->ocorreu() ) {
                $arData1 = explode("/", $this->dtDataLiberacao);
                $arData2 = explode("/", $this->dtDataAprovacao);
                $inData1 = $arData1[0].$arData1[1].$arData1[2];
                $inData2 = $arData2[0].$arData2[1].$arData2[2];
                if (( empty($this->dtDataLiberacao) )|| ( $inData1 >= $inData2 ) ) {
                    $this->obTCIMLoteamentoLoteOrigem->setDado( "cod_loteamento", $this->inCodigoLoteamento );
                    $this->obTCIMLoteamentoLoteOrigem->setDado( "cod_lote"      , $this->inLoteOrigem       );
                    $this->obTCIMLoteamentoLoteOrigem->setDado( "dt_aprovacao"  , $this->dtDataAprovacao    );
                    $this->obTCIMLoteamentoLoteOrigem->setDado( "dt_liberacao"  , $this->dtDataLiberacao    );
                    $obErro = $this->obTCIMLoteamentoLoteOrigem->alteracao( $boTransacao );
                } else {
                    $obErro->setDescricao("A Data de Liberação deve ser igual ou posterior à Data de Aprovação!");
                }

                if ( !$obErro->ocorreu() ) {
                    $obErro = $this->atualizarLoteamentoLote( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteamento );

    return $obErro;
}

/**
    * Faz a verificação se o trecho já esta relacionado a face de quadra e inclui ou exclui da tabela de relacionamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function atualizarLoteamentoLote($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //Verificar se precisa incluir, alterar ou excluir
        $this->inCodigoLoteamento = $this->getCodigoLoteamento();
        $this->listarLotesLoteamento( $rsRecordSet ,$boTransacao );
        $inRegistrosAnteriores = $rsRecordSet->getNumLinhas();
        $inRegistrosNovos = count(Sessao::read('lotes'));

        //INCLUIR OU ATUALIZAR
        if ( $inRegistrosNovos >= $inRegistrosAnteriores ) {
            foreach ($this->arLote as $obRCIMLote) {
                $stChaveLote = $obRCIMLote->getCodigoLote();
                
                $this->inCodLote = $obRCIMLote->getCodigoLote();
                $this->inCodigoLoteamento = $this->getCodigoLoteamento();
                $this->listarLotesLoteamento( $rsRecordSet ,$boTransacao );            
                
                if ( $rsRecordSet->getNumLinhas() > 0 ) {
                    $this->obTCIMLoteLoteamento->setDado( "cod_lote"      , $stChaveLote );
                    $this->obTCIMLoteLoteamento->setDado( "cod_loteamento", $this->inCodigoLoteamento    );
                    $this->obTCIMLoteLoteamento->setDado( "caucionado"    , $obRCIMLote->getCaucionado() );
                    $obErro = $this->obTCIMLoteLoteamento->alteracao( $boTransacao );
                }else{
                    $this->obTCIMLoteLoteamento->setDado( "cod_lote"      , $stChaveLote );
                    $this->obTCIMLoteLoteamento->setDado( "cod_loteamento", $this->inCodigoLoteamento    );
                    $this->obTCIMLoteLoteamento->setDado( "caucionado"    , $obRCIMLote->getCaucionado() );
                    $obErro = $this->obTCIMLoteLoteamento->inclusao( $boTransacao );   
                }
            }
        }else{
            //EXCLUIR
            $arRegistroAnteriores = $rsRecordSet->getElementos();
            $arRegistrosNovos = Sessao::read('lotes');
            $arExcluir = array_diff_assoc($arRegistroAnteriores, $arRegistrosNovos);
            foreach ($arExcluir as $lotesExcluir) {
                $this->obTCIMLoteLoteamento->setDado( "cod_lote"      , $lotesExcluir["cod_lote"] );
                $this->obTCIMLoteLoteamento->setDado( "cod_loteamento", $lotesExcluir["cod_loteamento"]);
                $this->obTCIMLoteLoteamento->setDado( "caucionado"    , $lotesExcluir["caucionado"]);
                $obErro = $this->obTCIMLoteLoteamento->exclusao( $boTransacao );
            }
        }

        if ( $obErro->ocorreu() ) {
            break;
        }
        
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteLoteamento );

    return $obErro;
}

/**
    * Excluir o loteamento
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirLoteamento($boTransacao = "")
{
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $stTmpCampoCod = $this->obTCIMLoteLoteamento->getCampoCod();
        $this->obTCIMLoteLoteamento->setCampoCod( 'cod_loteamento' );
        $this->obTCIMLoteLoteamento->setDado( "cod_loteamento" , $this->inCodigoLoteamento );
        $obErro = $this->obTCIMLoteLoteamento->exclusao( $boTransacao );
        $this->obTCIMLoteLoteamento->setCampoCod( $stTmpCampoCod );
        if ( !$obErro->ocorreu() ) {
            $this->obTCIMLoteamentoLoteOrigem->setDado( "cod_loteamento" , $this->inCodigoLoteamento );
            $obErro = $this->obTCIMLoteamentoLoteOrigem->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $this->obTCIMProcessoLoteamento->setDado( "cod_loteamento" , $this->inCodigoLoteamento );
                $obErro = $this->obTCIMProcessoLoteamento->exclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {
                    $this->obTCIMLoteamento->setDado( "cod_loteamento" , $this->inCodigoLoteamento );
                    $obErro = $this->obTCIMLoteamento->exclusao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $this->obTCIMLoteamento );

    return $obErro;
}

/**
    * Lista os loteamentos
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLoteamento(&$rsRecordSet, $stOrder = "" , $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoLoteamento) {
        $stFiltro = " l.cod_loteamento = ".  $this->inCodigoLoteamento ." and ";
    }

    if ($this->stNomeLoteamento) {
        $stFiltro .= " upper (l.nom_loteamento) like '%". strtoupper ($this->stNomeLoteamento) ."%' and ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen($stFiltro) - 4 );
    }

    $obErro = $this->obTCIMLoteamento->recuperaLote( $rsRecordSet, $stFiltro, '', $boTransacao );

    return $obErro;
}

/**
    * Lista os Lotes cadastrados para um loteamento DE ACORDO COM FILTRO
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLoteamentoLote(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLoteamento) {
        $stFiltro = " l.cod_loteamento = ".  $this->inCodigoLoteamento." and ";
    }

    if ( $this->obRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao() ) {
        $stFiltro .= " ll.cod_localizacao = ". $this->obRCIMLote->obRCIMLocalizacao->getCodigoLocalizacao()." and ";
    }
    if ( $this->obRCIMLote->obRCIMLocalizacao->getValorComposto() ) {
        $stFiltro .= " lo.codigo_composto = '". $this->obRCIMLote->obRCIMLocalizacao->getValorComposto()."' and ";
    }
    if ( $this->obRCIMLote->getCodigoLote() ) {
        $stFiltro .= " l.cod_lote = ". $this->obRCIMLote->getCodigoLote()." and ";
    }
    if ( $this->obRCIMLote->getNumeroLote() ) {
        $stFiltro .= " ltrim(ll.valor,'0') = '". ltrim($this->obRCIMLote->getNumeroLote(),'0')."' and ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrder = " order by l.cod_lote ";

    $obErro = $this->obTCIMLoteLoteamento->recuperaLoteLoteamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//    $this->obTCIMLoteLoteamento->debug();
    return $obErro;
}

/**
    * Lista os Lotes não cadastrados para um loteamento
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarNaoLoteamentoLote(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";

    if ($this->inCodigoLoteamento) {
        $stFiltro = " cod_loteamento <> ".  $this->inCodigoLoteamento ." and ";
    }

    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrder = " cod_lote ";

    $obErro = $this->obTCIMLoteLoteamento->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista os Lotes cadastrados para um loteamento
    * @access Public
    * @param  Object $rsRecordSet Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarLotesLoteamento(&$rsRecordSet, $boTransacao = "")
{
    $stFiltro = "";
    if ($this->inCodigoLoteamento) {
        $stFiltro = " l.cod_loteamento = ".  $this->inCodigoLoteamento." and ";
    }
    if ($this->inCodLote) {
        $stFiltro = " l.cod_lote = ".  $this->inCodLote." and ";
    }
    if ($stFiltro) {
        $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }

    $stOrder = " order by l.cod_lote ";

    $obErro = $this->obTCIMLoteLoteamento->recuperaLoteLoteamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
//    $this->obTCIMLoteLoteamento->debug();
    return $obErro;
}

}

?>

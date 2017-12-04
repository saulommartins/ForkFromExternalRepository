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
    * Classe de Regra de Negócio para Despesa
    * Data de Criação: 26/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Regra

    $Id: ROrcamentoDespesa.class.php 65434 2016-05-20 18:32:34Z michel $

    * Casos de uso: uc-02.01.06, uc-02.01.24, uc-02.01.07, uc-02.01.26, uc-02.03.03, uc-02.01.33

*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoRecurso.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoEntidade.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoUnidadeOrcamentaria.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoProjetoAtividade.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoFuncao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoSubfuncao.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoPrograma.class.php";
include_once CAM_GF_ORC_NEGOCIO."ROrcamentoClassificacaoDespesa.class.php";
include_once CAM_FW_TIPO."TPeriodo.class.php";
include_once CAM_FW_BANCO_DADOS."Transacao.class.php";

/**
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 26/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra
*/
class ROrcamentoDespesa
{
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoRecurso;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoEntidade;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoUnidadeOrcamentaria;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoProjetoAtividade;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoFuncao;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoSubfuncao;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoPrograma;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoClassificacaoDespesa;
/**
    * @var Object
    * @access Private
*/
var $obTPeriodo;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodDespesa;
/**
    * @var Numeric
    * @access Private
*/
var $inCodCentroCusto;
/**
    * @var Numeric
    * @access Private
*/
var $nuValorOriginal;
/**
    * @var Numeric
    * @access Private
*/
var $nuSaldoDotacao;
/**
    * @var String
    * @access Private
*/
var $stDescricao;

var $boDotacaoAnalitica;

/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoUnidadeOrcamentaria($valor) { $this->obROrcamentoUnidadeOrcamentaria  = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoRecurso($valor) { $this->obROrcamentoRecurso              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoProjetoAtividade($valor) { $this->obROrcamentoProjetoAtividade     = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoFuncao($valor) { $this->obROrcamentoFuncao      = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoSubfuncao($valor) { $this->obROrcamentoSubfuncao   = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoPrograma($valor) { $this->obROrcamentoPrograma             = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoClassificacaoDespesa($valor) { $this->obROrcamentoClassificacaoDespesa = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTPeriodo($valor) { $this->obTPeriodo = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade    = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao              = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio              = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodDespesa($valor) { $this->inCodDespesa             = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodCentroCusto($valor) { $this->inCodCentroCusto             = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setValorOriginal($valor) { $this->nuValorOriginal          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSaldoDotacao($valor) { $this->nuSaldoDotacao           = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao              = $valor; }

function setDotacaoAnalitica($valor) { $this->boDotacaoAnalitica = $valor; }

/**
     * @access Public
     * @return Object
*/
function getROrcamentoUnidadeOrcamentaria() { return $this->obROrcamentoUnidadeOrcamentaria;  }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoRecurso() { return $this->obROrcamentoRecurso;              }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoProjetoAtividade() { return $this->obROrcamentoProjetoAtividade;     }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoFuncao() { return $this->obROrcamentoFuncao;      }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoSubfuncao() { return $this->obROrcamentoSubfuncao;   }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoPrograma() { return $this->obROrcamentoPrograma;             }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoClassificacaoDespesa() { return $this->obROrcamentoClassificacaoDespesa;}
/**
     * @access Public
     * @return Object
*/
function getTPeriodo() { return $this->obTPeriodo;}
/**
     * @access Public
     * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;   }
/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;             }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;             }
/**
     * @access Public
     * @return Integer
*/
function getCodDespesa() { return $this->inCodDespesa;            }
/**
     * @access Public
     * @return Numeric
*/
function getCodCentroCusto() { return $this->inCodCentroCusto;            }
/**
     * @access Public
     * @return Numeric
*/
function getValorOriginal() { return $this->nuValorOriginal;         }
/**
     * @access Public
     * @return Numeric
*/
function getSaldoDotacao() { return $this->nuSaldoDotacao;          }
/**
     * @access Public
     * @return String
*/
function getDescricao() { return $this->stDescricao;             }

function getDotacaoAnalitica() { return $this->boDotacaoAnalitica; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoDespesa()
{
    $this->setROrcamentoUnidadeOrcamentaria ( new ROrcamentoUnidadeOrcamentaria  );
    $this->setROrcamentoRecurso             ( new ROrcamentoRecurso              );
    $this->setROrcamentoEntidade            ( new ROrcamentoEntidade    );
    $this->setROrcamentoProjetoAtividade    ( new ROrcamentoProjetoAtividade     );
    $this->setROrcamentoFuncao              ( new ROrcamentoFuncao      );
    $this->setROrcamentoSubfuncao           ( new ROrcamentoSubfuncao   );
    $this->setROrcamentoPrograma            ( new ROrcamentoPrograma             );
    $this->setROrcamentoClassificacaoDespesa( new ROrcamentoClassificacaoDespesa );
    $this->setTPeriodo                      ( new TPeriodo              );
    $this->setTransacao                     ( new Transacao             );
    $this->setExercicio                     ( Sessao::getExercicio()        );
}

/**
    * Salva fixação de despesa no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoDespesa->setDado( "exercicio"     , $this->getExercicio()                              );
        $obTOrcamentoDespesa->setDado( "vl_original"   , $this->getValorOriginal()                          );
        $obTOrcamentoDespesa->setDado( "cod_entidade"  , $this->obROrcamentoEntidade->getCodigoEntidade()   );
        $obTOrcamentoDespesa->setDado( "cod_programa"  , $this->obROrcamentoPrograma->getCodPrograma()               );
        $obTOrcamentoDespesa->setDado( "num_pao"       , $this->obROrcamentoProjetoAtividade->getNumeroProjeto()     );
        $obTOrcamentoDespesa->setDado( "cod_recurso"   , $this->obROrcamentoRecurso->getCodRecurso()                 );
        $obTOrcamentoDespesa->setDado( "cod_funcao"    , $this->obROrcamentoFuncao->getCodigoFuncao()       );
        $obTOrcamentoDespesa->setDado( "cod_subfuncao" , $this->obROrcamentoSubfuncao->getCodigoSubFuncao() );
        $obTOrcamentoDespesa->setDado( "cod_conta"     , $this->obROrcamentoClassificacaoDespesa->getCodConta()      );
        $obTOrcamentoDespesa->setDado( "num_unidade"   , $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade()  );
        $obTOrcamentoDespesa->setDado( "num_orgao"     , $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() );
        if ( $this->getCodDespesa() ) {
            $obTOrcamentoDespesa->setDado( "cod_despesa" , $this->getCodDespesa() );
            $obErro = $obTOrcamentoDespesa->alteracao( $boTransacao );
        } else {
            $obErro = $obTOrcamentoDespesa->proximoCod ( $inCodDespesa , $boTransacao );
            $obTOrcamentoDespesa->setDado( "cod_despesa" , $inCodDespesa );
            $obErro = $obTOrcamentoDespesa->inclusao( $boTransacao );
            $this->inCodDespesa = $inCodDespesa;
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoDespesa );
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPrevisaoDespesa.class.php"     );

    $obTOrcamentoDespesa         = new TOrcamentoDespesa;
    $obTOrcamentoPrevisaoDespesa = new TOrcamentoPrevisaoDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrevisaoDespesa->setDado( "cod_despesa" , $this->getCodDespesa() );
        $obTOrcamentoPrevisaoDespesa->setDado( "exercicio"   , $this->getExercicio()  );
        $obErro = $obTOrcamentoPrevisaoDespesa->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTOrcamentoDespesa->setDado( "cod_despesa" , $this->getCodDespesa() );
            $obTOrcamentoDespesa->setDado( "exercicio"   , $this->getExercicio()  );
            $obErro = $obTOrcamentoDespesa->exclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoDespesa );
    }

    return $obErro;
}

/**
    * Método para listar tabela TContaDespesa
    * @access Public
    * @param Objebct $boTransacao
    * @return Object $obErro
*/
function listarContaDespesa(&$rsRecordSet, $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"        );
    $obTContaDespesa = new TOrcamentoContaDespesa;
    $stFiltro        = "";

    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " cod_estrutural = '".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."' AND ";
    if( $this->obROrcamentoClassificacaoDespesa->getCodConta() )
        $stFiltro .= " cod_conta = ".$this->obROrcamentoClassificacaoDespesa->getCodConta()." AND ";
    if( $this->stExercicio )
        $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    $stFiltro = ( $stFiltro ) ? " WHERE " . substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
    $obErro = $obTContaDespesa->recuperaTodos( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente OrcamentoDespesa
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CD.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND OD.cod_despesa = ".$this->getCodDespesa();
    }
    if ( $this->obROrcamentoClassificacaoDespesa->getDescricao() ) {
        $stFiltro .= " AND lower(CD.descricao) like lower('%".$this->obROrcamentoClassificacaoDespesa->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        $stFiltro .= " AND lower(CD.mascara_classificacao) like lower('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."%')";
    }
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente OrcamentoDespesa
    * Retorna dados com valores originais  igual  à 0
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarCredEspecial(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php");
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CD.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND OD.cod_despesa = ".$this->getCodDespesa();
    }
    if ( $this->obROrcamentoClassificacaoDespesa->getDescricao() ) {
        $stFiltro .= " AND lower(CD.descricao) like
lower('%".$this->obROrcamentoClassificacaoDespesa->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
        $stFiltro .= " AND lower(CD.mascara_classificacao) like
lower('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."%')";
    }

    $stFiltro .= " AND vl_original <> 0.00 ";

    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente OrcamentoDespesa
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarOrcamentoDespesas(&$rsLista, $stFiltro = '', $stOrder = "", $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente OrcamentoDespesa
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    if(empty($stOrder))
        $stOrder = null;

    if(empty($stFiltro))
        $stFiltro = null;

    if ( $this->getCodDespesa() ) {
        $stFiltro .= " WHERE cod_despesa = ".$this->getCodDespesa();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio ='".$this->getExercicio() . "' ";
    }

    $obErro = $obTOrcamentoDespesa->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaRelacionameto na classe Persistente OrcamentoDespesa para recuperar a relação entre Despesa e Conta
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarContaDespesa(&$rsLista, $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND OD.cod_despesa = ".$this->getCodDespesa();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CD.exercicio = '".$this->getExercicio() . "' ";
    }
    $obErro = $obTOrcamentoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

function listarDespesaCredEspecial(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php";
    $obTOrcamentoDespesa = new TOrcamentoDespesa;

    $obTOrcamentoDespesa->setDado( 'exercicio'  , $this->stExercicio      );

    if ( $this->getCodDespesa() ) {
        $obTOrcamentoDespesa->setDado('cod_despesa', $this->getCodDespesa());
    }

    if ($this->obROrcamentoEntidade->getCodigoEntidade()) {
        $obTOrcamentoDespesa->setDado('cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade());
    }

    $obErro = $obTOrcamentoDespesa->recuperaListaDespesaCredEspecial($rsLista, $stOrder, $obTransacao);

    return $obErro;
}

/**
    * Recupera Valor da reserva de uma dotação
    * @access Public
    * @param  Object $nuVlReserva
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarValorReservaDotacao(&$nuVlReserva, $boTransacao = "")
{
    include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoValorReservaDotacao.class.php";
    $obFOrcamentoValorReservaDotacao = new FOrcamentoValorReservaDotacao;
    $obFOrcamentoValorReservaDotacao->setDado( 'exercicio'  , $this->stExercicio      );
    $obFOrcamentoValorReservaDotacao->setDado( 'cod_despesa', $this->getCodDespesa()  );
    $obErro = $obFOrcamentoValorReservaDotacao->executaFuncao( $rsValor, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $nuVlReserva = $rsValor->getCampo('valor_reserva_dotacao');
    }

    return $obErro;
}

/**
    * Recupera Valor da reserva de uma dotação até o período informado
    * @access Public
    * @param  Object $nuVlReserva
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarValorReservaDotacaoPeriodo(&$nuVlReserva, $boTransacao = "")
{
    include_once CAM_GF_ORC_MAPEAMENTO."FOrcamentoValorReservaDotacaoPeriodo.class.php";
    $obFOrcamentoValorReservaDotacaoPeriodo = new FOrcamentoValorReservaDotacaoPeriodo;
    $obFOrcamentoValorReservaDotacaoPeriodo->setDado( 'exercicio'  , $this->stExercicio                 );
    $obFOrcamentoValorReservaDotacaoPeriodo->setDado( 'cod_despesa', $this->getCodDespesa()             );
    $obFOrcamentoValorReservaDotacaoPeriodo->setDado( 'dt_final'   , $this->obTPeriodo->getTDataFinal() );
    $obErro = $obFOrcamentoValorReservaDotacaoPeriodo->executaFuncao( $rsValor, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $nuVlReserva = $rsValor->getCampo('valor_reserva_dotacao');
    }

    return $obErro;
}

/**
    * Lista as Despesas
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDespesa(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    $stOrder = " ORDER BY cod_despesa , publico.fn_mascarareduzida(CD.cod_estrutural)";
    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND O.cod_despesa = ".$this->getcodDespesa();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND O.exercicio = '" . $this->getExercicio() . "' ";
    }
    if ( $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) {
        $stFiltro .= " AND num_orgao = ".$this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
    }
    if ( $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() ) {
        $stFiltro .= " AND num_unidade = ".$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
    }
    if( $this->obROrcamentoPrograma->getCodPrograma() )
        $stFiltro .= " AND cod_programa = ".$this->obROrcamentoPrograma->getCodPrograma();
    if( $this->obROrcamentoProjetoAtividade->getNumeroProjeto() )
        $stFiltro .= " AND num_pao = ".$this->obROrcamentoProjetoAtividade->getNumeroProjeto();

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " AND publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%'";

    if( $this->getDescricao() )
        $stFiltro .= " AND CD.descricao ilike '%".$this->getDescricao()."%'";
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaDespesa( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Lista as Dotações
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaDuplicidade($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "WHERE cod_despesa is not null ";
    if( $this->getCodDespesa() )
        $stFiltro .= " AND cod_despesa <> ".$this->getCodDespesa();
    if( $this->getExercicio() )
        $stFiltro .= " AND exercicio = '" . $this->getExercicio() . "' ";
    if( $this->obROrcamentoPrograma->getCodPrograma() )
        $stFiltro .= " AND cod_programa = ".$this->obROrcamentoPrograma->getCodPrograma();
    if( $this->obROrcamentoClassificacaoDespesa->getCodConta() )
        $stFiltro .= " AND cod_conta = ".$this->obROrcamentoClassificacaoDespesa->getCodConta();
    if( $this->obROrcamentoProjetoAtividade->getNumeroProjeto() )
        $stFiltro .= " AND num_pao = ".$this->obROrcamentoProjetoAtividade->getNumeroProjeto();
    if( $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() )
        $stFiltro .= " AND num_orgao = ".$this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
    if( $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() )
        $stFiltro .= " AND num_unidade = ".$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
    if( $this->obROrcamentoRecurso->getCodRecurso() !== false)
        $stFiltro .= " AND cod_recurso = ".$this->obROrcamentoRecurso->getCodRecurso();
    if( $this->obROrcamentoFuncao->getCodigoFuncao() )
        $stFiltro .= " AND cod_funcao = ".$this->obROrcamentoFuncao->getCodigoFuncao();
    if( $this->obROrcamentoSubfuncao->getCodigoSubFuncao() )
        $stFiltro .= " AND cod_subfuncao = ".$this->obROrcamentoSubfuncao->getCodigoSubFuncao();

    $obErro = $obTOrcamentoDespesa->verificaDuplicidade($rsVerifica, $stFiltro, $stOrder, $obTransacao );

    if($rsVerifica->getNumLinhas() > 0)
        $obErro->setDescricao('Já existe uma despesa cadastrada com os dados informados!');

    return $obErro;
}

/**
    * Lista as Dotações
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDotacao(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    $stOrder = " ORDER BY D.cod_entidade, D.cod_despesa";
    if( $this->getCodDespesa() )
        $stFiltro .= " AND D.cod_despesa = ".$this->getCodDespesa();
    if( $this->getExercicio() )
        $stFiltro .= " AND D.exercicio = '" . $this->getExercicio() . "' ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND D.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " AND CD.cod_estrutural = '".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."'";

    $obTOrcamentoDespesa->setDado("stDataInicial", $this->obTPeriodo->getDataInicial());
    $obTOrcamentoDespesa->setDado("stDataFinal",   $this->obTPeriodo->getDataFinal());

    $obErro = $obTOrcamentoDespesa->recuperaListaDotacao( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Lista as Dotações
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarDotacao(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    $stOrder = " ORDER BY D.cod_entidade, D.cod_despesa ";
    if( $this->getCodDespesa() )
        $stFiltro .= " AND D.cod_despesa = ".$this->getCodDespesa();

    if( $this->getExercicio() )
        $obTOrcamentoDespesa->setDado('exercicio',$this->getExercicio());
        $stFiltro .= " AND D.exercicio = '" . $this->getExercicio() . "' ";

    if( $this->obROrcamentoRecurso->getCodRecurso() )
       $stFiltro .= " AND R.cod_recurso = ".$this->obROrcamentoRecurso->getCodRecurso();

    if( $this->obROrcamentoRecurso->getDestinacaoRecurso() )
       $stFiltro .= " AND R.masc_recurso_red like '".$this->obROrcamentoRecurso->getDestinacaoRecurso()."%' ";

    if( $this->obROrcamentoRecurso->getCodDetalhamento() )
       $stFiltro .= " AND R.cod_detalhamento = ".$this->obROrcamentoRecurso->getCodDetalhamento();

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
       $stFiltro .= " AND D.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")";

    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " AND CD.cod_estrutural = '".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."'";

    $stFiltro .="
        GROUP BY
              D.cod_entidade,
              D.exercicio,
              CGM.nom_cgm,

              D.cod_despesa,
              CD.cod_conta,
              CD.descricao,

              D.num_orgao,
              OO.nom_orgao,
              OU.nom_unidade,

              D.num_unidade,

              D.cod_funcao,
              F.descricao,

              D.cod_subfuncao,
              SF.descricao,

              D.cod_programa,
              ppa.programa.num_programa,
              P.descricao,

              D.num_pao,
              ppa.acao.num_acao,
              PAO.nom_pao,

              CD.cod_estrutural,

              D.cod_recurso,
              R.nom_recurso,
              R.masc_recurso,
              R.cod_fonte,
              R.masc_recurso_red,
              R.cod_detalhamento

    ";

    $obTOrcamentoDespesa->setDado("stDataInicial", $this->obTPeriodo->getDataInicial());
    $obTOrcamentoDespesa->setDado("stDataFinal",   $this->obTPeriodo->getDataFinal());

    $obErro = $obTOrcamentoDespesa->recuperaDotacao( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Verifica se pode ser inserido valor na Classificacao indicada
    * @access Public
    * @param  Object $stSumConta Retorna o somatório dos valores das contas
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaValorConta(&$stSumConta , $stFiltro, $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."FOrcamentoRelacaoDespesa.class.php"      );
    $obFOrcamentoRelacaoDespesa  = new FOrcamentoRelacaoDespesa;

    $obFOrcamentoRelacaoDespesa->setDado("exercicio", $this->getExercicio());

    $obErro = $obFOrcamentoRelacaoDespesa->consultaValorConta( $rsLista, $stFiltro, $stOrder, $obTransacao );
    $stSumConta = 0;
    if ( $rsLista->getNumLinhas() > -1 ) {
        $stSumConta = $rsLista->getCampo('sum');
    }

    return $obErro;
}

/**
    * Método para listar Conta Despesa
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarRelacionamentoContaDespesa(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"        );
    $obTContaDespesa              = new TOrcamentoContaDespesa;

    if( $this->inCodDespesa )
        $stFiltro .= " D.cod_despesa = ".$this->inCodDespesa." AND ";
    if( $this->obROrcamentoClassificacaoDespesa->getCodConta() )
        $stFiltro .= " CD.cod_conta = ".$this->obROrcamentoClassificacaoDespesa->getCodConta()." AND ";
    if( $this->stExercicio )
        $stFiltro .= " D.exercicio = '".$this->stExercicio."' AND ";
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' AND ";
    if( $this->obROrcamentoClassificacaoDespesa->getDescricao() )
        $stFiltro .= " lower(CD.descricao) like lower('%".$this->obROrcamentoClassificacaoDespesa->getDescricao()."%') AND ";
    $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $stOrder  = ( $stOrder  ) ? $stOrder : "CD.cod_estrutural,D.cod_conta";
    $obErro = $obTContaDespesa->recuperaRelacionamento( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );

    return $obErro;
}

/**
    * Método para listar Codigo Estrutural
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarCodEstruturalDespesa(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"        );
    $obTContaDespesa              = new TOrcamentoContaDespesa;
    if( empty($stFiltro) )
        $stFiltro = "";

    if ($this->inCodDespesa) {
        $stFiltro .= " EXISTS ( SELECT 1                     \n";
        $stFiltro .= "            FROM orcamento.despesa     \n";
        $stFiltro .= "           WHERE cod_despesa       = ".$this->inCodDespesa.  "\n";
        $stFiltro .= "             AND despesa.cod_conta = conta_despesa.cod_conta) AND\n";
    }
    if( $this->obROrcamentoClassificacaoDespesa->getCodConta() )
        $stFiltro .= " conta_despesa.cod_conta = ".$this->obROrcamentoClassificacaoDespesa->getCodConta()." AND\n";
    if( $this->stExercicio )
        $stFiltro .= " conta_despesa.exercicio = '".$this->stExercicio."' AND\n";
    if( $this->obROrcamentoClassificacaoDespesa->getClassificacao() )
        $stFiltro .= " publico.fn_mascarareduzida(conta_despesa.cod_estrutural) = publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getClassificacao()."') AND\n";
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " publico.fn_mascarareduzida(conta_despesa.cod_estrutural) lIkE publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' AND\n";
    if( $this->obROrcamentoClassificacaoDespesa->getDescricao() )
        $stFiltro .= " lower(conta_despesa.descricao) like lower('%".$this->obROrcamentoClassificacaoDespesa->getDescricao()."%') AND\n";

    if ( $this->getDotacaoAnalitica() ) {
        $stFiltro .= " NOT EXISTS ( SELECT 1 
                                        FROM orcamento.despesa
                                        WHERE despesa.cod_conta = conta_despesa.cod_conta
                                          AND despesa.exercicio = conta_despesa.exercicio ) AND\n";        
    }

    $stFiltro = ( $stFiltro ) ? substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $stOrder  = ( $stOrder  ) ? $stOrder : "conta_despesa.cod_estrutural,conta_despesa.cod_conta";
    $obErro = $obTContaDespesa->recuperaCodEstrutural( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    
    return $obErro;

}

/**
    * Método para listar Despesa por Usuario
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarDespesaUsuario(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;
    $rsRecordSet = new RecordSet();

    $obTOrcamentoDespesa->setDado( "numcgm"   , $this->obROrcamentoEntidade->obRUsuario->obRCGM->getNumCGM() );
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );

    if ($this->inCodCentroCusto) {
        $obTOrcamentoDespesa->setDado( 'cod_centro', $this->getCodCentroCusto() );
        $obTOrcamentoDespesa->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obTOrcamentoDespesa->setDado( 'cod_despesa', $this->getCodDespesa() );
        $obErro = $obTOrcamentoDespesa->recuperaDespesaCentroCusto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );        
    }
    
    //Caso nao tenha cod de centro de custo ou não existir dotacoes configuradas para tal
    //buscar as dotacoes na configuracoes empenho.permissao_autorizacao
    if ($rsRecordSet->getNumLinhas() < 0 ) {
        if( $this->getExercicio() )
            $stFiltro .= " O.exercicio = '".$this->stExercicio ."' AND ";

        if( $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() )
            $stFiltro .= " O.num_unidade = ".$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() ." AND ";

        if( $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() )
            $stFiltro .= " O.num_orgao = ".$this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ." AND ";

        if( $this->obROrcamentoPrograma->getCodPrograma())
            $stFiltro .= " O.cod_programa = ".$this->obROrcamentoPrograma->getCodPrograma() ." AND ";

        if( $this->obROrcamentoProjetoAtividade->getNumeroProjeto() )
            $stFiltro .= " O.num_pao = ".$this->obROrcamentoProjetoAtividade->getNumeroProjeto() ." AND ";

        if( $this->inCodDespesa )
            $stFiltro .= " O.cod_despesa = ".$this->inCodDespesa." AND ";

        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " O.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";

        if ( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ) {
            $stMascClassificacao = $this->obROrcamentoClassificacaoDespesa->getMascClassificacao();
            while (substr($stMascClassificacao, -1, 1) == '.') {
                $stMascClassificacao = substr($stMascClassificacao, 0, -1);
            }
            $stFiltro .= "publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('" . $stMascClassificacao . "')||'%' AND ";
        }

        if( $this->getDescricao() )
            $stFiltro .= "CD.descricao ilike '%".$this->getDescricao()."%' AND ";

        $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
        $stOrder  = ( $stOrder  ) ? $stOrder : "cod_estrutural,cod_conta";
        $obErro = $obTOrcamentoDespesa->recuperaDespesaUsuarioPermissao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;

}

/**
    * Método para listar Despesa por Usuario
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro

    /// DUPLICADO DEVIDO AO BUG 9112 ////
*/
function listarDespesaUsuarioOrcamento(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $obTOrcamentoDespesa->setDado( "numcgm"   , $this->obROrcamentoEntidade->obRUsuario->obRCGM->getNumCGM() );
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );

    if ($this->inCodCentroCusto) {
        $obTOrcamentoDespesa->setDado( 'cod_centro', $this->getCodCentroCusto() );
        $obTOrcamentoDespesa->setDado( 'cod_entidade', $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obTOrcamentoDespesa->setDado( 'cod_despesa', $this->getCodDespesa() );
        $obErro = $obTOrcamentoDespesa->recuperaDespesaCentroCusto( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    } else {
        if( $this->getExercicio() )
            $stFiltro .= " O.exercicio = '" . $this->stExercicio . "' AND ";

        if( $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() )
            $stFiltro .= " O.num_unidade = ".$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() ." AND ";

        if( $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() )
            $stFiltro .= " O.num_orgao = ".$this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ." AND ";

        if( $this->inCodDespesa )
            $stFiltro .= " O.cod_despesa = ".$this->inCodDespesa." AND ";

        if( $this->obROrcamentoEntidade->getCodigoEntidade() )
            $stFiltro .= " O.cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().") AND ";

        if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
            $stFiltro .= "publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%' AND ";

        $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
        $stOrder  = ( $stOrder  ) ? $stOrder : " cod_estrutural,cod_conta ";
        $obErro = $obTOrcamentoDespesa->recuperaDespesaUsuarioOrcamento( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    }

    return $obErro;

}

/**
    * Método para listar Codigo Reduzido, Descricao e dotacao
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function listarDespesaDotacao(&$rsRecordSet, $stOrder = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $obTOrcamentoDespesa->setDado( "numcgm"   , $this->obROrcamentoEntidade->obRUsuario->obRCGM->getNumCGM() );
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    if( $this->inCodDespesa )
        $stFiltro .= " O.cod_despesa = ".$this->inCodDespesa." AND ";
    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " O.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." AND ";
     if( $this->getExercicio())
        $stFiltro .= " O.exercicio = '" . $this->getExercicio() . "' AND ";

    $stFiltro = ( $stFiltro ) ? " AND ".substr($stFiltro,0,strlen($stFiltro)-4) : '';
    $stOrder  = ( $stOrder  ) ? $stOrder : " order by cod_despesa ";
    $obErro = $obTOrcamentoDespesa->recuperaDespesaDotacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );

    return $obErro;

}

/**
    * Método para Consultar o Saldo da Dotação
    * @access public
    * @param Object $rsRecordSet
    * @param String $stOrder
    * @param Object $boTransacao
    * @return Object $obErro
*/
function consultarSaldoDotacao($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;
    if(empty($stFiltro))
        $stFiltro = null;
    if(empty($stOrder))
        $stOrder = null;

    $obTOrcamentoDespesa->setDado( "cod_despesa" , $this->inCodDespesa );
    $obTOrcamentoDespesa->setDado( "exercicio"   , $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaSaldoDotacao( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
    $this->setSaldoDotacao ( $rsRecordSet->getCampo("saldo_dotacao") );

    return $obErro;
}

/**
    * Lista as Despesas
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDespesaConfiguracaoLancamento(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stFiltro = "";
    $stOrder = " ORDER BY cod_estrutural ";
    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND cod_despesa = ".$this->getcodDespesa();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND O.exercicio = '" . $this->getExercicio() . "' ";
    }
    if ( $this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao() ) {
        $stFiltro .= " AND num_orgao = ".$this->obROrcamentoUnidadeOrcamentaria->obROrcamentoOrgaoOrcamentario->getNumeroOrgao();
    }
    if ( $this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade() ) {
        $stFiltro .= " AND num_unidade = ".$this->obROrcamentoUnidadeOrcamentaria->getNumeroUnidade();
    }
    if( $this->obROrcamentoPrograma->getCodPrograma() )
        $stFiltro .= " AND cod_programa = ".$this->obROrcamentoPrograma->getCodPrograma();
    if( $this->obROrcamentoProjetoAtividade->getNumeroProjeto() )
        $stFiltro .= " AND num_pao = ".$this->obROrcamentoProjetoAtividade->getNumeroProjeto();

    if( $this->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " AND cod_entidade IN (".$this->obROrcamentoEntidade->getCodigoEntidade().")";
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " AND publico.fn_mascarareduzida(CD.cod_estrutural) like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%'";

    if( $this->getDescricao() )
        $stFiltro .= " AND CD.descricao ilike '%".$this->getDescricao()."%'";
    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaConfiguracaoLancamentoDespesa( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Lista as Despesas
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDespesaConfiguracaoLancamentoDetalhado(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stOrder = " ORDER BY tabela.cod_estrutural ";
    if ( $this->getCodDespesa() ) {
        $stFiltro .= " AND despesa.cod_despesa = ".$this->getcodDespesa();
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND conta_despesa.exercicio = '" . $this->getExercicio() . "' ";
    }
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() )
        $stFiltro .= " AND conta_despesa.cod_estrutural like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%'";

    if( $this->getDescricao() )
        $stFiltro .= " AND conta_despesa.descricao ilike '%".$this->getDescricao()."%'";

    $obTOrcamentoDespesa->setDado( "exercicio", $this->stExercicio );
    $obErro = $obTOrcamentoDespesa->recuperaConfiguracaoLancamentoDespesaDetalhado( $rsLista, $stFiltro, $stOrder, $obTransacao );
    
    return $obErro;
}

/**
    * Lista as Despesas do exercico atual e anteriores
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDespesaConfiguracaoLancamentoNovo(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;
    $obErro = $obTOrcamentoDespesa->recuperaConfiguracaoLancamentoDespesaNovo( $rsLista, $obTransacao );

    return $obErro;
}

/**
    * Lista as Despesas do exercicio atual e anteriores 
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stFiltro Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarDespesaConfiguracaoLancamentoDetalhadoNovo(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoDespesa.class.php"             );
    $obTOrcamentoDespesa         = new TOrcamentoDespesa;

    $stOrdem = " ORDER BY tabela.cod_estrutural ";

    if ( $this->getExercicio() ) {
        $stFiltro .= " AND conta_despesa.exercicio = '" . $this->getExercicio() . "' ";
    }
    if( $this->obROrcamentoClassificacaoDespesa->getMascClassificacao() ){
        $stFiltro .= " AND ((conta_despesa.cod_estrutural <> '".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."'";
        $stFiltro .= " AND conta_despesa.cod_estrutural like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."')||'%')";
        $stFiltro .= "OR REPLACE(conta_despesa.cod_estrutural,'.','') = REPLACE('".$this->obROrcamentoClassificacaoDespesa->getMascClassificacao()."', '.', '') )";
    }
    
    $obErro = $obTOrcamentoDespesa->recuperaConfiguracaoLancamentoDespesaDetalhadoNovo( $rsLista, $stFiltro, $stOrdem, $obTransacao );
   
    return $obErro;
}

}

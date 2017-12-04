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
    * Classe de Regra de Negócio Lançamento
    * Data de Criação   : 06/11/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Anderson R. M. Buzo
    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Autor: $
    $Date: 2006-07-05 17:51:50 -0300 (Qua, 05 Jul 2006) $

    * Casos de uso: uc-02.02.04
                    uc-02.02.05
                    uc-02.02.31

*/

/*
$Log$
Revision 1.9  2006/07/05 20:50:26  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"             );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeLote.class.php"            );
include_once ( CAM_GF_CONT_NEGOCIO."RContabilidadeHistoricoPadrao.class.php" );

class RContabilidadeLancamento
{
/**
    * @var Object
    * @access Private
*/
var $obTContabilidadeLancamento;
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Object
    * @access Private
*/
var $obRContabilidadeLote;
/**
    * @var Object
    * @access Private
*/
var $obContabilidadeHistoricoPadrao;
/**
    * @var Integer
    * @access Private
*/
var $inSequencia;
/**
    * @var String
    * @access Private
*/
var $stComplemento;
/**
    * @var Boolean
    * @access Private
*/
var $boComplemento;
/**
    * @var Boolean
    * @access Private
*/
var $boGravarBkpLancamento;

/**
    * @access Public
    * @param Object $valor
*/
function setTContabilidadeLancamento($valor) { $this->obTContabilidadeLancamento = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRContabilidadeLote($valor) { $this->obRContabilidadeLote  = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setRContabilidadeHistoricoPadrao($valor) { $this->obRContabilidadeHistoricoPadrao = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setSequencia($valor) { $this->inSequencia   = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setComplemento($valor) { $this->stComplemento = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setBoComplemento($valor) { $this->boComplemento = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setBoGravarBkpLancamento($valor) { $this->boGravarBkpLancamento = $valor; }

/**
    * @access Public
    * @return Object
*/
function getTContabilidadeLancamento() { return $this->obTContabilidadeLancamento; }
/**
     * @access Public
     * @param Object $valor
*/
function getTransacao() { return $this->obTransacao; }
/**
     * @access Public
     * @param Object $valor
*/
function getRContabilidadeLote() { return $this->obRContabilidadeLote; }
/**
     * @access Public
     * @param Object $valor
*/
function getRcontabilidadeHistoricoPadrao() { return $this->obRContabilidadeHistoricoPadrao; }
/**
     * @access Public
     * @param Integer $valor
*/
function getSequencia() { return $this->inSequencia;   }
/**
     * @access Public
     * @param String $valor
*/
function getComplemento() { return $this->stComplemento; }
/**
     * @access Public
     * @param Boolean $valor
*/
function getBoComplemento() { return $this->boComplemento; }
/**
     * @access Public
     * @param Boolean $valor
*/
function getBoGravarBkpLancamento() { return $this->boGravarBkpLancamento; }

/**
    * Método Construtor
    * @access Private
*/
function RContabilidadeLancamento()
{
    $this->obRContabilidadeHistoricoPadrao = new RContabilidadeHistoricoPadrao;
    $this->obRContabilidadeLote            = new RContabilidadeLote;
    $this->obTransacao                     = new Transacao;
}

/**
    * Inclui Lancamento no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if( !$this->boComplemento and !$this->stComplemento )
            $obErro->setDescricao("Campo Complemento é obrigatório!");

        if ( !$obErro->ocorreu() ) {
            $obTContabilidadeLancamento->proximoCod( $inSequencia, $boTransacao );
            $this->inSequencia = $inSequencia;

            $obTContabilidadeLancamento->setDado( "sequencia"     , $this->inSequencia    );
            $obTContabilidadeLancamento->setDado( "cod_lote"      , $this->obRContabilidadeLote->getCodLote()   );
            $obTContabilidadeLancamento->setDado( "tipo"          , $this->obRContabilidadeLote->getTipo()      );
            $obTContabilidadeLancamento->setDado( "exercicio"     , $this->obRContabilidadeLote->getExercicio() );
            $obTContabilidadeLancamento->setDado( "cod_entidade"  , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
            $obTContabilidadeLancamento->setDado( "cod_historico" , $this->obRContabilidadeHistoricoPadrao->getCodHistorico() );
            $obTContabilidadeLancamento->setDado( "complemento"   , $this->getComplemento() );

            $obErro = $obTContabilidadeLancamento->inclusao( $boTransacao );
         }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamento );

    return $obErro;
}

/**
    * Altera Lancamento no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if( $this->boComplemento and !$this->stComplemento )
            $obErro->setDescricao("Campo Complemento é obrigatório!");

        if ( !$obErro->ocorreu() ) {

            $obTContabilidadeLancamento->setDado( "sequencia"     , $this->inSequencia    );
            $obTContabilidadeLancamento->setDado( "cod_lote"      , $this->obRContabilidadeLote->getCodLote()   );
            $obTContabilidadeLancamento->setDado( "tipo"          , $this->obRContabilidadeLote->getTipo()      );
            $obTContabilidadeLancamento->setDado( "exercicio"     , $this->obRContabilidadeLote->getExercicio() );
            $obTContabilidadeLancamento->setDado( "cod_entidade"  , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
            $obTContabilidadeLancamento->setDado( "cod_historico" , $this->obRContabilidadeHistoricoPadrao->getCodHistorico() );
            $obTContabilidadeLancamento->setDado( "complemento"   , $this->getComplemento() );

            $obErro = $obTContabilidadeLancamento->alteracao( $boTransacao );
        }

    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamento );

    return $obErro;
}

/**
    * Altera Lancamento no Banco de Dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterarCodHistorico($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obErro = $this->consultarLancamento( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            if (!$this->boGravarBkpLancamento) {

                $obTContabilidadeLancamento->setTabela('contabilidade.lancamento');

                $obTContabilidadeLancamento->setDado( "sequencia"     , $this->inSequencia    );
                $obTContabilidadeLancamento->setDado( "cod_lote"      , $this->obRContabilidadeLote->getCodLote()   );
                $obTContabilidadeLancamento->setDado( "tipo"          , $this->obRContabilidadeLote->getTipo()      );
                $obTContabilidadeLancamento->setDado( "exercicio"     , $this->obRContabilidadeLote->getExercicio() );
                $obTContabilidadeLancamento->setDado( "cod_entidade"  , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamento->setDado( "cod_historico" , "800" );
                $obTContabilidadeLancamento->setDado( "complemento"   , $this->getComplemento() );

                $obErro = $obTContabilidadeLancamento->alteracao( $boTransacao );

            } else {

                $obTContabilidadeLancamento->setTabela('sw_bkp.contabilidade_ajusta_historico');

                $obTContabilidadeLancamento->setDado( "sequencia"     , $this->inSequencia    );
                $obTContabilidadeLancamento->setDado( "cod_lote"      , $this->obRContabilidadeLote->getCodLote()   );
                $obTContabilidadeLancamento->setDado( "tipo"          , $this->obRContabilidadeLote->getTipo()      );
                $obTContabilidadeLancamento->setDado( "exercicio"     , $this->obRContabilidadeLote->getExercicio() );
                $obTContabilidadeLancamento->setDado( "cod_entidade"  , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                $obTContabilidadeLancamento->setDado( "cod_historico" , $this->obRContabilidadeHistoricoPadrao->getCodHistorico() );
                $obTContabilidadeLancamento->setDado( "complemento"   , $this->getComplemento() );

                $obErro = $obTContabilidadeLancamento->inclusao( $boTransacao );
                if ( !$obErro->ocorreu() ) {

                    $obTContabilidadeLancamento->setTabela('contabilidade.lancamento');

                    $obTContabilidadeLancamento->setDado( "sequencia"     , $this->inSequencia    );
                    $obTContabilidadeLancamento->setDado( "cod_lote"      , $this->obRContabilidadeLote->getCodLote()   );
                    $obTContabilidadeLancamento->setDado( "tipo"          , $this->obRContabilidadeLote->getTipo()      );
                    $obTContabilidadeLancamento->setDado( "exercicio"     , $this->obRContabilidadeLote->getExercicio() );
                    $obTContabilidadeLancamento->setDado( "cod_entidade"  , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
                    $obTContabilidadeLancamento->setDado( "cod_historico" , "800" );
                    $obTContabilidadeLancamento->setDado( "complemento"   , $this->getComplemento() );

                    $obErro = $obTContabilidadeLancamento->alteracao( $boTransacao );
                }
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamento );

    return $obErro;
}

/**
    * Método para verificar se lote está sendo usado ou não
    * @access Private
    * @param Object $boTransacao
    * @param Object $boExcluirLote
    * @return Object $obErro
*/
function validarLote(&$boExcluirLote, $boTransacao = "")
{
    $inCodHistorico = $this->obRContabilidadeHistoricoPadrao->getCodHistorico();
    $inSequencia    = $this->inSequencia;
    $stComplemento  = $this->obRContabilidadeHistoricoPadrao->getComplemento();

    $this->inSequencia = '';
    $this->obRContabilidadeHistoricoPadrao->setCodHistorico( '' );
    $this->obRContabilidadeHistoricoPadrao->setComplemento( '' );

    $obErro = $this->listar( $rsRecordSet, '',$boTransacao );
    if ( !$obErro->ocorreu() ) {
        $this->inSequencia = $inSequencia;
        $this->obRContabilidadeHistoricoPadrao->setComplemento( $stComplemento );
        $this->obRContabilidadeHistoricoPadrao->setCodHistorico( $inCodHistorico );

        if ( $rsRecordSet->eof() ) {
            $boExcluirLote = true;
        }
    }

    return $obErro;
}

/**
    * Exclui dados do Lancamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLancamento->setDado("sequencia"   , $this->inSequencia );
        $obTContabilidadeLancamento->setDado("exercicio"   , $this->obRContabilidadeLote->getExercicio()   );
        $obTContabilidadeLancamento->setDado("cod_lote"    , $this->obRContabilidadeLote->getCodLote()     );
        $obTContabilidadeLancamento->setDado("tipo"        , $this->obRContabilidadeLote->getTipo()        );
        $obTContabilidadeLancamento->setDado("cod_entidade", $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTContabilidadeLancamento->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->validarLote( $boExcluirLote, $boTransacao );
            if ( !$obErro->ocorreu() and $boExcluirLote ) {
                $obErro = $this->obRContabilidadeLote->excluir( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamento );

    return $obErro;
}

/**
    * Exclui dados do Lancamento do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluirImplantado($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $obErro = new Erro;
    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTContabilidadeLancamento->setCampoCod('');
        $obTContabilidadeLancamento->setComplementoChave('cod_lote,tipo,exercicio,cod_entidade,sequencia');

        $obTContabilidadeLancamento->setDado("sequencia"   , $this->inSequencia );
        $obTContabilidadeLancamento->setDado("exercicio"   , $this->obRContabilidadeLote->getExercicio()   );
        $obTContabilidadeLancamento->setDado("cod_lote"    , $this->obRContabilidadeLote->getCodLote()     );
        $obTContabilidadeLancamento->setDado("tipo"        , $this->obRContabilidadeLote->getTipo()        );
        $obTContabilidadeLancamento->setDado("cod_entidade", $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );
        $obErro = $obTContabilidadeLancamento->exclusao( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->validarLote( $boExcluirLote, $boTransacao );
            if ( !$obErro->ocorreu() and $boExcluirLote ) {
                $obErro = $this->obRContabilidadeLote->excluirImplantado( $boTransacao );
            }
        }
    }
    $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTContabilidadeLancamento );

    return $obErro;
}

/**
    * Lista todos os Lancamentos de acordo com o filtro
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "" , $obTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $stFiltro = "";

    if( $this->inSequencia )
        $stFiltro  = " sequencia = ".$this->inSequencia." AND ";
    if( $this->obRContabilidadeLote->getExercicio() )
        $stFiltro .= " exercicio = '".$this->obRContabilidadeLote->getExercicio()."' AND ";
    if( $this->obRContabilidadeLote->getCodLote() )
        $stFiltro .= " cod_lote = ". $this->obRContabilidadeLote->getCodLote()." AND  ";
    if( $this->obRContabilidadeLote->getTipo() )
        $stFiltro .= " tipo = '".$this->obRContabilidadeLote->getTipo()."' AND ";
    if( $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() )
        $stFiltro .= " cod_entidade = ".$this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade()." AND ";
    if( $this->obRContabilidadeHistoricoPadrao->getCodHistorico() )
        $stFiltro .= " cod_historico = ".$this->obRContabilidadeHistoricoPadrao->getCodHistorico(). " AND ";
    if( $this->stComplemento )
        $stFiltro .= " LOWER(complemento) like LOWER('%".$this->stComplemento."%') AND ";

    $stFiltro = ($stFiltro)? " WHERE ".substr($stFiltro, 0, strlen($stFiltro)-4) : "";

    $obErro = $obTContabilidadeLancamento->recuperaTodos( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;

    $obTContabilidadeLancamento->setDado( "sequencia"    , $this->inSequencia );
    $obTContabilidadeLancamento->setDado( "exercicio"    , $this->obRContabilidadeLote->getExercicio()   );
    $obTContabilidadeLancamento->setDado( "cod_lote"     , $this->obRContabilidadeLote->getCodLote()     );
    $obTContabilidadeLancamento->setDado( "tipo"         , $this->obRContabilidadeLote->getTipo()        );
    $obTContabilidadeLancamento->setDado( "cod_entidade" , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );

    $obErro = $obTContabilidadeLancamento->recuperaPorChave( $rsRecordSet, $boTransacao );

    if ( !$obErro->ocorreu() ) {
        $this->obRContabilidadeHistoricoPadrao->setCodHistorico ( $rsRecordSet->getCampo("cod_historico") );
        $this->setComplemento   ( $rsRecordSet->getCampo("complemento") );
        $obErro = $this->obRContabilidadeHistoricoPadrao->consultar( $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obErro = $this->obRContabilidadeLote->consultar( $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Executa um recuperaPorChave na classe Persistente
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarLancamento($boTransacao = "")
{
    include_once ( CAM_GF_CONT_MAPEAMENTO."TContabilidadeLancamento.class.php" );
    $obTContabilidadeLancamento      = new TContabilidadeLancamento;
    $obTContabilidadeLancamento->setTabela('sw_bkp.contabilidade_ajusta_historico');

    $obTContabilidadeLancamento->setDado( "sequencia"    , $this->inSequencia );
    $obTContabilidadeLancamento->setDado( "exercicio"    , $this->obRContabilidadeLote->getExercicio()   );
    $obTContabilidadeLancamento->setDado( "cod_lote"     , $this->obRContabilidadeLote->getCodLote()     );
    $obTContabilidadeLancamento->setDado( "tipo"         , $this->obRContabilidadeLote->getTipo()        );
    $obTContabilidadeLancamento->setDado( "cod_entidade" , $this->obRContabilidadeLote->obROrcamentoEntidade->getCodigoEntidade() );

    $obErro = $obTContabilidadeLancamento->recuperaPorChave( $rsRecordSet, $boTransacao );
    if ( !$obErro->ocorreu() ) {

        if ($rsRecordSet->getNumLinhas() == -1) {
            $this->boGravarBkpLancamento = true;
        } else {
            $this->boGravarBkpLancamento = false;
        }

        $obTContabilidadeLancamento->setTabela('contabilidade.lancamento');
        $obErro = $obTContabilidadeLancamento->recuperaPorChave( $rsRecordSet, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $this->setSequencia( $rsRecordSet->getCampo("sequencia") );
            $this->obRContabilidadeLote->setExercicio( $rsRecordSet->getCampo("exercicio") );
            $this->obRContabilidadeLote->setCodLote( $rsRecordSet->getCampo("cod_lote") );
            $this->obRContabilidadeLote->setTipo( $rsRecordSet->getCampo("tipo") );
            $this->obRContabilidadeLote->obROrcamentoEntidade->setCodigoEntidade( $rsRecordSet->getCampo("cod_entidade") );
            $this->obRContabilidadeHistoricoPadrao->setCodHistorico( $rsRecordSet->getCampo("cod_historico") );
            $this->setComplemento( $rsRecordSet->getCampo("complemento") );
        }
    }

    return $obErro;
}
}

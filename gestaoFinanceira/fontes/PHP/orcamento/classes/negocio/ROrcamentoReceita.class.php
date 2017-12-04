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
    * Classe de Regra de Negócio Itens
    * Data de Criação   : 02/08/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: ROrcamentoReceita.class.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.01.06, uc-02.02.01, uc-02.04.04
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once( CAM_GF_ORC_NEGOCIO        ."ROrcamentoRecurso.class.php"                 );
include_once( CAM_GF_ORC_NEGOCIO        ."ROrcamentoEntidade.class.php"       );
include_once( CAM_GF_ORC_NEGOCIO        ."ROrcamentoPrevisaoOrcamentaria.class.php"    );
include_once( CAM_GF_ORC_NEGOCIO        ."ROrcamentoClassificacaoReceita.class.php"    );
include_once( CAM_FW_BANCO_DADOS."Transacao.class.php"                );

class ROrcamentoReceita
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
var $obROrcamentoPrevisaoOrcamentaria;
/**
    * @var Object
    * @access Private
*/
var $obROrcamentoClassificacaoReceita;
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
var $inCodigoReceita;
/**
    * @var Numeric
    * @access Private
*/
var $nuValorOriginal;
/**
    * @var Numeric
    * @access Private
*/
var $nuPercentualDesdobramento;
/**
    * @var Booolean
    * @access Private
*/
var $boCreditoTributario;

/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoRecurso($valor) { $this->obROrcamentoRecurso              = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoEntidade($valor) { $this->obROrcamentoEntidade    = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoPrevisaoOrcamentaria($valor) { $this->obROrcamentoPrevisaoOrcamentaria = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setROrcamentoClassificacaoReceita($valor) { $this->obROrcamentoClassificacaoReceita = $valor; }
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
function setCodReceita($valor) { $this->inCodigoReceita          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setValorOriginal($valor) { $this->nuValorOriginal          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setPercentualDesdobramento($valor) { $this->nuPercentualDesdobramento = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setCreditoTributario($valor) { $this->boCreditoTributario = $valor; }

/**
     * @access Public
     * @return Object
*/
function getROrcamentoRecurso() { return $this->obROrcamentoRecurso;              }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoEntidade() { return $this->obROrcamentoEntidade;   }
/**
     * @access Public
     * @return Object
*/
function getROrcamentoPrevisaoOrcamentaria() { return $this->obROrcamentoPrevisaoOrcamentaria;}
/**
     * @access Public
     * @return Object
*/
function getROrcamentoClassificacaoReceita() { return $this->obROrcamentoClassificacaoReceita;}
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
function getCodReceita() { return $this->inCodigoReceita;         }
/**
     * @access Public
     * @return Numeric
*/
function getValorOriginal() { return $this->nuValorOriginal;         }
/**
     * @access Public
     * @return Numeric
*/
function getPercentualDesdobramento() { return $this->nuPercentualDesdobramento; }
/**
     * @access Public
     * @return Boolean
*/
function getCreditoTributario() { return $this->boCreditoTributario; }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoReceita()
{
    $this->setROrcamentoPrevisaoOrcamentaria( new ROrcamentoPrevisaoOrcamentaria );
    $this->setROrcamentoRecurso             ( new ROrcamentoRecurso              );
    $this->setROrcamentoEntidade            ( new ROrcamentoEntidade    );
    $this->setROrcamentoClassificacaoReceita( new ROrcamentoClassificacaoReceita );
    $this->setTransacao                     ( new Transacao             );
    $this->setExercicio                     ( Sessao::getExercicio()        );
}

/**
    * Método para validar se uma receita possui valor orçado para conta mãe ou filha
    * @access Private
    * @param Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function validarReceitaOrcada($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
    $obTOrcamentoReceita = new TOrcamentoReceita;
    $obErro = $this->listarReceita( $rsRecordSet, '', $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodEstruturalAtual = $rsRecordSet->getCampo("mascara_classificacao");

        if ( $inCodEstruturalAtual != $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
            $stFiltro = "";
            if( $this->stExercicio )
                $stFiltro .= " AND ORE.exercicio = '".$this->stExercicio."' ";
            if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
                $stFiltro .= " AND( OCR.cod_estrutural like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."')||'%' ";
                $stFiltro .= " OR  OCR.cod_estrutural like publico.fn_mascarareduzida('".$inCodEstruturalAtual."' ) )";
            }
            if ($this->getCodReceita()) {
                $stFiltro .= " AND ORE.cod_receita <> ".$this->getCodReceita()." ";
            }
            $obErro = $obTOrcamentoReceita->recuperaRelacionamentoContaReceita( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
            if ( !$obErro->ocorreu() ) {
                if ( !$rsRecordSet->eof() ) {
                    while ( !$rsRecordSet->eof() ) {
                        $stContasComValor .= $rsRecordSet->getCampo( 'cod_estrutural' ).', ';
                        $rsRecordSet->proximo();
                    }
                    $obErro->setDescricao( 'A(s) Conta(s) '.substr($stContasComValor,0,strlen($stContasComValor)-2).' já possuem valor orçado' );
                }
            }
        }
    }

    return $obErro;
}

function recuperaEntidadeAnalitica(&$rsRecordSet,$stFiltro)
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
    $obTOrcamentoReceita = new TOrcamentoReceita;
    $stOrdem    = " ORDER BY conta_receita.cod_estrutural \n";
    $obErro     = $obTOrcamentoReceita->recuperaRelacionamentoEntidadeAnalitica( $rsRecordSet, $stFiltro, $stOrdem );
    if (!$obErro->ocorreu()) {
        if (!$rsRecordSet->eof()) {
           $this->setCodReceita($rsRecordSet->getCampo("cod_receita"));
        }
    }

  return $obErro;
}

function recuperaEntidadeSintetica(&$rsRecordSet,$stFiltro)
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php" );
    $obTOrcamentoReceita = new TOrcamentoReceita;
    
    $obErro     = $obTOrcamentoReceita->recuperaRelacionamentoEntidadeSintetica( $rsRecordSet, $stFiltro );
    if (!$obErro->ocorreu()) {
        if (!$rsRecordSet->eof()) {
           $this->setCodReceita($rsRecordSet->getCampo("cod_receita"));
        }
    }

  return $obErro;
}

/**
    * Salva fixação de despesa no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function salvar($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoReceita->setDado( "exercicio"    , $this->getExercicio()                            );
        $obTOrcamentoReceita->setDado( "vl_original"  , $this->getValorOriginal()                        );
        $obTOrcamentoReceita->setDado( "cod_entidade" , $this->obROrcamentoEntidade->getCodigoEntidade() );
        $obTOrcamentoReceita->setDado( "cod_recurso"  , $this->obROrcamentoRecurso->getCodRecurso()               );
        $obTOrcamentoReceita->setDado( "cod_conta"    , $this->obROrcamentoClassificacaoReceita->getCodConta()    );
        $obTOrcamentoReceita->setDado( "credito_tributario"    , $this->getCreditoTributario()    );
        if ( $this->getCodReceita() ) {
            $obTOrcamentoReceita->setDado( "cod_receita" , $this->getCodReceita() );
            $obErro = $obTOrcamentoReceita->alteracao( $boTransacao );
        } else {
            $obErro = $obTOrcamentoReceita->proximoCod ( $inCodReceita , $boTransacao );
            $obTOrcamentoReceita->setDado( "cod_receita" , $inCodReceita );
            $obErro = $obTOrcamentoReceita->inclusao( $boTransacao );
            $this->inCodigoReceita = $obTOrcamentoReceita->getDado('cod_receita');
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReceita );
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
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoReceita.class.php"        );
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoPrevisaoReceita.class.php"      );
    include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeDesdobramentoReceita.class.php" );
    $obTContabilidadeDesdobramentoReceita   = new TContabilidadeDesdobramentoReceita;
    $obTOrcamentoReceita                    = new TOrcamentoReceita;
    $obTOrcamentoPrevisaoReceita            = new TOrcamentoPrevisaoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoPrevisaoReceita->setDado( "cod_receita" , $this->getCodReceita() );
        $obTOrcamentoPrevisaoReceita->setDado( "exercicio"   , $this->getExercicio()  );
        $obErro = $obTOrcamentoPrevisaoReceita->recuperaLimpaReceita( $rsLista, "", "", $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $obTContabilidadeDesdobramentoReceita->setDado( "exercicio", $this->getExercicio() );
            $obTContabilidadeDesdobramentoReceita->setDado( "cod_receita_principal", $this->getCodReceita() );
            $obErro = $obTContabilidadeDesdobramentoReceita->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTOrcamentoReceita->setDado( "cod_receita" , $this->getCodReceita() );
                $obTOrcamentoReceita->setDado( "exercicio"   , $this->getExercicio()  );
                $obErro = $obTOrcamentoReceita->exclusao( $boTransacao );
            }
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoReceita );
    }

    return $obErro;
}

/**
    * Executa um recuperaRelacionamento na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CR.exercicio = '".$this->getExercicio()."'";
        $obTOrcamentoReceita->setDado('exercicio', $this->getExercicio());
    }
    if ( $this->getCodReceita() ) {
        $stFiltro .= " AND cod_receita = ".$this->getCodReceita();
    }
    if ( $this->obROrcamentoClassificacaoReceita->getDescricao() ) {
        $stFiltro .= " AND lower(CR.descricao) like lower('%".$this->obROrcamentoClassificacaoReceita->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
        $stFiltro .= " AND lower(CR.mascara_classificacao) like lower('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."%')";
    }
    $obErro = $obTOrcamentoReceita->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}
/**
    * Executa um recuperaRecita na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReceita(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;
    $stOrder = " ORDER BY cod_receita ";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CR.exercicio = '".$this->getExercicio()."'";
        $obTOrcamentoReceita->setDado( 'exercicio', $this->getExercicio() );
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND O.cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) \n";
    }
    if ( $this->getCodReceita() ) {
        $stFiltro .= " AND cod_receita = ".$this->getCodReceita();
    }
    if ( $this->obROrcamentoClassificacaoReceita->getDescricao() ) {
        $stFiltro .= " AND lower(CR.descricao) like lower('%".$this->obROrcamentoClassificacaoReceita->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
        $stFiltro .= " AND CR.mascara_classificacao like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."')||'%' ";
    }
    $obErro = $obTOrcamentoReceita->recuperaReceita( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaReceita na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReceitaAnalitica(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;
    $stFiltro = "";
    $stOrder = " ORDER BY mascara_classificacao ";

    if (Sessao::getExercicio() <= 2013) {
        if ( $this->getExercicio() ) {
            $stFiltro  = " AND RECEITA.exercicio = '".Sessao::getExercicio()."'";

        }
        if ( $this->getCodReceita() ) {
            $stFiltro .= " AND RECEITA.cod_receita = ".$this->getCodReceita()." ";
        }
        if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
            $stFiltro .= " AND RECEITA.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." ";
        }

        $stFiltro .= " AND NOT EXISTS (  SELECT dr.cod_receita_secundaria
                                           FROM contabilidade.desdobramento_receita as dr
                                          WHERE receita.cod_receita = dr.cod_receita_secundaria
                                            AND receita.exercicio   = dr.exercicio ) ";

        if ( Sessao::getExercicio() > '2012' ) {
            $stFiltro .= "AND CLR.estorno = 'f'";
            $obErro = $obTOrcamentoReceita->recuperaReceitaAnaliticaTCE( $rsLista, $stFiltro, $stOrder, $boTransacao );
        } else {
            $obErro = $obTOrcamentoReceita->recuperaReceitaAnalitica( $rsLista, $stFiltro, $stOrder, $boTransacao );
        }

    } else {

        if ( $this->getExercicio() ) {
            $stFiltro .= " AND CLASSIFICACAO.exercicio = '".$this->getExercicio()."'";
        }
        if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
            $stFiltro .= " AND RECEITA.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade();
        }
        if ( $this->getCodReceita() ) {
            $stFiltro .= " AND cod_receita = ".$this->getCodReceita();
        }
        if ( $this->obROrcamentoClassificacaoReceita->getDescricao() ) {
            $stFiltro .= " AND lower(CLASSIFICACAO.descricao) like lower('%".$this->obROrcamentoClassificacaoReceita->getDescricao()."%')";
        }
        if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
            $stFiltro .= " AND CLASSIFICACAO.mascara_classificacao like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."')||'%' ";
        }

        if ( Sessao::getExercicio() > '2012' ) {
            $stFiltro .= "AND CLR.estorno = 'f'";
            $obErro = $obTOrcamentoReceita->recuperaReceitaAnaliticaTCE( $rsLista, $stFiltro, $stOrder, $boTransacao );
        } else {
            $obErro = $obTOrcamentoReceita->recuperaReceitaAnalitica( $rsLista, $stFiltro, $stOrder, $boTransacao );
        }
    }

    return $obErro;
}
/**
    * Executa um recuperaRecita na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReceitaDedutora(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;
    $stFiltro = "";
    $stOrder = " ORDER BY cod_receita ";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND CR.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND O.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade();
    }
    if ( $this->getCodReceita() ) {
        $stFiltro .= " AND cod_receita = ".$this->getCodReceita();
    }
    if ( $this->obROrcamentoClassificacaoReceita->getDescricao() ) {
        $stFiltro .= " AND lower(CR.descricao) like lower('%".$this->obROrcamentoClassificacaoReceita->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
        $stFiltro .= " AND CR.mascara_classificacao like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."')||'%' ";
    }

    $obErro = $obTOrcamentoReceita->recuperaReceitaDedutora( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

/**
    * Executa um recuperaReceita na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReceitaDedutoraAnalitica(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;
    $stFiltro = "";
    $stOrder = " ORDER BY cod_receita ";
    if ( $this->getExercicio() ) {
        $obTOrcamentoReceita->setDado('exercicio', $this->getExercicio() );
        $stFiltro .= " AND CLASSIFICACAO.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND RECEITA.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade();
    }
    if ( $this->getCodReceita() ) {
        $stFiltro .= " AND cod_receita = ".$this->getCodReceita();
    }
    if ( $this->obROrcamentoClassificacaoReceita->getDescricao() ) {
        $stFiltro .= " AND lower(CLASSIFICACAO.descricao) like lower('%".$this->obROrcamentoClassificacaoReceita->getDescricao()."%')";
    }
    if ( $this->obROrcamentoClassificacaoReceita->getMascClassificacao() ) {
        $stFiltro .= " AND CLASSIFICACAO.mascara_classificacao like publico.fn_mascarareduzida('".$this->obROrcamentoClassificacaoReceita->getMascClassificacao()."')||'%' ";
    }

    $obErro = $obTOrcamentoReceita->recuperaReceitaDedutoraAnalitica( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $obTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;

    $stFiltro = "";
    if ( $this->getCodReceita() ) {
        $stFiltro .= " cod_receita = ".$this->getCodReceita() ." AND ";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND cod_entidade in ( ".$this->obROrcamentoEntidade->getCodigoEntidade()." ) \n";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " exercicio = '".$this->getExercicio() . "' AND ";
    }
    if ($stFiltro) {
            $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    }
    $obErro = $obTOrcamentoReceita->recuperaTodos( $rsLista, $stFiltro,"", $obTransacao );
      
    return $obErro;
}

/**
    * Verifica se pode ser inserido valor na Classificacao indicada
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaValorConta(&$stSumConta , $stFiltro, $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."FOrcamentoRelacaoReceita.class.php" );
    $obFOrcamentoRelacaoReceita  = new FOrcamentoRelacaoReceita;

    $obFOrcamentoRelacaoReceita->setDado("exercicio", $this->getExercicio());

    $obErro = $obFOrcamentoRelacaoReceita->consultaValorConta( $rsLista, $stFiltro, $stOrder, $boTransacao );
    
    $stSumConta = 0;
    if ( $rsLista->getNumLinhas() > -1 ) {
        $stSumConta = $rsLista->getCampo('sum');
    } else {
        $stSumConta = "0.00";
    }

    return $obErro;
}

/**
    * Verifica se pode ser inserido valor na Classificacao indicada
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaLancamentoAnterior(&$rsLista , $stFiltro, $stGrupo, $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO."FOrcamentoRelacaoReceita.class.php" );
    $obFOrcamentoRelacaoReceita  = new FOrcamentoRelacaoReceita;
    $obFOrcamentoRelacaoReceita->setDado("exercicio", $this->getExercicio());

    $obErro = $obFOrcamentoRelacaoReceita->consultaLancamentoAnterior( $rsLista, $stFiltro, $stGrupo, "ORDER BY ordem_estrutural", $boTransacao );
    
    return $obErro;
}

/**
    * Verifica se existe o relacionamento entre a receita e um determiinada entidade
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function verificaRelacionamentoReceitaEntidade($boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;

    $stFiltro  = " WHERE ";
    $stFiltro .= " exercicio = '".$this->stExercicio."' AND ";
    $stFiltro .= " cod_receita = ".$this->inCodigoReceita." AND ";
    $stFiltro .= " cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade()." ";
    $obErro = $obTOrcamentoReceita->recuperaTodos( $rsLista, $stFiltro, "", $boTransacao );
    if ( $rsLista->eof() and !$obErro->ocorreu() ) {
        $obErro->setDescricao( "A receita não pertence a entidade selecionada!" );
    }

    return $obErro;
}

/**
    * Executa um recuperaReceita na classe Persistente Previsão Receita
    * @access Public
    * @param  Object $rsListaCategoria Retorna o RecordSet preenchido
    * @param  String $stOrdem Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listarReceitaConfiguracaoLancamento(&$rsLista, $stFiltro = "", $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoReceita.class.php"        );
    $obTOrcamentoReceita         = new TOrcamentoReceita;
    $stFiltro = "";
    $stOrder = " ORDER BY conta_receita.cod_estrutural ";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND receita.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->obROrcamentoEntidade->getCodigoEntidade() ) {
        $stFiltro .= " AND receita.cod_entidade = ".$this->obROrcamentoEntidade->getCodigoEntidade();
    }
    if ( $this->getCodReceita() ) {
        $stFiltro .= " AND receita.cod_receita = ".$this->getCodReceita();
    }

    $obErro = $obTOrcamentoReceita->recuperaReceitaConfiguracaoLancamento( $rsLista, $stFiltro, $stOrder, $boTransacao );

    return $obErro;
}

}

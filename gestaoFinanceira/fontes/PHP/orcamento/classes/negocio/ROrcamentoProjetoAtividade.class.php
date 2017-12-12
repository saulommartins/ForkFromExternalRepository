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
    * Data de Criação   : 14/07/2004

    $Id: ROrcamentoProjetoAtividade.class.php 59612 2014-09-02 12:00:51Z gelson $

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.03
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"      );
include_once ( CAM_GF_ORC_NEGOCIO."ROrcamentoConfiguracao.class.php" );

/**
    * Classe de Regra de Negócio Itens
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoProjetoAtividade
{
/**
    * @var Object
    * @access Private
*/
var $obTransacao;
/**
    * @var Integer
    * @access Private
*/
var $inNumeroProjetoAtividade;
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var String
    * @access Private
*/
var $stNome;
/**
    * @var String
    * @access Private
*/
var $stDetalhamento;
/**
    * @var Objeto
    * @access Private
*/
var $obRConfiguracaoOrcamento;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var Integer
    * @access Private
*/
var $inTipo;

/**
     * @access Public
     * @param Object $valor
*/
function setTransacao($valor) { $this->obTransacao               = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setNumeroProjeto($valor) { $this->inNumeroProjetoAtividade  = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setExercicio($valor) { $this->stExercicio               = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setNome($valor) { $this->stNome                    = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDetalhamento($valor) { $this->stDetalhamento            = $valor; }
/**
    * @access Public
    * @param Object $valor
*/
function setRConfiguracaoOrcamento($valor) { $this->obRConfiguracaoOrcamento = $valor; }
/**
    * @access Public
    * @param String $valor
*/
function setMascara($valor) { $this->stMascara                 = $valor; }
/**
    * @access Public
    * @param Integer $valor
*/
function setCodTipo($valor) { $this->inCodTipo                 = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;              }
/**
     * @access Public
     * @return Integer
*/
function getNumeroProjeto() { return $this->inNumeroProjetoAtividade; }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;              }
/**
     * @access Public
     * @return String
*/
function getNome() { return $this->stNome;                   }
/**
     * @access Public
     * @return String
*/
function getDetalhamento() { return $this->stDetalhamento;           }
/**
    * @access Public
    * @return Object
*/
function getRConfiguracaoOrcamento() { return $this->obRConfiguracaoOrcamento;      }
/**
    * @access Public
    * @return String
*/
function getMascara() { return $this->stMascara;      }
/**
    * @access Public
    * @return String
*/
function getCodTipo() { return $this->inCodTipo;      }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoProjetoAtividade()
{
    $this->setRConfiguracaoOrcamento( new ROrcamentoConfiguracao );
    $this->setTransacao             ( new Transacao              );
    $this->setExercicio             ( Sessao::getExercicio()         );
}

/**
    * Inclui o PAO no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
            $obTOrcamentoProjetoAtividade->setDado( "num_pao"      , $this->getNumeroProjeto() );
            $obTOrcamentoProjetoAtividade->setDado( "exercicio"    , $this->getExercicio()     );
            $obTOrcamentoProjetoAtividade->setDado( "nom_pao"      , $this->getNome()          );
            $obTOrcamentoProjetoAtividade->setDado( "detalhamento" , $this->getDetalhamento()  );
            $obErro = $obTOrcamentoProjetoAtividade->recuperaPorChave($rsPAO, $stFiltro,'',$boTransacao);
            if ( !$obErro->ocorreu() ) {
                if ( $rsPAO->eof() ) {
                    $obErro = $obTOrcamentoProjetoAtividade->inclusao( $boTransacao );
                } else {
                    $obErro->setDescricao("Código ".$this->getNumeroProjeto()." já cadastrado!");
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoProjetoAtividade );
            }
    }

    return $obErro;
}

/**
    * Alterar o PAO no banco de dados
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoProjetoAtividade->setDado( "num_pao"      , $this->getNumeroProjeto() );
        $obTOrcamentoProjetoAtividade->setDado( "exercicio"    , $this->getExercicio()     );
        $obTOrcamentoProjetoAtividade->setDado( "nom_pao"      , $this->getNome()          );
        $obTOrcamentoProjetoAtividade->setDado( "detalhamento" , $this->getDetalhamento()  );
        $obErro = $obTOrcamentoProjetoAtividade->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoProjetoAtividade );
    }

    return $obErro;
}

/**
    * Exclui os dados do banco de dados
    * @access Public
    * @param  Object $obTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function excluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoProjetoAtividade->setDado( "num_pao"   , $this->getNumeroProjeto() );
        $obTOrcamentoProjetoAtividade->setDado( "exercicio" , $this->getExercicio()     );
        $obErro = $obTOrcamentoProjetoAtividade->exclusao( $boTransacao );
        if ($obErro->ocorreu()) {
            $obErro->setDescricao('PAO não pode ser excluído porque está sendo utilizado.');
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoProjetoAtividade );
    }

    return $obErro;
}

/**
    * Executa um recuperaTodos na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  String $stOrder Parâmetro de Ordenação
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function listar(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $this->pegarMascara($obTOrcamentoProjetoAtividade);
    $stFiltro = "";
    if ( $this->getNumeroProjeto() ) {
        $stFiltro .= " ppa.acao.num_acao = ".$this->getNumeroProjeto()." AND";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " orcamento.pao.exercicio = '" . $this->getExercicio() . "' AND";
    }
    if ( $this->getNome() ) {
        $stFiltro .= " lower(orcamento.pao.nom_pao)  like lower('%".$this->getNome()."%') AND";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $obTOrcamentoProjetoAtividade->recuperaMascarado( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

function listarSemMascara(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $stFiltro = "";
    if ( $this->getNumeroProjeto() ) {
        $stFiltro .= " orcamento.pao.num_pao = ".$this->getNumeroProjeto()." AND ";
    }
    if ( $this->getExercicio() ) {
        $stFiltro .= " orcamento.pao.exercicio    = '".$this->getExercicio()."' AND ";
    }
    if ( $this->getNome() ) {
        $stFiltro .= " lower(nom_pao)  like lower('%".$this->getNome()."%') AND";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $obTOrcamentoProjetoAtividade->recuperaSemMascara( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

function listarPorTipo(&$rsLista, $stOrder = "", $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $obErro = $this->obRConfiguracaoOrcamento->consultarConfiguracao( $boTransacao );
    if( !$obErro->ocorreu() ) $inPosicao = $this->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " exercicio = '" . $this->getExercicio() . "' AND";
    }
    if ($this->inCodTipo >= 0) {
        $arTipo = explode( ',', $this->inCodTipo );
        $stFiltro .= "(";
        foreach ($arTipo as $inCodTipo) {
            $stFiltro .= " substr( TRIM(TO_CHAR( num_pao, '0000' )),".$inPosicao.",1 ) = ".$inCodTipo." OR ";
        }
        $stFiltro = substr( $stFiltro, 0, strlen( $stFiltro ) - 4 ).")    ";
    }
    $stFiltro = " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro ) - 4 );
    $obErro = $obTOrcamentoProjetoAtividade->recuperaSemMascaraPorTipo( $rsLista, $stFiltro, $stOrder, $obTransacao );

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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;

    $obTOrcamentoProjetoAtividade->setDado( "exercicio" , $this->getExercicio()     );
    $obTOrcamentoProjetoAtividade->setDado( "num_pao"   , $this->getNumeroProjeto() );
    $obErro = $obTOrcamentoProjetoAtividade->recuperaPorChave( $rsLista, $obTransacao );

    return $obErro;
}

/**
    * Executa um recuperaPorNumAcao no mapeamento TOrcamentoProjetoAtividade
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarPorNumAcao(&$rsLista, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoProjetoAtividade.class.php"  );
    $obTOrcamentoProjetoAtividade         = new TOrcamentoProjetoAtividade;
    $stOrdem = null;
    if($this->getNumeroProjeto() != ""){
        $stSql  = "WHERE ppa.acao.num_acao = ".$this->getNumeroProjeto()." \n ";
        $stSql .= "AND pao.exercicio = '".$this->getExercicio()."' \n ";
    } else {
        $stSql  = "WHERE  pao.exercicio = '".$this->getExercicio()."' \n ";
    }
    
    

    $obErro = $obTOrcamentoProjetoAtividade->recuperaPorNumAcao( $rsLista, $stSql, $stOrdem, $obTransacao );

    return $obErro;
}

/**
    * Recupera a mascara do PAO
    * @access Public
*/
function pegarMascara(&$obTOrcamentoProjetoAtividadeParametro)
{
    $obErro = new Erro;
    $stMascara = $this->obRConfiguracaoOrcamento->consultarConfiguracaoEspecifica('masc_despesa');
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo X;
    $stMascara = $arMarcara[5];
    $this->setMascara( $stMascara );
    $obTOrcamentoProjetoAtividadeParametro->setDado( "stMascara"  , $this->getMascara() );

    return $obErro;
}

/**
    * Recupera a mascara do PAO
    * @access Public
*/
function buscarMascara()
{
    $this->obRConfiguracaoOrcamento->consultarConfiguracao();
    $stMascara = $this->obRConfiguracaoOrcamento->getMascDespesa();
    $arMarcara = preg_split( "/[^a-zA-Z0-9]/", $stMascara);

    // Grupo X;
    $stMascara = $arMarcara[5];
    $this->setMascara( $stMascara );
}

/**
    * Método para buscar proximo numero de PAO de acordo com o tipo( projeto, atividade, operação )
    * @access Public
    * @param  Object $boTransacao Objeto de Transação
    * @return Object $obErro      Objeto de Erro
*/
function buscaProximoCodPorTipo(&$inNumPao, $boTransacao = "")
{
    include_once( CAM_GF_ORC_MAPEAMENTO."TOrcamentoProjetoAtividade.class.php" );
    $obTOrcamentoProjetoAtividade = new TOrcamentoProjetoAtividade();

    $obErro = $this->obRConfiguracaoOrcamento->consultarConfiguracao( $boTransacao );
    if ( !$obErro->ocorreu() ) {

        $inPosicao = $this->obRConfiguracaoOrcamento->getNumPAOPosicaoDigitoID();
        $arTipoPAO = explode( ',', $this->inCodTipo );
        foreach ($arTipoPAO as $inCodTipo) {
            $stCodTipo = $stCodTipo.$inCodTipo.',';
        }
        $stCodTipo = substr($stCodTipo,0,strlen($stCodTipo)-1);
        $stFiltro = "";
        if( $this->stExercicio ) $stFiltro .= " exercicio = '" . $this->stExercicio . "' AND ";
        if( $this->inCodTipo >= 0 ) $stFiltro .= " substr( TRIM(TO_CHAR( num_pao, '0000' )),".$inPosicao.",1 ) in (".$stCodTipo.") AND ";
        $stFiltro = ( $stFiltro ) ? " WHERE ".substr( $stFiltro, 0, strlen( $stFiltro )-4 ) : '';
        $obErro = $obTOrcamentoProjetoAtividade->recuperaProximoCodPorTipo( $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
        if ( !$obErro->ocorreu() ) {
           $inNumPao = $rsRecordSet->getCampo( "num_pao" );
           $stAux1 = str_pad( "", $inPosicao-1, 9, STR_PAD_LEFT  );
           $stAux2 = str_pad( "", 4-$inPosicao, 9, STR_PAD_RIGHT );
           $inNumPaoMAX = $stAux1.$inCodTipo.$stAux2;
           if ($inNumPao < $inNumPaoMAX) {
              $inNumPao++;
           }
        }
        $inNumPao = str_pad( $inNumPao, 4, 0, STR_PAD_LEFT );
    }

    return $obErro;
}

}

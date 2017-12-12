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
    * Data de Criação   : 23/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    * @package URBEM
    * @subpackage Regra

    $Revision: 30824 $
    $Name$
    $Autor: $
    $Date: 2008-04-07 10:06:52 -0300 (Seg, 07 Abr 2008) $

    * Casos de uso: uc-02.01.04, uc-02.01.06
*/

/*
$Log$
Revision 1.14  2007/02/05 18:48:42  luciano
#8290#

Revision 1.13  2006/07/05 20:42:12  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php"                      );

/**
    * Classe de Regra de Negócio Itens
    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino
*/
class ROrcamentoClassificacaoDespesa
{
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
var $inCodigoClassificacao;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoPosicao;
/**
    * @var Integer
    * @access Private
*/
var $inCodigoConta;
/**
    * @var String
    * @access Private
*/
var $stDescricao;
/**
    * @var String
    * @access Private
*/
var $stMascClassificacao;
/**
    * @var String
    * @access Private
*/
var $stMascara;
/**
    * @var String
    * @access Private
*/
var $stCodEstrutural;
/**
    * @var Boolean
    * @access Private
*/
var $boListarAnaliticas;

var $stClassificacao;

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
function setCodClassificacao($valor) { $this->inCodigoClassificacao    = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodPosicao($valor) { $this->inCodigoPosicao          = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setCodConta($valor) { $this->inCodigoConta            = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setDescricao($valor) { $this->stDescricao              = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMascClassificacao($valor) { $this->stMascClassificacao      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setMascara($valor) { $this->stMascara                = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstrutural($valor) { $this->stCodEstrutural          = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setListarAnaliticas($valor) { $this->boListarAnaliticas       = $valor; }

function setClassificacao($valor) { $this->stClassificacao      = $valor; }

/**
     * @access Public
     * @return Object
*/
function getTransacao() { return $this->obTransacao;              }
/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio;              }
/**
     * @access Public
     * @return Integer
*/
function getCodClassificacao() { return $this->inCodigoClassificacao;    }
/**
     * @access Public
     * @return Integer
*/
function getCodPosicao() { return $this->inCodigoPosicao;          }
/**
     * @access Public
     * @return Integer
*/
function getCodConta() { return $this->inCodigoConta;            }
/**
     * @access Public
     * @return String
*/
function getDescricao() { return $this->stDescricao;              }
/**
     * @access Public
     * @return String
*/
function getMascClassificacao() { return $this->stMascClassificacao;      }
/**
     * @access Public
     * @return String
*/
function getMascara() { return $this->stMascara;                }
/**
     * @access Public
     * @return String
*/
function getCodEstrutural() { return $this->stCodEstrutural;          }
/**
     * @access Public
     * @return Boolean
*/
function getListarAnaliticas() { return $this->boListarAnaliticas;       }

function getClassificacao() { return $this->stClassificacao;             }

/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoClassificacaoDespesa()
{
    $this->setTransacao                     ( new Transacao                      );
    $this->setExercicio                     ( Sessao::getExercicio()                 );
}

/**
    * Inclui Classificacao de Despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"         );
    $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa;
    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;

    $obErro = new Erro;

    if ( !$obErro->ocorreu() ) {
        //Insere as Classificacoes da conta inserida na tabela ORCAMENTO.CLASSIFICACAO_DESPESA
        if ( !$obErro->ocorreu() ) {
            $obTOrcamentoClassificacaoDespesa->setDado( "exercicio"         , $this->getExercicio()        );
            $obTOrcamentoClassificacaoDespesa->setDado( "cod_classificacao" , $this->getCodClassificacao() );
            $obTOrcamentoClassificacaoDespesa->setDado( "cod_posicao"       , $this->getCodPosicao()       );
            $obTOrcamentoClassificacaoDespesa->setDado( "cod_conta"         , $this->getCodConta()         );
            $obErro = $obTOrcamentoClassificacaoDespesa->inclusao( $boTransacao );
        }
    }

    return $obErro;
}

/**
    * Altera Classificação de Despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"         );
    $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoContaDespesa->setDado( "cod_conta" , $this->getCodConta()  );
        $obTOrcamentoContaDespesa->setDado( "exercicio" , $this->getExercicio() );
        $obTOrcamentoContaDespesa->setDado( "descricao" , $this->getDescricao() );
        $obErro = $obTOrcamentoContaDespesa->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaDespesa );
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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaDespesa.class.php"         );
    $obTOrcamentoContaDespesa         = new TOrcamentoContaDespesa;
    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodConta = $this->getCodConta();
        $this->setCodConta(false);
        $obErro = $this->listar( $rsTesta, '', $boTransacao );
        $this->setCodConta($inCodConta);
        if (($rsTesta->getNumLinhas() > 1) and !$obErro->ocorreu()) {
            $obErro->setDescricao("Exclusão não Permitida");
        } elseif ( !$obErro->ocorreu() ) {
            $obTOrcamentoClassificacaoDespesa->setComplementoChave('exercicio,cod_conta');
            $obTOrcamentoClassificacaoDespesa->setDado( "cod_conta" , $this->getCodConta()  );
            $obTOrcamentoClassificacaoDespesa->setDado( "exercicio" , $this->getExercicio() );
            $obErro = $obTOrcamentoClassificacaoDespesa->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTOrcamentoContaDespesa->setDado( "cod_conta" , $this->getCodConta()  );
                $obTOrcamentoContaDespesa->setDado( "exercicio" , $this->getExercicio() );
                $obErro = $obTOrcamentoContaDespesa->exclusao( $boTransacao );
                if ($obErro->ocorreu()) {
                    $obErro->setDescricao('Rúbrica não pode ser excluída porque está sendo utilizada.');
                }
                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaDespesa );
            }
        }
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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php" );
    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodPosicao() ) {
        $stFiltro .= " AND cod_posicao = ".$this->getCodPosicao();
    }
    if ( $this->getCodConta() ) {
        $stFiltro .= " AND cod_conta = ".$this->getCodConta();
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " AND lower(descricao) like lower('%".$this->getDescricao()."%')";
    }
    if ( $this->getMascClassificacao() ) {
        $stFiltro .= " AND mascara_classificacao like '".$this->getMascClassificacao()."%' ";
    }
    if ( $this->getCodEstrutural() ) {
        $stFiltro .= " AND replace(mascara_classificacao, '.', '')=replace('".$this->getCodEstrutural()."', '.', '') ";
    }

    $obErro = $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php" );
    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getMascClassificacao() ) {
        $stFiltro .= " AND mascara_classificacao = '".$this->getMascClassificacao()."'";
    }
    if ( $this->getCodConta() ) {
        $stFiltro .= " AND cod_conta = ".$this->getCodConta()." ";
    }
    if ( $this->getListarAnaliticas() ) {
        $stFiltro .= " AND '3.'||mascara_classificacao||exercicio in (         \n";
        $stFiltro .= "    SELECT pc.cod_estrutural||pc.exercicio        \n";
        $stFiltro .= "    FROM                                          \n";
        $stFiltro .= "        contabilidade.plano_conta       as pc,    \n";
        $stFiltro .= "        contabilidade.plano_analitica   as pa     \n";
        $stFiltro .= "    WHERE                                         \n";
        $stFiltro .= "        pc.cod_conta = pa.cod_conta               \n";
        $stFiltro .= "    AND pc.exercicio = pa.exercicio               \n";
        $stFiltro .= "    AND pa.exercicio='".$this->getExercicio()."'  \n";
        $stFiltro .= " )                                                \n";
    }
    $obErro = $obTOrcamentoClassificacaoDespesa->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

/**
    * Recupera a mascara da Classificacao de Despesa
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMascara($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPosicaoDespesa.class.php"       );
    $obTOrcamentoPosicaoDespesa       = new TOrcamentoPosicaoDespesa;

    $obErro = $obTOrcamentoPosicaoDespesa->recuperaMascara( $this->getExercicio() );

    return $obErro;
}

/**
    * Busca a descrição da classificação de despesa informada
    * @access Public
    * @param  Object $stDescricaoDespesa Retorna a descricao da despesa
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaDescricaoDespesa(&$stDescricaoDespesa, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoDespesa.class.php" );
    $obTOrcamentoClassificacaoDespesa = new TOrcamentoClassificacaoDespesa;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND classificacao_despesa.exercicio = '".$this->getExercicio()."'";
    }
    // $stOrder = "ORDER by classificacao_despesa.cod_conta, classificacao_despesa.cod_posicao";
    $stOrder = "";
    $obErro = $obTOrcamentoClassificacaoDespesa->recuperaDescricaoDespesa( $rsLista, $stFiltro, $stOrder, $obTransacao );
    $inCodContaAtual = "";
    $stClassificacao = "";
    $stDescricaoDespesa = "";
    $stDescricaoAnterior = "";
    $boMudaConta     = false;
    $arClassificacao = array();
    $boFlagLoop = true;
    while ($boFlagLoop) {
        $boFlagLoop = !$rsLista->eof();
        if ( $inCodContaAtual != $rsLista->getCampo('cod_conta') AND $rsLista->getCampo('cod_conta') != 1 ) {
            $stClassificacao = substr( $stClassificacao, 0, strlen($stClassificacao) - 1 );
            $stClassificacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stClassificacao );
            if ( $stClassificacao[1] == $this->getMascClassificacao() ) {
                $stDescricaoDespesa = $stDescricaoAnterior;
                break;
            }
            $inCodContaAtual = $rsLista->getCampo('cod_conta');
            $stClassificacao = "";
        } else {
            $stDescricaoAnterior = $rsLista->getCampo('descricao');
        }
        $stClassificacao .= $rsLista->getCampo('cod_classificacao').".";
        $rsLista->proximo();
    }

    if ($stDescricaoAnterior != '' && $stDescricaoDespesa == '') {
        $stDescricaoDespesa = $stDescricaoAnterior;
    }

    return $obErro;
}

function buscaNivelConta(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNivelConta().$stCondicao.$stOrdem;
//    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNivelConta()
{
    $stSql  = " SELECT                                                                      \n";
    $stSql .= "    publico.fn_nivel('".$this->getMascClassificacao()."') as nivel_conta                     \n";

    return $stSql;
}

}

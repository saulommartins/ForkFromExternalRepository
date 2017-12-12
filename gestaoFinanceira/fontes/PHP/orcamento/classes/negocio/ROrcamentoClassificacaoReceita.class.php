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
    * Data de Criação   : 19/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo Boezzio Paulino

    $Id: ROrcamentoClassificacaoReceita.class.php 64198 2015-12-15 13:29:35Z evandro $

    * Casos de uso: uc-02.01.04, uc-02.01.06
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_NEGOCIO        ."RNorma.class.php" );
include_once ( CAM_FW_BANCO_DADOS."Transacao.class.php" );
include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

class ROrcamentoClassificacaoReceita
{
/**
    * @var Object
    * @access Private
*/
var $obRNorma;
/**
    * @var Boolean
    * @access Private
*/
var $boListarAnaliticas;

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
var $boDedutora;
/**
    * @var Integer
    * @access Private
*/
var $inCodTipoReceita;
/**
     * @access Public
     * @param Object $valor
*/
var $inCodReceita;
/**
     * @access Public
     * @param Object $valor
*/
function setCodReceita($valor) { $this->inCodReceita = $valor; }
/**
     * @access Public
     * @param Object $valor
*/
function setRNorma($valor) { $this->obRNorma                 = $valor; }
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
     * @param Boolean $valor
*/
function setListarAnaliticas($valor) { $this->boListarAnaliticas       = $valor; }

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
function setMascara($valor) { $this->stMascClassificacao      = $valor; }
/**
     * @access Public
     * @param String $valor
*/
function setCodEstrutural($valor) { $this->stCodEstrutural          = $valor; }
/**
     * @access Public
     * @param Boolean $valor
*/
function setDedutora($valor) { $this->boDedutora               = $valor; }
/**
     * @access Public
     * @param Integer $valor
*/
function setTipoReceita($valor) { $this->inCodTipoReceita         = $valor; }

/**
     * @access Public
     * @return Object
*/
function getRNorma() { return $this->obRNorma;                 }
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
     * @return Boolean
*/
function getListarAnaliticas() { return $this->boListarAnaliticas;       }
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
function getMascara() { return $this->stMascClassificacao;      }
/**
     * @access Public
     * @return String
*/
function getCodEstrutural() { return $this->stCodEstrutural;          }
/**
     * @access Public
     * @return Boolean
*/
function getDedutora() { return $this->boDedutora;               }
/**
     * @access Public
     * @return Integer
*/
function getTipoReceita() { return $this->inCodTipoReceita;         }

function getCodReceita() { return $this->inCodReceita; }


/**
    * Método Construtor
    * @access Private
*/
function ROrcamentoClassificacaoReceita()
{
    $this->setRNorma                        ( new RNorma                         );
    $this->setTransacao                     ( new Transacao                      );
    $this->setExercicio                     ( Sessao::getExercicio()                 );
    $this->setTipoReceita                   ( '0'                                );
}
function incluir($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaReceita.class.php"         );
    $obTOrcamentoContaReceita         = new TOrcamentoContaReceita;
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        //Se a conta ainda nao foi inserida insere ela na tabela ORCAMENTO.CONTA_RECEITA
        if ( !$this->getCodConta() ) {
            $obErro = $obTOrcamentoContaReceita->proximoCod( $inCodConta , $boTransacao );
            $this->setCodConta( $inCodConta );
            $obTOrcamentoContaReceita->setDado( "cod_conta"       , $this->getCodConta()           );
            $obTOrcamentoContaReceita->setDado( "exercicio"       , $this->getExercicio()          );
            $obTOrcamentoContaReceita->setDado( "descricao"       , $this->getDescricao()          );
            $obTOrcamentoContaReceita->setDado( "cod_norma"       , $_REQUEST['inCodNorma'] );
            $obTOrcamentoContaReceita->setDado( "cod_estrutural"  , $this->stCodEstrutural         );
            $obErro = $obTOrcamentoContaReceita->inclusao( $boTransacao );
        }
        //Insere as Classificacoes da conta inserida na tabela ORCAMENTO.CLASSIFICACAO_RECEITA
        if ( !$obErro->ocorreu() ) {
            $obTOrcamentoClassificacaoReceita->setDado( "exercicio"         , $this->getExercicio()        );
            $obTOrcamentoClassificacaoReceita->setDado( "cod_classificacao" , $this->getCodClassificacao() );
            $obTOrcamentoClassificacaoReceita->setDado( "cod_posicao"       , $this->getCodPosicao()       );
            $obTOrcamentoClassificacaoReceita->setDado( "cod_conta"         , $this->getCodConta()         );
            $obTOrcamentoClassificacaoReceita->setDado( "cod_tipo"          , $this->getTipoReceita()      );
            $obErro = $obTOrcamentoClassificacaoReceita->inclusao( $boTransacao );
        }
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaReceita );
    }

    return $obErro;
}
/**
    * Altera Classificação de Receita
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function alterar($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaReceita.class.php"         );
    $obTOrcamentoContaReceita        = new TOrcamentoContaReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $obTOrcamentoContaReceita->setDado( "cod_conta" , $this->getCodConta()           );
        $obTOrcamentoContaReceita->setDado( "exercicio" , $this->getExercicio()          );
        $obTOrcamentoContaReceita->setDado( "descricao" , $this->getDescricao()          );
        $obTOrcamentoContaReceita->setDado( "cod_norma" , $_REQUEST['inCodNorma'] );
        $obErro = $obTOrcamentoContaReceita->alteracao( $boTransacao );
        $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaReceita );
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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaReceita.class.php"         );
    $obTOrcamentoContaReceita          = new TOrcamentoContaReceita;
    $obTOrcamentoClassificacaoReceita  = new TOrcamentoClassificacaoReceita;

    $boFlagTransacao = false;
    $obErro = $this->obTransacao->abreTransacao( $boFlagTransacao, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        $inCodConta = $this->getCodConta();
        $this->setCodConta(false);
        $obErro = $this->listar( $rsTesta, '', $obTransacao );
        $this->setCodConta($inCodConta);
        if (($rsTesta->getNumLinhas() > 1) and !$obErro->ocorreu()) {
            $obErro->setDescricao("Exclusão não Permitida");
        } elseif (!$obErro->ocorreu()) {
            $obTOrcamentoClassificacaoReceita->setComplementoChave('exercicio,cod_conta');
            $obTOrcamentoClassificacaoReceita->setDado( "cod_conta" , $this->getCodConta()  );
            $obTOrcamentoClassificacaoReceita->setDado( "exercicio" , $this->getExercicio() );
            $obErro = $obTOrcamentoClassificacaoReceita->exclusao( $boTransacao );
            if ( !$obErro->ocorreu() ) {
                $obTOrcamentoContaReceita->setDado( "cod_conta" , $this->getCodConta()  );
                $obTOrcamentoContaReceita->setDado( "exercicio" , $this->getExercicio() );
                $obErro = $obTOrcamentoContaReceita->exclusao( $boTransacao );
                if ($obErro->ocorreu()) {
                    $obErro->setDescricao('Classificação não pode ser excluída porque está sendo utilizada.');
                }

                $this->obTransacao->fechaTransacao( $boFlagTransacao, $boTransacao, $obErro, $obTOrcamentoContaReceita );
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
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodPosicao() ) {
        $stFiltro .= " AND cod_posicao = ".$this->getCodPosicao();
    }
    if ( $this->obRNorma->getCodNorma() ) {
        $stFiltro .= " AND cod_norma = ".$this->obRNorma->getCodNorma();
    }
    if ( $this->getCodConta() ) {
        $stFiltro .= " AND cod_conta = ".$this->getCodConta();
    }
    if ( $this->getDescricao() ) {
        $stFiltro .= " AND lower(descricao) like lower('%".$this->getDescricao()."%')";
    }
    if ( $this->getDedutora() ) {
            $stFiltro .= " AND cod_estrutural like '9%'";
        if ( $this->getMascClassificacao() ) {
            $stFiltro .= " AND cod_estrutural like '".$this->getMascClassificacao()."%'";            
        }
    } else {
        if($this->getExercicio() >= 2008){
                $stFiltro .= " AND cod_estrutural not like '9%'";
        }
        if ( $this->getMascClassificacao() ) {            
            $stFiltro .= " AND cod_estrutural like '".$this->getMascClassificacao()."%'";
        }
    }

    if ( $this->getListarAnaliticas() ) {
        if ( $this->getExercicio() < 2014 ) {
            if ($this->getDedutora() && $this->getExercicio() >= 2008 && $this->getExercicio() <= 2012) {
                $stFiltro .= " AND EXISTS ( SELECT 1                                                    \n";
                $stFiltro .= "               FROM contabilidade.plano_conta as pc                       \n";
                $stFiltro .= "                    JOIN contabilidade.plano_analitica as pa              \n";
                $stFiltro .= "                      USING ( cod_conta, exercicio )                      \n";
                $stFiltro .= "              WHERE pc.cod_estrutural = conta_receita.cod_estrutural      \n";
                $stFiltro .= "                AND pc.exercicio      = conta_receita.exercicio           \n";
                $stFiltro .= "    )                                                                     \n";
            } else {
                $stFiltro .= " AND EXISTS  (                                    \n";
                $stFiltro .= "    SELECT 1                                      \n";
                $stFiltro .= "    FROM                                          \n";
                $stFiltro .= "        contabilidade.plano_conta       as pc,    \n";
                $stFiltro .= "        contabilidade.plano_analitica   as pa     \n";
                $stFiltro .= "    WHERE                                         \n";
                $stFiltro .= "        pc.cod_conta = pa.cod_conta               \n";
                $stFiltro .= "    AND pc.exercicio = pa.exercicio               \n";
                if ( Sessao::getExercicio() < '2012' ) {
                    $stFiltro .= "    AND pc.cod_estrutural = '4.'||conta_receita.cod_estrutural \n";
                }
                $stFiltro .= "    AND pc.exercicio = conta_receita.exercicio     \n";
                $stFiltro .= "    AND pa.exercicio = '".$this->getExercicio()."' \n";
                $stFiltro .= " )                                                 \n"; 
            }
        }
    }
    $obErro = $obTOrcamentoClassificacaoReceita->recuperaRelacionamento( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}
/**
    * Executa um recuperaPorChave na classe Persistente Item
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultar(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;
    $boTransacao = isset($boTransacao) ? $boTransacao : false;
    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'" ;
    }
    if ( $this->getMascClassificacao() ) {
        $stFiltro .= " AND cod_estrutural = '".$this->getMascClassificacao()."'";
    }
    if ( $this->getListarAnaliticas() ) {
        if (!Sessao::getExercicio() > '2012') {
            if ($this->getDedutora()) {
                $stFiltro .= " AND cod_estrutural||exercicio in (         \n";
                $stFiltro .= "    SELECT pc.cod_estrutural||pc.exercicio        \n";
                $stFiltro .= "    FROM                                          \n";
                $stFiltro .= "        contabilidade.plano_conta       as pc,    \n";
                $stFiltro .= "        contabilidade.plano_analitica   as pa     \n";
                $stFiltro .= "    WHERE                                         \n";
                $stFiltro .= "        pc.cod_conta = pa.cod_conta               \n";
                $stFiltro .= "    AND pc.exercicio = pa.exercicio               \n";
                $stFiltro .= "    AND pa.exercicio='".$this->getExercicio()."'  \n";
                $stFiltro .= " )                                                \n";
            } else {
                $stFiltro .= " AND '4.'||cod_estrutural||exercicio in (         \n";
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
        }
    }
    $obErro = $obTOrcamentoClassificacaoReceita->recuperaRelacionamento( $rsLista, $stFiltro, '', $boTransacao );

    return $obErro;
}
/**
    * @access Public
    * @param  Object $rsLista Retorna o RecordSet preenchido
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function consultarReceitaAnalitica(&$rsLista, $boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;
    $boTransacao = isset($boTransacao) ? $boTransacao : false;
    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stFiltro .= " AND exercicio = '".$this->getExercicio()."'" ;
    }
    if ( $this->getMascClassificacao() ) {
        $stFiltro .= " AND cod_estrutural = '".$this->getMascClassificacao()."'";
    }
    
    if ($this->getDedutora()) {
        $stFiltro .= "  AND cod_estrutural like '9%' ";
    }

    $obErro = $obTOrcamentoClassificacaoReceita->recuperaReceitaAnalitica( $rsLista, $stFiltro, '', $boTransacao );

    return $obErro;
}
/**
    * Recupera a mascara da Classificacao de Receita
    * @access Public
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaMascara($boTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoPosicaoReceita.class.php"       );
    $obTOrcamentoPosicaoReceita      = new TOrcamentoPosicaoReceita;
    if($this->getDedutora())
        $obTOrcamentoPosicaoReceita->setDado('dedutora', true);

    $obErro = $obTOrcamentoPosicaoReceita->recuperaMascara( $this->getExercicio() );

    return $obErro;
}

/**
    * Busca a descrição da classificação de receita informada
    * @access Public
    * @param  Object $stDescricaoReceita Retorna a descrição da receita
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaDescricaoReceita(&$stDescricaoReceita, $obTransacao = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;

    $stFiltro = "";
    if ( $this->getExercicio() ) {
        $stClassificacao = Mascara::validaMascaraDinamica( $this->getMascara(), $this->getMascClassificacao() );
        $stFiltro .= " AND CLR.exercicio = '".$this->getExercicio()."'";
    }
    $stOrder = "ORDER by CLR.cod_conta, CLR.cod_posicao";
    $obErro = $obTOrcamentoClassificacaoReceita->recuperaDescricaoReceita( $rsLista, $stFiltro, $stOrder, $obTransacao );

    $inCodContaAtual = "";
    $stClassificacao = "";
    $arClassificacao = array();
    $boFlagLoop = true;
    while ($boFlagLoop) {
        $boFlagLoop = !$rsLista->eof();
        if ( $inCodContaAtual != $rsLista->getCampo('cod_conta') AND $rsLista->getCampo('cod_conta') != 1 ) {
            $stClassificacao = substr( $stClassificacao, 0, strlen($stClassificacao) - 1 );
            $arClassificacao = Mascara::validaMascaraDinamica( $this->getMascara(), $stClassificacao );
            if ( $arClassificacao[1] == $this->getMascClassificacao() ) {
                $stDescricaoReceita = $stDescricaoAnterior;
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

    return $obErro;
}


function recuperaDescricaoReceitaIRRF(&$rsContaIRRF, $obTransacao = "")
{

    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaReceita.class.php"         );
    $obTOrcamentoContaReceita = new TOrcamentoContaReceita;
    
    $stFiltro = "WHERE conta_receita.exercicio  = '".$this->getExercicio()."'";

    if ( $this->getCodReceita() != "" )
        $stFiltro .= " AND receita.cod_receita = ".$this->getCodReceita();        

    if ( $this->getCodEstrutural() != "" )
        $stFiltro  .= " AND REPLACE(conta_receita.cod_estrutural,'.','') like '".$this->getCodEstrutural()."%'";

    if ( $this->getDescricao() != "")
        $stFiltro .= " AND conta_receita.descricao ilike '".$this->getDescricao()."'" ;

    $obErro = $obTOrcamentoContaReceita->recuperaDescricaoIRRF($rsContaIRRF,$stFiltro,"",$boTransacao);

    return $obErro;
}

function recuperaListaIRRF(&$rsContaIRRF, $obTransacao = "")
{

    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoContaReceita.class.php"         );
    $obTOrcamentoContaReceita = new TOrcamentoContaReceita;

    $obTOrcamentoContaReceita->setDado('exercicio',$this->getExercicio());

    if ( $this->getCodReceita() != "" )
        $stFiltro .= " AND cod_receita_irrf = ".$this->getCodReceita();        

    if ( $this->getCodEstrutural() != "" )
        $stFiltro  .= " AND REPLACE(cod_estrutural,'.','') like '".$this->getCodEstrutural()."%'" ;

    if ( $this->getDescricao() != "")
        $stFiltro .= " AND descricao ilike '".$this->getDescricao()."'" ;

    $obErro = $obTOrcamentoContaReceita->recuperaListaIRRF($rsContaIRRF,$stFiltro," ORDER BY cod_receita_irrf , cod_estrutural",$boTransacao);

    return $obErro;
}

/**
    * Busca a descrição da classificação de receita informada por filtro
    * @access Public
    * @param  Object $stDescricaoReceita Retorna a descrição da receita
    * @param  Object $boTransacao Parâmetro Transação
    * @return Object Objeto Erro
*/
function recuperaDescricaoReceitaFiltrada(&$rsLista, $obTransacao = "", $stFiltro = "")
{
    include_once ( CAM_GF_ORC_MAPEAMENTO   ."TOrcamentoClassificacaoReceita.class.php" );
    $obTOrcamentoClassificacaoReceita = new TOrcamentoClassificacaoReceita;

    if ( $this->getExercicio() ) {
        $stClassificacao = Mascara::validaMascaraDinamica( $this->getMascara(), $this->getMascClassificacao() );
        $stFiltro .= " AND CLR.exercicio = '".$this->getExercicio()."'";
    }
    if ( $this->getCodEstrutural() ) {
        $stFiltro .= " AND CTR.cod_estrutural = '".$this->getCodEstrutural()."'";
    }
    if ( $this->getCodClassificacao() ) {
        $stFiltro .= " AND cod_classificacao = ".$this->getCodClassificacao()."";
    }

    $stOrder = " ORDER by CLR.cod_conta, CLR.cod_posicao LIMIT 1";
    $obErro = $obTOrcamentoClassificacaoReceita->recuperaDescricaoReceita( $rsLista, $stFiltro, $stOrder, $obTransacao );

    return $obErro;
}

}

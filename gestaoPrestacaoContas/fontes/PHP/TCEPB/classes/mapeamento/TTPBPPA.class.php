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
    * Extensão da Classe de mapeamento
    * Data de Criação: 24/07/2014
    *
    *
    * @author Desenvolvedor: Arthur Cruz
    *
    * @package URBEM
    * @subpackage Mapeamento
    *
    $Id: TTPBPPA.class.php 59938 2014-09-22 20:05:41Z franver $
    *
    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTPBPPA extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTPBPPA()
{
    parent::Persistente();
    $this->setDado('exercicio', Sessao::getExercicio() );
}

function recuperaPPA(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaPPA().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPPA()
{    
    $stSql = "
    
           SELECT 0 AS reservado_tce
                , acao_recurso.exercicio_recurso AS exercicio
                , programa.num_programa AS cod_programa
                , acao.num_acao AS cod_acao
                , SUM(acao_quantidade.valor) AS valor_acao_prevista
                , SUM(acao_quantidade.quantidade) AS meta_fisica_prevista
             FROM ppa.programa
             JOIN ppa.acao
               ON programa.cod_programa = acao.cod_programa
             JOIN ppa.acao_dados
               ON acao_dados.cod_acao = acao.cod_acao
             JOIN ppa.acao_recurso
               ON acao_recurso.cod_acao = acao_dados.cod_acao
              AND acao_recurso.timestamp_acao_dados = acao_dados.timestamp_acao_dados
             JOIN ppa.acao_quantidade
               ON ppa.acao_quantidade.cod_acao = acao_recurso.cod_acao
              AND ppa.acao_quantidade.ano = acao_recurso.ano
              AND ppa.acao_quantidade.timestamp_acao_dados = acao_recurso.timestamp_acao_dados
              AND ppa.acao_quantidade.cod_recurso = acao_recurso.cod_recurso
              AND ppa.acao_quantidade.exercicio_recurso = acao_recurso.exercicio_recurso
            WHERE programa.ativo = true
              AND acao_quantidade.exercicio_recurso BETWEEN (SELECT ano_inicio FROM ppa.ppa WHERE ano_inicio <= '".$this->getDado('exercicio')."' AND ano_final >= '".$this->getDado('exercicio')."')
                                                        AND (SELECT ano_final  FROM ppa.ppa WHERE ano_inicio <= '".$this->getDado('exercicio')."' AND ano_final >= '".$this->getDado('exercicio')."')
         GROUP BY acao_recurso.exercicio_recurso
                , programa.num_programa
                , acao.num_acao
         ORDER BY programa.num_programa
                , acao.num_acao ASC
                  ";

    return $stSql;
}

}

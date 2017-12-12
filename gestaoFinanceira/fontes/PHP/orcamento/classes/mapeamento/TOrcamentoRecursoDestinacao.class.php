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
    * Classe de mapeamento da tabela orcamento.recurso_destinacao
    * Data de Criação: 29/10/2007

    * @author Analista: Anderson cAko Konze
    * @author Desenvolvedor: Anderson cAko Konze

    $Id: TOrcamentoRecursoDestinacao.class.php 64153 2015-12-09 19:16:02Z evandro $

    * Casos de uso: uc-02.01.38
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoRecursoDestinacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TOrcamentoRecursoDestinacao()
{
    parent::Persistente();
    $this->setTabela("orcamento.recurso_destinacao");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_recurso');

    $this->AddCampo('exercicio'        ,'char'   ,true  ,'4'  ,true,'TOrcamentoDetalhamentoDestinacaoRecurso');
    $this->AddCampo('cod_recurso'      ,'integer',true  ,''   ,true,'TOrcamentoRecurso');
    $this->AddCampo('cod_uso'          ,'integer',true  ,''   ,false,'TOrcamentoIdentificadorUso');
    $this->AddCampo('cod_destinacao'   ,'integer',true  ,''   ,false,'TOrcamentoDestinacaoRecurso');
    $this->AddCampo('cod_especificacao','integer',true  ,''   ,false,'TOrcamentoEspecificacaoDestinacaoRecurso');
    $this->AddCampo('cod_detalhamento' ,'integer',true  ,''   ,false,'TOrcamentoDetalhamentoDestinacaoRecurso');

}

function recuperaRecursoVinculoConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRecursoVinculoConta($boTransacao).$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRecursoVinculoConta($boTransacao = "")
{
    $stSql  = 'SELECT recurso_destinacao.cod_recurso';
    $stSql .= '  FROM orcamento.recurso_destinacao';
    $stSql .= '  JOIN contabilidade.plano_recurso ';
    $stSql .= '    ON plano_recurso.exercicio = recurso_destinacao.exercicio ';
    $stSql .= '   AND plano_recurso.cod_recurso = recurso_destinacao.cod_recurso ';
    if ( !Sessao::getExercicio() > '2012' ) {
        $stSql .= '  JOIN contabilidade.plano_analitica ';
        $stSql .= '    ON plano_analitica.cod_plano = plano_recurso.cod_plano ';
        $stSql .= '   AND plano_analitica.exercicio = plano_recurso.exercicio ';
        $stSql .= '  JOIN contabilidade.plano_conta ON plano_conta.cod_conta = plano_analitica.cod_conta ';
        $stSql .= '   AND plano_conta.exercicio = plano_analitica.exercicio ';
    }
    $stSql .= ' WHERE true ';
    if ($this->getDado('exercicio') != '') {
        $stSql .= ' AND recurso_destinacao.exercicio = '.$this->getDado('exercicio');
    }
    if ($this->getDado('cod_estrutural') != '') {
        $stSql .= ' AND plano_conta.cod_estrutural like '.$this->getDado('cod_estrutural');
    }
    if ($this->getDado('cod_especificacao') != '') {
        $stSql .= ' AND recurso_destinacao.cod_especificacao = '.$this->getDado('cod_especificacao');
    }
    $stSql .= ' AND true ';

    return $stSql;
}

function recuperaRecursoEspecificacaoSemConta(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRecursoEspecificacaoSemConta().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRecursoEspecificacaoSemConta()
{
    $stSql  = "SELECT especificacao_destinacao_recurso.cod_especificacao \n";
    $stSql .= "     , especificacao_destinacao_recurso.descricao         \n";
    $stSql .= "FROM orcamento.especificacao_destinacao_recurso           \n";
    $stSql .= "JOIN orcamento.recurso_destinacao                         \n";
    $stSql .= "  ON recurso_destinacao.cod_especificacao = especificacao_destinacao_recurso.cod_especificacao \n";
    $stSql .= " AND recurso_destinacao.exercicio = especificacao_destinacao_recurso.exercicio \n";
    $stSql .= "WHERE especificacao_destinacao_recurso.cod_especificacao NOT IN ( \n";
    $stSql .= "    SELECT DISTINCT recurso_destinacao.cod_especificacao  \n";
    $stSql .= "                FROM contabilidade.plano_recurso          \n";
    $stSql .= "          INNER JOIN contabilidade.plano_analitica        \n";
    $stSql .= "                  ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "                 AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "          INNER JOIN contabilidade.plano_conta            \n";
    $stSql .= "                  ON plano_conta.cod_conta = plano_analitica.cod_conta \n";
    $stSql .= "                 AND plano_conta.exercicio = plano_analitica.exercicio \n";
    $stSql .= "                 AND ( plano_conta.cod_estrutural like '1.9.3.2.0.00.00%' \n";
    $stSql .= "                    OR plano_conta.cod_estrutural like'2.9.3.2.0.00.00%' \n";
    $stSql .= "                     )                                    \n";
    $stSql .= "          INNER JOIN orcamento.recurso_destinacao         \n";
    $stSql .= "                  ON recurso_destinacao.exercicio = plano_recurso.exercicio \n";
    $stSql .= "                 AND recurso_destinacao.cod_recurso = plano_recurso.cod_recurso \n";
    $stSql .= "               WHERE recurso_destinacao.exercicio = ".$this->getDado('exercicio')."   \n";
    $stSql .= "    )                                                     \n";
    $stSql .= "  AND especificacao_destinacao_recurso.exercicio = ".$this->getDado('exercicio')."   \n";
    if(STRLEN($this->getDado('cod_especificacao_inicial')) > 0
    && STRLEN($this->getDado('cod_especificacao_final')) > 0) {
        $stSql .= " AND especificacao_destinacao_recurso.cod_especificacao \n";
        $stSql .= "     BETWEEN ".$this->getDado('cod_especificacao_inicial')." \n";
        $stSql .= "         AND ".$this->getDado('cod_especificacao_final')." \n";
    } elseif (STRLEN($this->getDado('cod_especificacao_inicial')) > 0) {
        $stSql .= " AND especificacao_destinacao_recurso.cod_especificacao >= ".$this->getDado('cod_especificacao_inicial');
    } elseif (STRLEN($this->getDado('cod_especificacao_final')) > 0) {
        $stSql .= " AND especificacao_destinacao_recurso.cod_especificacao <= ".$this->getDado('cod_especificacao_final');
    }
    $stSql .= " GROUP BY especificacao_destinacao_recurso.cod_especificacao \n";
    $stSql .= "        , especificacao_destinacao_recurso.descricao         \n";
    $stSql .= " ORDER BY especificacao_destinacao_recurso.cod_especificacao \n";

    return $stSql;
}

}
?>

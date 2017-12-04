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
    * Classe de mapeamento da tabela ORCAMENTO.RECURSO
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoRecurso.class.php 66353 2016-08-16 20:04:08Z michel $

    * Casos de uso: uc-02.01.05, uc-02.08.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TOrcamentoRecurso extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('orcamento.recurso');

    $this->setCampoCod('cod_recurso');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',true,false);
    $this->AddCampo('cod_recurso','integer',true,'',true,false);
    $this->AddCampo('cod_fonte', 'varchar', '13', '', false  , false );
    $this->AddCampo('nom_recurso','varchar', '200', '', false  , false );
}

/**
    * Método Para Recuperar a Máscara do recurso
    * @access Private
*/
function montaRecuperaRelacionamento()
{
    $stSql = " SELECT *
                FROM orcamento.recurso
                WHERE ";
    if ($this->getDado('cod_recurso') != NULL) {
        $stSql .= " recurso.cod_recurso = ".$this->getDado('cod_recurso')." AND ";
    }
    if ($this->getDado('exercicio') != NULL) {
        $stSql .= " recurso.exercicio = '".$this->getDado('exercicio')."' AND ";
    }

    $stSql = substr( $stSql, 0, (strlen($stSql)-5));

    return $stSql;
}

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaDadosExportacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "RO.cod_recurso" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function MontaRecuperaDadosExportacao()
    {
        $stSql  = "";
        $stSql .= "  SELECT                                                         \n";
        $stSql .= "     RO.cod_recurso,                                             \n";
        $stSql .= "     RO.nom_recurso,                                             \n";
        $stSql .= "     RO.finalidade,                                              \n";
        $stSql .= "     RO.tipo                                                     \n";
        $stSql .= "  FROM                                                           \n";
        $stSql .= "     orcamento.recurso('".$this->getDado("inExercicio")."') AS RO  \n";

        return $stSql;
    }

/**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaVerificaUtilizacao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function verificaUtilizacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaVerificaUtilizacao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaVerificaUtilizacao()
    {
        $stSql  = "";
        $stSql .= "  SELECT                                                                                            \n";
        $stSql .= "        CASE WHEN rec.cod_recurso is not null                                                       \n";
        $stSql .= "             THEN 'S'                                                                               \n";
        $stSql .= "             ELSE 'N' end as utilizado                                                              \n";
        $stSql .= "  FROM                                                                                              \n";
        $stSql .= "        orcamento.recurso                  as rec                                                   \n";
        $stSql .= "        LEFT JOIN  orcamento.despesa       as des on (     rec.cod_recurso = des.cod_recurso        \n";
        $stSql .= "                                                             and rec.exercicio   = des.exercicio   )\n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "        JOIN   empenho.pre_empenho_despesa as ped on (     ped.cod_despesa = des.cod_despesa        \n";
        $stSql .= "                                                       and ped.exercicio   = des.exercicio   )      \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "        JOIN   empenho.pre_empenho         as pre on (     pre.cod_pre_empenho = ped.cod_pre_empenho\n";
        $stSql .= "                                                       and pre.exercicio       = ped.exercicio )    \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "        JOIN   empenho.empenho             as emp on (     emp.cod_pre_empenho = pre.cod_pre_empenho\n";
        $stSql .= "                                                           and emp.exercicio       = emp.exercicio )\n";
        $stSql .= "  WHERE                                                                                             \n";
        $stSql .= "            rec.exercicio = '".$this->getDado("exercicio")."'                                       \n";
        $stSql .= "        and rec.cod_recurso = ".$this->getDado("cod_recurso")."                                     \n";
        $stSql .= "                                                                                                    \n";
        $stSql .= "  GROUP BY rec.cod_recurso                                                                          \n";

        return $stSql;
    }

/**
    * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaBuscaRecurso.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaBuscaRecurso(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaBuscaRecurso().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaBuscaRecurso()
{
    $stSql .= $this->montaRecuperaRelacionamento();
    $stSql .= " WHERE cod_recurso is not null ";
    if( strlen( $this->getDado("cod_recurso") ) > 0 )
        $stSql .= " AND cod_recurso = ".$this->getDado("cod_recurso");

    return $stSql;
}

function recuperaRecursoSemConta(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRecursoSemConta();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}
function montaRecuperaRecursoSemConta()
{
    $stSql  = "    SELECT recurso.cod_recurso                                   \n";
    $stSql .= "         , recurso.nom_recurso                                   \n";
    $stSql .= "      FROM orcamento.recurso                                     \n";
    $stSql .= "     WHERE recurso.exercicio = ".$this->getDado('exercicio')."   \n";
    $stSql .= "       AND recurso.cod_recurso NOT IN                            \n";
    $stSql .= "         ( SELECT DISTINCT plano_recurso.cod_recurso             \n";
    $stSql .= "             FROM contabilidade.plano_recurso                    \n";
    $stSql .= "       INNER JOIN contabilidade.plano_analitica                  \n";
    $stSql .= "               ON plano_recurso.cod_plano = plano_analitica.cod_plano \n";
    $stSql .= "              AND plano_recurso.exercicio = plano_analitica.exercicio \n";
    $stSql .= "       INNER JOIN contabilidade.plano_conta                      \n";
    $stSql .= "               ON plano_conta.cod_conta = plano_analitica.cod_conta \n";
    $stSql .= "              AND plano_conta.exercicio = plano_analitica.exercicio \n";
    $stSql .= "              AND ( plano_conta.cod_estrutural like '1.9.3.2.0.00.00%' \n";
    $stSql .= "                 OR plano_conta.cod_estrutural like '2.9.3.2.0.00.00%' \n";
    $stSql .= "                  )                                              \n";
    $stSql .= "         )                                                       \n";
    if(STRLEN($this->getDado('cod_recurso_inicial')) > 0
    && STRLEN($this->getDado('cod_recurso_final')) > 0) {
        $stSql .= " AND recurso.cod_recurso BETWEEN ".$this->getDado('cod_recurso_inicial');
        $stSql .= "                             AND ".$this->getDado('cod_recurso_final');
    } elseif (STRLEN($this->getDado('cod_recurso_inicial')) > 0) {
        $stSql .= " AND recurso.cod_recurso >= ".$this->getDado('cod_recurso_inicial');
    } elseif (STRLEN($this->getDado('cod_recurso_final')) > 0) {
        $stSql .= " AND recurso.cod_recurso <= ".$this->getDado('cod_recurso_final');
    }
    $stSql .= "  ORDER BY recurso.cod_recurso                                   \n";

    return $stSql;
}

function recuperaRecursoExercicio(&$rsRecordSet, $stCondicao = "", $stOrdem = " ORDER BY recurso.nom_recurso", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaRecursoExercicio();
    $stSql = $this->montaRecuperaRecursoExercicio().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRecursoExercicio()
{
    $stSql  = " SELECT *                                        \n";
    $stSql .= "   FROM orcamento.recurso                        \n";
    $stSql .= "  WHERE exercicio = '".Sessao::getExercicio()."' \n";
    //$stSql .= "  ORDER BY recurso.nom_recurso                 \n";

    return $stSql;
}

public function verificaContaRecurso(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql = $this->montaVerificaContaRecurso();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaVerificaContaRecurso()
{
    $stSql="SELECT  plano_conta.*
                    ,plano_recurso.*
            FROM contabilidade.plano_analitica
            
            INNER JOIN contabilidade.plano_recurso
               ON plano_recurso.cod_plano = plano_analitica.cod_plano
              AND plano_recurso.exercicio = plano_analitica.exercicio
            
            INNER JOIN contabilidade.plano_conta 
               ON plano_conta.cod_conta = plano_analitica.cod_conta
              AND plano_conta.exercicio = plano_analitica.exercicio
        
            WHERE plano_analitica.exercicio = '".$this->getDado('exercicio')."'         
              AND plano_conta.escrituracao = 'analitica'
              AND plano_conta.cod_estrutural SIMILAR TO ('7.2.1.1.1%|7.2.1.1.2%|8.2.1.1.1%|8.2.1.1.2%|8.2.1.1.3%|8.2.1.1.4%')
              AND plano_conta.cod_estrutural LIKE (SELECT fn_conta_mae('".$this->getDado('cod_estrutural')."'))||'%'
              AND plano_recurso.cod_recurso = '".$this->getDado('cod_recurso')."'
              ";
    if ($this->getDado('cod_conta') != '') {
    $stSql.= "
              AND plano_conta.cod_conta <> ".$this->getDado('cod_conta')." ";
    }
    $stSql.= "
            ORDER BY cod_estrutural
        ";
    return $stSql;
}

public function recuperaRecursoAcao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    
    $stSql = $this->montaRecuperaRecursoAcao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaRecursoAcao()
{
    $stSql="
               SELECT recurso.exercicio
                    , recurso.cod_recurso
                    , recurso.cod_fonte
                    , recurso.nom_recurso
                    , acao.num_acao
                    , acao_dados.cod_acao
                    , acao_dados.exercicio
                    , acao_dados.titulo
                FROM orcamento.recurso
          INNER JOIN orcamento.despesa AS d
                  ON d.exercicio = recurso.exercicio
                 AND d.cod_recurso = recurso.cod_recurso
          INNER JOIN orcamento.conta_despesa AS cd
                  ON cd.exercicio = d.exercicio
                 AND cd.cod_conta = d.cod_conta 
          INNER JOIN orcamento.pao_ppa_acao
                  ON pao_ppa_acao.exercicio = d.exercicio
                 AND pao_ppa_acao.num_pao = d.num_pao    
          INNER JOIN ppa.acao
                  ON pao_ppa_acao.cod_acao = acao.cod_acao
                 AND d.cod_programa = acao.cod_programa
                 AND acao.ativo = TRUE
          INNER JOIN ppa.acao_dados
                  ON acao.cod_acao = acao_dados.cod_acao
                 AND acao.ultimo_timestamp_acao_dados = acao_dados.timestamp_acao_dados
               WHERE recurso.exercicio = '".$this->getDado('exercicio')."' ";
    if ($this->getDado('cod_entidade') != '') {
        $stSql.= " AND d.cod_entidade = ".$this->getDado('cod_entidade')." ";
    }
    if ($this->getDado('num_unidade') != '') {
        $stSql.= " AND d.num_unidade = ".$this->getDado('num_unidade')." ";
    }
    if ($this->getDado('num_orgao') != '') {
        $stSql.= " AND d.num_orgao = ".$this->getDado('num_orgao')." ";
    }
    if ($this->getDado('cod_recurso') != '') {
        $stSql.= " AND recurso.cod_recurso = ".$this->getDado('cod_recurso')." ";
    }

    $stSql.= "
             GROUP BY recurso.exercicio
                    , recurso.cod_recurso
                    , recurso.cod_fonte
                    , recurso.nom_recurso
                    , acao.num_acao
                    , acao_dados.cod_acao
                    , acao_dados.exercicio
                    , acao_dados.titulo
             ORDER BY recurso.cod_recurso, acao.num_acao
              ";
    return $stSql;
}



}

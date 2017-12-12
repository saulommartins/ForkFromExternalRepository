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
    * Classe de mapeamento da tabela ORCAMENTO.PAO
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoProjetoAtividade.class.php 63259 2015-08-10 14:30:00Z franver $

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-21 10:31:00 -0300 (Sex, 21 Jul 2006) $

    * Casos de uso: uc-02.01.03 , uc-02.08.02
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

/**
  * Efetua conexão com a tabela  ORCAMENTO.PAO
  * Data de Criação: 13/07/2004

  * @author Analista: Jorge B. Ribarr
  * @author Desenvolvedor: Marcelo B. Paulino

*/
class TOrcamentoProjetoAtividade extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('orcamento.pao');
    
        $this->setCampoCod('num_pao');
        $this->setComplementoChave('exercicio');
    
        $this->AddCampo('exercicio','char',true,'04',true,false);
        $this->AddCampo('num_pao','integer',true,'',true,false);
        $this->AddCampo('nom_pao','varchar',true,'80',false,false);
        $this->AddCampo('detalhamento','text',true,'',false,false);
    
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaPorNumAcao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaPorNumAcao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPorNumAcao().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaPorNumAcao()
    {
        $stSql = "
        SELECT pao.exercicio
             , pao.num_pao
             , pao.nom_pao
             , pao.detalhamento
             , ppa.acao.num_acao
          FROM orcamento.pao
          JOIN orcamento.pao_ppa_acao
            ON pao_ppa_acao.num_pao =pao.num_pao
           AND pao_ppa_acao.exercicio = pao.exercicio
          JOIN ppa.acao
            ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao ";
    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaMascarado.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaMascarado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaMascarado().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaMascarado()
    {
        $stSql  = "  SELECT                                   \n";
        $stSql .= "     sw_fn_mascara_dinamica                \n";
        $stSql .= "     (                                     \n";
        $stSql .= "     '".$this->getDado('stMascara')."',    \n";
        $stSql .= "     ''||orcamento.pao.num_pao             \n";
        $stSql .= "     ) AS num_pao,                         \n";
        $stSql .= "     orcamento.pao.exercicio,              \n";
        $stSql .= "     orcamento.pao.detalhamento,           \n";
        $stSql .= "     orcamento.pao.nom_pao,                \n";
        $stSql .= "     sw_fn_mascara_dinamica                \n";
        $stSql .= "     (                                     \n";
        $stSql .= "     '".$this->getDado('stMascara')."',    \n";
        $stSql .= "     ''||ppa.acao.num_acao                 \n";
        $stSql .= "     ) AS num_acao                         \n";
        $stSql .= "  FROM                                     \n";
        $stSql .= "    ".$this->getTabela()."                 \n";
        $stSql .= "  JOIN orcamento.pao_ppa_acao              \n";
        $stSql .= "    ON pao_ppa_acao.num_pao = orcamento.pao.num_pao \n";
        $stSql .= "   AND pao_ppa_acao.exercicio = orcamento.pao.exercicio \n";
        $stSql .= "  JOIN ppa.acao                        \n";
        $stSql .= "    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao \n";
    
        return $stSql;
    }
    public function recuperaSemMascara(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaSemMascara().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaSemMascara()
    {
        $stSql  = "  SELECT                                         \n";
        $stSql .= "     orcamento.pao.num_pao AS num_pao,           \n";
        $stSql .= "     ppa.acao.num_acao AS num_acao,              \n";
        $stSql .= "     orcamento.pao.exercicio AS exercicio,       \n";
        $stSql .= "     orcamento.pao.detalhamento AS detalhamento, \n";
        $stSql .= "     orcamento.pao.nom_pao AS nom_pao            \n";
        $stSql .= "  FROM                                           \n";
        $stSql .= "     orcamento.pao                               \n";
        $stSql .= "  JOIN orcamento.pao_ppa_acao                    \n";
        $stSql .= "    ON pao_ppa_acao.num_pao = orcamento.pao.num_pao \n";
        $stSql .= "   AND pao_ppa_acao.exercicio = orcamento.pao.exercicio \n";
        $stSql .= "  JOIN ppa.acao                                  \n";
        $stSql .= "    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao \n";
    
        return $stSql;
    }
    
    public function recuperaSemMascaraPorTipo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaSemMascaraPorTipo().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaSemMascaraPorTipo()
    {
        $stSql  = "  SELECT                                   \n";
        $stSql .= "     lpad(num_pao,4,0) as num_pao,         \n";
        $stSql .= "     ppa.acao.num_acao AS num_acao,              \n";
        $stSql .= "     exercicio,                            \n";
        $stSql .= "     nom_pao                               \n";
        $stSql .= "  FROM                                     \n";
        $stSql .= "     orcamento.pao                         \n";
        $stSql .= "  JOIN orcamento.pao_ppa_acao                    \n";
        $stSql .= "    ON pao_ppa_acao.num_pao = orcamento.pao.num_pao \n";
        $stSql .= "   AND pao_ppa_acao.exercicio = orcamento.pao.exercicio \n";
        $stSql .= "  JOIN ppa.acao                                  \n";
        $stSql .= "    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao \n";
    
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
    public function recuperaDadosExportacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    
        $stSql = "select valor from administracao.configuracao where cod_modulo = 2 and parametro = 'samlink_host'";
        $obErro = $obConexao->executaSQL( $rsSamLink, $stSql, $boTransacao );
        if ( !$obErro->ocorreu() ) {
            $this->setDado("boTemSiam", !$rsSamLink->eof() );
            $stSql = $this->montaRecuperaDadosExportacao().$stCondicao.$stOrdem;
            $this->setDebug( $stSql );
            $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
        }
    
        return $obErro;
    }
    
    public function MontaRecuperaDadosExportacao()
    {
        $stSql  = "    
            SELECT * FROM (                                                  
                    SELECT orcamento.pao.exercicio::INTEGER                   
                         , CASE WHEN ppa.acao.num_acao IS NOT NULL 
                                THEN ppa.acao.num_acao 
                                ELSE orcamento.pao.num_pao 
                           END AS num_pao           
                         , CASE WHEN acao_dados.descricao IS NOT NULL 
                                THEN TRIM(UPPER(regexp_replace(acao_dados.descricao, E'[\\n\\r]+', '', 'g' )))
                                WHEN acao_dados.titulo IS NOT NULL 
                                THEN TRIM(UPPER(regexp_replace(acao_dados.titulo, E'[\\n\\r]+', '', 'g' ))) 
                                ELSE TRIM(UPPER(regexp_replace(orcamento.pao.nom_pao, E'[\\n\\r]+', '', 'g' )))
                            END AS nom_pao 
                         , '02'::VARCHAR as identificador                               
                      FROM orcamento.pao 
                                                           
                  LEFT JOIN orcamento.pao_ppa_acao                             
                         ON pao_ppa_acao.num_pao   = orcamento.pao.num_pao      
                        AND pao_ppa_acao.exercicio = orcamento.pao.exercicio  
                          
                  LEFT JOIN ppa.acao                                           
                        ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao  
                                 
                  LEFT JOIN ppa.acao_dados                                     
                        ON acao_dados.cod_acao = ppa.acao.cod_acao             
                       AND acao_dados.timestamp_acao_dados = ppa.acao.ultimo_timestamp_acao_dados \n";
            
            if ( $this->getDado("boTemSiam") ) {
                $stSql .= "  UNION                                                      \n";
                $stSql .= "  SELECT                                                     \n";
                $stSql .= "     2004 as exercicio,                                      \n";
                $stSql .= "     to_number(SP.num_pao,'9999') AS num_pao,                \n";
                $stSql .= "     'PROJATIV' as nom_pao,                                  \n";
                $stSql .= "     '02' as identificador                                   \n";
                $stSql .= "  FROM                                                       \n";
                $stSql .= "     samlink.vw_siam_pao_2004 AS SP                          \n";
            }
            
            $stSql .= ") AS tabela                                                 
                   WHERE tabela.exercicio <= ".$this->getDado('exercicio')."
                
                GROUP BY tabela.exercicio
                       , tabela.num_pao
                       , tabela.nom_pao
                       , tabela.identificador \n";
            
        return $stSql;
    }
    
    public function recuperaProximoCodPorTipo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaProximoCodPorTipo().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaProximoCodPorTipo()
    {
        $stSql  = "  SELECT MAX(num_pao) AS num_pao \n";
        $stSql .= "  FROM orcamento.pao             \n";
    
        return $stSql;
    }
    
    /**
        * Executa um Select no banco de dados a partir do comando SQL montado no método montaRecuperaPorNumAcao.
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaPorNumPAODotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
    
        $stGroupBy = "
                  GROUP BY dotacao
                     , pao.exercicio
                     , pao.num_pao
                     , acao.num_acao
                     , acao_dados.titulo
        ";
        
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaPorNumPAODotacao().$stCondicao.$stGroupBy.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    public function montaRecuperaPorNumPAODotacao()
    {
        $stSql = "
                SELECT sw_fn_mascara_dinamica('99.99.99.999.9999.9999', despesa.num_orgao||'.'||despesa.num_unidade||'.'||despesa.cod_funcao||'.'||despesa.cod_subfuncao||'.'||p_programa.num_programa||'.'||acao.num_acao) AS dotacao
                     , pao.exercicio
                     , pao.num_pao
                     , acao.num_acao
                     , acao_dados.titulo
                  FROM orcamento.despesa
            INNER JOIN orcamento.programa
                    ON programa.exercicio = despesa.exercicio
                   AND programa.cod_programa = despesa.cod_programa
            INNER JOIN orcamento.programa_ppa_programa
                    ON programa_ppa_programa.exercicio = programa.exercicio
                   AND programa_ppa_programa.cod_programa = programa.cod_programa
            INNER JOIN ppa.programa AS p_programa
                    ON p_programa.cod_programa = programa_ppa_programa.cod_programa_ppa
            INNER JOIN orcamento.pao
                    ON pao.exercicio = despesa.exercicio
                   AND pao.num_pao  = despesa.num_pao
            INNER JOIN orcamento.pao_ppa_acao
                    ON pao_ppa_acao.exercicio = pao.exercicio
                   AND pao_ppa_acao.num_pao = pao.num_pao
            INNER JOIN ppa.acao
                    ON acao.cod_acao = pao_ppa_acao.cod_acao
            INNER JOIN ppa.acao_dados
                    ON acao_dados.cod_acao = acao.cod_acao
                   AND acao_dados.timestamp_acao_dados = acao.ultimo_timestamp_acao_dados
        ";
        return $stSql;
    }

}

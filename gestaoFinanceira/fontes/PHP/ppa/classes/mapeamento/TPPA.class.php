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
 * Classe de mapeamento PPA
 * Data de Criação: 21/09/2008

 * @author Analista: Heleno Santos
 * @author Desenvolvedor: Fellipe Esteves dos Santos

 * $Id: TPPA.class.php 66144 2016-07-22 11:50:26Z evandro $

 * Casos de uso: uc-02.09.01
 */

include_once 'TPPAUtils.class.php';

class TPPA extends TPPAUtils
{
    /**
     * Método Construtor
     * @access Private
     */
    public function TPPA()
    {
        parent::Persistente();

        $this->setTabela('ppa.ppa');

        $this->setCampoCod('cod_ppa');

        $this->AddCampo('cod_ppa'           , 'sequence' , true, ''    , true , false);
        $this->AddCampo('timestamp'         , 'timestamp', true, ''    , false, false);
        $this->AddCampo('ano_inicio'        , 'varchar'  , true, '4'   , false, false);
        $this->AddCampo('ano_final'         , 'varchar'  , true, '4'   , false, false);
        $this->addCampo('valor_total_ppa'   , 'numeric'  , true, '14,2', false, false);
        $this->AddCampo('destinacao_recurso', 'boolean'  , true, ''    , false, false);
        $this->AddCampo('importado'         , 'boolean'  , true, ''    , false, false);
    }

    public function recuperaPPA(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaPPA", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPPA()
    {
        $stSQL  = "  SELECT ppa.cod_ppa                                     \n";
        $stSQL .= "       , ppa.ano_inicio                                  \n";
        $stSQL .= "       , ppa.ano_final                                   \n";
        $stSQL .= "       , ppa.ano_inicio||' a '||ppa.ano_final AS periodo \n";
        $stSQL .= "       , ppa.fn_verifica_homologacao(ppa.cod_ppa) AS homologado \n";
        $stSQL .= "    FROM ppa.ppa                                         \n";

        return $stSQL;
    }

    public function recuperaPPAImportacao(&$rsPPAs, $stFiltro = '', $stOrdem = '', $boTransacao = '')
    {
        $obConexao = new Conexao();

        $stSql = $this->montaRecuperaPPAImportacao($stFiltro, $stOrdem);
        $this->setDebug($stSql);

        return $obConexao->executaSQL($rsPPAs, $stSql, $boTransacao);
    }

    public function montaRecuperaPPAImportacao($stCondicao = '', $stOrdem = '')
    {
        $stSQL  = "  SELECT ppa.cod_ppa                                                     \n";
        $stSQL .= "       , ppa.ano_inicio                                                  \n";
        $stSQL .= "       , ppa.ano_final                                                   \n";
        $stSQL .= "       , ppa.valor_total_ppa                                             \n";
        $stSQL .= "       , ppa.ano_inicio || ' a ' || ppa.ano_final AS periodo             \n";
        $stSQL .= "       , proximo.cod_ppa AS prox_cod_ppa                                 \n";
        $stSQL .= "    FROM ppa.ppa                                                         \n";
        $stSQL .= "         INNER JOIN ppa.ppa_norma                                        \n";
        $stSQL .= "         ON ppa_norma.cod_ppa = ppa.cod_ppa                              \n";
        $stSQL .= "         INNER JOIN ppa.ppa AS proximo                                   \n";
        $stSQL .= "         ON proximo.ano_inicio::integer = ppa.ano_inicio::integer + 4    \n";
        $stSQL .= "        AND proximo.ano_final::integer  = ppa.ano_final::integer  + 4    \n";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    public function recuperaDadosPPA(&$rsRecordSet, $stFiltro='', $stOrder='', $boTransacao='')
    {
        $stFiltro .= " GROUP BY ppa.cod_ppa
                              , ppa.ano_inicio
                              , ppa.ano_final
                              , ppa_encaminhamento.dt_encaminhamento
                              , ppa_encaminhamento.dt_devolucao
                              , periodicidade.cod_periodicidade
                              , periodicidade.nom_periodicidade
                              , ppa.timestamp
                              , ppa.destinacao_recurso
                              , precisao.nivel
                              , ppa_precisao.cod_precisao ";

        return $this->executaRecupera('montaRecuperaDadosPPA', $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaDadosPPA()
    {
        $stSQL .= "     SELECT ppa.cod_ppa                                                      \n";
        $stSQL .= "          , ppa.ano_inicio                                                   \n";
        $stSQL .= "          , ppa.ano_final                                                    \n";
        $stSQL .= "          , CASE WHEN ppa.fn_verifica_homologacao(ppa.cod_ppa) = true THEN   \n";
        $stSQL .= "                    'Homologado'                                             \n";
        $stSQL .= "                 ELSE                                                        \n";
        $stSQL .= "                    'Não Homologado'                                         \n";
        $stSQL .= "            END AS status                                                    \n";
        $stSQL .= "          , CASE WHEN ppa.fn_verifica_homologacao(ppa.cod_ppa) = true THEN   \n";
        $stSQL .= "                     TO_CHAR(TO_DATE(ppa_encaminhamento.dt_encaminhamento::varchar, 'yyyy-mm-dd'), 'dd/mm/yyyy')  \n";
        $stSQL .= "                 ELSE                                                        \n";
        $stSQL .= "                    ''                                                       \n";
        $stSQL .= "            END AS dt_encaminhamento                                         \n";
        $stSQL .= "          , CASE WHEN ppa.fn_verifica_homologacao(ppa.cod_ppa) = true THEN   \n";
        $stSQL .= "                     TO_CHAR(TO_DATE(ppa_encaminhamento.dt_devolucao::varchar, 'yyyy-mm-dd'), 'dd/mm/yyyy') \n";
        $stSQL .= "                 ELSE                                                        \n";
        $stSQL .= "                    ''                                                       \n";
        $stSQL .= "            END AS dt_devolucao                                              \n";
        $stSQL .= "          , CASE WHEN ppa.fn_verifica_homologacao(ppa.cod_ppa) = true THEN   \n";
        $stSQL .= "                     periodicidade.cod_periodicidade || ' - ' || periodicidade.nom_periodicidade \n";
        $stSQL .= "                 ELSE                                                        \n";
        $stSQL .= "                    ''                                                       \n";
        $stSQL .= "            END AS periodo_apuracao_metas                                    \n";
        $stSQL .= "          , ppa.timestamp                                                    \n";
        $stSQL .= "          , ppa.ano_inicio || ' a ' || ppa.ano_final AS periodo              \n";
        $stSQL .= "          , ppa.destinacao_recurso                                           \n";
        $stSQL .= "          , precisao.nivel                                                   \n";
        $stSQL .= "          , CASE WHEN ppa.destinacao_recurso = 't'                           \n";
        $stSQL .= "                THEN 'Sim'                                                   \n";
        $stSQL .= "                ELSE 'Não'                                                   \n";
        $stSQL .= "           END AS destinacao                                                 \n";
        $stSQL .= "          , ppa_precisao.cod_precisao                                        \n";
        $stSQL .= "          , CASE WHEN ppa_precisao.cod_precisao IS NOT NULL                  \n";
        $stSQL .= "                THEN 'Sim'                                                   \n";
        $stSQL .= "                ELSE 'Não'                                                   \n";
        $stSQL .= "           END AS precisao                                                   \n";
        $stSQL .= "       FROM ppa.ppa                                                          \n";
        $stSQL .= "  LEFT JOIN ppa.macro_objetivo                                               \n";
        $stSQL .= "         ON macro_objetivo.cod_ppa = ppa.cod_ppa                             \n";
        $stSQL .= "  LEFT JOIN ppa.programa_setorial                                            \n";
        $stSQL .= "         ON programa_setorial.cod_macro = macro_objetivo.cod_macro           \n";
        $stSQL .= "  LEFT JOIN ppa.programa                                                     \n";
        $stSQL .= "         ON programa.cod_setorial = programa_setorial.cod_setorial           \n";
        $stSQL .= "  LEFT JOIN ppa.programa_dados                                               \n";
        $stSQL .= "         ON programa_dados.cod_programa = programa.cod_programa              \n";
        $stSQL .= "  LEFT JOIN ppa.ppa_publicacao                                               \n";
        $stSQL .= "         ON ppa_publicacao.cod_ppa = ppa.cod_ppa                             \n";
        $stSQL .= "        AND ppa_publicacao.timestamp = ( SELECT MAX(timestamp)               \n";
        $stSQL .= "                                           FROM ppa.ppa_publicacao           \n";
        $stSQL .= "                                          WHERE cod_ppa = ppa.cod_ppa )      \n";
        $stSQL .= "  LEFT JOIN ppa.ppa_encaminhamento                                           \n";
        $stSQL .= "         ON ppa_encaminhamento.cod_ppa   = ppa_publicacao.cod_ppa            \n";
        $stSQL .= "        AND ppa_encaminhamento.timestamp = ppa_publicacao.timestamp          \n";
        $stSQL .= "  LEFT JOIN ppa.periodicidade                                                \n";
        $stSQL .= "         ON periodicidade.cod_periodicidade = ppa_encaminhamento.cod_periodicidade \n";
        $stSQL .= "  LEFT JOIN ppa.ppa_precisao                                                 \n";
        $stSQL .= "         ON ppa_precisao.cod_ppa = ppa.cod_ppa                               \n";
        $stSQL .= "  LEFT JOIN ppa.precisao                                                     \n";
        $stSQL .= "         ON precisao.cod_precisao = ppa_precisao.cod_precisao                \n";

        return $stSQL;
    }

    public function recuperaPPAHomologacao(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
    {
        return $this->executaRecupera("montaRecuperaPPAHomologacao", $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
    }

    public function montaRecuperaPPAHomologacao()
    {
        $stSQL  = "     SELECT ppa.cod_ppa                                              \n";
        $stSQL .= "          , ppa.ano_inicio                                           \n";
        $stSQL .= "          , ppa.ano_final                                            \n";
        $stSQL .= "          , ppa.ano_inicio||' a '||ppa.ano_final AS periodo          \n";
        $stSQL .= "       FROM ppa.ppa                                                  \n";
        $stSQL .= " INNER JOIN ppa.macro_objetivo                                       \n";
        $stSQL .= "         ON macro_objetivo.cod_ppa = ppa.cod_ppa                     \n";
        $stSQL .= " INNER JOIN ppa.programa_setorial                                    \n";
        $stSQL .= "         ON programa_setorial.cod_macro = macro_objetivo.cod_macro   \n";
        $stSQL .= " INNER JOIN ppa.programa                                             \n";
        $stSQL .= "        ON programa.cod_setorial = programa_setorial.cod_setorial    \n";
        $stSQL .= " INNER JOIN ppa.acao                                                 \n";
        $stSQL .= "         ON acao.cod_programa = programa.cod_programa                \n";

        return $stSQL;
    }
    
    function recuperaPPAHomolagacaoNorma(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stOrdem = ($stOrdem != "") ? " ORDER BY ".$stOrdem : $stOrdem;
        $stSql = $this->montaRecuperaPPAHomolagacaoNorma().$stFiltro.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaRecuperaPPAHomolagacaoNorma()
    {
        $stSql = "SELECT ppa.cod_ppa								                                                                                            \n";					    
		$stSql.= "     , ppa.ano_inicio						                                                                                                    \n";						    
		$stSql.= "     , ppa.ano_final    				                                                                                                        \n";      						    
		$stSql.= "     , ppa.timestamp                                                                                                                          \n";   						    
		$stSql.= "     , ppa.ano_inicio||' a '||ppa.ano_final AS periodo					                                                                    \n";
		$stSql.= "     , ppa_publicacao.timestamp AS dt_homologacao				                                                                                \n";
		$stSql.= "     , ppa_publicacao.cod_norma 				                                                                                                \n";
		$stSql.= "     , tipo_norma.nom_tipo_norma||' '||LPAD(norma.num_norma, 6, '0')||'/'||norma.exercicio||' - '||norma.nom_norma AS descricao_norma 		\n";
	    $stSql.= "  FROM ppa.ppa       							                                                                                                \n";		                
	    $stSql.= "  JOIN ppa.ppa_publicacao						                                                                                                \n";					    
 		$stSql.= "    ON ppa_publicacao.cod_ppa = ppa.cod_ppa				                                                                                    \n";
 		$stSql.= "   AND ppa_publicacao.timestamp = (SELECT MAX(ppa_publicacao.timestamp) FROM ppa.ppa_publicacao WHERE ppa_publicacao.cod_ppa = ppa.cod_ppa)   \n";
        if($this->getDado('exercicio'))
            $stSql.= "   AND ".$this->getDado('exercicio')." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER				                            \n";
        else
            $stSql.= "   AND ".Sessao::getExercicio()." BETWEEN ppa.ano_inicio::INTEGER AND ppa.ano_final::INTEGER				                                \n";
	    $stSql.= "  JOIN normas.norma				                                                                                                            \n";
 		$stSql.= "    ON norma.cod_norma = ppa_publicacao.cod_norma				                                                                                \n";
	    $stSql.= "  JOIN normas.tipo_norma				                                                                                                        \n";
		$stSql.= "    ON tipo_norma.cod_tipo_norma = norma.cod_tipo_norma				                                                                        \n";
 		$stSql.= " WHERE ppa.fn_verifica_homologacao(ppa.cod_ppa) = TRUE          			                                                                    \n";

        return $stSql;
    }

    public function excluirPPA(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;        
        $stSql = $this->montaExcluirPPA();
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }

    public function montaExcluirPPA()
    {
        $stSql = " SELECT ppa.excluirPPA(".$this->getDado('cod_ppa').") as retorno; ";

        return $stSql;
    }

}

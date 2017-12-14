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
 * Classe de Mapeamento para a tabela processo
 * Data de Criação: 25/07/2005

 * @author Analista: Cassiano
 * @author Desenvolvedor: Cassiano

 Casos de uso: uc-01.06.98

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TProcesso extends Persistente
{

    public function TProcesso()
    {
        parent::Persistente();
        $this->setTabela('sw_processo');
        $this->setComplementoChave('cod_processo,ano_exercicio');

        $this->AddCampo('cod_processo','integer',true,'',true,false);
        $this->AddCampo('ano_exercicio','varchar',true,'',true,false);
        $this->AddCampo('cod_classificacao','integer',true,'',false,true);
        $this->AddCampo('cod_assunto','integer',true,'',false,true);
    #    $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('cod_usuario','integer',true,'',false,true);
        $this->AddCampo('cod_situacao','integer',true,'',false,true);
        $this->AddCampo('observacoes','text',true,'',false,false);
        $this->AddCampo('confidencial','boolean',true,1,false,false);
        $this->AddCampo('resumo_assunto','varchar',false,'80',false,false);
        $this->AddCampo('cod_centro','integer',false,'',false,true);
        $this->AddCampo('timestamp','timestamp_now',true,'',false,false);
    }

    public function recuperaProcesso(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaProcesso().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function recuperaSituacaoArquivamentoProcesso(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaSituacaoArquivamentoProcesso().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function listarProcessoAlteracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaListarProcessoAlteracao().$stOrdem;
        $stSqlTemp = explode('UNION',$stSql);

        foreach ($stSqlTemp as $key => $value) {
            $stGrupo = explode ('GROUP BY', $stSqlTemp[$key]);

            if ($stGrupo[1] && ($stSqlTemp[1] || $stSqlTemp[2])) {
                 $stSqlTemp[$key] = $stGrupo[0].' WHERE 1=1 '.$stFiltro.' GROUP BY '.$stGrupo[1];
            }
        }
        $stSql = implode('UNION',$stSqlTemp);
        $this->stDebug = $stSql;

        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function recuperaHistoricoArquivamentoProcesso(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaHistoricoArquivamentoProcesso().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql  = "   SELECT  P.*                                          \n";
        $stSql .= "        ,  P.cod_processo||'/'||P.ano_exercicio as cod_processo_completo  \n";
        $stSql .= "        ,  A.nom_assunto                                \n";
        $stSql .= "        ,  C.nom_classificacao                          \n";

        $stSql .= "     FROM  sw_processo      as P                        \n";
        $stSql .= "        ,  sw_assunto       as A                        \n";
        $stSql .= "        ,  sw_classificacao as C                        \n";

        $stSql .= "    WHERE  P.cod_assunto       = A.cod_assunto          \n";
        $stSql .= "      AND  P.cod_classificacao = C.cod_classificacao    \n";
        $stSql .= "      AND  C.cod_classificacao = A.cod_classificacao    \n";

        return $stSql;
    }

    public function montaRecuperaProcesso()
    {
        $stSql  = "   SELECT																	\n";
        $stSql .= "		      P.cod_processo													\n";
        $stSql .= "        ,  P.ano_exercicio													\n";
        $stSql .= "        ,  P.cod_classificacao												\n";
        $stSql .= "        ,  P.cod_assunto														\n";
        $stSql .= "        ,  P.cod_usuario														\n";
        $stSql .= "        ,  P.cod_situacao													\n";
        $stSql .= "		   ,  P.timestamp														\n";
        $stSql .= "		   ,  P.observacoes														\n";
        $stSql .= "		   ,  P.confidencial													\n";
        $stSql .= "		   ,  P.resumo_assunto													\n";

        $stSql .= "        ,  P.cod_processo||'/'||P.ano_exercicio as cod_processo_completo  	\n";
        $stSql .= "        ,  A.nom_assunto														\n";
        $stSql .= "        ,  C.nom_classificacao												\n";
        $stSql .= "        ,  TO_CHAR(P.timestamp,'dd/mm/yyyy') as inclusao						\n";
        $stSql .= "        ,  G.nom_cgm  														\n";
        $stSql .= "        ,  G.numcgm                                                          \n";

        $stSql .= "    FROM   sw_processo              as P           							\n";
        $stSql .= "        ,  sw_assunto               as A										\n";
        $stSql .= "        ,  sw_classificacao         as C										\n";
        $stSql .= "        ,  sw_cgm                   as G										\n";
        $stSql .= "        ,  sw_processo_interessado  as PI									\n";

        $stSql .= "    WHERE  P.cod_assunto       = A.cod_assunto								\n";
        $stSql .= "      AND  P.cod_classificacao = C.cod_classificacao							\n";
        $stSql .= "      AND  C.cod_classificacao = A.cod_classificacao							\n";
    #    $stSql .= "      AND  P.numcgm            = G.numcgm									\n";

        $stSql .= "		 AND  PI.ano_exercicio = P.ano_exercicio								\n";
        $stSql .= "		 AND  PI.cod_processo  = P.cod_processo									\n";
        $stSql .= "      AND  PI.numcgm		   = G.numcgm  										\n";

        return $stSql;
    }

    public function montaRecuperaSituacaoArquivamentoProcesso()
    {
        $stSql  = "    SELECT  sw_situacao_Processo.cod_situacao, sw_situacao_Processo.nom_situacao     \n";
        $stSql .= "      FROM  sw_situacao_Processo                                                     \n";
        $stSql .= "     WHERE  sw_situacao_Processo.cod_situacao = 9                                    \n";
        $stSql .= "        OR  sw_situacao_Processo.cod_situacao = 5                                    \n";

        return $stSql;
    }

    public function montaHistoricoArquivamentoProcesso()
    {
        $stSql  = "    SELECT   H.cod_historico, H.nom_historico                                     \n";
        $stSql .= "             from sw_historico_arquivamento as H  \n";

        return $stSql;
    }

    public function montaListarProcessoAlteracao()
    {

        $stSql  = " SELECT  cod_processo                                                       		\n";
        $stSql .= " 		, ano_exercicio                                                    		\n";
        $stSql .= " 		, array_to_string(array_agg(nom_cgm), ', ')as nom_cgm                   \n";
        $stSql .= " 		, nom_classificacao                                                		\n";
        $stSql .= " 		, nom_assunto                                                      		\n";
        $stSql .= " 		, cod_classificacao                                               		\n";
        $stSql .= " 		, cod_assunto                                                     		\n";
        $stSql .= " 		, inclusao                                                         		\n";
        $stSql .= " 		, resumo_assunto                                                        		\n";
        $stSql .= " FROM (                                                                    		\n";
        $stSql .= " SELECT                                                                    		\n";
        $stSql .= "     SW_PROCESSO.cod_processo                                                    \n";
        $stSql .= "    ,SW_PROCESSO.ano_exercicio                                                   \n";
        $stSql .= "    ,TO_CHAR(SW_PROCESSO.timestamp,'dd/mm/yyyy') AS inclusao                     \n";
        $stSql .= "    ,SW_CGM.nom_cgm                                                              \n";
        $stSql .= "    ,SW_CLASSIFICACAO.nom_classificacao                                          \n";
        $stSql .= "    ,SW_ASSUNTO.nom_assunto                                                      \n";
        $stSql .= "    ,SW_CLASSIFICACAO.cod_classificacao                                          \n";
        $stSql .= "    ,SW_ASSUNTO.cod_assunto                                                      \n";
        $stSql .= "    ,SW_PROCESSO.resumo_assunto                                                  \n";
        $stSql .= "    ,sw_processo_interessado.numcgm                                              \n";
        $stSql .= " FROM                                                                            \n";
        $stSql .= "     SW_PROCESSO                                                                 \n";

        $stSql .= " INNER JOIN sw_processo_interessado
                            ON sw_processo_interessado.cod_processo = sw_processo.cod_processo
                           AND sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio    \n";

        $stSql .= " LEFT JOIN sw_assunto_atributo_valor ON sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo  \n";
        $stSql .= "                                    AND sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio \n";
        $stSql .= "    ,SW_ULTIMO_ANDAMENTO                                                         \n";
        $stSql .= "    ,SW_CLASSIFICACAO                                                            \n";
        $stSql .= "    ,SW_ASSUNTO                                                                  \n";
        $stSql .= "    ,SW_CGM                                                                      \n";
        $stSql .= "    ,SW_ANDAMENTO                                                                \n";
        $stSql .= " WHERE                                                                        	\n";
        $stSql .= "     (
                            (SW_PROCESSO.cod_situacao  = '2' AND SW_ANDAMENTO.cod_andamento = '1' ) \n";
        $stSql .= "     OR (
                                     sw_processo.cod_situacao   = '3'
                                AND  sw_andamento.cod_andamento = '0'
                                AND  sw_andamento.cod_orgao           = '".Sessao::read('codOrgao')."'
                                -- AND  sw_andamento.cod_unidade         = '".Sessao::read('codUnidade')."'
                                -- AND  sw_andamento.cod_departamento    = '".Sessao::read('codDpto')."'
                                -- AND  sw_andamento.cod_setor           = '".Sessao::read('codSetor')."'
                                -- AND  sw_andamento.ano_exercicio_setor = '".Sessao::read('anoExercicio')."'

                            )
                        ) 																			\n";
        $stSql .= "     AND SW_ANDAMENTO.cod_andamento = SW_ULTIMO_ANDAMENTO.cod_andamento          \n";
        $stSql .= "     AND SW_ANDAMENTO.cod_processo  = SW_ULTIMO_ANDAMENTO.cod_processo           \n";
        $stSql .= "     AND SW_ANDAMENTO.ano_exercicio = SW_ULTIMO_ANDAMENTO.ano_exercicio          \n";
        $stSql .= "     AND SW_PROCESSO.cod_processo   = SW_ANDAMENTO.cod_processo                  \n";
        $stSql .= "     AND SW_PROCESSO.ano_exercicio  = SW_ANDAMENTO.ano_exercicio                 \n";
        $stSql .= "     AND SW_CLASSIFICACAO.cod_classificacao = SW_ASSUNTO.cod_classificacao       \n";
        $stSql .= "     AND SW_PROCESSO.cod_classificacao = SW_CLASSIFICACAO.cod_classificacao      \n";
        $stSql .= "     AND SW_PROCESSO.cod_assunto       = SW_ASSUNTO.cod_assunto                  \n";
        $stSql .= "     AND sw_processo_interessado.numcgm = SW_CGM.numcgm                          \n";
        $stSql .= "     AND NOT EXISTS
                                (
                                    SELECT  1
                                      FROM  sw_recebimento
                                     WHERE  sw_recebimento.cod_processo  = sw_andamento.cod_processo
                                       AND  sw_recebimento.ano_exercicio = sw_andamento.ano_exercicio
                                       AND  sw_recebimento.cod_andamento = 1
                                )

                        AND EXISTS
                            (
                                SELECT  1
                                  FROM  sw_andamento as andamento_cadastro
                                 WHERE  andamento_cadastro.cod_processo  = sw_andamento.cod_processo
                                   AND  andamento_cadastro.ano_exercicio = sw_andamento.ano_exercicio
                                   AND  andamento_cadastro.cod_andamento = 0
                                   AND  andamento_cadastro.cod_orgao           = '".Sessao::read('codOrgao')."'
                                   -- AND  andamento_cadastro.cod_unidade         = '".Sessao::read('codUnidade')."'
                                   -- AND  andamento_cadastro.cod_departamento    = '".Sessao::read('codDpto')."'
                                   -- AND  andamento_cadastro.cod_setor           = '".Sessao::read('codSetor')."'
                                   -- AND  andamento_cadastro.ano_exercicio_setor = '".Sessao::read('anoExercicio')."'
                            )";

        include_once (CAM_FW_LEGADO."funcoesLegado.lib.php"      );
        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $select = 	"
                    SELECT
                        *
                    FROM
                        administracao.usuario   AS AU
                    WHERE
                        AU.cod_orgao        = ".Sessao::read('codOrgao')."
                        -- AU.cod_unidade      = ".Sessao::read('codUnidade')."    AND
                        -- AU.cod_departamento = ".Sessao::read('codDpto')."       AND
                        -- AU.cod_setor        = ".Sessao::read('codSetor')."      AND
                        -- AU.ano_exercicio    = '".Sessao::getExercicio()."' ";

        $dbConfig = new dataBaseLegado;
        $dbConfig->abreBd();
        $dbConfig->abreSelecao($select);

        while (!($dbConfig->eof())) {
            $codUsuario = $dbConfig->pegaCampo("numcgm");
            $stSql .= "     AND SW_ULTIMO_ANDAMENTO.cod_usuario = ".$codUsuario."                     \n";
            $dbConfig->vaiProximo();
            if (!$dbConfig->eof()) {
                $stSql .= " UNION SELECT                                                                    \n";
                $stSql .= "     SW_PROCESSO.cod_processo                                                    \n";
                $stSql .= "    ,SW_PROCESSO.ano_exercicio                                                   \n";
                $stSql .= "    ,TO_CHAR(SW_PROCESSO.timestamp,'dd/mm/yyyy') AS inclusao                     \n";
                $stSql .= "    ,SW_CGM.nom_cgm                                                              \n";
                $stSql .= "    ,SW_CLASSIFICACAO.nom_classificacao                                          \n";
                $stSql .= "    ,SW_ASSUNTO.nom_assunto                                                      \n";
                $stSql .= "    ,SW_CLASSIFICACAO.cod_classificacao                                          \n";
                $stSql .= "    ,SW_ASSUNTO.cod_assunto                                                      \n";
                $stSql .= "    ,SW_PROCESSO.resumo_assunto                                                  \n";
                $stSql .= "    ,sw_processo_interessado.numcgm                                              \n";
                $stSql .= " FROM                                                                            \n";
                $stSql .= "     SW_PROCESSO                                                                 \n";
                $stSql .= " LEFT JOIN sw_assunto_atributo_valor ON sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo  \n";
                $stSql .= "                                    AND sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio \n";

                $stSql .= " INNER JOIN sw_processo_interessado
                                    ON sw_processo_interessado.cod_processo = sw_processo.cod_processo
                                   AND sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio    \n";

                $stSql .= "    ,SW_ULTIMO_ANDAMENTO                                                         \n";
                $stSql .= "    ,SW_CLASSIFICACAO                                                            \n";
                $stSql .= "    ,SW_ASSUNTO                                                                  \n";
                $stSql .= "    ,SW_CGM                                                                      \n";
                $stSql .= "    ,SW_ANDAMENTO                                                                \n";
                $stSql .= " WHERE                                                                           \n";
                $stSql .= "     SW_PROCESSO.cod_situacao  = '2'                                             \n";
                $stSql .= "     AND SW_ANDAMENTO.cod_andamento  = '1'                                       \n";
                $stSql .= "     AND SW_ANDAMENTO.cod_andamento = SW_ULTIMO_ANDAMENTO.cod_andamento          \n";
                $stSql .= "     AND SW_ANDAMENTO.cod_processo  = SW_ULTIMO_ANDAMENTO.cod_processo           \n";
                $stSql .= "     AND SW_ANDAMENTO.ano_exercicio = SW_ULTIMO_ANDAMENTO.ano_exercicio          \n";
                $stSql .= "     AND SW_PROCESSO.cod_processo   = SW_ANDAMENTO.cod_processo                  \n";
                $stSql .= "     AND SW_PROCESSO.ano_exercicio  = SW_ANDAMENTO.ano_exercicio                 \n";
                $stSql .= "     AND SW_CLASSIFICACAO.cod_classificacao = SW_ASSUNTO.cod_classificacao       \n";
                $stSql .= "     AND SW_PROCESSO.cod_classificacao = SW_CLASSIFICACAO.cod_classificacao      \n";
                $stSql .= "     AND SW_PROCESSO.cod_assunto       = SW_ASSUNTO.cod_assunto                  \n";
                $stSql .= "     AND sw_processo_interessado.numcgm = SW_CGM.numcgm                           \n";

            }
        }
        $stSql .= ") as resultado                                                              										\n";
        $stSql .= "GROUP BY cod_processo, ano_exercicio, nom_classificacao, nom_assunto, cod_classificacao, cod_assunto , inclusao, resumo_assunto 	\n";

        return $stSql;
    }

    public function recuperaProcessoAlteracao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stGroup = "GROUP BY 	sw_processo.ano_exercicio
                                , sw_processo.cod_processo
                                , sw_processo.timestamp
                                , sw_ultimo_andamento.cod_andamento
                                , sw_classificacao.nom_classificacao
                                , sw_assunto.nom_assunto ";
        $stSql = $this->montaRecuperaProcessoAlteracao().$stFiltro.$stGroup.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

        return $obErro;
    }

    public function montaRecuperaProcessoAlteracao()
    {
        $stSql  = " SELECT DISTINCT sw_processo.ano_exercicio
                     , sw_processo.cod_processo
                     , sw_processo.timestamp
                     , sw_ultimo_andamento.cod_andamento
                     , sw_classificacao.nom_classificacao
                     , sw_assunto.nom_assunto
                     , array_to_string(array_agg(nom_cgm), ', ')as nom_cgm

                     FROM  sw_processo

               INNER JOIN  sw_processo_interessado
                       ON  sw_processo_interessado.cod_processo = sw_processo.cod_processo
                      AND  sw_processo_interessado.ano_exercicio = sw_processo.ano_exercicio

               INNER JOIN  sw_assunto
                       ON  sw_assunto.cod_assunto       = sw_processo.cod_assunto
                      AND  sw_assunto.cod_classificacao = sw_processo.cod_classificacao

               INNER JOIN  sw_classificacao
                       ON  sw_assunto.cod_classificacao = sw_classificacao.cod_classificacao

               INNER JOIN  sw_cgm
                       ON  sw_cgm.numcgm = sw_processo_interessado.numcgm

               INNER JOIN  sw_situacao_processo
                       ON  sw_processo.cod_situacao  = sw_situacao_processo.cod_situacao

               INNER JOIN  sw_ultimo_andamento
                       ON  sw_processo.ano_exercicio = sw_ultimo_andamento.ano_exercicio
                      AND  sw_processo.cod_processo  = sw_ultimo_andamento.cod_processo

                LEFT JOIN  sw_assunto_atributo_valor
                       ON  sw_assunto_atributo_valor.cod_processo  = sw_processo.cod_processo
                      AND  sw_assunto_atributo_valor.exercicio     = sw_processo.ano_exercicio

                    WHERE  sw_ultimo_andamento.cod_orgao IN (select cod_orgao
                                                             from organograma.vw_orgao_nivel
                                                             where orgao_reduzido like (select distinct(vw_orgao_nivel.orgao_reduzido)
                                                                                          from organograma.vw_orgao_nivel
                                                                                         where vw_orgao_nivel.cod_orgao=".Sessao::read('codOrgao').")||'%'
                                                                                      group by cod_orgao)
                   AND sw_situacao_processo.cod_situacao = 3";

        return $stSql;

    }
}

?>

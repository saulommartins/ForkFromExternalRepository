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
    * Classe de mapeamento da tabela ORCAMENTO.DESPESA
    * Data de Criação: 13/07/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Marcelo B. Paulino

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TOrcamentoDespesa.class.php 66137 2016-07-21 13:50:55Z michel $

    * Casos de uso: uc-02.01.06
                    uc-02.01.26
                    uc-02.01.16
                    uc-02.08.07
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TOrcamentoDespesa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
public function __construct()
{
    parent::Persistente();
    $this->setTabela('orcamento.despesa');

    $this->setCampoCod('cod_despesa');
    $this->setComplementoChave('exercicio');

    $this->AddCampo('exercicio','char',true,'04',true,true);
    $this->AddCampo('cod_despesa','integer',true,'',true,false);
    $this->AddCampo('cod_entidade','integer',true,'',false,true);
    $this->AddCampo('cod_programa','integer',true,'',false,true);
    $this->AddCampo('cod_conta','integer',true,'',false,true);
    $this->AddCampo('num_pao','integer',true,'',false,true);
    $this->AddCampo('num_orgao','integer',true,'',false,true);
    $this->AddCampo('num_unidade','integer',true,'',false,true);
    $this->AddCampo('cod_funcao','integer',true,'',false,true);
    $this->AddCampo('cod_recurso','integer',true,'',false,true);
    $this->AddCampo('cod_subfuncao','integer',true,'',false,true);
    $this->AddCampo('vl_original','numeric',true,'14,02',false,false);
    $this->AddCampo('dt_criacao','date',false,'',false,false);
}

public function montaRecuperaRelacionamento()
{
    $stSql = "  SELECT
     CD.mascara_classificacao,
     ppa.acao.num_acao AS num_acao,
     trim(CD.descricao) as descricao,
     OD.*,
     R.cod_recurso
     ,R.cod_fonte
     ,publico.fn_mascara_dinamica( (
                SELECT valor
                FROM administracao.configuracao
                WHERE parametro = 'masc_despesa'
                AND exercicio = '2013'
                             ),
      OD.num_orgao
      ||'.'||OD.num_unidade
      ||'.'||OD.cod_funcao
      ||'.'||OD.cod_subfuncao
      ||'.'||ppa.programa.num_programa
      ||'.'||ppa.acao.num_acao
      ||'.'||replace(cd.mascara_classificacao,'.','')
                         )
                         ||'.'||replace(r.cod_fonte,'.','')
                         as dotacao
 FROM
     orcamento.vw_classificacao_despesa AS CD,
     orcamento.despesa        AS OD
     JOIN orcamento.programa_ppa_programa
    ON programa_ppa_programa.cod_programa =OD.cod_programa
   AND programa_ppa_programa.exercicio   =OD.exercicio
  JOIN ppa.programa
    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa
  JOIN orcamento.pao_ppa_acao
    ON pao_ppa_acao.num_pao =OD.num_pao
   AND pao_ppa_acao.exercicio = OD.exercicio
  JOIN ppa.acao
    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao
    , orcamento.recurso AS R
 WHERE
         CD.exercicio IS NOT NULL
     AND OD.cod_conta   = CD.cod_conta
     AND OD.exercicio   = CD.exercicio
     AND OD.cod_recurso = R.cod_recurso
     AND OD.exercicio   = R.exercicio";

    return $stSql;
}

public function recuperaDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDespesa().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaDespesa()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                             \n";
    $stSql .= "      CD.cod_estrutural as mascara_classificacao                     \n";
    $stSql .= "     ,CD.descricao                                                   \n";
    $stSql .= "     ,O.*                                                            \n";
    $stSql .= "     ,publico.fn_mascara_dinamica( (                                 \n";
    $stSql .= "                SELECT valor                                         \n";
    $stSql .= "                FROM administracao.configuracao                      \n";
    $stSql .= "                WHERE parametro = 'masc_despesa'                     \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'    \n";
    $stSql .= "                             ),                                      \n";
    $stSql .= "      O.num_orgao                                                    \n";
    $stSql .= "      ||'.'||O.num_unidade                                           \n";
    $stSql .= "      ||'.'||O.cod_funcao                                            \n";
    $stSql .= "      ||'.'||O.cod_subfuncao                                         \n";
    $stSql .= "      ||'.'||PP.num_programa                                         \n";
    $stSql .= "      ||'.'||PA.num_acao                                             \n";
    $stSql .= "      ||'.'||replace(cd.cod_estrutural,'.','')                       \n";
    $stSql .= "                         )                                           \n";
    $stSql .= "                         ||'.'||publico.fn_mascara_dinamica( (       \n";
    $stSql .= "                SELECT valor                                         \n";
    $stSql .= "                FROM administracao.configuracao                      \n";
    $stSql .= "                WHERE parametro = 'masc_recurso'                     \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'    \n";
    $stSql .= "                                                             ),      \n";
    $stSql .= "                         cast(cod_recurso as VARCHAR)                \n";
    $stSql .= "                         ) as dotacao                                \n";
    $stSql .= "  FROM                                                               \n";
    $stSql .= "      orcamento.conta_despesa  AS CD,                                \n";
    $stSql .= "      orcamento.despesa        AS O                                  \n";
    $stSql .= "  JOIN orcamento.programa AS OP                                      \n";
    $stSql .= "    ON OP.cod_programa=O.cod_programa                                \n";
    $stSql .= "   AND OP.exercicio=O.exercicio                                      \n";
    $stSql .= "  JOIN ppa.programa AS PP                                            \n";
    $stSql .= "    ON PP.cod_programa=OP.cod_programa                               \n";
    $stSql .= "  JOIN orcamento.despesa_acao                                        \n";
    $stSql .= "    ON despesa_acao.cod_despesa = O.cod_despesa                      \n";
    $stSql .= "   AND despesa_acao.exercicio_despesa = O.exercicio                  \n";
    $stSql .= "  JOIN ppa.acao AS PA                                                \n";
    $stSql .= "    ON PA.cod_acao = despesa_acao.cod_acao                           \n";
    $stSql .= "  WHERE                                                              \n";
    $stSql .= "          CD.exercicio IS NOT NULL                                   \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                             \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                             \n";

    return $stSql;
}

public function verificaDuplicidade(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaVerificaDuplicidade().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaVerificaDuplicidade()
{
    $stQuebra = "\n";
    $stSql  = "SELECT                                                   \n";
    $stSql .= "     *                                                   \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "     orcamento.despesa                                   \n";

    return $stSql;
}

public function recuperaListaDespesaCredEspecial(&$rsRecordSet, $stOrdem = "", $boTransacao = "")
{
    $obErro    = new Erro;
    $obConexao = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaDespesaCredEspecial().$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao ) ;
    $this->setDebug($stSql);

    return $obErro;
}

public function montaListaDespesaCredEspecial()
{
    
    $stSql  = " SELECT *                                                    
                  FROM orcamento.despesa                                      
             
             LEFT JOIN orcamento.conta_despesa                           
                    ON conta_despesa.cod_conta = despesa.cod_conta
                   AND conta_despesa.exercicio = despesa.exercicio
             
             LEFT JOIN empenho.pre_empenho_despesa                       
                    ON pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                   AND pre_empenho_despesa.exercicio   = despesa.exercicio   
             
             LEFT JOIN orcamento.suplementacao_suplementada              
                    ON suplementacao_suplementada.cod_despesa = despesa.cod_despesa
                   AND suplementacao_suplementada.exercicio   = despesa.exercicio   
             
             LEFT JOIN orcamento.suplementacao_reducao                   
                    ON suplementacao_reducao.cod_despesa = despesa.cod_despesa
                   AND suplementacao_reducao.exercicio   = despesa.exercicio  
                 
                 WHERE despesa.vl_original = 0.00                               
                   AND despesa.exercicio   = '".$this->getDado('exercicio')."' \n";
    
    if ($this->getDado('cod_despesa')) {
        $stSql .= "  AND despesa.cod_despesa = ".$this->getDado('cod_despesa')." \n";
    }

    if ($this->getDado('cod_entidade')) {
        $stSql .= "  AND despesa.cod_entidade = ".$this->getDado('cod_entidade')." \n";
    }
    
    $stSql .= " AND pre_empenho_despesa.cod_pre_empenho     IS NULL
                AND suplementacao_reducao.cod_suplementacao IS NULL ";

    return $stSql;
}

public function recuperaListaDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaDotacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaListaDotacao()
{
    $stQuebra = "\n";

    $stSql  = "SELECT                                                   \n";
    $stSql .= "      D.cod_entidade,                                    \n";
    $stSql .= "      D.cod_despesa,                                     \n";
    $stSql .= "      D.exercicio,                                       \n";
    $stSql .= "      CD.cod_conta,                                      \n";
    $stSql .= "      CD.descricao,                                      \n";
    $stSql .= "      CD.cod_estrutural,                                 \n";
    $stSql .= "      D.num_orgao,                                       \n";
    $stSql .= "      D.num_unidade                                      \n";
    $stSql .= "  FROM                                                   \n";
    $stSql .= "      orcamento.despesa        AS D,                     \n";
    $stSql .= "      orcamento.conta_despesa  AS CD                     \n";
    $stSql .= "  WHERE                                                  \n";
    $stSql .= "          D.cod_conta     = CD.cod_conta                 \n";
    $stSql .= "      AND D.exercicio     = CD.exercicio                 \n";

    return $stSql;
}

public function recuperaDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDotacao().$stCondicao.$stOrdem.") as tabela";
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaDotacao()
{
    $stQuebra = "\n";
    $stSql  = " SELECT *, \n";

    $stSql .= " (valor_orcado + valor_suplementado - valor_reduzido - valor_empenhado + valor_anulado - valor_reserva) as saldo_disponivel ";

    $stSql .= " ,(valor_orcado - valor_reserva) as saldo";
    
    $stSql .= " FROM(                                                           \n";

    $stSql .= " SELECT                                                          \n";
    $stSql .= "      D.cod_entidade,                                            \n";
    $stSql .= "      D.exercicio,                                               \n";
    $stSql .= "      CGM.nom_cgm as entidade,                                   \n";

    $stSql .= "      D.cod_despesa,                                             \n";
    $stSql .= "      CD.cod_conta,                                              \n";
    $stSql .= "      CD.descricao,                                              \n";

    $stSql .= "      D.num_orgao,                                               \n";
    $stSql .= "      OO.nom_orgao, ";

    $stSql .= "      D.num_unidade,                                             \n";
    $stSql .= "      OU.nom_unidade, ";

    $stSql .= "      D.cod_funcao,                                              \n";
    $stSql .= "      F.descricao as funcao,                                     \n";

    $stSql .= "      D.cod_subfuncao,                                           \n";
    $stSql .= "      SF.descricao as subfuncao,                                 \n";

    $stSql .= "      D.cod_programa,                                            \n";
    $stSql .= "      ppa.programa.num_programa AS num_programa,                 \n";
    $stSql .= "      P.descricao as programa,                                   \n";

    $stSql .= "      D.num_pao,                                                 \n";
    $stSql .= "      ppa.acao.num_acao AS num_acao,                             \n";
    $stSql .= "      PAO.nom_pao,                                               \n";

    $stSql .= "      CD.cod_estrutural,                                         \n";

    $stSql .= "      D.cod_recurso,                                             \n";
    $stSql .= "      R.nom_recurso,                                             \n";
    $stSql .= "      R.cod_fonte,                                            \n";
    $stSql .= "      R.masc_recurso_red,                                        \n";
    $stSql .= "      R.cod_detalhamento,                                        \n";

    $stSql .= "      coalesce(sum(D.vl_original),0.00) as valor_orcado,         \n";
    $stSql .= "      coalesce(sum(SS.valor),0.00)      as valor_suplementado,   \n";
    $stSql .= "      coalesce(sum(SR.valor),0.00)      as valor_reduzido,       \n";

    $stSql .= "      coalesce(sum(RS.vl_reserva),0.00) as valor_reserva,         \n";

    $stSql .= "      coalesce(sum(EMP.vl_empenhado),0.00) as valor_empenhado,   \n";
    $stSql .= "      coalesce(sum(EMP.vl_anulado),0.00)   as valor_anulado,     \n";
    $stSql .= "      coalesce(sum(EMP.vl_liquidado),0.00) as valor_liquidado,   \n";
    $stSql .= "      coalesce(sum(EMP.vl_pago),0.00)      as valor_pago         \n";

    $stSql .= "  FROM                                                           \n";
    $stSql .= "      orcamento.despesa        AS D                              \n";
    $stSql .= "        LEFT JOIN (                                                      \n";
    $stSql .= "            SELECT                                                       \n";
    $stSql .= "                SSUP.cod_despesa,                                        \n";
    $stSql .= "                SSUP.exercicio,                                          \n";
    $stSql .= "                coalesce(sum(SSUP.valor),0.00) as valor                  \n";
    $stSql .= "            FROM                                                         \n";
    $stSql .= "                orcamento.suplementacao_suplementada    as SSUP,         \n";
    $stSql .= "                orcamento.suplementacao                 as S             \n";
    $stSql .= "            WHERE                                                        \n";
    $stSql .= "                SSUP.cod_suplementacao  = S.cod_suplementacao   AND      \n";
    $stSql .= "                SSUP.exercicio          = S.exercicio                    \n";
    $stSql .= "                                                                         \n";
    $stSql .= "            AND S.cod_tipo <> 16                                         \n";
    $stSql .= "            AND ( select sa.cod_suplementacao                            \n";
    $stSql .= "                    from orcamento.suplementacao_anulada as sa           \n";
    $stSql .= "                   where sa.exercicio = S.exercicio                      \n";
    $stSql .= "                     and sa.cod_suplementacao = S.cod_suplementacao      \n";
    $stSql .= "                ) IS NULL                                                \n";
    $stSql .= "                                                                         \n";

    if($this->getDado("stDataInicial"))
        $stSql .= "            AND S.dt_suplementacao BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy') \n";
    $stSql .= "       GROUP BY SSUP.cod_despesa, SSUP.exercicio                         \n";
    $stSql .= "        )  as SS ON                                                      \n";
    $stSql .= "            D.cod_despesa   = SS.cod_despesa    AND                      \n";
    $stSql .= "            D.exercicio     = SS.exercicio                               \n";
    $stSql .= "        LEFT JOIN (                                                      \n";
    $stSql .= "            SELECT                                                       \n";
    $stSql .= "                SRED.cod_despesa,                                        \n";
    $stSql .= "                SRED.exercicio,                                          \n";
    $stSql .= "                coalesce(sum(SRED.valor),0.00) as valor                  \n";
    $stSql .= "            FROM                                                         \n";
    $stSql .= "                orcamento.suplementacao_reducao         as SRED,         \n";
    $stSql .= "                orcamento.suplementacao                 as S             \n";
    $stSql .= "            WHERE                                                        \n";
    $stSql .= "                SRED.cod_suplementacao  = S.cod_suplementacao   AND      \n";
    $stSql .= "                SRED.exercicio          = S.exercicio                    \n";
    $stSql .= "                                                                         \n";
    $stSql .= "            AND S.cod_tipo <> 16                                         \n";
    $stSql .= "            AND ( select sa.cod_suplementacao                            \n";
    $stSql .= "                    from orcamento.suplementacao_anulada as sa           \n";
    $stSql .= "                   where sa.exercicio = S.exercicio                      \n";
    $stSql .= "                     and sa.cod_suplementacao = S.cod_suplementacao      \n";
    $stSql .= "                ) IS NULL                                                \n";
    $stSql .= "                                                                         \n";

    if($this->getDado("stDataInicial"))
        $stSql .= "            AND S.dt_suplementacao BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy') \n";
    $stSql .= "       GROUP BY SRED.cod_despesa, SRED.exercicio                         \n";

    $stSql .= "        ) as SR ON                                                       \n";
    $stSql .= "            D.cod_despesa   = SR.cod_despesa    AND                      \n";
    $stSql .= "            D.exercicio     = SR.exercicio                               \n";
    $stSql .= "        LEFT JOIN (                                                      \n";
    $stSql .= "            SELECT                                                       \n";
    $stSql .= "                R.cod_despesa,                                           \n";
    $stSql .= "                R.exercicio,                                             \n";
    $stSql .= "                coalesce(sum(R.vl_reserva),0.00) as vl_reserva           \n";
    $stSql .= "            FROM                                                         \n";
    $stSql .= "                orcamento.reserva_saldos        AS R                     \n";
    $stSql .= "            WHERE NOT EXISTS ( SELECT 1                                      \n";
    $stSql .= "                                 FROM orcamento.reserva_saldos_anulada orsa  \n";
    $stSql .= "                                WHERE orsa.cod_reserva = R.cod_reserva       \n";
    $stSql .= "                                  AND orsa.exercicio   = R.exercicio         \n";

    $stSql .= "                             )                                               \n";

    $stSql .= "                                  AND R.dt_validade_final > to_date(now()::varchar, 'yyyy-mm-dd') \n";

    if ($this->getDado("stDataInicial")) {
       $stSql .= "        AND  R.dt_inclusao BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy') \n";
    }
    $stSql .= "        GROUP BY R.cod_despesa, R.exercicio                              \n";
    $stSql .= "        )            as RS ON                                            \n";
    $stSql .= "            D.cod_despesa   = RS.cod_despesa    AND                      \n";
    $stSql .= "            D.exercicio     = RS.exercicio                               \n";
    $stSql .= "        LEFT JOIN (                                                                                                  \n";
    $stSql .= "            SELECT                                                                                                   \n";
    $stSql .= "                PD.cod_despesa,                                                                                      \n";
    $stSql .= "                PD.exercicio,                                                                                        \n";
    $stSql .= "                EE.cod_entidade,                                                                                     \n";
    $stSql .= "                coalesce(sum(EMP.valor),0.00)               as vl_empenhado,                                         \n";
    $stSql .= "                coalesce(sum(ANU.valor),0.00)               as vl_anulado,                                           \n";
    $stSql .= "                (coalesce(sum(NL.vl_liquidado),0.00) - coalesce(sum(NL.vl_liquidado_anulado),0.00)) as vl_liquidado, \n";
    $stSql .= "                (coalesce(sum(NL.vl_pago),0.00) - coalesce(sum(NL.vl_estornado),0.00))              as vl_pago       \n";
    $stSql .= "            FROM                                                                                                     \n";
    $stSql .= "                    empenho.empenho             AS EE                                                                \n";
    $stSql .= "                        LEFT JOIN (                                                                                  \n";
    $stSql .= "                            SELECT                                                                                   \n";
    $stSql .= "                                PE.exercicio,                                                                        \n";
    $stSql .= "                                PE.cod_pre_empenho,                                                                  \n";
    $stSql .= "                                coalesce(sum(IE.vl_total),0.00) as valor                                             \n";
    $stSql .= "                            FROM                                                                                     \n";
    $stSql .= "                                empenho.empenho                       AS E,                                          \n";
    $stSql .= "                                empenho.pre_empenho                   AS PE,                                         \n";
    $stSql .= "                                empenho.item_pre_empenho              AS IE                                          \n";
    $stSql .= "                            WHERE                                                                                    \n";
    $stSql .= "                                    E.cod_pre_empenho   = PE.cod_pre_empenho                                         \n";
    $stSql .= "                            AND     E.exercicio         = PE.exercicio                                               \n";

    if($this->getDado("stDataInicial"))
        $stSql .= "                        AND     E.dt_empenho BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";

    $stSql .= "                            AND     IE.cod_pre_empenho   = PE.cod_pre_empenho                                        \n";
    $stSql .= "                            AND     IE.exercicio         = PE.exercicio                                              \n";
    $stSql .= "                            GROUP BY PE.exercicio,PE.cod_pre_empenho                                                 \n";
    $stSql .= "                        ) as EMP ON (                                                                                \n";
    $stSql .= "                                    EMP.exercicio         = EE.exercicio                                             \n";
    $stSql .= "                            AND     EMP.cod_pre_empenho   = EE.cod_pre_empenho                                       \n";
    $stSql .= "                        )                                                                                            \n";
    $stSql .= "                        LEFT JOIN (                                                                                  \n";
    $stSql .= "                            SELECT                                                                                   \n";
    $stSql .= "                                EA.exercicio,                                                                        \n";
    $stSql .= "                                EA.cod_empenho,                                                                      \n";
    $stSql .= "                                EA.cod_entidade,                                                                     \n";
    $stSql .= "                                coalesce(sum(EAI.vl_anulado),0.00) as valor                                          \n";
    $stSql .= "                            FROM                                                                                     \n";
    $stSql .= "                                empenho.empenho_anulado               AS EA,                                         \n";
    $stSql .= "                                empenho.empenho_anulado_item          AS EAI                                         \n";
    $stSql .= "                           WHERE                                                                                     \n";
    $stSql .= "                                    EA.exercicio        = EAI.exercicio                                              \n";
    $stSql .= "                            AND     EA.cod_entidade     = EAI.cod_entidade                                           \n";
    $stSql .= "                            AND     EA.cod_empenho      = EAI.cod_empenho                                            \n";
    $stSql .= "                            AND     EA.timestamp        = EAI.timestamp                                              \n";
    if($this->getDado("stDataInicial"))
    $stSql .= "                        AND     to_date(to_char(EA.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";
    $stSql .= "                        GROUP BY EA.exercicio, EA.cod_empenho, EA.cod_entidade                                       \n";
    $stSql .= "                        ) as ANU ON (                                                                                \n";
    $stSql .= "                                    ANU.exercicio     = EE.exercicio                                                 \n";
    $stSql .= "                            AND     ANU.cod_empenho   = EE.cod_empenho                                               \n";
    $stSql .= "                            AND     ANU.cod_entidade  = EE.cod_entidade                                              \n";
    $stSql .= "                        )                                                                                            \n";
    $stSql .= "                        LEFT JOIN (                                                                                  \n";
    $stSql .= "                            SELECT                                                                                   \n";
    $stSql .= "                                NL.exercicio,                                                                        \n";
    $stSql .= "                                NL.cod_empenho,                                                                      \n";
    $stSql .= "                                NL.cod_entidade,                                                                     \n";
    $stSql .= "                                sum(NLI.vl_total)       as vl_liquidado,                                             \n";
    $stSql .= "                                sum(NLIA.valor)         as vl_liquidado_anulado,                                     \n";
    $stSql .= "                                sum(NLP.vl_pago)        as vl_pago,                                                  \n";
    $stSql .= "                                sum(NLPA.vl_estornado)  as vl_estornado                                              \n";
    $stSql .= "                            FROM                                                                                     \n";
    $stSql .= "                                empenho.nota_liquidacao             AS NL                                            \n";
    $stSql .= "                                LEFT JOIN (                                                                          \n";
    $stSql .= "                                select                                                                               \n";
    $stSql .= "                                    exercicio,                                                                       \n";
    $stSql .= "                                    cod_nota,                                                                        \n";
    $stSql .= "                                    cod_entidade,                                                                    \n";
    $stSql .= "                                    coalesce(sum(vl_total),0.00)as vl_total                                                         \n";
    $stSql .= "                                    from                                                                             \n";
    $stSql .= "                                    empenho.nota_liquidacao_item                                                     \n";
    $stSql .= "                                    group by                                                                         \n";
    $stSql .= "                                    exercicio,cod_nota,cod_entidade                                                  \n";
    $stSql .= "                                ) as NLI on                                                                          \n";
    $stSql .= "                                    NL.exercicio         = NLI.exercicio                                             \n";
    $stSql .= "                                AND NL.cod_nota          = NLI.cod_nota                                              \n";
    $stSql .= "                                AND NL.cod_entidade      = NLI.cod_entidade                                          \n";
    if($this->getDado("stDataInicial"))
        $stSql .= "                                    AND NL.dt_liquidacao BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";

    $stSql .= "                                    LEFT JOIN (                                                                      \n";
    $stSql .= "                                        SELECT                                                                       \n";
    $stSql .= "                                            NLI.exercicio,                                                           \n";
    $stSql .= "                                            NLI.cod_nota,                                                            \n";
    $stSql .= "                                            NLI.cod_entidade,                                                        \n";
    $stSql .= "                                            coalesce(sum(NLIA.vl_anulado),0.00) as valor                             \n";
    $stSql .= "                                        FROM                                                                         \n";
    $stSql .= "                                            empenho.nota_liquidacao_item            AS NLI,                          \n";
    $stSql .= "                                            empenho.nota_liquidacao_item_anulado    AS NLIA                          \n";
    $stSql .= "                                        WHERE                                                                        \n";
    $stSql .= "                                                NLI.exercicio        = NLIA.exercicio                                \n";
    $stSql .= "                                            AND NLI.cod_nota         = NLIA.cod_nota                                 \n";
    $stSql .= "                                            AND NLI.num_item         = NLIA.num_item                                 \n";
    $stSql .= "                                            AND NLI.exercicio_item   = NLIA.exercicio_item                           \n";
    $stSql .= "                                            AND NLI.cod_pre_empenho  = NLIA.cod_pre_empenho                          \n";
    $stSql .= "                                            AND NLI.cod_entidade     = NLIA.cod_entidade                             \n";

    if($this->getDado("stDataInicial"))
        $stSql .= "                                        AND to_date(to_char(NLIA.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";
    $stSql .= "                                     GROUP BY nli.exercicio, nli.cod_nota, nli.cod_entidade                          \n";
    $stSql .= "                                    ) as NLIA ON                                                                     \n";
    $stSql .= "                                            NL.exercicio         = NLIA.exercicio                                    \n";
    $stSql .= "                                        AND NL.cod_nota          = NLIA.cod_nota                                     \n";
    $stSql .= "                                        AND NL.cod_entidade      = NLIA.cod_entidade                                 \n";
    $stSql .= "                                                                                                                     \n";
    $stSql .= "                                    LEFT JOIN (                                                                      \n";
    $stSql .= "                                       SELECT                                                                        \n";
    $stSql .= "                                           coalesce(sum(NLP.vl_pago),0.00) as vl_pago,                               \n";
    $stSql .= "                                           NLP.exercicio,                                                            \n";
    $stSql .= "                                           NLP.cod_entidade,                                                         \n";
    $stSql .= "                                           NLP.cod_nota                                                              \n";
    $stSql .= "                                       FROM                                                                          \n";
    $stSql .= "                                           empenho.nota_liquidacao_paga AS NLP                                       \n";
    $stSql .= "                                                                                                                     \n";
    if($this->getDado("stDataInicial"))
        $stSql .= "                                   WHERE to_date(to_char(NLP.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";
    $stSql .= "                                       GROUP BY NLP.exercicio, NLP.cod_entidade, NLP.cod_nota                        \n";
    $stSql .= "                                   ) as NLP ON                                                                       \n";
    $stSql .= "                                           NL.exercicio         = NLP.exercicio                                      \n";
    $stSql .= "                                       AND NL.cod_nota          = NLP.cod_nota                                       \n";
    $stSql .= "                                       AND NL.cod_entidade      = NLP.cod_entidade                                   \n";
    $stSql .= "                                                                                                                     \n";
    $stSql .= "                                    LEFT JOIN (                                                                      \n";
    $stSql .= "                                        SELECT                                                                       \n";
    $stSql .= "                                            NLP.exercicio,                                                           \n";
    $stSql .= "                                            NLP.cod_nota,                                                            \n";
    $stSql .= "                                            NLP.cod_entidade,                                                        \n";
    $stSql .= "                                            coalesce(sum(NLPA.vl_anulado),0.00) as vl_estornado                      \n";
    $stSql .= "                                        FROM                                                                         \n";
    $stSql .= "                                            empenho.nota_liquidacao_paga            AS NLP,                          \n";
    $stSql .= "                                            empenho.nota_liquidacao_paga_anulada    AS NLPA                          \n";
    $stSql .= "                                        WHERE                                                                        \n";
    $stSql .= "                                                NLP.exercicio        = NLPA.exercicio                                \n";
    $stSql .= "                                            AND NLP.cod_nota         = NLPA.cod_nota                                 \n";
    $stSql .= "                                            AND NLP.cod_entidade     = NLPA.cod_entidade                             \n";
    $stSql .= "                                            AND NLP.timestamp        = NLPA.timestamp                                \n";

    if($this->getDado("stDataInicial"))
        $stSql .= "                                        AND to_date(to_char(NLPA.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy') BETWEEN to_date('".$this->getDado("stDataInicial")."'::varchar,'dd/mm/yyyy') AND to_date('".$this->getDado("stDataFinal")."'::varchar,'dd/mm/yyyy')\n";
    $stSql .= "                                         GROUP BY nlp.exercicio,nlp.cod_nota,nlp.cod_entidade                       \n";
    $stSql .= "                        ) as NLPA ON                                                                                 \n";
    $stSql .= "                                            NL.exercicio         = NLPA.exercicio                                    \n";
    $stSql .= "                                        AND NL.cod_nota          = NLPA.cod_nota                                     \n";
    $stSql .= "                                        AND NL.cod_entidade      = NLPA.cod_entidade                                 \n";
    $stSql .= "                            GROUP BY                                                                                 \n";
    $stSql .= "                                NL.exercicio,                                                                        \n";
    $stSql .= "                                NL.cod_empenho,                                                                      \n";
    $stSql .= "                                NL.cod_entidade                                                                      \n";
    $stSql .= "                        ) as NL ON (                                                                                 \n";
    $stSql .= "                                    NL.exercicio     = EE.exercicio                                                  \n";
    $stSql .= "                            AND     NL.cod_empenho   = EE.cod_empenho                                                \n";
    $stSql .= "                            AND     NL.cod_entidade  = EE.cod_entidade                                               \n";
    $stSql .= "                        )                                                                                            \n";
    $stSql .= "                    ,empenho.pre_empenho         AS PE                                                               \n";
    $stSql .= "                    ,empenho.pre_empenho_despesa AS PD                                                               \n";
    $stSql .= "            WHERE                                                                                                    \n";
    $stSql .= "                       EE.cod_pre_empenho = PE.cod_pre_empenho                                                       \n";
    $stSql .= "                AND    EE.exercicio       = PE.exercicio                                                             \n";
    $stSql .= "                                                                                                                     \n";
    $stSql .= "                AND    PD.cod_pre_empenho = PE.cod_pre_empenho                                                       \n";
    $stSql .= "                AND    PD.exercicio       = PE.exercicio                                                             \n";
    $stSql .= "            GROUP BY                                                                                                 \n";
    $stSql .= "                PD.cod_despesa,                                                                                      \n";
    $stSql .= "                PD.exercicio,                                                                                        \n";
    $stSql .= "                EE.cod_entidade                                                                                      \n";
    $stSql .= "                                                                                                                     \n";
    $stSql .= "        ) AS EMP ON                                                                                                  \n";
    $stSql .= "            D.cod_despesa   = EMP.cod_despesa   AND                                                                  \n";
    $stSql .= "            D.exercicio     = EMP.exercicio     AND                                                                  \n";
    $stSql .= "            D.cod_entidade  = EMP.cod_entidade                                                                       \n";

    $stSql .= "            JOIN orcamento.programa_ppa_programa                                     \n";
    $stSql .= "              ON programa_ppa_programa.cod_programa = D.cod_programa           \n";
    $stSql .= "             AND programa_ppa_programa.exercicio   = D.exercicio               \n";
    $stSql .= "            JOIN ppa.programa                                                        \n";
    $stSql .= "              ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa  \n";
    $stSql .= "            JOIN orcamento.pao_ppa_acao                                              \n";
    $stSql .= "              ON pao_ppa_acao.num_pao = D.num_pao                              \n";
    $stSql .= "             AND pao_ppa_acao.exercicio = D.exercicio                          \n";
    $stSql .= "            JOIN ppa.acao                                                            \n";
    $stSql .= "              ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao                           \n";

    $stSql .= "      ,orcamento.conta_despesa  AS CD                            \n";

    $stSql .= "      ,orcamento.entidade       AS E                             \n";
    $stSql .= "      ,sw_cgm                   AS CGM                           \n";

    $stSql .= "      ,orcamento.orgao          AS OO                            \n";

    $stSql .= "      ,orcamento.unidade        AS OU                            \n";

    $stSql .= "      ,orcamento.funcao         AS F                             \n";

    $stSql .= "      ,orcamento.subfuncao      AS SF                            \n";

    $stSql .= "      ,orcamento.programa       AS P                             \n";

    $stSql .= "      ,orcamento.pao            AS PAO                           \n";
    if ( $this->getDado('stDataFinal') ) {
        $stSql .= "      ,orcamento.recurso(EXTRACT ( YEAR FROM to_date('".$this->getDado('stDataFinal')."'::varchar, 'dd/mm/yyyy'))::varchar) AS R                             \n";
    } else {
        $stSql .= "      ,orcamento.recurso('".$this->getDado('exercicio')."') AS R                             \n";
    }
    $stSql .= "  WHERE                                                          \n";
    $stSql .= "          D.cod_conta     = CD.cod_conta                         \n";
    $stSql .= "      AND D.exercicio     = CD.exercicio                         \n";

    $stSql .= "      AND D.exercicio     = OU.exercicio                         \n";
    $stSql .= "      AND D.num_unidade   = OU.num_unidade                       \n";
    $stSql .= "      AND D.num_orgao     = OU.num_orgao                         \n";

    $stSql .= "      AND D.exercicio     = E.exercicio                          \n";
    $stSql .= "      AND D.cod_entidade  = E.cod_entidade                       \n";

    $stSql .= "      AND E.numcgm        = CGM.numcgm                           \n";

    $stSql .= "      AND OU.exercicio    = OO.exercicio                         \n";
    $stSql .= "      AND OU.num_orgao    = OO.num_orgao                         \n";

    $stSql .= "      AND D.exercicio     = F.exercicio                          \n";
    $stSql .= "      AND D.cod_funcao    = F.cod_funcao                         \n";

    $stSql .= "      AND D.exercicio     = SF.exercicio                         \n";
    $stSql .= "      AND D.cod_subfuncao = SF.cod_subfuncao                     \n";

    $stSql .= "      AND D.exercicio     = P.exercicio                          \n";
    $stSql .= "      AND D.cod_programa  = P.cod_programa                       \n";

    $stSql .= "      AND D.exercicio     = PAO.exercicio                        \n";
    $stSql .= "      AND D.num_pao       = PAO.num_pao                          \n";

    $stSql .= "      AND D.exercicio     = R.exercicio                          \n";
    $stSql .= "      AND D.cod_recurso   = R.cod_recurso                        \n";

    return $stSql;
}

public function recuperaAnexo01(&$rsRecordSet, $stFiltroEntidade = "", $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAnexo01( $stFiltroEntidade ).$stFiltro.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaAnexo01($stFiltroEntidade = "")
{
    $stQuebra = "\n";
    $stSql  = " SELECT                                                             ".$stQuebra;
    $stSql .= "     VCLA.cod_conta,                                                ".$stQuebra;
    $stSql .= "     VCLA.descricao,                                                ".$stQuebra;
    $stSql .= "     substr( VCLA.mascara_classificacao, 1, 5 ) as classificacao,   ".$stQuebra;
    $stSql .= "     R.vl_original                                                  ".$stQuebra;
    $stSql .= " FROM                                                               ".$stQuebra;
    $stSql .= "     orcamento.vw_classificacao_despesa AS VCLA                 ".$stQuebra;
    $stSql .= "     LEFT OUTER JOIN orcamento.despesa AS R ON                      ".$stQuebra;
    $stSql .= "         R.exercicio = VCLA.exercicio AND                           ".$stQuebra;
    $stSql .= "         R.cod_conta = VCLA.cod_conta".$stFiltroEntidade."          ".$stQuebra;
    $stSql .= " WHERE                                                              ".$stQuebra;
    $stSql .= "     VCLA.exercicio IS NOT NULL                                     ".$stQuebra;

    return $stSql;
}

// Funcao para buscar nomes das funcoes de despesa
public function buscaNomesFuncao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "ORDER BY f.cod_funcao" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaBuscaNomesFuncao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaBuscaNomesFuncao()
{
    $stSql  = " SELECT                                                        \n";
    $stSql .= "     f.*,                                                      \n";
    $stSql .= "     'f_'||d.cod_funcao AS nom_funcao                          \n";
    $stSql .= " FROM                                                          \n";
    $stSql .= "    orcamento.funcao AS f                                  \n";
    $stSql .= " INNER JOIN                                                    \n";
    $stSql .= "     (SELECT DISTINCT cod_funcao,exercicio                     \n";
    $stSql .= "         FROM ".$this->getTabela()."                           \n";
    $stSql .= "     WHERE                                                     \n";
    $stSql .= "         exercicio = '".$this->getDado("exercicio")."'         \n";
    $stSql .= "        ".$this->getDado("stFiltro")."                         \n";
    $stSql .= "     ORDER BY cod_funcao ) AS d                                \n";
    $stSql .= " ON                                                            \n";
    $stSql .= "     f.cod_funcao = d.cod_funcao                               \n";
    $stSql .= " AND f.exercicio  = d.exercicio                                \n";

    return $stSql;

}

public function buscaGrupos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaBuscaGrupos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaBuscaGrupos()
{
    $stSql  = " select *,substr(classificacao_reduzida,3,2) as cod_grupo from orcamento.fn_somatorio_despesa('".$this->getDado('exercicio')."','".$this->getDado('stFiltro')."') as retorno(cod_conta integer, nivel integer, descricao varchar, classificacao varchar, classificacao_reduzida varchar, valor numeric) where nivel = 2 AND substr(classificacao,1,1)::integer = ".$this->getDado("categoria_economica");

    return $stSql;
}

public function montaRecuperaDespesaUsuario()
{
    $stSql  = "  SELECT                                                                   \n";
    $stSql .= "      CD.cod_estrutural as mascara_classificacao                           \n";
    $stSql .= "     ,CD.descricao                                                         \n";
    $stSql .= "     ,O.*                                                                  \n";
    $stSql .= "     ,publico.fn_mascara_dinamica( (                                       \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_despesa'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                             ),                                            \n";
    $stSql .= "      O.num_orgao                                                         \n";
    $stSql .= "      ||'.'||O.num_unidade                                                 \n";
    $stSql .= "      ||'.'||O.cod_funcao                                                  \n";
    $stSql .= "      ||'.'||O.cod_subfuncao                                               \n";
    $stSql .= "      ||'.'||O.cod_programa                                                \n";
    $stSql .= "      ||'.'||O.num_pao                                                     \n";
    $stSql .= "      ||'.'||replace(cd.cod_estrutural,'.','')                             \n";
    $stSql .= "                         )                                                 \n";
    $stSql .= "                         ||'.'||publico.fn_mascara_dinamica( (             \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_recurso'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                                                             ),            \n";
    $stSql .= "                         cast(cod_recurso as VARCHAR)                      \n";
    $stSql .= "                         ) as dotacao                                      \n";
    $stSql .= "  FROM                                                                     \n";
    $stSql .= "      orcamento.conta_despesa  AS CD,                                  \n";
    $stSql .= "      orcamento.despesa        AS O                                    \n";
    $stSql .= "  WHERE                                                                    \n";
    $stSql .= "          CD.exercicio IS NOT NULL                                         \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                                   \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                                   \n";

    return $stSql;
}

public function montaRecuperaDespesaUsuarioPermissao()
{
    $stSql  = "  SELECT                                                                   \n";
    $stSql .= "      CD.cod_estrutural as mascara_classificacao                           \n";
    $stSql .= "     ,CD.descricao                                                         \n";
    $stSql .= "     ,O.*                                                                  \n";
    $stSql .= "     ,publico.fn_mascara_dinamica( (                                       \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_despesa'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                             ),                                            \n";
    $stSql .= "      O.num_orgao                                                         \n";
    $stSql .= "      ||'.'||O.num_unidade                                                 \n";
    $stSql .= "      ||'.'||O.cod_funcao                                                  \n";
    $stSql .= "      ||'.'||O.cod_subfuncao                                               \n";
    $stSql .= "      ||'.'||PP.num_programa                                               \n";
    $stSql .= "      ||'.'||PA.num_acao                                                   \n";
    $stSql .= "      ||'.'||replace(cd.cod_estrutural,'.','')                             \n";
    $stSql .= "                         )                                                 \n";
    $stSql .= "                         ||'.'||publico.fn_mascara_dinamica( (             \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_recurso'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                                                             ),            \n";
    $stSql .= "                         cast(cod_recurso as VARCHAR)                      \n";
    $stSql .= "                         ) as dotacao                                      \n";
    $stSql .= "  FROM                                                                     \n";
    $stSql .= "      orcamento.conta_despesa  AS CD,                                      \n";
    $stSql .= "      orcamento.despesa        AS O                                        \n";
    $stSql .= "  JOIN orcamento.programa AS OP                                            \n";
    $stSql .= "    ON OP.cod_programa=O.cod_programa                                      \n";
    $stSql .= "   AND OP.exercicio=O.exercicio                                            \n";
    $stSql .= "  JOIN ppa.programa AS PP                                                  \n";
    $stSql .= "    ON PP.cod_programa=OP.cod_programa                                     \n";
    $stSql .= "  JOIN orcamento.despesa_acao                                              \n";
    $stSql .= "    ON despesa_acao.cod_despesa = O.cod_despesa                            \n";
    $stSql .= "   AND despesa_acao.exercicio_despesa = O.exercicio                        \n";
    $stSql .= "  JOIN ppa.acao AS PA                                                      \n";
    $stSql .= "    ON PA.cod_acao = despesa_acao.cod_acao                                 \n";
    $stSql .= "  WHERE                                                                    \n";
    $stSql .= "          CD.exercicio IS NOT NULL                                         \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                                   \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                                   \n";

    $stSql .= "      AND EXISTS (SELECT 1                                                                     \n";
    $stSql .= "                   FROM  empenho.permissao_autorizacao                                         \n";
    $stSql .= "                  WHERE  permissao_autorizacao.num_orgao   = O.num_orgao                       \n";
    $stSql .= "                    AND  permissao_autorizacao.num_unidade = O.num_unidade                     \n";
    $stSql .= "                    AND  permissao_autorizacao.numcgm      = ".$this->getDado("numcgm")."      \n";
    $stSql .= "                    AND  permissao_autorizacao.exercicio   = '".$this->getDado("exercicio")."' \n";
    $stSql .= "                  )                                                                            \n";

    return $stSql;
}

public function recuperaDespesaUsuarioOrcamento(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDespesaUsuarioOrcamento().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

// metedo duplicado para o bug 9112
public function montaRecuperaDespesaUsuarioOrcamento()
{
    $stSql  = "  SELECT                                                                   \n";
    $stSql .= "      CD.cod_estrutural as mascara_classificacao                           \n";
    $stSql .= "     ,CD.descricao                                                         \n";
    $stSql .= "     ,O.*                                                                  \n";
    $stSql .= "     ,publico.fn_mascara_dinamica( (                                       \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_despesa'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                             ),                                            \n";
    $stSql .= "      O.num_orgao                                                         \n";
    $stSql .= "      ||'.'||O.num_unidade                                                 \n";
    $stSql .= "      ||'.'||O.cod_funcao                                                  \n";
    $stSql .= "      ||'.'||O.cod_subfuncao                                               \n";
    $stSql .= "      ||'.'||PP.num_programa                                               \n";
    $stSql .= "      ||'.'||PA.num_acao                                                   \n";
    $stSql .= "      ||'.'||replace(cd.cod_estrutural,'.','')                             \n";
    $stSql .= "                         )                                                 \n";
    $stSql .= "                         ||'.'||publico.fn_mascara_dinamica( (             \n";
    $stSql .= "                SELECT valor                                               \n";
    $stSql .= "                FROM administracao.configuracao                                       \n";
    $stSql .= "                WHERE parametro = 'masc_recurso'                           \n";
    $stSql .= "                AND exercicio = '".$this->getDado('exercicio')."'          \n";
    $stSql .= "                                                             ),            \n";
    $stSql .= "                         cast(cod_recurso as VARCHAR)                      \n";
    $stSql .= "                         ) as dotacao                                      \n";
    $stSql .= "  FROM                                                                     \n";
    $stSql .= "      orcamento.conta_despesa  AS CD,                                      \n";
    $stSql .= "      orcamento.despesa        AS O                                        \n";
    $stSql .= "  JOIN orcamento.programa AS OP                                            \n";
    $stSql .= "    ON OP.cod_programa=O.cod_programa                                      \n";
    $stSql .= "   AND OP.exercicio=O.exercicio                                            \n";
    $stSql .= "  JOIN ppa.programa AS PP                                                  \n";
    $stSql .= "    ON PP.cod_programa=OP.cod_programa                                     \n";
    $stSql .= "  JOIN orcamento.despesa_acao                                              \n";
    $stSql .= "    ON despesa_acao.cod_despesa = O.cod_despesa                            \n";
    $stSql .= "   AND despesa_acao.exercicio_despesa = O.exercicio                        \n";
    $stSql .= "  JOIN ppa.acao AS PA                                                      \n";
    $stSql .= "    ON PA.cod_acao = despesa_acao.cod_acao                                 \n";
    $stSql .= "  WHERE                                                                    \n";
    $stSql .= "          CD.exercicio IS NOT NULL                                         \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                                   \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                                   \n";
    $stSql .= "      AND O.num_orgao||O.num_unidade IN (                                  \n";
    $stSql .= "                   SELECT                                                  \n";
    $stSql .= "                         num_orgao||num_unidade                            \n";
    $stSql .= "                   FROM                                                    \n";
    $stSql .= "                         orcamento.usuario_entidade                        \n";
    $stSql .= "                   WHERE                                                   \n";
    $stSql .= "                         numcgm    =  ".$this->getDado("numcgm")." AND     \n";
    $stSql .= "                         exercicio = '".$this->getDado("exercicio")."'     \n";
    $stSql .= "                  )                                                        \n";

    return $stSql;
}

public function montaRecuperaDespesaCentroCusto()
{
    $stSql  = "  SELECT                                                                   \n";
    $stSql .= "       cod_despesa,                                                                     \n";
    $stSql .= "       descricao                                                                     \n";
    $stSql .= "  FROM                                                                     \n";
    $stSql .= "       compras.fn_lista_dotacoes(                                             \n";
    if ( $this->getDado('cod_entidade') ) {
        $stSql .= $this->getDado('cod_entidade').",           \n";
    }
    if ( $this->getDado('exercicio') ) {
        $stSql .= "'".$this->getDado('exercicio')."',           \n";
    }
    if ( $this->getDado('cod_centro') ) {
        $stSql .= $this->getDado('cod_centro').",           \n";
    }
    if ( $this->getDado('cod_despesa') ) {
        $stSql .= "'".$this->getDado('cod_despesa')."',      \n";
    } else {
        $stSql .= "'', \n";
    }
    if ( $this->getDado('numcgm') ) {
        $stSql .= $this->getDado('numcgm')."      \n";
    }
    $stSql .= "       )                                            \n";
    $stSql .= "     AS (cod_despesa integer, descricao varchar)                                            \n";

    return $stSql;
}

/**
    * Mesma função do recuperaRelacionamento, mas efetua chamada a outro método para montar o SQL, o método montaRecuperaCodEstrutural.
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  String  $stOrdem     String de Ordenação do SQL (ORDER BY)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/

public function recuperaDespesaUsuario(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDespesaUsuario().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function recuperaDespesaUsuarioPermissao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDespesaUsuarioPermissao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function recuperaDespesaCentroCusto(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDespesaCentroCusto().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function recuperaDespesaDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDespesaDotacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaDespesaDotacao()
{
    $stSql  = "  SELECT                                                              \n";
    $stSql .= "     publico.fn_mascara_dinamica( ( SELECT valor FROM administracao.configuracao \n";
    $stSql .= "                                WHERE parametro = 'masc_despesa' AND exercicio='".$this->getDado( "exercicio" )."' ) \n";
    $stSql .= "             ,O.num_orgao                                 \n";
    $stSql .= "              ||'.'||O.num_unidade                        \n";
    $stSql .= "              ||'.'||O.cod_funcao                         \n";
    $stSql .= "              ||'.'||O.cod_subfuncao                      \n";
    $stSql .= "              ||'.'||ppa.programa.num_programa            \n";
    $stSql .= "              ||'.'||ppa.acao.num_acao                    \n";
    $stSql .= "              ||'.'||replace(CD.cod_estrutural,'.','')    \n";
    $stSql .= "      ) AS dotacao                                        \n";
    $stSql .= "     ,CD.descricao                                        \n";
    $stSql .= "     ,O.cod_despesa                                       \n";
    $stSql .= "     ,O.cod_recurso                                       \n";
    $stSql .= "  FROM                                                    \n";
    $stSql .= "      orcamento.conta_despesa  AS CD,                 \n";
    $stSql .= "      orcamento.despesa        AS O                   \n";
    $stSql .= "  JOIN orcamento.programa_ppa_programa                       \n";
    $stSql .= "    ON programa_ppa_programa.cod_programa = O.cod_programa  \n";
    $stSql .= "   AND programa_ppa_programa.exercicio   = O.exercicio      \n";
    $stSql .= "  JOIN ppa.programa                                          \n";
    $stSql .= "    ON ppa.programa.cod_programa = programa_ppa_programa.cod_programa_ppa \n";
    $stSql .= "  JOIN orcamento.pao_ppa_acao                                \n";
    $stSql .= "    ON pao_ppa_acao.num_pao = O.num_pao                     \n";
    $stSql .= "   AND pao_ppa_acao.exercicio = O.exercicio                 \n";
    $stSql .= "  JOIN ppa.acao                                              \n";
    $stSql .= "    ON ppa.acao.cod_acao = pao_ppa_acao.cod_acao             \n";
    $stSql .= "  WHERE                                                   \n";
    $stSql .= "          CD.exercicio IS NOT NULL                        \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                  \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                  \n";

    return $stSql;
}

public function recuperaSaldoDotacao(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaSaldoDotacao().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaSaldoDotacao()
{
    $stSql  = "SELECT                                                              \n";
    $stSql .= "  empenho.fn_saldo_dotacao (                                    \n";
    $stSql .= "                               '".$this->getDado( "exercicio" )."'  \n";
    $stSql .= "                               ,".$this->getDado( "cod_despesa" )." \n";
    $stSql .= "                               ) AS saldo_dotacao                   \n";

    return $stSql;
}

public function recuperaExportacaoBrubAnt(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaExportacaoBrubAnt().$stCondicao.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaExportacaoBrubAnt()
{
    $stSql  = " SELECT * FROM (                                                                                                                         \n";
    $stSql .= "   SELECT                                                                                                                                \n";
    $stSql .= "       od.num_orgao,                                                                                                                     \n";
    $stSql .= "       od.num_unidade,                                                                                                                   \n";
    $stSql .= "       od.cod_funcao,                                                                                                                    \n";
    $stSql .= "       od.cod_subfuncao,                                                                                                                 \n";
    $stSql .= "       programa.num_programa AS cod_programa,                                                                                            \n";
    $stSql .= "       acao.num_acao AS num_pao,                                                                                                         \n";
    $stSql .= "       ocd.cod_estrutural,                                                                                                               \n";
    $stSql .= "       od.cod_recurso,                                                                                                                   \n";
    $stSql .= "       coalesce(sum(empenhado_1.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_1.vl_anulado),0.00) as empenhado_1,                 \n";
    $stSql .= "       coalesce(sum(empenhado_2.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_2.vl_anulado),0.00) as empenhado_2,                 \n";
    $stSql .= "       coalesce(sum(empenhado_3.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_3.vl_anulado),0.00) as empenhado_3,                 \n";
    $stSql .= "       coalesce(sum(empenhado_4.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_4.vl_anulado),0.00) as empenhado_4,                 \n";
    $stSql .= "       coalesce(sum(empenhado_5.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_5.vl_anulado),0.00) as empenhado_5,                 \n";
    $stSql .= "       coalesce(sum(empenhado_6.vl_empenhado),0.00) - coalesce(sum(empenhado_anulado_6.vl_anulado),0.00) as empenhado_6,                 \n";
    $stSql .= "       coalesce(sum(liquidado_1.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_1.vl_anulado),0.00) as liquidado_1,                 \n";
    $stSql .= "       coalesce(sum(liquidado_2.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_2.vl_anulado),0.00) as liquidado_2,                 \n";
    $stSql .= "       coalesce(sum(liquidado_3.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_3.vl_anulado),0.00) as liquidado_3,                 \n";
    $stSql .= "       coalesce(sum(liquidado_4.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_4.vl_anulado),0.00) as liquidado_4,                 \n";
    $stSql .= "       coalesce(sum(liquidado_5.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_5.vl_anulado),0.00) as liquidado_5,                 \n";
    $stSql .= "       coalesce(sum(liquidado_6.vl_liquidado),0.00) - coalesce(sum(liquidado_anulado_6.vl_anulado),0.00) as liquidado_6,                 \n";
    $stSql .= "       coalesce(sum(pago_1.vl_pago),0.00) - coalesce(sum(pago_anulado_1.vl_anulado),0.00) as pago_1,                                     \n";
    $stSql .= "       coalesce(sum(pago_2.vl_pago),0.00) - coalesce(sum(pago_anulado_2.vl_anulado),0.00) as pago_2,                                     \n";
    $stSql .= "       coalesce(sum(pago_3.vl_pago),0.00) - coalesce(sum(pago_anulado_3.vl_anulado),0.00) as pago_3,                                     \n";
    $stSql .= "       coalesce(sum(pago_4.vl_pago),0.00) - coalesce(sum(pago_anulado_4.vl_anulado),0.00) as pago_4,                                     \n";
    $stSql .= "       coalesce(sum(pago_5.vl_pago),0.00) - coalesce(sum(pago_anulado_5.vl_anulado),0.00) as pago_5,                                     \n";
    $stSql .= "       coalesce(sum(pago_6.vl_pago),0.00) - coalesce(sum(pago_anulado_6.vl_anulado),0.00) as pago_6                                      \n";
    $stSql .= "       FROM                                                                                                                              \n";
    $stSql .= "           orcamento.despesa           as od,                                                                                            \n";
    $stSql .= "           orcamento.conta_despesa     as ocd,                                                                                           \n";
    $stSql .= "           orcamento.despesa_acao            ,                                                                                           \n";
    $stSql .= "           ppa.acao                          ,                                                                                           \n";
    $stSql .= "           ppa.programa                      ,                                                                                           \n";
    $stSql .= "           empenho.pre_empenho_despesa as eped                                                                                           \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "               AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')      \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_1 on(                                                                                                          \n";
    $stSql .= "                empenhado_1.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  empenhado_1.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "                   AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_2 on(                                                                                                          \n";
    $stSql .= "               empenhado_2.exercicio        = eped.exercicio                                                                             \n";
    $stSql .= "           AND empenhado_2.cod_pre_empenho  = eped.cod_pre_empenho                                                                       \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "            LEFT JOIN (                                                                                                                  \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "                   AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_3 on(                                                                                                          \n";
    $stSql .= "               empenhado_3.exercicio        = eped.exercicio                                                                             \n";
    $stSql .= "           AND empenhado_3.cod_pre_empenho  = eped.cod_pre_empenho                                                                       \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "                   AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_4 on(                                                                                                          \n";
    $stSql .= "               empenhado_4.exercicio        = eped.exercicio                                                                             \n";
    $stSql .= "           AND empenhado_4.cod_pre_empenho  = eped.cod_pre_empenho                                                                       \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "                   AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_5 on(                                                                                                          \n";
    $stSql .= "               empenhado_5.exercicio        = eped.exercicio                                                                             \n";
    $stSql .= "           AND empenhado_5.cod_pre_empenho  = eped.cod_pre_empenho                                                                       \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(ipe.vl_total),0.00) as vl_empenhado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND ipe.exercicio       = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.dt_empenho BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                  \n";
    $stSql .= "                   AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_6 on(                                                                                                          \n";
    $stSql .= "               empenhado_6.exercicio        = eped.exercicio                                                                             \n";
    $stSql .= "           AND empenhado_6.cod_pre_empenho  = eped.cod_pre_empenho                                                                       \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "            LEFT JOIN (                                                                                                                  \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')  \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_1 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_1.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_1.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_2 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_2.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_2.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "            LEFT JOIN (                                                                                                                  \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_3 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_3.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_3.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_4 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_4.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_4.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_5 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_5.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_5.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(eai.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                   ipe.exercicio,                                                                                                        \n";
    $stSql .= "                   ipe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.empenho             as e,                                                                                     \n";
    $stSql .= "                   empenho.pre_empenho         as epe,                                                                                   \n";
    $stSql .= "                   empenho.item_pre_empenho    as ipe,                                                                                   \n";
    $stSql .= "                   empenho.empenho_anulado_item as eai                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   to_date(to_char(eai.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND ipe.exercicio       = eai.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = eai.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.num_item        = eai.num_item                                                                                \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND ipe.cod_pre_empenho = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND ipe.exercicio       = epe.exercicio                                                                               \n";
    $stSql .= "               GROUP BY ipe.exercicio, ipe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as empenhado_anulado_6 on(                                                                                                  \n";
    $stSql .= "                empenhado_anulado_6.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  empenhado_anulado_6.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "                                                                                                                                         \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')   \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_1 on(                                                                                                          \n";
    $stSql .= "                liquidado_1.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_1.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_2 on(                                                                                                          \n";
    $stSql .= "                liquidado_2.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_2.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_3 on(                                                                                                          \n";
    $stSql .= "                liquidado_3.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_3.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_4 on(                                                                                                          \n";
    $stSql .= "                liquidado_4.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_4.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_5 on(                                                                                                          \n";
    $stSql .= "                liquidado_5.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_5.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nli.vl_total),0.00) as vl_liquidado,                                                                      \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli                                                                                    \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  e.cod_pre_empenho   = epe.cod_pre_empenho                                                                              \n";
    $stSql .= "              AND e.exercicio         = epe.exercicio                                                                                    \n";
    $stSql .= "              AND e.exercicio         = enl.exercicio_empenho                                                                            \n";
    $stSql .= "              AND e.cod_empenho       = enl.cod_empenho                                                                                  \n";
    $stSql .= "              AND e.cod_entidade      = enl.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.exercicio       = nli.exercicio                                                                                    \n";
    $stSql .= "              AND enl.cod_nota        = nli.cod_nota                                                                                     \n";
    $stSql .= "              AND enl.cod_entidade    = nli.cod_entidade                                                                                 \n";
    $stSql .= "              AND enl.dt_liquidacao BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                              \n";
    $stSql .= "                  AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_6 on(                                                                                                          \n";
    $stSql .= "                liquidado_6.exercicio        = eped.exercicio                                                                            \n";
    $stSql .= "           AND  liquidado_6.cod_pre_empenho  = eped.cod_pre_empenho                                                                      \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')   \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_1 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_1.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_1.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                        \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_2 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_2.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_2.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_3 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_3.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_3.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_4 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_4.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_4.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_5 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_5.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_5.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "              SELECT                                                                                                                     \n";
    $stSql .= "                  coalesce(sum(nlia.vl_anulado),0.00) as vl_anulado,                                                                     \n";
    $stSql .= "                  epe.exercicio,                                                                                                         \n";
    $stSql .= "                  epe.cod_pre_empenho                                                                                                    \n";
    $stSql .= "              FROM                                                                                                                       \n";
    $stSql .= "                  empenho.pre_empenho          as epe,                                                                                   \n";
    $stSql .= "                  empenho.empenho              as e,                                                                                     \n";
    $stSql .= "                  empenho.nota_liquidacao      as enl,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item as nli,                                                                                   \n";
    $stSql .= "                  empenho.nota_liquidacao_item_anulado as nlia                                                                           \n";
    $stSql .= "              WHERE                                                                                                                      \n";
    $stSql .= "                  to_date(to_char(nlia.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                  BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                \n";
    $stSql .= "                  AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                    \n";
    $stSql .= "                  AND nlia.exercicio      = nli.exercicio                                                                                \n";
    $stSql .= "                  AND nlia.cod_nota       = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND nlia.cod_entidade   = nli.cod_entidade                                                                             \n";
    $stSql .= "                  AND nlia.cod_pre_empenho= nli.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND nlia.exercicio_item = nli.exercicio_item                                                                           \n";
    $stSql .= "                  AND nlia.num_item  = nli.num_item                                                                                      \n";
    $stSql .= "                  AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                          \n";
    $stSql .= "                  AND e.exercicio         = epe.exercicio                                                                                \n";
    $stSql .= "                  AND e.exercicio         = enl.exercicio_empenho                                                                        \n";
    $stSql .= "                  AND e.cod_empenho       = enl.cod_empenho                                                                              \n";
    $stSql .= "                  AND e.cod_entidade      = enl.cod_entidade                                                                             \n";
    $stSql .= "                  AND enl.exercicio       = nli.exercicio                                                                                \n";
    $stSql .= "                  AND enl.cod_nota        = nli.cod_nota                                                                                 \n";
    $stSql .= "                  AND enl.cod_entidade    = nli.cod_entidade                                                                             \n";
    $stSql .= "              GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                                \n";
    $stSql .= "           ) as liquidado_anulado_6 on(                                                                                                  \n";
    $stSql .= "                liquidado_anulado_6.exercicio        = eped.exercicio                                                                    \n";
    $stSql .= "           AND  liquidado_anulado_6.cod_pre_empenho  = eped.cod_pre_empenho                                                              \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "                                                                                                                                         \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')  \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_1 on(                                                                                                               \n";
    $stSql .= "                pago_1.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_1.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_2 on(                                                                                                               \n";
    $stSql .= "                pago_2.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_2.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "          LEFT JOIN (                                                                                                                    \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_3 on(                                                                                                               \n";
    $stSql .= "                pago_3.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_3.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_4 on(                                                                                                               \n";
    $stSql .= "                pago_4.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_4.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_5 on(                                                                                                               \n";
    $stSql .= "                pago_5.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_5.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlp.vl_pago),0.00) as vl_pago,                                                                           \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp                                                                                   \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                   e.cod_pre_empenho   = epe.cod_pre_empenho                                                                             \n";
    $stSql .= "               AND e.exercicio         = epe.exercicio                                                                                   \n";
    $stSql .= "               AND e.exercicio         = enl.exercicio_empenho                                                                           \n";
    $stSql .= "               AND e.cod_empenho       = enl.cod_empenho                                                                                 \n";
    $stSql .= "               AND e.cod_entidade      = enl.cod_entidade                                                                                \n";
    $stSql .= "               AND enl.exercicio       = nlp.exercicio                                                                                   \n";
    $stSql .= "               AND enl.cod_nota        = nlp.cod_nota                                                                                    \n";
    $stSql .= "               AND enl.cod_entidade    = nlp.cod_entidade                                                                                \n";
    $stSql .= "               AND to_date(to_char(nlp.timestamp,'dd/mm/yyyy'),'dd/mm/yyyy')                                                             \n";
    $stSql .= "                   BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_6 on(                                                                                                               \n";
    $stSql .= "                pago_6.exercicio        = eped.exercicio                                                                                 \n";
    $stSql .= "           AND  pago_6.cod_pre_empenho  = eped.cod_pre_empenho                                                                           \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada as nlpa                                                                          \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/01/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date(to_char((to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')-1 ),'dd/mm/yyyy'),'dd/mm/yyyy')  \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_1 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_1.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_1.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada as nlpa                                                                          \n";
    $stSql .= "                WHERE                                                                                                                    \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/03/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/04/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_2 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_2.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_2.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "          LEFT JOIN (                                                                                                                    \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada as nlpa                                                                          \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/05/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('30/06/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_3 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_3.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_3.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada  as nlpa                                                                         \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/07/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/08/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_4 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_4.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_4.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada as nlpa                                                                          \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/09/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/10/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_5 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_5.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_5.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "           LEFT JOIN (                                                                                                                   \n";
    $stSql .= "               SELECT                                                                                                                    \n";
    $stSql .= "                   coalesce(sum(nlpa.vl_anulado),0.00) as vl_anulado,                                                                    \n";
    $stSql .= "                   epe.exercicio,                                                                                                        \n";
    $stSql .= "                   epe.cod_pre_empenho                                                                                                   \n";
    $stSql .= "               FROM                                                                                                                      \n";
    $stSql .= "                   empenho.pre_empenho          as epe,                                                                                  \n";
    $stSql .= "                   empenho.empenho              as e,                                                                                    \n";
    $stSql .= "                   empenho.nota_liquidacao      as enl,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga as nlp,                                                                                  \n";
    $stSql .= "                   empenho.nota_liquidacao_paga_anulada as nlpa                                                                          \n";
    $stSql .= "               WHERE                                                                                                                     \n";
    $stSql .= "                       nlpa.exercicio      = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND nlpa.cod_nota       = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND nlpa.cod_entidade   = nlp.cod_entidade                                                                            \n";
    $stSql .= "                   AND nlpa.timestamp      = nlp.timestamp                                                                               \n";
    $stSql .= "                   AND to_date(to_char(nlpa.timestamp_anulada,'dd/mm/yyyy'),'dd/mm/yyyy')                                                \n";
    $stSql .= "                   BETWEEN to_date('01/11/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                               \n";
    $stSql .= "                   AND to_date('31/12/".$this->getDado("stExercicio")."'::varchar,'dd/mm/yyyy')                                                   \n";
    $stSql .= "                   AND e.cod_pre_empenho   = epe.cod_pre_empenho                                                                         \n";
    $stSql .= "                   AND e.exercicio         = epe.exercicio                                                                               \n";
    $stSql .= "                   AND e.exercicio         = enl.exercicio_empenho                                                                       \n";
    $stSql .= "                   AND e.cod_empenho       = enl.cod_empenho                                                                             \n";
    $stSql .= "                   AND e.cod_entidade      = enl.cod_entidade                                                                            \n";
    $stSql .= "                   AND enl.exercicio       = nlp.exercicio                                                                               \n";
    $stSql .= "                   AND enl.cod_nota        = nlp.cod_nota                                                                                \n";
    $stSql .= "                   AND enl.cod_entidade    = nlp.cod_entidade                                                                            \n";
    $stSql .= "               GROUP BY epe.exercicio, epe.cod_pre_empenho                                                                               \n";
    $stSql .= "           ) as pago_anulado_6 on(                                                                                                       \n";
    $stSql .= "                pago_anulado_6.exercicio        = eped.exercicio                                                                         \n";
    $stSql .= "           AND  pago_anulado_6.cod_pre_empenho  = eped.cod_pre_empenho                                                                   \n";
    $stSql .= "           )                                                                                                                             \n";
    $stSql .= "       WHERE                                                                                                                             \n";
    $stSql .= "           od.cod_conta    = ocd.cod_conta                                                                                               \n";
    $stSql .= "       AND od.exercicio    = ocd.exercicio                                                                                               \n";
    $stSql .= "       AND od.exercicio    = eped.exercicio                                                                                              \n";
    $stSql .= "       AND od.cod_despesa  = eped.cod_despesa                                                                                            \n";
    $stSql .= "       AND despesa_acao.exercicio_despesa = od.exercicio                                                                                 \n";
    $stSql .= "       AND despesa_acao.cod_despesa       = od.cod_despesa                                                                               \n";
    $stSql .= "       AND acao.cod_acao = despesa_acao.cod_acao                                                                                         \n";
    $stSql .= "       AND programa.cod_programa = acao.cod_programa                                                                                     \n";
    $stSql .= "       AND od.exercicio='".$this->getDado("stExercicio")."'                                                                              \n";
    $stSql .= "       AND od.cod_entidade in (".$this->getDado("stCodEntidades").")                                                                     \n";
    $stSql .= "   GROUP by                                                                                                                              \n";
    $stSql .= "       od.num_orgao,                                                                                                                     \n";
    $stSql .= "       od.num_unidade,                                                                                                                   \n";
    $stSql .= "       od.cod_funcao,                                                                                                                    \n";
    $stSql .= "       od.cod_subfuncao,                                                                                                                 \n";
    $stSql .= "       programa.num_programa,                                                                                                            \n";
    $stSql .= "       acao.num_acao,                                                                                                                    \n";
    $stSql .= "       ocd.cod_estrutural,                                                                                                               \n";
    $stSql .= "       od.cod_recurso                                                                                                                    \n";
    $stSql .= "   ) as tbl                                                                                                                              \n";
    $stSql .= "   WHERE                                                                                                                                 \n";
    $stSql .= "       (tbl.empenhado_1 <> 0.00 OR                                                                                                       \n";
    $stSql .= "       tbl.empenhado_2 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.empenhado_3 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.empenhado_4 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.empenhado_5 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.empenhado_6 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_1 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_2 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_3 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_4 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_5 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.liquidado_6 <> 0.00 OR                                                                                                        \n";
    $stSql .= "       tbl.pago_1 <> 0.00 OR                                                                                                             \n";
    $stSql .= "       tbl.pago_2 <> 0.00 OR                                                                                                             \n";
    $stSql .= "       tbl.pago_3 <> 0.00 OR                                                                                                             \n";
    $stSql .= "       tbl.pago_4 <> 0.00 OR                                                                                                             \n";
    $stSql .= "       tbl.pago_5 <> 0.00 OR                                                                                                             \n";
    $stSql .= "       tbl.pago_6 <> 0.00                                                                                                                \n";
    $stSql .= "       );                                                                                                                                \n";

    return $stSql;
}

public function recuperaCodDespesa(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCodDespesa();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaCodDespesa()
{
    $stSql  =  "select rec.cod_despesa\n";
    $stSql .=  "from orcamento.conta_despesa    as ocr,\n";
    $stSql .=  "      orcamento.despesa          as rec\n";
    $stSql .=  "where     ocr.cod_estrutural= '".$this->getDado( 'cod_estrutural')."'\n";
    $stSql .=  "      and ocr.exercicio = '".$this->getDado( 'exercicio' ) ."'\n";
    $stSql .=  "      and rec.cod_conta = ocr.cod_conta\n";
    $stSql .=  "      and rec.exercicio = ocr.exercicio\n";

    return $stSql;
}

/* Utilizado no e-Sfinge (TCE-SC) */
public function recuperaProjetoAtividade(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProjetoAtividade();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Usado pelo e-Sfinge (TCE-SC) */
public function montaRecuperaProjetoAtividade()
{
    $stSql = "
                select despesa.exercicio
                     , despesa.cod_funcao
                     , despesa.cod_subfuncao
                     , substr(despesa.num_pao::VARCHAR, 1, 2) as tipo_acao
                     , despesa.num_pao
                     , pao.nom_pao
                  from orcamento.despesa
                  join orcamento.pao
                    on pao.exercicio = despesa.exercicio
                   and pao.num_pao = despesa.num_pao
                  join orcamento.recurso as recurso
                    on recurso.exercicio = despesa.exercicio
                   and recurso.cod_recurso = despesa.cod_recurso
                 where despesa.exercicio = '".$this->getDado('exercicio')."'
                 and despesa.cod_entidade in ( ".$this->getDado('cod_entidade')." )
             ";

    return $stSql;

}

/* Utilizado no e-Sfinge (TCE-SC) */
public function recuperaFonteRecursosDotacao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFonteRecursosDotacao();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/* Usado pelo e-Sfinge (TCE-SC) */
public function montaRecuperaFonteRecursosDotacao()
{
    $stSql = "
              select despesa.exercicio
                   , '9' as tipo_dotacao
                   , despesa.num_unidade
                   , substr(despesa.num_pao::VARCHAR, 1, 2) as tipo_acao
                   , num_pao
                   , substr(conta_despesa.cod_estrutural, 1, 1) as categoria_economica
                   , substr(conta_despesa.cod_estrutural, 3, 1) as grupo_natureza_despesa
                   , substr(conta_despesa.cod_estrutural, 5, 2) as modalidade_aplicacao
                   , substr(conta_despesa.cod_estrutural, 8, 2) as elemento
                   , recurso.cod_fonte
                   , receita.vl_original
                from orcamento.despesa
                join orcamento.conta_despesa
                  on conta_despesa.exercicio = despesa.exercicio
                 and conta_despesa.cod_conta = despesa.cod_conta
                join orcamento.recurso as recurso
                  on recurso.exercicio = despesa.exercicio
                 and recurso.cod_recurso= despesa.cod_recurso
                join orcamento.receita
                  on receita.exercicio = recurso.exercicio
                 and receita.cod_recurso = recurso.cod_recurso
               where despesa.exercicio = '".$this->getDado('exercicio')."'
                 and despesa.cod_entidade in ( ".$this->getDado('cod_entidade')." )
             ";

    return $stSql;

}

public function listaDespesa(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaListaDespesa",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

public function montaListaDespesa()
{
    $stSql = "select despesa.cod_despesa
                   , trim(conta_despesa.descricao) as descricao
                   , conta_despesa.cod_estrutural as mascara_classificacao
                   , recurso.cod_recurso
                   , recurso.cod_fonte ";
    if($this->getDado('exercicio'))
        $stSql .= ", recurso.cod_recurso ";
    $stSql .= " from orcamento.despesa
                join orcamento.conta_despesa
                  on ( despesa.exercicio = conta_despesa.exercicio
                 and   despesa.cod_conta = conta_despesa.cod_conta ) ";
    if ($this->getDado('exercicio')) {
        $stSql .= "
                JOIN orcamento.recurso('".$this->getDado('exercicio')."') as recurso
                  on ( despesa.cod_recurso = recurso.cod_recurso
                 and   despesa.exercicio   = recurso.exercicio ) ";
    }

    return $stSql;
}

public function listaDespesaAcao(&$rsRecordSet, $stFiltro = '', $stOrder = '', $boTransacao = '')
{
    return $this->executaRecupera('montaListaDespesaAcao', $rsRecordSet, $stFiltro, $stOrder, $boTransacao);
}

public function montaListaDespesaAcao()
{
    $stSql = "
        SELECT despesa.cod_despesa
             , despesa.cod_entidade
             , trim(conta_despesa.descricao) AS descricao
             , conta_despesa.cod_estrutural AS mascara_classificacao
             , recurso.cod_recurso
             , recurso.cod_fonte
             , recurso.cod_recurso
             , recurso.nom_recurso
             , ppa.cod_ppa
             , despesa_acao.cod_acao
             , acao.num_acao
             , CAST(despesa_acao.exercicio_despesa AS INTEGER) - CAST(ppa.ano_inicio AS INTEGER) + 1 AS ano
          FROM orcamento.despesa
          JOIN orcamento.conta_despesa
            ON despesa.exercicio = conta_despesa.exercicio
           AND despesa.cod_conta = conta_despesa.cod_conta
          JOIN orcamento.recurso('".$this->getDado('exercicio')."') AS recurso
            ON despesa.cod_recurso = recurso.cod_recurso
           AND despesa.exercicio   = recurso.exercicio
          JOIN orcamento.despesa_acao
            ON despesa_acao.cod_despesa       = despesa.cod_despesa
           AND despesa_acao.exercicio_despesa = despesa.exercicio
          JOIN ppa.acao
            ON acao.cod_acao = despesa_acao.cod_acao
          JOIN ppa.programa
            ON programa.cod_programa = acao.cod_programa
          JOIN ppa.programa_setorial
            ON programa_setorial.cod_setorial = programa.cod_setorial
          JOIN ppa.macro_objetivo
            ON macro_objetivo.cod_macro = programa_setorial.cod_macro
          JOIN ppa.ppa
            ON ppa.cod_ppa = macro_objetivo.cod_ppa
    ";

    return $stSql;
}

public function recuperaConfiguracaoLancamentoDespesa(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " GROUP BY CD.cod_estrutural
        , CD.descricao
        , o.exercicio
        , o.cod_conta ";
    $stSql = $this->montaRecuperaConfiguracaoLancamentoDespesa().$stCondicao.$stGroup.$stOrdem;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaConfiguracaoLancamentoDespesa()
{
    $stQuebra = "\n";
    $stSql  = "  SELECT                                                             \n";
    $stSql .= "      CD.cod_estrutural as mascara_classificacao                     \n";
    $stSql .= "     ,CD.descricao                                                   \n";
    $stSql .= "     , o.exercicio                                                   \n";
    $stSql .= "     , o.cod_conta                                                   \n";
    $stSql .= "  FROM                                                               \n";
    $stSql .= "      orcamento.despesa        AS O,                                  \n";
    $stSql .= "      orcamento.conta_despesa  AS CD                                 \n";
    $stSql .= "  WHERE                                                              \n";
    $stSql .= "          CD.exercicio IS NOT NULL                                   \n";
    $stSql .= "      AND O.cod_conta     = CD.cod_conta                             \n";
    $stSql .= "      AND O.exercicio     = CD.exercicio                             \n";

    return $stSql;
}

public function recuperaConfiguracaoLancamentoDespesaDetalhado(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaConfiguracaoLancamentoDespesaDetalhado($stCondicao).$stGroup.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaConfiguracaoLancamentoDespesaDetalhado($stFiltro)
{
    $stSql = "     SELECT orcamento.fn_consulta_class_despesa( tabela.cod_conta
                                                  , tabela.exercicio::character varying
                                                  , ( SELECT configuracao.valor
                                                        FROM administracao.configuracao
                                                       WHERE configuracao.cod_modulo = 8
                                                         AND configuracao.parametro::text = 'masc_class_despesa'::text
                                                         AND configuracao.exercicio = tabela.exercicio
                                                     )::character varying
                                                   ) AS mascara_classificacao
             , tabela.descricao
             , tabela.exercicio
             , tabela.cod_conta
          FROM (
                   SELECT conta_despesa.cod_estrutural
                        , conta_despesa.descricao
                        , conta_despesa.exercicio
                        , conta_despesa.cod_conta
                     FROM orcamento.conta_despesa
                LEFT JOIN orcamento.despesa
                       ON despesa.exercicio = conta_despesa.exercicio
                      AND despesa.cod_conta = conta_despesa.cod_conta
                    WHERE 1 = 1
                    ".$stFiltro."
                 GROUP BY conta_despesa.cod_estrutural
                        , conta_despesa.descricao
                        , conta_despesa.exercicio
                        , conta_despesa.cod_conta
                        , despesa.cod_despesa
               ) as tabela
                       ";

    return $stSql;
}

public function recuperaConfiguracaoLancamentoDespesaNovo(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaConfiguracaoLancamentoDespesaNovo();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    $this->setDebug($stSql);

    return $obErro;
}

public function montaRecuperaConfiguracaoLancamentoDespesaNovo()
{
    $stSql ="  SELECT tabela.mascara_classificacao
		      ,tabela.descricao
		      ,tabela.exercicio
		      ,conta_despesa.cod_conta 
		  FROM(
			SELECT mascara_classificacao,descricao, max(exercicio::INTEGER) as exercicio 
			  FROM ( SELECT conta_despesa.cod_estrutural as mascara_classificacao  
					,trim(conta_despesa.descricao) as descricao
					,max(despesa.exercicio::INTEGER) as exercicio
				   FROM orcamento.despesa 
			     INNER JOIN orcamento.conta_despesa 
				     ON despesa.exercicio = conta_despesa.exercicio
				    AND despesa.cod_conta = conta_despesa.cod_conta
			       GROUP BY conta_despesa.cod_estrutural
					,conta_despesa.descricao

				 UNION

				SELECT conta_despesa.cod_estrutural as mascara_classificacao  
				       ,trim(conta_despesa.descricao) as descricao
				       ,max(conta_despesa.exercicio::INTEGER) as exercicio
				  FROM orcamento.conta_despesa  
				  JOIN empenho.restos_pre_empenho 
				    ON REPLACE(conta_despesa.cod_estrutural,'.','') = restos_pre_empenho.cod_estrutural      
		             LEFT JOIN orcamento.despesa
		                    ON despesa.cod_conta = conta_despesa.cod_conta
		                   AND despesa.exercicio = conta_despesa.exercicio
		                 
		             LEFT JOIN ( 
					SELECT publico.fn_mascarareduzida(conta_despesa.cod_estrutural) AS cod_estrutural
					,max(despesa.exercicio::INTEGER) as exercicio
				   FROM orcamento.despesa 
			     INNER JOIN orcamento.conta_despesa 
				     ON despesa.exercicio = conta_despesa.exercicio
				    AND despesa.cod_conta = conta_despesa.cod_conta
			       GROUP BY conta_despesa.cod_estrutural 
				      ) AS tabela_pai
			             ON conta_despesa.cod_estrutural like tabela_pai.cod_estrutural||'%' 
			       WHERE tabela_pai.cod_estrutural IS NULL
			         AND despesa.cod_conta IS NULL
			    GROUP BY conta_despesa.cod_estrutural
				    ,conta_despesa.descricao 
		            ) AS max_exercicio
		     GROUP BY mascara_classificacao
		             ,descricao
		     ORDER BY mascara_classificacao
	   ) as tabela
              JOIN orcamento.conta_despesa
                ON conta_despesa.cod_estrutural = tabela.mascara_classificacao
               AND conta_despesa.exercicio::INTEGER = tabela.exercicio         
          ORDER BY tabela.mascara_classificacao  ";
    return $stSql;
}


public function recuperaConfiguracaoLancamentoDespesaDetalhadoNovo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaConfiguracaoLancamentoDespesaDetalhadoNovo($stCondicao).$stGroup.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

public function montaRecuperaConfiguracaoLancamentoDespesaDetalhadoNovo($stFiltro)
{
    $stSql = "  SELECT orcamento.fn_consulta_class_despesa( tabela.cod_conta
                                                  , max(tabela.exercicio)::character varying
                                                  , ( SELECT configuracao.valor
                                                        FROM administracao.configuracao
                                                       WHERE configuracao.cod_modulo = 8
                                                         AND configuracao.parametro::text = 'masc_class_despesa'::text
                                                         AND configuracao.exercicio = tabela.exercicio
                                                     )::character varying
                                                   ) AS mascara_classificacao
             , tabela.descricao
             , max(tabela.exercicio) as exercicio
             , tabela.cod_conta
          FROM (
                   SELECT conta_despesa.cod_estrutural
                        , conta_despesa.descricao
                        , max(conta_despesa.exercicio) as exercicio
                        , conta_despesa.cod_conta
                     FROM orcamento.conta_despesa
                LEFT JOIN orcamento.despesa
                       ON despesa.exercicio = conta_despesa.exercicio
                      AND despesa.cod_conta = conta_despesa.cod_conta
               LEFT JOIN empenho.restos_pre_empenho 
                 ON replace(conta_despesa.cod_estrutural,'.','') = restos_pre_empenho.cod_estrutural
                    WHERE 1 = 1
                    AND (
                        SELECT count(1)
                          FROM orcamento.conta_despesa d2
                         WHERE d2.exercicio = conta_despesa.exercicio
                           AND d2.cod_estrutural like publico.fn_mascarareduzida(conta_despesa.cod_estrutural)||'%'
                      ) = 1  
                    ".$stFiltro."
                 GROUP BY conta_despesa.cod_estrutural
                        , conta_despesa.descricao
                        , conta_despesa.exercicio
                        , conta_despesa.cod_conta
                        , despesa.cod_despesa
               ) as tabela
	 GROUP BY tabela.cod_conta
                 ,tabela.exercicio
                 ,tabela.descricao  
                 ,tabela.exercicio
                 ,tabela.cod_estrutural ";

    return $stSql;
}

}

?>
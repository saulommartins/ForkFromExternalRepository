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
    * Classe de mapeamento da tabela EMPENHO.RESPONSAVEL_ADIANTAMENTO
    * Data de Criação: 17/10/2006

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-02.03.32
*/

/*
$Log$
Revision 1.9  2007/09/06 21:05:27  luciano
Ticket#9094#

Revision 1.8  2007/07/09 21:02:59  luciano
Bug#9093#

Revision 1.7  2007/06/27 20:23:42  luciano
Bug#9093#

Revision 1.6  2007/06/20 15:38:45  luciano
Bug#9104#

Revision 1.5  2007/06/19 22:12:20  luciano
Bug#9104#

Revision 1.4  2007/05/18 14:42:44  luciano
Bug#9108#

Revision 1.3  2007/05/17 13:44:48  luciano
Bug#9093#

Revision 1.2  2007/05/03 20:24:08  luciano
Bug#9094#

Revision 1.1  2006/10/24 11:01:49  rodrigo
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TEmpenhoResponsavelAdiantamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
 function TEmpenhoResponsavelAdiantamento()
 {
     parent::Persistente();
     $this->setTabela('empenho.responsavel_adiantamento');

     $this->setCampoCod('conta_contrapartida');
     $this->setComplementoChave('exercicio, numcgm');

     $this->AddCampo('exercicio','char',true,'4',false,false);
     $this->AddCampo('numcgm','integer',true,'',true,false);
     $this->AddCampo('conta_contrapartida','integer',true,'',true,false);
     $this->AddCampo('conta_lancamento','integer',true,'',true,false);
     $this->AddCampo('ativo','boolean',true,'',true,true);

 }

 function recuperaResponsavelAdiantamento(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
 {
     $obErro      = new Erro;
     $obConexao   = new Conexao;
     $rsRecordSet = new RecordSet;
     $stSql = $this->montaRecuperaResponsavelAdiantamento().$stCondicao;
     $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

     return $obErro;
 }

 function montaRecuperaResponsavelAdiantamento()
 {
   $stSQL = "SELECT responsavel_adiantamento.exercicio                                                              \n";
   $stSQL.= "      ,responsavel_adiantamento.numcgm                                                                 \n";
   $stSQL.= "      ,responsavel_adiantamento.conta_contrapartida                                                    \n";
   $stSQL.= "      ,responsavel_adiantamento.conta_lancamento                                                       \n";
   $stSQL.= "      ,responsavel_adiantamento.ativo                                                                  \n";
   $stSQL.= "      ,sw_cgm.nom_cgm                                                                                  \n";
   $stSQL.= "  FROM empenho.responsavel_adiantamento                                                                \n";
   $stSQL.= "      ,sw_cgm                                                                                          \n";
   $stSQL.= " WHERE responsavel_adiantamento.numcgm = sw_cgm.numcgm                                                 \n";

   return $stSQL;
 }

    public function verificaExistenciaEmpenho(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaVerificaExistenciaEmpenho().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaVerificaExistenciaEmpenho()
    {
        $stSql  = "";
        $stSql .= " SELECT                                                              \n";
        $stSql .= "      epe.cgm_beneficiario                                           \n";
        $stSql .= "     ,ee.cod_empenho                                                 \n";
        $stSql .= "     ,null as cod_autorizacao                                        \n";
        $stSql .= " FROM                                                                \n";
        $stSql .= "     empenho.pre_empenho as epe                                      \n";
        $stSql .= "     LEFT JOIN                                                       \n";
        $stSql .= "         empenho.empenho as ee ON (                                  \n";
        $stSql .= "                 epe.cod_pre_empenho = ee.cod_pre_empenho            \n";
        $stSql .= "             AND epe.exercicio       = ee.exercicio                  \n";
        $stSql .= "         )                                                           \n";
        $stSql .= "     LEFT JOIN                                                       \n";
        $stSql .= "         empenho.contrapartida_empenho as ece ON (                   \n";
        $stSql .= "                 ee.cod_empenho      = ece.cod_empenho               \n";
        $stSql .= "             AND ee.cod_entidade     = ece.cod_entidade              \n";
        $stSql .= "             AND ee.exercicio        = ece.exercicio                 \n";
        $stSql .= "         )                                                           \n";
        $stSql .= " WHERE                                                               \n";
        $stSql .= "         ee.cod_categoria IN (2,3)                                   \n";
        $stSql .= " AND epe.exercicio = '".$this->getDado('exercicio')."'               \n";
        $stSql .= " AND epe.cgm_beneficiario = ".$this->getDado('numcgm')."             \n";
        $stSql .= " AND ece.conta_contrapartida = ".$this->getDado('conta_contrapartida')." \n";
        $stSql .= "                                                                     \n";
        $stSql .= " UNION                                                               \n";
        $stSql .= "                                                                     \n";
        $stSql .= " SELECT                                                              \n";
        $stSql .= "      epe.cgm_beneficiario                                           \n";
        $stSql .= "     ,null as cod_empenho                                            \n";
        $stSql .= "     ,eae.cod_autorizacao                                            \n";
        $stSql .= " FROM                                                                \n";
        $stSql .= "     empenho.pre_empenho as epe                                      \n";
        $stSql .= "     LEFT JOIN                                                       \n";
        $stSql .= "         empenho.autorizacao_empenho as eae ON (                     \n";
        $stSql .= "                 epe.cod_pre_empenho = eae.cod_pre_empenho           \n";
        $stSql .= "             AND epe.exercicio       = eae.exercicio                 \n";
        $stSql .= "         )                                                           \n";
        $stSql .= "     LEFT JOIN                                                       \n";
        $stSql .= "         empenho.contrapartida_autorizacao as eca ON (               \n";
        $stSql .= "                 eae.cod_autorizacao  = eca.cod_autorizacao          \n";
        $stSql .= "             AND eae.cod_entidade     = eca.cod_entidade             \n";
        $stSql .= "             AND eae.exercicio        = eca.exercicio                \n";
        $stSql .= "         )                                                           \n";
        $stSql .= " WHERE                                                               \n";
        $stSql .= "         eae.cod_categoria IN (2,3)                                  \n";
        $stSql .= " AND epe.exercicio = '".$this->getDado('exercicio')."'                 \n";
        $stSql .= " AND epe.cgm_beneficiario = ".$this->getDado('numcgm')."             \n";
        $stSql .= " AND eca.conta_contrapartida = ".$this->getDado('conta_contrapartida')." \n";

        return $stSql;
    }

    public function consultaEmpenhosFornecedor(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaConsultaEmpenhosFornecedor().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    public function montaConsultaEmpenhosFornecedor()
    {
        $stSql  = "															                                                            \n";
        $stSql .= "        SELECT                                                                                                       \n";
        $stSql .= "               tabela.cod_entidade                                                                                   \n";
        $stSql .= "              ,tabela.cod_empenho                                                                                    \n";
        $stSql .= "              ,tabela.exercicio                                                                                      \n";
        $stSql .= "              ,tabela.credor                                                                                         \n";
        $stSql .= "              ,TO_CHAR(TO_DATE(pagamento.data,'yyyy-mm-dd'),'dd/mm/yyyy') as dt_pagamento                            \n";
        $stSql .= "              ,TO_CHAR(pagamento.data ::date + tabela.prazo,'dd/mm/yyyy') as dt_prazo_prestacao                      \n";
        $stSql .= "              ,coalesce(tabela.vl_pago-tabela.vl_pago_anulado,0.00) as vl_pago                                       \n";
        $stSql .= "              ,coalesce(itens.vl_prestado,0.00) as vl_prestado                                                       \n";
        $stSql .= "        FROM (                                                                                                       \n";
        $stSql .= "                SELECT                                                                                               \n";
        $stSql .= "                      EE.cod_entidade                                                                                \n";
        $stSql .= "                     ,EE.cod_empenho                                                                                 \n";
        $stSql .= "                     ,PE.exercicio                                                                                   \n";
        $stSql .= "                     ,ECE.conta_contrapartida                                                                        \n";
        $stSql .= "                     ,ECR.prazo                                                                                      \n";
        $stSql .= "                     ,PE.cgm_beneficiario as credor                                                                  \n";
        $stSql .= "                     ,empenho.fn_consultar_valor_empenhado(                                                          \n";
        $stSql .= "                                                            EE.exercicio                                             \n";
        $stSql .= "                                                           ,EE.cod_empenho                                           \n";
        $stSql .= "                                                           ,EE.cod_entidade                                          \n";
        $stSql .= "                     ) AS vl_empenhado                                                                               \n";
        $stSql .= "                     ,empenho.fn_consultar_valor_empenhado_anulado(                                                  \n";
        $stSql .= "                                                            EE.exercicio                                             \n";
        $stSql .= "                                                           ,EE.cod_empenho                                           \n";
        $stSql .= "                                                           ,EE.cod_entidade                                          \n";
        $stSql .= "                     ) AS vl_empenhado_anulado                                                                       \n";
        $stSql .= "                     ,empenho.fn_consultar_valor_empenhado_pago(                                                     \n";
        $stSql .= "                                                            EE.exercicio                                             \n";
        $stSql .= "                                                           ,EE.cod_empenho                                           \n";
        $stSql .= "                                                           ,EE.cod_entidade                                          \n";
        $stSql .= "                      ) AS vl_pago                                                                                   \n";
        $stSql .= "                     ,empenho.fn_consultar_valor_empenhado_pago_anulado(                                             \n";
        $stSql .= "                                                            PE.exercicio                                             \n";
        $stSql .= "                                                           ,EE.cod_empenho                                           \n";
        $stSql .= "                                                           ,EE.cod_entidade                                          \n";
        $stSql .= "                      ) AS vl_pago_anulado                                                                           \n";
        $stSql .= "                FROM                                                                                                 \n";
        $stSql .= "                      empenho.empenho          AS EE                                                                 \n";
        $stSql .= "                      JOIN empenho.pre_empenho AS PE                                                                 \n";
        $stSql .= "                      ON (                                                                                           \n";
        $stSql .= "                               EE.exercicio       = PE.exercicio                                                     \n";
        $stSql .= "                           AND EE.cod_pre_empenho = PE.cod_pre_empenho                                               \n";
        $stSql .= "                      )                                                                                              \n";
        $stSql .= "                      LEFT JOIN empenho.contrapartida_empenho as ECE                                                 \n";
        $stSql .= "                      ON (                                                                                           \n";
        $stSql .= "                               EE.exercicio    = ECE.exercicio                                                       \n";
        $stSql .= "                           AND EE.cod_entidade = ECE.cod_entidade                                                    \n";
        $stSql .= "                           AND EE.cod_empenho  = ECE.cod_empenho                                                     \n";
        $stSql .= "                      )                                                                                              \n";
        $stSql .= "                      LEFT JOIN empenho.contrapartida_responsavel as ECR                                             \n";
        $stSql .= "                      ON (                                                                                           \n";
        $stSql .= "                               ECE.exercicio           = ECR.exercicio                                               \n";
        $stSql .= "                            AND ECE.conta_contrapartida = ECR.conta_contrapartida                                    \n";
        $stSql .= "                      )                                                                                              \n";
        $stSql .= "                WHERE                                                                                                \n";
        $stSql .= "                         EE.exercicio = '".$this->getDado('exercicio')."'                                            \n";
        $stSql .= "                   AND ( EE.cod_categoria = 2 OR EE.cod_categoria = 3 )                                              \n";
        $stSql .= "        ) AS tabela                                                                                                  \n";
        $stSql .= "        LEFT JOIN                                                                                                    \n";
        $stSql .= "        (                                                                                                            \n";
        $stSql .= "            SELECT                                                                                                   \n";
        $stSql .= "                 exercicio                                                                                           \n";
        $stSql .= "                ,cod_empenho                                                                                         \n";
        $stSql .= "                ,cod_entidade                                                                                        \n";
        $stSql .= "                ,TO_CHAR(MAX(timestamp),'yyyy-mm-dd') as data                                                        \n";
        $stSql .= "            FROM (                                                                                                   \n";
        $stSql .= "                 SELECT                                                                                              \n";
        $stSql .= "                      enl.cod_empenho                                                                                \n";
        $stSql .= "                     ,enl.cod_entidade                                                                               \n";
        $stSql .= "                     ,enl.exercicio_empenho as exercicio                                                             \n";
        $stSql .= "                     ,enlp.timestamp as timestamp                                                                    \n";
        $stSql .= "                     ,enlp.cod_nota                                                                                  \n";
        $stSql .= "                     ,coalesce(sum(enlp.vl_pago),0.00) as vl_pago                                                    \n";
        $stSql .= "                     ,0.00 as vl_anulado                                                                             \n";
        $stSql .= "                 FROM                                                                                                \n";
        $stSql .= "                     empenho.nota_liquidacao as enl                                                                  \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_paga as enlp                                                  \n";
        $stSql .= "                     ON (    enlp.exercicio      = enl.exercicio                                                     \n";
        $stSql .= "                         AND enlp.cod_entidade   = enl.cod_entidade                                                  \n";
        $stSql .= "                         AND enlp.cod_nota       = enl.cod_nota                                                      \n";
        $stSql .= "                        )                                                                                            \n";
        $stSql .= "                     GROUP BY enlp.cod_nota,enlp.timestamp,enl.cod_empenho,enl.cod_entidade,enl.exercicio_empenho    \n";
        $stSql .= "                                                                                                                     \n";
        $stSql .= "                 UNION                                                                                               \n";
        $stSql .= "                                                                                                                     \n";
        $stSql .= "                 SELECT                                                                                              \n";
        $stSql .= "                      enl.cod_empenho                                                                                \n";
        $stSql .= "                     ,enl.cod_entidade                                                                               \n";
        $stSql .= "                     ,enl.exercicio_empenho as exercicio                                                             \n";
        $stSql .= "                     ,enlp.timestamp as timestamp                                                                    \n";
        $stSql .= "                     ,enlp.cod_nota                                                                                  \n";
        $stSql .= "                     ,0.00 as vl_pago                                                                                \n";
        $stSql .= "                     ,coalesce(sum(enlpa.vl_anulado),0.00) as vl_anulado                                             \n";
        $stSql .= "                FROM                                                                                                 \n";
        $stSql .= "                     empenho.nota_liquidacao as enl                                                                  \n";
        $stSql .= "                     LEFT JOIN empenho.nota_liquidacao_paga as enlp                                                  \n";
        $stSql .= "                     ON (    enlp.exercicio      = enl.exercicio                                                     \n";
        $stSql .= "                         AND enlp.cod_entidade   = enl.cod_entidade                                                  \n";
        $stSql .= "                         AND enlp.cod_nota       = enl.cod_nota                                                      \n";
        $stSql .= "                        )                                                                                            \n";
        $stSql .= "                    LEFT JOIN empenho.nota_liquidacao_paga_anulada as enlpa                                          \n";
        $stSql .= "                    ON (    enlpa.exercicio      = enlp.exercicio                                                    \n";
        $stSql .= "                        AND enlpa.cod_entidade   = enlp.cod_entidade                                                 \n";
        $stSql .= "                        AND enlpa.cod_nota       = enlp.cod_nota                                                     \n";
        $stSql .= "                        AND enlpa.timestamp      = enlp.timestamp                                                    \n";
        $stSql .= "                       )                                                                                             \n";
        $stSql .= "                    GROUP BY enlp.cod_nota,enlp.timestamp,enl.cod_empenho,enl.cod_entidade,enl.exercicio_empenho     \n";
        $stSql .= "              ) as enlp                                                                                              \n";
        $stSql .= "              GROUP BY  exercicio,cod_empenho,cod_entidade                                                           \n";
        $stSql .= "          ) AS pagamento ON (                                                                                        \n";
        $stSql .= "                     pagamento.cod_empenho  = tabela.cod_empenho                                                     \n";
        $stSql .= "                 AND pagamento.exercicio    = tabela.exercicio                                                       \n";
        $stSql .= "                 AND pagamento.cod_entidade = tabela.cod_entidade                                                    \n";
        $stSql .= "          )                                                                                                          \n";
        $stSql .= "          LEFT JOIN                                                                                                  \n";
        $stSql .= "          (                                                                                                          \n";
        $stSql .= "            SELECT                                                                                                   \n";
        $stSql .= "                 data                                                                                                \n";
        $stSql .= "                ,cod_empenho                                                                                         \n";
        $stSql .= "                ,exercicio                                                                                           \n";
        $stSql .= "                ,cod_entidade                                                                                        \n";
        $stSql .= "            FROM                                                                                                     \n";
        $stSql .= "                empenho.prestacao_contas                                                                             \n";
        $stSql .= "          ) AS prestacaocontas ON (                                                                                  \n";
        $stSql .= "                  prestacaocontas.cod_empenho  = tabela.cod_empenho                                                  \n";
        $stSql .= "              AND prestacaocontas.exercicio    = tabela.exercicio                                                    \n";
        $stSql .= "              AND prestacaocontas.cod_entidade = tabela.cod_entidade                                                 \n";
        $stSql .= "          )                                                                                                          \n";
        $stSql .= "                                                                                                                     \n";
        $stSql .= "          LEFT JOIN                                                                                                  \n";
        $stSql .= "           (                                                                                                         \n";
        $stSql .= "        SELECT tbl.cod_empenho                                            \n";
        $stSql .= "             , tbl.cod_entidade                                           \n";
        $stSql .= "             , tbl.exercicio                                              \n";
        $stSql .= "             , COALESCE(sum(tbl.vl_prestado), 0.00) as vl_prestado FROM ( \n";
        $stSql .= "            ( SELECT cod_empenho                                          \n";
        $stSql .= "                   , cod_entidade                                         \n";
        $stSql .= "                   , exercicio                                            \n";
        $stSql .= "                   , coalesce(SUM(valor_item),0.00) as vl_prestado        \n";
        $stSql .= "                FROM empenho.item_prestacao_contas as eipc                \n";
        $stSql .= "               WHERE NOT EXISTS ( SELECT num_item                         \n";
        $stSql .= "                            FROM empenho.item_prestacao_contas_anulado    \n";
        $stSql .= "                           WHERE cod_empenho   = eipc.cod_empenho         \n";
        $stSql .= "                             AND exercicio     = eipc.exercicio           \n";
        $stSql .= "                             AND cod_entidade  = eipc.cod_entidade        \n";
        $stSql .= "                             AND num_item      = eipc.num_item            \n";
        $stSql .= "                        )                                                 \n";
        $stSql .= "            GROUP BY cod_empenho                                          \n";
        $stSql .= "                   , exercicio                                            \n";
        $stSql .= "                   , cod_entidade                                         \n";
        $stSql .= "            )                                                             \n";
        $stSql .= "                                                                          \n";
        $stSql .= "            UNION ALL                                                     \n";
        $stSql .= "                                                                          \n";
        $stSql .= "            ( SELECT prestacao_contas.cod_empenho                         \n";
        $stSql .= "                   , prestacao_contas.cod_entidade                        \n";
        $stSql .= "                   , prestacao_contas.exercicio                           \n";
        $stSql .= "                   , SUM(COALESCE(nota_liquidacao_paga.vl_pago, 0.00)) -  \n";
        $stSql .= "                     SUM(COALESCE(nota_liquidacao_paga_anulada.vl_anulado, 0.00)) AS vl_prestado \n";
        $stSql .= "                FROM empenho.prestacao_contas                             \n";
        $stSql .= "                JOIN empenho.empenho                                      \n";
        $stSql .= "                  ON empenho.cod_empenho = prestacao_contas.cod_empenho   \n";
        $stSql .= "                 AND empenho.exercicio = prestacao_contas.exercicio       \n";
        $stSql .= "                 AND empenho.cod_entidade = prestacao_contas.cod_entidade \n";
        $stSql .= "                JOIN empenho.nota_liquidacao                              \n";
        $stSql .= "                  ON nota_liquidacao.cod_empenho = empenho.cod_empenho    \n";
        $stSql .= "                 AND nota_liquidacao.exercicio = empenho.exercicio        \n";
        $stSql .= "                 AND nota_liquidacao.cod_entidade = empenho.cod_entidade  \n";
        $stSql .= "                JOIN empenho.nota_liquidacao_paga                         \n";
        $stSql .= "                  ON nota_liquidacao_paga.cod_nota = nota_liquidacao.cod_nota \n";
        $stSql .= "                 AND nota_liquidacao_paga.exercicio = nota_liquidacao.exercicio \n";
        $stSql .= "                 AND nota_liquidacao_paga.cod_entidade = nota_liquidacao.cod_entidade \n";
        $stSql .= "           LEFT JOIN empenho.nota_liquidacao_paga_anulada                 \n";
        $stSql .= "                  ON nota_liquidacao_paga_anulada.cod_nota = nota_liquidacao_paga.cod_nota \n";
        $stSql .= "                 AND nota_liquidacao_paga_anulada.exercicio = nota_liquidacao_paga.exercicio \n";
        $stSql .= "                 AND nota_liquidacao_paga_anulada.cod_entidade = nota_liquidacao_paga.cod_entidade \n";
        $stSql .= "                 AND nota_liquidacao_paga_anulada.timestamp = nota_liquidacao_paga.timestamp \n";
        $stSql .= "    WHERE NOT EXISTS ( SELECT cod_empenho                                 \n";
        $stSql .= "                            , cod_entidade                                \n";
        $stSql .= "                            , exercicio                                   \n";
        $stSql .= "                            , coalesce(SUM(valor_item),0.00) as vl_prestado \n";
        $stSql .= "                         FROM empenho.item_prestacao_contas as eipc       \n";
        $stSql .= "                        WHERE NOT EXISTS ( SELECT num_item  as eipc       \n";
        $stSql .= "                                             FROM empenho.item_prestacao_contas_anulado \n";
        $stSql .= "                                            WHERE cod_empenho = eipc.cod_empenho \n";
        $stSql .= "                                              AND exercicio    = eipc.exercicio \n";
        $stSql .= "                                              AND cod_entidade = eipc.cod_entidade \n";
        $stSql .= "                                              AND num_item     = eipc.num_item \n";
        $stSql .= "                                         )                                \n";
        $stSql .= "                          AND cod_empenho = prestacao_contas.cod_empenho  \n";
        $stSql .= "                          AND cod_entidade = prestacao_contas.cod_entidade \n";
        $stSql .= "                          AND exercicio = prestacao_contas.exercicio      \n";
        $stSql .= "                     GROUP BY cod_empenho                                 \n";
        $stSql .= "                            , exercicio                                   \n";
        $stSql .= "                            , cod_entidade                                \n";
        $stSql .= "                     )                                                    \n";
        $stSql .= "            GROUP BY prestacao_contas.cod_empenho                         \n";
        $stSql .= "                   , prestacao_contas.cod_entidade                        \n";
        $stSql .= "                   , prestacao_contas.exercicio                           \n";
        $stSql .= "                   , prestacao_contas.data                                \n";
        $stSql .= "            ORDER BY prestacao_contas.cod_empenho                         \n";
        $stSql .= "                   , prestacao_contas.data                                \n";
        $stSql .= "                   , prestacao_contas.cod_entidade                        \n";
        $stSql .= "                   , prestacao_contas.exercicio                           \n";
        $stSql .= "            )                                                             \n";
        $stSql .= "        ) as tbl                                                          \n";
        $stSql .= " GROUP BY tbl.cod_empenho                                                 \n";
        $stSql .= "        , tbl.cod_entidade                                                \n";
        $stSql .= "        , tbl.exercicio                                                   \n";
        $stSql .= "          ) AS itens ON (                                                                                            \n";
        $stSql .= "                             itens.cod_empenho  = tabela.cod_empenho                                                 \n";
        $stSql .= "                         AND itens.exercicio    = tabela.exercicio                                                   \n";
        $stSql .= "                         AND itens.cod_entidade = tabela.cod_entidade                                                \n";
        $stSql .= "                        )                                                                                            \n";
        $stSql .= "         WHERE                                                                                                       \n";
        $stSql .= "                  tabela.exercicio = '".$this->getDado('exercicio')."'                                               \n";
        $stSql .= "              AND tabela.vl_pago > 0.00                                                                              \n";
        $stSql .= "              AND tabela.vl_pago-tabela.vl_pago_anulado > coalesce(itens.vl_prestado,0.00)                           \n";
        $stSql .= "              AND tabela.credor = ".$this->getDado('numcgm')."                                                       \n";
        if($this->getDado('conta_contrapartida'))
        $stSql .= "              AND tabela.conta_contrapartida = ".$this->getDado('conta_contrapartida')."                             \n";
        $stSql .= "        GROUP BY                                                                                                     \n";
        $stSql .= "              tabela.cod_entidade                                                                                    \n";
        $stSql .= "             ,tabela.cod_empenho                                                                                     \n";
        $stSql .= "             ,tabela.exercicio                                                                                       \n";
        $stSql .= "             ,tabela.credor                                                                                          \n";
        $stSql .= "             ,tabela.prazo                                                                                           \n";
        $stSql .= "             ,tabela.vl_pago                                                                                         \n";
        $stSql .= "             ,tabela.vl_pago_anulado                                                                                 \n";
        $stSql .= "             ,itens.vl_prestado                                                                                      \n";
        $stSql .= "             ,pagamento.data                                                                                         \n";
        $stSql .= "       ORDER BY tabela.cod_entidade, tabela.cod_empenho                                                              \n";

        return $stSql;
    }

    public function recuperaContrapartidaLancamento(&$rsRecordSet, $stCondicao = "", $boTransacao = "")
    {
         $obErro      = new Erro;
         $obConexao   = new Conexao;
         $rsRecordSet = new RecordSet;
         $stSql = $this->montaRecuperaContrapartidaLancamento().$stCondicao;
         $this->setDebug( $stSql );
         $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

         return $obErro;
    }

    public function montaRecuperaContrapartidaLancamento()
    {
        $stSQL  = " SELECT  era.conta_contrapartida                                                                      \n";
        $stSQL .= "        ,cpc.nom_conta                                                                                \n";
        $stSQL .= " FROM  empenho.responsavel_adiantamento as era                                                        \n";
        $stSQL .= "     JOIN contabilidade.plano_analitica as cpa ON (                                                   \n";
        $stSQL .= "             era.exercicio           = cpa.exercicio                                                  \n";
        $stSQL .= "         AND era.conta_contrapartida = cpa.cod_plano                                                  \n";
        $stSQL .= "     )                                                                                                \n";
        $stSQL .= "     JOIN contabilidade.plano_conta as cpc ON (                                                       \n";
        $stSQL .= "             cpa.exercicio = cpc.exercicio                                                            \n";
        $stSQL .= "         AND cpa.cod_conta = cpc.cod_conta                                                            \n";
        $stSQL .= "     )                                                                                                \n";
        $stSQL .= " WHERE  ativo = true                                                                                  \n";
        if($this->getDado('exercicio'))
            $stSQL .= "    AND era.exercicio = '".$this->getDado('exercicio')."'                                         \n";
        if($this->getDado('numcgm'))
            $stSQL .= "    AND era.numcgm = ".$this->getDado('numcgm')."                                                 \n";

        return $stSQL;
    }

}

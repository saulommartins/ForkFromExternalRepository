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
    * Classe de mapeamento da tabela RECIBO_EXTRA
    * Data de Criação: 01/08/2006

    * @author Analista: Cleisson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 31732 $
    $Name$
    $Author: cako $
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.30, uc-02.04.26, uc-02.04.27, uc-02.04.29
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTesourariaReciboExtra extends Persistente
{
   public function TTesourariaReciboExtra()
   {
        parent::Persistente();

        $this->setTabela( 'tesouraria.recibo_extra' );
        $this->setCampoCod ( '' );
        $this->setComplementoChave ( 'cod_recibo_extra, exercicio, cod_entidade, tipo_recibo' );

        $this->addCampo('cod_recibo_extra'   , 'integer'  ,true  , ''  , true , false );
        $this->addCampo('exercicio'          , 'varchar'  ,true  , ''  , true , true );
        $this->addCampo('cod_entidade'       , 'integer'  ,true  , ''  , true , true );
        $this->addCampo('timestamp_usuario'  , 'timestamp',false , ''  , false , true );
        $this->addCampo('cgm_usuario'        , 'integer'  ,false , ''  , false , true );
        $this->addCampo('timestamp_terminal' , 'timestamp',false , ''  , false , true );
        $this->addCampo('cod_terminal'       , 'integer'  ,false , ''  , false , true );
        $this->addCampo('cod_plano'          , 'integer'  ,true  , ''  , false , true );
        $this->addCampo('historico'          , 'varchar'  ,false , ''  , false , false );
        $this->addCampo('timestamp'          , 'timestamp',true  , ''  , false , false );
        $this->addCampo('valor'              , 'numeric'  ,true  , ''  , false , false );
        $this->addCampo('tipo_recibo'        , 'varchar'  ,true  , ''  , true , false );
   }

   public function montaRecuperaRelacionamento()
   {
        $stSql  = "select  recibo_extra.cod_entidade                                                    \n  ";
        $stSql .= "        , recibo_extra.cod_recibo_extra                                              \n  ";
        $stSql .= "        , recibo_extra.exercicio                                                     \n  ";
        $stSql .= "        , to_char (recibo_extra.timestamp,'dd/mm/yyyy' ) as data                     \n  ";
        $stSql .= "        , plano_conta.cod_conta                                                      \n  ";
        $stSql .= "        , plano_conta.nom_conta                                                      \n  ";
        $stSql .= "        , recibo_extra.cod_plano         as cod_plano_despesa                        \n  ";
        $stSql .= "        , recibo_extra_banco.cod_plano   as cod_plano_banco                          \n  ";
        $stSql .= "        , recibo_extra.valor                                                         \n  ";
        $stSql .= "        , recibo_extra.tipo_recibo                                                   \n  ";
        $stSql .= "        , recibo_extra.timestamp                                                     \n  ";
        $stSql .= "        , recibo_extra_credor.numcgm as cod_credor                                   \n  ";
        $stSql .= "        , cgm_credor.nom_cgm as nom_cgm_credor                                       \n  ";
        $stSql .= "        , recurso.masc_recurso_red as cod_recurso                                    \n  ";
        $stSql .= "        , recibo_extra.historico                                                     \n  ";
        $stSql .= "        , recurso.nom_recurso                                                        \n  ";
        $stSql .= "        , recibo_extra_assinatura.nom_cgm                                            \n  ";
        $stSql .= "        , recibo_extra_assinatura.cargo                                              \n  ";
        $stSql .= "        , plano_conta.cod_estrutural                                                 \n  ";
        $stSql .= "        , to_char(timestamp, 'yyyy-mm-dd HH24:MI:SS') AS dt_emissao                  \n  ";
        $stSql .= "        , cgme.nom_cgm AS nom_prefeitura                                             \n  ";
        $stSql .= "        , sw_municipio.nom_municipio                                                 \n  ";
        $stSql .= "from tesouraria.recibo_extra                                                         \n  ";
        $stSql .= "inner join contabilidade.plano_analitica                                             \n  ";
        $stSql .= "        on(plano_analitica.cod_plano = recibo_extra.cod_plano                        \n  ";
        $stSql .= "       and plano_analitica.exercicio = recibo_extra.exercicio )                      \n  ";
        $stSql .= "       and (not recibo_extra.exercicio                                               \n  ";
        $stSql .= "             || recibo_extra.cod_entidade                                            \n  ";
        $stSql .= "             || recibo_extra.cod_recibo_extra                                        \n  ";
        $stSql .= "             || recibo_extra.tipo_recibo in (                                        \n  ";
        $stSql .= "                       select recibo_extra_anulacao.exercicio ||                     \n  ";
        $stSql .= "                              recibo_extra_anulacao.cod_entidade ||                  \n  ";
        $stSql .= "                              recibo_extra_anulacao.cod_recibo_extra ||              \n  ";
        $stSql .= "                              recibo_extra_anulacao.tipo_recibo as dado              \n  ";
        $stSql .= "                       from tesouraria.recibo_extra_anulacao)                        \n  ";
        $stSql .= "                     )                                                               \n  ";
        $stSql .= "inner join contabilidade.plano_conta                                                 \n  ";
        $stSql .= "        on(plano_conta.exercicio = plano_analitica.exercicio                         \n  ";
        $stSql .= "       and plano_conta.cod_conta = plano_analitica.cod_conta  )                      \n  ";
        $stSql .= "left join tesouraria.recibo_extra_banco                                              \n  ";
        $stSql .= "       on ( recibo_extra_banco.cod_recibo_extra =  recibo_extra.cod_recibo_extra     \n  ";
        $stSql .= "        and recibo_extra_banco.exercicio        =  recibo_extra.exercicio            \n  ";
        $stSql .= "        and recibo_extra_banco.cod_entidade     =  recibo_extra.cod_entidade         \n  ";
        $stSql .= "        and recibo_extra_banco.tipo_recibo      =  recibo_extra.tipo_recibo    )     \n  ";
        $stSql .= "left join tesouraria.recibo_extra_credor                                             \n  ";
        $stSql .= "       on ( recibo_extra_credor.cod_recibo_extra =  recibo_extra.cod_recibo_extra    \n  ";
        $stSql .= "        and recibo_extra_credor.exercicio        =  recibo_extra.exercicio           \n  ";
        $stSql .= "        and recibo_extra_credor.cod_entidade     =  recibo_extra.cod_entidade        \n  ";
        $stSql .= "        and recibo_extra_credor.tipo_recibo      =  recibo_extra.tipo_recibo    )    \n  ";
        $stSql .= "left join sw_cgm cgm_credor                                                          \n  ";
        $stSql .= "       on ( cgm_credor.numcgm = recibo_extra_credor.numcgm )                         \n  ";
        $stSql .= "left join tesouraria.recibo_extra_recurso                                            \n  ";
        $stSql .= "       on ( recibo_extra_recurso.cod_recibo_extra =  recibo_extra.cod_recibo_extra   \n  ";
        $stSql .= "        and recibo_extra_recurso.exercicio        =  recibo_extra.exercicio          \n  ";
        $stSql .= "        and recibo_extra_recurso.cod_entidade     =  recibo_extra.cod_entidade       \n  ";
        $stSql .= "        and recibo_extra_recurso.tipo_recibo      =  recibo_extra.tipo_recibo    )   \n  ";
        $stSql .= "INNER JOIN orcamento.entidade                                                        \n  ";
        $stSql .= "        ON entidade.cod_entidade = recibo_extra.cod_entidade                         \n  ";
        $stSql .= "       AND entidade.exercicio    = recibo_extra.exercicio                            \n  ";
        $stSql .= "INNER JOIN sw_cgm AS cgme                                                            \n  ";
        $stSql .= "        ON cgme.numcgm = entidade.numcgm                                             \n  ";
        $stSql .= "INNER JOIN sw_municipio                                                              \n  ";
        $stSql .= "        ON sw_municipio.cod_municipio = cgme.cod_municipio                           \n  ";
        $stSql .= "       AND sw_municipio.cod_uf        = cgme.cod_uf                                  \n  ";
        $stSql .= "left join orcamento.recurso('".$this->getDado('exercicio')."') as recurso            \n  ";
        $stSql .= "        on (recurso.exercicio   = recibo_extra_recurso.exercicio                     \n  ";
        $stSql .= "        and recurso.cod_recurso = recibo_extra_recurso.cod_recurso)                  \n  ";
        $stSql .= "LEFT JOIN ( SELECT recibo_extra_assinatura.cod_recibo_extra                          \n  ";
        $stSql .= "                 , recibo_extra_assinatura.cod_entidade                              \n  ";
        $stSql .= "                 , recibo_extra_assinatura.exercicio                                 \n  ";
        $stSql .= "                 , recibo_extra_assinatura.tipo_recibo                               \n  ";
        $stSql .= "                 , recibo_extra_assinatura.cargo                                     \n  ";
        $stSql .= "                 , sw_cgm.nom_cgm                                                    \n  ";
        $stSql .= "              FROM tesouraria.recibo_extra_assinatura                                \n  ";
        $stSql .= "        INNER JOIN sw_cgm                                                            \n  ";
        $stSql .= "                ON recibo_extra_assinatura.numcgm = sw_cgm.numcgm                    \n  ";
        $stSql .= "       ) AS recibo_extra_assinatura                                                  \n  ";
        $stSql .= "      ON recibo_extra_assinatura.cod_recibo_extra = recibo_extra.cod_recibo_extra    \n  ";
        $stSql .= "     AND recibo_extra_assinatura.cod_entidade     = recibo_extra.cod_entidade        \n  ";
        $stSql .= "     AND recibo_extra_assinatura.exercicio        = recibo_extra.exercicio           \n  ";
        $stSql .= "     AND recibo_extra_assinatura.tipo_recibo      = recibo_extra.tipo_recibo         \n  ";

        return $stSql;
   }

    public function montaRecuperaRelatorioRecibosExtra()
    {
        $stSql  ="    SELECT                                                                 \n";
        $stSql .="         re.cod_recibo_extra as recibo_extra                              \n";
        $stSql .="        ,ore.masc_recurso_red as recurso                                  \n";
        $stSql .="        ,rec.numcgm as credor                                             \n";
        $stSql .="        ,reb.cod_plano as caixa                                           \n";
        $stSql .="        ,re.cod_plano  as conta                                           \n";
        $stSql .="        ,CASE WHEN                                                        \n";
        $stSql .="            ret.cod_recibo_extra is null                                  \n";
        $stSql .="        THEN                                                              \n";
        $stSql .="            'Não'                                                         \n";
        $stSql .="        ELSE                                                              \n";
        $stSql .="            'Sim'                                                         \n";
        $stSql .="        END as autenticado                                                \n";
        $stSql .="        ,cgm.nom_cgm                                                      \n";
        $stSql .="        ,re.valor                                                         \n";
        $stSql .="        ,re.timestamp as data                                             \n";
        $stSql .="        ,re.tipo_recibo                                                   \n";
        $stSql .="    FROM                                                                  \n";
        $stSql .="        tesouraria.recibo_extra as re                                     \n";
        $stSql .="        LEFT JOIN tesouraria.recibo_extra_recurso as rer on (re.cod_recibo_extra = rer.cod_recibo_extra AND re.exercicio = rer.exercicio AND re.cod_recibo_extra = rer.cod_recibo_extra AND re.tipo_recibo = rer.tipo_recibo)\n";
        $stSql .="        LEFT JOIN orcamento.recurso('".$this->getDado('stExercicio')."') as ore
                          on ( ore.cod_recurso = rer.cod_recurso
                           AND ore.exercicio = rer.exercicio \n";
        if ($this->getDado('stDestinacaoRecurso'))
            $stSql .="        AND ore.masc_recurso_red like '".$this->getDado("stDestinacaoRecurso")."%' \n";
        if ($this->getDado('inCodDetalhamento'))
            $stSql .="        AND ore.cod_detalhamento = ".$this->getDado("inCodDetalhamento")." \n";
        $stSql .="        ) \n";
        $stSql .="        LEFT JOIN tesouraria.recibo_extra_credor as rec on (re.cod_entidade = rec.cod_entidade AND re.exercicio = rec.exercicio AND re.cod_recibo_extra = rec.cod_recibo_extra AND re.tipo_recibo = rec.tipo_recibo)\n";
        $stSql .="        LEFT JOIN tesouraria.recibo_extra_banco as reb on (re.cod_entidade = reb.cod_entidade AND re.exercicio = reb.exercicio AND re.cod_recibo_extra = reb.cod_recibo_extra AND re.tipo_recibo = reb.tipo_recibo)\n";
        $stSql .="        LEFT JOIN tesouraria.recibo_extra_anulacao as rea on (re.exercicio = rea.exercicio AND re.cod_entidade = rea.cod_entidade AND re.cod_recibo_extra = rea.cod_recibo_extra  AND re.tipo_recibo = rea.tipo_recibo )\n";
        $stSql .="        LEFT JOIN tesouraria.recibo_extra_transferencia as ret on (re.cod_recibo_extra = ret.cod_recibo_extra AND re.cod_entidade = ret.cod_entidade AND re.exercicio = ret.exercicio AND re.tipo_recibo = ret.tipo_recibo)\n";
        $stSql .="        LEFT JOIN sw_cgm as cgm on( cgm.numcgm = rec.numcgm)                                        \n";
        $stSql .="    WHERE                                                                 \n";
        $stSql .="             rea.cod_recibo_extra is null                                 \n";
        if ($this->getDado('stExercicio'))
            $stSql .="        AND re.exercicio = '".$this->getDado('stExercicio')."'        \n";
        if ($this->getDado('inCodEntidade'))
            $stSql .="        AND re.cod_entidade  in (".$this->getDado('inCodEntidade').")   \n";
        if ($this->getDado('inCodContaAnalitica'))
            $stSql .="        AND re.cod_plano = ".$this->getDado('inCodContaAnalitica')."      \n";
        if ($this->getDado('stTipoDemonstracao'))
            $stSql .="        AND re.tipo_recibo in (".$this->getDado('stTipoDemonstracao').")    \n";
        if ($this->getDado('inCodContaBanco'))
            $stSql .="        AND reb.cod_plano = ".$this->getDado('inCodContaBanco')."                \n";
        if ($this->getDado('inCodRecurso'))
            $stSql .="        AND rer.cod_recurso = ".$this->getDado('inCodRecurso')."                \n";
        if ($this->getDado('inCodCredor'))
            $stSql .="        AND rec.numcgm = ".$this->getDado('inCodCredor')."                \n";
        if ($this->getDado('stDataInicial'))
            $stSql .="       AND to_date(re.timestamp,'yyyy-mm-dd') BETWEEN to_date('".$this->getDado('stDataInicial')."','dd/mm/yyy') AND to_date('".$this->getDado('stDataFinal')."','dd/mm/yyyy')  \n";

        return $stSql;
    }

    /**
        * Executa um Select no banco de dados a partir do comando SQL
        * @access Public
        * @param  Object  $rsRecordSet Objeto RecordSet
        * @param  String  $stCondicao  String de condição do SQL (WHERE)
        * @param  Boolean $boTransacao
        * @return Object  Objeto Erro
    */
    public function recuperaRelatoriosRecibosExtra(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        if(trim($stOrdem))
            $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
        $stSql = $this->montaRecuperaRelatorioRecibosExtra().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function proximoCod(&$inCod, $parametro = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $boTransacao = false;
        $stComplemento = " Where exercicio =  '".Sessao::getExercicio()."' AND tipo_recibo = '".$this->getDado('tipo_recibo')."' ";
        if ($parametro == 'P') {
            $stComplemento .= " AND cod_entidade =  " . $this->getDado('cod_entidade');
        }
        $stSql = "SELECT MAX( cod_recibo_extra ) AS CODIGO FROM ".$this->getTabela().$stComplemento;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        if ( !$obErro->ocorreu() ) {
            $inCod = $rsRecordSet->getCampo("codigo") + 1;
        }

        return $obErro;
    }

function montaRecuperaReciboExtraPagamento()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_despesa                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,ore.masc_recurso                                                   \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join orcamento.recurso('".$this->getDado("stExercicio")."') as ore ON (            \n";
    $stSql .= "                     ore.cod_recurso = rer.cod_recurso                       \n";
    $stSql .= "                 AND ore.exercicio   = rer.exercicio  )                      \n";
//    $stSql .= "         left join tesouraria.recibo_extra_transferencia ret ON (            \n";
//    $stSql .= "                     re.cod_recibo_extra  = ret.cod_recibo_extra             \n";
//    $stSql .= "                 AND re.exercicio         = ret.exercicio                    \n";
//    $stSql .= "                 AND re.cod_entidade      = ret.cod_entidade                 \n";
//    $stSql .= "                 AND re.tipo_recibo      =  ret.tipo_recibo                  \n";
//    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_anulacao rea      ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = rea.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = rea.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = rea.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo      =  rea.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE rea.cod_recibo_extra     is     null                            \n";
    $stSql .= "       AND NOT EXISTS ( SELECT 1
                     FROM tesouraria.recibo_extra_transferencia
               INNER JOIN ( SELECT (SUM(valor) - SUM(COALESCE(transferencia_estornada.vl_estornado,0))) AS vl_saldo
                                 , transferencia.cod_lote
                                 , transferencia.cod_entidade
                                 , transferencia.tipo
                                 , transferencia.exercicio
                              FROM tesouraria.transferencia
                         LEFT JOIN ( SELECT SUM(valor) AS vl_estornado
                                          , transferencia_estornada.cod_lote
                                          , transferencia_estornada.cod_entidade
                                          , transferencia_estornada.tipo
                                          , transferencia_estornada.exercicio
                                       FROM tesouraria.transferencia_estornada
                                   GROUP BY  transferencia_estornada.cod_lote
                                          , transferencia_estornada.cod_entidade
                                          , transferencia_estornada.tipo
                                          , transferencia_estornada.exercicio
                                   ) AS transferencia_estornada
                                ON transferencia_estornada.cod_lote     = transferencia.cod_lote
                               AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
                               AND transferencia_estornada.tipo         = transferencia.tipo
                               AND transferencia_estornada.exercicio    = transferencia.exercicio
                          GROUP BY transferencia.cod_lote
                                 , transferencia.cod_entidade
                                 , transferencia.tipo
                                 , transferencia.exercicio
                          ) AS transferencia
                       ON transferencia.cod_lote     = recibo_extra_transferencia.cod_lote
                      AND transferencia.cod_entidade = recibo_extra_transferencia.cod_entidade
                      AND transferencia.tipo         = recibo_extra_transferencia.tipo
                      AND transferencia.exercicio    = recibo_extra_transferencia.exercicio
                    WHERE recibo_extra_transferencia.cod_recibo_extra = re.cod_recibo_extra
                      AND recibo_extra_transferencia.exercicio        = re.exercicio
                      AND recibo_extra_transferencia.cod_entidade     = re.cod_entidade
                      AND recibo_extra_transferencia.tipo_recibo      = re.tipo_recibo
                      AND transferencia.vl_saldo > 0
                 ) ";
    $stSql .= "         AND re.tipo_recibo           =   'D'                                \n";
    if ($this->getDado("stExercicio")) {
        $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'\n";
    }
    if ($this->getDado("inCodRecibo")) {
        $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."  \n";
    }
    if ($this->getDado("inCodEntidade")) {
        $stSql .= "     AND re.cod_entidade          =   ".$this->getDado("inCodEntidade")."\n";
    }

    return $stSql;
}

function recuperaReciboExtraPagamento(&$rsRecordSet, $stFiltro= "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReciboExtraPagamento().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboExtraArrecadacao()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_receita                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,ore.masc_recurso   as masc_recurso                                 \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join orcamento.recurso('".$this->getDado('stExercicio')."') as ore \n";
    $stSql .= "              ON ( ore.cod_recurso = rer.cod_recurso                         \n";
    $stSql .= "               AND ore.exercicio   = rer.exercicio )                         \n";
    $stSql .= "         left join tesouraria.recibo_extra_anulacao rea      ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = rea.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = rea.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = rea.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo      =  rea.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE rea.cod_recibo_extra     is     null                            \n";
    $stSql .= "       AND NOT EXISTS ( SELECT 1
                     FROM tesouraria.recibo_extra_transferencia
               INNER JOIN ( SELECT (SUM(valor) - SUM(COALESCE(transferencia_estornada.vl_estornado,0))) AS vl_saldo
                                 , transferencia.cod_lote
                                 , transferencia.cod_entidade
                                 , transferencia.tipo
                                 , transferencia.exercicio
                              FROM tesouraria.transferencia
                         LEFT JOIN ( SELECT SUM(valor) AS vl_estornado
                                          , transferencia_estornada.cod_lote
                                          , transferencia_estornada.cod_entidade
                                          , transferencia_estornada.tipo
                                          , transferencia_estornada.exercicio
                                       FROM tesouraria.transferencia_estornada
                                   GROUP BY  transferencia_estornada.cod_lote
                                          , transferencia_estornada.cod_entidade
                                          , transferencia_estornada.tipo
                                          , transferencia_estornada.exercicio
                                   ) AS transferencia_estornada
                                ON transferencia_estornada.cod_lote     = transferencia.cod_lote
                               AND transferencia_estornada.cod_entidade = transferencia.cod_entidade
                               AND transferencia_estornada.tipo         = transferencia.tipo
                               AND transferencia_estornada.exercicio    = transferencia.exercicio
                          GROUP BY transferencia.cod_lote
                                 , transferencia.cod_entidade
                                 , transferencia.tipo
                                 , transferencia.exercicio
                          ) AS transferencia
                       ON transferencia.cod_lote     = recibo_extra_transferencia.cod_lote
                      AND transferencia.cod_entidade = recibo_extra_transferencia.cod_entidade
                      AND transferencia.tipo         = recibo_extra_transferencia.tipo
                      AND transferencia.cod_lote     = recibo_extra_transferencia.cod_lote
                    WHERE recibo_extra_transferencia.cod_recibo_extra = re.cod_recibo_extra
                      AND recibo_extra_transferencia.exercicio        = re.exercicio
                      AND recibo_extra_transferencia.cod_entidade     = re.cod_entidade
                      AND recibo_extra_transferencia.tipo_recibo      = re.tipo_recibo
                      AND transferencia.vl_saldo > 0
                 ) ";
    $stSql .= "         AND re.tipo_recibo           =   'R'                                \n";
    if ($this->getDado("stExercicio")) {
        $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'\n";
    }
    if ($this->getDado("inCodRecibo")) {
        $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."  \n";
    }
    if ($this->getDado("inCodEntidade")) {
        $stSql .= "     AND re.cod_entidade          =   ".$this->getDado("inCodEntidade")."\n";
    }

    return $stSql;
}

function recuperaReciboExtraArrecadacao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReciboExtraArrecadacao().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboExtraDespesa()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_despesa                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "             re.tipo_recibo           =   'D'                                \n";
    if ($this->getDado("stExercicio")) {
        $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'\n";
    }
    if ($this->getDado("inCodRecibo")) {
        $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."  \n";
    }
    if ($this->getDado("inCodEntidade")) {
        $stSql .= "     AND re.cod_entidade        IN (".$this->getDado("inCodEntidade").") \n";
    }

    return $stSql;
}

function recuperaReciboExtraDespesa(&$rsRecordSet, $stFiltro= "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReciboExtraDespesa().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboExtraDespesaParaAnulacao()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_despesa                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_transferencia ret ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = ret.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = ret.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = ret.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo      =  ret.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_anulacao rea      ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = rea.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = rea.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = rea.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo       = rea.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.transferencia tt               ON(             \n";
    $stSql .= "                     ret.exercicio        = tt.exercicio                     \n";
    $stSql .= "                 AND ret.cod_entidade     = tt.cod_entidade                  \n";
    $stSql .= "                 AND ret.cod_lote         = tt.cod_lote                      \n";
    $stSql .= "                 AND ret.tipo             = tt.tipo                          \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.transferencia_estornada tte    ON(             \n";
    $stSql .= "                     tt.cod_lote          = tte.cod_lote                     \n";
    $stSql .= "                 AND tt.cod_entidade      = tte.cod_entidade                 \n";
    $stSql .= "                 AND tt.exercicio         = tte.exercicio                    \n";
    $stSql .= "                 AND tt.tipo              = tte.tipo                         \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         re.tipo_recibo           =   'D'                                    \n";
    $stSql .= "         AND rea.cod_recibo_extra IS NULL                                    \n";
         if ((!$this->getDado("boDemonstrarPagos")) AND ($this->getDado("inCodRecibo"))) {
    $stSql .= "         AND ret.cod_recibo_extra     = ".$this->getDado("inCodRecibo")."    \n";
         }
         if ($this->getDado("stExercicio")) {
    $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'    \n";
      }
      if ($this->getDado("inCodRecibo")) {
    $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."      \n";
      }
      if ($this->getDado("inCodEntidade")) {
    $stSql .= "     AND re.cod_entidade        IN (".$this->getDado("inCodEntidade").")     \n";
      }
      if ($this->getDado("cod_plano")) {
    $stSql .= "     AND re.cod_plano        IN (".$this->getDado("cod_plano").")            \n";
      }

    return $stSql;
}

function recuperaReciboExtraDespesaParaAnulacao(&$rsRecordSet, $stFiltro= "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaReciboExtraDespesaParaAnulacao().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboExtra()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_receita                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         re.tipo_recibo           =   'R'                                    \n";
    if ($this->getDado("stExercicio")) {
        $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'\n";
    }
    if ($this->getDado("inCodRecibo")) {
        $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."  \n";
    }
    if ($this->getDado("inCodEntidade")) {
        $stSql .= "     AND re.cod_entidade          IN (".$this->getDado("inCodEntidade").") \n";
    }
    if ($this->getDado('data_emissao')) {
        $stSql .= "     AND TO_CHAR(re.timestamp,'dd/mm/yyyy') = '".$this->getDado('data_emissao')."' ";
    }
    if ($this->getDado('cod_plano')) {
        $stSql .= "  AND re.cod_plano = ".$this->getDado('cod_plano')." ";
    }
    $stSql .= "             AND not exists  (SELECT                                         \n";
    $stSql .= "                  rea.cod_recibo_extra                                       \n";
    $stSql .= "             FROM                                                            \n";
    $stSql .= "                 tesouraria.recibo_extra_anulacao  as rea                    \n";
    $stSql .= "             WHERE    rea.cod_recibo_extra =  re.cod_recibo_extra            \n";
    $stSql .= "                  AND rea.exercicio        =  re.exercicio                   \n";
    $stSql .= "                  AND rea.cod_entidade     =  re.cod_entidade                \n";
    $stSql .= "                  AND rea.tipo_recibo      =  re.tipo_recibo)                \n";

    return $stSql;
}

function recuperaReciboExtra(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReciboExtra().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaReciboExtraParaAnulacao()
{
    $stSql  = "     SELECT                                                                  \n";
    $stSql .= "          re.cod_recibo_extra                                                \n";
    $stSql .= "         ,re.exercicio                                                       \n";
    $stSql .= "         ,re.cod_entidade                                                    \n";
    $stSql .= "         ,re.tipo_recibo                                                     \n";
    $stSql .= "         ,re.cod_plano       as cod_plano_receita                            \n";
    $stSql .= "         ,reb.cod_plano      as cod_plano_banco                              \n";
    $stSql .= "         ,rer.cod_recurso    as cod_recurso                                  \n";
    $stSql .= "         ,rec.numcgm         as cod_credor                                   \n";
    $stSql .= "         ,re.valor                                                           \n";
    $stSql .= "         ,to_char(re.timestamp,'dd/mm/yyyy') as dt_recibo                    \n";
    $stSql .= "         ,pc.nom_conta                                                       \n";
    $stSql .= "     FROM                                                                    \n";
    $stSql .= "         tesouraria.recibo_extra re                                          \n";
    $stSql .= "         inner join contabilidade.plano_analitica as pa on(                  \n";
    $stSql .= "             pa.cod_plano = re.cod_plano                                     \n";
    $stSql .= "         and pa.exercicio = re.exercicio )                                   \n";
    $stSql .= "         inner join contabilidade.plano_conta as pc on(                      \n";
    $stSql .= "             pc.exercicio = pa.exercicio                                     \n";
    $stSql .= "         and pc.cod_conta = pa.cod_conta  )                                  \n";
    $stSql .= "         left join tesouraria.recibo_extra_banco as reb      ON (            \n";
    $stSql .= "                     reb.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND reb.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND reb.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND reb.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_credor as rec     ON (            \n";
    $stSql .= "                     rec.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rec.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rec.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rec.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_recurso as rer    ON (            \n";
    $stSql .= "                     rer.cod_recibo_extra =  re.cod_recibo_extra             \n";
    $stSql .= "                 AND rer.exercicio        =  re.exercicio                    \n";
    $stSql .= "                 AND rer.cod_entidade     =  re.cod_entidade                 \n";
    $stSql .= "                 AND rer.tipo_recibo      =  re.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_transferencia ret ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = ret.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = ret.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = ret.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo       = ret.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.recibo_extra_anulacao rea      ON (            \n";
    $stSql .= "                     re.cod_recibo_extra  = rea.cod_recibo_extra             \n";
    $stSql .= "                 AND re.exercicio         = rea.exercicio                    \n";
    $stSql .= "                 AND re.cod_entidade      = rea.cod_entidade                 \n";
    $stSql .= "                 AND re.tipo_recibo       = rea.tipo_recibo                  \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.transferencia tt               ON(             \n";
    $stSql .= "                     ret.exercicio        = tt.exercicio                     \n";
    $stSql .= "                 AND ret.cod_entidade     = tt.cod_entidade                  \n";
    $stSql .= "                 AND ret.cod_lote         = tt.cod_lote                      \n";
    $stSql .= "                 AND ret.tipo             = tt.tipo                          \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "         left join tesouraria.transferencia_estornada tte    ON(             \n";
    $stSql .= "                     tt.cod_lote          = tte.cod_lote                     \n";
    $stSql .= "                 AND tt.cod_entidade      = tte.cod_entidade                 \n";
    $stSql .= "                 AND tt.exercicio         = tte.exercicio                    \n";
    $stSql .= "                 AND tt.tipo              = tte.tipo                         \n";
    $stSql .= "         )                                                                   \n";
    $stSql .= "     WHERE                                                                   \n";
    $stSql .= "         re.tipo_recibo           =   'R'                                    \n";
    if ($this->getDado("stExercicio")) {
        $stSql .= "     AND re.exercicio             =   '".$this->getDado("stExercicio")."'\n";
    }
    if ($this->getDado("inCodRecibo")) {
        $stSql .= "     AND re.cod_recibo_extra      =   ".$this->getDado("inCodRecibo")."  \n";
    }
    if ($this->getDado("inCodEntidade")) {
        $stSql .= "     AND re.cod_entidade          IN (".$this->getDado("inCodEntidade").") \n";
    }
    if ($this->getDado('data_emissao')) {
        $stSql .= "     AND TO_CHAR(re.timestamp,'dd/mm/yyyy') = '".$this->getDado('data_emissao')."' ";
    }
    if ($this->getDado('cod_plano')) {
        $stSql .= "  AND re.cod_plano = ".$this->getDado('cod_plano')." ";
    }
    $stSql .= " AND NOT EXISTS (SELECT * FROM tesouraria.recibo_extra_anulacao WHERE
               re.cod_recibo_extra  = cod_recibo_extra
               AND re.exercicio         = exercicio
               AND re.cod_entidade      = cod_entidade
               AND re.tipo_recibo       = tipo_recibo)";

    return $stSql;
}

function recuperaReciboExtraParaAnulacao(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaReciboExtraParaAnulacao().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;

}

function recuperaReciboExtraConsulta(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaReciboExtraConsulta", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaReciboExtraConsulta()
{
    $stSql = "
            SELECT recibo_extra.cod_recibo_extra
                 , recibo_extra.exercicio
                 , recibo_extra.cod_entidade
                 , entidade_cgm.nom_cgm AS nom_entidade
                 , recibo_extra.tipo_recibo
                 , recibo_extra.cod_plano AS cod_plano_receita
                 , plano_conta.nom_conta AS nom_plano_receita
                 , recibo_extra_banco.cod_plano AS cod_plano_banco
                 , plano_conta_banco.nom_conta AS nom_plano_banco
                 , recibo_extra_recurso.cod_recurso
                 , recurso.nom_recurso
                 , recibo_extra_credor.numcgm AS cod_credor
                 , credor_cgm.nom_cgm AS nom_credor
                 , recibo_extra.valor
                 , COALESCE(recibo_extra_transferencia.valor_pago,0) AS valor_pago
                 , COALESCE((recibo_extra.valor - recibo_extra_transferencia.valor_pago),0) AS valor_saldo
                 , TO_CHAR(recibo_extra.timestamp,'dd/mm/yyyy') AS dt_recibo
                 , plano_conta.nom_conta
                 , recibo_extra.historico
                 , CASE WHEN recibo_extra_anulacao.cod_recibo_extra IS NOT NULL
                        THEN 'Anulado'
                        WHEN recibo_extra_transferencia.cod_recibo_extra IS NOT NULL
                        THEN 'Arrecadado'
                        ELSE 'A Receber'
                   END AS status

              FROM tesouraria.recibo_extra

        INNER JOIN contabilidade.plano_analitica
                ON recibo_extra.cod_plano = plano_analitica.cod_plano
               AND recibo_extra.exercicio = plano_analitica.exercicio

        INNER JOIN contabilidade.plano_conta
                ON plano_analitica.cod_conta = plano_conta.cod_conta
               AND plano_analitica.exercicio = plano_conta.exercicio

         LEFT JOIN tesouraria.recibo_extra_banco
                ON recibo_extra.cod_recibo_extra = recibo_extra_banco.cod_recibo_extra
               AND recibo_extra.exercicio        = recibo_extra_banco.exercicio
               AND recibo_extra.cod_entidade     = recibo_extra_banco.cod_entidade
               AND recibo_extra.tipo_recibo      = recibo_extra_banco.tipo_recibo

         LEFT JOIN tesouraria.recibo_extra_credor
                ON recibo_extra.cod_recibo_extra = recibo_extra_credor.cod_recibo_extra
               AND recibo_extra.exercicio        = recibo_extra_credor.exercicio
               AND recibo_extra.cod_entidade     = recibo_extra_credor.cod_entidade
               AND recibo_extra.tipo_recibo      = recibo_extra_credor.tipo_recibo

         LEFT JOIN tesouraria.recibo_extra_recurso
                ON recibo_extra.cod_recibo_extra = recibo_extra_recurso.cod_recibo_extra
               AND recibo_extra.exercicio        = recibo_extra_recurso.exercicio
               AND recibo_extra.cod_entidade     = recibo_extra_recurso.cod_entidade
               AND recibo_extra.tipo_recibo      = recibo_extra_recurso.tipo_recibo

        --Recupera a entidade
        INNER JOIN orcamento.entidade
                ON recibo_extra.cod_entidade = entidade.cod_entidade
               AND recibo_extra.exercicio    = entidade.exercicio
        INNER JOIN sw_cgm AS entidade_cgm
                ON entidade.numcgm = entidade_cgm.numcgm

        --Recupera os dados do credor
         LEFT JOIN sw_cgm AS credor_cgm
                ON recibo_extra_credor.numcgm = credor_cgm.numcgm

        --Recupera os dados do recurso
         LEFT JOIN orcamento.recurso
                ON recibo_extra_recurso.cod_recurso = recurso.cod_recurso
               AND recibo_extra_recurso.exercicio   = recurso.exercicio

        --Recupera os dados do banco
         LEFT JOIN contabilidade.plano_analitica AS plano_analitica_banco
                ON recibo_extra_banco.cod_plano = plano_analitica_banco.cod_plano
               AND recibo_extra_banco.exercicio = plano_analitica_banco.exercicio
         LEFT JOIN contabilidade.plano_conta AS plano_conta_banco
                ON plano_analitica_banco.cod_conta = plano_conta_banco.cod_conta
               AND plano_analitica_banco.exercicio = plano_conta_banco.exercicio

        --Verifica se esta anulado
         LEFT JOIN tesouraria.recibo_extra_anulacao
                ON recibo_extra.exercicio        = recibo_extra_anulacao.exercicio
               AND recibo_extra.cod_entidade     = recibo_extra_anulacao.cod_entidade
               AND recibo_extra.cod_recibo_extra = recibo_extra_anulacao.cod_recibo_extra
               AND recibo_extra.tipo_recibo      = recibo_extra_anulacao.tipo_recibo

        --Verifica se esta pago
         LEFT JOIN ( SELECT recibo_extra_transferencia.exercicio
                          , recibo_extra_transferencia.cod_entidade
                          , recibo_extra_transferencia.cod_recibo_extra
                          , recibo_extra_transferencia.tipo_recibo
                          , (SUM(transferencia.valor) - SUM(COALESCE(transferencia_estornada.valor,0))) AS valor_pago
                       FROM tesouraria.recibo_extra_transferencia
                 INNER JOIN tesouraria.transferencia
                         ON recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
                        AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
                        AND recibo_extra_transferencia.exercicio    = transferencia.exercicio
                        AND recibo_extra_transferencia.tipo         = transferencia.tipo
                  LEFT JOIN tesouraria.transferencia_estornada
                         ON transferencia.cod_lote     = transferencia_estornada.cod_lote
                        AND transferencia.cod_entidade = transferencia_estornada.cod_entidade
                        AND transferencia.exercicio    = transferencia_estornada.exercicio
                        AND transferencia.tipo         = transferencia_estornada.tipo
                   GROUP BY recibo_extra_transferencia.exercicio
                          , recibo_extra_transferencia.cod_entidade
                          , recibo_extra_transferencia.cod_recibo_extra
                          , recibo_extra_transferencia.tipo_recibo
                   ) AS recibo_extra_transferencia
                ON recibo_extra.exercicio        = recibo_extra_transferencia.exercicio
               AND recibo_extra.cod_entidade     = recibo_extra_transferencia.cod_entidade
               AND recibo_extra.cod_recibo_extra = recibo_extra_transferencia.cod_recibo_extra
               AND recibo_extra.tipo_recibo      = recibo_extra_transferencia.tipo_recibo
    ";
    if ($this->getDado('stExercicio')) {
        $stFiltro .= " AND recibo_extra.exercicio = '".$this->getDado("stExercicio")."' \n";
    }
    if ($this->getDado('inCodRecibo')) {
        $stFiltro .= " AND recibo_extra.cod_recibo_extra = ".$this->getDado("inCodRecibo")." \n";
    }
    if ($this->getDado('inCodEntidade')) {
        $stFiltro .= " AND recibo_extra.cod_entidade IN (".$this->getDado("inCodEntidade").") \n";
    }
    if ($this->getDado('data_emissao')) {
        $stFiltro .= " AND TO_CHAR(recibo_extra.timestamp,'dd/mm/yyyy') = '".$this->getDado('data_emissao')."' \n";
    }
    if ($this->getDado('tipo_recibo')) {
        $stFiltro .= " AND recibo_extra.tipo_recibo = '".$this->getDado('tipo_recibo')."' \n";
    }
    if ($this->getDado('cod_plano')) {
        $stFiltro .= " AND recibo_extra.cod_plano = '".$this->getDado('cod_plano')."' \n";
    }

    return ($stFiltro) ? $stSql . ' WHERE ' . substr($stFiltro,4) : $stSql;

}

function recuperaReciboReceitaExtraConsultaPagamento(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaReciboReceitaExtraConsultaPagamento", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaReciboReceitaExtraConsultaPagamento()
{
    $stSql = "
            SELECT TO_CHAR(transferencia.timestamp_transferencia,'dd/mm/YYYY') AS dt_pagamento
                 , valor
                 , plano_analitica.cod_plano
                 , plano_conta.nom_conta AS nom_plano
                 , transferencia.cod_entidade
                 , transferencia.tipo
                 , transferencia.exercicio
                 , transferencia.cod_lote
                 , CASE WHEN ( transferencia_estornada.cod_lote IS NOT NULL )
                        THEN 't'
                        ELSE 'f'
                   END AS estornado
              FROM tesouraria.recibo_extra_transferencia

        INNER JOIN tesouraria.transferencia
                ON recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
               AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
               AND recibo_extra_transferencia.exercicio    = transferencia.exercicio
               AND recibo_extra_transferencia.tipo         = transferencia.tipo

        INNER JOIN contabilidade.plano_analitica
                ON transferencia.cod_plano_debito = plano_analitica.cod_plano
               AND transferencia.exercicio        = plano_analitica.exercicio

        INNER JOIN contabilidade.plano_conta
                ON plano_analitica.cod_conta = plano_conta.cod_conta
               AND plano_analitica.exercicio = plano_conta.exercicio

        LEFT JOIN ( SELECT transferencia_estornada.cod_lote
                      , transferencia_estornada.cod_entidade
                      , transferencia_estornada.exercicio
                      , transferencia_estornada.tipo
                   FROM tesouraria.transferencia_estornada
               GROUP BY transferencia_estornada.cod_lote
                      , transferencia_estornada.cod_entidade
                      , transferencia_estornada.exercicio
                      , transferencia_estornada.tipo
               ) AS transferencia_estornada
            ON transferencia.cod_lote     = transferencia_estornada.cod_lote
           AND transferencia.cod_entidade = transferencia_estornada.cod_entidade
           AND transferencia.exercicio    = transferencia_estornada.exercicio
           AND transferencia.tipo         = transferencia_estornada.tipo

    ";

    if ($this->getDado('stExercicio')) {
        $stFiltro .= " AND recibo_extra_transferencia.exercicio = '".$this->getDado("stExercicio")."' \n";
    }
    if ($this->getDado('inCodRecibo')) {
        $stFiltro .= " AND recibo_extra_transferencia.cod_recibo_extra = ".$this->getDado("inCodRecibo")." \n";
    }
    if ($this->getDado('inCodEntidade')) {
        $stFiltro .= " AND recibo_extra_transferencia.cod_entidade IN (".$this->getDado("inCodEntidade").") \n";
    }
    if ($this->getDado('tipo_recibo')) {
        $stFiltro .= " AND recibo_extra_transferencia.tipo_recibo = '".$this->getDado('tipo_recibo')."' \n";
    }

    return ($stFiltro) ? $stSql . ' WHERE ' . substr($stFiltro,4) : $stSql;
}

function recuperaReciboDespesaExtraConsultaPagamento(&$rsRecordSet, $stFiltro="", $stOrder="", $boTransacao="")
{
    return $this->executaRecupera( "montaRecuperaReciboDespesaExtraConsultaPagamento", $rsRecordSet, $stFiltro, $stOrder, $boTransacao );
}

function montaRecuperaReciboDespesaExtraConsultaPagamento()
{
    $stSql = "
            SELECT TO_CHAR(transferencia.timestamp_transferencia,'dd/mm/YYYY') AS dt_pagamento
                 , valor
                 , plano_analitica.cod_plano
                 , plano_conta.nom_conta AS nom_plano
                 , transferencia.cod_entidade
                 , transferencia.tipo
                 , transferencia.exercicio
                 , transferencia.cod_lote
                 , CASE WHEN ( transferencia_estornada.cod_lote IS NOT NULL )
                        THEN 't'
                        ELSE 'f'
                   END AS estornado
              FROM tesouraria.recibo_extra_transferencia

        INNER JOIN tesouraria.transferencia
                ON recibo_extra_transferencia.cod_lote     = transferencia.cod_lote
               AND recibo_extra_transferencia.cod_entidade = transferencia.cod_entidade
               AND recibo_extra_transferencia.exercicio    = transferencia.exercicio
               AND recibo_extra_transferencia.tipo         = transferencia.tipo

        INNER JOIN contabilidade.plano_analitica
                ON transferencia.cod_plano_credito = plano_analitica.cod_plano
               AND transferencia.exercicio        = plano_analitica.exercicio

        INNER JOIN contabilidade.plano_conta
                ON plano_analitica.cod_conta = plano_conta.cod_conta
               AND plano_analitica.exercicio = plano_conta.exercicio

        LEFT JOIN ( SELECT transferencia_estornada.cod_lote
                      , transferencia_estornada.cod_entidade
                      , transferencia_estornada.exercicio
                      , transferencia_estornada.tipo
                   FROM tesouraria.transferencia_estornada
               GROUP BY transferencia_estornada.cod_lote
                      , transferencia_estornada.cod_entidade
                      , transferencia_estornada.exercicio
                      , transferencia_estornada.tipo
               ) AS transferencia_estornada
            ON transferencia.cod_lote     = transferencia_estornada.cod_lote
           AND transferencia.cod_entidade = transferencia_estornada.cod_entidade
           AND transferencia.exercicio    = transferencia_estornada.exercicio
           AND transferencia.tipo         = transferencia_estornada.tipo

    ";

    if ($this->getDado('stExercicio')) {
        $stFiltro .= " AND recibo_extra_transferencia.exercicio = '".$this->getDado("stExercicio")."' \n";
    }
    if ($this->getDado('inCodRecibo')) {
        $stFiltro .= " AND recibo_extra_transferencia.cod_recibo_extra = ".$this->getDado("inCodRecibo")." \n";
    }
    if ($this->getDado('inCodEntidade')) {
        $stFiltro .= " AND recibo_extra_transferencia.cod_entidade IN (".$this->getDado("inCodEntidade").") \n";
    }
    if ($this->getDado('tipo_recibo')) {
        $stFiltro .= " AND recibo_extra_transferencia.tipo_recibo = '".$this->getDado('tipo_recibo')."' \n";
    }

    return ($stFiltro) ? $stSql . ' WHERE ' . substr($stFiltro,4) : $stSql;
}

function recuperaUltimaDataRecibo(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaUltimaDataRecibo();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaUltimaDataRecibo()
{
    $stSql  = " SELECT                                                      \n";
    $stSql .= "      max(re.timestamp) as data                              \n";
    $stSql .= " FROM tesouraria.recibo_extra as re                          \n";
    $stSql .= "      LEFT JOIN tesouraria.recibo_extra_anulacao as ra on(   \n";
    $stSql .= "         re.cod_recibo_extra = ra.cod_recibo_extra           \n";
    $stSql .= "      and re.exercicio        = ra.exercicio                 \n";
    $stSql .= "      and re.cod_entidade     = ra.cod_entidade              \n";
    $stSql .= "      and re.tipo_recibo      = ra.tipo_recibo )             \n";
    $stSql .= " WHERE ra.cod_recibo_extra IS NULL                           \n";
    if ( $this->getDado( 'exercicio' ) ) {
        $stSql .= " and re.exercicio = '".$this->getDado( 'exercicio' )."'" ;
    }
    if ( $this->getDado( 'tipo_recibo' ) ) {
        $stSql .= " and re.tipo_recibo = '".$this->getDado( 'tipo_recibo' )."'" ;
    }
    if ( $this->getDado( 'cod_entidade' ) ) {
        $stSql .= " and re.cod_entidade = ".$this->getDado( 'cod_entidade' ) ;
    }

    return $stSql;
}

}

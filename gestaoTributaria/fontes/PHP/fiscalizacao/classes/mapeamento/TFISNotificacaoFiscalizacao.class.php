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
 * Classe de mapeamento para notificao_fiscalizacao
 * Data de Criação: 28/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISNotificacaoFiscalizacao.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

/**
 * Classe de mapeamento para notificacao_fiscalizacao.
 */
class TFISNotificacaoFiscalizacao extends Persistente
{
    /*
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.notificacao_fiscalizacao' );

        $this->setCampoCod( 'cod_processo' );
        $this->setComplementoChave( 'cod_processo' );

        $this->addCampo( 'cod_processo', 'integer', true, '', true, true );
        $this->addCampo( 'cod_tipo_documento', 'integer', true, '', false, true );
        $this->addCampo( 'cod_documento', 'integer', true, '', false, true );
        $this->addCampo( 'cod_fiscal', 'integer', true, '', false, false );
        $this->addCampo( 'dt_notificacao', 'date', true, '', false, false );
        $this->addCampo( 'observacao', 'text', true, '', false, false );
        //$this->addCampo( 'timestamp', 'timestamp', true, '', false, false );
        $this->addCampo( 'num_notificacao', 'integer', true, '', false, false );
        $this->addCampo( 'exercicio', 'integer', true, '', false, false );
    }

    public function recuperaInfracaoProcesso(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaInfracaoProcesso($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaInfracaoProcesso($condicao)
    {
        $stSql ="  SELECT                                                               \n";
        $stSql.="    inf.cod_infracao,                                                  \n";
        $stSql.="    inf.nom_infracao,                                                  \n";
        $stSql.="    nor.nom_norma,                                                     \n";
        $stSql.="    ninf.observacao as infracao_observacao,                            \n";
        $stSql.="    pen.nom_penalidade,                                                \n";
        $stSql.="    ninfm.valor,                                                       \n";
        $stSql.="    ninfo.observacao as penalidade_observacao,                         \n";
        $stSql.="    ninfo.dt_ocorrencia                                                \n";
        $stSql.="  FROM                                                                 \n";
        $stSql.="    fiscalizacao.auto_fiscalizacao afis                                \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.auto_infracao ninf                                    \n";
        $stSql.="    on afis.cod_auto_fiscalizacao = ninf.cod_auto_fiscalizacao         \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.infracao inf                                          \n";
        $stSql.="    on inf.cod_infracao = ninf.cod_infracao                            \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.penalidade pen                                        \n";
        $stSql.="    on pen.cod_penalidade = ninf.cod_penalidade                        \n";
        $stSql.="  LEFT JOIN                                                            \n";
        $stSql.="    fiscalizacao.auto_infracao_multa ninfm on                          \n";
        $stSql.="    (                                                                  \n";
        $stSql.="      ninfm.cod_processo = ninf.cod_processo and                       \n";
        $stSql.="      ninfm.cod_infracao = ninf.cod_infracao and                       \n";
        $stSql.="      ninfm.cod_penalidade = ninf.cod_penalidade and                   \n";
        $stSql.="      ninfm.cod_auto_fiscalizacao = ninf.cod_auto_fiscalizacao         \n";
        $stSql.="    )                                                                  \n";
        $stSql.="  LEFT OUTER JOIN                                                      \n";
        $stSql.="      fiscalizacao.auto_infracao_outros ninfo                          \n";
        $stSql.="      on ninfo.cod_processo = ninf.cod_processo                        \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    normas.norma nor                                                   \n";
        $stSql.="    on nor.cod_norma = inf.cod_norma                                   \n";
        $stSql.="  $condicao                                                            \n";
        $stSql.="  GROUP BY                                                             \n";
        $stSql.="    inf.cod_infracao,                                                  \n";
        $stSql.="    inf.nom_infracao,                                                  \n";
        $stSql.="    nor.nom_norma,                                                     \n";
        $stSql.="    ninf.observacao,                                                   \n";
        $stSql.="    pen.nom_penalidade,                                                \n";
        $stSql.="    ninfm.valor,                                                       \n";
        $stSql.="    ninfo.observacao,                                                  \n";
        $stSql.="    ninfo.dt_ocorrencia                                                \n";

        return $stSql ;
    }

    public function recuperaProximoNumNotificacao(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaProximoNumNotificacao().$stCondicao.$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaRecuperaProximoNumNotificacao()
    {
        $stSql = "
            SELECT
                max(num_notificacao) AS num_notificacao
            FROM
                fiscalizacao.notificacao_fiscalizacao
        ";

        return $stSql;
    }

    public function recuperaNotificacaoInfracao(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaNotificacaoInfracao($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    private function montaNotificacaoInfracao($condicao)
    {
        $stSql ="  SELECT                                                               \n";
        $stSql.="    inf.cod_infracao,                                                  \n";
        $stSql.="    inf.nom_infracao,                                                  \n";
        $stSql.="    nor.nom_norma,                                                     \n";
        $stSql.="    nini.observacao as infracao_observacao,                            \n";
        $stSql.="    ninf.observacao,                                                   \n";
        $stSql.="    ninf.num_notificacao as num_notificacao,                            \n";
        $stSql.="    ninf.exercicio as exercicio_notificacao                            \n";
        $stSql.="  FROM                                                                 \n";
        $stSql.="    fiscalizacao.notificacao_fiscalizacao ninf                         \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.notificacao_infracao nini                             \n";
        $stSql.="    on ninf.cod_processo = nini.cod_processo                           \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    fiscalizacao.infracao inf                                          \n";
        $stSql.="    on inf.cod_infracao = nini.cod_infracao                            \n";
        $stSql.="  INNER JOIN                                                           \n";
        $stSql.="    normas.norma nor                                                   \n";
        $stSql.="    on nor.cod_norma = inf.cod_norma                                   \n";
        $stSql.="  $condicao                                                            \n";
        $stSql.="  GROUP BY                                                             \n";
        $stSql.="    inf.cod_infracao,                                                  \n";
        $stSql.="    inf.nom_infracao,                                                  \n";
        $stSql.="    nor.nom_norma,                                                     \n";
        $stSql.="    nini.observacao,                                                   \n";
        $stSql.="    ninf.num_notificacao,                                              \n";
        $stSql.="    ninf.exercicio,                                                    \n";
        $stSql.="    ninf.observacao                                                    \n";

        return $stSql;
    }

    public function recuperaTotalLevantamento(&$rsRecordSet, $stCondicao, $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaTotalLevantamento($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaTotalLevantamento($condicao)
    {
            $stSql ="  SELECT fl.cod_processo                                                             \n";
            $stSql.="            , to_real(sum(round(fl.receita_declarada, 2))) as max_declarada  \n";
            $stSql.="            , to_real(sum(round(fl.receita_efetiva, 2))) as max_efetiva      \n";
            $stSql.="            , to_real(sum(round(fl.iss_pago ,2))) as max_pago                \n";
            $stSql.="            , to_real(sum(round(fl.iss_devido ,2))) as max_devido            \n";
            $stSql.="            , to_real(sum(round(fl.iss_devolver ,2))) as max_devolver        \n";
            $stSql.="            , to_real(sum(round(fl.iss_pagar ,2))) as max_pagar              \n";
            $stSql.="            , to_real(sum(round(fl.total_devolver ,2))) as max_total_d       \n";
            $stSql.="            , to_real(sum(round(fl.total_pagar ,2))) as max_total_p          \n";
            $stSql.="            , to_real(sum(round(flc.valor ,2))) as max_valor_c               \n";
            $stSql.="            , to_real(sum(round(fla_mora.valor ,2))) as max_valor_mora       \n";
            $stSql.="            , to_real(sum(round(fla_juros.valor ,2))) as max_valor_juros     \n";
            $stSql.="       FROM fiscalizacao.levantamento as fl                                          \n";
            $stSql.="  LEFT JOIN fiscalizacao.levantamento_correcao as flc                                \n";
            $stSql.="         ON flc.cod_processo = fl.cod_processo                                       \n";
            $stSql.="         AND flc.competencia = fl.competencia                                        \n";
            $stSql.="   LEFT JOIN ( SELECT   fla.cod_processo                                             \n";
            $stSql.="                      , fla.competencia                                              \n";
            $stSql.="                      , fla.valor                                                    \n";
            $stSql.="                   FROM fiscalizacao.levantamento_acrescimo as fla                   \n";
            $stSql.="                  WHERE cod_tipo = 2 ) as fla_juros                                  \n";
            $stSql.="                     ON fla_juros.cod_processo = fl.cod_processo                     \n";
            $stSql.="                    AND fla_juros.competencia = fl.competencia                       \n";
            $stSql.="              LEFT JOIN ( SELECT fla.cod_processo                                    \n";
            $stSql.="                               , fla.competencia                                     \n";
            $stSql.="                               , fla.valor                                           \n";
            $stSql.="                            FROM fiscalizacao.levantamento_acrescimo as fla          \n";
            $stSql.="                           WHERE cod_tipo = 3 ) as fla_mora                          \n";
            $stSql.="                              ON fla_mora.cod_processo = fl.cod_processo             \n";
            $stSql.="                             AND fla_mora.competencia = fl.competencia               \n";
            $stSql.="  $condicao                                                                          \n";
            $stSql.="  GROUP BY fl.cod_processo                                                           \n";

    return $stSql;
    }

    public function recuperaTermosInfracoes(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaDadosTermosInfracoes($stCondicao).$stOrdem;
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    private function montaDadosTermosInfracoes($condicao)
    {
        $stSql   ="     SELECT                                                  \n";
        $stSql  .="         inf.cod_infracao,                                   \n";
        $stSql  .="             inf.nom_infracao,                               \n";
        $stSql  .="             nn.nom_norma,                                   \n";
        $stSql  .="             afis.observacao as observacao_fnt,              \n";
        $stSql  .="             ntinf.observacao as observacao_fnti,            \n";
        $stSql  .="             afis.num_notificacao,                           \n";
        $stSql  .="             pfo.inscricao_municipal                         \n";
        $stSql  .="           FROM fiscalizacao.notificacao_termo afis          \n";
        $stSql  .="           INNER JOIN                                        \n";
        $stSql  .="             fiscalizacao.notificacao_termo_infracao ntinf   \n";
        $stSql  .="             on afis.num_notificacao = ntinf.num_notificacao \n";
        $stSql  .="           INNER JOIN                                        \n";
        $stSql  .="             fiscalizacao.infracao inf                       \n";
        $stSql  .="             on inf.cod_infracao = ntinf.cod_infracao        \n";
        $stSql  .="           INNER JOIN                                        \n";
        $stSql  .="             normas.norma nn                                 \n";
        $stSql  .="             on  nn.cod_norma = inf.cod_norma                \n";
        $stSql  .="           INNER JOIN                                        \n";
        $stSql  .="             fiscalizacao.processo_fiscal fpf                \n";
        $stSql  .="             on fpf.cod_processo = afis.cod_processo         \n";
        $stSql  .="               INNER JOIN                                    \n";
        $stSql  .="                 fiscalizacao.processo_fiscal_obras pfo      \n";
        $stSql  .="                 on pfo.cod_processo = afis.cod_processo     \n";
        $stSql  .= $condicao.                                                  "\n";
        $stSql  .="           GROUP BY                                          \n";
        $stSql  .="             inf.cod_infracao,                               \n";
        $stSql  .="             inf.nom_infracao,                               \n";
        $stSql  .="             nn.nom_norma,                                   \n";
        $stSql  .="             afis.observacao,                                \n";
        $stSql  .="             ntinf.observacao,                               \n";
        $stSql  .="             afis.num_notificacao,                           \n";
        $stSql  .="             pfo.inscricao_municipal                         \n";

        return $stSql ;
    }

}

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
 * Classe de mapeamento para Penalidade
 * Data de Criação: 25/07/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Pedro Vaz de Mello de Medeiros

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISPenalidade.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso:
 */

/**
 * Classe de mapeamento para Penalidade.
 */
class TFISPenalidade extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.penalidade' );

        $this->setCampoCod( 'cod_penalidade' );
        $this->setComplementoChave( 'cod_tipo_penalidade, cod_norma' );

        $this->addCampo( 'cod_penalidade', 'integer', true, '', true, false );
        $this->addCampo( 'cod_tipo_penalidade', 'integer', true, '', false, true );
        $this->addCampo( 'cod_norma', 'integer', true, '', false, true );
        $this->addCampo( 'nom_penalidade', 'varchar', true, 80, false, false );
    }

    public function recuperaListaPenalidadesBaixadas(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaPenalidadesBaixadas( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    private function montaRecuperaListaPenalidadesBaixadas($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " AND ".$stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL = "
                SELECT penalidade.cod_tipo_penalidade
                     , tipo_penalidade.descricao
                     , penalidade.cod_penalidade
                     , penalidade.nom_penalidade
                     , penalidade.cod_norma
                     , norma.cod_norma
                     , norma.nom_norma
                     , penalidade_baixa.timestamp_inicio
                     , to_char( penalidade_baixa.timestamp_inicio::date, 'dd/mm/yyyy' ) AS data_baixa
                     , penalidade_baixa.motivo
                  FROM fiscalizacao.penalidade
            INNER JOIN fiscalizacao.tipo_penalidade
                    ON tipo_penalidade.cod_tipo = penalidade.cod_tipo_penalidade
            INNER JOIN normas.norma
                    ON norma.cod_norma = penalidade.cod_norma
            INNER JOIN ( SELECT tmp.*
                           FROM fiscalizacao.penalidade_baixa AS tmp
                     INNER JOIN ( SELECT MAX(timestamp_inicio) AS timestamp_inicio
                                       , cod_penalidade
                                    FROM fiscalizacao.penalidade_baixa
                                GROUP BY cod_penalidade
                                )AS tmp2
                             ON tmp.cod_penalidade = tmp2.cod_penalidade
                            AND tmp.timestamp_inicio = tmp2.timestamp_inicio
                       )AS penalidade_baixa
                    ON penalidade_baixa.cod_penalidade = penalidade.cod_penalidade
                 WHERE CASE WHEN ( penalidade_baixa.timestamp_inicio IS NOT NULL ) AND ( penalidade_baixa.timestamp_termino IS NULL ) THEN
                           true
                       ELSE
                           false
                       END
        ";

        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    /**
     * Recupera todos os dados da lista de Penalidades em um record set.
     * @param  RecordSet &$rsRecordSet o record set retornado pela operação
     * @param  array     $arCondicao   array contendo as condições da busca
     * @param  string    $stOrdem      especifica a ordem em que os elementos são ordenados
     * @param  boolean   $boTransacao  especifica se a conexão continua aberta depois da transação
     * @return Error     erro da requisição, se houver
     * @access public
     */
    public function recuperaListaPenalidades(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaPenalidades( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    /**
     * Monta a requisição SQL para recuperaListaPenalidades.
     * @param  array  $arCondicao array contendo as condições de busca
     * @return string a requisição SQL
     * @access private
     */
    private function montaRecuperaListaPenalidades($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " AND ".$stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL = "
                SELECT penalidade.cod_tipo_penalidade
                     , tipo_penalidade.descricao
                     , penalidade.cod_penalidade
                     , penalidade.nom_penalidade
                     , penalidade.cod_norma
                     , norma.cod_norma
                     , norma.nom_norma
                  FROM fiscalizacao.penalidade
            INNER JOIN fiscalizacao.tipo_penalidade
                    ON tipo_penalidade.cod_tipo = penalidade.cod_tipo_penalidade
            INNER JOIN normas.norma
                    ON norma.cod_norma = penalidade.cod_norma
             LEFT JOIN ( SELECT tmp.*
                           FROM fiscalizacao.penalidade_baixa AS tmp
                     INNER JOIN ( SELECT MAX(timestamp_inicio) AS timestamp_inicio
                                       , cod_penalidade
                                    FROM fiscalizacao.penalidade_baixa
                                GROUP BY cod_penalidade
                                )AS tmp2
                             ON tmp.cod_penalidade = tmp2.cod_penalidade
                            AND tmp.timestamp_inicio = tmp2.timestamp_inicio
                       )AS penalidade_baixa
                    ON penalidade_baixa.cod_penalidade = penalidade.cod_penalidade
                 WHERE CASE WHEN ( penalidade_baixa.timestamp_inicio IS NOT NULL ) AND ( penalidade_baixa.timestamp_termino IS NULL ) THEN
                           false
                       ELSE
                           true
                       END
        ";

        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    public function recuperaListaPenalidadesPorInfracao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();
        $stSQL = $this->montaRecuperaListaPenalidadesPorInfracao( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL );
    }

    private function montaRecuperaListaPenalidadesPorInfracao($stCondicao = "", $stOrdem = "")
    {
        if ( $stCondicao )
            $stCondicao .= " AND ";

        $stCondicao .= "
            CASE WHEN ( infracao_baixa.timestamp_inicio IS NOT NULL AND infracao_baixa.timestamp_termino IS NULL ) THEN
                false
            ELSE
                true
            END
            AND CASE WHEN ( penalidade_baixa.timestamp_inicio IS NOT NULL AND penalidade_baixa.timestamp_termino IS NULL ) THEN
                false
            ELSE
                true
            END
        ";

        if ($stCondicao) {
            $stCondicao = " WHERE ".$stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY ".$stOrdem;
        }

        $stSQL  = "
               SELECT penalidade.cod_tipo_penalidade
                    , tipo_penalidade.descricao
                    , penalidade.cod_penalidade
                    , penalidade.nom_penalidade
                FROM fiscalizacao.penalidade
            LEFT JOIN ( SELECT tmp.*
                        FROM fiscalizacao.penalidade_baixa AS tmp
                    INNER JOIN ( SELECT MAX(timestamp_inicio) AS timestamp_inicio
                                    , cod_penalidade
                                    FROM fiscalizacao.penalidade_baixa
                                GROUP BY cod_penalidade
                                )AS tmp2
                            ON tmp.timestamp_inicio = tmp2.timestamp_inicio
                            AND tmp.cod_penalidade = tmp2.cod_penalidade
                    )AS penalidade_baixa
                    ON penalidade_baixa.cod_penalidade = penalidade.cod_penalidade

            INNER JOIN fiscalizacao.tipo_penalidade
                    ON tipo_penalidade.cod_tipo = penalidade.cod_tipo_penalidade
            INNER JOIN ( SELECT tmp.*
                        FROM fiscalizacao.infracao_penalidade AS tmp
                    INNER JOIN ( SELECT MAX(timestamp) AS timestamp
                                      , cod_infracao
                                   FROM fiscalizacao.infracao_penalidade
                               GROUP BY cod_infracao
                               )AS tmp2
                            ON tmp.cod_infracao = tmp2.cod_infracao
                           AND tmp.timestamp = tmp2.timestamp
                    )AS infracao_penalidade
                    ON infracao_penalidade.cod_penalidade = penalidade.cod_penalidade

             LEFT JOIN ( SELECT tmp.*
                           FROM fiscalizacao.infracao_baixa AS tmp
                     INNER JOIN ( SELECT MAX(timestamp_inicio) AS timestamp_inicio
                                       , cod_infracao
                                    FROM fiscalizacao.infracao_baixa
                                GROUP BY cod_infracao
                                )AS tmp2
                             ON tmp.timestamp_inicio = tmp2.timestamp_inicio
                            AND tmp.cod_infracao = tmp2.cod_infracao
                       )AS infracao_baixa
                    ON infracao_baixa.cod_infracao = infracao_penalidade.cod_infracao
        ";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    public function recuperaListaInfracaoPenalidadeNotificacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $obConexao   = new Conexao();
        $rsRecordSet = new RecordSet();

        $stSQL = $this->montaRecuperaInfracaoPenalidadeNotificacao( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL );
    }

    private function montaRecuperaInfracaoPenalidadeNotificacao($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " WHERE " . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL  = "    SELECT DISTINCT fai.cod_processo                         \n";
        $stSQL .= "         , fai.cod_auto_fiscalizacao                         \n";
        $stSQL .= "         , fip.cod_penalidade                                \n";
        $stSQL .= "         , fip.cod_infracao                                  \n";
        $stSQL .= "         , fai.observacao                                    \n";
        $stSQL .= "         , fni.observacao AS obs_fni                         \n";
        $stSQL .= "         , fni.observacao AS obs_ftni                        \n";
        $stSQL .= "      FROM fiscalizacao.infracao_penalidade AS fip           \n";
        $stSQL .= "INNER JOIN fiscalizacao.infracao AS fi                       \n";
        $stSQL .= "        ON fi.cod_infracao = fip.cod_infracao                \n";
        $stSQL .= "INNER JOIN fiscalizacao.penalidade AS fp                     \n";
        $stSQL .= "        ON fp.cod_penalidade = fip.cod_penalidade            \n";
        $stSQL .= " LEFT JOIN fiscalizacao.auto_infracao AS fai                 \n";
        $stSQL .= "        ON fai.cod_penalidade = fip.cod_penalidade           \n";
        $stSQL .= "       AND fai.cod_infracao = fip.cod_infracao               \n";
        $stSQL .= " LEFT JOIN fiscalizacao.notificacao_infracao AS fni          \n";
        $stSQL .= "        ON fni.cod_infracao = fi.cod_infracao                \n";
        $stSQL .= " LEFT JOIN fiscalizacao.notificacao_termo_infracao AS fnti   \n";
        $stSQL .= "        ON fnti.cod_infracao = fi.cod_infracao               \n";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }
}

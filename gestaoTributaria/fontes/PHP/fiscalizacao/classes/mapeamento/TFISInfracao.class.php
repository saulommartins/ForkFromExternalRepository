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
 * Classe de mapeamento para fiscalizacao.infracao
 * Data de Criação: 04/08/2008

 * @author Analista      : Heleno Menezes dos Santos
 * @author Desenvolvedor : Fellipe Esteves dos Santos

 * @package URBEM
 * @subpackage Mapeamento

 $Id: TFISInfracao.class.php 59612 2014-09-02 12:00:51Z gelson $

 * Casos de uso: uc-05.07.06
 */

/**
 * Classe de mapeamento para infracao.
 */
class TFISInfracao extends Persistente
{
    /**
     * Método construtor
     * @access public
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTabela( 'fiscalizacao.infracao' );

        $this->setCampoCod( 'cod_infracao' );
        $this->setComplementoChave( 'cod_norma,cod_tipo_fiscalizacao' );

        $this->addCampo( 'cod_infracao', 'integer', 'true', '', true, false );
        $this->addCampo( 'nom_infracao', 'varchar', 'true', 80, false, false );
        $this->addCampo( 'comminar', 'boolean', 'true', '', false, false );
        $this->addCampo( 'cod_tipo_fiscalizacao', 'integer', 'true', '', false, true );
        $this->addCampo( 'cod_norma', 'integer', 'true', '', false, true );
    }

    public function recuperaListaInfracoesBaixa(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaInfracoesBaixa( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    private function montaRecuperaListaInfracoesBaixa($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " AND " . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL  = "
            SELECT tipo_fiscalizacao.cod_tipo
                 , tipo_fiscalizacao.descricao
                 , infracao.cod_infracao
                 , infracao.nom_infracao
                 , infracao_baixa.motivo
                 , infracao_baixa.timestamp_inicio
                 , to_char( infracao_baixa.timestamp_inicio::date, 'dd/mm/yyyy' ) AS data_baixa
              FROM fiscalizacao.infracao
         LEFT JOIN ( SELECT tmp.*
                       FROM fiscalizacao.infracao_baixa AS tmp
                 INNER JOIN ( SELECT MAX( timestamp_inicio ) AS timestamp_inicio
                                   , cod_infracao
                                FROM fiscalizacao.infracao_baixa
                            GROUP BY cod_infracao
                            ) AS tmp2
                         ON tmp.cod_infracao = tmp2.cod_infracao
                        AND tmp.timestamp_inicio = tmp2.timestamp_inicio
                   ) AS infracao_baixa
                ON infracao_baixa.cod_infracao = infracao.cod_infracao
         LEFT JOIN fiscalizacao.infracao_baixa_processo
                ON infracao_baixa_processo.cod_infracao = infracao_baixa.cod_infracao
        INNER JOIN fiscalizacao.tipo_fiscalizacao
                ON tipo_fiscalizacao.cod_tipo = infracao.cod_tipo_fiscalizacao
             WHERE CASE WHEN ( infracao_baixa.timestamp_inicio IS NOT NULL ) AND ( infracao_baixa.timestamp_termino IS NULL ) THEN
                        true
                   ELSE
                        false
                   END
        ";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    /**
     * Recupera todos os dados para listar as Infrações em um record set.
     * @param  RecordSet &$rsRecordSet o record set retornado pela operação
     * @param  array     $arCondicao   array contendo as condições da busca
     * @param  string    $stOrdem      especifica a ordem em que os elementos são ordenados
     * @param  boolean   $boTransacao  especifica se a conexão continua aberta depois da transação
     * @return Error     erro da requisição, se houver
     * @access public
     */
    public function recuperaListaInfracoes(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaInfracoes( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    /**
     * Monta a requisição SQL para recuperaListaInfracoes.
     * @param  array  $arCondicao array contendo as condições de busca
     * @return string a requisição SQL
     * @access private
     */
    private function montaRecuperaListaInfracoes($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " AND " . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL  = "
            SELECT tipo_fiscalizacao.cod_tipo
                 , tipo_fiscalizacao.descricao
                 , infracao.cod_infracao
                 , infracao.nom_infracao
              FROM fiscalizacao.infracao
         LEFT JOIN ( SELECT tmp.*
                       FROM fiscalizacao.infracao_baixa AS tmp
                 INNER JOIN ( SELECT MAX( timestamp_inicio ) AS timestamp_inicio
                                   , cod_infracao
                                FROM fiscalizacao.infracao_baixa
                            GROUP BY cod_infracao
                            ) AS tmp2
                         ON tmp.cod_infracao = tmp2.cod_infracao
                        AND tmp.timestamp_inicio = tmp2.timestamp_inicio
                   ) AS infracao_baixa
                ON infracao_baixa.cod_infracao = infracao.cod_infracao
        INNER JOIN fiscalizacao.tipo_fiscalizacao
                ON tipo_fiscalizacao.cod_tipo = infracao.cod_tipo_fiscalizacao
             WHERE CASE WHEN ( infracao_baixa.timestamp_inicio IS NOT NULL ) AND ( infracao_baixa.timestamp_termino IS NULL ) THEN
                        false
                   ELSE
                        true
                   END
        ";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    /**
     * Retorna os dados da Infração.
     * @parma RecordSet o RecordSet onde retornar os dados
     * @param  string $stCondicao a condição de busca
     * @param  string $stOrdem    a ordenação da busca
     * @parma boolean $boTransacao executar a operação dentro de uma transação
     * @return obErro objeto contendo condição de erro, se for o caso
     * @access public
     */
    public function recuperaDadosInfracao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaDadosInfracao( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    /**
     * Monta a requisição para recuperaDadosInfracao.
     * @param  string $stCondicao condição de busca
     * @param  string $stOrdem    ordenação da busca
     * @return string a requisição SQL
     * @access private
     */
    private function montaDadosInfracao($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " WHERE " . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL  = " SELECT infracao.cod_infracao                                                \n";
        $stSQL .= "       ,infracao.nom_infracao                                                \n";
        $stSQL .= "       ,infracao.comminar                                                    \n";
        $stSQL .= "       ,infracao.cod_tipo_fiscalizacao                                       \n";
        $stSQL .= "       ,tipo_fiscalizacao.descricao                                          \n";
//      $stSQL .= "       ,penalidade.cod_norma                                                 \n";
        $stSQL .= "       ,infracao.cod_norma                                                   \n";
        $stSQL .= "       ,norma.nom_norma                                                      \n";
        $stSQL .= "  FROM fiscalizacao.infracao                                                 \n";
        $stSQL .= "       LEFT JOIN fiscalizacao.tipo_fiscalizacao                              \n";
        $stSQL .= "       ON tipo_fiscalizacao.cod_tipo = infracao.cod_tipo_fiscalizacao        \n";
        $stSQL .= "       LEFT JOIN normas.norma                                                \n";
        $stSQL .= "       ON norma.cod_norma = infracao.cod_norma                               \n";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }

    public function recuperaListaNotificacaoInfracao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
    {
        $rsRecordSet = new RecordSet();
        $obConexao   = new Conexao();

        $stSQL = $this->montaRecuperaListaNotificacaoInfracao( $stCondicao, $stOrdem );

        return $obConexao->executaSQL( $rsRecordSet, $stSQL, $boTransacao );
    }

    public function montaRecuperaListaNotificacaoInfracao($stCondicao = "", $stOrdem = "")
    {
        if ($stCondicao) {
            $stCondicao = " WHERE " . $stCondicao;
        }

        if ($stOrdem) {
            $stOrdem = " ORDER BY " . $stOrdem;
        }

        $stSQL  = " SELECT infracao.cod_infracao                                                \n";
        $stSQL .= "       ,infracao.nom_infracao                                                \n";
        $stSQL .= "  FROM fiscalizacao.infracao                                                 \n";
        $stSQL .= "       INNER JOIN fiscalizacao.auto_infracao                          \n";
        $stSQL .= "       ON notificacao_infracao.cod_infracao = infracao.cod_infracao          \n";
        $stSQL .= $stCondicao . $stOrdem;

        return $stSQL;
    }
}

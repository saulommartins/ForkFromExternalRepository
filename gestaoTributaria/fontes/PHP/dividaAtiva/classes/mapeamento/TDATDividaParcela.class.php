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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_PARCELA
    * Data de Criação: 29/09/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaParcela.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.7  2007/07/24 19:02:21  dibueno
*** empty log message ***

Revision 1.6  2007/07/11 21:17:05  cercato
consulta para listar carnes na cobranca para cancelamento.

Revision 1.5  2007/02/09 18:27:49  cercato
correcoes para divida.cobranca

Revision 1.4  2006/10/06 17:03:52  dibueno
inserção das chaves da tabela

Revision 1.3  2006/10/05 12:15:21  dibueno
Alterações nas colunas da tabela

Revision 1.2  2006/09/29 17:29:59  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaParcela extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaParcela()
    {
        parent::Persistente();
        $this->setTabela('divida.parcela');

        $this->setCampoCod('');
        $this->setComplementoChave('num_parcelamento, num_parcela');

        $this->AddCampo('num_parcelamento','integer',true,'',true,true);
        $this->AddCampo('num_parcela','integer',true,'',true,false);

        $this->AddCampo('vlr_parcela','numeric',false,'',false,false);
        $this->AddCampo('dt_vencimento_parcela','date',false,'',false,false);
        $this->AddCampo('paga','boolean',false,'',false,false);
        $this->AddCampo('cancelada','boolean',true,'',false,false);
    }

    public function recuperaListaParcelasCreditos(&$rsRecordSet, $stCondicao , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaParcelasCreditos().$stCondicao.$stOrdem;
        $this->setDebug( $stSql );
        //$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaParcelasCreditos()
    {
        $stSql  = "
            SELECT
                t.*
            FROM
                (
                SELECT
                    COALESCE( acg.cod_credito, ac.cod_credito ) AS cod_credito
                    , COALESCE( acg.cod_especie, ac.cod_especie ) AS cod_especie
                    , COALESCE( acg.cod_genero, ac.cod_genero ) AS cod_genero
                    , COALESCE( acg.cod_natureza, ac.cod_natureza ) AS cod_natureza
                    , COALESCE( acg.cod_credito, ac.cod_credito ) || '.' || COALESCE( acg.cod_especie, ac.cod_especie )  || '.' || COALESCE( acg.cod_genero, ac.cod_genero ) || '.' || COALESCE( acg.cod_natureza, ac.cod_natureza ) AS credito
                    , dpap.vl_credito
                    , dpap.cod_parcela
                    , dpap.num_parcelamento
                    , dpap.num_parcela
                    , ap.vencimento
                    , ap.valor
                    , (
                            SELECT
                                COALESCE( ddi.inscricao_municipal, dde.inscricao_economica, ddc.numcgm )
                            FROM
                                divida.divida_parcelamento AS ddp

                            INNER JOIN
                                divida.divida_cgm AS ddc
                            ON
                                ddc.cod_inscricao = ddp.cod_inscricao
                                AND ddc.exercicio = ddp.exercicio

                            LEFT JOIN
                                divida.divida_imovel AS ddi
                            ON
                                ddi.cod_inscricao = ddp.cod_inscricao
                                AND ddi.exercicio = ddp.exercicio

                            LEFT JOIN
                                divida.divida_empresa AS dde
                            ON
                                dde.cod_inscricao = ddp.cod_inscricao
                                AND dde.exercicio = ddp.exercicio

                            WHERE
                                ddp.num_parcelamento = dpap.num_parcelamento
                    )AS inscricao

                    , (
                            SELECT
                                CASE WHEN ddi.inscricao_municipal IS NOT NULL THEN
                                    'im'
                                ELSE
                                    CASE WHEN dde.inscricao_economica IS NOT NULL THEN
                                        'ie'
                                    ELSE
                                        'ic'
                                    END
                                END
                            FROM
                                divida.divida_parcelamento AS ddp

                            INNER JOIN
                                divida.divida_cgm AS ddc
                            ON
                                ddc.cod_inscricao = ddp.cod_inscricao
                                AND ddc.exercicio = ddp.exercicio

                            LEFT JOIN
                                divida.divida_imovel AS ddi
                            ON
                                ddi.cod_inscricao = ddp.cod_inscricao
                                AND ddi.exercicio = ddp.exercicio

                            LEFT JOIN
                                divida.divida_empresa AS dde
                            ON
                                dde.cod_inscricao = ddp.cod_inscricao
                                AND dde.exercicio = ddp.exercicio

                            WHERE
                                ddp.num_parcelamento = dpap.num_parcelamento
                    )AS tipo_inscricao

                    , (
                            SELECT
                                ddc.numcgm
                            FROM
                                divida.divida_parcelamento AS ddp

                            INNER JOIN
                                divida.divida_cgm AS ddc
                            ON
                                ddc.cod_inscricao = ddp.cod_inscricao
                                AND ddc.exercicio = ddp.exercicio
                     ) AS numcgm
                FROM
                    divida.parcela_arrecadacao_parcela AS dpap

                INNER JOIN
                    arrecadacao.calculo AS ac
                ON
                    ac.cod_calculo = dpap.cod_calculo

                LEFT JOIN
                    arrecadacao.calculo_grupo_credito AS acgc
                ON
                    acgc.cod_calculo = ac.cod_calculo

                LEFT JOIN
                    arrecadacao.credito_grupo AS acg
                ON
                    acg.cod_grupo = acgc.cod_grupo
                    AND acg.ano_exercicio = acgc.ano_exercicio

                INNER JOIN
                    arrecadacao.parcela AS ap
                ON
                    ap.cod_parcela = dpap.cod_parcela
            )AS t \n";

        return $stSql;
    }

    public function recuperaListaCarnesParaCancelar(&$rsRecordSet, $stCondicao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaCarnesParaCancelar().$stCondicao;
        $this->setDebug( $stSql );
        #$this->debug();
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaCarnesParaCancelar()
    {
        $stSql  = "
            SELECT DISTINCT
                ac.numeracao,
                ac.cod_convenio

            FROM
                divida.parcela_calculo AS dpc

            INNER JOIN
                arrecadacao.lancamento_calculo AS alc
            ON
                alc.cod_calculo = dpc.cod_calculo

            INNER JOIN
                arrecadacao.parcela AS ap
            ON
                ap.nr_parcela = dpc.num_parcela
                AND ap.cod_lancamento = alc.cod_lancamento

            INNER JOIN
                arrecadacao.carne AS ac
            ON
                ac.cod_parcela = ap.cod_parcela

            WHERE
        \n";

        return $stSql;
    }

    public function recuperaListaParcelasDoEstornoLote(&$rsRecordSet, $stNumeracao, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaListaParcelasDoEstornoLote($stNumeracao);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaListaParcelasDoEstornoLote($stNumeracao)
    {
        $stSql  = "
            SELECT dp.num_parcela
                 , dp.num_parcelamento
              FROM arrecadacao.carne
        INNER JOIN arrecadacao.parcela AS ap
                ON ap.cod_parcela = carne.cod_parcela
        INNER JOIN arrecadacao.lancamento_calculo
                ON lancamento_calculo.cod_lancamento = ap.cod_lancamento
        INNER JOIN divida.parcela_calculo
                ON parcela_calculo.cod_calculo = lancamento_calculo.cod_calculo
               AND parcela_calculo.num_parcela = ap.nr_parcela
        INNER JOIN divida.parcela AS dp
                ON dp.num_parcela = parcela_calculo.num_parcela
               AND dp.num_parcelamento = parcela_calculo.num_parcelamento
             WHERE dp.paga = true
               AND carne.numeracao = ".$stNumeracao;

        return $stSql;
    }

    public function recuperaTotaisParcelamento(&$rsRecordSet, $inParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaTotaisParcelamento($inParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaTotaisParcelamento($inParcelamento)
    {
        $stSql = "
            SELECT SUM(parcela.vlr_parcela) AS vlr_parcela
                 , (SELECT SUM(parcela_calculo.vl_credito)     FROM divida.parcela_calculo   WHERE parcela_calculo.num_parcelamento   = parcela.num_parcelamento ) AS vlr_credito
                 , (SELECT SUM(parcela_acrescimo.vlracrescimo) FROM divida.parcela_acrescimo WHERE parcela_acrescimo.num_parcelamento = parcela.num_parcelamento ) AS vlr_acrescimo
                 , (SELECT SUM(parcela_reducao.valor)          FROM divida.parcela_reducao   WHERE parcela_reducao.num_parcelamento   = parcela.num_parcelamento ) AS vlr_reducao
              FROM divida.parcela
             WHERE parcela.num_parcelamento = $inParcelamento
          GROUP BY parcela.num_parcelamento";

        return $stSql;
    }
}// end of class
?>

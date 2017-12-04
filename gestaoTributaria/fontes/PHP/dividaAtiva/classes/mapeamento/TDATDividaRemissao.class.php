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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_REMISSAO
    * Data de Criação: 20/08/2008

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Fernando Piccini Cercato
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaRemissao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
//include_once    ( CLA_PERSISTENTE );

class TDATDividaRemissao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaRemissao()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_remissao');

        $this->setCampoCod('');
        $this->setComplementoChave('');

        $this->AddCampo('cod_inscricao', 'integer', true, '', true, true );
        $this->AddCampo('exercicio', 'varchar', true, '4', true, true );
        $this->AddCampo('cod_norma', 'integer', true, '', false, true );
        $this->AddCampo('numcgm', 'integer', true, '', false, true );
        $this->AddCampo('dt_remissao', 'date', true, '', false, false );
        $this->AddCampo('observacao', 'text', true, '', false, false );
    }

    public function ListaLancamentosPraRemissao(&$rsRecordSet, $stFiltro, $stFuncaoCredito = "", $stValorCredito = "", $stFiltroLancamentoAtivo = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaLancamentosPraRemissao( $stFuncaoCredito, $stValorCredito, $stFiltro ).$stFiltroLancamentoAtivo;
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaListaLancamentosPraRemissao($stFuncaoCredito, $stValorCredito, $stFiltro)
    {
        //d = divida, e = economica, i = imob
        $stSql = "
        SELECT lancamento.valor
        ";

        if ($stFuncaoCredito) {
            $stSql .= " , ".$stFuncaoCredito."( lancamento.cod_lancamento, ".$stValorCredito." ) AS credito_remir ";
        }

        $stSql .= "

             , lancamento.cod_lancamento
             , CASE WHEN lancamento.situacao = 'D' THEN
                    'd'
               ELSE
                    grupo.tipo_inscricao
               END AS tipo_inscricao
             , grupo.inscricao
             , grupo.numcgm
             , grupo.cod_grupo
             , grupo.exercicio
             , divida_ativa.dt_inscricao_da

          FROM ( SELECT lancamento_calculo.cod_lancamento
                      , calculo_cgm.numcgm
                      , calculo_grupo_credito.cod_grupo
                      , calculo_grupo_credito.ano_exercicio AS exercicio
                      , COALESCE( cadastro_economico_calculo.inscricao_economica, imovel_calculo.inscricao_municipal ) AS inscricao
                      , CASE WHEN cadastro_economico_calculo.inscricao_economica IS NOT NULL THEN
                             'e'
                        ELSE
                             'i'
                        END AS tipo_inscricao
                   FROM arrecadacao.calculo_grupo_credito
             INNER JOIN arrecadacao.lancamento_calculo
                     ON lancamento_calculo.cod_calculo = calculo_grupo_credito.cod_calculo
             INNER JOIN arrecadacao.calculo_cgm
                     ON calculo_cgm.cod_calculo = lancamento_calculo.cod_calculo
              LEFT JOIN arrecadacao.imovel_calculo
                     ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo
              LEFT JOIN arrecadacao.cadastro_economico_calculo
                     ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo
                  WHERE 1 = 1
                      ".$stFiltro."
               GROUP BY lancamento_calculo.cod_lancamento
                      , calculo_cgm.numcgm
                      , calculo_grupo_credito.cod_grupo
                      , calculo_grupo_credito.ano_exercicio
                      , cadastro_economico_calculo.inscricao_economica
                      , imovel_calculo.inscricao_municipal
               )AS grupo

    INNER JOIN arrecadacao.lancamento
            ON grupo.cod_lancamento = lancamento.cod_lancamento

     LEFT JOIN ( SELECT parcela.cod_lancamento
                      , CASE WHEN ( SELECT count(*)
                                      FROM divida.parcela AS tmp
                                     WHERE tmp.num_parcelamento = parcelamento.num_parcelamento
                                       AND cancelada = true
                                  ) > 0
                              AND ( SELECT count(*)
                                      FROM divida.parcela AS tmp
                                     WHERE tmp.num_parcelamento = parcelamento.num_parcelamento
                                       AND paga = true
                                  ) = 0 THEN
                              null --camarada com cobranca cancelada
                        ELSE
                              parcelamento.numero_parcelamento --camarada com cobranca!!
                        END AS numero_parcelamento
                   FROM divida.parcelamento
             INNER JOIN ( SELECT max( parcela_origem.num_parcelamento ) AS num_parcelamento
                               , parcela.cod_lancamento
                            FROM arrecadacao.parcela
                      INNER JOIN divida.parcela_origem
                              ON parcela_origem.cod_parcela = parcela.cod_parcela
                        GROUP BY parcela.cod_lancamento
                        )AS parcela
                     ON parcelamento.num_parcelamento = parcela.num_parcelamento
                  WHERE parcelamento.numero_parcelamento <> -1
               GROUP BY parcela.cod_lancamento
                      , parcelamento.num_parcelamento
                      , parcelamento.numero_parcelamento
               )AS divida_cobranca
            ON divida_cobranca.cod_lancamento = lancamento.cod_lancamento
     LEFT JOIN ( SELECT parcela.cod_lancamento
                      , max(divida_ativa.dt_inscricao) AS dt_inscricao_da
                      , CASE WHEN divida_estorno.cod_inscricao IS NOT NULL OR divida_cancelada.cod_inscricao IS NOT NULL OR divida_remissao.cod_inscricao IS NOT NULL THEN
                            true
                        ELSE
                            false
                        END as cancelada
                   FROM arrecadacao.parcela
             INNER JOIN divida.parcela_origem
                     ON parcela_origem.cod_parcela = parcela.cod_parcela
             INNER JOIN divida.divida_parcelamento
                     ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
             INNER JOIN divida.divida_ativa
                     ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                    AND divida_ativa.exercicio = divida_parcelamento.exercicio
              LEFT JOIN divida.divida_cancelada
                     ON divida_cancelada.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_cancelada.exercicio = divida_ativa.exercicio
              LEFT JOIN divida.divida_estorno
                     ON divida_estorno.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_estorno.exercicio = divida_ativa.exercicio
              LEFT JOIN divida.divida_remissao
                     ON divida_remissao.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_remissao.exercicio = divida_ativa.exercicio
               GROUP BY parcela.cod_lancamento
                      , divida_estorno.cod_inscricao
                      , divida_cancelada.cod_inscricao
                      , divida_remissao.cod_inscricao
               )AS divida_ativa
            ON divida_ativa.cod_lancamento = lancamento.cod_lancamento
         WHERE lancamento.valor > 0
           AND lancamento.ativo = true
           AND CASE WHEN divida_ativa.cancelada = true THEN
                    false
               ELSE
                    true
               END
           AND ( lancamento.situacao = 'D' OR lancamento.situacao IS NULL )
           AND grupo.inscricao IS NOT NULL
           AND CASE WHEN divida_cobranca.numero_parcelamento IS NOT NULL THEN
                    false
               ELSE
                    true
               END
        ";

        return $stSql;
    }

    public function ListaLancamentosPraRemissaoCredito(&$rsRecordSet, $stFiltro, $stFuncaoCredito = '', $stValorCredito = '', $stFiltroLancamentoAtivo = '', $boTransacao = '')
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaListaLancamentosPraRemissaoCredito($stFuncaoCredito, $stValorCredito, $stFiltro).$stFiltroLancamentoAtivo;
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

        return $obErro;
    }

    public function montaListaLancamentosPraRemissaoCredito($stFuncaoCredito, $stValorCredito, $stFiltro)
    {
        //d = divida, e = economica, i = imob
        $stSql = "
        SELECT lancamento.valor
        ";

        if ($stFuncaoCredito) {
            $stSql .= " , ".$stFuncaoCredito."( lancamento.cod_lancamento, ".$stValorCredito." ) AS credito_remir ";
        }

        $stSql .= "

             , lancamento.cod_lancamento
             , CASE WHEN lancamento.situacao = 'D' THEN
                    'd'
               ELSE
                    grupo.tipo_inscricao
               END AS tipo_inscricao
             , grupo.inscricao
             , grupo.numcgm
             , grupo.cod_credito
             , grupo.exercicio
             , divida_ativa.dt_inscricao_da
             , divida_ativa.exercicio as exercicio_da
             , divida_ativa.cod_inscricao as cod_inscricao_da

          FROM ( SELECT lancamento_calculo.cod_lancamento
                      , calculo_cgm.numcgm
                      , calculo.cod_credito
                      , calculo.exercicio
                      , COALESCE( cadastro_economico_calculo.inscricao_economica, imovel_calculo.inscricao_municipal ) AS inscricao
                      , CASE WHEN cadastro_economico_calculo.inscricao_economica IS NOT NULL THEN
                             'e'
                        ELSE
                             'i'
                        END AS tipo_inscricao
                   FROM arrecadacao.calculo
             INNER JOIN arrecadacao.lancamento_calculo
                     ON lancamento_calculo.cod_calculo = calculo.cod_calculo
             INNER JOIN arrecadacao.calculo_cgm
                     ON calculo_cgm.cod_calculo = lancamento_calculo.cod_calculo
              LEFT JOIN arrecadacao.imovel_calculo
                     ON imovel_calculo.cod_calculo = lancamento_calculo.cod_calculo
              LEFT JOIN arrecadacao.cadastro_economico_calculo
                     ON cadastro_economico_calculo.cod_calculo = lancamento_calculo.cod_calculo
                  WHERE 1 = 1
                      ".$stFiltro."
               GROUP BY lancamento_calculo.cod_lancamento
                      , calculo_cgm.numcgm
                      , calculo.cod_credito
                      , calculo.exercicio
                      , cadastro_economico_calculo.inscricao_economica
                      , imovel_calculo.inscricao_municipal
               )AS grupo

    INNER JOIN arrecadacao.lancamento
            ON grupo.cod_lancamento = lancamento.cod_lancamento

     LEFT JOIN ( SELECT parcela.cod_lancamento
                      , CASE WHEN ( SELECT count(*)
                                      FROM divida.parcela AS tmp
                                     WHERE tmp.num_parcelamento = parcelamento.num_parcelamento
                                       AND cancelada = true
                                  ) > 0
                              AND ( SELECT count(*)
                                      FROM divida.parcela AS tmp
                                     WHERE tmp.num_parcelamento = parcelamento.num_parcelamento
                                       AND paga = true
                                  ) = 0 THEN
                              null --camarada com cobranca cancelada
                        ELSE
                              parcelamento.numero_parcelamento --camarada com cobranca!!
                        END AS numero_parcelamento
                   FROM divida.parcelamento
             INNER JOIN ( SELECT max( parcela_origem.num_parcelamento ) AS num_parcelamento
                               , parcela.cod_lancamento
                            FROM arrecadacao.parcela
                      INNER JOIN divida.parcela_origem
                              ON parcela_origem.cod_parcela = parcela.cod_parcela
                        GROUP BY parcela.cod_lancamento
                        )AS parcela
                     ON parcelamento.num_parcelamento = parcela.num_parcelamento
                  WHERE parcelamento.numero_parcelamento <> -1
               GROUP BY parcela.cod_lancamento
                      , parcelamento.num_parcelamento
                      , parcelamento.numero_parcelamento
               )AS divida_cobranca
            ON divida_cobranca.cod_lancamento = lancamento.cod_lancamento
     LEFT JOIN ( SELECT parcela.cod_lancamento
                      , divida_ativa.exercicio
                      , divida_ativa.cod_inscricao
                      , max(divida_ativa.dt_inscricao) AS dt_inscricao_da
                      , CASE WHEN divida_estorno.cod_inscricao IS NOT NULL OR divida_cancelada.cod_inscricao IS NOT NULL OR divida_remissao.cod_inscricao IS NOT NULL THEN
                            true
                        ELSE
                            false
                        END as cancelada
                   FROM arrecadacao.parcela
             INNER JOIN divida.parcela_origem
                     ON parcela_origem.cod_parcela = parcela.cod_parcela
             INNER JOIN divida.divida_parcelamento
                     ON divida_parcelamento.num_parcelamento = parcela_origem.num_parcelamento
             INNER JOIN divida.divida_ativa
                     ON divida_ativa.cod_inscricao = divida_parcelamento.cod_inscricao
                    AND divida_ativa.exercicio = divida_parcelamento.exercicio
              LEFT JOIN divida.divida_cancelada
                     ON divida_cancelada.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_cancelada.exercicio = divida_ativa.exercicio
              LEFT JOIN divida.divida_estorno
                     ON divida_estorno.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_estorno.exercicio = divida_ativa.exercicio
              LEFT JOIN divida.divida_remissao
                     ON divida_remissao.cod_inscricao = divida_ativa.cod_inscricao
                    AND divida_remissao.exercicio = divida_ativa.exercicio
               GROUP BY parcela.cod_lancamento
                      , divida_estorno.cod_inscricao
                      , divida_cancelada.cod_inscricao
                      , divida_remissao.cod_inscricao
                      , divida_ativa.exercicio
                      , divida_ativa.cod_inscricao
               )AS divida_ativa
            ON divida_ativa.cod_lancamento = lancamento.cod_lancamento
         WHERE lancamento.valor > 0
           AND lancamento.ativo = true
           AND CASE WHEN divida_ativa.cancelada = true THEN
                    false
               ELSE
                    true
               END
           AND ( lancamento.situacao = 'D' OR lancamento.situacao IS NULL )
           AND grupo.inscricao IS NOT NULL
           AND CASE WHEN divida_cobranca.numero_parcelamento IS NOT NULL THEN
                    false
               ELSE
                    true
               END
        ";

        return $stSql;
    }

    public function recuperaDadosCertidaoRemissao(&$rsRecordSet, $stNumParcelamentos, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDadosCertidaoRemissao($stNumParcelamentos);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDadosCertidaoRemissao($stNumParcelamentos)
    {
        $stSql = "
            SELECT DISTINCT
                divida.fn_busca_origem_num_parcelamento( divida_parcelamento.num_parcelamento ) as origem,
                fn_lista_numparcelamento_remissao_exercicio_origem ( divida_parcelamento.num_parcelamento ) as exercicio_original,
                CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                    'imóvel'
                ELSE
                    CASE WHEN divida_empresa.inscricao_economica IS NOT NULL THEN
                        'econômica'
                    ELSE
                        'cgm'
                    END
                END AS tipo_inscricao,
                (
                    SELECT
                        sw_cgm.nom_cgm
                    FROM
                        sw_cgm

                    WHERE
                        sw_cgm.numcgm = divida_cgm.numcgm
                )as prop,
                COALESCE ( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica, divida_cgm.numcgm ) AS inscricao,
                (
                    SELECT
                        CASE WHEN sw_cgm_pessoa_fisica.numcgm IS NOT NULL THEN
                            publico.mascara_cpf_cnpj( sw_cgm_pessoa_fisica.cpf, 'cpf' )
                        ELSE
                            publico.mascara_cpf_cnpj( sw_cgm_pessoa_juridica.cnpj, 'cnpj' )
                        END

                    FROM
                        sw_cgm

                    LEFT JOIN
                        sw_cgm_pessoa_fisica
                    ON
                        sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm

                    LEFT JOIN
                        sw_cgm_pessoa_juridica
                    ON
                        sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm

                    WHERE
                        sw_cgm.numcgm = divida_cgm.numcgm
                )as cnpj_cpf,
                CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                    arrecadacao.fn_consulta_endereco_todos( divida_imovel.inscricao_municipal, 1, 5 )
                ELSE
                    CASE WHEN divida_empresa.inscricao_economica IS NOT NULL THEN
                        arrecadacao.fn_consulta_endereco_todos( divida_empresa.inscricao_economica, 2, 5 )
                    ELSE
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 5 )
                    END
                END AS localizacao,
                CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                    arrecadacao.fn_consulta_endereco_todos( divida_imovel.inscricao_municipal, 1, 1 )
                ELSE
                    CASE WHEN divida_empresa.inscricao_economica IS NOT NULL THEN
                        arrecadacao.fn_consulta_endereco_todos( divida_empresa.inscricao_economica, 2, 1 )
                    ELSE
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 1 )
                    END
                END AS endereco,
                CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                    arrecadacao.fn_consulta_endereco_todos( divida_imovel.inscricao_municipal, 1, 2 )
                ELSE
                    CASE WHEN divida_empresa.inscricao_economica IS NOT NULL THEN
                        arrecadacao.fn_consulta_endereco_todos( divida_empresa.inscricao_economica, 2, 2 )
                    ELSE
                        arrecadacao.fn_consulta_endereco_todos( divida_cgm.numcgm, 3, 2 )
                    END
                END AS bairro,
                remissao_processo.cod_processo ||'/'|| remissao_processo.ano_exercicio AS processo,
                publico.fn_data_extenso( now()::date ) AS data_corrente

            FROM
                divida.divida_remissao

            INNER JOIN
                divida.divida_parcelamento
            ON
                divida_parcelamento.cod_inscricao = divida_remissao.cod_inscricao
                AND divida_parcelamento.exercicio = divida_remissao.exercicio

            INNER JOIN
                divida.divida_cgm
            ON
                divida_cgm.cod_inscricao = divida_remissao.cod_inscricao
                AND divida_cgm.exercicio = divida_remissao.exercicio

            LEFT JOIN
                divida.divida_empresa
            ON
                divida_empresa.cod_inscricao = divida_remissao.cod_inscricao
                AND divida_empresa.exercicio = divida_remissao.exercicio

            LEFT JOIN
                divida.divida_imovel
            ON
                divida_imovel.cod_inscricao = divida_remissao.cod_inscricao
                AND divida_imovel.exercicio = divida_remissao.exercicio

            LEFT JOIN
                divida.remissao_processo
            ON
                remissao_processo.cod_inscricao = divida_remissao.cod_inscricao
                AND remissao_processo.exercicio = divida_remissao.exercicio

            WHERE
                divida_parcelamento.num_parcelamento in ( ".$stNumParcelamentos." )
        ";

        return $stSql;
    }

    public function recuperaInscricaoTipoRemissao(&$rsRecordSet, $stNumParcelamento, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaInscricaoTipoRemissao($stNumParcelamento);
        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaInscricaoTipoRemissao($stNumParcelamento)
    {
        $stSql = "
            SELECT
                fn_lista_numparcelamento_remissao(divida_parcelamento.num_parcelamento) AS num_parcelamentos,
                divida_remissao.dt_remissao,
                CASE WHEN divida_imovel.inscricao_municipal IS NOT NULL THEN
                    1
                ELSE
                    2
                END AS tipo,
                COALESCE( divida_imovel.inscricao_municipal, divida_empresa.inscricao_economica ) AS inscricao

            FROM
                divida.divida_parcelamento

            INNER JOIN
                divida.divida_remissao
            ON
                divida_remissao.cod_inscricao = divida_parcelamento.cod_inscricao
                AND divida_remissao.exercicio = divida_parcelamento.exercicio

            LEFT JOIN
                divida.divida_imovel
            ON
                divida_imovel.cod_inscricao = divida_parcelamento.cod_inscricao
                AND divida_imovel.exercicio = divida_parcelamento.exercicio

            LEFT JOIN
                divida.divida_empresa
            ON
                divida_empresa.cod_inscricao = divida_parcelamento.cod_inscricao
                AND divida_empresa.exercicio = divida_parcelamento.exercicio

            WHERE
                divida_parcelamento.num_parcelamento = ".$stNumParcelamento;

        return $stSql;
    }
}// end of class

?>

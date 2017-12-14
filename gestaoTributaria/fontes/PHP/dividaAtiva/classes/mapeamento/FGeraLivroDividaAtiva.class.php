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
class FGeraLivroDividaAtiva extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function FGeraLivroDividaAtiva ()
    {
        parent::Persistente();
    }
    
    public function recuperaLivroDividaAtiva(&$rsRecordSet, $stFiltro)
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaLivroDividaAtiva().$stFiltro;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }
    
    public function montaRecuperaLivroDividaAtiva()
    {
        $stSql = "
        SELECT cod_inscricao as inscricao_da
             , exercicio as exercicio_da
             , inscricao_municipal
             , inscricao_economica
             , num_livro::integer as num_livro
             , num_folha::integer as num_folha
             , coalesce(exercicio_livro, '0000') as exercicio_livro
             , numcgm
             , nom_cgm
             , valor_original
             , 1 AS qtd_parcelas
             , 1 AS cod_credito
             , 1 AS cod_grupo
             , exercicio_origem
             , origem
             , 'bird' AS endereco
             , 'bird' AS bairro
             , 'bird' AS cep
             , 'bird' AS municipio
             , proc_da
             , array_to_string(
                 ARRAY(
                     SELECT recupera_acrescimo_modalidade_relatorio_divida_juros(cod_inscricao, exercicio, acrescimo.cod_acrescimo, acrescimo.cod_tipo)||':'||acrescimo.descricao_acrescimo
                       FROM divida.modalidade_acrescimo
                      INNER JOIN monetario.acrescimo
                         ON acrescimo.cod_acrescimo = modalidade_acrescimo.cod_acrescimo
                        AND acrescimo.cod_tipo = modalidade_acrescimo.cod_tipo
                      INNER JOIN divida.modalidade_vigencia
                         ON modalidade_vigencia.cod_modalidade = modalidade_acrescimo.cod_modalidade
                        AND modalidade_vigencia.timestamp = modalidade_acrescimo.timestamp
                        AND modalidade_vigencia.cod_tipo_modalidade = 1
                      GROUP BY acrescimo.cod_acrescimo, acrescimo.cod_tipo, acrescimo.descricao_acrescimo
                      ORDER BY acrescimo.cod_acrescimo, acrescimo.cod_tipo
                 ),'#'
               ) as array_acrescimos
             , 'bird' as situacao
          FROM (
             SELECT dda.cod_inscricao AS cod_inscricao
                  , ddi.inscricao_municipal
                  , dde.inscricao_economica
                  , ddproc.cod_processo||'/'||ddproc.ano_exercicio as proc_da
                  , dda.num_livro::integer
                  , dda.num_folha::integer
                  , dda.exercicio_livro
                  , dda.exercicio AS exercicio
                  , ddc.numcgm
                  , (SELECT nom_cgm FROM sw_cgm WHERE sw_cgm.numcgm = ddc.numcgm )AS nom_cgm
                  , dpor.valor AS valor_original
                  , substr(divida.fn_busca_origem_exercicio_num_parcelamento( dp.num_parcelamento ),1,14) as exercicio_origem
                  , substr(divida.fn_busca_origem_num_parcelamento_para_livro( dp.num_parcelamento ),1,16) as origem
                  , (select sum(valor) from divida.divida_acrescimo where  divida_acrescimo.cod_inscricao = dda.cod_inscricao and divida_acrescimo.exercicio= dda.exercicio and cod_tipo=3) as multa
                  , (select sum(valor) from divida.divida_acrescimo where  divida_acrescimo.cod_inscricao = dda.cod_inscricao and divida_acrescimo.exercicio= dda.exercicio and cod_tipo=2) as juros
                  , (select sum(valor) from divida.divida_acrescimo where  divida_acrescimo.cod_inscricao = dda.cod_inscricao and divida_acrescimo.exercicio= dda.exercicio and cod_tipo=1) as correcao
               FROM divida.divida_ativa AS dda
               LEFT JOIN divida.divida_processo AS ddproc
                 ON ddproc.cod_inscricao = dda.cod_inscricao
                AND ddproc.exercicio = dda.exercicio
               LEFT JOIN divida.divida_estorno AS ddest
                 ON ddest.cod_inscricao = dda.cod_inscricao
                AND ddest.exercicio = dda.exercicio
              INNER JOIN divida.divida_cgm AS ddc
                 ON ddc.cod_inscricao = dda.cod_inscricao
                AND ddc.exercicio = dda.exercicio
               LEFT JOIN divida.divida_imovel AS ddi
                 ON ddi.cod_inscricao = dda.cod_inscricao
                AND ddi.exercicio = dda.exercicio
               LEFT JOIN divida.divida_empresa AS dde
                 ON dde.cod_inscricao = dda.cod_inscricao
                AND dde.exercicio = dda.exercicio
              INNER JOIN divida.divida_parcelamento AS ddp 
                 ON ddp.cod_inscricao = dda.cod_inscricao
                AND ddp.exercicio = dda.exercicio
              INNER JOIN divida.parcelamento AS dp 
                 ON dp.num_parcelamento = ddp.num_parcelamento
                AND dp.numero_parcelamento = -1
                AND dp.exercicio = '-1'
              INNER JOIN
                    (
                        SELECT DISTINCT
                               dpo.num_parcelamento
                             , sum(dpo.valor) AS valor
                          FROM divida.parcela_origem AS dpo
                         INNER JOIN arrecadacao.parcela AS ap
                            ON ap.cod_parcela = dpo.cod_parcela
                         GROUP BY dpo.num_parcelamento
                    )AS dpor
                 ON dpor.num_parcelamento = dp.num_parcelamento
        ";
        return $stSql;
    }
}


?>
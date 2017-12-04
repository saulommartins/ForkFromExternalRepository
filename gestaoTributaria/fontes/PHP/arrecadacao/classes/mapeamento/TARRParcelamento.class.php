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
    * Classe de mapeamento da tabela ARRECADACAO.PARCELAMENTO
    * Data de Criação: 24/03/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRParcelamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.20
*/

/*
$Log$
Revision 1.6  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.LANCAMENTO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Diego Bueno Coelho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRParcelamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRParcelamento()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.lancamento');

    $this->setCampoCod('cod_lancamento');
    $this->setComplementoChave('');

    $this->AddCampo('cod_lancamento','integer',true,'',true,false);
    //$this->AddCampo('numcgm','integer',true,'',false,true);
    $this->AddCampo('vencimento','date',true,'',false,false);
    $this->AddCampo('total_parcelas','integer',true,'',false,false);
    $this->AddCampo('ativo','boolean',true,'',false,false);
    $this->AddCampo('observacao','text',true,'',false,false);
    $this->AddCampo('valor','numeric',true,'',false,false);
}

function recuperaListaConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY cod_lancamento ";
    $stSql  = $this->montaRecuperaListaConsulta($stFiltro).$stOrdem;
    $this->setDebug($stSql);
    //$this->debug();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaConsulta($stFiltro)
{
     $stSql .= " select
    BUSCA_2.*,
    ( BUSCA_2.valor + BUSCA_2.JUROS + BUSCA_2.MULTA ) as ValorTOTAL
FROM
(
    SELECT
            al.cod_lancamento::integer,
            al.total_parcelas,
            BUSCA_1.numcgm,
            BUSCA_1.nom_cgm,
            (
                case when  BUSCA_1.inscricao_municipal is not null then
                     BUSCA_1.inscricao_municipal
                else
                    BUSCA_1.inscricao_economica
                end
            ) as inscricao,
            BUSCA_1.CodGrupo,
            ap.cod_parcela::integer,
            ap.valor::numeric,
            ap.nr_parcela,
            (to_char (ap.vencimento,'dd/mm/YYYY'))::varchar as vencimento,
            case
                when ap.nr_parcela = 0 then 'Única'::VARCHAR
                else (ap.nr_parcela::varchar||'/'|| arrecadacao.fn_total_parcelas(al.cod_lancamento))::varchar
            end as info_parcela,
            carne.numeracao,
            carne.exercicio::integer,
            carnem.numeracao_migracao,
            carnem.prefixo,

            BUSCA_1.origem,
          (
               aplica_juro ( carne.numeracao, carne.exercicio::integer, ap.cod_parcela, to_char(now(),'dd/mm/yyyy') )
               /
              arrecadacao.fn_total_parcelas(al.cod_lancamento)
           )::numeric(14,2) as JUROS,
          (
               aplica_multa (carne.numeracao, carne.exercicio::integer, ap.cod_parcela, to_char(now(),'dd/mm/yyyy') )
               /
              arrecadacao.fn_total_parcelas(al.cod_lancamento)
           )::numeric(14,2) as MULTA

         FROM
            arrecadacao.lancamento al,
            arrecadacao.parcela ap
            INNER JOIN
            arrecadacao.carne_parcela as acp
            ON
            acp.cod_parcela = ap.cod_parcela
            INNER JOIN
            arrecadacao.carne as CARNE
            ON
            CARNE.cod_convenio = acp.cod_convenio and
            acp.numeracao = CARNE.numeracao
            INNER JOIN
            arrecadacao.carne_migracao CARNEM
            ON
            CARNE.numeracao = CARNEM.numeracao,


            ( SELECT   DISTINCT

                    lc.cod_lancamento,
                    cgm.numcgm,
                    cgm.nom_cgm,
                    ic.inscricao_municipal,
                    cec.inscricao_economica,
                    ( case
                        when grupo.cod_modulo is not  null then grupo.descricao ||'/'||grupo.ano_exercicio
                        else mc.descricao_credito
                        end
                    ) as origem,
                    grupo.cod_grupo as CodGrupo

                FROM
                    sw_cgm cgm
                    INNER JOIN
                    arrecadacao.calculo_cgm ccgm
                    ON
                    cgm.numcgm = ccgm.numcgm

                    INNER JOIN
                    arrecadacao.lancamento_calculo lc
                    ON
                    ccgm.cod_calculo = lc.cod_calculo

                    INNER JOIN
                    arrecadacao.calculo c
                    ON
                    c.cod_calculo = lc.cod_calculo

                        INNER JOIN   (
                                    SELECT gc.cod_grupo, gc.descricao, gc.ano_exercicio, cgc.cod_calculo, m.cod_modulo
                                            FROM arrecadacao.calculo_grupo_credito cgc
                                    INNER JOIN arrecadacao.grupo_credito gc ON gc.cod_grupo     = cgc.cod_grupo
                                    INNER JOIN administracao.modulo m       ON m.cod_modulo     = gc.cod_modulo
                                    ) as grupo ON grupo.cod_calculo = c.cod_calculo

                        LEFT JOIN arrecadacao.imovel_calculo as ic
                        ON ic.cod_calculo   = c.cod_calculo

                        LEFT JOIN arrecadacao.cadastro_economico_calculo as cec
                        ON cec.cod_calculo = c.cod_calculo
                    ,
                    monetario.credito mc
                WHERE
                    mc.cod_credito          = c.cod_credito         AND
                    mc.cod_especie          = c.cod_especie         AND
                    mc.cod_genero           = c.cod_genero          AND
                    mc.cod_natureza         = c.cod_natureza

                ". $stFiltro ."
                ORDER BY cod_lancamento
                ) as BUSCA_1

        WHERE
            al.cod_lancamento   = ap.cod_lancamento   AND
            ap.vencimento < to_char(now(),'dd/mm/yyyy') AND
            ap.nr_parcela > 0 AND
            CARNE.numeracao NOT IN ( select numeracao from arrecadacao.pagamento where numeracao = CARNE.numeracao)
            AND
            CARNE.numeracao NOT IN ( select numeracao from arrecadacao.carne_devolucao where numeracao = CARNE.numeracao)
            AND   al.cod_lancamento = BUSCA_1.cod_lancamento
            AND ap.cod_parcela not in ( select cod_parcela from arrecadacao.parcelamento_lancamento where cod_parcela = ap.cod_parcela)
        ORDER BY
            ap.cod_parcela
  ) as BUSCA_2

  \n";

    return $stSql;
}

}//fim da class
?>

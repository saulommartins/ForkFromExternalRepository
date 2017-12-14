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
    * Classe de mapeamento da função para listar os dados para Relatorio CNPJ
    * Data de Criação: 27/12/2007

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Vitor Hugo
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FARRListaRelatorioCNPJ.class.php 65763 2016-06-16 17:31:43Z evandro $

* Casos de uso: uc-05.03.24
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class FARRListaRelatorioCNPJ extends Persistente
{
function executaFuncao(&$rsRecordset, $boTransacao = "")
{
        $obErro      = new Erro;
        $obConexao   = new Conexao;

        $stSql  = $this->montaExecutaFuncao();
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL($rsRecordset,$stSql, $boTransacao );

        return $obErro;
}

function montaExecutaFuncao()
{
    $stSql  = "

--              select arrecadacao.fn_lista_relatorio_CNPJ()
--              as lista_relatorio ( cnpj varchar );
                  SELECT DISTINCT
                     cnpj
                  FROM   (

                          SELECT
                               carne.numeracao
                              , ap.vencimento
                              , acc.numcgm
                              , substring(cnpj, 1,2)||'.'||
                                substring(cnpj,3,3) ||'.'||
                                substring(cnpj,6,3) ||'/'||
                                substring(cnpj,9,4) ||'-'||
                                substring(cnpj,13,2) as cnpj

                              , ( CASE WHEN apag.numeracao is not null THEN
                                      apag.pagamento_tipo
                                  ELSE
                                      CASE WHEN acd.devolucao_data is not null THEN
                                          acd.devolucao_descricao
                                      ELSE
                                          CASE WHEN ap.nr_parcela = 0
                                                      and (ap.vencimento < '2007-12-31')
                                          THEN
                                              'Cancelada (Parcela Única vencida)'
                                          ELSE
                                              'Em Aberto'
                                          END
                                      END
                                  END
                              )::varchar as situacao

                         FROM
                             arrecadacao.carne as carne
                  ---- PARCELA
                 ---- PARCELA
                      INNER JOIN arrecadacao.parcela as ap
                      ON ap.cod_parcela = carne.cod_parcela

                      INNER JOIN arrecadacao.lancamento as al
                      ON al.cod_lancamento = ap.cod_lancamento

                      INNER JOIN arrecadacao.lancamento_calculo as alc
                      ON alc.cod_lancamento = al.cod_lancamento

                      INNER JOIN arrecadacao.calculo as ac
                      ON ac.cod_calculo = alc.cod_calculo

                      INNER JOIN arrecadacao.calculo_cgm as acc
                      ON acc.cod_calculo = ac.cod_calculo

                      INNER JOIN sw_cgm_pessoa_juridica as scpj
                      ON scpj.numcgm = acc.numcgm

                  ---- PAGAMENTO
                      LEFT JOIN (
                          SELECT
                              apag.numeracao
                              , apag.cod_convenio
                              , apag.observacao
                              , atp.pagamento as tp_pagamento
                              , apag.data_pagamento as pagamento_data
                              , to_char(apag.data_baixa,'dd/mm/YYYY') as pagamento_data_baixa
                              , app.cod_processo::varchar||'/'||app.ano_exercicio as processo_pagamento
                              , cgm.numcgm as pagamento_cgm
                              , cgm.nom_cgm as pagamento_nome
                              , atp.nom_tipo as pagamento_tipo
                              , apag.valor as pagamento_valor
                              , apag.ocorrencia_pagamento
                          FROM
                       arrecadacao.pagamento as apag
                              INNER JOIN sw_cgm as cgm
                              ON cgm.numcgm = apag.numcgm
                              INNER JOIN arrecadacao.tipo_pagamento as atp
                              ON atp.cod_tipo = apag.cod_tipo
                              LEFT JOIN arrecadacao.processo_pagamento as app
                              ON app.numeracao = apag.numeracao AND app.cod_convenio = apag.cod_convenio
                      ) as apag
                      ON apag.numeracao = carne.numeracao
                      AND apag.cod_convenio = carne.cod_convenio

                ---- CARNE DEVOLUCAO
                      LEFT JOIN (
                          SELECT
                              acd.numeracao
                              , acd.cod_convenio
                              , acd.dt_devolucao as devolucao_data
                              , amd.descricao as devolucao_descricao
                          FROM
                              arrecadacao.carne_devolucao as acd
                              INNER JOIN arrecadacao.motivo_devolucao as amd
                              ON amd.cod_motivo = acd.cod_motivo
                      ) as acd
                      ON acd.numeracao = carne.numeracao
                      AND acd.cod_convenio = carne.cod_convenio


                  WHERE

                      apag.pagamento_data is null and
                      devolucao_data is null      and
                      scpj.cnpj is not null       and
                      ap.vencimento < now()
                  ) as consulta
                   WHERE situacao = 'Em Aberto'
                    ";

    return $stSql;

}
}
?>

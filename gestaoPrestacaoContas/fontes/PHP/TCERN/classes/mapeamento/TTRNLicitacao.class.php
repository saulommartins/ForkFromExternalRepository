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
    * Extensão da Classe de mapeamento
    * Data de Criação: 12/10/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Mapeamento

    $Id: $

    * Casos de uso: uc-06.06.00
*/

/*
$Log$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 12/10/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTRNLicitacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTRNLicitacao()
{
    $this->setEstrutura( array() );
    $this->setEstruturaAuxiliar( array() );
    $this->setDado('exercicio',Sessao::getExercicio());
}

function montaRecuperaRelacionamento()
{
    $stSql .= "
          SELECT   lic.cod_licitacao
                  ,lic.cod_modalidade
                  ,obj.descricao as objeto
                  ,lic.cod_modalidade
                  ,lic.cod_processo||lic.exercicio_processo as cod_processo
                  ,to_char(lic.timestamp,'dd/mm/yyyy') as data_emissao
                  ,to_char(publicacao_edital.data_publicacao, 'dd/mm/yyyy') as data_publicacao
                  ,func.fundamento_legal
                  ,CASE   WHEN lic.cod_modalidade = 1 THEN 3
                          WHEN lic.cod_modalidade = 2 THEN 2
                          WHEN lic.cod_modalidade = 3 THEN 1
                          WHEN lic.cod_modalidade = 5 THEN 4
                          WHEN lic.cod_modalidade = 8 THEN 7
                          WHEN lic.cod_modalidade = 9 THEN 9
                  END AS modalidade
                  ,REPLACE(mapa_valor.vl_total, '.', '') as valor_licitacao
          FROM     licitacao.licitacao    as lic

                  LEFT JOIN tcern.processo_fundamento as func
                  ON (
                      lic.exercicio = func.exercicio AND lic.cod_entidade = func.cod_entidade AND lic.cod_modalidade = func.cod_modalidade AND lic.cod_licitacao = func.cod_licitacao
                  )

                  LEFT JOIN licitacao.edital
                  ON  lic.cod_licitacao  = edital.cod_licitacao
                  AND lic.cod_modalidade = edital.cod_modalidade
                  AND lic.cod_entidade   = edital.cod_entidade
                  AND lic.exercicio      = edital.exercicio_licitacao

                  LEFT JOIN licitacao.publicacao_edital
                  ON  edital.num_edital = publicacao_edital.num_edital
                  AND edital.exercicio  = publicacao_edital.exercicio

                  INNER JOIN (
                            SELECT  mapa_item.cod_mapa
                                 ,  mapa_item.exercicio
                                 ,  SUM(mapa_item.vl_total) - COALESCE(SUM(mapa_item_anulacao.vl_total),0) as vl_total

                              FROM  compras.mapa_item

                         LEFT JOIN  compras.mapa_item_anulacao
                                ON  mapa_item_anulacao.exercicio             = mapa_item.exercicio
                               AND  mapa_item_anulacao.cod_mapa              = mapa_item.cod_mapa
                               AND  mapa_item_anulacao.exercicio_solicitacao = mapa_item.exercicio_solicitacao
                               AND  mapa_item_anulacao.cod_entidade          = mapa_item.cod_entidade
                               AND  mapa_item_anulacao.cod_solicitacao       = mapa_item.cod_solicitacao
                               AND  mapa_item_anulacao.cod_centro            = mapa_item.cod_centro
                               AND  mapa_item_anulacao.cod_item              = mapa_item.cod_item
                               AND  mapa_item_anulacao.lote                  = mapa_item.lote

                             WHERE  mapa_item.cod_mapa IS NOT NULL
                          GROUP BY  mapa_item.cod_mapa
                                 ,  mapa_item.exercicio
                  ) as mapa_valor
                  ON  lic.cod_mapa = mapa_valor.cod_mapa
                  AND lic.exercicio_mapa = mapa_valor.exercicio

                  ,compras.objeto         as obj
                  ,compras.modalidade     as mod
          WHERE   lic.exercicio = ".$this->getDado('exercicio')."
          AND     lic.cod_objeto     = obj.cod_objeto
          AND     lic.cod_modalidade = mod.cod_modalidade
          AND     to_char(lic.timestamp,'dd/mm/yyyy') >= '".$this->getDado('dtInicial')."'
          AND     to_char(lic.timestamp,'dd/mm/yyyy') <= '".$this->getDado('dtFinal')."' ";
          if ($this->getDado('inCodEntidade')) {
            $stSql .= "\n          AND     lic.cod_entidade IN (".$this->getDado('inCodEntidade').") ";
          }

    return $stSql;
}

function recuperaDadosParticipantes(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosParticipantes().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosParticipantes()
{
    $stSql .= "
       SELECT   col.cod_licitacao
               ,col.cod_modalidade
               ,cgm.nom_cgm
               ,case when  pf.numcgm is not null then 2
                      else 1
               end as pf_pj
               ,case when  pf.cpf is not null  then pf.cpf
                     when pj.cnpj is not null  then pj.cnpj
                      else ''
               end as cpf_cnpj
               ,count(col.cod_item) as quant_itens
               ,lpad(replace(sum(cfi.vl_cotacao),'.',''),14,'0') as valor_total
               ,lic.cod_processo||lic.exercicio_processo as cod_processo
       FROM     licitacao.cotacao_licitacao        as col
               ,licitacao.licitacao                as lic
               ,compras.cotacao_fornecedor_item    as cfi
               ,sw_cgm                             as cgm
               LEFT JOIN   sw_cgm_pessoa_fisica    as pf
                   ON ( cgm.numcgm = pf.numcgm )
               LEFT JOIN   sw_cgm_pessoa_juridica  as pj
                   ON ( cgm.numcgm = pj.numcgm )
       WHERE   col.exercicio_cotacao   = ".$this->getDado('exercicio')."
       AND     col.exercicio_cotacao   = cfi.exercicio
       AND     col.lote                = cfi.lote
       AND     col.cod_cotacao         = cfi.cod_cotacao
       AND     col.cgm_fornecedor      = cfi.cgm_fornecedor
       AND     col.cod_item            = cfi.cod_item
       AND     col.cgm_fornecedor      = cgm.numcgm
       AND     col.exercicio_licitacao = lic.exercicio
       AND     col.cod_entidade        = lic.cod_entidade
       AND     col.cod_modalidade      = lic.cod_modalidade
       AND     col.cod_licitacao       = lic.cod_licitacao
       AND     to_char(lic.timestamp,'dd/mm/yyyy') >= '".$this->getDado('dtInicial')."'
       AND     to_char(lic.timestamp,'dd/mm/yyyy') <= '".$this->getDado('dtFinal')."'
       GROUP BY col.cod_licitacao
               ,col.cod_modalidade
               ,cgm.nom_cgm
               ,pf.numcgm
               ,pf.cpf
               ,pj.cnpj
               ,lic.cod_processo,lic.exercicio_processo
    ";

    return $stSql;
}

function recuperaDadosContratos(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosContratos().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosContratos()
{
    $stSql .= "
        SELECT   cont.exercicio
            ,    case when  pf.cpf is not null  then pf.cpf
                      when pj.cnpj is not null  then pj.cnpj
                      else ''
                 end as cpf_cnpj
            ,    cont.num_contrato
            ,    contrato_licitacao.cod_licitacao
            ,    lpad(replace(cont.valor_contratado,'.',''),14,'0') as valor_contratado
            ,    to_char(cont.inicio_execucao,'dd/mm/yyyy') as inicio_vigencia
            ,    to_char(cont.fim_execucao,'dd/mm/yyyy') as fim_vigencia
            ,    to_char(cont.dt_assinatura,'dd/mm/yyyy') as data_assinatura
            ,    to_char(puco.dt_publicacao,'dd/mm/yyyy') as data_publicacao
            ,    (SELECT lic.cod_processo||lic.exercicio_processo
                    FROM licitacao.licitacao as lic
                   WHERE contrato_licitacao.exercicio     = lic.exercicio
                     AND contrato_licitacao.cod_entidade  = lic.cod_entidade
                     AND contrato_licitacao.cod_modalidade= lic.cod_modalidade
                     AND contrato_licitacao.cod_licitacao = lic.cod_licitacao
                     AND to_char(lic.timestamp,'dd/mm/yyyy') >= '".$this->getDado('dtInicial')."'
                     AND to_char(lic.timestamp,'dd/mm/yyyy') <= '".$this->getDado('dtFinal')."'
                  ) as cod_processo

          FROM   licitacao.contrato as cont
     LEFT JOIN   sw_cgm_pessoa_fisica as pf
            ON   cont.cgm_contratado = pf.numcgm
     LEFT JOIN   sw_cgm_pessoa_juridica as pj
            ON   cont.cgm_contratado = pj.numcgm

     LEFT JOIN   licitacao.publicacao_contrato as puco
            ON   cont.exercicio     = puco.exercicio
           AND   cont.cod_entidade  = puco.cod_entidade
           AND   cont.num_contrato  = puco.num_contrato

    INNER JOIN   licitacao.contrato_licitacao
            ON   contrato_licitacao.num_contrato = cont.num_contrato
           AND   contrato_licitacao.cod_entidade = cont.cod_entidade
           AND   contrato_licitacao.exercicio    = cont.exercicio

         WHERE   cont.exercicio                  = '".$this->getDado('exercicio')."'

      ORDER BY   cont.num_contrato

    ";

    return $stSql;
}

function recuperaDadosAditivo(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosAditivo().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosAditivo()
{
    $stSql .= "
        SELECT  contrato.exercicio
            ,   contrato.num_contrato
            ,   contrato_aditivos.num_aditivo
            ,   objeto.descricao as objeto
            ,   lpad(replace(contrato.valor_contratado,'.',''),14,'0') as valor_contratado
            ,   to_char(contrato.dt_assinatura,'dd/mm/yyyy') as data_assinatura
            ,   to_char(contrato_aditivos.dt_vencimento,'dd/mm/yyyy') as data_vencimento
            ,   to_char(publicacao_contrato.dt_publicacao,'dd/mm/yyyy') as data_publicacao
            ,   to_char(contrato.vencimento,'dd/mm/yyyy') as data_termino
            ,   processo_fundamento.fundamento_legal

          FROM  licitacao.contrato

     LEFT JOIN  licitacao.publicacao_contrato
            ON  contrato.exercicio     = publicacao_contrato.exercicio
           AND  contrato.cod_entidade  = publicacao_contrato.cod_entidade
           AND  contrato.num_contrato  = publicacao_contrato.num_contrato

    INNER JOIN  licitacao.contrato_licitacao
            ON  contrato_licitacao.num_contrato = contrato.num_contrato
           AND  contrato_licitacao.cod_entidade = contrato.cod_entidade
           AND  contrato_licitacao.exercicio    = contrato.exercicio

    INNER JOIN  licitacao.contrato_aditivos
            ON  contrato_aditivos.cod_entidade = contrato.cod_entidade
           AND  contrato_aditivos.num_contrato = contrato.num_contrato

    INNER JOIN  licitacao.licitacao
            ON  licitacao.exercicio      = contrato_licitacao.exercicio_licitacao
           AND  licitacao.cod_entidade   = contrato_licitacao.cod_entidade
           AND  licitacao.cod_modalidade = contrato_licitacao.cod_modalidade
           AND  licitacao.cod_licitacao  = contrato_licitacao.cod_licitacao

     LEFT JOIN  tcern.processo_fundamento
            ON  processo_fundamento.exercicio      = licitacao.exercicio
           AND  processo_fundamento.cod_entidade   = licitacao.cod_entidade
           AND  processo_fundamento.cod_modalidade = licitacao.cod_modalidade
           AND  processo_fundamento.cod_licitacao  = licitacao.cod_licitacao

    INNER JOIN  compras.objeto
            ON  objeto.cod_objeto = licitacao.cod_objeto

         WHERE  contrato.exercicio   = '".$this->getDado('exercicio')."'
           AND  to_char(licitacao.timestamp,'dd/mm/yyyy') >= '".$this->getDado('dtInicial')."'
           AND  to_char(licitacao.timestamp,'dd/mm/yyyy') <= '".$this->getDado('dtFinal')."'
    ";

    return $stSql;
}

}

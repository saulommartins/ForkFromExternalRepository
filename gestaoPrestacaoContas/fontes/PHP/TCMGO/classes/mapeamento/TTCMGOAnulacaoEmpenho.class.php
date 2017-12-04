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
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAnulacaoEmpenho.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-06.04.00
*/

include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php" );

class TTCMGOAnulacaoEmpenho extends TEmpenhoEmpenho
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TTCMGOAnulacaoEmpenho()
    {
        parent::TEmpenhoEmpenho();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }
    public function montaRecuperaTodos()
    {
        $stSQL = "
                    select '10' as tipo_registro
                         , programa.num_programa as cod_programa
                         , despesa.num_orgao
                         , despesa.num_unidade
                         , despesa.cod_funcao
                         , despesa.cod_subfuncao
                         , substr( acao.num_acao::varchar  ,1,'1') as natureza_acao
                         , acao.num_acao as num_projeto_atividade
                         , empenho.cod_empenho
                         , TO_CHAR(empenho_anulado.timestamp,'dd/mm/yyyy')  as data_anulacao
                         , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp )
                         , TO_CHAR(empenho.dt_empenho,'dd/mm/yyyy') as dt_empenho
                         , ( select sum ( vl_total )
                               from empenho.item_pre_empenho
                               where item_pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho
                                 and item_pre_empenho.exercicio       = empenho.exercicio ) as vl_original
                         , ( select sum ( vl_anulado )
                               from empenho.empenho_anulado_item
                              where empenho_anulado_item.exercicio    = empenho_anulado.exercicio
                                and empenho_anulado_item.cod_entidade = empenho_anulado.cod_entidade
                                and empenho_anulado_item.cod_empenho  = empenho_anulado.cod_empenho
                                and empenho_anulado_item.timestamp    = empenho_anulado.timestamp ) as vl_anulado
                         , ( select cpf
                               from sw_cgm_pessoa_fisica
                              where sw_cgm_pessoa_fisica.numcgm =sw_cgm.numcgm ) as cpf
                         , ( select cnpj
                               from sw_cgm_pessoa_juridica
                              where sw_cgm_pessoa_juridica.numcgm =sw_cgm.numcgm ) as cnpj
                         , sw_cgm.nom_cgm
                         , pre_empenho.descricao
                         , substr(replace(conta_despesa.cod_estrutural,'.',''),1,6) as elemento_despesa
                         , CASE WHEN ( elemento_de_para.estrutural IS NOT NULL )
                                THEN substr(replace(elemento_de_para.estrutural,'.',''),7,2)
                                ELSE '00'
                            END AS sub_elemento_despesa ";
      if (Sessao::getExercicio() > 2012) {
        $stSQL .= " , sw_cgm.cod_pais ";
      }
      $stSQL .= "
                    from empenho.empenho
                    join empenho.pre_empenho
                      on ( empenho.exercicio       = pre_empenho.exercicio
                     and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )
                    join empenho.pre_empenho_despesa
                      on ( pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     and   pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
                    join orcamento.despesa
                      on ( pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     and   pre_empenho_despesa.exercicio   = despesa.exercicio )
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa       = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = acao.cod_programa
                    join orcamento.conta_despesa
                          on ( pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                         and   pre_empenho_despesa.exercicio = conta_despesa.exercicio )
                    join empenho.empenho_anulado
                      on ( empenho.exercicio    = empenho_anulado.exercicio
                     and   empenho.cod_entidade = empenho_anulado.cod_entidade
                     and   empenho.cod_empenho  = empenho_anulado.cod_empenho )
                    join sw_cgm
                      on ( sw_cgm.numcgm = pre_empenho.cgm_beneficiario )
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.exercicio = conta_despesa.exercicio
                     AND elemento_de_para.cod_conta = conta_despesa.cod_conta
                   where empenho.exercicio = '". $this->getDado('exercicio')."'
                        and empenho_anulado.timestamp::date BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
        ";
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSQL .= "\n and empenho.cod_entidade in  ( ". $this->getDado ( 'stEntidades' ) ." ) ";
        }

        return $stSQL;
    }

    public function recuperaEmpenhoAnuladoFonte(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoAnuladoFonte",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoAnuladoFonte()
    {
        $stSQL = "
                  select '11' as tipo_registro
                       , programa.num_programa as cod_programa
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , substr( acao.num_acao::varchar  ,1,'1') as natureza_acao
                       , acao.num_acao as num_projeto_atividade
                       , substr(replace(conta_despesa.cod_estrutural::varchar,'.',''),1,6) as elemento_despesa
                       , CASE WHEN ( elemento_de_para.estrutural IS NOT NULL )
                              THEN substr(replace(elemento_de_para.estrutural,'.',''),7,2)
                              ELSE '00'
                         END AS sub_elemento_despesa
                       , empenho.cod_empenho
                       , (recurso_direto.codigo_tc) AS cod_fonte
                       , SUM(item_pre_empenho.vl_total) as vl_empenho_fonte
                       , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) as numero_anulacao_empenho
                       , SUM(empenho_anulado_item.vl_anulado) as vl_anulacao_fonte
                       , TO_CHAR(empenho_anulado.timestamp,'dd/mm/yyyy')  as data_anulacao
                    from empenho.empenho
                    join empenho.pre_empenho
                      on ( empenho.exercicio       = pre_empenho.exercicio
                     and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )

                     INNER JOIN  empenho.item_pre_empenho
                      ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                        AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    join empenho.pre_empenho_despesa
                      on ( pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     and   pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
                    join orcamento.despesa
                      on ( pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     and   pre_empenho_despesa.exercicio   = despesa.exercicio )
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa       = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = acao.cod_programa
                    join orcamento.conta_despesa
                      on ( pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     and   pre_empenho_despesa.exercicio = conta_despesa.exercicio )
                    join empenho.empenho_anulado
                      on ( empenho.exercicio    = empenho_anulado.exercicio
                     and   empenho.cod_entidade = empenho_anulado.cod_entidade
                     and   empenho.cod_empenho  = empenho_anulado.cod_empenho )

                      join empenho.empenho_anulado_item
                      on ( item_pre_empenho.exercicio = empenho_anulado_item.exercicio
                     and   item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                     and   item_pre_empenho.num_item = empenho_anulado_item.num_item )

                    join orcamento.recurso
                      on ( despesa.exercicio = recurso.exercicio
                     and   despesa.cod_recurso = recurso.cod_recurso )
                    join orcamento.recurso_direto
                      on recurso_direto.exercicio = recurso.exercicio
                     and recurso_direto.cod_recurso = recurso.cod_recurso
               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
                    where empenho.exercicio = '". $this->getDado('exercicio')."'
                      and empenho_anulado.timestamp::date BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
        ";
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSQL .= "\n and empenho.cod_entidade in  ( ". $this->getDado ( 'stEntidades' ) ." ) ";
        }
        $stSQL.= " group by programa.num_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao,conta_despesa.cod_estrutural, natureza_acao, num_projeto_atividade, elemento_despesa, empenho.cod_empenho, recurso_direto.codigo_tc, elemento_de_para.estrutural,empenho_anulado.timestamp,empenho.exercicio,empenho.cod_entidade ";

        return $stSQL;

    }

    public function recuperaEmpenhoAnuladoContrato(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEmpenhoAnuladoContrato",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoAnuladoContrato()
    {
        $stSql = "
              select '13' as tipo_registro
                       , programa.num_programa as cod_programa
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , substr( acao.num_acao::varchar  ,1,'1') as natureza_acao
                       , acao.num_acao as num_projeto_atividade
                       , substr(replace(conta_despesa.cod_estrutural,'.',''),1,6) as elemento_despesa
                       , CASE WHEN ( elemento_de_para.estrutural IS NOT NULL )
                              THEN substr(replace(elemento_de_para.estrutural,'.',''),7,2)
                              ELSE '00'
                         END AS sub_elemento_despesa
                       , empenho.cod_empenho
                       , contrato.nro_contrato
                       , contrato.exercicio
                       , '1' as tipo_ajuste
                       , SUM(empenho_anulado_item.vl_anulado) as vl_anulacao
                       , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) as numero_anulacao_empenho
                       , TO_CHAR(empenho_anulado.timestamp,'dd/mm/yyyy')  as data_anulacao

                    from empenho.empenho
                      join empenho.pre_empenho
                      on ( empenho.exercicio       = pre_empenho.exercicio
                     and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )

                     INNER JOIN  empenho.item_pre_empenho
                      ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                        AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    join empenho.pre_empenho_despesa
                      on ( pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     and   pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
                    join orcamento.despesa
                      on ( pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     and   pre_empenho_despesa.exercicio   = despesa.exercicio )
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa       = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = acao.cod_programa
                    join orcamento.conta_despesa
                      on ( pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     and   pre_empenho_despesa.exercicio = conta_despesa.exercicio )
                    join empenho.empenho_anulado
                      on ( empenho.exercicio    = empenho_anulado.exercicio
                     and   empenho.cod_entidade = empenho_anulado.cod_entidade
                     and   empenho.cod_empenho  = empenho_anulado.cod_empenho )
              INNER JOIN  tcmgo.contrato_empenho
                      ON  contrato_empenho.cod_empenho = empenho.cod_empenho
                     AND  contrato_empenho.exercicio_empenho = empenho.exercicio
                     AND  contrato_empenho.cod_entidade = empenho.cod_entidade
              INNER JOIN  tcmgo.contrato
                      ON contrato.cod_contrato = contrato_empenho.cod_contrato
                     AND contrato.exercicio = contrato_empenho.exercicio
                     AND contrato.cod_entidade = contrato_empenho.cod_entidade
                    join empenho.empenho_anulado_item
                      on ( item_pre_empenho.exercicio = empenho_anulado_item.exercicio
                     and   item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                     and   item_pre_empenho.num_item = empenho_anulado_item.num_item )
                     
                JOIN  tcmgo.empenho_modalidade
		ON  empenho_modalidade.exercicio = empenho.exercicio
		AND  empenho_modalidade.cod_entidade = empenho.cod_entidade
		AND  empenho_modalidade.cod_empenho = empenho.cod_empenho

		LEFT JOIN tcmgo.processos
		ON  processos.cod_empenho  = empenho.cod_empenho
		AND  processos.cod_entidade = empenho.cod_entidade
		AND  processos.exercicio    = empenho.exercicio

               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
                    where empenho.exercicio =  '". $this->getDado('exercicio')."'
                      and empenho.dt_empenho::date BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
        ";
        if ( $this->getDado ( 'stEntidades' ) ) {
             $stSql .= "\n and empenho.cod_entidade in  ( ". $this->getDado ( 'stEntidades' ) ." ) ";
        }
         $stSql.= " \n AND (
                            empenho_modalidade.cod_modalidade='00'
                            and empenho_modalidade.cod_modalidade <> '10' AND empenho_modalidade.cod_modalidade <> '11'
                            and processos.numero_processo IS NULL
                            and processos.numero_processo IS NULL
                            and processos.processo_administrativo IS NULL
                            and contrato.nro_contrato IS NULL
                    ) 
                    \n group by programa.num_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao,conta_despesa.cod_estrutural, natureza_acao, num_projeto_atividade, elemento_despesa, empenho.cod_empenho,  elemento_de_para.estrutural,contrato.nro_contrato,contrato.exercicio,empenho.exercicio,empenho.cod_entidade,empenho_anulado.timestamp";

        return  $stSql;

    }

     function recuperaEmpenhoAnuladoObra(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
     {
        return $this->executaRecupera("montaRecuperaEmpenhoAnuladoObra",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEmpenhoAnuladoObra()
    {
        $stSql = "
            select '12' as tipo_registro
                       , programa.num_programa as cod_programa
                       , despesa.num_orgao
                       , despesa.num_unidade
                       , despesa.cod_funcao
                       , despesa.cod_subfuncao
                       , substr( acao.num_acao::varchar  ,1,'1') as natureza_acao
                       , acao.num_acao as num_projeto_atividade
                       , substr(replace(conta_despesa.cod_estrutural::varchar,'.',''),1,6) as elemento_despesa
                       , CASE WHEN ( elemento_de_para.estrutural IS NOT NULL )
                              THEN substr(replace(elemento_de_para.estrutural::varchar,'.',''),7,2)
                              ELSE '00'
                         END AS sub_elemento_despesa
                       , empenho.cod_empenho
                       ,  (LPAD(obra_empenho.cod_obra::varchar,4,'0') || obra_empenho.ano_obra) AS cod_obra
                       , SUM(empenho_anulado_item.vl_anulado) as vl_anulacao
                       , TO_CHAR(empenho_anulado.timestamp,'dd/mm/yyyy')  as data_anulacao
                       , tc.numero_anulacao_empenho( empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho, empenho_anulado.timestamp ) as numero_anulacao_empenho

                    from empenho.empenho
                      join empenho.pre_empenho
                      on ( empenho.exercicio       = pre_empenho.exercicio
                     and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )

                     INNER JOIN  empenho.item_pre_empenho
                      ON  item_pre_empenho.exercicio = pre_empenho.exercicio
                        AND  item_pre_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho

                    join empenho.pre_empenho_despesa
                      on ( pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho
                     and   pre_empenho_despesa.exercicio       = pre_empenho.exercicio )
                    join orcamento.despesa
                      on ( pre_empenho_despesa.cod_despesa = despesa.cod_despesa
                     and   pre_empenho_despesa.exercicio   = despesa.exercicio )
                    JOIN orcamento.despesa_acao
                      ON despesa_acao.exercicio_despesa = despesa.exercicio
                     AND despesa_acao.cod_despesa       = despesa.cod_despesa
                    JOIN ppa.acao
                      ON acao.cod_acao = despesa_acao.cod_acao
                    JOIN ppa.programa
                      ON programa.cod_programa = acao.cod_programa
                    join orcamento.conta_despesa
                      on ( pre_empenho_despesa.cod_conta = conta_despesa.cod_conta
                     and   pre_empenho_despesa.exercicio = conta_despesa.exercicio )
                    join empenho.empenho_anulado
                      on ( empenho.exercicio    = empenho_anulado.exercicio
                     and   empenho.cod_entidade = empenho_anulado.cod_entidade
                     and   empenho.cod_empenho  = empenho_anulado.cod_empenho )
             JOIN  tcmgo.obra_empenho
                  ON  obra_empenho.cod_entidade = empenho.cod_entidade
              AND  obra_empenho.cod_empenho = empenho.cod_empenho
                  AND  obra_empenho.exercicio = empenho.exercicio
                      join empenho.empenho_anulado_item
                      on ( item_pre_empenho.exercicio = empenho_anulado_item.exercicio
                     and   item_pre_empenho.cod_pre_empenho = empenho_anulado_item.cod_pre_empenho
                     and   item_pre_empenho.num_item = empenho_anulado_item.num_item )


               LEFT JOIN tcmgo.elemento_de_para
                      ON elemento_de_para.cod_conta = conta_despesa.cod_conta
                     AND elemento_de_para.exercicio = conta_despesa.exercicio
                    where empenho.exercicio = '2011'
                      and empenho.dt_empenho::date BETWEEN to_date( '".$this->getDado('dtInicio')."', 'dd/mm/yyyy' ) AND to_date( '".$this->getDado('dtFim')."', 'dd/mm/yyyy' )
        ";
        if ( $this->getDado ( 'stEntidades' ) ) {
            $stSql.= "\n and empenho.cod_entidade in  ( ". $this->getDado ( 'stEntidades' ) ." ) ";
        }
        $stSql.= " group by programa.num_programa, despesa.num_orgao, despesa.num_unidade, despesa.cod_funcao, despesa.cod_subfuncao,conta_despesa.cod_estrutural, natureza_acao, num_projeto_atividade, elemento_despesa, empenho.cod_empenho,elemento_de_para.estrutural,obra_empenho.cod_obra,obra_empenho.ano_obra,empenho_anulado.timestamp,empenho.exercicio , empenho.cod_entidade,  empenho.cod_empenho";

        return $stSql;

    }

}

?>

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
    * Classe de mapeamento da tabela pessoal.aposentadoria
    * Data de Criação: 21/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-15 15:00:34 -0300 (Sex, 15 Jun 2007) $

    * Casos de uso: uc-04.04.21
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.aposentadoria
  * Data de Criação: 21/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalAposentadoria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAposentadoria()
{
    parent::Persistente();
    $this->setTabela("pessoal.aposentadoria");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_contrato'     ,'integer'      ,true ,''    ,true,'TPessoalContratoServidor');
    $this->AddCampo('timestamp'        ,'timestamp_now',true ,''    ,true,false);
    $this->AddCampo('cod_enquadramento','integer'      ,true ,''    ,false,'TPessoalClassificacaoEnquadramento');
    $this->AddCampo('cod_classificacao','integer'      ,true ,''    ,false,'TPessoalClassificacaoEnquadramento');
    $this->AddCampo('dt_requirimento'  ,'date'         ,true ,''    ,false,false);
    $this->AddCampo('num_processo_tce' ,'varchar'      ,true ,'10'  ,false,false);
    $this->AddCampo('dt_concessao'     ,'date'         ,true ,''    ,false,false);
    $this->AddCampo('percentual'       ,'numeric'      ,true ,'6,2' ,false,false);
    $this->AddCampo('dt_publicacao'    ,'date'         ,true ,''    ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT aposentadoria.*                                               \n";
    $stSql .= "     , to_char(aposentadoria.dt_requirimento,'dd/mm/yyyy') as data_requirimento                                               \n";
    $stSql .= "     , to_char(aposentadoria.dt_concessao,'dd/mm/yyyy') as data_concessao                                               \n";
    $stSql .= "     , to_char(aposentadoria.dt_publicacao,'dd/mm/yyyy') as data_publicacao                                               \n";
    $stSql .= "  FROM pessoal.aposentadoria                                         \n";
    $stSql .= "     , (SELECT cod_contrato                                          \n";
    $stSql .= "             , max(timestamp) as timestamp                           \n";
    $stSql .= "          FROM pessoal.aposentadoria                                 \n";
    $stSql .= "        GROUP BY cod_contrato) as max_aposentadoria                  \n";
    $stSql .= " WHERE aposentadoria.cod_contrato = max_aposentadoria.cod_contrato   \n";
    $stSql .= "   AND aposentadoria.timestamp = max_aposentadoria.timestamp         \n";
    $stSql .= "   AND aposentadoria.cod_contrato::varchar||aposentadoria.timestamp NOT IN (SELECT cod_contrato::varchar||max(timestamp_aposentadoria)
                                                                                    FROM pessoal.aposentadoria_excluida
                                                                                  GROUP BY cod_contrato) \n";

    return $stSql;
}

function recuperaAposentadoriaEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAposentadoriaEsfinge().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAposentadoriaEsfinge()
{
    $stSql = "
        select contrato_servidor_caso_causa_norma.cod_norma
        , norma.dt_assinatura
        , norma.dt_publicacao
        , norma.descricao
        , aposentadoria.cod_enquadramento
        , contrato.registro
        , '01' as um
        , case when contrato_servidor_especialidade_cargo.cod_especialidade is not null then '2'||contrato_servidor.cod_cargo||contrato_servidor_especialidade_cargo.cod_especialidade
            else '1'||contrato_servidor.cod_cargo
        end as cod_cargo
        , case when timestamp_criacao_especialidade.timestamp is not null then to_char( timestamp_criacao_especialidade.timestamp, 'dd/mm/yyyy' )
        else to_char( timestamp_criacao_cargo.timestamp, 'dd/mm/yyyy' )
        end as timestamp_criacao_cargo
        , contrato_servidor_padrao.cod_padrao
        , cid.sigla
        , case servidor.cod_estado_civil
                when 5 then 3
                when 6 then 4
                when 3 then 5
                when 7 then 1
                when 0 then 1
                else cod_estado_civil
        end as cod_estado_civil
        from pessoal.contrato_servidor_caso_causa

        join pessoal.contrato_servidor_caso_causa_norma
        on contrato_servidor_caso_causa_norma.cod_contrato = contrato_servidor_caso_causa.cod_contrato

        join normas.norma
        on norma.cod_norma = contrato_servidor_caso_causa_norma.cod_norma

        join pessoal.caso_causa
        on caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa

        join pessoal.causa_rescisao
        on causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao

        join (
                select aposentadoria.*
                from pessoal.aposentadoria

                join (  select cod_contrato
                            , max(timestamp) as timestamp
                        from pessoal.aposentadoria
                       where timestamp < to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                    group by cod_contrato
                    ) as max_aposentadoria
                on max_aposentadoria.cod_contrato = aposentadoria.cod_contrato
                and max_aposentadoria.timestamp = aposentadoria.timestamp
        ) as aposentadoria
        on aposentadoria.cod_contrato = contrato_servidor_caso_causa.cod_contrato

        join pessoal.contrato_servidor
        on contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato

        left join pessoal.contrato_servidor_especialidade_cargo
        on contrato_servidor_especialidade_cargo.cod_contrato = contrato_servidor.cod_contrato

        left join ( select cod_especialidade
        , cod_sub_divisao
        , min(timestamp) as timestamp
        from pessoal.especialidade_sub_divisao
        group by cod_especialidade
        , cod_sub_divisao
        ) as timestamp_criacao_especialidade
        on timestamp_criacao_especialidade.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade

        left join ( select cargo_sub_divisao.cod_cargo
                    , min(timestamp) as timestamp
                    from pessoal.cargo_sub_divisao
                    group by cargo_sub_divisao.cod_cargo
        ) as timestamp_criacao_cargo
        on timestamp_criacao_cargo.cod_cargo = contrato_servidor.cod_cargo

        left join ( select contrato_servidor_padrao.cod_contrato
                    , contrato_servidor_padrao.cod_padrao
                    , max(timestamp) as timestamp
                    from pessoal.contrato_servidor_padrao
                    where timestamp < to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                    group by contrato_servidor_padrao.cod_contrato
                    , contrato_servidor_padrao.cod_padrao
        ) as contrato_servidor_padrao
        on contrato_servidor_padrao.cod_contrato = contrato_servidor.cod_contrato

        join pessoal.contrato
        on contrato.cod_contrato = contrato_servidor.cod_contrato

        join pessoal.servidor_contrato_servidor
        on servidor_contrato_servidor.cod_contrato = contrato_servidor_caso_causa.cod_contrato

        join pessoal.servidor
        on servidor.cod_servidor = servidor_contrato_servidor.cod_servidor

        join (
            select servidor_cid.cod_servidor
                , servidor_cid.cod_cid
                , servidor_cid.timestamp
                from pessoal.servidor_cid

                join(     select cod_servidor
                            , cod_cid
                            , max(timestamp) as timestamp
                            from pessoal.servidor_cid
                           where timestamp < to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
                        group by cod_servidor
                            , cod_cid
                    ) as max_servidor_cid
                on max_servidor_cid.cod_servidor = servidor_cid.cod_servidor
                and max_servidor_cid.cod_cid = servidor_cid.cod_cid
                and max_servidor_cid.timestamp = servidor_cid.timestamp
            ) as servidor_cid
        on servidor_cid.cod_servidor = servidor.cod_servidor

        join pessoal.cid
        on cid.cod_cid = servidor_cid.cod_cid

        where causa_rescisao.num_causa >= 70
        and causa_rescisao.num_causa <= 79

        and pessoal.contrato_servidor_caso_causa.dt_rescisao >= to_date( '".$this->getDado( 'dt_inicial' )."', 'dd/mm/yyyy' )
        and pessoal.contrato_servidor_caso_causa.dt_rescisao <= to_date( '".$this->getDado( 'dt_final' )."', 'dd/mm/yyyy' )
    ";
    
    return $stSql;
}

}

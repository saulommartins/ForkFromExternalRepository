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
  * Classe de mapeamento da tabela pessoal.assentamento
  * Data de Criação: 31/01/2005

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento

    $Id: TPessoalAssentamento.class.php 61019 2014-11-28 20:56:10Z jean $

    Caso de uso: uc-04.04.08

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPessoalAssentamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalAssentamento()
{
    parent::Persistente();
    $this->setTabela('pessoal.assentamento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_assentamento,timestamp');

    $this->AddCampo('cod_assentamento'          ,'integer'      ,true,'',true,true);
    $this->AddCampo('grade_efetividade'         ,'boolean'      ,true,'',false,false);
    $this->AddCampo('rel_funcao_gratificada'    ,'boolean'      ,true,'',false,false);
    $this->AddCampo('evento_automatico'         ,'boolean'      ,true,'',false,false);
    $this->AddCampo('assentamento_automatico'   ,'boolean'      ,true,'',false,false);
    $this->AddCampo('timestamp'                 ,'timestamp'    ,false,'',true,false);
    $this->AddCampo('cod_esfera'                ,'integer'      ,true,'' ,false,true);
    $this->AddCampo('assentamento_inicio'       ,'boolean'      ,true,'',false,false);
    $this->AddCampo('quant_dias_onus_empregador','integer',false,'10'   ,false  ,false);
    $this->AddCampo('quant_dias_licenca_premio' ,'integer',false,'10'   ,false  ,false);
}

function recuperaAssentamentos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY paa.descricao ";
    $stSql  = $this->montaRecuperaAssentamentos().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentos()
{
   $stSQL .="SELECT                                                         \n";
   $stSQL .="       trim(paa.sigla) as sigla_sem_espaco,                        \n";
   $stSQL .="       to_char(av.dt_inicial,'dd/mm/yyyy') as data_inicial,    \n";
   $stSQL .="       to_char(av.dt_final,'dd/mm/yyyy') as data_final,        \n";
   $stSQL .="       to_char(ava.dt_inicial,'dd/mm/yyyy') as data_inicial_vantagem,    \n";
   $stSQL .="       to_char(ava.dt_final,'dd/mm/yyyy') as data_final_vantagem,        \n";
   $stSQL .="       av.cancelar_direito,                                             \n";
   $stSQL .="       *                                                       \n";
   $stSQL .="  FROM pessoal.assentamento as A                           \n";
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_assentamento as paa             \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = paa.cod_assentamento               \n";
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_vantagem as ava                 \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = ava.cod_assentamento               \n";
   $stSQL .="   AND A.timestamp = ava.timestamp                             \n";
   $stSQL .="LEFT JOIN (                                                    \n";
   $stSQL .="   SELECT                                                      \n";
   $stSQL .="       ad.*                                                    \n";
   $stSQL .="   FROM                                                        \n";
   $stSQL .="       pessoal.assentamento_afastamento_temporario as at,  \n";
   $stSQL .="       pessoal.assentamento_afastamento_temporario_duracao as ad  \n";
   $stSQL .="   WHERE                                                       \n";
   $stSQL .="           at.cod_assentamento = ad.cod_assentamento           \n";
   $stSQL .="       AND at.timestamp = ad.timestamp                         \n";
   $stSQL .=" ) as tabela                                                   \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = tabela.cod_assentamento            \n";
   $stSQL .="   AND A.timestamp = tabela.timestamp                          \n";
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_validade as av                  \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = av.cod_assentamento                \n";
   $stSQL .="   AND A.timestamp = av.timestamp                              \n";
   $stSQL .="      ,(SELECT cod_assentamento , max(timestamp) as timestamp  \n";
   $stSQL .="          FROM pessoal.assentamento                        \n";
   $stSQL .="         GROUP BY cod_assentamento ) as ult                    \n";
   $stSQL .=" WHERE A.cod_assentamento = ult.cod_assentamento               \n";
   $stSQL .="   AND A.timestamp = ult.timestamp                             \n";

   return $stSQL;
}

function recuperaAssentamentosPorContrato(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $comboType = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stGroup = " GROUP BY paa.sigla,
                          av.dt_inicial,
                          av.dt_final,
                          ava.dt_inicial,
                          ava.dt_final,
                          av.cancelar_direito,
                          paa.descricao,
                          A.cod_assentamento";
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY paa.descricao ";
    $stSql  = $this->montaRecuperaAssentamentosPorContrato($comboType).$stFiltro.$stGroup.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentosPorContrato($comboType)
{
   $stSQL .="SELECT                                                         \n";
   $stSQL .="       trim(paa.sigla) as sigla_sem_espaco,                        \n";
   $stSQL .="       to_char(av.dt_inicial,'dd/mm/yyyy') as data_inicial,    \n";
   $stSQL .="       to_char(av.dt_final,'dd/mm/yyyy') as data_final,        \n";
   $stSQL .="       to_char(ava.dt_inicial,'dd/mm/yyyy') as data_inicial_vantagem,    \n";
   $stSQL .="       to_char(ava.dt_final,'dd/mm/yyyy') as data_final_vantagem,        \n";
   $stSQL .="       av.cancelar_direito,                                    \n";
   $stSQL .="       paa.descricao,                                          \n";
   $stSQL .="       A.cod_assentamento                                      \n";
   $stSQL .="  FROM pessoal.assentamento as A                               \n";
   $stSQL .="INNER JOIN                                                     \n";
   $stSQL .="      pessoal.assentamento_sub_divisao                         \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="      A.cod_assentamento = assentamento_sub_divisao.cod_assentamento \n";
   $stSQL .="  AND A.timestamp = assentamento_sub_divisao.timestamp         \n";
   $stSQL .="  AND assentamento_sub_divisao.timestamp = (SELECT MAX(timestamp)  \n";
   $stSQL .="                                              FROM pessoal.assentamento_sub_divisao sdf  \n";
   $stSQL .="                                             WHERE assentamento_sub_divisao.cod_assentamento = sdf.cod_assentamento \n";
   $stSQL .="                                               AND assentamento_sub_divisao.cod_sub_divisao = sdf.cod_sub_divisao ) \n";
   if ($comboType != 'cargo_exercido') {
     $stSQL .="INNER JOIN                                                     \n";
     $stSQL .="      pessoal.contrato_servidor_sub_divisao_funcao             \n";
     $stSQL .="ON                                                             \n";
     $stSQL .="      assentamento_sub_divisao.cod_sub_divisao = contrato_servidor_sub_divisao_funcao.cod_sub_divisao \n";
     $stSQL .="  AND contrato_servidor_sub_divisao_funcao.timestamp = (SELECT MAX(timestamp)  \n";
     $stSQL .="                                                          FROM pessoal.contrato_servidor_sub_divisao_funcao sdf  \n";
     $stSQL .="                                                         WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = sdf.cod_contrato \n";
     $stSQL .="                                                           AND contrato_servidor_sub_divisao_funcao.cod_sub_divisao = sdf.cod_sub_divisao ) \n";
     $stSQL .="INNER JOIN                                                     \n";
     $stSQL .="      pessoal.contrato                                         \n";
     $stSQL .="ON                                                             \n";
     $stSQL .="      contrato_servidor_sub_divisao_funcao.cod_contrato = contrato.cod_contrato \n";
   }
   switch ($comboType) {
       case 'cargo_exercido':
           $stSQL .= "INNER JOIN \n";
           $stSQL .= "   pessoal.contrato_servidor \n";
           $stSQL .= "ON \n";
           $stSQL .= "   assentamento_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao \n";
           break;
       case 'cargo':
           $stSQL .= "INNER JOIN \n";
           $stSQL .= "   pessoal.contrato_servidor \n";
           $stSQL .= "ON \n";
           $stSQL .= "   contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato \n";
           break;
       case 'lotacao':
           $stSQL .= "INNER JOIN \n";
           $stSQL .= "   pessoal.contrato_servidor \n";
           $stSQL .= "ON \n";
           $stSQL .= "   contrato_servidor_sub_divisao_funcao.cod_contrato = contrato_servidor.cod_contrato \n";
           $stSQL .= "INNER JOIN \n";
           $stSQL .= "   pessoal.contrato_servidor_orgao \n";
           $stSQL .= "ON \n";
           $stSQL .= "   contrato_servidor.cod_contrato = contrato_servidor_orgao.cod_contrato AND \n";
           $stSQL .= "   contrato_servidor_orgao.timestamp = (SELECT MAX(timestamp)              \n";
           $stSQL .= "                                          FROM pessoal.contrato_servidor_orgao orgao                         \n";
           $stSQL .= "                                         WHERE contrato_servidor_orgao.cod_contrato = orgao.cod_contrato \n";
           $stSQL .= "                                           AND contrato_servidor_orgao.cod_orgao = orgao.cod_orgao ) \n";
           break;
   }
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_assentamento as paa             \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = paa.cod_assentamento               \n";
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_vantagem as ava                 \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = ava.cod_assentamento               \n";
   $stSQL .="   AND A.timestamp = ava.timestamp                             \n";
   $stSQL .="LEFT JOIN (                                                    \n";
   $stSQL .="   SELECT                                                      \n";
   $stSQL .="       ad.*                                                    \n";
   $stSQL .="   FROM                                                        \n";
   $stSQL .="       pessoal.assentamento_afastamento_temporario as at,  \n";
   $stSQL .="       pessoal.assentamento_afastamento_temporario_duracao as ad  \n";
   $stSQL .="   WHERE                                                       \n";
   $stSQL .="           at.cod_assentamento = ad.cod_assentamento           \n";
   $stSQL .="       AND at.timestamp = ad.timestamp                         \n";
   $stSQL .=" ) as tabela                                                   \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = tabela.cod_assentamento            \n";
   $stSQL .="   AND A.timestamp = tabela.timestamp                          \n";
   $stSQL .="LEFT JOIN                                                      \n";
   $stSQL .="      pessoal.assentamento_validade as av                  \n";
   $stSQL .="ON                                                             \n";
   $stSQL .="       A.cod_assentamento = av.cod_assentamento                \n";
   $stSQL .="   AND A.timestamp = av.timestamp                              \n";
   $stSQL .="      ,(SELECT cod_assentamento , max(timestamp) as timestamp  \n";
   $stSQL .="          FROM pessoal.assentamento                        \n";
   $stSQL .="         GROUP BY cod_assentamento ) as ult                    \n";
   $stSQL .=" WHERE A.cod_assentamento = ult.cod_assentamento               \n";
   $stSQL .="   AND A.timestamp = ult.timestamp                             \n";

   return $stSQL;
}

function recuperaAssentamentoDisponivel(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY A.descricao ";
    $stSql  = $this->montaRecuperaAssentamentoDisponivel().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoDisponivel()
{
    $stSQL .="SELECT                                                        \n";
    $stSQL .="       trim(paa.sigla) as sigla_sem_espaco,                   \n";
    $stSQL .="       trim(paa.abreviacao) as abreviacao,                   \n";
    $stSQL .="       paa.descricao   as descricao,                          \n";
    $stSQL .="       A.*                                                    \n";
    $stSQL .="FROM                                                          \n";
    $stSQL .="  pessoal.assentamento as A                               \n";
    $stSQL .="LEFT JOIN                                                     \n";
    $stSQL .="      pessoal.assentamento_assentamento as paa            \n";
    $stSQL .="ON                                                            \n";
    $stSQL .="       A.cod_assentamento = paa.cod_assentamento,             \n";
    $stSQL .="  (SELECT                                                     \n";
    $stSQL .="      cod_assentamento,                                       \n";
    $stSQL .="      max(timestamp) as timestamp                             \n";
    $stSQL .="   FROM                                                       \n";
    $stSQL .="      pessoal.assentamento                                \n";
    $stSQL .="   GROUP BY                                                   \n";
    $stSQL .="      cod_assentamento ) as ult                               \n";
    $stSQL .="WHERE                                                         \n";
    $stSQL .="       A.cod_assentamento = ult.cod_assentamento              \n";
    $stSQL .="   AND A.timestamp = ult.timestamp                            \n";
    $stSQL .="   AND A.cod_assentamento not in (                            \n";
    $stSQL .="   SELECT                                                     \n";
    $stSQL .="       ca.cod_assentamento                                    \n";
    $stSQL .="   FROM                                                       \n";
    $stSQL .="       pessoal.condicao_assentamento as ca,                   \n";
    $stSQL .="       (SELECT                                                \n";
    $stSQL .="           cod_assentamento,                                  \n";
    $stSQL .="           max(timestamp) as timestamp                        \n";
    $stSQL .="       FROM                                                   \n";
    $stSQL .="           pessoal.condicao_assentamento                      \n";
    $stSQL .="       GROUP BY                                               \n";
    $stSQL .="           cod_assentamento                                   \n";
    $stSQL .="       ) as ult                                               \n";
    $stSQL .="   WHERE                                                      \n";
    $stSQL .="           ca.cod_assentamento = ult.cod_assentamento         \n";
    $stSQL .="       AND ca.timestamp = ult.timestamp                       \n";
    $stSQL .="       AND ca.cod_condicao::varchar||ca.cod_assentamento::varchar||ca.timestamp::varchar not in \n";
    $stSQL .="       (SELECT                                                \n";
    $stSQL .="           cod_condicao::varchar||cod_assentamento::varchar||timestamp::varchar                 \n";
    $stSQL .="       FROM                                                   \n";
    $stSQL .="           pessoal.condicao_assentamento_excluido             \n";
    $stSQL .="       )                                                      \n";
    $stSQL .="   )                                                          \n";

    return $stSQL;
}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaValidaExclusao().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Este assentamento está vinculado a outro assentamento, por esse motivo não pode ser excluído!');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSQL .="SELECT                                                        \n";
    $stSQL .="  *                                                           \n";
    $stSQL .="FROM                                                          \n";
    $stSQL .="  pessoal.assentamento as a,                              \n";
    $stSQL .="  pessoal.assentamento_vinculado as ac                    \n";
    $stSQL .="WHERE                                                         \n";
    $stSQL .="       a.cod_assentamento = ac.cod_assentamento               \n";
    if ( $this->getDado('cod_assentamento') ) {
        $stSQL .= "  AND ac.cod_assentamento = " . $this->getDado('cod_assentamento') . "\n";
    }

    return $stSQL;
}

function recuperaAfastamentoLicencaEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAfastamentoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAfastamentoEsfinge()
{
    $stSql = "
select norma.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao
      ,contrato.registro
      ,to_char(assentamento_gerado.periodo_final, 'dd/mm/yyyy') as periodo_final
      ,motivo_licenca_esfinge_sw.cod_motivo_licenca_esfinge
      ,assentamento_gerado.observacao
      ,1 as cod_tipo_quadro
      ,'1'||contrato_servidor.cod_cargo as cod_cargo
      ,to_char(cargo_sub_divisao.timestamp, 'dd/mm/yyyy') as dt_criacao
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato.cod_contrato = contrato_servidor.cod_contrato
join normas.norma
  on norma.cod_norma = contrato_servidor.cod_norma
join (select assentamento_gerado_contrato_servidor.cod_contrato
            ,assentamento_gerado.periodo_final
            ,assentamento_gerado.observacao
            ,assentamento_gerado.cod_assentamento
            ,assentamento_gerado.timestamp
      from pessoal.assentamento_gerado
          ,(select cod_assentamento_gerado, max(timestamp) as timestamp
              from pessoal.assentamento_gerado
              where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_assentamento_gerado) as ultimo_assetamento
          ,pessoal.assentamento_gerado_contrato_servidor
      where assentamento_gerado.cod_assentamento_gerado = ultimo_assetamento.cod_assentamento_gerado
        and assentamento_gerado.timestamp = ultimo_assetamento.timestamp
        and assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado) as assentamento_gerado
  on assentamento_gerado.cod_contrato = contrato_servidor.cod_contrato
join (select cod_sub_divisao, cod_cargo, max(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_cargo ) as cargo_sub_divisao
  on cargo_sub_divisao.cod_cargo = contrato_servidor.cod_cargo
 and cargo_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
join (select assentamento_afastamento_temporario.cod_assentamento
      from pessoal.assentamento_afastamento_temporario
          ,(select cod_assentamento, max(timestamp) as timestamp
              from pessoal.assentamento_afastamento_temporario
               where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_assentamento) as ultimo_assetamento
      where assentamento_afastamento_temporario.cod_assentamento = ultimo_assetamento.cod_assentamento
        and assentamento_afastamento_temporario.timestamp = ultimo_assetamento.timestamp ) as assentamento_afastamento_temporario
  on assentamento_afastamento_temporario.cod_assentamento = assentamento_gerado.cod_assentamento
join tcesc.motivo_licenca_esfinge_sw
  on motivo_licenca_esfinge_sw.cod_assentamento = assentamento_afastamento_temporario.cod_assentamento
where assentamento_gerado.timestamp between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
union
select norma.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao
      ,contrato.registro
      ,to_char(assentamento_gerado.periodo_final, 'dd/mm/yyyy') as periodo_final
      ,motivo_licenca_esfinge_sw.cod_motivo_licenca_esfinge
      ,assentamento_gerado.observacao
      ,1 as cod_tipo_quadro
      ,'2'||contrato_servidor.cod_cargo||contrato_servidor_especialidade_cargo.cod_especialidade as cod_cargo
      ,to_char(especialidade_sub_divisao.timestamp, 'dd/mm/yyyy') as periodo_final
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato.cod_contrato = contrato_servidor.cod_contrato
join pessoal.contrato_servidor_especialidade_cargo
  on contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
join normas.norma
  on norma.cod_norma = contrato_servidor.cod_norma
join (select assentamento_gerado_contrato_servidor.cod_contrato
            ,assentamento_gerado.periodo_final
            ,assentamento_gerado.observacao
            ,assentamento_gerado.cod_assentamento
            ,assentamento_gerado.timestamp
      from pessoal.assentamento_gerado
          ,(select cod_assentamento_gerado, max(timestamp) as timestamp
              from pessoal.assentamento_gerado
              where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_assentamento_gerado) as ultimo_assetamento
          ,pessoal.assentamento_gerado_contrato_servidor
      where assentamento_gerado.cod_assentamento_gerado = ultimo_assetamento.cod_assentamento_gerado
        and assentamento_gerado.timestamp = ultimo_assetamento.timestamp
        and assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado) as assentamento_gerado
  on assentamento_gerado.cod_contrato = contrato_servidor.cod_contrato
join (select cod_sub_divisao, cod_especialidade, max(timestamp) as timestamp
        from pessoal.especialidade_sub_divisao
      where timestamp <  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_especialidade ) as especialidade_sub_divisao
  on especialidade_sub_divisao.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
 and especialidade_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
join (select assentamento_afastamento_temporario.cod_assentamento
      from pessoal.assentamento_afastamento_temporario
          ,(select cod_assentamento, max(timestamp) as timestamp
              from pessoal.assentamento_afastamento_temporario
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_assentamento) as ultimo_assetamento
      where assentamento_afastamento_temporario.cod_assentamento = ultimo_assetamento.cod_assentamento
        and assentamento_afastamento_temporario.timestamp = ultimo_assetamento.timestamp ) as assentamento_afastamento_temporario
  on assentamento_afastamento_temporario.cod_assentamento = assentamento_gerado.cod_assentamento
join tcesc.motivo_licenca_esfinge_sw
  on motivo_licenca_esfinge_sw.cod_assentamento = assentamento_afastamento_temporario.cod_assentamento
where assentamento_gerado.timestamp between  to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and  to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

    return $stSql;
}



function recuperaAssentamentoRelatorio(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;    
    $stSql  = $this->montaRecuperaAssentamentoRelatorio().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAssentamentoRelatorio()
{
    $stSql="SELECT  cadastro.nom_cgm
                    , cadastro.registro
                    , (SELECT descricao FROM pessoal.classificacao_assentamento WHERE cod_classificacao = assentamento_assentamento.cod_classificacao) as classificacao
                    , assentamento_assentamento.descricao as assentamento
                    , CASE WHEN assentamento_gerado.periodo_inicial IS NULL 
                           THEN to_char(assentamento_gerado.periodo_final,'dd/mm/yyyy') 
                           WHEN assentamento_gerado.periodo_final IS NULL 
                           THEN to_char(assentamento_gerado.periodo_inicial,'dd/mm/yyyy') 
                           ELSE to_char(assentamento_gerado.periodo_inicial,'dd/mm/yyyy')||' a '||to_char(assentamento_gerado.periodo_final,'dd/mm/yyyy')  
                      END AS periodo 
                    , CASE WHEN (assentamento_gerado.periodo_final - assentamento_gerado.periodo_inicial + 1) > 9 
                           THEN CAST((assentamento_gerado.periodo_final - assentamento_gerado.periodo_inicial + 1) AS VARCHAR) 
                              ELSE '0'||(assentamento_gerado.periodo_final - assentamento_gerado.periodo_inicial + 1) 
                      END AS dias 
                    , (SELECT num_norma||'/'||exercicio||' - '||descricao FROM normas.norma WHERE cod_norma = assentamento_assentamento.cod_norma) as norma
                    , assentamento_gerado.observacao
                    , (select valor from administracao.configuracao where parametro = 'dtContagemInicial' and exercicio = '".$this->getDado('exercicio')."') as contagem_tempo
                    , to_char(cadastro.dt_posse,'dd/mm/yyyy') as dt_posse
                    , to_char(cadastro.dt_nomeacao,'dd/mm/yyyy') as dt_nomeacao
                    , to_char(cadastro.dt_admissao,'dd/mm/yyyy') as dt_admissao
                    , cadastro.desc_regime_funcao as regime
                    , cadastro.desc_sub_divisao_funcao as sub_divisao
                    , cadastro.desc_funcao as funcao
                    , cadastro.desc_especialidade_funcao as especialidade     
            FROM pessoal.assentamento_gerado
            , ( SELECT cod_assentamento_gerado
                        , max(timestamp) as timestamp
                FROM pessoal.assentamento_gerado
                GROUP BY cod_assentamento_gerado
            ) as max_assentamento_gerado
            , pessoal.assentamento_assentamento
            , pessoal.assentamento_gerado_contrato_servidor
            , ( 
                SELECT * FROM recuperarContratoServidor('cgm,f,ef,rf,sf,o,oo,l,anp'
                                                        ,'".$this->getDado('cod_entidade')."'
                                                        ,0
                                                        ,'".$this->getDado('tipo_filtro')."'
                                                        ,'".$this->getDado('dado_filtro')."'
                                                        ,'".$this->getDado('exercicio')."') 
            ) as cadastro
            
            WHERE assentamento_gerado.cod_assentamento_gerado = max_assentamento_gerado.cod_assentamento_gerado
            AND assentamento_gerado.timestamp = max_assentamento_gerado.timestamp
            AND assentamento_gerado.cod_assentamento = assentamento_assentamento.cod_assentamento
            AND assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_contrato_servidor.cod_assentamento_gerado
            AND assentamento_gerado_contrato_servidor.cod_contrato = cadastro.cod_contrato
            AND NOT EXISTS (SELECT 1
                            FROM pessoal.assentamento_gerado_excluido
                            WHERE assentamento_gerado.cod_assentamento_gerado = assentamento_gerado_excluido.cod_assentamento_gerado 
                            AND assentamento_gerado.timestamp = assentamento_gerado_excluido.timestamp
                         )

        ";  
  return $stSql;
}

}

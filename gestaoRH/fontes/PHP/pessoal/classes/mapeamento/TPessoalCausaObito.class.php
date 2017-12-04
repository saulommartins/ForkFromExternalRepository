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
    * Classe de mapeamento da tabela pessoal.causa_obito
    * Data de Criação: 25/09/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-07 09:41:04 -0300 (Qui, 07 Jun 2007) $

    * Casos de uso: uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  pessoal.causa_obito
  * Data de Criação: 25/09/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCausaObito extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCausaObito()
{
    parent::Persistente();
    $this->setTabela("pessoal.causa_obito");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato');

    $this->AddCampo('cod_contrato'      ,'integer',false ,''    ,true,'TPessoalContratoServidorCasoCausa');
    $this->AddCampo('num_certidao_obito','varchar',false ,'10'  ,false,false);
    $this->AddCampo('causa_mortis'      ,'varchar',false ,'200' ,false,false);

}

function recuperaFalecimentoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaFalecimentoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaFalecimentoEsfinge()
{
    $stSql = "
select causa_obito.num_certidao_obito
      ,to_char(contrato_servidor_caso_causa.dt_rescisao, 'dd/mm/yyyy') as dt_rescisao
      ,contrato.registro
      ,causa_obito.causa_mortis
      ,'S' as indicativo_ativo
      ,1 as cod_tipo_quadro
      ,'1'||contrato_servidor.cod_cargo as cod_cargo
      ,to_char(cargo_sub_divisao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,contrato_servidor_padrao.cod_padrao
      ,case cod_estado_civil
         when 5 then 3
         when 6 then 4
         when 3 then 5
         when 7 then 1
         when 0 then 1
         else cod_estado_civil
       end as cod_estado_civil
      ,padrao_padrao.valor
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join pessoal.servidor_contrato_servidor
  on servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
join pessoal.servidor
  on servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
join pessoal.causa_obito
  on causa_obito.cod_contrato = contrato.cod_contrato
join pessoal.contrato_servidor_caso_causa
  on contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
join pessoal.caso_causa
  on caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
join (select cod_sub_divisao, cod_cargo, max(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_cargo ) as cargo_sub_divisao
  on cargo_sub_divisao.cod_cargo = contrato_servidor.cod_cargo
 and cargo_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao

join (select contrato_servidor_padrao.cod_padrao, contrato_servidor_padrao.cod_contrato
        from pessoal.contrato_servidor_padrao
            ,(select cod_contrato
                    , max(timestamp) as timestamp
              from pessoal.contrato_servidor_padrao
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
              group by cod_contrato) as max_contrato_servidor_padrao
              where contrato_servidor_padrao.cod_contrato  = max_contrato_servidor_padrao.cod_contrato
              and contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao
  on contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
join (select padrao_padrao.cod_padrao, padrao_padrao.valor
      from folhapagamento.padrao_padrao
          ,(select cod_padrao, max(timestamp) as timestamp
              from folhapagamento.padrao_padrao
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_padrao) as ultimo_padrao
      where padrao_padrao.cod_padrao = ultimo_padrao.cod_padrao
        and padrao_padrao.timestamp = ultimo_padrao.timestamp) as padrao_padrao
  on padrao_padrao.cod_padrao = contrato_servidor_padrao.cod_padrao

  where caso_causa.cod_causa_rescisao in (10, 12)
    and contrato_servidor_caso_causa.dt_rescisao between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
union
select causa_obito.num_certidao_obito
      ,to_char(contrato_servidor_caso_causa.dt_rescisao, 'dd/mm/yyyy') as dt_rescisao
      ,contrato.registro
      ,causa_obito.causa_mortis
      ,'S' as inditicativo_ativo
      ,1 as cod_tipo_quadro
      ,'2'||contrato_servidor.cod_cargo||contrato_servidor_especialidade_cargo.cod_especialidade as cod_cargo
      ,to_char(especialidade_sub_divisao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,contrato_servidor_padrao.cod_padrao
      ,case cod_estado_civil
         when 5 then 3
         when 6 then 4
         when 3 then 5
         when 7 then 1
         when 0 then 1
         else cod_estado_civil
       end as cod_estado_civil
      ,padrao_padrao.valor
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join pessoal.contrato_servidor_especialidade_cargo
  on contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
join pessoal.servidor_contrato_servidor
  on servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato
join pessoal.servidor
  on servidor.cod_servidor = servidor_contrato_servidor.cod_servidor
join pessoal.causa_obito
  on causa_obito.cod_contrato = contrato.cod_contrato
join pessoal.contrato_servidor_caso_causa
  on contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
join pessoal.caso_causa
  on caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
join (select cod_sub_divisao, cod_especialidade, max(timestamp) as timestamp
        from pessoal.especialidade_sub_divisao
      where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_especialidade ) as especialidade_sub_divisao
  on especialidade_sub_divisao.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
 and especialidade_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
join (select contrato_servidor_padrao.cod_padrao, contrato_servidor_padrao.cod_contrato
        from pessoal.contrato_servidor_padrao
            ,(select cod_contrato
                    , max(timestamp) as timestamp
              from pessoal.contrato_servidor_padrao
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
              group by cod_contrato) as max_contrato_servidor_padrao
              where contrato_servidor_padrao.cod_contrato  = max_contrato_servidor_padrao.cod_contrato
              and contrato_servidor_padrao.timestamp = max_contrato_servidor_padrao.timestamp) as contrato_servidor_padrao
  on contrato_servidor_padrao.cod_contrato = contrato.cod_contrato
join (select padrao_padrao.cod_padrao, padrao_padrao.valor
      from folhapagamento.padrao_padrao
          ,(select cod_padrao, max(timestamp) as timestamp
              from folhapagamento.padrao_padrao
              where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
            group by cod_padrao) as ultimo_padrao
      where padrao_padrao.cod_padrao = ultimo_padrao.cod_padrao
        and padrao_padrao.timestamp = ultimo_padrao.timestamp) as padrao_padrao
  on padrao_padrao.cod_padrao = contrato_servidor_padrao.cod_padrao
  where caso_causa.cod_causa_rescisao in (10, 12)
    and contrato_servidor_caso_causa.dt_rescisao between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
";

   return $stSql;
}

}

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
* Classe de mapeamento para PESSOAL.CAUSA_RESCISAO
* Data de Criação: 05/05/2005

* @author Analista: Leandro OLiveira
* @author Desenvolvedor: Vandré Miguel Ramos

* @package URBEM
* @subpackage Mapeamento

$Revision: 30566 $
$Name$
$Author: souzadl $
$Date: 2007-11-16 17:47:10 -0200 (Sex, 16 Nov 2007) $

* Casos de uso: uc-04.04.10
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.CAUSA_RESCISAO
  * Data de Criação: 05/05/2005

  * @author Analista: Leandro OLiveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalCausaRescisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalCausaRescisao()
{
    parent::Persistente();
    $this->setTabela('pessoal.causa_rescisao');

    $this->setCampoCod('cod_causa_rescisao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_causa_rescisao','sequence',true,'',true,false);
    $this->AddCampo('cod_sefip_saida','integer',true,'',false,true);
    $this->AddCampo('num_causa','integer',true,'',false,false);
    $this->AddCampo('descricao','varchar',true,'200',false,false);
    $this->AddCampo('cod_causa_afastamento','varchar',true,'',false,true);

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT causa_rescisao.*                                                     \n";
    $stSql .= "     , sefip.num_sefip                                                      \n";
    $stSql .= "     , sefip.descricao                                                      \n";
    $stSql .= "  FROM pessoal.causa_rescisao                                               \n";
    $stSql .= "     , pessoal.mov_sefip_saida                                              \n";
    $stSql .= "     , pessoal.sefip                                                        \n";
    $stSql .= " WHERE causa_rescisao.cod_sefip_saida = mov_sefip_saida.cod_sefip_saida     \n";
    $stSql .= "   AND mov_sefip_saida.cod_sefip_saida = sefip.cod_sefip                    \n";

    return $stSql;
}

function recuperaDesligamentoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDesligamentoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDesligamentoEsfinge()
{
    $stSql = "
select contrato_servidor_caso_causa_norma.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao
      ,contrato.registro
      ,1 as cod_tipo_quadro
      ,'1'||contrato_servidor.cod_cargo as cod_cargo
      ,to_char(cargo_sub_divisao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,'N' as indicativo_debito
      ,'N' as indicativo_respondendo
      ,case causa_rescisao.num_causa
         when 21 then 1
         when 20 then 2
         when 10 then 3
         else 9
       end as cod_motivo_desligamento
      ,case
        when num_causa = 60 or num_causa = 64 then 6
        when (num_causa = 10 or num_causa = 12 or num_causa = 20 or num_causa = 80) or (cod_regime = 2) then 3
        when (num_causa = 10 or num_causa = 12 or num_causa = 20 or num_causa = 80) or (cod_regime = 1) then 4
        when (num_causa between 70 and 79) and (cod_regime_previdencia = 1) then 1
        when (num_causa between 70 and 79) and (cod_regime_previdencia = 2 or cod_regime_previdencia = 0) then 2
        when num_causa = 12 then 5
       end as cod_tipo_desligamento
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join pessoal.contrato_servidor_previdencia
  on contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
join folhapagamento.previdencia
  on previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
join pessoal.contrato_servidor_caso_causa_norma
  on contrato_servidor_caso_causa_norma.cod_contrato = contrato.cod_contrato
join normas.norma
  on contrato_servidor_caso_causa_norma.cod_norma = norma.cod_norma
join pessoal.contrato_servidor_caso_causa
  on contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
join pessoal.caso_causa
  on caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
join pessoal.causa_rescisao
  on causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao
join (select cod_sub_divisao, cod_cargo, max(timestamp) as timestamp
        from pessoal.cargo_sub_divisao
      where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_cargo ) as cargo_sub_divisao
  on cargo_sub_divisao.cod_cargo = contrato_servidor.cod_cargo
 and cargo_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
where cargo_sub_divisao.cod_sub_divisao in (1,4,6)
  and pessoal.contrato_servidor_caso_causa.dt_rescisao between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')

union
select contrato_servidor_caso_causa_norma.cod_norma
      ,to_char(norma.dt_assinatura, 'dd/mm/yyyy') as dt_assinatura
      ,to_char(norma.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao
      ,norma.descricao
      ,contrato.registro
      ,1 as cod_tipo_quadro
      ,'2'||contrato_servidor.cod_cargo||contrato_servidor_especialidade_cargo.cod_especialidade as cod_cargo
      ,to_char(especialidade_sub_divisao.timestamp, 'dd/mm/yyyy') as dt_criacao
      ,'N' as indicativo_debito
      ,'N' as indicativo_respondendo
      ,case causa_rescisao.num_causa
         when 21 then 1
         when 20 then 2
         when 10 then 3
         else 9
       end as cod_motivo_desligamento
      ,case
        when num_causa = 60 or num_causa = 64 then 6
        when (num_causa = 10 or num_causa = 12 or num_causa = 20 or num_causa = 80) or (cod_regime = 2) then 3
        when (num_causa = 10 or num_causa = 12 or num_causa = 20 or num_causa = 80) or (cod_regime = 1) then 4
        when (num_causa between 70 and 79) and (cod_regime_previdencia = 1) then 1
        when (num_causa between 70 and 79) and (cod_regime_previdencia = 2 or cod_regime_previdencia = 0) then 2
        when num_causa = 12 then 5
       end as cod_tipo_desligamento
from pessoal.contrato
join pessoal.contrato_servidor
  on contrato_servidor.cod_contrato = contrato.cod_contrato
join pessoal.contrato_servidor_previdencia
  on contrato_servidor_previdencia.cod_contrato = contrato_servidor.cod_contrato
join folhapagamento.previdencia
  on previdencia.cod_previdencia = contrato_servidor_previdencia.cod_previdencia
join pessoal.contrato_servidor_especialidade_cargo
  on contrato_servidor.cod_contrato = contrato_servidor_especialidade_cargo.cod_contrato
join pessoal.contrato_servidor_caso_causa_norma
  on contrato_servidor_caso_causa_norma.cod_contrato = contrato.cod_contrato
join normas.norma
  on contrato_servidor_caso_causa_norma.cod_norma = norma.cod_norma
join pessoal.contrato_servidor_caso_causa
  on contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato
join pessoal.caso_causa
  on caso_causa.cod_caso_causa = contrato_servidor_caso_causa.cod_caso_causa
join pessoal.causa_rescisao
  on causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao
join (select cod_sub_divisao, cod_especialidade, max(timestamp) as timestamp
        from pessoal.especialidade_sub_divisao
      where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
      group by cod_sub_divisao, cod_especialidade ) as especialidade_sub_divisao
  on especialidade_sub_divisao.cod_especialidade = contrato_servidor_especialidade_cargo.cod_especialidade
 and especialidade_sub_divisao.cod_sub_divisao = contrato_servidor.cod_sub_divisao
where especialidade_sub_divisao.cod_sub_divisao in (1,4,6)
  and pessoal.contrato_servidor_caso_causa.dt_rescisao between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy') and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')

";

    return $stSql;
}

function recuperaCausaRescisao(&$rsLista, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsLista     = new RecordSet;
    $stSql = $this->montaRecuperaCausaRescisao().$stFiltro.$stOrdem;
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsLista, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCausaRescisao()
{
    $stSql .= "SELECT causa_rescisao.*                                                     \n";
    $stSql .= "     , sefip.num_sefip                                                      \n";
    $stSql .= "     , sefip.descricao                                                      \n";
    $stSql .= "  FROM pessoal.causa_rescisao                                               \n";
    $stSql .= "     , pessoal.caso_causa                                                   \n";
    $stSql .= "     , pessoal.mov_sefip_saida                                              \n";
    $stSql .= "     , pessoal.sefip                                                        \n";
    $stSql .= " WHERE causa_rescisao.cod_sefip_saida = mov_sefip_saida.cod_sefip_saida     \n";
    $stSql .= "   AND mov_sefip_saida.cod_sefip_saida = sefip.cod_sefip                    \n";
    $stSql .= "   AND causa_rescisao.cod_causa_rescisao = caso_causa.cod_causa_rescisao    \n";

    return $stSql;
}

function recuperaSefipRescisao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaSefipRescisao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaSefipRescisao()
{
    $stSql .= "SELECT causa_rescisao.cod_sefip_saida                                                             \n";
    $stSql .= "   FROM pessoal.contrato_servidor_caso_causa                             \n";
    $stSql .= "      , pessoal.caso_causa                                               \n";
    $stSql .= "      , pessoal.causa_rescisao                                           \n";
    $stSql .= "  WHERE contrato_servidor_caso_causa.cod_caso_causa = caso_causa.cod_caso_causa                    \n";
    $stSql .= "    AND caso_causa.cod_causa_rescisao = causa_rescisao.cod_causa_rescisao                          \n";

    return $stSql;
}

}

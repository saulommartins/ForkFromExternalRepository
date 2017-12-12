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
* Classe de regra de negócio para Pessoal-Padrao
* Data de Criação: 02/12/2004

* @author Analista: Leandro Oliveira
* @author Desenvolvedor: Gustavo Passos Tourinho

* @package URBEM
* @subpackage  Mapeamento

$Revision: 30566 $
$Name$
$Author: leandro.zis $
$Date: 2007-09-05 15:52:01 -0300 (Qua, 05 Set 2007) $

* Casos de uso: uc-04.05.05
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  FOLHAPAGAMENTO.PADRAO
  * Data de Criação: 02/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Gustavo Passos Tourinho

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoPadrao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
    public function TFolhaPagamentoPadrao()
    {
        parent::Persistente();
        $this->setTabela('folhapagamento.padrao');

        $this->setCampoCod('cod_padrao');
        $this->setComplementoChave('');

        $this->AddCampo('cod_padrao'    , 'integer'  , true,    ''    ,  true, false);
        $this->AddCampo('descricao'     , 'varchar'  , true,    '80'  , false, false);
        $this->AddCampo('horas_mensais' , 'numeric,' , true,    '5.2' , false, false);
        $this->AddCampo('horas_semanais', 'numeric,' , true,    '5.2' , false, false);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSQL  = " SELECT                                                   \n";
        $stSQL .= "    FP.cod_padrao,                                        \n";
        $stSQL .= "    FP.descricao,                                         \n";
        $stSQL .= "    to_char(FPP.vigencia,'dd/mm/yyyy') as vigencia,       \n";
        $stSQL .= "    FPP.cod_norma,                                        \n";
        $stSQL .= "    FP.horas_mensais,                                     \n";
        $stSQL .= "    FP.horas_semanais,                                    \n";
        $stSQL .= "    FPP.valor,                                            \n";
        $stSQL .= "    MAXTFPP.timestamp                                     \n";
        $stSQL .= " FROM                                                     \n";
        $stSQL .= "    folhapagamento.padrao FP,                             \n";
        $stSQL .= "    folhapagamento.padrao_padrao FPP,                     \n";
        $stSQL .= "    (SELECT                                               \n";
        $stSQL .= "        MAXFPP.cod_padrao,                                \n";
        $stSQL .= "        MAX(timestamp) as timestamp                       \n";
        $stSQL .= "        FROM folhapagamento.padrao_padrao MAXFPP          \n";
        $stSQL .= "                   GROUP BY MAXFPP.cod_padrao) as MAXTFPP \n";
        $stSQL .= " WHERE FP.cod_padrao = MAXTFPP.cod_padrao                 \n";
        $stSQL .= "       AND FPP.cod_padrao = MAXTFPP.cod_padrao            \n";
        $stSQL .= "       AND FPP.timestamp  = MAXTFPP.timestamp             \n";

        return $stSQL;
    }

    public function recuperaRelacionamentoPorContratosInativos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $stFiltroPadraoParaInativos = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaRelacionamentoPorContratosInativos($stFiltroPadraoParaInativos).$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaRelacionamentoPorContratosInativos($stFiltroPadraoParaInativos)
    {
        $stSQL  = " SELECT                                                   \n";
        $stSQL .= "    FP.cod_padrao,                                        \n";
        $stSQL .= "    FP.descricao,                                         \n";
        $stSQL .= "    to_char(FPP.vigencia,'dd/mm/yyyy') as vigencia,       \n";
        $stSQL .= "    FPP.cod_norma,                                        \n";
        $stSQL .= "    FP.horas_mensais,                                     \n";
        $stSQL .= "    FP.horas_semanais,                                    \n";
        $stSQL .= "    FPP.valor,                                            \n";
        $stSQL .= "    MAXTFPP.timestamp                                     \n";
        $stSQL .= " FROM                                                     \n";
        $stSQL .= "    folhapagamento.padrao FP,                             \n";
        $stSQL .= "    folhapagamento.padrao_padrao FPP,                     \n";
        $stSQL .= "    (SELECT                                               \n";
        $stSQL .= "        MAXFPP.cod_padrao,                                \n";
        $stSQL .= "        MAX(timestamp) as timestamp                       \n";
        $stSQL .= "        FROM folhapagamento.padrao_padrao MAXFPP          \n";
        $stSQL .= " " .$stFiltroPadraoParaInativos."                         \n";
        $stSQL .= "                   GROUP BY MAXFPP.cod_padrao) as MAXTFPP \n";
        $stSQL .= " WHERE FP.cod_padrao = MAXTFPP.cod_padrao                 \n";
        $stSQL .= "       AND FPP.cod_padrao = MAXTFPP.cod_padrao            \n";
        $stSQL .= "       AND FPP.timestamp  = MAXTFPP.timestamp             \n";

        return $stSQL;
    }

    public function validaExclusao($stFiltro = "", $boTransacao = "")
    {
        $obErro = new erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql  = $this->montaValidaExclusaoCargo().$stFiltro;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        if ( !$obErro->ocorreu() ) {

            if ( $rsRecordSet->getNumLinhas() > 0 ) {
                $obErro->setDescricao("O padrão a ser excluído está sendo utilizado por um cargo.");
            } else { //verificando ligação com a tabela pessoal.contrato_servidor

                  $stSql  = $this->montaValidaExclusaoContratoServidor().$stFiltro;
                  $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                  if ( !$obErro->ocorreu() ) {
                      if ( $rsRecordSet->getNumLinhas() > 0 ) {
                          $obErro->setDescricao("O padrão a ser excluído está sendo utilizado por um contrato.");
                      } else { // ligação com especialidade

                              $stSql  = $this->montaValidaExclusaoEspecialidade().$stFiltro;
                              $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

                              if ( !$obErro->ocorreu() ) {
                                  if ( $rsRecordSet->getNumLinhas() > 0 ) {
                                      $obErro->setDescricao("O padrão a ser excluído está sendo utilizado por uma especialidade.");
                                  }
                              }
                      }
                  }
            }
       }

       return $obErro;
    }

    public function montaValidaExclusaoCargo()
    {
        $stSQL  = " SELECT fp.cod_padrao                   \n";
        $stSQL .= "   FROM folhapagamento.padrao fp        \n";
        $stSQL .= "   JOIN pessoal.cargo_padrao as pcp     \n";
        $stSQL .= "     ON pcp.cod_padrao  = fp.cod_padrao \n";
        $stSQL .= "  WHERE fp.cod_padrao   =  ".$this->getDado('cod_padrao')." \n";

        return $stSQL;
    }
    public function montaValidaExclusaoContratoServidor()
    {
        $stSQL .= " SELECT fp.cod_padrao                            \n";
        $stSQL .= "   FROM folhapagamento.padrao fp                 \n";
        $stSQL .= "   JOIN pessoal.contrato_servidor_padrao as pcsp \n";
        $stSQL .= "     ON pcsp.cod_padrao  = fp.cod_padrao         \n";
        $stSQL .= "  WHERE fp.cod_padrao    =  ".$this->getDado('cod_padrao')." \n";

        return $stSQL;
    }
    public function montaValidaExclusaoEspecialidade()
    {
        $stSQL .= " SELECT fp.cod_padrao                       \n";
        $stSQL .= "   FROM folhapagamento.padrao fp            \n";
        $stSQL .= "   JOIN pessoal.especialidade_padrao as pep \n";
        $stSQL .= "     ON pep.cod_padrao = fp.cod_padrao      \n";
        $stSQL .= "  WHERE fp.cod_padrao  =  ".$this->getDado('cod_padrao')." \n";

        return $stSQL;
    }

function recuperaAtualizacaoNivelEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAtualizacaoNivelEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaAtualizacaoNivelEsfinge()
{
    $stSql = "
select case
         when padrao_padrao.timestamp = padrao_criacao.timestamp then 1
         else 2
       end as tipo_atualizacao
      ,padrao_padrao.cod_norma
      ,case norma.cod_tipo_norma
          when 1 then 1
          when 4 then 2
          when 2 then 5
          else 10
      end as cod_tipo_norma
      ,1 as cod_tipo_quadro
      ,to_char(cargo_criacao.dt_criacao, 'dd/mm/yyyy') as dt_criacao
      ,cargo.cod_cargo
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,padrao_padrao.cod_padrao
from folhapagamento.padrao_padrao
join (select cod_padrao, min(timestamp) as timestamp
      from folhapagamento.padrao_padrao
      group by cod_padrao) as padrao_criacao
  on padrao_padrao.cod_padrao = padrao_criacao.cod_padrao
join normas.norma
  on padrao_padrao.cod_norma = norma.cod_norma
join (select cod_padrao, cod_cargo, max(timestamp) as timestamp
  from pessoal.cargo_padrao
  where timestamp < to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
  group by cod_padrao, cod_cargo
   ) as cargo_padrao
 on cargo_padrao.cod_padrao = padrao_padrao.cod_padrao
join pessoal.cargo
  on cargo_padrao.cod_cargo = cargo.cod_cargo
join (select cod_cargo, min(timestamp) as dt_criacao
      from pessoal.cargo_sub_divisao
      group by cod_cargo) as cargo_criacao
  on cargo.cod_cargo = cargo_criacao.cod_cargo
where padrao_padrao.timestamp between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
  and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')
union
select 3 as tipo_atualizacao
      ,padrao_padrao.cod_norma
      ,case norma.cod_tipo_norma
          when 1 then 1
          when 4 then 2
          when 2 then 5
          else 10
      end as cod_tipo_norma
      ,1 as cod_tipo_quadro
      ,to_char(cargo_criacao.dt_criacao, 'dd/mm/yyyy') as dt_criacao
      ,cargo.cod_cargo
      ,1 as cod_grupo
      ,1 as cod_referencia
      ,padrao_padrao.cod_padrao
from normas.norma_data_termino
join folhapagamento.padrao_padrao
  on norma_data_termino.cod_norma = padrao_padrao.cod_norma
join normas.norma
  on norma_data_termino.cod_norma = norma.cod_norma
join (select cod_padrao, cod_cargo, max(timestamp) as timestamp
  from pessoal.cargo_padrao
  where timestamp < now()
  group by cod_padrao, cod_cargo
   ) as cargo_padrao
 on cargo_padrao.cod_padrao = padrao_padrao.cod_padrao
join pessoal.cargo
  on cargo_padrao.cod_cargo = cargo.cod_cargo
join (select cod_cargo, min(timestamp) as dt_criacao
      from pessoal.cargo_sub_divisao
      group by cod_cargo) as cargo_criacao
  on cargo.cod_cargo = cargo_criacao.cod_cargo
where padrao_padrao.timestamp between to_date('".$this->getDado('dt_inicial')."', 'dd/mm/yyyy')
  and to_date('".$this->getDado('dt_final')."', 'dd/mm/yyyy')

";

    return $stSql;
}

function recuperaVencimentoCargoEsfinge(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVencimentoCargoEsfinge().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVencimentoCargoEsfinge()
{
    $stSql = "
select 1 as cod_grupo
      ,1 as cod_referencia
      ,padrao_padrao.cod_padrao
      ,to_char(padrao_padrao.vigencia, 'dd/mm/yyyy') as vigencia
      ,padrao_padrao.valor
  from folhapagamento.padrao_padrao
";

    return $stSql;
}

}

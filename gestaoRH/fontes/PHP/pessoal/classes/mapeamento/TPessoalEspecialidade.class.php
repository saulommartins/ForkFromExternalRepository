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
  * Classe de mapeamento da tabela PESSOAL.ESPECIALIDADE
  * Data de Criação: 29/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: souzadl $
    $Date: 2007-06-15 15:00:34 -0300 (Sex, 15 Jun 2007) $

    Caso de uso: uc-04.04.06

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  PESSOAL.ESPECIALIDADE
  * Data de Criação: 29/12/2004

  * @author Analista: Leandro Oliveira
  * @author Desenvolvedor: Vandré Miguel Ramos

  * @package URBEM
  * @subpackage Mapeamento
*/
class TPessoalEspecialidade extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPessoalEspecialidade()
{
    parent::Persistente();
    $this->setTabela('pessoal.especialidade');

    $this->setCampoCod('cod_especialidade');
    $this->setComplementoChave('');

    $this->AddCampo('cod_especialidade','integer',true,'',true,false);
    $this->AddCampo('cod_cargo','integer',true,'',false,true);
    $this->AddCampo('descricao','varchar',true,'80',false,false);
   }

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT cargo.cod_cargo as cod_cargo                                                          \n";
    $stSql .= "     , cargo.descricao as descricao                                                          \n";
    //$stSql .= "     , cargo.cbo as cbo                                                                      \n";
    $stSql .= "     , cargo.cargo_cc                                                                        \n";
    $stSql .= "     , cargo.funcao_gratificada                                                              \n";
    $stSql .= "     , esp.cod_especialidade                                                                 \n";
    $stSql .= "     , esp.descricao as descricao_especialidade                                              \n";
    $stSql .= "     , (SELECT codigo FROM pessoal.cbo WHERE cod_cbo = esp.cod_cbo) as cbo_especialidade     \n";
    $stSql .= "     , esp.cod_cbo as cod_cbo_especialidade     \n";
    $stSql .= "     , esp.cod_padrao as cod_padrao_especialidade                                     \n";
    $stSql .= "     , esp.timestamp_padrao_padrao                                                           \n";
    $stSql .= "     , esp.cod_sub_divisao                                                               \n";
    $stSql .= "     , esp.vagas_ocupadas                                                                     \n";
    $stSql .= "     , esp.nro_vaga_criada                                                               \n";
    $stSql .= "     , esp.cod_norma as norma_maxima                                                     \n";
    $stSql .= "     , esp.timestamp_especialidade_sub_divisao                                                                     \n";
    $stSql .= "     , esp.cod_tipo_norma as cod_tipo_norma_especialidade                                  \n";
    $stSql .= "     , esp.cod_regime                                                                     \n";
    $stSql .= "     , esp.descricao_regime as nom_regime                                                        \n";
    $stSql .= "     , esp.descricao_sub_divisao as nom_sub_divisao                                                      \n";
    $stSql .= "     , (SELECT PESDMIN.cod_norma                                                             \n";
    $stSql .= "          FROM pessoal.especialidade_sub_divisao PESDMIN                                     \n";
    $stSql .= "             , (  SELECT MIN(timestamp) as timestamp                                         \n";
    $stSql .= "                       , cod_especialidade                                                   \n";
    $stSql .= "                       , cod_sub_divisao                                                     \n";
    $stSql .= "                    FROM pessoal.especialidade_sub_divisao                                   \n";
    $stSql .= "                GROUP BY cod_especialidade                                                   \n";
    $stSql .= "                       , cod_sub_divisao) as max_PESDMIN                                     \n";
    $stSql .= "         WHERE PESDMIN.cod_especialidade = esp.cod_especialidade                         \n";
    $stSql .= "           AND PESDMIN.cod_sub_divisao   = esp.cod_sub_divisao                           \n";
    $stSql .= "           AND PESDMIN.timestamp         = max_PESDMIN.timestamp                             \n";
    $stSql .= "           AND PESDMIN.cod_especialidade = max_PESDMIN.cod_especialidade                     \n";
    $stSql .= "           AND PESDMIN.cod_sub_divisao   = max_PESDMIN.cod_sub_divisao ) as norma_minima     \n";
    $stSql .= "     , esp.horas_mensais                                                                  \n";
    $stSql .= "     , esp.horas_semanais                                                                 \n";
    $stSql .= "     , esp.valor                                                                   \n";
    $stSql .= "     , esp.cod_padrao                                                                        \n";
    $stSql .= "  FROM pessoal.cargo         as cargo                                                        \n";
    $stSql .= "LEFT JOIN ( SELECT especialidade.cod_especialidade                                           \n";
    $stSql .= "                 , especialidade.cod_cargo                                                   \n";
    $stSql .= "                 , especialidade.descricao                                                   \n";
    $stSql .= "                 , cbo_especialidade.cod_cbo                                                 \n";
    $stSql .= "                 , getVagasOcupadasEspecialidade(regime.cod_regime,sub_divisao.cod_sub_divisao,especialidade.cod_especialidade,0,true,'".Sessao::getEntidade()."') as vagas_ocupadas                                       \n";
    $stSql .= "                 , especialidade_sub_divisao.nro_vaga_criada                                 \n";
    $stSql .= "                 , especialidade_sub_divisao.timestamp as timestamp_especialidade_sub_divisao\n";
    $stSql .= "                 , padrao.cod_padrao                                                         \n";
    $stSql .= "                 , padrao.horas_mensais                                                      \n";
    $stSql .= "                 , padrao.horas_semanais                                                     \n";
    $stSql .= "                 , padrao_padrao.timestamp as timestamp_padrao_padrao                        \n";
    $stSql .= "                 , padrao_padrao.valor                                                       \n";
    $stSql .= "                 , norma.cod_norma                                                           \n";
    $stSql .= "                 , norma.cod_tipo_norma                                                      \n";
    $stSql .= "                 , sub_divisao.cod_sub_divisao                                               \n";
    $stSql .= "                 , sub_divisao.descricao as descricao_sub_divisao                            \n";
    $stSql .= "                 , regime.descricao as descricao_regime                                      \n";
    $stSql .= "                 , regime.cod_regime                                                         \n";
    $stSql .= "              FROM pessoal.especialidade                                                     \n";
    $stSql .= "                 , pessoal.especialidade_padrao                                              \n";
    $stSql .= "                 , (  SELECT cod_especialidade                                               \n";
    $stSql .= "                           , max(timestamp) as timestamp                                     \n";
    $stSql .= "                        FROM pessoal.especialidade_padrao                                    \n";
    $stSql .= "                    GROUP BY cod_especialidade) as max_espepecialidade_padrao                \n";
    $stSql .= "                 , pessoal.especialidade_sub_divisao                                         \n";
    $stSql .= "                 , (  SELECT cod_especialidade                                               \n";
    $stSql .= "                           , max(timestamp) as timestamp                                     \n";
    $stSql .= "                        FROM pessoal.especialidade_sub_divisao                               \n";
    $stSql .= "                    GROUP BY cod_especialidade) as max_especialidade_subdivisao              \n";
    $stSql .= "                 , normas.norma                                                              \n";
    $stSql .= "                 , pessoal.sub_divisao                                                       \n";
    $stSql .= "                 , pessoal.regime                                                            \n";
    $stSql .= "                 , folhapagamento.padrao                                                     \n";
    $stSql .= "                 , folhapagamento.padrao_padrao                                              \n";
    $stSql .= "                 , (  SELECT cod_padrao                                                      \n";
    $stSql .= "                           , max(timestamp) as timestamp                                     \n";
    $stSql .= "                        FROM folhapagamento.padrao_padrao                                    \n";
    $stSql .= "                    GROUP BY cod_padrao) as max_padrao_padrao                                \n";

    $stSql .= "                 , pessoal.cbo_especialidade                       \n";
    $stSql .= "                 , (  SELECT cod_especialidade                                               \n";
    $stSql .= "                           , max(timestamp) as timestamp                                     \n";
    $stSql .= "                        FROM pessoal.cbo_especialidade             \n";
    $stSql .= "                    GROUP BY cod_especialidade) as max_cbo_especialidade                     \n";

    $stSql .= "             WHERE especialidade.cod_especialidade = especialidade_padrao.cod_especialidade  \n";
    $stSql .= "               AND especialidade_padrao.cod_especialidade = max_espepecialidade_padrao.cod_especialidade\n";
    $stSql .= "               AND especialidade_padrao.timestamp         = max_espepecialidade_padrao.timestamp\n";
    $stSql .= "               AND especialidade.cod_especialidade = especialidade_sub_divisao.cod_especialidade\n";
    $stSql .= "               AND especialidade_sub_divisao.cod_especialidade = max_especialidade_subdivisao.cod_especialidade\n";
    $stSql .= "               AND especialidade_sub_divisao.timestamp         = max_especialidade_subdivisao.timestamp\n";
    $stSql .= "               AND especialidade_sub_divisao.cod_norma         = norma.cod_norma             \n";
    $stSql .= "               AND especialidade_sub_divisao.cod_sub_divisao   = sub_divisao.cod_sub_divisao \n";
    $stSql .= "               AND sub_divisao.cod_regime = regime.cod_regime                                \n";
    $stSql .= "               AND especialidade_padrao.cod_padrao = padrao.cod_padrao                       \n";
    $stSql .= "               AND padrao.cod_padrao = padrao_padrao.cod_padrao                              \n";
    $stSql .= "               AND padrao_padrao.cod_padrao = max_padrao_padrao.cod_padrao                   \n";
    $stSql .= "               AND padrao_padrao.timestamp  = max_padrao_padrao.timestamp                    \n";

    $stSql .= "               AND especialidade.cod_especialidade = cbo_especialidade.cod_especialidade             \n";
    $stSql .= "               AND cbo_especialidade.cod_especialidade = max_cbo_especialidade.cod_especialidade             \n";
    $stSql .= "               AND cbo_especialidade.timestamp  = max_cbo_especialidade.timestamp                    \n";

    if ($this->getDado('cod_especialidade'))
        $stSql .= "           AND especialidade.cod_especialidade = ". $this->getDado('cod_especialidade') ."\n";
    if ($this->getDado('cod_sub_divisao'))
        $stSql .= "           AND sub_divisao.cod_sub_divisao = ". $this->getDado('cod_sub_divisao') ."     \n";

    $stSql .= ") as  esp           \n";
    $stSql .= "       ON esp.cod_cargo = cargo.cod_cargo                                                    \n";
    if ($this->getDado('cod_cargo')) {
        $stSql .= "WHERE cargo.cod_cargo = ". $this->getDado('cod_cargo') ."                         \n";
        $stSql .= " AND esp.cod_especialidade is not null                               \n";
    }

    return $stSql;
}

function recuperaCargoEspecialidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCargoEspecialidade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCargoEspecialidade()
{
$stSQL .= " SELECT                                                                        \n";
$stSQL .= "    PESD.cod_cargo as cod_cargo,                                               \n";
$stSQL .= "    PESD.cod_regime as cod_regime,                                             \n";
$stSQL .= "    PESD.cod_sub_divisao as cod_sub_divisao,                                   \n";
$stSQL .= "    PESD.regime as nom_regime,                                                 \n";
$stSQL .= "    PESD.subdivisao as nom_subdivisao,                                         \n";
$stSQL .= "    PESD.cod_especialidade as cod_especialidade,                               \n";
$stSQL .= "    PESD.cod_cargo as cod_cargo,                                               \n";
$stSQL .= "    PESD.descricao as  descricao,                                              \n";
$stSQL .= "    PESD.cargo_cc,                                                             \n";
$stSQL .= "    PESD.funcao_gratificada,                                                   \n";
$stSQL .= "    PESD.regime as nom_regime,                                                 \n";
$stSQL .= "    PESD.subdivisao as nom_sub_divisao,                                        \n";
$stSQL .= "    getVagasDisponiveisEspecialidade(PESD.cod_regime,PESD.cod_sub_divisao,PESD.cod_especialidade,0,true,'".Sessao::getEntidade()."') as vagas_disponiveis,                                                           \n";
$stSQL .= "    PESDD.nro_vaga_criada,                                                     \n";
$stSQL .= "    getVagasOcupadasEspecialidade(PESD.cod_regime,PESD.cod_sub_divisao,PESD.cod_especialidade,0,true,'".Sessao::getEntidade()."') as vagas_ocupadas                \n";
$stSQL .= " FROM                                                                          \n";
$stSQL .= "(SELECT                                                                        \n";
$stSQL .= "      PR.cod_regime as cod_regime,                                             \n";
$stSQL .= "      PSD.cod_sub_divisao as cod_sub_divisao,                                  \n";
$stSQL .= "      PR.descricao as regime,                                                  \n";
$stSQL .= "      PSD.descricao as subdivisao,                                             \n";
$stSQL .= "      PC.cod_cargo as cod_cargo,                                               \n";
if ($this->getDado('cod_especialidade')) {
   $stSQL .= "      MAX(PC.descricao) as  descricao,                                      \n";
   $stSQL .= "      MAX(PE.cod_especialidade) as cod_especialidade,                       \n";
} else {
   $stSQL .= "      PC.descricao as  descricao,                                           \n";
   $stSQL .= "      PE.cod_especialidade as cod_especialidade,                            \n";
   $stSQL .= "      PE.descricao as descricao_especialidade,                              \n";
}
$stSQL .= "      PC.cargo_cc,                                                             \n";
$stSQL .= "      PC.funcao_gratificada                                                    \n";
$stSQL .= "    FROM                                                                       \n";
$stSQL .= "         pessoal.regime PR                                                     \n";
$stSQL .= "    JOIN pessoal.sub_divisao PSD                                               \n";
$stSQL .= "      ON (pr.cod_regime = psd.cod_regime),                                     \n";
$stSQL .= "         pessoal.especialidade PE,                                             \n";
$stSQL .= "         pessoal.cargo         PC                                              \n";
$stSQL .= "   WHERE                                                                       \n";
$stSQL .= "         PC.cod_cargo = PE.cod_cargo  AND                                      \n";
$stSQL .= "         PE.cod_cargo=".$this->getDado('cod_cargo')."                          \n";
if ($this->getDado('cod_especialidade')) {
   $stSQL .= "     AND PE.cod_especialidade =".$this->getDado('cod_especialidade')."      \n";
   $stSQL .= "    group by                                                                \n";
   $stSQL .= "      PR.cod_regime ,                                                       \n";
   $stSQL .= "      PSD.cod_sub_divisao,                                                  \n";
   $stSQL .= "      PR.descricao,                                                         \n";
   $stSQL .= "      PSD.descricao,                                                        \n";
   $stSQL .= "      PC.cod_cargo,                                                         \n";
   $stSQL .= "      PC.cargo_cc,                                                          \n";
   $stSQL .= "      PC.funcao_gratificada                                                 \n";
}
$stSQL .= "    order by cod_regime) as PESD                                               \n";
$stSQL .= "LEFT JOIN                                                                      \n";
$stSQL .= "                                                                               \n";
$stSQL .= "    (select a.timestamp,                                                       \n";
$stSQL .= "            a.cod_sub_divisao,                                                 \n";
$stSQL .= "            a.nro_vaga_criada,                                                 \n";
$stSQL .= "            a.cod_especialidade                                                \n";
$stSQL .= "           from                                                                \n";
$stSQL .= "                pessoal.especialidade_sub_divisao a,                           \n";
$stSQL .= "               (select  max(b.timestamp) as timestamp,                         \n";
$stSQL .= "                        b.cod_especialidade                                   \n";
// $stSQL .= "                        b.cod_sub_divisao                                      \n";
$stSQL .= "                  from                                                         \n";
$stSQL .= "                        pessoal.especialidade_sub_divisao b                    \n";
$stSQL .= "          WHERE   b.cod_especialidade is not null                              \n";

if ($this->getDado('cod_especialidade')) {
   $stSQL .= "     and b.cod_especialidade =".$this->getDado('cod_especialidade')."       \n";
}
$stSQL .= "              group by b.cod_especialidade) PESDA            \n";
$stSQL .= "         WHERE a.cod_especialidade = PESDA.cod_especialidade and               \n";
$stSQL .= "               a.timestamp          = PESDA.timestamp) as PESDD                      \n";
// $stSQL .= "               a.cod_sub_divisao   = PESDA.cod_sub_divisao) as PESDD           \n";
$stSQL .= "   ON(     PESD.cod_especialidade = PESDD.cod_especialidade                    \n";
$stSQL .= "      AND  PESD.cod_sub_divisao   = PESDD.cod_sub_divisao )                    \n";

return $stSQL;

}

function validaExclusao($stFiltro = "", $boTransacao = "")
{
    $obErro = new erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql  = $this->montaValidaExclusao().$stFiltro;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    if ( !$obErro->ocorreu() ) {
        if ( $rsRecordSet->getNumLinhas() > 0 ) {
            $obErro->setDescricao('Esta especialidade está sendo utilizada por um servidor, por esse motivo não pode ser excluída!');
        }
    }

    return $obErro;
}

function montaValidaExclusao()
{
    $stSQL  = "SELECT                                                              \n";
    $stSQL .= "   C.cod_especialidade, F.cod_especialidade                         \n";
    $stSQL .= "FROM                                                                \n";
    $stSQL .= "   pessoal.contrato_servidor_especialidade_cargo C,                 \n";
    $stSQL .= "   pessoal.contrato_servidor_especialidade_funcao F,                \n";
    $stSQL .= "   folhapagamento.configuracao_evento_caso_especialidade E          \n";
    $stSQL .= "WHERE C.cod_especialidade = ".$this->getDado('cod_especialidade')." \n";
    $stSQL .= "OR    F.cod_especialidade = ".$this->getDado('cod_especialidade')." \n";
    $stSQL .= "OR    E.cod_especialidade = ".$this->getDado('cod_especialidade')." \n";

    return $stSQL;
}

function recuperaEspecialidadeDeCodigosCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEspecialidadeDeCodigosCargo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEspecialidadeDeCodigosCargo()
{
    $stSql .= "SELECT especialidade.cod_especialidade                               \n";
    $stSql .= "     , especialidade.descricao                                       \n";
    $stSql .= "  FROM pessoal.especialidade                                         \n";
    $stSql .= "     , pessoal.cargo                                                 \n";
    $stSql .= " WHERE especialidade.cod_cargo = cargo.cod_cargo                     \n";

    return $stSql;
}

function recuperaPorCargo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaPorCargo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaPorCargo()
{
    $stSql .= "SELECT * FROM pessoal.especialidade \n";
    $stSql .= "WHERE especialidade.cod_cargo = ".$this->getDado('cod_cargo')." \n";

    return $stSql;
}

}

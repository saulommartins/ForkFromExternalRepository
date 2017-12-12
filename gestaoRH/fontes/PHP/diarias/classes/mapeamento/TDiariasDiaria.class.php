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
    * Classe de mapeamento da tabela diarias.diaria
    * Data de Criação: 15/08/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.09.02

    $Id: TDiariasDiaria.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TDiariasDiaria extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TDiariasDiaria()
{
    parent::Persistente();
    $this->setTabela("diarias.diaria");

    $this->setCampoCod('cod_diaria');
    $this->setComplementoChave('cod_contrato,timestamp');

    $this->AddCampo('cod_diaria'    ,'sequence'     ,true  ,''      ,true,false);
    $this->AddCampo('cod_contrato'  ,'integer'      ,true  ,''      ,true,'TPessoalContrato');
    $this->AddCampo('timestamp'     ,'timestamp_now',true  ,''      ,true,false);
    $this->AddCampo('cod_tipo'      ,'integer'      ,true  ,''      ,false,'TDiariasTipoDiaria');
    $this->AddCampo('cod_municipio' ,'integer'      ,true  ,''      ,false,false);
    $this->AddCampo('cod_uf'        ,'integer'      ,true  ,''      ,false,false);
    $this->AddCampo('cod_norma'     ,'integer'      ,true  ,''      ,false,'TNormasNorma');
    $this->AddCampo('numcgm'        ,'integer'      ,true  ,''      ,false,'TAdministracaoUsuario');
    $this->AddCampo('dt_inicio'     ,'date'         ,true  ,''      ,false,false);
    $this->AddCampo('dt_termino'    ,'date'         ,true  ,''      ,false,false);
    $this->AddCampo('quantidade'    ,'numeric'      ,true  ,'14,2'  ,false,false);
    $this->AddCampo('vl_total'      ,'numeric'      ,true  ,'14,2'  ,false,false);
    $this->AddCampo('motivo'        ,'text'         ,true  ,''      ,false,false);
    $this->AddCampo('vl_unitario'   ,'numeric'      ,true  ,'14,2'  ,false,false);
    $this->AddCampo('hr_inicio'     ,'time'         ,true  ,''      ,false,false);
    $this->AddCampo('hr_termino'    ,'time'         ,true  ,''      ,false,false);
    $this->AddCampo('timestamp_tipo','timestamp'    ,true  ,''      ,false,'TDiariasTipoDiaria');
}

function montaRecuperaRelacionamento()
{
    $stSql.= "   SELECT diaria.*\n";
    $stSql.= "        , to_char(diaria.dt_inicio,  'dd/mm/yyyy') as dt_inicio\n";
    $stSql.= "        , to_char(diaria.dt_termino, 'dd/mm/yyyy') as dt_termino\n";
    $stSql.= "        , to_char(diaria.hr_inicio,  'hh24:mi') as hr_inicio\n";
    $stSql.= "        , to_char(diaria.hr_termino, 'hh24:mi') as hr_termino\n";
    $stSql.= "        , sw_cgm.nom_cgm\n";
    $stSql.= "        , contrato.registro\n";
    $stSql.= "        , ovw.orgao as cod_estrutural\n";
    $stSql.= "        , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao\n";
    $stSql.= "        , norma.descricao as norma_descricao\n";
    $stSql.= "        , norma.num_norma||'/'||norma.exercicio as num_norma_exercicio\n";
    $stSql.= "        , norma.dt_publicacao as dt_publicacao\n";
    $stSql.= "        , diaria_empenho.cod_autorizacao||'/'||diaria_empenho.exercicio as autorizacao_empenho\n";
    $stSql.= "        , CASE WHEN nota_liquidacao_paga.timestamp IS NOT NULL \n";
    $stSql.= "               THEN (SELECT publico.fn_data_extenso(nota_liquidacao_paga.timestamp::date) )\n";
    $stSql.= "               ELSE '' \n";
    $stSql.= "           END as dt_autorizacao_empenho\n";
    $stSql.= "     FROM diarias.diaria\n";
    $stSql.= "LEFT JOIN diarias.diaria_empenho\n";
    $stSql.= "       ON (    diaria.cod_diaria   = diaria_empenho.cod_diaria \n";
    $stSql.= "           AND diaria.cod_contrato = diaria_empenho.cod_contrato\n";
    $stSql.= "           AND diaria.timestamp    = diaria_empenho.timestamp)\n";
    $stSql.= "LEFT JOIN empenho.autorizacao_empenho\n";
    $stSql.= "       ON (    diaria_empenho.exercicio       = autorizacao_empenho.exercicio\n";
    $stSql.= "           AND diaria_empenho.cod_entidade    = autorizacao_empenho.cod_entidade\n";
    $stSql.= "           AND diaria_empenho.cod_autorizacao = autorizacao_empenho.cod_autorizacao)\n";
    $stSql.= "LEFT JOIN empenho.pre_empenho\n";
    $stSql.= "       ON (    autorizacao_empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho\n";
    $stSql.= "           AND autorizacao_empenho.exercicio       = pre_empenho.exercicio)\n";
    $stSql.= "LEFT JOIN empenho.empenho\n";
    $stSql.= "       ON (    pre_empenho.cod_pre_empenho    = empenho.cod_pre_empenho\n";
    $stSql.= "           AND pre_empenho.exercicio          = empenho.exercicio)\n";
    $stSql.= "LEFT JOIN empenho.nota_liquidacao\n";
    $stSql.= "       ON (    empenho.cod_empenho            = nota_liquidacao.cod_empenho\n";
    $stSql.= "           AND empenho.exercicio              = nota_liquidacao.exercicio_empenho\n";
    $stSql.= "           AND empenho.cod_entidade           = nota_liquidacao.cod_entidade)\n";
    $stSql.= "LEFT JOIN empenho.nota_liquidacao_paga\n";
    $stSql.= "       ON (    nota_liquidacao.cod_nota       = nota_liquidacao_paga.cod_nota\n";
    $stSql.= "           AND nota_liquidacao.exercicio      = nota_liquidacao_paga.exercicio\n";
    $stSql.= "           AND nota_liquidacao.cod_entidade   = nota_liquidacao_paga.cod_entidade)\n";

    $stSql.= "        , (  SELECT cod_diaria\n";
    $stSql.= "                  , cod_contrato\n";
    $stSql.= "                  , max(timestamp) as timestamp\n";
    $stSql.= "               FROM diarias.diaria\n";
    $stSql.= "           GROUP BY cod_diaria, cod_contrato) as max_diaria\n";
    $stSql.= "        , pessoal.contrato\n";
    $stSql.= "        , pessoal.servidor_contrato_servidor\n";
    $stSql.= "        , pessoal.servidor\n";
    $stSql.= "        , sw_cgm\n";
    $stSql.= "        , pessoal.contrato_servidor_orgao\n";
    $stSql.= "        , (  SELECT cod_contrato\n";
    $stSql.= "               , max(timestamp) as timestamp\n";
    $stSql.= "               FROM pessoal.contrato_servidor_orgao\n";
    $stSql.= "           GROUP BY cod_contrato) as max_contrato_orgao\n";

    $stSql.= "        , organograma.orgao\n";
    $stSql.= "        , organograma.organograma\n";
    $stSql.= "        , organograma.orgao_nivel\n";
    $stSql.= "        , organograma.nivel\n";
    $stSql.= "        , organograma.vw_orgao_nivel as ovw\n";
    $stSql.= "        , normas.norma\n";

    $stSql.= "    WHERE diaria.cod_diaria                    = max_diaria.cod_diaria         \n";
    $stSql.= "      AND diaria.cod_contrato                  = max_diaria.cod_contrato       \n";
    $stSql.= "      AND diaria.timestamp                     = max_diaria.timestamp          \n";
    $stSql.= "      AND diaria.cod_contrato                  = contrato.cod_contrato         \n";
    $stSql.= "      AND contrato.cod_contrato                = servidor_contrato_servidor.cod_contrato\n";
    $stSql.= "      AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor      \n";
    $stSql.= "      AND servidor.numcgm                      = sw_cgm.numcgm                 \n";
    $stSql.= "      AND contrato_servidor_orgao.cod_contrato = contrato.cod_contrato         \n";
    $stSql.= "      AND contrato_servidor_orgao.cod_contrato = max_contrato_orgao.cod_contrato\n";
    $stSql.= "      AND contrato_servidor_orgao.timestamp    = max_contrato_orgao.timestamp  \n";

    $stSql.= "      AND contrato_servidor_orgao.cod_orgao   = orgao.cod_orgao                \n";
    $stSql.= "      AND organograma.cod_organograma = nivel.cod_organograma                  \n";
    $stSql.= "      AND nivel.cod_organograma       = orgao_nivel.cod_organograma            \n";
    $stSql.= "      AND nivel.cod_nivel             = orgao_nivel.cod_nivel                  \n";
    $stSql.= "      AND orgao_nivel.cod_orgao       = orgao.cod_orgao                        \n";
    $stSql.= "      AND orgao.cod_orgao             = ovw.cod_orgao                          \n";
    $stSql.= "      AND orgao_nivel.cod_organograma = ovw.cod_organograma                    \n";
    $stSql.= "      AND nivel.cod_nivel             = ovw.nivel                              \n";
    $stSql.= "      AND diaria.cod_norma            = norma.cod_norma                        \n";

    return $stSql;
}

function recuperaListaContratos(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    if (trim($stOrdem)=="") {$stOrdem=" ORDER BY nom_cgm";}
    $obErro = $this->executaRecupera("montaRecuperaListaContratos",$rsRecordSet,$stFiltro,$stOrdem);

    return $obErro;
}

function montaRecuperaListaContratos()
{
    $stSql.= "SELECT sw_cgm.nom_cgm\n";
    $stSql.= "     , contrato.cod_contrato\n";
    $stSql.= "     , contrato.registro\n";
    $stSql .= "    , ovw.orgao as cod_estrutural\n";
    $stSql .= "    , recuperaDescricaoOrgao(orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_lotacao\n";
    $stSql.= "  FROM pessoal.contrato\n";
    $stSql.= "     , pessoal.servidor_contrato_servidor\n";
    $stSql.= "     , pessoal.servidor\n";
    $stSql.= "     , sw_cgm\n";
    $stSql.= "     , pessoal.contrato_servidor_orgao\n";
    $stSql.= "     , (  SELECT cod_contrato\n";
    $stSql.= "               , max(timestamp) as timestamp\n";
    $stSql.= "            FROM pessoal.contrato_servidor_orgao\n";
    $stSql.= "        GROUP BY cod_contrato) as max_contrato_orgao\n";

    $stSql.= "     , organograma.orgao\n";
    $stSql.= "     , organograma.organograma\n";
    $stSql.= "     , organograma.orgao_nivel\n";
    $stSql.= "     , organograma.nivel\n";
    $stSql.= "     , organograma.vw_orgao_nivel as ovw\n";

    $stSql.= " WHERE contrato.cod_contrato                = servidor_contrato_servidor.cod_contrato\n";
    $stSql.= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor      \n";
    $stSql.= "   AND servidor.numcgm                      = sw_cgm.numcgm                 \n";
    $stSql.= "   AND contrato_servidor_orgao.cod_contrato = contrato.cod_contrato         \n";
    $stSql.= "   AND contrato_servidor_orgao.cod_contrato = max_contrato_orgao.cod_contrato\n";
    $stSql.= "   AND contrato_servidor_orgao.timestamp    = max_contrato_orgao.timestamp  \n";

    $stSql.= "   AND contrato_servidor_orgao.cod_orgao   = orgao.cod_orgao                \n";
    $stSql.= "   AND organograma.cod_organograma = nivel.cod_organograma                  \n";
    $stSql.= "   AND nivel.cod_organograma       = orgao_nivel.cod_organograma            \n";
    $stSql.= "   AND nivel.cod_nivel             = orgao_nivel.cod_nivel                  \n";
    $stSql.= "   AND orgao_nivel.cod_orgao       = orgao.cod_orgao                        \n";
    $stSql.= "   AND orgao.cod_orgao             = ovw.cod_orgao                          \n";
    $stSql.= "   AND orgao_nivel.cod_organograma = ovw.cod_organograma                    \n";
    $stSql.= "   AND nivel.cod_nivel             = ovw.nivel                              \n";

    if ($this->getDado('ativos')) {
        $stSql.= " AND NOT EXISTS    (SELECT 1                                                                                   \n";
        $stSql.= "                      FROM pessoal.aposentadoria                                      \n";
        $stSql.= "                     WHERE aposentadoria.cod_contrato = contrato.cod_contrato                                  \n";
        $stSql.= "                       AND NOT EXISTS (SELECT 1                                                                \n";
        $stSql.= "                                         FROM pessoal.aposentadoria_excluida          \n";
        $stSql.= "                                        WHERE aposentadoria_excluida.cod_contrato = aposentadoria.cod_contrato \n";
        $stSql.= "                                          AND aposentadoria_excluida.timestamp_aposentadoria = aposentadoria.timestamp)\n";
        $stSql.= "                   )                                                                                           \n";

        $stSql.= " AND NOT EXISTS    (SELECT 1                                                                                   \n";
        $stSql.= "                      FROM pessoal.contrato_servidor_caso_causa                       \n";
        $stSql.= "                     WHERE contrato_servidor_caso_causa.cod_contrato = contrato.cod_contrato)                  \n";
    }

    return $stSql;
}

function recuperaDiariasAutorizacaoEmpenho(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaDiariasAutorizacaoEmpenho",$rsRecordSet,$stFiltro,$stOrdem);
}

function montaRecuperaDiariasAutorizacaoEmpenho()
{
    $stSql = "select * from resumoEmissaoAutorizacaoEmpenhoDiarias('".$this->getDado("stTipoFiltro")."','".$this->getDado("stCodigos")."','".Sessao::getExercicio()."','".Sessao::getEntidade()."');";

    return $stSql;
}

}
?>

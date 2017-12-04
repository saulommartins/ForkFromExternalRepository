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
    * Classe de mapeamento da tabela ponto.dados_relogio_ponto
    * Data de Criação: 21/10/2008

    * @author Analista     : Dagiane Vieira
    * @author Desenvolvedor: Rafael Garbin

    * @package URBEM
    * @subpackage Mapeamento

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoDadosRelogioPonto extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoDadosRelogioPonto()
{
    parent::Persistente();
    $this->setTabela("ponto.dados_relogio_ponto");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato');

    $this->AddCampo('cod_contrato'          ,'integer'      ,true  ,'',true,'TPessoalContrato');

}

function montaRecuperaDadosContratoServidor()
{
    $stSql  = "    SELECT sw_cgm.nom_cgm                                                                                    \n";
    $stSql .= "         , contrato.registro                                                                                 \n";
    $stSql .= "         , contrato.cod_contrato                                                                             \n";
    $stSql .= "         , cargo.descricao as funcao                                                                         \n";
    $stSql .= "         , to_char(contrato_servidor_nomeacao_posse.dt_nomeacao, 'dd/mm/yyyy') as dt_nomeacao                \n";
    $stSql .= "         , to_char(contrato_servidor_nomeacao_posse.dt_posse, 'dd/mm/yyyy') as dt_posse                      \n";
    $stSql .= "         , to_char(contrato_servidor_nomeacao_posse.dt_admissao, 'dd/mm/yyyy') as dt_admissao                \n";
    $stSql .= "         , lpad(CAST(grade_horario.cod_grade AS VARCHAR),3,'0') as cod_grade_formatado                                          \n";
    $stSql .= "         , grade_horario.cod_grade                                                                           \n";
    $stSql .= "         , grade_horario.descricao as grade_horario                                                          \n";
    $stSql .= "      FROM pessoal.contrato                                                         \n";
    $stSql .= "INNER JOIN pessoal.servidor_contrato_servidor                                       \n";
    $stSql .= "        ON contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                                   \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_funcao                                         \n";
    $stSql .= "        ON contrato.cod_contrato = contrato_servidor_funcao.cod_contrato                                     \n";
    $stSql .= "INNER JOIN ( SELECT cod_contrato                                                                             \n";
    $stSql .= "                  , max(timestamp) as timestamp                                                              \n";
    $stSql .= "               FROM pessoal.contrato_servidor_funcao                                \n";
    $stSql .= "           GROUP BY cod_contrato) as max_contrato_servidor_funcao                                            \n";
    $stSql .= "        ON contrato_servidor_funcao.cod_contrato = max_contrato_servidor_funcao.cod_contrato                 \n";
    $stSql .= "       AND contrato_servidor_funcao.timestamp = max_contrato_servidor_funcao.timestamp                       \n";
    $stSql .= "INNER JOIN pessoal.cargo                                                            \n";
    $stSql .= "        ON contrato_servidor_funcao.cod_cargo = cargo.cod_cargo                                              \n";
    $stSql .= "INNER JOIN pessoal.servidor                                                         \n";
    $stSql .= "        ON servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                                   \n";
    $stSql .= "INNER JOIN sw_cgm                                                                                            \n";
    $stSql .= "        ON servidor.numcgm = sw_cgm.numcgm                                                                   \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor_nomeacao_posse                                 \n";
    $stSql .= "        ON contrato.cod_contrato = contrato_servidor_nomeacao_posse.cod_contrato                             \n";
    $stSql .= "INNER JOIN ( SELECT cod_contrato                                                                             \n";
    $stSql .= "         , MAX(timestamp) as timestamp                                                                       \n";
    $stSql .= "      FROM pessoal.contrato_servidor_nomeacao_posse                                 \n";
    $stSql .= "  GROUP BY cod_contrato) as max_contrato_servidor_nomeacao_posse                                             \n";
    $stSql .= "        ON contrato_servidor_nomeacao_posse.cod_contrato = max_contrato_servidor_nomeacao_posse.cod_contrato \n";
    $stSql .= "       AND contrato_servidor_nomeacao_posse.timestamp = max_contrato_servidor_nomeacao_posse.timestamp       \n";
    $stSql .= "INNER JOIN pessoal.contrato_servidor                                                \n";
    $stSql .= "        ON contrato.cod_contrato = contrato_servidor.cod_contrato                                            \n";
    $stSql .= "INNER JOIN pessoal.grade_horario                                                    \n";
    $stSql .= "        ON contrato_servidor.cod_grade = grade_horario.cod_grade                                             \n";

    if (trim($this->getDado("contrato"))!="") {
        $stSql .= "      WHERE contrato.cod_contrato IN (".$this->getDado("contrato").")                       \n";
    }

    if (trim($this->getDado("cgm_contrato"))!="") {
        $stSql .= "      WHERE contrato.cod_contrato IN (".$this->getDado("cgm_contrato").")                       \n";
    }

    if (trim($this->getDado("local"))!="") {
        $stSql .= "      WHERE EXISTS ( SELECT 1                                                                               \n";
        $stSql .= "                       FROM pessoal.contrato_servidor_local                        \n";
        $stSql .= "                 INNER JOIN ( SELECT cod_contrato                                                           \n";
        $stSql .= "                                   , max(timestamp) as timestamp                                            \n";
        $stSql .= "                                FROM pessoal.contrato_servidor_local               \n";
        $stSql .= "                            GROUP BY cod_contrato) as max_contrato_servidor_local                           \n";
        $stSql .= "                         ON contrato_servidor_local.cod_contrato = max_contrato_servidor_local.cod_contrato \n";
        $stSql .= "                        AND contrato_servidor_local.timestamp = max_contrato_servidor_local.timestamp       \n";
        $stSql .= "                        AND contrato_servidor_local.cod_contrato = contrato.cod_contrato                    \n";
        $stSql .= "                        AND contrato_servidor_local.cod_local IN (".$this->getDado("local")."))             \n";
    }

    if (trim($this->getDado("lotacao"))!="") {
        $stSql .= "      WHERE EXISTS ( SELECT 1                                                                               \n";
        $stSql .= "                       FROM pessoal.contrato_servidor_orgao                        \n";
        $stSql .= "                 INNER JOIN ( SELECT cod_contrato                                                           \n";
        $stSql .= "                                   , max(timestamp) as timestamp                                            \n";
        $stSql .= "                                FROM pessoal.contrato_servidor_orgao               \n";
        $stSql .= "                            GROUP BY cod_contrato) as max_contrato_servidor_orgao                           \n";
        $stSql .= "                         ON contrato_servidor_orgao.cod_contrato = max_contrato_servidor_orgao.cod_contrato \n";
        $stSql .= "                        AND contrato_servidor_orgao.timestamp = max_contrato_servidor_orgao.timestamp       \n";
        $stSql .= "                        AND contrato_servidor_orgao.cod_contrato = contrato.cod_contrato                    \n";
        $stSql .= "                        AND contrato_servidor_orgao.cod_orgao IN (".$this->getDado("lotacao")."))           \n";
    }

    if (trim($this->getDado("sub_divisao_funcao"))!="") {
        $stSql .= "      WHERE EXISTS ( SELECT 1                                                                                \n";
        $stSql .= "                       FROM pessoal.contrato_servidor                               \n";
        $stSql .= "                      WHERE contrato_servidor.cod_contrato = contrato.cod_contrato                           \n";
        $stSql .= "                        AND contrato_servidor.cod_sub_divisao IN (".$this->getDado("sub_divisao_funcao").")) \n";
    }

    //Só trás os servidor que possuem configuração para a lotação
    if (trim($this->getDado("existe_configuracao"))!="") {
        $stSql .= " AND EXISTS (SELECT 1                                                                                                                        \n";
        $stSql .= "               FROM pessoal.contrato_servidor                                                                       \n";
        $stSql .= "         INNER JOIN pessoal.contrato_servidor_orgao                                                                 \n";
        $stSql .= "                 ON contrato_servidor_orgao.cod_contrato = contrato_servidor.cod_contrato                                                    \n";
        $stSql .= "         INNER JOIN (  SELECT cod_contrato                                                                                                   \n";
        $stSql .= "                            , max(timestamp) as timestamp                                                                                    \n";
        $stSql .= "                         FROM pessoal.contrato_servidor_orgao                                                       \n";
        $stSql .= "                     GROUP BY cod_contrato) as max_contrato_servidor_orgao                                                                   \n";
        $stSql .= "                  ON max_contrato_servidor_orgao.cod_contrato = contrato_servidor_orgao.cod_contrato                                         \n";
        $stSql .= "                 AND max_contrato_servidor_orgao.timestamp = contrato_servidor_orgao.timestamp                                               \n";
        $stSql .= "          INNER JOIN ponto.configuracao_lotacao                                                                     \n";
        $stSql .= "                  ON contrato_servidor_orgao.cod_orgao = configuracao_lotacao.cod_orgao                                                      \n";
        $stSql .= "          INNER JOIN ponto.configuracao_relogio_ponto                                                               \n";
        $stSql .= "                  ON configuracao_lotacao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao                                     \n";
        $stSql .= "                 AND configuracao_lotacao.timestamp = configuracao_relogio_ponto.ultimo_timestamp                                            \n";
        $stSql .= "               WHERE NOT EXISTS (SELECT 1                                                                                                    \n";
        $stSql .= "                                   FROM ponto.configuracao_relogio_ponto_exclusao                                   \n";
        $stSql .= "                                  WHERE configuracao_relogio_ponto_exclusao.cod_configuracao = configuracao_relogio_ponto.cod_configuracao)) \n";
    }

    return $stSql;
}

function recuperaDadosContratoServidor(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDadosContratoServidor",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaDadosRelogioPontoPeriodo()
{
    $stDataInicial = $this->getDado("stDataInicial");
    $stDataFinal   = $this->getDado("stDataFinal");
    $inCodContrato = $this->getDado("inCodContrato");

    $stSql = "SELECT *                                                             \n";
    $stSql .= " FROM recuperaRelogioPontoPeriodo(   '".$stDataInicial."'           \n";
    $stSql .= "                                   , '".$stDataFinal."'             \n";
    $stSql .= "                                   ,  ".$inCodContrato."            \n";
    $stSql .= "                                   , '".Sessao::getEntidade()."');  \n";

    return $stSql;
}

function recuperaDadosRelogioPontoPeriodo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaDadosRelogioPontoPeriodo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

}
?>

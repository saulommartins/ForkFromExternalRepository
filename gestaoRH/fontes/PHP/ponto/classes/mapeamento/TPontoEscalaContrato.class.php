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
    * Classe de mapeamento da tabela ponto.escala_contrato
    * Data de Criação: 09/10/2008

    * @author Analista: Dagiane Vieira
    * @author Desenvolvedor: Alex Cardoso

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-04.10.02

    $Id:$
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPontoEscalaContrato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPontoEscalaContrato()
{
    parent::Persistente();
    $this->setTabela("ponto.escala_contrato");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_contrato,cod_escala,timestamp');

    $this->AddCampo('cod_contrato','integer'      ,true  ,'',true,'TPessoalContrato');
    $this->AddCampo('cod_escala'  ,'integer'      ,true  ,'',true,'TPontoEscala');
    $this->AddCampo('timestamp'   ,'timestamp_now',true  ,'',true,false);

}

function recuperaContratosEscala(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosEscala",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaContratosEscala()
{
    $stSql .= "SELECT escala_contrato.*\n";
    $stSql .= "  FROM ponto.escala_contrato\n";
    $stSql .= "     , (  SELECT cod_contrato\n";
    $stSql .= "               , cod_escala\n";
    $stSql .= "               , max(timestamp) AS timestamp \n";
    $stSql .= "            FROM ponto.escala_contrato\n";
    $stSql .= "        GROUP BY cod_contrato\n";
    $stSql .= "               , cod_escala) as max_contrato\n";
    $stSql .= " WHERE escala_contrato.cod_contrato = max_contrato.cod_contrato\n";
    $stSql .= "   AND escala_contrato.cod_escala = max_contrato.cod_escala\n";
    $stSql .= "   AND escala_contrato.timestamp = max_contrato.timestamp\n";
    $stSql .= "   AND NOT EXISTS (SELECT 1 \n";
    $stSql .= "                     FROM ponto.escala_contrato_exclusao\n";
    $stSql .= "                    WHERE escala_contrato_exclusao.cod_escala   = escala_contrato.cod_escala \n";
    $stSql .= "                      AND escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato\n";
    $stSql .= "                      AND escala_contrato_exclusao.timestamp    = escala_contrato.timestamp)\n";

    return $stSql;
}

function recuperaContratosDetalhadosEscala(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaContratosDetalhadosEscala",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaRecuperaContratosDetalhadosEscala()
{
    $stSql .= "    SELECT escala_contrato.cod_escala\n";
    $stSql .= "         , lpad(escala_contrato.cod_escala::VARCHAR, 5, '0') as cod_escala_formatado\n";

    if ($this->getDado('stRetorno') == 'contrato') {
        $stSql .= "         , contrato.cod_contrato\n";
        $stSql .= "         , contrato.cod_contrato as codigo\n";
        $stSql .= "         , contrato.registro\n";
        $stSql .= "         , servidor.numcgm\n";
        $stSql .= "         , (SELECT nom_cgm   FROM sw_cgm WHERE sw_cgm.numcgm = servidor.numcgm) AS nom_cgm\n";
    }
    if ($this->getDado('stRetorno') == 'lotacao') {
        $stSql .= "         , orgao_escala.cod_orgao\n";
        $stSql .= "         , orgao_escala.cod_orgao as codigo\n";
        $stSql .= "         , detalhes_orgao.mascara AS mascara_orgao\n";
        $stSql .= "         , recuperaDescricaoOrgao(detalhes_orgao.cod_orgao, '".Sessao::getExercicio()."-01-01') as descricao_orgao \n";
    }
    if ($this->getDado('stRetorno') == 'local') {
        $stSql .= "         , local_escala.cod_local\n";
        $stSql .= "         , local_escala.cod_local as codigo\n";
        $stSql .= "         , lpad(local_escala.cod_local, 3, '0') as cod_local_formatado\n";
        $stSql .= "	        , (SELECT descricao FROM organograma.local WHERE cod_local = local_escala.cod_local) AS descricao_local\n";
    }
    if ($this->getDado('stRetorno') == 'sub_divisao_funcao') {
        $stSql .= "         , regime_funcao.cod_regime\n";
        $stSql .= "         , sub_divisao_funcao.cod_sub_divisao\n";
        $stSql .= "         , funcao.cod_cargo\n";
        $stSql .= "         , regime_funcao.cod_regime||'_'||sub_divisao_funcao.cod_sub_divisao||'_'||funcao.cod_cargo as codigo\n";
        $stSql .= "         , (SELECT descricao FROM pessoal.regime WHERE cod_regime = regime_funcao.cod_regime) AS descricao_regime\n";
        $stSql .= "	        , (SELECT descricao FROM pessoal.sub_divisao WHERE cod_sub_divisao = sub_divisao_funcao.cod_sub_divisao) AS descricao_sub_divisao\n";
        $stSql .= "         , (SELECT descricao FROM pessoal.cargo WHERE cod_cargo = funcao.cod_cargo) AS descricao_cargo\n";
    }

    $stSql .= "         , to_char(min_max_escala.min_turno, 'dd/mm/yyyy') as min_turno\n";
    $stSql .= "         , to_char(min_max_escala.max_turno, 'dd/mm/yyyy') as max_turno\n";
    $stSql .= "      FROM ponto.escala_contrato\n";
    $stSql .= "      JOIN (  SELECT cod_contrato\n";
    $stSql .= "                   , max(timestamp) AS timestamp\n";
    $stSql .= "                FROM ponto.escala_contrato\n";
    $stSql .= "            GROUP BY cod_contrato) as max_escala\n";
    $stSql .= "        ON (escala_contrato.cod_contrato = max_escala.cod_contrato )\n";

    $stSql .= "      JOIN pessoal.contrato\n";
    $stSql .= "        ON (escala_contrato.cod_contrato = contrato.cod_contrato)\n";

    if ($this->getDado('stRetorno') == 'contrato') {

        $stSql .= "      JOIN pessoal.contrato_servidor\n";
        $stSql .= "        ON (contrato.cod_contrato = contrato_servidor.cod_contrato)\n";

        $stSql .= "      JOIN pessoal.servidor_contrato_servidor\n";
        $stSql .= "        ON (servidor_contrato_servidor.cod_contrato = contrato_servidor.cod_contrato)\n";

        $stSql .= "      JOIN pessoal.servidor\n";
        $stSql .= "        ON (servidor.cod_servidor = servidor_contrato_servidor.cod_servidor)\n";

    }

    if ($this->getDado('stRetorno') == 'lotacao') {

        $stSql .= "      JOIN (SELECT contrato_servidor_orgao.cod_contrato\n";
        $stSql .= "                 , contrato_servidor_orgao.cod_orgao\n";
        $stSql .= "              FROM pessoal.contrato_servidor_orgao\n";
        $stSql .= "                 , (  SELECT cod_contrato\n";
        $stSql .= "                           , max(timestamp) AS timestamp\n";
        $stSql .= "                        FROM pessoal.contrato_servidor_orgao\n";
        $stSql .= "                    GROUP BY cod_contrato) AS max_timestamp_orgao\n";
        $stSql .= "             WHERE contrato_servidor_orgao.cod_contrato = max_timestamp_orgao.cod_contrato\n";
        $stSql .= "               AND contrato_servidor_orgao.timestamp = max_timestamp_orgao.timestamp\n";
        $stSql .= "           ) AS orgao_escala\n";
        $stSql .= "        ON (contrato.cod_contrato = orgao_escala.cod_contrato)\n";

        $stSql .= "      JOIN (SELECT ovw.cod_orgao\n";
        $stSql .= "                 , ovw.orgao as mascara\n";
        $stSql .= "                 , recuperaDescricaoOrgao(ovw.cod_orgao,'".Sessao::getExercicio()."-01-01') as descricao\n";
        $stSql .= "              FROM organograma.orgao\n";
        $stSql .= "                 , organograma.organograma\n";
        $stSql .= "                 , organograma.orgao_nivel\n";
        $stSql .= "                 , organograma.nivel\n";
        $stSql .= "                 , organograma.vw_orgao_nivel as ovw\n";
        $stSql .= "             WHERE organograma.cod_organograma = nivel.cod_organograma\n";
        $stSql .= "               AND nivel.cod_organograma       = orgao_nivel.cod_organograma\n";
        $stSql .= "               AND nivel.cod_nivel             = orgao_nivel.cod_nivel\n";
        $stSql .= "               AND orgao_nivel.cod_orgao       = orgao.cod_orgao\n";
        $stSql .= "               AND orgao.cod_orgao             = ovw.cod_orgao\n";
        $stSql .= "               AND orgao_nivel.cod_organograma = ovw.cod_organograma\n";
        $stSql .= "               AND nivel.cod_nivel             = ovw.nivel) as detalhes_orgao\n";
        $stSql .= "          ON (orgao_escala.cod_orgao = detalhes_orgao.cod_orgao)\n";

    }

    if ($this->getDado('stRetorno') == 'sub_divisao_funcao') {

        $stSql .= "      JOIN (SELECT contrato_servidor_regime_funcao.cod_contrato\n";
        $stSql .= "                 , contrato_servidor_regime_funcao.cod_regime\n";
        $stSql .= "              FROM pessoal.contrato_servidor_regime_funcao\n";
        $stSql .= "                 , (   SELECT cod_contrato\n";
        $stSql .= "                            , max(timestamp) AS timestamp\n";
        $stSql .= "                         FROM pessoal.contrato_servidor_regime_funcao \n";
        $stSql .= "                     GROUP BY cod_contrato) AS max_regime_funcao\n";
        $stSql .= "             WHERE contrato_servidor_regime_funcao.cod_contrato = max_regime_funcao.cod_contrato\n";
        $stSql .= "               AND contrato_servidor_regime_funcao.timestamp = max_regime_funcao.timestamp\n";
        $stSql .= "           ) AS regime_funcao\n";
        $stSql .= "        ON (contrato.cod_contrato = regime_funcao.cod_contrato)\n";

        $stSql .= "      JOIN (SELECT contrato_servidor_sub_divisao_funcao.cod_contrato\n";
        $stSql .= "                 , contrato_servidor_sub_divisao_funcao.cod_sub_divisao\n";
        $stSql .= "              FROM pessoal.contrato_servidor_sub_divisao_funcao\n";
        $stSql .= "                 , (   SELECT cod_contrato\n";
        $stSql .= "                            , max(timestamp) AS timestamp\n";
        $stSql .= "                         FROM pessoal.contrato_servidor_sub_divisao_funcao \n";
        $stSql .= "                     GROUP BY cod_contrato) AS max_sub_divisao_funcao\n";
        $stSql .= "             WHERE contrato_servidor_sub_divisao_funcao.cod_contrato = max_sub_divisao_funcao.cod_contrato\n";
        $stSql .= "               AND contrato_servidor_sub_divisao_funcao.timestamp = max_sub_divisao_funcao.timestamp\n";
        $stSql .= "           ) AS sub_divisao_funcao\n";
        $stSql .= "        ON (contrato.cod_contrato = sub_divisao_funcao.cod_contrato)\n";

        $stSql .= "      JOIN (SELECT contrato_servidor_funcao.cod_contrato\n";
        $stSql .= "                 , contrato_servidor_funcao.cod_cargo\n";
        $stSql .= "              FROM pessoal.contrato_servidor_funcao\n";
        $stSql .= "                 , (   SELECT cod_contrato\n";
        $stSql .= "                            , max(timestamp) AS timestamp\n";
        $stSql .= "                         FROM pessoal.contrato_servidor_funcao \n";
        $stSql .= "                     GROUP BY cod_contrato) AS max_funcao\n";
        $stSql .= "             WHERE contrato_servidor_funcao.cod_contrato = max_funcao.cod_contrato\n";
        $stSql .= "               AND contrato_servidor_funcao.timestamp = max_funcao.timestamp\n";
        $stSql .= "           ) AS funcao\n";
        $stSql .= "        ON (contrato.cod_contrato = funcao.cod_contrato)\n";

    }

    $stSql .= "      JOIN ( SELECT escala.cod_escala\n";
    $stSql .= "                  , min(escala_turno.dt_turno) AS min_turno\n";
    $stSql .= "                  , max(escala_turno.dt_turno) AS max_turno\n";
    $stSql .= "               FROM ponto.escala\n";
    $stSql .= "                  , ponto.escala_turno\n";
    $stSql .= "              WHERE escala.cod_escala = escala_turno.cod_escala\n";
    $stSql .= "                AND escala.ultimo_timestamp = escala_turno.timestamp\n";
    $stSql .= "           GROUP BY escala.cod_escala\n";
    $stSql .= "           ) as min_max_escala\n";
    $stSql .= "        ON (escala_contrato.cod_escala = min_max_escala.cod_escala)\n";

    if ($this->getDado('stRetorno') == 'local') {

        $stSql .= " LEFT JOIN (SELECT contrato_servidor_local.cod_contrato\n";
        $stSql .= "                 , contrato_servidor_local.cod_local\n";
        $stSql .= "              FROM pessoal.contrato_servidor_local\n";
        $stSql .= "                 , (  SELECT cod_contrato\n";
        $stSql .= "                           , max(timestamp) as timestamp\n";
        $stSql .= "                        FROM pessoal.contrato_servidor_local\n";
        $stSql .= "                    GROUP BY cod_contrato) as max_timestamp_local\n";
        $stSql .= "             WHERE contrato_servidor_local.cod_contrato = max_timestamp_local.cod_contrato\n";
        $stSql .= "               AND contrato_servidor_local.timestamp    = max_timestamp_local.timestamp\n";
        $stSql .= "           ) AS local_escala\n";
        $stSql .= "        ON (contrato.cod_contrato = local_escala.cod_contrato)\n";

    }

    //Filtros
    if ($this->getDado('inCodEscala')) {
        $stFiltrosSql .= " AND escala_contrato.cod_escala = ".ltrim($this->getDado('inCodEscala'), "0")."\n";
    }

    if ($this->getDado('dtInicioPeriodo')) {
        $stFiltrosSql .= " AND min_turno >= TO_DATE('".$this->getDado('dtInicioPeriodo')."', 'dd/mm/yyyy')\n";
    }

    if ($this->getDado('dtFimPeriodo')) {
        $stFiltrosSql .= " AND max_turno <= TO_DATE('".$this->getDado('dtFimPeriodo')."', 'dd/mm/yyyy')\n";
    }

    if ($this->getDado('stCodigos')) {
        if ($this->getDado('stRetorno') == 'contrato') {
            $stFiltrosSql .= " AND escala_contrato.cod_contrato IN (".$this->getDado('stCodigos').")\n";

        } elseif ($this->getDado('stRetorno') == 'lotacao') {
            $stFiltrosSql .= " AND orgao_escala.cod_orgao IN (".$this->getDado('stCodigos').")\n";

        } elseif ($this->getDado('stRetorno') == 'local') {
            $stFiltrosSql .= " AND local_escala.cod_local IN (".$this->getDado('stCodigos').")\n";

        } elseif ($this->getDado('stRetorno') == 'sub_divisao_funcao') {
            $stFiltrosSql .= " AND sub_divisao_funcao.cod_sub_divisao IN (".$this->getDado('stCodigos').")\n";
        }
    }

    $stFiltrosSql .= " AND NOT EXISTS (SELECT 1
                                         FROM ponto.escala_contrato_exclusao
                                        WHERE escala_contrato_exclusao.cod_escala   = escala_contrato.cod_escala
                                          AND escala_contrato_exclusao.cod_contrato = escala_contrato.cod_contrato
                                          AND escala_contrato_exclusao.timestamp    = escala_contrato.timestamp)\n";

    if ($stFiltrosSql != "") {
        $stSql .= " WHERE ".substr($stFiltrosSql, 4);
    }

    //Agrupamentos
    if ($this->getDado('stRetorno') != 'contrato') {
        if ($this->getDado('stRetorno') == 'lotacao') {
            $stAgrupamentosSql .= ", orgao_escala.cod_orgao, mascara_orgao, descricao_orgao";

        } elseif ($this->getDado('stRetorno') == 'local') {
            $stAgrupamentosSql .= ", local_escala.cod_local, cod_local_formatado, descricao_local";

        } elseif ($this->getDado('stRetorno') == 'sub_divisao_funcao') {
            $stAgrupamentosSql .= ", cod_regime, cod_sub_divisao, cod_cargo, descricao_regime, descricao_sub_divisao, descricao_cargo";

        }
        $stSql .= " GROUP BY escala_contrato.cod_escala, cod_escala_formatado ".$stAgrupamentosSql.", min_turno, max_turno \n";
    }

    //Ordenacao
    if ($this->getDado('stRetorno') == 'contrato') {
        $stSql .= " ORDER BY nom_cgm, escala_contrato.cod_escala \n";

    } elseif ($this->getDado('stRetorno') == 'lotacao') {
        $stSql .= " ORDER BY mascara_orgao, descricao_orgao, escala_contrato.cod_escala \n";

    } elseif ($this->getDado('stRetorno') == 'local') {
        $stSql .= " ORDER BY descricao_local, escala_contrato.cod_escala \n";

    } elseif ($this->getDado('stRetorno') == 'sub_divisao_funcao') {
        $stSql .= " ORDER BY descricao_sub_divisao, escala_contrato.cod_escala \n";
    }

    return $stSql;
}

function recuperaVerificaConflitoVincularEscalaContrato(&$rsRecordset,$stFiltro="",$stOrdem="",$boTransacao="")
{
    return $this->executaRecupera("montaVerificaConflitoVincularEscalaContrato",$rsRecordset,$stFiltro,$stOrdem,$boTransacao);
}

function montaVerificaConflitoVincularEscalaContrato()
{
    /* Busca todos os turnos de um contrato
     * Com base nestes turnos, compara se para a escala a vincular, existe algum turno igual
     **/

    $stSql .= "SELECT turnos_contrato.cod_escala\n";
    $stSql .= "     , to_char(escala_turno.dt_turno, 'dd/mm/yyyy') as dt_turno\n";
    $stSql .= "     , (SELECT registro FROM pessoal.contrato WHERE cod_contrato = ".$this->getDado('cod_contrato').") as registro\n";
    $stSql .= "  FROM ponto.escala\n";
    $stSql .= "  JOIN ponto.escala_turno\n";
    $stSql .= "    ON (escala_turno.cod_escala = escala.cod_escala AND\n";
    $stSql .= "        escala_turno.timestamp = escala.ultimo_timestamp)\n";
    $stSql .= "  JOIN (SELECT escala.cod_escala\n";
    $stSql .= "             , escala_turno.dt_turno\n";
    $stSql .= "          FROM ponto.escala\n";
    $stSql .= "          JOIN ponto.escala_turno\n";
    $stSql .= "            ON (escala_turno.cod_escala = escala.cod_escala AND\n";
    $stSql .= "                escala_turno.timestamp = escala.ultimo_timestamp)\n";
    $stSql .= "          JOIN ponto.escala_contrato\n";
    $stSql .= "            ON (escala.cod_escala = escala_contrato.cod_escala)\n";
    $stSql .= "          JOIN ( SELECT cod_contrato\n";
    $stSql .= "                      , cod_escala\n";
    $stSql .= "                      , max(timestamp) as timestamp\n";
    $stSql .= "                   FROM ponto.escala_contrato\n";
    $stSql .= "               GROUP BY cod_contrato\n";
    $stSql .= "                      , cod_escala\n";
    $stSql .= "               ) as max_escala_contrato\n";
    $stSql .= "            ON (max_escala_contrato.cod_contrato = escala_contrato.cod_contrato AND\n";
    $stSql .= "                max_escala_contrato.cod_escala = escala_contrato.cod_escala AND\n";
    $stSql .= "                max_escala_contrato.timestamp = escala_contrato.timestamp\n";
    $stSql .= "                )\n";
    $stSql .= "         WHERE NOT EXISTS (SELECT 1 \n";
    $stSql .= "                             FROM ponto.escala_contrato_exclusao\n";
    $stSql .= "                            WHERE escala_contrato_exclusao.cod_contrato = max_escala_contrato.cod_contrato \n";
    $stSql .= "                              AND escala_contrato_exclusao.cod_escala = max_escala_contrato.cod_escala\n";
    $stSql .= "                              AND escala_contrato_exclusao.timestamp = max_escala_contrato.timestamp)\n";
    $stSql .= "               AND NOT EXISTS (SELECT 1\n";
    $stSql .= "                                 FROM ponto.escala_exclusao\n";
    $stSql .= "                                WHERE escala_exclusao.cod_escala = escala.cod_escala)\n";
    $stSql .= "               AND escala_contrato.cod_contrato = ".$this->getDado('cod_contrato')."\n";
    $stSql .= "       ) as turnos_contrato\n";
    $stSql .= "    ON (turnos_contrato.cod_escala <> escala_turno.cod_escala AND\n";
    $stSql .= "        turnos_contrato.dt_turno = escala_turno.dt_turno)\n";
    $stSql .= " WHERE NOT EXISTS (SELECT 1\n";
    $stSql .= "                     FROM ponto.escala_exclusao\n";
    $stSql .= "                    WHERE escala_exclusao.cod_escala = escala.cod_escala)\n";
    $stSql .= "       AND escala.cod_escala = ".$this->getDado('cod_escala');//Codigo da Escala a Vincular

    return $stSql;
}

}
?>

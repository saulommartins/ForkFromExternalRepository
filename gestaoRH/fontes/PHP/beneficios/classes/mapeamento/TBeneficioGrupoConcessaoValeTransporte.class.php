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
    * Classe de mapeamento da tabela BENEFICIO.GRUPO_CONCESSAO_VALE_TRANSPORTE
    * Data de Criação: 11/10/2005

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TBeneficioGrupoConcessaoValeTransporte.class.php 65736 2016-06-10 20:18:11Z michel $

    * Casos de uso: uc-04.06.09
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE;

class TBeneficioGrupoConcessaoValeTransporte extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function __construct()
{
    parent::Persistente();
    $this->setTabela('beneficio.grupo_concessao_vale_transporte');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_grupo,cod_mes,cod_concessao,exercicio');

    $this->AddCampo('cod_grupo','integer',true,'',true,true);
    $this->AddCampo('cod_mes','integer',true,'',true,true);
    $this->AddCampo('cod_concessao','integer',true,'',true,true);
    $this->AddCampo('exercicio','char',true,'4',true,true);
    $this->AddCampo('vigencia','date',true,'',false,false);

}

function recuperaGrupoConcessaoValeTransporte(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaGrupoConcessaoValeTransporte().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGrupoConcessaoValeTransporte()
{
    $stSql  .= "SELECT                                                      \r\n";
    $stSql  .= "    temp.*,                                                 \r\n";
    $stSql  .= "    to_char(temp.dt_dia,'dd/mm/yyyy') as stData,            \r\n";
    $stSql  .= "    bt.cod_concessao,                                       \r\n";
    $stSql  .= "    bt.exercicio,                                           \r\n";
    $stSql  .= "    bt.cod_mes,                                             \r\n";
    $stSql  .= "    bt.cod_vale_transporte,                                 \r\n";
    $stSql  .= "    bt.cod_tipo,                                            \r\n";
    $stSql  .= "    bt.quantidade as quantidade_mensal,                     \r\n";
    $stSql  .= "    bc.cod_calendario,                                      \r\n";
    $stSql  .= "    to_char(bg.vigencia,'dd/mm/yyyy') as vigencia           \r\n";
    $stSql  .= "FROM                                                        \r\n";
    $stSql  .= "    beneficio.grupo_concessao_vale_transporte   as bg,  \r\n";
    $stSql  .= "    beneficio.concessao_vale_transporte         as bt   \r\n";

    $stSql  .= "LEFT JOIN (                                                 \r\n";
    $stSql  .= "SELECT                                                      \r\n";
    $stSql  .= "    bs.quantidade as quantidade_semanal,                    \r\n";
    $stSql  .= "    bs.obrigatorio as obrigatorio_semanal,                  \r\n";
    $stSql  .= "    bd.*                                                    \r\n";
    $stSql  .= "FROM                                                        \r\n";
    $stSql  .= "    beneficio.concessao_vale_transporte_semanal as bs,  \r\n";
    $stSql  .= "    beneficio.concessao_vale_transporte_diario  as bd   \r\n";
    $stSql  .= "WHERE                                                       \r\n";
    $stSql  .= "        bs.exercicio     = bd.exercicio                     \r\n";
    $stSql  .= "    AND bs.cod_concessao = bd.cod_concessao                 \r\n";
    $stSql  .= "    AND bs.cod_dia       = bd.cod_dia                       \r\n";
    $stSql  .= "    AND bs.cod_mes       = bd.cod_mes) AS temp              \r\n";
    $stSql  .= "ON                                                          \r\n";
    $stSql  .= "        bt.cod_concessao = temp.cod_concessao               \r\n";
    $stSql  .= "    AND bt.exercicio     = temp.exercicio                   \r\n";
    $stSql  .= "    AND bt.cod_mes       = temp.cod_mes                     \r\n";

    $stSql  .= "LEFT JOIN                                                   \r\n";
    $stSql  .= "    beneficio.concessao_vale_transporte_calendario as bc\r\n";
    $stSql  .= "ON                                                          \r\n";
    $stSql  .= "        bt.cod_mes       = bc.cod_mes                       \r\n";
    $stSql  .= "    AND bt.exercicio     = bc.exercicio                     \r\n";
    $stSql  .= "    AND bt.cod_concessao = bc.cod_concessao                 \r\n";
    $stSql  .= "WHERE                                                       \r\n";
    $stSql  .= "        bg.cod_concessao = bt.cod_concessao                 \r\n";
    $stSql  .= "    AND bg.exercicio     = bt.exercicio                     \r\n";
    $stSql  .= "    AND bg.cod_mes       = bt.cod_mes                       \r\n";

    return $stSql;
}

function montaRecuperaGrupoConcessaoValeTransporteSituacao()
{
    $stSql .= "SELECT Bgcvt.cod_grupo                                                    \n";
    $stSql .= "     , Bgcvt.cod_mes                                                      \n";
    $stSql .= "     , Bgcvt.cod_concessao                                                \n";
    $stSql .= "     , Bgcvt.exercicio                                                    \n";
    $stSql .= "     , TO_CHAR(Bgcvt.vigencia,'dd/mm/yyyy') AS vigencia                   \n";
    $stSql .= "  FROM beneficio.grupo_concessao_vale_transporte AS Bgcvt             \n";
    $stSql .= "     , beneficio.concessao_vale_transporte AS Bcvt                    \n";
    $stSql .= " WHERE Bgcvt.cod_concessao = Bcvt.cod_concessao                           \n";
    $stSql .= "   AND Bgcvt.exercicio = Bcvt.exercicio                                   \n";
    $stSql .= "   AND Bgcvt.cod_mes = Bcvt.cod_mes                                       \n";

    return $stSql;
}

function recuperaGrupoConcessaoValeTransporteSituacao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaGrupoConcessaoValeTransporteSituacao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGrupoConcessaoVigenciaAtual()
{
    $stSql .= "  SELECT MIN(Bgcvt.cod_mes) AS cod_mes                                              \n";
    $stSql .= "       , Bgcvt.cod_concessao                                                        \n";
    $stSql .= "       , Bgcvt.exercicio                                                            \n";
    $stSql .= "       , TO_CHAR(Bgcvt.vigencia,'dd/mm/yyyy') AS vigencia                           \n";
    $stSql .= "       , Bgcvt.cod_grupo                                                            \n";
    $stSql .= "    FROM beneficio.grupo_concessao_vale_transporte AS Bgcvt                     \n";
    $stSql .= "   WHERE Bgcvt.vigencia = (                                                         \n";
    $stSql .= "         SELECT MAX(Bgcvt.vigencia)                                                 \n";
    $stSql .= "           FROM beneficio.grupo_concessao_vale_transporte AS Bgcvt              \n";
    $stSql .= "          WHERE Bgcvt.cod_concessao = ".$this->getDado('cod_concessao')."           \n";
    $stSql .= "            AND Bgcvt.vigencia <= now() )                                           \n";
    $stSql .= "     AND Bgcvt.cod_concessao = ".$this->getDado('cod_concessao')."                  \n";
    $stSql .= "GROUP BY Bgcvt.cod_concessao                                                        \n";
    $stSql .= "       , Bgcvt.exercicio                                                            \n";
    $stSql .= "       , Bgcvt.vigencia                                                             \n";
    $stSql .= "       , Bgcvt.cod_grupo                                                            \n";

    return $stSql;
}

function recuperaGrupoConcessaoVigenciaAtual(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaGrupoConcessaoVigenciaAtual().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
  * Lista os grupos (distintos) que possuem uma concessao
  **/
function montaRecuperaGrupoConcessao()
{
    $stSql .= "   SELECT Bgcvt.cod_grupo                                         \n";
    $stSql .= "     FROM beneficio.grupo_concessao_vale_transporte AS Bgcvt  \n";
    $stSql .= " GROUP BY Bgcvt.cod_grupo                                         \n";
    $stSql .= " ORDER BY Bgcvt.cod_grupo                                         \n";

    return $stSql;
}

function recuperaGrupoConcessao(&$rsRecordSet,$stFiltro="",$stOrdem="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if (trim($stOrdem)) {
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    }
    $stSql = $this->montaRecuperaGrupoConcessao().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

}

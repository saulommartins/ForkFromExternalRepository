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
    * Classe de mapeamento da tabela TESOURARIA_BOLETIM
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTesourariaBoletim.class.php 59612 2014-09-02 12:00:51Z gelson $

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-07-13 14:57:26 -0300 (Sex, 13 Jul 2007) $

    * Casos de uso: uc-02.04.04,uc-02.04.05,uc-02.04.06,uc-02.04.17
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_BOLETIM
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaBoletim extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaBoletim()
{
    parent::Persistente();
    $this->setTabela("tesouraria.boletim");

    $this->setCampoCod('cod_boletim');
    $this->setComplementoChave('exercicio,cod_entidade');

    $this->AddCampo('cod_boletim'        , 'integer'  , true, ''  , true  , false );
    $this->AddCampo('cod_entidade'       , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio'          , 'varchar'  , true, '04', true  , true  );
    $this->AddCampo('cod_terminal'       , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal' , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'        , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'  , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('dt_boletim'         , 'date'     , true, ''  , false , false );
}

function montaRecuperaRelacionamento()
{
    $stSql = "SELECT TB.cod_boletim                                                             \n";
    $stSql .= "      ,TB.exercicio                                                               \n";
    $stSql .= "      ,TB.cod_entidade                                                            \n";
    $stSql .= "      ,TB.cod_terminal                                                            \n";
    $stSql .= "      ,TB.timestamp_terminal                                                      \n";
    $stSql .= "      ,TB.cgm_usuario                                                             \n";
    $stSql .= "      ,TB.timestamp_usuario                                                       \n";
    $stSql .= "      ,TB.dt_boletim                                                              \n";
    $stSql .= "      ,TBF.timestamp_fechamento                                                   \n";
    $stSql .= "      ,TBR.timestamp_reabertura                                                   \n";
    $stSql .= "      ,TO_CHAR( TB.dt_boletim, 'dd/mm/yyyy' ) as data_boletim                     \n";
    $stSql .= "      ,TO_CHAR( TBF.timestamp_fechamento, 'dd/mm/yyyy - HH24:mm:ss' ) as dt_fechamento  \n";
    $stSql .= "      ,CGM.nom_cgm                                                               \n";
    $stSql .= "      ,TBL.timestamp_liberado                                                     \n";
    $stSql .= "      ,CASE WHEN TBF.timestamp_fechamento IS NOT NULL                             \n";
    $stSql .= "        THEN CASE WHEN TBR.timestamp_reabertura IS NOT NULL                       \n";
    $stSql .= "              THEN CASE WHEN TBF.timestamp_fechamento >= TBR.timestamp_reabertura \n";
    $stSql .= "                    THEN CASE WHEN TBL.timestamp_liberado IS NOT NULL             \n";
    $stSql .= "                          THEN 'liberado'                                         \n";
    $stSql .= "                          ELSE 'fechado'                                          \n";
    $stSql .= "                         END                                                      \n";
    $stSql .= "                    ELSE 'reaberto'                                               \n";
    $stSql .= "                   END                                                            \n";
    $stSql .= "              ELSE CASE WHEN TBL.timestamp_liberado IS NOT NULL                   \n";
    $stSql .= "                     THEN 'liberado'                                              \n";
    $stSql .= "                     ELSE 'fechado'                                               \n";
    $stSql .= "                   END                                                            \n";
    $stSql .= "             END                                                                  \n";
    $stSql .= "        ELSE 'aberto'                                                             \n";
    $stSql .= "       END AS situacao                                                            \n";
    $stSql .= "FROM tesouraria.boletim AS TB                                                     \n";
    $stSql .= "LEFT JOIN( SELECT TBF.cod_boletim                                                 \n";
    $stSql .= "                 ,TBF.exercicio                                                   \n";
    $stSql .= "                 ,TBF.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBF.timestamp_fechamento ) as timestamp_fechamento         \n";
    $stSql .= "           FROM tesouraria.boletim_fechado AS TBF                                 \n";
    $stSql .= "           GROUP BY cod_boletim                                                   \n";
    $stSql .= "                   ,exercicio                                                     \n";
    $stSql .= "                   ,cod_entidade                                                  \n";
    $stSql .= "           ORDER BY cod_boletim                                                   \n";
    $stSql .= "                   ,exercicio                                                     \n";
    $stSql .= "                   ,cod_entidade                                                  \n";
    $stSql .= ") AS TBF ON( TB.cod_boletim = TBF.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBF.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBF.cod_entidade )                                  \n";
    $stSql .= "LEFT JOIN( SELECT TBR.cod_boletim                                                 \n";
    $stSql .= "                 ,TBR.exercicio                                                   \n";
    $stSql .= "                 ,TBR.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBR.timestamp_reabertura ) as timestamp_reabertura         \n";
    $stSql .= "           FROM tesouraria.boletim_reaberto AS TBR                                \n";
    $stSql .= "           GROUP BY TBR.cod_boletim                                               \n";
    $stSql .= "                   ,TBR.exercicio                                                 \n";
    $stSql .= "                   ,TBR.cod_entidade                                              \n";
    $stSql .= "           ORDER BY TBR.cod_boletim                                               \n";
    $stSql .= "                   ,TBR.exercicio                                                 \n";
    $stSql .= "                   ,TBR.cod_entidade                                              \n";
    $stSql .= ") AS TBR ON( TB.cod_boletim = TBR.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBR.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBR.cod_entidade )                                  \n";
    $stSql .= "LEFT JOIN( SELECT TBL.cod_boletim                                                 \n";
    $stSql .= "                 ,TBL.exercicio                                                   \n";
    $stSql .= "                 ,TBL.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBL.timestamp_liberado  ) as timestamp_liberado            \n";
    $stSql .= "           FROM tesouraria.boletim_liberado   AS TBL                              \n";
    $stSql .= "           GROUP BY TBL.cod_boletim                                               \n";
    $stSql .= "                   ,TBL.exercicio                                                 \n";
    $stSql .= "                   ,TBL.cod_entidade                                              \n";
    $stSql .= "           ORDER BY TBL.cod_boletim                                               \n";
    $stSql .= "                   ,TBL.exercicio                                                 \n";
    $stSql .= "                   ,TBL.cod_entidade                                              \n";
    $stSql .= ") AS TBL ON( TB.cod_boletim = TBL.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBL.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBL.cod_entidade )                                  \n";
    $stSql .= ",sw_cgm as CGM                                                                    \n";
    $stSql .= "WHERE TB.cgm_usuario = CGM.numcgm                                                 \n";

    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  String  $stCondicao  String de condição do SQL (WHERE)
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaBoletimAberto(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaBoletimAberto().$stCondicao.$stOrder;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta SQL para recuperar boletim aberto
    * @access Private
    * @return String $stSql
*/
function montaRecuperaBoletimAberto()
{
    $stSql  = "SELECT TB.cod_boletim                                                             \n";
    $stSql .= "      ,TB.exercicio                                                               \n";
    $stSql .= "      ,TB.cod_entidade                                                            \n";
    $stSql .= "      ,TB.cod_terminal                                                            \n";
    $stSql .= "      ,TB.timestamp_terminal                                                      \n";
    $stSql .= "      ,TB.cgm_usuario                                                             \n";
    $stSql .= "      ,TB.timestamp_usuario                                                       \n";
    $stSql .= "      ,TBF.timestamp_fechamento                                                   \n";
    $stSql .= "      ,TBR.timestamp_reabertura                                                   \n";
    $stSql .= "      ,TO_CHAR( TB.dt_boletim, 'dd/mm/yyyy' ) as dt_boletim                       \n";
    $stSql .= "      ,TO_CHAR( TBF.timestamp_fechamento, 'dd/mm/yyyy - HH24:mm:ss' ) as dt_fechamento  \n";
    $stSql .= "      ,CGM.nom_cgm                                                               \n";
    $stSql .= "      ,TBL.timestamp_liberado                                                     \n";
    $stSql .= "      ,CASE WHEN TBF.timestamp_fechamento IS NOT NULL                             \n";
    $stSql .= "        THEN CASE WHEN TBR.timestamp_reabertura IS NOT NULL                       \n";
    $stSql .= "              THEN CASE WHEN TBF.timestamp_fechamento >= TBR.timestamp_reabertura \n";
    $stSql .= "                    THEN CASE WHEN TBL.timestamp_liberado IS NOT NULL             \n";
    $stSql .= "                          THEN 'liberado'                                         \n";
    $stSql .= "                          ELSE 'fechado'                                          \n";
    $stSql .= "                         END                                                      \n";
    $stSql .= "                    ELSE 'reaberto'                                               \n";
    $stSql .= "                   END                                                            \n";
    $stSql .= "              ELSE CASE WHEN TBL.timestamp_liberado IS NOT NULL                   \n";
    $stSql .= "                     THEN 'liberado'                                              \n";
    $stSql .= "                     ELSE 'fechado'                                               \n";
    $stSql .= "                   END                                                            \n";
    $stSql .= "             END                                                                  \n";
    $stSql .= "        ELSE 'aberto'                                                             \n";
    $stSql .= "       END AS situacao                                                            \n";
    $stSql .= "FROM tesouraria.boletim AS TB                                                     \n";
    $stSql .= "LEFT JOIN( SELECT TBF.cod_boletim                                                 \n";
    $stSql .= "                 ,TBF.exercicio                                                   \n";
    $stSql .= "                 ,TBF.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBF.timestamp_fechamento ) as timestamp_fechamento         \n";
    $stSql .= "           FROM tesouraria.boletim_fechado AS TBF                                 \n";
    $stSql .= "           GROUP BY cod_boletim                                                   \n";
    $stSql .= "                   ,exercicio                                                     \n";
    $stSql .= "                   ,cod_entidade                                                  \n";
    $stSql .= "           ORDER BY cod_boletim                                                   \n";
    $stSql .= "                   ,exercicio                                                     \n";
    $stSql .= "                   ,cod_entidade                                                  \n";
    $stSql .= ") AS TBF ON( TB.cod_boletim = TBF.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBF.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBF.cod_entidade )                                  \n";
    $stSql .= "LEFT JOIN( SELECT TBR.cod_boletim                                                 \n";
    $stSql .= "                 ,TBR.exercicio                                                   \n";
    $stSql .= "                 ,TBR.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBR.timestamp_reabertura ) as timestamp_reabertura         \n";
    $stSql .= "           FROM tesouraria.boletim_reaberto AS TBR                                \n";
    $stSql .= "           GROUP BY TBR.cod_boletim                                               \n";
    $stSql .= "                   ,TBR.exercicio                                                 \n";
    $stSql .= "                   ,TBR.cod_entidade                                              \n";
    $stSql .= "           ORDER BY TBR.cod_boletim                                               \n";
    $stSql .= "                   ,TBR.exercicio                                                 \n";
    $stSql .= "                   ,TBR.cod_entidade                                              \n";
    $stSql .= ") AS TBR ON( TB.cod_boletim = TBR.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBR.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBR.cod_entidade )                                  \n";
    $stSql .= "LEFT JOIN( SELECT TBL.cod_boletim                                                 \n";
    $stSql .= "                 ,TBL.exercicio                                                   \n";
    $stSql .= "                 ,TBL.cod_entidade                                                \n";
    $stSql .= "                 ,MAX( TBL.timestamp_liberado  ) as timestamp_liberado            \n";
    $stSql .= "           FROM tesouraria.boletim_liberado   AS TBL                              \n";
    $stSql .= "           GROUP BY TBL.cod_boletim                                               \n";
    $stSql .= "                   ,TBL.exercicio                                                 \n";
    $stSql .= "                   ,TBL.cod_entidade                                              \n";
    $stSql .= "           ORDER BY TBL.cod_boletim                                               \n";
    $stSql .= "                   ,TBL.exercicio                                                 \n";
    $stSql .= "                   ,TBL.cod_entidade                                              \n";
    $stSql .= ") AS TBL ON( TB.cod_boletim = TBL.cod_boletim                                     \n";
    $stSql .= "         AND TB.exercicio   = TBL.exercicio                                       \n";
    $stSql .= "         AND TB.cod_entidade= TBL.cod_entidade )                                  \n";
    $stSql .= ",sw_cgm as CGM                                                                    \n";
    $stSql .= "WHERE TB.cgm_usuario = CGM.numcgm                                                 \n";

    return $stSql;
}

function recuperaBoletimFechado(&$rsRecordSet, $stCondicao = "", $stOrder = "",  $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if ($stOrder != "") {
        if( !strstr( $stOrder, "ORDER BY" ) )
            $stOrder = " ORDER BY ".$stOrder;
    }
    $stSql = $this->montaRecuperaBoletimFechado().$stCondicao;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta SQL para recuperar boletim aberto
    * @access Private
    * @return String $stSql
*/
function montaRecuperaBoletimFechado()
{
    $stSql  = "SELECT TB.exercicio                                                                      \n";
    $stSql .= "      ,TB.cod_boletim                                                                    \n";
    $stSql .= "      ,TO_CHAR( TB.dt_boletim, 'dd/mm/yyyy' ) as dt_boletim                              \n";
    $stSql .= "      ,TO_CHAR( TBF.timestamp_fechamento, 'dd/mm/yyyy - HH:mm:ss' ) as dt_fechamento     \n";
    $stSql .= "      ,TBF.timestamp_fechamento                                                          \n";
    $stSql .= "      ,TB.timestamp_terminal                                                             \n";
    $stSql .= "      ,TB.timestamp_usuario                                                              \n";
    $stSql .= "      ,TB.cod_terminal                                                                   \n";
    $stSql .= "      ,TBF.cgm_usuario                                                                   \n";
    $stSql .= "      ,CGM.nom_cgm                                                                       \n";
    $stSql .= "FROM tesouraria.boletim AS TB, tesouraria.boletim_fechado AS TBF,                        \n";
    $stSql .= "sw_cgm AS CGM                                                                            \n";

    $stSql .= "WHERE EXISTS ( SELECT 1                                                                  \n";
    $stSql .= "                 FROM tesouraria.boletim_fechado AS TBF                                  \n";
    $stSql .= "                WHERE TBF.exercicio    = TB.exercicio                                    \n";
    $stSql .= "                  AND TBF.cod_boletim  = TB.cod_boletim                                  \n";
    $stSql .= "                  AND TBF.cod_entidade = TB.cod_entidade                                 \n";
    $stSql .= "                  AND NOT EXISTS ( SELECT 1                                              \n";
    $stSql .= "                                     FROM tesouraria.boletim_reaberto AS TBR             \n";
    $stSql .= "                                    WHERE TBR.exercicio            = TBF.exercicio       \n";
    $stSql .= "                                      AND TBR.cod_boletim          = TBF.cod_boletim     \n";
    $stSql .= "                                      AND TBR.cod_entidade         = TBF.cod_entidade    \n";
    $stSql .= "                                      AND TBR.timestamp_fechamento = TBF.timestamp_fechamento \n";
    $stSql .= "                                 )                                                       \n";
    $stSql .= "                  AND NOT EXISTS ( SELECT 1                                              \n";
    $stSql .= "                                     FROM tesouraria.boletim_liberado AS TBL             \n";
    $stSql .= "                                    WHERE TBL.exercicio            = TBF.exercicio       \n";
    $stSql .= "                                      AND TBL.cod_boletim          = TBF.cod_boletim     \n";
    $stSql .= "                                      AND TBL.cod_entidade         = TBF.cod_entidade    \n";
    $stSql .= "                                      AND TBL.timestamp_fechamento = TBF.timestamp_fechamento \n";
    $stSql .= "                                 )                                                       \n";
    $stSql .= "             )                                                                           \n";
    $stSql .= "AND CGM.numcgm = TBF.cgm_usuario                                                         \n";
    $stSql .= "AND TB.cod_boletim = TBF.cod_boletim                                                     \n";
    $stSql .= "AND TB.exercicio = TBF.exercicio                                                         \n";

    return $stSql;
}

}

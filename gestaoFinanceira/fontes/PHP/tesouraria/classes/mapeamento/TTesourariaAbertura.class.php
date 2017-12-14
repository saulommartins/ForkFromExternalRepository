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
    * Classe de mapeamento da tabela TESOURARIA_ABERTURA
    * Data de Criação: 12/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Lucas Leusin Oaigen

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32122 $
    $Name$
    $Autor:$
    $Date: 2008-03-26 11:31:58 -0300 (Qua, 26 Mar 2008) $

    * Casos de uso: uc-02.04.06
*/

/*
$Log$
Revision 1.18  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_ABERTURA
  * Data de Criação: 11/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Lucas Leusin Oaigen

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaAbertura extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaAbertura()
{
    parent::Persistente();
    $this->setTabela("tesouraria.abertura");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_terminal, timestamp_terminal, cgm_usuario, timestamp_usuario, timestamp_abertura');

    $this->AddCampo('cod_terminal'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal'    , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'           , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'     , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('timestamp_abertura'    , 'timestamp',false, ''  , false , false );
    $this->AddCampo('exercicio_boletim'     , 'varchar'  , true, '04', false , true  );
    $this->AddCampo('cod_entidade'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cod_boletim'           , 'integer'  , true, ''  , false , true  );
}

function montaRecuperaRelacionamento()
{
    $stSql .= " SELECT                                                                  \n";
    $stSql .= "     TT.timestamp_terminal,                                              \n";
    $stSql .= "     TT.cod_terminal,                                                    \n";
    $stSql .= "     TT.ip,                                                              \n";
    $stSql .= "     UT.cgm_usuario,                                                     \n";
    $stSql .= "     CGM.nom_cgm,                                                        \n";
    $stSql .= "     UT.timestamp_usuario,                                               \n";
    $stSql .= "     TD.timestamp_desativado,                                            \n";
    $stSql .= "     A.timestamp_abertura,                                               \n";
    $stSql .= "     to_char(A.timestamp_abertura,'dd/mm/yyyy') as dt_abertura,          \n";
    $stSql .= "     to_char(F.timestamp_fechamento,'dd/mm/yyyy') as dt_fechamento,      \n";
    $stSql .= "     F.timestamp_fechamento                                              \n";
    $stSql .= " FROM                                                                    \n";
    $stSql .= "     tesouraria.terminal as TT                                           \n";
    $stSql .= "         LEFT JOIN tesouraria.terminal_desativado as TD on(              \n";
    $stSql .= "             TT.cod_terminal         = TD.cod_terminal       AND         \n";
    $stSql .= "             TT.timestamp_terminal   = TD.timestamp_terminal             \n";
    $stSql .= "         )                                                               \n";
    $stSql .= "         LEFT JOIN tesouraria.usuario_terminal as UT on(                 \n";
    $stSql .= "             TT.timestamp_terminal   = UT.timestamp_terminal AND         \n";
    $stSql .= "             TT.cod_terminal         = UT.cod_terminal       AND         \n";
    $stSql .= "             UT.responsavel          = true                              \n";
    $stSql .= "         ),                                                              \n";
    $stSql .= "     tesouraria.abertura         as A                                    \n";
    $stSql .= "         LEFT JOIN tesouraria.fechamento as F on(                        \n";
    $stSql .= "             A.timestamp_terminal    = F.timestamp_terminal      AND     \n";
    $stSql .= "             A.cod_terminal          = F.cod_terminal            AND     \n";
    $stSql .= "             A.timestamp_abertura    = F.timestamp_abertura              \n";
    $stSql .= "         ),                                                              \n";
    $stSql .= "     sw_cgm                     as CGM                                  \n";
    $stSql .= " WHERE                                                                   \n";
    $stSql .= "     UT.cgm_usuario          = CGM.numcgm           AND                  \n";
    $stSql .= "     UT.timestamp_terminal   = A.timestamp_terminal AND                  \n";
    $stSql .= "     UT.cod_terminal         = A.cod_terminal       AND                  \n";
    $stSql .= "     UT.timestamp_usuario    = A.timestamp_usuario  AND                  \n";
    $stSql .= "     UT.cgm_usuario          = A.cgm_usuario                             \n";

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
function recuperaSituacao(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaSituacao().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaSituacao()
{
    $stSql  = "SELECT TT.cod_terminal                                                               \n";
    $stSql .= "      ,TTD.timestamp_desativado                                                      \n";
    $stSql .= "      ,TT.timestamp_terminal                                                         \n";
    $stSql .= "      ,TT.cod_verificador                                                            \n";
    $stSql .= "      ,TA.exercicio_boletim                                                          \n";
    $stSql .= "      ,TA.cod_entidade                                                               \n";
    $stSql .= "      ,TA.cod_boletim                                                                \n";
    $stSql .= "      ,TA.timestamp_abertura                                                         \n";
    $stSql .= "      ,TF.timestamp_fechamento                                                       \n";
    $stSql .= "      ,TUT.cgm_usuario                                                               \n";
    $stSql .= "      ,CGM.nom_cgm                                                                   \n";
    $stSql .= "      ,TUT.timestamp_usuario                                                         \n";
    $stSql .= "      ,TO_CHAR(TA.timestamp_abertura  ,'dd/mm/yyyy') AS dt_abertura                  \n";
    $stSql .= "      ,TO_CHAR(TF.timestamp_fechamento,'dd/mm/yyyy') AS dt_fechamento                \n";
    $stSql .= "      ,CASE WHEN TA.timestamp_abertura IS NULL                                       \n";
    $stSql .= "        THEN 'nao aberto'                                                            \n";
    $stSql .= "        ELSE CASE WHEN TF.timestamp_fechamento IS NULL                               \n";
    $stSql .= "              THEN 'aberto'                                                          \n";
    $stSql .= "              ELSE CASE WHEN TA.timestamp_abertura >= TF.timestamp_fechamento        \n";
    $stSql .= "                    THEN 'reaberto'                                                  \n";
    $stSql .= "                    ELSE 'fechado'                                                   \n";
    $stSql .= "                   END                                                               \n";
    $stSql .= "             END                                                                     \n";
    $stSql .= "       END AS situacao                                                               \n";
    $stSql .= "FROM tesouraria.terminal AS TT                                                       \n";
    $stSql .= "LEFT JOIN( SELECT TA.exercicio_boletim                                               \n";
    $stSql .= "                 ,TA.cod_entidade                                                    \n";
    $stSql .= "                 ,TA.cod_boletim                                                     \n";
    $stSql .= "                 ,TA.cod_terminal                                                    \n";
    $stSql .= "                 ,TA.timestamp_terminal                                              \n";
    $stSql .= "                 ,MAX( TA.timestamp_abertura ) AS timestamp_abertura                 \n";
    $stSql .= "           FROM tesouraria.abertura AS TA                                            \n";
    $stSql .= "           GROUP BY TA.exercicio_boletim                                             \n";
    $stSql .= "                   ,TA.cod_entidade                                                  \n";
    $stSql .= "                   ,TA.cod_boletim                                                   \n";
    $stSql .= "                   ,TA.cod_terminal                                                  \n";
    $stSql .= "                   ,TA.timestamp_terminal                                            \n";
    $stSql .= "           ORDER BY TA.exercicio_boletim                                             \n";
    $stSql .= "                   ,TA.cod_entidade                                                  \n";
    $stSql .= "                   ,TA.cod_boletim                                                   \n";
    $stSql .= "                   ,TA.cod_terminal                                                  \n";
    $stSql .= "                   ,TA.timestamp_terminal                                            \n";
    $stSql .= "                   ,TA.timestamp_terminal                                            \n";
    $stSql .= ") AS TA ON( TT.cod_terminal       = TA.cod_terminal                                  \n";
    $stSql .= "        AND TT.timestamp_terminal = TA.timestamp_terminal )                          \n";
    $stSql .= "LEFT JOIN( SELECT TF.cod_terminal                                                    \n";
    $stSql .= "                 ,TF.timestamp_terminal                                              \n";
    $stSql .= "                 ,TF.exercicio_boletim                                               \n";
    $stSql .= "                 ,TF.cod_entidade                                                    \n";
    $stSql .= "                 ,TF.cod_boletim                                                     \n";
    $stSql .= "                 ,MAX( timestamp_fechamento  ) AS timestamp_fechamento               \n";
    $stSql .= "           FROM tesouraria.abertura   AS TA                                          \n";
    $stSql .= "               ,tesouraria.fechamento AS TF                                          \n";
    $stSql .= "           WHERE TA.cod_terminal       = TF.cod_terminal                             \n";
    $stSql .= "             AND TA.timestamp_terminal = TF.timestamp_terminal                       \n";
    $stSql .= "             AND TA.timestamp_abertura = TF.timestamp_abertura                       \n";
    $stSql .= "           GROUP BY TF.cod_terminal                                                  \n";
    $stSql .= "                   ,TF.timestamp_terminal                                            \n";
    $stSql .= "                   ,TF.exercicio_boletim                                             \n";
    $stSql .= "                   ,TF.cod_entidade                                                  \n";
    $stSql .= "                   ,TF.cod_boletim                                                   \n";
    $stSql .= "           ORDER BY TF.cod_terminal                                                  \n";
    $stSql .= "                   ,TF.timestamp_terminal                                            \n";
    $stSql .= "                   ,TF.exercicio_boletim                                             \n";
    $stSql .= "                   ,TF.cod_entidade                                                  \n";
    $stSql .= "                   ,TF.cod_boletim                                                   \n";
    $stSql .= ") AS TF ON( TA.cod_terminal = TF.cod_terminal                                        \n";
    $stSql .= "        AND TA.timestamp_terminal = TF.timestamp_terminal                            \n";
    $stSql .= "        AND TA.exercicio_boletim  = TF.exercicio_boletim                             \n";
    $stSql .= "        AND TA.cod_entidade       = TF.cod_entidade                                  \n";
    $stSql .= "        AND TA.cod_boletim        = TF.cod_boletim         )                         \n";
    $stSql .= "LEFT JOIN tesouraria.terminal_desativado AS TTD                                      \n";
    $stSql .= "ON(                                                                                  \n";
    $stSql .= "    TT.cod_terminal       = TTD.cod_terminal                                         \n";
    $stSql .= "AND TT.timestamp_terminal = TTD.timestamp_terminal )                                 \n";
    $stSql .= "INNER JOIN tesouraria.usuario_terminal AS TUT                                        \n";
    $stSql .= "ON(                                                                                  \n";
    $stSql .= "    TT.cod_terminal       = TUT.cod_terminal                                         \n";
    $stSql .= "AND TT.timestamp_terminal = TUT.timestamp_terminal                                   \n";
    //$stSql .= "AND TUT.responsavel       = true                   )                                 \n";
    $stSql .= ")                                                                                    \n";
    $stSql .= "INNER JOIN sw_cgm AS CGM                                                             \n";
    $stSql .= "ON( TUT.cgm_usuario       = CGM.numcgm             )                                 \n";

    return $stSql;
}

    public function recuperaMaxAbertura(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
         return $this->executaRecupera("montaRecuperaMaxAbertura",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaMaxAbertura()
    {
        $stSql = "
            SELECT cod_terminal
                 , timestamp_terminal
                 , MAX(timestamp_abertura) AS timestamp_abertura
                 , cod_boletim
                 , cod_entidade
                 , exercicio_boletim
                 , cgm_usuario
                 , timestamp_usuario
              FROM tesouraria.abertura
             WHERE cod_terminal = ".$this->getDado('cod_terminal')."
               AND cod_entidade = ".$this->getDado('cod_entidade')."
          GROUP BY cod_terminal
                 , timestamp_terminal
                 , cod_boletim
                 , cod_entidade
                 , exercicio_boletim
                 , cgm_usuario
                 , timestamp_usuario
        ";

        return $stSql;

    }

}

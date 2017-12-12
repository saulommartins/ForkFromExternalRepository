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
    * Classe de mapeamento da tabela TESOURARIA_BORDERO
    * Data de Criação: 24/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-12-05 15:12:56 -0200 (Qua, 05 Dez 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

/*
$Log$
Revision 1.13  2007/04/30 19:21:00  cako
implementação uc-02.03.28

Revision 1.12  2007/03/30 22:00:14  cako
Bug #7884#

Revision 1.11  2006/07/05 20:38:37  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_BORDERO
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Jose Eduardo Porto

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaBordero extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaBordero()
{
    parent::Persistente();

    $this->setTabela("tesouraria.bordero");

    $this->setCampoCod('cod_bordero');
    $this->setComplementoChave('exercicio,cod_entidade');

    $this->AddCampo('cod_bordero'        , 'integer'  , true, ''  , true  , false );
    $this->AddCampo('cod_entidade'       , 'integer'  , true, ''  , true  , true  );
    $this->AddCampo('exercicio'          , 'varchar'  , true, '04', true  , true  );
    $this->AddCampo('cod_plano'          , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cod_boletim'        , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('exercicio_boletim'  , 'varchar'  , true, '04', false , true  );
    $this->AddCampo('timestamp_bordero'  , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('cod_autenticacao'   , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('dt_autenticacao'    , 'date'     , true, ''  , false , true  );
    $this->AddCampo('cod_terminal'       , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('cgm_usuario'        , 'integer'  , true, ''  , false , true  );
    $this->AddCampo('timestamp_terminal' , 'timestamp', true, ''  , false , true  );
    $this->AddCampo('timestamp_usuario'  , 'timestamp', true, ''  , false , true  );
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT  BOLETIM.cod_boletim                                                           \n";
    $stSql .= "       ,BOLETIM.cod_entidade                                                          \n";
    $stSql .= "       ,BOLETIM.exercicio                                                             \n";
    $stSql .= "       ,BOLETIM.cod_terminal                                                          \n";
    $stSql .= "       ,BOLETIM.timestamp_terminal                                                    \n";
    $stSql .= "       ,BOLETIM.cgm_usuario                                                           \n";
    $stSql .= "       ,BOLETIM.timestamp_usuario                                                     \n";
    $stSql .= "       ,TO_CHAR(BOLETIM.dt_boletim, 'dd/mm/yyyy') AS dt_boletim                       \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "       ,TB.cod_bordero                                                                \n";
    $stSql .= "       ,lpad(TB.cod_bordero,4,'0') AS bordero                                         \n";
//  $stSql .= "       ,TB.cod_entidade                                                               \n";
    $stSql .= "       ,TB.exercicio AS exercicio_bordero                                             \n";
//  $stSql .= "       ,TB.cod_boletim                                                                \n";
    $stSql .= "       ,TB.exercicio_boletim                                                          \n";
    $stSql .= "       ,TO_CHAR(TB.timestamp_bordero,'dd/mm/yyyy') as dt_bordero                      \n";
//  $stSql .= "       ,TB.cod_terminal                                                               \n";
//  $stSql .= "       ,TB.cgm_usuario                                                                \n";
    $stSql .= "       ,TB.cod_plano                                                                  \n";
    $stSql .= "       ,CGM.nom_cgm                                                                   \n";
//  $stSql .= "       ,TB.timestamp_terminal                                                         \n";
//  $stSql .= "       ,TB.timestamp_usuario                                                          \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "       ,CPB.cod_banco                                                                 \n";
    $stSql .= "       ,BANCO.nom_banco                                                               \n";
    $stSql .= "       ,BANCO.num_banco                                                               \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "       ,CPB.cod_agencia                                                               \n";
    $stSql .= "       ,AGENCIA.nom_agencia                                                           \n";
    $stSql .= "       ,AGENCIA.num_agencia                                                           \n";
    $stSql .= "       ,CPB.conta_corrente                                                            \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "       ,CPC.nom_conta                                                                 \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "       ,RECURSO.cod_recurso                                                           \n";
    $stSql .= "       ,RECURSO.nom_recurso                                                           \n";
    $stSql .= "                                                                                      \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                        \n";
    $stSql .= "             'T'                                                                      \n";
    $stSql .= "        ELSE                                                                          \n";
    $stSql .= "             'P'                                                                      \n";
    $stSql .= "        END AS tipo_bordero                                                           \n";  */
    $stSql .= "       ,cast('P' as varchar) as tipo_bordero                                                          \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                        \n";
    $stSql .= "             TTT.vl_pagamento                                                         \n";
    $stSql .= "        ELSE                                                                          \n";
    $stSql .= "             TTP.vl_pagamento                                                         \n";
    $stSql .= "        END AS vl_pagamento                                                           \n";  */
    $stSql .= "       ,ttp.cod_ordem                                                                 \n";
    $stSql .= "       ,ttp.exercicio_ordem                                                           \n";
    $stSql .= "       ,ttp.vl_pagamento                                                              \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                        \n";
    $stSql .= "             TTT.nom_cgm                                                              \n";
    $stSql .= "        ELSE                                                                          \n";
    $stSql .= "             TTP.nom_cgm                                                              \n";
    $stSql .= "        END AS credor                                                                 \n";  */
    $stSql .= "       ,cgm_credor.nom_cgm as credor                                                         \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                        \n";
    $stSql .= "             TTT.numcgm                                                               \n";
    $stSql .= "        ELSE                                                                          \n";
    $stSql .= "             TTP.cgm_beneficiario                                                     \n";
    $stSql .= "        END AS num_credor                                                             \n";  */
    $stSql .= "       ,ttp.cgm_beneficiario as num_credor                                            \n";
    $stSql .= "       ,ttp.vl_retencoes                                                              \n";
    $stSql .= "FROM    tesouraria.boletim AS BOLETIM                                                 \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "        INNER JOIN tesouraria.bordero AS TB  ON (                                     \n";
    $stSql .= "                    TB.cod_boletim            = BOLETIM.cod_boletim                   \n";
    $stSql .= "            AND     TB.cod_entidade           = BOLETIM.cod_entidade                  \n";
    $stSql .= "            AND     TB.exercicio_boletim      = BOLETIM.exercicio                     \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        INNER JOIN orcamento.entidade AS OE ON (                                      \n";
    $stSql .= "                    OE.cod_entidade           = TB.cod_entidade                       \n";
    $stSql .= "            AND     OE.exercicio              = TB.exercicio                          \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        INNER JOIN sw_cgm as CGM ON (                                                 \n";
    $stSql .= "                    CGM.numcgm                = OE.numcgm                             \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        INNER JOIN contabilidade.plano_banco AS CPB ON (                              \n";
    $stSql .= "                    CPB.cod_plano             = TB.cod_plano                          \n";
    $stSql .= "            AND     CPB.exercicio             = TB.exercicio                          \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN monetario.agencia AS AGENCIA ON (                                   \n";
    $stSql .= "                    AGENCIA.cod_banco         = CPB.cod_banco                         \n";
    $stSql .= "            AND     AGENCIA.cod_agencia       = CPB.cod_agencia                       \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN monetario.banco AS BANCO ON (                                       \n";
    $stSql .= "                    BANCO.cod_banco           = AGENCIA.cod_banco                     \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN contabilidade.plano_analitica AS CPA ON (                           \n";
    $stSql .= "                    CPA.cod_plano             = CPB.cod_plano                         \n";
    $stSql .= "            AND     CPA.exercicio             = CPB.exercicio                         \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN contabilidade.plano_conta AS CPC ON (                               \n";
    $stSql .= "                    CPC.cod_conta             = CPA.cod_conta                         \n";
    $stSql .= "            AND     CPC.exercicio             = CPA.exercicio                         \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN contabilidade.plano_recurso AS CPR ON (                             \n";
    $stSql .= "                    CPR.cod_plano             = CPA.cod_plano                         \n";
    $stSql .= "            AND     CPR.exercicio             = CPA.exercicio                         \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN orcamento.recurso('".$this->getDado('stExercicio')."') AS RECURSO ON ( \n";
    $stSql .= "                    RECURSO.cod_recurso       = CPR.cod_recurso                       \n";
    $stSql .= "            AND     RECURSO.exercicio         = CPR.exercicio                         \n";
    $stSql .= "        )                                                                             \n";
/*  $stSql .= "        LEFT JOIN (                                                                   \n";
    $stSql .= "            SELECT                                                                    \n";
    $stSql .= "                TTT.cod_bordero                                                       \n";
    $stSql .= "               ,TTT.cod_entidade                                                      \n";
    $stSql .= "               ,TTT.exercicio                                                         \n";
    $stSql .= "               ,TTT.numcgm                                                            \n";
    $stSql .= "               ,CGM.nom_cgm                                                           \n";
    $stSql .= "               ,sum(valor) AS vl_pagamento                                            \n";
    $stSql .= "            FROM                                                                      \n";
    $stSql .= "                tesouraria.transacoes_transferencia AS TTT                            \n";
    $stSql .= "               ,sw_cgm                              AS CGM                            \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            WHERE                                                                     \n";
    $stSql .= "                TTT.numcgm                    = CGM.numcgm                            \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            GROUP BY                                                                  \n";
    $stSql .= "                TTT.cod_bordero                                                       \n";
    $stSql .= "               ,TTT.cod_entidade                                                      \n";
    $stSql .= "               ,TTT.exercicio                                                         \n";
    $stSql .= "               ,TTT.numcgm                                                            \n";
    $stSql .= "               ,CGM.nom_cgm                                                           \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "        )AS TTT ON (                                                                  \n";
    $stSql .= "                    TTT.cod_bordero           = TB.cod_bordero                        \n";
    $stSql .= "            AND     TTT.cod_entidade          = TB.cod_entidade                       \n";
    $stSql .= "            AND     TTT.exercicio             = TB.exercicio                          \n";
    $stSql .= "        )                                                                             \n";  */
    $stSql .= "        LEFT JOIN (                                                                   \n";
    $stSql .= "            SELECT                                                                    \n";
    $stSql .= "                 TTP.cod_bordero                                                      \n";
    $stSql .= "                ,TTP.cod_entidade                                                     \n";
    $stSql .= "                ,TTP.exercicio                                                        \n";
    $stSql .= "                ,TTP.conta_corrente                                                   \n";
    $stSql .= "                ,EOP.cod_ordem                                                        \n";
    $stSql .= "                ,EOP.exercicio as exercicio_ordem                                     \n";
    $stSql .= "                ,EPE.cgm_beneficiario                                                 \n";
//  $stSql .= "                ,CGM.nom_cgm                                                          \n";
    $stSql .= "                ,NLCP.cod_plano                                                       \n";
    $stSql .= "                ,ENLP.timestamp                                                       \n";
    $stSql .= "                ,sum(ENLP.vl_pago) - eopr.vl_retencao AS vl_pagamento                 \n";
    $stSql .= "                ,eopr.vl_retencao AS vl_retencoes                                     \n";
    $stSql .= "            FROM                                                                      \n";
    $stSql .= "                 tesouraria.transacoes_pagamento  AS TTP                              \n";
    $stSql .= "                ,empenho.ordem_pagamento          AS EOP                              \n";
    $stSql .= "                LEFT JOIN (                                                           \n";
    $stSql .= "                     SELECT cod_ordem                                                 \n";
    $stSql .= "                           ,cod_entidade                                              \n";
    $stSql .= "                           ,exercicio                                                 \n";
    $stSql .= "                           ,sum(coalesce(vl_retencao,0.00)) as vl_retencao            \n";
    $stSql .= "                       FROM empenho.ordem_pagamento_retencao as EOPR                  \n";
    $stSql .= "                     GROUP BY cod_ordem, cod_entidade, exercicio                      \n";
    $stSql .= "                ) AS eopr ON (    eop.exercicio    = eopr.exercicio                   \n";
    $stSql .= "                            AND eop.cod_entidade = eopr.cod_entidade                  \n";
    $stSql .= "                            AND eop.cod_ordem    = eopr.cod_ordem                     \n";
    $stSql .= "                )                                                                     \n";
    $stSql .= "                ,empenho.pagamento_liquidacao     AS EPL                              \n";
    $stSql .= "                ,empenho.nota_liquidacao          AS ENL                              \n";
    $stSql .= "                ,empenho.empenho                  AS EE                               \n";
    $stSql .= "                ,empenho.pre_empenho              AS EPE                              \n";
    $stSql .= "                ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP          \n";
    $stSql .= "                ,empenho.nota_liquidacao_paga                      AS ENLP            \n";
    $stSql .= "                JOIN empenho.nota_liquidacao_conta_pagadora as NLCP                   \n";
    $stSql .= "                ON (   nlcp.exercicio_liquidacao = enlp.exercicio                     \n";
    $stSql .= "                   AND nlcp.cod_entidade   = enlp.cod_entidade                        \n";
    $stSql .= "                   AND nlcp.timestamp      = enlp.timestamp                           \n";
    $stSql .= "                   AND nlcp.cod_nota       = enlp.cod_nota                            \n";
    $stSql .= "                )                                                                     \n";
    $stSql .= "            WHERE                                                                     \n";
    $stSql .= "                    TTP.cod_ordem             = EOP.cod_ordem                         \n";
    $stSql .= "            AND     TTP.cod_entidade          = EOP.cod_entidade                      \n";
    $stSql .= "            AND     TTP.exercicio             = EOP.exercicio                         \n";
    if ($this->getDado('cod_bordero')) {
        $stSql .= "        AND     TTP.cod_bordero = ".$this->getDado('cod_bordero')."      \n";
    } else {
        if($this->getDado('cod_bordero_inicial'))
            $stSql .= "        AND     TTP.cod_bordero >= ".$this->getDado('cod_bordero_inicial')."      \n";
        if($this->getDado('cod_bordero_final'))
            $stSql .= "        AND     TTP.cod_bordero <= ".$this->getDado('cod_bordero_final')."        \n";
        $stSql .= "                                                                                      \n";
    }
    if($this->getDado('stCodOrdem'))
        $stSql .= "        AND     EOP.cod_ordem in (".$this->getDado('stCodOrdem').")               \n";
    if($this->getDado('cod_entidade'))
        $stSql .= "        AND     EOP.cod_entidade in (".$this->getDado('cod_entidade').")               \n";
    if($this->getDado('stExercicio'))
        $stSql .= "        AND     EOP.exercicio = '".$this->getDado('stExercicio')."'                \n";
    $stSql .= "            AND     EOP.cod_ordem             = EPL.cod_ordem                         \n";
    $stSql .= "            AND     EOP.cod_entidade          = EPL.cod_entidade                      \n";
    $stSql .= "            AND     EOP.exercicio             = EPL.exercicio                         \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            AND     EPL.exercicio_liquidacao  = ENL.exercicio                         \n";
    $stSql .= "            AND     EPL.cod_nota              = ENL.cod_nota                          \n";
    $stSql .= "            AND     EPL.cod_entidade          = ENL.cod_entidade                      \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            AND     ENL.exercicio_empenho     = EE.exercicio                          \n";
    $stSql .= "            AND     ENL.cod_empenho           = EE.cod_empenho                        \n";
    $stSql .= "            AND     ENL.cod_entidade          = EE.cod_entidade                       \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            AND     EE.cod_pre_empenho        = EPE.cod_pre_empenho                   \n";
    $stSql .= "            AND     EE.exercicio              = EPE.exercicio                         \n";
    $stSql .= "                                                                                      \n";
//  $stSql .= "            AND     EPE.cgm_beneficiario      = CGM.numcgm                            \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            AND     EPL.exercicio_liquidacao  = EPLNLP.exercicio_liquidacao           \n";
    $stSql .= "            AND     EPL.cod_nota              = EPLNLP.cod_nota                       \n";
    $stSql .= "            AND     EPL.cod_entidade          = EPLNLP.cod_entidade                   \n";
    $stSql .= "            AND     EPL.cod_ordem             = EPLNLP.cod_ordem                      \n";
    $stSql .= "            AND     EPL.exercicio             = EPLNLP.exercicio                      \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            AND     EPLNLP.exercicio_liquidacao = ENLP.exercicio                      \n";
    $stSql .= "            AND     EPLNLP.cod_nota             = ENLP.cod_nota                       \n";
    $stSql .= "            AND     EPLNLP.cod_entidade         = ENLP.cod_entidade                   \n";
    $stSql .= "            AND     EPLNLP.timestamp            = ENLP.timestamp                      \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "            GROUP BY                                                                  \n";
    $stSql .= "                 TTP.cod_bordero                                                      \n";
    $stSql .= "                ,TTP.cod_entidade                                                     \n";
    $stSql .= "                ,TTP.exercicio                                                        \n";
    $stSql .= "                ,TTP.conta_corrente                                                   \n";
    $stSql .= "                ,EPE.cgm_beneficiario                                                 \n";
    $stSql .= "                ,EOP.cod_ordem                                                        \n";
    $stSql .= "                ,EOP.exercicio                                                        \n";
//  $stSql .= "                ,CGM.nom_cgm                                                          \n";
    $stSql .= "                ,NLCP.cod_plano                                                       \n";
    $stSql .= "                ,ENLP.timestamp                                                       \n";
    $stSql .= "                ,EOPR.vl_retencao                                                     \n";
    $stSql .= "                                                                                      \n";
    $stSql .= "        )AS TTP ON (                                                                  \n";
    $stSql .= "                    TTP.cod_bordero    = TB.cod_bordero                               \n";
    $stSql .= "            AND     TTP.cod_entidade   = TB.cod_entidade                              \n";
    $stSql .= "            AND     TTP.exercicio      = TB.exercicio                                 \n";
    $stSql .= "            AND     TTP.cod_plano      = TB.cod_plano                                 \n";
    $stSql .= "            AND     TTP.timestamp      = TB.timestamp_bordero                         \n";
    $stSql .= "        )                                                                             \n";
    $stSql .= "        LEFT JOIN sw_cgm as CGM_CREDOR ON (                                                \n";
    $stSql .= "                     CGM_CREDOR.numcgm = ttp.cgm_beneficiario                              \n";
    $stSql .= "        )                                                                            \n";

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
function recuperaDadosBordero(&$rsRecordSet, $stCondicao = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaDadosBordero().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosBordero()
{
    $stSql  = "SELECT  BOLETIM.cod_boletim                                                                                        \n";
    $stSql .= "       ,BOLETIM.cod_entidade                                                                                       \n";
    $stSql .= "       ,BOLETIM.exercicio                                                                                          \n";
    $stSql .= "       ,lpad(TB.cod_bordero,4,'0') AS bordero                                                                      \n";
    $stSql .= "       ,TB.cod_bordero as cod_bordero                                                                              \n";
    $stSql .= "       ,TB.cod_entidade as cod_entidade_bordero                                                                    \n";
    $stSql .= "       ,TB.exercicio AS exercicio_bordero                                                                          \n";
    $stSql .= "       ,TO_CHAR(TB.timestamp_bordero,'dd/mm/yyyy') as dt_bordero                                                   \n";
    $stSql .= "       ,CPB.cod_banco                                                                                              \n";
    $stSql .= "       ,MB.num_banco                                                                                               \n";
    $stSql .= "       ,MA.num_agencia                                                                                             \n";
    $stSql .= "       ,CPB.cod_agencia                                                                                            \n";
    $stSql .= "       ,CPB.conta_corrente                                                                                         \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                                                     \n";
    $stSql .= "             'T'                                                                                                   \n";
    $stSql .= "        ELSE                                                                                                       \n";
    $stSql .= "             'P'                                                                                                   \n";
    $stSql .= "        END AS tipo_bordero                                                                                        \n";  */
    $stSql .= "       ,CAST('P' as varchar) AS tipo_bordero                                                                       \n";
/*  $stSql .= "       ,CASE WHEN TTP.cod_bordero IS NULL THEN                                                                     \n";
    $stSql .= "             TTT.vl_pagamento                                                                                      \n";
    $stSql .= "        ELSE                                                                                                       \n";
    $stSql .= "             TTP.vl_pagamento                                                                                      \n";
    $stSql .= "        END AS vl_pagamento                                                                                        \n";  */
    $stSql .= "        ,TTP.vl_pagamento                                                                                          \n";
    $stSql .= "        ,TTP.vl_liquido                                                                                            \n";
    $stSql .= "        ,TTP.vl_retencoes                                                                                           \n";
    $stSql .= "FROM    tesouraria.boletim AS BOLETIM                                                                              \n";
    $stSql .= "        INNER JOIN tesouraria.bordero AS TB  ON (                                                                  \n";
    $stSql .= "                    TB.cod_boletim            = BOLETIM.cod_boletim                                                \n";
    $stSql .= "            AND     TB.cod_entidade           = BOLETIM.cod_entidade                                               \n";
    $stSql .= "            AND     TB.exercicio_boletim      = BOLETIM.exercicio                                                  \n";
    $stSql .= "        )                                                                                                          \n";
    $stSql .= "        INNER JOIN contabilidade.plano_banco AS CPB ON (                                                           \n";
    $stSql .= "                    CPB.cod_plano             = TB.cod_plano                                                       \n";
    $stSql .= "            AND     CPB.exercicio             = TB.exercicio                                                       \n";
    $stSql .= "        )                                                                                                          \n";
    $stSql .= "        INNER JOIN monetario.banco as MB on ( cpb.cod_banco = mb.cod_banco )                                       \n";
    $stSql .= "        INNER JOIN monetario.agencia as MA                                                                         \n";
    $stSql .= "        on (   ma.cod_banco = cpb.cod_banco                                                                        \n";
    $stSql .= "           AND ma.cod_agencia = cpb.cod_agencia                                                                    \n";
    $stSql .= "        )                                                                                                          \n";
/*  $stSql .= "        LEFT JOIN (                                                                                                \n";
    $stSql .= "            SELECT                                                                                                 \n";
    $stSql .= "                TTT.cod_bordero                                                                                    \n";
    $stSql .= "               ,TTT.cod_entidade                                                                                   \n";
    $stSql .= "               ,TTT.exercicio                                                                                      \n";
    $stSql .= "               ,sum(valor) AS vl_pagamento                                                                         \n";
    $stSql .= "            FROM                                                                                                   \n";
    $stSql .= "                tesouraria.transacoes_transferencia AS TTT                                                         \n";
    $stSql .= "            GROUP BY                                                                                               \n";
    $stSql .= "                TTT.cod_bordero                                                                                    \n";
    $stSql .= "               ,TTT.cod_entidade                                                                                   \n";
    $stSql .= "               ,TTT.exercicio                                                                                      \n";
    $stSql .= "        )AS TTT ON (                                                                                               \n";
    $stSql .= "                    TTT.cod_bordero           = TB.cod_bordero                                                     \n";
    $stSql .= "            AND     TTT.cod_entidade          = TB.cod_entidade                                                    \n";
    $stSql .= "            AND     TTT.exercicio             = TB.exercicio                                                       \n";
    $stSql .= "        )                                                                                                          \n";  */
    $stSql .= "        LEFT JOIN (                                                                                                \n";
    $stSql .= "            SELECT                                                                                                 \n";
    $stSql .= "                 TTP.cod_bordero                                                                                   \n";
    $stSql .= "                ,TTP.cod_entidade                                                                                  \n";
    $stSql .= "                ,TTP.exercicio                                                                                     \n";
    $stSql .= "                ,ENLP.timestamp                                                                                    \n";
    $stSql .= "                ,NLCP.cod_plano                                                                                    \n";
    $stSql .= "                ,sum(coalesce(enlp.vl_pago,0.00)) AS vl_pagamento                                                  \n";
    $stSql .= "                ,sum(coalesce(enlp.vl_pago,0.00)) - coalesce(eopr.vl_retencao,0.00) AS vl_liquido                  \n";
    $stSql .= "                ,coalesce(eopr.vl_retencao,0.00) AS vl_retencoes                                                   \n";
    $stSql .= "            FROM                                                                                                   \n";
    $stSql .= "                 tesouraria.transacoes_pagamento  AS TTP                                                           \n";
    $stSql .= "                ,empenho.ordem_pagamento          AS EOP                                                           \n";
    $stSql .= "                LEFT JOIN (                                                                                        \n";
    $stSql .= "                     SELECT cod_ordem                                                                              \n";
    $stSql .= "                           ,cod_entidade                                                                           \n";
    $stSql .= "                           ,exercicio                                                                              \n";
    $stSql .= "                           ,sum(coalesce(vl_retencao,0.00)) as vl_retencao                                         \n";
    $stSql .= "                       FROM empenho.ordem_pagamento_retencao as EOPR                                               \n";
    $stSql .= "                     GROUP BY cod_ordem, cod_entidade, exercicio                                                   \n";
    $stSql .= "                ) AS eopr ON (    eop.exercicio    = eopr.exercicio                                                \n";
    $stSql .= "                            AND eop.cod_entidade = eopr.cod_entidade                                               \n";
    $stSql .= "                            AND eop.cod_ordem    = eopr.cod_ordem                                                  \n";
    $stSql .= "                )                                                                                                  \n";
    $stSql .= "                ,empenho.pagamento_liquidacao     AS EPL                                                           \n";
    $stSql .= "                ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP                                       \n";
    $stSql .= "                JOIN empenho.nota_liquidacao_paga AS ENLP                                                          \n";
    $stSql .= "                 ON (     enlp.cod_nota     = eplnlp.cod_nota                                                      \n";
    $stSql .= "                      AND enlp.exercicio    = eplnlp.exercicio_liquidacao                                          \n";
    $stSql .= "                      AND enlp.cod_entidade = eplnlp.cod_entidade                                                  \n";
    $stSql .= "                      AND enlp.timestamp    = eplnlp.timestamp                                                     \n";
    $stSql .= "                 )                                                                                                 \n";
    $stSql .= "                JOIN empenho.nota_liquidacao_conta_pagadora as NLCP                                                \n";
    $stSql .= "                ON (   nlcp.exercicio_liquidacao = enlp.exercicio                                                  \n";
    $stSql .= "                   AND nlcp.cod_entidade   = enlp.cod_entidade                                                     \n";
    $stSql .= "                   AND nlcp.timestamp      = enlp.timestamp                                                        \n";
    $stSql .= "                   AND nlcp.cod_nota       = enlp.cod_nota                                                         \n";
    $stSql .= "                )                                                                                                  \n";
    $stSql .= "            WHERE                                                                                                  \n";
    $stSql .= "                    TTP.cod_ordem             = EOP.cod_ordem                                                      \n";
    $stSql .= "            AND     TTP.cod_entidade          = EOP.cod_entidade                                                   \n";
    $stSql .= "            AND     TTP.exercicio             = EOP.exercicio                                                      \n";
    $stSql .= "                                                                                                                   \n";
    $stSql .= "            AND     EOP.cod_ordem             = EPL.cod_ordem                                                      \n";
    $stSql .= "            AND     EOP.cod_entidade          = EPL.cod_entidade                                                   \n";
    $stSql .= "            AND     EOP.exercicio             = EPL.exercicio                                                      \n";
    $stSql .= "                                                                                                                   \n";
    $stSql .= "            AND     EPL.exercicio_liquidacao  = EPLNLP.exercicio_liquidacao                                        \n";
    $stSql .= "            AND     EPL.cod_nota              = EPLNLP.cod_nota                                                    \n";
    $stSql .= "            AND     EPL.cod_entidade          = EPLNLP.cod_entidade                                                \n";
    $stSql .= "            AND     EPL.cod_ordem             = EPLNLP.cod_ordem                                                   \n";
    $stSql .= "            AND     EPL.exercicio             = EPLNLP.exercicio                                                   \n";
    $stSql .= "                                                                                                                   \n";
    $stSql .= "            GROUP BY                                                                                               \n";
    $stSql .= "                 TTP.cod_bordero                                                                                   \n";
    $stSql .= "                ,TTP.cod_entidade                                                                                  \n";
    $stSql .= "                ,TTP.exercicio                                                                                     \n";
    $stSql .= "                ,NLCP.cod_plano                                                                                    \n";
    $stSql .= "                ,ENLP.timestamp                                                                                    \n";
    $stSql .= "                ,EOPR.vl_retencao                                                                                  \n";
    $stSql .= "                                                                                                                   \n";
    $stSql .= "        )AS TTP ON (                                                                                               \n";
    $stSql .= "                    TTP.cod_bordero    = TB.cod_bordero                                                            \n";
    $stSql .= "            AND     TTP.cod_entidade   = TB.cod_entidade                                                           \n";
    $stSql .= "            AND     TTP.exercicio      = TB.exercicio                                                              \n";
    $stSql .= "            AND     TTP.timestamp      = TB.timestamp_bordero                                                      \n";
    $stSql .= "            AND     TTP.cod_plano      = TB.cod_plano                                                              \n";
    $stSql .= "        )                                                                                                          \n";

    return $stSql;
}

function recuperaListaBorderos(&$rsRecordSet, $stFiltro, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaBorderos( $stFiltro );
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta SQL para recuperar boletim aberto
    * @access Private
    * @return String $stSql
*/
function montaRecuperaListaBorderos($stFiltro)
{
   $stSql  = "  SELECT                                                                   \n";
   $stSql .= "         cod_entidade                                                      \n";
   $stSql .= "        ,exercicio                                                         \n";
   $stSql .= "        ,cgm_usuario                                                       \n";
   $stSql .= "        ,dt_boletim                                                        \n";
   $stSql .= "        ,cod_bordero                                                       \n";
   $stSql .= "        ,bordero                                                           \n";
   $stSql .= "        ,exercicio_bordero                                                 \n";
   $stSql .= "        ,exercicio_boletim                                                 \n";
   $stSql .= "        ,dt_bordero                                                        \n";
   $stSql .= "        ,cod_plano                                                         \n";
   $stSql .= "        ,nom_cgm                                                           \n";
   $stSql .= "        ,cod_banco                                                         \n";
   $stSql .= "        ,nom_banco                                                         \n";
   $stSql .= "        ,num_banco                                                         \n";
   $stSql .= "        ,cod_agencia                                                       \n";
   $stSql .= "        ,nom_agencia                                                       \n";
   $stSql .= "        ,num_agencia                                                       \n";
   $stSql .= "        ,conta_corrente                                                    \n";
   $stSql .= "        ,cod_recurso                                                       \n";
   $stSql .= "        ,tipo_bordero                                                      \n";
   $stSql .= "        ,sum(vl_pagamento) as vl_pagamento                                 \n";
   $stSql .= " FROM (                                                                    \n";
   $stSql .= $this->montaRecuperaRelacionamento().$stFiltro;
   $stSql .= " ) as tbl                                                                  \n";
   $stSql .= "   WHERE vl_pagamento > 0.00                                               \n";
   $stSql .= "     GROUP BY                                                              \n";
   $stSql .= "         cod_entidade                                                      \n";
   $stSql .= "        ,exercicio                                                         \n";
   $stSql .= "        ,cgm_usuario                                                       \n";
   $stSql .= "        ,dt_boletim                                                        \n";
   $stSql .= "        ,cod_bordero                                                       \n";
   $stSql .= "        ,bordero                                                           \n";
   $stSql .= "        ,exercicio_bordero                                                 \n";
   $stSql .= "        ,exercicio_boletim                                                 \n";
   $stSql .= "        ,dt_bordero                                                        \n";
   $stSql .= "        ,cod_plano                                                         \n";
   $stSql .= "        ,nom_cgm                                                           \n";
   $stSql .= "        ,cod_banco                                                         \n";
   $stSql .= "        ,nom_banco                                                         \n";
   $stSql .= "        ,num_banco                                                         \n";
   $stSql .= "        ,cod_agencia                                                       \n";
   $stSql .= "        ,nom_agencia                                                       \n";
   $stSql .= "        ,num_agencia                                                       \n";
   $stSql .= "        ,conta_corrente                                                    \n";
   $stSql .= "        ,tipo_bordero                                                      \n";
   $stSql .= "        ,cod_recurso                                                       \n";
   $stSql .= "   ORDER BY cod_bordero                                                    \n";

   return $stSql;
}

function recuperaListaOP(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaListaOP();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaOP()
{
    $stSql .= " SELECT tp.cod_ordem                                                            \n";
    $stSql .= "  FROM tesouraria.bordero as tb                                                  \n";
    $stSql .= "      JOIN tesouraria.transacoes_pagamento as tp                                  \n";
    $stSql .= "      ON (   tb.cod_bordero = tp.cod_bordero                                      \n";
    $stSql .= "         AND tb.cod_entidade = tp.cod_entidade                                   \n";
    $stSql .= "         AND tb.exercicio = tp.exercicio                                         \n";
    $stSql .= "      )                                                                          \n";
    $stSql .= " WHERE tb.cod_bordero is not null                                                \n";
if ($this->getDado('cod_bordero'))
    $stSql .= "       AND    tb.cod_bordero = ".$this->getDado('cod_bordero')."  \n";

if ( $this->getDado('cod_bordero_inicial') and $this->getDado('cod_bordero_final') ) {
    if(( $this->getDado('cod_bordero_inicial') == $this->getDado('cod_bordero_final' ) &&
       ( $this->getDado('cod_bordero_inicial') != '' && $this->getDado('cod_bordero_final') != '')))
    {
        $stSql .= "   AND tb.cod_bordero = ".$this->getDado('cod_bordero_inicial');
    } else {
        $stSql .= " AND tb.cod_bordero BETWEEN ".$this->getDado('cod_bordero_inicial')." AND ".$this->getDado('cod_bordero_final');
    }

} elseif ( $this->getDado('cod_bordero_inicial') and !$this->getDado('cod_bordero_final') ) {
    $stSql .= " AND tb.cod_bordero >= ".$this->getDado('cod_bordero_inicial');

} elseif ( !$this->getDado('cod_bordero_inicial') and $this->getDado('cod_bordero_final') ) {
    $stSql .= " AND TB.cod_bordero <= ".$this->getDado('cod_bordero_final');
}

if ($this->getDado('exercicio'))
    $stSql .= "       AND    tb.exercicio    = '".$this->getDado('exercicio')."'      \n";
if ($this->getDado('cod_entidade'))
    $stSql .= "       AND    tb.cod_entidade  in ( ".$this->getDado('cod_entidade').") \n";

    return $stSql;
}

}

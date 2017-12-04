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
    * Classe de mapeamento da tabela TESOURARIA_TRANSACOES_PAGAMENTO
    * Data de Criação: 26/01/2006

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Jose Eduardo Porto

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30668 $
    $Name$
    $Autor:$
    $Date: 2007-04-30 16:21:28 -0300 (Seg, 30 Abr 2007) $

    * Casos de uso: uc-02.04.20,uc-02.03.28
*/

/*
$Log$
Revision 1.9  2007/04/30 19:21:00  cako
implementação uc-02.03.28

Revision 1.8  2007/03/30 22:00:14  cako
Bug #7884#

Revision 1.7  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_TRANSACOES_PAGAMENTO
  * Data de Criação: 26/01/2006

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Jose Eduardo Porto

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaTransacaoPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaTransacaoPagamento()
{
    parent::Persistente();

    $this->setTabela("tesouraria.transacoes_pagamento");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_bordero,cod_ordem,cod_entidade,exercicio');

    $this->AddCampo('cod_bordero'        , 'integer'  , true, ''   , true  , true  );
    $this->AddCampo('cod_ordem'          , 'integer'  , true, ''   , true  , true  );
    $this->AddCampo('cod_entidade'       , 'integer'  , true, ''   , true  , true  );
    $this->AddCampo('exercicio'          , 'varchar'  , true, '04' , true  , true  );
    $this->AddCampo('cod_tipo'           , 'integer'  , true, ''   , false , false );
    $this->AddCampo('cod_agencia'        , 'integer'  , true, ''   , false , true  );
    $this->AddCampo('cod_banco'          , 'integer'  , true, ''   , false , true  );
    $this->AddCampo('conta_corrente'     , 'varchar'  , true, '20' , false , false );
    $this->AddCampo('documento'          , 'varchar'  , true, '100', false , false );
    $this->AddCampo('descricao'          , 'text'     , true, ''   , false , false );

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT                                                                                 \n";
    $stSql .= "        BOLETIM.cod_boletim,                                                           \n";
    $stSql .= "        BOLETIM.cod_entidade,                                                          \n";
    $stSql .= "        BOLETIM.exercicio,                                                             \n";
    $stSql .= "        BOLETIM.cod_terminal,                                                          \n";
    $stSql .= "        BOLETIM.timestamp_terminal,                                                    \n";
    $stSql .= "        BOLETIM.cgm_usuario,                                                           \n";
    $stSql .= "        BOLETIM.timestamp_usuario,                                                     \n";
    $stSql .= "        TO_CHAR(BOLETIM.dt_boletim, 'dd/mm/yyyy') AS dt_boletim,                       \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "        TB.cod_bordero,                                                                \n";
    $stSql .= "        TB.cod_entidade,                                                               \n";
    $stSql .= "        TB.exercicio AS exercicio_bordero,                                             \n";
    $stSql .= "        TB.cod_boletim,                                                                \n";
    $stSql .= "        TB.exercicio_boletim,                                                          \n";
    $stSql .= "        TO_CHAR(TB.timestamp_bordero,'dd/mm/yyyy') AS dt_bordero,                      \n";
    $stSql .= "        TB.cod_plano,                                                                  \n";
    $stSql .= "        TB.cod_terminal,                                                               \n";
    $stSql .= "        TB.cgm_usuario,                                                                \n";
    $stSql .= "        TB.timestamp_terminal,                                                         \n";
    $stSql .= "        TB.timestamp_usuario,                                                          \n";
    $stSql .= "        CGM.nom_cgm AS nom_cgm_bordero,                                                \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "        TTP.cod_bordero,                                                               \n";
    $stSql .= "        TTP.cod_ordem,                                                                 \n";
    $stSql .= "        TTP.cod_entidade AS cod_entidade_pagamento,                                    \n";
    $stSql .= "        TTP.exercicio AS exercicio_pagamento,                                          \n";
    $stSql .= "        TTP.cod_tipo,                                                                  \n";
    $stSql .= "        TTP.descricao,                                                                 \n";
    $stSql .= "        TTP.documento,                                                                 \n";
    $stSql .= "        TTP.vl_pagamento,                                                              \n";
    $stSql .= "        TTP.cod_banco,                                                                 \n";
    $stSql .= "        TTP.cod_agencia,                                                               \n";
    $stSql .= "        TTP.conta_corrente AS conta_corrente_pagamento,                                \n";
    $stSql .= "        TTP.cgm_beneficiario AS num_cgm_pagamento,                                     \n";
    $stSql .= "        TTP.nom_cgm AS nom_cgm_pagamento,                                              \n";
    $stSql .= "        MAP.num_agencia AS num_agencia_pagamento,                                      \n";
    $stSql .= "        MAP.nom_agencia,                                                               \n";
    $stSql .= "        MBP.num_banco AS num_banco_pagamento,                                          \n";
    $stSql .= "        MBP.nom_banco,                                                                 \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "        CASE WHEN PJ.numcgm IS NULL THEN                                               \n";
    $stSql .= "            PF.cpf                                                                     \n";
    $stSql .= "        ELSE                                                                           \n";
    $stSql .= "            PJ.cnpj                                                                    \n";
    $stSql .= "        END AS inscricao                                                               \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "FROM    tesouraria.boletim AS BOLETIM                                                  \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "        INNER JOIN tesouraria.bordero AS TB  ON (                                      \n";
    $stSql .= "                    TB.cod_boletim            = BOLETIM.cod_boletim                    \n";
    $stSql .= "            AND     TB.cod_entidade           = BOLETIM.cod_entidade                   \n";
    $stSql .= "            AND     TB.exercicio_boletim      = BOLETIM.exercicio                      \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        INNER JOIN orcamento.entidade AS OE ON (                                       \n";
    $stSql .= "                    OE.cod_entidade           = TB.cod_entidade                        \n";
    $stSql .= "            AND     OE.exercicio              = TB.exercicio                           \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        INNER JOIN sw_cgm AS CGM ON (                                                  \n";
    $stSql .= "                    CGM.numcgm                = OE.numcgm                              \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        JOIN (                                                                         \n";
    $stSql .= "            SELECT                                                                     \n";
    $stSql .= "                 TTP.*                                                                 \n";
    $stSql .= "                ,EPE.cgm_beneficiario                                                  \n";
    $stSql .= "                ,CGM.nom_cgm                                                           \n";
    $stSql .= "                ,NLCP.cod_plano                                                        \n";
    $stSql .= "                ,ENLP.timestamp                                                        \n";
    $stSql .= "                ,CASE WHEN opr.vl_retencao > 0.00 THEN                                 \n";
    $stSql .= "                           sum(ENLP.vl_pago) - opr.vl_retencao                         \n";
    $stSql .= "                      ELSE sum(enlp.vl_pago)                                           \n";
    $stSql .= "                 END AS vl_pagamento                                                   \n";
    $stSql .= "            FROM                                                                       \n";
    $stSql .= "                 tesouraria.transacoes_pagamento  AS TTP                               \n";
    $stSql .= "                ,empenho.ordem_pagamento          AS EOP                               \n";
    $stSql .= "                 LEFT JOIN ( SELECT cod_ordem                                          \n";
    $stSql .= "                                   ,cod_entidade                                       \n";
    $stSql .= "                                   ,exercicio                                          \n";
    $stSql .= "                                   ,coalesce(sum(vl_retencao),0.00) as vl_retencao     \n";
    $stSql .= "                              FROM empenho.ordem_pagamento_retencao                    \n";
    $stSql .= "                             WHERE cod_ordem is not null                               \n";
    if ($this->getDado('stCodOrdem'))
         $stSql .= "                          AND cod_ordem in ( ".$this->getDado('stCodOrdem')." )   \n";
    if ($this->getDado('exercicio'))
         $stSql .= "                          AND exercicio = '".$this->getDado('exercicio')."'       \n";
    if ($this->getDado('cod_entidade'))
         $stSql .= "                          AND cod_entidade = ".$this->getDado('cod_entidade')."   \n";
    $stSql .= "                          GROUP BY cod_ordem, cod_entidade, exercicio                  \n";
    $stSql .= "                 ) as opr ON ( opr.cod_ordem  = eop.cod_ordem                          \n";
    $stSql .= "                         AND opr.exercicio    = eop.exercicio                          \n";
    $stSql .= "                         AND opr.cod_entidade = eop.cod_entidade                       \n";
    $stSql .= "                 )                                                                     \n";
    $stSql .= "                ,empenho.pagamento_liquidacao     AS EPL                               \n";
    $stSql .= "                ,empenho.nota_liquidacao          AS ENL                               \n";
    $stSql .= "                ,empenho.empenho                  AS EE                                \n";
    $stSql .= "                ,empenho.pre_empenho              AS EPE                               \n";
    $stSql .= "                ,sw_cgm                           AS CGM                               \n";
    $stSql .= "                ,empenho.pagamento_liquidacao_nota_liquidacao_paga AS EPLNLP           \n";
    $stSql .= "                ,empenho.nota_liquidacao_paga                      AS ENLP             \n";
    $stSql .= "                JOIN empenho.nota_liquidacao_conta_pagadora as NLCP                   \n";
    $stSql .= "                ON (   nlcp.exercicio_liquidacao = enlp.exercicio                     \n";
    $stSql .= "                   AND nlcp.cod_entidade   = enlp.cod_entidade                        \n";
    $stSql .= "                   AND nlcp.timestamp      = enlp.timestamp                           \n";
    $stSql .= "                   AND nlcp.cod_nota       = enlp.cod_nota                            \n";
    $stSql .= "                )                                                                     \n";
    $stSql .= "            WHERE                                                                      \n";
    $stSql .= "                    TTP.cod_ordem             = EOP.cod_ordem                          \n";
    $stSql .= "            AND     TTP.cod_entidade          = EOP.cod_entidade                       \n";
    $stSql .= "            AND     TTP.exercicio             = EOP.exercicio                          \n";
    if ($this->getDado('cod_bordero'))
         $stSql .= "       AND     TTP.cod_bordero           = ".$this->getDado('cod_bordero')."      \n";
    $stSql .= "                                                                                       \n";
    if ($this->getDado('stCodOrdem'))
         $stSql .= "       AND     EOP.cod_ordem          in ( ".$this->getDado('stCodOrdem')." )     \n";
    if ($this->getDado('exercicio'))
         $stSql .= "       AND     EOP.exercicio             = '".$this->getDado('exercicio')."'      \n";
    if ($this->getDado('cod_entidade'))
         $stSql .= "       AND     EOP.cod_entidade          = ".$this->getDado('cod_entidade')."     \n";
    $stSql .= "            AND     EOP.cod_ordem             = EPL.cod_ordem                          \n";
    $stSql .= "            AND     EOP.cod_entidade          = EPL.cod_entidade                       \n";
    $stSql .= "            AND     EOP.exercicio             = EPL.exercicio                          \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     EPL.exercicio_liquidacao  = ENL.exercicio                          \n";
    $stSql .= "            AND     EPL.cod_nota              = ENL.cod_nota                           \n";
    $stSql .= "            AND     EPL.cod_entidade          = ENL.cod_entidade                       \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     ENL.exercicio_empenho     = EE.exercicio                           \n";
    $stSql .= "            AND     ENL.cod_empenho           = EE.cod_empenho                         \n";
    $stSql .= "            AND     ENL.cod_entidade          = EE.cod_entidade                        \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     EE.cod_pre_empenho        = EPE.cod_pre_empenho                    \n";
    $stSql .= "            AND     EE.exercicio              = EPE.exercicio                          \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     EPE.cgm_beneficiario      = CGM.numcgm                             \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     EPL.exercicio_liquidacao  = EPLNLP.exercicio_liquidacao            \n";
    $stSql .= "            AND     EPL.cod_nota              = EPLNLP.cod_nota                        \n";
    $stSql .= "            AND     EPL.cod_entidade          = EPLNLP.cod_entidade                    \n";
    $stSql .= "            AND     EPL.cod_ordem             = EPLNLP.cod_ordem                       \n";
    $stSql .= "            AND     EPL.exercicio             = EPLNLP.exercicio                       \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            AND     EPLNLP.exercicio_liquidacao = ENLP.exercicio                       \n";
    $stSql .= "            AND     EPLNLP.cod_nota             = ENLP.cod_nota                        \n";
    $stSql .= "            AND     EPLNLP.cod_entidade         = ENLP.cod_entidade                    \n";
    $stSql .= "            AND     EPLNLP.timestamp            = ENLP.timestamp                       \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "            GROUP BY                                                                   \n";
    $stSql .= "                 TTP.cod_bordero                                                       \n";
    $stSql .= "                ,TTP.cod_ordem                                                         \n";
    $stSql .= "                ,TTP.cod_entidade                                                      \n";
    $stSql .= "                ,TTP.exercicio                                                         \n";
    $stSql .= "                ,TTP.cod_tipo                                                          \n";
    $stSql .= "                ,TTP.cod_banco                                                         \n";
    $stSql .= "                ,TTP.cod_agencia                                                       \n";
    $stSql .= "                ,TTP.conta_corrente                                                    \n";
    $stSql .= "                ,TTP.documento                                                         \n";
    $stSql .= "                ,TTP.descricao                                                         \n";
    $stSql .= "                ,EPE.cgm_beneficiario                                                  \n";
    $stSql .= "                ,CGM.nom_cgm                                                           \n";
    $stSql .= "                ,NLCP.cod_plano                                                        \n";
    $stSql .= "                ,ENLP.timestamp                                                        \n";
    $stSql .= "                ,OPR.vl_retencao                                                       \n";
    $stSql .= "                                                                                       \n";
    $stSql .= "        )AS TTP ON (                                                                   \n";
    $stSql .= "                    TTP.cod_bordero    = TB.cod_bordero                                \n";
    $stSql .= "            AND     TTP.cod_entidade   = TB.cod_entidade                               \n";
    $stSql .= "            AND     TTP.exercicio      = TB.exercicio                                  \n";
    $stSql .= "            AND     TTP.cod_plano      = TB.cod_plano                                  \n";
    $stSql .= "            AND     TTP.timestamp      = TB.timestamp_bordero                          \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        INNER JOIN monetario.agencia AS MAP ON (                                       \n";
    $stSql .= "                    MAP.cod_banco              = TTP.cod_banco                         \n";
    $stSql .= "            AND     MAP.cod_agencia            = TTP.cod_agencia                       \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        INNER JOIN monetario.banco AS MBP ON (                                         \n";
    $stSql .= "                    MBP.cod_banco              = MAP.cod_banco                         \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        LEFT OUTER JOIN sw_cgm_pessoa_fisica AS PF ON (                                \n";
    $stSql .= "                    PF.numcgm                 = TTP.cgm_beneficiario                   \n";
    $stSql .= "        )                                                                              \n";
    $stSql .= "        LEFT OUTER JOIN sw_cgm_pessoa_juridica AS PJ ON (                              \n";
    $stSql .= "                    PJ.numcgm                 = TTP.cgm_beneficiario                   \n";
    $stSql .= "        )                                                                              \n";

    return $stSql;

}

/**
    * Executa um Select no banco de dados a partir do comando SQL
    * @access Public
    * @param  Object  $rsRecordSet Objeto RecordSet
    * @param  Boolean $boTransacao
    * @return Object  Objeto Erro
*/
function recuperaDadosBancariosCGM(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaDadosBancariosCGM();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

/**
    * Monta SQL para recuperar boletim aberto
    * @access Private
    * @return String $stSql
*/
function montaRecuperaDadosBancariosCGM()
{
    $stSql  = "SELECT                                                       \n";
    $stSql .= "    MB.num_banco,                                            \n";
    $stSql .= "    MA.num_agencia,                                          \n";
    $stSql .= "    TP.conta_corrente,                                       \n";
    $stSql .= "    PE.cgm_beneficiario                                      \n";
    $stSql .= "FROM                                                         \n";
    $stSql .= "    tesouraria.transacoes_pagamento as TP,                   \n";
    $stSql .= "    monetario.banco                 as MB,                   \n";
    $stSql .= "    monetario.agencia               as MA,                   \n";
    $stSql .= "    empenho.ordem_pagamento         as OP,                   \n";
    $stSql .= "    empenho.pagamento_liquidacao    as PL,                   \n";
    $stSql .= "    empenho.nota_liquidacao         as NL,                   \n";
    $stSql .= "    empenho.empenho                 as E,                    \n";
    $stSql .= "    empenho.pre_empenho             as PE                    \n";
    $stSql .= "WHERE                                                        \n";
    $stSql .= "    TP.cod_banco            = MB.cod_banco          AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    TP.cod_banco            = MA.cod_banco          AND      \n";
    $stSql .= "    TP.cod_agencia          = MA.cod_agencia        AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    TP.cod_ordem            = OP.cod_ordem          AND      \n";
    $stSql .= "    TP.exercicio            = OP.exercicio          AND      \n";
    $stSql .= "    TP.cod_entidade         = OP.cod_entidade       AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    OP.cod_ordem            = PL.cod_ordem          AND      \n";
    $stSql .= "    OP.cod_entidade         = PL.cod_entidade       AND      \n";
    $stSql .= "    OP.exercicio            = PL.exercicio          AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    PL.cod_nota             = NL.cod_nota           AND      \n";
    $stSql .= "    PL.cod_entidade         = NL.cod_entidade       AND      \n";
    $stSql .= "    PL.exercicio            = NL.exercicio          AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    NL.cod_empenho          = E.cod_empenho         AND      \n";
    $stSql .= "    NL.cod_entidade         = E.cod_entidade        AND      \n";
    $stSql .= "    NL.exercicio_empenho    = E.exercicio           AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    E.cod_pre_empenho       = PE.cod_pre_empenho    AND      \n";
    $stSql .= "    E.exercicio             = PE.exercicio          AND      \n";
    $stSql .= "                                                             \n";
    $stSql .= "    PE.cgm_beneficiario     = ".$this->getDado('numcgm')."   \n";
    $stSql .= "ORDER BY TP.oid DESC                                         \n";
    $stSql .= "LIMIT 1                                                      \n";

    return $stSql;
}

}
